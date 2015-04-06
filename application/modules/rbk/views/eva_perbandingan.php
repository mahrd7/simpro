<html>
<head>
<style type="text/css">
.link {
    text-decoration: none;
    color: rgb(11, 100, 214);
}
</style>
<style type="text/css">
p {
    margin:5px;
}

.footer {
font-size: 10px;
font-family: 'Arial'
}

.x-change-cell .yellow .x-grid-cell-inner{
    background-color: #EEEE11;
}

.x-change-cell .blue .x-grid-cell-inner{
    background-color: #27CBFF;
}

.x-change-cell .green .x-grid-cell-inner{
    background-color: #48CF36;
}

.icon-new {
	background: url(<?php echo base_url(); ?>assets/images/new-icon.png) no-repeat 0 -1px;
}

.new-tab {
    background-image:url(<?php echo base_url(); ?>assets/images/new_tab.gif) !important;
}

.icon-add {
    background-image:url(<?php echo base_url(); ?>assets/images/add.gif) !important;
}

.icon-del {
    background-image:url(<?php echo base_url(); ?>assets/images/delete.png) !important;
}
.icon-copy {
    background-image:url(<?php echo base_url(); ?>assets/images/copy.png ) !important;
}
.icon-paste {
    background-image:url(<?php echo base_url(); ?>assets/images/paste.png ) !important;
}

.tabs {
    background-image:url(<?php echo base_url(); ?>assets/images/tabs.gif ) !important;
}

.icon-back {
    background-image:url(<?php echo base_url(); ?>assets/images/back.png) !important;
}

.icon-table {
    background-image:url(<?php echo base_url(); ?>assets/images/table.png) !important;
}

.icon-print {
    background-image:url(<?php echo base_url(); ?>assets/images/print.png) !important;
}

.icon-reload {
    background-image:url(<?php echo base_url(); ?>assets/images/reload.png) !important;
}

.task .x-grid-cell-inner {
    padding-left: 15px;
}
.x-grid-row-summary .x-grid-cell-inner {
    font-weight: bold;
    font-size: 11px;
}

.icon-grid {
    background: url(<?php echo base_url(); ?>assets/images/grid.png) no-repeat 0 -1px;
}

.msg .x-box-mc {
    font-size:14px;
}

#msg-div {
    position:absolute;
    left:35%;
    top:10px;
    width:300px;
    z-index:999999;
}

#msg-div .msg {
    border-radius: 8px;
    -moz-border-radius: 8px;
    background: #F6F6F6;
    border: 2px solid #ccc;
    margin-top: 2px;
    padding: 10px 15px;
    color: #555;
}

#msg-div .msg h3 {
    margin: 0 0 8px;
    font-weight: bold;
    font-size: 15px;
}

#msg-div .msg p {
    margin: 0;
}   
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

<script type="text/javascript">

Ext.require([
    '*'
]);

	/*
	Ext.define('mdl_eva', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'no', mapping: 'no'},
            {name: 'item_pekerjaan', mapping: 'item_pekerjaan'},
            {name: 'satuan', mapping: 'satuan'},
            {name: 'rab_volume', mapping: 'rab_volume'},
            {name: 'rab_harga_satuan', mapping: 'rab_harga_satuan'},
            {name: 'rab_jumlah_harga', mapping: 'rab_jumlah_harga'},
            {name: 'rat_volume', mapping: 'rat_volume'},
            {name: 'rat_harga_satuan ', mapping: 'rat_harga_satuan'},
            {name: 'rat_jumlah_harga', mapping: 'rat_jumlah_harga'},
            {name: 'rat_persen', mapping: 'rat_persen'},
            {name: 'rapi_volume', mapping: 'rapi_volume'},
            {name: 'rapi_harga_satuan', mapping: 'rapi_harga_satuan'},
            {name: 'rapi_jumlah_harga', mapping: 'rapi_jumlah_harga'},
            {name: 'rapi_persen', mapping: 'rapi_persen'}
         ]
    });

    var dummyeva =[
        {
            "no":"1",
            "item_pekerjaan":"",
            "satuan":"",
            "rab_volume":"",
            "rab_harga_satuan":"",
            "rab_jumlah_harga":"",
            "rat_volume":"",
            "rat_harga_satuan":"",
            "rat_jumlah_harga":"",
            "rat_persen":"",
            "rapi_volume":"",
            "rapi_harga_satuan":"",
            "rapi_jumlah_harga":"",
            "rapi_persen":""
        },{
            "no":"2",
            "item_pekerjaan":"",
            "satuan":"",
            "rab_volume":"",
            "rab_harga_satuan":"",
            "rab_jumlah_harga":"",
            "rat_volume":"",
            "rat_harga_satuan":"",
            "rat_jumlah_harga":"",
            "rat_persen":"",
            "rapi_volume":"",
            "rapi_harga_satuan":"",
            "rapi_jumlah_harga":"",
            "rapi_persen":""
        }
    ];
	*/
	
Ext.onReady(function() {

	Ext.define('mdl_eva', {
		extend: 'Ext.data.Model',
		fields: [ 
			'kode_tree','tree_item','tree_satuan','harga','volume','subtotal',
			'harga_rat','volume_rat','subtotal_rat',
			'harga_rab','volume_rab','subtotal_rab'			
		],
	});
	
	var store = Ext.create('Ext.data.Store', {
		 model: 'mdl_eva',
		 proxy: {
			type: 'ajax',
			//url: '<?=base_url();?>rbk/get_evaluasi_rab_rat_rapi/<?=$id_proyek;?>',
			url: '<?=base_url();?>rbk/rbk_analisa/get_task_tree_items/<?=$id_proyek;?>',
			reader: {
				type: 'json',
				root: 'data',
				totalProperty: 'total'
			},
			simpleSortMode: true				 
		 },
		autoLoad: false,
		remoteSort: true,
		pageSize: 1000,
		sorters: [{
			property: 'item_pekerjaan',
			direction: 'DESC'
		}]			
	});			
	
	var grid = Ext.create('Ext.grid.Panel', {
			store: store,
			autoscroll: true,
			title: 'EVALUASI PERBANDINGAN ANTARA RAT, RAB DAN RAPI (FM-EVA-T01)',
			columns: [
				{
					text: "NO", 			
					xtype: 'rownumberer',
					width: 35,
					sortable: false
				},
				//{text: "NO", width:50, sortable: true, dataIndex: 'no'},
				{text: "ITEM PEKERJAAN", width:400, sortable: true, dataIndex: 'tree_item'},
				{text: "SATUAN", width:100, sortable: true, dataIndex: 'tree_satuan'},
				{text: "RENCANA ANGGARAN TENDER (RAT)", 
				columns: [
					{text: "VOLUME", width:100, sortable: true, dataIndex: 'volume_rat',
						renderer: Ext.util.Format.numberRenderer('00,000')},
					{text: "HARGA SATUAN", width:100, align: 'right', sortable: true, dataIndex: 'harga_rat',
						renderer: Ext.util.Format.numberRenderer('00,000')},
					{text: "JUMLAH HARGA", width:100, align: 'right', sortable: true, dataIndex: 'subtotal_rat',
						renderer: Ext.util.Format.numberRenderer('00,000'),
						tdCls: 'green'
					},
					{text: "%", width:100, sortable: true, dataIndex: 'persen_rat',
						renderer: Ext.util.Format.numberRenderer('00,000'),
						tdCls: 'green'
					}
				]},
				{text: "RAB / KONTRAK", 
				columns: [
					{text: "VOLUME", width:100, sortable: true, align: 'center', dataIndex: 'volume_rab',
						renderer: Ext.util.Format.numberRenderer('00,000')},
					{text: "HARGA SATUAN", width:100, align: 'right', sortable: true, dataIndex: 'harga_rab',
						renderer: Ext.util.Format.numberRenderer('00,000')},
					{text: "JUMLAH HARGA", width:100, align: 'right', sortable: true, dataIndex: 'subtotal_rab',
						renderer: Ext.util.Format.numberRenderer('00,000'),
						tdCls: 'yellow'
					},
					{text: "%", width:100, sortable: true, dataIndex: 'persen_rab',
						renderer: Ext.util.Format.numberRenderer('00,000'),
						tdCls: 'yellow'
					}
				]},
				{text: "USULAN RENCANA ANGGARAN PELAKSANAAN INDUK (RAPI)", 
				columns: [
					{text: "VOLUME", width:100, sortable: true, align: 'center', dataIndex: 'volume',
						renderer: Ext.util.Format.numberRenderer('00,000')},
					{text: "HARGA SATUAN", width:100, align: 'right', sortable: true, dataIndex: 'harga',
						renderer: Ext.util.Format.numberRenderer('00,000')},
					{text: "JUMLAH HARGA", width:100, align: 'right', sortable: true, dataIndex: 'subtotal',
						renderer: Ext.util.Format.numberRenderer('00,000'),
						tdCls: 'blue'
					},
					{text: "%", width:100, sortable: true, align: 'center', dataIndex: 'persen_rap',
						renderer: Ext.util.Format.numberRenderer('00,000'),
						tdCls: 'blue'
					}
				]}
			],
	        viewConfig: {
	        getRowClass: function(record, index) {

					return 'x-change-cell';
	                
	            }
	        },
			dockedItems: [{
				xtype: 'toolbar',
				dock: 'top',
				items: [{
                    text:'Export Evaluasi Perbandingan',
                    iconCls:'icon-print',
                    handler:function(){
                        Ext.MessageBox.confirm('Export', 'Apakah anda akan meng-Export item ini?',function(resbtn){
                            if(resbtn == 'yes')
                            {
                                window.location='<?=base_url()?>rbk/print_data/eva_perbandingan';                                                                             
                            }
                        });
                    }
                },'-',
                {
                	text:'Open In New Tab',
                	iconCls:'icon-new',
                	handler:function(){
                		window.open(document.URL,'_blank');
                	}
                }]
			},{
				xtype: 'toolbar',
				dock: 'top',
				items: [
					'<h2> Proyek : </h2>'
				]
			},{
				dock: 'bottom',
				xtype: 'toolbar',
				items: [                
					'<h2>Total : </h2>'
				]
			},{
				dock: 'bottom',
				xtype: 'toolbar',
				items: [
					'Biaya Bank Proyek : ',
					'BAU Proyek : ',
					'Biaya Lain - Lain Proyek : '
				]
			}],
			columnLines: true,
			width: '100%',
			height: '100%',
			renderTo: Ext.getBody(),
			listeners:{
				beforerender:function(){
					store.load();
				}
			},				
			bbar: Ext.create('Ext.PagingToolbar', {
				store: store,
				displayInfo: true,
				displayMsg: 'Displaying Data {0} - {1} of {2}',
				emptyMsg: "No data to display"
			})											
			
			/*
			bbar: [Ext.create('Ext.toolbar.Paging', {
								 pageSize: 1000,
								 store: store,
								 displayInfo: true
						 })
			]
			*/
	});
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>