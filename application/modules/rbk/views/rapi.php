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
<?php
$proyek_id = "1";
//$proyek_id = $this->session->userdata('proyek_id');
$query = "select sum(komposisi_volume_total_kendali+komposisi_harga_satuan_kendali) as total from simpro_tbl_komposisi_kendali a 
inner join simpro_tbl_detail_material b on a.detail_material_id = b.detail_material_id 
inner join simpro_tbl_subbidang c on b.subbidang_kode = c.subbidang_kode 
where a.proyek_id = $proyek_id and tahap_tanggal_kendali = (select distinct(tahap_tanggal_kendali) from simpro_tbl_input_kontrak where proyek_id =$proyek_id ) 
";
$result = $this->db->query($query);
echo "var totalall = ";
echo $result->row()->total?$result->row()->total:0;
echo ";";
echo "var persentase = ";
echo $result->row()->total?$result->row()->total:0;
echo ";";
?>
var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

Ext.require([
    '*'
]);
	
    Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'komposisi_kendali_id', mapping: 'komposisi_kendali_id'},
            {name: 'detail_material_kode', mapping: 'detail_material_kode'},
            {name: 'detail_material_nama', mapping: 'detail_material_nama'},            
            {name: 'detail_material_satuan', mapping: 'detail_material_satuan'},
            {name: 'komposisi_volume_total_kendali', mapping: 'komposisi_volume_total_kendali'},
            {name: 'komposisi_harga_satuan_kendali', mapping: 'komposisi_harga_satuan_kendali'},
            {name: 'total', mapping: 'total'},
            {name: 'keterangan', mapping: 'keterangan'},
            {name: 'kode_simpro', mapping: 'kode_simpro'},
            {name: 'totalall', mapping: 'totalall'}
         ]
    });

    Ext.define('mdl_combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value'}
         ]
    });

	var storebln = Ext.create('Ext.data.Store', {
        id: 'storebln',
        model: 'mdl_combo',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getbulan',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    storebln.load();

    var storethn = Ext.create('Ext.data.Store', {
        id: 'storethn',
        model: 'mdl_combo',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/gettahun',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    storethn.load();

    var storesatuan = Ext.create('Ext.data.Store', {
        id: 'storectg',
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getlistsatuan',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

    var dummy = [{
        "id":"",
        "no":"",
        "nama":"",
        "satuan":"",
        "volume":"",
        "harga":"",
        "keterangan":"",
        "kode_simpro":""
    }
    ];

    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        groupField:'detail_material_kode',
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getdata/rapi',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });
	store.load();

Ext.onReady(function() {
	 var groupingFeature = Ext.create('Ext.grid.feature.Grouping',{
        groupHeaderTpl: 'Kode RAP: {name} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',		
            id: 'group',
            ftype: 'groupingsummary',        
            //hideGroupedHeader: true,
            //enableGroupingMenu: false        
    });
	function prosesSearch(){
		store.load({
			//method:'POST',
			params:{bulan:Ext.getCmp('cmbbulan').getValue(),tahun:Ext.getCmp('cmbtahun').getValue()}
		});
		Ext.getCmp('txtTotal').setText('Total Keseluruhan : '+store.getAt(0).get('totalall'));
		Ext.getCmp('txtPersentase').setText('Prosentase Thd Kontrak : '+store.getAt(0).get('totalall'));
		return;
	}
    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        title: 'RAPI',
        columns: [
            {text: "", width:100,hidden:true, sortable: true, dataIndex: 'komposisi_kendali_id'},
            {text: "KODE RAP", width:100, sortable: true, dataIndex: 'detail_material_kode', hidden:true},
            {text: "NAMA", width:400, sortable: true, dataIndex: 'detail_material_nama'},
            {text: "SATUAN", width:100, sortable: true, dataIndex: 'detail_material_satuan'},
            {text: "VOLUME", width:100, sortable: true, dataIndex: 'komposisi_volume_total_kendali'},
            {text: "HARGA SATUAN", width:200, sortable: true, dataIndex: 'komposisi_harga_satuan_kendali',summaryType: 'sum',            
            summaryRenderer: function(value, summaryData, dataIndex) {
                return "TOTAL" ;
            }},
            {text: "JUMLAH", width:250, sortable: true, dataIndex: 'total',summaryType: 'sum',            
            summaryRenderer: function(value, summaryData, dataIndex) {
                return Ext.util.Format.number(value,'0,000.00') ;
            }},
            {text: "Keterangan", width:100, sortable: true, dataIndex: 'keterangan'},
            {text: "KODE SIMPRO", width:100, sortable: true, dataIndex: 'kode_simpro'}
        ],
		features: [{
            id: 'group',
            ftype: 'groupingsummary',
            groupHeaderTpl: 'Kode RAP: {name} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
            hideGroupedHeader: true,
            enableGroupingMenu: false
        }],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text:'Print',
                tooltip:'Print',
                handler: function(){

                }
            }]
        },{
            xtype: 'toolbar',
            dock: 'top',
            items: [
            'URAIAN BIAYA : ',{
            xtype: 'combobox',
            id: 'cmbbulan',
            name: 'bulan',
            store: storebln,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih Bulan..',
            width: 120
            },{
            xtype: 'combobox',
            id: 'cmbtahun',
            name: 'tahun',
            store: storethn,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih Tahun..',
            width: 100
            },{
                text: 'Go>>',
				handler : function(){
					prosesSearch();
				}
            }]
        }],
        width: '100%',
        height: '100%',
        renderTo: Ext.getBody()
        ,
        bbar: [{
				xtype: 'label',
				//forId: 'myFieldId',
				id : 'txtTotal',
				text: 'Total Keseluruhan : '+totalall,
				margins: '0 0 0 10'
			},{
				xtype: 'label',
				//forId: 'myFieldId',
				id : 'txtPersentase',
				text: 'Prosentase Thd Kontrak : '+persentase,
				margins: '0 0 0 10'
			}
         ]
    });
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>