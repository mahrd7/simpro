<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Sistem Informasi Manajemen Proyek :: PT. Nindya Karya</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.gif" />
	<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/bootstrap.js"></script> -->
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/examples.js"></script>

	<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/styles.css" /> -->
<style>
.icon-rat {
    background-image:url(<?php echo base_url(); ?>assets/images/rat.png) !important;
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
	Ext.require([
		'*'
	]);	
	
	Ext.QuickTips.init();

	Ext.define('mdl_material', {
		extend: 'Ext.data.Model',
		fields: [
		'detail_material_id',
		'detail_material_kode',
		'detail_material_nama', 
		'detail_material_spesifikasi',
		'subbidang_kode',
		'subbidang_name',
		'detail_material_satuan',
		'detail_material_harga',
		'detail_material_propinsi',
		'tgl_update',
		'ip_update',
		'divisi_name',
		'waktu_update'
		],
	});

	Ext.define('combo', {
		extend: 'Ext.data.Model',
		fields: [
		'text',
		'value'
		],
	});
	
	var store_subbidang = Ext.create('Ext.data.Store', {
		model: 'combo',
		autoLoad: true,		
		proxy: {
			type: 'ajax',
			url: '<?php echo base_url() ?>admin/get_subbidang',
			reader: {
				type: 'json',
				root: 'data'
			}
		}		
	});	

	var store_satuan = Ext.create('Ext.data.Store', {
		model: 'combo',
		autoLoad: true,		
		proxy: {
			type: 'ajax',
			url: '<?php echo base_url() ?>admin/get_satuan',
			reader: {
				type: 'json',
				root: 'data'
			}
		}		
	});	
	
	Ext.onReady(function() {

		var store_material = Ext.create('Ext.data.Store', {
			model: 'mdl_material',
			autoLoad: true,		
			pageSize:50,
			proxy: {
				extraParams:{
					search:'',
					cbo:'500'
				},
				type: 'ajax',
				url: '<?php echo base_url() ?>admin/get_material',
				reader: {
					type: 'json',
					root: 'data'
				}
			},
			listeners:{
				beforeload:function(){
					bar = grid.getComponent('bar1');
					search = bar.getComponent('search').getValue();
					cbo = bar.getComponent('cbo').getValue();
					store_material.proxy.extraParams = {'search':search,'cbo':cbo};
				}
			}		
		});	

		var grid = Ext.create('Ext.grid.Panel',{
			store:store_material,
			width:'100%',
			height:'100%',
			plugins: Ext.create('Ext.grid.plugin.RowEditing', {
			clicksToMoveEditor: 2,
			autoCancel: false,
			listeners: {
				'edit': function () {
					var editedRecords = grid.getStore().getUpdatedRecords();
					Ext.Ajax.request({
						url: '<?=base_url();?>admin/material/edit',
						method: 'POST',
						params: {
							'detail_material_id':editedRecords[0].data.detail_material_id,
							'detail_material_kode':editedRecords[0].data.detail_material_kode,
							'detail_material_nama':editedRecords[0].data.detail_material_nama,
							'detail_material_spesifikasi':editedRecords[0].data.detail_material_spesifikasi,
							'detail_material_satuan':editedRecords[0].data.detail_material_satuan,
							'detail_material_harga':editedRecords[0].data.detail_material_harga,
							'subbidang_kode':editedRecords[0].data.subbidang_kode
						},								
						success: function(response) {
							var text = response.responseText;

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
					text: 'kode',
					width:100,
					dataIndex:'detail_material_kode',
					editor:{
						xtype:'textfield'
					}
				},
				{
					text: 'Nama',
					flex:1,
					dataIndex:'detail_material_nama',
					editor:{
						xtype:'textfield'
					}
				},
				{
					text: 'Spesifikasi',
					width:80,
					dataIndex:'detail_material_spesifikasi',
					editor:{
						xtype:'textfield'
					}
				},
				{
					text: 'Sumber Daya',
					width:120,
					dataIndex:'subbidang_kode',
					renderer:function(val){
						index = store_subbidang.findExact('value',val);
						value = store_subbidang.getAt(index).get('text');
						return value;
					},
					editor:{
						xtype:'combo',
						store:store_subbidang,
						displayField:'text',
						valueField:'value',
						query:'local'
					}
				},
				{
					text: 'Satuan',
					width:50,
					dataIndex:'detail_material_satuan',
					editor:{
						xtype:'combo',
						store:store_satuan,
						displayField:'text',
						valueField:'value',
						query:'local'
					}
				},
				{
					text: 'Harga',
					width:60,
					dataIndex:'detail_material_harga',
					editor:{
						xtype:'numberfield'
					}
				},
				{
					text: 'Provinsi',
					width:80,
					dataIndex:'detail_material_propinsi'
				},
				{
					text: 'Tanggal',
					width:80,
					dataIndex:'tgl_update'
				},
				{
					text: 'IP',
					width:80,
					dataIndex:'ip_update'
				},
				{
					text: 'Divisi',
					width:90,
					dataIndex:'divisi_name'
				},
				{
					text: 'Waktu',
					width:60,
					dataIndex:'waktu_update'
				},
				{
					xtype: 'actioncolumn',
                    width: 25,
					align: 'center',
					items:[
						{
							icon: '<?=base_url();?>assets/images/delete.gif',  
							tooltip: 'Delete',
							handler:function(grid, rowIndex, colIndex, d, e,f,g,h){
								val = f.data.detail_material_id;
								Ext.MessageBox.confirm('Informasi','Apakah anda akan menghapus item ini?',function(btn){
									if (btn == 'yes') {
										Ext.Ajax.request({
											url:'<?=base_url();?>admin/material/hapus',
											method:'post',
											params:{
												'id':val
											},
											success:function(){
												store_material.load();
											},
											failure:function(){
												Ext.MessageBox.alert("Peringatan..",'Terjadi kesalahan, Data gagal dihapus..!');
											}
										});
									}
								});
							}
						}
					]
				}
			],
			dockedItems:[{
				xtype:'toolbar',
				itemId:'bar1',
				items:[{ xtype: 'tbfill' },
				'Search : ',{
					xtype:'textfield',
					itemId:'search'
				},'-',{
					xtype:'combo',
					itemId:'cbo',
					store:store_subbidang,
					displayField:'text',
					valueField:'value',
					query:'local',
					emptyText:'Pilih..',
					width:250,
					value:'500'
				},{
					text:'Go>>',
					handler:function(){
						store_material.loadPage(1);
					}
				},
				{ xtype: 'tbfill' }]
			},{
				xtype:'toolbar',
				items:[
					{
						text:'Tambah',
						iconCls:'icon-add',
						handler:function(){							
							bar = grid.getComponent('bar1');
							cbo = bar.getComponent('cbo').getValue();

							Ext.Ajax.request({
								url:'<?=base_url();?>admin/get_last_material',
								method:'post',
								params:{
									'subbidang':cbo
								},
								success:function(response){
									last_kode = response.responseText;
									console.log(last_kode);
									tambah_material(cbo,last_kode);
								}
							});
						}
					}
				]
			}],
            hideCollapseTool: true,	
            viewConfig: {
                stripeRows: true,
                markDirty:false
            },
			bbar: Ext.create('Ext.PagingToolbar', {
				store: store_material,
				displayInfo: true,
				pageSize:50,
				displayMsg: 'Displaying Data {0} - {1} of {2}',
				emptyMsg: "No data to display"
			})
		});

		var viewport = Ext.create('Ext.Viewport', {
			layout:'fit',
			items:grid
		});
	});

	function tambah_material(subbidang,last_kode){
		var form = Ext.widget({
			xtype:'form',
			url:'<?=base_url();?>admin/material/tambah',
			layout: 'form',
			bodyPadding: '5 5 0',
			frame:false,
			autoScroll:true,
			items:[
			{
				xtype:'combo',
				name:'subbidang_kode',
				fieldLabel:'Subbidang ',
				store: store_subbidang,
				displayField:'text',
				valueField:'value',
				query:'local',
				allowBlank:false,
				emptyText:'Pilih..',
				value:subbidang,
				readOnly:true
			},
			{
				xtype:'textfield',
				name:'detail_material_kode',
				fieldLabel:'Kode ',
				allowBlank:false,
				value:last_kode
			},{
				xtype:'textfield',
				name:'detail_material_nama',
				fieldLabel:'Nama ',
				allowBlank:false
			},{
				xtype:'textarea',
				name:'detail_material_spesifikasi',
				fieldLabel:'Keterangan ',
				value:'-'
			},{
				xtype:'combo',
				name:'detail_material_satuan',
				fieldLabel:'Satuan ',
				store: store_satuan,
				displayField:'text',
				valueField:'value',
				query:'local',
				allowBlank:false,
				emptyText:'Pilih..',
				value:'Ls'
			},{
				xtype:'numberfield',
				name:'detail_material_harga',
				fieldLabel:'Harga ',
				value:0,
				minValue:0
			},{
				xtype:'textfield',
				name:'tgl_update',
				value:'<?php echo date("Y-m-d") ?>',
				hidden:true
			},{
				xtype:'textfield',
				name:'user_update',
				value:'<?php echo $this->session->userdata("uid") ?>',
				hidden:true
			},{
				xtype:'textfield',
				name:'divisi_update',
				value:'<?php echo $this->session->userdata("divisi_id") ?>',
				hidden:true
			},{
				xtype:'textfield',
				name:'ip_update',
				value:'<?php echo $this->session->userdata("ip_address") ?>',
				hidden:true
			},{
				xtype:'textfield',
				name:'waktu_update',
				value:'<?php echo date("H:i:s") ?>',
				hidden:true
			}],
			buttons:[{
				text:'Save',
				handler:function(){
					form = this.up('form').getForm();
					if (form.isValid()) {
						form.submit({
							waitMsg:true,
							success:function(){
								win.hide();
								Ext.MessageBox.alert('Informasi','Data telah disimpan..',function(){
									store_material.load();
								});
							},
							failure:function(){

							}
						})
						
					}
				}
			},{
				text:'Cancel',
				handler:function(){
					win.hide();
				}
			}]
		});

		var win = Ext.widget({
			xtype:'window',
			title:'Tambah Data Sumber Daya (Material)',
			closeAction:'hide',
			width:400,
			height:300,			
			layout: 'fit',
			resizable: true,
			modal: true,
			items: form
		}).show();
	}
</script>
</head>
<body>
</body>
</html>
