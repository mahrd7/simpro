<html>
<head>
<style type="text/css">
.link {
    text-decoration: none;
    color: rgb(11, 100, 214);
}
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

<script type="text/javascript">
//var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

Ext.require([
    '*'
]);

	Ext.define('analisa_rapa', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'tahap_kode_kendali', mapping: 'tahap_kode_kendali'},
            {name: 'tahap_kendali_id', mapping: 'tahap_nama_kendali_id'},
            {name: 'tahap_satuan_kendali', mapping: 'tahap_satuan_kendali'},
            {name: 'tahap_keterangan_kendali', mapping: 'tahap_keterangan_kendali'},
            {name: 'tahap_volume_kendali', mapping: 'tahap_volume_kendali'},
            {name: 'tahap_harga_satuan_kendali', mapping: 'tahap_harga_satuan_kendali', type: 'float',
            convert: function (value, record) {
            var kode = record.get('tahap_kode_kendali');
            if (value && kode.length==1) { 
                var val = "<b>Rp. "+Ext.util.Format.number(value, '0,000.00')+"</b>";
                return val;
            } else {
                var val = "Rp. "+Ext.util.Format.number(0, '0,000.00');
                return val;
            }
            }
            },
            {name: 'tahap_harga_satuan_kendali', mapping: 'tahap_harga_satuan_kendali', type: 'float'},
            {name: 'tahap_total_kendali', mapping: 'tahap_total_kendali', type: 'float', 
            convert: function (value, record) {
            var kode = record.get('tahap_kode_kendali');
            if (value && kode.length==1) { 
                var val = "<b>Rp. "+Ext.util.Format.number(value, '0,000.00')+"</b>";
                return val;
            } else {
                var val = "Rp. "+Ext.util.Format.number(0, '0,000.00');
                return val;
            }
            }
            },
            {name: 'tahap_kode_induk_kendali', mapping: 'tahap_kode_induk_kendali'},
            {name: 'tahap_tanggal_kendali', mapping: 'tahap_tanggal_kendali'}/*,
            {name: 'user_update', mapping: 'user_update'},
            {name: 'tgl_update', mapping: 'tgl_update'},
            {name: 'ip_update', mapping: 'ip_update'},
            {name: 'divisi_update', mapping: 'divisi_update'},
            {name: 'waktu_update', mapping: 'waktu_update'}*/
         ]
    });


	var storebln = Ext.create('Ext.data.Store', {
        id: 'storebln',
        model: 'analisabln',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getbulan',
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
         url: '<?php echo base_url() ?>rbk/gettahun',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    storethn.load();

    var storesatuan = Ext.create('Ext.data.Store', {
        id: 'storectg',
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

    var storedivisi = Ext.create('Ext.data.Store', {
        id: 'storedivisi',
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getdivisicombo',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

    var storeproyek = Ext.create('Ext.data.Store', {
        id: 'storeproyek',
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getproyekcombo',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

    var storetanggal = Ext.create('Ext.data.Store', {
        id: 'storetanggal',
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/gettanggalcombo',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

    var storegetedithargasatuan = Ext.create('Ext.data.Store', {
        id: 'storegetedithargasatuan',
        model: 'mdl_getedithargasatuan',
        remoteSort: true,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getedithargasatuan',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

    var storesubbidang = Ext.create('Ext.data.Store', {
        id: 'storectg',
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getsubbidangkode',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

    
Ext.onReady(function() {


var store_rapa = Ext.create('Ext.data.TreeStore', {
        id: 'store_rapa',
        model: 'analisa_rapa',
        //pageSize: 50,     
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getdata2/rapa_detail/<?php echo $bln;?>/<?php echo $thn;?>/',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        //remoteFilter: true,
        autoLoad: false
    });
    store_rapa.load();

var grid = Ext.create('Ext.tree.Panel', {
        id:'grid',
		collapsible: true,
        useArrows: true,
        rootVisible: false,
        
        multiSelect: true,
        singleExpand: true,
        store: store_rapa,
        autoscroll: true,
        title: 'RAPA <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
            //{text: "", width:50, sortable: true, dataIndex: 'tahap_kendali_id'},
            {text: "ITEM PEKERJAAN", width:400, sortable: true, dataIndex: 'tahap_kode_kendali',xtype: 'treecolumn'},
            {text: "SATUAN", width:100, sortable: true, dataIndex: 'tahap_satuan_kendali'},
            {text: "VOLUME", width:100, sortable: true, dataIndex: 'tahap_volume_kendali'},
            {text: "HARGA SATUAN", width:200, sortable: true, dataIndex: 'tahap_harga_satuan_kendali'},
            {text: "JUMLAH", width:250, sortable: true, dataIndex: 'tahap_total_kendali'},
            {text: "Kontrol",
            columns:[
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/tomboledit.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = store_rapa.getAt(rowIndex);
            }},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/tomboldel.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = store_rapa.getAt(rowIndex);
                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                            if(resbtn == 'yes')
                            {
                                                                                                                        
                            }
                });
            }},{text: "",xtype: 'actioncolumn', width:28,icon:'<?=base_url();?>assets/images/tombollog.gif',
            handler: function(grid, rowIndex, colIndex){
            }},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/tombolplus.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = store_rapa.getAt(rowIndex);
                analisa();
            }}
            ]}
        ],
        //columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Edit Harga Satuan',
                tooltip:'Edit Harga Satuan',
                handler: function(){
                    confirmedithargasatuan();
                }
            },'-',{
                text:'Password Harga Satuan',
                tooltip:'Password Harga Satuan',
                handler: function(){

                }
            },'-',{
                text:'Print Analisa',
                tooltip:'Print Analisa',
                handler: function(){

                }
            },'-',{
                text:'Print Tahap',
                tooltip:'Print Tahap',
                handler: function(){

                }
            },'-',{
                text:'Print APEK',
                tooltip:'Print APEK',
                handler: function(){

                }
            },'-',{
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    var url ='<?php echo base_url(); ?>rbk/pilih_rapa';
                    // console.log(url);
                    window.location=url;
                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text: 'Copy From RAB',
                handler: function(){

                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [                
            'Total : ',               
            'Prosentase Thd Kontrak : '
            ]
        }],
        width: '100%',
        height: '100%',
        renderTo: Ext.getBody()
        // ,
        // bbar: [Ext.create('Ext.toolbar.Paging', {
        //                      pageSize: 50,
        //                      store: store,
        //                      displayInfo: true
        //              })
        // ]
    });
    store_rapa.load();
});

function analisa(id,no){

    var storeanalisa = Ext.create('Ext.data.Store', {
        id: 'storeanalisa',
        model: 'mdl_analisa',
        data: dummyanalisa,
        groupField: 'kode_analisa'
    });

      var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
            groupHeaderTpl: '{name} : ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
            hideGroupedHeader: true,
            startCollapsed: false,
            id: 'analisa'
        }),
        groups = storeanalisa.getGroups()
        ;

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: storeanalisa,
        autoscroll: true,
        frame: false,
        features: [groupingFeature],
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'no'},
            {text: "SUMBER DAYA", width:240, sortable: true, dataIndex: 'sumber_daya'},
            {text: "SATUAN", width:55, sortable: true, dataIndex: 'satuan'},
            {text: "HARGA SATUAN", width:100, sortable: true, dataIndex: 'harga_satuan'},
            {text: "KOEFISIEN", width:100, sortable: true, dataIndex: 'koefisien'},
            {text: "JUMLAH", width:100, sortable: true, dataIndex: 'jumlah'},
            {text: "Kontrol",
            columns:[
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/tomboledit.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = storeanalisa.getAt(rowIndex);
                id = rec.get('id');
                harga_satuan = rec.get('harga_satuan');
                koefisien = rec.get('koefisien');
                editanalisa(id,harga_satuan,koefisien);
            }},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/tomboldel.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = storeanalisa.getAt(rowIndex);
                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                            if(resbtn == 'yes')
                            {
                                                                                                                        
                            }
                });
            }}
            ]}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Copy Analisa',
                tooltip:'Copy Analisa',
                handler: function(){
                    copy_analisa_dari_orgn_budget();
                }
            },'-',{
                text:'Delete Analisa',
                tooltip:'Delete Analisa',
                handler: function(){

                }
            },'-',{
                text:'Copy Ke',
                tooltip:'Copy Ke',
                handler: function(){
                    copy_analisa_ke_orgn_budget();
                }
            },'-',{
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    winadd.hide();
                }
            }]
        },{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [{
                text:'Tambah',
                tooltip:'Tambah',
                handler: function(){
                    tambahanalisa();
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
        height: '100%',
        renderTo: Ext.getBody(),
        bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: storeanalisa,
                             displayInfo: true
                     })
        ]
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'Analisa ('+no+')',
        closeAction: 'hide',
        width: '80%',
        height: '80%',
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

function tambahanalisa(){

    var storesumberdaya = Ext.create('Ext.data.Store', {
        id: 'storeanalisa',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        model: 'mdl_sumber_daya',
        data: dummysumberdaya
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: storesumberdaya,
        autoscroll: true,
        frame: false,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        columns: [
            {text: "KODE MATERIAL", width:100, sortable: true, dataIndex: 'kode'},
            {text: "NAMA MATERIAL", width:240, sortable: true, dataIndex: 'nama'},
            {text: "SPESIFIKASI", width:100, sortable: true, dataIndex: 'spesifikasi'},
            {text: "PROPINSI", width:120, sortable: true, dataIndex: 'provinsi'},
            {text: "KOTA", width:90, sortable: true, dataIndex: 'kota'},
            {text: "KOEFISIEN", width:100, sortable: true, dataIndex: 'koefisien'},
            {text: "HARGA SATUAN", width:100, sortable: true, dataIndex: 'harga_satuan'}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
            'Pilih : ',{
                xtype: 'textfield',
                name:'pilih'
            },{
            xtype: 'combobox',
            name: 'cboshort',
            store: storesubbidang,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih..'
            },{
                text: 'Go>>'
            }
            ]
        },{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [{
                text:'Tambah',
                tooltip:'Tambah',
                handler: function(){

                }
            },{
                text:'Cancel',
                tooltip:'Cancel',
                handler: function(){
                    winadd.hide();
                }
            }]
        }],
        width: '100%',
        height: '100%',
        renderTo: Ext.getBody(),
        bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: storesumberdaya,
                             displayInfo: true
                     })
        ]
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'Tambah Analisa',
        closeAction: 'hide',
        width: '80%',
        height: '80%',
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

function editanalisa(id,harga_satuan,koefisien){
    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>',
        id:'frmadd ',
        width:'100%',
        height:'100%',
        bodyStyle: 'padding:5px 5px 0',
        autoScroll: true,
        frame: false,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 200
        },
            items: [{
                xtype:'textfield',
                fieldLabel: 'Id',
                anchor: '-5',
                name: 'id',
                afterLabelTextTpl: required,
                allowBlank: false,
                hidden: true,
                value: id
            },{
                xtype:'textfield',
                fieldLabel: 'MarkUp Harga Satuan',
                anchor: '-5',
                name: 'id',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: harga_satuan
            },{
                xtype:'textfield',
                fieldLabel: 'MarkUp Koefisien',
                anchor: '-5',
                name: 'id',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: koefisien
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
        title: 'FORM TAMBAH SUMBER DAYA',
        closeAction: 'hide',
        width: 400,
        height: 130,
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

function copy_analisa_dari_orgn_budget(){
    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummyctg
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        frame: false,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'no'},
            {text: "ITEM PEKERJAAN", width:400, sortable: true, dataIndex: 'item_pekerjaan'},
            {text: "HARGA SATUAN", width:120, sortable: true, dataIndex: 'harga'}
        ],
        columnLines: true,
        dockedItems: [{
            dock: 'top',
            xtype: 'toolbar',
            items: [{
                text: 'Copy Data Dari Recovery',
                handler: function(){
                    copy_analisa_recovery();
                    winadd.hide();
                }
            }
            ]
        },{
            xtype: 'toolbar',
            items: [
            'Divisi : ',{
            xtype: 'combobox',
            name: 'divisi',
            store: storedivisi,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            listeners: {
                change: function(combo){
                    // console.log(combo.value);
                    storeproyek.load({
                    params: {
                        divisi_kode: combo.value
                    }
                    });
                }
            }
        },
            'Nama Proyek : ',{
            xtype: 'combobox',
            name: 'proyek',
            store: storeproyek,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            listeners: {
                change: function(combo){
                    // console.log(combo.value);
                    storetanggal.load({
                    params: {
                        no_spk: combo.value 
                    }
                    });
                }
            }
        },
            'Tanggal : ',{
            xtype: 'combobox',
            name: 'tanggal',
            store: storetanggal,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..'
        },'-',{
            text: 'Go'
        }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text: 'Simpan',
                tooltip:'Simpan',
                handler: function(){

                }
            },{
                text: 'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    winadd.hide();
                }
            }]
        }],
        width: '100%',
        height: '100%',
        bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: store,
                             displayInfo: true
                     })
        ]
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'DATA ORIGINAL BUDGET',
        closeAction: 'hide',
        width: '760',
        height: '90%',
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

function copy_analisa_recovery(){
    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummyctg
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        frame: false,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'no'},
            {text: "ITEM PEKERJAAN", width:400, sortable: true, dataIndex: 'item_pekerjaan'},
            {text: "HARGA SATUAN", width:120, sortable: true, dataIndex: 'harga'}
        ],
        columnLines: true,
        dockedItems: [{
            dock: 'top',
            xtype: 'toolbar',
            items: [{
                text: 'Copy Data Dari Original Budget',
                handler: function(){
                    copy_analisa_dari_orgn_budget();
                    winadd.hide();
                }
            }
            ]
        },{
            xtype: 'toolbar',
            items: [
            'Divisi : ',{
            xtype: 'combobox',
            name: 'divisi',
            store: storedivisi,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            listeners: {
                change: function(combo){
                    // console.log(combo.value);
                    storeproyek.load({
                    params: {
                        divisi_kode: combo.value
                    }
                    });
                }
            }
        },
            'Nama Proyek : ',{
            xtype: 'combobox',
            name: 'proyek',
            store: storeproyek,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            listeners: {
                change: function(combo){
                    // console.log(combo.value);
                    storetanggal.load({
                    params: {
                        no_spk: combo.value 
                    }
                    });
                }
            }
        },
            'Tanggal : ',{
            xtype: 'combobox',
            name: 'tanggal',
            store: storetanggal,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..'
        },'-',{
            text: 'Go'
        }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text: 'Simpan',
                tooltip:'Simpan',
                handler: function(){

                }
            },{
                text: 'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    winadd.hide();
                }
            }]
        }],
        width: '100%',
        height: '100%',
        bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: store,
                             displayInfo: true
                     })
        ]
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'DATA RECOVERY',
        closeAction: 'hide',
        width: '760',
        height: '90%',
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

function copy_analisa_ke_orgn_budget(){
    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummyctg
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        frame: false,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'no'},
            {text: "ITEM PEKERJAAN", width:400, sortable: true, dataIndex: 'item_pekerjaan'},
            {text: "HARGA SATUAN", width:120, sortable: true, dataIndex: 'harga'}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
            'Divisi : ',{
            xtype: 'combobox',
            name: 'divisi',
            store: storedivisi,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            listeners: {
                change: function(combo){
                    // console.log(combo.value);
                    storeproyek.load({
                    params: {
                        divisi_kode: combo.value
                    }
                    });
                }
            }
        },
            'Nama Proyek : ',{
            xtype: 'combobox',
            name: 'proyek',
            store: storeproyek,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            listeners: {
                change: function(combo){
                    // console.log(combo.value);
                    storetanggal.load({
                    params: {
                        no_spk: combo.value 
                    }
                    });
                }
            }
        },
            'Tanggal : ',{
            xtype: 'combobox',
            name: 'tanggal',
            store: storetanggal,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..'
        },'-',{
            text: 'Go'
        }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text: 'Simpan',
                tooltip:'Simpan',
                handler: function(){

                }
            },{
                text: 'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    winadd.hide();
                }
            }]
        }],
        width: '100%',
        height: '100%',
        bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: store,
                             displayInfo: true
                     })
        ]
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'DATA ORIGINAL BUDGET',
        closeAction: 'hide',
        width: '760',
        height: '90%',
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

function copy_analisa_recovery(){
    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummyctg
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        frame: false,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'no'},
            {text: "ITEM PEKERJAAN", width:400, sortable: true, dataIndex: 'item_pekerjaan'},
            {text: "HARGA SATUAN", width:120, sortable: true, dataIndex: 'harga'}
        ],
        columnLines: true,
        dockedItems: [{
            dock: 'top',
            xtype: 'toolbar',
            items: [{
                text: 'Copy Data Dari Original Budget',
                handler: function(){
                    copy_analisa_dari_orgn_budget();
                    winadd.hide();
                }
            }
            ]
        },{
            xtype: 'toolbar',
            items: [
            'Divisi : ',{
            xtype: 'combobox',
            name: 'divisi',
            store: storedivisi,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            listeners: {
                change: function(combo){
                    // console.log(combo.value);
                    storeproyek.load({
                    params: {
                        divisi_kode: combo.value
                    }
                    });
                }
            }
        },
            'Nama Proyek : ',{
            xtype: 'combobox',
            name: 'proyek',
            store: storeproyek,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            listeners: {
                change: function(combo){
                    // console.log(combo.value);
                    storetanggal.load({
                    params: {
                        no_spk: combo.value 
                    }
                    });
                }
            }
        },
            'Tanggal : ',{
            xtype: 'combobox',
            name: 'tanggal',
            store: storetanggal,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..'
        },'-',{
            text: 'Go'
        }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text: 'Simpan',
                tooltip:'Simpan',
                handler: function(){

                }
            },{
                text: 'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    winadd.hide();
                }
            }]
        }],
        width: '100%',
        height: '100%',
        bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: store,
                             displayInfo: true
                     })
        ]
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'DATA RECOVERY',
        closeAction: 'hide',
        width: '760',
        height: '90%',
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

function edit_harga_satuan(){
    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: storegetedithargasatuan,
        autoscroll: true,
        frame: false,
        columns: [
            {text: "KODE MATERIAL", flex:1, sortable: true, dataIndex: 'detail_material_kode'},
            {text: "NAMA MATERIAL", flex:1, sortable: true, dataIndex: 'detail_material_nama'},
            {text: "HARGA", flex:1, sortable: true, dataIndex: 'harga'}
        ],
        columnLines: true,
        dockedItems: [{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text: 'Simpan',
                tooltip:'Simpan',
                handler: function(){

                }
            },{
                text: 'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    winadd.hide();
                }
            }]
        }],
        width: '100%',
        height: '100%',
        bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: storegetedithargasatuan,
                             displayInfo: true
                     })
        ]
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'EDIT HARGA SATUAN',
        closeAction: 'hide',
        width: '530',
        height: '90%',
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

function confirmedithargasatuan(){

    var frmadd = Ext.create('Ext.form.Panel', {
        id:'frmadd ',
        width:'100%',
        height:'100%',
        bodyStyle: 'padding:5px 5px 0',
        autoScroll: true,
        frame: false,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 100
        },
            items: [{
                xtype:'textfield',
                fieldLabel: 'Username',
                anchor: '-5',
                name: 'username',
                afterLabelTextTpl: required,
                allowBlank: false
            },{
                xtype:'textfield',
                inputType: 'password',
                fieldLabel: 'Password',
                anchor: '-5',
                name: 'password',
                afterLabelTextTpl: required,
                allowBlank: false
            }],
        buttons: ['->', {
            text: 'Ok',
            handler: function() {                    
                var form = this.up('form').getForm();
                username = this.up('form').getForm().findField('username').getValue();
                password = this.up('form').getForm().findField('password').getValue();
                if(form.isValid()){
                    if (username == 'nindya' && password == 'karya'){                        
                    storegetedithargasatuan.load({
                        params: {
                            no_spk : '01/div-1/sira-sira/03/2012',
                            tgl_rab : '2009-05-01'
                        }
                    });
                    edit_harga_satuan();              
                    winadd.hide();
                    } else {
                        Ext.MessageBox.alert('Information','Maaf username atau password salah..!');
                        form.reset();
                    }           
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
        title: 'EDIT HARGA SATUAN',
        closeAction: 'hide',
        width: '300',
        height: '130',
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