<html>
<head>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/examples.js"></script>
	<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/styles.css" /> -->
	<style>

	p {
		margin:5px;
	}

	.footer {
		font-size: 10px;
		font-family: 'Arial'
	}

	.icon-import {
	    background-image:url(<?php echo base_url(); ?>assets/images/file_import.png) !important;
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

	.icon-export-csv {
		background-image:url(<?php echo base_url(); ?>assets/images/csv.png) !important;
	}

	.icon-print {
		background-image:url(<?php echo base_url(); ?>assets/images/print.png) !important;
	}

	.icon-reload {
		background-image:url(<?php echo base_url(); ?>assets/images/reload.png) !important;
	}

	.icon-total {
		background-image:url(<?php echo base_url(); ?>assets/images/sum.png) !important;
	}

	.icon-edit {
		background-image:url(<?php echo base_url(); ?>assets/images/edit.png) !important;
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
	<script type="text/javascript">
	Ext.require([
		'*',
		'Ext.ux.grid.Printer',		
		'Ext.ux.form.SearchField'		
		]);

	Ext.Ajax.timeout = 3600000;
	Ext.override(Ext.form.Basic, {timeout: Ext.Ajax.timeout});
	Ext.override(Ext.data.proxy.Server, {timeout: Ext.Ajax.timeout });
	Ext.override(Ext.data.Connection, {timeout: Ext.Ajax.timeout });
	
	Ext.onReady(function() {
		Ext.QuickTips.init();
		
		Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
		
		/* RAT */
		
		var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';		
		
		Ext.define('ModelAnsat', {
			extend: 'Ext.data.Model',
			fields: [
			'detail_material_id', 'detail_material_kode', 'detail_material_nama', 'detail_material_spesifikasi', 
			'subbidang_kode', 'detail_material_satuan', 'kategori', 'koefisien'
			],
			idProperty: 'ModelBKANSATid'
		});

		var storeDataSatuan = Ext.create('Ext.data.Store', {
			model: 'ModelAnsat',
			extraParams:{
				query: ''
			},
			proxy: {
				type: 'ajax',
				url: '<?=base_url();?>rencana/get_data_ansat/<?=$idtender;?>',
				reader: {
					type: 'json',
					root: 'data',
					totalProperty: 'total'
				},
				simpleSortMode: true				 
			},
			autoLoad: false,
			remoteSort: true,
			pageSize: 100,
			listeners:{
				beforeload : function(){
					storeDataSatuan.proxy.setExtraParam('query', Ext.getCmp('detail_material_nama_id').getValue());
				}
			}
		});			

		var storePilihBiayaUmum = Ext.create('Ext.data.Store', {
			model: 'ModelAnsat',
			proxy: {
				type: 'ajax',
				url: '<?=base_url();?>rencana/get_data_ansat/<?=$idtender;?>',
				reader: {
					type: 'json',
					root: 'data',
					totalProperty: 'total'
				},
				simpleSortMode: true				 
			},
			listeners: {
				'beforeload': function(store, options) {
					storePilihBiayaUmum.proxy.extraParams.subbidang_kode='505'
				}
			},			 
			autoLoad: false,
			remoteSort: true,
			pageSize: 100,
		});			
		
		/* DirectCost */
		Ext.define('DCModel', {
			extend: 'Ext.data.Model',
			fields: [
			'id_rat_direct_cost','id_kategori_pekerjaan', 'kat_rat', 
			'type_rat', 'id_proyek_rat', 'id_satuan_pekerjaan', 
			'kode', 'uraian', 'satuan', 'mharga', 'volume', 'subtotal'
			],
			idProperty: 'dcmodelid'
		});

		var storeDC = Ext.create('Ext.data.Store', {
			pageSize: 50,
			model: 'DCModel',
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
		
		var winDCAdd;
		function showfrmAddDC() {
			if (!winDCAdd) {
				var frmAddDC = Ext.widget({
					xtype: 'form',
					layout: 'form',
					id: 'frmDCAdd',
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
						id: 'id_type_rat',
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
						fieldLabel: 'Pilih satuan Pekerjaan',
						emptyText: 'Pilih satuan Pekerjaan',
						displayField: 'kode_satuan',
						typeAhead: false,
						hideLabel: false,
						hideTrigger:false,
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
							pageSize: 50
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
											Ext.example.msg('Success', action.result.message, function(btn){
												if(btn == 'ok')
												{
													storeDC.load();
													frmAddDC.getForm().reset();
												}
											});
										},
										failure: function(form, action) {
											Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
										}
									});
								} else {
									Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
								}
							}						
						},
						{
							text: 'Reset',
							handler: function() {
								frmAddDC.getForm().reset();
							}
						},
						{
							text: 'Cancel',
							handler: function() {
								frmAddDC.getForm().reset();
								winDCAdd.hide();
							}
						}
						]
					});

winDCAdd = Ext.widget('window', {
	title: 'Tambah Item Direct Cost',
	closeAction: 'hide',
	width: 550,
	height: 250,
	layout: 'fit',
	resizable: true,
	modal: true,
	items: frmAddDC
});
}
winDCAdd.show();
}				

var totalRAT = 0;
var gridDC = Ext.create('Ext.grid.Panel', {
	width: 700,
	height: 500,
	store: storeDC,
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
							var rec = storeDC.getAt(rowIndex);
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
											Ext.example.msg( "Status", text, function(){
												storeDC.load();
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
					store: storeDC,
					displayInfo: true,
					displayMsg: 'Displaying data {0} - {1} of {2}',
					emptyMsg: "No data to display",
				}),
				dockedItems: [{
					xtype: 'toolbar',
					items: [{
						text: 'Add',
						iconCls: 'icon-add',
						handler: showfrmAddDC
					}, '-', 		
					{
						text: 'Delete',
						iconCls: 'icon-del',
						disabled: true,
						handler: function(){
							var selection = gridDC.getView().getSelectionModel().getSelection()[0];
							if (selection) {
							//storeDC.remove(selection);
						}					
					}
				}]
			}],
			listeners:{
				beforerender:function(){
					storeDC.load();
				}
			}			
		});
/* end Direct Cost */


/* start IN-Direct Cost */				
Ext.define('IDCModel', {
	extend: 'Ext.data.Model',
	fields: [
	'id_rat_indirect_cost', 'kat_rat', 'id_proyek_rat', 'id_satuan_pekerjaan', 'kode', 'uraian', 'satuan', 'icharga', 'icvolume', 'subtotal'
	],
	idProperty: 'idcmodelid'
});

var storeIDC = Ext.create('Ext.data.Store', {
	pageSize: 50,
	model: 'IDCModel',
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

var winIDCAdd;
function showfrmAddIDC() {
	if (!winIDCAdd) {
		var frmAddIDC = Ext.widget({
			xtype: 'form',
			layout: 'form',
			id: 'frmIDCAdd',
			url: '<?=base_url();?>rencana/tambah_biaya_umum/<?=$idtender;?>',
			frame: false,
			bodyPadding: '5 5 0',
			width: '100%',
			height: '100%',
			fieldDefaults: {
				msgTarget: 'side',
				labelWidth: 150
			},
			defaultType: 'textfield',										
			items: [
			{
				name: 'idtender',
				xtype: 'hiddenfield',
				value: <?=$idtender;?>,
			},												
			{							
				xtype: 'combo',
				name: 'icitem',							
				afterLabelTextTpl: required,			
				allowBlank: false,							
				store: { 
					fields: ['detail_material_id', 'detail_material_kode', 'detail_material_nama', 'detail_material_satuan', 'subbidang_kode'], 
					pageSize: 10, 
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>rencana/get_tbl_data_umum', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				fieldLabel: 'Uraian Pekerjaan',
				emptyText: 'Ketik uraian pekerjaan..',
				displayField: 'detail_material_nama',
				typeAhead: true,
				hideLabel: false,
				hideTrigger:true,
				anchor: '100%',
				valueField: 'detail_material_nama',
				listeners: {
					'select': function(combo, row, index) {
					}
				},														
				pageSize: 10
			},			
			{
				xtype: 'combo',
				name: 'satuan_id',
				afterLabelTextTpl: required,
				allowBlank: false,
				store: { 
					fields: ['satuan_id','satuan_kode'], 
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
				anchor: '100%',
				displayField: 'satuan_kode',
				valueField: 'satuan_id',
				listeners: {
					'select': function(combo, row, index) {
					}
				},														
			},												
			{
				fieldLabel: 'Harga satuan',
				afterLabelTextTpl: required,
				name: 'icharga',
				xtype: 'numberfield',
				emptyText: 'ketik harga satuan...',
				allowBlank: false,
			},
			{
				fieldLabel: 'volume',
				emptyText: 'volume...',
				afterLabelTextTpl: required,
				name: 'icvolume',
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
								Ext.example.msg('Success', action.result.message, function(btn){
									if(btn == 'ok')
									{
										storeBiayaUmum.load();
										frmAddIDC.getForm().reset();
									}
								});
							},
							failure: function(form, action) {
								Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
							}
						});
					} else {
						Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
					}
				}						
			},
			{
				text: 'Reset',
				handler: function() {
					frmAddIDC.getForm().reset();
				}
			},
			{
				text: 'Close',
				handler: function() {
					frmAddIDC.getForm().reset();
					winIDCAdd.hide();
				}
			}
			]
		});

winIDCAdd = Ext.widget('window', {
	title: 'Tambah uraian Data Umum',
	closeAction: 'hide',
	width: 500,
	height: 200,
	layout: 'fit',
	resizable: true,
	modal: true,
	items: frmAddIDC
});
}
winIDCAdd.show();
}				

/* bank */		
var winIDCAddBank;
function showfrmAddIDCBank() {
	if (!winIDCAddBank) {
		var frmAddIDCBank = Ext.widget({
			xtype: 'form',
			layout: 'form',
			url: '<?=base_url();?>rencana/tambah_uraian_bank/<?=$idtender;?>',
			frame: false,
			bodyPadding: '5 5 0',
			width: 400,
			height: 250,
			fieldDefaults: {
				msgTarget: 'side',
				labelWidth: 150
			},
			defaultType: 'textfield',										
			items: [
			{
				name: 'idtender',
				xtype: 'hiddenfield',
				value: <?=$idtender;?>,
			},												
			{
				name: 'type_uraian',
				xtype: 'hiddenfield',
				value: 'bank',
			},																		
			{							
				xtype: 'combo',
				name: 'icitem_bank',							
				afterLabelTextTpl: required,			
				allowBlank: false,							
				store: { 
					fields: ['detail_material_id', 'detail_material_kode', 'detail_material_nama', 'detail_material_satuan', 'subbidang_kode'], 
					pageSize: 10, 
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>rencana/get_tbl_data_umum', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				fieldLabel: 'Uraian',
				emptyText: 'Ketik uraian..',
				displayField: 'detail_material_nama',
				typeAhead: true,
				hideLabel: false,
				hideTrigger:true,
				anchor: '100%',
				valueField: 'detail_material_nama',
				listeners: {
					'select': function(combo, row, index) {
						var valsatuan = row[0].get('detail_material_satuan');
						Ext.getCmp('satuan_bank').setValue(valsatuan);
					}
				},														
				pageSize: 10
			},
			{
				xtype: 'combo',
				name: 'id_satuan',
				afterLabelTextTpl: required,
				allowBlank: false,
				store: { 
					fields: ['satuan_id','satuan_kode'], 
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
				valueField: 'satuan_id',
				listeners: {
					'select': function(combo, row, index) {
					}
				},														
			},						
			{
				fieldLabel: 'Persentase',
				afterLabelTextTpl: required,
				id: 'persentase',
				name: 'persentase',
				xtype: 'numberfield',
				emptyText: 'persentase...',
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
								Ext.example.msg('Success', action.result.message, function(btn){
									if(btn == 'ok')
									{
										storeBank.load();
										sdummyIDC.load();
										frmAddIDCBank.getForm().reset();
									}
								});
							},
							failure: function(form, action) {
								Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
							}
						});
					} else {
						Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
					}
				}						
			},
			{
				text: 'Reset',
				handler: function() {
					frmAddIDCBank.getForm().reset();
				}
			},
			{
				text: 'Close',
				handler: function() {
					frmAddIDCBank.getForm().reset();
					winIDCAddBank.hide();
				}
			}
			]
		});

winIDCAddBank = Ext.widget('window', {
	title: 'Tambah uraian Data Bank',
	closeAction: 'hide',
	width: 500,
	height: 200,
	layout: 'fit',
	resizable: true,
	modal: true,
	items: frmAddIDCBank
});
}
winIDCAddBank.show();
}								
/*end bank*/

/* asuransi */ 
var winIDCAddAsuransi;
function showfrmAddIDCAsuransi() {
	if (!winIDCAddAsuransi) {
		var frmAddIDCAsuransi = Ext.widget({
			xtype: 'form',
			layout: 'form',
			url: '<?=base_url();?>rencana/tambah_uraian_asuransi/<?=$idtender;?>',
			frame: false,
			bodyPadding: '5 5 0',
			width: 400,
			height: 250,
			fieldDefaults: {
				msgTarget: 'side',
				labelWidth: 150
			},
			defaultType: 'textfield',										
			items: [
			{
				name: 'idtender',
				xtype: 'hiddenfield',
				value: <?=$idtender;?>,
			},												
			{							
				xtype: 'combo',
				name: 'icitem_asuransi',							
				afterLabelTextTpl: required,			
				allowBlank: false,							
				store: { 
					fields: ['detail_material_id', 'detail_material_kode', 'detail_material_nama', 'detail_material_satuan', 'subbidang_kode'], 
					pageSize: 20, 
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>rencana/get_tbl_data_umum', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				fieldLabel: 'Uraian',
				emptyText: 'Ketik uraian..',
				displayField: 'detail_material_nama',
				typeAhead: true,
				hideLabel: false,
				hideTrigger:true,
				anchor: '100%',
				valueField: 'detail_material_nama',
				listeners: {
					'select': function(combo, row, index) {
						var valsatuan = row[0].get('detail_material_satuan');
						Ext.getCmp('satuan_bank').setValue(valsatuan);
					}
				},														
				pageSize: 20
			},
			{
				xtype: 'combo',
				name: 'id_satuan',
				afterLabelTextTpl: required,
				allowBlank: false,
				store: { 
					fields: ['satuan_id','satuan_kode'], 
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
				typeAhead: false,
				hideLabel: false,
				hideTrigger:false,
				anchor: '100%',
				displayField: 'satuan_kode',
				valueField: 'satuan_id',
				listeners: {
					'select': function(combo, row, index) {
					}
				},														
			},						
			{
				fieldLabel: 'Persentase',
				afterLabelTextTpl: required,
				name: 'persentase',
				xtype: 'numberfield',
				emptyText: 'persentase...',
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
								Ext.example.msg('Success', action.result.message, function(){									   
									storeAsuransi.load();
									sdummyIDC.load();
									frmAddIDCAsuransi.getForm().reset();
								});
							},
							failure: function(form, action) {
								Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
							}
						});
					} else {
						Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
					}
				}						
			},
			{
				text: 'Reset',
				handler: function() {
					frmAddIDCAsuransi.getForm().reset();
				}
			},
			{
				text: 'Close',
				handler: function() {
					frmAddIDCAsuransi.getForm().reset();
					winIDCAddAsuransi.hide();
				}
			}
			]
		});

winIDCAddAsuransi = Ext.widget('window', {
	title: 'Tambah uraian Data Asuransi',
	closeAction: 'hide',
	width: 500,
	height: 200,
	layout: 'fit',
	resizable: true,
	modal: true,
	items: frmAddIDCAsuransi
});
}
winIDCAddAsuransi.show();
}								
/* end asuransi */

var winIDCfrm;
function showwinIDCfrm() {
	if (!winIDCfrm) {
		var frmAddIDC = Ext.widget({
			xtype: 'form',
			layout: 'form',
			url: '<?=base_url();?>rencana/tambah_idc_ba/<?=$idtender;?>',
			frame: false,
			bodyPadding: '5 5 0',
			width: 400,
			height: 550,
			fieldDefaults: {
				msgTarget: 'side',
				labelWidth: 150
			},
			defaultType: 'textfield',										
			items: [
			{
				name: 'id_type_rat',
				xtype: 'hiddenfield',
				value: 2,
			},						
			{
				name: 'idtender',
				xtype: 'hiddenfield',
				value: <?=$idtender;?>,
			},												
			{
				xtype:'fieldset',
				title: 'BANK',
				defaultType: 'textfield',
				layout: 'anchor',
				defaults: {
					anchor: '100%'
				},
				items :[
				{
					fieldLabel: 'Provisi Jaminan',
					emptyText: 'provisi jaminan (dalam %)...',
					afterLabelTextTpl: required,
					name: 'provisi_jaminan',
					xtype: 'numberfield',
					allowBlank: false,
				},
				{
					fieldLabel: 'Bunga Bank',
					emptyText: 'bunga bank (dalam %)...',
					afterLabelTextTpl: required,
					name: 'bunga_bank',
					xtype: 'numberfield',
					allowBlank: false,
				},
				]					
			},						
			{
				xtype:'fieldset',
				title: 'ASURANSI',
				defaultType: 'textfield',
				layout: 'anchor',
				defaults: {
					anchor: '100%'
				},
				items :[
				{
					fieldLabel: 'Astek',
					emptyText: 'astek (dalam %)...',
					afterLabelTextTpl: required,
					name: 'astek',
					xtype: 'numberfield',
					allowBlank: false,
				},                                                
				{
					fieldLabel: 'C.A.R',
					emptyText: 'car (dalam %)...',
					afterLabelTextTpl: required,
					name: 'car',
					xtype: 'numberfield',
					allowBlank: false,
				},                                                							
				]					
			},												
			],
			buttons: [{
				text: 'Save',
				handler: function() {
					var form = this.up('form').getForm();
					if (form.isValid()) {
						form.submit({
							success: function(form, action) {
								Ext.example.msg('Success', action.result.message, function(btn){
									if(btn == 'ok')
									{
										storeIDC.load();
										frmAddIDC.getForm().reset();
									}
								});
							},
							failure: function(form, action) {
								Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
							}
						});
					} else {
						Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
					}
				}						
			},
			{
				text: 'Reset',
				handler: function() {
					frmAddIDC.getForm().reset();
				}
			},
			{
				text: 'Close',
				handler: function() {
					frmAddIDC.getForm().reset();
					winIDCfrm.hide();
				}
			}
			]
		});

winIDCfrm = Ext.widget('window', {
	title: 'Tambah Item in-Direct Cost',
	closeAction: 'hide',
	width: '80%',
	height: '85%',
	layout: 'fit',
	resizable: true,
	modal: true,
	items: [
	{
		region: 'west',
		xtype: 'tabpanel',
		items: [
		{
			title: 'Bank',
			items: gridBank,
			layout: 'fit',
			listeners: {
				activate: function(tab){
					setTimeout(function() {
						storeBank.load();										
					}, 1);
				}
			},																
		}, 
		{
			title: 'Asuransi',
			items: gridAsuransi,
			layout: 'fit',
			listeners: {
				activate: function(tab){
					setTimeout(function() {
						storeAsuransi.load();										
					}, 1);
				}
			},								
		}, 							
		{
			title: 'Biaya Umum',
			items: gridBiayaUmum,
			layout: 'fit',
			listeners: {
				activate: function(tab){
					setTimeout(function() {
						storeBiayaUmum.load();										
					}, 1);
				}
			},																
		}, 
		]
	}],
	dockedItems:{
		xtype:'toolbar',
		dock:'top',
		items:[
		{
			text:'Kembali',
			iconCls: 'icon-back',
			handler: function(){
				winIDCfrm.hide();
			}
		}
		]
	}				
});
}
winIDCfrm.show();
}			

Ext.define('mdlBiayaUmum', {
	extend: 'Ext.data.Model',
	fields: [
	'id_rat_biaya_umum', 'id_proyek_rat', 'kode_material', 'icitem', 'icvolume', 'icharga', 'satuan_nama', 'subtotal'
	],
	idProperty: 'id_rat_biaya_umum'
});		

var storeBiayaUmum = Ext.create('Ext.data.Store', {
	remoteSort: true,
	pageSize: 200,
	model: 'mdlBiayaUmum',
	proxy: {
		type: 'ajax',
		url: '<?=base_url();?>rencana/get_data_rat_biayaumum/<?=$idtender;?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	},
	autoLoad: false	 
});

/* window biaya umum */

var gridPilihBU = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	store: storePilihBiayaUmum,
	disableSelection: false,
	loadMask: true,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
	}),					
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},		
	columns:[
	{ 
		header: 'Kode', 
		dataIndex: 'detail_material_kode', 
		width: 70,
	},
	{
		text: 'Nama Material',
		dataIndex: 'detail_material_nama',
		flex: 3,
		sortable: false
	},
	{
		text: 'Satuan',
		dataIndex: 'detail_material_satuan',
		flex: 1,
		sortable: false
	},
	{
		text: "Kategori",
		dataIndex: 'kategori',
		flex: 2,
		sortable: false
	},						
	],
	bbar: Ext.create('Ext.PagingToolbar', {
		store: storePilihBiayaUmum,
		displayInfo: true,
		displayMsg: 'Displaying data {0} - {1} of {2}',
		emptyMsg: "No data to display",
	}),			
	dockedItems: [
	{

		xtype: 'toolbar',
		dock: 'top',						
		items: [
		'->',
		{
			flex: 1,
			fieldLabel: 'Pencarian',
			labelWidth: 70,									
			tooltip:'masukan kode analisa / uraian',
			emptyText: 'kode material / nama material...',
			xtype: 'searchfield',
			name: 'caribiayaumum',
			store: storePilihBiayaUmum,
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
			text: 'Simpan Pilihan',
			flex: 1,							
			handler: function(){
				var records = gridPilihBU.getView().getSelectionModel().getSelection(),
				kd = [],nama = [],sat = [],dmid = [];
				Ext.Array.each(records, function(rec){
					kd.push(rec.get('detail_material_kode'));
					nama.push(rec.get('detail_material_nama'));
					sat.push(rec.get('detail_material_satuan'));
					dmid.push(rec.get('detail_material_id'));
				});
				if(nama != '')
				{
					Ext.Ajax.request({
						url: '<?=base_url();?>rencana/tambah_biaya_umum_updated',
						method: 'POST',
						params: {												
							'kode_material' : kd.join(','),
							'nama_material' : nama.join(','),
							'satuan' : sat.join(','),
							'id_tender' : <?=$idtender;?>
						},								
						success: function(response) {
							Ext.example.msg( "Tambah Biaya Umum", response.responseText, function(){								
								storePilihBiayaUmum.load({
									params:{'subbidang_kode':'505'},
									scope: this,
								});	
								storeBiayaUmum.load();
								sdummyIDC.load();
							});											
						},
						failure: function(response) {
							Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem or duplicate entries!');
						}
					});			   																													
				} else 
				{
					Ext.example.msg('Error', 'Silahkan pilih material');
				}
			}
		},'-',
		{
			text: ' Tutup ',
			flex: 1,
			handler: function()
			{
				winPilihBiayaUmum.hide();
			}
		},
		]
	}
	],
	listeners:{
		beforerender:function(){
			storePilihBiayaUmum.load({
				params:{'subbidang_kode':'505'},
				scope: this,
			});											
		},
		itemclick: function(dv, record, item, index, e) {
		}						
	},
});

var winPilihBiayaUmum = Ext.widget('window', {
	closeAction: 'hide',
	closable: false,
	width: '80%',
	height: '80%',
	layout: 'fit',
	resizable: false,
	modal: true,
	items: gridPilihBU,
});

/* end window biaya umum */

var gridBiayaUmum = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	store: storeBiayaUmum,
	disableSelection: false,
	loadMask: true,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
	}),								
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},		
	plugins: Ext.create('Ext.grid.plugin.RowEditing', {
		clicksToMoveEditor: 1,
		autoCancel: false,
		listeners: {
			'edit': function () {
				var editedRecords = gridBiayaUmum.getStore().getUpdatedRecords();
				Ext.Ajax.request({
					url: '<?=base_url();?>rencana/update_idc_biaya_umum',
					method: 'POST',
					params: {								
						'id_proyek_rat' : editedRecords[0].data.id_proyek_rat,
						'id_rat_biaya_umum' : editedRecords[0].data.id_rat_biaya_umum,
						'id_satuan' : editedRecords[0].data.satuan_nama,
						'icitem' : editedRecords[0].data.icitem,
						'icvolume' : editedRecords[0].data.icvolume,
						'icharga' : editedRecords[0].data.icharga
					},								
					success: function(response) {
						var text = response.responseText;
						Ext.example.msg( "Status", text, function(){
							storeBiayaUmum.load();
							sdummyIDC.load();
						});											
					},
					failure: function() {
						Ext.example.msg( "Error", "Data GAGAL diupdate!");											
					}
				});			   																										
			}
		}				
	}),						
columns:[
{
	xtype: 'rownumberer',
	width: 35,
	sortable: false
},
{
	text: "KODE",
	dataIndex: 'kode_material',
	width: 70,
	sortable: false,
},				
{
	text: "URAIAN",
	dataIndex: 'icitem',
	flex: 3,
	sortable: false,
},				
{
	text: "SATUAN",
	dataIndex: 'satuan_nama',
	flex: 1,
	sortable: false,
					/*
					editor: {
						xtype: 'combo',
						afterLabelTextTpl: required,
						allowBlank: false,
						store: { 
							fields: ['satuan_id','satuan_kode'], 
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
						triggerAction : 'all',					
						anchor: '100%',
						displayField: 'satuan_kode',
						valueField: 'satuan_id',
					}
					*/
				},
				{
					text: "HARGA",
					dataIndex: 'icharga',
					renderer: Ext.util.Format.numberRenderer('00,000'),
					flex: 1,
					align: 'right',
					sortable: false,
					editor: {
						xtype: 'numberfield',
					}															
				},
				{
					text: "VOLUME",
					dataIndex: 'icvolume',
					flex: 1,
					align: 'right',
					sortable: false,
					editor: {
						xtype: 'numberfield',
					}															
				},
				{
					text: "SUBTOTAL",
					dataIndex: 'subtotal',
					flex: 1,
					align: 'right',
					sortable: false,
					groupable: false,
					renderer: Ext.util.Format.numberRenderer('00,000'),
				},			
				/*
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/delete.gif',  
								tooltip: 'Hapus item Biaya Umum',
								handler: function(grid, rowIndex, colIndex) {
									var rec = storeBiayaUmum.getAt(rowIndex);
									var id = rec.get('id_rat_biaya_umum');
									Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini ('+rec.get('icitem')+') ?',function(resbtn){
										if(resbtn == 'yes')
										{
											Ext.Ajax.request({
												url: '<?=base_url();?>rencana/del_item_biaya_umum',
												method: 'POST',
												params: {
													'id_rat_biaya_umum' : id
												},								
												success: function(response) {
													var text = response.responseText;
													Ext.example.msg( "Status", text, function(){
														storeBiayaUmum.load();
													});											
												},
												failure: function() {
													Ext.example.msg( "Error", "Data GAGAL dihapus!");											
												}
											});			   																			
										}
									})
								}
						}]
                },
                */				
                ],
                bbar: Ext.create('Ext.PagingToolbar', {
                	store: storeBiayaUmum,
                	displayInfo: true,
                	displayMsg: 'Displaying data {0} - {1} of {2}',
                	emptyMsg: "No data to display",
                }),
                dockedItems: [
                {
                	xtype: 'toolbar',
                	dock: 'top',
                	items: 
                	[{
                		text: 'Tambah uraian Biaya Umum',
                		iconCls: 'icon-add',
                		handler: function(){
                			winPilihBiayaUmum.on('show', function(win) {	   
                				storePilihBiayaUmum.load({
                					params:{'subbidang_kode':'505'},
                					scope: this,
                				});
                			});
                			winPilihBiayaUmum.doLayout();						
                			winPilihBiayaUmum.show();
					} //showfrmAddIDC
				},'-',
				{
					text: 'Info: klik dua kali pada item yang mau diedit.',
				}]
			},
			{
				xtype: 'toolbar',
				dock: 'bottom',
				items: 
				[{
					text: 'Delete',
					iconCls: 'icon-del',
					handler: function(){
						var records = gridBiayaUmum.getView().getSelectionModel().getSelection(),
						itemid = [];
						Ext.Array.each(records, function(rec){
							itemid.push(rec.get('id_rat_biaya_umum'));
						});
						if(itemid != '')
						{
							Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item tersebut?',function(resbtn){
								if(resbtn == 'yes')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/del_biaya_umum',
										method: 'POST',											
										params: {												
											'id_rat_biaya_umum' : itemid.join(','),
											'id_tender' : <?=$idtender;?>
										},								
										success: function(response) {
											Ext.example.msg('OK', 'Item berhasil dihapus!', function(){
												storeBiayaUmum.load();
												sdummyIDC.load();
											});
										},
										failure: function(response) {
											Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
										}
									});			   	
								}
							});	
						} else 
						{
							Ext.example.msg('Error', 'Silahkan pilih item yang mau di-hapus!');
						}					
					} 
				}]
			}			
			],
			listeners:{
				beforerender:function(){
					storeBiayaUmum.load();
				}
			}			
		});

/* biaya bank */
var gridPilihBiayaBank = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	store: storePilihBiayaUmum,
	disableSelection: false,
	loadMask: true,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
	}),					
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},		
	columns:[
	{ 
		header: 'Kode', 
		dataIndex: 'detail_material_kode', 
		width: 70,
	},
	{
		text: 'Nama Material',
		dataIndex: 'detail_material_nama',
		flex: 3,
		sortable: false
	},
	{
		text: 'Satuan',
		dataIndex: 'detail_material_satuan',
		flex: 1,
		sortable: false
	},
	{
		text: "Kategori",
		dataIndex: 'kategori',
		flex: 2,
		sortable: false
	},						
	],
	bbar: Ext.create('Ext.PagingToolbar', {
		store: storePilihBiayaUmum,
		displayInfo: true,
		displayMsg: 'Displaying data {0} - {1} of {2}',
		emptyMsg: "No data to display",
	}),			
	dockedItems: [
	{

		xtype: 'toolbar',
		dock: 'top',						
		items: [
		'->',
		{
			flex: 1,
			fieldLabel: 'Pencarian',
			labelWidth: 70,									
			tooltip:'masukan kode analisa / uraian',
			emptyText: 'kode material / nama material...',
			xtype: 'searchfield',
			name: 'caribiayaumum',
			store: storePilihBiayaUmum,
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
			text: 'Simpan Pilihan',
			flex: 1,							
			handler: function(){
				var records = gridPilihBiayaBank.getView().getSelectionModel().getSelection(),
				kd = [],nama = [],sat = [],dmid = [];
				Ext.Array.each(records, function(rec){
					kd.push(rec.get('detail_material_kode'));
					nama.push(rec.get('detail_material_nama'));
					sat.push(rec.get('detail_material_satuan'));
					dmid.push(rec.get('detail_material_id'));
				});
				if(nama != '')
				{
					Ext.Ajax.request({
						url: '<?=base_url();?>rencana/tambah_biaya_bank_updated',
						method: 'POST',
						params: {												
							'kode_material' : kd.join(','),
							'nama_material' : nama.join(','),
							'satuan' : sat.join(','),
							'id_tender' : <?=$idtender;?>
						},								
						success: function(response) {
							Ext.example.msg( "Tambah Biaya Bank", response.responseText, function(){								
								storePilihBiayaUmum.load({
									params:{'subbidang_kode':'504'},
									scope: this,
								});	
								storeBank.load();
								sdummyIDC.load();
							});											
						},
						failure: function(response) {
							Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem or duplicate entries!');
						}
					});			   																													
				} else 
				{
					Ext.example.msg('Error', 'Silahkan pilih material');
				}
			}
		},'-',
		{
			text: ' Tutup ',
			flex: 1,
			handler: function()
			{
				winPilihBiayaBank.hide();
			}
		},
		]
	}
	],
	listeners:{
		beforerender:function(){
			storePilihBiayaUmum.load({
				params:{'subbidang_kode':'504'},
				scope: this,
			});											
		},
		itemclick: function(dv, record, item, index, e) {
		}						
	},
});

var winPilihBiayaBank = Ext.widget('window', {
	closeAction: 'hide',
	closable: false,
	width: '80%',
	height: '80%',
	layout: 'fit',
	resizable: false,
	modal: true,
	items: gridPilihBiayaBank,
});

Ext.define('mdlBank', {
	extend: 'Ext.data.Model',
	fields: [
	'id_rat_idc_bank','icitem_bank', 'id_proyek_rat', 'persentase', 'satuan_nama','kode_material'
	],
	idProperty: 'idcmodelid'
});		

var storeBank = Ext.create('Ext.data.Store', {
	remoteSort: true,
	model: 'mdlBank',			
	proxy: {
		type: 'ajax',
		url: '<?=base_url();?>rencana/get_data_bank/<?=$idtender;?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	},
	autoLoad: false	 
});

var gridBank = Ext.create('Ext.grid.Panel', {
	width: 700,
	height: 500,
	store: storeBank,
	disableSelection: false,
	loadMask: true,
	plugins: Ext.create('Ext.grid.plugin.RowEditing', {
		clicksToMoveEditor: 1,
		autoCancel: false,
		listeners: {
			'edit': function () {
				var editedRecords = gridBank.getStore().getUpdatedRecords();
				Ext.Ajax.request({
					url: '<?=base_url();?>rencana/update_data_bank',
					method: 'POST',
					params: {									
						'id_proyek_rat' : editedRecords[0].data.id_proyek_rat,
						'id_rat_idc_bank' : editedRecords[0].data.id_rat_idc_bank,
						'icitem_bank' : editedRecords[0].data.icitem_bank,
						'id_satuan' : editedRecords[0].data.satuan_nama,
						'persentase' : editedRecords[0].data.persentase
					},								
					success: function(response) {
						var text = response.responseText;
						Ext.example.msg( "Status", text, function(){
							storeBank.load();
							sdummyIDC.load();											
						});											
					},
					failure: function() {
						Ext.example.msg( "Error", "Update GAGAL!");											
					}
				});			   																										
			}
		}				
	}),	
	columns:[
	{
		xtype: 'rownumberer',
		width: 35,
		sortable: false
	},
	{
		text: "KODE",
		dataIndex: 'kode_material',
		width: 70,
		sortable: false,
	},				
	{
		text: "URAIAN",
		dataIndex: 'icitem_bank',
		width: 250,
		sortable: false,
		editor: {
			xtype: 'textfield',
		}										
	},				
	{
		text: "SATUAN",
		dataIndex: 'satuan_nama',
		width: 100,
		sortable: false,
		editor: {
			xtype: 'combo',
			afterLabelTextTpl: required,
			allowBlank: false,
			store: { 
				fields: ['satuan_id','satuan_kode'], 
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
			triggerAction : 'all',					
			anchor: '100%',
			displayField: 'satuan_kode',
			valueField: 'satuan_id',
		}					
	},
	{
		text: "BOBOT (%)",
		dataIndex: 'persentase',
		width: 100,
		align: 'right',
		sortable: false,
		editor: {
			xtype: 'numberfield',
		}															
	},
				/*
				{
					text: "JUMLAH (Rp)",
					dataIndex: 'jumlah_rp',
					width: 200,
					align: 'right',
					sortable: false,
					renderer: Ext.util.Format.numberRenderer('00,000'),					
				},
				*/				
				{
					xtype: 'actioncolumn',
					width: 25,
					align: 'center',									
					items: [{
						icon   : '<?=base_url();?>assets/images/delete.gif',  
						tooltip: 'Hapus item satuan pekerjaan',
						handler: function(grid, rowIndex, colIndex) {
							var rec = storeBank.getAt(rowIndex);
							var id = rec.get('id_rat_idc_bank');
							Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
								if(resbtn == 'yes')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/del_item_idc_bank',
										method: 'POST',
										params: {
											'id_rat_idc_bank' : id
										},								
										success: function(response) {
											var text = response.responseText;
											Ext.example.msg( "Status", text, function(){
												storeBank.load();
												sdummyIDC.load();
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
					store: storeBank,
					displayInfo: true,
					displayMsg: 'Displaying data {0} - {1} of {2}',
					emptyMsg: "No data to display",
				}),			
				dockedItems: [{
					xtype: 'toolbar',
					items: 
					[
					{
						text: 'Tambah uraian Bank',
						iconCls: 'icon-add',
						handler: function() {
							winPilihBiayaBank.on('show', function(win) {	   
								storePilihBiayaUmum.load({
									params:{'subbidang_kode':'504'},
									scope: this,
								});											
							});
							winPilihBiayaBank.doLayout();						
							winPilihBiayaBank.show();							
						}
							//showfrmAddIDCBank
						}, '-',
						{
							text: 'Info: klik dua kali pada item yang mau diedit.',
						}
						]
					}],
					listeners:{
						beforerender:function(){
							storeBank.load();
						}
					}			
				});

/*  asuransi  */
var gridPilihBiayaAsuransi = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	store: storePilihBiayaUmum,
	disableSelection: false,
	loadMask: true,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
	}),					
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},		
	columns:[
	{ 
		header: 'Kode', 
		dataIndex: 'detail_material_kode', 
		width: 70,
	},
	{
		text: 'Nama Material',
		dataIndex: 'detail_material_nama',
		flex: 3,
		sortable: false
	},
	{
		text: 'Satuan',
		dataIndex: 'detail_material_satuan',
		flex: 1,
		sortable: false
	},
	{
		text: "Kategori",
		dataIndex: 'kategori',
		flex: 2,
		sortable: false
	},						
	],
	bbar: Ext.create('Ext.PagingToolbar', {
		store: storePilihBiayaUmum,
		displayInfo: true,
		displayMsg: 'Displaying data {0} - {1} of {2}',
		emptyMsg: "No data to display",
	}),			
	dockedItems: [
	{

		xtype: 'toolbar',
		dock: 'top',						
		items: [
		'->',
		{
			flex: 1,
			fieldLabel: 'Pencarian',
			labelWidth: 70,									
			tooltip:'masukan kode analisa / uraian',
			emptyText: 'kode material / nama material...',
			xtype: 'searchfield',
			name: 'caribiayaumum',
			store: storePilihBiayaUmum,
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
			text: 'Simpan Pilihan',
			flex: 1,							
			handler: function(){
				var records = gridPilihBiayaAsuransi.getView().getSelectionModel().getSelection(),
				kd = [],nama = [],sat = [],dmid = [];
				Ext.Array.each(records, function(rec){
					kd.push(rec.get('detail_material_kode'));
					nama.push(rec.get('detail_material_nama'));
					sat.push(rec.get('detail_material_satuan'));
					dmid.push(rec.get('detail_material_id'));
				});
				if(nama != '')
				{
					Ext.Ajax.request({
						url: '<?=base_url();?>rencana/tambah_biaya_asuransi_updated',
						method: 'POST',
						params: {												
							'kode_material' : kd.join(','),
							'nama_material' : nama.join(','),
							'satuan' : sat.join(','),
							'id_tender' : <?=$idtender;?>
						},								
						success: function(response) {
							Ext.example.msg( "Tambah Biaya Asuransi", response.responseText, function(){								
								storePilihBiayaUmum.load({
									params:{'subbidang_kode':'504'},
									scope: this,
								});	
								storeAsuransi.load();
								sdummyIDC.load();
							});											
						},
						failure: function(response) {
							Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem or duplicate entries!');
						}
					});			   																													
				} else 
				{
					Ext.example.msg('Error', 'Silahkan pilih material');
				}
			}
		},'-',
		{
			text: ' Tutup ',
			flex: 1,
			handler: function()
			{
				winPilihBiayaAsuransi.hide();
			}
		},
		]
	}
	],
	listeners:{
		beforerender:function(){
			storePilihBiayaUmum.load({
				params:{'subbidang_kode':'505'},
				scope: this,
			});											
		},
		itemclick: function(dv, record, item, index, e) {
		}						
	},
});

var winPilihBiayaAsuransi = Ext.widget('window', {
	closeAction: 'hide',
	closable: false,
	width: '80%',
	height: '80%',
	layout: 'fit',
	resizable: false,
	modal: true,
	items: gridPilihBiayaAsuransi,
});

Ext.define('mdlAsuransi', {
	extend: 'Ext.data.Model',
	fields: [
	'id_rat_idc_asuransi','icitem_asuransi', 
	'id_proyek_rat', 'persentase', 'satuan_nama','kode_material'
	],
	idProperty: 'idcmodelid'
});		

var storeAsuransi = Ext.create('Ext.data.Store', {
	remoteSort: true,			
	model: 'mdlAsuransi',			
	proxy: {
		type: 'ajax',
		url: '<?=base_url();?>rencana/get_data_rat_asuransi/<?=$idtender;?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	},
	autoLoad: false	 
});

var rowEditAsuransi = Ext.create('Ext.grid.plugin.RowEditing', {
	clicksToMoveEditor: 1,
	autoCancel: false,
	listeners: {
		'edit': function () {
			var editedRecords = gridAsuransi.getStore().getUpdatedRecords();
			Ext.Ajax.request({
				url: '<?=base_url();?>rencana/update_idc_asuransi',
				method: 'POST',
				params: {
					'id_proyek_rat' : editedRecords[0].data.id_proyek_rat,
					'id_rat_idc_asuransi' : editedRecords[0].data.id_rat_idc_asuransi,
					'icitem_asuransi' : editedRecords[0].data.icitem_asuransi,
					'id_satuan' : editedRecords[0].data.satuan_nama,
					'persentase' : editedRecords[0].data.persentase
				},								
				success: function(response) {
					var text = response.responseText;
					Ext.example.msg( "Status", text, function(){
						storeAsuransi.load();
						sdummyIDC.load();
					});											
				},
				failure: function() {
				}
			});			   																										
		}
	}				
});

var gridAsuransi = Ext.create('Ext.grid.Panel', {
	width: 700,
	height: 500,
	store: storeAsuransi,
	disableSelection: false,
	loadMask: true,
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},		
	plugins: [rowEditAsuransi],
	columns:[
	{
		xtype: 'rownumberer',
		width: 35,
		sortable: false
	},
	{
		text: "KODE",
		dataIndex: 'kode_material',
		width: 70,
		sortable: false,
	},		
	{
		text: "URAIAN",
		dataIndex: 'icitem_asuransi',
		width: 250,
		sortable: false,
		editor: {
			xtype: 'textfield',
		}
	},		
	{
		text: "SATUAN",
		dataIndex: 'satuan_nama',
		width: 100,
		sortable: false,
		editor: {
			xtype: 'combo',
			afterLabelTextTpl: required,
			allowBlank: false,
			store: { 
				fields: ['satuan_id','satuan_kode'], 
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
			triggerAction : 'all',					
			anchor: '100%',
			displayField: 'satuan_kode',
			valueField: 'satuan_id',
						/*
						renderer: function(value, metaData, record, rowIndex, colIndex, store) {
							var idx = this.columns[colIndex].field.store.find('satuan_id', value);
							alert(this.columns[colIndex].field.store.getAt(idx));
							return idx !== -1 ? this.columns[colIndex].field.store.getAt(idx).get('satuan_kode') : '';
						},
						*/						
					}
				},
				{
					text: "BOBOT (%)",
					dataIndex: 'persentase',
					width: 100,
					align: 'right',
					sortable: true,
					editor: {
						xtype: 'numberfield',
					}					
				},
				/*
				{
					text: "JUMLAH (Rp)",
					dataIndex: 'jumlah_rp',
					width: 200,
					align: 'right',
					sortable: true,
					renderer: Ext.util.Format.numberRenderer('00,000'),
				},
				*/
				{
					xtype: 'actioncolumn',
					width: 25,
					align: 'center',									
					items: [{
						icon   : '<?=base_url();?>assets/images/delete.gif',  
						tooltip: 'Hapus item satuan pekerjaan',
						handler: function(grid, rowIndex, colIndex) {
							var rec = storeAsuransi.getAt(rowIndex);
							var id = rec.get('id_rat_idc_asuransi');
							Ext.MessageBox.confirm('Delete item Asuransi', 'Apakah anda akan menghapus item ini ('+rec.get('icitem_asuransi')+') ?',function(resbtn){
								if(resbtn == 'yes')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/del_item_idc_asuransi',
										method: 'POST',
										params: {
											'id_rat_idc_asuransi' : id
										},								
										success: function(response) {
											var text = response.responseText;
											Ext.example.msg( "Status", text, function(){
												storeAsuransi.load();
												sdummyIDC.load();
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
					store: storeAsuransi,
					displayInfo: true,
					displayMsg: 'Displaying data {0} - {1} of {2}',
					emptyMsg: "No data to display",
				}),			
				dockedItems: [{
					xtype: 'toolbar',
					items: 
					[
					{
						text: 'Tambah uraian Asuransi',
						iconCls: 'icon-add',
						handler: function(){
							winPilihBiayaAsuransi.on('show', function(win) {	   
								storePilihBiayaUmum.load({
									params:{'subbidang_kode':'504'},
									scope: this,
								});
							});
							winPilihBiayaAsuransi.doLayout();						
							winPilihBiayaAsuransi.show();						
						}
						//showfrmAddIDCAsuransi
					}, '-',
					{
						text: 'Info: klik dua kali pada item yang mau diedit.',
					},
					]
				}],
				listeners:{
					beforerender:function(){
						storeAsuransi.load();
					}
				}			
			});

/* end IN-Direct Cost */


/* BIAYA KONSTRUKSI */ 
Ext.define('DCModelBK', {
	extend: 'Ext.data.Model',
	fields: [
	'id_simpro_rat_analisa', 'detail_material_kode','detail_material_nama',
	'detail_material_satuan','subbidang_name', 'asatuan',
	{name: 'aharga', type: 'float'},
	{name: 'avolume', type: 'float'},
	{name: 'akoefisien', type: 'float'},
	{name: 'subtotal', type: 'float'}, 
	'kode_rap', 'tree_item'
	],
	idProperty: 'DCModelBKid'
});

var storeDCBK = Ext.create('Ext.data.Store', {
	pageSize: 2000,
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
			groupField: ['tree_item'], //subbidang_name
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
											Ext.example.msg('Success', action.result.message, function(btn){
												if(btn == 'ok')
												{
													storeDCBK.load();
													frmAddDCBK.getForm().reset();
												}
											});
										},
										failure: function(form, action) {
											Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
										}
									});
								} else {
									Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
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
	title: 'Tambah Uraian Analisa Pekerjaan / APEK',
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

/* tambah item menu BK */
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
						url: '<?=base_url();?>rencana/get_detailmaterial_kode/',
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
				valueField: 'detail_material_nama',
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
							matchFieldWidth: false,
							listWidth: 50,
							hideLabel: false,
							hideTrigger: false,
							displayField: 'satuan_kode',
							valueField: 'satuan_kode',
							listeners: {
								'select': function(combo, row, index) {
									//var valharga = row[0].get('mharga');
									//Ext.getCmp('hargasatuan_idc').setValue(valharga);
								}
							},														
						},
						{
							name: 'volume',
							xtype: 'numberfield',
							fieldLabel: 'Volume',
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
													storeTree.load();
													frmAddIDCBK.getForm().reset();
												}
											});
										},
										failure: function(form, action) {
											Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
										}
									});
								} else {
									Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
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
	title: 'Tambah Item Biaya Konstruksi :: Proyek -> <?=$data_tender['nama_proyek'];?>',
	closeAction: 'hide',
	width: '50%',
	height: '40%',
	layout: 'fit',
	resizable: true,
	modal: true,
	items: frmAddIDCBK
});
}
winIDCAddBK.show();
}						
/* end tambah item menu BK */

/* data analisa */

/* edit harga asat */
Ext.define('mdlEditHargaASAT', {
	extend: 'Ext.data.Model',
	fields: [
	'kode_material','detail_material_nama','detail_material_satuan','harga','kode_rap','keterangan','kategori'
	]
});

var storeEditHargaASAT = Ext.create('Ext.data.Store', {
	model: 'mdlEditHargaASAT',
	pageSize: 200,  
	remoteFilter: true,
	autoLoad: false,
	proxy: {
		type: 'ajax',
		url: '<?php echo base_url() ?>rencana/daftar_analisa/edit_harga_satuan_asat/<?=$idtender;?>',
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
	viewConfig:{
    markDirty:false
	},
	store: storeEditHargaASAT,
	plugins: Ext.create('Ext.grid.plugin.RowEditing', {
					//clicksToEdit: 1,
					listeners : {
						edit : function() {
							var editedRecords = gridEditHargaASAT.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/update_harga_asat',
								method: 'POST',
								params: {
									'id_tender' : <?=$idtender;?>,
									'kode_material' : editedRecords[0].data.kode_material,
									'harga' : editedRecords[0].data.harga,
									'kode_rap' : editedRecords[0].data.kode_rap,
									'keterangan' : editedRecords[0].data.keterangan,
								},								
								success: function(response) {
									Ext.Msg.alert("Status", response.responseText, function()
									{
										storeEditHargaASAT.load();
										storeASAT.load();
										// storeTree.load();
										storeAnalisaPekerjaan.load();
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
{text: "Uraian", flex: 2, sortable: false, dataIndex: 'detail_material_nama'},
{text: "Satuan", flex: 1, sortable: false, dataIndex: 'detail_material_satuan'},
{text: "Kategori", flex: 1, sortable: false, dataIndex: 'kategori'},
{text: "Harga Satuan", flex: 1, sortable: false, align: 'right', dataIndex: 'harga', editor:'numberfield', renderer: Ext.util.Format.numberRenderer('00,000'),},
{text: "Kode RAP", flex: 1, sortable: false, dataIndex: 'kode_rap'},
{text: "Keterangan", flex: 3, sortable: false, dataIndex: 'keterangan', editor:'textfield'},
],
columnLines: true,
dockedItems: [
{
	xtype: 'toolbar',
	dock: 'top',
	items: [
	{
		flex: 4,
		fieldLabel: 'Search',
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
	title: 'Edit Harga Satuan :: ASAT',
	closeAction: 'hide',
	closable: false,					
	width: '70%',
	height: '85%',
	layout: 'fit',
	resizable: true,
	modal: true,
	items: gridEditHargaASAT,
	listeners:{
		hide:function() {
			storeASAT.load();
			storeAnalisaPekerjaan.load();
		}
	}
});

/* end edit harga asat */

Ext.define('mdlAnalisaPekerjaan', {
	extend: 'Ext.data.Model',
	fields: [
	'id_data_analisa', 'kode_analisa', 'id_kat_analisa', 'kategori', 'nama_kategori',
	'nama_item', 'id_satuan', 'satuan', 'id_tender', 'harga_satuan','c_asat','c_apek'
	]
});

var storeAnalisaPekerjaan = Ext.create('Ext.data.Store', {
	model: 'mdlAnalisaPekerjaan',
	pageSize: 100,  
	remoteFilter: true,
	autoLoad: false,
	proxy: {
		type: 'ajax',
		url: '<?php echo base_url() ?>rencana/daftar_analisa/get_daftar_analisa/<?=$idtender;?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	},
	sorters: [{
		property: 'kode_analisa',
		direction: 'ASC'
	}]							
});		

Ext.define('mdlASAT', {
	extend: 'Ext.data.Model',
	fields: [
	'id_analisa_asat', 'id_data_analisa', 'kode_material', 'id_detail_material', 'koefisien', 
	'harga', 'kode_analisa', 'id_tender', 'detail_material_nama', 'detail_material_satuan',
	'detail_material_kode', 'asat_kat', 'subtotal', 'parent_name', 'detail_material_id'
	]
});

var storeASAT = Ext.create('Ext.data.Store', {
	model: 'mdlASAT',
	pageSize: 300,  
	remoteFilter: true,
	autoLoad: false,
	proxy: {
		type: 'ajax',
		url: '<?php echo base_url() ?>rencana/daftar_analisa/get_asat/<?=$idtender?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	},
	groupField: 'asat_kat',
	sorters: [{
		property: 'kode_analisa',
		direction: 'ASC'
	}]
});		storeASAT.load();

var gridAnalisaSatuan = Ext.create('Ext.grid.Panel', {
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
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},	
	invalidateScrollerOnRefresh: false,	
	plugins: Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToMoveEditor: 1,				
		clicksToEdit: 1,
		listeners : {
			edit : function(a,b) {							
				var editedRecords = gridAnalisaSatuan.getStore().getUpdatedRecords();
					rec = b.record;
					// var kd = editedRecords[0].data.kode_analisa;

					// rt = storeTree.getRootNode();
					
					// cek(rt,kd);

					// function cek(a,kd){
					// 	for (var i = 0; i < a.childNodes.length; i++) {
					// 		b = a.childNodes[i];
					// 		if (b.childNodes.length > 0) {
					// 			cek(b,kd);
					// 		} else {
					// 			if (a.childNodes[i].data.kode_analisa == kd) {
					// 				a.childNodes[i].set('subtotal',10000);
					// 			}
					// 		}
					// 	};
					// }

					rec.set('koefisien',editedRecords[0].data.koefisien);
					rec.set('subtotal',editedRecords[0].data.koefisien * editedRecords[0].data.harga);
					rec.commit();
							//var editedRecords = gridAnalisaSatuan.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/edit_koefisien_satuan',
								method: 'POST',
								params: {
									'id_tender' : <?=$idtender;?>,
									'id_analisa_asat' : editedRecords[0].data.id_analisa_asat,
									'kode_analisa' : editedRecords[0].data.kode_analisa,
									'detail_material_kode' : editedRecords[0].data.detail_material_kode,									
									'koefisien' : editedRecords[0].data.koefisien,
								},								
								success: function(response) {
									var text = response.responseText;

									// Ext.Msg.alert("Status", response.responseText, function()
									// {
									// 	storeASAT.load();
										// storeAnalisaPekerjaan2.load();
										// storeAnalisaPekerjaan.load();
										storeTree.load();
									// });
								},
								failure: function(response) {
									Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
								}
							});
						}
					}
				}),				
columns: [
					/*
					{
						xtype: 'rownumberer',
						width: 35,
						sortable: false
					},
					{
						text: "Kode", flex: 1, sortable: false, dataIndex: 'kode_analisa',
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
						},
					},
					*/
					{
						text: "Kode", 
						flex: 2, 
						sortable: false, 
						dataIndex: 'detail_material_kode',
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
						},						
					},
					{text: "Uraian", flex: 3, sortable: false, dataIndex: 'detail_material_nama'},
					{text: "Satuan", flex: 1, sortable: false, dataIndex: 'detail_material_satuan'},
					{text: "Koefisien", flex: 1, sortable: false, dataIndex: 'koefisien',editor: {
						xtype: 'numberfield',
						decimalPrecision: 4
					}
				},
				{
					text: "Harga Satuan", 
					flex: 2, 
					sortable: false, 
					dataIndex: 'harga',
					align: 'right',
					renderer: Ext.util.Format.numberRenderer('00,000'),						
				},
				{
					text: "Sub Total", 
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
				{text: "",xtype: 'actioncolumn', flex:1, align: 'center', sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
				handler: function(grid, rowIndex, colIndex){        
					rec = storeASAT.getAt(rowIndex);
					var id = rec.get('id_analisa_asat');
					Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
						if(resbtn == 'yes')
						{
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/delete_asat/',
								method: 'POST',
								params: {
									'id_analisa_asat':id,
									'id_tender' : <?=$idtender;?>,
									'kode_analisa': rec.get('detail_material_kode')
								},
								success: function(response) {
									Ext.example.msg("Status", response.responseText, function()
									{
										storeASAT.load();
										storeAnalisaPekerjaan.load();													
									});
								},
								failure: function(response) {
									Ext.example.msg("Error", response.responseText, function()
									{
										storeASAT.load();
									});
								},
							});			   																			
						}
					});
				}
			},
			],
			columnLines: true,
			dockedItems: [
			{
				xtype: 'toolbar',
				dock: 'top',
				items: [
				{
					iconCls:'icon-edit',
					text:'Edit Harga Satuan',
					tooltip:'Edit Harga Satuan',
					handler: function(){        
						winEditHargaSatuan.on('show', function(win) {	   
							storeEditHargaASAT.load();									
						});										
						winEditHargaSatuan.doLayout();
						winEditHargaSatuan.show();								
					}
				},'-',
							/*
							{
								text:'Reset All Data',
								iconCls: 'icon-del',
								tooltip:'Reset All Data',
								handler: function(){        
									Ext.example.msg("Status","Reset All Data");
								}
							},'-',
							*/
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
						{
							xtype: 'toolbar',
							dock: 'bottom',
							items: [
							{xtype:'tbfill'},
							{
								iconCls:'icon-add',
								text: 'Tambah Data Analisa',
								// flex: 1,
								handler: function(){
									winDaftarAnalisa.setTitle('Data Analisa Pekerjaan');
									winDaftarAnalisa.on('show', function(win) {
										storeASAT.load();
										storeAnalisaPekerjaan.load();
									});								
									winDaftarAnalisa.doLayout();
									winDaftarAnalisa.show();									
								}
							},		
							{xtype:'tbfill'}					
							]
						}					
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

var gridASAT = Ext.create('Ext.grid.Panel', {
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
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},	
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
	}),
	invalidateScrollerOnRefresh: false,	
	plugins: Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToMoveEditor: 1,				
		clicksToEdit: 1,
		listeners : {
			edit : function() {
				var editedRecords = gridASAT.getStore().getUpdatedRecords();						
							//var editedRecords = gridASAT.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/edit_koefisien_satuan',
								method: 'POST',
								params: {
									'id_tender' : <?=$idtender;?>,
									'id_analisa_asat' : editedRecords[0].data.id_analisa_asat,
									'kode_analisa' : editedRecords[0].data.kode_analisa,
									'detail_material_kode' : editedRecords[0].data.detail_material_kode,									
									'koefisien' : editedRecords[0].data.koefisien,
								},								
								success: function(response) {
									var text = response.responseText;
									Ext.Msg.alert("Status", response.responseText, function()
									{
										storeASAT.load();
										storeAnalisaPekerjaan.load();
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
					/*
					{
						xtype: 'rownumberer',
						width: 35,
						sortable: false
					},
					{
						text: "Kode", flex: 1, sortable: false, dataIndex: 'kode_analisa',
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
						},
					},
					*/
					{
						text: "Kode", 
						flex: 2, 
						sortable: false, 
						dataIndex: 'detail_material_kode',
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
						},						
					},					
					{text: "Uraian", flex: 3, sortable: false, dataIndex: 'detail_material_nama'},
					{text: "Satuan", flex: 1, sortable: false, dataIndex: 'detail_material_satuan'},
					{text: "Koefisien", flex: 1, sortable: false, dataIndex: 'koefisien',editor: {
						xtype: 'numberfield',
						decimalPrecision: 4
					}
				},
				{
					text: "Harga Satuan", 
					flex: 2, 
					sortable: false, 
					dataIndex: 'harga',
					align: 'right',
					renderer: Ext.util.Format.numberRenderer('00,000'),						
				},
				{
					text: "Sub Total", 
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
					/*
					{text: "",xtype: 'actioncolumn', flex:1, align: 'center', sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
						handler: function(grid, rowIndex, colIndex){        
							rec = storeASAT.getAt(rowIndex);
							var id = rec.get('id_analisa_asat');
							Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
									if(resbtn == 'yes')
									{
										Ext.Ajax.request({
											url: '<?=base_url();?>rencana/daftar_analisa/delete_asat/'+id,
											method: 'POST',
											params: {
												'id_analisa_asat':id,
												'kode_analisa': rec.get('detail_material_kode')
											},
											success: function(response) {
												Ext.example.msg("Status", response.responseText, function()
												{
													storeASAT.load();
												});
											},
											failure: function(response) {
												Ext.example.msg("Error", response.responseText, function()
												{
													storeASAT.load();
												});
											},
										});			   																			
									}
							});
						}
					},
					*/
					],
					columnLines: true,
					dockedItems: [
					{
						xtype: 'toolbar',
						dock: 'top',
						items: [
						{
							iconCls:'icon-edit',
							text:'Edit Harga',
							tooltip:'Edit Harga Satuan',
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
					{
						xtype: 'toolbar',
						dock: 'bottom',
						items: [
						{
							text:'Copy ASAT',
							iconCls: 'icon-copy',
							handler: function(){
								var records = gridASAT.getView().getSelectionModel().getSelection(),
								artikel = [], idart=[], koef = [],harga = [];
								Ext.Array.each(records, function(rec){
									artikel.push(rec.get('detail_material_kode'));
									koef.push(rec.get('koefisien'));
									harga.push(rec.get('harga'));
									idart.push(rec.get('detail_material_id'));
								});
								if(artikel != '')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/daftar_analisa/copy_asat',
										method: 'POST',											
										params: {												
											'id_artikel' : idart.join(','),
											'kode_artikel' : artikel.join(','),
											'koefisien' : koef.join(','),
											'harga' : harga.join(','),
											'id_tender' : <?=$idtender;?>
										},								
										success: function(response) {
											Ext.MessageBox.alert('OK', 'Data ASAT telah di-copy silahkan pilih Analisa di sebelah kanan, kemudian pilih "Paste ASAT" pada item tersebut.');
										},
										failure: function(response) {
											Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
										}
									});			   	
								} else 
								{
									Ext.example.msg('Error', 'Silahkan pilih item yang mau di-copy!');
								}								
							}
						},'-',
						{
							text:'Delete',
							iconCls: 'icon-del',
							handler: function(){
								var records = gridASAT.getView().getSelectionModel().getSelection(),
								analisa=[], idanalisa=[];
								Ext.Array.each(records, function(rec){
									analisa.push(rec.get('detail_material_kode'));
									idanalisa.push(rec.get('id_analisa_asat'));
								});
								if(analisa != '')
								{
									Ext.MessageBox.confirm('Hapus Item Satuan Pekerjaan', 'Apakah anda akan menghapus item ini (' + analisa.join(',') + ') ?',
										function(resbtn){
											if(resbtn == 'yes')
											{
												Ext.Ajax.request({
													url: '<?=base_url();?>rencana/daftar_analisa/delete_ansat',
													method: 'POST',											
													params: {
														'id_analisa_asat': idanalisa.join(','),
														'kode_analisa': analisa.join(','),
														'id_tender' : <?=$idtender;?>
													},								
													success: function(response) {
														Ext.MessageBox.alert('OK', response.responseText, function()
														{
															storeAnalisaPekerjaan.load();
															storeASAT.load();
														});
													},
													failure: function(response) {
														Ext.MessageBox.alert('Failure', 'Error due to connection problem!');
													}
												});			   	
											} else 
											{
												Ext.example.msg('Error', 'Silahkan pilih item yang mau dihapus!');
											}																		
										});
} else 
{
	Ext.example.msg('Error', 'Silahkan pilih item yang mau dihapus!');
}
}
},'->',
'Info: Maks. Dua jenjang level Analisa',
]
}					
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

var APcellEditing = Ext.create('Ext.grid.plugin.RowEditing', {
					//clicksToEdit: 1,
					clicksToMoveEditor: 1,
					autoCancel: false,
					listeners : {
						'edit' : function() {						
							var editedRecords = gridAnalisaPekerjaan.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/tambah_daftar_analisa',
								method: 'POST',
								params: {
									'id_tender' : <?=$idtender;?>,
									'id_kat_analisa': editedRecords[0].data.nama_kategori,
									'kode_analisa' : editedRecords[0].data.kode_analisa,
									'nama_item' : editedRecords[0].data.nama_item,
									'id_satuan' : editedRecords[0].data.satuan,
								},								
								success: function(response) {
									var text = response.responseText;
									storeAnalisaPekerjaan.load();
								},
								failure: function(response) {
									Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
								}
							});			   																																				
						}
					}
				});			

var gridAnalisaPekerjaan = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	frame: false,
	store: storeAnalisaPekerjaan,
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
	}),
	plugins: [APcellEditing],										
	columns: [
	{
		xtype: 'rownumberer',
		width: 35,
		sortable: false
	},
	{
		text: "Kode", 
		flex: 2, 
		id: 'id_kode_analisa',
		sortable: false, 
		dataIndex: 'kode_analisa',			
						/*
						editor: {
							xtype: 'textfield',
						},
						*/
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
						},                                                          
						renderer: function(v, meta, record) {
							var harga_sat = record.get('harga_satuan');
							var c_apek = record.get('c_apek');
							var c_asat = record.get('c_asat');

							if(c_asat == 0 && c_apek == 0) {                                                                      
								return '<font color=red><b>'+v+'</b></font>';
							} else {
								return v;
							}
						}										
					},
					{
						text: "Uraian", 
						flex: 3, 
						sortable: false, 
						dataIndex: 'nama_item',
						editor: {
							xtype: 'textfield',
						}						
					},
					{
						text: "Satuan", 
						id: 'satuan_id',
						flex: 1, 
						sortable: false, 
						dataIndex: 'satuan',
						editor: {
							id: 'cmb_id_satuan',						
							xtype: 'combo',
							store: { 
								fields: ['satuan_id','satuan_kode'], 
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
							triggerAction : 'all',					
							anchor: '100%',
							displayField: 'satuan_kode',
							valueField: 'satuan_id',
							listeners: {
								'select': function(combo, row, index) {
								}
							},																					
						},
					},
					{
						text: "Harga Satuan", 
						flex: 2, 
						sortable: false, 
						dataIndex: 'harga_satuan',
						align: 'right',
						renderer: Ext.util.Format.numberRenderer('00,000'),					
					},
					{text: "",xtype: 'actioncolumn', flex:1,  align: 'center', sortable: false,icon:'<?=base_url();?>assets/images/application_go.png',
					handler: function(grid, rowIndex, colIndex){        
						var rec = storeAnalisaPekerjaan.getAt(rowIndex);
							// console.log(rec.get('id_tender')+'+'+rec.get('id_data_analisa')+'+'+rec.get('kode_analisa'));
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/set_analisa_itemid',
								method: 'POST',
								params: {
									'id_tender' : rec.get('id_tender'),
									'id_data_analisa' : rec.get('id_data_analisa'),
									'kode_analisa' : rec.get('kode_analisa'),										
								},
								success: function() {
									storeANSAT.load();										
								},
								failure: function() {
									Ext.example.msg("ERROR", "Error due to connection problem!");
								}
							});
							winANSAT.setTitle('Tambah Analisa Satuan :: '+rec.get('kategori')+' :: '+rec.get('kode_analisa')+' - '+rec.get('nama_item'));
							winANSAT.on('show', function(win) {

							});						
							winANSAT.doLayout();
							winANSAT.show();							
						}
					},				
					/*
					{text: "",xtype: 'actioncolumn', flex:1, align: 'center', tooltip: 'Delete item ini', sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
						handler: function(grid, rowIndex, colIndex){        
							var rec = storeAnalisaPekerjaan.getAt(rowIndex);
							var id = rec.get('kode_analisa');
							var item = rec.get('kode_analisa')+'-'+rec.get('nama_item');
							Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ('+item+') ini?',function(resbtn){
									if(resbtn == 'yes')
									{
										Ext.Ajax.request({
											url: '<?=base_url();?>rencana/daftar_analisa/delete_analisa_pekerjaan/'+id,
											method: 'POST',
											params: {
												'kode_analisa':id,
												'id_tender':<?=$idtender;?>,
											},								
											success: function(response) {
												Ext.example.msg( "Status", response.responseText, function(){
													storeASAT.load();
													storeAnalisaPekerjaan.load();
												});											
											},
											failure: function(response) {
												Ext.example.msg( "Error", response.responseText, function(){
													storeAnalisaPekerjaan.load();
												});											
											}
										});			   																			
									}
							});
						}
					},
					*/
					{text: "",xtype: 'actioncolumn', flex:1, align: 'center', tooltip: 'Hapus seluruh analisa untuk item ini', sortable: true,icon:'<?=base_url();?>assets/images/cross.gif',
					handler: function(grid, rowIndex, colIndex){        
						var rec = storeAnalisaPekerjaan.getAt(rowIndex);
						var id = rec.get('kode_analisa');
						var item = rec.get('kode_analisa')+'-'+rec.get('nama_item');
						Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus seluruh Analisa Satuan Pekerjaan untuk item ('+item+') ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>rencana/daftar_analisa/delete_asat_apek/',
									method: 'POST',
									params: {
										'kode_analisa':id,
										'id_tender':<?=$idtender;?>,
									},								
									success: function(response) {
										Ext.example.msg( "Status", response.responseText, function(){
											storeASAT.load();
											storeAnalisaPekerjaan.load();
										});											
									},
									failure: function(response) {
										Ext.example.msg( "Error", response.responseText, function(){
											storeAnalisaPekerjaan.load();
										});											
									}
								});			   																			
							}
						});
					}
				},
				],
				bbar: Ext.create('Ext.PagingToolbar', {
					store: storeAnalisaPekerjaan,
					displayInfo: true,
					displayMsg: 'Displaying data {0} - {1} of {2}',
					emptyMsg: "No data to display",
				}),				
				columnLines: true,
				dockedItems: [
				{

					xtype: 'toolbar',
					dock: 'top',						
					items: [
					{
						text:'Kembali',
						iconCls: 'icon-back',
						handler: function(){
							winDaftarAnalisa.hide();
						}
					},'-',{
						text:'Tambah Data',
						iconCls: 'icon-add',
						handler: function(){          
							var r = Ext.create('mdlAnalisaPekerjaan', {
								kode_analisa: 'AN000',
								nama_item: '[ Uraian Analisa ]',
								id_satuan: 8,
								id_kat_analisa: 1
							});
							storeAnalisaPekerjaan.insert(0, r);
							APcellEditing.startEdit(0, 0);									
						}
					},'-',
					{
						iconCls:'icon-reload',
						text:'Clear Search',
						handler: function(){          
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/clear_search_data_analisa',
								method: 'POST',
								params: {
									'clearsearch' : 1,
									'page' : 1,
								},
								success: function() {
									storeAnalisaPekerjaan.load();
								},
								failure: function() {
									Ext.example.msg("ERROR", "Error due to connection problem!");
								}
							});			   								
						}
					},'-',
					{
						flex: 4,
						tooltip:'masukan kode analisa / uraian',
						emptyText: 'kode analisa / uraian',
						xtype: 'searchfield',
						name: 'cari_analisa',
						store: storeAnalisaPekerjaan,
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
						text:'Paste ASAT',
						iconCls: 'icon-paste',
						handler: function(){          
							var records = gridAnalisaPekerjaan.getView().getSelectionModel().getSelection(),
							analisa=[], idanalisa=[];
							Ext.Array.each(records, function(rec){
								analisa.push(rec.get('kode_analisa'));
								idanalisa.push(rec.get('id_data_analisa'));
							});
							if(analisa != '')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>rencana/daftar_analisa/paste_asat',
									method: 'POST',											
									params: {												
										'kode_analisa' : analisa.join(','),
										'id_data_analisa': idanalisa.join(','),
										'id_tender' : <?=$idtender;?>
									},								
									success: function(response) {
										Ext.MessageBox.alert('OK', 'Data ASAT berhasil di-paste.',function()
										{
											storeAnalisaPekerjaan.load();
											storeASAT.load();
										});
									},
									failure: function(response) {
										Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
									}
								});			   	
							} else 
							{
								Ext.example.msg('Error', 'Silahkan pilih item yang mau di-copy!');
							}
						}
					},'-',						
					{
						text:'Delete',
						iconCls: 'icon-del',
						handler: function(){          
							var records = gridAnalisaPekerjaan.getView().getSelectionModel().getSelection(),
							analisa=[], idanalisa=[];
							Ext.Array.each(records, function(rec){
								analisa.push(rec.get('kode_analisa'));
								idanalisa.push(rec.get('id_data_analisa'));
							});
							if(analisa != '')
							{
								Ext.MessageBox.confirm('Hapus item Analisa Pekerjaan', 'Apakah anda akan menghapus item ini (' + analisa.join(',') + ') ?',
									function(resbtn){
										if(resbtn == 'yes')
										{
											Ext.Ajax.request({
												url: '<?=base_url();?>rencana/daftar_analisa/delete_analisa',
												method: 'POST',											
												params: {												
													'kode_analisa' : analisa.join(','),
													'id_data_analisa': idanalisa.join(','),
													'id_tender' : <?=$idtender;?>
												},								
												success: function(response) {
													Ext.MessageBox.alert('OK', response.responseText, function()
													{
														storeAnalisaPekerjaan.load();
														storeASAT.load();
													});
												},
												failure: function(response) {
													Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
												}
											});			   	
										} else 
										{
											Ext.example.msg('Error', 'Silahkan pilih item yang mau dihapus!');
										}																		
									});
} else 
{
	Ext.example.msg('Error', 'Silahkan pilih item yang mau dihapus!');
}
}
},'-',
							
							{
								text:'Copy Analisa dari Proyek Lain',
								iconCls: 'icon-copy',
								handler: function(){          
									copy_analisa_proyek_lain();
								}
							}, '-',		
														
							{
								text:'Import Analisa (CSV)',
								iconCls: 'icon-table',
								handler: function(){          
									winUploadAnalisa.setTitle('Upload Analisa (CSV)');
									winUploadAnalisa.on('show', function(win) {
									});				
									winUploadAnalisa.doLayout();
									winUploadAnalisa.show();										
								}
							}	
							]							
						}
						],
					});						

var winDaftarAnalisa = Ext.widget('window', {
	title: 'Data Analisa Pekerjaan :: Proyek -> <?=$data_tender['nama_proyek'];?>',
	closeAction: 'hide',
	width: '95%',
	height: '90%',
	layout: 'border',
	resizable: false,
	modal: true,
	items: [
	{
		region:'west',
		layout: 'fit',
		width: '50%',
		split: true,
		title: 'Data Analisa',
		items: [gridAnalisaPekerjaan]
	},					
	{
		region:'center',
		layout: 'fit',
		width: '50%',
		title: 'Detail Analisa',
						items: [gridASAT] //DataRATBK
					}					
					],
					listeners:{
						hide:function(){
							storeTree.load();
						}
					}
				});			
/* end data analisa */

/* grid analisa */ 
var grid_analisa = Ext.create('Ext.grid.Panel', {
	id: 'id_grid_analisa',
	width: 700,
	height: 500,
	store: storeDCBK,
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
	plugins: Ext.create('Ext.grid.plugin.RowEditing', {
		clicksToMoveEditor: 1,
		autoCancel: false,
		listeners: {
			'edit': function () {
				var editedRecords = grid_analisa.getStore().getUpdatedRecords();
				Ext.Ajax.request({
					url: '<?=base_url();?>rencana/update_rat_koefisien',
					method: 'POST',
					params: {								
						'id_simpro_rat_analisa' : editedRecords[0].data.id_simpro_rat_analisa,
						'akoefisien' : editedRecords[0].data.akoefisien
					},								
					success: function(response) {
						var text = response.responseText;
						Ext.example.msg( "Status", text, function(){
							storeDCBK.load();
							storeTree.load();										
						});											
					},
					failure: function() {
						Ext.example.msg( "Error", "Data koefisien GAGAL diupdate!");											
					}
				});			   																										
			}
		}				
	}),				
	columns:[
	{
		text: "KODE",
		dataIndex: 'detail_material_kode',
		width: 70,
		sortable: false,
		summaryType: 'count',
		summaryRenderer: function(value, summaryData, dataIndex) {
			return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
		}					
	},
	{
		text: "URAIAN",
		dataIndex: 'detail_material_nama',
		width: 250,
		sortable: false,
	},				
	{
		text: "SATUAN",
		dataIndex: 'detail_material_satuan',
		width: 50,
		sortable: false
	},
	{
		text: "SUB BIDANG",
		dataIndex: 'subbidang_name',
		width: 100,
		sortable: false
	},
	{
		text: "HARGA",
		dataIndex: 'aharga',
		renderer: Ext.util.Format.numberRenderer('00,000'),
		width: 100,
		align: 'right',
		sortable: false,
	},
	{
		text: "KOEFISIEN",
		dataIndex: 'akoefisien',
		width: 60,
		align: 'center',
		sortable: false,
		editor: {
			xtype: 'numberfield',
		}
	},				
	{
		text: "SUBTOTAL",
		dataIndex: 'subtotal',
		width: 100,
		align: 'right',
		sortable: false,
		groupable: false,
		renderer: Ext.util.Format.numberRenderer('00,000'),
		summaryType: function(records){
			var i = 0,
			length = records.length,
			total = 0,
			record;

			for (; i < length; ++i) {
				record = records[i];
				total += record.get('aharga') * record.get('akoefisien');
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
				var id = rec.get('id_simpro_rat_analisa');
				Ext.MessageBox.confirm('Hapus item Analisa Satuan Pekerjaan', 'Apakah anda akan menghapus item ini (' + rec.get('detail_material_nama') + ') ?',
					function(resbtn){
						if(resbtn == 'yes')
						{
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/del_item_rat',
								method: 'POST',
								params: {
									'id_rat_item_analisa' : id
								},								
								success: function(response) {
									var text = response.responseText;
									Ext.example.msg( "Status", text, function(){
										storeDCBK.load();
										storeTree.load();
									});											
								},
								failure: function() {
								}
							});			   																			
						}
					});
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
	dockedItems: [
	{
		xtype: 'toolbar',
		dock: 'top',
		items: [
		{
			text: 'Edit Harga Satuan',
			iconCls: 'icon-add',
			handler: function(){
				storeEditHarga.load();
				showWinHargaSatuan();
			}
		},				
		{
			flex: 4,
			fieldLabel: 'Search',
			labelWidth: 45,
			tooltip:'masukan kode analisa / uraian',
			emptyText: 'kode analisa / uraian',
			xtype: 'searchfield',
			store: storeDCBK,
			listeners: {
				keyup: function(e){ 
											/*
											for(var key in e)
												alert(key+'='+e[key]);
											*/
										}
									}
								},
								]
							},
							{
								xtype: 'toolbar',
								dock: 'bottom',
								items: [
								{
									iconCls:'icon-add',
									text: 'Tambah Data Analisa',
									handler: function(){
										winDaftarAnalisa.setTitle('Data Analisa Pekerjaan');
										winDaftarAnalisa.on('show', function(win) {
											storeASAT.load();
											storeAnalisaPekerjaan.load();
										});								
										winDaftarAnalisa.doLayout();
										winDaftarAnalisa.show();									
								/*
								winANSAT.setTitle('Tambah Analisa Pekerjaan');
								winANSAT.on('show', function(win) {
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/rencana/set_rat_item_tree',
										method: 'POST',
										params: {
											'id_tender' : <?=$idtender;?>,
										},
										success: function() {
											storeANSAT.load();
										},
										failure: function() {
											Ext.example.msg("ERROR", "Error due to connection problem!");
										}
									});			   
								});
								winANSAT.doLayout();
								winANSAT.show();	
								*/								
							}
						},
						]
					}
					],
					listeners:{
						beforerender:function(){
							storeDCBK.load();
						}
					}			
				});		
/* end grid analisa */

/* edit-harga-satuan */				
Ext.define('Writer.Grid', {
	extend: 'Ext.grid.Panel',
	alias: 'widget.writergrid',
	requires: [
	/* 'Ext.grid.plugin.CellEditing', */
	'Ext.grid.plugin.RowEditing',
	'Ext.form.field.Text',
	'Ext.toolbar.TextItem'
	],
	initComponent: function(){
		/* this.editing = Ext.create('Ext.grid.plugin.CellEditing'); */
		Ext.apply(this, {
			frame: false,
			/* plugins: [this.editing], */
			plugins: [
			Ext.create('Ext.grid.plugin.RowEditing', {
				clicksToEdit: 1
			})
			],					
			dockedItems: [
			{					
				xtype: 'toolbar',
				dock: 'top',
				items: 'Info: Double klik pada item yang mau diedit.',
			},
			{
				weight: 2,
				xtype: 'toolbar',
				dock: 'bottom',
				items: [{
					xtype: 'tbtext',
					text: '<b>@cfg</b>'
				}, '|', {
					text: 'autoSync',
					enableToggle: true,
					pressed: true,
					disabled: true,
					tooltip: 'When enabled, Store will execute Ajax requests as soon as a Record becomes dirty.',
					scope: this,
					toggleHandler: function(btn, pressed){
						this.storeEditHarga.autoSync = pressed;
					}
				}, {
					text: 'batch',
					enableToggle: true,
					pressed: true,
					disabled: true,							
					tooltip: 'When enabled, Store will batch all records for each type of CRUD verb into a single Ajax request.',
					scope: this,
					toggleHandler: function(btn, pressed){
						this.storeEditHarga.getProxy().batchActions = pressed;
					}
				}]
			}, {
				weight: 1,
				xtype: 'toolbar',
				dock: 'bottom',
				ui: 'footer',
				items: ['->', 
				{
					text: 'Tutup',
					scope: this,
					handler: function(){ winEditHargaSatuan.hide(); }
				}						
				]
			}],	
			columns: [
			{
				xtype: 'rownumberer',
				width: 50,
				sortable: false
			},		
					/*
					{
						xtype: 'hiddenfield',
						name: 'id_proyek_rat',
						dataIndex: 'id_proyek_rat',
					},					
					{
						xtype: 'hiddenfield',
						name: 'dm_kode',
						dataIndex: 'detail_material_kode',
					},
					*/					
					{
						text: 'KODE',
						width: 80,
						sortable: false,
						dataIndex: 'detail_material_kode'
					}, {
						header: 'URAIAN',
						width: 200,
						sortable: false,
						dataIndex: 'detail_material_nama',
					}, {
						header: 'SATUAN',
						width: 70,
						sortable: false,
						dataIndex: 'detail_material_satuan',
					}, {
						header: 'HARGA',
						width: 100,
						align: 'right',
						sortable: false,
						dataIndex: 'aharga',
						field: {
							type: 'numberfield',
						}
					}, 
					{
						header: 'KOEFISIEN',
						width: 100,
						sortable: false,
						dataIndex: 'akoefisien',
					},					
					{
						header: 'SUB TOTAL',
						width: 100,
						align: 'right',
						renderer: Ext.util.Format.numberRenderer('00,000'),
						sortable: false,
						dataIndex: 'subtotal',
					},					
					{
						header: 'TOTAL ITEM',
						width: 50,
						sortable: false,
						dataIndex: 'totitem',
					},					
					{
						header: 'TOTAL',
						width: 100,
						renderer: Ext.util.Format.numberRenderer('00,000'),
						align: 'right',
						sortable: false,
						dataIndex: 'total',
					},					
					{
						header: 'KODE RAP',
						width: 100,
						sortable: false,
						dataIndex: 'kode_rap',
						field: {
							type: 'textfield'
						}
					},					
					{
						header: 'KETERANGAN',
						width: 200,
						sortable: true,
						dataIndex: 'aketerangan',
						field: {
							type: 'textfield'
						}
					}],
					listeners:{
						beforerender:function(){
							storeEditHarga.load();
						}
					}											
				});
this.callParent();
this.getSelectionModel().on('selectionchange', this.onSelectChange, this);
},

onSelectChange: function(selModel, selections){
	/* this.down('#delete').setDisabled(selections.length === 0); */
},

onSync: function(){
	this.storeEditHarga.sync();
},

onDeleteClick: function(){
	var selection = this.getView().getSelectionModel().getSelection()[0];
	if (selection) {
		this.storeEditHarga.remove(selection);
	}
},
});

Ext.define('Writer.EditHarga', {
	extend: 'Ext.data.Model',
	idProperty: 'detail_material_kode',					
	fields: [
	'detail_material_kode', 
	'detail_material_nama', 
	'id_proyek_rat',
	'detail_material_satuan', 'totitem', 
	{ name: 'aharga', type: 'int'}, 
	{ name: 'akoefisien', type: 'float'}, 
	{ name: 'subtotal', type: 'float'}, 
	{ name: 'total', type: 'float'}, 
	{ name: 'kode_rap', type: 'string'},
	{ name: 'aketerangan', type: 'string', useNull: true}
	],
	validations: [
	{
		type: 'length',
		field: 'aharga',
		min: 1
	}, 
	]
});

Ext.require([
	'Ext.data.*',
	'Ext.tip.QuickTipManager',
	'Ext.window.MessageBox'
	]);

var storeEditHarga = Ext.create('Ext.data.Store', {
	model: 'Writer.EditHarga',
	autoLoad: false,
	pageSize: 2000,
	autoSync: true,
	proxy: {
		type: 'ajax',
		api: {
			read: '<?php echo base_url(); ?>rencana/rest_client/view/<?=$idtender;?>',
			create: '<?php echo base_url(); ?>rencana/rest_client/create/<?=$idtender;?>',
			update: '<?php echo base_url(); ?>rencana/rest_client/update/<?=$idtender;?>',
			destroy: '<?php echo base_url(); ?>rencana/rest_client/destroy/<?=$idtender;?>'
		},
		reader: {
			type: 'json',
			successProperty: 'success',
			root: 'data',
			messageProperty: 'message'
		},
		writer: {
			type: 'json',
			writeAllFields: false,
			root: 'data'
		},
		listeners: {
			exception: function(proxy, response, operation){
				Ext.MessageBox.show({
					title: 'REMOTE EXCEPTION',
					msg: operation.getError(),
					icon: Ext.MessageBox.ERROR,
					buttons: Ext.Msg.OK
				});
			}
		}
	},
	listeners: {
		write: function(proxy, operation){
			if (operation.action == 'destroy') {
			}
			Ext.example.msg(operation.action, operation.resultSet.message, function(btn){
				if(btn == 'ok') { 
					storeEditHarga.load(); 
					storeDCBK.load();
					storeTree.load();
				}
			}
			);
		}
	}
});

var winEditHargaSatuan;
function showWinHargaSatuan() {					
	if (!winEditHargaSatuan) {		
		var mainEditHarga = Ext.create('Ext.container.Container', {
			width: '100%',
			height: '100%',
			layout: {
				type: 'vbox',
				align: 'stretch'
			},
			items: [
			{
				itemId: 'gridEditHargaID',
				xtype: 'writergrid',
				flex: 1,
				store: storeEditHarga,
				listeners: {
					selectionchange: function(selModel, selected) {
					}
				}
			}]
		});
		winEditHargaSatuan = Ext.widget('window', {
			title: 'Edit Harga Satuan :: Proyek -> <?=$data_tender['nama_proyek'];?>',
			closeAction: 'hide',
			closable: false,
			width: '80%',
			height: '85%',
			layout: 'fit',
			resizable: false,
			modal: true,
			items: mainEditHarga
		});
	}
	winEditHargaSatuan.show();
}						
/* end-edit-harga-satuan */

Ext.define('treegriditem', {
	extend: 'Ext.data.Model',
	fields: [
	{name: 'rat_item_tree',     type: 'string'},
	{name: 'id_proyek_rat',     type: 'string'},
	{name: 'id_satuan', 		type: 'string'},
	{name: 'kode_tree',     	type: 'string'},
	{name: 'kode_analisa',     	type: 'string'},
	{name: 'tree_item',     	type: 'string'},
	{name: 'tree_satuan',     	type: 'string'},
	{name: 'ishaschild',     	type: 'string'},				
	{name: 'volume',     	type: 'float'},
	{name: 'harga',     	type: 'float'},
	{name: 'subtotal',     	type: 'float'},				
	{name: 'tree_parent_id',     	type: 'string'}
	]
});

var storeTree = Ext.create('Ext.data.TreeStore', {
	model: 'treegriditem',
	expanded: true,	
	extraParams: {
		param : ''
	},	
	proxy: {
		type: 'ajax',
		url: '<?=base_url()?>rencana/get_task_tree_item/<?=$idtender;?>',
	},
	listeners:{
		beforeload:function(){
			update_total_bk();
		},
		beforeload:function(){
			Ext.Msg.wait("Loading...","Please Wait");
		},
		load:function(){
			Ext.MessageBox.hide();
		}
	}
});

var ctxTambahSubItem = Ext.create('Ext.Action', {
	iconCls: 'buy-button',
	text: 'Tambah sub Item',
	disabled: false,
	handler: function(widget, event) {
		var rec = gridTreeBK.getSelectionModel().getSelection()[0];
		if (rec) {
			Ext.MessageBox.alert('Tambah sub Item', 'Tambah sub item ' + rec.get('rat_item_tree'));				
		}
	}
});

var ctxTambahAnalisa = Ext.create('Ext.Action', {
	iconCls: 'buy-button',
	text: 'Tambah Detail Analisa',
	disabled: false,
	handler: function(widget, event) {
		var rec = gridTreeBK.getSelectionModel().getSelection()[0];
		if (rec) {
			Ext.MessageBox.alert('Tambah Analisa', 'Tambah analisa item ' + rec.get('rat_item_tree'));				
		}
	}
});

var ctxEdit = Ext.create('Ext.Action', {
	iconCls: 'buy-button',
	text: 'Edit Item',
	disabled: false,
	handler: function(widget, event) {
		var rec = gridTreeBK.getSelectionModel().getSelection()[0];
		if (rec) {
			Ext.MessageBox.alert('Edit', 'Edit item ' + rec.get('rat_item_tree'));				
		}
	}
});

var ctxDelete = Ext.create('Ext.Action', {
	iconCls: 'buy-button',
	text: 'Delete item',
	disabled: false,
	handler: function(widget, event) {
		var rec = gridTreeBK.getSelectionModel().getSelection()[0];
		if (rec) {
			Ext.MessageBox.alert('Delete', 'Delete item ' + rec.get('rat_item_tree'));				
		}
	}
});

var gridBKctx = Ext.create('Ext.menu.Menu', {
	items: [
	ctxTambahSubItem,
	ctxTambahAnalisa,
	ctxEdit,
	ctxDelete				
	]
});

/* grid analisaapek */
Ext.define('mdlTreeAPEK', {
	extend: 'Ext.data.Model',
	fields: [
	'id_analisa_item_apek','id_proyek_rat','id_data_analisa','kode_analisa','harga','volume','subtotal',
	'harga','rat_item_tree','kode_tree','tree_item','tree_satuan','item_analisa','uraian_analisa'
	],
	idProperty: 'id_analisa_item_apek'
});

var storeAnalisaAPEK = Ext.create('Ext.data.Store', {
	pageSize: 2000,
	model: 'mdlTreeAPEK',
	remoteSort: true,
	proxy: {
		type: 'jsonp',
		url: '<?=base_url();?>rencana/get_data_analisa_apek/<?=$idtender;?>',
		reader: {
			root: 'data',
			totalProperty: 'total'
		},
		simpleSortMode: true
	},		
	groupField: ['item_analisa'],
	sorters: [{
		property: 'kode_tree',
		direction: 'ASC'
	}]
});		

var gridAnalisaAPEK = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	store: storeAnalisaAPEK,
	disableSelection: false,
	loadMask: true,
	features: [{
		ftype: 'groupingsummary',
		groupHeaderTpl: '{name}',
		hideGroupedHeader: true,
		enableGroupingMenu: false
	}],		
	columns:[
	{
		text: "ANALISA",
		dataIndex: 'kode_analisa',
		flex: 2,
		sortable: false,
		summaryType: 'count',
		summaryRenderer: function(value, summaryData, dataIndex) {
			return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
		}					
	},
	{
		text: "URAIAN",
		dataIndex: 'uraian_analisa',
		flex: 6,
		sortable: false,
	},				
	{
		text: "SATUAN",
		dataIndex: 'tree_satuan',
		flex: 1,
		sortable: false
	},
	{
		text: "HARGA",
		dataIndex: 'harga',
		renderer: Ext.util.Format.numberRenderer('00,000'),
		flex: 2,
		align: 'right',
		sortable: false,
	},
	{
		text: "VOLUME",
		dataIndex: 'volume',
		flex: 1,
		align: 'center',
		sortable: false,
	},				
	{
		text: "SUBTOTAL",
		dataIndex: 'subtotal',
		flex: 3,
		align: 'right',
		sortable: false,
		groupable: false,
		renderer: Ext.util.Format.numberRenderer('00,000'),
		summaryType: function(records){
			var i = 0,
			length = records.length,
			total = 0,
			record;

			for (; i < length; ++i) {
				record = records[i];
				total += record.get('harga') * record.get('volume');
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
			tooltip: 'Hapus item Analisa pekerjaan',
			handler: function(grid, rowIndex, colIndex) {
				var rec = storeAnalisaAPEK.getAt(rowIndex);
				var id = rec.get('id_analisa_item_apek');
				Ext.MessageBox.confirm('Hapus item Analisa Satuan Pekerjaan', 'Apakah anda akan menghapus item ini (' + rec.get('item_analisa') + ') ?',
					function(resbtn){
						if(resbtn == 'yes')
						{
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/del_item_apek',
								method: 'POST',
								params: {
									'id_analisa_item_apek' : id
								},								
								success: function(response) {
									var text = response.responseText;
									Ext.Msg.alert( "Status", text, function(){
										storeAnalisaAPEK.load();
										storeTree.load();
									});											
								},
								failure: function() {
								}
							});			   																			
						}
					});
			}
		}]
	},				
	],
	bbar: Ext.create('Ext.PagingToolbar', {
		store: storeAnalisaAPEK,
		displayInfo: true,
		displayMsg: 'Displaying data {0} - {1} of {2}',
		emptyMsg: "No data to display",
	}),			
	dockedItems: [{
		xtype: 'toolbar',
		items: [
					/*
					{
						text: 'Reset All Data',
						tooltip:'Reset All Data',
						handler: function() {
							Ext.example.msg("Status", "Reset All Data");
						}
					},'->',
					*/
					'->',
					{
						fieldLabel: 'Search',
						flex: 4,
						labelWidth: 45,
						tooltip:'masukan kode analisa / uraian',
						emptyText: 'kode analisa / uraian',
						xtype: 'searchfield',
						store: storeAnalisaAPEK,
						listeners: {
							keyup: function(e){ 
							}
						}
					},
					]
				}],
				listeners:{
					beforerender:function(){
						storeAnalisaAPEK.load();
					}
				}			
			});				
/* end grid analisa apek */

var winAPEKBK;
function showfrmAddApekBK(iditem, itemname) {
	var wintt = itemname;
	if (!winAPEKBK) {
		var frmAddApekBK = Ext.widget({
			xtype: 'form',
			layout: 'form',
			url: '<?=base_url();?>rencana/tambah_analisa_pekerjaan/<?=$idtender;?>/itemid/'+iditem,
			frame: false,
			bodyPadding: '5 5 0',
			width: 300,
			height: 180,
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
			/* ngambil dari detail material */
			{
				xtype: 'combo',
				name: 'id_satuan_pekerjaan',
				store: { 
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
				fieldLabel: 'Uraian Pekerjaan',
				emptyText: 'uraian pekerjaan...',
				displayField: 'kode_satuan',
				afterLabelTextTpl: required,		
				allowBlank: false,							
				typeAhead: true,
				hideLabel: false,
							//hideTrigger:true,							
							anchor: '100%',
							displayField: 'kode_satuan',
							valueField: 'id_satuan_pekerjaan',
							listeners: {
								'select': function(combo, row, index) {
									//var valharga = row[0].get('mharga');
									var valsatuan = row[0].get('satuan');
									Ext.getCmp('satuanbk').setValue(valsatuan);
								}
							},														
							pageSize: 10
						},			
						{
							fieldLabel: 'Satuan',
							emptyText: 'satuan...',
							afterLabelTextTpl: required,
							id: 'satuanbk',
							name: 'satuan',
							xtype: 'textfield',
							allowBlank: false,
							readonly: true,
						},
						/*
						{
							fieldLabel: 'Harga satuan',
							emptyText: 'harga satuan...',
							afterLabelTextTpl: required,
							id: 'hargasatuanapek',
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
						*/
						],
						buttons: [{
							text: 'Save',
							handler: function() {
								var form = this.up('form').getForm();
								if (form.isValid()) {
									form.submit({
										success: function(form, action) {
											Ext.example.msg('Success', action.result.message, function(btn){
												if(btn == 'ok')
												{
											//storeDCBK.load();
											frmAddApekBK.getForm().reset();
										}
									});
										},
										failure: function(form, action) {
											Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
										}
									});
								} else {
									Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
								}
							}						
						},
						{
							text: 'Reset',
							handler: function() {
								frmAddApekBK.getForm().reset();
							}
						},
						{
							text: 'Cancel',
							handler: function() {
								frmAddApekBK.getForm().reset();
								winAPEKBK.hide();
							}
						}
						]
					});

winAPEKBK = Ext.widget('window', {
	closeAction: 'hide',
	width: 500,
	height: 200,
	layout: 'fit',
	resizable: true,
	modal: true,
	items: frmAddApekBK
});
}
winAPEKBK.setTitle('Tambah Uraian Analisa Pekerjaan / '+wintt);
winAPEKBK.show();
}				

/* upload analisa */
var frmUploadAnalisa = Ext.widget({
	xtype: 'form',
	layout: 'form',
	url: '<?php echo base_url(); ?>rencana/daftar_analisa/upload_daftar_analisa',
	frame: false,
	bodyPadding: '5 5 0',
	width: 350,
	fieldDefaults: {
		msgTarget: 'side',
		labelWidth: 75
	},
	items: [
	{
		xtype: 'hidden',
		name: 'id_proyek_rat',
		value: '<?=$idtender;?>'
	},
	{
		xtype: 'filefield',
		id: 'form-file',
		emptyText: 'silahkan pilih file...',
		afterLabelTextTpl: required,
		fieldLabel: 'File',
		name: 'upload_analisa',
		buttonText: 'pilih file',
		allowBlank: false
	},				
	],

	buttons: [{
		text: 'Upload',
		handler: function(){            
			var form = this.up('form').getForm();
			if(form.isValid()){
				form.submit({
					enctype: 'multipart/form-data',
					waitMsg: 'Upload CSV Daftar Analisa ...',
					success: function(fp, o) {
						Ext.MessageBox.alert('Status','Upload file "'+ o.result.file + '" berhasil.', function()
						{
							storeAnalisaPekerjaan.load();
						});
					},
					failure: function(fp, o){								
						Ext.MessageBox.alert('Error','GAGAL Upload file "'+ o.result.file + '", pesan: '+o.result.message);
					}
				});
				winUploadAnalisa.hide();
			}
		}
	},
	{
		text: 'Cancel',
		handler: function() {
			winUploadAnalisa.hide();
		}
	}]
});

var winUploadAnalisa = Ext.create('Ext.Window', {
	title: 'Upload Data Analisa',
	closeAction: 'hide',
	height: '20%',
	width: '30%',
	layout: 'fit',
	modal: true,
	items: frmUploadAnalisa
});				
/* end upload analisa */

/* copy rat dari proyek lain */
Ext.define('mdlCopyRAT', {
	extend: 'Ext.data.Model',
	fields: [		
	{name: 'rat_item_tree',     type: 'string'},
	{name: 'id_proyek_rat',     type: 'string'},
	{name: 'id_satuan', 		type: 'string'},
	{name: 'kode_tree',     	type: 'string'},
				//{name: 'kode_analisa',     	type: 'string'},
				{name: 'tree_item',     	type: 'string'},
				{name: 'tree_satuan',     	type: 'string'},
				{name: 'volume',     	type: 'float'},
				//{name: 'harga',     	type: 'float'},
				//{name: 'subtotal',     	type: 'float'},				
				{name: 'tree_parent_id',     	type: 'string'}
				]
			});

var storeCopyRAT = Ext.create('Ext.data.Store', {
	model: 'mdlCopyRAT',
	proxy: {
		type: 'ajax',
		url: '<?=base_url();?>rencana/copy_rat_proyek_lain/',
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
});			

var gridCopyItemRAT = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	store: storeCopyRAT,
	disableSelection: false,
	loadMask: true,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
	}),					
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},		
	columns:[
	{
		text: 'Kode',
		width: 50,
		sortable: false,
		dataIndex: 'kode_tree',
	},
	{
		text: 'Uraian',
		flex: 3,
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
		text: 'Volume',
		dataIndex: 'volume',
		flex: 1,
		align: 'center',
		sortable: false,
	},
	],
	bbar: Ext.create('Ext.PagingToolbar', {
		store: storeCopyRAT,
		displayInfo: true,
		displayMsg: 'Displaying data {0} - {1} of {2}',
		emptyMsg: "No data to display",
	}),			
	dockedItems: [
	{
		dock: 'top',
		xtype: 'toolbar',
		items: [							
		{
			fieldLabel: 'Pilih Proyek',
			xtype: 'combo',
			scope: this,
			name: 'pilih_id_proyek_rat',
			emptyText: 'Pilih Proyek',
			labelWidth: 100,
			flex: 2,
			valueField: 'id_proyek_rat',
			displayField: 'nama_proyek',
			typeAhead: true,
			queryMode: 'remote',
			store: { 
				fields: ['id_proyek_rat','nama_proyek'], 
				pageSize: 100, 
				proxy: { 
					type: 'ajax', 
					url: '<?=base_url();?>rencana/get_data_proyek_copy',
					reader: { 
						root: 'data',
						type: 'json' 
					} 
				} 
			},
			listeners: {
				select: function(combo, record, index) {
					storeCopyRAT.load({
						params:{'id_proyek_rat':combo.getValue()},
						scope: this,
						callback: function(records, operation, success)
						{
							if (success) {
							} else {
							}
						}
					});											
				}
			},
		}, 									
		]
	},
	{
		xtype: 'toolbar',
		dock: 'bottom',
		items: [
		{
			text: 'Copy',
			flex: 1,
			handler: function(){
				var records = gridCopyItemRAT.getView().getSelectionModel().getSelection(),
				itemid = [],uraian = [],volume = [],satuan = [],treeid = [];
				Ext.Array.each(records, function(rec){
					treeid.push(rec.get('kode_tree'));
					itemid.push(rec.get('rat_item_tree'));
					uraian.push(rec.get('tree_item'));
					volume.push(rec.get('volume'));
					satuan.push(rec.get('tree_satuan'));
				});
				if(treeid != '')
				{
					Ext.Ajax.request({
						url: '<?=base_url();?>rencana/copy_tree',
						method: 'POST',											
						params: {												
							'kode_tree' : treeid.join(','),
							'tree_item_id' : itemid.join(','),
							'tree_item' : uraian.join(','),
							'volume' : volume.join(','),
							'satuan' : satuan.join(','),
							'id_tender' : <?=$idtender;?>
						},								
						success: function(response) {
							Ext.MessageBox.alert('OK', 'Data telah di-copy silahkan pilih item kemudian paste pada item tersebut.', function(){
								winCopyRAT.hide();											
							});
						},
						failure: function(response) {
							Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
						}
					});			   	
				} else 
				{
					Ext.example.msg('Error', 'Silahkan pilih item yang mau di-copy!');
				}
			}
		},'-',
		{
			text: ' Tutup ',
			flex: 1,
			handler: function()
			{
				winCopyRAT.hide();
			}
		},
		]
	}
	],
	listeners:{
		beforerender:function(){
			storeCopyRAT.load();
		},
		itemclick: function(dv, record, item, index, e) {
		}						
	},
});

var winCopyRAT = Ext.widget('window', {
	closeAction: 'hide',
	closable: false,
	width: '80%',
	height: '80%',
	layout: 'fit',
	resizable: true,
	modal: true,
	items: [gridCopyItemRAT],
});		
/* end copy rat dari proyek lain */

var gvar = '';		
var gridTreeBK = Ext.create('Ext.tree.Panel', {
	title: 'RAT',
	id: 'gridTreeBKid',
	useArrows: true,
	rootVisible: false,
	store: storeTree,
	multiSelect: false,
	singleExpand: false,
	hideCollapseTool: false,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
		listeners:{		
			selectionchange:function(node, checked){
				console.log(node);
				nodes = node.selected.items;
				// for (var i = 0; i < nodes.length; i++) {
				// 	nodes[i].cascadeBy(function(n) {
				//         n.set('checked', checked);
				//     });
				// };
			}
		}
	}),			
	viewConfig: {
		stripeRows: true,
		listeners: {
			itemcontextmenu: function(view, rec, node, index, e) {
						/*
						e.stopEvent();
						gridBKctx.showAt(e.getXY());
						return false;
						*/
					}
				}
			},		
			plugins: Ext.create('Ext.grid.plugin.RowEditing', {
				clicksToMoveEditor: 1,
				autoCancel: false,
				listeners: {
					beforeedit: function(rec,obj){   
						if (obj.record.get('ishaschild') == 1) {
                            // return false;
                            gridTreeBK.columns[4].getEditor().setDisabled(true);
                            gridTreeBK.columns[5].getEditor().setDisabled(true);
                        } else {
                        	gridTreeBK.columns[4].getEditor().setDisabled(false);
                        	gridTreeBK.columns[5].getEditor().setDisabled(false);
                        }
                    },
                    'edit': function (a,b) {
                    	var editedRecords = gridTreeBK.getStore().getUpdatedRecords();
                    	// rec = b.record;
                    	// rec.set('tree_item',editedRecords[0].data.tree_item);
                    	// rec.set('tree_satuan',editedRecords[0].data.tree_satuan);
                    	// rec.set('volume',editedRecords[0].data.volume);
                    	// rec.set('subtotal',editedRecords[0].data.volume * editedRecords[0].data.harga);
                    	// rec.commit();

                    	Ext.Ajax.request({
                    		url: '<?=base_url();?>rencana/update_tree_item',
                    		method: 'POST',
                    		params: {								
                    			'rat_item_tree' : editedRecords[0].data.rat_item_tree,
                    			'tree_parent_id' : editedRecords[0].data.tree_parent_id,
                    			'id_proyek_rat' : editedRecords[0].data.id_proyek_rat,
                    			'kode_tree' : editedRecords[0].data.kode_tree,
                    			'satuan_id' : editedRecords[0].data.tree_satuan,
                    			'tree_item' : editedRecords[0].data.tree_item,
                    			'volume' : editedRecords[0].data.volume
                    		},								
                    		success: function(response) {
                    			var text = response.responseText;
                    			// Ext.Msg.alert( "Status", text, function(){
                    				storeTree.load();
                    			// });											
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
	dataIndex: 'kode_tree'
					/*
					editor: {
						xtype: 'textfield',
					}
					*/					
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
					editor: {
						xtype: 'textfield',
					}															
				}, 
				{
					text: 'Satuan',
					dataIndex: 'tree_satuan',
					flex: 1,
					sortable: false,
					editor: {
						xtype: 'combo',
						afterLabelTextTpl: required,
						allowBlank: false,
						store: { 
							fields: ['satuan_id','satuan_kode'], 
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
						triggerAction : 'all',					
						anchor: '100%',
						displayField: 'satuan_kode',
						valueField: 'satuan_kode',
					}										
				}, 				
				{
					text: 'Volume',
					dataIndex: 'volume',
					flex: 1,
					align: 'center',
					sortable: false,
					editor: {
						xtype: 'numberfield',
					}															
				}, 				
				{
					text: 'Harga',
					dataIndex: 'harga',
					width: 100,
					renderer: Ext.util.Format.numberRenderer('00,000'),
					align: 'right',
					sortable: false
				}, 								
				{
					text: 'Subtotal',
					width: 100,
					dataIndex: 'subtotal',
					align: 'right',
					renderer: Ext.util.Format.numberRenderer('00,000'),
					sortable: false
				}, 								
				{
					text: '',
					width: 20,
					menuDisabled: true,
					xtype: 'actioncolumn',
					align: 'center',
					items: [{
						icon: '<?=base_url();?>/assets/images/add.png',
						tooltip: 'Tambah sub item',
						handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
							var parentid = record.get('rat_item_tree');
							var parentname = record.get('kode_tree')+ ' ' +record.get('tree_item');
							Ext.Ajax.request({
								url: '<?php echo base_url();?>rencana/set_parent_tree_id/',
								params: {
									parent_id: parentid,
									parent_kode_tree: record.get('kode_tree')
								},
								success: function(response){
								//var text = response.responseText;
								winAddBKItem.setTitle('Tambah Uraian BK :: Sub Item - ' + parentname);
								winAddBKItem.on('show', function(win) {
									Ext.getCmp('info_uraian_id').setValue(parentname);
								});
								winAddBKItem.show();								
							}
						});						
						//showfrmAddBKItem(parentid,record,record.get('kode_tree'));
					},                                                          
					getClass: function(v, meta, record) {  
						var volume = record.get('volume');
						var harga = record.get('harga');
						var kode = record.get('kode_analisa'); 
						var kode_tree = record.get('kode_tree');
						var satuan = record.get('tree_satuan');

						if(volume == 1 && harga > 0 && kode !='' && (record.get('ishaschild') != 1) && kode_tree.length > 1 && satuan != 'Ls') {                                                                      
							return 'x-hide-display';
						}
						else if(volume > 1 && harga == 0 && kode !='' && (record.get('ishaschild') != 1) && kode_tree.length > 1 && satuan != 'Ls'){
							return 'x-hide-display';
						}
						else if(volume > 1 && harga == 0 && kode =='' && (record.get('ishaschild') != 1) && kode_tree.length > 1 && satuan != 'Ls'){
							return 'x-hide-display';
						}
						else if(volume > 1 && harga > 0 && kode !='' && (record.get('ishaschild') != 1) && kode_tree.length > 1 && satuan != 'Ls'){
							return 'x-hide-display';
						}
						else if(volume == 1 && harga == 0 && kode !='' && (record.get('ishaschild') != 1) && kode_tree.length > 1 && satuan != 'Ls'){
							return 'x-hide-display';
						}
						else if(volume == 1 && harga == 0 && kode =='' && (record.get('ishaschild') != 1) && kode_tree.length > 1 && satuan != 'Ls'){
							return 'x-hide-display';
						}
						else if(volume == 1 && harga == 0 && kode =='' && (record.get('ishaschild') != 1) && kode_tree.length == 1 && satuan != 'Ls'){
							return 'x-hide-display';
						}
                        // else if(parseInt(record.get('kode_tree').length,10) == 1){
                        // 	return 'x-hide-display';
                        // }
                    }
                }]
            },				
            {
            	text: '',
            	width: 20,
            	menuDisabled: true,
            	xtype: 'actioncolumn',
            	align: 'center',
            	items: [{
            		icon: '<?=base_url();?>/assets/images/application_go.png',
            		tooltip: 'Tambah Analisa Pekerjaan',
            		handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
            			var itemid = record.get('rat_item_tree');
            			var itemname = record.get('kode_tree')+ '. ' +record.get('tree_item');
						// if((parseInt(record.get('kode_tree').length,10) != 1) && (record.get('ishaschild') != 1))
						// {				
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/set_rat_item_tree',
								method: 'POST',
								params: {
									'id_tender' : <?=$idtender;?>,
									'rat_item_tree' : record.get('rat_item_tree'),
									'kode_tree' : record.get('kode_tree'),
								},
								success: function() {
									storeAnalisaPekerjaan2.load();
								},
								failure: function() {
									Ext.example.msg("ERROR", "Error due to connection problem!");
								}
							});			
							winDataAnalisa.setTitle('Data Analisa Pekerjaan :: '+itemname);
							winDataAnalisa.on('show', function(win) {

							});				
							winDataAnalisa.doLayout();
							winDataAnalisa.show();		
						// } else {
						// 	Ext.MessageBox.show({
						// 		title: 'Error',
						// 		msg: 'Tidak bisa menambah Analisa Satuan Pekerjaan',
						// 		buttons: Ext.MessageBox.OK,
						// 		icon: Ext.MessageBox.Error
						// 	});
						// }
					},                                                          
					getClass: function(v, meta, record) {         
						if(record.get('ishaschild') == 1) {                                                                      
							return 'x-hide-display';
						}
					}
				}]
			},
			{
				text: '',
				width: 20,
				menuDisabled: true,
				xtype: 'actioncolumn',
				tooltip: 'Delete Item',
				align: 'center',
				icon: '<?=base_url();?>/assets/images/delete.png',
				handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
					var itemid = record.get('rat_item_tree');
					if(record.get('ishaschild') != 1)
					{
						Ext.MessageBox.confirm('Hapus item menu > '+record.get('kode_tree') +'. '+record.get('tree_item'), 'Apakah anda akan menghapus Analisa Satuan untuk item ini?', function(btn){
							if(btn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?php echo base_url(); ?>rencana/del_tree_item',
									params: {
										tree_item_id: record.get('rat_item_tree')
									},
									success: function(form, action){
										Ext.MessageBox.alert('Sukses', 'Data berhasil dihapus!', function(){
											storeTree.load();
										});									
									},
									failure: function(response, options) {
										Ext.MessageBox.alert('Error', "Hapus terlebih dahulu Data Analisa Satuan Pekerjaan yang terhubung dengan item ini!");
									},									
								});
							}
						});						
					} else {
						Ext.MessageBox.show({
							title: 'Error',
							msg: 'Tidak bisa menghapus menu ini, hapus dulu sub menu di bawahnya!',
							buttons: Ext.MessageBox.OK,
							icon: Ext.MessageBox.Error
						});
					}						
				}
			},
			{
				text: '',
				flex: 1,
				menuDisabled: true,
				xtype: 'actioncolumn',
				align: 'center',
				items: [{
					icon: '<?=base_url();?>/assets/images/cross.gif',
					tooltip: 'Delete Analisa Satuan',
					handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
						var itemid = record.get('rat_item_tree');
						if(record.get('ishaschild') != 1)
						{
							Ext.MessageBox.confirm('Hapus Analisa Satuan -> '+record.get('kode_tree') +'. '+record.get('tree_item'), 'Apakah anda akan menghapus Analisa Satuan untuk item ini?', function(btn){
								if(btn == 'yes')
								{
									Ext.Ajax.request({
										url: '<?php echo base_url(); ?>rencana/del_analisa_satuan_item',
										params: {
											'tree_item_id': record.get('rat_item_tree')
										},
										success: function(form, action){
											Ext.MessageBox.alert('Sukses', 'Data Analisa satuan berhasil dihapus!', function(){
												storeTree.load();
												storeASAT.load();
												storeANSAT.load();
												storeDCBK.load();												
											});									
										},
										failure: function(response, options) {
											Ext.MessageBox.alert('Error', "Error due to connection problem!");
										},									
									});
								}
							});						
						} else {
							Ext.MessageBox.show({
								title: 'Error',
								msg: 'Tidak bisa menghapus Analisa satuan untuk item ini!',
								buttons: Ext.MessageBox.OK,
								icon: Ext.MessageBox.Error
							});
						}						
					},                                                          
					getClass: function(v, meta, record) {          
						if(record.get('ishaschild') == 1) {                                                                      
							return 'x-hide-display';
						}
					}
				}]
			}								
			],
			dockedItems: [
			{
				xtype:'toolbar',
				dock:'top',
				itemId:'top_bar',
				items:[						

				{
					text:'Kembali',
					iconCls: 'icon-back',
					handler: function(){
						winBKAdd.hide();
					}
				},'-',
				{
					fieldLabel:'Search <b>(Press ENTER) <b>',
					labelWidth:140,
					width:244,
					xtype:'textfield',
					itemId:'search',
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
					iconCls:'icon-reload',
					text:'Clear',
					handler: function(){
						bar = gridTreeBK.getComponent('top_bar');
						bar.getComponent('search').setValue('');
						storeTree.proxy.setExtraParam('param','');
						storeTree.load();
					}
				},'->','-',
				{
					text: 'Export Item Pekerjaan',
					iconCls: 'icon-print',
					handler: function(){
						Ext.MessageBox.confirm('Export', 'Apakah anda akan meng-Export Item Pekerjaan ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								window.location = '<?=base_url();?>rencana/excel/rat';
							}
						});	
					}
				},
				]
			},
			{
				xtype: 'toolbar',
				dock: 'top',
				items: [{
					text: 'Tambah Item utama',
					iconCls: 'icon-add',
					handler: function(){
						Ext.Ajax.request({
							url: '<?php echo base_url();?>rencana/set_parent_tree_id/',
							params: {
								parent_id: '0',
								parent_kode_tree: ''
							},
							success: function(response){
								showfrmAddIDCBKBK(0);
							}
						});													
					}
				}, 
						/*
						{
							text: 'Reset All Data',
							iconCls: 'icon-del',
							handler: function(){
									Ext.MessageBox.confirm('RESET ALL DATA', 'Apakah anda akan menghapus semua item dan analisa satuan pekerjaan ini?',function(resbtn){
										if(resbtn == 'yes')
										{
											Ext.Ajax.request({
												url: '<?=base_url();?>rencana/reset_data_rat',
												method: 'POST',
												params: {
													'id_proyek_rat' : <?=$idtender;?>
												},								
												success: function(response) {
													var text = response.responseText;
													Ext.example.msg( "STATUS", text, function(){
														storeTree.load();
														storeANSAT.load();
														storeDCBK.load();
													});											
												},
												failure: function(response) {
													Ext.MessageBox.alert('Failure', 'Reset All Data Error due to connection problem!');
												}
											});										
										}
									});
								}
						},'-',
						*/
						{
							text: 'Import CSV',
							iconCls: 'icon-add',
							handler: function(){
								Ext.MessageBox.alert("Peringatan","Import CSV akan menimpa Item yang sudah ada...",function(){
									winUploadRATitem.setTitle('Upload RAT Item (CSV)');
									winUploadRATitem.on('show', function(win) {
									});				
									winUploadRATitem.doLayout();
									winUploadRATitem.show();
								});
							}
						},'-',
						{
							text: 'Refresh',
							iconCls: 'icon-reload',
							handler: function(){          
								storeTree.load();
							}
						},'-',
						'->',
						{
							text: 'Export Analisa',
							iconCls: 'icon-print',
							handler: function(){
								Ext.MessageBox.confirm('Export Analisa', 'Apakah anda akan meng-Export Analisa Item Pekerjaan ini?',function(resbtn){
									if(resbtn == 'yes')
									{
										window.location = '<?=base_url();?>rencana/daftar_analisa/print_data_analisa/rat_data_analisa';
									}
								});	
							}
						},'-',
						{
							text: 'Copy Analisa',
							iconCls: 'icon-copy',
							handler: function(){
								winCopyAnalisa.setTitle('Copy Analisa Pekerjaan');
								winCopyAnalisa.on('show', function(win) {
									storeAnalisaPekerjaan2.load();								
								});				
								winCopyAnalisa.doLayout();
								winCopyAnalisa.show();
							}
						},'-',
						{
							text: 'Paste Analisa',
							iconCls: 'icon-paste',
							handler: function(){
								var records = gridTreeBK.getView().getSelectionModel().getSelection(),
								itemid = [], treeid = [];
								Ext.Array.each(records, function(rec){
									treeid.push(rec.get('kode_tree'));
									itemid.push(rec.get('rat_item_tree'));
								});
								if(treeid != '')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/paste_analisa_tree',
										method: 'POST',											
										params: {												
											'kode_tree' : treeid.join(','),
											'tree_item_id' : itemid.join(','),
											'id_tender' : <?=$idtender;?>
										},								
										success: function(response) {										
											Ext.MessageBox.alert('Status', response.responseText, function(){
												storeTree.load();
											});
										},
										failure: function(response) {
											Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
										}
									});			   	
								} else 
								{
									Ext.example.msg('Error', 'Silahkan pilih item untuk Paste item yg sudah di-copy!');
								}							
							}
						},
						]
					},
					{
						xtype: 'toolbar',
						dock: 'bottom',
						items: [
						{
							text: 'Copy Uraian',
							iconCls: 'icon-copy',
							handler: function(){          
								var records = gridTreeBK.getView().getSelectionModel().getSelection(),
								itemid = [],kdanalisa = [],uraian = [],volume = [],satuan = [],treeid = [];
								Ext.Array.each(records, function(rec){
									treeid.push(rec.get('kode_tree'));
									itemid.push(rec.get('rat_item_tree'));
									kdanalisa.push(rec.get('kode_analisa'));
									uraian.push(rec.get('tree_item'));
									volume.push(rec.get('volume'));
									satuan.push(rec.get('tree_satuan'));
								});
								if(treeid != '')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/copy_tree',
										method: 'POST',											
										params: {												
											'kode_tree' : treeid.join(','),
											'tree_item_id' : itemid.join(','),
											'tree_item' : uraian.join(','),
											'kode_analisa' : kdanalisa.join(','),
											'volume' : volume.join(','),
											'satuan' : satuan.join(','),
											'id_tender' : <?=$idtender;?>
										},								
										success: function(response) {
											Ext.MessageBox.alert('OK', 'Data telah di-copy silahkan pilih item kemudian paste pada item tersebut.');
										},
										failure: function(response) {
											Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
										}
									});			   	
								} else 
								{
									Ext.example.msg('Error', 'Silahkan pilih item yang mau di-copy!');
								}
							}
						},'-',
						{
							text: 'Paste Uraian',
							iconCls: 'icon-paste',
							handler: function(){
								var records = gridTreeBK.getView().getSelectionModel().getSelection(),
								itemid = [], treeid = [];
								Ext.Array.each(records, function(rec){
									treeid.push(rec.get('kode_tree'));
									itemid.push(rec.get('rat_item_tree'));
								});
								if(treeid != '')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/paste_tree',
										method: 'POST',											
										params: {												
											'kode_tree' : treeid.join(','),
											'tree_item_id' : itemid.join(','),
											'id_tender' : <?=$idtender;?>
										},								
										success: function(response) {										
											Ext.MessageBox.alert('Status', response.responseText, function(){
												storeTree.load();
											});
										},
										failure: function(response) {
											Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
										}
									});			   	
								} else 
								{
									Ext.example.msg('Error', 'Silahkan pilih item untuk Paste item yg sudah di-copy!');
								}
							}
						},'-',
						{
							text:'Import Proyek Lain',
							iconCls:'icon-import',
							handler:function(){
								Ext.MessageBox.alert("Peringatan","Import Proyek Lain akan menimpa Item yang sudah ada...",function(){									
									copy_item_proyek_lain(storeTree);
								});
							}
						},'-',
						{
							text: 'Copy dari Proyek Lain',
							iconCls: 'icon-copy',
							handler: function(){
								winCopyRAT.setTitle('Copy item RAT dari Proyek Lain');
								winCopyRAT.on('show', function(win) {
									storeCopyRAT.load();
								});						
								winCopyRAT.doLayout();
								winCopyRAT.show();
							}							
						},'-',
						{
							text: 'Delete',
							iconCls: 'icon-del',
							handler: function(){
								var records = gridTreeBK.getView().getSelectionModel().getSelection(),
								itemid = [],ischild = [],treeid = [];
								Ext.Array.each(records, function(rec){
									treeid.push(rec.get('kode_tree'));
									ischild.push(rec.get('ishaschild'));
									itemid.push(rec.get('rat_item_tree'));
								});
								if(treeid != '')
								{
									Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus kode item ('+treeid.join(', ')+') ini?',function(resbtn){
										if(resbtn == 'yes')
										{
											Ext.Ajax.request({
												url: '<?=base_url();?>rencana/delete_tree_item',
												method: 'POST',											
												params: {												
													'kode_tree' : treeid.join(','),
													'tree_item_id' : itemid.join(','),
													'ishaschild': ischild.join(','),
													'id_tender' : <?=$idtender;?>
												},								
												success: function(response) {										
													Ext.MessageBox.alert('Status', response.responseText, function(){
														storeTree.load();
													});
												},
												failure: function(response) {
													Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
												}
											});
										}
									});								
								} else 
								{
									Ext.example.msg('Error', 'Silahkan pilih item yang mau dihapus!');
								}							
							}							
						},
						]
					},
					{
						xtype: 'toolbar',
						dock: 'bottom',
						items: [
						'->',
						{
							text: 'Total Biaya Konstruksi : ',
							id: 'id-total-rat',
							iconCls: 'icon-total',
							handler: update_total_bk
						},
						]
					}				
					],
					listeners:{
						beforerender:function(){
							storeTree.load();
							update_total_bk();
						}
					}						
				});

function update_total_bk()
{
	Ext.Ajax.request({
		url: '<?=base_url();?>rencana/get_total_bk',
		method: 'POST',											
		params: {												
			'id_tender' : <?=$idtender;?>
		},								
		success: function(response) {			
			Ext.getCmp('id-total-rat').setText('<b>Total Biaya Konstruksi : '+ response.responseText+'</b>');
		},
		failure: function(response) {
					// Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
				}
			});			   			
}

/* import rat csv */
var frmUploadRATItem = Ext.widget({
	xtype: 'form',
	layout: 'form',
	url: '<?php echo base_url(); ?>rencana/upload_rat_item',
	frame: false,
	bodyPadding: '5 5 0',
	width: 350,
	fieldDefaults: {
		msgTarget: 'side',
		labelWidth: 75
	},
	items: [
	{
		xtype: 'hidden',
		name: 'id_proyek_rat',
		value: '<?=$idtender;?>'
	},
	{
		xtype: 'filefield',
		id: 'form-file',
		emptyText: 'silahkan pilih file...',
		afterLabelTextTpl: required,
		fieldLabel: 'File',
		name: 'upload_analisa',
		buttonText: 'pilih file',
		allowBlank: false
	},				
	],

	buttons: [{
		text: 'Upload',
		handler: function(){            
			var form = this.up('form').getForm();
			if(form.isValid()){

				Ext.MessageBox.show({
					title: 'Please wait',
					msg: 'Loading items...',
					width:300,
					progress:true,
					closable:false,
					animate:true,	
					buttons: Ext.Msg.OK,
					buttonText:{ 
		                ok: "Cancel"
		            },
					fn:function(){
						Ext.MessageBox.alert('Status','Cancel', function()
						{
							return false;
							location.reload();
						});
					}
				});

				form.submit({
					enctype: 'multipart/form-data',
							// waitMsg: 'Upload CSV RAT item ...',
							success: function(fp, o) {
								Ext.MessageBox.alert('Status','Upload file "'+ o.result.file + '" berhasil.', function()
								{
									storeTree.load();
								});
							},
							failure: function(fp, o){								
								Ext.MessageBox.alert('Error','GAGAL Upload file "'+ o.result.file + '", pesan: '+o.result.message);
							}
						});


				winUploadRATitem.hide();
			}
		}
	},
	{
		text: 'Cancel',
		handler: function() {
			winUploadRATitem.hide();
		}
	}]
});



var winUploadRATitem = Ext.create('Ext.Window', {
	title: 'Upload Data Analisa',
	closeAction: 'hide',
	height: '25%',
	width: '40%',
	layout: 'fit',
	modal: true,
	items: frmUploadRATItem
});									
/* end import rat csv */

/* add item BK */
var frmAddBKItem = Ext.widget({
	xtype: 'form',
	layout: 'form',
	url: '<?=base_url();?>rencana/tambah_rat_tree_item/<?=$idtender;?>',
	frame: false,
	bodyPadding: '5 5 0',
	formBind:true,
	width: 400,
	height: 200,
	fieldDefaults: {
		msgTarget: 'side',
		labelWidth: 150
	},
	defaultType: 'textfield',										
	items: [
				/*
				{
					name: 'tree_parent_id',
					xtype: 'hiddenfield',
					value: parentid
				},												
				{
					name: 'kode_tree',
					xtype: 'hiddenfield',
					value: k_tree
				},
				*/
				{
					name: 'id_proyek_rat',
					xtype: 'hiddenfield',
					value: <?=$idtender;?>,
				},
				{
					name: 'info_uraian',
					fieldLabel: 'sub Menu',							
					id: 'info_uraian_id',
					xtype: 'textfield',
					//value: parentname.get('kode_tree')+ ' ' +parentname.get('tree_item'),
					readOnly: true,
				},												
				{
					xtype: 'combo',
					name: 'tree_item',
					afterLabelTextTpl: required,
					allowBlank: false,
					store: { 
						fields: ['detail_material_id','detail_material_kode', 'detail_material_nama','detail_material_satuan','subbidang_kode'], 
						pageSize: 200, 
						proxy: { 
							type: 'ajax', 
							url: '<?=base_url();?>rencana/get_detailmaterial_kode/',
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
					valueField: 'detail_material_nama',
					listeners: {
						'select': function(combo, row, index) {
						}
					},														
					pageSize: 200
				},	
				{
					xtype: 'combo',
					name: 'tree_satuan',
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
					pageSize: 100
				},
				{
					name: 'volume',
					xtype: 'numberfield',
					fieldLabel: 'Volume',
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
											storeTree.load();
											frmAddBKItem.getForm().reset();
										}
									});
								},
								failure: function(form, action) {
									Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
								}
							});
						} else {
							Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
						}
					}						
				},
				{
					text: 'Reset',
					handler: function() {
						frmAddBKItem.getForm().reset();
					}
				},
				{
					text: 'Close',
					handler: function() {
						frmAddBKItem.getForm().reset();
						winAddBKItem.hide();
					}
				}
				],
				renderer: function(){
				}
			});

var winAddBKItem = Ext.widget('window', {
	closeAction: 'hide',
	width: 500,
	height: 200,
	layout: 'fit',
	resizable: true,
	modal: true,
	items: frmAddBKItem
});		
/* end add BK */

Ext.define('ModelBKAnsat', {
	extend: 'Ext.data.Model',
	fields: [
	'detail_material_kode', 'detail_material_nama', 'detail_material_spesifikasi', 
	'subbidang_kode', 'detail_material_satuan', 'kategori', 'koefisien'
	],
	idProperty: 'ModelBKANSATid'
});

var storeANSAT = Ext.create('Ext.data.Store', {
	model: 'ModelBKAnsat',
	proxy: {
		type: 'ajax',
		url: '<?=base_url();?>rencana/get_data_ansat/<?=$idtender;?>',
		reader: {
			type: 'json',
			root: 'data',
			totalProperty: 'total'
		},
		simpleSortMode: true				 
	},
	autoLoad: true,
	remoteSort: true,
	pageSize: 100,
});

		/*
		var storeANSAT = Ext.create('Ext.data.Store', {
			pageSize: 100,
			model: ModelBKAnsat,			
			remoteSort: true,
			proxy: {
				type: 'jsonp',
				url: '<?=base_url();?>rencana/get_data_ansat/<?=$idtender;?>',
				reader: {
					root: 'data',
					totalProperty: 'total'
				},
				simpleSortMode: true
			},		
			sorters: [{
				property: 'detail_material_kode',
				direction: 'DESC'
			}]
		});		
*/

Ext.define('modelSubbidang', {
	extend: 'Ext.data.Model',
	fields: [
	'subbidang_kode', 'subbidang_name', 'kd_bidang'
	],
});

var store_subbidang = Ext.create('Ext.data.Store', {
	pageSize: 100,
	model: modelSubbidang,
	remoteSort: true,
	proxy: {
		type: 'jsonp',
		url: '<?=base_url();?>rencana/get_subbidang/<?=$idtender;?>',
		reader: {
			root: 'data',
			totalProperty: 'total'
		},
		simpleSortMode: true
	},		
	sorters: [{
		property: 'subbidang_kode',
		direction: 'DESC'
	}]
});				

/* window analisa pekerjaan */
Ext.define('mdlAnalisaPekerjaan2', {
	extend: 'Ext.data.Model',
	fields: [
	'id_data_analisa', 'kode_analisa', 'id_kat_analisa', 'kategori', 'nama_kategori',
	'nama_item', 'id_satuan', 'satuan', 'id_tender', 'harga_satuan'
	]
});

var storeAnalisaPekerjaan2 = Ext.create('Ext.data.Store', {
	model: 'mdlAnalisaPekerjaan2',
	pageSize: 100,  
	remoteFilter: true,
	autoLoad: false,
	proxy: {
		type: 'ajax',
		url: '<?php echo base_url() ?>rencana/daftar_analisa/get_data_analisa_pekerjaan/<?=$idtender;?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	},
	groupField: 'kategori',
	sorters: [{
		property: 'kode_analisa',
		direction: 'ASC'
	}],								
});		

var gridBKAnalisa = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	frame: false,
	store: storeAnalisaPekerjaan2,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'SINGLE', 
		multiSelect: true,
		keepExisting: true,
	}),
	columns: [
	{
		text: "Kode", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'kode_analisa',
	},
	{
		text: "Item Pekerjaan", 
		flex: 3, 
		sortable: false, 
		dataIndex: 'nama_item',
	},
	{
		text: "Satuan", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'satuan',
	},
	{
		text: "Kategori", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'nama_kategori',
	},		
	{
		text: "Harga Satuan", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'harga_satuan',
		align: 'right',
		renderer: Ext.util.Format.numberRenderer('00,000'),										
	},
	],
	bbar: Ext.create('Ext.PagingToolbar', {
		store: storeAnalisaPekerjaan2,
		displayInfo: true,
		displayMsg: 'Displaying data {0} - {1} of {2}',
		emptyMsg: "No data to display",
	}),				
	columnLines: true,
	dockedItems: [
	{

		xtype: 'toolbar',
		dock: 'top',						
		items: [
		'->',
		{
			flex: 1,
			fieldLabel: 'Pencarian',
			labelWidth: 50,
			tooltip:'masukan kode analisa / uraian',
			emptyText: 'kode analisa / uraian...',
			xtype: 'searchfield',
			name: 'cari_analisa',
			store: storeAnalisaPekerjaan2,
			listeners: {
				keyup: function(e){ 
											/*
											for(var key in e)
												alert(key+'='+e[key]);
											*/
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
									text:'Simpan Pilihan',
									flex: 1,								
									handler: function(){          
										var records = gridBKAnalisa.getView().getSelectionModel().getSelection(),
										names = [],
										koef = [],
										dmid = [];
										Ext.Array.each(records, function(rec){
											names.push(rec.get('kode_analisa'));
											dmid.push(rec.get('id_data_analisa'));
											koef.push(rec.data.koefisien);
										});
										if(names != '')
										{
											Ext.Ajax.request({
												url: '<?=base_url();?>rencana/tambah_apek',
												method: 'POST',											
												params: {												
													'kode_analisa' : names.join(','),
													'id_data_analisa' : dmid.join(','),
													'koefisien' : koef.join(','),
													'id_tender' : <?=$idtender;?>
												},								
												success: function(response) {
													var text = response.responseText;
													Ext.Msg.alert( "Tambah ANSAT", text, function(){													
														storeANSAT.load();
														storeTree.load();
														storeAnalisaPekerjaan2.load();
														winDataAnalisa.hide();
													});	

												},
												failure: function(response) {
													Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
												}
											});			   																													
										} else 
										{
											Ext.example.msg('Error', 'Silahkan pilih material');
										}
									}
								}, '-',				
								{
									text:'Tutup',
									flex: 1,
									handler: function(){          
										winDataAnalisa.hide();
									}
								},
								]							
							}
							],
						});

var winDataAnalisa = Ext.widget('window', {
	title: 'Pilih Data Analisa',
	closeAction: 'hide',
	closable: false,					
	width: '80%',
	height: '90%',
	layout: 'fit',
	resizable: true,
	modal: true,					
	items: gridBKAnalisa,
});		
/* end window analisa pekerjaan  */

/* copy analisa */
var gridCopyAnalisa = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	frame: false,
	store: storeAnalisaPekerjaan2,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'SINGLE', 
		multiSelect: true,
		keepExisting: true,
	}),
	columns: [
	{
		text: "Kode", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'kode_analisa',
	},
	{
		text: "Item Pekerjaan", 
		flex: 3, 
		sortable: false, 
		dataIndex: 'nama_item',
	},
	{
		text: "Satuan", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'satuan',
	},
	{
		text: "Kategori", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'nama_kategori',
	},		
	{
		text: "Harga Satuan", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'harga_satuan',
		align: 'right',
		renderer: Ext.util.Format.numberRenderer('00,000'),										
	},
	],
	bbar: Ext.create('Ext.PagingToolbar', {
		store: storeAnalisaPekerjaan2,
		displayInfo: true,
		displayMsg: 'Displaying data {0} - {1} of {2}',
		emptyMsg: "No data to display",
	}),				
	columnLines: true,
	dockedItems: [
	{

		xtype: 'toolbar',
		dock: 'top',						
		items: [
		'->',
		{
			flex: 1,
			fieldLabel: 'Pencarian',
			labelWidth: 50,
			tooltip:'masukan kode analisa / uraian',
			emptyText: 'kode analisa / uraian...',
			xtype: 'searchfield',
			name: 'cari_analisa',
			store: storeAnalisaPekerjaan2,
			listeners: {
				keyup: function(e){ 
											/*
											for(var key in e)
												alert(key+'='+e[key]);
											*/
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
									text:'Copy Analisa',
									flex: 1,								
									handler: function(){          
										var records = gridCopyAnalisa.getView().getSelectionModel().getSelection(),
										names = [],harga = [],dmid = [];
										Ext.Array.each(records, function(rec){
											names.push(rec.get('kode_analisa'));
											dmid.push(rec.get('id_data_analisa'));
											harga.push(rec.data.harga_satuan);
										});
										if(names != '')
										{
											Ext.Ajax.request({
												url: '<?=base_url();?>rencana/copy_analisa_tree',
												method: 'POST',											
												params: {												
													'kode_analisa' : names.join(','),
													'id_data_analisa' : dmid.join(','),
													'harga' : harga.join(','),
													'id_tender' : <?=$idtender;?>
												},								
												success: function(response) {
													Ext.Msg.alert("Status", response.responseText);
												},
												failure: function(response) {
													Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
												}
											});			   																													
										} else 
										{
											Ext.example.msg('Error', 'Silahkan pilih Analisa yang mau dicopy');
										}
									}
								}, '-',				
								{
									text:'Tutup',
									flex: 1,
									handler: function(){          
										winCopyAnalisa.hide();
									}
								},
								]							
							}
							],
						});

var winCopyAnalisa = Ext.widget('window', {
	title: 'Pilih Data Analisa',
	closeAction: 'hide',
	closable: false,					
	width: '80%',
	height: '90%',
	layout: 'fit',
	resizable: true,
	modal: true,					
	items: gridCopyAnalisa,
});
/* end copy analisa */

/* pilih analisa */
var gridPilihAnalisa = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	frame: false,
	store: storeAnalisaPekerjaan2,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
	}),
	columns: [
	{
		text: "Kode", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'kode_analisa',
	},
	{
		text: "Item Pekerjaan", 
		flex: 3, 
		sortable: false, 
		dataIndex: 'nama_item',
	},
	{
		text: "Satuan", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'satuan',
	},
	{
		text: "Harga Satuan", 
		flex: 1, 
		sortable: false, 
		dataIndex: 'harga_satuan',
		align: 'right',
		renderer: Ext.util.Format.numberRenderer('00,000'),										
	},
	],
	bbar: Ext.create('Ext.PagingToolbar', {
		store: storeAnalisaPekerjaan2,
		displayInfo: true,
		displayMsg: 'Displaying data {0} - {1} of {2}',
		emptyMsg: "No data to display",
	}),				
	columnLines: true,
	dockedItems: [
	{

		xtype: 'toolbar',
		dock: 'top',						
		items: [
		'->',
		{
			flex: 1,
			fieldLabel: 'Pencarian',
			labelWidth: 50,
			tooltip:'masukan kode analisa / uraian',
			emptyText: 'kode analisa / uraian...',
			xtype: 'searchfield',
			name: 'cari_analisa',
			store: storeAnalisaPekerjaan2,
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
			text:'Simpan Pilihan',
			flex: 1,
			handler: function(){          
				var records = gridPilihAnalisa.getView().getSelectionModel().getSelection(),
				names = [],
				harga = [],
				dmid = [];
				Ext.Array.each(records, function(rec){
					names.push(rec.get('kode_analisa'));
					dmid.push(rec.get('id_data_analisa'));
					harga.push(rec.data.harga_satuan);
				});
				if(names != '')
				{
					Ext.Ajax.request({
						url: '<?=base_url();?>rencana/daftar_analisa/tambah_apek',
						method: 'POST',											
						params: {																	
							'kode_analisa' : names.join(','),
							'id_data_analisa' : dmid.join(','),
							'harga_satuan': harga.join(','), 
							'koefisien' : 1,
							'id_tender' : <?=$idtender;?>
						},								
						success: function(response) {
							var text = response.responseText;
							Ext.example.msg( "Status", text, function(){
								storeAnalisaPekerjaan.load();
								storeANSAT.load();
								storeASAT.load();													
													// storeTree.load();
													storeAnalisaPekerjaan2.load();
													winPilihAnalisa.hide();
												});											
						},
						failure: function(response) {											
							Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
						}
					});			   																													
				} else 
				{
					Ext.example.msg('Error', 'Silahkan pilih material');
				}

									/*
									var records = gridPilihAnalisa.getView().getSelectionModel().getSelection();
									var rec = storeAnalisaPekerjaan2.getAt(rowIndex);								
									var item = rec.get('kode_analisa');
									if(item != '')
									{
										Ext.Ajax.request({
											url: '<?=base_url();?>rencana/daftar_analisa/tambah_apek',
											method: 'POST',											
											params: {												
												'kode_analisa' : rec.get('kode_analisa'),
												'id_data_analisa' : rec.get('id_data_analisa'),
												'harga_satuan': rec.data.harga_satuan,
												'id_tender' : <?=$idtender;?>
											},								
											success: function(response) {
												Ext.example.msg( "Status", response.responseText, function(){
													storeTree.load();
													storeDCBK.load();
													winPilihAnalisa.hide();
												});											
											},
											failure: function(response) {
												var pesan = 'Insert Data Error, duplicate key, Please check your selection,\nor you have already the same data!';
												Ext.MessageBox.show({
													title: 'ERROR',
													msg: pesan,
													icon: Ext.MessageBox.ERROR,
													buttons: Ext.Msg.OK
												});										
											}
										});			   																													
									} else 
									{
										Ext.example.msg('Error', 'Silahkan pilih material');
									}
									*/
								}
							}, '-',
							{
								text:'Tutup',
								flex: 1,
								handler: function(){          
									winPilihAnalisa.hide();
								}
							},
							]							
						}
						],
					});

var winPilihAnalisa = Ext.widget('window', {
	title: 'Pilih Data Analisa',
	closeAction: 'hide',
	closable: false,					
	width: '80%',
	height: '80%',
	layout: 'fit',
	resizable: true,
	modal: true,					
	items: gridPilihAnalisa,
	listeners:{
		hide:function() {
			storeASAT.load();
			storeAnalisaPekerjaan.load();
		}
	}
});				
/* end pilih analisa */

/* grid analisa satuan */
		/*
		var gridANSAT = Ext.create('Ext.grid.Panel', {
			width: '100%',
			height: '100%',
			store: storeANSAT,
			disableSelection: false,
			loadMask: true,
			selModel: Ext.create('Ext.selection.CheckboxModel', {
				mode: 'MULTI', 
				multiSelect: true,
				keepExisting: true,
			}),
			viewConfig: {
				trackOver: true,
				stripeRows: true,
			},		
			plugins: Ext.create('Ext.grid.plugin.CellEditing', {
				clicksToEdit: 1,
				listeners : {
					afteredit : function() {
					}
				}
			}),						
			columns:[
				{ 
					header: 'Kode Material', 
					dataIndex: 'detail_material_kode', 
					width: 90,
				},
				{
					text: 'Nama Material',
					dataIndex: 'detail_material_nama',
					width: 250,
					sortable: false
				},
				{
					text: 'Spesifikasi',
					dataIndex: 'detail_material_spesifikasi',
					width: 70,
					sortable: false
				},
				{
					text: 'Satuan',
					dataIndex: 'detail_material_satuan',
					width: 50,
					sortable: false
				},
				{
					text: "Kategori",
					dataIndex: 'kategori',
					width: 150,
					sortable: false
				},						
				{
					text: 'Koefisien',
					dataIndex: 'koefisien',
					width: 60,
					sortable: false,
					editor: {
						xtype: 'numberfield',
						allowBlank: false,
					},							
				},						
			],
			bbar: Ext.create('Ext.PagingToolbar', {
				store: storeANSAT,
				displayInfo: true,
				displayMsg: 'Displaying data {0} - {1} of {2}',
				emptyMsg: "No data to display",
			}),			
			dockedItems: [
				{
					layout : 'hbox',
					layoutConfig : {
						type :'hbox',
						align : 'stretch',
					},
					defaults :{
						frame : true,
						flex : 1
					},
					dock: 'top',
					xtype: 'toolbar',
					items: [							
							'Ambil dari Analisa',
							{
								xtype: 'combo',
								scope: this,
								name: 'analisa',
								emptyText: 'Pilih Analisa',
								flex: 3,
								valueField: 'subbidang_kode',
								displayField: 'kd_bidang',
								typeAhead: true,
								queryMode: 'remote',
								store: { 
									fields: ['subbidang_kode', 'kd_bidang'], 
									pageSize: 100, 
									proxy: { 
										type: 'ajax', 
										url: '<?=base_url();?>rencana/get_subbidang', 
										reader: { 
											root: 'data',
											type: 'json' 
										} 
									} 
								},
								listeners: {
									select: function(combo, record, index) {
										storeANSAT.load({
											params:{'subbidang_kode':combo.getValue()},
											scope: this,
											callback: function(records, operation, success)
											{
												if (success) {
													console.log("Category: "+combo.getValue());
												} else {
													console.log('Error get Category');
												}
											}
										});											
									}
								},
							},'->',
							'Kategori',
							{
								xtype: 'combo',
								scope: this,
								id: 'cmb_kategori_id',
								name: 'kategori',
								emptyText: 'Pilih Kategori',
								flex: 2,
								valueField: 'subbidang_kode',
								displayField: 'kd_bidang',
								typeAhead: true,
								queryMode: 'remote',
								store: { 
									fields: ['subbidang_kode', 'kd_bidang'], 
									pageSize: 100, 
									proxy: { 
										type: 'ajax', 
										url: '<?=base_url();?>rencana/get_subbidang', 
										reader: { 
											root: 'data',
											type: 'json' 
										} 
									} 
								},
								listeners: {
									select: function(combo, record, index) {
										storeANSAT.load({
											params:{'subbidang_kode':combo.getValue()},
											scope: this,
											callback: function(records, operation, success)
											{
												if (success) {
													console.log("Category: "+combo.getValue());
												} else {
													console.log('Error get Category');
												}
											}
										});											
									}
								},
							}, 									
							{
								xtype: 'textfield',
								flex: 3,
								name: 'detail_material_nama',
								id: 'detail_material_nama_id',
								emptyText: 'masukan nama / kode material ...',
								enableKeyEvents: false,
								listeners: {
								  keypress : function(textfield,eventObject){
									  if (eventObject.getCharCode() == Ext.EventObject.ENTER) {
											storeANSAT.load({
												params:{
													'query':Ext.getCmp('detail_material_nama_id').getValue(),
													'subbidang_kode':Ext.getCmp('cmb_kategori_id').getValue()												
													},
												scope: this,
												callback: function(records, operation, success)
												{
													if (success) {
														 console.log("Filter: " + Ext.getCmp('detail_material_nama_id').getValue()); 
													} else {
														 console.log('Error filter');
													}
												}
											});																																
									  }
								  }
								}									
							},												
							{
								text: 'Filter >>',
								cls: 'button',
								handler: function()
								{
									storeANSAT.load({
										params:{
											'query':Ext.getCmp('detail_material_nama_id').getValue(),
											'subbidang_kode':Ext.getCmp('cmb_kategori_id').getValue()												
											},
										scope: this,
										callback: function(records, operation, success)
										{
											if (success) {
												console.log("Filter: " + Ext.getCmp('detail_material_nama_id').getValue()); 
											} else {
												console.log('Error filter'); 
											}
										}
									});																				
								}
							}, 
					]
				},
				{
					xtype: 'toolbar',
					dock: 'bottom',
					items: [
						{
							text: 'Simpan semua pilihan',
							handler: function(){
								var records = gridANSAT.getView().getSelectionModel().getSelection(),
									names = [],
									koef = [],
									dmid = [];
								//var editedRecords = gridANSAT.getStore().getUpdatedRecords();
								Ext.Array.each(records, function(rec){
									names.push(rec.get('detail_material_kode'));
									dmid.push(rec.get('detail_material_id'));
									koef.push(rec.data.koefisien);
								});
								if(dmid != '')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/tambah_ansat',
										method: 'POST',
										params: {
											'detail_material_kode' : names.join(','),
											'detail_material_id' : dmid.join(','),
											'koefisien' : koef.join(','),
											//'rat_item_tree' : iditem,
											'id_proyek_rat' : <?=$idtender;?>
										},								
										success: function(response) {
											var text = response.responseText;
											Ext.example.msg( "Tambah ANSAT", text, function(){
												storeANSAT.load();
												storeDCBK.load();
											});											
										},
										failure: function(response) {
											Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
										}
									});			   																													
								} else 
								{
									Ext.example.msg('Error', 'Silahkan pilih material');
								}
							}
						},'-',
						{
							text: ' Tutup ',
							handler: function()
							{
								winANSAT.hide();
							}
						}, '-', 
						'Info: Gunakan tombol Ctrl (control) di keyboard kemudian tahan dan klik item yg mau ditambahhkan.'
					]
				}
			],
			listeners:{
				beforerender:function(){
					storeANSAT.load();
				},
				itemclick: function(dv, record, item, index, e) {
					//alert(record.get('kategori'));                                       
				}						
			},
		});
*/

/* start gridansat */	

var gridANSAT = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	store: storeDataSatuan,
	disableSelection: false,
	loadMask: true,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
		mode: 'MULTI', 
		multiSelect: true,
		keepExisting: true,
		checkOnly:true
	}),					
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},		
	plugins: Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 1,
		listeners : {
			afteredit : function() {
			}
		}
	}),						
	columns:[
	{ 
		header: 'Kode Material', 
		dataIndex: 'detail_material_kode', 
		width: 70,
	},
	{
		text: 'Nama Material',
		dataIndex: 'detail_material_nama',
		flex: 3,
		sortable: false
	},
	{
		text: 'Spesifikasi',
		dataIndex: 'detail_material_spesifikasi',
		flex: 1,
		sortable: false
	},
	{
		text: 'Satuan',
		dataIndex: 'detail_material_satuan',
		flex: 1,
		sortable: false
	},
	{
		text: "Kategori",
		dataIndex: 'kategori',
		flex: 1,
		sortable: false
	},						
	// {
	// 	text: 'Koefisien',
	// 	dataIndex: 'koefisien',
	// 	flex: 1,
	// 	sortable: false,
	// 	// editor: {
	// 	// 	xtype: 'numberfield',
	// 	// 	allowBlank: false,
	// 	// 	decimalPrecision: 4
	// 	// },							
	// },						
	],
	bbar: Ext.create('Ext.PagingToolbar', {
		store: storeDataSatuan,
		displayInfo: true,
		displayMsg: 'Displaying data {0} - {1} of {2}',
		emptyMsg: "No data to display",
	}),			
	dockedItems: [
	{
		layout : 'hbox',
		layoutConfig : {
			type :'hbox',
			align : 'stretch',
		},
		defaults :{
			frame : true,
			flex : 1
		},
		dock: 'top',
		xtype: 'toolbar',
		items: [							
		{
			text: 'Pilih Analisa',
			handler: function(){
				winPilihAnalisa.setTitle('Pilih Analisa');
				winPilihAnalisa.on('show', function(win) {
					storeAnalisaPekerjaan2.load();
				});						
				winPilihAnalisa.doLayout();
				winPilihAnalisa.show();
			}
		},							
		'->',					
		{
			fieldLabel: 'Kategori',
			xtype: 'combo',
			scope: this,
			id: 'cmb_kategori_id',
			name: 'kategori',
			emptyText: 'Pilih Kategori',
			labelWidth: 50,
			flex: 2,
			valueField: 'subbidang_kode',
			displayField: 'kd_bidang',
			typeAhead: true,
			queryMode: 'remote',
			store: { 
				fields: ['subbidang_kode', 'kd_bidang'], 
				pageSize: 100, 
				proxy: { 
					type: 'ajax', 
					url: '<?=base_url();?>rencana/get_subbidang', 
					reader: { 
						root: 'data',
						type: 'json' 
					} 
				} 
			},
			listeners: {
				select: function(combo, record, index) {
					storeDataSatuan.load({
						params:{'subbidang_kode':combo.getValue()},
						scope: this,
						callback: function(records, operation, success)
						{
							if (success) {
							} else {
							}
						}
					});											
				}
			},
		}, 									
		{
			xtype: 'textfield',
			flex: 3,
			name: 'detail_material_nama',
			id: 'detail_material_nama_id',
			emptyText: 'masukan nama / kode material ...',
			enableKeyEvents: true,
			listeners: {
				keypress : function(textfield,eventObject){
					if (eventObject.getCharCode() == Ext.EventObject.ENTER) {
						storeDataSatuan.load({
							params:{
								'query':Ext.getCmp('detail_material_nama_id').getValue(),
								'subbidang_kode':Ext.getCmp('cmb_kategori_id').getValue()												
							},
							scope: this,
							callback: function(records, operation, success)
							{
								if (success) {
									/* console.log("Filter: " + Ext.getCmp('detail_material_nama_id').getValue()); */
								} else {
									/* console.log('Error filter'); */
								}
							}
						});																																
					}
				}
			}									
		},												
		{
			text: 'Filter',
			handler: function()
			{
				storeDataSatuan.load({
					params:{
						'query':Ext.getCmp('detail_material_nama_id').getValue(),
						'subbidang_kode':Ext.getCmp('cmb_kategori_id').getValue()												
					},
					scope: this,
					callback: function(records, operation, success)
					{
						if (success) {
							/* console.log("Filter: " + Ext.getCmp('detail_material_nama_id').getValue()); */
						} else {
							/* console.log('Error filter'); */
						}
					}
				});																				
			}
		}, 
		]
	},
	{
		xtype: 'toolbar',
		dock: 'bottom',
		items: [
		{
			text: 'Simpan semua pilihan',
			flex: 1,							
			handler: function(){
				var records = gridANSAT.getView().getSelectionModel().getSelection(),
				names = [],
				koef = [],
				dmid = [];
								//var editedRecords = gridANSAT.getStore().getUpdatedRecords();
								Ext.Array.each(records, function(rec){
									names.push(rec.get('detail_material_kode'));
									dmid.push(rec.get('detail_material_id'));
									koef.push(rec.data.koefisien);
								});
								if(names != '')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/daftar_analisa/tambah_ansat',
										method: 'POST',
										params: {												
											'kode_material' : names.join(','),
											'id_detail_material' : dmid.join(','),
											'koefisien' : koef.join(','),
											'id_tender' : <?=$idtender;?>
										},								
										success: function(response) {
											var text = response.responseText;
											Ext.Msg.alert( "Tambah ANSAT", text, function(){
												storeDataSatuan.load();
												storeASAT.load();
											});											
										},
										failure: function(response) {
											Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem or duplicate entries!');
										}
									});			   																													
								} else 
								{
									Ext.example.msg('Error', 'Silahkan pilih material');
								}
							}
						},'-',
						{
							text: ' Tutup ',
							flex: 1,
							handler: function()
							{
								winANSAT.hide();
							}
						},
						]
					}
					],
					listeners:{
						beforerender:function(){
							storeDataSatuan.load();
						},
						itemclick: function(dv, record, item, index, e) {
					//alert(record.get('kategori'));                                       
				}						
			},
		});
/* end gridansat edited */

var winANSAT = Ext.widget('window', {
	closeAction: 'hide',
	closable: false,
	width: '80%',
	height: '80%',
	layout: 'fit',
	resizable: true,
	modal: true,
	items: gridANSAT,
	listeners:{
		hide:function() {
			storeASAT.load();
			storeAnalisaPekerjaan.load();
		}
	}
});

/* end grid analisa satuan */

function showMenu(grid, index, event, rec) {
	var item_tree_id = rec.get('rat_item_tree');
	var menu = new Ext.menu.Menu({
		items: [
		{
			text: 'Tambah sub kategori',
			handler: function() {
				alert(item_tree_id);
			}
		}, 
		{
			text: 'Edit',
			handler: function() {
				alert(rec.get('rat_item_tree'));
			}
		},
		{
			text: 'Hapus',
			handler: function() {
				alert(rec.get('rat_item_tree'));
			}
		}					
		]
	}); 
	menu.showAt(event.xy);
}		

/* end IN-Direct Cost */

var accRATBK = Ext.create('Ext.Panel', {
	title: 'Uraian Biaya Konstruksi',
			//collapsible: true,
			region:'west',
			//margins:'5 0 5 5',
			//split:true,
			width: '50%',
			layout:'fit',
			items: [gridTreeBK]
		});

var DataRATBK = Ext.create('Ext.panel.Panel', {
	title: 'Analisa Satuan Pekerjaan / ASAT',
	width: '100%',
	layout: 'fit',
			items: [gridAnalisaSatuan] // grid_analisa gridASAT
		});

var winBKAdd;
function showWinBK() {
	if (!winBKAdd) {
		winBKAdd = Ext.widget('window', {
			title: 'Tambah Item Biaya Konstruksi :: Proyek -> <?=$data_tender['nama_proyek'];?>',
			closeAction: 'hide',					
			width: '90%',
			height: '85%',
			layout: 'border',
			resizable: true,
					//maximized: true,
					maximizable: true,
					modal: true,
					items: [
					{
						region:'west',
							//margins:'5 5 5 0',
							layout: 'fit',
							width: '55%',
							split: true,
							items: gridTreeBK //accRATBK
						},					
						{
							region:'center',
							//margins:'5 5 5 0',
							layout: 'fit',
							width: '45%',
							items: DataRATBK
						}					
						],
						listeners:{
							hide :function(){							
								sdummyDC.load();
							}
						}
					});
	}
	winBKAdd.on('show', function(win) {
	});			
	winBKAdd.doLayout();
	winBKAdd.show();
}						
/* END BIAYA KONSTRUKSI */

		/*
		Ext.define('svarDC', {
			extend: 'Ext.data.Model',
			fields: [
			   {name: 'item', type: 'string', defaultValue: 'Direct Cost'},
			   {name: 'uraian', type: 'string',defaultValue: 'Biaya Konstruksi (BK)'},
			   {name: 'diajukan', type: 'int', convert: null, defaultValue: undefined},
			   {name: 'persen_bobot', type: 'float', convert: null, defaultValue: undefined},
			]
		});

		var dummyDC = [
			['1','Biaya Konstruksi','',  '']
		];
		
		var sdummyDC = Ext.create('Ext.data.ArrayStore', {
			model: 'svarDC',
			data: dummyDC
		});
*/

Ext.define('summVCS', {
	extend: 'Ext.data.Model',
	fields: ['item', 'uraian', 'diajukan', 'persen_bobot']
});

var sdummyDC = Ext.create('Ext.data.Store', {
	model: 'summVCS',
	proxy: {
		type: 'ajax',
		url: '<?php echo base_url();?>rencana/get_persen_bk/<?php echo $idtender;?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	},
	autoLoad: true,
});		


var summDC = Ext.create('Ext.grid.Panel', {
	id: 'sumgdc',
	frame: false,
	width: '100%',
	height: '100%',
	store: sdummyDC,
	disableSelection: false,
	loadMask: true,
	viewConfig: {
		id: 'sigv',
		trackOver: true,
		stripeRows: true,
	},		
	columns:[
	{
		text: "Uraian",
		dataIndex: 'uraian',
		flex: 1,
		sortable: true,
	},
	{
		text: "Diajukan",
		dataIndex: 'diajukan',
		align: 'right',
		renderer: Ext.util.Format.numberRenderer('00,000'),
		flex: 1,
		sortable: true,
	},				
	{
		text: "% (bobot terhadap total kontrak)",
		align: 'center',
		dataIndex: 'persen_bobot',
		flex: 1,
		sortable: true
	},
	],
	dockedItems: [		
	{
		xtype: 'toolbar',
		items: [
		{ xtype: 'tbfill' },
		{
			iconCls:'icon-edit',
			dock: 'top',
			text: 'Edit Biaya Konstruksi',
			// flex: 1,
			align:'center',
			handler:  showWinBK,
		},
		{ xtype: 'tbfill' }
		]
	},
	],			
	listeners:{
		beforerender:function(){
			sdummyDC.load();
			storeIDC.load();
		}
	}			
});

		/*
		Ext.define('svarIDC', {
			extend: 'Ext.data.Model',
			fields: [
			   {name: 'item', type: 'string', defaultValue: 'Direct Cost'},
			   {name: 'uraian', type: 'string',defaultValue: 'Biaya Konstruksi (BK)'},
			   {name: 'diajukan', type: 'int', convert: null, defaultValue: undefined},
			   {name: 'persen_bobot', type: 'float', convert: null, defaultValue: undefined},
			]
		});

		var dummyIDC = [
			['2','Provisi Jaminan', '',  ''],
			['3','Bunga Bank','',  ''],
			['4','ASTEK', '',  ''],
			['5','C.A.R','',  ''],
			['6','BIAYA UMUM','',  '']
		];
			
		var sdummyIDC = Ext.create('Ext.data.ArrayStore', {
			model: 'svarIDC',
			data: dummyIDC
		});		
*/

Ext.define('summVCS', {
	extend: 'Ext.data.Model',
	fields: ['item', 'uraian', 'diajukan', 'persen_bobot']
});

var sdummyIDC = Ext.create('Ext.data.Store', {
	model: 'summVCS',
	proxy: {
		type: 'ajax',
		url: '<?php echo base_url();?>rencana/get_persen_idc/<?php echo $idtender;?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	},
	groupField: 'item',			
	autoLoad: true,
});		

var summIDC = Ext.create('Ext.grid.Panel', {
	id: 'sumgidc',
	width: '100%',
	height: '100%',
	store: sdummyIDC,
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
				/*
				{

					xtype: 'rownumberer',
					width: 25,
					sortable: false
				},
				*/				
				{
					text: "Uraian",
					dataIndex: 'uraian',
					flex: 1,
					sortable: true,
					summaryType: 'count',
					summaryRenderer: function(value, summaryData, dataIndex) {
						return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
					}
				},
				{
					text: "Diajukan",
					dataIndex: 'diajukan',
					align: 'right',
					renderer: Ext.util.Format.numberRenderer('00,000'),					
					flex: 1,
					sortable: true,
				},				
				{
					text: "% (bobot terhadap total kontrak)",
					dataIndex: 'persen_bobot',
					align: 'center',
					flex: 1,
					sortable: true
				},
				],
				dockedItems: [
				{
					xtype: 'toolbar',
					items: [
					{ xtype: 'tbfill' },
					{
						dock: 'top',
						iconCls:'icon-edit',
						// flex: 1,
						text: 'Edit in-Direct Cost',
						handler: showwinIDCfrm,
					},
					{ xtype: 'tbfill' }
					]
				},
				],						
				listeners:{
					beforerender:function(){
					//storeIDC.load();
				},
				hide:function(){
					sdummyIDC.load();
				}
			}			
		});

Ext.define('svarRAP', {
	extend: 'Ext.data.Model',
	fields: [
	{name: 'item', type: 'string', defaultValue: 'Direct Cost'},
	{name: 'uraian', type: 'string',defaultValue: 'Biaya Konstruksi (BK)'},
	{name: 'diajukan', type: 'int', convert: null, defaultValue: undefined},
	{name: 'persen_bobot', type: 'float', convert: null, defaultValue: undefined},
	]
});

Ext.define('summVCS', {
	extend: 'Ext.data.Model',
	fields: ['id_rat_varcost', 'id_proyek_rat', 'vitem','persentase','diajukan']
});

var storeSVC = Ext.create('Ext.data.Store', {
	model: 'summVCS',
	proxy: {
		type: 'ajax',
		url: '<?php echo base_url();?>rencana/get_variable_cost/<?php echo $idtender;?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	},
	autoLoad: true,
	params: {id: <?php echo $idtender;?>}
});		

var summVC = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	store: storeSVC,
	disableSelection: false,
	loadMask: true,
	viewConfig: {
		trackOver: true,
		stripeRows: true,
	},		
	plugins: Ext.create('Ext.grid.plugin.RowEditing', {
		clicksToMoveEditor: 1,
		autoCancel: false,
		listeners: {
			'edit': function () {
				var editedRecords = summVC.getStore().getUpdatedRecords();
				Ext.Ajax.request({
					url: '<?=base_url();?>rencana/update_varcost',
					method: 'POST',
					params: {								
						'id_proyek_rat' : editedRecords[0].data.id_proyek_rat,
						'id_rat_varcost' : editedRecords[0].data.id_rat_varcost,
						'persentase' : editedRecords[0].data.persentase
					},								
					success: function(response) {
						var text = response.responseText;
						Ext.example.msg( "Status", text, function(){
							storeSVC.load();
						});											
					},
					failure: function() {
						Ext.example.msg( "Error", "Data GAGAL diupdate!");											
					}
				});			   																										
			}
		}				
	}),			
	columns:[
	{

		xtype: 'rownumberer',
		width: 35,
		sortable: false
	},
	{
		text: "Uraian",
		dataIndex: 'vitem',
		flex: 1,
		sortable: false,
	},
	{
		text: "Diajukan",
		dataIndex: 'diajukan',
		renderer: Ext.util.Format.numberRenderer('00,000'),
		align: 'right',
		flex: 1,
		sortable: false,
	},				
	{
		text: "% (bobot terhadap total kontrak)",
		dataIndex: 'persentase',
		flex: 1,
		align: 'center',
		sortable: false,
		editor: {
			xtype: 'numberfield',
		}					
	},
	],
	dockedItems: [
	{
		xtype: 'toolbar',
		dock: 'top',
		items: ['Double klik untuk mengedit item']
	}],						
	listeners:{
		beforerender:function(){
			storeSVC.load();
		}
	}			
});

var accRAT = Ext.create('Ext.Panel', {
	title: 'Entry Data RAT :: Proyek -> <?=$data_tender['nama_proyek'];?>',
	region:'west',
	margins:'5 0 5 5',
	split:true,
	width: '50%',
	layout:'accordion',
	items: [
	{
		region: 'south',
		title: 'Direct Cost',
		layout: {
			type: 'border',
			padding: 5
		},
		items: [
		{
			region: 'north',
			height: '100%',
			split: true,
			layout: 'fit',
			items: summDC,
		}, 
						/*
						{
							region: 'west',
							width: '100%',
							layout: 'fit',
							split: true,
							items: gridDC
						}
						*/
						]
					},	
					{
						region: 'south',
						title: 'In-Direct Cost',
						layout: {
							type: 'border',
							padding: 5
						},
						items: [
						{
							region: 'north',
							height: '100%',
							layout: 'fit',
							split: true,
							items: summIDC,
						}, 						
						/*
						{
							region: 'west',
							width: '100%',
							height: '100%',
							layout: 'fit',
							split: true,
							items: gridBiayaUmum
						}
						*/
						]
					},	
					{
						region: 'south',
						title: 'Variable Cost',
						layout: {
							type: 'border',
							padding: 5
						},
						items: [
						{
							region: 'north',
							height: '100%',
							layout: 'fit',
							split: true,
							items: summVC,
						}, 						
						]
					},	
				//avarcost				
				],
				dockedItems: [{
					xtype: 'toolbar',
					items: [
					{ 
						xtype: 'button', 
						iconCls: 'icon-back',
						text: 'Kembali ke Menu Tender',
						handler: function()
						{
							window.location = '<?=base_url();?>rencana/entry_rat';
						}
					},				
					{
						text: 'Export proyek ke CSV',
						iconCls: 'icon-export-csv',
						handler: function(){
							// window.location = '<?=base_url();?>rencana/export_project_csv/<?php echo $idtender;?>';
							window.location = '<?=base_url();?>rencana/export_tender';
						}
					},  
					/*
					'-',
					{
						text: 'Print Pdf',
						iconCls: 'icon-print',
						handler: function(){
						}
					},
					*/					
					]
				}],			
			});

		/*
		var accRAT = Ext.create('Ext.Panel', {
			title: 'Entry Detail Data RAT',
			collapsible: true,
			region:'west',
			margins:'5 0 5 5',
			split:true,
			width: '100%',
			layout:'accordion',
			items: [gridDC, gridBiayaUmum, avarcost]
		});		
*/

var DataRAT = Ext.create('Ext.panel.Panel', {
	title: 'DATA RAT :: Proyek -> <?=$data_tender['nama_proyek'];?>',
	width: '100%',
	layout: 'fit',
	defaults: {
		bodyStyle: 'padding:20px'
	},
	dockedItems: [{
		xtype: 'toolbar',
		dock: 'top',
		items: [
					// { 
					// 	xtype: 'button',
					// 	iconCls: 'icon-print',
					// 	text: 'Print Pdf' 
					// },
					{ 
						xtype: 'button', 
						iconCls: 'icon-table',
						text: 'Save to Excel',
						handler:function(){
							Ext.MessageBox.confirm('Export','Apakah anda akan meng-Export Data RAT Ini?',function(res){
								if (res=='yes') {
									window.location='<?=base_url();?>rencana/printed_rat/<?=$idtender;?>';
								}
							});
						}
					},
					{ 
						xtype: 'button', 
						iconCls: 'icon-reload',
						text: 'Refresh',
						handler: function(){
							document.getElementById('frm_summary').contentWindow.location.reload();						
						}
					}					
					]
				}],			
				items: [{
					bodyStyle:'background:#f1f1f1',
					html:'<iframe id="frm_summary" src="<?=base_url();?>rencana/summary_rat/<?=$idtender;?>" width="100%" height="100%" border="0"></iframe>'				
				}],
			});

		/*
		var viewport = Ext.create('Ext.Viewport', {
			layout:'border',
			items:[ accRAT ]
		});				
*/

		/*
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
*/

/* rat_rata */
Ext.define('mdlRATA', {
	extend: 'Ext.data.Model',
	fields: [
	'kode_rap','kd_material', 'detail_material_nama',
	'detail_material_satuan','total_volume','harga','subtotal','simpro_tbl_subbidang'
	],
});

var storeRATA = Ext.create('Ext.data.Store', {
	model: 'mdlRATA',
	proxy: {
		type: 'ajax',
		url: '<?=base_url();?>rencana/daftar_analisa/get_rata/<?=$idtender;?>',
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
	groupField: 'simpro_tbl_subbidang',
	sorters: [{
		property: 'kode_tree',
		direction: 'DESC'
	}]			
});			

var gridRATA = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	store: storeRATA,
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

		xtype: 'rownumberer',
		width: 45,
		sortable: false
	},
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
				/*
				{
					text: 'Jumlah',
					dataIndex: 'subtotal',
					renderer: Ext.util.Format.numberRenderer('00,000'),
					flex:1,
					align: 'right',
					sortable: false
				},
				*/
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
					store: storeRATA,
					displayInfo: true,
					displayMsg: 'Displaying data {0} - {1} of {2}',
					emptyMsg: "No data to display",
				}),		
				dockedItems: [
				{
					xtype: 'toolbar',
					dock: 'top',
					items: [
					{
						text: 'Print',
						iconCls: 'icon-print',
						handler: function()
						{
								//Ext.ux.grid.Printer.print(gridRATA);							
								window.location = '<?php echo base_url(); ?>rencana/cetak_rata/<?=$idtender;?>';
							}
						},
						{
							text: 'Export to Excel',
							iconCls: 'icon-table',
							handler: function()
							{
								window.location = '<?php echo base_url(); ?>rencana/rata_to_xls/<?=$idtender;?>';
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
							text: '<b>Persentase terhadap Pagu: (%)</b>',
							id: 'id-persen-rata',
							handler: update_persen_rata
							//handler: 
						}
						]
					},				
					{
						xtype: 'toolbar',
						dock: 'bottom',
						items: [
						'->',
						{
							text: 'Total Keseluruhan RAT(A) : ',
							id: 'id-total-rata',
							iconCls: 'icon-total',
							handler: update_total_rata
						},
						]
					},				
					],			
					listeners:{
						beforerender:function(){
							storeRATA.load();
							update_total_rata();
						},
						itemclick: function(dv, record, item, index, e) {
						}						
					},
				});

function update_total_rata()
{
	Ext.Ajax.request({
		url: '<?=base_url();?>rencana/get_total_rata/<?=$idtender;?>',
		method: 'POST',											
		params: {												
			'id_tender' : <?=$idtender;?>
		},								
		success: function(response) {			
			Ext.getCmp('id-total-rata').setText('<b>Total Keseluruhan RAT(A) : '+ response.responseText +'</b>');
			update_persen_rata();
		},
		failure: function(response) {
					// Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
				}
			});			   			
}

function copy_analisa_proyek_lain(){

	Ext.define('mdlAnalisaPekerjaan2', {
		extend: 'Ext.data.Model',
		fields: [
		'id_data_analisa', 'kode_analisa', 'id_kat_analisa', 'kategori', 'nama_kategori',
		'nama_item', 'id_satuan', 'satuan', 'id_tender', 'harga_satuan', 'jenis_analisa', 'kd', 'xn'
		]
	});

	var storeAnalisaPekerjaan2 = Ext.create('Ext.data.Store', {
		model: 'mdlAnalisaPekerjaan2',
		pageSize: 100,  
		remoteFilter: true,
		autoLoad: false,
		extraParams:{
			id:'',
			param:'copy_proyek_lain',
			search:''
		},
		proxy: {
			type: 'ajax',
			url: '<?php echo base_url() ?>rencana/daftar_analisa/get_data_analisa_pekerjaan_copy',
			reader: {
				type: 'json',
				root: 'data'
			}
		},
		groupField: 'kategori',
		sorters: [{
			property: 'kode_analisa',
			direction: 'ASC'
		}],						
	});	

						var gridAnalisaPekerjaan = Ext.create('Ext.grid.Panel', {
						width: '100%',
						height: '100%',
						frame: false,
						store: storeAnalisaPekerjaan2,
						viewConfig: {
							trackOver: true,
							stripeRows: true,
						},
						selModel: Ext.create('Ext.selection.CheckboxModel', {
							mode: 'MULTI', 
							multiSelect: true,
							keepExisting: true,
						}),									
						columns: [
						// {
						// 	xtype: 'rownumberer',
						// 	width: 35,
						// 	sortable: false
						// },
					{
						text: "Jenis Analisa", 
						flex: 2, 
						sortable: false, 
						dataIndex: 'jenis_analisa'
					},
					{
						text: "Kode Asat Apek", 
						flex: 2, 
						sortable: false, 
						dataIndex: 'kd'
					},
						{
							text: "Kode", 
							flex: 2, 
							sortable: false, 
							dataIndex: 'kode_analisa',
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
						},                                                          
						renderer: function(v, meta, record) {
							var harga_sat = record.get('harga_satuan');

							if(harga_sat == 0) {                                                                      
								return '<font color=red><b>'+v+'</b></font>';
							} else {
								return v;
							}
						}										
					},
					{
						text: "Uraian", 
						flex: 3, 
						sortable: false, 
						dataIndex: 'nama_item'
					},
					{
						text: "Satuan", 
						flex: 1, 
						sortable: false, 
						dataIndex: 'satuan'
					},
					{
						text: "Harga Satuan", 
						flex: 2, 
						sortable: false, 
						dataIndex: 'harga_satuan',
						align: 'right',
						renderer: Ext.util.Format.numberRenderer('00,000'),					
					}
				],		
				columnLines: true,
				dockedItems:[{
					xtype:'toolbar',
					items:[{
							text:'Kembali',
							iconCls:'icon-back',
							handler:function(){
								winDaftarAnalisa.hide();
							}
						},'-',
						{
							xtype:'radio',
							name:'copy_proyek',
							itemId:'copy_proyek_lain',
							boxLabel:'Pilih Proyek Lain',
							inputValue:'copy_proyek_lain',
							checked:true,
							listeners:{
								change:function(val){
									if (val.checked == true) {
										gridAnalisaPekerjaan.getDockedItems()[0].getComponent('proyek_pilih').setDisabled(false);
										gridAnalisaPekerjaan.getDockedItems()[0].getComponent('pilih_divisi').setDisabled(false);
										gridAnalisaPekerjaan.getDockedItems()[0].getComponent('search').setValue('');
										storeAnalisaPekerjaan2.proxy.extraParams = {'param':'copy_proyek_lain','search':''};
										storeAnalisaPekerjaan2.loadData([],false);
									}
								}
							}
						},'-',{
							xtype:'radio',
							name:'copy_proyek',
							itemId:'copy_master_analisa',
							boxLabel:'Pilih Master Analisa',
							inputValue:'copy_master_analisa',
							listeners:{
								change:function(val){
									if (val.checked == true) {
										gridAnalisaPekerjaan.getDockedItems()[0].getComponent('proyek_pilih').setDisabled(true);
										gridAnalisaPekerjaan.getDockedItems()[0].getComponent('pilih_divisi').setDisabled(true);
										gridAnalisaPekerjaan.getDockedItems()[0].getComponent('proyek_pilih').setValue('');
										gridAnalisaPekerjaan.getDockedItems()[0].getComponent('pilih_divisi').setValue('');
										gridAnalisaPekerjaan.getDockedItems()[0].getComponent('search').setValue('');
										storeAnalisaPekerjaan2.proxy.extraParams = {'param':'copy_master_analisa','search':''};
										storeAnalisaPekerjaan2.load();
									}
								}
							}
						},'-',{
							xtype:'textfield',
							itemId:'search',
							listeners:{
								change:function(val){
									cek = gridAnalisaPekerjaan.getDockedItems()[0].getComponent('copy_proyek_lain').checked;
									id = gridAnalisaPekerjaan.getDockedItems()[0].getComponent('proyek_pilih').getValue();

									if (cek == true) {
										param = 'copy_proyek_lain';
									} else {
										param = 'copy_master_analisa';
									}
									v = val.getValue();
									storeAnalisaPekerjaan2.proxy.extraParams = {'param':param,'search':v,'id':id};
									storeAnalisaPekerjaan2.load();
								}
							}
						},
						'->','-',
						{
							text:'Pilih : '
						},
						{
							xtype: 'combo',
							itemId:'pilih_divisi',
							emptyText:'Pilih Divisi',
							store: { 
								fields: ['divisi_id','divisi_name'], 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_divisi', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							triggerAction : 'all',					
							anchor: '100%',
							displayField: 'divisi_name',
							valueField: 'divisi_id',
							listeners: {
								'select': function(combo, row, index) {
									gridAnalisaPekerjaan.getDockedItems()[0].getComponent('proyek_pilih').setValue('');
									storeAnalisaPekerjaan2.loadData([],false);
									store_proyek_pilih = gridAnalisaPekerjaan.getDockedItems()[0].getComponent('proyek_pilih').getStore();
									store_proyek_pilih.proxy.setExtraParam('divisi_id', combo.getValue());
									store_proyek_pilih.load();
								}
							},
						},
						{
							xtype: 'combo',
							itemId:'proyek_pilih',
							emptyText:'Pilih Proyek',
							width:200,
							store: { 
								fields: ['id_proyek_rat','nama_proyek'], 
								autoLoad:false,								
								queryMode: 'local',
								proxy: { 
									extraParams:{
										'divisi_id':''
									},
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_proyek_pilih', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							triggerAction : 'all',					
							anchor: '100%',
							displayField: 'nama_proyek',
							valueField: 'id_proyek_rat',
							listeners: {
								'select': function(combo, row, index) {
									storeAnalisaPekerjaan2.proxy.setExtraParam('id',combo.getValue());
									storeAnalisaPekerjaan2.loadPage(1);
								}
							},
						}
					]
				},{
					xtype:'toolbar',
					dock:'bottom',
					items:[
					'->',
					{
						text:'Simpan Semua Pilihan',
						handler:function(){
							var records = gridAnalisaPekerjaan.getView().getSelectionModel().getSelection();
							id_data = [];
							jml = 0;

							Ext.Array.each(records, function(rec){
								id_data.push(rec.get('id_data_analisa'));
								jml = jml + rec.get('xn');
							});

							Ext.MessageBox.confirm("Info","Apakah anda akan mengcopy item ini?",function(res){
							if (res == 'yes') {
								if (gridAnalisaPekerjaan.getDockedItems()[0].getComponent('copy_proyek_lain').checked == true) {
									id_tender = gridAnalisaPekerjaan.getDockedItems()[0].getComponent('proyek_pilih').getValue();
									param = 'copy_proyek_lain';
								} else {
									id_tender = 0;
									param = 'copy_master_analisa';
								}

								Ext.Ajax.request({
									url:'<?=base_url()?>rencana/copy_analisa_proyek_lain',
									method:'POST',
									params:{
										'id_data':id_data.join(','),
										'id_tender':id_tender,
										'jml':jml,
										'param':param
									},
									success:function(obj){
										Ext.MessageBox.alert('Info',obj.responseText,function(){
											winDaftarAnalisa.hide();
											storeAnalisaPekerjaan.load();
											storeASAT.load();
										});
									},
									failure:function(){

									}
								});												
							}
							});							
						}
					}
					]
				}],				
				bbar: Ext.create('Ext.PagingToolbar', {
					store: storeAnalisaPekerjaan2,
					displayInfo: true,
					displayMsg: 'Displaying data {0} - {1} of {2}',
					emptyMsg: "No data to display",
				}),		
			});						

var winDaftarAnalisa = Ext.widget('window', {
	title: 'Data Analisa Pekerjaan :: Proyek -> <?=$data_tender['nama_proyek'];?>',
	closeAction: 'hide',
	width: 900,
	height: 400,
	layout: 'fit',
	resizable: true,
	modal: true,
	items: [gridAnalisaPekerjaan]
}).show();

}

function update_persen_rata()
{
	Ext.Ajax.request({
		url: '<?=base_url();?>rencana/get_persen_rata/<?=$idtender;?>',
		method: 'POST',											
		params: {												
			'id_tender' : <?=$idtender;?>
		},								
		success: function(response) {			
			Ext.getCmp('id-persen-rata').setText('<b>Persentase terhadap pagu : '+ response.responseText +'%</b>');
		},
		failure: function(response) {
			Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
		}
	});			   			
}

function copy_item_proyek_lain(storeTree){
	Ext.define('mdlCopyRAT', {
		extend: 'Ext.data.Model',
		fields: [		
		{name: 'rat_item_tree',     type: 'string'},
		{name: 'id_proyek_rat',     type: 'string'},
		{name: 'id_satuan', 		type: 'string'},
		{name: 'kode_tree',     	type: 'string'},
		//{name: 'kode_analisa',     	type: 'string'},
		{name: 'tree_item',     	type: 'string'},
		{name: 'tree_satuan',     	type: 'string'},
		{name: 'volume',     	type: 'float'},
		//{name: 'harga',     	type: 'float'},
		//{name: 'subtotal',     	type: 'float'},				
		{name: 'tree_parent_id',     	type: 'string'}
		]
	});

	var storeCopyRAT = Ext.create('Ext.data.Store', {
		model: 'mdlCopyRAT',
		proxy: {
			type: 'ajax',
			url: '<?=base_url();?>rencana/copy_rat_proyek_lain/',
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
	});			

	var gridCopyItemRAT = Ext.create('Ext.grid.Panel', {
		width: '100%',
		height: '100%',
		store: storeCopyRAT,
		disableSelection: false,
		loadMask: true,
		// selModel: Ext.create('Ext.selection.CheckboxModel', {
		// 	mode: 'MULTI', 
		// 	multiSelect: true,
		// 	keepExisting: true,
		// }),					
		viewConfig: {
			trackOver: true,
			stripeRows: true,
		},		
		columns:[
		{
			text: 'Kode',
			width: 50,
			sortable: false,
			dataIndex: 'kode_tree',
		},
		{
			text: 'Uraian',
			flex: 3,
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
			text: 'Volume',
			dataIndex: 'volume',
			flex: 1,
			align: 'center',
			sortable: false,
		},
		],
		bbar: Ext.create('Ext.PagingToolbar', {
			store: storeCopyRAT,
			displayInfo: true,
			displayMsg: 'Displaying data {0} - {1} of {2}',
			emptyMsg: "No data to display",
		}),			
		dockedItems: [
		{
			dock: 'top',
			xtype: 'toolbar',
			itemId:'bar',
			items: [							
			{
				fieldLabel: 'Pilih Proyek',
				itemId:'tender',
				xtype: 'combo',
				scope: this,
				name: 'pilih_id_proyek_rat',
				emptyText: 'Pilih Proyek',
				labelWidth: 100,
				flex: 2,
				valueField: 'id_proyek_rat',
				displayField: 'nama_proyek',
				typeAhead: true,
				queryMode: 'remote',
				store: { 
					fields: ['id_proyek_rat','nama_proyek'], 
					pageSize: 100, 
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>rencana/get_data_proyek_copy',
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				listeners: {
					select: function(combo, record, index) {
						storeCopyRAT.load({
							params:{'id_proyek_rat':combo.getValue()},
							scope: this,
							callback: function(records, operation, success)
							{
								if (success) {
								} else {
								}
							}
						});											
					}
				},
			}, 									
			]
		},
		{
			xtype: 'toolbar',
			dock: 'bottom',
			items: [
			{
				text: 'Import Data',
				flex: 1,
				handler: function(){
					bar = gridCopyItemRAT.getComponent('bar');
					id_tender = bar.getComponent('tender').getValue();
					if(id_tender != '')
					{
						Ext.Ajax.request({
							url: '<?=base_url();?>rencana/import_proyek_lain',
							method: 'POST',											
							params: {							
								'id_tender' : id_tender
							},								
							success: function(response) {

								Ext.MessageBox.alert('OK', 'Data telah di-copy silahkan pilih item kemudian paste pada item tersebut.', function(){										
									storeTree.load();
									winCopyRAT.hide();								
								});
							},
							failure: function(response) {
								Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
							}
						});			   	
					} else 
					{
						Ext.example.msg('Error', 'Silahkan pilih item yang mau di-copy!');
					}
				}
			},'-',
			{
				text: ' Tutup ',
				flex: 1,
				handler: function()
				{
					winCopyRAT.hide();
				}
			},
			]
		}
		],
		listeners:{
			beforerender:function(){
				storeCopyRAT.load();
			},
			itemclick: function(dv, record, item, index, e) {
			}						
		},
	});

	var winCopyRAT = Ext.widget('window', {
		closeAction: 'hide',
		closable: false,
		width: '80%',
		height: '80%',
		layout: 'fit',
		resizable: true,
		modal: true,
		items: [gridCopyItemRAT],
	}).show();		
}

/* end rat_rata */

var tabRAT = Ext.widget('tabpanel', {
	renderTo: Ext.getBody(),
	activeTab: 0,
	width: '100%',
	height: '100%',
	deferredRender: false, 
	items: [
	{			
		title: 'RAT',
		layout: 'fit',
		items: [
		{
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
		}
		],
		listeners: {
			activate: function(tab){
				setTimeout(function() {
					update_total_bk();
				}, 1);
			}
		},																				
	},
	{
		title: 'RAT(A)',
		items: gridRATA,
		layout: 'fit',
		listeners: {
			activate: function(tab){
				setTimeout(function() {
					storeRATA.load();
					update_total_rata();
				}, 1);
			}
		},																
	}, 			
	]			
});		

});
</script>
<head>
	<body>
		<div id="grid-tender" class="x-hide-display"></div>
	</body>
	</html>