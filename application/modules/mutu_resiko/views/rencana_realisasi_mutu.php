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
        	{name: 'rr_id', mapping: 'rr_id'},
            {name: 'proyek_id', mapping: 'proyek_id'},
            {name: 'rr_uraian_rencana', mapping: 'rr_uraian_rencana'},            
            {name: 'rr_uraian_realisasi', mapping: 'rr_uraian_realisasi'},
            {name: 'rr_tgl', mapping: 'rr_tgl'},
            {name: 'user_id', mapping: 'user_id'},
            {name: 'rr_jenis', mapping: 'rr_jenis'},
            {name: 'rr_jenis_id', mapping: 'rr_jenis_id'}
         ]
    });

Ext.onReady(function() {

    var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
	
	var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>mutu_resiko/getdata/rencana_realisasi_mutu',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    var grid = Ext.create('Ext.grid.Panel', {
		title: 'Rencana & Realisasi Mutu',
        id:'button-grid',
        store: store,
        columns: [
            {text: "Uraian Rencana Mutu", flex:1, sortable: true, dataIndex: 'rr_uraian_rencana'},
            {text: "Uraian Realisasi Mutu", flex:1, sortable: true, dataIndex: 'rr_uraian_realisasi'},
            {text: "Jenis", flex:1, sortable: true, dataIndex: 'rr_jenis'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = store.getAt(rowIndex);
                frmedit.getForm().findField('editid').setValue(rec.get('rr_id')); 
                frmedit.getForm().findField('editjenis').setValue(rec.get('rr_jenis_id'));
                frmedit.getForm().findField('edituraian_rencana_mutu').setValue(rec.get('rr_uraian_rencana'));
                frmedit.getForm().findField('edituraian_realisasi_mutu').setValue(rec.get('rr_uraian_realisasi'));               
                winedit.show();
            }
        },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('rr_id');
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>mutu_resiko/deletedata/rencana_realisasi_mutu',
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
       	bbar: Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: store,
                             displayInfo: true
                     }),
       	dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
            {
                text:'Tambah',
                handler: function(){
                    winadd.show();
                }
            }]
        }]
    });
    store.load();
    grid.render(document.body);

    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>mutu_resiko/insertdata/rencana_realisasi_mutu',
        bodyStyle: 'padding:5px 5px 0',
        width: '100%',
        autoScroll: true,
        frame: false,
            items: [
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'Jenis',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_status_jenis', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Jenis...',
				name: 'jenis',
				typeAhead: true,
				triggerAction: 'all',
				enableKeyEvents:true,							
				selectOnFocus:true,							
				displayField: 'text',
				valueField: 'value',
				anchor: '-5',
				listeners: {
					 'select': function(combo, row, index) {
					},
				},
			}),
			/*{
                xtype:'combobox',
                fieldLabel: 'Jenis',
                anchor: '-5',
                name: 'jenis',
                store: storejenis,
                valueField: 'value',
                displayField: 'text',
                typeAhead: true,
                queryMode: 'local',
                emptyText: 'Pilih Jenis...',
            afterLabelTextTpl: required,
            allowBlank: false
            },*/{
                xtype:'textarea',
                fieldLabel: 'Uraian Rencana Mutu',
                anchor: '-5',
                name: 'uraian_rencana_mutu',
            afterLabelTextTpl: required,
            allowBlank: false
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
        url: '<?php echo base_url() ?>mutu_resiko/editdata/rencana_realisasi_mutu',
        id:'frmedit ',
        bodyStyle: 'padding:5px 5px 0',
        width: '100%',
        autoScroll: true,
        frame: false,
            items: [{
                xtype:'textfield',
                fieldLabel: 'Id',
                anchor: '-5',
                name: 'editid',
                hidden: true
            },
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'Jenis',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_status_jenis', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Jenis...',
				name: 'editjenis',
				typeAhead: true,
				triggerAction: 'all',
				enableKeyEvents:true,							
				selectOnFocus:true,							
				displayField: 'text',
				valueField: 'value',
				//queryMode: 'local',
				listeners: {
					 'select': function(combo, row, index) {
					},
				},
			}),/*{
                xtype:'combobox',
                fieldLabel: 'Jenis',
                anchor: '-5',
                name: 'editjenis',
                store: storejenis,
                valueField: 'value',
                displayField: 'text',
                typeAhead: true,
                queryMode: 'local',
                emptyText: 'Pilih Jenis...',
            afterLabelTextTpl: required,
            allowBlank: false
            },*/{
                xtype:'textarea',
                fieldLabel: 'Uraian Rencana Mutu',
                anchor: '-5',
                name: 'edituraian_rencana_mutu',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'Uraian Realisasi Mutu',
                anchor: '-5',
                name: 'edituraian_realisasi_mutu',
            afterLabelTextTpl: required,
            allowBlank: false
            }],
        buttons: ['->', {
            text: 'Update',
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

    var winadd = Ext.create('Ext.Window', {
        title: 'Tambah',
        closeAction: 'hide',
		modal: true,
        width: 400,
        height: 170,
        layout: 'fit',
        items: frmadd 
    });

    var winedit = Ext.create('Ext.Window', {
        title: 'Edit',
		modal: true,
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