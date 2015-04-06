<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/bootstrap.js"></script> -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<!-- <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBX9mIZ_YEXJUegZymZLMiCDiwDGdg8sxM&sensor=false"></script> -->
<!--
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/src/ux/Ext.ux.GMapPanel3.js"></script>
-->
<style>

p {
    margin:5px;
}
.icon-new {
	background: url(<?php echo base_url(); ?>assets/images/new-icon.png) no-repeat 0 -1px;
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
            {name: 'name', mapping: 'name'},
            {name: 'value', mapping: 'value', type:'string'}
         ]
    });		

    Ext.define('cbo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'name'},
            {name: 'value'}
         ]
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
	
	var store_propinsi = Ext.create('Ext.data.Store', {
        model: 'mdl_cbo',
        proxy: { 
			type: 'ajax', 
			url: '<?=base_url();?>main/get_propinsi', 
			reader: { 
				root: 'data',
				type: 'json' 
			} 
		},
        remoteSort: true,
        autoLoad: false
    });

    var store_status_proyek = Ext.create('Ext.data.Store', {
        model: 'mdl_cbo',
        proxy: { 
			type: 'ajax', 
			url: '<?=base_url();?>main/get_status_proyek', 
			reader: { 
				root: 'data',
				type: 'json' 
			} 
		},
        remoteSort: true,
        autoLoad: true
    });

    var store_pekerjaan = Ext.create('Ext.data.Store', {
        model: 'mdl_cbo',
        proxy: { 
			type: 'ajax', 
			url: '<?=base_url();?>main/get_store_pekerjaan', 
			reader: { 
				root: 'data',
				type: 'json' 
			} 
		},
        remoteSort: true,
        autoLoad: true
    });

    var store_ekskalasi = Ext.create('Ext.data.Store', {
        model: 'mdl_cbo',
        proxy: { 
			type: 'ajax', 
			url: '<?=base_url();?>main/get_ekskalasi', 
			reader: { 
				root: 'data',
				type: 'json' 
			} 
		},
        remoteSort: true,
        autoLoad: true
    });

    var number = [
    	{"value":"1","name":"1"},
    	{"value":"2","name":"2"},
    	{"value":"3","name":"3"},
    	{"value":"4","name":"4"},
    	{"value":"5","name":"5"},
    	{"value":"6","name":"6"},
    	{"value":"7","name":"7"},
    	{"value":"8","name":"8"},
    	{"value":"9","name":"9"}
    ];

    var store_number = Ext.create('Ext.data.Store', {
    	fields:['value','name'],
        data: number
    });
	
    Ext.onReady(function() {	        
		Ext.QuickTips.init();			  		
        Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));		
		
		var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';		
		
		var frmproyek = new Ext.form.Panel({
			title: 'Data Umum Proyek',
			xtype: 'form',
			layout: 'anchor',
			frame: false,
			url: '<?=base_url();?>rbk/update_data_proyek/',
			width: '100%',
			height: '100%',
			bodyPadding: '5 5 0',			
			fieldDefaults: {
				msgTarget: 'side',
				labelWidth: 275,
				anchor:'100%'
			},
			autoScroll: true,
			defaultType: 'textfield',				
			items: [
				{
					fieldLabel: 'PROYEK ID ',
					name: 'proyek_id',
					afterLabelTextTpl: required,
					allowBlank: false,
					hidden:'true'
				},
				Ext.create('Ext.form.ComboBox', {
					fieldLabel: 'DIVISI ',
					allowBlank: false,				
					afterLabelTextTpl: required,
					store: store_divisi,					
					emptyText: 'Pilih...',
					name: 'divisi_kode',
					typeAhead: true,							
					displayField: 'name',
					valueField: 'value',
					listeners: {
						 'change': function(combo, row, index) {
						 	store_propinsi.load({
						 		params:{
						 			'divisi_id':combo.getValue()
						 		}
						 	});
						},
					},
				}),	
				Ext.create('Ext.form.ComboBox', {
					fieldLabel: 'PROPINSI/KABUPATEN ',
					allowBlank: false,				
					afterLabelTextTpl: required,
					store: store_propinsi,					
					emptyText: 'Pilih...',
					name: 'propinsi',
					typeAhead: true,							
					displayField: 'name',
					valueField: 'value'
				}),
				{
					xtype:'textfield',
					fieldLabel: 'KODE WILAYAH / DIVISI (Min. 3,Max. 3)',
					name: 'kode_wilayah',
					maxLength: 3,
					minLength:3,
					afterLabelTextTpl: required,
					allowBlank: false
				},
				Ext.create('Ext.form.ComboBox', {
					fieldLabel: 'STATUS PEKERJAAN ',
					allowBlank: false,				
					afterLabelTextTpl: required,
					store: store_pekerjaan,					
					emptyText: 'Pilih...',
					name: 'status_pekerjaan',
					typeAhead: true,							
					displayField: 'name',
					valueField: 'value'
				}),
				{
					xtype:'numberfield',
					fieldLabel: 'PROSENTASE JO %',
					name: 'sts_pekerjaan'
				},	
				Ext.create('Ext.form.ComboBox', {
					fieldLabel: 'JENIS PEKERJAAN ',
					allowBlank: false,				
					afterLabelTextTpl: required,
					store: store_sbu,					
					emptyText: 'Pilih...',
					name: 'sbu_kode',
					typeAhead: true,							
					displayField: 'name',
					valueField: 'value'
				}),
				{
					fieldLabel: 'NAMA PEKERJAAN ',
					name: 'proyek',
					afterLabelTextTpl: required,
					allowBlank: false
				},	
				{
					xtype: 'filefield',
					name: 'sketsa_proyek',
					fieldLabel: 'Peta Lokasi Proyek'
				}, 	
				{
					xtype: 'filefield',
					name: 'struktur_organisasi',
					fieldLabel: 'Struktur Organisasi'
				}, 
				{
					xtype:'fieldset',
					title: 'Titik Lokasi Proyek',
					defaultType: 'numberfield',
					layout: 'anchor',
					defaults: {
						anchor: '100%'
					},
					items :[
						{
							fieldLabel: 'Longitude',
							name: 'lokasi_longitude',
						},
						{
							fieldLabel: 'Latitude',
							name: 'lokasi_latitude'
						}
					]
				},
				{
					fieldLabel: 'LINGKUP PEKERJAAN ',
					name: 'lingkup_pekerjaan',
					xtype:'textarea'
				},	
				{
					fieldLabel: 'ALAMAT KANTOR PROYEK ',
					name: 'proyek_alamat',
					afterLabelTextTpl: required,
					allowBlank: false
				},	
				{
					fieldLabel: 'TELEPON KANTOR ',
					name: 'proyek_telp',
					afterLabelTextTpl: required,
					allowBlank: false
				},	
				{
					fieldLabel: 'LOKASI PROYEK ',
					name: 'lokasi_proyek',
					afterLabelTextTpl: required,
					allowBlank: false
				},	
				{
					fieldLabel: 'PEMILIK PROYEK ',
					name: 'pemberi_kerja'
				},	
				{
					fieldLabel: 'PIMPRO/KALAYEK ',
					name: 'kepala_proyek'
				},	
				{
					fieldLabel: 'KONSULTAN PENGAWAS ',
					name: 'proyek_konsultan_pengawas',
					afterLabelTextTpl: required,
					allowBlank: false
				},	
				{
					fieldLabel: 'NO FILE ',
					name: 'no_spk',
					afterLabelTextTpl: required,
					allowBlank: false
				},	
				{
					fieldLabel: 'NO SPK ',
					name: 'no_spk_2'
				},	
				{
					fieldLabel: 'NO KONTRAK 1 ',
					name: 'no_kontrak',
					afterLabelTextTpl: required,
					allowBlank: false
				},	
				{
					fieldLabel: 'NO KONTRAK 2 ',
					name: 'no_kontrak2'
				},	
				{
					fieldLabel: 'NOMOR WO (Min. 3,Max. 3)',
					name: 'wo',
					maxLength: 4,
					minLength:3,
					maxValue:999,
					minValue:000,
					afterLabelTextTpl: required,
					allowBlank: false
				},	
				{
					xtype:'numberfield',
					fieldLabel: 'NILAI KONTRAK (+) PPN ',
					name: 'nilai_kontrak_ppn',
					minValue:0
				},	
				{
					xtype:'numberfield',
					fieldLabel: 'NILAI KONTRAK (-) PPN ',
					name: 'nilai_kontrak_non_ppn',
					minValue:0
				},	
				{
					xtype:'numberfield',
					fieldLabel: 'PPH FINAL (%) ',
					name: 'pph_final',
					minValue:0
				},	
				{
					fieldLabel: 'SUMBER DANA ',
					name: 'proyek_nama_sumber_1'
				},	
				{
					xtype:'numberfield',
					fieldLabel: 'UANG MUKA (%) ',
					name: 'uang_muka',
					minValue:0
				},	
				{
					fieldLabel: 'TERMIJN ',
					name: 'termijn'
				},	
				{
					xtype:'numberfield',
					fieldLabel: 'RETENSI (%) ',
					name: 'retensi',
					minValue:0
				},	
				{
					fieldLabel: 'JAMINAN PELAKSANAAN ',
					name: 'jaminan_pelaksanaan'
				},	
				{
					fieldLabel: 'ASURANSI PEKERJAAN ',
					name: 'asuransi_pekerjaan'
				},
				{
					xtype:'numberfield',
					fieldLabel: 'DENDA MINIMAL ',
					name: 'denda_minimal',
					minValue:0
				},	
				{
					xtype:'numberfield',
					fieldLabel: 'DENDA MAKSIMAL ',
					name: 'denda_maksimal',
					minValue:0
				},	
				{
					fieldLabel: 'SIFAT KONTRAK ',
					name: 'sifat_kontrak'
				},	
				{
					fieldLabel: 'MANAJEMEN PELAKSANAAN ',
					name: 'manajemen_pelaksanaan'
				},	
				Ext.create('Ext.form.ComboBox', {
					fieldLabel: 'ESKALASI ',
					allowBlank: false,				
					afterLabelTextTpl: required,
					store: store_ekskalasi,					
					emptyText: 'Pilih...',
					name: 'eskalasi',
					typeAhead: true,							
					displayField: 'name',
					valueField: 'value'
				}),
				{
					xtype:'numberfield',
					fieldLabel: 'BEDA KURS ',
					name: 'beda_kurs',
					minValue:0
				},	
				{
					fieldLabel: 'PEK. TAMBAH/KURANG ',
					name: 'pek_tambah_kurang'
				},	
				{
					xtype:'numberfield',
					fieldLabel: 'USULAN ',
					name: 'rap_usulan',
					minValue:0
				},	
				{
					xtype:'numberfield',
					fieldLabel: 'DITETAPKAN ',
					name: 'rap_ditetapkan',
					minValue:0
				},	
				{
					xtype:'datefield',
					fieldLabel: 'TANGGAL MULAI ',
					name: 'mulai',
					format:'d M Y',
					submitFormat:'Y-m-d'
				},	
				{
					xtype:'datefield',
					fieldLabel: 'TANGGAL SELESAI ',
					name: 'berakhir',
					format:'d M Y',
					submitFormat:'Y-m-d'
				},	
				{
					fieldLabel: 'JANGKA WAKTU PELAKSANAAN (HARI) ',
					name: 'jangka_selisih',
					readOnly:true
				},	
				{
					xtype:'numberfield',
					fieldLabel: 'PERPANJANGAN WAKTU PELAKSANAAN (HARI) ',
					name: 'perpanjangan_waktu',
					minValue:0
				},	
				{
					fieldLabel: 'TOTAL WAKTU PELAKSANAAN (HARI) ',
					name: 'total_tambah_waktu',
					readOnly:true
				},	
				{
					xtype:'numberfield',
					fieldLabel: 'MASA PEMELIHARAAN (HARI) ',
					name: 'masa_pemeliharaan',
					minValue:0
				},	
				{
					xtype:'datefield',
					fieldLabel: 'TANGGAL TENDER ',
					name: 'tgl_tender',
					format: 'd M Y',
					submitFormat:'Y-m-d'
				},	
				{
					xtype:'datefield',
					fieldLabel: 'TANGGAL BERAKHIR LAPORAN ',
					name: 'tgl_pengumuman',
					format: 'd M Y',
					submitFormat:'Y-m-d'
				},	
				Ext.create('Ext.form.ComboBox', {
					fieldLabel: 'STATUS PROYEK ',
					allowBlank: false,				
					afterLabelTextTpl: required,
					store: store_status_proyek,					
					emptyText: 'Pilih...',
					name: 'proyek_status',
					typeAhead: true,							
					displayField: 'name',
					valueField: 'value'
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
									frmproyek.load(
										{
											url: '<?=base_url();?>rbk/get_data_proyek_data/<?php echo $idtender ?>',
											params: {
												id: <?php echo $idtender ?>
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
					frmproyek.getForm().reset();
				}
			}],	
			dockedItems: [{
                xtype: 'toolbar',
                items: [
                {
                	text:'Open In New Tab',
                	iconCls:'icon-new',
                	handler:function(){
                		window.open(document.URL,'_blank');
                	}
                }
                ]
            }],
			listeners: {
				'load' :  function(store,records,options) {
						//console.log(this, this.loaded);						
						//store.loaded = true;									
				}
			}				
		});

		frmproyek.load(
			{
				url: '<?=base_url();?>rbk/get_data_proyek_data/<?php echo $idtender ?>',
				params: {
					id: <?php echo $idtender ?>
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
				'id_dokumen_proyek', 'du_proyek_tgl_upload', 
				'du_proyek_judul', 'du_proyek_keterangan', 'du_proyek_file', 
				'du_proyek_file_type', 'du_proyek_file_type','proyek_id'
			 ]
		});

		var storedoc = Ext.create('Ext.data.Store', {
			model: 'mdlDokumen',
			pageSize: 50,  
			remoteFilter: true,
			autoLoad: false,
			
			proxy: {
				 type: 'ajax',
				 url: '<?php echo base_url() ?>rbk/get_data_dokumen/<?=$idtender;?>',
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
						var id = rec.get('id_dokumen_proyek');
						var file = rec.get('du_proyek_file');
						Ext.Msg.alert('Download', 'Download file '+file);
						window.location='<?php echo base_url(); ?>rbk/download/'+file;
					}
				},				
				{text: "",xtype: 'actioncolumn', flex:1, align: 'center', sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
					handler: function(grid, rowIndex, colIndex){        
						rec = storedoc.getAt(rowIndex);
						var id = rec.get('id_dokumen_proyek');
						var file = rec.get('du_proyek_file');
						Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
								if(resbtn == 'yes')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rbk/delete_dokumen/'+id,
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
			url: '<?php echo base_url(); ?>rbk/tambah_dokumen',
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
					name: 'proyek_id',
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
			url: '<?php echo base_url(); ?>rbk/tambah_sketsa_proyek',
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
				'foto_no', 
				'foto_proyek_tgl', 
				'foto_proyek_judul', 
				'foto_proyek_keterangan', 
				'foto_proyek_file', 
				'foto_proyek_file_type', 
				'proyek_id'
			 ]
		});

		var storeSketsa = Ext.create('Ext.data.Store', {
			model: 'mdlSketsa',
			pageSize: 50,  
			remoteFilter: true,
			autoLoad: false,			
			proxy: {
				 type: 'ajax',
				 url: '<?php echo base_url() ?>rbk/get_data_sketsa/<?=$idtender;?>',
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
				{text: "Tanggal", flex: 1, sortable: true, dataIndex: 'foto_proyek_tgl'},
				{text: "Judul", flex: 2, sortable: true, dataIndex: 'foto_proyek_judul',editor: 'textfield'},
				{text: "File", width: 113, sortable: true, dataIndex: 'foto_proyek_file',editor: 'textfield', renderer:function(v){
					val = '<img src="<?=base_url();?>uploads/'+v+'" width="100px" height="60px">';
					return val;
				}},
				{text: "File Type", flex: 1, sortable: true, dataIndex: 'foto_proyek_file_type',editor: 'textfield'},
				{text: "Keterangan", flex: 1, sortable: true, dataIndex: 'foto_proyek_keterangan'},
				{text: "",xtype: 'actioncolumn', flex:1,  align: 'center', sortable: true,icon:'<?=base_url();?>assets/images/application_view_list.png',
					handler: function(grid, rowIndex, colIndex){        
						rec = storeSketsa.getAt(rowIndex);
						var file = rec.get('foto_proyek_file');
						fnViewFile(file);
					}
				},				
				{text: "",xtype: 'actioncolumn', flex:1,  align: 'center', sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
					handler: function(grid, rowIndex, colIndex){        
						rec = storeSketsa.getAt(rowIndex);
						var id = rec.get('foto_no');
						var file = rec.get('foto_proyek_file');
						Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
								if(resbtn == 'yes')
								{
									Ext.Ajax.request({
										url: '<?=base_url();?>rbk/delete_sketsa_proyek/'+id,
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
		$q = $this->db->query("SELECT * FROM simpro_tbl_proyek WHERE proyek_id='".$idtender."'")->row();
		if($q->sketsa_proyek != '')
			$gambar_peta = "uploads/".$q->sketsa_proyek;
		else
			$gambar_peta = "assets/images/no-image.jpg";
		?>

		var panelFile22 = Ext.create('Ext.panel.Panel', { 
				layout: 'fit',
				items: Ext.create('Ext.Img', {
					src: '<?=base_url();?><?php echo $gambar_peta; ?>',
				}),
			});

		<?php
		$q_sk = $this->db->query("SELECT * FROM simpro_tbl_proyek WHERE proyek_id='".$idtender."'")->row();
		if($q_sk->struktur_organisasi != '')
			$gambar_sk = "uploads/".$q_sk->struktur_organisasi;
		else
			$gambar_sk = "assets/images/no-image.jpg";
		?>

		var panelFileSK = Ext.create('Ext.panel.Panel', { 
				layout: 'fit',
				items: Ext.create('Ext.Img', {
					src: '<?=base_url();?><?php echo $gambar_sk; ?>',
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
						'<?php echo $q->proyek." ( Latitude : ".$q->lokasi_latitude."  Longitude : ".$q->lokasi_longitude." )"; ?>'
						]
					}
				],
			},
			{			
				title: 'Struktur Organisasi',
				layout: 'fit',
				items: [panelFileSK],
				dockedItems: [
					{
						xtype: 'toolbar',
						items: [
						'<?php echo $q_sk->proyek." (".$q_sk->struktur_organisasi.")"; ?>'
						]
					}
				],
			},
			{
				title: 'Dokumentasi Tender',
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
					items: frmproyek
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