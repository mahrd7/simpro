<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/examples.js"></script>
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

.icon-import {
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
    Ext.require([
		'*',
		'Ext.ux.form.SearchField'		
	]);
	
    Ext.onReady(function() {
        Ext.QuickTips.init();
		
        Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
		
		var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';		
		
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
				 url: '<?php echo base_url() ?>pengendalian/cbd_analisa/get_data_analisa_pekerjaan/<?=$id_proyek;?>/<?=$tgl_rab;?>',
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
				url: '<?=base_url();?>pengendalian/cbd_analisa/get_data_ansat/<?=$id_proyek;?>/<?=$tgl_rab;?>',
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
							tooltip:'Simpan pilihan',
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
											url: '<?=base_url();?>pengendalian/cbd_analisa/tambah_apek/<?=$tgl_rab;?>',
											method: 'POST',											
											params: {																	
												'kode_analisa' : names.join(','),
												'id_data_analisa' : dmid.join(','),
												'harga_satuan': harga.join(','), 
												'koefisien' : 1,
												'id_proyek' : <?=$id_proyek;?>
											},								
											success: function(response) {
												var text = response.responseText;
												Ext.example.msg( "Status", text, function(){
													storeANSAT.load();
													storeASAT.load();													
													storeTree.load();
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
		});				


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
				url: '<?=base_url();?>pengendalian/cbd_analisa/get_data_ansat/<?=$id_proyek;?>/<?=$tgl_rab;?>',
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
					flex: 1,
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
				// 	editor: {
				// 		xtype: 'numberfield',
				// 		allowBlank: false,
				// 		decimalPrecision: 4
				// 	},							
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
										url: '<?=base_url();?>pengendalian/cbd_analisa/tambah_ansat/<?=$tgl_rab;?>',
										method: 'POST',
										params: {			
											/*
											id_detail_material	15633
											id_proyek	20
											kode_material	505.016
											koefisien	1											
											*/
											'kode_material' : names.join(','),
											'id_detail_material' : dmid.join(','),
											'koefisien' : koef.join(','),
											'id_proyek' : <?=$id_proyek;?>
										},								
										success: function(response) {
											var text = response.responseText;
											Ext.example.msg( "Tambah ANSAT", text, function(){
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
		});
		
			/* asat */
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
					 url: '<?php echo base_url() ?>pengendalian/cbd_analisa/get_asat/<?=$id_proyek;?>/<?=$tgl_rab;?>',
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
				plugins: Ext.create('Ext.grid.plugin.CellEditing', {
					clicksToMoveEditor: 1,				
					clicksToEdit: 1,
					listeners : {
						edit : function() {
							var editedRecords = gridASAT.getStore().getUpdatedRecords();						
							Ext.Ajax.request({
								url: '<?=base_url();?>pengendalian/cbd_analisa/edit_koefisien_satuan/<?=$tgl_rab;?>',
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
											url: '<?=base_url();?>pengendalian/cbd_analisa/delete_asat/<?=$tgl_rab;?>',
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
				],
				columnLines: true,
				dockedItems: [
					{
						xtype: 'toolbar',
						dock: 'top',
						items: [
							{
								text:'Edit Harga',
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
													url: '<?=base_url();?>pengendalian/cbd_analisa/delete_ansat/<?php echo $tgl_rab ?>',
													method: 'POST',											
													params: {
														'id_analisa_asat': idanalisa.join(','),
														'kode_analisa': analisa.join(','),
														'id_proyek' : <?=$id_proyek;?>
													},								
													success: function(response) {
														Ext.example.msg("Status","Data Deleted..!");
														storeAnalisaPekerjaan.load();
														storeASAT.load();
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
			/* end asat */
			
			Ext.define('mdlAnalisaPekerjaan', {
				extend: 'Ext.data.Model',
				fields: [
					'id_data_analisa', 'kode_analisa', 'id_kat_analisa', 'kategori', 'nama_kategori',
					'nama_item', 'id_satuan', 'satuan', 'id_proyek', 'harga_satuan','c_apek','c_asat'
				 ]
			});
							
			var storeAnalisaPekerjaan = Ext.create('Ext.data.Store', {
				model: 'mdlAnalisaPekerjaan',
				pageSize: 100,  
				remoteFilter: true,
				autoLoad: false,
				proxy: {
					 type: 'ajax',
					 url: '<?php echo base_url() ?>pengendalian/cbd_analisa/get_daftar_analisa/<?=$id_proyek;?>/<?=$tgl_rab;?>',
					 reader: {
						 type: 'json',
						 root: 'data'
					 }
				},
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
						'edit' : function() {						
							var editedRecords = gridAnalisaPekerjaan.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>pengendalian/cbd_analisa/tambah_daftar_analisa/<?=$tgl_rab;?>',
								method: 'POST',
								params: {
									'id_proyek' : <?=$id_proyek;?>,
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
					 url: '<?php echo base_url() ?>pengendalian/cbd_analisa/edit_harga_satuan_asat/<?=$id_proyek;?>/<?=$tgl_rab;?>',
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
							var editedRecords = gridEditHargaASAT.getView().getSelectionModel().getSelection();
							Ext.Ajax.request({
								url: '<?=base_url();?>pengendalian/cbd_analisa/update_harga_asat/<?=$tgl_rab;?>',
								method: 'POST',
								params: {
									'id_proyek' : <?=$id_proyek;?>,
									'kode_material' : editedRecords[0].data.kode_material,
									'harga' : editedRecords[0].data.harga,
									'kode_rap' : editedRecords[0].data.kode_rap,
									'keterangan' : editedRecords[0].data.keterangan,
								},								
								success: function(response) {
									Ext.example.msg("Status", response.responseText, function()
									{
										storeAnalisaPekerjaan.load();
										storeASAT.load();
										storeEditHargaASAT.load();
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
			});
				
			/* end edit harga asat */
			
		/* upload analisa */
        var frmUploadAnalisa = Ext.widget({
			xtype: 'form',
			layout: 'form',
			url: '<?php echo base_url(); ?>pengendalian/cbd_analisa/upload_daftar_analisa/<?=$tgl_rab;?>',
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
					value: '<?=$id_proyek;?>'
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
						renderer: function(v, meta, record) {
							var harga_sat = record.get('harga_satuan');
							var c_apek = record.get('c_apek');
							var c_asat = record.get('c_asat');

							if(c_asat == 0 && c_apek == 0) {                                                                      
								return '<font color=red><b>'+v+'</b></font>';
							} else {
								return v;
							}
						},				
						/*
						editor: {
							xtype: 'textfield',
						},
						*/
						summaryType: 'count',
						summaryRenderer: function(value, summaryData, dataIndex) {
							return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
						}											
					},
					{
						text: "Uraian", 
						flex: 4, 
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
								Ext.Ajax.request({
									url: '<?=base_url();?>pengendalian/cbd_analisa/set_analisa_itemid',
									method: 'POST',
									params: {
										'id_proyek' : rec.get('id_proyek'),
										'id_data_analisa' : rec.get('id_data_analisa'),
										'kode_analisa' : rec.get('kode_analisa'),										
									},
									success: function() {
										storeANSAT.load();	
										storeDataSatuan.load();
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
					{text: "",xtype: 'actioncolumn', flex:1, align: 'center', tooltip: 'Delete item ini', sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
						handler: function(grid, rowIndex, colIndex){        
							var rec = storeAnalisaPekerjaan.getAt(rowIndex);
							var id = rec.get('kode_analisa');
							var item = rec.get('kode_analisa')+'-'+rec.get('nama_item');
							Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ('+item+') ini?',function(resbtn){
									if(resbtn == 'yes')
									{
										Ext.Ajax.request({
											url: '<?=base_url();?>pengendalian/cbd_analisa/delete_analisa_pekerjaan/<?=$tgl_rab;?>',
											method: 'POST',
											params: {
												'kode_analisa':id,
												'id_proyek':<?=$id_proyek;?>,
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
					{text: "",xtype: 'actioncolumn', flex:1, align: 'center', tooltip: 'Hapus seluruh analisa untuk item ini', sortable: true,icon:'<?=base_url();?>assets/images/cross.gif',
						handler: function(grid, rowIndex, colIndex){        
							var rec = storeAnalisaPekerjaan.getAt(rowIndex);
							var id = rec.get('kode_analisa');
							var item = rec.get('kode_analisa')+'-'+rec.get('nama_item');
							Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus seluruh Analisa Satuan Pekerjaan untuk item ('+item+') ini?',function(resbtn){
									if(resbtn == 'yes')
									{
										Ext.Ajax.request({
											url: '<?=base_url();?>pengendalian/cbd_analisa/delete_asat_apek/<?=$tgl_rab;?>',
											method: 'POST',
											params: {
												'kode_analisa':id,
												'id_proyek':<?=$id_proyek;?>,
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
				listeners:{
					beforerender:function(){
						storeAnalisaPekerjaan.load();
					}
				},				
				columnLines: true,
				dockedItems: [
					{
						
						xtype: 'toolbar',
						dock: 'top',						
						items: [
							{
								text:'Kembali',
								tooltip:'Kembali ke edit RAP',
								iconCls: 'icon-back',
								handler: function(){    
									window.location = '<?=base_url();?>pengendalian/current_budget/<?=$kunci;?>/<?=$tgl_rab;?>';
								}
							},'-',
							{
								text:'Tambah Data',
								iconCls: 'icon-add',
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
										url: '<?=base_url();?>pengendalian/cbd_analisa/<?=$tgl_rab;?>',
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
								text:'Copy Analisa dari Proyek Lain',
								iconCls: 'icon-copy',
								handler: function(){          
									Ext.example.msg("Status","Kopi Analisa dari analisa lain yang pernah dibuat");
								}
							}, '-',							
							{
								text:'Import Analisa (CSV)',
								iconCls: 'icon-import',
								handler: function(){          
									winUploadAnalisa.setTitle('Upload Analisa (CSV)');
									winUploadAnalisa.on('show', function(win) {
									});				
									winUploadAnalisa.doLayout();
									winUploadAnalisa.show();										
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
													url: '<?=base_url();?>pengendalian/cbd_analisa/delete_analisa/<?php echo $tgl_rab ?>',
													method: 'POST',											
													params: {												
														'kode_analisa' : analisa.join(','),
														'id_data_analisa': idanalisa.join(','),
														'id_proyek' : <?=$id_proyek;?>
													},								
													success: function(response) {
														Ext.example.msg("Status","Data Deleted..!");
														storeAnalisaPekerjaan.load();
														storeASAT.load();
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
							},							
						]							
					}
				],
			});						

			/*
			frame: false,
			store: storeASAT,
			var winDaftarAnalisa = Ext.widget('window', {			
			closeAction: 'hide',
			width: '95%',
			height: '90%',
			resizable: false,
			modal: true,
			*/
			
			//var gridASAT = Ext.create('Ext.grid.Panel', {
			var accRATBK = Ext.create('Ext.Panel', {
				renderTo: Ext.getBody(),
				title: 'Data Analisa Pekerjaan :: <?php echo $data_proyek['proyek']; ?>',
				width: '100%',
				height: '100%',
				layout: 'border',
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
						title: 'Detail Analisa Satuan',
						items: [gridASAT]
					}					
				]
			});			
	});
</script>
</head>
<body>
</body>
</html>