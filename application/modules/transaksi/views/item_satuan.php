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
                    {name: 'no', mapping: 'tahap_kode_kendali'},
                    {name: 'kode_toko', mapping: 'kode_toko'},
                    {name: 'nama_toko', mapping: 'nama_toko'},
                    {name: 'user_update', mapping: 'user_update'},
                    {name: 'tgl_update', mapping: 'tgl_update'},
                    {name: 'ip_update', mapping: 'ip_update'},
                    {name: 'divisi_update', mapping: 'divisi_update'},
                    {name: 'waktu_update', mapping: 'waktu_update'},
                    {name: 'kontrol', mapping: 'kontrol'}

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

            Ext.onReady(function() {

                var store = Ext.create('Ext.data.Store', {
                    id: 'store',
                    model: 'mdl',
                    remoteSort: true,
                    pageSize: 50,
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: '<?php echo base_url() ?>trasaksi/getdata/toko',
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });

                var grid = Ext.create('Ext.grid.Panel', {
					title: 'Item Transaksi',
                    store: store,
                    autoscroll: true,
                    columns: [
                        {text: "NO", flex: 1, sortable: true, dataIndex: 'NO'},
                        {text: "Kode Toko", flex: 1, sortable: true, dataIndex: 'Kode Toko'},
                        {text: "Nama Toko", flex: 1, sortable: true, dataIndex: 'Nama Toko'},
                        {text: "User Update", flex: 1, sortable: true, dataIndex: 'User Update'},
                        {text: "Tanggal Update", flex: 1, sortable: true, dataIndex: 'Ip Update'},
                        {text: "Ip Update", flex: 1, sortable: true, dataIndex: 'tahap_total_kendali'},
                        {text: "Divisi Update", flex: 1, sortable: true, dataIndex: 'Divisi Update'},
                        {text: "Waktu Update", flex: 1, sortable: true, dataIndex: 'Waktu Update'},
                        {text: "KOntrol", flex: 1, sortable: true, dataIndex: 'KOntrol'},
                        {text: "", xtype: 'actioncolumn', width: 25, sortable: true, icon: '<?= base_url(); ?>assets/images/delete.gif',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                var id = rec.get('rr_id');
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
                                                store.load();
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
                                    }
                                }, {
                                    text: 'Copy Dari Proyek Lain',
                                    tooltip: 'Copy Dari Bulan Sebelumnya',
                                    handler: function() {
                                        pilih_project_lain();
                                    }
                                }, {
                                    text: 'Delete ALL',
                                    tooltip: 'Copy Dari Bulan Sebelumnya',
                                    handler: function() {

                                    }
                                }]
                        }],
                    width: '100%',
                    height: '100%',
                    bbar: Ext.create('Ext.toolbar.Paging', {
                        pageSize: 50,
                        store: store,
                        displayInfo: true
                    })
                });
                store.load();
                grid.render(document.body);
            });

            function tambah() {
                var store = Ext.create('Ext.data.Store', {
                    id: 'store',
                    model: 'mdl',
                    remoteSort: true,
                    pageSize: 50,
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: '<?php echo base_url() ?>trasaksi/getdata/toko',
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });

                var grid = Ext.create('Ext.grid.Panel', {
                    selType: 'checkboxmodel',
                    store: store,
                    autoscroll: true,
                    columns: [
                        {text: "Kode Toko", flex: 1, sortable: true, dataIndex: 'Kode Toko'},
                        {text: "Nama Toko", flex: 1, sortable: true, dataIndex: 'Nama Toko'},
                        {text: "Lokasi", flex: 1, sortable: true, dataIndex: 'lokasi'},
                        {text: "Produk", flex: 1, sortable: true, dataIndex: 'Produk'},
                        {text: "", xtype: 'actioncolumn', width: 25, sortable: true, icon: '<?= base_url(); ?>assets/images/delete.gif',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                var id = rec.get('rr_id');
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
                                                store.load();
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
                                    text: 'Cari Data Data',
                                    tooltip: 'Tambah Data',
                                    handler: function() {
                                        tambah();
                                    }

                                },
                                {
                                    xtype: 'textfield',
                                    name: 'search'

                                }, {
                                    text: 'go',
                                    tooltip: 'Cari Data',
                                    handler: function() {

                                    }
                                }]
                        }, {
                            xtype: 'toolbar',
                            dock: 'bottom',
                            items: [{
                                    text: 'Simpan Data',
                                    tooltip: 'Save',
                                    handler: function() {

                                    }

                                },
                                {
                                    text: 'Cancel',
                                    tooltip: 'Cancel',
                                    handler: function() {

                                    }
                                }]
                        }],
                    width: '100%',
                    height: '100%',
                    bbar: Ext.create('Ext.toolbar.Paging', {
                        pageSize: 50,
                        store: store,
                        displayInfo: true
                    })
                });
				
                winadd = Ext.create('Ext.Window', {
                    title: 'PILIH LAH KODE TOKO SESUAI DENGAN PROYEK DAN DIVISI ANDA',
                    closeAction: 'hide',
					modal: true,
                    width: 600,
                    height: 400,
                    layout: 'fit',
                    items: grid
                }).show();
            }
            function pilih_project_lain() {
                var storedivisi = Ext.create('Ext.data.Store', {
                    id: 'storebln',
                    model: 'combobox',
                    pageSize: 50,
                    remoteFilter: true,
                    autoLoad: false,
                    data: dummydivisi
                });
                var storeproject = Ext.create('Ext.data.Store', {
                    id: 'storebln',
                    model: 'combobox',
                    pageSize: 50,
                    remoteFilter: true,
                    autoLoad: false,
                    data: dummyproject
                });

                var frmadd = Ext.create('Ext.form.Panel', {
                    url: '<?php echo base_url() ?>admin/insertdata/direktorat',
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
                            name: 'blndoc',
                            store: storedivisi,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih...'
                        }, {
                            xtype: 'combobox',
                            fieldLabel: 'Project',
                            name: 'blndoc',
                            store: storeproject,
                            valueField: 'value',
                            displayField: 'text',
                            typeAhead: true,
                            queryMode: 'local',
                            emptyText: 'Pilih...'

                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Tanggal Satuan',
                            name: 'blndoc',
                            store: storedivisi,
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
                                            Ext.MessageBox.alert('Master Data', 'Insert successfully..!');
                                            form.reset();
                                            store.load();
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
                    title: 'Copy DATA DB HARGA SATUAN Dari Kontrak LainS',
                    closeAction: 'hide',
                    width: 300,
                    height: 200,
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