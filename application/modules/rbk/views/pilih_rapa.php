<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

<script type="text/javascript">
Ext.require([
    '*'
]);

	Ext.define('analisa_rapa', {
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
                mapping: 'status'},
			{
                name: 'status_del',
                mapping: 'status_del'},
			{
                name: 'status_app',
                mapping: 'status_app'}
         ]
    });

Ext.onReady(function() {

var store_rapa = Ext.create('Ext.data.Store', {
        id: 'store_rapa',
        model: 'analisa_rapa',
        pageSize: 50,     
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getdata/rapa',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        remoteFilter: true,
        autoLoad: false
    });
    store_rapa.load();

var grid_rapa = Ext.create('Ext.grid.Panel', {
        id:'grid_rapa',
        store: store_rapa,
        columns: [
            {text: "BULAN", flex:1, sortable: true, dataIndex: 'bulan'},
            {text: "TAHUN", flex:1, sortable: true, dataIndex: 'tahun'},
            {text: "STATUS", flex:1, sortable: true, dataIndex: 'status'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid,rowIndex,colIndex){
                rec = store_rapa.getAt(rowIndex);
                var bln = rec.get('bulan');
                var thn = rec.get('tahun');
                window.location='<?php echo base_url() ?>rbk/rapa/'+bln+"/"+thn;
            }
        },{text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid,rowIndex,colIndex){
                rec = storedummy.getAt(rowIndex);
				if (rec.get('status_del') == "1" ){
					Ext.Msg.alert( "Status", "Maaf Laporan Bulanan telah di APPROVE, Anda harus menghapus approval untuk Acces Kontrol!")
                
				}else{
					var bln = rec.get('bulan');
					var thn = rec.get('tahun');
					window.location='<?php echo base_url() ?>rbk/deletedata/pilih_rapa/'+bln+"/"+thn;
				}
            }
        },{text: "",xtype: 'actioncolumn', width:55,  sortable: true,icon:'<?=base_url();?>assets/images/application_go.png',
            handler: function(grid,rowIndex,colIndex){
                rec = storedummy.getAt(rowIndex);
                var bln = rec.get('bulan');
                var thn = rec.get('tahun');
				if(rec.get('status_del') == "1" ){
				//edit
				}else{
				
				}
            }
        }
        ],
        columnLines: true,
        width: '100%',
        height: '100%',
        title: 'RAPA',
        renderTo: Ext.getBody()
    });

});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>
