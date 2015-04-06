<html>
<head>
<style type="text/css">
.link {
    text-decoration: none;
    color: rgb(11, 100, 214);
}
</style>
<style type="text/css">
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
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

<script type="text/javascript">
var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

Ext.require([
    '*'
]);

	Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'uraian_pekerjaan', mapping: 'uraian_pekerjaan'},
            {name: 'suplier', mapping: 'suplier'},
            {name: 'satuan_id', mapping: 'satuan_id'},
            {name: 'harga_satuan', mapping: 'harga_satuan'},
            {name: 'spen_ya', mapping: 'spen_ya'},
            {name: 'spen_tidak', mapping: 'spen_tidak'},
            {name: 'rekan', mapping: 'rekan'},
            {name: 'spen_tidak', mapping: 'spen_tidak'},
            {name: 'keterangan', mapping: 'keterangan'},
            {name: 'status_penawaran', mapping: 'status_penawaran'},
            {name: 'rekan_usul', mapping: 'rekan_usul'}
         ]
    });

    Ext.define('mdl_combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value'}
         ]
    });

    var storesatuan = Ext.create('Ext.data.Store', {
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getlistsatuan',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

    var storecd = Ext.create('Ext.data.Store', {
        model: 'mdl',
        pageSize: 100,
        remoteFilter: true,
        autoLoad: false,        
        proxy: {
            type: 'ajax',            
            extraParams: {
                sort: ''
            },
            url: '<?php echo base_url() ?>rbk/get_data_checklist_dokumen',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    
Ext.onReady(function() {
storecd.load();

var grid = Ext.create('Ext.grid.Panel', {
        store: storecd,
        autoscroll: true,
        title: 'CHECKLIST DOKUMEN PENAWARAN REKANAN (FM-CDPR)',
        columns: [
            {xtype:'rownumberer', width:32, align:'center'},
            {text: "ITEM PEKERJAAN", flex:1, sortable: true, dataIndex: 'uraian_pekerjaan'},
            {text: "NAMA SUPLIER /<br>SUBKONTRAKTOR", width:150, sortable: true, dataIndex: 'suplier'},
            {text: "SAT", width:50, sortable: true, dataIndex: 'satuan_id', align:'center',
                renderer: function(val){
                    var index = storesatuan.findExact('value',val);
                    if (index != -1) {
                        var rec = storesatuan.getAt(index);
                        text = rec.get('text');
                    } else {
                        text = val;
                    }
                    return text;
                }
            },
            {text: "HARGA SATUAN", width:100, sortable: true, dataIndex: 'harga_satuan'},
            {text: "SURAT<br>PENAWARAN", columns:[
                {text: "YA", width:50, sortable: true, dataIndex: 'spen_ya', align:'center'},
                {text: "TIDAK", width:50, sortable: true, dataIndex: 'spen_tidak', align:'center'},
            ]},            
            {text: "REKANAN<br>YANG<br>DIUSULKAN", width:90, sortable: true, dataIndex: 'rekan', align:'center'},
            {text: "KETERANGAN", width:100, sortable: true, dataIndex: 'keterangan'},
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
                rec = storecd.getAt(rowIndex);
                id = rec.get('id');
                uraian_pekerjaan = rec.get('uraian_pekerjaan');
                suplier = rec.get('suplier');
                satuan_id = rec.get('satuan_id');
                harga_satuan = rec.get('harga_satuan');
                status_penawaran = rec.get('status_penawaran');
                rekan_usul = rec.get('rekan_usul');
                keterangan = rec.get('keterangan');
                edit(id,uraian_pekerjaan,suplier,satuan_id,harga_satuan,status_penawaran,rekan_usul,keterangan);
            }},
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
                rec = storecd.getAt(rowIndex);
                id = rec.get('id');
                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                    if(resbtn == 'yes')
                    {
                        Ext.Ajax.request({
                            url: '<?=base_url();?>rbk/delete/checklist_dokumen',
                            method: 'POST',
                            params: {
                                'id' :  id
                            },                              
                            success: function() {
                            storecd.load();
                            Ext.Msg.alert( "Status", "Delete successfully..!", function(){  
                            });                                         
                            },
                            failure: function() {
                            }
                        });                                                                                     
                    }
                });
            }}
        ],
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                fieldLabel: 'Search Item Pekerjaan atau Suplier',
                labelWidth: 200,
                xtype: 'textfield',
                name:'sort',
                itemId:'sort',
                listeners:{
                    change: function(val){
                        value = val.getValue();
                        storecd.proxy.setExtraParam('sort', value);
                        storecd.load();
                    }
                }
            },'-',{
                    text:'Export SKBDN',
                    iconCls:'icon-print',
                    handler:function(){
                        Ext.MessageBox.confirm('Export', 'Apakah anda akan meng-Export item ini?',function(resbtn){
                            if(resbtn == 'yes')
                            {
                                window.location='<?=base_url()?>rbk/print_data/checklist_dokumen';                                                                             
                            }
                        });
                    }
                },'-',
                {
                    text:'Open In New Tab',
                    iconCls:'icon-new',
                    handler:function(){
                        window.open(document.URL,'_blank');
                    }
                }]
        },{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [{
                text:'Tambah',
                tooltip:'Tambah',
                handler: function(){
                    tambah();
                }
            }]
        }],
        columnLines: true,
        width: '100%',
        height: '100%',
        bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 100,
                             store: storecd,
                             displayInfo: true
                     })
        ]
    });

    var viewport = Ext.create('Ext.Viewport', {
        layout: {
            type: 'border',
        },
        defaults: {
            split: true
        },
        items: [{
            region: 'north', 
            height: '100%',       
            border: 0,
            layout: 'fit',
            items: grid
        }]
    });
});

function tambah(){
    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>rbk/insert/checklist_dokumen',
        width:'100%',
        height:'100%',
        bodyStyle: 'padding:5px 5px 0',
        autoScroll: true,
        frame: false,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 120
        },
        items: [{
            xtype:'textfield',
                fieldLabel: 'Uraian Pekerjaan',
                anchor: '-5',
                name: 'uraian_pekerjaan',
                afterLabelTextTpl: required,
                allowBlank: false
            },{
                xtype:'textfield',
                fieldLabel: 'Nama Suplier',
                anchor: '-5',
                name: 'nama_suplier',
                afterLabelTextTpl: required,
                allowBlank: false
            },{
                xtype: 'combobox',
                fieldLabel: 'SATUAN',
                name: 'satuan',
                store: storesatuan,
                valueField: 'value',
                displayField: 'text',
                typeAhead: true,
                queryMode: 'local',
                anchor: '-5',
                emptyText: 'Pilih..',   
                editable:false,         
                afterLabelTextTpl: required,
                allowBlank: false
            },{
                xtype:'numberfield',
                fieldLabel: 'Harga Satuan',
                anchor: '-5',
                minValue: 0,
                name: 'harga_satuan',
                afterLabelTextTpl: required,
                allowBlank: false
            },{
                xtype: 'radiogroup',
                fieldLabel: 'Surat Penawaran',
                anchor: '-5',
                columns: 2,
                items: [
                    {boxLabel: 'Ya', name: 'spen', itemId: 'spen_ya', inputValue: 1, checked:true},
                    {boxLabel: 'Tidak', name: 'spen', itemId: 'spen_tidak', inputValue: 0},
                ]
            },{
                xtype: 'radiogroup',
                fieldLabel: 'Rekanan Yang Diusulkan',
                anchor: '-5',
                columns: 2,
                items: [
                    {boxLabel: 'Ya', name: 'rekan', itemId: 'rekan_ya', inputValue: 1, checked:true},
                    {boxLabel: 'Tidak', name: 'rekan', itemId: 'rekan_tidak', inputValue: 0},
                ]
            },{
                xtype:'textarea',
                fieldLabel: 'Keterangan',
                anchor: '-5',
                name: 'keterangan',
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
                                storecd.load();
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

    var winadd = Ext.create('Ext.Window', {
        title: 'FORM TAMBAH',
        closeAction: 'hide',
        width: 430,
        height: '65%',
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

function edit(id,uraian_pekerjaan,suplier,satuan_id,harga_satuan,status_penawaran,rekan_usul,keterangan){
    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>rbk/edit/checklist_dokumen',
        width:'100%',
        height:'100%',
        bodyStyle: 'padding:5px 5px 0',
        autoScroll: true,
        frame: false,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 120
        },
        items: [{
                xtype:'textfield',
                fieldLabel: 'ID',
                anchor: '-5',
                name: 'id',
                afterLabelTextTpl: required,
                allowBlank: false,
                hidden: true,
                value: id
            },{
                xtype:'textfield',
                fieldLabel: 'Uraian Pekerjaan',
                anchor: '-5',
                name: 'uraian_pekerjaan',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: uraian_pekerjaan
            },{
                xtype:'textfield',
                fieldLabel: 'Nama Suplier',
                anchor: '-5',
                name: 'nama_suplier',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: suplier
            },{
                xtype: 'combobox',
                fieldLabel: 'SATUAN',
                name: 'satuan',
                store: storesatuan,
                valueField: 'value',
                displayField: 'text',
                typeAhead: true,
                queryMode: 'local',
                anchor: '-5',
                emptyText: 'Pilih..',   
                editable:false,         
                afterLabelTextTpl: required,
                allowBlank: false,
                value: satuan_id
            },{
                xtype:'numberfield',
                fieldLabel: 'Harga Satuan',
                anchor: '-5',
                minValue: 0,
                name: 'harga_satuan',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: harga_satuan
            },{
                xtype: 'radiogroup',
                fieldLabel: 'Surat Penawaran',
                itemId: 'spen',
                anchor: '-5',
                columns: 2,
                items: [
                    {boxLabel: 'Ya', name: 'spen', itemId: 'spen_ya', inputValue: 1, checked:true},
                    {boxLabel: 'Tidak', name: 'spen', itemId: 'spen_tidak', inputValue: 0},
                ]
            },{
                xtype: 'radiogroup',
                fieldLabel: 'Rekanan Yang Diusulkan',
                itemId: 'rekan',
                anchor: '-5',
                columns: 2,
                items: [
                    {boxLabel: 'Ya', name: 'rekan', itemId: 'rekan_ya', inputValue: 1, checked:true},
                    {boxLabel: 'Tidak', name: 'rekan', itemId: 'rekan_tidak', inputValue: 0},
                ]
            },{
                xtype:'textarea',
                fieldLabel: 'Keterangan',
                anchor: '-5',
                name: 'keterangan',
                afterLabelTextTpl: required,
                allowBlank: false, 
                value: keterangan
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
                                storecd.load();
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
        }],
        listeners:{
            beforerender: function(){
                spen = frmadd.getComponent('spen');
                rekan = frmadd.getComponent('rekan');

                if (status_penawaran == 1) {
                    spen.getComponent('spen_ya').setValue(true);
                } else {
                    spen.getComponent('spen_tidak').setValue(true);
                }

                if (rekan_usul == 1) {
                    rekan.getComponent('rekan_ya').setValue(true);
                } else {
                    rekan.getComponent('rekan_tidak').setValue(true);
                }
            }
        }
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'FORM TAMBAH',
        closeAction: 'hide',
        width: 430,
        height: '65%',
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}
</script>

</head>
<body>
</body>
</html>