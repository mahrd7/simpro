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
var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
Ext.require([
    '*'
]);

    Ext.define('mos', {
        extend: 'Ext.data.Model',
        fields: [{
                name: 'id' ,mapping:'mos_id'
            },{
                name: 'detail_material_nama' ,mapping:'detail_material_nama'
            },{
                name: 'detail_material_satuan' ,mapping:'detail_material_satuan'
            },{
                name: 'mos_total_volume' ,mapping:'mos_total_volume'
            },{
                name: 'mos_total_harsat' ,mapping:'mos_total_harsat'
            },{
                name: 'total_jumlah_mos' ,mapping:'total_jumlah_mos'
            },{
                name: 'mos_diakui_volume' ,mapping:'mos_diakui_volume'
            },{
                name: 'total_mos_diakui' ,mapping:'total_mos_diakui'
            },{
                name: 'mos_belum_volume' ,mapping:'mos_belum_volume'
            },{
                name: 'total_mos_belum_diakui' ,mapping:'total_mos_belum_diakui'
            },{
                name: 'mos_keterangan' ,mapping:'mos_keterangan'
            }
         ]
    });

    Ext.define('analisabln', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'value', mapping: 'value'},
            {name: 'text', mapping: 'text'},
            {name: 'kode_rap', mapping: 'kode_rap'},
            {name: 'satuan', mapping: 'satuan'},
            {name: 'harga', mapping: 'harga'},
            {name: 'id_detail_material', mapping: 'id_detail_material'},
            {name: 'detail_material_kode', mapping: 'detail_material_kode'},
            {name: 'volume_total', mapping: 'volume_total'}
         ]
    });

    Ext.define('isi_uraian', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'satuan', mapping: 'satuan'},
            {name: 'harga', mapping: 'harga'}
         ]
    });

    var store_data_uraian = Ext.create('Ext.data.Store', {
        model: 'analisabln',
        remoteFilter: true,
        autoLoad: true,        
         proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/get_uraian_mos',
             reader: {
                 type: 'json',
                 root: 'data'
             }
         }
    });

    var store_isi_data_uraian = Ext.create('Ext.data.Store', {
        model: 'isi_uraian',
        autoLoad: false,        
         proxy: {
            type: 'ajax',
            actionMethods: {
                read: 'POST'
            },
            url: '<?php echo base_url() ?>pengendalian/get_isi_uraian_mos',
            reader: {
                 type: 'json',
                 root: 'data'
            }
         }
    });

    
    var store_mos = Ext.create('Ext.data.Store', {
        model: 'mos',
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/get_data/mos',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        remoteFilter: true,
        autoLoad: false
    });

Ext.onReady(function() {
store_mos.load();

    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            afteredit: function(rec,obj) {
                var selectedNode = grid_mos.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                id = data.id;

                Ext.Ajax.request({
                     url: '<?=base_url();?>pengendalian/mos_action/edit',
                        method: 'POST',
                        params: {
                            'id' :  id,
                            'volume_total': data.mos_total_volume,
                            // 'harga_satuan': data.mos_total_harsat,
                            'volume_diakui': data.mos_diakui_volume,
                            'keterangan_diakui': data.mos_keterangan 
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    store_mos.load();                                        
                    },
                    failure: function() {
                    Ext.Msg.alert( "Status", "No Respond..!"); 
                    }
                });

                // console.log(id+tahap_nama_kendali+tahap_satuan_kendali+tahap_volume_kendali+tahap_harga_satuan_kendali);
            }   
        }
    });

var grid_mos = Ext.create('Ext.grid.Panel', {
        store: store_mos,     
        autoscroll:true,  
        plugins: [rowEditing],
        columns: [
            {text: "URAIAN", flex:1, sortable: true, dataIndex: 'detail_material_nama'},
            {text: "SATUAN", width: 60, sortable: true, dataIndex: 'detail_material_satuan'},
            {text: "TOTAL MOS", flex:1,
                columns: [                    
                    {text: "VOLUME", flex:1, sortable: true, dataIndex: 'mos_total_volume',
                        editor: {
                            xtype: 'numberfield'
                        }
                    },
                    {text: "HARGA SATUAN", flex:1, sortable: true, dataIndex: 'mos_total_harsat'
                    // ,
                    //     editor: {
                    //         xtype: 'numberfield'
                    //     }
                    },
                    {text: "JUMLAH HARGA", flex:1, sortable: true, dataIndex: 'total_jumlah_mos'}
                ]
            },
            {text: "MOS DIAKUI OWNER", flex:1,
                columns: [                    
                    {text: "VOLUME", flex:1, sortable: true, dataIndex: 'mos_diakui_volume',
                        editor: {
                            xtype: 'numberfield'
                        }
                    },
                    {text: "HARGA SATUAN", flex:1, sortable: true, dataIndex: 'mos_total_harsat'},
                    {text: "JUMLAH HARGA", flex:1, sortable: true, dataIndex: 'total_mos_diakui'}
                ]
            },
            {text: "MOS BELUM DIAKUI", flex:1,
                columns: [                    
                    {text: "VOLUME", flex:1, sortable: true, dataIndex: 'mos_belum_volume',
                        editor: {
                            xtype: 'numberfield'
                        }
                    },
                    {text: "JUMLAH HARGA", flex:1, sortable: true, dataIndex: 'total_mos_belum_diakui'}
                ]
            },
            {text: "KETERANGAN", flex:1, sortable: true, dataIndex: 'mos_keterangan',
                        editor: {
                            xtype: 'textfield'
                        }
            },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(rec,rowIndex,colIndex){
                var selectedNode = rec.store.data.items[rowIndex].data;
                data = selectedNode;

                id = data.id;

                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                    if(resbtn == 'yes')
                    {
                        Ext.Ajax.request({
                             url: '<?=base_url();?>pengendalian/delete_data/mos',
                                method: 'POST',
                                params: {
                                    'id' :  id
                                    },                              
                            success: function() {
                            Ext.Msg.alert( "Status", "Delete successfully..!"); 
                            store_mos.load();                                        
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
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [
                {
                    text:'Open In New Tab',
                    iconCls:'icon-new',
                    handler:function(){
                        window.open(document.URL,'_blank');
                    }
                }
                ]
            },{
            dock: 'top',
            xtype: 'toolbar',
            items: ['Pengelompokan Berdasarkan Uraian : ',
                {
                    xtype : 'textfield',
                    itemId : 'text',
                    width: 150
                },'-','Periode : ',
                {
                    xtype: 'datefield',
                    itemId: 'tglawal',
                    emptyText: 'Pilih..',
                    format: 'Y-m-d',
                    width: 150,
                    value: new Date()
                },
                'S/D : ',
                {
                    xtype: 'datefield',
                    itemId: 'tglakhir',
                    emptyText: 'Pilih..',
                    format: 'Y-m-d',
                    width: 150,
                    value: new Date()
                }
            ,{
                text: 'Go >>',
                handler: function(){
                    data = this.up('grid').down('toolbar');
                    text = data.getComponent('text').value;
                    tglawal = data.getComponent('tglawal').rawValue;
                    tglakhir = data.getComponent('tglakhir').rawValue;
                    store_mos.load({
                        params:{
                            'text':text,
                            'tglawal':tglawal,
                            'tglakhir':tglakhir
                        }
                    });
                }
            },'-',{
                text: 'Print'
            }]
            },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [
                {
                    text: 'Tambah Data',
                    handler: function(){
                        tambahmos();
                    }
                }
            ]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [
                'Total : ',
                'Presentase : '
            ]
        }],
        height: '100%',
        title: 'Material On Site'
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
            items: grid_mos
        }]
    });
});

function tambahmos(){
    var formmos = Ext.widget({
        xtype: 'form',
        layout: 'form',
        frame: false,
        autoScroll: true,
        url: '<?php echo base_url(); ?>pengendalian/mos_action/tambah',
        bodyPadding: '5 5 0',
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 150,
            width: 380
        },
        items: [{
            // Fieldset in Column 1 - collapsible via toggle button
            xtype:'fieldset',
            itemId: 'fieldset1',
            title: 'FORM INPUT PERSEDIAAN',
            defaults: {anchor: '100%'},
            collapsible: true,
            items :[
            {
                xtype: 'datefield',
                fieldLabel: 'TANGGAL',
                afterLabelTextTpl: required,
                name: 'tanggal',
                allowBlank: false,
                format: 'Y-m-d',
                value: new Date(),
                submitFormat: 'Y-m-d'
            },{
                xtype: 'combobox',
                fieldLabel: 'URAIAN',
                name: 'uraian',
                store: store_data_uraian,
                valueField: 'value',
                displayField: 'text',
                queryMode: 'local',
                afterLabelTextTpl: required,
                emptyText: 'Pilih...',
                queryMode: 'remote',
                minChars:1,
                forceSelection:true,
                typeAhead:true,
                listeners:{
                    change: function(val){
                        // store_isi_data_uraian.load({
                        //     params:{
                        //         'id':val.value
                        //     },
                        //     callback: function(records, options, success){
                        //         satuan = records[0].data.satuan;
                        //         harga = records[0].data.harga;
                            // store_data_uraian.load();
                            var index = store_data_uraian.findExact('value',val.value);
                            if (index != -1) {
                                var rec = store_data_uraian.getAt(index);
                                satuan = rec.get('satuan');
                                harga = rec.get('harga');
                                id_detail_material = rec.get('id_detail_material');
                                detail_material_kode = rec.get('detail_material_kode');
                                volume_total = rec.get('volume_total');

                                fieldset = formmos.getComponent('fieldset1');

                                fieldset.getComponent('satuan').setValue(satuan);
                                fieldset.getComponent('harga').setValue(harga);
                                fieldset.getComponent('id_detail_material').setValue(id_detail_material);
                                fieldset.getComponent('detail_material_kode').setValue(detail_material_kode);
                                // fieldset.getComponent('volume_total').setValue(volume_total);
                            }
                        //     }
                        // })
                    }
                }
            },{
                xtype: 'textfield',
                itemId: 'satuan',
                fieldLabel: 'SATUAN',
                name: 'satuan',
                readOnly:true
            },{
                xtype: 'numberfield',
                fieldLabel: 'HARGA SATUAN',
                itemId: 'harga',
                minValue: 0,
                name: 'harga_satuan',
                readOnly:true
            },{
                xtype: 'textfield',
                fieldLabel: 'ID Detail Material',
                itemId: 'id_detail_material',
                name: 'id_detail_material',
                hidden:true,
                readOnly:true
            },{
                xtype: 'textfield',
                fieldLabel: 'Kode Detail Material',
                itemId: 'detail_material_kode',
                name: 'detail_material_kode',
                hidden:true,
                readOnly:true
            },{
                xtype: 'numberfield',
                fieldLabel: 'VOLUME TOTAL',
                itemId: 'volume_total',
                name: 'volume_total',
                minValue: 0,
                allowBlank: false,
                // readOnly:true
            }
            ]
        },{
            // Fieldset in Column 1 - collapsible via toggle button
            xtype:'fieldset',
            title: 'DIAKUI OWNER',
            defaults: {anchor: '100%'},
            collapsible: true,
            items :[
            {
                xtype: 'numberfield',
                fieldLabel: 'VOLUME',
                minValue: 0,
                name: 'volume_diakui',
                allowBlank: false,
                afterLabelTextTpl: required
            },{
                xtype: 'textarea',
                fieldLabel: 'KETERANGAN',
                name: 'keterangan_diakui',
                allowBlank: false,
                afterLabelTextTpl: required
            }
            ]
        }],

        buttons: [{
            text: 'Save',
            handler: function(){               
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        success: function() {
                            Ext.MessageBox.alert('Informasi','Insert successfully..!');
                            store_mos.load();
                        }
                    });
                    winaddmos.hide();
                }
            }
        }
        ,{
            text: 'Cancel',
            handler: function() {
               winaddmos.hide();
            }
        }]
    });

    var winaddmos = Ext.create('Ext.Window', {
        title: 'Tambah MOS',
        closeAction: 'hide',
        height: '80%',
        width: 450,
        layout: 'fit',
        items: formmos
    }).show();
}
</script>

</head>
<body>
</body>
</html>
