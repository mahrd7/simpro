<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript">

Ext.require([
    '*'
]);

Ext.Ajax.timeout = 3600000;

var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
var kode_induk;
    Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'tahap_kode_kendali', mapping: 'tahap_kode_kendali'},
            {name: 'tahap_nama_kendali', mapping: 'tahap_nama_kendali'},
            {name: 'tahap_satuan_kendali', mapping: 'tahap_satuan_kendali'},            
            {name: 'tahap_volume_kendali', mapping: 'tahap_volume_kendali'},
            {name: 'tahap_harga_satuan_kendali', mapping: 'tahap_harga_satuan_kendali'},
            {name: 'tahap_total_kendali', mapping: 'tahap_total_kendali'},
            {name: 'tahap_kode_induk_kendali', mapping: 'tahap_kode_induk_kendali'},            
            {name: 'tahap_tanggal_kendali', mapping: 'tahap_tanggal_kendali'},
            {name: 'tgl_update', mapping: 'tgl_update'},
            {name: 'ip_update', mapping: 'ip_update'},
            {name: 'divisi_update', mapping: 'divisi_update'},            
            {name: 'waktu_update', mapping: 'waktu_update'},
            {name: 'tahap_volume_kendali_new', mapping: 'tahap_volume_kendali_new'},
            {name: 'tahap_total_kendali_new', mapping: 'tahap_total_kendali_new'},
            {name: 'tahap_harga_satuan_kendali_new', mapping: 'tahap_harga_satuan_kendali_new'},            
            {name: 'tahap_volume_kendali_kurang', mapping: 'tahap_volume_kendali_kurang'},
            {name: 'tgl_rencana_aak', mapping: 'tgl_rencana_aak'},
            {name: 'volume_rencana', mapping: 'volume_rencana'},
            {name: 'volume_rencana1', mapping: 'volume_rencana1'},            
            {name: 'volume_eskalasi', mapping: 'volume_eskalasi'},
            {name: 'harga_satuan_eskalasi', mapping: 'harga_satuan_eskalasi'},
            {name: 'rencana_volume_eskalasi', mapping: 'rencana_volume_eskalasi'},
            {name: 'rencana_harga_satuan_eskalasi', mapping: 'rencana_harga_satuan_eskalasi'},            
            {name: 'is_nilai', mapping: 'is_nilai'},
            {name: 'tahap_total_kendali_kurang', mapping: 'tahap_total_kendali_kurang'},
            {name: 'total_tambah_kurang', mapping: 'total_tambah_kurang'},
            {name: 'total_volume_rencana', mapping: 'total_volume_rencana'},            
            {name: 'tot_rencana1', mapping: 'tot_rencana1'},
            {name: 'tot_rencana2', mapping: 'tot_rencana2'},
            {name: 'vol_tambah_kurang', mapping: 'vol_tambah_kurang'},
            {name: 'proyek_id', mapping: 'proyek_id'},            
            {name: 'user_update', mapping: 'user_update'},
            {name: 'id_kontrak_terkini', mapping: 'id_kontrak_terkini'}
         ]
    });

    Ext.define('mdl_terkini', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_kontrak_terkini', mapping: 'id_kontrak_terkini'},
            {name: 'rab_tahap_kode_kendali', mapping: 'rab_tahap_kode_kendali'},
            {name: 'rab_tahap_nama_kendali', mapping: 'rab_tahap_nama_kendali'},
            {name: 'rab_tahap_satuan_kendali', mapping: 'rab_tahap_satuan_kendali'},            
            {name: 'rab_tahap_volume_kendali', mapping: 'rab_tahap_volume_kendali'},
            {name: 'rab_tahap_harga_satuan_kendali', mapping: 'rab_tahap_harga_satuan_kendali', type:'float'},
            {name: 'jml_rab', mapping: 'jml_rab', type:'float'},
            {name: 'tahap_kode_kendali', mapping: 'tahap_kode_kendali'},            
            {name: 'tahap_nama_kendali', mapping: 'tahap_nama_kendali'},
            {name: 'tahap_satuan_kendali', mapping: 'tahap_satuan_kendali'},
            {name: 'tahap_volume_kendali', mapping: 'tahap_volume_kendali'},
            {name: 'tahap_harga_satuan_kendali', mapping: 'tahap_harga_satuan_kendali', type:'float'},
            {name: 'jml_kontrak_kini', mapping: 'jml_kontrak_kini', type:'float'},            
            {name: 'tahap_volume_kendali_new', mapping: 'tahap_volume_kendali_new'},
            {name: 'jml_tambah', mapping: 'jml_tambah', type:'float'},
            {name: 'tahap_volume_kendali_kurang', mapping: 'tahap_volume_kendali_kurang'},
            {name: 'jml_kurang', mapping: 'jml_kurang', type:'float'},        
            {name: 'volume_eskalasi', mapping: 'volume_eskalasi'},
            {name: 'harga_satuan_eskalasi', mapping: 'harga_satuan_eskalasi', type:'float'},
            {name: 'jml_eskalasi', mapping: 'jml_eskalasi', type:'float'},            
            {name: 'vol_total', mapping: 'vol_total'},
            {name: 'jml_total', mapping: 'jml_total', type:'float'},
            {name: 'ishaschild', mapping: 'ishaschild'}

         ]
    });

    Ext.define('mdl_sub_terkini', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'tahap_kode_kendali', mapping: 'tahap_kode_kendali'},
            {name: 'tahap_nama_kendali', mapping: 'tahap_nama_kendali'},
            {name: 'tahap_satuan_kendali', mapping: 'tahap_satuan_kendali'},            
            {name: 'tahap_volume_kendali', mapping: 'tahap_volume_kendali'},
            {name: 'tahap_volume_kendali_new', mapping: 'tahap_volume_kendali_new'},
            {name: 'harga_sub', mapping: 'harga_sub'},
            {name: 'id_kontrak_terkini', mapping: 'id_kontrak_terkini'}
         ]
    });

    Ext.define('mdl_combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value'}
         ]
    });

    Ext.define('mdl_get_data', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'value', mapping: 'value'}
         ]
    });

    var store_get_sub_kode = Ext.create('Ext.data.Store', {
        id: 'store_get_sub_kode',
        model: 'mdl_get_data',
        remoteSort: true,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/get_sub_kode',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

     var store_get_kode = Ext.create('Ext.data.Store', {
        id: 'store_get_kode',
        model: 'mdl_get_data',
        remoteSort: true,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/get_kode/kk',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var storesatuan = Ext.create('Ext.data.Store', {
        model: 'mdl_combo',
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getlistsatuan',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var storeterkini = Ext.create('Ext.data.TreeStore', {
        model: 'mdl_terkini',
        remoteSort: true,
        expanded: true,
        autoLoad: false,        
            proxy: {
                timeout: 900000,
                async: false,
                cache: false,
                type: 'ajax',
                url: '<?php echo base_url() ?>pengendalian/get_data_kk/kk',
                reader: 'json'
            },
        listeners:{
            beforeload:function(){
                Ext.Msg.wait("Loading...","Please Wait");
            },
            load:function(){
                Ext.MessageBox.hide();
            }
        }
    });

    var storesubkontrakterkini = Ext.create('Ext.data.Store', {
        id: 'storesubkontrakterkini',
        model: 'mdl',
        remoteSort: true,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/get_sub_kontrk_terkini/kk',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var store_sub_terkini = Ext.create('Ext.data.Store', {
        id: 'store_sub_terkini',
        model: 'mdl_sub_terkini',
        remoteSort: true,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/get_sub_kontrk_terkini/kk',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

Ext.onReady(function() {
    storeterkini.load({
        params: {
            'tgl_rab':'<?php echo $tgl_rab; ?>'
        }
    }        
    );

    var cellEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,     
        listeners: {
            beforeedit: function(rec,obj){   
                if (obj.record.get('ishaschild') == 1) {
                    return false;
                }
            },
            afteredit: function(rec,obj) {

                var selectedNode = grid.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                // if (obj.colIdx==14) {
                //     field = 'tahap_volume_kendali_new';
                // } else if (obj.colIdx==16) {
                //     field = 'tahap_volume_kendali_kurang';
                // }

                id = data.id_kontrak_terkini;
                kode = data.tahap_kode_kendali;
                vol = data.tahap_volume_kendali;
                vol_tambah = data.tahap_volume_kendali_new;
                vol_kurang = data.tahap_volume_kendali_kurang;
                vol_eskalasi = data.volume_eskalasi;
                harga_eskalasi = data.harga_satuan_eskalasi;

                Ext.Ajax.request({
                     url: '<?=base_url();?>pengendalian/update_kk/kk',
                        method: 'POST',
                        params: {
                            'id' :  id,
                            'vol': vol,
                            'vol_tambah': vol_tambah,
                            'vol_kurang': vol_kurang,
                            'vol_eskalasi': vol_eskalasi,
                            'harga_eskalasi': harga_eskalasi,
                            'kode': kode,
                            'tgl_rab': '<?php echo $tgl_rab ?>'
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    storeterkini.load({
                                    params: {
                                        'tgl_rab':'<?php echo $tgl_rab; ?>'
                                    }
                                }        
                                );                                        
                    },
                    failure: function() {
                    Ext.Msg.alert( "Status", "No Respond..!"); 
                    }
                }); 

                // console.log(id);
            }   
        }
    });

    var grid = Ext.create('Ext.tree.Panel', {
        rootVisible: false,
        store: storeterkini,
        multiSelect: false,
        singleExpand: false,
        hideCollapseTool: false,
        height: '100%',
        autoScroll:true,
        title: 'ADDENDUM/AMANDEMEN KONTRAK S/D <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
        {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            renderer: function (value, metadata, record) {
                if (record.get('rab_tahap_kode_kendali') == record.get('tahap_kode_kendali')) {
                    this.items[0].icon = '<?=base_url();?>assets/images/recycle.png';
                    // this.items[0].tooltip = 'kosongkan data';
                } else {
                    this.items[0].icon = '<?=base_url();?>assets/images/delete.gif';
                    // this.items[0].tooltip = 'hapus data';
                }
            },
            handler: function(grid, rowIndex, colIndex, actionItem, event, record, row){
                // rec = storeterkini.getAt(rowIndex);
                id = record.get('id_kontrak_terkini');
                rab_kode = record.get('rab_tahap_kode_kendali');
                kode = record.get('tahap_kode_kendali');
                if (rab_kode == kode) {
                    Ext.MessageBox.confirm('Reset', 'Apakah anda akan mereset data item ini?',function(resbtn){
                        if(resbtn == 'yes')
                            {
                                Ext.Ajax.request({
                                url: '<?=base_url();?>pengendalian/reset/item_kontrak_terkini',
                                method: 'POST',
                                params: {
                                    'id':id
                                },                              
                                success: function() {
                                    storeterkini.load({
                                        params: {
                                            'tgl_rab':'<?php echo $tgl_rab; ?>'
                                        }
                                    }        
                                    );
                                Ext.Msg.alert( "Status", "Reset successfully..!");                                         
                                },
                                failure: function() {
                                }
                            });                                                                                     
                        }
                    });
                } else {
                    Ext.MessageBox.confirm('Delete', 'Apakah anda akan menghapus data item ini?',function(resbtn){
                        if(resbtn == 'yes')
                            {
                                Ext.Ajax.request({
                                url: '<?=base_url();?>pengendalian/delete_data/item_kontrak_terkini',
                                method: 'POST',
                                params: {
                                    'id':id,
                                    'kode':kode,
                                    'tgl_rab':'<?php echo $tgl_rab ?>'
                                },                              
                                success: function() {
                                    storeterkini.load({
                                        params: {
                                            'tgl_rab':'<?php echo $tgl_rab; ?>'
                                        }
                                    }        
                                    );
                                Ext.Msg.alert( "Status", "Delete successfully..!");                                         
                                },
                                failure: function() {
                                }
                            });                                                                                     
                        }
                    });
                }
            }
        },
        {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/add.gif',
            handler: function(grid, rowIndex, colIndex, actionItem, event, record, row){
                // rec = storeterkini.getAt(rowIndex);
                id = record.get('id_kontrak_terkini');
                kode = record.get('tahap_kode_kendali');
                // storesubkontrakterkini.load({
                //     params: {
                //         'kode': rec.get('kode_terkini'),
                //         'tgl_rab': '<?php echo $tgl_rab; ?>',
                //         'id': rec.get('id_kontrak_terkini')
                //     }
                // });   
                store_sub_terkini.load({
                    params: {
                        'kode': kode,
                        'tgl_rab': '<?php echo $tgl_rab; ?>',
                        'id': record.get('id_kontrak_terkini')
                    }
                }); 
                link(id,kode);             
            }
        },
            {text: "KONTRAK AWAL", sortable: false,
            columns:[
                {text: "KODE", xtype: 'treecolumn', sortable: true, width:100, dataIndex: 'rab_tahap_kode_kendali'},
                {text: "ITEM PEKERJAAN", width:150, sortable: true, dataIndex: 'rab_tahap_nama_kendali'},
                {text: "SAT", sortable: true, width:40, dataIndex: 'rab_tahap_satuan_kendali',
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
                {text: "VOL", sortable: true, width:50, dataIndex: 'rab_tahap_volume_kendali'},
                {text: "HARGA KONTRAK", width:120, sortable: true, dataIndex: 'rab_tahap_harga_satuan_kendali'},
                {text: "JUMLAH", sortable: true, width:120, dataIndex: 'jml_rab'}
            ]
        },
            {text: "KONTRAK TERKINI", sortable: false,
            columns:[
                {text: "KODE", sortable: true, width:60, dataIndex: 'tahap_kode_kendali'},
                {text: "ITEM PEKERJAAN", width:150, sortable: true, dataIndex: 'tahap_nama_kendali'},
                {text: "SAT", sortable: true, width:40, dataIndex: 'tahap_satuan_kendali',
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
                {text: "VOL", sortable: true, width:50, dataIndex: 'tahap_volume_kendali'},
                {text: "HARGA KONTRAK", width:120, sortable: true, dataIndex: 'tahap_harga_satuan_kendali'},
                {text: "JUMLAH", sortable: true, width:120, dataIndex: 'jml_kontrak_kini'}
            ]
        },
            {text: "PEKERJAAN TAMBAH", sortable: false,
            columns:[
                {text: "VOLUME", sortable: true, dataIndex: 'tahap_volume_kendali_new',
                        field: {   
                            editing: true,
                            xtype: 'numberfield',
                            name: 'a'
                        }
                },
                {text: "JUMLAH", sortable: true, dataIndex: 'jml_tambah'}
            ]
        },
            {text: "PEKERJAAN KURANG", sortable: false,
            columns:[
                {text: "VOLUME", sortable: true, dataIndex: 'tahap_volume_kendali_kurang',
                        field: {
                            xtype: 'numberfield'
                        }
                },
                {text: "JUMLAH", sortable: true, dataIndex: 'jml_kurang'}
            ]
        },
            {text: "ESKALASI", sortable: false,
            columns:[
                {text: "VOLUME",  sortable: true, dataIndex: 'volume_eskalasi',
                    field: {
                        xtype: 'numberfield'
                    }
                },
                {text: "HARGA SATUAN",  sortable: true, dataIndex: 'harga_satuan_eskalasi',
                    field: {
                        xtype: 'numberfield'
                    }
                },
                {text: "JUMLAH", sortable: true, dataIndex: 'jml_eskalasi'}
            ]
        },
            {text: "VOL.TOTAL PEKERJAAN", width:180, sortable: true, dataIndex: 'vol_total'},
            {text: "TOTAL JUMLAH", width:120, sortable: true, dataIndex: 'jml_total'}
        ],
        plugins: [cellEditing],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Tambah Data',
                tooltip:'Tambah Data',
                handler: function(){
                    store_get_kode.load({
                    params: {
                        'tgl_rab': '<?php echo $tgl_rab; ?>'
                    },
                    callback: function(records, options, success){
                        kode = records[0].data.value;
                        // console.log(kode);                   
                        tambah(kode);
                    }
                    });
                }
            },{
                text:'Print',
                tooltip:'Print',
                handler: function(){

                }
            },{
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    var url ='<?php echo base_url(); ?>pengendalian/kontrak_terkini';
                    // console.log(url);
                    window.location=url;
                }
            }]
        }]
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

function tambah(kode){

    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>pengendalian/insert/kontrak_terkini',
        id:'frmadd ',
        bodyStyle: 'padding:5px 5px 0',
        autoScroll: true,
        frame: false,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 120
        },
            items: [{
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'tgl_rab',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: '<?php echo $tgl_rab; ?>',
                hidden: true
            },{
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'tgl_awal',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: '<?php echo $tgl_awal; ?>',
                hidden: true
            },
            {
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'kode',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: kode
            },{
                xtype:'textfield',
                fieldLabel: 'Tahap Pekerjaan',
                anchor: '-5',
                name: 'tahap_pekerjaan',
                afterLabelTextTpl: required,
                allowBlank: false
            },{
                xtype:'combobox',
                fieldLabel: 'Satuan',
                anchor: '-5',
                name: 'satuan',
                store: storesatuan,
                valueField: 'value',
                displayField: 'text',
                typeAhead: true,
                queryMode: 'local',
                emptyText: 'Pilih..',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'numberfield',
                fieldLabel: 'Volume',
                anchor: '-5',
                name: 'volume',
                afterLabelTextTpl: required,
                allowBlank: false
            },{
                xtype:'textfield',
                fieldLabel: 'Harga Satuan',
                anchor: '-5',
                name: 'harga_satuan',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: '0'
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
                                storeterkini.load({
                                    params: {
                                        'tgl_rab':'<?php echo $tgl_rab; ?>'
                                    }
                                }        
                                );
                            // window.location='<?php echo base_url() ?>pengendalian/kontrak_kini/'+bln+'/'+thn+'?tgl_rab=';
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
        title: 'Tambah',
        closeAction: 'hide',
        width: 400,
        minHeight: '80%',
        maxHeight: 220,
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

function link(id,kode){

    var grid = Ext.create('Ext.grid.Panel', {
        store: store_sub_terkini,
        autoscroll: true,
        frame: false,
        // title: 'Current Budget <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "ITEM PEKERJAAN", width:240, sortable: true, dataIndex: 'tahap_nama_kendali'},
            {text: "SATUAN", width:55, sortable: true, dataIndex: 'tahap_satuan_kendali',
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
            {text: "VOLUME KONTRAK", width:150, sortable: true, dataIndex: 'tahap_volume_kendali'},
            {text: "VOLUME TAMBAH/KURANG", width:150, sortable: true, dataIndex: 'tahap_volume_kendali_new'},
            {text: "HARGA", width:100, sortable: true, dataIndex: 'harga_sub'}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Tambah Data',
                tooltip:'Tambah Data',
                handler: function(){                    
                    store_get_sub_kode.load({
                    params: {
                        'kode': kode,
                        'tgl_rab': '<?php echo $tgl_rab ?>',
                        'id': id,
                        'info': 'kontrak_terkini'
                    },
                    callback: function(records, options, success){
                        kode_induk = records[0].data.value;
                        tambahlink(kode,kode_induk);
                        // console.log(kode_induk);
                        
                    }
                    });
                }
            },'-',{
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    winadd.hide();
                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [                
            'Total: '
            ]
        }],
        width: '100%',
        height: '100%'
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'FORM SUB KONTRAK TERKINI',
        closeAction: 'hide',
        width: '80%',
        height: '80%',
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

function tambahlink(kode,kode_induk){
    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>pengendalian/insert/sub_kontrak_terkini/kk',
        width:'100%',
        height:'100%',
        bodyStyle: 'padding:10px',
        autoScroll: true,
        frame: false,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 160
        },
            items: [{
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'kds',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: kode,
                hidden: true
            },{
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'tgl_rab',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: '<?php echo $tgl_rab ?>',
                hidden: true
            },{
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'tgl_awal',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: '<?php echo $tgl_awal ?>',
                hidden: true
            },{
                xtype:'textfield',
                fieldLabel: 'Sub Kode',
                anchor: '-5',
                name: 'kode',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: kode+'.'+kode_induk
            },{
                xtype:'textarea',
                fieldLabel: 'Sub Tahap Pekerjaan',
                anchor: '-5',
                name: 'tahap_pekerjaan',
                afterLabelTextTpl: required,
                allowBlank: false
        },{
            xtype: 'combobox',
            fieldLabel: 'Satuan',
            name: 'satuan',
            store: storesatuan,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..'
        },{
                xtype:'textfield',
                fieldLabel: 'Harga Satuan',
                anchor: '-5',
                name: 'harga_satuan',
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
                                storeterkini.load({
                                    params: {
                                        'tgl_rab':'<?php echo $tgl_rab; ?>'
                                    }
                                }        
                                );  
                            store_sub_terkini.load({
                                params: {
                                'kode': kode,
                                'tgl_rab': '<?php echo $tgl_rab ?>',
                            }
                            });
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
        title: 'FORM TAMBAH SUB KONTRAK TERKINI',
        closeAction: 'hide',
        width: 500,
        height: '80%',
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>