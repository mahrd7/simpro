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

	Ext.define('analisa_kontrak_terkini', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'bln',
                mapping: 'bln'
            },
            {
                name: 'thn',
                mapping: 'thn'},
            {
                name: 'status',
                mapping: 'status'
            },
            {
                name: 'tgl_akhir',
                mapping: 'tgl_akhir'
            },
            {
                name: 'blnnama',
                mapping: 'blnnama'
            },
            {
                name: 'blnnamanew',
                mapping: 'blnnamanew'
            },
            {
                name: 'tahap_tanggal_kendali',
                mapping: 'tahap_tanggal_kendali'
            },
            {
                name: 'tgl_akhir',
                mapping: 'tgl_akhir'
            },
            {
                name: 'kunci',
                mapping: 'kunci'
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

    Ext.define('get_value', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'value', mapping: 'value'}
         ]
    });

    var store_get_status_approve = Ext.create('Ext.data.Store', {
        model: 'get_value',
        autoLoad: false,        
         proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/get_status_approve',
             reader: {
                 type: 'json',
                 root: 'data'
             }
         }
    });

    var store_get_approve_terakhir = Ext.create('Ext.data.Store', {
        model: 'get_value',
        autoLoad: false,        
         proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/get_value/approve_terakhir',
             reader: {
                 type: 'json',
                 root: 'data'
             }
         }
    });

	var storebln = Ext.create('Ext.data.Store', {
        id: 'storebln',
        model: 'analisabln',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getbulan',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    storebln.load();

    var storethn = Ext.create('Ext.data.Store', {
        id: 'storethn',
        model: 'analisabln',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/gettahun',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    storethn.load();

    var store_kontrak_terkini = Ext.create('Ext.data.Store', {
        id: 'store_kontrak_terkini',
        model: 'analisa_kontrak_terkini',
        pageSize: 50,     
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/get_tanggal_kontrak_terkini',
         reader: {
             type: 'json',
             root: 'data'
         }
     },
        remoteFilter: true,
        autoLoad: false
    });

    store_kontrak_terkini.load();

Ext.onReady(function() {



var grid_kontrak_terkini = Ext.create('Ext.grid.Panel', {
        store: store_kontrak_terkini,
        columns: [
            {text: "MULAI", flex:1, sortable: true, dataIndex: 'blnnamanew'},
            {text: "AKHIR", flex:1, sortable: true, dataIndex: 'blnnama'},
            {text: "STATUS", flex:1, sortable: true, dataIndex: 'status'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
                handler: function(grid, rowIndex, colIndex){
                    rec = store_kontrak_terkini.getAt(rowIndex);
                    thn = rec.get('thn');
                    bln = rec.get('bln');
                    tgl = rec.get('tahap_tanggal_kendali');  
                    tgl_akhir = rec.get('tgl_akhir');              
                    kunci = rec.get('kunci');
                    window.location='<?php echo base_url() ?>pengendalian/kontrak_kini/'+kunci+'/'+bln+'/'+thn+'?tgl_rab='+tgl_akhir+'&tgl_awal='+tgl;
                }
            },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
                renderer: function (value, metadata, record) {
                    if (record.get('tahap_tanggal_kendali') == record.get('tgl_akhir')) {
                        this.items[0].icon = '<?=base_url();?>assets/images/delete.gif';
                    } else {
                        this.items[0].icon = '<?=base_url();?>assets/images/back.png';
                    }
                },
                handler: function(grid, rowIndex, colIndex){                    
                    rec = store_kontrak_terkini.getAt(rowIndex);
                    tgl = rec.get('tahap_tanggal_kendali');
                    tgl_akhir = rec.get('tgl_akhir');
                    status = rec.get('kunci');
                    if (tgl == tgl_akhir) {
                        url = 'all_kontrak_terkini';
                    } else {
                        url = 'all_kontrak_terkini_new';                        
                    }
                    if (status == 'close') {
                                    Ext.Msg.alert('Informasi','<center>Approval sudah terkunci</center>');
                    } else {
                        Ext.MessageBox.confirm('Delete', 'Apakah anda akan menghapus item ini?',function(resbtn){
                                if(resbtn == 'yes')
                                {
                                    Ext.Ajax.request({
                                        url: '<?=base_url();?>pengendalian/delete_data/'+url,
                                        method: 'POST',
                                        params: {
                                            'tgl_rab':tgl_akhir
                                        },                              
                                        success: function() {
                                        store_kontrak_terkini.load(); 
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
            },
            // {text: "",xtype: 'actioncolumn', width:55,  sortable: true,icon:'<?=base_url();?>assets/images/approve.gif',
            //     handler: function(grid, rowIndex, colIndex){
            //         rec = store_kontrak_terkini.getAt(rowIndex);
            //         tgl = rec.get('tgl_akhir');
            //         tgl_akhir = rec.get('tgl_akhir');
            //         store_get_approve_terakhir.load({
            //             callback: function(records, options, success){
            //                 if (records[0].data.value == tgl_akhir) {
            //                     confirm_approve(tgl);
            //                 } else {
            //                     Ext.Msg.alert('Informasi','<center>Tidak diizinkan melakukan perubahan Approval ulang dibulan sebelumnya</center>');
            //                 }
            //             }
            //         });
            //     }
            // }
        ],
        columnLines: true,
        // inline buttons
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Tambah Data',
                tooltip:'Tambah Data',
                handler: function(records, options, success){
                        store_get_status_approve.load({
                            callback: function(records, options, success){
                                if (records[0].data.value == 'open') {
                                    Ext.Msg.alert('Informasi','<center>Maaf sebelum membuat LAPORAN BULANAN bulan berikutnya,<br>LAPORAN BULANAN bulan sebelumnya harus di APPROVE terlebih dahulu<br><br>Terima Kasih</center>');
                                } else {
                                    if (store_kontrak_terkini.getRange().length > 0) {
                                        add_kontrak_terkini();
                                    } else {                                        
                                        winaddakk.show();
                                    } 
                                }
                            }
                        });
                                       
                	// winaddakk.show();
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
        title: 'Kontrak Terkini'
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
            items: grid_kontrak_terkini
        }]
    });

var formaddakk = Ext.widget({
        xtype: 'form',
        layout: 'form',
        url: '<?php echo base_url(); ?>pengendalian/kontrak_kini',
        frame: false,
        bodyPadding: '5 5 0',
        width: 350,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 75
        },
        items: [{
            xtype: 'combobox',
            fieldLabel: 'Bulan',
            name: 'bln',
            store: storebln,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih Bulan...',
            value: new Date().getMonth() + 1
        },{
            xtype: 'combobox',
            fieldLabel: 'Tahun',
            name: 'thn',
            store: storethn,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih Tahun...',
            value:new Date().getFullYear()
        }],

        buttons: [{
            text: 'Pilih',
            handler: function(){               
            	var bln = formaddakk.getForm().findField('bln').getValue();
            	var thn = formaddakk.getForm().findField('thn').getValue();
            	// console.log(bln+"/"+thn);
                window.location='<?php echo base_url() ?>pengendalian/kontrak_kini/open/'+bln+'/'+thn+'?tgl_rab='+thn+'-'+bln+'-01';
                winaddakk.hide();
            }
        }
        ,{
            text: 'Cancel',
            handler: function() {
               winaddakk.hide();
            }
        }]
    });

var winaddakk = Ext.create('Ext.Window', {
        title: 'Tambah Kontrak Terkini',
        closeAction: 'hide',
		modal: true,
        height: 130,
        width: 300,
        layout: 'fit',
        items: formaddakk
    });
});

function confirm_approve(tgl,tgl_akhir){
    var form = Ext.widget({
            xtype: 'form',
            layout: 'form',
            id: 'formdoc',
            url: '<?php echo base_url(); ?>pengendalian/approve/'+tgl+'/'+tgl_akhir,
            frame: false,
            bodyPadding: '5 5 0',
            fieldDefaults: {
                msgTarget: 'side',
                labelWidth: 75
            },
            items: [{
                xtype: 'textfield',
                fieldLabel: 'Username',
                name: 'username',
                allowBlank: false
            },{
                xtype: 'textfield',
                fieldLabel: 'Password',
                name: 'password',
                inputType: 'password',
                allowBlank: false
            }],
            buttons: [{
                text: 'Ok',
                handler: function(){  
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        form.submit({
                            success: function(response, opts) {
                                winaddakk.hide();
                                Ext.MessageBox.alert('Informasi',opts.result.data);
                                form.reset();
                                store_kontrak_terkini.load();
                            }
                        });
                    } else {
                        Ext.Msg.alert('Informasi', 'Isi Semua');
                    }
                }
            }
            ,{
                text: 'Cancel',
                handler: function() {
                   winaddakk.hide();
                }
            }]
        });

    var winaddakk = Ext.create('Ext.Window', {
            title: 'Confirm Approve',
            closeAction: 'hide',
            modal: true,
            height: 130,
            width: 300,
            layout: 'fit',
            items: form
    }).show();
}

function add_kontrak_terkini(){
    var form = Ext.widget({
            xtype: 'form',
            layout: 'form',
            url: '<?php echo base_url(); ?>pengendalian/add_data/kontrak_terkini',
            frame: false,
            bodyPadding: '5 5 0',
            items: [{
                xtype: 'radiogroup',
                fieldLabel: '<b>Tambah Data </b>',
                labelWidth: 82,
                columns: 1,
                items: [
                    {boxLabel: 'Tambah (Tanpa Kontrak Terkini)', name: 'status_tambah_kontrak', inputValue: 'non_kontrak', checked: true},
                    {boxLabel: 'Tambah (Beserta Kontrak Terkini)', name: 'status_tambah_kontrak', inputValue: 'kontrak'},
                ]
            }],
            buttons: [{
                text: 'Ok',
                handler: function(){  
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        form.submit({
                            success: function(response, opts) {
                                winaddakk.hide();
                                Ext.MessageBox.alert('Informasi',"Insert successfully");
                                form.reset();
                                store_kontrak_terkini.load();
                            }
                        });
                    }
                }
            }
            ,{
                text: 'Cancel',
                handler: function() {
                   winaddakk.hide();
                }
            }]
        });

    var winaddakk = Ext.create('Ext.Window', {
            title: 'Tambah Kontrak Terkini',
            closeAction: 'hide',
            modal: true,
            height: 130,
            width: 320,
            layout: 'fit',
            items: form
    }).show();
}
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>
