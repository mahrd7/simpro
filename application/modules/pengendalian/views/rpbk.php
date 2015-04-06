<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript">

Ext.require([
    '*'
]);

Ext.Ajax.timeout = 3600000;

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
        	{name: 'subbidang_kode', mapping: 'subbidang_kode'},
            {name: 'subbidang_name', mapping: 'subbidang_name'},
            {name: 'user_update', mapping: 'user_update'},            
            {name: 'tgl_update', mapping: 'tgl_update'},
            {name: 'ip_update', mapping: 'ip_update'},
            {name: 'divisi_update', mapping: 'divisi_update'},
            {name: 'waktu_update', mapping: 'waktu_update'},
            {name: 'urutan', mapping: 'urutan'}
         ]
    });

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
         url: '<?php echo base_url() ?>pengendalian/pengendalian/getsubbidang',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

Ext.onReady(function() {

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        title: 'RENCANA POSISI BIAYA KONSTRUKSI <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
            Ext.create('Ext.grid.RowNumberer'),
            {text: "URAIAN PEKERJAAN", width:400, sortable: true, dataIndex: 'subbidang_name'},
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/edit.png',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var kode = rec.get('subbidang_kode');
            	window.location='<?php echo base_url() ?>pengendalian/edit_rpbk/'+kode+'/<?php echo $bln."/".$thn ?>';
            }}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    var url ='<?php echo base_url(); ?>pengendalian/pilih_rp_beban_kontrak';
                    // console.log(url);
                    window.location=url;
                }
            }]
        }],
        width: '100%',
        height: '100%'
        // ,
        // renderTo: Ext.getBody()
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