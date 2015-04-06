<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

        <script type="text/javascript">
            var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
            Ext.require([
                '*'
            ]);

            Ext.define('klad_bank', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id', mapping: 'id'},
                    {name: 'tanggal', mapping: 'tanggal', type:'date'},
                    {name: 'pic', mapping: 'pic'},
                    {name: 'keterangan', mapping: 'keterangan'},
                    {name: 'no_bukti', mapping: 'no_bukti'},
                    {name: 'kredit', mapping: 'kredit'},
                    {name: 'debet', mapping: 'debet'},
                    {name: 'saldo', mapping: 'saldo'},
                    {name: 'status', mapping: 'status'}
                ]
            });
            Ext.define('combobox', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'value', mapping: 'value'},
                    {name: 'text', mapping: 'text'}
                ]
            });

            Ext.define('temp', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id', mapping: 'id'},
                    {name: 'no_bukti', mapping: 'no_bukti'},
                    {name: 'nama_bahan', mapping: 'nama_bahan'},
                    {name: 'nama_bahan', mapping: 'nama_bahan'},
                    {name: 'jumlah', mapping: 'jumlah'}
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

            var store_temp = Ext.create('Ext.data.Store', {
                model: 'temp',
                remoteFilter: true,
                autoLoad: true,
                data: temp
            });

            var pilih_item_transaksi =[
                {'text':'Management Cash','value':'management_cash'},
                {'text':'Hutang','value':'hutang'},
                {'text':'Antisipasi','value':'antisipasi'}
            ];

            var store_klad_bank = Ext.create('Ext.data.Store', {
                model: 'klad_bank',
                proxy: {
                    type: 'ajax',
                    url: '<?php echo base_url() ?>transaksi/get_data_klad_bank',
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                },
                remoteFilter: true,
                autoLoad: false
            });

            var store_pilih_item_transaksi = Ext.create('Ext.data.Store', {
                model: 'combobox',
                remoteFilter: true,
                autoLoad: false,
                data: pilih_item_transaksi
            });

            Ext.onReady(function() {
                store_klad_bank.load();

                var grid_kladbank = Ext.create('Ext.grid.Panel', {
                    store: store_klad_bank,
                    columns: [
                        {text: "Tanggal", flex:1, sortable: true, dataIndex: 'tanggal',
                            renderer: function(val){
                                fullyear = val.getFullYear();
                                month = toMonthName(val.getMonth());
                                date = val.getDate();

                                data = date+' '+month+' '+fullyear;
                                return data;
                            }
                        },
                        {text: "PIC", flex:1, sortable: true, dataIndex: 'pic'},
                        {text: "Keterangan", flex:1, sortable: true, dataIndex: 'keterangan'},
                        {text: "Nomor Bukti", flex:1, sortable: true, dataIndex: 'no_bukti'},
                        {text: "Transaksi",
                            columns: [
                                {text: "Debet", width: 120, sortable: true, dataIndex: 'debet',
                                    renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                                },
                                {text: "Kredit", width: 120, sortable: true, dataIndex: 'kredit',
                                    renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                                },
                            ]
                        },
                        {text: "Saldo", flex: 1, sortable: true, dataIndex: 'saldo',
                            renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                        }
                    ],
                    dockedItems: [{
                            dock: 'top',
                            xtype: 'toolbar',
                            items: [
                                'Periode: ',
                                {
                                    xtype: 'datefield',
                                    name: 'tglawal',
                                    emptyText: 'Pilih..',
                                    width: 150
                                },
                                'S/D : ',
                                {
                                    xtype: 'datefield',
                                    name: 'tglakhir',
                                    emptyText: 'Pilih..',
                                    width: 150
                                }, {
                                    text: 'Go >>'
                                }
                            ]
                        }
                    ],
                    columnLines: true,
                    width: '100%',
                    height: '100%',
                    title: 'KLAD KAS'
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
                        items: grid_kladbank
                    }]
                });
            });

            function toMonthName(val){
                if (val == 0){
                    data = 'Januari';
                } else if (val == 1){
                    data = 'Februari';
                } else if (val == 2){
                    data = 'Maret';
                } else if (val == 3){
                    data = 'April';
                } else if (val == 4){
                    data = 'Mei';
                } else if (val == 5){
                    data = 'Juni';
                } else if (val == 6){
                    data = 'Juli';
                } else if (val == 7){
                    data = 'Agustus';
                } else if (val == 8){
                    data = 'September';
                } else if (val == 9){
                    data = 'Oktober';
                } else if (val == 10){
                    data = 'November';
                } else if (val == 11){
                    data = 'Desember';
                }
                return data;
            }
        </script>
    </head>
    <body>
    </body>
</html>
