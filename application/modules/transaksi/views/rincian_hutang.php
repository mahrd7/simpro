<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

        <script type="text/javascript">
            var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
            Ext.require([
                '*'
            ]);
            Ext.define('rincian_hutang', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'value', mapping: 'value'},
                    {name: 'text', mapping: 'text'}
                ]
            });
            var store_rincian_hutang = Ext.create('Ext.data.Store', {
                id: 'store_rincian_hutang',
                model: 'rincian_hutang',
                pageSize: 50,
                proxy: {
                    type: 'ajax',
                    url: '<?php echo base_url() ?>',
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                },
                remoteFilter: true,
                autoLoad: false
            });
            store_rincian_hutang.load();

            Ext.onReady(function() {
                var grid_rincianhutang = Ext.create('Ext.grid.Panel', {
                id: 'grid_rincianhutang',
                    store: store_rincian_hutang,
                columns: [
                    {text: "No", flex: 1, sortable: true, dataIndex: 'No'},
                    {text: "Unit Usaha", width: 60, sortable: true, dataIndex: 'Unit Usaha'},
                    {text: "KOntrak Kini", width: 60, sortable: true, dataIndex: 'Unit Usaha'},
                        {text: "Awal", flex: 1,
                            columns: [
                            {text: "PU", flex: 1, sortable: true, dataIndex: 'PU'},
                            {text: "BK", flex: 1, sortable: true, dataIndex: 'BK'},
                    {text: "Laba Kotor", flex: 1, sortable: true, dataIndex: 'laba_kotor'}
                    ]
                    },
                        {text: "Posisi S/D", flex: 1,
                            columns: [
                            {text: "BK", flex: 1, sortable: true, dataIndex: 'bk'},
                            {text: "Selisih PU BK", flex: 1, sortable: true, dataIndex: 'selisih_pu_bk'},
                    {text: "Laba Kotor", flex: 1, sortable: true, dataIndex: 'Laba_kotor'}
                    ]
                    },
                    {text: "Sisa Anggaran", flex: 1, flex:1, sortable: true, dataIndex: 'sisa_anggaran'},
                        {text: "Proyeksi S/D akhir", flex: 1,
                            columns: [
                            {text: "PU", flex: 1, sortable: true, dataIndex: 'pu'},
                            {text: " BK", flex: 1, sortable: true, dataIndex: 'bk'},
                            {text: "MOS", flex: 1, sortable: true, dataIndex: 'mos'},
                            {text: "Laba Kotor", flex: 1, sortable: true, dataIndex: 'Laba_kotor'},
                        {text: "Deviasi", flex: 1, sortable: true, dataIndex: 'deviasi'}

                ]
                },
                        ],
                        dockedItems: [{
                        dock: 'top',
                            xtype: 'toolbar',
                        items: [
                                'Periode  S/D: ',
                            {
                                xtype: 'datefield',
                                name: 'tglakhir',
                                emptyText: 'Pilih..',
                            width: 150
                                }
                            , {
                                text: 'Go >>'
                            }, '-', {
                        text: 'Print'
                    }
                ]
                }],
                columnLines: true,
                width: '100%',
                height: '100%',
            title: 'RINCIAN HUTANG',
                renderTo: Ext.getBody()
            });
});

        </script>

    </head>
    <body>
    </body>
</html>
