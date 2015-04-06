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
        	{name: 'alat_id', mapping: 'alat_id'},
        	{name: 'no_spk', mapping: 'no_spk'},
            {name: 'uraian_jenis_alat', mapping: 'uraian_jenis_alat'},
            {name: 'merk_model', mapping: 'merk_model'},  
            {name: 'type_penggerak', mapping: 'type_penggerak'},
            {name: 'kapasitas', mapping: 'kapasitas'},          
            {name: 'tgl', mapping: 'tgl'},
            {name: 'user_tambah', mapping: 'user_tambah'}
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
         url: '<?php echo base_url() ?>admin/getdata/daftar_peralatan',
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
            {text: "Uraian Jenis Alat", flex:1, sortable: true, dataIndex: 'uraian_jenis_alat'},
            {text: "Merk / Model", flex:1, sortable: true, dataIndex: 'merk_model'},
            {text: "Type / Penggerak", flex:1, sortable: true, dataIndex: 'type_penggerak'},
            {text: "Kapasitas", flex:1, sortable: true, dataIndex: 'kapasitas'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('alat_id');
				var uraian_jenis_alat = rec.get('uraian_jenis_alat');
				var merk_model = rec.get('merk_model');
				var type_penggerak = rec.get('type_penggerak');
				var kapasitas = rec.get('kapasitas');
				// Ext.get('edit ').setValue( nama);
				frmedit.getForm().findField('editid').setValue(id);
            	frmedit.getForm().findField('edituraian_jenis_alat').setValue(uraian_jenis_alat);
				frmedit.getForm().findField('editmerk_model').setValue(merk_model);				
            	frmedit.getForm().findField('edittype_penggerak').setValue(type_penggerak);
				frmedit.getForm().findField('editkapasitas').setValue(kapasitas);
            	winedit.show();
            }
        },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('alat_id');
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>admin/deletedata/daftar_peralatan',
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
        url: '<?php echo base_url() ?>admin/insertdata/daftar_peralatan',
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
                fieldLabel: 'Uraian Jenis Alat',
                anchor: '-5',
                name: 'uraian_jenis_alat'
            },{
                xtype:'textfield',
                fieldLabel: 'Merk / Model',
                anchor: '-5',
                name: 'merk_model'
            },{
                xtype:'textfield',
                fieldLabel: 'Type / Penggerak',
                anchor: '-5',
                name: 'type_penggerak'
            },{
                xtype:'textfield',
                fieldLabel: 'Kapasitas',
                anchor: '-5',
                name: 'kapasitas'
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
        url: '<?php echo base_url() ?>admin/editdata/daftar_peralatan',
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
                fieldLabel: 'Uraian Jenis Alat',
                anchor: '-5',
                name: 'edituraian_jenis_alat'
            },{
                xtype:'textfield',
                fieldLabel: 'Merk / Model',
                anchor: '-5',
                name: 'editmerk_model'
            },{
                xtype:'textfield',
                fieldLabel: 'Type / Penggerak',
                anchor: '-5',
                name: 'edittype_penggerak'
            },{
                xtype:'textfield',
                fieldLabel: 'Kapasitas',
                anchor: '-5',
                name: 'editkapasitas'
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
		width: 400,
		height: 280,
		layout: 'fit',
        items: frmadd 
    });

    winedit = Ext.create('Ext.Window', {
        title: 'Edit',
        closeAction: 'hide',
		width: 400,
		height: 280,
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