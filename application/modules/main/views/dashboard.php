<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Sistem Informasi Manajemen Proyek :: PT. Nindya Karya</title>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/xtheme-light-orange/css/xtheme-light-orange.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/xtheme-light-orange/css/xtheme-light-orange-colors.css" />
 -->
<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.gif" />
<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/bootstrap.js"></script> -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/examples.js"></script>

<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/styles.css" /> -->
<style>
.icon-rat {
    background-image:url(<?php echo base_url(); ?>assets/images/rat.png) !important;
}

.icon-import {
    background-image:url(<?php echo base_url(); ?>assets/images/file_import.png) !important;
}

.icon-blue-folder {
    background-image:url(<?php echo base_url(); ?>assets/images/blue-folder-open-image.png) !important;
}

.icon-rap {
    background-image:url(<?php echo base_url(); ?>assets/images/rap.png) !important;
}

.icon-scheduler {
    background-image:url(<?php echo base_url(); ?>assets/images/scheduler.png) !important;
}

.icon-Sync {
    background-image:url(<?php echo base_url(); ?>assets/images/Sync.png) !important;
}

.icon-pengendalian {
    background-image:url(<?php echo base_url(); ?>assets/images/pengendalian.png) !important;
}

.icon-report {
    background-image:url(<?php echo base_url(); ?>assets/images/report.png) !important;
}

.icon-transaksi {
    background-image:url(<?php echo base_url(); ?>assets/images/transaksi.png) !important;
}

.icon-information {
    background-image:url(<?php echo base_url(); ?>assets/images/information.png) !important;
}

.icon-back {
    background-image:url(<?php echo base_url(); ?>assets/images/back.png) !important;
}

#loading-mask {
  position: absolute;
  left:     0;
  top:      0;
  width:    100%;
  height:   100%;
  z-index:  20000;
  background-color: white;
}

#loading {
	height:auto;
	position:absolute;
	left:45%;
	top:40%;
	padding:2px;
	z-index:20001;
}

#loading .loading-indicator {
  background: url(<?=base_url();?>assets/images/extanim32.gif) no-repeat;
  color:      #555;
  font:       bold 13px tahoma,arial,helvetica;
  padding:    8px 42px;
  margin:     0;
  text-align: center;
  height:     auto;
}

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

.icon-user {
    background-image:url(<?php echo base_url(); ?>assets/images/user.png) !important;
}

.icon-profile {
    background-image:url(<?php echo base_url(); ?>assets/images/user_suit.png) !important;
}

.icon-folder {
    background-image:url(<?php echo base_url(); ?>assets/images/folder_go.png) !important;
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

.msg .x-box-mc {
    font-size:14px;
}

#msg-div {
    position:absolute;
    left:35%;
    top:10px;
    width:300px;
    z-index:20000;
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
	  var myChecker = setInterval(function () {
	    Ext.Ajax.request({
	    	method:'post',
	    	url:'<?php echo base_url() ?>main/login/check_session',
	    	success:function(response){
	    		var text = response.responseText;
	    		if (text == false) {
	    			window.location = document.URL;
	    		}
	    	}
	    });
	  }, 62 * 1000);


    Ext.require([
		'*',
		'Ext.ux.form.SearchField',
		'Ext.ux.TabCloseMenu'
	]);

	Ext.Ajax.timeout = 3600000;
	//Ext.require('Ext.ux.TabCloseMenu');	
	
	Ext.define('realisasi_proyek', {
		extend: 'Ext.data.Model',
		fields: [
			{name:'grup', mapping:'grup'},
			{name:'divisi_kode', mapping:'divisi_kode'},
			{name:'unit_usaha', mapping:'unit_usaha'},
			{name:'kontrak_kini', mapping:'kontrak_kini', type:'float'},
			{name:'pu_awal', mapping:'pu_awal', type:'float'},
			{name:'bk_awal', mapping:'bk_awal', type:'float'},
			{name:'laba_kotor', mapping:'laba_kotor', type:'float'},
			{name:'pu_sd_bulanini', mapping:'pu_sd_bulanini', type:'float'},
			{name:'bk_sd_bulanini', mapping:'bk_sd_bulanini', type:'float'},
			{name:'selisihpu_bk', mapping:'selisihpu_bk', type:'float'},
			{name:'mos', mapping:'mos', type:'float'},
			{name:'laba_kotor_sd_blnini', mapping:'laba_kotor_sd_blnini', type:'float'},
			{name:'cash_in', mapping:'cash_in', type:'float'},
			{name:'cash_out', mapping:'cash_out', type:'float'},
			{name:'sisa_anggaran', mapping:'sisa_anggaran', type:'float'},
			{name:'pu_proyeksi', mapping:'pu_proyeksi', type:'float'},
			{name:'bk_proyeksi', mapping:'bk_proyeksi', type:'float'},
			{name:'mos_proyeksi', mapping:'mos_proyeksi', type:'float'},
			{name:'laba_kotor_proyeksi', mapping:'laba_kotor_proyeksi', type:'float'},
			{name:'deviasi', mapping:'deviasi', type:'float'}
		]
	});

	var store_realisasi_proyek = Ext.create('Ext.data.Store', {
        model: 'realisasi_proyek',
        remoteSort: true,
        autoLoad: false,  
        groupField: 'grup',      
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url() ?>main/realisasi_pengendalian_proyek',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    Ext.define('realisasi_divisi', {
		extend: 'Ext.data.Model',
		fields: [
			{name:'id_kumpulan_laporan', mapping:'id_kumpulan_laporan', type:'float'},
			{name:'nama_proyek', mapping:'nama_proyek'},
			{name:'unit_usaha', mapping:'unit_usaha'},
			{name:'kontrak_kini', mapping:'kontrak_kini', type:'float'},
			{name:'prog', mapping:'prog', type:'float'},
			{name:'perpu', mapping:'perpu', type:'float'},
			{name:'perbk', mapping:'perbk', type:'float'},
			{name:'pu_awal', mapping:'pu_awal', type:'float'},
			{name:'bk_awal', mapping:'bk_awal', type:'float'},
			{name:'laba_kotor', mapping:'laba_kotor', type:'float'},
			{name:'pu_sd_bulanini', mapping:'pu_sd_bulanini', type:'float'},
			{name:'bk_sd_bulanini', mapping:'bk_sd_bulanini', type:'float'},
			{name:'selisihpu_bk', mapping:'selisihpu_bk', type:'float'},
			{name:'mos', mapping:'mos', type:'float'},
			{name:'laba_kotor_sd_blnini', mapping:'laba_kotor_sd_blnini', type:'float'},
			{name:'cash_in', mapping:'cash_in', type:'float'},
			{name:'cash_out', mapping:'cash_out', type:'float'},
			{name:'sisa_anggaran', mapping:'sisa_anggaran', type:'float'},
			{name:'pu_proyeksi', mapping:'pu_proyeksi', type:'float'},
			{name:'bk_proyeksi', mapping:'bk_proyeksi', type:'float'},
			{name:'mos_proyeksi', mapping:'mos_proyeksi', type:'float'},
			{name:'laba_kotor_proyeksi', mapping:'laba_kotor_proyeksi', type:'float'},
			{name:'deviasi', mapping:'deviasi', type:'float'},
			{name:'sb_dana', mapping:'sb_dana'},
			{name:'mulai', mapping:'mulai', type:'date'},
			{name:'selesai', mapping:'selesai', type:'date'},
			{name:'tgl_approve', mapping:'tgl_approve', type:'date'},
			{name:'status', mapping:'status'},
			{name:'sp', mapping:'sp', type:'float'},
			{name:'status_pekerjaan', mapping:'status_pekerjaan', type:'string'}
		]
	});

	var store_realisasi_divisi = Ext.create('Ext.data.Store', {
        model: 'realisasi_divisi',
        remoteSort: true,
        autoLoad: false,  
        groupField: 'status_pekerjaan',      
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url() ?>main/realisasi_pengendalian_proyek_divisi',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
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

	Ext.define('cbo', {
		extend: 'Ext.data.Model',
		fields: [
			{name:'text', mapping:'text'},
			{name:'value', mapping:'value'}
		]
	});

    var store_bln = Ext.create('Ext.data.Store', {
        model: 'cbo',
        remoteSort: true,
        autoLoad: true,       
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url() ?>main/getbulan',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_thn = Ext.create('Ext.data.Store', {
        model: 'cbo',
        remoteSort: true,
        autoLoad: true,       
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url() ?>main/gettahun',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    Ext.onReady(function() {
        Ext.QuickTips.init();
        store_realisasi_proyek.load();	
		
		  setTimeout(function(){
			Ext.get('loading').remove();
			Ext.get('loading-mask').fadeOut({remove:true});
		  }, 50);
  
        Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
		
		var mstore = Ext.create('Ext.data.TreeStore', {
			root: {
				expanded: true
			},			
			proxy: {
				type: 'ajax',
				url: '<?=base_url();?>/menu.json'
			}
		});
				
		/* RAT */
		
		/* DirectCost */
		Ext.define('DCModel', {
			extend: 'Ext.data.Model',
			fields: [
				'id_rat_direct_cost', 'id_kategori_pekerjaan', 'kat_rat', 'type_rat', 'id_proyek_rat', 'id_satuan_pekerjaan', 'uraian', 'satuan', 'mharga', 'volume', 'subtotal'
			],
			idProperty: 'dcmodelid'
		});

		var storeDC = Ext.create('Ext.data.Store', {
			pageSize: 50,
			model: 'DCModel',
			remoteSort: true,
			proxy: {
				type: 'jsonp',
				url: '<?=base_url();?>rencana/get_data_dc',
				reader: {
					root: 'data',
					totalProperty: 'total'
				},
				simpleSortMode: true
			},		
			groupField: 'type_rat',
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
					url: '<?=base_url();?>rencana/tambah_direct_cost',
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
						Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'Tipe RAT',
							afterLabelTextTpl: required,
							allowBlank: false,
							store: { 
								fields: ['id_type_rat','type_rat'], 
								pageSize: 50, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_tipe_rat', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							value :'',							
							emptyText: 'Pilih tipe RAT...',
							name: 'id_type_rat',
							triggerAction: 'all',
							queryMode: 'remote',
							minChars: 3,
							enableKeyEvents:true,							
							selectOnFocus:true,																												
							typeAhead: true,
							pageSize: true,
							displayField: 'type_rat',
							valueField: 'id_type_rat',
							listeners: {
								 'select': function(combo, row, index) {
								}
							},
						}),														
						Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'Sub RAT',
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
							emptyText: 'Pilih Sub RAT...',
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
						Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'Pilih Kategori pekerjaan',
							store: { 
								fields: ['id_kategori_pekerjaan','nama_kategori'], 
								pageSize: 50, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_kategori_pekerjaan', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							value :'',							
							emptyText: 'Pilih Kategori Pekerjaan...',
							name: 'id_kategori_pekerjaan',
							triggerAction: 'all',
							queryMode: 'remote',
							minChars: 3,
							enableKeyEvents:true,							
							selectOnFocus:true,																												
							typeAhead: true,
							pageSize: true,
							displayField: 'nama_kategori',
							valueField: 'id_kategori_pekerjaan',
							listeners: {
								 'select': function(combo, row, index) {
								}
							},
						}),						
						Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'Pilih item pekerjaan',
							store: { 
								id : 'scmb_harga_satuan',
								fields: ['id_satuan_pekerjaan','kode_satuan','mharga'], 
								pageSize: 50, 
								proxy: { 
									type: 'ajax', 
									url: '<?=base_url();?>rencana/get_harga_satuan', 
									reader: { 
										root: 'data',
										type: 'json' 
									} 
								} 
							},
							value :'',
							emptyText: 'Pilih item pekerjaan...',
							name: 'id_satuan_pekerjaan',
							triggerAction: 'all',
							queryMode: 'remote',
							minChars: 3,
							enableKeyEvents:true,							
							selectOnFocus:true,																												
							displayField: 'kode_satuan',
							valueField: 'id_satuan_pekerjaan',
							listeners: {
								 'select': function(combo, row, index) {
									var valharga = row[0].get('mharga');
									Ext.getCmp('hargasatuan').setValue(valharga);
								}
							},							
						}),
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
											storeDC.loadPage(1);
											frmAddDC.getForm().reset();
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
			id: 'gdc',
			width: 700,
			height: 500,
			store: storeDC,
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
					text: "Tipe RAT",
					dataIndex: 'type_rat',
					width: 50,
					sortable: true,
				},
				{
					text: "Kategori RAT",
					dataIndex: 'kat_rat',
					width: 150,
					sortable: true,
					summaryType: 'count',
					summaryRenderer: function(value, summaryData, dataIndex) {
						return ((value === 0 || value > 1) ? '(' + value + ' item)' : '(1 Item)');
					}					
				},
				{
					text: "URAIAN",
					dataIndex: 'uraian',
					width: 250,
					sortable: true
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
													Ext.Msg.alert( "Status", text, function(){
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
			fbar  : ['->', {
					text:'Hitung Total RAT',
					id: 'totrat',
					iconCls: 'icon-clear-group',
					handler : function() {				
						Ext.Ajax.request({
							url: '<?=base_url();?>rencana/get_total_rat',
							method: 'POST',
							params: {
							},								
							success: function(response) {
								var text = response.responseText;
								Ext.Msg.alert( "Hitung jumlah RAT", "Total RAT: " + text);
							},
							failure: function() {
							}
						});			   
					},
			}],								
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
				Ext.Ajax.request({
					url: '<?=base_url();?>rencana/set_tender_id',
					method: 'POST',
					params: {
						'tenderid' : idtender
					},
					success: function() {
						storeDC.load();										
					},
					failure: function() {
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
				'lokasi_proyek','pemilik_proyek','konsultan_pelaksana','konsultan_pengawas','tanggal_tender'
			],
			idProperty: 'datatenderid'
		});

		var store = Ext.create('Ext.data.Store', {
			pageSize: 50,
			model: 'dataTenderMdl',
			remoteSort: true,
			proxy: {
				type: 'jsonp',
				url: '<?=base_url();?>rencana/get_data_tender_dashboard',
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


		var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';		
		var win;

		/* proyek */
		
		function showfrmProyek() {

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

		  //   var store_sbu = Ext.create('Ext.data.Store', {
		  //       model: 'mdl_cbo',
		  //       proxy: { 
				// 	type: 'ajax', 
				// 	url: '<?=base_url();?>main/get_sbu', 
				// 	reader: { 
				// 		root: 'data',
				// 		type: 'json' 
				// 	} 
				// },
		  //       remoteSort: true,
		  //       autoLoad: true
		  //   });
			
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

				var frmproyek = new Ext.form.Panel({
					layout: 'form',
					frame: false,
					url: '<?=base_url();?>main/update_data_proyek/',
					bodyPadding: '5 5 0',			
					fieldDefaults: {
						msgTarget: 'side',
						labelWidth: 275,
						anchor:'100%'
					},
					autoScroll: true,
					defaultType: 'textfield',				
					items: [
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
							fieldLabel: 'LINGKUP PEKERJAAN ',
							name: 'lingkup_pekerjaan'
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
											store.loadPage(1);
											frmproyek.getForm().reset();										
											winp.hide();
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
					},
					{
						text: 'Cancel',
						handler: function() {
							frmproyek.getForm().reset();
							winp.hide();
						}
					}],
					listeners: {
						'load' :  function(store,records,options) {
								//console.log(this, this.loaded);						
								//store.loaded = true;									
						}
					}				
				});
			
				var winp = Ext.widget('window', {
					title: 'Tambah Data Proyek',
					closeAction: 'hide',
					width: 550,
					height: 500,
					layout: 'fit',
					modal: true,
					items: frmproyek
				}).show();
		}				
		/* end form proyek */

					
		/* tender */

		function showfrmTender() {
			if (!win) {
				// Ext.define('mdl_cbo', {
			 //        extend: 'Ext.data.Model',
			 //        fields: [
			 //            {name: 'name', mapping: 'name'},
			 //            {name: 'value', mapping: 'value', type:'string'}
			 //         ]
			 //    });

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

				var frmTender = Ext.widget({
					xtype: 'form',
					layout: 'form',
					id: 'frmTenderId',
					url: '<?=base_url();?>rencana/tambah_tender',
					frame: false,
					bodyPadding: '5 5 0',
					width: 600,
					height: 600,
					fieldDefaults: {
						msgTarget: 'side',
						labelWidth: 150
					},
					autoScroll: true,
					defaultType: 'textfield',					
					items: [
						{
							xtype:'label',
							html:'<font size="4">Silahkan Isi Form Dengan Benar..!!!</font>'
						},
						{
							xtype:'label',
							html:'<font color="red">Ket: Simbol (*) / Tanda Merah Harus Diisi..</font>'
						},
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
						Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'DIVISI ',
							allowBlank: false,				
							afterLabelTextTpl: required,
							store: store_divisi,					
							emptyText: 'Pilih...',
							name: 'divisi_id',
							typeAhead: true,							
							displayField: 'name',
							valueField: 'value',
							value: '<?=$this->session->userdata('divisi_id');?>'
						}),	
						// {
						// 	fieldLabel: 'Divisi',						
						// 	name: 'divisi_name',
						// 	xtype: 'textfield',
						// 	readOnly: true,
						// 	value: '<?=$this->session->userdata('divisi');?>',
						// },
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
						// {
						// 	fieldLabel: 'Jenis Proyek',
						// 	afterLabelTextTpl: required,
						// 	name: 'jenis_proyek',
						// 	allowBlank: false
						// },
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
					height: '98%',
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
			title: 'Proyek RAT',
            hideCollapseTool: true,
            store: store,
            columnLines: true,
            columns: [
				{
					xtype: 'rownumberer',
					width: 35,
					sortable: false
				},			
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/delete.gif',  
								tooltip: 'Hapus Tender',
								handler: function(grid, rowIndex, colIndex) {
									var rec = store.getAt(rowIndex);
									Ext.MessageBox.confirm('Hapus Tender ', 'Apakah anda yakin akan mengahapus tender ini ("'+rec.get('nama_proyek')+'")', function(btn){
										if(btn == 'yes')
										{
											Ext.Ajax.request({
												url: '<?php echo base_url(); ?>rencana/hapus_tender',
												params: {
													tender_id: rec.get('id_proyek_rat')
												},
												success: function(response){
													Ext.Msg.alert('Status', response.responseText, function(){
														store.load();
														window.location = '<?=base_url();?>main/index';
													});									
												},
												failure: function(response, options) {
													Ext.MessageBox.alert('Error', 'Masalah dengan koneksi internet anda!');
												},									
											});										
										}
									});									
								}
						}]
                },							
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/accept.gif',  
								tooltip: 'Pilih Tender',
								handler: function(grid, rowIndex, colIndex) {
									var rec = store.getAt(rowIndex);
									Ext.MessageBox.confirm('Pilih Tender ', 'Tender yg dipilih "'+rec.get('nama_proyek')+'"', function(btn){
										if(btn == 'yes')
										{
											Ext.Ajax.request({
												url: '<?php echo base_url(); ?>main/set_tender_id',
												params: {
													tender_id: rec.get('id_proyek_rat')
												},
												success: function(form, action){
													Ext.example.msg('Pilih Proyek', 'Berhasil memilih tender ' + rec.get('nama_proyek'), function(){
														window.location = '<?=base_url();?>main/index';
														//$nama_tender
													});									
												},
												failure: function(response, options) {
													Ext.MessageBox.alert('Error', 'Masalah dengan koneksi internet anda!');
												},									
											});										
										}
									});									
								}
						}]
                },			
				/*
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
									window.location = "<?=base_url() . 'rencana/data_umum/index/'; ?>"+idrat;
								}
						}]
                },
				*/
                {
                    text     : 'Nama Proyek',
					flex: 3,
                    sortable : false,
                    dataIndex: 'nama_proyek'
                },
                {
                    text     : 'Owner',
					flex: 1,
                    sortable : true,
                    dataIndex: 'pemilik_proyek'
                },
                {
                    text     : 'Nilai Pagu Proyek',
					flex: 1,
                    sortable : true,
					align	: 'right',
					renderer: Ext.util.Format.numberRenderer('00,000'),											
                    dataIndex: 'nilai_pagu_proyek'
                },
                {
                    text     : 'Nilai Penawaran',
					flex: 1,
					align	: 'right',
                    sortable : true,
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
                items: [{
                    iconCls: 'icon-add',
                    text: 'Tambah Tender',
                    scope: this,
                    handler: showfrmTender
                }]
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
		
		store.loadPage(1);

		var grid_divisi = Ext.create('Ext.grid.Panel', {
			title: 'Realisasi Pengendalian Proyek Divisi',
            hideCollapseTool: true,
            store: store_realisasi_divisi,
            columnLines: true,
	        features: [{
	            id: 'group',
	            ftype: 'groupingsummary',
	            groupHeaderTpl: '{name}',
	            hideGroupedHeader: true,
	            enableGroupingMenu: false
	        }],
            columns: [
				{
					xtype: 'rownumberer',
					width: 35,
					sortable: false
				},
                {text: 'UNIT USAHA', width: 100, dataIndex: 'nama_proyek',summaryRenderer: function(value, summaryData, dataIndex) {
	                return 'Total';
	            }
	            },
	            {text: 'SUMBER DANA', width: 100, dataIndex: 'sb_dana'},
        		{text: 'TGL PELAKSANAAN', 
                	columns:[
                		{text: 'MULAI', width: 100, dataIndex: 'mulai', renderer: Ext.util.Format.dateRenderer('m/d/Y')},
                		{text: 'SELESAI', width: 100, dataIndex: 'selesai', renderer: Ext.util.Format.dateRenderer('m/d/Y')}
                	]
            	},
                {text: 'KONTRAK KINI', width: 100, dataIndex: 'kontrak_kini', summaryType: 'sum'},
                {text: 'AWAL', 
                	columns:[
                		{text: 'PU', width: 100, dataIndex: 'pu_awal', summaryType: 'sum'},
                		{text: 'B.K', width: 100, dataIndex: 'bk_awal', summaryType: 'sum'},
                		{text: 'LABA KOTOR', width: 100, dataIndex: 'laba_kotor', summaryType: 'sum'}
                	]
            	},
                {text: 'POSISI S/D TAHUN ????',
                	columns:[
                		{text: '%PROG', width: 100, dataIndex: 'prog', summaryType: 'sum'},
                		{text: 'PU', width: 100, dataIndex: 'pu_sd_bulanini', summaryType: 'sum'},
                		{text: 'B.K', width: 100, dataIndex: 'bk_sd_bulanini', summaryType: 'sum'},
                		{text: '%BK/PU', 
                			columns:[                				
		                		{text: 'AWAL', width: 100, dataIndex: 'perpu', summaryType: 'sum'},
		                		{text: 'SD BLN INI', width: 100, dataIndex: 'perbk', summaryType: 'sum'}
                			]
                		},
                		{text: 'SELISIH PU-BK', width: 100, dataIndex: 'selisihpu_bk', summaryType: 'sum'},
                		{text: 'MOS', width: 100, dataIndex: 'mos', summaryType: 'sum'},
                		{text: 'LABA KOTOR', width: 100, dataIndex: 'laba_kotor_sd_blnini', summaryType: 'sum'},
                		{text: 'CASH', 
                			columns:[                				
		                		{text: 'IN', width: 100, dataIndex: 'cash_in', summaryType: 'sum'},
		                		{text: 'OUT', width: 100, dataIndex: 'cash_out', summaryType: 'sum'}
                			]
                		}
                	]
            	},
                {text: 'SISA ANGGARAN', width: 100, dataIndex: 'sisa_anggaran', summaryType: 'sum'},
                {text: 'PROYEKSI S/D AKHIR',
                	columns:[                		
                		{text: 'PU', width: 100, dataIndex: 'pu_proyeksi', summaryType: 'sum'},
                		{text: 'B.K', width: 100, dataIndex: 'bk_proyeksi', summaryType: 'sum'},
                		{text: 'MOS', width: 100, dataIndex: 'mos_proyeksi', summaryType: 'sum'},
                		{text: 'LABA KOTOR', width: 100, dataIndex: 'laba_kotor_proyeksi', summaryType: 'sum'},
                		{text: 'DEVIASI', width: 100, dataIndex: 'deviasi', summaryType: 'sum'}
                	]
            	},
            	{text: 'INPUT S/D BULAN', width: 100, dataIndex: 'tgl_approve', renderer: Ext.util.Format.dateRenderer('m/d/Y')},
            	{text: 'STATUS', width: 100, dataIndex: 'status'}
            ],
            dockedItems:[{
            	xtype:'toolbar',
            	items:[
            		{
            		text:'Kembali',
            		iconCls:'icon-back',
	            		handler: function(){
							acc_produksi.items.items[0] = grid_produksi;
							acc_produksi.doLayout();
	            		}
	            	}
            	]
            }]		
        });
		
		var grid_produksi = Ext.create('Ext.grid.Panel', {
			title: 'Realisasi Pengendalian Proyek',
            hideCollapseTool: true,
            store: store_realisasi_proyek,
            columnLines: true,
	        features: [{
	            id: 'group',
	            ftype: 'groupingsummary',
	            groupHeaderTpl: '{name}',
	            hideGroupedHeader: true,
	            enableGroupingMenu: false
	        }],
            columns: [
				{
					xtype: 'rownumberer',
					width: 35,
					sortable: false
				},			
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
						icon   : '<?=base_url();?>assets/images/accept.gif',  
						tooltip: 'Pilih Divisi',
						handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
							// acc_produksi.remove(grid_produksi);
							// acc_produksi.add(grid_divisi);
        					store_realisasi_divisi.load({
        						params:{
        							'kode':record.get('divisi_kode')
        						}
        					});
							acc_produksi.items.items[0] = grid_divisi;
							acc_produksi.doLayout();		
						}
					}]
                },
                {text: 'UNIT USAHA', width: 100, dataIndex: 'unit_usaha',summaryRenderer: function(value, summaryData, dataIndex) {
	                return 'Total';
	            }
        		},
                {text: 'KONTRAK KINI', width: 100, dataIndex: 'kontrak_kini', summaryType: 'sum'},
                {text: 'AWAL', 
                	columns:[
                		{text: 'PU', width: 100, dataIndex: 'pu_awal', summaryType: 'sum'},
                		{text: 'B.K', width: 100, dataIndex: 'bk_awal', summaryType: 'sum'},
                		{text: 'LABA KOTOR', width: 100, dataIndex: 'laba_kotor', summaryType: 'sum'}
                	]
            	},
                {text: 'POSISI S/D TAHUN <?php echo date("Y") ?>',
                	columns:[
                		{text: 'PU', width: 100, dataIndex: 'pu_sd_bulanini', summaryType: 'sum'},
                		{text: 'B.K', width: 100, dataIndex: 'bk_sd_bulanini', summaryType: 'sum'},
                		{text: 'SELISIH PU-BK', width: 100, dataIndex: 'selisihpu_bk', summaryType: 'sum'},
                		{text: 'MOS', width: 100, dataIndex: 'mos', summaryType: 'sum'},
                		{text: 'LABA KOTOR', width: 100, dataIndex: 'laba_kotor_sd_blnini', summaryType: 'sum'},
                		{text: 'CASH', 
                			columns:[                				
		                		{text: 'IN', width: 100, dataIndex: 'cash_in', summaryType: 'sum'},
		                		{text: 'OUT', width: 100, dataIndex: 'cash_out', summaryType: 'sum'}
                			]
                		}
                	]
            	},
                {text: 'SISA ANGGARAN', width: 100, dataIndex: 'sisa_anggaran', summaryType: 'sum'},
                {text: 'PROYEKSI S/D AKHIR',
                	columns:[                		
                		{text: 'PU', width: 100, dataIndex: 'pu_proyeksi', summaryType: 'sum'},
                		{text: 'B.K', width: 100, dataIndex: 'bk_proyeksi', summaryType: 'sum'},
                		{text: 'MOS', width: 100, dataIndex: 'mos_proyeksi', summaryType: 'sum'},
                		{text: 'LABA KOTOR', width: 100, dataIndex: 'laba_kotor_proyeksi', summaryType: 'sum'},
                		{text: 'DEVIASI', width: 100, dataIndex: 'deviasi', summaryType: 'sum'}
                	]
            	}
            ],
            dockedItems:[{
            	xtype:'toolbar',
            	itemId:'bar_dash',
            	items:[
            		{
            			text:'Tambah Proyek',
            			iconCls:'icon-add',
            			handler:function(){
            				showfrmProyek();
            			}
            		},
            		'->',
            		'Pilih : ',
            		{
            			xtype:'combo',
            			itemId:'cbo_bulan',
            			value: (new Date().getMonth() + 1),
            			width:100,
            			store: store_bln,
            			name: 'cbo_bulan',
            			triggerAction: 'all',
            			queryMode: 'remote',            			
            			enableKeyEvents:true,							
            			selectOnFocus:true,																												
            			typeAhead: true,
            			displayField: 'text',
            			valueField: 'value',
            		},
            		{
            			xtype:'combo',
            			itemId:'cbo_tahun',
            			value: new Date().getFullYear(),
            			width:100,
            			store: store_thn,
            			name: 'cbo_tahun',
            			triggerAction: 'all',
            			queryMode: 'remote',           			
            			enableKeyEvents:true,							
            			selectOnFocus:true,																												
            			typeAhead: true,
            			displayField: 'text',
            			valueField: 'value',
            		},'-',
            		{
            			text:'GO',
            			handler:function(){
            				bar = grid_produksi.getComponent('bar_dash');
            				bulan = bar.getComponent('cbo_bulan').getValue();
            				tahun = bar.getComponent('cbo_tahun').getValue();
            				grid_produksi.columns[5].setText('POSISI S/D '+to_bulan(bulan)+' TAHUN '+tahun);
            				Ext.Ajax.request({
            					url: '<?=base_url();?>main/generate_kumpulan_laporan',
            					method: 'POST',											
            					params: {
            						'bulan' : bulan,
            						'tahun' : tahun,
            					},								
            					success: function(response) {
            						store_realisasi_proyek.load();
            					},
            					failure: function(response) {
            						Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
            					}
            				});
            			}
            		}
            	]
            }],
            listeners:{
            	beforerender:function(){            		
            		bar = grid_produksi.getComponent('bar_dash');
            		bulan = bar.getComponent('cbo_bulan').getValue();
            		tahun = bar.getComponent('cbo_tahun').getValue();
            		grid_produksi.columns[5].setText('POSISI S/D '+to_bulan(bulan)+' TAHUN '+tahun);
            	}
            }
        });

		var acc_produksi = Ext.create('Ext.Panel', {
			title: 'Produksi',
			layout:'fit',
			items: grid_produksi
			// html: '<iframe scr="<?php echo base_url() ?>main/realisasi_pengendalian_proyek" border=0 width="100%" height="100%"></iframe>',
			// cls:'empty'
		});
		
		var accordion = Ext.create('Ext.Panel', {
			title: 'Dashboard',
			collapsible: true,
			region:'west',
			margins:'0 0 0 0',
			split:true,
			width: '100%',
			layout:'accordion',
			items: [grid, acc_produksi]
		});
		
		/* end main dashboard */
		
		var currentItem;		
		var contentPanel = Ext.create('Ext.tab.Panel', {
			id: 'content-panel-id',
			region: 'center',
			layout: 'fit',
			forceFit: true,
			deferredRender: false,
			resizeTabs: true,
			//forceLayout: false,
			defaults: {
				autoScroll: true,
			},		
			activeTab: 0,
			margin: '5 5 5 0',			
			items: [accordion],
			tabBar:{
				plain: true
			},
			plugins: Ext.create('Ext.ux.TabCloseMenu', {
				extraItemsTail: [
					'-',
					{
						text: 'Closable',
						checked: true,
						hideOnClick: true,
						handler: function (item) {
							currentItem.tab.setClosable(item.checked);
						}
					},
					'-',
					{
						text: 'Enabled',
						checked: true,
						hideOnClick: true,
						handler: function(item) {
							currentItem.tab.setDisabled(!item.checked);
						}
					}
				],
				listeners: {
					aftermenu: function () {
						currentItem = null;
					},
					beforemenu: function (menu, item) {
						menu.child('[text="Closable"]').setChecked(item.closable);
						menu.child('[text="Enabled"]').setChecked(!item.tab.isDisabled());
						currentItem = item;
					}
				}			
			})			
		});							

		function addTab(ttitle,act) 
		{
			if(act != '')
			{    
				var tabExist = false;
				var i=0;
				for(i=0; i < contentPanel.items.items.length; i++)
				{
					if(contentPanel.items.items[i].id == act){
						tabExist = true;
						break;
					}
				}
				if(tabExist==false)
				{
					contentPanel.add({
						id: act,
						closable: true,
						title: ttitle,				
						html: '<iframe src="'+act+'" width="100%" id="content-iframe" height="100%" frameborder="0">Your browser does not support iframe!</iframe>',
						iconCls: 'tabs',
						autoScroll: true,
						forceFit: true,
						deferredRender: false,
						resizeTabs: true,				
						enableTabScroll: true,
						layout: 'fit',
					}).show();
				} else 
				{
					contentPanel.setActiveTab(i);
					contentPanel.on('tabchange', function(me, newCard, oldCard){
						//document.getElementById("content-iframe").contentWindow.location.reload();						
					});					
				}
			}		
		}
		<?php
		/*
		echo "tes";
		var_dump(($this->session->userdata('proyek_id') > 0) OR ($this->session->userdata('id_tender') > 0) OR !empty($_SESSION['proyek_id']));
		*/
		?>
		 var treePanel = Ext.create('Ext.tree.Panel', {
			id: 'tree-panel-id',
			<?php
				if(($this->session->userdata('proyek_id') > 0)
				OR ($this->session->userdata('id_tender') > 0)
				OR !empty($_SESSION['proyek_id'])				
				) echo 'disabled: false,';
					else echo 'disabled: true,';
			?>
			title: 'Menu',
			region:'north',
			split: true,
			collapsible: true,			
			height: '70%',
			rootVisible: false,
			autoScroll: true,
			store: mstore,
			resizeTabs: true,
			enableTabScroll: true,			
			viewConfig: {
                stripeRows: true
            },			
			dockedItems: [{
				xtype: 'toolbar',
				items: [{
					text: 'Expand All',
					handler: function(){
						treePanel.getEl().mask('Expanding tree...');
						var toolbar = this.up('toolbar');
						toolbar.disable();						
						treePanel.expandAll(function() {
							treePanel.getEl().unmask();
							toolbar.enable();
						});
					}
				}, {
					text: 'Collapse All',
					handler: function(){
						var toolbar = this.up('toolbar');
						toolbar.disable();						
						treePanel.collapseAll(function() {
							toolbar.enable();
						});
					}
				}]
			}],
			listeners: {
				itemclick : function(view,rec,item,index,eventObj) {
					var tid = rec.get('id');
					var ttext = rec.get('text');
					if(typeof tid != 'undefined') addTab(ttext, tid);
						else return false;
				}				
			}			
		});
			
		/* pilih proyek */
		Ext.define('dataProyekMdl', {
			extend: 'Ext.data.Model',
			fields: [
				'proyek_id', 'proyek', 'lokasi_proyek', 'no_spk',
				'mulai', 'berakhir', 'total_waktu_pelaksanaan', 'tgl_tender'
			],
		});

		var storeProyek = Ext.create('Ext.data.Store', {
			pageSize: 100,
			model: 'dataProyekMdl',
			remoteSort: true,
			proxy: {
				type: 'jsonp',
				url: '<?=base_url();?>rencana/get_proyek',
				reader: {
					root: 'data',
					totalProperty: 'total'
				},
				simpleSortMode: true
			},
			sorters: [{
				property: 'proyek_id',
				direction: 'DESC'
			}]
		});

        var gridProyek = Ext.create('Ext.grid.Panel', {
            hideCollapseTool: true,
            store: storeProyek,
			height: '100%',			
			width: '100%',			
            columnLines: true,
            columns: [
				{
					xtype: 'rownumberer',
					width: 35,
					sortable: false
				},	
				{
					xtype: 'actioncolumn',
                    width: 25,
					align: 'center',
					icon   : '<?=base_url();?>assets/images/delete.png',  
					tooltip: 'Hapus Proyek',
					handler:function(grid, rowIndex, colIndex){
						Ext.MessageBox.confirm("Peringatan","Apakah anda yakin akan menghapus proyek ini, semua yang berkaitan dengan Proyek Ini akan dihapus (RAB, RAP, Pengendalian, dll..)",function(b){
							if (b == 'yes') {
								var rec = storeProyek.getAt(rowIndex);
								var proyek_id = rec.get('proyek_id');
								Ext.Ajax.request({
									url: '<?=base_url();?>main/hapus_proyek',
									method: 'POST',											
									params: {
										'id_proyek' : proyek_id
									},								
									success: function(response) {
										Ext.example.msg('Status Pilih Proyek', response.responseText, function(){
											storeProyek.load();			
											window.location = '<?=base_url();?>main/index';								
										});
									},
									failure: function(response) {
										Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
									}
								});
							}
						})
					}
				},
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/application_go.png',  
								tooltip: 'Pilih Proyek',
								handler: function(grid, rowIndex, colIndex) {
									var rec = storeProyek.getAt(rowIndex);
									var proyek_id = rec.get('proyek_id');
									Ext.Ajax.request({
										url: '<?=base_url();?>main/set_proyek_id',
										method: 'POST',											
										params: {
											'id_proyek' : proyek_id
										},								
										success: function(response) {
											Ext.example.msg('Status Pilih Proyek', response.responseText, function(){
												window.location = '<?=base_url();?>main/index';												
											});
										},
										failure: function(response) {
											Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
										}
									});
								}
						}]
                },
                {
                    text: 'Nama Proyek',
					flex: 3,
                    sortable : false,
                    dataIndex: 'proyek'
                },
                {
                    text     : 'No SPK',
					flex: 2,
                    sortable : false,
                    dataIndex: 'no_spk'
                },
                {
                    text     : 'Lokasi',
					flex: 1,
                    sortable : false,
                    dataIndex: 'lokasi_proyek'
                },
                {
                    text     : 'Mulai',
					flex: 1,
                    sortable : false,					
                    dataIndex: 'mulai'
                },
                {
                    text     : 'Berakhir',
					flex: 1,
                    sortable : false,
                    dataIndex: 'berakhir'
                },
                {
                    text     : 'Total Waktu Pelaksanaan',
					flex: 1,
					align	: 'right',
                    sortable : false,
                    dataIndex: 'total_waktu_pelaksanaan'
                },				
                {
                    text     : 'Tanggal Tender',
					flex: 1,
                    sortable : true,
                    renderer : Ext.util.Format.dateRenderer('d/m/Y'),
					align	 : 'center',
                    dataIndex: 'tgl_tender'
                },
            ],
			listeners:{
				beforerender:function(){
					storeProyek.load();
				}
			},							
			bbar: Ext.create('Ext.PagingToolbar', {
				store: storeProyek,
				displayInfo: true,
				displayMsg: 'Displaying Data {0} - {1} of {2}',
				emptyMsg: "No data to display"
			}),
			dockedItems: [
				{
					xtype:'toolbar',
					dock:'top',
					items:[
					{
						text:'Kembali',
						iconCls:'icon-back',
						handler:function(){
							winPilihProyek.hide();
						}
					},'->',
					{
						text:'Import Proyek Online',
						iconCls:'icon-import',
						handler:function(){
							winCekProyekOnline(storeProyek);
						}
					}
					]
				},
				{
					
					xtype: 'toolbar',
					dock: 'top',						
					items: [
						'->',
						{
							flex: 2,
							fieldLabel: 'Cari',
							labelWidth: 50,
							tooltip:'masukan nama proyek',
							emptyText: 'masukan nama proyek / no spk / lokasi ...',
							xtype: 'searchfield',
							name: 'cari_proyek',
							store: storeProyek,
							listeners: {
										keyup: function(e){ 
										}
								}
						}
					]
				}
			]			
        });	
		
		var winPilihProyek = Ext.create('Ext.Window', {
			title: 'Pilih Proyek',
			closeAction: 'hide',
			height: '60%',
			width: '70%',
			layout: 'fit',
			modal: true,
			items: [gridProyek]
		});				
		
		/* end pilih proyek */
		
		var detailsPanel = {
			id: 'details-panel',
			title: 'Informasi',
			minSize: 100,
			region: 'center',
			bodyStyle: 'padding-bottom:15px;background:#eee;',
			autoScroll: true,			
			html: '<p class="details-info">Silahkan mendaftarkan Proyek/Kapro, penambahan Item Analisa, Password Harga Satuan <br/>ke : SUHARTONO, Admin Simpro, Dept.Produksi KP <br/>No.HP : 08176424861 <br/>e-mail:suhartono@nindyakarya.co.id<hr/></p><p class="details-info">Jika anda mempunyai kendala / saran dalam penggunaan SIMPRO-NK, Silakan menghubungi kontak di bawah ini : <br/>Andri Rohim M. IT - Dept. SDM & Sistem <br/>Telp. 021-8093276 <br/>Hp. 081294280142 <br/>Pin BB. 23610EF7<br/>e-mail : m.it@nindyakarya.co.id<br/>chat via ym : <br/><hr/> atau ke: Octavianty Staff IT - Dept. SDM & Sistem<br/>Telp. 021-8093276 Hp. 081213626940<br/>e-mail : octavianty@nindyakarya.co.id<br/>chat via ym : </p>'
		};
						
		var clock = Ext.create('Ext.toolbar.TextItem', {text: Ext.Date.format(new Date(), 'g:i:s A')});
		
		<?php if(!empty($nama_tender)){ ?>
		addTab('RAT', '/simpro/rencana/entry_rat');
		<?php } ?>

		var toolbar = Ext.widget('toolbar', {
			items   : [
				{
					text: 'Date: <?=standard_date('DATE_RFC1123', time());?>',
				}, '-',
				{
					text: 'Login as: <?=$this->session->userdata('uname');?>',
					iconCls: 'icon-profile',
					handler: function()	
					{
						Ext.Msg.alert('Status','Change Profile');
					}					
				}, '-', 'Divisi: <?=$this->session->userdata('divisi');?>', '-',
				{
					text: 'Pilih Proyek',
					iconCls: 'icon-folder',
					handler: function()	
					{
						winPilihProyek.on('show', function(win) {	   
							storeProyek.load();
						});					
						winPilihProyek.show();						
						winPilihProyek.doLayout();
					}
				},'-',{
					text:'Import Tender',
					iconCls:'icon-import',
					handler:function(){
						import_tender();
					}
				}
				,'->',				
				{
					text: 'Tender terpilih: [<b> <?php echo !empty($nama_tender) ? $nama_tender : '--belum pilih tender--'; ?> </b>]',
				},'-',
				{
					text: 'Proyek terpilih: [<b> <?php echo !empty($nama_proyek) ? $nama_proyek : '--belum pilih proyek--' ; ?> </b>]',
				},'-',
				{					
					text: 'Logout',
					iconCls: 'icon-user',
					handler: function(btn)	
					{
						Ext.MessageBox.confirm('Logout', 'Apakah anda akan keluar dari aplikasi SIMPRO?',function(resbtn){
							if(resbtn == 'yes')
							{
								window.location = '<?=base_url();?>main/login/logout';
							}
						})
					}					
				},				
			],
			listeners: {
				render: {
					fn: function(){
						Ext.TaskManager.start({
							 run: function(){
								 Ext.fly(clock.getEl()).update(Ext.Date.format(new Date(), 'g:i:s A'));
							 },
							 interval: 1000
						});
					},
					delay: 100
				}
			}		
		});

		var toolbar2 = Ext.widget('toolbar', {
		
			items   : [
				{
				
					icon   : '<?=base_url();?>assets/images/rat.png',   
					text:'Tender',
					tooltip: 'Tender',
					handler:function(){
						addTab('RAT', '<?=base_url();?>rencana/entry_rat');
					}
				},'-',
				{
					icon   : '<?=base_url();?>assets/images/rab.png',
					text:'RAB',
					tooltip: 'RAB',
					handler:function(){
						addTab('RAB', '<?=base_url();?>rencana/rab/index');
					}
				}, '-',
				{
					icon   : '<?=base_url();?>assets/images/rap.png',
					text:'RAP',
					tooltip: 'RAP',
					handler:function(){
						addTab('RAP', '<?=base_url();?>rbk/rap_rapa');
					}
				}, '-',
				{
					icon   : '<?=base_url();?>assets/images/pengendalian.png',
					text:'Pengendalian',
					tooltip: 'Pengendalian',
					handler:function(){
						addTab('Kontrak Terkini', '<?=base_url();?>pengendalian/kontrak_terkini');
					}
				}, '-',
				{
					icon   : '<?=base_url();?>assets/images/scheduler.png',
					text:'Scheduler',
					tooltip: 'Scheduler',
					handler:function(){
						addTab('Scheduler', '<?=base_url();?>pengendalian/Schedule');
					}
				}, '-',
				{
					icon   : '<?=base_url();?>assets/images/transaksi.png',
					text:'Transaksi',
					tooltip: 'Transaksi',
					handler:function(){
						addTab('Pilih Leveransir / Toko', '<?=base_url();?>transaksi/toko');
					}
				}, '-',
				
				{
					icon   : '<?=base_url();?>assets/images/report.png',
					text:'Report',
					tooltip: 'Report',
					handler:function(){
						addTab('LBP-01', '<?=base_url();?>laporan/lbp01');
					}
				}, '-',{
					text:'Sinkronisasi',
					iconCls:'icon-Sync',
					handler:function(){
						Ext.Ajax.request({
							url:'<?php echo base_url(); ?>sinkronisasi/is_connected',
							success:function(response, opts){
								if (response.responseText == 'true') {
									Ext.MessageBox.alert("Informasi","Koneksi Ok..!!",function(){
										win_sync();
									});
								} else {
									Ext.MessageBox.alert("Informasi","Tidak Terkoneksi..!!");
								}
							},
							failure:function(){

							}
						});
					}
				},'-',
				{
					iconCls:'icon-user',
					text:'Ubah Password',
					handler:function(){
						win_ubah_pass();
					}
				}
				// '->','[',
				
				
				// 				'Sinkronisasi Database',
								
				// , ': <img src="<?=base_url();?>assets/images/custom1_bar.png" width="120px" height="10px">',

				// {
				// 	// icon   : '<?=base_url();?>assets/images/custom1_bar.png',
				// 	text:'Progress % : ',
				// 	tooltip: 'Progressbar',
				// },
				// {
				// 	icon   : '<?=base_url();?>assets/images/sinc_simpro.png',
				// 	text:'Sinkronisasi Simpro Online',
				// 	tooltip: 'Sinkronisasi Simpro Online',
				// }, '-',
				// {
				// 	icon   : '<?=base_url();?>assets/images/sinc_simak.png',
				// 	text:'Sinkronisasi Simak',
				// 	tooltip: 'Sinkronisasi Simak',
				// }
				// , ']',
				// , '     >>',
				

				// 'Connected Status',
								
				// , ':',
				// {
				// 	icon   : '<?=base_url();?>assets/images/connected.png',
				// 	text:'On',
				// 	tooltip: 'Connected Status',
				// }
				
			]	
		});
					
        var viewport = Ext.create('Ext.Viewport', {
            id: 'border-example',
            layout: 'border',
            items: [
				{
					region:'north',
					layout: 'border',
					id: 'top-header',
					border: false,
					frame: false,
					bodyStyle: 'background-color:#dfe8f5;',
					margins: '0 0 0 0',
					width: '100%',
					height:125,
					itemId:'view1',
					items: [
						{
							region: 'north',
							height: 73,
							width: '100%',
							items: [
								Ext.create('Ext.Component', {
									region: 'north',
									margins: '0 0 0 0',
									height: 73, 
									autoEl: {
										tag: 'div',
										html:'<div><img src="<?=base_url()?>assets/images/topheader.jpg" width="100%" height="100%"></div>'
									},
								})							
							]
						},
						{
							region: 'west',
							width: '100%',
							height: 25,
							items: [
								Ext.widget('panel', {
									renderTo: 'toolbar',
									margins: '5 5 5 5',
									tbar    : toolbar,
									border  : false,
									frame: false,
									width   : '100%'
								})
							]
						},
						{
							region: 'south',
							width: '100%',
							height: 25,
							itemId:'bar1',
							items: [
								Ext.widget('panel', {
									renderTo: 'toolbar2',
									margins: '5 5 5 5',
									tbar    : toolbar2,
									border  : false,
									frame: false,
									width   : '100%',
									itemId:'toolbar2'
								})
							]
						}					
					]
				},						
				{
					region: 'south',
					contentEl: 'south',
					height: 25,
					minSize: 25,
					maxSize: 25,
					split:false,
					collapsible: false,
					collapsed: false,
					frame: true,
					margins: '0 0 0 0'
				}, 
				{
					layout: 'border',
					id: 'layout-browser',
					region:'west', //
					border: false,
					split:true,
					margins: '2 0 5 5',
					width: 210,
					minSize: 100,
					maxSize: 500,
					items: [treePanel, detailsPanel]
				},			
					
					contentPanel
				],
				listeners:{
					beforerender:function(a,b,c,d){
						<?php
							if(($this->session->userdata('proyek_id') > 0)
							OR ($this->session->userdata('id_tender') > 0)
							OR !empty($_SESSION['proyek_id'])				
							) { ?>
								n = a.getComponent('view1');
								b = n.getComponent('bar1');
								b.getComponent('toolbar2').setDisabled(false);
						<?php }
							else { ?>
								n = a.getComponent('view1');
								b = n.getComponent('bar1');
								b.getComponent('toolbar2').setDisabled(true);
						<?php }
						?>
						
					}
				}
			});
			
    });

function win_ubah_pass(){
	var frm_ubah_pass = new Ext.form.Panel({
		layout: 'anchor',
		frame: false,
		url: '<?=base_url();?>main/ubah_pass/',
		bodyPadding: '5 5 0',	
		autoScroll:true,
		items: [{
			xtype:'textfield',
			inputType:'password',
			fieldLabel:'Password Lama',
			name:'password_lama',
			anchor:'100%',
			labelWidth:120,
			allowBlank:false
		},{
			xtype:'textfield',
			inputType:'password',
			fieldLabel:'Password Baru',
			name:'password_baru',
			anchor:'100%',
			labelWidth:120,
			allowBlank:false
		},{
			xtype:'textfield',
			inputType:'password',
			fieldLabel:'Ulang Password Baru',
			name:'ulang_password_baru',
			anchor:'100%',
			labelWidth:120,
			allowBlank:false
		}],
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
										frm_ubah_pass.getForm().reset();
										
										if (action.result.status == true) {
											window.location = location.URL;
										} else {
											win.hide();
										}
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
			},{
				text:'Cancel',
				handler:function(){
					frm_ubah_pass.getForm().reset();
					win.hide();
				}
			}]
	});

	var win = Ext.create('Ext.Window', {
		title: 'Ubah Password',
		closeAction: 'hide',
		height: '30%',
		width: '35%',
		layout: 'fit',
		modal: true,
		items:frm_ubah_pass
	}).show();
}

function win_sync(){
	var store = Ext.create('Ext.data.TreeStore', {
        proxy: {
            type: 'ajax',
            url: 'http://localhost/simpro/menu_sync.json'
        },
        sorters: [{
            property: 'leaf',
            direction: 'ASC'
        }, {
            property: 'text',
            direction: 'ASC'
        }]
    });

	var tree = Ext.create('Ext.tree.Panel', {
        store: store,
        rootVisible: false,
        useArrows: true,
        frame: false,
        width: 200,
        height: 250,
        // selModel: Ext.create('Ext.selection.CheckboxModel', {
        // 	mode: 'MULTI', 
        // 	multiSelect: true,
        // 	keepExisting: true,
        // }),        
        // multiSelect: false,
        singleExpand: false,
        dockedItems: [{
            xtype: 'toolbar',
            dock:'top',
            items: {
                text: 'Sinkronisasi On',
                handler: function(){
                    var records = tree.getView().getChecked(),
                        names = [];
                    	text = [];

                    Ext.Array.each(records, function(rec){
                        names.push(rec.get('id'));
                    });

                    Ext.Array.each(records, function(rec){
                        text.push(rec.get('text'));
                    });
                    
                    if (records.length > 0) {
                    	Ext.MessageBox.confirm("Konfirmasi","Apakah anda akan meng-sinkronisasi data yang di Centang?",function(res){
                    		if (res == 'yes') {
                    			Ext.Msg.wait("Loading...","Please Wait");
                    			Ext.Ajax.request({
			                    	url:'<?php echo base_url() ?>sinkronisasi/sinkron',
			                    	waitMsg:true,
			                    	params:{
			                    		'id' : names.join(',')
			                    	},
			                    	method:'post',
			                    	success:function(response,opts){
			                    		Ext.MessageBox.alert("Informasi","Data<br>"+text.join('<br />')+"<br>Telah di Sinkronisasi..",function(){
	            							win.hide();
			                    		});
			                    	}
			                    });
                    		}
                    	});
                    } else {
                    	Ext.MessageBox.alert("Informasi","Tidak ada data yang akan di Sinkronisasi..!");
                    }
                }
            }
        },{        	
            xtype:'toolbar',
            dock:'bottom',
            items:[
            	'->',
            	{
	            	text:'Cancel',
	            	handler:function(){
	            		win.hide();
	            	}
            	}
            ]
        }],
        listeners:{
        	checkchange : function(node, checked) {
			    node.cascadeBy(function(n) {
			        n.set('checked', checked);
			    });
			}
        }
    });

    var win = Ext.create('Ext.Window', {
			title: 'Sync',
			closeAction: 'hide',
			height: '50%',
			width: '30%',
			layout: 'fit',
			modal: true,
			items: [tree]
		}).show();	
}

function import_tender(){
	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
	
	var frmUploadAnalisa = Ext.widget({
		xtype: 'form',
		layout: 'form',
		url: '<?php echo base_url(); ?>main/import_tender',
		frame: false,
		bodyPadding: '5 5 0',
		width: 350,
		fieldDefaults: {
			msgTarget: 'side',
			labelWidth: 75
		},
		items: [
		{
			xtype: 'filefield',
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
						waitMsg: 'Import Tender ...',
						success: function(fp, o) {
							Ext.MessageBox.alert('Status','Upload file "'+ o.result.file + '" berhasil.', function()
							{
								window.location = document.URL;
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
		title: 'Import Tender',
		closeAction: 'hide',
		height: '20%',
		width: '30%',
		layout: 'fit',
		modal: true,
		items: frmUploadAnalisa
	}).show();	
}

function winCekProyekOnline(storeProyek){
	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
	
	var frmUploadAnalisa = Ext.widget({
		xtype: 'form',
		layout: 'form',
		url: '<?php echo base_url(); ?>main/is_connected',
		frame: false,
		bodyPadding: '5 5 0',
		width: 350,
		fieldDefaults: {
			msgTarget: 'side',
			labelWidth: 75
		},
		items: [
		{
			xtype: 'textfield',
			emptyText: 'silahkan Masukan No SPK...',
			afterLabelTextTpl: required,
			fieldLabel: 'No SPK',
			name: 'no_spk',
			allowBlank: false
		},				
		],

		buttons: [{
			text: 'Ok',
			handler: function(){            
				var form = this.up('form').getForm();
				if(form.isValid()){
					form.submit({
						waitMsg: 'Cek Proyek Online...',
						success: function(fp, o) {
							Ext.MessageBox.alert('Informasi',o.result.message,function(){
								if (o.result.proyek_cek == true) {
									winPasswordCekProyekOnline(o.result.no_spk,storeProyek);
								}
							});
						},
						failure: function(fp, o){								
							Ext.MessageBox.alert('Error','Proses Gagal..');
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
		title: 'Cek Proyek',
		closeAction: 'hide',
		height: '20%',
		width: '30%',
		layout: 'fit',
		modal: true,
		items: frmUploadAnalisa
	}).show();
}

function winPasswordCekProyekOnline(no_spk,storeProyek){
	var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
	
	var frmUploadAnalisa = Ext.widget({
		xtype: 'form',
		layout: 'form',
		url: '<?php echo base_url(); ?>main/cek_proyek_online/match_userpass/',
		frame: false,
		bodyPadding: '5 5 0',
		width: 350,
		fieldDefaults: {
			msgTarget: 'side',
			labelWidth: 75
		},
		items: [
		{
			xtype: 'textfield',
			afterLabelTextTpl: required,
			fieldLabel: 'No SPK',
			name: 'no_spk',
			allowBlank: false,
			hidden:true,
			value: no_spk
		},	
		{
			xtype: 'textfield',
			afterLabelTextTpl: required,
			fieldLabel: 'Username',
			name: 'username',
			allowBlank: false
		},	
		{
			xtype: 'textfield',
			afterLabelTextTpl: required,
			fieldLabel: 'Password',
			name: 'password',
			allowBlank: false,
			inputType:'password'
		},			
		],
		buttons: [{
			text: 'Ok',
			handler: function(){            
				var form = this.up('form').getForm();
				if(form.isValid()){
					form.submit({
						waitMsg: 'Processing...',
						success: function(fp, o) {
							Ext.MessageBox.alert('Informasi',o.result.message,function(){								
								storeProyek.load();
							});
						},
						failure: function(fp, o){								
							Ext.MessageBox.alert('Error','Proses Gagal..');
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
		title: 'Cek Username Proyek',
		closeAction: 'hide',
		height: '20%',
		width: '30%',
		layout: 'fit',
		modal: true,
		items: frmUploadAnalisa
	}).show();
}

function to_bulan(i){
	if (i==1){
		bul='Januari';
	} else if (i==2) {
		bul='Februari';	
	} else if (i==3) {
		bul='Maret';	
	} else if (i==4) {
		bul='April';	
	} else if (i==5) {
		bul='Mei';	
	} else if (i==6) {
		bul='Juni';	
	} else if (i==7) {
		bul='Juli';	
	} else if (i==8) {
		bul='Agustus';	
	} else if (i==9) {
		bul='September';	
	} else if (i==10) {
		bul='Oktober';	
	} else if (i==11) {
		bul='November';	
	} else if (i==12) {
		bul='Desember';	
	}
	return bul
}
    </script>
</head>
<body>
	<div id="loading-mask"></div>
	<div id="loading">
	  <div class="loading-indicator">
		SIMPRO Loading...
	  </div>
	</div>	
	
    <div id="toolbar" class="x-hide-display"></div>	
    <div id="toolbar2" class="x-hide-display"></div>
    <div id="north" class="x-hide-display"></div>
    <div id="west" class="x-hide-display"></div>
    <div id="center2" class="x-hide-display">
		<div id="form-ct"></div>
    </div>
    <div id="props-panel" class="x-hide-display" style="width:200px;height:200px;overflow:hidden;">
	tes2
    </div>
    <div id="south" class="x-hide-display">
        <p align="center" class="footer">&copy; 2013 - PT. NINDYA KARYA | Best viewed with browser <a style="text-decoration: none;" href="http://www.google.com/intl/id/chrome/browser/" target="_blank">Google Chrome</a></p>
    </div>
</body>
</html>
