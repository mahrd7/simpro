<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/examples.js"></script>
<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/styles.css" /> -->
<style>

p {
    margin:5px;
}

.footer {
font-size: 10px;
font-family: 'Arial'
}

.icon-new {
    background: url(<?php echo base_url(); ?>assets/images/new-icon.png) no-repeat 0 -1px;
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
    background-image:url(<?php echo base_url(); ?>assets/images/table.png ) !important;
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
				url: '<?=base_url();?>rbk/get_data_proyek_rap',
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
			renderTo: Ext.getBody(), //'grid-proyek',
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
                    items: [{
								icon   : '<?=base_url();?>assets/images/application_go.png',
								tooltip: 'Edit RAP',
								handler: function(grid, rowIndex, colIndex) {
									var rec = storeProyek.getAt(rowIndex);
									var pid = rec.get('proyek_id');
									window.location = '<?=base_url();?>rbk/edit_rap/' + pid;
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
			})	,
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
		/* end pilih proyek */
		
		
		/* RAP */
		
		/* END RAP */
		
	});
</script>
</head>
<body>
<div id="grid-proyek" class="x-hide-display"></div>
</body>
</html>