<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/examples.js"></script>
<!-- <link rel="stylesheet" type="text/css" href="http://localhost/ext-4.1.1a/examples/toolbar/toolbars.css" /> -->
<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/styles.css" /> -->
<style>

p {
    margin:5px;
}

.footer {
font-size: 10px;
font-family: 'Arial'
}


.new-tab {
    background-image:url(<?php echo base_url(); ?>assets/images/new_tab.gif) !important;
}

.icon-back {
    background-image:url(<?php echo base_url(); ?>assets/images/back.png) !important;
}

.icon-add {
    background-image:url(<?php echo base_url(); ?>assets/images/add.gif) !important;
}

.icon-table {
    background-image:url(<?php echo base_url(); ?>assets/images/table.png) !important;
}

.icon-print {
    background-image:url(<?php echo base_url(); ?>assets/images/print.png) !important;
}

.icon-total {
    background-image:url(<?php echo base_url(); ?>assets/images/sum.png) !important;
}

.icon-reload {
    background-image:url(<?php echo base_url(); ?>assets/images/reload.png) !important;
}

.tabs {
    background-image:url(<?php echo base_url(); ?>assets/images/tabs.gif ) !important;
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

.my-column-style {
    background-color: yellow !important;
}

.col-style-green {
    background-color: #B0C9F5 !important;
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
<script type="text/javascript">
    Ext.require([
		'*',
		'Ext.ux.form.SearchField'
	]);

	Ext.Ajax.timeout = 3600000;
	Ext.override(Ext.form.Basic, {     timeout: Ext.Ajax.timeout / 1000 });
	Ext.override(Ext.data.proxy.Server, {     timeout: Ext.Ajax.timeout });
	Ext.override(Ext.data.Connection, {     timeout: Ext.Ajax.timeout });
	
    Ext.onReady(function() {
        Ext.QuickTips.init();
		
        Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
		
		/* RAT */
		
		var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';		

		
		Ext.define('treegriditem', {
			extend: 'Ext.data.Model',
			fields: ['rat_item_tree','id_proyek_rat','id_satuan','kode_tree','tree_item','tree_satuan','ishaschild','kode_analisa','selisih',
				'volume','harga','subtotal','id_rat_rab_item_tree','volume_rab','harga_rab','subtotal_rab','tree_parent_id']
		});
							
		var storeTree = Ext.create('Ext.data.TreeStore', {
			model: 'treegriditem',
			expanded: true,		
			extraParams: {
	            param : ''
	        },		
			proxy: {
				type: 'ajax',
				url: '<?=base_url()?>rencana/rab/get_task_tree_item_rab/<?=$idtender;?>',
			},
			autoLoad: false,
			listeners:{
	            beforeload:function(){
	                Ext.Msg.wait("Loading...","Please Wait");
	            },
	            load:function(){
	                Ext.MessageBox.hide();
	            }
	        }
		});
		
		var gridTreeBK = Ext.create('Ext.tree.Panel', {
			id: 'gridTreeBKid',
			useArrows: true,
			rootVisible: false,
			store: storeTree,
			multiSelect: false,
			loadMask: true,
			singleExpand: false,
			hideCollapseTool: false,
			viewConfig: {
				stripeRows: true,
			},		
			plugins: Ext.create('Ext.grid.plugin.RowEditing', {
				clicksToMoveEditor: 1,
				autoCancel: false,
				listeners: {
					beforeedit: function(rec,obj){   
                        if (obj.record.get('ishaschild') == 1) {
                            return false;
                        }
                    },
					'edit': function () {
							var editedRecords = gridTreeBK.getStore().getUpdatedRecords();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/rab/update_tree_item_rab',
								method: 'POST',
								params: {								
									'rat_item_tree' : editedRecords[0].data.rat_item_tree,
									'tree_parent_id' : editedRecords[0].data.tree_parent_id,
									'id_proyek_rat' : editedRecords[0].data.id_proyek_rat,
									'kode_tree' : editedRecords[0].data.kode_tree,
									'satuan_id' : editedRecords[0].data.tree_satuan,
									'tree_item' : editedRecords[0].data.tree_item,
									'volume' : editedRecords[0].data.volume_rab,
									'volume_rat' : editedRecords[0].data.volume,
									'harga_rat' : editedRecords[0].data.harga
								},								
								success: function(response) {
									var text = response.responseText;
									Ext.Msg.alert( "Status", text, function(){
										storeTree.load();
									});											
								},
								failure: function() {
									Ext.example.msg( "Error", "Data GAGAL diupdate!");											
								}
							});			   																										
						}
				 }				
			}),									
			columns: [
				{
					text: 'Kode',
					xtype: 'treecolumn', 
					width: 150,
					sortable: false,
					dataIndex: 'kode_tree',
				},
				{
					text: 'Kode Analisa',
					width:70,
					sortable: false,
					dataIndex: 'kode_analisa',
				},
				{
					text: 'Uraian',
					width:200,
					dataIndex: 'tree_item',
					sortable: false,
				}, 
				{
					text: 'Satuan',
					dataIndex: 'tree_satuan',
					flex: 1,
					sortable: false,
				}, 				
				{
					text: 'Volume RAT',
					dataIndex: 'volume',
					flex: 1,
					align: 'center',
					sortable: false,
				}, 				
				{
					text: 'Harga RAT',
					dataIndex: 'harga',
					width: 100,
					renderer: Ext.util.Format.numberRenderer('00,000'),
					align: 'right',
					sortable: false
				}, 								
				{
					text: 'Subtotal RAT',
					width: 100,
					dataIndex: 'subtotal',
					align: 'right',
					renderer: Ext.util.Format.numberRenderer('00,000'),
					sortable: false
				}, 								
				{
					text: 'Volume RAB',
					dataIndex: 'volume_rab',
					flex: 1,					
					align: 'center',
					tdCls: 'my-column-style',
					sortable: false,
					editor: {
						xtype: 'numberfield',
					}															
				}, 	//RAB
				{
					text: 'Harga RAB',
					dataIndex: 'harga_rab',
					width: 80,					
					tdCls: 'my-column-style',
					renderer: Ext.util.Format.numberRenderer('00,000'),
					align: 'right',
					sortable: false
				}, 	//RAB										
				{
					text: 'Subtotal RAB',
					dataIndex: 'subtotal_rab',
					align: 'right',
					tdCls: 'my-column-style',					
					renderer: Ext.util.Format.numberRenderer('00,000'),
					width: 80,					
					sortable: false
				},	//RAB			
				{
					text: 'Selisih',
					dataIndex: 'selisih',
					width: 80,					
					renderer: Ext.util.Format.numberRenderer('00,000'),
					align: 'right',
					sortable: false
				}, 												
			],
			listeners:{
				beforerender:function(){
					//storeTree.load();
					update_total_rat();
					update_total_rab();
				}
			},
			dockedItems: [				
				{
					xtype:'toolbar',
					dock:'top',
					items:[
					{
						text:'Export RAB',
						iconCls:'icon-print',
						handler:function(){
							Ext.MessageBox.confirm('Export', 'Apakah anda akan meng-Export Item Pekerjaan ini?',function(resbtn){
								if(resbtn == 'yes')
								{
									window.location='<?=base_url();?>rencana/rab/export_rab/rab';
								}
							});
						}
					},
					{
						text:'Export Analisa RAB',
						iconCls:'icon-print',
						handler:function(){
							Ext.MessageBox.confirm('Export', 'Apakah anda akan meng-Export Analisa Item Pekerjaan ini?',function(resbtn){
								if(resbtn == 'yes')
								{
									window.location='<?=base_url();?>rencana/rab/print_data_analisa/analisa_rab';
								}
							});
						}
					}
					]
				},
				{
					xtype: 'toolbar',
					dock: 'top',
					itemId:'top_bar',
					items: [
						{
							text: 'Kembali ke menu RAB',
							iconCls: 'icon-back',
							handler: function()
							{
								window.location = '<?=base_url();?>rencana/rab/index';
							}
						},'-',
						{
							text: 'Refresh',
							iconCls: 'icon-reload',
							handler: function()
							{
								storeTree.load();
							}
						},'-',
						{
							fieldLabel:'Search <b>(Press ENTER) <b>',
							labelWidth:140,
							width:244,
							itemId:'search',
							xtype:'textfield',
							listeners: {
								scope:this,
								specialKey: function(f,n){
									if (n.getKey() == n.ENTER){
										storeTree.proxy.setExtraParam('param', f.getValue());
										storeTree.load();
									}
								}
							}
						},'-',
						{
							text:'Clear',
							handler: function(){
								bar = gridTreeBK.getComponent('top_bar');
								bar.getComponent('search').setValue('');
								storeTree.proxy.setExtraParam('param','');
								storeTree.load();
							}
						}
						/*
						'->',
						{
							flex: 1,
							fieldLabel: 'Edit Volume RAB',
							emptyText: 'masukan angka...',
							xtype: 'numberfield',
						},
						{
							text: 'Simpan',
							handler: function()
							{
								Ext.example.msg('Status','Volume berhasil diedit!');
							}
						}
						*/
					]
				},
				{
					xtype: 'toolbar',
					dock: 'bottom',
					items: [
						{
							text: 'Total RAT : ',
							iconCls: 'icon-total',
							id: 'id-total-rat',
							handler: update_total_rat
						},'->',						
						{
							text: 'Total RAB : ',
							iconCls: 'icon-total',
							id: 'id-total-rab',
							handler: update_total_rab
						}
					]
				}
			]			
		});		
		
		function update_total_rat()
		{
			Ext.Ajax.request({
				url: '<?=base_url();?>rencana/total_rat/<?=$idtender;?>',
				method: 'POST',											
				params: {												
					'id_tender' : <?=$idtender;?>
				},								
				success: function(response) {			
					Ext.getCmp('id-total-rat').setText('<b>Total RAT : '+ response.responseText+'</b>');
				},
				failure: function(response) {
					Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
				}
			});			   			
		}

		function update_total_rab()
		{
			Ext.Ajax.request({
				url: '<?=base_url();?>rencana/rab/get_total_rab',
				method: 'POST',											
				params: {												
					'id_tender' : <?=$idtender;?>
				},								
				success: function(response) {			
					Ext.getCmp('id-total-rab').setText('<b>Total RAB : '+ response.responseText+'</b>');
				},
				failure: function(response) {
					Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
				}
			});			   			
		}
		
		/* edit harga satuan RAB */
			Ext.define('mdlEditHargaASAT', {
				extend: 'Ext.data.Model',
				fields: [
					'kode_material','detail_material_nama','detail_material_satuan','harga','kode_rap', 'keterangan','kategori', 'nilai_pengali','harga_rab'
				 ]
			});

			var storeEditHargaASAT = Ext.create('Ext.data.Store', {
				model: 'mdlEditHargaASAT',
				pageSize: 200,  
				remoteFilter: true,
				autoLoad: false,
				proxy: {
					 type: 'ajax',
					 url: '<?php echo base_url() ?>rencana/rab/edit_harga_satuan_asat_rab/<?=$idtender;?>',
					 reader: {
						 type: 'json',
						 root: 'data'
					 }
				},		
			});		

			var gridEditHargaASAT = Ext.create('Ext.grid.Panel', {
				width: '100%',
				height: '100%',
				frame: false,
				store: storeEditHargaASAT,
				plugins: Ext.create('Ext.grid.plugin.RowEditing', {
					//clicksToEdit: 1,
					listeners : {
						edit : function() {
							//var editedRecords = gridEditHargaASAT.getView().getSelectionModel().getSelection();
							var editedRecords = gridEditHargaASAT.getStore().getUpdatedRecords();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/rab/update_harga_asat/<?=$idtender;?>',
								method: 'POST',
								params: {
									'id_tender' : <?=$idtender;?>,
									'kode_material' : editedRecords[0].data.kode_material,
									'harga' : editedRecords[0].data.harga,
									'kode_rap' : editedRecords[0].data.kode_rap,
									'pengali' : editedRecords[0].data.nilai_pengali,
									'keterangan' : editedRecords[0].data.keterangan,
								},								
								success: function(response) {
									Ext.Msg.alert("Status", response.responseText, function()
									{
										storeEditHargaASAT.load();
										storeASAT.load();
										storeTree.load();
									});
								},
								failure: function(response) {
									Ext.MessageBox.alert('Failure', 'Error due to connection problem!');
								}
							});
						}
					}
				}),				
				columns: [						
					{
						xtype: 'rownumberer',
						width: 35,
						sortable: false
					},
					{text: "Kode RAP", flex: 1, sortable: false, dataIndex: 'kode_rap'},					
					{text: "Kode Material", flex: 1, sortable: false, dataIndex: 'kode_material'},					
					/*
					{
						text: "Kode Material", 
						flex: 1, 
						sortable: false, 
						dataIndex: 'kode_material',
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
						},						
					},
					*/
					{text: "Uraian", flex: 2, sortable: false, dataIndex: 'detail_material_nama'},
					{text: "Satuan", flex: 1, sortable: false, dataIndex: 'detail_material_satuan'},
					{text: "Kategori", flex: 1, sortable: false, dataIndex: 'kategori'},
					{text: "Harga RAT", flex: 1, sortable: false, align: 'right', dataIndex: 'harga', renderer: Ext.util.Format.numberRenderer('00,000'),},
					{text: "Nilai Pengali", flex: 1, sortable: false, dataIndex: 'nilai_pengali', editor:'numberfield',tdCls: 'my-column-style',},
					{text: "Harga RAB", flex: 1, sortable: false, align: 'right', dataIndex: 'harga_rab', renderer: Ext.util.Format.numberRenderer('00,000'),tdCls: 'my-column-style'},
				],
				columnLines: true,
				dockedItems: [
					{
						xtype: 'toolbar',
						dock: 'top',
						items: [
							{
								flex: 3,
								fieldLabel: 'Edit nilai pengali',
								emptyText: 'masukan angka...',
								id: 'id_edit_harga',
								name: 'edit_harga',
								xtype: 'numberfield',
								decimalPrecision: 4
							},
							{
								text: 'Simpan',			
								handler: function()
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/rab/update_harga_pengali',
										method: 'POST',
										params: {
											'id_tender' : <?=$idtender;?>,
											'nilai_pengali' : Ext.getCmp('id_edit_harga').getValue(),
										},								
										success: function(response) {
											var text = response.responseText;
											Ext.Msg.alert("Status", response.responseText, function()
											{
												storeEditHargaASAT.load();
												// storeTree.load();												
												// storeASAT.load();
											});
										},
										failure: function(response) {
											Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
										}
									});									
								}
							},'->',
							{
								flex: 4,
								fieldLabel: 'Cari',
								labelWidth: 50,
								tooltip:'masukan kode analisa / uraian',
								emptyText: 'masukan kode analisa / uraian...',
								xtype: 'searchfield',
								store: storeEditHargaASAT,
								listeners: {
											keyup: function(e){ 
											}
										}
							}
						]
					},
					{
						xtype: 'toolbar',
						dock: 'bottom',
						items: [
							{
								text:'Tutup',
								flex: 1,
								handler: function(){      
									winEditHargaSatuan.hide();
								}
							},
						]
					}					
				],
				bbar: Ext.create('Ext.PagingToolbar', {
					store: storeEditHargaASAT,
					displayInfo: true,
					displayMsg: 'Displaying Data {0} - {1} of {2}',
					emptyMsg: "No data to display"
				})											
			});
		
		var winEditHargaSatuan = Ext.widget('window', {
			title: 'Edit Harga RAB',
			closeAction: 'hide',
			closable: false,					
			width: '70%',
			height: '85%',
			layout: 'fit',
			resizable: true,
			modal: true,
			items: gridEditHargaASAT,
			listeners:{
				hide:function(){
					storeASAT.load();
					storeTree.load();
				}
			}
		});
		
		/* end edit harga satuan RAB */
		
		/* grid analisa */ 
			Ext.define('mdlASAT', {
				extend: 'Ext.data.Model',
				fields: [
					'id_analisa_asat', 'id_data_analisa', 'kode_material', 'id_detail_material', 'koefisien', 
					'harga', 'kode_analisa', 'id_tender', 'detail_material_nama', 'detail_material_satuan',
					'detail_material_kode', 'asat_kat', 'subtotal', 'parent_name', 'harga_rab', 
					'koefisien_rab', 'nilai_pengali', 'subtotal_rab', 'nilai_pengali','id_rab_analisa'
				 ]
			});

			var storeASAT = Ext.create('Ext.data.Store', {
				model: 'mdlASAT',
				pageSize: 300,  
				remoteFilter: true,
				autoLoad: false,
				proxy: {
					 type: 'ajax',
					 url: '<?php echo base_url() ?>rencana/rab/get_asat/<?=$idtender?>',
					 reader: {
						 type: 'json',
						 root: 'data'
					}
				},
				groupField: 'asat_kat',
				sorters: [{
					property: 'kode_analisa',
					direction: 'ASC'
				}],
			});		
		
			var grid_analisa = Ext.create('Ext.grid.Panel', {
				width: '100%',
				height: '100%',
				frame: false,
				store: storeASAT,
				features: [{
					ftype: 'groupingsummary',
					groupHeaderTpl: '{name}',
					hideGroupedHeader: true,
					enableGroupingMenu: false
				}],
				disableSelection: false,				
				
				/*
				viewConfig: {
					trackOver: true,
					stripeRows: true,
				},
				*/				
				plugins: Ext.create('Ext.grid.plugin.RowEditing', {
					clicksToMoveEditor: 1,
					autoCancel: false,
					listeners : {
						'edit' : function() {							
							var editedRecords = grid_analisa.getStore().getUpdatedRecords();
							//var editedRecords = grid_analisa.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/rab/update_koefisien_rab',
								method: 'POST',
								params: {
									'id_tender' : <?=$idtender;?>,
									'id_simpro_rat_analisa' : editedRecords[0].data.id_analisa_asat,
									'id_rab_analisa' : editedRecords[0].data.id_rab_analisa,
									'koefisien_rab' :  editedRecords[0].data.koefisien_rab,
									'harga_rat' :  editedRecords[0].data.harga, 
									'harga_rab' :  editedRecords[0].data.harga_rab,
									'koefisien_rat' :  editedRecords[0].data.koefisien,
									'kode_analisa' : editedRecords[0].data.detail_material_kode,																		
								},								
								success: function(response) {
									var text = response.responseText;
									Ext.Msg.alert("Status", response.responseText, function()
									{
										storeASAT.load();
										storeTree.load();
									});
								},
								failure: function(response) {
									Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
								}
							});
						}
					}
				}),				
				columns: [
					{
						text: "Kode", 
						width: 70, 
						sortable: false, 
						dataIndex: 'detail_material_kode',
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
						},						
					},
					{text: "Uraian", flex: 3, sortable: false, dataIndex: 'detail_material_nama'},
					{text: "Satuan", flex: 1, sortable: false, dataIndex: 'detail_material_satuan'},
					{text: "Koefisien RAT", flex: 1, sortable: false, dataIndex: 'koefisien'},
					{
						text: "Harga RAT", 
						flex: 2, 
						sortable: false, 
						dataIndex: 'harga',
						align: 'right',
						renderer: Ext.util.Format.numberRenderer('00,000'),						
					},
					{
						text: "Sub Total RAT", 
						flex: 2, 
						sortable: false, 
						dataIndex: 'subtotal',
						align: 'right',
						groupable: false,
						renderer: Ext.util.Format.numberRenderer('00,000'),
						summaryType: function(records){
							var i = 0,
								length = records.length,
								total = 0,
								record;

							for (; i < length; ++i) {
								record = records[i];
								total += record.get('harga') * record.get('koefisien');
							}
							return total;
						},
						summaryRenderer: Ext.util.Format.numberRenderer('00,000')					
					},
					{text: "Pengali", flex: 1, sortable: false, dataIndex: 'nilai_pengali', tdCls: 'my-column-style'},					
					{text: "Koefisien RAB", flex: 1, sortable: false, dataIndex: 'koefisien_rab', editor: {
						xtype: 'numberfield',
						decimalPrecision: 4

					},tdCls: 'my-column-style'},
					{
						text: "Harga RAB", 
						flex: 2, 
						sortable: false, 
						dataIndex: 'harga_rab',
						align: 'right',
						tdCls: 'my-column-style',
						renderer: Ext.util.Format.numberRenderer('00,000'),						
					},
					{
						text: "Sub Total RAB", 
						flex: 2, 
						sortable: false, 
						dataIndex: 'subtotal_rab',
						tdCls: 'my-column-style',
						align: 'right',
						groupable: false,
						renderer: Ext.util.Format.numberRenderer('00,000'),
						summaryType: function(records){
							var i = 0,
								length = records.length,
								total = 0,
								record;

							for (; i < length; ++i) {
								record = records[i];
								total += record.get('harga_rab') * record.get('koefisien_rab');
							}
							return total;
						},
						summaryRenderer: Ext.util.Format.numberRenderer('00,000')					
					},
				],
				columnLines: true,
				dockedItems: [
					{
						xtype: 'toolbar',
						dock: 'top',
						items: [
							{
								text:'Edit Harga RAB',
								tooltip:'Edit Harga Satuan RAB',
								handler: function(){        
									winEditHargaSatuan.on('show', function(win) {	   
										storeEditHargaASAT.load();									
									});										
									winEditHargaSatuan.doLayout();
									winEditHargaSatuan.show();								
								}
							},'-',
							{
								flex: 4,
								tooltip:'masukan kode analisa / uraian',
								emptyText: 'masukan kode analisa / uraian...',
								xtype: 'searchfield',
								store: storeASAT,
								listeners: {
											keyup: function(e){ 
											}
										}
							}							
						]
					},
				],
				listeners:{
					beforerender:function(){
						storeASAT.load();
					}
				},				
				bbar: Ext.create('Ext.PagingToolbar', {
					store: storeASAT,
					displayInfo: true,
					displayMsg: 'Displaying Data {0} - {1} of {2}',
					emptyMsg: "No data to display"
				})											
			});
		/* end grid analisa */
						
		var DataRATBK = Ext.create('Ext.panel.Panel', {
			title: 'Analisa Satuan Pekerjaan / ASAT',
			width: '100%',
			layout: 'fit',
			items: [grid_analisa],
		});
				
		var panelRAB = Ext.widget('panel', {
			width: '100%',
			height: '100%',
			layout: 'border',
			items: [
				{
					region:'west',
					layout: 'fit',
					width: '50%',
					split: true,
					items: gridTreeBK 
				},					
				{
					region:'center',
					layout: 'fit',
					width: '50%',
					items: DataRATBK
				}					
			]
		});
				
		/* RABA */
		Ext.define('mdlRABA', {
			extend: 'Ext.data.Model',
			fields: [
				'kd_material','detail_material_nama','detail_material_satuan',
				'total_volume','harga','subtotal','subbidang','kode_rap'
			],
		});

		var storeRABA = Ext.create('Ext.data.Store', {
			 model: 'mdlRABA',
			 proxy: {
				type: 'ajax',
				url: '<?=base_url();?>rencana/rab/get_raba/<?=$idtender;?>',
				reader: {
					type: 'json',
					root: 'data',
					totalProperty: 'total'
				},
				simpleSortMode: true				 
			 },
			autoLoad: false,
			remoteSort: true,
			pageSize: 200,
			groupField: 'subbidang',
			sorters: [{
				property: 'kode_tree',
				direction: 'DESC'
			}]			
		});			

		var gridRABA = Ext.create('Ext.grid.Panel', {
			width: '100%',
			height: '100%',
			store: storeRABA,
			disableSelection: false,
			loadMask: true,
			viewConfig: {
				trackOver: true,
				stripeRows: true,
			},		
			features: [{
				ftype: 'groupingsummary',
				groupHeaderTpl: '{name}',
				hideGroupedHeader: true,
				enableGroupingMenu: false
			}],						
			columns:[
				{
					text: '',
					dataIndex: 'subbidang',
					sortable: false
				},
				{
					text: "Kode RAP",
					dataIndex: 'kode_rap',
					width: 60,
					sortable: true,
					summaryType: 'count',
					summaryRenderer: function(value, summaryData, dataIndex) {
						return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
					}					
				},
				{
					text: 'Kode Material',
					dataIndex: 'kd_material',
					flex:1,
					sortable: false
				},
				{
					text: 'Nama',
					dataIndex: 'detail_material_nama',
					flex:3,
					sortable: false
				},
				{
					text: 'Satuan',
					dataIndex: 'detail_material_satuan',
					flex:1,
					sortable: false
				},
				{
					text: 'Volume',
					dataIndex: 'total_volume',
					align: 'right',
					flex:1,
					sortable: false
				},
				{
					text: 'Harga Satuan',
					dataIndex: 'harga',
					renderer: Ext.util.Format.numberRenderer('00,000'),
					flex:1,
					align: 'right',
					sortable: false
				},
				{
					text: "SUBTOTAL",
					dataIndex: 'subtotal',
					width: 200,
					align: 'right',
					sortable: true,
					groupable: false,
					renderer: Ext.util.Format.numberRenderer('00,000'),
					summaryType: function(records){
						var i = 0,
							length = records.length,
							total = 0,
							record;

						for (; i < length; ++i) {
							record = records[i];
							total += record.get('harga') * record.get('total_volume');
						}
						return total;
					},
					summaryRenderer: Ext.util.Format.numberRenderer('00,000')
				},							
			],
			bbar: Ext.create('Ext.PagingToolbar', {
				store: storeRABA,
				displayInfo: true,
				displayMsg: 'Displaying data {0} - {1} of {2}',
				emptyMsg: "No data to display",
			}),		
			dockedItems: [
				{
					xtype: 'toolbar',
					items: [
						{
							text: 'Print',
							iconCls: 'icon-print',
							handler: function()
							{
								window.location = '<?php echo base_url(); ?>rencana/rab/cetak_raba/<?=$idtender;?>';
							}							
						},
						{
							text: 'Export to Excel',
							iconCls: 'icon-table',
							handler: function()
							{
								window.location = '<?php echo base_url(); ?>rencana/rab/raba_to_xls/<?=$idtender;?>';
							}
						}
					]
				},
				{
					xtype: 'toolbar',
					dock: 'bottom',
					items: [
						'->',
						{
							text: 'Persentase terhadap nilai kontrak (incl PPN): (%)',
							id: 'id-persen-raba',
							handler: update_persen_raba
						},
					]
				},
				{
					xtype: 'toolbar',
					dock: 'bottom',
					items: [
						'->',
						{
							text: 'Total RAB(A) : ',
							id: 'id-total-raba',
							iconCls: 'icon-total',
							handler: update_total_raba
							//handler: 
						},
					]
				}
			],			
			listeners:{
				beforerender:function(){
					storeRABA.load();
					update_total_raba();
				},
				itemclick: function(dv, record, item, index, e) {
				}						
			},
		});				
		
		function update_total_raba()
		{
			Ext.Ajax.request({
				url: '<?=base_url();?>rencana/rab/get_total_raba/<?=$idtender;?>',
				method: 'POST',											
				params: {												
					'id_tender' : <?=$idtender;?>
				},								
				success: function(response) {			
					Ext.getCmp('id-total-raba').setText('<b>Total RAB(A) : '+ response.responseText +'</b>');
					update_persen_raba();					
				},
				failure: function(response) {
					Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
				}
			});
		}
		
		function update_persen_raba()
		{
			Ext.Ajax.request({
				url: '<?=base_url();?>rencana/rab/get_persen_raba/<?=$idtender;?>',
				method: 'POST',											
				params: {												
					'id_tender' : <?=$idtender;?>
				},								
				success: function(response) {			
					Ext.getCmp('id-persen-raba').setText('<b>Persentase terhadap nilai kontrak (incl PPN): '+ response.responseText +'%</b>');
				},
				failure: function(response) {
					Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
				}
			});
		}
		
		/* end RABA */
		
		var tabRAT = Ext.widget('tabpanel', {
			title: 'Edit Harga RAB :: Proyek -> <?=$data_tender['nama_proyek'];?>',
			renderTo: Ext.getBody(),
			activeTab: 0,
			width: '100%',
			height: '100%',
			deferredRender: false, 
			items: [
			{			
				title: 'RAB',
				layout: 'fit',
				items: [panelRAB],
				listeners: {
					activate: function(tab){
						setTimeout(function() {
							update_total_rab();
							update_total_rat();						
						}, 1);
					}
				},																				
			},
			{
				title: 'RAB(A)',
				layout: 'fit',
				items: [gridRABA],
				listeners: {
					activate: function(tab){
						setTimeout(function() {
							storeRABA.load();
							update_total_raba();							
						}, 1);
					}
				},
			}, 			
			]			
		});		
		
						
	});
	
</script>

<div id="panel-rab"></div>