<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/ext-4.1.1a/resources/css/ext-all.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/ext-4.1.1a/examples/toolbar/toolbars.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/ext-4.1.1a/ext-all.js"></script>
<script type="text/javascript">

Ext.require([
    '*'
]);

Ext.define('Person', {
    extend: 'Ext.data.Model',
    fields: [{
        name: 'id',
        type: 'int',
        useNull: true
    }, 'email', 'first', 'last'],
    validations: [{
        type: 'length',
        field: 'email',
        min: 1
    }, {
        type: 'length',
        field: 'first',
        min: 1
    }, {
        type: 'length',
        field: 'last',
        min: 1
    }]
});

Ext.onReady(function() {

    var formPanel = Ext.create('Ext.form.Panel', {
        frame: true,
		frameBorder: 0,
		//layout: 'fit',
        //title: 'RAT',
        width: '100%',
        bodyPadding: 5,
		tbar: [{
			xtype:'buttongroup',
			items: [{
				text: 'Print',
				iconCls: 'print',
				scale: 'small'
			},{
				text: 'Donload as Pdf',
				iconCls: 'print',
				scale: 'small'
			},]
		}],			
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },

        items: [{
            xtype: 'textfield',
            name: 'textfield1',
            fieldLabel: 'Divisi',
            value: '',
			emptyText: 'divisi'
        }, {
            xtype: 'hiddenfield',
            name: 'hidden1',
            value: 'Hidden field value'
        },
		{
            xtype: 'textfield',
            name: 'textfield1',
            fieldLabel: 'Proyek',
            value: '',
			emptyText: 'proyek'
        },		
		{
            xtype: 'textfield',
            name: 'textfield1',
            fieldLabel: 'Waktu pelaksanaan',
            value: '',
			emptyText: 'waktu pelaksanaan proyek'
        },				
		{
            xtype: 'textfield',
            name: 'textfield1',
            fieldLabel: 'Pagu',
            value: '',
			emptyText: 'nilai pagu'
        },						
		{
            xtype: 'textfield',
            name: 'textfield1',
            fieldLabel: 'Nilai kontrak',
            value: '',
			emptyText: 'nilai kontrak'
        },								
		{
            xtype: 'textfield',
            name: 'textfield1',
            fieldLabel: 'Nilai kontrak (excl. PPN)',
            value: '',
			emptyText: 'nilai kontrak blm PPN'
        },										
		{
            xtype: 'textfield',
            name: 'textfield1',
            fieldLabel: 'Masa kontrak',
            value: '',
        },										
		{
            xtype: 'textareafield',
            name: 'textarea1',
            fieldLabel: 'Keterangan',
            value: '',
			emptyText: 'keterangan tentang proyek'
        }, {
            xtype: 'displayfield',
            name: 'displayfield1',
            fieldLabel: 'Display field',
            value: 'Display field <span style="color:green;">value</span>'
        }],
		
		buttons: [{
            text: 'Save',
            handler: function() {
                this.up('form').getForm().isValid();
            }
        },{
            text: 'Cancel',
            handler: function() {
                this.up('form').getForm().reset();
            }
        }]    
		
	});

    //formPanel.render('form-ct');
	
	var indirectcost = Ext.create('Ext.Panel', {
		title: 'Indirect Cost',
		html: '&lt;empty panel&gt;',
		cls:'empty'
	});

	var varcost = Ext.create('Ext.Panel', {
		title: 'Variable Cost',
		html: '&lt;empty panel&gt;',
		cls:'empty'
	});

	var fvarcost = Ext.create('Ext.Panel', {
		title: 'Fixed Variable Cost',
		html: '&lt;empty panel&gt;',
		cls:'empty'
	});
		
	// grid 1

    var store = Ext.create('Ext.data.Store', {
        autoLoad: true,
        autoSync: true,
        model: 'Person',
        proxy: {
            type: 'rest',
            url: '/ext-4.1.1a/examples/restful/app.php/users',
            reader: {
                type: 'json',
                root: 'data'
            },
            writer: {
                type: 'json'
            }
        },
        listeners: {
            write: function(store, operation){
                var record = operation.getRecords()[0],
                    name = Ext.String.capitalize(operation.action),
                    verb;
                    
                    
                if (name == 'Destroy') {
                    record = operation.records[0];
                    verb = 'Destroyed';
                } else {
                    verb = name + 'd';
                }
                Ext.example.msg(name, Ext.String.format("{0} user: {1}", verb, record.getId()));
                
            }
        }
    });
    
    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing');
    
    var grd_directcost = Ext.create('Ext.grid.Panel', {
        plugins: [rowEditing],
        width: 400,
        height: 300,
        frame: true,
        title: 'Direct Cost',
        store: store,
        frame: false,
		layout: 'fit',
        //iconCls: 'icon-user',
		columnLines: true,		
		hideCollapseTool: true,		
        columns: [{
            text: 'ID',
            width: 40,
            sortable: true,
            dataIndex: 'id'
        }, {
            text: 'Email',
            flex: 1,
            sortable: true,
            dataIndex: 'email',
            field: {
                xtype: 'textfield'
            }
        }, {
            header: 'First',
            width: 80,
            sortable: true,
            dataIndex: 'first',
            field: {
                xtype: 'textfield'
            }
        }, {
            text: 'Last',
            width: 80,
            sortable: true,
            dataIndex: 'last',
            field: {
                xtype: 'textfield'
            }
        }],
		viewConfig: {
			stripeRows: true
		},
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text: 'Add',
                iconCls: 'icon-add',
                handler: function(){
                    // empty record
                    store.insert(0, new Person());
                    rowEditing.startEdit(0, 0);
                }
            }, '-', {
                itemId: 'delete',
                text: 'Delete',
                iconCls: 'icon-delete',
                disabled: true,
                handler: function(){
                    var selection = grd_directcost.getView().getSelectionModel().getSelection()[0];
                    if (selection) {
                        store.remove(selection);
                    }
                }
            }]
        }]
    });
	
    grd_directcost.getSelectionModel().on('selectionchange', function(selModel, selections){
        grd_directcost.down('#delete').setDisabled(selections.length === 0);
    });
	
	var rw1 = Ext.create('Ext.grid.plugin.RowEditing');
	
    var grd_indirectcost = Ext.create('Ext.grid.Panel', {
        plugins: [rw1],
        width: 400,
        height: 300,
        frame: true,
        title: 'inDirect Cost',
        store: store,
        frame: false,
		layout: 'fit',
        //iconCls: 'icon-user',
		columnLines: true,		
		hideCollapseTool: true,		
        columns: [{
            text: 'ID',
            width: 40,
            sortable: true,
            dataIndex: 'id'
        }, {
            text: 'Email',
            flex: 1,
            sortable: true,
            dataIndex: 'email',
            field: {
                xtype: 'textfield'
            }
        }, {
            header: 'First',
            width: 80,
            sortable: true,
            dataIndex: 'first',
            field: {
                xtype: 'textfield'
            }
        }, {
            text: 'Last',
            width: 80,
            sortable: true,
            dataIndex: 'last',
            field: {
                xtype: 'textfield'
            }
        }],
		viewConfig: {
			stripeRows: true
		},
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text: 'Add',
                iconCls: 'icon-add',
                handler: function(){
                    // empty record
                    store.insert(0, new Person());
                    rowEditing.startEdit(0, 0);
                }
            }, '-', {
                itemId: 'delete',
                text: 'Delete',
                iconCls: 'icon-delete',
                disabled: true,
                handler: function(){
                    var selection = grd_indirectcost.getView().getSelectionModel().getSelection()[0];
                    if (selection) {
                        store.remove(selection);
                    }
                }
            }]
        }]
    });
	
    grd_indirectcost.getSelectionModel().on('selectionchange', function(selModel, selections){
        grd_indirectcost.down('#delete').setDisabled(selections.length === 0);
    });
	
	var vari = Ext.create('Ext.Panel', {
		title: 'RAT Project Calculation',
		collapsible: false,
		region:'center',		
		margins:'5 0 5 5',
		split:true,
		width: 210,
		layout:'accordion',
		items: [grd_directcost, grd_indirectcost, fvarcost, varcost],
	});
	
    var action = Ext.create('Ext.Action', {
        text: 'Action 1',
        iconCls: 'icon-add',
        handler: function(){
            Ext.example.msg('Click', 'You clicked on "Action 1".');
        }
    });

	var viewport = Ext.create('Ext.Viewport', {
		layout:'border',
		items:[
			{
				title: 'Project Properties',
				region:'north',
				collapsible: true,				
				margins:'1 1 1 0',
				cls:'empty',
                animCollapse: true,
                collapsible: true,
                split: true,				
				items: [formPanel],
			},
			vari, 
		]
	});	
	
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>
