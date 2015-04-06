<html>
<head>
<style type="text/css">
.link {
    text-decoration: none;
    color: rgb(11, 100, 214);
}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript">

Ext.require([
    '*'
]);

    Ext.define('mdl_rk', {
        extend: 'Ext.data.Model',
        fields: [
        {name:'total_rkp_id', mapping:'total_rkp_id'},
        {name:'tahap_kode_kendali', mapping:'tahap_kode_kendali'},
        {name:'tahap_nama_kendali', mapping:'tahap_nama_kendali'},
        {name:'tahap_satuan_kendali', mapping:'tahap_satuan_kendali'},
        {name:'vol_kk', mapping:'vol_kk'},
        {name:'tahap_harga_satuan_kendali', mapping:'tahap_harga_satuan_kendali', type:'float'},
        {name:'jml_rkp_kini', mapping:'jml_rkp_kini', type:'float'},
        {name:'vol_sd_bln_ini', mapping:'vol_sd_bln_ini'},
        {name:'jml_sd_bln_ini', mapping:'jml_sd_bln_ini', type:'float'},
        {name:'tahap_volume_bln1', mapping:'tahap_volume_bln1'},
        {name:'jml_bln1', mapping:'jml_bln1', type:'float'},
        {name:'tahap_volume_bln2', mapping:'tahap_volume_bln2'},
        {name:'jml_bln2', mapping:'jml_bln2', type:'float'},
        {name:'tahap_volume_bln3', mapping:'tahap_volume_bln3'},
        {name:'jml_bln3', mapping:'jml_bln3', type:'float'},
        {name:'tahap_volume_bln4', mapping:'tahap_volume_bln4'},
        {name:'jml_bln4', mapping:'jml_bln4', type:'float'},
        {name:'deviasi', mapping:'deviasi'},
        {name: 'ishaschild', mapping: 'ishaschild'}
        ]
    });

    Ext.define('mdl_combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value'}
         ]
    });

    var storesatuan = Ext.create('Ext.data.Store', {
        model: 'mdl_combo',
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getlistsatuan',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var store = Ext.create('Ext.data.TreeStore', {
        model: 'mdl_rk',
        expanded: true,
        autoLoad: false,
        proxy: {
            timeout: 900000,
            async: false,
            type: 'ajax',
            url: '<?php echo base_url() ?>pengendalian/get_data_rkp',
            reader: 'json'
        },
        listeners:{
            beforeload:function(){
                Ext.Msg.wait("Loading...","Please Wait");
            },
            load:function(){
                Ext.MessageBox.hide();
            }
        }
    }); 

Ext.onReady(function() {   
    store.load({
        params:{
            'tgl_rab': '<?php echo $tgl_rab ?>'
        }
    });

        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            beforeedit: function(grid,obj){   
                if (obj.record.get('ishaschild') == 1) {
                    return false;
                }
            },
            afteredit: function(rec,obj) {
                var selectedNode = grid.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                id = data.total_rkp_id;

                Ext.Ajax.request({
                     url: '<?=base_url();?>pengendalian/update_data/rencana_kerja',
                        method: 'POST',
                        params: {
                            'id' :  id,
                            'data1': data.tahap_volume_bln1, 
                            'data2': data.tahap_volume_bln2,
                            'data3': data.tahap_volume_bln3,
                            'data4': data.tahap_volume_bln4
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    store.load({
                        params:{
                            'tgl_rab': '<?php echo $tgl_rab ?>'
                        }
                    });                                        
                    },
                    failure: function() {
                    Ext.Msg.alert( "Status", "No Respond..!"); 
                    }
                });

                // console.log(id+tahap_nama_kendali+tahap_satuan_kendali+tahap_volume_kendali+tahap_harga_satuan_kendali);
            }   
        }
    });

    var grid = Ext.create('Ext.tree.Panel', {
        store: store,        
        rootVisible: false,
        multiSelect: false,
        singleExpand: false,
        hideCollapseTool: false,
        autoscroll: true,
        plugins: [rowEditing],
        title: 'RENCANA KERJA <?php echo $bln1; ?> Tahun <?php echo $thn; ?>',
        columns: [
        {text:"TOTAL PEKERJAAN",
            columns:[
            {text: "NO", xtype: 'treecolumn', width:100, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "ITEM PEKERJAAN", width:300, sortable: true, dataIndex: 'tahap_nama_kendali'},
            {text: "SATUAN", width:80, sortable: true, dataIndex: 'tahap_satuan_kendali',
                renderer: function(val){
                        var index = storesatuan.findExact('value',val);
                        if (index != -1) {
                            var rec = storesatuan.getAt(index);
                            text = rec.get('text');
                        } else {
                            text = val;
                        }
                        return text;
                    }
            },
            {text: "VOLUME", width:80, sortable: true, dataIndex: 'vol_kk'},
            {text: "HARGA KONTRAK", width:120, sortable: true, dataIndex: 'tahap_harga_satuan_kendali'},
            {text: "TOTAL", width:120, sortable: true, dataIndex: 'jml_rkp_kini'}
            ]
        },{text:"PRESTASI s/d BULAN INI",
            columns:[
            {text: "VOLUME", width:120, sortable: true, dataIndex: 'vol_sd_bln_ini'},
            {text: "JUMLAH", width:120, sortable: true, dataIndex: 'jml_sd_bln_ini'},
            ]
        },{text: "RENCANA KERJA",
            columns:[
            {text:"<?php echo $bln1; ?>", sortable: false,
            columns:[
            {text: "1 - 15", sortable: false,
            columns: [
            {text: "VOLUME", width:120, sortable: true, dataIndex: 'tahap_volume_bln1',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "JUMLAH", width:120, sortable: true, dataIndex: 'jml_bln1'}
            ]
        },
            {text: "16 - 31", sortable: false,
            columns: [
            {text: "VOLUME", width:120, sortable: true, dataIndex: 'tahap_volume_bln2',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "JUMLAH", width:120, sortable: true, dataIndex: 'jml_bln2'}
            ]
        },
            ]
        },{text:"<?php echo $bln2; ?>", sortable: false,
            columns:[
            {text: "1 - 15", sortable: false,
            columns: [
            {text: "VOLUME", width:120, sortable: true, dataIndex: 'tahap_volume_bln3',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "JUMLAH", width:120, sortable: true, dataIndex: 'jml_bln3'}
            ]
        },
            {text: "16 - 31", sortable: false,
            columns: [
            {text: "VOLUME", width:120, sortable: true, dataIndex: 'tahap_volume_bln4',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "JUMLAH", width:120, sortable: true, dataIndex: 'jml_bln4'}
            ]
        },
            ]
        }
            ]
    },
        {text: "DEVIASI", width:80, sortable: true, dataIndex: 'deviasi'}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Print',
                tooltip:'Print',
                handler: function(){

                }
            },'-',{
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    var url ='<?php echo base_url(); ?>pengendalian/pilih_rincian_rencana_kerja';
                    // console.log(url);
                    window.location=url;
                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text: 'Kalkulasi',
                handler: function(){

                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [
            'Total : '
            ]
        }],
        width: '100%',
        height: '100%'
        // ,
        // renderTo: Ext.getBody()
        // ,
        // bbar: [Ext.create('Ext.toolbar.Paging', {
        //                      pageSize: 50,
        //                      store: store,
        //                      displayInfo: true
        //              })
        // ]
    });

    var viewport = Ext.create('Ext.Viewport', {
        layout: {
            type: 'border',
        },
        defaults: {
            split: true
        },
        items: [{
            region: 'north', 
            height: '100%',       
            border: 0,
            layout: 'fit',
            items: grid
        }]
    });
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>