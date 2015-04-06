<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

<script type="text/javascript">

Ext.require([
    '*'
]);

	Ext.define('analisa_lap_penanganan', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'id',
                mapping: 'id'
            },
            {
                name: 'bulan',
                mapping: 'bulan'
            },
            {
                name: 'tahun',
                mapping: 'tahun'},
            {
                name: 'status',
                mapping: 'status'}
         ]
    });

    

Ext.onReady(function() {


var store_lap_penanganan = Ext.create('Ext.data.Store', {
        model: 'analisa_lap_penanganan',
        pageSize: 50,     
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>mutu_resiko/getdata/lap_penanganan',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        remoteFilter: true,
        autoLoad: false
    });
    store_lap_penanganan.load();

var grid_lap_penanganan = Ext.create('Ext.grid.Panel', {
        title: 'Laporan Penanganan',
        store: store_lap_penanganan,
        columns: [
            {text: "BULAN", flex:1, sortable: true, dataIndex: 'bulan'},
            {text: "TAHUN", flex:1, sortable: true, dataIndex: 'tahun'},
            {text: "STATUS", flex:1, sortable: true, dataIndex: 'status'},
			/*{text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = store.getAt(rowIndex);
            }
        },*/
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/cog.gif',
            handler: function(grid,rowIndex,colIndex){
                rec = store_lap_penanganan.getAt(rowIndex);
                var bln = rec.get('bulan');
                var thn = rec.get('tahun');
                window.location='<?php echo base_url() ?>mutu_resiko/lap_penanganan/'+bln+'/'+thn;
            }
        }
        ],
        columnLines: true,

        width: '100%',
        height: '100%',
        renderTo: Ext.getBody()
    });

});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>
