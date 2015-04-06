<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
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

.icon-add {
    background-image:url(<?php echo base_url(); ?>assets/images/add.gif) !important;
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
		
</style>
<script type="text/javascript">
	Ext.ns('EntryRATTree');
	
	Ext.Loader.setConfig({
		enabled: true
	});
	
	Ext.Loader.setPath('Ext.ux', '/simpro/assets/js/extjs/src/ux');

    Ext.require(
	['*']
	);
	
	
	Ext.define('MySharedData', {
		singleton: true,
		foo: 'bar',
	});  
	
    Ext.onReady(function() {
        Ext.QuickTips.init();
		
        Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
		
		/* RAT */
		
		var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';		
		
		/* DirectCost */
		Ext.define('DCModelBK', {
			extend: 'Ext.data.Model',
			fields: [
				'id_rat_direct_cost','id_kategori_pekerjaan', 'kat_rat', 'type_rat', 'id_proyek_rat', 'id_satuan_pekerjaan', 'kode', 'uraian', 'satuan', 'mharga', 'volume', 'subtotal'
			],
			idProperty: 'DCModelBKid'
		});

		var storeDCBK = Ext.create('Ext.data.Store', {
			pageSize: 50,
			model: 'DCModelBK',
			remoteSort: true,
			proxy: {
				type: 'jsonp',
				url: '<?=base_url();?>rencana/get_data_dc/<?=$idtender;?>',
				reader: {
					root: 'data',
					totalProperty: 'total'
				},
				simpleSortMode: true
			},		
			groupField: 'kat_rat',
			sorters: [{
				property: 'id_rat_direct_cost',
				direction: 'DESC'
			}]
		});
		
		var winDCAddBK;
		function showfrmAddDCBK() {
			if (!winDCAddBK) {
				var frmAddDCBK = Ext.widget({
					xtype: 'form',
					layout: 'form',
					url: '<?=base_url();?>rencana/tambah_direct_cost/<?=$idtender;?>',
					frame: false,
					bodyPadding: '5 5 0',
					width: 400,
					height: 200,
					fieldDefaults: {
						msgTarget: 'side',
						labelWidth: 150
					},
					defaultType: 'textfield',										
					items: [
						{
							name: 'id_type_rat',
							xtype: 'hiddenfield',
							value: 1,
						},						
						Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'Kategori',
							afterLabelTextTpl: required,
							allowBlank: false,
							store: { 
								fields: ['id_kat_rat','kategori'], 
								pageSize: 50, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_sub_rat', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							value :'',							
							emptyText: 'Pilih Kategori...',
							name: 'id_kat_rat',
							triggerAction: 'all',
							queryMode: 'remote',
							minChars: 3,
							enableKeyEvents:true,							
							selectOnFocus:true,																												
							typeAhead: true,
							pageSize: true,
							displayField: 'kategori',
							valueField: 'id_kat_rat',
							listeners: {
								 'select': function(combo, row, index) {
								}
							},
						}),						
						{
							xtype: 'combo',
							name: 'id_satuan_pekerjaan',
							store: { 
								id : 'scmb_harga_satuan',
								fields: ['id_satuan_pekerjaan','kode_satuan','mharga'], 
								pageSize: 10, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_harga_satuan', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							fieldLabel: 'satuan Pekerjaan',
							emptyText: 'satuan Pekerjaan',
							displayField: 'kode_satuan',
							typeAhead: true,
							hideLabel: false,
							hideTrigger:true,							
							anchor: '100%',
							displayField: 'kode_satuan',
							valueField: 'id_satuan_pekerjaan',
							listeners: {
								 'select': function(combo, row, index) {
									var valharga = row[0].get('mharga');
									var valsatuan = row[0].get('satuan');
									Ext.getCmp('hargasatuan').setValue(valharga);
									//Ext.getCmp('satuan').setValue(valsatuan);
								}
							},														
							pageSize: 10
						},			
						/*
						{
							fieldLabel: 'Satuan',
							afterLabelTextTpl: required,
							id: 'satuan',
							name: 'satuan',
							xtype: 'textfield',
							allowBlank: false,
						},
						*/
						{
							fieldLabel: 'Harga satuan',
							afterLabelTextTpl: required,
							id: 'hargasatuan',
							name: 'harga',
							xtype: 'numberfield',
							allowBlank: false,
						},
						{
							fieldLabel: 'volume',
							emptyText: 'volume...',
							afterLabelTextTpl: required,
							name: 'volume',
							xtype: 'numberfield',
							allowBlank: false,
						},
					],
					buttons: [{
						text: 'Save',
						handler: function() {
							var form = this.up('form').getForm();
							if (form.isValid()) {
								form.submit({
									success: function(form, action) {
									   Ext.Msg.alert('Success', action.result.message, function(btn){
										if(btn == 'ok')
										{
											storeDCBK.loadPage(1);
											frmAddDCBK.getForm().reset();
										}
									   });
									},
									failure: function(form, action) {
										Ext.Msg.alert('Failed', action.result ? action.result.message : 'No response');
									}
								});
							} else {
								Ext.Msg.alert( "Error!", "Silahkan isi form dg benar!" );
							}
						}						
					},
					{
						text: 'Reset',
						handler: function() {
							frmAddDCBK.getForm().reset();
						}
					},
					{
						text: 'Cancel',
						handler: function() {
							frmAddDCBK.getForm().reset();
							winDCAddBK.hide();
						}
					}
					]
				});
			
				winDCAddBK = Ext.widget('window', {
					title: 'Tambah Item Direct Cost',
					closeAction: 'hide',
					width: 550,
					height: 250,
					layout: 'fit',
					resizable: true,
					modal: true,
					items: frmAddDCBK
				});
			}
			winDCAddBK.show();
		}				

		var totalRATBK = 0;
		var gridDCBK = Ext.create('Ext.grid.Panel', {
			//title: 'Direct Cost / Biaya Konstruksi',
			width: 700,
			height: 500,
			store: storeDCBK,
			disableSelection: false,
			loadMask: true,
			viewConfig: {
				id: 'gv',
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
				/*
				{
					xtype: 'rownumberer',
					width: 35,
					sortable: false
				},
				*/				
				{
					text: "Kode",
					dataIndex: 'kode',
					width: 70,
					sortable: true,
					summaryType: 'count',
					summaryRenderer: function(value, summaryData, dataIndex) {
						return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
					}					
				},
				{
					text: "Kategori RAT",
					dataIndex: 'kat_rat',
					width: 150,
					sortable: true,
				},
				{
					text: "URAIAN",
					dataIndex: 'uraian',
					width: 250,
					sortable: true,
				},				
				{
					text: "SATUAN",
					dataIndex: 'satuan',
					width: 50,
					sortable: true
				},
				{
					text: "HARGA",
					dataIndex: 'mharga',
					renderer: Ext.util.Format.numberRenderer('00,000'),
					width: 100,
					align: 'right',
					sortable: true
				},
				{
					text: "VOLUME",
					dataIndex: 'volume',
					width: 50,
					align: 'right',
					sortable: true
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
							total += record.get('mharga') * record.get('volume');
						}
						return total;
					},
					summaryRenderer: Ext.util.Format.numberRenderer('00,000')
				},			
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/delete.gif',  
								tooltip: 'Hapus item satuan pekerjaan',
								handler: function(grid, rowIndex, colIndex) {
									var rec = storeDCBK.getAt(rowIndex);
									var id = rec.get('id_rat_direct_cost');
									Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
										if(resbtn == 'yes')
										{
											Ext.Ajax.request({
												url: '<?=base_url();?>rencana/del_item_rat',
												method: 'POST',
												params: {
													'id_rat_direct_cost' : id
												},								
												success: function(response) {
													var text = response.responseText;
													Ext.Msg.alert( "Status", text, function(){
														storeDCBK.load();
													});											
												},
												failure: function() {
												}
											});			   																			
										}
									})
								}
						}]
                },				
			],
			bbar: Ext.create('Ext.PagingToolbar', {
				store: storeDCBK,
				displayInfo: true,
				displayMsg: 'Displaying data {0} - {1} of {2}',
				emptyMsg: "No data to display",
			}),
			dockedItems: [{
				xtype: 'toolbar',
				items: [
					{
						text: 'Add',
						iconCls: 'icon-add',
						handler: showfrmAddDCBK
					}
				]
			}],
			listeners:{
				beforerender:function(){
					storeDCBK.load();
				}
			}			
		});
		/* end Direct Cost */
		
		/* grid analisa */ 
		var grid_analisa = Ext.create('Ext.grid.Panel', {
			id: 'id_grid_analisa',
			width: 700,
			height: 500,
			store: storeDCBK,
			disableSelection: false,
			loadMask: true,
			viewConfig: {
				id: 'gv',
				trackOver: true,
				stripeRows: true,
			},		
			features: [{
				id: 'group',
				ftype: 'groupingsummary',
				groupHeaderTpl: '{name}',
				hideGroupedHeader: true,
				enableGroupingMenu: false
			}],			
			columns:[
				{
					xtype: 'rownumberer',
					width: 35,
					sortable: false
				},
				{
					text: "Kode",
					dataIndex: 'kode',
					width: 70,
					sortable: true,
					summaryType: 'count',
					summaryRenderer: function(value, summaryData, dataIndex) {
						return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
					}					
				},
				{
					text: "Kategori RAT",
					dataIndex: 'kat_rat',
					width: 150,
					sortable: true,
				},
				{
					text: "URAIAN",
					dataIndex: 'uraian',
					width: 250,
					sortable: true,
				},				
				{
					text: "SATUAN",
					dataIndex: 'satuan',
					width: 50,
					sortable: true
				},
				{
					text: "HARGA",
					dataIndex: 'mharga',
					renderer: Ext.util.Format.numberRenderer('00,000'),
					width: 100,
					align: 'right',
					sortable: true
				},
				{
					text: "VOLUME",
					dataIndex: 'volume',
					width: 50,
					align: 'right',
					sortable: true
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
							total += record.get('mharga') * record.get('volume');
						}
						return total;
					},
					summaryRenderer: Ext.util.Format.numberRenderer('00,000')
				},			
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/delete.gif',  
								tooltip: 'Hapus item satuan pekerjaan',
								handler: function(grid, rowIndex, colIndex) {
									var rec = storeDCBK.getAt(rowIndex);
									var id = rec.get('id_rat_direct_cost');
									Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
										if(resbtn == 'yes')
										{
											Ext.Ajax.request({
												url: '<?=base_url();?>rencana/del_item_rat',
												method: 'POST',
												params: {
													'id_rat_direct_cost' : id
												},								
												success: function(response) {
													var text = response.responseText;
													Ext.Msg.alert( "Status", text, function(){
														storeDCBK.load();
													});											
												},
												failure: function() {
												}
											});			   																			
										}
									})
								}
						}]
                },				
			],
			bbar: Ext.create('Ext.PagingToolbar', {
				store: storeDCBK,
				displayInfo: true,
				displayMsg: 'Displaying data {0} - {1} of {2}',
				emptyMsg: "No data to display",
			}),
			dockedItems: [{
				xtype: 'toolbar',
				items: [
					{
						text: 'Add',
						iconCls: 'icon-add',
						handler: showfrmAddDCBK
					}
				]
			}],
			listeners:{
				beforerender:function(){
					storeDCBK.load();
				}
			}			
		});		
		/* end grid analisa */

		/* start IN-Direct Cost */				
		Ext.define('IDCModelBK', {
			extend: 'Ext.data.Model',
			fields: [
				'id_rat_indirect_cost', 'kat_rat', 'id_proyek_rat', 'id_satuan_pekerjaan', 'kode', 'uraian', 'satuan', 'icharga', 'icvolume', 'subtotal'
			],
			idProperty: 'iDCModelBKid'
		});

		var storeIDCBK = Ext.create('Ext.data.Store', {
			pageSize: 50,
			model: 'IDCModelBK',
			remoteSort: true,
			proxy: {
				type: 'jsonp',
				url: '<?=base_url();?>rencana/get_data_idc/<?=$idtender;?>',
				reader: {
					root: 'data',
					totalProperty: 'total'
				},
				simpleSortMode: true
			},		
			groupField: 'kat_rat',
			sorters: [{
				property: 'id_rat_indirect_cost',
				direction: 'DESC'
			}]
		});
		
		var winIDCAddBK, selSubbidang;
		function showfrmAddIDCBKBK(parentid) {					
			parentid = 0;
			if (!winIDCAddBK) {
				var frmAddIDCBK = Ext.widget({
					xtype: 'form',
					layout: 'form',
					url: '<?=base_url();?>rencana/tambah_rat_tree_item/<?=$idtender;?>',
					frame: false,
					bodyPadding: '5 5 0',
					width: 400,
					height: 200,
					fieldDefaults: {
						msgTarget: 'side',
						labelWidth: 150
					},
					defaultType: 'textfield',										
					items: [
						{
							name: 'tree_parent_id',
							xtype: 'hiddenfield',
							value: 0
						},												
						{
							name: 'id_proyek_rat',
							xtype: 'hiddenfield',
							value: <?=$idtender;?>,
						},			
						/*
						Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'sub Bidang',
							id: 'sub_bidang_id',
							afterLabelTextTpl: required,
							allowBlank: false,
							store: { 
								fields: ['subbidang_kode','subbidang_name', 'kd_bidang'], 
								pageSize: 50, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_subbidang', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							value :'',							
							emptyText: 'sub bidang pekerjaan...',
							name: 'id_kat_rat',
							triggerAction: 'all',
							queryMode: 'remote',
							minChars: 3,
							enableKeyEvents:true,							
							selectOnFocus:true,																												
							typeAhead: true,
							pageSize: true,
							displayField: 'kd_bidang',
							valueField: 'subbidang_kode',
							listeners: {
								 'select': function(combo, row, index) {
									EntryRATTree.selSubbidang = row[0].get('subbidang_kode');
								}
							},
						}),				
						*/
						{
							fieldLabel: 'Kode',
							afterLabelTextTpl: required,
							name: 'kode_tree',
							xtype: 'textfield',
							allowBlank: false,
							emptyText: 'kode misal: 1.1'
						},						
						{
							xtype: 'combo',
							name: 'tree_item',
							afterLabelTextTpl: required,
							allowBlank: false,
							store: { 
								fields: ['detail_material_id','detail_material_kode', 'detail_material_nama','detail_material_satuan','subbidang_kode'], 
								pageSize: 20, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_detailmaterial_kode/'+EntryRATTree.selSubbidang,
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							fieldLabel: 'Uraian pekerjaan...',
							emptyText: 'uraian...',
							displayField: 'detail_material_nama',
							typeAhead: true,
							hideLabel: false,
							hideTrigger:true,
							anchor: '100%',
							valueField: 'detail_material_kode',
							listeners: {
								 'select': function(combo, row, index) {
									//var valharga = row[0].get('mharga');
									//Ext.getCmp('hargasatuan_idc').setValue(valharga);
								}
							},														
							pageSize: 10
						},	
						{
							xtype: 'combo',
							name: 'tree_satuan',
							afterLabelTextTpl: required,
							allowBlank: false,
							store: { 
								fields: ['satuan_kode'], 
								pageSize: 100, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_satuan', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							fieldLabel: 'Satuan',
							emptyText: 'pilih satuan...',
							displayField: 'satuan_kode',
							typeAhead: false,
							hideLabel: false,
							hideTrigger:false,
							anchor: '100%',
							displayField: 'satuan_kode',
							valueField: 'satuan_kode',
							listeners: {
								 'select': function(combo, row, index) {
									//var valharga = row[0].get('mharga');
									//Ext.getCmp('hargasatuan_idc').setValue(valharga);
								}
							},														
							pageSize: 10
						},
						/*
						{
							fieldLabel: 'Harga satuan',
							id: 'tree_satuan_id',
							name: 'tree_satuan',
							xtype: 'numberfield',
							emptyText: 'harga satuan...',
						},
						*/
						{
							fieldLabel: 'volume',
							emptyText: 'volume...',
							name: 'tree_volume',
							xtype: 'numberfield',
						},
					],
					buttons: [{
						text: 'Save',
						handler: function() {
							var form = this.up('form').getForm();
							if (form.isValid()) {
								form.submit({
									success: function(form, action) {
									   Ext.Msg.alert('Success', action.result.message, function(btn){
										if(btn == 'ok')
										{
											storeIDCBK.loadPage(1);
											frmAddIDCBK.getForm().reset();
										}
									   });
									},
									failure: function(form, action) {
										Ext.Msg.alert('Failed', action.result ? action.result.message : 'No response');
									}
								});
							} else {
								Ext.Msg.alert( "Error!", "Silahkan isi form dg benar!" );
							}
						}						
					},
					{
						text: 'Reset',
						handler: function() {
							frmAddIDCBK.getForm().reset();
						}
					},
					{
						text: 'Close',
						handler: function() {
							frmAddIDCBK.getForm().reset();
							winIDCAddBK.hide();
						}
					}
					]
				});
			
				winIDCAddBK = Ext.widget('window', {
					title: 'Tambah Item Biaya Konstruksi',
					closeAction: 'hide',
					width: 550,
					height: 250,
					layout: 'fit',
					resizable: true,
					modal: true,
					items: frmAddIDCBK
				});
			}
			winIDCAddBK.show();
		}				

		Ext.define('Task', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'rat_item_tree',     type: 'string'},
				{name: 'id_proyek_rat',     type: 'string'},
				{name: 'id_satuan', 		type: 'string'},
				{name: 'kode_tree',     	type: 'string'},
				{name: 'tree_item',     	type: 'string'},
				{name: 'tree_volume',     	type: 'string'},
				{name: 'tree_harga',     	type: 'string'},
				{name: 'tree_satuan',     	type: 'string'},
				{name: 'tree_bidang',     	type: 'string'},
				{name: 'tree_sub_bidang',     	type: 'string'},
				{name: 'tree_sub_bidang',     	type: 'string'},
				{name: 'task',     	type: 'string'}
			]
		});

		var storeTree = Ext.create('Ext.data.TreeStore', {
			model: 'Task',
			proxy: {
				type: 'ajax',
				url: '<?=base_url()?>rencana/get_task_tree_item/<?=$idtender;?>'
				//url: '<?=base_url()?>treegrid.json'
			},
			folderSort: true
		});

		var gridTreeBK = Ext.create('Ext.tree.Panel', {
			title: 'RAT',
			collapsible: true,
			useArrows: true,
			rootVisible: false,
			store: storeTree,
			multiSelect: false,
			singleExpand: true,
			viewConfig: {
				listeners: {
					contextmenu: function(view, index, node, e)
					{
						showMenu(grid, index, event).show(node.getUI().wrap);					
					}
				}			
			},
			columns: [
				{
					xtype: 'treecolumn', 
					text: 'Task',
					flex: 2,
					sortable: true,
					dataIndex: 'task'
				},
				{
					text: 'Assigned To',
					flex: 1,
					dataIndex: 'user',
					sortable: true
				}, 
				{
					text: 'Edit',
					width: 40,
					menuDisabled: true,
					xtype: 'actioncolumn',
					tooltip: 'Edit task',
					align: 'center',
					icon: '<?=base_url();?>/assets/images/edit_task.png',
					handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
						Ext.Msg.alert('Editing' + (record.get('done') ? ' completed task' : '') , record.get('task'));
					}
				}
			],
			bbar: Ext.create('Ext.PagingToolbar', {
				store: storeDCBK,
				displayInfo: true,
				displayMsg: 'Displaying data {0} - {1} of {2}',
				emptyMsg: "No data to display",
			}),			
			dockedItems: [{
				xtype: 'toolbar',
				items: [{
					text: 'Add',
					iconCls: 'icon-add',
					handler: showfrmAddIDCBKBK
				}, '-', 		
				{
					text: 'Delete',
					iconCls: 'icon-del',
					disabled: true,
					handler: function(){
						var selection = gridTreeBK.getView().getSelectionModel().getSelection()[0];
						if (selection) {
							//storeDCBK.remove(selection);
						}					
					}
				}]
			}],
			listeners:{
				beforerender:function(){
					//storeTree.load();
				},
				rowcontextmenu: function(grid, index, event) {
					 event.stopEvent();
					 showMenu(grid, index, event).show(node.getUI().wrap);
				}				
			}						
		});
		
		function showMenu(grid, index, event) {
			  event.stopEvent();
			  var record = grid.getStore().getAt(index);
			  var menu = new Ext.menu.Menu({
					items: [
					{
						text: 'Tambah sub kategori',
						handler: function() {
							alert(record.get('job_id'));
						}
					}, 
					{
						text: 'Edit',
						handler: function() {
							alert(rec.get('a'));
						}
					},
					{
						text: 'Hapus',
						handler: function() {
							alert(rec.get('customer_namea'));
						}
					}					
					]
				}); //.showAt(event.xy);				
		}		
		
		/* end IN-Direct Cost */
				
		var accRAT = Ext.create('Ext.Panel', {
			title: 'Data RAT',
			collapsible: true,
			region:'west',
			margins:'5 0 5 5',
			split:true,
			width: '50%',
			layout:'accordion',
			items: [gridTreeBK]
		});
		
		var DataRAT = Ext.create('Ext.panel.Panel', {
			title: 'Tabel Analisa Pekerjaan / Apek',
			width: '100%',
			layout: 'fit',
			dockedItems: [{
				xtype: 'toolbar',
				dock: 'top',
				items: [
					{ 
						xtype: 'button', 
						text: '<< Kembali ke Menu Tender',
						handler: function()
						{
							window.location = '<?=base_url();?>rencana/entry_rat';
						}
					},
					{ 
						xtype: 'button', 
						text: 'Print' 
					},
					{ 
						xtype: 'button', 
						text: 'Save to Pdf' 
					},
				]
			}],			
			items: [grid_analisa],
		});
		
		var viewport = Ext.create('Ext.Viewport', {
			layout:'border',
			items:[
				accRAT, 
				{
					region:'center',
					margins:'5 5 5 0',
					layout: 'fit',
					items: DataRAT
				}
			]
		});				
	});
</script>

<div id="grid-tender" class="x-hide-display"></div>