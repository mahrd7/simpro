<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/bootstrap.js"></script> -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
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
    Ext.require([
		'*',
		'Ext.ux.form.SearchField'
	]);	
	
    Ext.onReady(function() {
        Ext.QuickTips.init();	
		  
        Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
		
		/* RAT */
				
		/* DAFTAR ANALISA PEKERJAAN */
		
		Ext.define('mdlAnalisaPekerjaan', {
			extend: 'Ext.data.Model',
			fields: [
				'id_data_analisa', 'kode_analisa', 'id_kat_analisa', 'kategori', 'nama_kategori',
				'nama_item', 'id_satuan', 'satuan', 'id_tender', 'harga_satuan'
			 ]
		});
			
		Ext.define('mdlASAT', {
			extend: 'Ext.data.Model',
			fields: [
				'id_analisa_asat', 'id_data_analisa', 'kode_material', 'id_detail_material', 'koefisien', 
				'harga', 'kode_analisa', 'id_tender', 'detail_material_nama', 'detail_material_satuan',
				'detail_material_kode', 'asat_kat', 'subtotal', 'parent_name'
			 ]
		});

		Ext.define('mdlAPEK', {
			extend: 'Ext.data.Model',
			fields: [
				'id_analisa_apek', 'id_data_analisa', 'kode_analisa', 'koefisien', 
				'harga', 'id_tender', 'parent_kode_analisa', 'parent_id_analisa',
				'apek_kat', 'nama_item', 'id_satuan', 'satuan_nama','subtotal'
			 ]
		});
				
			var storeASAT = Ext.create('Ext.data.Store', {
				model: 'mdlASAT',
				pageSize: 300,  
				remoteFilter: true,
				autoLoad: false,
				proxy: {
					 type: 'ajax',
					 url: '<?php echo base_url() ?>rencana/daftar_analisa/get_asat/<?=$id_tender?>',
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

			var storeAPEK = Ext.create('Ext.data.Store', {
				model: 'mdlAPEK',
				pageSize: 200,  
				remoteFilter: true,
				autoLoad: false,
				proxy: {
					 type: 'ajax',
					 url: '<?php echo base_url() ?>rencana/daftar_analisa/get_apek/<?=$id_tender;?>',
					 reader: {
						 type: 'json',
						 root: 'data'
					 }
				},		
				groupField: 'apek_kat',
				sorters: [{
					property: 'kode_analisa',
					direction: 'ASC'
				}],
			});		
			
			var storeAnalisaPekerjaan = Ext.create('Ext.data.Store', {
				model: 'mdlAnalisaPekerjaan',
				pageSize: 100,  
				remoteFilter: true,
				autoLoad: false,
				proxy: {
					 type: 'ajax',
					 url: '<?php echo base_url() ?>rencana/daftar_analisa/get_daftar_analisa/<?=$id_tender;?>',
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
						
			var APcellEditing = Ext.create('Ext.grid.plugin.RowEditing', {
					//clicksToEdit: 1,
					clicksToMoveEditor: 1,
					autoCancel: false,
					listeners : {
						edit : function() {						
							var editedRecords = gridAnalisaPekerjaan.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/tambah_daftar_analisa',
								method: 'POST',
								params: {
									'id_tender' : <?=$id_tender;?>,
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
				/*
				selModel: {
					selType: 'cellmodel'
				},
				*/				
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
				plugins: [APcellEditing],										
				columns: [
					{
						xtype: 'rownumberer',
						width: 35,
						sortable: false
					},
					{
						text: "Kode", 
						flex: 1, 
						id: 'id_kode_analisa',
						sortable: false, 
						dataIndex: 'kode_analisa',						
						editor: {
							xtype: 'textfield',
						},
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
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
									/*
									var kda = gridAnalisaPekerjaan.getSelectionModel().getSelection();
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/daftar_analisa/tambah_daftar_analisa',
										method: 'POST',
										params: {
											'id_tender' : <?=$id_tender;?>,
											'id_kat_analisa': 10,
											'nama_item': kda[0].data.nama_item,
											'id_satuan' : combo.getValue(),
											'kode_analisa' : kda[0].data.kode_analisa
										},								
										success: function(response) {
											storeAnalisaPekerjaan.load();
										},
										failure: function(response) {
										}
									});
									*/
								}
							},																					
						},
					},
					{
						text: "Kategori", 
						flex: 1, 
						sortable: false, 
						dataIndex: 'nama_kategori',
						/*
						editor: {
							id: 'cmb_id_kategori',
							xtype: 'combo',
							store: { 
								fields: ['id_kat_analisa','kategori'], 
								pageSize: 100, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/daftar_analisa/get_kategori_bahan', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							triggerAction : 'all',
							anchor: '100%',
							displayField: 'kategori',
							valueField: 'id_kat_analisa',
							listeners: {
								'select': function(combo, row, index) {
								}
							},																					
						},
						*/						
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
							rec = storeAnalisaPekerjaan.getAt(rowIndex);
							if(rec.get('id_kat_analisa') != 10)
							{
								winANSAT.setTitle('Tambah Analisa Satuan :: '+rec.get('kategori')+' :: '+rec.get('kode_analisa')+' - '+rec.get('nama_item'));
								winANSAT.on('show', function(win) {
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
											Ext.Msg.alert("ERROR", "Error due to connection problem!");
										}
									});			   
								});						
								winANSAT.doLayout();
								winANSAT.show();
							} else
							{
								winDataAnalisa.setTitle('Tambah Analisa Pekerjaan :: '+rec.get('kategori')+' :: '+rec.get('kode_analisa')+' - '+rec.get('nama_item'));
								winDataAnalisa.on('show', function(win) {
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/daftar_analisa/set_analisa_itemid',
										method: 'POST',
										params: {
											'id_tender' : rec.get('id_tender'),
											'id_data_analisa' : rec.get('id_data_analisa'),
											'kode_analisa' : rec.get('kode_analisa'),										
										},
										success: function() {
											storeAnalisaPekerjaan2.load();			
										},
										failure: function() {
											Ext.Msg.alert("ERROR", "Error due to connection problem!");
										}
									});			   
								});				
								winDataAnalisa.doLayout();
								winDataAnalisa.show();
							}
						}
					},				
					{text: "",xtype: 'actioncolumn', flex:1, align: 'center', sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
						handler: function(grid, rowIndex, colIndex){        
							var rec = storeAnalisaPekerjaan.getAt(rowIndex);
							var id = rec.get('kode_analisa');
							Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
									if(resbtn == 'yes')
									{
										Ext.Ajax.request({
											url: '<?=base_url();?>rencana/daftar_analisa/delete_analisa_pekerjaan/'+id,
											method: 'POST',
											params: {
												'kode_analisa':id,
											},								
											success: function(response) {
												Ext.Msg.alert( "Status", response.responseText, function(){
													storeAnalisaPekerjaan.load();
												});											
											},
											failure: function(response) {
												Ext.Msg.alert( "Error", response.responseText, function(){
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
								text:'Tambah Data',
								tooltip:'Tambah Data',
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
								text:'Clear Search',
								tooltip:'Clear Search',
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
											Ext.Msg.alert("ERROR", "Error due to connection problem!");
										}
									});			   								
								}
							},'-',
							{
								fieldLabel: 'Kategori',		
								labelWidth: 50,								
								xtype: 'combo',
								store: { 
									fields: ['id_kat_analisa','kategori'], 
									pageSize: 100, 
									proxy: { 
										type: 'ajax', 
										url: '<?=base_url();?>rencana/daftar_analisa/get_kategori_bahan', 
										reader: { 
											root: 'data',
											type: 'json' 
										} 
									} 
								},
								triggerAction : 'all',					
								anchor: '100%',
								displayField: 'kategori',
								valueField: 'id_kat_analisa',
								listeners: {
									'select': function(combo, row, index) {
										storeAnalisaPekerjaan.load({
											params:{'id_kat_analisa':combo.getValue()},
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
								flex: 4,
								tooltip:'masukan kode analisa / uraian',
								emptyText: 'kode analisa / uraian',
								xtype: 'searchfield',
								name: 'cari_analisa',
								store: storeAnalisaPekerjaan,
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
								text:'<< Kembali ke daftar RAT',
								tooltip:'Kembali ke daftar RAT',
								handler: function(){          
									window.location = '<?=base_url().'rencana/entry_rat';?>';
								}
							}, '-',
							{
								text:'Copy Analisa dari Proyek Lain',
								tooltip:'Copy Analisa dari analisa yang lain',
								handler: function(){          
									Ext.Msg.alert("Status","Kopi Analisa dari analisa lain yang pernah dibuat");
								}
							}, '-',							
							{
								text:'Import Analisa (CSV)',
								tooltip:'Import Analisa dari data CSV',
								handler: function(){          
									Ext.Msg.alert("Status","Import Analisa dari data CSV");
								}
							},'-',		
							{
								text:'Reset All Data',
								tooltip:'Reset Data',
								handler: function(){          
									Ext.Msg.alert("Status","Reset Data");
								}
							},							
						]							
					}
				],
			});
			
		/* tambah ansat */			
		Ext.define('ModelBKAnsat', {
			extend: 'Ext.data.Model',
			fields: [
				'detail_material_id', 'detail_material_kode', 'detail_material_nama', 'detail_material_spesifikasi', 
				'subbidang_kode', 'detail_material_satuan', 'kategori', 'koefisien'
			],
			idProperty: 'ModelBKANSATid'
		});

		var storeANSAT = Ext.create('Ext.data.Store', {
			 model: 'ModelBKAnsat',
			 proxy: {
				type: 'ajax',
				url: '<?=base_url();?>rencana/get_data_ansat/<?=$id_tender;?>',
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
		});			
			
				var sm = Ext.create('Ext.selection.CheckboxModel', {
					mode: 'MULTI', 
					multiSelect: true,
					keepExisting: true,
				});										

				var gridANSAT = Ext.create('Ext.grid.Panel', {
					width: '100%',
					height: '100%',
					store: storeANSAT,
					disableSelection: false,
					loadMask: true,
					selModel: sm,
					//singleExpand: true,
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
								/* start hbox */
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
												storeANSAT.load({
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
													storeANSAT.load({
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
											storeANSAT.load({
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
								/* end hbox */
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
										if(names != '')
										{
											Ext.Ajax.request({
												url: '<?=base_url();?>rencana/daftar_analisa/tambah_ansat',
												method: 'POST',
												params: {												
													'kode_material' : names.join(','),
													'id_detail_material' : dmid.join(','),
													'koefisien' : koef.join(','),
													'id_tender' : <?=$id_tender;?>
												},								
												success: function(response) {
													var text = response.responseText;
													Ext.Msg.alert( "Tambah ANSAT", text, function(){
														storeANSAT.load();
														storeASAT.load();
													});											
												},
												failure: function(response) {
													Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
												}
											});			   																													
										} else 
										{
											Ext.Msg.alert('Error', 'Silahkan pilih material');
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
			
				var winANSAT = Ext.widget('window', {
					title: 'Analisa Tahapan Pekerjaan',
					closeAction: 'hide',
					closable: false,					
					width: '70%',
					height: '90%',
					layout: 'fit',
					resizable: true,
					modal: true,					
					items: gridANSAT,
				});
				
			
		Ext.define('mdlAnalisaPekerjaan2', {
			extend: 'Ext.data.Model',
			fields: [
				'id_data_analisa', 'kode_analisa', 'id_kat_analisa', 'kategori', 'nama_kategori',
				'nama_item', 'id_satuan', 'satuan', 'id_tender', 'koefisien', 'harga_satuan'
			 ]
		});

		var storeAnalisaPekerjaan2 = Ext.create('Ext.data.Store', {
			model: 'mdlAnalisaPekerjaan2',
			pageSize: 100,  
			remoteFilter: true,
			autoLoad: false,
			proxy: {
				 type: 'ajax',
				 url: '<?php echo base_url() ?>rencana/daftar_analisa/get_daftar_analisa_koef/<?=$id_tender;?>',
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
			
			/* window pilih Analisa Pekerjaan */
			var gridBKAnalisa = Ext.create('Ext.grid.Panel', {
				width: '100%',
				height: '100%',
				frame: false,
				store: storeAnalisaPekerjaan2,
				selModel: Ext.create('Ext.selection.CheckboxModel', {
					mode: 'MULTI', 
					multiSelect: true,
					keepExisting: true,
				}),
				plugins: Ext.create('Ext.grid.plugin.CellEditing', {
					clicksToEdit: 1,
					listeners : {
						afteredit : function() {
						}
					}
				}),
				columns: [
					{
						text: "Kode", 
						flex: 1, 
						sortable: false, 
						dataIndex: 'kode_analisa',
					},
					{
						text: "Uraian", 
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
						text: "Koefisien", 
						flex: 1, 
						sortable: false, 
						dataIndex: 'koefisien',
						editor: {
							xtype: 'numberfield',
							allowBlank: false,
						},													
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
							{
								text:'Clear Search',
								tooltip:'Clear Search',
								handler: function(){          
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/daftar_analisa/clear_search_data_analisa',
										method: 'POST',
										params: {
											'clearsearch' : 1,
											'page' : 1,
										},
										success: function() {
											storeAnalisaPekerjaan2.load();
										},
										failure: function() {
											Ext.Msg.alert("ERROR", "Error due to connection problem!");
										}
									});			   								
								}
							},'->',
							{
								fieldLabel: 'Kategori',		
								labelWidth: 50,								
								xtype: 'combo',
								store: { 
									fields: ['id_kat_analisa','kategori'], 
									pageSize: 100, 
									proxy: { 
										type: 'ajax', 
										url: '<?=base_url();?>rencana/daftar_analisa/get_kategori_bahan', 
										reader: { 
											root: 'data',
											type: 'json' 
										} 
									} 
								},
								triggerAction : 'all',					
								anchor: '100%',
								displayField: 'kategori',
								valueField: 'id_kat_analisa',
								listeners: {
									'select': function(combo, row, index) {
										storeAnalisaPekerjaan2.load({
											params:{'id_kat_analisa':combo.getValue()},
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
								flex: 1,
								//fieldLabel: 'Search',
								tooltip:'masukan kode analisa / uraian',
								emptyText: 'kode analisa / uraian',
								labelWidth: 40,
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
								tooltip:'Simpan semua pilihan',
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
											url: '<?=base_url();?>rencana/daftar_analisa/tambah_apek',
											method: 'POST',											
											params: {												
												'kode_analisa' : names.join(','),
												'id_data_analisa' : dmid.join(','),
												'koefisien' : koef.join(','),
												'id_tender' : <?=$id_tender;?>
											},								
											success: function(response) {
												var text = response.responseText;
												Ext.Msg.alert( "Tambah ANSAT", text, function(){
													storeANSAT.load();
													storeAPEK.load();
												});											
											},
											failure: function(response) {
												Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
											}
										});			   																													
									} else 
									{
										Ext.Msg.alert('Error', 'Silahkan pilih material');
									}
								}
							}, '-',
							{
								text:'Tutup',
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
								
		/* end tambah ansat */			
															
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
				plugins: Ext.create('Ext.grid.plugin.CellEditing', {
					clicksToEdit: 1,
					listeners : {
						edit : function() {
							var editedRecords = gridASAT.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/edit_koefisien_satuan',
								method: 'POST',
								params: {
									'id_tender' : <?=$id_tender;?>,
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
					{text: "Kode Material", flex: 1, sortable: false, dataIndex: 'detail_material_kode'},
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
											url: '<?=base_url();?>rencana/daftar_analisa/delete_asat/'+id,
											method: 'POST',
											params: {
												'id_analisa_asat':id,
											},
											success: function(response) {
												Ext.Msg.alert("Status", response.responseText, function()
												{
													storeASAT.load();
												});
											},
											failure: function(response) {
												Ext.Msg.alert("Error", response.responseText, function()
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
						items: [
							{
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
							{
								text:'Reset All Data',
								tooltip:'Reset All Data',
								handler: function(){        
									Ext.Msg.alert("Status","Reset All Data");
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
					}
				],
				bbar: Ext.create('Ext.PagingToolbar', {
					store: storeASAT,
					displayInfo: true,
					displayMsg: 'Displaying Data {0} - {1} of {2}',
					emptyMsg: "No data to display"
				})											
			});
			
			var gridAPEK = Ext.create('Ext.grid.Panel', {
				width: '100%',
				height: '100%',
				frame: false,
				store: storeAPEK,
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
				/*
				selModel: Ext.create('Ext.selection.CheckboxModel', {
					mode: 'MULTI', 
					multiSelect: true,
					keepExisting: true,
				}),
				*/
				plugins: Ext.create('Ext.grid.plugin.CellEditing', {
					//clicksToEdit: 1,
					listeners : {
						edit : function() {
							var editedRecords = gridAPEK.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/edit_koefisien_apek',
								method: 'POST',
								params: {
									'id_tender' : <?=$id_tender;?>,
									'id_analisa_apek' : editedRecords[0].data.id_analisa_apek,
									'koefisien' : editedRecords[0].data.koefisien,
								},								
								success: function(response) {
									Ext.Msg.alert("Status", response.responseText, function()
									{
										storeAPEK.load();
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
						text: "Kode", 
						flex: 1, 
						sortable: false, 
						dataIndex: 'kode_analisa',
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
						},						
					},
					{text: "Uraian", flex: 2, sortable: false, dataIndex: 'nama_item'},
					{text: "Satuan", flex: 1, sortable: false, dataIndex: 'satuan_nama'},
					{text: "Volume", flex: 1, sortable: false, align: 'right', dataIndex: 'koefisien',editor:'numberfield'},
					{
						text: "Harga Satuan", 
						flex: 1, 
						sortable: false, 
						dataIndex: 'harga',
						align: 'right',
						groupable: false,
						renderer: Ext.util.Format.numberRenderer('00,000'),
					},
					{
						text: "Sub Total", 
						flex: 1, 
						sortable: false, 
						dataIndex: 'subtotal',
						groupable: false,
						align: 'right',
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
							rec = storeAPEK.getAt(rowIndex);
							var id = rec.get('id_analisa_apek');
							Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
									if(resbtn == 'yes')
									{
										Ext.Ajax.request({
											url: '<?=base_url();?>rencana/daftar_analisa/delete_apek/'+id,
											method: 'POST',
											params: {
												'id_analisa_apek':id,
											},								
											success: function(fn,o) {
												Ext.Msg.alert( "Status", "Item APEK berhasil dihapus.", function(){	
													storeAPEK.load();
												});											
											},
											failure: function() {
												Ext.Msg.alert( "ERROR", "Error due to connection problem.", function(){	
													storeAPEK.load();
												});											
											}
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
						items: [
							{
								text:'Reset All Data',
								tooltip:'Tambah Data',
								handler: function(){          
								}
							}, '->',
							{
								flex: 4,
								tooltip:'masukan kode analisa / uraian',
								emptyText: 'masukan kode analisa / uraian...',
								xtype: 'searchfield',
								store: storeAPEK,
								listeners: {
											keyup: function(e){ 
											}
										}
							}
						]
					}
				],
				bbar: Ext.create('Ext.PagingToolbar', {
					store: storeAPEK,
					displayInfo: true,
					displayMsg: 'Displaying Data {0} - {1} of {2}',
					emptyMsg: "No data to display"
				})											
			});			
				
				
			/* edit harga asat */
			Ext.define('mdlEditHargaASAT', {
				extend: 'Ext.data.Model',
				fields: [
					'kode_material','detail_material_nama','detail_material_satuan','harga'
				 ]
			});

			var storeEditHargaASAT = Ext.create('Ext.data.Store', {
				model: 'mdlEditHargaASAT',
				pageSize: 200,  
				remoteFilter: true,
				autoLoad: false,
				proxy: {
					 type: 'ajax',
					 url: '<?php echo base_url() ?>rencana/daftar_analisa/edit_harga_satuan_asat',
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
				plugins: Ext.create('Ext.grid.plugin.CellEditing', {
					//clicksToEdit: 1,
					listeners : {
						edit : function() {
							var editedRecords = gridEditHargaASAT.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>rencana/daftar_analisa/update_harga_asat',
								method: 'POST',
								params: {
									'id_tender' : <?=$id_tender;?>,
									'kode_material' : editedRecords[0].data.kode_material,
									'harga' : editedRecords[0].data.harga,
									'kode_rap' : editedRecords[0].data.kode_rap,
								},								
								success: function(response) {
									Ext.Msg.alert("Status", response.responseText, function()
									{
										storeEditHargaASAT.load();
										storeASAT.load();
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
					{text: "Harga Satuan", flex: 1, sortable: false, dataIndex: 'harga', editor:'numberfield'},
					{text: "Kode RAP", flex: 1, sortable: false, dataIndex: 'kode_rap', editor:'textfield'},
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
								tooltip:'Close',
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
				height: '90%',
				layout: 'fit',
				resizable: true,
				modal: true,
				items: gridEditHargaASAT,
			});
				
			/* end edit harga asat */
			
			var tabsAnalisaPekerjaan = Ext.widget('tabpanel', {
				//activeTab: 0,
				width: '100%',
				height: '100%',
				defaults :{
					autoScroll: true,
					bodyPadding: 0
				},
				items: [
					{
						title: 'Data Analisa',
						layout: 'fit',
						items: gridAnalisaPekerjaan,
						listeners: {
							activate: function(tab){
								setTimeout(function() {
									storeAnalisaPekerjaan.load();
								}, 1);
							}
						},
					},
				]
			});
			
			var tabsAnalisaASATAPEK = Ext.widget('tabpanel', {
				//activeTab: 0,
				width: '100%',
				height: '100%',
				defaults :{
					autoScroll: true,
					bodyPadding: 0
				},
				items: [
					{
						title: 'ASAT Bahan, Upah dll',
						layout: 'fit',
						items: gridASAT,
						listeners: {
							activate: function(tab){
								setTimeout(function() {
									storeASAT.load();
								}, 1);
							}
						},
					},
					{
						title: 'APEK',
						items: gridAPEK,
						layout: 'fit',
						listeners: {
							activate: function(tab){
								setTimeout(function() {
									storeAPEK.load();
								}, 1);
							}
						},
					},
					/*
					{
						title: 'VOLPEK',
						html: 'Volume Pekerjaan',
						layout: 'fit',
						listeners: {
							activate: function(tab){
								setTimeout(function(){
								}, 1);
							}
						},
					}
					*/					
				]
			});	
		
		var panelDaftarAnalisa = Ext.create('Ext.Panel', {
			title: 'Data Analisa Kebutuhan Proyek :: Proyek -> <?=$data_tender['nama_proyek'];?>',
			layout:'border',
			width: '100%',
			height: '100%',			
			items: [
				{
					region:'west',
					layout: 'fit',
					width: '50%',
					split: true,
					items: tabsAnalisaPekerjaan
				},					
				{
					region:'center',
					layout: 'fit',
					width: '50%',
					items: tabsAnalisaASATAPEK
				}			
			]
		});
		
		var viewport = Ext.create('Ext.Viewport', {
			title: 'Data Analisa Proyek',
			renderTo: Ext.getBody(),
			layout: 'fit',
			items:[panelDaftarAnalisa]
		});						
								
		/* END DAFTAR ANALISA PEKERJAAN */		
});
    </script>
</head>
<body>	
    <!-- <div id="grid-tender" class="x-hide-display"></div> -->
</body>
</html>
