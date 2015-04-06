<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

        <script type="text/javascript">
            var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
            Ext.require([
                '*'
            ]);

            Ext.define('combobox', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'value', mapping: 'value'},
                    {name: 'text', mapping: 'text'}
                ]
            });

            Ext.define('combobox2', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'value', mapping: 'value'},
                    {name: 'text', mapping: 'text'},
                    {name: 'kode', mapping: 'kode'},
                    {name: 'nama', mapping: 'nama'},
                    {name: 'kode_rap', mapping: 'kode_rap'},
                    {name: 'id_detail_material', mapping: 'id_detail_material'}
                ]
            });

            Ext.define('temp_cashtodate', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id', mapping: 'id'},
                    {name: 'kode_bahan', mapping: 'kode_bahan'},
                    {name: 'nama_bahan', mapping: 'nama_bahan'},
                    {name: 'volume', mapping: 'volume'},
                    {name: 'jumlah', mapping: 'jumlah'},
                    {name: 'keterangan_item', mapping: 'keterangan_item'},
                    {name: 'kode_rap', mapping: 'kode_rap'}
                ]
            });

            Ext.define('cashtodate', {
                extend: 'Ext.data.Model',
                fields: [                    
                    {name: 'id', mapping: 'id'},
                    {name: 'detail_material_id', mapping: 'detail_material_id'},
                    {name: 'pilih_toko_id', mapping: 'pilih_toko_id'},
                    {name: 'no_bukti', mapping: 'no_bukti'},
                    {name: 'toko_kode', mapping: 'toko_kode'},
                    {name: 'toko_nama', mapping: 'toko_nama'},
                    {name: 'kode_rap', mapping: 'kode_rap'},
                    {name: 'detail_material_kode', mapping: 'detail_material_kode'},
                    {name: 'detail_material_nama', mapping: 'detail_material_nama'},
                    {name: 'tanggal', mapping: 'tanggal'},
                    {name: 'uraian', mapping: 'uraian'},
                    {name: 'volume', mapping: 'volume'},
                    {name: 'pilihan', mapping: 'pilihan'},
                    {name: 'jumlah', mapping: 'jumlah'},
                    {name: 'subbidang_name', mapping: 'subbidang_name'},
                    {name: 'debet', mapping: 'debet'},
                    {name: 'keterangan_item', mapping: 'keterangan_item'},
                    {name: 'saldo', mapping: 'saldo'},
                    {name: 'urutan_subbidang_name', mapping: 'urutan_subbidang_name'},
                    {name: 'urutan', mapping: 'urutan'},
                    {name: 'pic', mapping: 'pic'}

                ]
            });

            var temp = [
                // {
                //     "id":"",
                //     "kode_bahan":"",
                //     "nama_bahan":"",
                //     "volume":"",
                //     "jumlah":""
                // }
            ];

            var pilihan_sort = [
                {"text": "Nomor Bukti", "value": "no_bukti"},
                {"text": "Uraian", "value": "uraian"},
                {"text": "Nama Material", "value": "detail_material_nama"},
                {"text": "Nama Toko", "value": "toko_nama"} 

            ];

            var store_pilihan_sort = Ext.create('Ext.data.Store', {
                model: 'combobox',
                remoteFilter: true,
                autoLoad: true,
                data: pilihan_sort
            });

            var store_temp = Ext.create('Ext.data.Store', {
                model: 'temp_cashtodate',
                remoteFilter: true,
                autoLoad: true,
                data: temp
            });

            var storepilihan = Ext.create('Ext.data.Store', {
                model: 'combobox',
                remoteFilter: true,
                autoLoad: true,
                proxy: {
                     type: 'ajax',
                     url: '<?php echo base_url() ?>transaksi/get_combo_pilihan',
                     reader: {
                         type: 'json',
                         root: 'data'
                     }
                 }
            });

            var store_item_toko = Ext.create('Ext.data.Store', {
                model: 'combobox',
                remoteFilter: true,
                autoLoad: true,
                 proxy: {
                     type: 'ajax',
                     url: '<?php echo base_url() ?>transaksi/get_combo_item_toko',
                     reader: {
                         type: 'json',
                         root: 'data'
                     }
                 }
            });

            var store_item_material = Ext.create('Ext.data.Store', {
                model: 'combobox2',
                remoteFilter: true,
                autoLoad: true,
                 proxy: {
                     type: 'ajax',
                     url: '<?php echo base_url() ?>transaksi/get_combo_item_material',
                     reader: {
                         type: 'json',
                         root: 'data'
                     }
                 }
            }); 

            var store_cashtodate = Ext.create('Ext.data.Store', {
                model: 'cashtodate',
                remoteFilter: true,
                autoLoad: false,
                // groupField: 'urutan_subbidang_name',
                 proxy: {
                    type: 'ajax',
                    extraParams: {
                        sort : '',
                        pilihan_sort : store_pilihan_sort.getAt('0').get('value'),
                        tgl_awal : new Date(),
                        tgl_akhir : new Date()
                    },
                    url: '<?php echo base_url() ?>transaksi/get_data_cashtodate',
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                 }
            });  

            Ext.onReady(function() {
                
                store_cashtodate.load();

                var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
                    groupHeaderTpl: '{name} : ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
                    hideGroupedHeader: true,
                    startCollapsed: false
                }),
                groups = store_cashtodate.getGroups();

                var grid_cashtodate = Ext.create('Ext.grid.Panel', {
                    store: store_cashtodate,
                    // features: [groupingFeature],
                    columns: [
                        {text: "", xtype: 'actioncolumn', width: 25, sortable: true, icon: '<?= base_url(); ?>assets/images/accept.gif',
                            renderer: function (value, metadata, record) {
                                if (record.get('id') == 0) {
                                    this.items[0].icon = '';
                                    // this.items[0].tooltip = 'kosongkan data';
                                } else {
                                    this.items[0].icon = '<?=base_url();?>assets/images/accept.gif';
                                    // this.items[0].tooltip = 'hapus data';
                                }
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                rec = store_cashtodate.getAt(rowIndex);
                                id = rec.get('id');
                                urutan = rec.get('urutan');
                                tanggal = rec.get('tanggal');
                                no_bukti = rec.get('no_bukti');
                                pic = rec.get('pic');
                                uraian = rec.get('uraian');
                                pilih_toko_id = rec.get('pilih_toko_id');
                                pilihan = rec.get('pilihan');
                                volume = rec.get('volume');
                                jumlah = rec.get('jumlah');
                                keterangan_item = rec.get('keterangan_item');
                                detail_material_id = rec.get('detail_material_id');
                                debet = rec.get('debet');
                                kode_rap = rec.get('kode_rap');
                                editcashtodate(id,urutan,tanggal,no_bukti,pic,uraian,pilih_toko_id,pilihan,volume,jumlah,keterangan_item,detail_material_id,debet,kode_rap);
                            }
                        },
                        {text: "", xtype: 'actioncolumn', width: 25, sortable: true, icon: '<?= base_url(); ?>assets/images/delete.gif',
                            renderer: function (value, metadata, record) {
                                if (record.get('id') == 0) {
                                    this.items[0].icon = '';
                                    // this.items[0].tooltip = 'kosongkan data';
                                } else {
                                    this.items[0].icon = '<?=base_url();?>assets/images/delete.gif';
                                    // this.items[0].tooltip = 'hapus data';
                                }
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                rec = store_cashtodate.getAt(rowIndex);
                                id = rec.get('id');
                                Ext.MessageBox.confirm('Delete', 'Apakah anda akan menghapus item ini?',function(resbtn){
                                    if (resbtn == 'yes') {
                                        Ext.Ajax.request({
                                            url: '<?=base_url();?>transaksi/delete_data/cashtodate',
                                            method: 'POST',
                                            params: {
                                                'id':id
                                            },                              
                                            success: function() {
                                                store_cashtodate.load();
                                                Ext.Msg.alert( "Status", "Delete successfully..!"); 
                                            },
                                            failure: function() {
                                                Ext.Msg.alert( "Status", "No Respond..!"); 
                                            }
                                        });
                                    }
                                }); 
                            }
                        },
                        {text: "Nomor Bukti", width:120, sortable: true, dataIndex: 'no_bukti'},
                        {text: "PIC", width:120, sortable: true, dataIndex: 'pic'},
                        {text: "Kode Toko", width:75, sortable: true, dataIndex: 'toko_kode'},
                        {text: "Nama Toko", width:120, sortable: true, dataIndex: 'toko_nama'},
                        {text: "Kode RAP", width:120, sortable: true, dataIndex: 'kode_rap'},
                        {text: "Kode", width:120, sortable: true, dataIndex: 'detail_material_kode'},
                        {text: "Nama", width:120, sortable: true, dataIndex: 'detail_material_nama'},
                        {text: "Keterangan<br>Item", width:120, sortable: true, dataIndex: 'keterangan_item'},
                        {text: "Tanggal", width:120, sortable: true, dataIndex: 'tanggal'},
                        {text: "Uraian", width:120, sortable: true, dataIndex: 'uraian'},
                        {text: "Volume", width:120, sortable: true, dataIndex: 'volume'},
                        {text: "Pemasukan", width:120, sortable: true, dataIndex: 'debet',
                            renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                        },
                        {text: "Pengeluaran ", width:120, sortable: true, dataIndex: 'jumlah',
                            renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                        },
                        {text: "Saldo", width:120, sortable: true, dataIndex: 'saldo',
                            renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                        }
                    ],
                    
                    dockedItems: [{
                            itemId: 'dock',
                            dock: 'top',
                            xtype: 'toolbar',
                            items: [
                                'Pengelompokan Berdasarkan : ',
                                {
                                    xtype: 'textfield',
                                    itemId: 'sort',
                                    width: 130
                                },
                                {
                                    xtype: 'combobox',
                                    itemId: 'pilihan_sort',
                                    store: store_pilihan_sort,
                                    valueField: 'value',
                                    displayField: 'text',
                                    typeAhead: true,
                                    queryMode: 'local',
                                    emptyText: 'Pilih',
                                    width: 100
                                },
                                'Periode : ',
                                {
                                    xtype: 'datefield',
                                    itemId: 'tglawal',
                                    name: 'tglawal',
                                    submitFormat:'Y-m-d',
                                    format:'Y-m-d',
                                    emptyText: 'Pilih..',
                                    width: 150,
                                    value: new Date()
                                },
                                'S/D : ',
                                {
                                    xtype: 'datefield',
                                    itemId: 'tglakhir',
                                    name: 'tglakhir',
                                    submitFormat:'Y-m-d',
                                    format:'Y-m-d',
                                    emptyText: 'Pilih..',
                                    width: 150,
                                    value: new Date()
                                }, {
                                    text: 'Go >>',
                                    handler: function(){
                                        dock = grid_cashtodate.getComponent('dock');
                                        sort = dock.getComponent('sort').getValue();
                                        pilihan_sort = dock.getComponent('pilihan_sort').getValue();
                                        tgl_awal = dock.getComponent('tglawal').getValue();
                                        // tgl_awal = convertdate(awal);
                                        tgl_akhir = dock.getComponent('tglakhir').getValue();
                                        // tgl_akhir = convertdate(akhir);
                                        store_cashtodate.proxy.setExtraParam('sort', sort);
                                        store_cashtodate.proxy.setExtraParam('pilihan_sort', pilihan_sort);
                                        store_cashtodate.proxy.setExtraParam('tgl_awal', tgl_awal);
                                        store_cashtodate.proxy.setExtraParam('tgl_akhir', tgl_akhir);
                                        store_cashtodate.load();
                                    }
                                }, '-', {
                                    text: 'Print'
                                }
                            ]
                        }, {
                            dock: 'bottom',
                            xtype: 'toolbar',
                            items: [
                                {
                                    text: 'Tambah Data',
                                    handler: function() {
                                        tambahcashtodate();
                                    }
                                }
                            ]
                        }],
                    columnLines: true,
                    width: '100%',
                    height: '100%',
                    title: 'INPUT CASH TO DATE',
                    listeners:{
                        beforerender: function(){                            
                            dock = grid_cashtodate.getComponent('dock');
                            dock.getComponent('pilihan_sort').setValue(store_pilihan_sort.getAt('0').get('value'));
                        }
                    }
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
                        items: grid_cashtodate
                    }]
                });
            });

            function tambahcashtodate() {

                var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
                    clicksToEdit: 2,        
                    listeners: {
                        afteredit: function(rec,obj) {
                    
                        }   
                    }
                });

                var grid_cashtodate = Ext.create('Ext.grid.Panel', {
                    store: store_temp,
                    viewConfig: {
                        markDirty: false
                    },
                    plugins: [rowEditing],
                    columns: [
                        {text: "No Bahan", flex: 1, sortable: true, dataIndex: 'kode_bahan'},
                        {text: "Nama Bahan", flex: 1, sortable: true, dataIndex: 'nama_bahan'},
                        {text: "Volume", flex: 1, sortable: true, dataIndex: 'volume',
                            editor:{
                                xtype: 'numberfield'
                            }
                        },
                        {text: "Jumlah", flex: 1, sortable: true, dataIndex: 'jumlah',
                            editor:{
                                xtype: 'numberfield'
                            }
                        },
                        {text: "Keterangan", flex: 1, sortable: true, dataIndex: 'keterangan_item',
                            editor:{
                                xtype: 'textfield'
                            }
                        },
                        {text: "Kode Rap", flex: 1, sortable: true, dataIndex: 'kode_rap'},
                        {text: "", xtype: 'actioncolumn', width: 25, sortable: true, icon: '<?= base_url(); ?>assets/images/delete.gif',
                            handler: function(grid, rowIndex, colIndex) {
                                store_temp.removeAt(rowIndex);
                            }
                        }
                    ],
                    columnLines: true,
                    width: '100%',
                    height: '100%'
                });

                var frmtambahcashtodate = Ext.create('Ext.form.Panel', {
                    // url: '<?php echo base_url() ?>transaksi/insertdata/cashtodate',
                    bodyStyle: 'padding:5px 5px 5px 5px',
                    width: '100%',
                    height: '100%',
                    autoScroll: true,
                    fieldDefaults: {
                        msgTarget: 'side',
                        labelWidth: 140,
                        width: 500
                    },
                    items: [                        
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Tanggal ',
                            name: 'tanggal',
                            itemId: 'tanggal',
                            format:'M, d / Y',
                            submitFormat: 'Y-m-d',
                            value: new Date(),
                            allowBlank: false
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Nomor Bukti',
                            name: 'no_bukti',
                            itemId: 'no_bukti',
                            allowBlank: false
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'PIC',
                            name: 'pic',
                            itemId: 'pic',
                            allowBlank: false
                        },
                        {
                            xtype: 'textarea',
                            fieldLabel: 'Uraian',
                            name: 'uraian',
                            itemId: 'uraian',
                            allowBlank: false
                        },
                        {
                            xtype: 'radiogroup',
                            fieldLabel: 'Pilih Pemasukan / Pengeluaran',
                            itemId: 'status',
                            columns: 2,
                            items: [
                                {boxLabel: 'Pengeluaran', name: 'status', itemId: 'kredit', inputValue: 'kredit'},
                                {boxLabel: 'Pemasukan', name: 'status', itemId: 'debet', inputValue: 'debet'}
                            ],
                            listeners: {
                                change: function(val){
                                    if (val.getValue().status == 'kredit') {
                                        frmtambahcashtodate.getComponent('jumlah').setVisible(false);
                                        frmtambahcashtodate.getComponent('kode_toko').setVisible(true);
                                        frmtambahcashtodate.getComponent('pilihan').setVisible(true);
                                        frmtambahcashtodate.getComponent('button_tambah').setVisible(true);
                                        frmtambahcashtodate.getComponent('button_delete').setVisible(true);
                                        frmtambahcashtodate.getComponent('grid_1').setVisible(true);
                                    } else {
                                        frmtambahcashtodate.getComponent('jumlah').setVisible(true);
                                        frmtambahcashtodate.getComponent('kode_toko').setVisible(false);
                                        frmtambahcashtodate.getComponent('pilihan').setVisible(false);
                                        frmtambahcashtodate.getComponent('button_tambah').setVisible(false);
                                        frmtambahcashtodate.getComponent('button_delete').setVisible(false);
                                        frmtambahcashtodate.getComponent('grid_1').setVisible(false);
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Jumlah',
                            name: 'jumlah',
                            itemId: 'jumlah'
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Kode Toko',
                            name: 'kode_toko',
                            itemId: 'kode_toko',
                            store: store_item_toko,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih Toko...'

                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Pilihan',
                            name: 'pilihan',
                            itemId: 'pilihan',
                            store: storepilihan,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih Item...'
                        },
                        {
                            xtype: 'button',
                            itemId:'button_tambah',
                            text: 'Tambah Item',
                            handler: function(){
                                tambah_item_cashtodate();
                            }
                        },
                        {
                            xtype: 'button',
                            itemId:'button_delete',
                            text: 'Delete All Item',
                            handler: function(){
                                store_temp.removeAll();
                            }
                        },
                        {
                            height: 140,
                            width: 510,
                            layout: 'fit',
                            itemId:'grid_1',
                            autoScroll: true,
                            bodyStyle: 'padding:5px 0px 0px 0px; border: 0px;',
                            items: [grid_cashtodate]
                        }
                       
                    ],
                    buttons: [{
                            text: 'Save',
                            handler: function() {          
                                var form = this.up('form').getForm();
                                if (form.isValid()) {
                                    if (store_temp.getRange().length > 0) {
                                        for (var i = 0; i < store_temp.getRange().length; i++) {
                                            rec = store_temp.getAt(i);
                                            Ext.Ajax.request({
                                                url: '<?=base_url();?>transaksi/insertdata/cashtodate',
                                                method: 'POST',
                                                params: {
                                                    'no_bukti':frmtambahcashtodate.getComponent('no_bukti').getValue(),
                                                    'pic':frmtambahcashtodate.getComponent('pic').getValue(),
                                                    'kode_toko':frmtambahcashtodate.getComponent('kode_toko').getValue(),
                                                    'tanggal':frmtambahcashtodate.getComponent('tanggal').getValue(),
                                                    'pilihan':frmtambahcashtodate.getComponent('pilihan').getValue(),
                                                    'item':rec.get('id'),
                                                    'volume':rec.get('volume'),
                                                    'jumlah_bayar':rec.get('jumlah'),
                                                    'keterangan_item':rec.get('keterangan_item'),
                                                    'detail_material_kode':rec.get('kode_bahan'),
                                                    'kode_rap':rec.get('kode_rap'),
                                                    'uraian':frmtambahcashtodate.getComponent('uraian').getValue(),
                                                    'status':frmtambahcashtodate.getComponent('status').getValue(),
                                                    'debet':frmtambahcashtodate.getComponent('jumlah').getValue()
                                                },                              
                                            success: function() {
                                            },
                                            failure: function() {
                                                Ext.Msg.alert( "Status", "No Respond..!"); 
                                            }
                                            });
                                        };
                                        Ext.MessageBox.alert('Master Data', 'Insert successfully..!');
                                        form.reset();
                                        store_cashtodate.load();
                                        store_temp.removeAll();
                                        winadds.hide();
                                    } else {
                                        radio=frmtambahcashtodate.getComponent('status');
                                        st = radio.getComponent('debet');

                                        if (st.getValue() == true) {
                                            Ext.Ajax.request({
                                                url: '<?=base_url();?>transaksi/insertdata/cashtodate',
                                                method: 'POST',
                                                params: {
                                                    'no_bukti':frmtambahcashtodate.getComponent('no_bukti').getValue(),
                                                    'pic':frmtambahcashtodate.getComponent('pic').getValue(),
                                                    'kode_toko':frmtambahcashtodate.getComponent('kode_toko').getValue(),
                                                    'tanggal':frmtambahcashtodate.getComponent('tanggal').getValue(),
                                                    'pilihan':frmtambahcashtodate.getComponent('pilihan').getValue(),
                                                    'item':'',
                                                    'volume':'',
                                                    'jumlah_bayar':'',
                                                    'keterangan_item':'',
                                                    'kode_rap':'',
                                                    'uraian':frmtambahcashtodate.getComponent('uraian').getValue(),
                                                    'status':frmtambahcashtodate.getComponent('status').getValue(),
                                                    'debet':frmtambahcashtodate.getComponent('jumlah').getValue()
                                                },                              
                                            success: function() {
                                            },
                                            failure: function() {
                                                Ext.Msg.alert( "Status", "No Respond..!"); 
                                            }
                                            });
                                            Ext.MessageBox.alert('Master Data', 'Insert successfully..!');
                                            form.reset();
                                            store_cashtodate.load();
                                            store_temp.removeAll();
                                            winadds.hide();
                                        } else {
                                            Ext.MessageBox.alert('Master Data', 'Item Kosong..!');
                                        }
                                    }
                                    
                                }

                            }
                        }, {
                            text: 'Cancel',
                            handler: function() {
                                store_temp.removeAll();
                                winadds.hide();
                            }
                        }],
                    listeners:{
                        beforerender: function(){
                            radio=frmtambahcashtodate.getComponent('status');
                            radio.getComponent('kredit').setValue(true);
                        }
                    }
                });

                winadds = Ext.create('Ext.Window', {
                    title: 'Tambah Data',
                    closeAction: 'hide',
                    autoScroll: true,
                    width: 560,
                    height: '80%',
                    layout: 'fit',
                    items: frmtambahcashtodate
                }).show();
            }

            function tambah_item_cashtodate() {
                
                var sm = Ext.create('Ext.selection.CheckboxModel', {
                    checkOnly: true,
                    mode: 'multi'
                });

                var grid_cashtodate = Ext.create('Ext.grid.Panel', {
                    store: store_item_material,
                    selModel: sm,
                    selType: 'cellmodel',     
                    columns: [
                        {text: "No Bahan", flex: 1, sortable: true, dataIndex: 'kode'},
                        {text: "Nama Bahan", flex: 1, sortable: true, dataIndex: 'nama'},
                        {text: "Kode Rap", flex: 1, sortable: true, dataIndex: 'kode_rap'}
                    ],
                    columnLines: true,
                    width: '100%',
                    height: '100%',
                    dockedItems:[{
                        dock: 'bottom',
                        xtype: 'toolbar',
                        items: [
                            {
                                text: 'Tambah',
                                handler: function() {
                                    winadd.hide();
                                    var selectedNode = grid_cashtodate.getSelectionModel().getSelection();
                                    for (i = 0; i < selectedNode.length; i++) {
                                        store_temp.add({
                                            id : selectedNode[i].data.id_detail_material,
                                            kode_bahan: selectedNode[i].data.kode,
                                            nama_bahan: selectedNode[i].data.nama,
                                            kode_rap: selectedNode[i].data.kode_rap,
                                            volume: 0,
                                            jumlah: 0,
                                            keterangan_item: '-'
                                        });                                
                                    }
                                }
                            },{
                                text: 'Cancel',
                                handler: function() {
                                    winadd.hide();
                                }
                            }
                        ]
                    }]
                });

                winadd = Ext.create('Ext.Window', {
                    title: 'Tambah Bahan',
                    closeAction: 'hide',
                    autoScroll: true,
                    width: 560,
                    height: '80%',
                    layout: 'fit',
                    items: grid_cashtodate
                }).show();
            }

            function editcashtodate(id,urutan,tanggal,no_bukti,pic,uraian,pilih_toko_id,pilihan,volume,jumlah,keterangan_item,detail_material_id,debet,kode_rap) {

                var frmtambahcashtodate = Ext.create('Ext.form.Panel', {
                    url: '<?php echo base_url() ?>transaksi/editdata/cashtodate',
                    bodyStyle: 'padding:5px 5px 0',
                    width: '100%',
                    height: '100%',
                    autoScroll: true,
                    fieldDefaults: {
                        msgTarget: 'side'
                    },
                    items: [     
                        {
                            xtype: 'textfield',
                            fieldLabel: 'ID',
                            name: 'id',
                            anchor: '-5',
                            itemId: 'id',
                            allowBlank: false,
                            value: id,
                            hidden: true
                        },                  
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Tanggal ',
                            name: 'tanggal',
                            itemId: 'tanggal',
                            format:'M, d / Y',
                            anchor: '-5',
                            submitFormat: 'Y-m-d',
                            value: new Date(),
                            allowBlank: false,
                            value: tanggal
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Nomor Bukti',
                            name: 'no_bukti',
                            anchor: '-5',
                            itemId: 'no_bukti',
                            allowBlank: false,
                            value: no_bukti
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'PIC',
                            name: 'pic',
                            anchor: '-5',
                            itemId: 'pic',
                            allowBlank: false,
                            value: pic
                        },
                        {
                            xtype: 'textarea',
                            fieldLabel: 'Uraian',
                            name: 'uraian',
                            itemId: 'uraian',
                            anchor: '-5',
                            allowBlank: false,
                            value: uraian
                        },
                        {
                            xtype: 'radiogroup',
                            fieldLabel: 'Pilih Pemasukan / Pengeluaran',
                            itemId: 'status',
                            anchor: '-5',
                            columns: 1,
                            items: [
                                {boxLabel: 'Pemasukan', name: 'status', itemId: 'kredit', inputValue: 'kredit'},
                                {boxLabel: 'Pengeluaran', name: 'status', itemId: 'debet', inputValue: 'debet'}
                            ],
                            listeners: {
                                change: function(val){
                                    if (val.getValue().status == 'kredit') {
                                        frmtambahcashtodate.getComponent('jumlah').setVisible(false);
                                        frmtambahcashtodate.getComponent('kode_toko').setVisible(true);
                                        frmtambahcashtodate.getComponent('pilihan').setVisible(true);
                                        frmtambahcashtodate.getComponent('volume').setVisible(true);
                                        frmtambahcashtodate.getComponent('jumlah_bayar').setVisible(true);
                                        frmtambahcashtodate.getComponent('keterangan_item').setVisible(true);
                                    } else {
                                        frmtambahcashtodate.getComponent('jumlah').setVisible(true);
                                        frmtambahcashtodate.getComponent('kode_toko').setVisible(false);
                                        frmtambahcashtodate.getComponent('pilihan').setVisible(false);
                                        frmtambahcashtodate.getComponent('volume').setVisible(false);
                                        frmtambahcashtodate.getComponent('jumlah_bayar').setVisible(false);
                                        frmtambahcashtodate.getComponent('keterangan_item').setVisible(false);
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Jumlah',
                            anchor: '-5',
                            name: 'jumlah',
                            itemId: 'jumlah',
                            value: debet
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Kode Toko',
                            name: 'kode_toko',
                            itemId: 'kode_toko',
                            store: store_item_toko,
                            valueField: 'value',
                            displayField: 'text',
                            anchor: '-5',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih Toko...',
                            value: pilih_toko_id

                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Pilihan',
                            name: 'pilihan',
                            itemId: 'pilihan',
                            store: storepilihan,
                            valueField: 'value',
                            displayField: 'text',
                            anchor: '-5',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih Item...',
                            value: pilihan
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Kode Material',
                            name: 'item_kode',
                            itemId: 'item_kode',
                            store: store_item_material,
                            valueField: 'value',
                            displayField: 'text',
                            anchor: '-5',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih Item...',
                            value: kode_rap,
                            listeners:{
                                change: function(val){
                                    var index = store_item_material.findExact('value',val.value);
                                    if (index != -1) {
                                        var rec = store_item_material.getAt(index);
                                        item = rec.get('id_detail_material');

                                        frmtambahcashtodate.getComponent('kode_rap').setValue(val.value);
                                        frmtambahcashtodate.getComponent('item').setValue(item);
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Volume',
                            anchor: '-5',
                            name: 'volume',
                            itemId: 'volume',
                            allowBlank: false,
                            value: volume
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Jumlah yang dibayar',
                            anchor: '-5',
                            name: 'jumlah_bayar',
                            itemId: 'jumlah_bayar',
                            allowBlank: false,
                            value: jumlah
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Keterangan Item',
                            anchor: '-5',
                            name: 'keterangan_item',
                            itemId: 'keterangan_item',
                            allowBlank: false,
                            value: keterangan_item
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Kode Rap',
                            anchor: '-5',
                            name: 'kode_rap',
                            itemId: 'kode_rap',
                            allowBlank: false,
                            value: kode_rap,
                            hidden: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Kode Material',
                            anchor: '-5',
                            name: 'item',
                            itemId: 'item',
                            allowBlank: false,
                            value: detail_material_id,
                            hidden: true
                        }
                       
                    ],
                    buttons: ['->', {
                            text: 'Save',
                            handler: function() {
                                var form = this.up('form').getForm();
                                if (form.isValid()) {
                                    form.submit({
                                        success: function(fp, o) {
                                            Ext.MessageBox.alert('Status', 'Update successfully..!');
                                            form.reset();
                                            store_cashtodate.load();
                                        }
                                    });
                                    winadd.hide();
                                }

                            }
                        }, {
                            text: 'Cancel',
                            handler: function() {
                                winadd.hide();
                            }
                        }],
                    listeners:{
                        beforerender: function(){
                            if (urutan == 1) {
                                radio=frmtambahcashtodate.getComponent('status');
                                radio.getComponent('debet').setValue(true);
                                radio.getComponent('kredit').setVisible(false);
                            } else {
                                radio=frmtambahcashtodate.getComponent('status');
                                radio.getComponent('kredit').setValue(true);
                                radio.getComponent('debet').setVisible(false);
                            }
                        }
                    }
                });

                winadd = Ext.create('Ext.Window', {
                    title: 'Tambah Data',
                    closeAction: 'hide',
                    autoScroll: true,
                    width: 400,
                    height: 340,
                    layout: 'fit',
                    items: frmtambahcashtodate
                }).show();
            }

        function convertdate(val){
            Date = val.getDate();
            Month = val.getMonth();
            Year = val.getFullYear();
            tgl = Year+'-'+Month+'-'+Date;
            return tgl;
        }

        </script>
</head>
<body>
<!--		
    <div style="padding: 10px;">
        <h2 align="center">INPUT CASH TO DATE</h2>
    </div>
-->	
</body>
</html>
