<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

        <script type="text/javascript">
            var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
            Ext.require([
                '*'
            ]);
            var dummysumberdana = [
                {"text": "ABPN", "value": "ABPN"},
                {"text": "APBD", "value": "APBD"},
                {"text": "LOAN", "value": "LOAN"}

            ];
            var dummyproject = [
                {"text": "MC", "value": "MC"},
                {"text": "Progres", "value": "Progres"},
            ];
            var dummyproyek = [
                {"text": "Jalan Tol Cipularang", "value": "Jalan Tol Cipularang"},
                {"text": "Pembangunan Jembatan", "value": "Pembangunan Jembatan"},
                {"text": "Pembangunan Ruko", "value": "Pembangunan Ruko"}

            ];
            var dummykontrak = [
                {"text": "11/scan/10-2013", "value": "11/scan/10-2013"},
                {"text": "400/scan/1001-2013", "value": "400/scan/1001-2013"},
                {"text": "500/scan/1003-2013", "value": "500/scan/1003-2013"}

            ];
            Ext.define('kartu_hutang', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id', mapping: 'id_piutang'},
                    {name: 'No', mapping: 'no'},
                    {name: 'uraian', mapping: 'uraian'},
                    {name: 'tanggal', mapping: 'tanggal_pengajuan_tagihan'},
                    {name: 'prog', mapping: 'prog_pengajuan_tagihan'},
                    {name: 'nilai', mapping: 'nilai_pengajuan_tagihan'},
                    {name: 'uang_muka', mapping: 'potongan_uang_muka'},
                    {name: 'retensi', mapping: 'potongan_retensi'},
                    {name: 'ppn', mapping: 'potongan_ppn'},
                    {name: 'sisa_anggaran', mapping: 'jumlah_neto'},
                    {name: 'PPH23', mapping: 'potongan_pph23'},
                    {name: 'ByBank', mapping: 'potongan_bank'},
                    {name: 'lainlain', mapping: 'potongan_lain'},
                    {name: 'tanggal2', mapping: 'tanggal_penerimaan_bersih'},
                    {name: 'jumlah', mapping: 'jumlah'},
                    {name: 'Keterangan', mapping: 'keterangan'}
                ]
            });
            Ext.define('combobox', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'value', mapping: 'value'},
                    {name: 'text', mapping: 'text'}
                ]
            });
            var store_kartu_hutang = Ext.create('Ext.data.Store', {
                id: 'store_kartu_hutang',
                model: 'kartu_hutang',
                pageSize: 50,
                proxy: {
                    type: 'ajax',
                    url: '<?php echo base_url() ?>transaksi/get_data_piutang',
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                },
                remoteFilter: true,
                autoLoad: false
            });
            store_kartu_hutang.load();


            var storesumberdana = Ext.create('Ext.data.Store', {
                id: 'storebln',
                model: 'combobox',
                pageSize: 50,
                remoteFilter: true,
                autoLoad: false,
                data: dummysumberdana
            });
            var storecarabayar = Ext.create('Ext.data.Store', {
                id: 'storebln',
                model: 'combobox',
                pageSize: 50,
                remoteFilter: true,
                autoLoad: false,
                data: dummyproject
            });
            var storeproyek = Ext.create('Ext.data.Store', {
                id: 'storebln',
                model: 'combobox',
                pageSize: 50,
                remoteFilter: true,
                autoLoad: false,
                data: dummyproyek
            });
            var storekontrak = Ext.create('Ext.data.Store', {
                id: 'storebln',
                model: 'combobox',
                pageSize: 50,
                remoteFilter: true,
                autoLoad: false,
                data: dummykontrak
            });

            Ext.onReady(function() {
                store_kartu_hutang.load();

                var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
                    clicksToEdit: 2,        
                    listeners: {
                        afteredit: function(rec,obj) {
                            var selectedNode = grid_kartuhutang.getSelectionModel().getSelection();
                            data = selectedNode[0].data;

                            id = data.id;

                            No = data.No;
                            uraian = data.uraian;
                            tanggal = data.tanggal;
                            prog = data.prog;
                            nilai = data.nilai;
                            uang_muka = data.uang_muka;
                            retensi = data.retensi;
                            PPH23 = data.PPH23;
                            ByBank = data.ByBank;
                            lainlain = data.lainlain;
                            tanggal2 = data.tanggal2;
                            Keterangan = data.Keterangan;

                            Ext.Ajax.request({
                                 url: '<?=base_url();?>transaksi/kartu_piutang_action/edit',
                                    method: 'POST',
                                    params: {
                                        'id' :  id,
                                        'no' : No, 
                                        'uraian' : uraian,
                                        'tgl_tagihan' : tanggal,
                                        'prog_tagihan' : prog,
                                        'nilai_tagihan' : nilai,
                                        'uangmuka_pot' : uang_muka,
                                        'retensi_pot' : retensi,
                                        'pph23_pot' : PPH23,
                                        'bank_pot' : ByBank,
                                        'lain_pot' : lainlain,
                                        'tgl_penerimaan' : tanggal2,
                                        'keterangan' : Keterangan
                                        },                              
                                success: function() {
                                Ext.Msg.alert( "Status", "Update successfully..!"); 
                                store_kartu_hutang.load();                                        
                                },
                                failure: function() {
                                Ext.Msg.alert( "Status", "No Respond..!"); 
                                }
                            });

                            // console.log(id+tahap_nama_kendali+tahap_satuan_kendali+tahap_volume_kendali+tahap_harga_satuan_kendali);
                        }   
                    }
                });

                var grid_kartuhutang = Ext.create('Ext.grid.Panel', {
                    id: 'grid_kartuhutang',
                    store: store_kartu_hutang,
                    plugins: [rowEditing],
                    columns: [
                        {text: "No", width: 40, sortable: true, dataIndex: 'No',
                            editor:{
                                xtype: 'textfield'
                            }
                        },
                        {text: "Uraian", width: 90, sortable: true, dataIndex: 'uraian',
                            editor:{
                                xtype: 'textfield'
                            }
                        },
                        {text: "Pengajuan Tagihan (Incl.PPN)", flex: 1,
                            columns: [
                                {text: "Tanggal", width: 70, sortable: true, dataIndex: 'tanggal',
                                    editor:{
                                        xtype: 'datefield',
                                        format: 'Y-m-d'
                                    }
                                },
                                {text: "Prog (%)", width: 70, sortable: true, dataIndex: 'prog',
                                renderer: Ext.util.Format.numberRenderer('00.00 %'),
                                editor:{
                                    xtype: 'textfield'
                                }
                                },
                                {text: "Nilai (Rp)", width: 70, sortable: true, dataIndex: 'nilai',
                                renderer: Ext.util.Format.numberRenderer('00,000.00'),
                                editor:{
                                    xtype: 'textfield'
                                }
                                }
                            ]
                        },
                        {text: "Potongan", flex: 1,
                            columns: [
                                {text: "Uang Muka", width: 70, sortable: true, dataIndex: 'uang_muka',
                                renderer: Ext.util.Format.numberRenderer('00,000.00'),
                                editor:{
                                    xtype: 'textfield'
                                }
                                },
                                {text: "Retensi", width: 70, sortable: true, dataIndex: 'retensi',
                                renderer: Ext.util.Format.numberRenderer('00,000.00'),
                                editor:{
                                    xtype: 'textfield'
                                }
                                },
                                {text: "PPN", width: 70, sortable: true, dataIndex: 'ppn',
                                renderer: Ext.util.Format.numberRenderer('00,000.00'),
                                editor:{
                                    xtype: 'textfield',
                                    disabled: true
                                }
                                }
                            ]
                        },
                        {text: "Jumlah Netto", width: 90, sortable: true, dataIndex: 'sisa_anggaran',
                                renderer: Ext.util.Format.numberRenderer('00,000.00'),
                                editor:{
                                    xtype: 'textfield',
                                    disabled: true
                                }
                                },
                        {text: "Potongan", flex: 1,
                            columns: [
                                {text: "PPH Final", width: 70, sortable: true, dataIndex: 'PPH23',
                                renderer: Ext.util.Format.numberRenderer('00,000.00'),
                                editor:{
                                    xtype: 'textfield'
                                }
                                },
                                {text: "By.Bank", width: 70, sortable: true, dataIndex: 'ByBank',
                                renderer: Ext.util.Format.numberRenderer('00,000.00'),
                                editor:{
                                    xtype: 'textfield'
                                }
                                },
                                {text: "Lain-lain", width: 70, sortable: true, dataIndex: 'lainlain',
                                renderer: Ext.util.Format.numberRenderer('00,000.00'),
                                editor:{
                                    xtype: 'textfield'
                                }
                                }
                            ]
                        },
                        {text: "Penerimaan Bersih", flex: 1,
                            columns: [
                                {text: "Tanggal", width: 70, sortable: true, dataIndex: 'tanggal2',
                                    editor:{
                                        xtype: 'datefield',
                                        format: 'Y-m-d'
                                    }
                                },
                                {text: "Jumlah", width: 70, sortable: true, dataIndex: 'jumlah',
                                renderer: Ext.util.Format.numberRenderer('00,000.00'),
                                editor:{
                                    xtype: 'textfield',
                                    disabled: true
                                }
                                }
                            ]
                        },
                        {text: "Keterangan", flex:1, sortable: true, dataIndex: 'Keterangan',
                                    editor:{
                                        xtype: 'textarea'
                                    }
                                },
                        {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
                        handler: function(rec,rowIndex,colIndex){
                            var selectedNode = rec.store.data.items[rowIndex].data;
                            data = selectedNode;

                            id = data.id;
                            Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                                if(resbtn == 'yes')
                                {
                                    Ext.Ajax.request({
                                         url: '<?=base_url();?>transaksi/delete_data/piutang',
                                            method: 'POST',
                                            params: {
                                                'id' :  id
                                                },                              
                                        success: function() {
                                        Ext.Msg.alert( "Status", "Delete successfully..!"); 
                                        store_kartu_hutang.load();                                        
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
                    dockedItems: [{
                            dock: 'top',
                            xtype: 'toolbar',
                            items: [
                                // 'Tanggal: ',
                                // {
                                //     xtype: 'datefield',
                                //     name: 'tglawal',
                                //     emptyText: 'Pilih..',
                                //     width: 150
                                // },
                                // 'S/D : ',
                                // {
                                //     xtype: 'datefield',
                                //     name: 'tglakhir',
                                //     emptyText: 'Pilih..',
                                //     width: 150
                                // }, {
                                //     text: 'Go >>'},
                                // {
                                //     xtype: 'combobox',
                                //     fieldLabel: 'Sumber Dana',
                                //     name: 'blndoc',
                                //     store: storesumberdana,
                                //     valueField: 'value',
                                //     displayField: 'text',
                                //     typeAhead: true,
                                //     queryMode: 'local',
                                //     emptyText: 'Pilih...'

                                // },
                                // {
                                //     xtype: 'combobox',
                                //     fieldLabel: 'Cara Bayar',
                                //     name: 'blndoc',
                                //     store: storecarabayar,
                                //     valueField: 'value',
                                //     displayField: 'text',
                                //     typeAhead: true,
                                //     queryMode: 'local',
                                //     emptyText: 'Pilih...'

                                // }
                                ]
                        },{
                                    dock: 'top',
                                    xtype: 'toolbar',
                                    items: [
                                        {
                                            text:'Print'
                                        }
                                        // {
                                        //     xtype: 'combobox',
                                        //     fieldLabel: 'Nama Proyek',
                                        //     name: 'blndoc',
                                        //     store: storeproyek,
                                        //     valueField: 'value',
                                        //     displayField: 'text',
                                        //     typeAhead: true,
                                        //     queryMode: 'local',
                                        //     emptyText: 'Pilih...'

                                        // },
                                        // {
                                        //     xtype: 'combobox',
                                        //     fieldLabel: 'Nomor Kontrak',
                                        //     name: 'blndoc',
                                        //     store: storekontrak,
                                        //     valueField: 'value',
                                        //     displayField: 'text',
                                        //     typeAhead: true,
                                        //     queryMode: 'local',
                                        //     emptyText: 'Pilih...'

                                        // },
                                        // 'Tanggal Kontrak : ',
                                        // {                                                                                         xtype: 'datefield',                                           
                                        //     name: 'tglkontrak',
                                        //     emptyText: 'Pilih..',
                                        //     width: 150
                                        // },
                                        // {
                                        //     text: 'Print'
                                        // }
                                    ]
                                },{
                                dock: 'bottom',
                                xtype: 'toolbar',
                                items: [
                                    {
                                    text: 'Tambah Data',
                                    handler: function(){
                                        tambahKartuPiutang();
                                    }
                                    }
                                ]
                            }],
                    columnLines: true,
                    width: '100%',
                    height: '100%',
                    title: 'KARTU PIUTANG',
                    renderTo: Ext.getBody()
                });
            });


function tambahKartuPiutang(){
    var formkartupiutang = Ext.widget({
        xtype: 'form',
        layout: 'form',
        frame: false,
        autoScroll: true,
        url: '<?php echo base_url(); ?>transaksi/kartu_piutang_action/tambah',
        bodyPadding: '5 5 0',
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 150
        },
        items: [{
            xtype: 'textfield',
            fieldLabel: 'No',
            name: 'no',
            allowBlank: false
        },{
            xtype: 'textfield',
            fieldLabel: 'Uraian',
            name: 'uraian',
            allowBlank: false
        },{
            xtype: 'datefield',
            fieldLabel: 'Tanggal Pengajuan Tagihan',
            name: 'tgl_tagihan',
            allowBlank: false,
            value: new Date()
        },{
            xtype: 'textfield',
            fieldLabel: 'Prog Pengajuan Tagihan(%)',
            name: 'prog_tagihan',
            allowBlank: false
        },{
            xtype: 'textfield',
            fieldLabel: 'Nilai Pengajuan Tagihan(Rp)',
            name: 'nilai_tagihan',
            allowBlank: false
        },{
            xtype: 'textfield',
            fieldLabel: 'Potongan Uang Muka',
            name: 'uangmuka_pot',
            allowBlank: false
        },{
            xtype: 'textfield',
            fieldLabel: 'Potongan Retensi',
            name: 'retensi_pot',
            allowBlank: false
        }
        // ,{
        //     xtype: 'textfield',
        //     fieldLabel: 'Potongan PPN',
        //     name: 'ppn_pot',
        //     allowBlank: false
        // }
        // ,{
        //     xtype: 'textfield',
        //     fieldLabel: 'Jumlah Neto',
        //     name: 'jumlah_neto',
        //     allowBlank: false
        // }
        ,{
            xtype: 'textfield',
            fieldLabel: 'Potongan PPH23',
            name: 'pph23_pot',
            allowBlank: false
        },{
            xtype: 'textfield',
            fieldLabel: 'Potongan Bank',
            name: 'bank_pot',
            allowBlank: false
        },{
            xtype: 'textfield',
            fieldLabel: 'Potongan Lain-lain',
            name: 'lain_pot',
            allowBlank: false
        },{
            xtype: 'datefield',
            fieldLabel: 'Tanggal Penerimaan Bersih',
            name: 'tgl_penerimaan',
            allowBlank: false,
            value: new Date()
        }
        // ,{
        //     xtype: 'textfield',
        //     fieldLabel: 'Penerimaan Bersih Lain-lain',
        //     name: 'lain_penerimaan',
        //     allowBlank: false
        // }
        ,{
            xtype: 'textarea',
            fieldLabel: 'Keterangan',
            name: 'keterangan',
            allowBlank: false
        },],

        buttons: [{
            text: 'Save',
            handler: function(){               
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        success: function() {
                            Ext.MessageBox.alert('Informasi','Insert successfully..!');
                            store_kartu_hutang.load();
                        }
                    });
                    winaddkartupiutang.hide();
                }
            }
        }
        ,{
            text: 'Cancel',
            handler: function() {
               winaddkartupiutang.hide();
            }
        }]
    });

    var winaddkartupiutang = Ext.create('Ext.Window', {
        closeAction: 'hide',
        height: 450,
        width: 520,
        layout: 'fit',        
        title: 'Tambah Kartu Piutang',
        items: formkartupiutang
    }).show();
}

        </script>

    </head>
    <body>
    </body>
</html>
