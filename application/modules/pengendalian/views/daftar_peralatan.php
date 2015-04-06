<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<style type="text/css">
.icon-new {
    background: url(<?php echo base_url(); ?>assets/images/new-icon.png) no-repeat 0 -1px;
}
</style>
<script type="text/javascript">

Ext.require([
    '*'
]);
var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

    var storelistalat = Ext.create('Ext.data.Store', {
        id: 'storebln',
        fields: ['value','text'],
        autoLoad: true,        
         proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/getlistalat',
             reader: {
                 type: 'json',
                 root: 'data'
             }
         }
    });

    Ext.define('analisa', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'id',
                mapping: 'daftar_peralatan_id'
            },
            {
                name: 'uraian_jenis_alat',
                mapping: 'uraian_jenis_alat'
            },
            {
                name: 'merk_model',
                mapping: 'merk_model'},
            {
                name: 'type_penggerak',
                mapping: 'type_penggerak'},
            {
                name: 'kapasitas',
                mapping: 'kapasitas'},
            {
                name: 'status_kepemilikan',
                mapping: 'status_kepemilikan'},
            {
                name: 'kondisi',
                mapping: 'kondisi'},
            {
                name: 'status_operasi',
                mapping: 'status_operasi'},
            {
                name: 'keterangan',
                mapping: 'keterangan'},
            {
                name: 'master_peralatan_id',
                mapping: 'master_peralatan_id'},
            {
                name: 'kondisi_id',
                mapping: 'kondisi_id'},
            {
                name: 'status_operasi_id',
                mapping: 'status_operasi_id'},
            {
                name: 'status_kepemilikan_id',
                mapping: 'status_kepemilikan_id'}
         ]
    });

    Ext.define('combo', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'text',
                mapping: 'text'
            },
            {
                name: 'value',
                mapping: 'value'
            }
         ]
    });

    var storealat = Ext.create('Ext.data.Store', {
        id: 'storealat',
        model: 'analisa',
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getdaftar_alat',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        autoLoad: false
    });

    var store_kondisi = Ext.create('Ext.data.Store', {
        id: 'storealat',
        model: 'combo',
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getkondisi',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        autoLoad: true
    });

    var store_status_operasi = Ext.create('Ext.data.Store', {
        id: 'storealat',
        model: 'combo',
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getstatusoperasi',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        autoLoad: true
    });

    var store_status_kepemilikan = Ext.create('Ext.data.Store', {
        id: 'storealat',
        model: 'combo',
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getstatuskepemilikan',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        autoLoad: true
    });

Ext.onReady(function() {
    storealat.load();
    
    var grid = Ext.create('Ext.grid.Panel', {
        store: storealat,
        autoscroll:true,
        columns: [
            {text: "URAIAN JENIS ALAT", flex:1, sortable: true, dataIndex: 'uraian_jenis_alat'},
            {text: "MERK / MODEL", flex:1, sortable: true, dataIndex: 'merk_model'},
            {text: "TYPE/PENGGERAK", flex:1, sortable: true, dataIndex: 'type_penggerak'},
            {text: "KAPASITAS", flex:1, sortable: true, dataIndex: 'kapasitas'},
            {text: "MILIK/ SEWA", flex:1, sortable: true, dataIndex: 'status_kepemilikan'},
            {text: "KONDISI", flex:1, sortable: true, dataIndex: 'kondisi'},
            {text: "OPERASI", flex:1, sortable: true, dataIndex: 'status_operasi'},
            {text: "KETERANGAN", flex:1, sortable: true, dataIndex: 'keterangan'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
                handler: function(grid, rowIndex, colIndex){
                    rec = storealat.getAt(rowIndex); 
                    id = rec.get('id');
                    model = rec.get('merk_model');
                    penggerak = rec.get('type_penggerak');
                    kapasitas = rec.get('kapasitas');
                    ket = rec.get('keterangan');

                    uraian_id = rec.get('master_peralatan_id');
                    milik_id = rec.get('status_kepemilikan_id');
                    kondisi_id = rec.get('kondisi_id');
                    operasi_id = rec.get('status_operasi_id');

                    edit(id,ket,uraian_id,milik_id,kondisi_id,operasi_id);
                }
            },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
                handler: function(grid,rowIndex,colIndex){
                    var rec = storealat.getAt(rowIndex);
                    id = rec.get('id');

                    Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                        if(resbtn == 'yes')
                        {
                            Ext.Ajax.request({
                                 url: '<?=base_url();?>pengendalian/delete_data/daftar_peralatan',
                                    method: 'POST',
                                    params: {
                                        'id' :  id
                                        },                              
                                success: function() {
                                Ext.Msg.alert( "Status", "Delete successfully..!"); 
                                storealat.load();                                        
                                },
                                failure: function() {
                                Ext.Msg.alert( "Status", "No Respond..!"); 
                                }
                            });                                                                                        
                        }
                    });
                }
            }
        ],
        columnLines: true,
        // inline buttons
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Tambah Data',
                tooltip:'Tambah Data',
                handler: function(){
                    add();
                }
            },'-',
            {
                    text:'Open In New Tab',
                    iconCls:'icon-new',
                    handler:function(){
                        window.open(document.URL,'_blank');
                    }
                }]
        }],
        height: '100%',
        frame: true,
        title: 'Daftar Peralatan',
        iconCls: 'icon-grid'
        // ,
        // renderTo: Ext.getBody()
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

function add(){
        var formadd = Ext.widget({
        xtype: 'form',
        layout: 'form',
        frame: false,
        url: '<?php echo base_url(); ?>pengendalian/action_daftar_alat/simpan',
        frame: true,
        bodyPadding: '5 5 0',
        width: 350,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 125
        },
        items: [{
            xtype: 'combobox',
            fieldLabel: 'URAIAN JENIS ALAT ',
            name: 'uraian_jenis_alat',
            store: storelistalat,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih..',
            allowBlank: false
        },{
            xtype: 'combobox',
            fieldLabel: 'MILIK / SEWA ',
            name: 'milik',
            store: store_status_kepemilikan,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih..',
            allowBlank: false
        },{
            xtype: 'combobox',
            fieldLabel: 'KONDISI ',
            name: 'kondisi',
            store: store_kondisi,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih..',
            allowBlank: false
        },{
            xtype: 'combobox',
            fieldLabel: 'OPERASI ',
            name: 'operasi',
            store: store_status_operasi,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih..',
            allowBlank: false
        },{
            xtype: 'textarea',
            name: 'keterangan',
            fieldLabel: 'KETERANGAN'
        }],

        buttons: [{
            text: 'Simpan',
            handler: function(){            
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        success: function() {
                            Ext.MessageBox.alert('Informasi','Insert successfully..!');
                            storealat.load();
                        }
                    });
                    winadd.hide();
                }
            }
        }
        ,{
            text: 'Cancel',
            handler: function() {
               winadd.hide();
            }
        }]
    });
    
    
    var winadd = Ext.create('Ext.Window', {
        title: 'Tambah Daftar Peralatan',
        closeAction: 'hide',
        height: 270,
        width: 540,
        layout: 'fit',
        modal:true,
        items: formadd
    }).show();
}

function edit(id,ket,uraian_id,milik_id,kondisi_id,operasi_id){

        var formedit = Ext.widget({
        xtype: 'form',
        layout: 'form',
        frame: false,
        url: '<?php echo base_url(); ?>pengendalian/action_daftar_alat/edit',
        frame: true,
        bodyPadding: '5 5 0',
        width: 350,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 150
        },
        items: [{
            xtype: 'textfield',
            fieldLabel: 'Id',
            afterLabelTextTpl: required,
            name: 'id',
            allowBlank: false,
            value: id,
            hidden: true
        },{
            xtype: 'combobox',
            afterLabelTextTpl: required,
            fieldLabel: 'URAIAN JENIS ALAT ',
            name: 'uraian_jenis_alat',
            store: storelistalat,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih..',
            allowBlank: false,
            value: uraian_id
        },{
            xtype: 'combobox',
            afterLabelTextTpl: required,
            fieldLabel: 'MILIK / SEWA ',
            name: 'milik',
            store: store_status_kepemilikan,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih..',
            allowBlank: false,
            value: milik_id
        },{
            xtype: 'combobox',
            afterLabelTextTpl: required,
            fieldLabel: 'KONDISI ',
            name: 'kondisi',
            store: store_kondisi,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih..',
            allowBlank: false,
            value: kondisi_id
        },{
            xtype: 'combobox',
            afterLabelTextTpl: required,
            fieldLabel: 'OPERASI ',
            name: 'operasi',
            store: store_status_operasi,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih..',
            allowBlank: false,
            value: operasi_id
        },{
            xtype: 'textarea',
            afterLabelTextTpl: required,
            name: 'keterangan',
            fieldLabel: 'KETERANGAN',
            value: ket
        }],

        buttons: [{
            text: 'Update',
            handler: function(){            
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        success: function() {
                            Ext.MessageBox.alert('Informasi','Upload successfully..!');
                            storealat.load();
                        }
                    });
                    winedit.hide();
                }
            }
        }
        ,{
            text: 'Cancel',
            handler: function() {
               winedit.hide();
            }
        }]
    });
    
    
    var winedit = Ext.create('Ext.Window', {
        title: 'Edit Daftar ALAT',
        closeAction: 'hide',
        height: 270,
        width: 540,
        layout: 'fit',
        modal:true,
        items: formedit
    }).show();
}
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>
