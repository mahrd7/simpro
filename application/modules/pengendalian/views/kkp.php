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
    
	Ext.define('kkp', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'id', mapping:'kkp_id'
            },
            {
                name: 'kkp_uraian', mapping:'kkp_uraian'
            },
            {
                name: 'kkp_tempat', mapping:'kkp_tempat'
            },
            {
                name: 'kkp_rencana', mapping:'kkp_rencana'
            },
            {
                name: 'kkp_tgl', mapping:'kkp_tgl'
            },
            {
                name: 'jabatan', mapping:'jabatan'
            }
         ]
    });

    Ext.define('analisabln', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'value', mapping: 'value'},
            {name: 'text', mapping: 'text'}
         ]
    });

    var store_data_jabatan = Ext.create('Ext.data.Store', {
        model: 'analisabln',
        autoLoad: true,        
         proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/get_jabatan',
             reader: {
                 type: 'json',
                 root: 'data'
             }
         }
    });

    var storekkp = Ext.create('Ext.data.Store', {
        model: 'kkp',  
        remoteFilter: true,
        autoLoad: false,
        proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/get_data/kkp',
             reader: {
                 type: 'json',
                 root: 'data'
             }
        }
    });

Ext.onReady(function() {
    storekkp.load();

    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            afteredit: function(rec,obj) {
                var selectedNode = grid_kkp.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                id = data.id;
                tgl = new Date(data.kkp_tgl);
                year = tgl.getFullYear();
                month = tgl.getMonth() + 1;
                day = tgl.getDate();

                tgl_kkp = year+'-'+month+'-'+day;
                uraian = data.kkp_uraian;
                sebab = data.kkp_tempat;
                rencana_penanggulangan = data.kkp_rencana;
                jabatan = data.jabatan;

                Ext.Ajax.request({
                     url: '<?=base_url();?>pengendalian/kkp_action/edit',
                        method: 'POST',
                        params: {
                            'id' :  id,
                            'uraian': uraian, 
                            'sebab': sebab,
                            'rencana_penanggulangan': rencana_penanggulangan,
                            'waktu': tgl_kkp,
                            'jabatan': jabatan
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    storekkp.load();                                        
                    },
                    failure: function() {
                    Ext.Msg.alert( "Status", "No Respond..!"); 
                    }
                });

                // console.log(id+tahap_nama_kendali+tahap_satuan_kendali+tahap_volume_kendali+tahap_harga_satuan_kendali);
            }   
        }
    });

    var grid_kkp = Ext.create('Ext.grid.Panel', {
        store: storekkp,  
        autoscroll:true,
        plugins: [rowEditing],
        columns: [
            {text: "URAIAN KENDALA", flex:1, sortable: true, dataIndex: 'kkp_uraian',
                editor:{
                    xtype: 'textfield'
                }
            },
            {text: "SEBAB TERJADI/TEMPATNYA", flex:1, sortable: true, dataIndex: 'kkp_tempat',
                editor:{
                    xtype: 'textfield'
                }
            },
            {text: "RENCANA PENANGGULANGAN", flex:1, sortable: true, dataIndex: 'kkp_rencana',
                editor:{
                    xtype: 'textfield'
                }
            },
            {text: "WAKTU", flex:1, sortable: true, dataIndex: 'kkp_tgl', renderer: Ext.util.Format.dateRenderer('Y-m-d'),
                editor:{
                    xtype: 'datefield',
                    format: 'Y-m-d'
                }
            },
            {text: "PELAKUNYA", flex:1, sortable: true, dataIndex: 'jabatan',
                editor:{
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
                             url: '<?=base_url();?>pengendalian/delete_data/kkp',
                                method: 'POST',
                                params: {
                                    'id' :  id
                                    },                              
                            success: function() {
                            Ext.Msg.alert( "Status", "Delete successfully..!"); 
                            storekkp.load();                                        
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
                    storekkp.load({
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
                    tambahkkp();
                }
                }
            ]
        }],
        height: '100%',
        title: 'Kajian Kendala Proyek'
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
            items: grid_kkp
        }]
    });
});

function tambahkkp(){
    var formkkp = Ext.widget({
        xtype: 'form',
        layout: 'form',
        frame: false,
        autoScroll:true,
        url: '<?php echo base_url(); ?>pengendalian/kkp_action/tambah',
        bodyPadding: '5 5 0',
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 150
        },
        items: [{
            xtype: 'textarea',
            fieldLabel: 'URAIAN',
            afterLabelTextTpl: required,
            name: 'uraian',
            allowBlank: false
        },{
            xtype: 'textarea',
            fieldLabel: 'SEBAB TERJADI',
            afterLabelTextTpl: required,
            name: 'sebab',
            allowBlank: false
        },{
            xtype: 'textarea',
            fieldLabel: 'RENCANA PENANGGULANGAN',
            afterLabelTextTpl: required,
            name: 'rencana_penanggulangan',
            allowBlank: false
        },{
            xtype: 'datefield',
            fieldLabel: 'WAKTU',
            afterLabelTextTpl: required,
            name: 'waktu',
            allowBlank: false,
            format: 'Y-m-d',
            submitFormat: 'Y-m-d',
            value: new Date()
        },
        // {
        //     xtype: 'combobox',
        //     fieldLabel: 'PELAKUNYA',
        //     name: 'jabatan',
        //     store: store_data_jabatan,
        //     valueField: 'value',
        //     displayField: 'text',
        //     queryMode: 'local',
        //     afterLabelTextTpl: required,
        //     emptyText: 'Pilih...',
        //     queryMode: 'remote',
        //     minChars:1,
        //     forceSelection:true,
        //     typeAhead:true
        // },
        {
            xtype: 'textarea',
            fieldLabel: 'PELAKUNYA',
            afterLabelTextTpl: required,
            name: 'jabatan',
            allowBlank: false
        },],

        buttons: [{
            text: 'Save',
            handler: function(){               
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        success: function() {
                            Ext.MessageBox.alert('Informasi','Insert successfully..!');
                            storekkp.load();
                        }
                    });
                    winaddkkp.hide();
                }
            }
        }
        ,{
            text: 'Cancel',
            handler: function() {
               winaddkkp.hide();
            }
        }]
    });

    var winaddkkp = Ext.create('Ext.Window', {
        closeAction: 'hide',
        height: 325,
        width: 520,
        layout: 'fit',        
        title: 'Tambah KKP',
        items: formkkp
    }).show();
}
</script>

</head>
<body>
</body>
</html>
