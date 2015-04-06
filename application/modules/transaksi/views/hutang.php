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

            Ext.define('comboboxbukti', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'value', mapping: 'value'},
                    {name: 'text', mapping: 'text'},
                    {name: 'jumlah', mapping: 'jumlah'}
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

            Ext.define('bayar', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id', mapping: 'id'},
                    {name: 'no_bukti', mapping: 'no_bukti'},
                    {name: 'bayar', mapping: 'bayar'},
                    {name: 'tanggal', mapping: 'tanggal'},
                    {name: 'pic', mapping: 'pic'},
                    {name: 'jml', mapping: 'jml'},
                    {name: 'no_bukti_bayar_hutang', mapping: 'no_bukti_bayar_hutang'}
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
                    {name: 'keterangan_item', mapping: 'keterangan_item'},
                    {name: 'pic', mapping: 'pic'}
                ]
            });

            Ext.define('lap_hutang', {
                extend: 'Ext.data.Model',
                fields: [                    
                    {name: 'tanggal', mapping: 'tanggal'},
                    {name: 'no_bukti', mapping: 'no_bukti'},
                    {name: 'pic', mapping: 'pic'},
                    {name: 'detail', mapping: 'detail'},
                    {name: 'jumlah', mapping: 'jumlah'},
                    {name: 'telah_dibayar', mapping: 'telah_dibayar'},
                    {name: 'sisa_hutang', mapping: 'sisa_hutang'},
                    {name: 'pembayaran', mapping: 'pembayaran'}
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
                {"text": "Nama Material", "value": "c.detail_material_nama"},
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

            var store_bayar_hutang = Ext.create('Ext.data.Store', {
                model: 'bayar',
                remoteFilter: true,
                autoLoad: false,
                proxy: {
                     type: 'ajax',
                     extraParams: {
                        no_bukti : ''
                    },
                     url: '<?php echo base_url() ?>transaksi/get_bayar_hutang',
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

            var store_no_bukti = Ext.create('Ext.data.Store', {
                model: 'comboboxbukti',
                remoteFilter: true,
                autoLoad: false,
                 proxy: {
                     type: 'ajax',
                     url: '<?php echo base_url() ?>transaksi/get_combo_no_bukti',
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
                groupField: 'subbidang_name',
                 proxy: {
                    type: 'ajax',
                    extraParams: {
                        sort : '',
                        pilihan_sort : store_pilihan_sort.getAt('0').get('value'),
                        tgl_awal : new Date(),
                        tgl_akhir : new Date()
                    },
                    url: '<?php echo base_url() ?>transaksi/get_data_hutang',
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                 }
            });  

            var store_lap_hutang = Ext.create('Ext.data.Store', {
                model: 'lap_hutang',
                remoteFilter: true,
                autoLoad: false,
                 proxy: {
                    type: 'ajax',
                    extraParams: {
                        tgl_awal : new Date(),
                        tgl_akhir : new Date()
                    },
                    url: '<?php echo base_url() ?>transaksi/get_data_laporan_hutang',
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                 }
            });  

            Ext.onReady(function() {
                
                store_cashtodate.load();
                store_lap_hutang.load();

                var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
                    groupHeaderTpl: '{name} : ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
                    hideGroupedHeader: true,
                    startCollapsed: false
                }),
                groups = store_cashtodate.getGroups();

                var grid_cashtodate = Ext.create('Ext.grid.Panel', {
                    store: store_cashtodate,
                    features: [groupingFeature],
                    columns: [
                        {text: "", xtype: 'actioncolumn', width: 25, sortable: true, icon: '<?= base_url(); ?>assets/images/accept.gif',
                            handler: function(grid, rowIndex, colIndex) {
                                rec = store_cashtodate.getAt(rowIndex);
                                id = rec.get('id');
                                no_bukti = rec.get('no_bukti');
                                uraian = rec.get('uraian');
                                toko = rec.get('pilih_toko_id');
                                tanggal = rec.get('tanggal');
                                pilihan = rec.get('pilihan');
                                item = rec.get('detail_material_id');
                                volume = rec.get('volume');
                                jumlah = rec.get('jumlah');
                                keterangan_item = rec.get('keterangan_item');
                                pic = rec.get('pic');
                                kode_rap = rec.get('kode_rap');
                                editcashtodate(id,no_bukti,uraian,toko,tanggal,pilihan,item,volume,jumlah,keterangan_item,pic,kode_rap);
                            }
                        },
                        {text: "", xtype: 'actioncolumn', width: 25, sortable: true, icon: '<?= base_url(); ?>assets/images/delete.gif',
                            handler: function(grid, rowIndex, colIndex) {
                                rec = store_cashtodate.getAt(rowIndex);
                                id = rec.get('id');
                                no_bukti = rec.get('no_bukti');
                                Ext.MessageBox.confirm('Delete', 'Apakah anda akan menghapus item ini?',function(resbtn){
                                    if (resbtn == 'yes') {
                                        Ext.Ajax.request({
                                            url: '<?=base_url();?>transaksi/delete_data/hutang',
                                            method: 'POST',
                                            params: {
                                                'id':id,
                                                'no_bukti':no_bukti
                                            },                              
                                            success: function() {
                                                store_cashtodate.load();
                                                store_lap_hutang.load();

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
                        {text: "jumlah", width:120, sortable: true, dataIndex: 'jumlah',
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
                                        store_lap_hutang.load();
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
                                },
                                {
                                    text: 'Bayar Hutang',
                                    handler: function() {
                                        bayar_hutang();
                                    }
                                }
                            ]
                        }],
                    columnLines: true,
                    width: '100%',
                    height: '100%',
                    // title: 'INPUT HUTANG',
                    listeners:{
                        beforerender: function(){                            
                            dock = grid_cashtodate.getComponent('dock');
                            dock.getComponent('pilihan_sort').setValue(store_pilihan_sort.getAt('0').get('value'));
                        }
                    }
                });

                var grid_laporan_hutang = Ext.create('Ext.grid.Panel', {
                    store: store_lap_hutang,
                    columns: [
                        {xtype: 'rownumberer', width:25},
                        {text: "Tanggal", width:80, sortable: true, dataIndex: 'tanggal'},
                        {text: "No Bukti", width:75, sortable: true, dataIndex: 'no_bukti'},
                        {text: "PIC", width:100, sortable: true, dataIndex: 'pic'},
                        {text: "Uraian", width:260, sortable: true, dataIndex: 'detail'},
                        {text: "Jumlah", width:120, sortable: true, dataIndex: 'jumlah',
                            renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                        },
                        {text: "Pembayaran", width:180, sortable: true, dataIndex: 'pembayaran'},
                        {text: "Telah Dibayar", width:120, sortable: true, dataIndex: 'telah_dibayar',
                            renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                        },
                        {text: "Sisa Hutang", width:120, sortable: true, dataIndex: 'sisa_hutang',
                            renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                        }
                    ],                    
                    dockedItems: [{
                            itemId: 'dock',
                            dock: 'top',
                            xtype: 'toolbar',
                            items: [
                                // 'Periode : ',
                                // {
                                //     xtype: 'datefield',
                                //     itemId: 'tglawal',
                                //     name: 'tglawal',
                                //     submitFormat:'Y-m-d',
                                //     format:'Y-m-d',
                                //     emptyText: 'Pilih..',
                                //     width: 150,
                                //     value: new Date()
                                // },
                                // 'S/D : ',
                                // {
                                //     xtype: 'datefield',
                                //     itemId: 'tglakhir',
                                //     name: 'tglakhir',
                                //     submitFormat:'Y-m-d',
                                //     format:'Y-m-d',
                                //     emptyText: 'Pilih..',
                                //     width: 150,
                                //     value: new Date()
                                // }, {
                                //     text: 'Go >>',
                                //     handler: function(){
                                //         dock = grid_cashtodate.getComponent('dock');
                                //         tgl_awal = dock.getComponent('tglawal').getValue();
                                //         // tgl_awal = convertdate(awal);
                                //         tgl_akhir = dock.getComponent('tglakhir').getValue();
                                //         // tgl_akhir = convertdate(akhir);
                                //         store_lap_hutang.proxy.setExtraParam('tgl_awal', tgl_awal);
                                //         store_lap_hutang.proxy.setExtraParam('tgl_akhir', tgl_akhir);
                                //         store_lap_hutang.load();
                                //     }
                                // }, '-', 
                                {
                                    text: 'Print'
                                }
                            ]
                        }],
                    columnLines: true,
                    width: '100%',
                    height: '100%',
                    // title: 'INPUT HUTANG',
                });

                var tab = Ext.create('Ext.tab.Panel', {
                    width: '100%',
                    height: '100%',
                    activeTab: 0,
                    items: [
                        {
                            title: 'Hutang',
                            items: grid_cashtodate,
                            layout: 'fit'
                        },                        
                        {
                            title: 'Mutasi Hutang',
                            items: grid_laporan_hutang,
                            layout: 'fit'
                        }
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
                        items: tab
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
                    // selModel: new Ext.selection.CheckboxModel({
                    //     checkOnly: true
                    // }),        
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
                    // url: '<?php echo base_url() ?>transaksi/insertdata/hutang',
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
                            xtype: 'combobox',
                            fieldLabel: 'Kode Toko',
                            name: 'kode_toko',
                            itemId: 'kode_toko',
                            store: store_item_toko,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih Toko...',
                            allowBlank: false

                        },
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Tanggal ',
                            name: 'tanggal',
                            itemId: 'tanggal',
                            format:'M, d / Y',
                            submitFormat: 'Y-m-d',
                            allowBlank: false,
                            value: new Date()
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
                            emptyText: 'Pilih Item...',
                            allowBlank: false
                        },
                        {
                            xtype: 'button',
                            text: 'Tambah Item',
                            handler: function(){
                                tambah_item_cashtodate();
                            }
                        },
                        {
                            xtype: 'button',
                            text: 'Delete All Item',
                            handler: function(){
                                store_temp.removeAll();
                            }
                        },
                        {
                            height: 140,
                            width: 510,
                            layout: 'fit',
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
                                    for (var i = 0; i < store_temp.getRange().length; i++) {
                                        rec = store_temp.getAt(i);
                                        Ext.Ajax.request({
                                            url: '<?=base_url();?>transaksi/insertdata/hutang',
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
                                                'kode_rap':rec.get('kode_rap'),
                                                'uraian':frmtambahcashtodate.getComponent('uraian').getValue(),
                                                'keterangan_item':rec.get('keterangan_item'),
                                                'detail_material_kode':rec.get('kode_bahan')
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
                                    store_lap_hutang.load();
                                    store_temp.removeAll();
                                    winadds.hide();
                                }

                            }
                        }, {
                            text: 'Cancel',
                            handler: function() {
                                store_temp.removeAll();
                                winadds.hide();
                            }
                        }]
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
                    mode: 'multi',
                    selectByPosition : Ext.emptyFn,
                    listeners:{
                        select: function(){
                            // console.log('a');
                        },
                        deselect: function(){
                            // console.log('b');
                        }
                    }
                });

                var editor = Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 1,
                    listeners:{
                        beforeedit: function(){
                            selectedNode = grid_cashtodate.getSelectionModel().getSelection();
                            sm = grid_cashtodate.getSelectionModel();
                            // sm.select(1,true);
                            // sm.select(2,true);
                            // console.log(selectedNode);
                            for (var i = 0; i < selectedNode.length; i++) {
                                sm = grid_cashtodate.getSelectionModel();
                                sm.select(selectedNode[i].index,true);
                            };
                        }
                    }
                });

                var grid_cashtodate = Ext.create('Ext.grid.Panel', {
                    store: store_item_material,
                    selModel: sm,
                    selType: 'cellmodel',
                    // plugins: [editor],      
                    columns: [
                        {text: "No Bahan", flex: 1, sortable: true, dataIndex: 'kode'},
                        {text: "Nama Bahan", flex: 1, sortable: true, dataIndex: 'nama'},
                        {text: "Kode Rap", flex: 1, sortable: true, dataIndex: 'kode_rap'}
                        // ,
                        // {text: "Volume", flex: 1, sortable: true, dataIndex: 'volume',
                        //     editor:{
                        //         xtype: 'numberfield'
                        //     }
                        // },
                        // {text: "Jumlah", flex: 1, sortable: true, dataIndex: 'jumlah',
                        //     editor:{
                        //         xtype: 'numberfield'
                        //     }
                        // }
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
                                        // console.log(selectedNode[i]);
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

            function editcashtodate(id,no_bukti,uraian,toko,tanggal,pilihan,item,volume,jumlah,keterangan_item,pic,kode_rap) {

                var frmtambahcashtodate = Ext.create('Ext.form.Panel', {
                    url: '<?php echo base_url() ?>transaksi/editdata/hutang',
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
                            anchor: '-5',
                            name: 'id',
                            allowBlank: false,
                            hidden: true,
                            value: id
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Nomor Bukti',
                            anchor: '-5',
                            name: 'no_bukti',
                            allowBlank: false,
                            value: no_bukti
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'PIC',
                            anchor: '-5',
                            name: 'pic',
                            allowBlank: false,
                            value: pic
                        },
                        {
                            xtype: 'textarea',
                            fieldLabel: 'Uraian',
                            anchor: '-5',
                            name: 'uraian',
                            allowBlank: false,
                            value: uraian
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Kode Toko',
                            name: 'kode_toko',
                            store: store_item_toko,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih Toko...',
                            anchor: '-5',
                            allowBlank: false,
                            value: toko

                        },
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Tanggal ',
                            name: 'tanggal',
                            format:'M, d / Y',
                            submitFormat: 'Y-m-d',
                            anchor: '-5',
                            allowBlank: false,
                            value: tanggal
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Pilihan',
                            name: 'pilihan',
                            store: storepilihan,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih Item...',
                            anchor: '-5',
                            allowBlank: false,
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
                            allowBlank: false,
                            value: volume
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Jumlah yang dibayar',
                            anchor: '-5',
                            name: 'jumlah_bayar',
                            allowBlank: false,
                            value: jumlah
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Keterangan',
                            anchor: '-5',
                            name: 'keterangan_item',
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
                            value: item,
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
                                            store_lap_hutang.load();
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
                        }]
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

        function bayar_hutang() {

                var grid_cash = Ext.create('Ext.grid.Panel', {
                    store: store_bayar_hutang,
                    columns: [
                        {text: "Tanggal", flex: 1, sortable: true, dataIndex: 'tanggal'},
                        {text: "No Bukti<br>Pembayaran", flex: 1, sortable: true, dataIndex: 'no_bukti_bayar_hutang'},
                        {text: "PIC", flex: 1, sortable: true, dataIndex: 'pic'},
                        {text: "Bayar", flex: 1, sortable: true, dataIndex: 'bayar',
                            renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                        },
                        {text: "", xtype: 'actioncolumn', width: 25, sortable: true, icon: '<?= base_url(); ?>assets/images/delete.gif',
                            handler: function(grid, rowIndex, colIndex) {
                                rec = store_bayar_hutang.getAt(rowIndex);
                                id = rec.get('id');
                                Ext.MessageBox.confirm('Delete', 'Apakah anda akan menghapus item ini?',function(resbtn){
                                    if (resbtn == 'yes') {
                                        Ext.Ajax.request({
                                            url: '<?=base_url();?>transaksi/delete_data/bayar_hutang',
                                            method: 'POST',
                                            params: {
                                                'id':id
                                            },                              
                                            success: function() {
                                                store_cashtodate.load();
                                                store_lap_hutang.load();
                                                frmtambahcashtodate.getForm().reset();
                                                Ext.Msg.alert( "Status", "Delete successfully..!"); 
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
                    width: '100%',
                    height: '100%'
                });

                var frmtambahcashtodate = Ext.create('Ext.form.Panel', {
                    url: '<?php echo base_url() ?>transaksi/insertdata/bayar_hutang',
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
                            allowBlank: false,
                            value: new Date()
                        }, 
                        {
                            xtype: 'textfield',
                            fieldLabel: 'PIC',
                            name: 'pic',
                            itemId: 'pic',
                            allowBlank: false
                        },               
                        {
                            xtype: 'textfield',
                            fieldLabel: 'No Bukti Pembayaran',
                            name: 'no_bukti_bayar_hutang',
                            itemId: 'no_bukti_bayar_hutang',
                            allowBlank: false
                        },        
                        {
                            xtype: 'combobox',
                            fieldLabel: 'No Bukti',
                            name: 'no_bukti',
                            itemId: 'no_bukti',
                            store: store_no_bukti,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih No Bukti...',
                            allowBlank: false,
                            listeners:{
                                change: function(val){                                    

                                    value = val.getValue();

                                    store_bayar_hutang.proxy.setExtraParam('no_bukti', value);

                                    index = store_no_bukti.findExact('value',value);
                                    if (index != -1) {
                                        var rec = store_no_bukti.getAt(index);
                                        text = rec.get('jumlah');
                                        store_bayar_hutang.load({
                                            callback:function(a,b){
                                                if (b.resultSet.count > 0) {
                                                    jml = a[0].data.jml;
                                                    frmtambahcashtodate.getComponent('sisa').setValue(text - jml);
                                                } else {
                                                    frmtambahcashtodate.getComponent('sisa').setValue(text);
                                                }
                                            }
                                        });
                                    } else {
                                        store_bayar_hutang.load();
                                        text = value;
                                        frmtambahcashtodate.getComponent('sisa').setValue(0);
                                    }

                                    frmtambahcashtodate.getComponent('jumlah').setValue(text);

                                },
                                beforerender: function(){
                                    store_no_bukti.load();
                                }
                            }
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Jumlah Hutang',
                            name: 'jumlah',
                            itemId: 'jumlah',
                            allowBlank: false,
                            minValue: 0
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Bayar Hutang',
                            name: 'bayar',
                            itemId: 'bayar',
                            allowBlank: false,
                            value: 0,
                            minValue: 0
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Sisa Hutang',
                            name: 'sisa',
                            itemId: 'sisa',
                            allowBlank: false,
                            minValue: 0
                        },                   
                        {
                            height: 140,
                            width: 510,
                            layout: 'fit',
                            autoScroll: true,
                            bodyStyle: 'padding:5px 0px 0px 0px; border: 0px;',
                            items: [grid_cash]
                        }                
                    ],
                    buttons: [{
                            text: 'Save',
                            handler: function() {                                
                                var form = this.up('form').getForm();
                                if (form.isValid()) {
                                    form.submit({
                                        success: function(fp, o) {
                                            Ext.MessageBox.alert('Master Data', 'Insert successfully..!');
                                            form.reset();
                                            store_cashtodate.load();
                                            store_lap_hutang.load();
                                        }
                                    });
                                }

                            }
                        }, {
                            text: 'Cancel',
                            handler: function() {
                                form = this.up('form').getForm();
                                form.reset();
                                winadds.hide();
                            }
                        }]
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
