<html>
<head>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
	<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/bootstrap.js"></script> -->
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
	
<!--
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/src/ux/Ext.ux.GMapPanel3.js"></script>
-->
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

.icon-back {
	background-image:url(<?php echo base_url(); ?>assets/images/back.png) !important;
}

</style>

<script type="text/javascript">
Ext.require([
	'*',
	'Ext.grid.*',
	'Ext.data.*',
	'Ext.util.*',
	'Ext.state.*'		
	]);
Ext.require(['Ext.ux.GMapPanel']);

Ext.define('mdl_cbo', {
	extend: 'Ext.data.Model',
	fields: [
	{name: 'status', mapping: 'status'},
	{name: 'id_status_rat', mapping: 'id_status_rat', type:'string'}
	]
});	

var store_cbo = Ext.create('Ext.data.Store', {
	model: 'mdl_cbo',
	proxy: { 
		type: 'ajax', 
		url: '<?=base_url();?>rencana/get_status_tender', 
		reader: { 
			root: 'data',
			type: 'json' 
		} 
	},
	remoteSort: true,
	autoLoad: true
});
Ext.define('mdl_cbo', {
	extend: 'Ext.data.Model',
	fields: [
	{name: 'name', mapping: 'name'},
	{name: 'value', mapping: 'value', type:'string'}
	]
});

var store_sbu = Ext.create('Ext.data.Store', {
	model: 'mdl_cbo',
	proxy: { 
		type: 'ajax', 
		url: '<?=base_url();?>main/get_sbu', 
		reader: { 
			root: 'data',
			type: 'json' 
		} 
	},
	remoteSort: true,
	autoLoad: true
});

Ext.onReady(function() {	        
	Ext.QuickTips.init();			  		
	Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));		

	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';		

	Ext.define('dataTenderMdl', {
		extend: 'Ext.data.Model',
		fields: [
		'id_proyek_rat','id_status_rat','nama_proyek','jenis_proyek','status',
		'lingkup_pekerjaan','waktu_pelaksanaan','waktu_pemeliharaan','nilai_pagu_proyek','nilai_penawaran',
		'lokasi_proyek','pemilik_proyek','konsultan_pelaksana','konsultan_pengawas','tanggal_tender','divisi','divisi_id'
		],
		idProperty: 'datatenderid'
	});

	var store = Ext.create('Ext.data.Store', {
		pageSize: 50,
		model: 'dataTenderMdl',
		remoteSort: true,
		proxy: {
			type: 'jsonp',
			url: '<?=base_url();?>rencana/get_data_tender',
			reader: {
				root: 'data',
				totalProperty: 'total'
			},
			simpleSortMode: true
		},
		sorters: [{
			property: 'id_proyek_rat',
			direction: 'DESC'
		}]
	});

	var store_divisi = Ext.create('Ext.data.Store', {
		        model: 'mdl_cbo',
		        proxy: { 
					type: 'ajax', 
					url: '<?=base_url();?>main/get_divisi', 
					reader: { 
						root: 'data',
						type: 'json' 
					} 
				},
		        remoteSort: true,
		        autoLoad: true
		    });

	var frmTender = new Ext.form.Panel({
		title: 'Data Umum RAT :: Proyek -> <?=$data_tender['nama_proyek'];?>',
		xtype: 'form',
		layout: 'anchor',
		frame: false,
		url: '<?=base_url();?>rencana/data_umum/update_data_tender/',
		width: '100%',
		height: '100%',
		bodyPadding: '5 5 0',			
		fieldDefaults: {
			msgTarget: 'side',
			labelWidth: 150,				
			anchor: '100%'
		},
		autoScroll: true,
		defaultType: 'textfield',				
		items: [
		{
			xtype: 'hidden',
			name: 'id_proyek_rat',
		},
				/*
				{
					fieldLabel: 'Divisi',				
					value: 'divisi',
				},
				*/
				{
					fieldLabel: 'Nama Proyek',
					afterLabelTextTpl: required,
					name: 'nama_proyek',
					allowBlank: false
				},
				// {
				// 	fieldLabel: 'Jenis Proyek',
				// 	afterLabelTextTpl: required,
				// 	name: 'jenis_proyek',
				// 	allowBlank: false
				// },
				Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'Divisi',
							allowBlank: false,				
							afterLabelTextTpl: required,
							store: store_divisi,					
							emptyText: 'Pilih...',
							name: 'divisi_id',
							typeAhead: true,							
							displayField: 'name',
							valueField: 'value'
						}),	
				Ext.create('Ext.form.ComboBox', {
					fieldLabel: 'Jenis Proyek',
					allowBlank: false,				
					afterLabelTextTpl: required,
					store: store_sbu,					
					emptyText: 'Pilih...',
					name: 'jenis_proyek',
					typeAhead: true,							
					displayField: 'name',
					valueField: 'value'
				}),
				{
					fieldLabel: 'Lingkup Pekerjaan',
					afterLabelTextTpl: required,
					allowBlank: false,
					xtype: 'textareafield',
					name: 'lingkup_pekerjaan'
				}, 
				{
					fieldLabel: 'Waktu Pelaksanaan',
					afterLabelTextTpl: required,
					name: 'waktu_pelaksanaan',
					xtype: 'numberfield',
					allowBlank: false,
				}, 
				{
					fieldLabel: 'Waktu Pemeliharaan',
					afterLabelTextTpl: required,
					name: 'waktu_pemeliharaan',
					xtype: 'numberfield',
					allowBlank: false,
				}, 
				{
					fieldLabel: 'Nilai Pagu Proyek',
					afterLabelTextTpl: required,
					name: 'nilai_pagu_proyek',
					xtype: 'numberfield',
					minValue: 0
				}, 
				{
					fieldLabel: 'Nilai Penawaran',
					afterLabelTextTpl: required,
					name: 'nilai_penawaran',
					xtype: 'numberfield',
					minValue: 0
				}, 					
				{
					fieldLabel: 'Lokasi Proyek',
					name: 'lokasi_proyek',
					afterLabelTextTpl: required,
					xtype: 'textfield',
					allowBlank: false
				},
				{
					xtype: 'filefield',
					name: 'peta_lokasi_proyek',
					fieldLabel: 'Peta Lokasi Proyek'
				}, 	
				{
					xtype:'fieldset',
					title: 'Titik Lokasi Proyek',
					defaultType: 'textfield',
					layout: 'anchor',
					defaults: {
						anchor: '100%'
					},
					items :[
					{
						fieldLabel: 'Longitude',
						name: 'xlong',
					},
					{
						fieldLabel: 'Latitude',
						name: 'xlat'
					}
					]
				},			
				{
					fieldLabel: 'Pemilik Proyek',
					afterLabelTextTpl: required,
					name: 'pemilik_proyek',
					allowBlank: false,
					xtype: 'textfield',
				}, 					
				{
					fieldLabel: 'Konsultan Pelaksana',
					name: 'konsultan_pelaksana',
					xtype: 'textfield',
				}, 					
				{
					fieldLabel: 'Konsultaan Pengawas',
					name: 'konsultan_pengawas',
					xtype: 'textfield',
				},
				{
					fieldLabel: 'Tanggal Tender',
					afterLabelTextTpl: required,
					name: 'tanggal_tender',
					xtype: 'datefield',
					format: 'Y-m-d',
					allowBlank: false						
				},
				{
					fieldLabel: 'Tanggal Awal Proyek',
					afterLabelTextTpl: required,
					name: 'mulai',
					xtype: 'datefield',
					format: 'Y-m-d',
					allowBlank: false						
				},
				{
					fieldLabel: 'Tanggal Akhir Proyek',
					afterLabelTextTpl: required,
					name: 'akhir',
					xtype: 'datefield',
					format: 'Y-m-d',
					allowBlank: false						
				},
				Ext.create('Ext.form.ComboBox', {
					fieldLabel: 'Status',
					allowBlank: false,				
					afterLabelTextTpl: required,
					store: store_cbo,					
					emptyText: 'Pilih status tender...',
					name: 'id_status_rat',
					typeAhead: true,							
					displayField: 'status',
					valueField: 'id_status_rat',
					listeners: {
						'select': function(combo, row, index) {
						},
					},
				})
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
											frmTender.load(
											{
												url: '<?=base_url();?>rencana/data_umum/get_data_tender/<?=$idtender;?>',
												params: {
													id: <?=$idtender;?>
												},
												success:  function(form, action) {
												},
												failure: function(form, action) {
													Ext.Msg.alert("Load failed", action.result.message);
												}
											}		
											);
											location.reload();		
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
						frmTender.getForm().reset();
					}
				}],
				listeners: {
					'load' :  function(store,records,options) {
						//console.log(this, this.loaded);						
						//store.loaded = true;									
					}
				},
				dockedItems: [
				{
					xtype: 'toolbar',
					dock: 'top',
					items: [
					{
						text:'Kembali',
						iconCls: 'icon-back',
						tooltip:'Kembali ke menu Tender',
						handler: function(){        
							window.location = '<?=base_url();?>rencana/entry_rat';							
						}
					}
					]
				}
				],				
			});

frmTender.load(
{
	url: '<?=base_url();?>rencana/data_umum/get_data_tender/<?=$idtender;?>',
	params: {
		id: <?=$idtender;?>
	},
	success:  function(form, action) {
	},
	failure: function(form, action) {
		Ext.Msg.alert("Load failed", action.result.message);
	}
}		
);		

/* dokumen proyek */
Ext.define('mdlDokumen', {
	extend: 'Ext.data.Model',
	fields: [
	'id_dokumen_tender', 'du_proyek_tgl_upload', 
	'du_proyek_judul', 'du_proyek_keterangan', 'du_proyek_file', 
	'du_proyek_file_type', 'du_proyek_file_type','id_proyek_rat'
	]
});

var storedoc = Ext.create('Ext.data.Store', {
	model: 'mdlDokumen',
	pageSize: 50,  
	remoteFilter: true,
	autoLoad: false,

	proxy: {
		type: 'ajax',
		url: '<?php echo base_url() ?>rencana/data_umum/get_data_dokumen/<?=$idtender;?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	}		
});		
storedoc.load();

var gridDokSurvey = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	frame: false,
	store: storedoc,
	columns: [
	{
		xtype: 'rownumberer',
		width: 35,
		sortable: false
	},
	{text: "Tanggal", flex: 1, sortable: false, dataIndex: 'du_proyek_tgl_upload'},
	{text: "Judul", flex: 2, sortable: false, dataIndex: 'du_proyek_judul',editor: 'textfield'},
	{text: "File", width: 113, sortable: false, dataIndex: 'du_proyek_file',editor: 'textfield', renderer:function(v,a,b){
		if (b.data.du_proyek_file_type == 'image/jpeg' || b.data.du_proyek_file_type == 'image/png' || b.data.du_proyek_file_type == 'image/gif') {
			val = '<img src="<?=base_url();?>uploads/'+v+'" width="100px" height="60px">';
		} else {
			val = v;
		}
		return val;
	}},
	{text: "File Type", flex: 1, sortable: false, dataIndex: 'du_proyek_file_type',editor: 'textfield'},
	{text: "Keterangan", flex: 1, sortable: false, dataIndex: 'du_proyek_keterangan'},
	{text: "",xtype: 'actioncolumn', flex:1,  align: 'center', sortable: false,icon:'<?=base_url();?>assets/images/download.png',
	handler: function(grid, rowIndex, colIndex){        
		rec = storedoc.getAt(rowIndex);
		var id = rec.get('id_dokumen_tender');
		var file = rec.get('du_proyek_file');
		Ext.Msg.alert('Download', 'Download file '+file);
		window.location='<?php echo base_url(); ?>rbk/download/'+file;
	}
},				
{text: "",xtype: 'actioncolumn', flex:1, align: 'center', sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
handler: function(grid, rowIndex, colIndex){        
	rec = storedoc.getAt(rowIndex);
	var id = rec.get('id_dokumen_tender');
	var file = rec.get('du_proyek_file');
	Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
		if(resbtn == 'yes')
		{
			Ext.Ajax.request({
				url: '<?=base_url();?>rencana/data_umum/delete_dokumen/'+id,
				method: 'POST',
				params: {
					'id':id,
					'file':file
				},
				success: function(fn,o) {
					Ext.Msg.alert( "Status", fn.responseText, function(){	
						storedoc.load();
					});											
				},
				failure: function() {
					Ext.Msg.alert( "Status", "File GAGAL dihapus.", function(){	
						storedoc.load();
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
	items: [{
		text:'Tambah Data',
		tooltip:'Tambah Data',
		handler: function(){          
			formdoc.getForm().reset();						
			winadddoc.show();
		}
	},
	'-','Ukuran file maks. 4MB, file yang diperbolehkan: gif,jpg,png,doc,pdf,docx,ppt,pptx,xls,xlsx,zip'					
	]
}
],
});

var formdoc = Ext.widget({
	xtype: 'form',
	layout: 'form',
	url: '<?php echo base_url(); ?>rencana/data_umum/tambah_dokumen',
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
				/*
				{
					xtype: 'datefield',
					fieldLabel: 'Tanggal',
					name: 'du_proyek_tgl_upload',
					format: 'Y-m-d',					
					value: new Date(),
				},
				*/				
				{
					xtype: 'textfield',
					fieldLabel: 'Nama Dokumen',
					name: 'du_proyek_judul',
					allowBlank: false,
					afterLabelTextTpl: required,
				},
				{
					xtype: 'textarea',
					fieldLabel: 'Keterangan',
					allowBlank: false,
					afterLabelTextTpl: required,
					name: 'du_proyek_keterangan',
				},
				{
					xtype: 'filefield',
					id: 'form-doc-file',
					emptyText: 'silahkan pilih file...',
					afterLabelTextTpl: required,
					fieldLabel: 'File',
					name: 'du_proyek_file',
					buttonText: 'pilih file',
					allowBlank: false
				},				
				],

				buttons: [{
					text: 'Upload Dokumen',
					handler: function(){            
						var form = this.up('form').getForm();
						if(form.isValid()){
							form.submit({
								enctype: 'multipart/form-data',
								waitMsg: 'Upload dokumen ...',
								success: function(fp, o) {
									Ext.MessageBox.alert('Status','Upload file "'+ o.result.file + '" berhasil.', function()
									{
										storedoc.load();
									});
								},
								failure: function(fp, o){								
									Ext.MessageBox.alert('Error','GAGAL Upload file "'+ o.result.file + '", pesan: '+o.result.message);
								}
							});
							winadddoc.hide();
						}
					}
				},
				{
					text: 'Cancel',
					handler: function() {
						winadddoc.hide();
					}
				}]
			});

var winadddoc = Ext.create('Ext.Window', {
	title: 'Dokumentasi Proyek',
	closeAction: 'hide',
	height: 250,
	width: 500,
	layout: 'fit',
	modal: true,
	items: formdoc
});

/* end dokumen proyek */

function fnViewFile(filename)
{
	var panelFile = Ext.create('Ext.panel.Panel', { 
		layout: 'fit',
		items: Ext.create('Ext.Img', {
			src: '<?=base_url();?>uploads/'+filename,
		}),
	});
	var winViewFile = Ext.create('Ext.Window', {
		title: 'View File',
		closeAction: 'hide',
		height: '90%',
		width: '60%',
		layout: 'fit',
		modal: true,
		items: [panelFile]
	});		
	winViewFile.doLayout();
	winViewFile.show();
}

var formsketsa = Ext.widget({
	xtype: 'form',
	layout: 'form',
	url: '<?php echo base_url(); ?>rencana/data_umum/tambah_sketsa_proyek',
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
				/*
				{
					xtype: 'datefield',
					fieldLabel: 'Tanggal',
					name: 'du_proyek_tgl_upload',
					format: 'Y-m-d',					
					value: new Date(),
				},
				*/				
				{
					xtype: 'textfield',
					fieldLabel: 'Nama Dokumen',
					name: 'du_proyek_judul',
					allowBlank: false,
					afterLabelTextTpl: required,
				},
				{
					xtype: 'textarea',
					fieldLabel: 'Keterangan',
					allowBlank: false,
					afterLabelTextTpl: required,
					name: 'du_proyek_keterangan',
				},
				{
					xtype: 'filefield',
					id: 'form-file',
					emptyText: 'silahkan pilih file...',
					afterLabelTextTpl: required,
					fieldLabel: 'File',
					name: 'du_sketsa_file',
					buttonText: 'pilih file',
					allowBlank: false
				},				
				],

				buttons: [{
					text: 'Upload Gambar',
					handler: function(){            
						var form = this.up('form').getForm();
						if(form.isValid()){
							form.submit({
								enctype: 'multipart/form-data',
								waitMsg: 'Upload gambar sketsa proyek ...',
								success: function(fp, o) {
									Ext.MessageBox.alert('Status','Upload file "'+ o.result.file + '" berhasil.', function()
									{
										storeSketsa.load();
									});
								},
								failure: function(fp, o){								
									Ext.MessageBox.alert('Error','GAGAL Upload file "'+ o.result.file + '", pesan: '+o.result.message);
								}
							});
							winsketsa.hide();
						}
					}
				},
				{
					text: 'Cancel',
					handler: function() {
						winsketsa.hide();
					}
				}]
			});

var winsketsa = Ext.create('Ext.Window', {
	title: 'Sketsa Proyek :: Upload Gambar',
	closeAction: 'hide',
	height: 250,
	width: 500,
	layout: 'fit',
	modal: true,
	items: formsketsa
});				

/* sketsa proyek */
Ext.define('mdlSketsa', {
	extend: 'Ext.data.Model',
	fields: [
	'id_sketsa_file', 'du_proyek_tgl_upload', 
	'du_proyek_judul', 'du_proyek_keterangan', 'du_proyek_file', 
	'du_proyek_file_type', 'du_proyek_file_type','id_proyek_rat'
	]
});

var storeSketsa = Ext.create('Ext.data.Store', {
	model: 'mdlSketsa',
	pageSize: 50,  
	remoteFilter: true,
	autoLoad: false,			
	proxy: {
		type: 'ajax',
		url: '<?php echo base_url() ?>rencana/data_umum/get_data_sketsa/<?=$idtender;?>',
		reader: {
			type: 'json',
			root: 'data'
		}
	}		
});		
storeSketsa.load();

var gridSketsaProyek = Ext.create('Ext.grid.Panel', {
	width: '100%',
	height: '100%',
	frame: false,
	store: storeSketsa,
	columns: [
	{
		xtype: 'rownumberer',
		width: 35,
		sortable: false
	},
	{text: "Tanggal", flex: 1, sortable: true, dataIndex: 'du_proyek_tgl_upload'},
	{text: "Judul", flex: 2, sortable: true, dataIndex: 'du_proyek_judul',editor: 'textfield'},
	{text: "File", width: 113, sortable: true, dataIndex: 'du_proyek_file',editor: 'textfield', renderer:function(v){
		val = '<img src="<?=base_url();?>uploads/'+v+'" width="100px" height="60px">';
		return val;
	}},
	{text: "File Type", flex: 1, sortable: true, dataIndex: 'du_proyek_file_type',editor: 'textfield'},
	{text: "Keterangan", flex: 1, sortable: true, dataIndex: 'du_proyek_keterangan'},
	{text: "",xtype: 'actioncolumn', flex:1,  align: 'center', sortable: true,icon:'<?=base_url();?>assets/images/application_view_list.png',
	handler: function(grid, rowIndex, colIndex){        
		rec = storeSketsa.getAt(rowIndex);
		var file = rec.get('du_proyek_file');
		fnViewFile(file);
	}
},				
{text: "",xtype: 'actioncolumn', flex:1,  align: 'center', sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
handler: function(grid, rowIndex, colIndex){        
	rec = storeSketsa.getAt(rowIndex);
	var id = rec.get('id_sketsa_file');
	var file = rec.get('du_proyek_file');
	Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
		if(resbtn == 'yes')
		{
			Ext.Ajax.request({
				url: '<?=base_url();?>rencana/data_umum/delete_sketsa_proyek/'+id,
				method: 'POST',
				params: {
					'id':id,
					'file':file
				},								
				success: function(fn,o) {
					Ext.Msg.alert( "Status", fn.responseText, function(){	
						storeSketsa.load();
					});											
				},
				failure: function() {
					Ext.Msg.alert( "Status", "File GAGAL dihapus.", function(){	
						storeSketsa.load();
					});											
				}
			});			   																			
		}
	});
}
},
],
columnLines: true,
bbar: Ext.create('Ext.PagingToolbar', {
	store: storeSketsa,
	displayInfo: true,
	displayMsg: 'Displaying Data {0} - {1} of {2}',
	emptyMsg: "No data to display"
}),
			/*
			dockedItems: [
				{
					xtype: 'toolbar',
					items: [{
						text:'Tambah Data',
						tooltip:'Tambah Data',
						handler: function(){          
							formdoc.getForm().reset();						
							winadddoc.show();
						}
					},
					'-','Ukuran file maks. 4MB, file yang diperbolehkan: gif,jpg,png'					
					]
				}
			],
			*/
		});

<?php
$q = $this->db->query("SELECT * FROM simpro_m_rat_proyek_tender WHERE id_proyek_rat='".$idtender."'")->row();
if($q->peta_lokasi_proyek != '')
	$gambar_peta = "uploads/".$q->peta_lokasi_proyek;
else
	$gambar_peta = "assets/images/no-image.jpg";
?>

var panelFile22 = Ext.create('Ext.panel.Panel', { 
	layout: 'fit',
	items: Ext.create('Ext.Img', {
		src: '<?=base_url();?><?php echo $gambar_peta; ?>',
	}),
});

/* end sketsa proyek */		
var tabSketsa = Ext.widget('tabpanel', {
	activeTab: 0,
	width: '100%',
	height: '100%',
	deferredRender: false, 
	items: [
	{			
		title: 'Sketsa Proyek',
		layout: 'fit',
		items: [gridSketsaProyek],
		listeners: {
			activate: function(tab){
				setTimeout(function() {
					storeSketsa.load();										
				}, 1);
			}
		},																				
				/*
					Ext.create('Ext.panel.Panel', { 
					layout: 'fit',
					items: Ext.create('Ext.Img', {
						src: '<?=base_url();?>uploads/images/sketsa.jpg',
					}),
				}),
*/
dockedItems: [
{
	xtype: 'toolbar',
	items: [{
		text:'Upload Gambar',
		tooltip:'Tambah Data',
		handler: function(){          
			formsketsa.getForm().reset();						
			winsketsa.show();
		}
	},
	'-','Ukuran file maks. 2MB, file yang diperbolehkan: gif,jpg,png'
	]
}
],
},
{			
	title: 'Foto Peta Lokasi Proyek',
	layout: 'fit',
	items: [panelFile22],
	dockedItems: [
	{
		xtype: 'toolbar',
		items: [
		'<?php echo $q->nama_proyek." ( Latitude : ".$q->xlat."  Longitude : ".$q->xlong." )"; ?>'
		]
	}
	],
},
{
	title: 'Dokumentasi survey',
	items: gridDokSurvey,
	layout: 'fit',
	listeners: {
		activate: function(tab){
			setTimeout(function() {
				storedoc.load();										
			}, 1);
		}
	},																
}, 			
]			
});

var viewport = Ext.create('Ext.Viewport', {
	title: 'Data Umum Tender',
	renderTo: 'panel-data-umum',
	layout: {
		type: 'border',
		padding: 5,
	},
	defaults: {
		split: true,
		autoScroll: true
	},
	items: [
	{
		region:'west',
		layout: 'fit',
		width: '50%',
		split: true,
		scope: this,
		items: frmTender 
	},					
	{
		region:'center',
		layout: 'fit',
		width: '50%',
		items: [tabSketsa],
	}					
	]
});

});
</script>
</head>
<body>
	<div id="panel-data-umum" class="x-hide-display"></div>
</body>
</html>