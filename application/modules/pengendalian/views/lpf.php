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

var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

Ext.require([
    '*'
]);

    Ext.define('mdl_lpf_new', {
        extend: 'Ext.data.Model',
        fields: [
        {name:'id_tahap_pekerjaan', mapping:'id_tahap_pekerjaan'},
        {name:'tahap_kode_kendali', mapping:'tahap_kode_kendali'},
        {name:'tahap_nama_kendali', mapping:'tahap_nama_kendali'},
        {name:'tahap_satuan_kendali', mapping:'tahap_satuan_kendali'},
        {name:'vol_kk', mapping:'vol_kk'},
        {name:'tahap_harga_satuan_kendali', mapping:'tahap_harga_satuan_kendali', type:'float'},
        {name:'jml_lpf_kini', mapping:'jml_lpf_kini', type:'float'},
        {name:'jlm_sd_bln_lalu', mapping:'jlm_sd_bln_lalu'},
        {name:'tahap_diakui_bobot', mapping:'tahap_diakui_bobot'},
        {name:'jlm_sd_bln_ini', mapping:'jlm_sd_bln_ini'},
        {name:'vol_total_tagihan', mapping:'vol_total_tagihan'},
        {name:'jml_tagihan', mapping:'jml_tagihan', type:'float'},
        {name:'vol_bruto', mapping:'vol_bruto'},
        {name:'jml_bruto', mapping:'jml_bruto', type:'float'},
        {name:'tagihan_cair', mapping:'tagihan_cair'},
        {name:'jml_cair', mapping:'jml_cair', type:'float'},
        {name:'vol_sisa_pekerjaan', mapping:'vol_sisa_pekerjaan'},
        {name:'jml_sisa_pekerjaan', mapping:'jml_sisa_pekerjaan', type:'float'},
        {name:'tagihan_rencana_piutang', mapping:'tagihan_rencana_piutang'},
        {name: 'ishaschild', mapping: 'ishaschild'}
        ]
    });

    Ext.define('mdl_mos_new', {
        extend: 'Ext.data.Model',
        fields: [
        {name:'mos_uraian', mapping:'mos_uraian'},
        {name:'mos_satuan', mapping:'mos_satuan'},
        {name:'mos_diakui_volume', mapping:'mos_diakui_volume'},
        {name:'mos_diakui_harsat', mapping:'mos_diakui_harsat'},
        {name:'mos_diakui_jumlah', mapping:'mos_diakui_jumlah'}
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

    var store_lpf_new = Ext.create('Ext.data.TreeStore', {
        model: 'mdl_lpf_new',
        expanded: true,
        autoLoad: false,
        proxy: {
            timeout: 900000,
            async: false,
            type: 'ajax',
            url: '<?php echo base_url() ?>pengendalian/get_data_total_pekerjaan',
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

    var store_mos_new = Ext.create('Ext.data.Store', {
        model: 'mdl_mos_new',
        remoteSort: true,
        autoLoad: false,  
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url() ?>pengendalian/get_data_mos',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });


Ext.onReady(function() { 
    
    store_mos_new.load();

    store_lpf_new.load({
        params:{
            'tgl_rab':'<?php echo $tgl_rab ?>'
        }
    });  

    var cellEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            beforeedit: function(rec,obj){   
                if (obj.record.get('ishaschild') == 1) {
                    return false;
                }
            },
            afteredit: function(rec,obj) {
                var selectedNode = grid.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                id = data.id_tahap_pekerjaan;
                kode = data.tahap_kode_kendali;
                tahap_diakui_bobot = data.tahap_diakui_bobot;
                vol_total_tagihan = data.vol_total_tagihan;
                tagihan_cair = data.tagihan_cair;
                tagihan_rencana_piutang = data.tagihan_rencana_piutang;

                Ext.Ajax.request({
                     url: '<?=base_url();?>pengendalian/update_lpf',
                        method: 'POST',
                        params: {
                            'id' :  id,
                            'tahap_diakui_bobot': tahap_diakui_bobot,
                            'vol_total_tagihan': vol_total_tagihan,
                            'tagihan_cair': tagihan_cair,
                            'tagihan_rencana_piutang': tagihan_rencana_piutang,
                            'kode': kode,
                            'tgl_rab': '<?php echo $tgl_rab ?>'
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!", function(){  
                    }); 
                    store_lpf_new.load({
                                    params: {
                                        'tgl_rab':'<?php echo $tgl_rab; ?>'
                                    }
                                }        
                                );                                        
                    },
                    failure: function() {

                    }
                }); 
                // console.log(id+"+"+value+"+"+data);
            }   
        }
    });

    var grid_diakui = Ext.create('Ext.grid.Panel', {
        autoScroll: true,
        store: store_mos_new,
        columns: [
            {text: "URAIAN", flex:1, sortable: true, dataIndex: 'mos_uraian'},
            {text: "SATUAN", sortable: true, dataIndex: 'mos_satuan',
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
            {text: "PERSEDIAAN DIAKUI OWNER",
            columns: [
                {text: "VOLUME", flex:1, sortable: true, dataIndex: 'mos_diakui_volume'},
                {text: "HARGA SATUAN", flex:1, sortable: true, dataIndex: 'mos_diakui_harsat'},
                {text: "JUMLAH HARGA", flex:1, sortable: true, dataIndex: 'mos_diakui_jumlah'}
            ]
        }
        ],
        columnLines: true,
        dockedItems: [{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [                
            'Total : ',
            'Presentase : '
            ]
        }],
        title: 'Progress Persediaan Diakui'
        // renderTo: Ext.getBody()
    });

    var grid = Ext.create('Ext.tree.Panel', {
        store: store_lpf_new,
        rootVisible: false,
        multiSelect: false,
        singleExpand: false,
        hideCollapseTool: false,
        autoscroll: true,
        title: 'Laporan Progress Fisik ( FM LPF-01 )  Tahun ',
        plugins: [cellEditing],
        columns: [
            {text: "LAPORAN PROGRES FISIK", sortable: false,
            columns:[
                {text: "KODE", xtype: 'treecolumn', width:100, sortable: true, dataIndex: 'tahap_kode_kendali'},
                {text: "URAIAN PEKERJAAN", flex:1, sortable: true, dataIndex: 'tahap_nama_kendali'},
                {text: "SAT", flex:1, sortable: true, dataIndex: 'tahap_satuan_kendali',
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
                {text: "VOLUME", flex:1, sortable: true, dataIndex: 'vol_kk'},
                {text: "HARGA KONTRAK", flex:1, sortable: true, dataIndex: 'tahap_harga_satuan_kendali'},
                {text: "JUMLAH", flex:1, sortable: true, dataIndex: 'jml_lpf_kini'}
            ]
        },
            {text: "VOLUME PROGRESS AKTUAL", sortable: false,
            columns:[
                {text: "Progress S/D BLN LALU", flex:1, sortable: true, dataIndex: 'jlm_sd_bln_lalu'},
                {text: "Progress BLN INI", flex:1, sortable: true, dataIndex: 'tahap_diakui_bobot',
                    field: {
                            xtype: 'numberfield'
                        }
                },
                {text: "Progress S/D BLN INI", flex:1, sortable: true, dataIndex: 'jlm_sd_bln_ini'}
            ]
        },
            {text: "TOTAL TAGIHAN", sortable: false,
            columns:[
                {text: "VOLUME", flex:1, sortable: true, dataIndex: 'vol_total_tagihan',
                    field: {
                            xtype: 'numberfield'
                        }
                },
                {text: "JUMLAH", flex:1, sortable: true, dataIndex: 'jml_tagihan'}
            ]
        },
            {text: "TAGIHAN BRUTO", sortable: false,
            columns:[
                {text: "VOLUME", flex:1, sortable: true, dataIndex: 'vol_bruto'},
                {text: "JUMLAH", flex:1, sortable: true, dataIndex: 'jml_bruto'}
            ]
        },
            {text: "TAGIHAN CAIR", sortable: false,
            columns:[
                {text: "VOLUME", flex:1, sortable: true, dataIndex: 'tagihan_cair',
                    field: {
                        xtype: 'numberfield'
                    }
                },
                {text: "JUMLAH", flex:1, sortable: true, dataIndex: 'jml_cair'}
            ]
        },
            {text: "SISA PEKERJAAN", sortable: false,
            columns:[
                {text: "VOLUME", flex:1, sortable: true, dataIndex: 'vol_sisa_pekerjaan'},
                {text: "JUMLAH", flex:1, sortable: true, dataIndex: 'jml_sisa_pekerjaan'}
            ]
        },
            {text: "RENCANA TAGIHAN<br>PIUTANG", width:120, sortable: true, dataIndex: 'tagihan_rencana_piutang',
                    field: {
                        xtype: 'numberfield'
                    }
            }
        ],
        columnLines: true,
        dockedItems: [{
            // dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text:'Print',
                tooltip:'Print',
                handler: function(){

                }
            },{
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    var url ='<?php echo base_url(); ?>pengendalian/pilih_lpf';
                    // console.log(url);
                    window.location=url;
                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [                
            'Total A : ',
            'Total B : ',
            'Prosentase Thd Kontrak : '
            ]
        }],
        listeners:{
            afterrender: function(){

            }
        }
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
            height: '60%',       
            border: 0,
            layout: 'fit',
            items: grid
        },{
            region: 'north',
            layout: 'fit',
            height: '40%',   
            border: 0,
            items: grid_diakui
        }]
    });
    // grid.render(document.body);
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>