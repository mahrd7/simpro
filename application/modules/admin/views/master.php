<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript" language="JavaScript" src="../../assets/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript">

Ext.require([
    '*'
]);

	Ext.define('mdl_satuan', {
        extend: 'Ext.data.Model',
        fields: [
        	{name: 'id', mapping: 'id'},
            {name: 'satuan_nama', mapping: 'satuan_nama'},
            {name: 'user_update', mapping: 'user_update'},
            {name: 'tgl_update', mapping: 'tgl_update'},
            {name: 'ip_update', mapping: 'ip_update'},
            {name: 'divisi_update', mapping: 'divisi_update'},
            {name: 'waktu_update', mapping: 'waktu_update'}
         ]
    });

Ext.onReady(function() {
     
    var store_satuan = Ext.create('Ext.data.Store', {
        id: 'store_satuan',
        model: 'mdl_satuan',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>admin/getdata/master',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });
    store_satuan.load();

    var grid_satuan = Ext.create('Ext.grid.Panel', {
        id:'button-grid',
        store: store_satuan,
        columns: [
            // {text: "Id", width: 80, sortable: true, dataIndex: 'id'},
            {text: "Satuan", width: 80, sortable: true, dataIndex: 'satuan_nama'},
            {text: "User", width: 120, sortable: true, dataIndex: 'user_update'},
            {text: "Tanggal", width: 120, sortable: true, dataIndex: 'tgl_update'},
            {text: "IP", width: 120, sortable: true, dataIndex: 'ip_update'},
            {text: "Divisi", width: 120, sortable: true, dataIndex: 'divisi_update'},
            {text: "Waktu", width: 120, sortable: true, dataIndex: 'waktu_update'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store_satuan.getAt(rowIndex);
            	var id = rec.get('id');
				var satuan_nama = rec.get('satuan_nama');
				// Ext.get('editsatuan').setValue(satuan_nama);
				frmeditsatuan.getForm().findField('editsatuan').setValue(satuan_nama);
            	frmeditsatuan.getForm().findField('editid').setValue(id);
            	wineditsatuan.show();
            }
        },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store_satuan.getAt(rowIndex);
				var satuan_nama = rec.get('satuan_nama');
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>admin/deletedata/master',
									method: 'POST',
									params: {
										'satuan_nama' : satuan_nama
									},								
									success: function() {
									store_satuan.load();
									Ext.Msg.alert( "Status", "Delete successfully..!", function(){	
									});											
									},
									failure: function() {
									}
								});			   																			
							}
				});
            }
        }
        ],
        columnLines: true,
        width: '100%',
        height: '100%',
       	bbar: Ext.create('Ext.PagingToolbar', {
				store: store_satuan,
				displayInfo: true,
				displayMsg: 'Displaying data {0} - {1} of {2}',
				emptyMsg: "No data to display",
			}),
       	dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
            {
                text:'Add',
                handler: function(){
                	winaddsatuan.show();
                }
            }]
        }],
        listeners:{
				beforerender:function(){
					store_satuan.load();
				}
			}
    });
    grid_satuan.render(document.body);

    var frmaddsatuan = Ext.create('Ext.form.Panel', {    	
        url: '<?php echo base_url() ?>admin/insertdata/master',
        id:'frmaddsatuan',
        bodyStyle: 'padding:5px 5px 0',
        width: '100%',
        autoScroll: true,
        fieldDefaults: {
            labelAlign: 'top',
            msgTarget: 'side'
        },
        defaults: {
            border: false,
            xtype: 'panel',
            flex: 1,
            layout: 'anchor'
        },

        layout: 'hbox',
        items: [{
            items: [{
                xtype:'textfield',
                fieldLabel: 'Satuan',
                anchor: '-5',
                name: 'satuan'
            }]
        }],
        buttons: ['->', {
            text: 'Save',
            handler: function() {                    
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Master Data','Insert successfully..!');
                        	form.reset();                        	
                    		store_satuan.load();
                        }
                    });                            
            		winaddsatuan.hide();
                }  

            }
        }, {
            text: 'Cancel',
            handler: function(){
            	winaddsatuan.hide();
            }
        }]
    });

	var frmeditsatuan = Ext.create('Ext.form.Panel', {    	
        url: '<?php echo base_url() ?>admin/editdata/master',
        id:'frmeditsatuan',
        bodyStyle: 'padding:5px 5px 0',
        width: '100%',
        autoScroll: true,
        fieldDefaults: {
            labelAlign: 'top',
            msgTarget: 'side'
        },
        defaults: {
            border: false,
            xtype: 'panel',
            flex: 1,
            layout: 'anchor'
        },

        layout: 'hbox',
        items: [{
            items: [{
                xtype:'textfield',
                fieldLabel: 'Id',
                anchor: '-5',
                name: 'editid',
                hidden: true
            },{
                xtype:'textfield',
                fieldLabel: 'Satuan',
                anchor: '-5',
                name: 'editsatuan'
            }]
        }],
        buttons: ['->', {
            text: 'Save',
            handler: function() {                    
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Master Data','Update successfully..!');
                        	form.reset();                        	
                    		store_satuan.load();
                        }
                    });                            
            		wineditsatuan.hide();
                }  

            }
        }, {
            text: 'Cancel',
            handler: function(){
            	wineditsatuan.hide();
            }
        }]
    });

    winaddsatuan = Ext.create('Ext.Window', {
        title: 'Tambah Satuan',
        closeAction: 'hide',
		width: 300,
		height: 130,
		layout: 'fit',
        items: frmaddsatuan
    });

    wineditsatuan = Ext.create('Ext.Window', {
        title: 'Tambah Satuan',
        closeAction: 'hide',
		width: 300,
		height: 180,
		layout: 'fit',
        items: frmeditsatuan
    });
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>
