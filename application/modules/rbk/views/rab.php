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
                        
    Ext.Ajax.timeout = 3600000;
    Ext.override(Ext.form.Basic, {     timeout: Ext.Ajax.timeout / 1000 });
    Ext.override(Ext.data.proxy.Server, {     timeout: Ext.Ajax.timeout });
    Ext.override(Ext.data.Connection, {     timeout: Ext.Ajax.timeout });

    Ext.define('mdl_cbo', {
        extend: 'Ext.data.Model',
        fields: [
        {name: 'name', mapping: 'name'},
        {name: 'value', mapping: 'value', type:'string'}
        ]
    });              

    var store_divisi = Ext.create('Ext.data.Store', {
        model: 'mdl_cbo',
        proxy: { 
            type: 'ajax', 
            url: '<?=base_url();?>main/get_divisi', 
            reader: { 
                root: 'data',
                type: 'json' 
            } 
        },
        remoteSort: true,
        autoLoad: false
    });

    var store_pilpro = Ext.create('Ext.data.Store', {
        model: 'mdl_cbo',
        proxy: { 
            type: 'ajax', 
            url: '<?=base_url();?>rbk/rab_analisa/get_data_proyek',
            reader: { 
                root: 'data',
                type: 'json' 
            } 
        },
        autoLoad: false
    });

    
    Ext.onReady(function() {
        Ext.QuickTips.init();
        
        Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
        
        var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';        
        
        var frmAddIDCBK = Ext.widget({
            xtype: 'form',
            layout: 'form',
            url: '<?=base_url();?>rbk/rab_analisa/tambah_rap_tree_item/<?=$id_proyek;?>',
            frame: false,
            bodyPadding: '5 5 0',
            width: 400,
            height: 200,
            fieldDefaults: {
                msgTarget: 'side',
                labelWidth: 150
            },
            defaultType: 'textfield',
            items: [
                {
                    name: 'tree_parent_id',
                    xtype: 'hiddenfield',
                    value: 0
                },                                              
                {
                    name: 'id_proyek',
                    xtype: 'hiddenfield',
                    value: <?=$id_proyek;?>,
                },
                {
                    xtype: 'combo',
                    name: 'tree_item',
                    afterLabelTextTpl: required,
                    allowBlank: false,
                    store: { 
                        fields: ['detail_material_id','detail_material_kode', 'detail_material_nama','detail_material_satuan','subbidang_kode'], 
                        pageSize: 20, 
                        proxy: { 
                            type: 'ajax', 
                            url: '<?=base_url();?>rbk/rab_analisa/get_detailmaterial_kode/',
                            reader: { 
                                root: 'data',
                                type: 'json' 
                            } 
                        } 
                    },
                    fieldLabel: 'Uraian pekerjaan...',
                    emptyText: 'uraian...',
                    displayField: 'detail_material_nama',
                    typeAhead: true,
                    hideLabel: false,
                    hideTrigger:true,
                    anchor: '100%',
                    valueField: 'detail_material_nama',
                    listeners: {
                         'select': function(combo, row, index) {
                            //var valharga = row[0].get('mharga');
                            //Ext.getCmp('hargasatuan_idc').setValue(valharga);
                        }
                    },                                                      
                    pageSize: 10
                },  
                {
                    xtype: 'combo',
                    name: 'tree_satuan',
                    afterLabelTextTpl: required,
                    allowBlank: false,
                    store: { 
                        fields: ['satuan_kode'], 
                        pageSize: 100, 
                        proxy: { 
                            type: 'ajax', 
                            url: '<?=base_url();?>rbk/rab_analisa/get_satuan', 
                            reader: { 
                                root: 'data',
                                type: 'json' 
                            } 
                        } 
                    },
                    fieldLabel: 'Satuan',
                    emptyText: 'pilih satuan...',
                    displayField: 'satuan_kode',
                    typeAhead: false,
                    hideLabel: false,
                    hideTrigger:false,
                    anchor: '100%',
                    displayField: 'satuan_kode',
                    valueField: 'satuan_kode',
                    listeners: {
                         'select': function(combo, row, index) {
                            //var valharga = row[0].get('mharga');
                            //Ext.getCmp('hargasatuan_idc').setValue(valharga);
                        }
                    },                                                      
                },
                {
                    name: 'volume',
                    xtype: 'numberfield',
                    fieldLabel: 'Volume',
                },{
                    name: 'harga',
                    xtype: 'numberfield',
                    fieldLabel: 'Harga',
                },                        
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
                                    storeTree.load();
                                    frmAddIDCBK.getForm().reset();
                                }
                               });
                            },
                            failure: function(form, action) {
                                Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
                            }
                        });
                    } else {
                        Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
                    }
                }                       
            },
            {
                text: 'Reset',
                handler: function() {
                    frmAddIDCBK.getForm().reset();
                }
            },
            {
                text: 'Close',
                handler: function() {
                    frmAddIDCBK.getForm().reset();
                    winIDCAddBK.hide();
                }
            }
            ]
        });
            
        var winIDCAddBK = Ext.widget('window', {
            title: 'Tambah Uraian RAB :: Proyek -> ',
            closeAction: 'hide',
            width: '40%',
            height: '35%',
            layout: 'fit',
            resizable: true,
            modal: true,
            items: frmAddIDCBK
        });
        
    /* copy rap */
        Ext.define('mdlCopyRAT', {
            extend: 'Ext.data.Model',
            fields: [       
                {name: 'rap_item_tree',     type: 'string'},
                {name: 'id_proyek',     type: 'string'},
                {name: 'id_satuan',         type: 'string'},
                {name: 'kode_tree',         type: 'string'},
                {name: 'tree_item',         type: 'string'},
                {name: 'tree_satuan',       type: 'string'},
                {name: 'volume',        type: 'float'},
                {name: 'tree_parent_id',        type: 'string'}
            ]
        });

        var storeCopyRAT = Ext.create('Ext.data.Store', {
             model: 'mdlCopyRAT',
             proxy: {
                type: 'ajax',
                url: '<?=base_url();?>rbk/rab_analisa/copy_rat_proyek_lain/',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                },
                simpleSortMode: true                 
             },
            autoLoad: false,
            remoteSort: true,
        });         

        var gridCopyItemRAT = Ext.create('Ext.grid.Panel', {
            width: '100%',
            height: '100%',
            store: storeCopyRAT,
            disableSelection: false,
            loadMask: true,
            selModel: Ext.create('Ext.selection.CheckboxModel', {
                mode: 'MULTI', 
                multiSelect: true,
                keepExisting: true,
            }),                 
            viewConfig: {
                trackOver: true,
                stripeRows: true,
            },      
            columns:[
                {
                    text: 'Kode',
                    width: 50,
                    sortable: false,
                    dataIndex: 'kode_tree',
                },
                {
                    text: 'Uraian',
                    flex: 3,
                    dataIndex: 'tree_item',
                    sortable: false,
                }, 
                {
                    text: 'Satuan',
                    dataIndex: 'tree_satuan',
                    flex: 1,
                    sortable: false,
                },              
                {
                    text: 'Volume',
                    dataIndex: 'volume',
                    flex: 1,
                    align: 'center',
                    sortable: false,
                },
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeCopyRAT,
                displayInfo: true,
                displayMsg: 'Displaying data {0} - {1} of {2}',
                emptyMsg: "No data to display",
            }),         
            dockedItems: [
                {
                    dock: 'top',
                    xtype: 'toolbar',
                    items: [                            
                            {
                                fieldLabel: 'Pilih Proyek',
                                xtype: 'combo',
                                scope: this,
                                name: 'pilih_id_proyek',
                                emptyText: 'Pilih Proyek',
                                labelWidth: 100,
                                flex: 2,
                                valueField: 'id_proyek',
                                displayField: 'nama_proyek',
                                typeAhead: true,
                                queryMode: 'remote',
                                store: { 
                                    fields: ['id_proyek','nama_proyek'], 
                                    pageSize: 100, 
                                    proxy: { 
                                        type: 'ajax', 
                                        url: '<?=base_url();?>rbk/rab_analisa/get_data_proyek',
                                        reader: { 
                                            root: 'data',
                                            type: 'json' 
                                        } 
                                    } 
                                },
                                listeners: {
                                    select: function(combo, record, index) {
                                        storeCopyRAT.load({
                                            params:{'id_proyek':combo.getValue()},
                                            scope: this,
                                            callback: function(records, operation, success)
                                            {
                                                if (success) {
                                                } else {
                                                }
                                            }
                                        });                                         
                                    }
                                },
                            },                                  
                        ]
                },
                {
                    xtype: 'toolbar',
                    dock: 'bottom',
                    items: [
                        {
                            text: 'Copy',
                            flex: 1,
                            handler: function(){
                                var records = gridCopyItemRAT.getView().getSelectionModel().getSelection(),
                                    itemid = [],uraian = [],volume = [],satuan = [],treeid = [];
                                Ext.Array.each(records, function(rec){
                                    treeid.push(rec.get('kode_tree'));
                                    itemid.push(rec.get('rap_item_tree'));
                                    uraian.push(rec.get('tree_item'));
                                    volume.push(rec.get('volume'));
                                    satuan.push(rec.get('tree_satuan'));
                                });
                                if(treeid != '')
                                {
                                    Ext.Ajax.request({
                                        url: '<?=base_url();?>rbk/rab_analisa/copy_tree',
                                        method: 'POST',                                         
                                        params: {                                               
                                            'kode_tree' : treeid.join(','),
                                            'tree_item_id' : itemid.join(','),
                                            'tree_item' : uraian.join(','),
                                            'volume' : volume.join(','),
                                            'satuan' : satuan.join(','),
                                            'id_proyek' : <?=$id_proyek;?>
                                        },                              
                                        success: function(response) {
                                            Ext.MessageBox.alert('OK', 'Data telah di-copy silahkan pilih item kemudian paste pada item tersebut.', function(){
                                                winCopyRAP.hide();                                          
                                            });
                                        },
                                        failure: function(response) {
                                            // Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
                                        }
                                    });             
                                } else 
                                {
                                    Ext.example.msg('Error', 'Silahkan pilih item yang mau di-copy!');
                                }
                            }
                        },'-',
                        {
                            text: ' Tutup ',
                            flex: 1,
                            handler: function()
                            {
                                winCopyRAP.hide();
                            }
                        },
                    ]
                }
            ],
            listeners:{
                beforerender:function(){
                    storeCopyRAT.load();
                },
                itemclick: function(dv, record, item, index, e) {
                }                       
            },
        });

        var frm_copy = Ext.widget({
            xtype: 'form',
            layout: 'form',
            url: '<?=base_url();?>rbk/rab_analisa/copy_rab',
            frame: false,
            bodyPadding: '5 5 0',
            formBind:true,
            fieldDefaults: {
                msgTarget: 'side',
                labelWidth: 150
            },
            defaultType: 'textfield',                                       
            items: [       
            Ext.create('Ext.form.ComboBox', {
                    fieldLabel: 'Pilih Divisi ',
                    allowBlank: false,              
                    afterLabelTextTpl: required,
                    store: store_divisi,                    
                    emptyText: 'Pilih...',
                    name: 'divisi_kode',
                    typeAhead: true,                            
                    displayField: 'name',
                    valueField: 'value',
                    listeners: {
                       select: function(combo, row, index) {
                        // frm_copy.getComponent('pilpro').setValue('');
                        store_pilpro.load({
                            params:{
                                'divisi_id':combo.getValue()
                            },
                            callback:function(a){ 
                                if (a.length <= 0) {
                                    frm_copy.getComponent('pilpro').setValue('');
                                } else {
                                    frm_copy.getComponent('pilpro').setValue(a[0].data.value);
                                }
                            }
                        });
                    },
                },
            }),                                     
            Ext.create('Ext.form.ComboBox', {
                    fieldLabel: 'Pilih Proyek ',
                    allowBlank: false,              
                    afterLabelTextTpl: required,
                    store: store_pilpro, 
                    itemId:'pilpro',                   
                    emptyText: 'Pilih...',
                    name: 'id_proyek',
                    typeAhead: true,
                    queryMode: 'local',                      
                    displayField: 'name',
                    valueField: 'value'
            }),                   
            ],
            buttons: [{
                text: 'Save',
                handler: function() {
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        form.submit({
                            success: function(form, action) {
                               Ext.MessageBox.alert('Success', "Data Telah Dicopy..", function(btn){
                                if(btn == 'ok')
                                {
                                    frm_copy.getForm().reset();
                                    winCopyRAP.hide();
                                    storeTree.load();
                                }
                               });
                            },
                            failure: function(form, action) {
                                Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
                            }
                        });
                    } else {
                        Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
                    }
                }                       
            },
            {
                text: 'Reset',
                handler: function() {
                    frm_copy.getForm().reset();
                }
            },
            {
                text: 'Close',
                handler: function() {
                    frm_copy.getForm().reset();
                    winCopyRAP.hide();
                }
            }
            ],
            renderer: function(){
            }
        });

        var winCopyRAP = Ext.widget('window', {
            title:'Copy item RAB dari Proyek Lain',
            closeAction: 'hide',
            closable: false,
            width: '80%',
            height: 120,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: [frm_copy],
        });     
    /* copy rap */
    
    /* tambah sub item */
        var frmAddBKItem = Ext.widget({
            xtype: 'form',
            layout: 'form',
            url: '<?=base_url();?>rbk/rab_analisa/tambah_rap_tree_item/<?=$id_proyek;?>',
            frame: false,
            bodyPadding: '5 5 0',
            formBind:true,
            width: 400,
            height: 200,
            fieldDefaults: {
                msgTarget: 'side',
                labelWidth: 150
            },
            defaultType: 'textfield',                                       
            items: [
                /*
                {
                    name: 'tree_parent_id',
                    xtype: 'hiddenfield',
                    value: parentid
                },                                              
                {
                    name: 'kode_tree',
                    xtype: 'hiddenfield',
                    value: k_tree
                },
                */              
                {
                    name: 'id_proyek',
                    xtype: 'hiddenfield',
                    value: <?=$id_proyek;?>,
                },
                {
                    name: 'info_uraian',
                    fieldLabel: 'sub Menu',                         
                    id: 'info_uraian_id',
                    xtype: 'textfield',
                    //value: parentname.get('kode_tree')+ ' ' +parentname.get('tree_item'),
                    readOnly: true,
                },                                              
                {
                    xtype: 'combo',
                    name: 'tree_item',
                    afterLabelTextTpl: required,
                    allowBlank: false,
                    store: { 
                        fields: ['detail_material_id','detail_material_kode', 'detail_material_nama','detail_material_satuan','subbidang_kode'], 
                        pageSize: 200, 
                        proxy: { 
                            type: 'ajax', 
                            url: '<?=base_url();?>rencana/get_detailmaterial_kode/',
                            reader: { 
                                root: 'data',
                                type: 'json' 
                            } 
                        } 
                    },
                    fieldLabel: 'Uraian pekerjaan...',
                    emptyText: 'uraian...',
                    displayField: 'detail_material_nama',
                    typeAhead: true,
                    hideLabel: false,
                    hideTrigger:true,
                    anchor: '100%',
                    valueField: 'detail_material_nama',
                    listeners: {
                         'select': function(combo, row, index) {
                        }
                    },                                                      
                    pageSize: 200
                },  
                {
                    xtype: 'combo',
                    name: 'tree_satuan',
                    store: { 
                        fields: ['satuan_kode'], 
                        pageSize: 100, 
                        proxy: { 
                            type: 'ajax', 
                            url: '<?=base_url();?>rencana/get_satuan', 
                            reader: { 
                                root: 'data',
                                type: 'json' 
                            } 
                        } 
                    },
                    fieldLabel: 'Satuan',
                    emptyText: 'pilih satuan...',
                    displayField: 'satuan_kode',
                    typeAhead: false,
                    hideLabel: false,
                    hideTrigger:false,
                    anchor: '100%',
                    displayField: 'satuan_kode',
                    valueField: 'satuan_kode',
                    listeners: {
                         'select': function(combo, row, index) {
                        }
                    },                                                      
                    pageSize: 100
                },
                {
                    name: 'volume',
                    xtype: 'numberfield',
                    fieldLabel: 'Volume',
                },{
                    name: 'harga',
                    xtype: 'numberfield',
                    fieldLabel: 'Harga',
                },                       
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
                                    storeTree.load();
                                    frmAddBKItem.getForm().reset();
                                }
                               });
                            },
                            failure: function(form, action) {
                                Ext.example.msg('Failed', action.result ? action.result.message : 'No response');
                            }
                        });
                    } else {
                        Ext.example.msg( "Error!", "Silahkan isi form dg benar!" );
                    }
                }                       
            },
            {
                text: 'Reset',
                handler: function() {
                    frmAddBKItem.getForm().reset();
                }
            },
            {
                text: 'Close',
                handler: function() {
                    frmAddBKItem.getForm().reset();
                    winAddBKItem.hide();
                }
            }
            ],
            renderer: function(){
            }
        });
    
        var winAddBKItem = Ext.widget('window', {
            closeAction: 'hide',
            width: 500,
            height: 200,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: frmAddBKItem
        });
    /* end tambah sub item */
                
        
        Ext.define('treegriditem', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'rap_item_tree',     type: 'string'},
                {name: 'id_proyek',     type: 'string'},
                {name: 'id_satuan',         type: 'string'},
                {name: 'kode_tree',         type: 'string'},
                {name: 'kode_analisa',      type: 'string'},
                {name: 'tree_item',         type: 'string'},
                {name: 'tree_satuan',       type: 'string'},
                {name: 'ishaschild',        type: 'string'},                
                {name: 'volume',        type: 'float'},
                {name: 'harga',         type: 'float'},
                {name: 'subtotal',      type: 'float'},             
                {name: 'tree_parent_id',        type: 'string'},            
                {name: 'kdt'}
            ]
        });
                            
        var storeTree = Ext.create('Ext.data.TreeStore', {
            model: 'treegriditem',
            expanded: true,         
            extraParams: {
                param : ''
            },
            proxy: {                
                timeout: 900000,
                async: false,
                cache: false,
                type: 'ajax',
                url: '<?=base_url()?>rbk/rab_analisa/get_task_tree_item/<?=$id_proyek;?>',
            },
            listeners :{  
                load:function(){
                    Ext.MessageBox.hide();
                }, 
                beforeload:function(){
                    update_total_rap();
                    Ext.Msg.wait("Loading...","Please Wait");
                }                 
                // beforeload : function(){
                //  Ext.MessageBox.show({ 
                //      msg: 'Loading, please wait...', 
                //      progressText: 'Loading...', 
                //      width:300, 
                //      wait:true, 
                //      waitConfig: {
                //          interval:100
                //      }
                //  });
                // },
                // afterload : function(){
                //  Ext.MessageBox.hide();
                // }
            }
        });
        
        var gridTreeBK = Ext.create('Ext.tree.Panel', {
            //title: 'RAP',
            id: 'gridTreeBKid',
            useArrows: true,
            rootVisible: false,
            store: storeTree,
            multiSelect: false,
            singleExpand: false,
            hideCollapseTool: false,
            selModel: Ext.create('Ext.selection.CheckboxModel', {
                mode: 'MULTI', 
                multiSelect: true,
                keepExisting: true,
            }),         
            viewConfig: {
                stripeRows: true,
                listeners: {
                    itemcontextmenu: function(view, rec, node, index, e) {
                        /*
                        e.stopEvent();
                        gridBKctx.showAt(e.getXY());
                        return false;
                        */
                    }
                }
            },      
            plugins: Ext.create('Ext.grid.plugin.RowEditing', {
                clicksToMoveEditor: 1,
                autoCancel: false,
                listeners: {
                    beforeedit: function(rec,obj){   
                        if (obj.record.get('ishaschild') == 1) {
                            // return false;
                            gridTreeBK.columns[3].getEditor().setDisabled(true);
                            gridTreeBK.columns[4].getEditor().setDisabled(true);
                            gridTreeBK.columns[5].getEditor().setDisabled(true);
                        } else {
                            gridTreeBK.columns[3].getEditor().setDisabled(false);
                            gridTreeBK.columns[4].getEditor().setDisabled(false);
                            gridTreeBK.columns[5].getEditor().setDisabled(false);
                        }
                    },
                    'edit': function () {
                            var editedRecords = gridTreeBK.getStore().getUpdatedRecords();
                            Ext.Ajax.request({
                                url: '<?=base_url();?>rbk/rab_analisa/update_tree_item',
                                method: 'POST',
                                params: {                               
                                    'rap_item_tree' : editedRecords[0].data.rap_item_tree,
                                    'tree_parent_id' : editedRecords[0].data.tree_parent_id,
                                    'id_proyek' : editedRecords[0].data.id_proyek,
                                    'kode_tree' : editedRecords[0].data.kode_tree,
                                    'satuan_id' : editedRecords[0].data.tree_satuan,
                                    'tree_item' : editedRecords[0].data.tree_item,
                                    'volume' : editedRecords[0].data.volume,
                                    'harga' : editedRecords[0].data.harga
                                },                              
                                success: function(response) {
                                    var text = response.responseText;
                                    Ext.Msg.alert( "Status", text, function(){
                                        storeTree.load();
                                    });    
                                    update_total_rap();                                     
                                },
                                failure: function() {
                                    Ext.example.msg( "Error", "Data GAGAL diupdate!");                                          
                                }
                            });                                                                                                                 
                        }
                 }              
            }),                                 
            columns: [
                {
                    text: 'Kode',
                    xtype: 'treecolumn', 
                    width: 100,
                    sortable: false,
                    dataIndex: 'kode_tree',
                    /*
                    editor: {
                        xtype: 'textfield',
                    }
                    */                  
                },
                {
                    text: 'Uraian',
                    flex: 3,
                    dataIndex: 'tree_item',
                    sortable: false,
                    editor: {
                        xtype: 'textfield',
                    }                                                           
                }, 
                {
                    text: 'Satuan',
                    dataIndex: 'tree_satuan',
                    flex: 1,
                    sortable: false,
                    editor: {
                        xtype: 'combo',
                        afterLabelTextTpl: required,
                        allowBlank: false,
                        store: { 
                            fields: ['satuan_id','satuan_kode'], 
                            pageSize: 100, 
                            proxy: { 
                                type: 'ajax', 
                                url: '<?=base_url();?>rbk/rab_analisa/get_satuan', 
                                reader: { 
                                    root: 'data',
                                    type: 'json' 
                                } 
                            } 
                        },
                        triggerAction : 'all',                  
                        anchor: '100%',
                        displayField: 'satuan_kode',
                        valueField: 'satuan_kode',
                    }                                       
                },              
                {
                    text: 'Volume',
                    dataIndex: 'volume',
                    flex: 1,
                    align: 'center',
                    sortable: false,
                    editor: {
                        xtype: 'numberfield',
                    }                                                           
                },              
                {
                    text: 'Harga',
                    dataIndex: 'harga',
                    flex: 2,
                    renderer: Ext.util.Format.numberRenderer('00,000.00'),
                    align: 'right',
                    sortable: false,
                    editor: {
                        xtype: 'numberfield',
                    }
                },                              
                {
                    text: 'Jumlah',
                    flex: 2,
                    dataIndex: 'subtotal',
                    align: 'right',
                    renderer: Ext.util.Format.numberRenderer('00,000.00'),
                    sortable: false
                },                              
                {
                    text: '',
                    flex: 1,
                    menuDisabled: true,
                    xtype: 'actioncolumn',
                    align: 'center',
                    items :[{
                    icon: '<?=base_url();?>/assets/images/add.png',
                    handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
                        var parentid = record.get('rap_item_tree');
                        var parentname = record.get('kode_tree')+ ' ' +record.get('tree_item');
                        Ext.Ajax.request({
                            url: '<?php echo base_url();?>rbk/rab_analisa/set_parent_tree_id/',
                            params: {
                                parent_id: parentid,
                                proyek_id: record.get('id_proyek'),
                                parent_kode_tree: record.get('kode_tree')
                            },
                            success: function(response){
                                //var text = response.responseText;
                                winAddBKItem.setTitle('Tambah Uraian BK :: Sub Item - ' + parentname);
                                winAddBKItem.on('show', function(win) {
                                    Ext.getCmp('info_uraian_id').setValue(parentname);
                                });
                                winAddBKItem.show();
                            }
                        });                     
                    },                                                          
                    getClass: function(v, meta, record) {  
                        var volume = record.get('volume');
                        var harga = record.get('harga'); 
                        var satuan = record.get('tree_satuan');       

                        if(volume > 1 && harga > 0 && record.get('ishaschild') != 1 && satuan != 'Ls'){
                            return 'x-hide-display';
                        } else if(volume > 1 && harga == 0 && record.get('ishaschild') != 1 && satuan != 'Ls'){
                            return 'x-hide-display';
                        } else if(volume == 1 && harga > 0 && record.get('ishaschild') != 1 && satuan != 'Ls'){
                            return 'x-hide-display';
                        } else if(volume == 1 && harga == 0 && record.get('ishaschild') != 1 && satuan != 'Ls'){
                            return 'x-hide-display';
                        }
                    }
                    }]
                },
                {
                    text: '',
                    flex: 1,
                    menuDisabled: true,
                    xtype: 'actioncolumn',
                    tooltip: 'Delete Item',
                    align: 'center',
                    icon: '<?=base_url();?>/assets/images/delete.png',
                    handler: function(grid, rowIndex, colIndex, actionItem, event, record, row) {
                        var itemid = record.get('rap_item_tree');
                        if(record.get('ishaschild') != 1)
                        {
                            Ext.MessageBox.confirm('Hapus item menu > '+record.get('kode_tree') +'. '+record.get('tree_item'), 'Apakah anda akan menghapus Analisa Satuan untuk item ini?', function(btn){
                                if(btn == 'yes')
                                {
                                    Ext.Ajax.request({
                                        url: '<?php echo base_url(); ?>rbk/rab_analisa/del_tree_item',
                                        params: {
                                            tree_item_id: record.get('rap_item_tree')
                                        },
                                        success: function(form, action){
                                            Ext.MessageBox.alert('Sukses', 'Data berhasil dihapus!', function(){
                                                storeTree.load();
                                            });                                 
                                        },
                                        failure: function(response, options) {
                                            Ext.MessageBox.alert('Error', "Hapus terlebih dahulu Data Analisa Satuan Pekerjaan yang terhubung dengan item ini!");
                                        },                                  
                                    });
                                }
                            });                     
                        } else {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Tidak bisa menghapus menu ini, hapus dulu sub menu di bawahnya!',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.Error
                            });
                        }                       
                    }
                }                              
            ],
            dockedItems: [
                {
                    xtype: 'toolbar',
                    itemId:'top_bar',
                    items: [
                    {
                        text: 'Tambah Item',
                        iconCls: 'icon-add',
                        handler: function(){
                            Ext.Ajax.request({
                                url: '<?php echo base_url();?>rbk/rab_analisa/set_parent_tree_id/',
                                params: {
                                    proyek_id: <?=$id_proyek;?>,
                                    parent_id: '0',
                                    parent_kode_tree: ''
                                },
                                success: function(response){
                                    winIDCAddBK.show();
                                    winIDCAddBK.doLayout();
                                }
                            });                                                 
                        }
                    },
                    {
                        text: 'Import CSV',
                        iconCls: 'icon-add',
                            handler: function(){
                            winUploadRATitem.show();
                        }
                    },
                    {
                        text: 'Print Excel',
                        iconCls: 'icon-print',
                            handler: function(){
                            Ext.MessageBox.confirm('Delete item', 'Apakah anda akan mengeksport uraian ini?',function(resbtn){
                                if(resbtn == 'yes')
                                {
                                    window.location = '<?=base_url();?>rbk/rab_analisa/excel/rab';
                                }
                            });
                        }
                    },
                    {
                        fieldLabel:'Search <b>(Press ENTER) <b>',
                        labelWidth:140,
                        xtype:'textfield',
                        itemId:'search',
                        listeners: {
                            scope:this,
                            specialKey: function(f,n){
                                if (n.getKey() == n.ENTER){
                                    storeTree.proxy.setExtraParam('param', f.getValue());
                                    storeTree.load();
                                }
                            }
                        }
                    },'-',
                    {
                        text:'Clear',
                        handler: function(){
                            bar = gridTreeBK.getComponent('top_bar');
                            bar.getComponent('search').setValue('');
                            storeTree.proxy.setExtraParam('param','');
                            storeTree.load();
                        }
                    },{
                        text: 'Expand All',
                        handler: function(){                
                            gridTreeBK.expandAll();
                        }
                    }, {
                        text: 'Collapse All',
                        handler: function(){        
                            gridTreeBK.collapseAll();
                        }
                    },'-',
                    {
                        text:'Open In New Tab',
                        iconCls:'icon-new',
                        handler:function(){
                            window.open(document.URL,'_blank');
                        }
                    }
                    ]
                },          
                {
                    xtype: 'toolbar',
                    dock: 'bottom',
                    items: [
                        {
                            text: 'Refresh',
                            iconCls: 'icon-reload',
                            handler: function()
                            {
                                storeTree.load();
                            }
                        },                  
                        {
                            text: 'Copy',
                            iconCls: 'icon-copy',
                            handler: function(){          
                                var records = gridTreeBK.getView().getSelectionModel().getSelection(),
                                    itemid = [],kdanalisa = [],uraian = [],volume = [],satuan = [],treeid = [];
                                Ext.Array.each(records, function(rec){
                                    treeid.push(rec.get('kode_tree'));
                                    itemid.push(rec.get('rap_item_tree'));
                                    kdanalisa.push(rec.get('kode_analisa'));
                                    uraian.push(rec.get('tree_item'));
                                    volume.push(rec.get('volume'));
                                    satuan.push(rec.get('tree_satuan'));
                                });
                                if(treeid != '')
                                {
                                    Ext.Ajax.request({
                                        url: '<?=base_url();?>rbk/rab_analisa/copy_tree',
                                        method: 'POST',                                         
                                        params: {                                               
                                            'kode_tree' : treeid.join(','),
                                            'tree_item_id' : itemid.join(','),
                                            'tree_item' : uraian.join(','),
                                            'kode_analisa' : kdanalisa.join(','),
                                            'volume' : volume.join(','),
                                            'satuan' : satuan.join(','),
                                            'id_proyek' : <?=$id_proyek;?>
                                        },                              
                                        success: function(response) {
                                            Ext.MessageBox.alert('OK', 'Data telah di-copy silahkan pilih item kemudian paste pada item tersebut.');
                                        },
                                        failure: function(response) {
                                            // Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
                                        }
                                    });             
                                } else 
                                {
                                    Ext.example.msg('Error', 'Silahkan pilih item yang mau di-copy!');
                                }
                            }
                        },'-',
                        {
                            text: 'Paste',
                            iconCls: 'icon-paste',
                            handler: function(){
                                var records = gridTreeBK.getView().getSelectionModel().getSelection(),
                                    itemid = [],
                                    treeid = [];
                                Ext.Array.each(records, function(rec){
                                    treeid.push(rec.get('kode_tree'));
                                    itemid.push(rec.get('rap_item_tree'));
                                });
                                if(treeid != '')
                                {
                                    Ext.Ajax.request({
                                        url: '<?=base_url();?>rbk/rab_analisa/paste_tree',
                                        method: 'POST',                                         
                                        params: {                                               
                                            'kode_tree' : treeid.join(','),
                                            'tree_item_id' : itemid.join(','),
                                            'id_proyek' : <?=$id_proyek;?>
                                        },                              
                                        success: function(response) {                                       
                                            Ext.MessageBox.alert('Status', response.responseText, function(){
                                                storeTree.load();
                                            });
                                        },
                                        failure: function(response) {
                                            // Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
                                        }
                                    });             
                                } else 
                                {
                                    Ext.example.msg('Error', 'Silahkan pilih item untuk Paste item yg sudah di-copy!');
                                }
                            }
                        },'-',
                        {
                            text: 'Copy dari Proyek Lain',
                            iconCls: 'icon-copy',
                            handler: function(){
                                winCopyRAP.show();
                            }                           
                        },'-',
                        {
                            text: 'Delete',
                            iconCls: 'icon-del',
                            handler: function(){
                                var records = gridTreeBK.getView().getSelectionModel().getSelection(),
                                    itemid = [],ischild = [],treeid = [];
                                Ext.Array.each(records, function(rec){
                                    treeid.push(rec.get('kode_tree'));
                                    ischild.push(rec.get('ishaschild'));
                                    itemid.push(rec.get('rap_item_tree'));
                                });
                                if(treeid != '')
                                {
                                    Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus kode item ('+treeid.join(', ')+') ini?',function(resbtn){
                                        if(resbtn == 'yes')
                                        {
                                            Ext.Ajax.request({
                                                url: '<?=base_url();?>rbk/rab_analisa/delete_tree_item',
                                                method: 'POST',                                         
                                                params: {                                               
                                                    'kode_tree' : treeid.join(','),
                                                    'tree_item_id' : itemid.join(','),
                                                    'ishaschild': ischild.join(','),
                                                    'id_proyek' : <?=$id_proyek;?>
                                                },                              
                                                success: function(response) {                                       
                                                    Ext.MessageBox.alert('Status', response.responseText, function(){
                                                        storeTree.load();
                                                    });
                                                },
                                                failure: function(response) {
                                                    // Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem, or duplicate entries!');
                                                }
                                            });
                                        }
                                    });                             
                                } else 
                                {
                                    Ext.example.msg('Error', 'Silahkan pilih item yang mau dihapus!');
                                }                           
                            }                           
                        },'->',
                        {
                            text: 'Total RAP: ',
                            id: 'id-total-rap',
                            handler: update_total_rap,
                        }                   
                    ]
                }               
            ],
            listeners:{
                beforerender:function(){
                    update_total_rap();
                    //storeTree.load();
                }
            }                       
        });

        function update_total_rap()
        {
            Ext.Ajax.request({
                url: '<?=base_url();?>rbk/rab_analisa/get_total_rap',
                method: 'POST',             
                timeout: 900000,                            
                params: {                                               
                    'id_proyek' : <?=$id_proyek;?>
                },                              
                success: function(response) {           
                    Ext.getCmp('id-total-rap').setText('<b>Total RAB : '+ response.responseText +'</b>');
                },
                failure: function(response) {
                    // Ext.MessageBox.alert('Failure', 'Insert Data Error due to connection problem!');
                }
            });                     
        }      
            
        var tabRAT = Ext.widget('tabpanel', {
            title: 'RAB :: Proyek <?php echo $data_proyek['proyek']; ?>',
            renderTo: Ext.getBody(),
            activeTab: 0,
            width: '100%',
            height: '100%',
            deferredRender: false, 
            items: [
            {           
                title: 'RAB',
                layout: 'fit',
                items: gridTreeBK, 
                listeners: {
                    activate: function(tab){
                        setTimeout(function() {
                            update_total_rap();
                        }, 1);
                    }
                },                                                                              
            }          
            ]           
        }); 

        var frmUploadRATItem = Ext.widget({
            xtype: 'form',
            layout: 'form',
            url: '<?php echo base_url(); ?>rbk/upload_rap_item_rab',
            frame: false,
            bodyPadding: '5 5 0',
            width: 350,
            fieldDefaults: {
                msgTarget: 'side',
                labelWidth: 75
            },
            items: [
                {
                    xtype: 'hidden',
                    name: 'id_proyek',
                    value: '<?php echo $data_proyek['proyek_id']; ?>'
                },
                {
                    xtype: 'filefield',
                    emptyText: 'silahkan pilih file...',
                    fieldLabel: 'File',
                    name: 'upload_analisa',
                    buttonText: 'pilih file',
                    allowBlank: false
                },              
            ],

            buttons: [{
                text: 'Upload',
                handler: function(){            
                    var form = this.up('form').getForm();
                    if(form.isValid()){
                        form.submit({
                            enctype: 'multipart/form-data',
                            waitMsg: 'Upload CSV RAB item ...',
                            success: function(fp, o) {
                                Ext.MessageBox.alert('Status','Upload file "'+ o.result.file + '" berhasil.', function()
                                {
                                    storeTree.load();
                                });
                            },
                            failure: function(fp, o){                               
                                Ext.MessageBox.alert('Error','GAGAL Upload file "'+ o.result.file + '", pesan: '+o.result.message);
                            }
                        });
                        // storeTree.load();
                        winUploadRATitem.hide();
                    }
                }
            },
            {
                text: 'Cancel',
                handler: function() {
                   winUploadRATitem.hide();
                }
            }]
        });

        var winUploadRATitem = Ext.create('Ext.Window', {
            title: 'Upload RAB Item (CSV)',
            closeAction: 'hide',
            height: '25%',
            width: '40%',
            layout: 'fit',
            modal: true,
            items: frmUploadRATItem
        }); 
        
    });

</script>
</head>
<body>
<div id="grid-proyek" class="x-hide-display"></div>
</body>
</html>