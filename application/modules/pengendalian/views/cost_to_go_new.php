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
<script type="text/javascript">
	// Ext.Loader.setConfig({enabled: true});
	// Ext.Loader.setPath('Ext.ux', '/path-to-ext-ux');

	// Ext.define('App.view.SomeGrid', {
	//     extend: 'Ext.grid.Panel',
	//     uses: [
	//         'Ext.ux.exporter.Exporter'
	//     ],

	//     initComponent: function() {
	//         this.store = "SomeStore";

	//         this.dockedItems = [{
	//             xtype: 'toolbar',
	//             dock: 'top',
	//             items: [{
	//                 xtype: 'exporterbutton'
	//             }];
	//         this.columns = [/* YOUR COLUMNS */];
	//         this.callParent(arguments);
	//     }
	// });

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
		
		var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';		
		
		/* pilih analisa costogo */
		Ext.define('mdlAnalisaPekerjaan2', {
			extend: 'Ext.data.Model',
			fields: [
				'id_data_analisa', 'kode_analisa', 'id_kat_analisa', 'kategori', 'nama_kategori',
				'nama_item', 'id_satuan', 'satuan', 'id_proyek', 'harga_satuan'
			 ]
		});

		var storeAnalisaPekerjaan2 = Ext.create('Ext.data.Store', {
			model: 'mdlAnalisaPekerjaan2',
			pageSize: 100,  
			remoteFilter: true,
			autoLoad: false,
			proxy: {
				 type: 'ajax',
				 url: '<?php echo base_url() ?>pengendalian/ctg_analisa/get_data_analisa_pekerjaan/<?=$id_proyek;?>/<?=$tgl_rab;?>',
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
											url: '<?=base_url();?>pengendalian/ctg_analisa/tambah_apek_costogo/<?=$tgl_rab;?>',
											method: 'POST',											
											params: {												
												'kode_analisa' : names.join(','),
												'id_data_analisa' : dmid.join(','),
												'koefisien' : koef.join(','),
												'id_proyek' : <?=$id_proyek;?>
											},								
											success: function(response) {												
												var text = response.responseText;
												Ext.Msg.alert( "Tambah ANSAT", text, function(){
													winDataAnalisa.hide();
													storeAnalisaPekerjaan2.load();
													storeTree.load();
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
		/* end pilih analisa costogo */
		
		/* grid ansat */
			Ext.define('mdlASAT', {
				extend: 'Ext.data.Model',
				fields: [
					'id_analisa_asat', 'id_data_analisa', 'kode_material', 'id_detail_material', 'koefisien', 
					'harga', 'kode_analisa', 'id_proyek', 'detail_material_nama', 'detail_material_satuan',
					'detail_material_kode', 'asat_kat', 'subtotal', 'parent_name'
				 ]
			});

			var storeASAT = Ext.create('Ext.data.Store', {
				model: 'mdlASAT',
				pageSize: 300,  
				remoteFilter: true,
				autoLoad: false,
				proxy: {
					 type: 'ajax',
					 url: '<?php echo base_url() ?>pengendalian/ctg_analisa/get_asat/<?=$id_proyek;?>/<?=$tgl_rab;?>',
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
				plugins: Ext.create('Ext.grid.plugin.CellEditing', {
					clicksToMoveEditor: 1,				
					clicksToEdit: 1,
					listeners : {
						edit : function() {							
							var editedRecords = gridAnalisaSatuan.getStore().getUpdatedRecords();
							//var editedRecords = gridAnalisaSatuan.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>pengendalian/ctg_analisa/edit_koefisien_satuan/<?=$tgl_rab;?>',
								method: 'POST',
								params: {
									'id_proyek' : <?=$id_proyek;?>,
									'id_analisa_asat' : editedRecords[0].data.id_analisa_asat,
									'kode_analisa' : editedRecords[0].data.kode_analisa,
									'detail_material_kode' : editedRecords[0].data.detail_material_kode,									
									'koefisien' : editedRecords[0].data.koefisien,
								},								
								success: function(response) {
									var text = response.responseText;
									Ext.example.msg("Status", response.responseText, function()
									{
										storeASAT.load();
										storeAnalisaPekerjaan2.load();
										storeAnalisaPekerjaan.load();
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
					{text: "Koefisien", flex: 1, sortable: false, dataIndex: 'koefisien',editor: 'numberfield'},
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
											url: '<?=base_url();?>pengendalian/ctg_analisa/delete_asat/<?=$tgl_rab;?>',
											method: 'POST',
											params: {
												'id_analisa_asat':id,
												'id_proyek' : <?=$id_proyek;?>,
												'kode_analisa': rec.get('kode_analisa')
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
								flex: 4,
								fieldLabel: 'Cari',
								labelWidth: 50,
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
								text: 'Data Analisa',
								flex: 1,
								handler: function(){
									window.location = '<?=base_url();?>pengendalian/edit_analisa_costogo/<?=$id_proyek;?>/<?=$tgl_rab;?>';
								}
							},							
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
		/* end grid asat*/
		
		var frmAddIDCBK = Ext.widget({
			xtype: 'form',
			layout: 'form',
			url: '<?=base_url();?>pengendalian/ctg_analisa/tambah_costogo_tree_item/<?=$id_proyek;?>/<?=$tgl_rab;?>',
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
					name: 'id_proyek',
					xtype: 'hiddenfield',
					value: <?=$id_proyek;?>,
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
							url: '<?=base_url();?>pengendalian/ctg_analisa/get_detailmaterial_kode',
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
							url: '<?=base_url();?>pengendalian/ctg_analisa/get_satuan', 
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
			
		var winIDCAddBK = Ext.widget('window', {
			title: 'Tambah Uraian costogo :: Proyek -> ',
			closeAction: 'hide',
			width: '40%',
			height: '35%',
			layout: 'fit',
			resizable: true,
			modal: true,
			items: frmAddIDCBK
		});
		
	/* copy costogo */
		Ext.define('mdlCopyRAT', {
			extend: 'Ext.data.Model',
			fields: [		
				{name: 'costogo_item_tree',     type: 'string'},
				{name: 'id_proyek',     type: 'string'},
				{name: 'id_satuan', 		type: 'string'},
				{name: 'kode_tree',     	type: 'string'},
				{name: 'tree_item',     	type: 'string'},
				{name: 'tree_satuan',     	type: 'string'},
				{name: 'volume',     	type: 'float'},
				{name: 'tree_parent_id',     	type: 'string'}
			]
		});

		var storeCopyRAT = Ext.create('Ext.data.Store', {
			 model: 'mdlCopyRAT',
			 proxy: {
				type: 'ajax',
				url: '<?=base_url();?>pengendalian/ctg_analisa/copy_rat_proyek_lain/<?=$tgl_rab;?>',
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
								name: 'pilih_id_proyek',
								emptyText: 'Pilih Proyek',
								labelWidth: 100,
								flex: 2,
								valueField: 'id_proyek',
								displayField: 'nama_proyek',
								typeAhead: true,
								queryMode: 'remote',
								store: { 
									fields: ['id_proyek','nama_proyek'], 
									pageSize: 100, 
									proxy: { 
										type: 'ajax', 
										url: '<?=base_url();?>pengendalian/ctg_analisa/get_data_proyek/<?=$tgl_rab;?>',
										reader: { 
											root: 'data',
											type: 'json' 
										} 
									} 
								},
								listeners: {
									select: function(combo, record, index) {
										storeCopyRAT.load({
											params:{'id_proyek':combo.getValue()},
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
									itemid.push(rec.get('costogo_item_tree'));
									uraian.push(rec.get('tree_item'));
									volume.push(rec.get('volume'));
									satuan.push(rec.get('tree_satuan'));
								});
								if(treeid != '')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>pengendalian/ctg_analisa/copy_tree/<?=$tgl_rab;?>',
										method: 'POST',											
										params: {												
											'kode_tree' : treeid.join(','),
											'tree_item_id' : itemid.join(','),
											'tree_item' : uraian.join(','),
											'volume' : volume.join(','),
											'satuan' : satuan.join(','),
											'id_proyek' : <?=$id_proyek;?>
										},								
										success: function(response) {
											Ext.MessageBox.alert('OK', 'Data telah di-copy silahkan pilih item kemudian paste pada item tersebut.', function(){
												winCopycostogo.hide();											
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
								winCopycostogo.hide();
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

		var winCopycostogo = Ext.widget('window', {
			closeAction: 'hide',
			closable: false,
			width: '80%',
			height: '80%',
			layout: 'fit',
			resizable: true,
			modal: true,
			items: [gridCopyItemRAT],
		});		
	/* copy costogo */
	
	/* tambah sub item */
		var frmAddBKItem = Ext.widget({
			xtype: 'form',
			layout: 'form',
			url: '<?=base_url();?>pengendalian/ctg_analisa/tambah_costogo_tree_item/<?=$id_proyek;?>/<?=$tgl_rab;?>',
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
					name: 'id_proyek',
					xtype: 'hiddenfield',
					value: <?=$id_proyek;?>,
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
	/* end tambah sub item */
				
		
		Ext.define('treegriditem', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'costogo_item_tree',     type: 'string'},
				{name: 'id_proyek',     type: 'string'},
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
			extraParams: {
	            param : ''
	        },
			expanded: true,			
			proxy: {
				type: 'ajax',
				url: '<?=base_url()?>pengendalian/ctg_analisa/get_task_tree_item/<?=$id_proyek;?>/<?=$tgl_rab;?>'
			},
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
			//title: 'costogo',
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
					'edit': function () {
							var editedRecords = gridTreeBK.getStore().getUpdatedRecords();
							Ext.Ajax.request({
								url: '<?=base_url();?>pengendalian/ctg_analisa/update_tree_item/<?=$tgl_rab;?>',
								method: 'POST',
								params: {								
									'costogo_item_tree' : editedRecords[0].data.costogo_item_tree,
									'tree_parent_id' : editedRecords[0].data.tree_parent_id,
									'id_proyek' : editedRecords[0].data.id_proyek,
									'kode_tree' : editedRecords[0].data.kode_tree,
									'satuan_id' : editedRecords[0].data.tree_satuan,
									'tree_item' : editedRecords[0].data.tree_item,
									'volume' : editedRecords[0].data.volume
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
					width: 100,
					sortable: false,
					dataIndex: 'kode_tree',
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
					flex: 3,
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
								url: '<?=base_url();?>pengendalian/ctg_analisa/get_satuan/<?=$tgl_rab;?>', 
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
					flex: 2,
					renderer: Ext.util.Format.numberRenderer('00,000'),
					align: 'right',
					sortable: false
				}, 								
				{
					text: 'Jumlah',
					flex: 2,
					dataIndex: 'subtotal',
					align: 'right',
					renderer: Ext.util.Format.numberRenderer('00,000'),
					sortable: false
				}, 								
				{
					text: '',
					flex: 1,
					menuDisabled: true,
					xtype: 'actioncolumn',
					align: 'center',
                    items: [{
                    icon: '<?=base_url();?>/assets/images/add.png',
					handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
						var parentid = record.get('costogo_item_tree');
						var parentname = record.get('kode_tree')+ ' ' +record.get('tree_item');
						Ext.Ajax.request({
							url: '<?php echo base_url();?>pengendalian/ctg_analisa/set_parent_tree_id/<?=$tgl_rab;?>',
							params: {
								parent_id: parentid,
								proyek_id: record.get('id_proyek'),
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
					},                                                          
                    getClass: function(v, meta, record) {  
                    	var volume = record.get('volume');
                    	var harga = record.get('harga');
                    	var kode = record.get('kode_analisa');       
                    	var satuan = record.get('tree_satuan');

                    	if(volume > 1 && harga > 0 && kode !='' && (record.get('ishaschild') != 1) && record.get('kode_tree').length != 1 && satuan != 'Ls') {                                                                      
                        	return 'x-hide-display';
                        }
                        else if(volume > 1 && harga == 0 || kode !='' && (record.get('ishaschild') != 1) && record.get('kode_tree').length != 1 && satuan != 'Ls'){
                        	return 'x-hide-display';
                        }
                        else if(volume > 1 && harga == 0 && kode =='' && (record.get('ishaschild') != 1) && record.get('kode_tree').length != 1 && satuan != 'Ls'){
                        	return 'x-hide-display';
                        }
                        else if(volume > 1 && harga > 0 && kode !='' && (record.get('ishaschild') != 1) && record.get('kode_tree').length != 1 && satuan != 'Ls'){
                        	return 'x-hide-display';
                        }
                        else if(volume == 1 && harga == 0 && kode !='' && (record.get('ishaschild') != 1) && record.get('kode_tree').length != 1 && satuan != 'Ls'){
                        	return 'x-hide-display';
                        }
                        else if(parseInt(record.get('kode_tree').length,10) == 1 && record.get('kode_tree').length != 1 && satuan != 'Ls'){
                        	return 'x-hide-display';
                        }
                        else if(volume == 1 && harga == 0 && kode =='' && (record.get('ishaschild') != 1) && record.get('kode_tree').length != 1 && satuan != 'Ls'){
                        	return 'x-hide-display';
                        }
                    }
				}]
				},				
				{
					text: '',
					flex: 1,
					menuDisabled: true,
					xtype: 'actioncolumn',
					align: 'center',
                    items: [{
                    icon: '<?=base_url();?>/assets/images/application_go.png',
                    tooltip: 'Edit Analisa Pekerjaan',
					handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
						var itemid = record.get('costogo_item_tree');
						var itemname = record.get('kode_tree')+ '. ' +record.get('tree_item');
						if((parseInt(record.get('kode_tree').length,10) != 1) && (record.get('ishaschild') != 1))
						{							
								Ext.Ajax.request({
									url: '<?=base_url();?>pengendalian/ctg_analisa/set_costogo_item_tree/<?=$tgl_rab;?>',
									method: 'POST',
									params: {
										'id_proyek' : <?=$id_proyek;?>,
										'costogo_item_tree' : record.get('costogo_item_tree'),
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
						} else {
							Ext.MessageBox.show({
								title: 'Error',
								msg: 'Tidak bisa menambah Analisa Satuan Pekerjaan',
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
				},
				{
					text: '',
					flex: 1,
					menuDisabled: true,
					xtype: 'actioncolumn',
					tooltip: 'Delete Item',
					align: 'center',
					icon: '<?=base_url();?>/assets/images/delete.png',
					handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
						var itemid = record.get('costogo_item_tree');
						if(record.get('ishaschild') != 1)
						{
							Ext.MessageBox.confirm('Hapus item menu > '+record.get('kode_tree') +'. '+record.get('tree_item'), 'Apakah anda akan menghapus Analisa Satuan untuk item ini?', function(btn){
								if(btn == 'yes')
								{
									Ext.Ajax.request({
										url: '<?php echo base_url(); ?>pengendalian/ctg_analisa/del_tree_item/<?=$tgl_rab;?>',
										params: {
											tree_item_id: record.get('costogo_item_tree')
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
						var itemid = record.get('costogo_item_tree');
						if(record.get('ishaschild') != 1)
						{
							Ext.MessageBox.confirm('Hapus Analisa Satuan -> '+record.get('kode_tree') +'. '+record.get('tree_item'), 'Apakah anda akan menghapus Analisa Satuan untuk item ini?', function(btn){
								if(btn == 'yes')
								{
									Ext.Ajax.request({
										url: '<?php echo base_url(); ?>pengendalian/ctg_analisa/del_analisa_satuan_item/<?=$tgl_rab;?>',
										params: {
											'tree_item_id': record.get('costogo_item_tree')
										},
										success: function(form, action){
											Ext.MessageBox.alert('Sukses', 'Data Analisa satuan berhasil dihapus!', function(){
												storeTree.load();
												storeASAT.load();											
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
					xtype: 'toolbar',
					itemid:'top_bar',
					items: [
					{
						text: 'Kembali',
						iconCls: 'icon-back',
						handler: function()
						{
							window.location = '<?=base_url();?>pengendalian/pilihcosttogo';
						}
					},'-',
					{
						text: 'Tambah Item',
						iconCls: 'icon-add',
						handler: function(){
							Ext.Ajax.request({
								url: '<?php echo base_url();?>pengendalian/ctg_analisa/set_parent_tree_id/<?=$tgl_rab;?>',
								params: {
									proyek_id: <?=$id_proyek;?>,
									parent_id: '0',
									parent_kode_tree: ''
								},
								success: function(response){
									winIDCAddBK.show();
									winIDCAddBK.doLayout();
								}
							});													
						}
					},
					{
						text: 'Data Analisa',
						//flex: 1,
						iconCls: 'icon-add',
						handler: function(){
							window.location = '<?=base_url();?>pengendalian/edit_analisa_costogo/<?=$kunci;?>/<?=$tgl_rab;?>';
						}
					},{
						text: 'Export Cost-To-Go',
						iconCls: 'icon-print',
							handler: function(){
							Ext.MessageBox.confirm('Export', 'Apakah anda akan mengeksport uraian ini?',function(resbtn){
								if(resbtn == 'yes')
								{
									window.location = '<?=base_url();?>pengendalian/ctg_analisa/excel/costogo/<?=$tgl_rab?>';
								}
							});	
						}
					},'-',
					{
						text: 'Export Analisa Cost-To-Go',
						iconCls: 'icon-print',
							handler: function(){
							Ext.MessageBox.confirm('Export', 'Apakah anda akan mengeksport analisa uraian ini?',function(resbtn){
								if(resbtn == 'yes')
								{
									window.location = '<?=base_url();?>pengendalian/ctg_analisa/print_data_analisa/analisa_costogo/<?=$tgl_rab?>';
								}
							});	
						}
					},'-',
					{
						fieldLabel:'Search <b>(Press ENTER) <b>',
						labelWidth:140,
						width:244,
						xtype:'textfield',
						itemid:'search',
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
					]
				},			
				{
					xtype: 'toolbar',
					dock: 'bottom',
					itemId:'dock_up',
					items: [
						{
							text: 'Refresh',
							iconCls: 'icon-reload',
							handler: function()
							{
								storeTree.load();
							}
						},					
						{
							text: 'Copy',
							iconCls: 'icon-copy',
							handler: function(){          
								var records = gridTreeBK.getView().getSelectionModel().getSelection(),
									itemid = [],kdanalisa = [],uraian = [],volume = [],satuan = [],treeid = [];
								Ext.Array.each(records, function(rec){
									treeid.push(rec.get('kode_tree'));
									itemid.push(rec.get('costogo_item_tree'));
									kdanalisa.push(rec.get('kode_analisa'));
									uraian.push(rec.get('tree_item'));
									volume.push(rec.get('volume'));
									satuan.push(rec.get('tree_satuan'));
								});
								if(treeid != '')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>pengendalian/ctg_analisa/copy_tree/<?=$tgl_rab;?>',
										method: 'POST',											
										params: {												
											'kode_tree' : treeid.join(','),
											'tree_item_id' : itemid.join(','),
											'tree_item' : uraian.join(','),
											'kode_analisa' : kdanalisa.join(','),
											'volume' : volume.join(','),
											'satuan' : satuan.join(','),
											'id_proyek' : <?=$id_proyek;?>
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
							text: 'Paste',
							iconCls: 'icon-paste',
							handler: function(){
								var records = gridTreeBK.getView().getSelectionModel().getSelection(),
									itemid = [],
									treeid = [];
								Ext.Array.each(records, function(rec){
									treeid.push(rec.get('kode_tree'));
									itemid.push(rec.get('costogo_item_tree'));
								});
								if(treeid != '')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>pengendalian/ctg_analisa/paste_tree/<?=$tgl_rab;?>',
										method: 'POST',											
										params: {												
											'kode_tree' : treeid.join(','),
											'tree_item_id' : itemid.join(','),
											'id_proyek' : <?=$id_proyek;?>
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
						// {
						// 	text: 'Copy dari Proyek Lain',
						// 	iconCls: 'icon-copy',
						// 	handler: function(){
						// 		winCopycostogo.setTitle('Copy item RAT dari Proyek Lain');
						// 		winCopycostogo.on('show', function(win) {
						// 			storeCopyRAT.load();
						// 		});						
						// 		winCopycostogo.doLayout();
						// 		winCopycostogo.show();
						// 	}							
						// },'-',
						{
							text: 'Delete',
							iconCls: 'icon-del',
							handler: function(){
								var records = gridTreeBK.getView().getSelectionModel().getSelection(),
									itemid = [],ischild = [],treeid = [];
								Ext.Array.each(records, function(rec){
									treeid.push(rec.get('kode_tree'));
									ischild.push(rec.get('ishaschild'));
									itemid.push(rec.get('costogo_item_tree'));
								});
								if(treeid != '')
								{
									Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus kode item ('+treeid.join(', ')+') ini?',function(resbtn){
										if(resbtn == 'yes')
										{
											Ext.Ajax.request({
												url: '<?=base_url();?>pengendalian/ctg_analisa/delete_tree_item/<?=$tgl_rab;?>',
												method: 'POST',											
												params: {												
													'kode_tree' : treeid.join(','),
													'tree_item_id' : itemid.join(','),
													'ishaschild': ischild.join(','),
													'id_proyek' : <?=$id_proyek;?>
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
						},'->',
						{
							text: 'Total RAP: ',
							id: 'id-total-rap',
							// handler: update_total_rap,
						}					
					]
				}				
			],
			listeners:{
				beforerender:function(){

					// update_total_rap();

					// dock_up = gridTreeBK.getComponent('dock_up');
					// data = dock_up.getComponent('totals');
					

					// storeTree.load({
					// 	callback:function(a,b,c,d){
					// 		totals = 0
					// 		for (var i = 0; i < a.length; i++) {
					// 			totals += a[i].data.subtotal
					// 		}
							
							// data.setText('Total Cost To Go : '+ 500);
					// 	}
					// }
					// );


				}
			}						
		});

		function update_total_costogo()
		{
			Ext.Ajax.request({
				url: '<?=base_url();?>rbk/ctg_analisa/get_total_costogo',
				method: 'POST',											
				params: {												
					'id_proyek' : <?=$id_proyek;?>
				},								
				success: function(response) {			
					Ext.getCmp('id-total-costogo').setText('<b>Total Cost To Go : '+ response.responseText +'</b>');
				},
				failure: function(response) {
					Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
				}
			});			   			
		}

		var accRATBK = Ext.create('Ext.Panel', {
			title: 'Uraian costogo',
			region:'west',
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
		
		Ext.define('mdlRATA', {
			extend: 'Ext.data.Model',
			fields: [
				'kd_material',
				'detail_material_nama',
				'detail_material_satuan',
				'koefisien',
				'harga',
				'kode_rap',
				{name: 'subtotal', type: 'float'},
				'simpro_tbl_subbidang'
			],
		});

		var storecostogoA = Ext.create('Ext.data.Store', {
			 model: 'mdlRATA',
			 proxy: {
				type: 'ajax',
				url: '<?=base_url();?>pengendalian/ctg_analisa/get_rata/<?=$id_proyek;?>/<?=$tgl_rab;?>',
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

		var gridcostogoA = Ext.create('Ext.grid.Panel', {
			width: '100%',
			height: '100%',
			store: storecostogoA,
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
					dataIndex: 'koefisien',

					align: 'right',
					flex:1,
					renderer: Ext.util.Format.numberRenderer('00,000.00'),					
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
					summaryType: 'sum',
					summaryRenderer: Ext.util.Format.numberRenderer('00,000')
				},							
			],
			bbar: Ext.create('Ext.PagingToolbar', {
				store: storecostogoA,
				displayInfo: true,
				displayMsg: 'Displaying data {0} - {1} of {2}',
				emptyMsg: "No data to display",
			}),		
			dockedItems: [
				{
					xtype: 'toolbar',
					dock: 'top',
					items: [
						// {
						// 	text: 'Print',
						// 	iconCls: 'icon-print',
						// 	//handler: 
						// },
						{
							text: 'Export to Excel',
							iconCls: 'icon-table',
							handler: function()
							{
								window.location = '<?=base_url();?>pengendalian/ctg_analisa/costogoa_to_xls/<?=$tgl_rab;?>';
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
							text: 'Persentase terhadap kontrak: (%)',
							//handler: 
						},
					]
				},
				{
					xtype: 'toolbar',
					dock: 'bottom',
					items: [
						'->',
						{
							text: 'Total keseluruhan: ',
							//handler: 
						},
					]
				}
			],			
			listeners:{
				beforerender:function(){
					storecostogoA.load();
				},
				itemclick: function(dv, record, item, index, e) {
				}						
			},
		});
		/* end rat_rata */
			
		
		// var panelcostogo = Ext.create('Ext.panel.Panel', {
		// 	renderTo: Ext.getBody(),
		// 	title: 'Cost-To-Go :: Proyek <?php echo $data_proyek['proyek']; ?>',
		// 	width: '100%',
		// 	height: '100%',
		// 	layout: 'fit',
		// 	items:[gridTreeBK],
		// });
		
		var tabRAT = Ext.widget('tabpanel', {
			title: 'costogo & costogo(A) :: Proyek <?php echo $data_proyek['proyek']; ?>',
			renderTo: Ext.getBody(),
			activeTab: 0,
			width: '100%',
			height: '100%',
			deferredRender: false, 
			items: [
			{			
				title: 'costogo',
				layout: 'fit',
				items: gridTreeBK, //[panelcostogo],
				listeners: {
					activate: function(tab){
						setTimeout(function() {
						}, 1);
					}
				},																				
			},
			{
				title: 'costogo(A)',
				items: gridcostogoA,
				layout: 'fit',
				listeners: {
					activate: function(tab){
						setTimeout(function() {
							storecostogoA.load();						
						}, 1);
					}
				},																
			}, 			
			]			
		});	
		
	});
</script>
</head>
<body>
<div id="grid-proyek" class="x-hide-display"></div>
</body>
</html>