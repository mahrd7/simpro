<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

        <script type="text/javascript">
            var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
            Ext.require([
                '*'
            ]);

            Ext.define('saldo_kas', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'tanggal', mapping: 'tanggal', type:'date'},
                    {name: 'uraian', mapping: 'uraian'},
                    {name: 'saldo', mapping: 'saldo'}
                ]
            });

            var store_saldo_kas = Ext.create('Ext.data.Store', {
                model: 'saldo_kas',
                proxy: {
                    type: 'ajax',
                    extraParams: {
                        tgl_awal : new Date(),
                        tgl_akhir : new Date()
                    },
                    url: '<?php echo base_url() ?>transaksi/get_data_saldo_kas',
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                },
                remoteFilter: true,
                autoLoad: false
            });

            Ext.onReady(function() {
                store_saldo_kas.load();

                var grid_kladbank = Ext.create('Ext.grid.Panel', {
                    store: store_saldo_kas,
                    columns: [
                        {text: "Tanggal", width:120, align:'center', sortable: true, dataIndex: 'tanggal',
                            renderer: function(val){
                                if (val) {
                                    fullyear = val.getFullYear();
                                    month = toMonthName(val.getMonth());
                                    date = val.getDate();

                                    data = date+' '+month+' '+fullyear;
                                } else {
                                    data = '';
                                }
                                return data;
                            }
                        },
                        {text: "Uraian", flex:1, sortable: true, dataIndex: 'uraian'},
                        {text: "Saldo", flex: 1, sortable: true, dataIndex: 'saldo',
                            renderer: Ext.util.Format.numberRenderer('Rp 00,000')
                        }
                    ],
                    dockedItems: [{
                            itemId: 'dock',
                            dock: 'top',
                            xtype: 'toolbar',
                            items: [
                                // 'Periode: ',
                                // {
                                //     xtype: 'datefield',
                                //     name: 'tglawal',
                                //     itemId:'tglawal',
                                //     emptyText: 'Pilih..',
                                //     width: 150,
                                //     value: new Date()
                                // },
                                'Periode S/D : ',
                                {
                                    xtype: 'datefield',
                                    name: 'tglakhir',
                                    itemId:'tglakhir',
                                    emptyText: 'Pilih..',
                                    width: 150,
                                    value: new Date()
                                }, {
                                    text: 'Go >>',
                                    handler: function(){
                                        dock = grid_kladbank.getComponent('dock');
                                        // tgl_awal = dock.getComponent('tglawal').getValue();
                                        tgl_akhir = dock.getComponent('tglakhir').getValue();

                                        // store_saldo_kas.proxy.setExtraParam('tgl_awal', tgl_awal);
                                        store_saldo_kas.proxy.setExtraParam('tgl_akhir', tgl_akhir);
                                        store_saldo_kas.load();
                                    }
                                },'-',
                                {
                                    text:'Print'
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
