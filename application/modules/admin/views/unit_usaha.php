<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript" language="JavaScript" src="../../assets/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript">

Ext.require([
    '*'
]);

	Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
        	{name: 'divisi_id', mapping: 'divisi_id'},
            {name: 'divisi_kode', mapping: 'divisi_kode'},
            {name: 'divisi_name', mapping: 'divisi_name'},            
            {name: 'user_update', mapping: 'user_update'},
            {name: 'tgl_update', mapping: 'tgl_update'},
            {name: 'ip_update', mapping: 'ip_update'},
            {name: 'divisi_update', mapping: 'divisi_update'},
            {name: 'waktu_update', mapping: 'waktu_update'}
         ]
    });

    Ext.define('mdldivisipropinsi', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'divisi_kode', mapping: 'divisi_kode'},
            {name: 'propinsi', mapping: 'propinsi'}
         ]
    });

    Ext.define('mdlpropinsi', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'propinsi', mapping: 'propinsi'}
         ]
    });

function griddivisipropinsi(kode){
    var storedivpro = Ext.create('Ext.data.Store', {
        id: 'storedivpro',
        model: 'mdldivisipropinsi',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>admin/getdatasearch/unit_usaha?param='+kode,
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });
    storedivpro.load();

    var storepro = Ext.create('Ext.data.Store', {
        id: 'storepro',
        model: 'mdlpropinsi',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>admin/getdatasearch/unit_usaha_pro',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });
    storepro.load();

    var griddivpro = Ext.create('Ext.grid.Panel', {
        id:'griddivpro',
        store: storedivpro,
        columns: [
            {text: "Kode", flex:1, sortable: true, dataIndex: 'divisi_kode'},
            {text: "Propinsi", flex:1, sortable: true, dataIndex: 'propinsi'}
        ],
        columnLines: true,
        width: '100%',
        height: '100%',
        bbar: Ext.create('Ext.PagingToolbar', {
                store: storedivpro,
                displayInfo: true,
                displayMsg: 'Displaying data {0} - {1} of {2}',
                emptyMsg: "No data to display",
            }),
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
            {
                text:'Edit',
                handler: function(){
                    wineditanalisa.show();
                }
            },{
                text:'Cancel',
                handler: function(){
                    winanalisa.hide();
                }
            }]
        }],
        listeners:{
                beforerender:function(){
                    storedivpro.load();
                }
            }
    });

    var sm = Ext.create('Ext.selection.CheckboxModel');
    var grideditdivpro = Ext.create('Ext.grid.Panel', {
        id:'grideditdivpro',
        store: storepro,
        selModel: sm,
        columns: [
            {text: "Propinsi", flex:1, sortable: true, dataIndex: 'propinsi'}
        ],
        columnLines: true,
        width: '100%',
        height: '100%',
        bbar: Ext.create('Ext.PagingToolbar', {
                store: storedivpro,
                displayInfo: true,
                displayMsg: 'Displaying data {0} - {1} of {2}',
                emptyMsg: "No data to display",
            }),
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
            {
                text:'Add',
                handler: function(){
                }
            },{
                text:'Cancel',
                handler: function(){
                    wineditanalisa.hide();
                }
            }]
        }],
        listeners:{
                beforerender:function(){
                    storedivpro.load();
                }
            }
    });

    winanalisa = Ext.create('Ext.Window', {
        title: 'Analisa',
        closeAction: 'hide',
        width: 400,
        height: 300,
        layout: 'fit',
        items: griddivpro 
    }).show();

    wineditanalisa = Ext.create('Ext.Window', {
        title: 'Analisa',
        closeAction: 'hide',
        width: 400,
        height: 300,
        layout: 'fit',
        items: grideditdivpro
    });
}

Ext.onReady(function() {
     
    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>admin/getdata/unit_usaha',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });
    store.load();

    var grid = Ext.create('Ext.grid.Panel', {
        id:'button-grid',
        store: store,
        columns: [
            {text: "unit_usaha Kode", flex:1, sortable: true, dataIndex: 'divisi_kode'},
            {text: "unit_usaha Nama", flex:1, sortable: true, dataIndex: 'divisi_name'},
            {text: "User", flex:1, sortable: true, dataIndex: 'user_update'},
            {text: "Tanggal", flex:1, sortable: true, dataIndex: 'tgl_update'},
            {text: "IP", flex:1, sortable: true, dataIndex: 'ip_update'},
            {text: "Divisi", flex:1, sortable: true, dataIndex: 'divisi_update'},
            {text: "Waktu", flex:1, sortable: true, dataIndex: 'waktu_update'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('divisi_id');
				var kode = rec.get('divisi_kode');
				var nama = rec.get('divisi_name');
				// Ext.get('edit ').setValue( nama);
				frmedit.getForm().findField('editid').setValue(id);
            	frmedit.getForm().findField('editkode').setValue(kode);
				frmedit.getForm().findField('editnama').setValue(nama);
            	winedit.show();
            }
        },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('divisi_id');
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>admin/deletedata/unit_usaha',
									method: 'POST',
									params: {
										'id' :  id
									},								
									success: function() {
									store.load();
									Ext.Msg.alert( "Status", "Delete successfully..!", function(){	
									});											
									},
									failure: function() {
									}
								});			   																			
							}
				});
            }
        },
        	{text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/application_go.png',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
                var kode = rec.get('divisi_kode');
                griddivisipropinsi(kode);
            }
        }
        ],
        columnLines: true,
        width: '100%',
        height: '100%',
       	bbar: Ext.create('Ext.PagingToolbar', {
				store: store,
                pageSize: 50,
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
                	winadd.show();
                }
            }]
        }],
        listeners:{
				beforerender:function(){
					store.load();
				}
			}
    });
    grid.render(document.body);

    var frmadd = Ext.create('Ext.form.Panel', {    	
        url: '<?php echo base_url() ?>admin/insertdata/unit_usaha',
        id:'frmadd ',
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
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'kode'
            },{
                xtype:'textfield',
                fieldLabel: 'Nama',
                anchor: '-5',
                name: 'nama'
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
                    		store.load();
                        }
                    });                            
            		winadd.hide();
                }  

            }
        }, {
            text: 'Cancel',
            handler: function(){
            	winadd.hide();
            }
        }]
    });

	var frmedit = Ext.create('Ext.form.Panel', {    	
        url: '<?php echo base_url() ?>admin/editdata/unit_usaha',
        id:'frmedit ',
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
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'editkode'
            },{
                xtype:'textfield',
                fieldLabel: 'Nama',
                anchor: '-5',
                name: 'editnama'
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
                    		store.load();
                        }
                    });                            
            		winedit.hide();
                }  

            }
        }, {
            text: 'Cancel',
            handler: function(){
            	winedit.hide();
            }
        }]
    });

    winadd = Ext.create('Ext.Window', {
        title: 'Tambah',
        closeAction: 'hide',
		width: 300,
		height: 200,
		layout: 'fit',
        items: frmadd 
    });

    winedit = Ext.create('Ext.Window', {
        title: 'Edit',
        closeAction: 'hide',
		width: 300,
		height: 200,
		layout: 'fit',
        items: frmedit 
    });
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>