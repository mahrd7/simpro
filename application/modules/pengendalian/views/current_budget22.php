<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript">

Ext.require([
    '*'
]);

    Ext.define('dummydatast', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'id'
            },
            {
                name: 'uraian'
            },
            {
                name: 'satuan'
            },
            {
                name: 'volume'
            },
            {
                name: 'harga_satuan'
            },
            {
                name: 'jumlah_harga'
            }
         ]
    });

    var dummydata = [
        ['1','Bahan','ls','5','1000','5000']
    ];

	Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
        	{name: 'tahap_kode_kendali', mapping: 'tahap_kode_kendali'},
            {name: 'tahap_nama_kendali', mapping: 'tahap_nama_kendali'},
            {name: 'tahap_satuan_kendali', mapping: 'tahap_satuan_kendali'},            
            {name: 'tahap_volume_kendali', mapping: 'tahap_volume_kendali'},
            {name: 'tahap_harga_satuan_kendali', mapping: 'tahap_harga_satuan_kendali'},
            {name: 'tahap_total_kendali', mapping: 'tahap_total_kendali'},
            {name: 'tahap_kode_induk_kendali', mapping: 'tahap_kode_induk_kendali'},
            {name: 'tahap_tanggal_kendali', mapping: 'tahap_tanggal_kendali'},
            {name: 'tahap_volume_kendali_new', mapping: 'tahap_volume_kendali_new'},
            {name: 'tahap_total_kendali_new', mapping: 'tahap_total_kendali_new'},
            {name: 'tahap_harga_satuan_kendali_new', mapping: 'tahap_harga_satuan_kendali_new'},
            {name: 'tahap_volume_kendali_kurang', mapping: 'tahap_volume_kendali_kurang'},
            {name: 'tgl_rencana_aak', mapping: 'tgl_rencana_aak'},            
            {name: 'volume_rencana', mapping: 'volume_rencana'},
            {name: 'volume_rencana1', mapping: 'volume_rencana1'},
            {name: 'volume_eskalasi', mapping: 'volume_eskalasi'},
            {name: 'harga_satuan_eskalasi', mapping: 'harga_satuan_eskalasi'},
            {name: 'rencana_volume_eskalasi', mapping: 'rencana_volume_eskalasi'},            
            {name: 'rencana_harga_satuan_eskalasi', mapping: 'rencana_harga_satuan_eskalasi'},       
            {name: 'is_nilai', mapping: 'is_nilai'},
            {name: 'tahap_total_kendali_kurang', mapping: 'tahap_total_kendali_kurang'},  
            {name: 'total_tambah_kurang', mapping: 'total_tambah_kurang'},
            {name: 'total_volume_rencana', mapping: 'total_volume_rencana'},
            {name: 'tot_rencana1', mapping: 'tot_rencana1'},
            {name: 'tot_rencana2', mapping: 'tot_rencana2'},
            {name: 'vol_tambah_kurang', mapping: 'vol_tambah_kurang'},
            
            {name: 'no_spk', mapping: 'no_spk'},
            {name: 'user_update', mapping: 'user_update'},       
            {name: 'tgl_update', mapping: 'tgl_update'},
            {name: 'ip_update', mapping: 'ip_update'},  
            {name: 'divisi_update', mapping: 'divisi_update'},
            {name: 'waktu_update', mapping: 'waktu_update'}
         ]
    });

Ext.onReady(function() {

    var storedummy = Ext.create('Ext.data.ArrayStore', {
        model: 'dummydatast',
        data: dummydata
    });

	var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getdata/kontrak_kini',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        title: 'CURRENT BUDGET <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "ITEM PEKERJAAN", width:400, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "SATUAN", flex:1, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "VOLUME SISA ANGGARAN", flex:1, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "HARGA SATUAN", flex:1, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "TOTAL HARGA", flex:1, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "Kontrol",
        	columns:[
        	{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/tomboledit.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = storedummy.getAt(rowIndex);
            }},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/tomboldel.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = storedummy.getAt(rowIndex);
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
											   																			
							}
				});
            }},{text: "",xtype: 'actioncolumn', width:28,icon:'<?=base_url();?>assets/images/tombollog.gif',
            handler: function(grid, rowIndex, colIndex){
            }},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/tombolplus.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = storedummy.getAt(rowIndex);
            }}
        	]}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'RAPI',
                tooltip:'RAPI',
                handler: function(){

                }
            },'-',{
                text:'Edit Harga Satuan',
                tooltip:'Edit Harga Satuan',
                handler: function(){

                }
            },'-',{
                text:'Print Analisa',
                tooltip:'Print Analisa',
                handler: function(){

                }
            },'-',{
                text:'Print Tahap',
                tooltip:'Print Tahap',
                handler: function(){

                }
            },'-',{
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    var url ='<?php echo base_url(); ?>pengendalian/pilihcurrentbudget';
                    // console.log(url);
                    window.location=url;
                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
            	text: 'Copy Dari Original Budget',
            	handler: function(){

            	}
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [                
            'Total: '
            ]
        }],
        width: '100%',
        height: '100%',
        renderTo: Ext.getBody()
        // ,
       	// bbar: [Ext.create('Ext.toolbar.Paging', {
        //                      pageSize: 50,
        //                      store: store,
        //                      displayInfo: true
        //              })
        // ]
    });
    store.load();
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>