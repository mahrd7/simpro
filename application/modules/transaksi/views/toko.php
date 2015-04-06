<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
        <script type="text/javascript">

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

            Ext.define('mdl', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'pilih_toko_id', mapping: 'pilih_toko_id'},
                    {name: 'proyek_id', mapping: 'proyek_id'},
                    {name: 'toko_id', mapping: 'toko_id'},
                    {name: 'toko_kode', mapping: 'toko_kode'},
                    {name: 'toko_nama', mapping: 'toko_nama'},
                    {name: 'user_id', mapping: 'user_name'},
                    {name: 'tgl_update', mapping: 'tgl_update'},
                    {name: 'ip_update', mapping: 'ip_update'},
                    {name: 'divisi_id', mapping: 'divisi_name'},
                    {name: 'waktu_update', mapping: 'waktu_update'}

                ]
            });

            Ext.define('mdl_toko', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'toko_id', mapping: 'toko_id'},
                    {name: 'toko_kode', mapping: 'toko_kode'},
                    {name: 'toko_nama', mapping: 'toko_nama'},
                    {name: 'toko_alamat', mapping: 'toko_alamat'},
                    {name: 'toko_produk', mapping: 'toko_produk'}

                ]
            });

            var dummydivisi = [
                {"text": "divisi 1", "value": "divisi 1"},
                {"text": "divisi 2", "value": "divisi 2"},
                {"text": "divisi 3", "value": "divisi 3"},
                {"text": "divisi 4", "value": "divisi 4"},
                {"text": "divisi 5", "value": "divisi 5"},
                {"text": "divisi 6", "value": "divisi 6"}
            ];

            var dummyproject = [
                {"text": "Jalan Tol Cipularang", "value": "Jalan Tol Cipularang"},
                {"text": "Pembangunan Jembatan", "value": "Pembangunan Jembatan"},
                {"text": "Pembangunan Ruko", "value": "Pembangunan Ruko"}

            ];
                
                var store_get_toko = Ext.create('Ext.data.Store', {
                    model: 'mdl_toko',
                    remoteSort: true,
                    pageSize: 50,
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: '<?php echo base_url() ?>transaksi/get_data_toko',
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });

            var store_get_pilih_toko = Ext.create('Ext.data.Store', {
                    model: 'mdl',
                    remoteSort: true,
                    pageSize: 50,
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: '<?php echo base_url() ?>transaksi/get_data_pilih_toko',
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });

            store_get_pilih_toko.load();

            var storedivisi = Ext.create('Ext.data.Store', {
                    id: 'storebln',
                    model: 'combobox',
                    pageSize: 50,
                    remoteFilter: true,
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: '<?php echo base_url() ?>transaksi/get_data_divisi',
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });

            storedivisi.load();

            var storeproject = Ext.create('Ext.data.Store', {
                    id: 'storebln',
                    model: 'combobox',
                    pageSize: 50,
                    remoteFilter: true,
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: '<?php echo base_url() ?>transaksi/get_data_proyek',
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });

            var storetanggal = Ext.create('Ext.data.Store', {
                    id: 'storebln',
                    model: 'combobox',
                    pageSize: 50,
                    remoteFilter: true,
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: '<?php echo base_url() ?>transaksi/get_data_tanggal',
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });


            Ext.onReady(function() {

                var grid = Ext.create('Ext.grid.Panel', {
                    title: 'Pilih Leveransir / Toko',
                    store: store_get_pilih_toko,
                    autoscroll: true,
                    columns: [
                        {text: "Kode Toko", flex: 1, sortable: true, dataIndex: 'toko_kode'},
                        {text: "Nama Toko", flex: 1, sortable: true, dataIndex: 'toko_nama'},
                        {text: "User Update", flex: 1, sortable: true, dataIndex: 'user_id'},
                        {text: "Tanggal Update", flex: 1, sortable: true, dataIndex: 'tgl_update'},
                        {text: "Ip Update", flex: 1, sortable: true, dataIndex: 'ip_update'},
                        {text: "Divisi Update", flex: 1, sortable: true, dataIndex: 'divisi_id'},
                        {text: "Waktu Update", flex: 1, sortable: true, dataIndex: 'waktu_update'},
                        {text: "", xtype: 'actioncolumn', width: 25, sortable: true, icon: '<?= base_url(); ?>assets/images/delete.gif',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store_get_pilih_toko.getAt(rowIndex);
                                var id = rec.get('pilih_toko_id');
                                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?', function(resbtn) {
                                    if (resbtn == 'yes')
                                    {
                                        Ext.Ajax.request({
                                            url: '<?= base_url(); ?>transaksi/deletedata/toko',
                                            method: 'POST',
                                            params: {
                                                'id': id
                                            },
                                            success: function() {
                                                store_get_pilih_toko.load();
                                                Ext.Msg.alert("Status", "Delete successfully..!", function() {
                                                });
                                            },
                                            failure: function() {
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    ],
                    columnLines: true,
                    dockedItems: [{
                            xtype: 'toolbar',
                            items: [{
                                    text: 'Tambah Data',
                                    tooltip: 'Tambah Data',
                                    handler: function() {
                                        tambah();
                                        store_get_toko.load();
                                    }
                                }, {
                                    text: 'Copy Dari Proyek Lain',
                                    tooltip: 'Copy Dari Bulan Sebelumnya',
                                    handler: function() {
                                        pilih_project_lain();
                                    }
                                }, {
                                    text: 'Delete ALL',
                                    tooltip: 'Delete Semua Item',
                                    handler: function() {
                                        Ext.MessageBox.confirm('Delete All', 'Apakah anda akan menghapus semua item ini?', function(resbtn) {
                                            if (resbtn == 'yes')
                                            {
                                                Ext.Ajax.request({
                                                    url: '<?= base_url(); ?>transaksi/deletedataall/toko',
                                                    success: function() {
                                                        store_get_pilih_toko.load();
                                                        Ext.Msg.alert("Status", "Delete All successfully..!", function() {
                                                        });
                                                    },
                                                    failure: function() {
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }]
                        }],
                    width: '100%',
                    height: '100%',
                    bbar: Ext.create('Ext.toolbar.Paging', {
                        pageSize: 50,
                        store: store_get_pilih_toko,
                        displayInfo: true
                    })
                });
                grid.render(document.body);
            });

            function tambah() {

                var grid = Ext.create('Ext.grid.Panel', {
                    title: 'Pilih Toko',
                    store: store_get_toko,
                    autoscroll: true,
                    selModel: Ext.create('Ext.selection.CheckboxModel'),
                    columns: [
                        {text: "Kode Toko", flex: 1, sortable: true, dataIndex: 'toko_kode'},
                        {text: "Nama Toko", flex: 1, sortable: true, dataIndex: 'toko_nama'},
                        {text: "Lokasi", flex: 1, sortable: true, dataIndex: 'toko_alamat'},
                        {text: "Produk", flex: 1, sortable: true, dataIndex: 'toko_produk'},
                        {text: "", xtype: 'actioncolumn', width: 25, sortable: true, icon: '<?= base_url(); ?>assets/images/delete.gif',
                            handler: function(grid, rowIndex, colIndex) {

                            }
                        }
                    ],
                    columnLines: true,
                    dockedItems: [{
                            xtype: 'toolbar',
                            itemId: 'bar1',
                            items: [{
                                    text: 'Cari Data',
                                },
                                {
                                    xtype: 'textfield',
                                    itemId: 'search'
                                }, {
                                    text: 'go',
                                    tooltip: 'Cari Data',
                                    handler: function() {
                                        text = grid.getComponent('bar1').getComponent('search').getValue();
                                        store_get_toko.load({
                                            params:{
                                                'text':text
                                            }
                                        })
                                    }
                                }]
                        }, {
                            xtype: 'toolbar',
                            dock: 'bottom',
                            items: [{
                                    text: 'Simpan Data',
                                    tooltip: 'Save',
                                    handler: function() {

                                    winadd.hide();
                                
                                    selectedNode = grid.getSelectionModel().getSelection();

                                    for (i = 0; i < selectedNode.length; i++) {
                                    Ext.Ajax.request({
                                        url: '<?=base_url();?>transaksi/insert_pilih_toko',
                                        method: 'POST',
                                        params: {
                                            'toko_id': selectedNode[i].data.toko_id
                                        },                              
                                    success: function() {
                                        Ext.Msg.alert( "Status", "Insert successfully..!");  
                                        store_get_pilih_toko.load();                                     
                                    },
                                    failure: function() {
                                        Ext.Msg.alert( "Status", "No Respond..!"); 
                                    }
                                    });                                     
                                    }

                                    }

                                },
                                {
                                    text: 'Cancel',
                                    tooltip: 'Cancel',
                                    handler: function() {
                                        winadd.hide();
                                    }
                                }]
                        }],
                    width: '100%',
                    height: '100%',
                    bbar: Ext.create('Ext.toolbar.Paging', {
                        pageSize: 50,
                        store: store_get_toko,
                        displayInfo: true
                    })
                });
                
                winadd = Ext.create('Ext.Window', {
                    title: 'PILIH LAH KODE TOKO SESUAI DENGAN PROYEK DAN DIVISI ANDA',
                    modal: true,
                    closeAction: 'hide',
                    width: '70%',
                    height: '80%',
                    layout: 'fit',
                    items: grid
                }).show();
            }
            
            function pilih_project_lain() {

                var frmadd = Ext.create('Ext.form.Panel', {
                    url: '<?php echo base_url() ?>transaksi/insertdatacopy',
                    id: 'frmadd ',
                    bodyStyle: 'padding:5px 5px 0',
                    width: '100%',
                    autoScroll: true,
                    fieldDefaults: {
                        msgTarget: 'side'
                    },
                    items: [{
                            xtype: 'combobox',
                            fieldLabel: 'Divisi',
                            name: 'divisi',
                            store: storedivisi,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih...',
                            listeners: {
                                change: function(val){
                                    storeproject.load({
                                        params:{
                                            'divisi': val.value
                                        }
                                    });                                
                                }
                            }
                        }, {
                            xtype: 'combobox',
                            fieldLabel: 'Project',
                            name: 'proyek',
                            store: storeproject,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih...',
                            listeners: {
                                change: function(val){
                                    storetanggal.load({
                                        params:{
                                            'proyek': val.value
                                        }
                                    });                                
                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Tanggal Satuan',
                            name: 'tanggal',
                            store: storetanggal,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih...'
                        }],
                    buttons: ['->', {
                            text: 'Save',
                            handler: function() {
                                var form = this.up('form').getForm();
                                if (form.isValid()) {
                                    form.submit({
                                        success: function(fp, o) {
                                            Ext.MessageBox.alert('Master Data', o.result.data);
                                            form.reset();
                                            store_get_pilih_toko.load();
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
                    title: 'Copy DATA DB HARGA SATUAN Dari Kontrak Lain',
                    closeAction: 'hide',
                    width: 400,
                    height: 300,
                    layout: 'fit',
                    items: frmadd
                }).show();
            }

        </script>

    </head>
    <body>
        <div id="form-ct"></div>
    </body>
</html>