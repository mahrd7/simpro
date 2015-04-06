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
		'*',
		'Ext.ux.form.SearchField',
		'Ext.ux.TabCloseMenu',
    	'Ext.ux.form.MultiSelect'
		]);

	Ext.Ajax.timeout = 3600000;

	Ext.define('combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'value', mapping: 'value'},
            {name: 'text', mapping: 'text'}
         ]
    });

	store_divisi = Ext.create('Ext.data.Store', {
        model: 'combo',
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url(); ?>admin/get_combo/divisi',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: true
    });

    store_jabatan = Ext.create('Ext.data.Store', {
        model: 'combo',
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url(); ?>admin/get_combo/jabatan',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: true
    });

    store_peran = Ext.create('Ext.data.Store', {
        model: 'combo',
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url(); ?>admin/get_combo/peran',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: true
    });

    store_proyek = Ext.create('Ext.data.Store', {
        model: 'combo',
        extraParams:'param_proyek',
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url(); ?>admin/get_combo/proyek',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: true
    });
	//Ext.require('Ext.ux.TabCloseMenu');	
	
	Ext.onReady(function() {
		var grid_proyek = Ext.create('Ext.grid.Panel', {
			selModel: Ext.create('Ext.selection.CheckboxModel', {
                    checkOnly: true,
                    mode: 'multi',
                    listeners:{
                    	select:function(a,record,index){
                    		var sel = grid_proyek.getSelectionModel().getSelection(),proyek_id = [];
                    		Ext.Array.each(sel, function(rec){
								proyek_id.push(rec.get('value'));
							});
							proyek_id_join = proyek_id.join(',');
							frmproyek.getComponent('no_spk').setValue(proyek_id_join);
                    	}
                    }
                }),
	        frame:false,
	        store: store_proyek,
	        columns: [
	            {text: "Nama Proyek", flex:1, sortable: false, dataIndex: 'text'},
	        ],
	        columnLines: true,
	        width: '100%',
	        height: '100%'
	    });

		var frmproyek = new Ext.form.Panel({
			layout: {
		        type: 'vbox',
		        align: 'center',
        		pack: 'center'
		     },
			frame: true,
			title: 'FORM DATA USER',
			url: '<?=base_url();?>admin/update_user/',
			bodyPadding: '5 5 0',			
			fieldDefaults: {
				labelWidth: 120,
				width: 400
			},		
			align:'center',
			autoScroll:true,
			items: [
			{
				xtype:'filefield',
				fieldLabel: 'Foto ',
				name: 'foto',
			},
			{
				xtype:'combo',
				fieldLabel: 'Unit Usaha ',
				name: 'kode_entitas',
				store:store_divisi,
				valueField:'value',
				displayField: 'text',
				listeners:{
					change:function(val){
						store_proyek.proxy.extraParams = {'param_proyek':val.getValue()};
						store_proyek.load();
					}
				}
			},
			// {
			// 	xtype:'label',
			// 	text:'Proyek : ',
			// },
			// {
			// 	height: 140,
   //              width: 500,
   //              layout: 'fit',
   //              itemId:'grid_1',
   //              autoScroll: true,
   //              bodyStyle: 'margin:5px 5px 0px 0px; border: 1px;',
			// 	items: grid_proyek
			// },
			{
	            xtype: 'multiselect',
	            fieldLabel: 'Proyek ',
	            name: 'no_spk',
	            allowBlank: false,
	            store: store_proyek,
	            valueField: 'value',
	            displayField: 'text',
	            ddReorder: true
	        },
			// {
			// 	xtype:'textfield',
			// 	name:'no_spk',
			// 	itemId:'no_spk',
			// 	// hidden:true
			// },
			{
				xtype:'checkbox',
				fieldLabel:'Check All Proyek ',
				name:'proyek_check',
				itemId:'proyek_check'
			},
			{
				xtype:'textfield',
				fieldLabel:'User Id ',
				name:'user_name',
				readOnly:true
			},
			// {
			// 	xtype:'textfield',
			// 	fieldLabel:'Password ',
   //          	inputType: 'password',
			// 	name:'password'
			// },
			{
				xtype:'textfield',
				fieldLabel:'First Name',
				name:'first_name'
			},
			{
				xtype:'textfield',
				fieldLabel:'Last Name ',
				name:'last_name'
			},
			{
				xtype:'combo',
				fieldLabel: 'Jabatan ',
				name: 'jabatan',
				store:store_jabatan,
				valueField:'value',
				displayField: 'text'
			},
			{
				xtype:'textfield',
				fieldLabel: 'NIP ',
				name: 'nip',
			},
			{
				xtype:'textfield',
				vtype: 'email',
				fieldLabel: 'Email ',
				name: 'email',
			},
			{
				xtype:'textfield',
				fieldLabel: 'No. Hp ',
				name: 'no_hp',
			},
			{
				xtype:'datefield',
				fieldLabel: 'Masa Jabatan ',
				name: 'tanggal_masuk',
			},
			{
				xtype:'combo',
				fieldLabel: 'Peran ',
				name: 'level_akses',
				store:store_peran,
				valueField:'value',
				displayField: 'text'
			}
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
										formload();
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
				text: 'Reload',
				handler: function() {
					formload();
				}
			}]				
		});

		formload();

		function formload(){
			frmproyek.load(
				{
					url: '<?=base_url();?>admin/get_combo/user',
					success:function(a,b){
						proyek_check=b.result.proyek_check;
						no_spk=b.result.no_spk;
						if (proyek_check == 'ALL') {
							frmproyek.getComponent('proyek_check').setValue(true);
						} else {
							frmproyek.getComponent('proyek_check').setValue(false);
						}
					}
				}		
			);
		}		

		var viewport = Ext.create('Ext.Viewport', {
	        layout: 'fit',
	        items: [{ 
	            height: '100%', 
	            width:'100%',  
	            layout: 'fit',
	            items: frmproyek
	        }]
	    });

	});
</script>
</head>
<body>
</body>
</html>
