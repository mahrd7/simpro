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
.icon-new {
	background: url(<?php echo base_url(); ?>assets/images/new-icon.png) no-repeat 0 -1px;
}
		
</style>
<script type="text/javascript">
    Ext.require([
		'*',
		'Ext.layout.container.Anchor'		
	]);

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
		
		/* RAT */
						
		// rat float windows
		var winRat;
		function entryDataRat(idtender)
		{
			if (!winRat) {
				winRat = Ext.create('widget.window', {
					title: 'Entry Data RAT',
					closable: true,
					maximizable: true,
					closeAction: 'hide',
					width: 850,
					minWidth: 550,
					height: 500,
					layout: {
						type: 'border',
						padding: 5
					},
					items: [{
						region: 'west',
						title: 'Project Detail',
						width: 200,
						layout: 'fit',
						split: true,
						collapsible: false,
						floatable: false,
						items:[ 
							Ext.create('Ext.grid.PropertyGrid', {
								editing: false,
								source: {
									'nama_proyek' 		: 'nama_proyek',
									'jenis_proyek' 		: 'jenis_proyek',
									'lingkup_pekerjaan' : 'lingkup_pekerjaan',
									'waktu_pelaksanaan' : 'waktu_pelaksanaan',
									'waktu_pemeliharaan'	: 'waktu_pemeliharaan', 
									'nilai_pagu_proyek' : 'nilai_pagu_proyek',
									'lokasi_proyek' 	: 'lokasi_proyek',
									'pemilik_proyek' 	: 'pemilik_proyek',
									'konsultan_pelaksana' : 'konsultan_pelaksana',
									'konsultan_pengawas': 'konsultan_pengawas',
									'tanggal_tender' 	: 'tanggal_tender'
								}
							})
							]					
					}, 
					{
						region: 'center',
						xtype: 'tabpanel',
						items: [{
							title: 'Perhitugan RAT',
							layout: 'fit',
							items: [gridDC]
						}]
					}]
				});
			}
			//winRat.myExtraParams = { tenderid: idtender}; 
			winRat.on('show', function(win) {
				//console.log(win.myExtraParams.tenderid);
				Ext.Ajax.request({
					url: '<?=base_url();?>rencana/set_tender_id',
					method: 'POST',
					params: {
						'tenderid' : idtender
					},
					success: function() {
						//console.log('success');
						storeDC.load();										
					},
					failure: function() {
						//console.log('error');
					}
				});			   
			});						
			winRat.show();
		}
		
		/* end rat */
		
		/* main dashboard */

		Ext.define('dataTenderMdl', {
			extend: 'Ext.data.Model',
			fields: [
				'id_proyek_rat','id_status_rat','nama_proyek','jenis_proyek','status',
				'lingkup_pekerjaan','waktu_pelaksanaan','waktu_pemeliharaan','nilai_pagu_proyek','nilai_penawaran',
				'lokasi_proyek','pemilik_proyek','konsultan_pelaksana','konsultan_pengawas','tanggal_tender','divisi'
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
					
		/* tender */
		var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';		
		var win;
		function showfrmTender() {
			if (!win) {
				var frmTender = Ext.widget({
					xtype: 'form',
					layout: 'form',
					id: 'frmTenderId',
					url: '<?=base_url();?>rencana/tambah_tender',
					frame: false,
					bodyPadding: '5 5 0',
					width: '100%',
					height: '100%',
					fieldDefaults: {
						msgTarget: 'side',
						labelWidth: 150
					},
					autoScroll: true,
					defaultType: 'textfield',					
					items: [
						{
							name: 'divisi_id',
							xtype: 'hiddenfield',
							value: '<?=$this->session->userdata('divisi_id');?>',
						},					
						{
							name: 'user_entry',
							xtype: 'hiddenfield',
							value: '<?=trim($this->session->userdata('uname'));?>',
						},					
						{
							fieldLabel: 'Divisi',						
							name: 'divisi_name',
							xtype: 'textfield',
							readOnly: true,
							value: '<?=$this->session->userdata('divisi');?>',
						},
						/*
						{
							xtype: 'combo',
							name: 'divisi_id',
							store: { 
								id : 'ds_divisi',
								fields: ['divisi_id', 'divisi_kode', 'divisi'], 
								pageSize: 20, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_divisi', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							fieldLabel: 'Pilih divisi',
							emptyText: 'Pilih divisi...',
							typeAhead: false,
							hideLabel: false,
							hideTrigger:false,
							anchor: '100%',
							displayField: 'divisi',
							valueField: 'divisi_id',
							pageSize: 20
						},	
						*/						
						{
							fieldLabel: 'Nama Proyek',
							afterLabelTextTpl: required,
							name: 'nama_proyek',
							allowBlank: false
						},
						{
							fieldLabel: 'Jenis Proyek',
							afterLabelTextTpl: required,
							name: 'jenis_proyek',
							allowBlank: false
						},
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
						/*
						{
							fieldLabel: 'Status',
							name: 'id_status_rat',
							xtype: 'textfield',
						},
						*/
						Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'Status',
							allowBlank: false,				
							afterLabelTextTpl: required,
							store: { 
								fields: ['id_status_rat','status'],
								pageSize: 50, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_status_tender', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							value: '',							
							emptyText: 'Pilih status tender...',
							name: 'id_status_rat',
							typeAhead: true,
							triggerAction: 'all',
							enableKeyEvents:true,							
							selectOnFocus:true,							
							displayField: 'status',
							valueField: 'id_status_rat',
							listeners: {
								 'select': function(combo, row, index) {
								},
							},
						}),											
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
											store.loadPage(1);
											frmTender.getForm().reset();										
											win.hide();
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
					},
					{
						text: 'Cancel',
						handler: function() {
							frmTender.getForm().reset();
							//this.up('form').getForm().reset();
							win.hide();
						}
					}
					]
				});
			
				win = Ext.widget('window', {
					title: 'Tambah Data Tender',
					closeAction: 'hide',
					width: 550,
					height: 400,
					layout: 'fit',
					resizable: true,
					modal: true,
					items: frmTender
				});
			}
			win.show();
		}					
		/* end form tender */ 
		
        var grid = Ext.create('Ext.grid.Panel', {
            hideCollapseTool: true,
            store: store,
			height: 600,
			width: '100%',			
            columnLines: true,
            columns: [
				{
					xtype: 'rownumberer',
					width: 35,
					sortable: false
				},		
				/*
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/accept.gif',  
								tooltip: 'Ubah Status Tender',
								handler: function(grid, rowIndex, colIndex) {
									var rec = store.getAt(rowIndex);
									ubahStatusTender(rec.get('id_proyek_rat'), rec.get('nama_proyek'));
								}
						}]
                },
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/application_view_list.png',  
								tooltip: 'Daftar Analisa',
								handler: function(grid, rowIndex, colIndex) {
									var rec = store.getAt(rowIndex);
									var idrat = rec.get('id_proyek_rat');
									window.location = "<?=base_url() . 'rencana/daftar_analisa/home/'; ?>"+idrat;
								}
						}]
                },
				*/
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/application_go.png',  
								tooltip: 'Tambah / Edit RAT',
								handler: function(grid, rowIndex, colIndex) {
									var rec = store.getAt(rowIndex);
									//entryDataRat(rec.get('id_proyek_rat'));
									var id_tender = rec.get('id_proyek_rat');
									window.location = '<?=base_url();?>rencana/entry_detail_rat/' + id_tender;
								}
						}]
                },
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/application_view_list.png',  
								tooltip: 'Data Umum',
								handler: function(grid, rowIndex, colIndex) {
									var rec = store.getAt(rowIndex);
									var idrat = rec.get('id_proyek_rat');
									Ext.Ajax.request({
										url: '<?=base_url();?>rencana/data_umum/set_tender_id',
										method: 'POST',
										params: {								
											'id_tender' : idrat,
										},								
										success: function(response) {
											var text = response.responseText;
											window.location = "<?=base_url() . 'rencana/data_umum/index/'; ?>"+idrat;
										},
										failure: function() {
											Ext.example.msg( "Error", "Data GAGAL diupdate!");											
										}
									});
								}
						}]
                },				
                {
                    text     : 'Divisi',
					flex: 1,
                    sortable : false,
                    dataIndex: 'divisi'
                },
                {
                    text     : 'Nama Proyek',
					flex: 2,
                    sortable : false,
                    dataIndex: 'nama_proyek'
                },
                {
                    text     : 'Owner',
					flex: 1,
                    sortable : false,					
                    dataIndex: 'pemilik_proyek'
                },
                {
                    text     : 'Nilai Pagu Proyek',
					flex: 1,
                    sortable : false,
					renderer: Ext.util.Format.numberRenderer('00,000'),					
					align	: 'right',
                    dataIndex: 'nilai_pagu_proyek'
                },
                {
                    text     : 'Nilai Penawaran',
					flex: 1,
					align	: 'right',
                    sortable : false,
					renderer: Ext.util.Format.numberRenderer('00,000'),					
                    dataIndex: 'nilai_penawaran'
                },				
                {
                    text     : 'Jenis Proyek',
					flex: 1.2,
                    sortable : true,
                    dataIndex: 'jenis_proyek',
                    renderer:function(val){

                    	index = store_sbu.findExact('value',val);
                    	if (index != -1) {
							rec = store_sbu.getAt(index);
							value = rec.get('name');
                    	} else {
                    		value = '';
                    	}
                    	return value.trim();
                    }
                },				
                {
                    text     : 'Lokasi',
					flex: 1,
					align	: 'right',
                    sortable : true,
                    dataIndex: 'lokasi_proyek'
                },								
                {
                    text     : 'Tanggal Tender',
					flex: 1,
                    sortable : true,
                    renderer : Ext.util.Format.dateRenderer('m/d/Y'),
					align	 : 'center',
                    dataIndex: 'tanggal_tender'
                },
                {
                    text     : 'Status',
					flex: 1,
                    sortable : true,
                    dataIndex: 'status'
                },				
            ],
           dockedItems: [{
                xtype: 'toolbar',
                items: [
                // {
                //     iconCls: 'icon-add',
                //     text: 'Tambah Tender',
                //     scope: this,
                //     handler: showfrmTender
                // },
                {
                	text:'Open In New Tab',
                	iconCls:'icon-new',
                	handler:function(){
                		window.open(document.URL,'_blank');
                	}
                }
                ]
            }],			
            title: 'Tender',			
            viewConfig: {
                stripeRows: true
            },
			bbar: Ext.create('Ext.PagingToolbar', {
				store: store,
				displayInfo: true,
				displayMsg: 'Displaying Data {0} - {1} of {2}',
				emptyMsg: "No data to display"
			})			
        });
		
		function ubahStatusTender(idTender, namaProyek)
		{
			var formStatusTender = Ext.widget({
				xtype: 'form',
				layout: 'form',
				url: '<?php echo base_url(); ?>rencana/rencana/ubah_status_tender/'+idTender,
				frame: false,
				bodyPadding: '5 5 0',
				width: 350,
				fieldDefaults: {
					msgTarget: 'side',
					labelWidth: '100'
				},
				items: [
					{
						xtype: 'hidden',
						name: 'id_proyek_rat',
						value: idTender
					},
					{
						xtype: 'radiofield',
						name: 'status_tender',
						afterLabelTextTpl: required,						
						inputValue: '4',
						value: '4',
						fieldLabel: 'Status Tender',
						boxLabel: 'Menang'
					}, {
						xtype: 'radiofield',
						name: 'status_tender',
						inputValue: '3',
						value: '3',
						fieldLabel: '',
						labelSeparator: '',
						hideEmptyLabel: false,
						boxLabel: 'Kalah'
					},				
					{
						xtype: 'textarea',
						fieldLabel: 'Keterangan',
						allowBlank: false,
						afterLabelTextTpl: required,
						name: 'keterangan',
					},
				],

				buttons: [{
					text: 'Save',
					handler: function(){            
						var form = this.up('form').getForm();
						if(form.isValid()){
							form.submit({
								enctype: 'multipart/form-data',
								waitMsg: 'Menyimpan...',
								success: function(fp, o) {
									Ext.Msg.alert('Status',o.result.message, function()
									{
										store.load();
									});
								},
								failure: function(fp, o){								
									Ext.Msg.alert('Error',o.result.message);
								}
							});
							store.load();
							winStatusTender.hide();
						}
					}
				},
				{
					text: 'Cancel',
					handler: function() {
					   winStatusTender.hide();
					}
				}]
			});
			
			var winStatusTender = Ext.create('Ext.Window', {
				title: 'Ubah Status Tender',
				closeAction: 'hide',
				height: 200,
				width: 400,
				layout: 'fit',
				modal: true,
				items: formStatusTender
			});

			winStatusTender.doLayout();
			winStatusTender.setTitle("Ubah Status Tender :: Proyek -> "+namaProyek);
			winStatusTender.show();
		}
	
		var bviewPort = new Ext.Viewport({
			renderTo: Ext.getBody(),
			layout: "fit",
			items: [ grid ]
		});
		bviewPort.doLayout();

		store.loadPage(1);
						
		/* end main dashboard */					
    });
    </script>
</head>
<body>	
    <div id="grid-tender" class="x-hide-display"></div>
</body>
</html>
