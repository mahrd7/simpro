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
		
</style>
<script type="text/javascript">
    Ext.require(['*']);
	
    Ext.onReady(function() {
        Ext.QuickTips.init();	
		  
        Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
		
		Ext.define('dataTenderMdl', {
			extend: 'Ext.data.Model',
			fields: [
				'id_proyek_rat','id_status_rat','nama_proyek','jenis_proyek','status',
				'lingkup_pekerjaan','waktu_pelaksanaan','waktu_pemeliharaan','nilai_pagu_proyek','nilai_penawaran',
				'lokasi_proyek','pemilik_proyek','konsultan_pelaksana','konsultan_pengawas','tanggal_tender','divisi'
			],
			idProperty: 'datatenderid'
		});

		var storeRAB = Ext.create('Ext.data.Store', {
			pageSize: 50,
			autoLoad: true,
			model: 'dataTenderMdl',
			remoteSort: true,
			proxy: {
				type: 'jsonp',
				url: '<?=base_url();?>rencana/get_data_rab_dashboard/<?php echo $id_tender; ?>',
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
        
		var gridRAB = Ext.create('Ext.grid.Panel', {
            title: 'Adjustment Data RAB',			
            viewConfig: {
                stripeRows: true
            },		
            hideCollapseTool: true,
            store: storeRAB,
			height: '100%',
			width: '100%',
			layout: 'fit',
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
								icon   : '<?=base_url();?>assets/images/accept.gif',  
								tooltip: 'Aproval RAB',
								handler: function(grid, rowIndex, colIndex) {
									var rec = storeRAB.getAt(rowIndex);
									var id = rec.get('id_proyek_rat');
									Ext.MessageBox.confirm('Approve RAB', 'Apakah anda yakin akan meng-approve RAB proyek "'+rec.get('nama_proyek')+'" ?',function(resbtn){
										if(resbtn == 'yes')
										{
											var box = Ext.MessageBox.wait('Please wait..', 'Performing Actions');

											Ext.Ajax.request({
												url: '<?=base_url();?>rencana/rab/approve_rab',
												method: 'POST',	
												timeout: 900000,
												params: {												
													'id_tender' : id
												},								
												success: function(response) {
													box.hide();
													Ext.Msg.alert("Status", response.responseText);
												},
												failure: function(response) {
													box.hide();
													Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
												}
											});
										}
									});
									/*
									var rec = storeRAB.getAt(rowIndex);
									ubahStatusTender(rec.get('id_proyek_rat'), rec.get('nama_proyek'));
									*/
								}
						}]
                },
                {
                    xtype: 'actioncolumn',
                    width: 25,
					align: 'center',									
                    items: [{
								icon   : '<?=base_url();?>assets/images/application_go.png',  
								tooltip: 'Adjust RAT -> RAB',
								handler: function(grid, rowIndex, colIndex) {
									var rec = storeRAB.getAt(rowIndex);
									var id_tender = rec.get('id_proyek_rat');
									window.location = '<?=base_url();?>rencana/rab/adjust_rab/' + id_tender;
								}
						}]
                },				
                {
                    text     : 'Divisi',
                    flex    : 1,
                    sortable : false,
                    dataIndex: 'divisi'
                },
                {
                    text     : 'Nama Proyek',
                    flex    : 3,
                    sortable : false,
                    dataIndex: 'nama_proyek'
                },
                {
                    text     : 'Owner',
                    flex    : 1,
                    sortable : true,
                    dataIndex: 'pemilik_proyek'
                },
                {
                    text     : 'Nilai Pagu Proyek',
                    flex    : 1,
                    sortable : true,
					align	: 'right',
                    dataIndex: 'nilai_pagu_proyek'
                },
                {
                    text     : 'Nilai Penawaran',
                    flex    : 1,
					align	: 'right',
                    sortable : true,
                    dataIndex: 'nilai_penawaran'
                },				
                {
                    text     : 'Jenis Proyek',
                    flex    : 1,
					align	: 'right',
                    sortable : true,
                    dataIndex: 'jenis_proyek'
                },				
                {
                    text     : 'Lokasi',
                    flex    : 1,
					align	: 'right',
                    sortable : true,
                    dataIndex: 'lokasi_proyek'
                },								
                {
                    text     : 'Tanggal Tender',
                    flex    : 1,
                    sortable : true,
                    renderer : Ext.util.Format.dateRenderer('m/d/Y'),
					align	 : 'center',
                    dataIndex: 'tanggal_tender'
                },
                {
                    text     : 'Status Tender',
                    flex    : 1,
                    sortable : true,
                    dataIndex: 'status'
                },				
            ],
			bbar: Ext.create('Ext.PagingToolbar', {
				store: storeRAB,
				displayInfo: true,
				displayMsg: 'Displaying Data {0} - {1} of {2}',
				emptyMsg: "No data to display"
			}),
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
        });
		
		var bviewPort = new Ext.Viewport({
			renderTo: Ext.getBody(),
			layout: "fit",
			items: [ gridRAB ]
		});
		bviewPort.doLayout();
		

    });
	
</script>
</head>
<body>

<!--
class="x-hide-display"
-->

<div id="grid-rab"></div>
</body>
</html>