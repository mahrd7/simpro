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
        	{name: 'propinsi_id', mapping: 'propinsi_id'},
            {name: 'kode_propinsi', mapping: 'kode_propinsi'},
            {name: 'propinsi', mapping: 'propinsi'},            
            {name: 'user_update', mapping: 'user_update'},
            {name: 'tgl_update', mapping: 'tgl_update'},
            {name: 'ip_update', mapping: 'ip_update'},
            {name: 'divisi_update', mapping: 'divisi_update'},
            {name: 'waktu_update', mapping: 'waktu_update'}
         ]
    });

    Ext.define('mdlkota', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'propinsi_id', mapping: 'propinsi_id'},
            {name: 'propinsi_induk', mapping: 'propinsi_induk'},
            {name: 'kode_propinsi', mapping: 'kode_propinsi'},
            {name: 'propinsi', mapping: 'propinsi'},            
            {name: 'user_update', mapping: 'user_update'},
            {name: 'tgl_update', mapping: 'tgl_update'},
            {name: 'ip_update', mapping: 'ip_update'},
            {name: 'divisi_update', mapping: 'divisi_update'},
            {name: 'waktu_update', mapping: 'waktu_update'}
         ]
    });

    Ext.define('mdlcombo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'value', mapping: 'propinsi'},
            {name: 'text', mapping: 'propinsi'}
         ]
    });

    var storecombo = Ext.create('Ext.data.Store', {
        id: 'storecombo',
        model: 'mdlcombo',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>admin/getdata/propinsi/',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    storecombo.load();

function gridkota(propinsi){
    var storekota = Ext.create('Ext.data.Store', {
        id: 'storekota',
        model: 'mdlkota',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>admin/getdatasearch/propinsi?param='+propinsi,
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    storekota.load();

    var gridkota = Ext.create('Ext.grid.Panel', {
        id:'gridkota',
        store: storekota,
        columns: [
            {text: "Propinsi", flex:1, sortable: true, dataIndex: 'propinsi_induk'},
            {text: "Kota", flex:1, sortable: true, dataIndex: 'propinsi'},
            {text: "Kode Kota", flex:1, sortable: true, dataIndex: 'kode_propinsi'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = storekota.getAt(rowIndex);
                var id = rec.get('propinsi_id');
                var kode = rec.get('kode_propinsi');
                var kota = rec.get('propinsi');
                var kategori = rec.get('propinsi_induk');
                // Ext.get('edit ').setValue( nama);
                frmeditkota.getForm().findField('editid').setValue(id);
                frmeditkota.getForm().findField('editkode').setValue(kode);
                frmeditkota.getForm().findField('editkota').setValue(kota);
                frmeditkota.getForm().findField('editkategori').setValue(kategori);
                wineditanalisa.show();
            }
        },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = storekota.getAt(rowIndex);
                var id = rec.get('propinsi_id');
                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                            if(resbtn == 'yes')
                            {
                                Ext.Ajax.request({
                                    url: '<?=base_url();?>admin/deletedata/propinsi',
                                    method: 'POST',
                                    params: {
                                        'id' :  id
                                    },                              
                                    success: function() {
                                    storekota.load();
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
                store: storekota,
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
                    frmaddkota.getForm().findField('kategori').setValue(propinsi);
                    winaddanalisa.show();
                }
            }]
        }],
        listeners:{
                beforerender:function(){
                    storekota.load();
                }
            }
    });

        winanalisa = Ext.create('Ext.Window', {
        title: 'Analisa',
        closeAction: 'hide',
        width: 600,
        height: 350,
        layout: 'fit',
        items: gridkota
    }).show();

        var frmaddkota = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>admin/insertdata/kota',
        id:'frmaddkota',
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
            items: [Ext.create('Ext.form.ComboBox', {
                            fieldLabel: 'Kategori',
                            anchor: '-5',
                            store: storecombo,
                            allowBlank: false,          
                            value: '',                          
                            emptyText: 'Pilih Kategori...',
                            name: 'kategori',
                            typeAhead: true,
                            triggerAction: 'all',
                            enableKeyEvents:true,                           
                            selectOnFocus:true,                         
                            displayField: 'text',
                            valueField: 'value'
                        }),
            {
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'kode'
            },{
                xtype:'textfield',
                fieldLabel: 'Kota',
                anchor: '-5',
                name: 'kota'
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
                            storecombo.load();
                            storekota.load();
                        }
                    });                            
                    winaddanalisa.hide();
                }  

            }
        }, {
            text: 'Cancel',
            handler: function(){
                winaddanalisa.hide();
            }
        }]
    });

        var frmeditkota = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>admin/editdata/kota',
        id:'frmeditkota ',
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
            },Ext.create('Ext.form.ComboBox', {
                            fieldLabel: 'Kategori',
                            anchor: '-5',
                            store: storecombo,
                            allowBlank: false,          
                            value: '',                          
                            emptyText: 'Pilih Kategori...',
                            name: 'editkategori',
                            typeAhead: true,
                            triggerAction: 'all',
                            enableKeyEvents:true,                           
                            selectOnFocus:true,                         
                            displayField: 'text',
                            valueField: 'value'
                        }),
            {
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'editkode'
            },{
                xtype:'textfield',
                fieldLabel: 'Kota',
                anchor: '-5',
                name: 'editkota'
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
                            storecombo.load();
                            storekota.load();
                        }
                    });                            
                    wineditanalisa.hide();
                }  

            }
        }, {
            text: 'Cancel',
            handler: function(){
                wineditanalisa.hide();
            }
        }]
    });

    var winaddanalisa = Ext.create('Ext.Window', {
        title: 'Tambah',
        closeAction: 'hide',
        width: 300,
        height: 240,
        layout: 'fit',
        items: frmaddkota 
    });

    var wineditanalisa = Ext.create('Ext.Window', {
        title: 'Edit',
        closeAction: 'hide',
        width: 300,
        height: 240,
        layout: 'fit',
        items: frmeditkota 
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
         url: '<?php echo base_url() ?>admin/getdata/propinsi',
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
            {text: "propinsi Kode", flex:1, sortable: true, dataIndex: 'kode_propinsi'},
            {text: "propinsi Nama", flex:1, sortable: true, dataIndex: 'propinsi'},
            {text: "User", flex:1, sortable: true, dataIndex: 'user_update'},
            {text: "Tanggal", flex:1, sortable: true, dataIndex: 'tgl_update'},
            {text: "IP", flex:1, sortable: true, dataIndex: 'ip_update'},
            {text: "Divisi", flex:1, sortable: true, dataIndex: 'divisi_update'},
            {text: "Waktu", flex:1, sortable: true, dataIndex: 'waktu_update'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('propinsi_id');
				var kode = rec.get('kode_propinsi');
				var nama = rec.get('propinsi');
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
            	var id = rec.get('propinsi_id');
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>admin/deletedata/propinsi',
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
                var propinsi = rec.get('propinsi');
                gridkota(propinsi);
            }
        }
        ],
        columnLines: true,
        width: '100%',
        height: '100%',
       	bbar: Ext.create('Ext.PagingToolbar', {
				store: store,
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

    var frmadd = Ext.create('Ext.form.Panel', {    	
        url: '<?php echo base_url() ?>admin/insertdata/propinsi',
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
        url: '<?php echo base_url() ?>admin/editdata/propinsi',
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

    grid.render(document.body);

});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>