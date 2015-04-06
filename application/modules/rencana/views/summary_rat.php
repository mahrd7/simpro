<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/bootstrap.js"></script> -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
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
		'Ext.layout.container.Anchor'
	]);

	Ext.onReady(function() {
	
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
				url: '<?=base_url();?>',
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
	
		var dummyDC = [
			['1','Biaya Konstruksi','',  '']
		];
		
		Ext.define('svarDC', {
			extend: 'Ext.data.Model',
			fields: [
			   {name: 'item', type: 'string', defaultValue: 'Direct Cost'},
			   {name: 'uraian', type: 'string',defaultValue: 'Biaya Konstruksi (BK)'},
			   {name: 'diajukan', type: 'int', convert: null, defaultValue: undefined},
			   {name: 'persen_bobot', type: 'float', convert: null, defaultValue: undefined},
			]
		});
		
		var sdummyDC = Ext.create('Ext.data.ArrayStore', {
			model: 'svarDC',
			data: dummyDC
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
					flex: 1,
					sortable: true,
				},				
				{
					text: "% (bobot terhadap total kontrak)",
					dataIndex: 'persen_bobot',
					flex: 1,
					sortable: true
				},
			],
			listeners:{
				beforerender:function(){
					storeIDC.load();
				}
			}			
		});
	
		var dummyIDC = [
			['2','Provisi Jaminan', '',  ''],
			['3','Bunga Bank','',  ''],
			['4','ASTEK', '',  ''],
			['5','C.A.R','',  ''],
			['6','DATA UMUM','',  '']
		];
			
		Ext.define('svarIDC', {
			extend: 'Ext.data.Model',
			fields: [
			   {name: 'item', type: 'string', defaultValue: 'Direct Cost'},
			   {name: 'uraian', type: 'string',defaultValue: 'Biaya Konstruksi (BK)'},
			   {name: 'diajukan', type: 'int', convert: null, defaultValue: undefined},
			   {name: 'persen_bobot', type: 'float', convert: null, defaultValue: undefined},
			]
		});
			
		var sdummyIDC = Ext.create('Ext.data.ArrayStore', {
			model: 'svarIDC',
			data: dummyIDC
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
			columns:[
				{

					xtype: 'rownumberer',
					width: 25,
					sortable: false
				},			
				{
					text: "Uraian",
					dataIndex: 'uraian',
					flex: 1,
					sortable: true,
				},
				{
					text: "Diajukan",
					dataIndex: 'diajukan',
					flex: 1,
					sortable: true,
				},				
				{
					text: "% (bobot terhadap total kontrak)",
					dataIndex: 'persen_bobot',
					flex: 1,
					sortable: true
				},
			],
			dockedItems: [
				{
					xtype: 'toolbar',
					items: [
						{
							dock: 'top',
							flex: 1,
							text: 'Edit in-Direct Cost',
							//handler: showwinIDCfrm,
						}
					]
				},
			],						
			listeners:{
				beforerender:function(){
					//storeIDC.load();
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
		 fields: [
			 {name: 'id_rat_varcost', type: 'int'},
			 {name: 'id_proyek_rat', type: 'int'},
			 {name: 'vitem', type: 'string'},
			 {name: 'persentase',       type: 'string'},
		 ]
		});
				
		var storeSVC = Ext.create('Ext.data.Store', {
			model: 'summVCS',
			proxy: {
			 type: 'ajax',
			 url: '<?php echo base_url();?>',
			 //url: '<?php echo base_url();?>rencana/get_variable_cost/',
			 reader: {
				 type: 'json',
				 root: 'data'
			 }
			},
			autoLoad: true,
			//params: {id: <?php //echo $idtender;?>}
			params: {id:1}
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
									Ext.Msg.alert( "Status", text, function(){
										storeSVC.load();
									});											
								},
								failure: function() {
									Ext.Msg.alert( "Error", "Data GAGAL diupdate!");											
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
	
		var panelutama = Ext.create('Ext.form.Panel', {
			renderTo: Ext.getBody(),
			title: 'DATA RAT :: PROYEK ',
			bodyStyle: 'padding:5px 5px 0',
			width: '800',
			fieldDefaults: {
				//labelAlign: 'top',
				msgTarget: 'side',
				//labelWidth: '500px'
			},
			defaults: {
				border: false,
				xtype: 'panel',
				flex: 1,
				layout: 'anchor'
			},
			layout: 'hbox',
			items: [
				{
					items: [
					{
						xtype: 'fieldset',
						title: 'Data Proyek',
						defaultType: 'textfield',
						layout: 'anchor',
						defaults: {
							anchor: '100%',
							flex: 1,
						},						
						items: [
						{
							xtype: 'fieldcontainer',
							msgTarget : 'side',
							layout: 'hbox',
							defaults: {
								flex: 1,
							},							
							items: [
								{
									fieldLabel: 'Divisi',
									xtype:'textfield',
									readOnly: true,
									name: 'divisi'
								}, {
									fieldLabel: 'Pagu',
									xtype:'textfield',
									readOnly: true,
									name: 'pagu'
								}					
							]
						},
						{
							xtype: 'fieldcontainer',
							msgTarget : 'side',
							layout: 'hbox',
							defaults: {
								flex: 1,
							},							
							items:[
								{
									fieldLabel: 'Proyek',
									xtype:'textfield',
									name: 'proyek',
									readOnly: true,
								},
								{
									fieldLabel: 'Nilai Kontrak (excl. PPN)',
									xtype:'textfield',
									readOnly: true,
									name: 'nilai_kontrak'							
								},							
							]
						},
						{
							xtype: 'fieldcontainer',
							msgTarget : 'side',
							layout: 'hbox',
							defaults: {
								flex: 1,
							},							
							items:[
								{
									fieldLabel: 'Waktu Pelaksanaan',
									xtype:'textfield',
									name: 'waktu_pelaksanaan',
									readOnly: true,
								},
								{
									fieldLabel: 'Nilai Kontrak (incl. PPN)',
									xtype:'textfield',
									readOnly: true,
									name: 'nilai_kontrak_ppn'
								},							
							]
						},
						{
							xtype: 'fieldcontainer',
							msgTarget : 'side',
							layout: 'hbox',
							defaults: {flex: 1,},							
							items:[
								{
									fieldLabel: 'Masa pemeliharaan',
									xtype:'textfield',
									name: 'masa_pemeliharaan',
									readOnly: true,
								},
								{
									fieldLabel: 'Nilai Penawaran',
									xtype:'textfield',
									readOnly: true,
									name: 'nilai_penawaran'
								},							
							]
						},														
						]
					},
					{
						xtype: 'fieldset',
						title: 'Direct Cost, in-Direct Cost, Variable Cost',
						defaultType: 'textfield',
						layout: 'anchor',
						defaults: {
							anchor: '100%',
							flex: 1,
						},						
						items: [
							{
								xtype: 'fieldset',
								title: 'Direct Cost',
								defaultType: 'textfield',
								layout: 'anchor',
								defaults: {
									anchor: '100%',
									flex: 1,
								},						
								items: [					
									{
										xtype: 'fieldcontainer',
										msgTarget : 'side',
										layout: 'hbox',
										defaults: {flex: 1,},							
										items:[
											{
												fieldLabel: 'Biaya Konstruksi',
												xtype:'textfield',
												labelWidth: '250',										
												flex: 1,
												anchor: '100%',
												readOnly: true,
												name: 'subtotal_dc'
											},															
											{
												fieldLabel: 'Persentase (%)',
												xtype:'textfield',
												flex: 1,
												anchor: '20%',
												readOnly: true,
												name: 'persen_bk'
											},							
										]
									},																						
								]
							},
							{
								xtype: 'fieldset',
								title: 'in-Direct Cost',
								defaultType: 'textfield',
								layout: 'anchor',
								defaults: {
									anchor: '100%',
									flex: 1,
								},						
								items: [			
								{
									xtype: 'fieldset',
									title: 'Bank',
									defaultType: 'textfield',
									layout: 'anchor',
									defaults: {
										anchor: '100%',
										flex: 1,
									},						
									items: [
									{
										xtype: 'fieldcontainer',
										msgTarget : 'side',
										layout: 'hbox',
										defaults: {flex: 1,},							
										items:[
											{
												fieldLabel: 'Bunga Bank',
												labelWidth: '250',
												xtype:'textfield',
												flex: 1,
												anchor: '100%',
												readOnly: true,
												name: 'bunga_bank'
											},
											{
												fieldLabel: 'Persentase (%)',
												xtype:'textfield',
												flex: 1,
												anchor: '20%',
												readOnly: true,
												name: 'bunga_bank_bk'
											},							
										]
									},																						
									{
										xtype: 'fieldcontainer',
										msgTarget : 'side',
										layout: 'hbox',
										defaults: {flex: 1,},							
										items:[
											{
												fieldLabel: 'Provisi Jaminan',
												labelWidth: '250',
												xtype:'textfield',
												flex: 1,
												anchor: '100%',
												readOnly: true,
												name: 'provisi_jaminan'
											},
											{
												fieldLabel: 'Persentase (%)',
												xtype:'textfield',
												flex: 1,
												anchor: '20%',
												readOnly: true,
												name: 'bunga_bank_bk'
											},							
										]
									},																															
									]	
								}									
								]
							},
							{
								xtype: 'fieldset',
								title: 'Variable Cost',
								defaultType: 'textfield',
								layout: 'anchor',
								defaults: {
									anchor: '100%',
									flex: 1,
								},						
								items: [summVC]
							}
						]
					}					
					]
				},
			],
			buttons: ['->', {
				text: 'Load Data...'
			}]
		});
		
		
	});
</script>
<body></body>
</html>