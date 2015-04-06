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

    Ext.define('mdl_rkk', {
        extend: 'Ext.data.Model',
        fields: [
        {name:'id', mapping:'id_kontrak_terkini'},
        {name:'kk_kode', mapping:'tahap_kode_kendali'},
        {name:'kk_uraian_pekerjaan', mapping:'tahap_nama_kendali',
        convert: function(value,record){
            kode = record.get('kk_kode');            
            volume = record.get('kk_volume');
            if (volume == 0){
                val = '<span class="link">'+value+'</span>';
                // alert('test');
            } else if (kode.length == 1){
                val = '<b><span class="link">'+value+'</span></b>';
                // alert('test');
            } else {
                val = value;
            }
            return val;
        }
        },
        {name:'kk_satuan', mapping:'satuan_nama'},
        {name:'kk_volume', mapping:'tahap_volume_kendali'},
        {name:'kk_harga_kontrak', mapping:'tahap_harga_satuan_kendali'},
        {name:'kk_total', mapping:'total'},
        {name:'pt_volume', mapping:'volume_rencana'},
        {name:'pt_jumlah', mapping:'jumlah'},
        {name:'pk_volume', mapping:'volume_rencana1'},
        {name:'pk_jumlah', mapping:'jumlah1'},
        {name:'eks_volume', mapping:'rencana_volume_eskalasi'},
        {name:'eks_satuan', mapping:'harga_satuan_eskalasi'},
        {name:'eks_jumlah', mapping:'jumlah_eskalasi'}
        ]
    });

    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl_rkk',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url() ?>pengendalian/get_data_rencana_kontrak',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

Ext.onReady(function() {
    store.load({
        params:{
            'tgl_rab':'<?php echo $tgl_rab ?>'
        }
    });
 
        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            beforeedit: function(grid,obj){   
                if (obj.record.get('kk_kode').length == 1) {
                    return false;
                }
            },
            afteredit: function(rec,obj) {
                var selectedNode = grid.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                id = data.id;

                Ext.Ajax.request({
                     url: '<?=base_url();?>pengendalian/update_data/rencana_kontrak_kini',
                        method: 'POST',
                        params: {
                            'id' :  id,
                            'data1': data.pt_volume, 
                            'data2': data.pk_volume,
                            'data3': data.eks_volume,
                            'data4': data.eks_satuan
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

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        plugins: [rowEditing],
        title: 'RENCANA KONTRAK KINI <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
        {text:"KONTRAK TERKINI <?php echo $bln; ?> Tahun <?php echo $thn; ?>",
        	columns:[
        	{text: "NO", width:50, sortable: true, dataIndex: 'kk_kode'},
            {text: "ITEM PEKERJAAN", width:300, sortable: true, dataIndex: 'kk_uraian_pekerjaan'},
            {text: "SATUAN", sortable: true, dataIndex: 'kk_satuan'},
            {text: "VOLUME", sortable: true, dataIndex: 'kk_volume'},
            {text: "HARGA", sortable: true, dataIndex: 'kk_harga_kontrak'},
            {text: "TOTAL", sortable: true, dataIndex: 'kk_total'}
        	]
        },{text:"PEKERJAAN TAMBAH <?php echo $bln1; ?> Tahun <?php echo $thn; ?>",
        	columns:[
        	{text: "VOLUME", width:150, sortable: true, dataIndex: 'pt_volume',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "JUMLAH", width:150, sortable: true, dataIndex: 'pt_jumlah'},
            ]
        },{text:"PEKERJAAN KURANG <?php echo $bln1; ?> Tahun <?php echo $thn; ?>",
        	columns:[
        	{text: "VOLUME", width:150, sortable: true, dataIndex: 'pk_volume',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "JUMLAH", width:150, sortable: true, dataIndex: 'pk_jumlah'},
            ]
        },{text:"ESKALASI <?php echo $bln1; ?> Tahun <?php echo $thn; ?>",
        	columns:[
        	{text: "VOLUME", sortable: true, dataIndex: 'eks_volume',
                editor:{
                    xtype:'numberfield'
                }
            },
        	{text: "HARGA SATUAN", sortable: true, dataIndex: 'eks_satuan',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "JUMLAH", sortable: true, dataIndex: 'eks_jumlah'},
            ]
        }
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
                    var url ='<?php echo base_url(); ?>pengendalian/pilih_rencana_kontrak_kini';
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
            'Total Kontrak Terini : ','-',
            'Total Pekerjaan Tambah : ','-',
            'Total Pekerjaan Kurang : ','-',
            'Total Ekskalasi : ','-',
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