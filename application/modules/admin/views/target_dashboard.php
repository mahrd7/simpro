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
        	{name: 'id', mapping: 'id'},
            {name: 'tahun', mapping: 'tahun'},
            {name: 'jumlah', mapping: 'jumlah'},            
            {name: 'kategori', mapping: 'kategori'}
         ]
    });

Ext.onReady(function() {
     
    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>admin/getdata/target_dashboard',
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
            {text: "Tahun", flex:1, sortable: true, dataIndex: 'tahun'},
            {text: "Jumlah", flex:1, sortable: true, dataIndex: 'jumlah'},
            {text: "Kategori", flex:1, sortable: true, dataIndex: 'kategori'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('id');
				var tahun = rec.get('tahun');
				var jumlah = rec.get('jumlah');
				var kategori = rec.get('kategori');
				// Ext.get('edit ').setValue( nama);
				frmedit.getForm().findField('editid').setValue(id);
            	frmedit.getForm().findField('edittahun').setValue(tahun);
				frmedit.getForm().findField('editjumlah').setValue(jumlah);
				frmedit.getForm().findField('editkategori').setValue(kategori);
            	winedit.show();
            }
        },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('id');
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>admin/deletedata/target_dashboard',
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
    grid.render(document.body);

    var frmadd = Ext.create('Ext.form.Panel', {    	
        url: '<?php echo base_url() ?>admin/insertdata/target_dashboard',
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
                fieldLabel: 'Tahun',
                anchor: '-5',
                name: 'tahun'
            },{
                xtype:'textfield',
                fieldLabel: 'Jumlah',
                anchor: '-5',
                name: 'jumlah'
            },
            // {
            //     xtype:'textfield',
            //     fieldLabel: 'Kategori',
            //     anchor: '-5',
            //     name: 'kategori'
            // },
            Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'Kategori',
							anchor: '-5',
							store: { 
								fields: ['value','text'],
								data:[
								{"value":"AAK","text":"Kontrak"},
								{"value":"PU","text":"PU"},
								{"value":"LABA","text":"LABA"}
								] 
							},
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
						})]
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
        url: '<?php echo base_url() ?>admin/editdata/target_dashboard',
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
                fieldLabel: 'Tahun',
                anchor: '-5',
                name: 'edittahun'
            },{
                xtype:'textfield',
                fieldLabel: 'Jumlah',
                anchor: '-5',
                name: 'editjumlah'
            },
            // {
            //     xtype:'textfield',
            //     fieldLabel: 'Kategori',
            //     anchor: '-5',
            //     name: 'editkategori'
            // }
             Ext.create('Ext.form.ComboBox', {
							fieldLabel: 'Kategori',
							anchor: '-5',
							store: { 
								fields: ['value','text'],
								data:[
								{"value":"AAK","text":"Kontrak"},
								{"value":"PU","text":"PU"},
								{"value":"LABA","text":"LABA"}
								] 
							},
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
						})
            ]
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
		width: 400,
		height: 240,
		layout: 'fit',
        items: frmadd 
    });

    winedit = Ext.create('Ext.Window', {
        title: 'Edit',
        closeAction: 'hide',
		width: 400,
		height: 240,
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