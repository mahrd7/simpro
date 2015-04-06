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

Ext.require([
    '*'
]);
var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

    Ext.define('dummydatast', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'id'
            },
            {
                name: 'uraian'
            },
            {
                name: 'satuan'
            },
            {
                name: 'volume'
            },
            {
                name: 'harga_satuan'
            },
            {
                name: 'jumlah_harga'
            }
         ]
    });

    var dummydata = [
        ['1','Bahan','ls','5','1000','5000']
    ];

	Ext.define('mdl_cb', {
        extend: 'Ext.data.Model',
        fields: [
        	{name: 'id', mapping: 'id'},
            {name: 'no', mapping: 'no'},
            {name: 'item_pekerjaan', mapping: 'item_pekerjaan'},            
            {name: 'satuan', mapping: 'satuan'},
            {name: 'volume', mapping: 'volume'},
            {name: 'harga', mapping: 'harga'},
            {name: 'total', mapping: 'total'}
         ]
    });

        Ext.define('mdl_analisa', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'no', mapping: 'no'},
            {name: 'sumber_daya', mapping: 'sumber_daya'},            
            {name: 'satuan', mapping: 'satuan'},
            {name: 'harga_satuan', mapping: 'harga_satuan'},
            {name: 'koefisien', mapping: 'koefisien'},
            {name: 'jumlah', mapping: 'jumlah'},
            {name: 'kode_analisa', mapping: 'kode_analisa'},
            {name: 'nama_analisa', mapping: 'nama_analisa'}
         ]
    });

    Ext.define('mdl_sumber_daya', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'kode', mapping: 'no'},
            {name: 'nama', mapping: 'sumber_daya'},            
            {name: 'spesifikasi', mapping: 'satuan'},
            {name: 'provinsi', mapping: 'harga_satuan'},
            {name: 'kota', mapping: 'koefisien'},
            {name: 'koefisien', mapping: 'jumlah'},
            {name: 'harga_satuan', mapping: 'kode_analisa'}
         ]
    });

    Ext.define('mdl_getedithargasatuan', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'detail_material_kode', mapping: 'detail_material_kode'},
            {name: 'detail_material_nama', mapping: 'detail_material_nama'},
            {name: 'harga', mapping: 'harga', type: 'int'}
         ]
    });

    Ext.define('mdl_combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value'}
         ]
    });

    dummysumberdaya = [
    {
        "id":"1",
        "kode":"504.0001",
        "nama":"Biaya Adminstrasi Bank",
        "spesifikasi":"1.1",
        "provinsi":"-",
        "kota":"DKI Jakarta",
        "koefisien":"1",
        "harga_satuan":"0"
    },{
        "id":"1",
        "kode":"504.0002",
        "nama":"Bunga Bank",
        "spesifikasi":"1.1",
        "provinsi":"-",
        "kota":"DKI Jakarta",
        "koefisien":"1",
        "harga_satuan":"0"
    },{
        "id":"1",
        "kode":"504.0003",
        "nama":"Biaya Bank",
        "spesifikasi":"1.1",
        "provinsi":"-",
        "kota":"",
        "koefisien":"1",
        "harga_satuan":"0"
    },{
        "id":"1",
        "kode":"504.0004",
        "nama":"Asuransi CAR",
        "spesifikasi":"1.1",
        "provinsi":"-",
        "kota":"",
        "koefisien":"1",
        "harga_satuan":"0"
    },{
        "id":"1",
        "kode":"504.0005",
        "nama":"PSP",
        "spesifikasi":"1.1",
        "provinsi":"-",
        "kota":"",
        "koefisien":"1",
        "harga_satuan":"0"
    }
    ];

    dummyanalisa = [
    {
        "nama_analisa":"UPAH ( 501 )",
        "kode_analisa":"501",
        "id":"1",
        "no":"1.1",
        "sumber_daya":"Laboratory technician",
        "satuan":"Month",
        "harga_satuan":"1,250,000.00",
        "koefisien":"20.00",
        "jumlah":"25,000,000.00"
    },{
        "nama_analisa":"UPAH ( 501 )",
        "kode_analisa":"501",
        "id":"2",
        "no":"1.2",
        "sumber_daya":"Labour",
        "satuan":"Day",
        "harga_satuan":"40,000.00",
        "koefisien":"1,200.00",
        "jumlah":"48,000,000.00"
    },{
        "nama_analisa":"PERALATAN ( 502 )",
        "kode_analisa":"502",
        "id":"3",
        "no":"2.1",
        "sumber_daya":"Cone penetorometer",
        "satuan":"Set",
        "harga_satuan":"10,000,000.00",
        "koefisien":"0.00",
        "jumlah":"0.00"
    },{
        "nama_analisa":"PERALATAN ( 502 )",
        "kode_analisa":"502",
        "id":"4",
        "no":"2.2",
        "sumber_daya":"Soil compaction test apparatus",
        "satuan":"Set",
        "harga_satuan":"14,025,000.00",
        "koefisien":"1.00",
        "jumlah":"14,025,000.00"
    },{
        "nama_analisa":"PERALATAN ( 502 )",
        "kode_analisa":"502",
        "id":"5",
        "no":"2.3",
        "sumber_daya":"Air meter",
        "satuan":"Set",
        "harga_satuan":"10,000,000.00",
        "koefisien":"1.00",
        "jumlah":"10,000,000.00"
    }
    ];

    dummycb = [
    {
        "id":"1",
        "no":"1",
        "item_pekerjaan":"Biaya Langsung",
        "satuan":"",
        "volume":"1.00",
        "harga":"6,054,824,955.00",
        "total":"6,054,824,955.00"
    },{
        "id":"2",
        "no":"1.1",
        "item_pekerjaan":"GENERAL ITEM",
        "satuan":"Ls",
        "volume":"1.00",
        "harga":"5,954,412,530.00",
        "total":"5,954,412,530.00"
    },{
        "id":"3",
        "no":"1.1.1",
        "item_pekerjaan":"Contractors temporary facilities : Office Quartes.",
        "satuan":"Ls",
        "volume":"1.00",
        "harga":"668,054,100.00",
        "total":"668,054,100.00"
    }
    ];

    var storesatuan = Ext.create('Ext.data.Store', {
        id: 'storecb',
        model: 'mdl_combo',
        remoteSort: true,
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

    var storedivisi = Ext.create('Ext.data.Store', {
        id: 'storedivisi',
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getdivisicombo',
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
         url: '<?php echo base_url() ?>pengendalian/getproyekcombo',
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
         url: '<?php echo base_url() ?>pengendalian/gettanggalcombo',
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
         url: '<?php echo base_url() ?>pengendalian/getedithargasatuan',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

    var storesubbidang = Ext.create('Ext.data.Store', {
        id: 'storecb',
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getsubbidangkode',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

Ext.onReady(function() {

    var storedummy = Ext.create('Ext.data.ArrayStore', {
        model: 'dummydatast',
        data: dummydata
    });

	var storecb = Ext.create('Ext.data.Store', {
        id: 'storecb',
        model: 'mdl_cb',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummycb
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: storecb,
        autoscroll: true,
        title: 'Current Budget <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'no'},
            {text: "ITEM PEKERJAAN", width:400, sortable: true, dataIndex: 'item_pekerjaan'},
            {text: "SATUAN", width:60, sortable: true, dataIndex: 'satuan'},
            {text: "VOLUME SISA ANGGARAN", width:150, sortable: true, dataIndex: 'volume'},
            {text: "HARGA SATUAN", width:120, sortable: true, dataIndex: 'harga'},
            {text: "TOTAL HARGA", width:120, sortable: true, dataIndex: 'total'},
            {text: "Kontrol",
        	columns:[
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/add.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = storecb.getAt(rowIndex);
                id = rec.get('id');
                no = rec.get('no');
                link(id,no);
            }},
        	{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/edit.png',
            handler: function(grid, rowIndex, colIndex){
            	var rec = storecb.getAt(rowIndex);
                id = rec.get('id');
                no = rec.get('no');
                satuan = rec.get('satuan');
                item_pekerjaan = rec.get('item_pekerjaan');
                volume = rec.get('volume');
                harga = rec.get('harga');
                total = rec.get('total');
                editcb(id,no,satuan,item_pekerjaan,volume,harga,total);
            }},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/accept.png',
            handler: function(grid, rowIndex, colIndex){
            	var rec = storecb.getAt(rowIndex);
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
											   																			
							}
				});
            }},{text: "",xtype: 'actioncolumn', width:28,icon:'<?=base_url();?>assets/images/application_go.png',
            handler: function(grid, rowIndex, colIndex){
            }},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/add.png',
            handler: function(grid, rowIndex, colIndex){
            	var rec = storecb.getAt(rowIndex);
                id = rec.get('id');
                no = rec.get('no');
                analisa(id,no)
            }}
        	]}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'RAPI',
                tooltip:'RAPI',
                handler: function(){

                }
            },'-',{
                text:'Edit Harga Satuan',
                tooltip:'Edit Harga Satuan',
                handler: function(){
                    confirmedithargasatuan();
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
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    var url ='<?php echo base_url(); ?>pengendalian/pilihcurrentbudget';
                    // console.log(url);
                    window.location=url;
                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text: 'Copy Dari Original Budget',
                handler: function(){

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
        renderTo: Ext.getBody()
        // ,
       	// bbar: [Ext.create('Ext.toolbar.Paging', {
        //                      pageSize: 50,
        //                      store: store,
        //                      displayInfo: true
        //              })
        // ]
    });
});

function editcb(id,no,satuan,item_pekerjaan,volume,harga,total){
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
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'kode',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: no
            },{
                xtype:'textarea',
                fieldLabel: 'Tahap Pekerjaan',
                anchor: '-5',
                name: 'tahap_pekerjaan',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: item_pekerjaan
        },{
            xtype: 'combobox',
            fieldLabel: 'SATUAN',
            name: 'satuan',
            store: storesatuan,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            value: satuan
        },{
                xtype:'textfield',
                fieldLabel: 'Volume Sisa Anggaran',
                anchor: '-5',
                name: 'volume_sisa_anggaran',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: volume
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
                            storecb.load();
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
        title: 'FORM EDIT RAP PENGENDALIAN',
        closeAction: 'hide',
        width: 500,
        height: 220,
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

function link(id,no){

    var storecb = Ext.create('Ext.data.Store', {
        id: 'storecb',
        model: 'mdl_cb',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummycb
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: storecb,
        autoscroll: true,
        title: 'Current Budget <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'no'},
            {text: "ITEM PEKERJAAN", width:240, sortable: true, dataIndex: 'item_pekerjaan'},
            {text: "SATUAN", width:55, sortable: true, dataIndex: 'satuan'},
            {text: "VOLUME SISA ANGGARAN", width:150, sortable: true, dataIndex: 'volume'},
            {text: "HARGA SATUAN", width:100, sortable: true, dataIndex: 'harga'},
            {text: "Kontrol",
            columns:[
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/edit.png',
            handler: function(grid, rowIndex, colIndex){
                var rec = storecb.getAt(rowIndex);
                id = rec.get('id');
                no = rec.get('no');
                satuan = rec.get('satuan');
                item_pekerjaan = rec.get('item_pekerjaan');
                volume = rec.get('volume');
                harga = rec.get('harga');
                editlink(id,no,satuan,item_pekerjaan,volume,harga);
            }},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/delete.png',
            handler: function(grid, rowIndex, colIndex){
                var rec = storecb.getAt(rowIndex);
                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
					if(resbtn == 'yes')
					{                                                                                                                      
					}
                });
            }},{text: "",xtype: 'actioncolumn', width:28,icon:'<?=base_url();?>assets/images/application_go.png',
            handler: function(grid, rowIndex, colIndex){
            }}
            ]}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Tambah Data',
                tooltip:'Tambah Data',
                handler: function(){
                    tambahlink();
                }
            },'-',{
                text:'Tambah Komposisi',
                tooltip:'Tambah Komposisi',
                handler: function(){
                    tambahkomposisi();
                }
            },'-',{
                text:'Copy Tahapan',
                tooltip:'Copy Tahapan',
                handler: function(){

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

    var winadd = Ext.create('Ext.Window', {
        title: 'FORM EDIT RAP PENGENDALIAN',
        closeAction: 'hide',
        width: 690,
        height: 440,
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

function tambahlink(){
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
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'kode',
                afterLabelTextTpl: required,
                allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'Tahap Pekerjaan',
                anchor: '-5',
                name: 'tahap_pekerjaan',
                afterLabelTextTpl: required,
                allowBlank: false
        },{
            xtype: 'combobox',
            fieldLabel: 'SATUAN',
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
                fieldLabel: 'Volume Sisa Anggaran',
                anchor: '-5',
                name: 'volume_sisa_anggaran',
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
                            storecb.load();
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
        title: 'FORM TAMBAH SUB RAP PENGENDALIAN',
        closeAction: 'hide',
        width: 500,
        height: 220,
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

function editlink(id,no,satuan,item_pekerjaan,volume,harga){
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
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'kode',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: no
            },{
                xtype:'textarea',
                fieldLabel: 'Tahap Pekerjaan',
                anchor: '-5',
                name: 'tahap_pekerjaan',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: item_pekerjaan
        },{
            xtype: 'combobox',
            fieldLabel: 'SATUAN',
            name: 'satuan',
            store: storesatuan,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            value: satuan
        },{
                xtype:'textfield',
                fieldLabel: 'Volume Sisa Anggaran',
                anchor: '-5',
                name: 'volume_sisa_anggaran',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: volume
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
                            storecb.load();
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
        title: 'FORM EDIT SUB RAP PENGENDALIAN',
        closeAction: 'hide',
        width: 500,
        height: 220,
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

function tambahkomposisi(){
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
                fieldLabel: 'Sub Kode',
                anchor: '-5',
                name: 'kode',
                afterLabelTextTpl: required,
                allowBlank: false
            },{
            xtype: 'combobox',
            fieldLabel: 'Sub Tahap Pekerjaan',
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
                fieldLabel: 'Volume',
                anchor: '-5',
                name: 'volume',
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
                            storecb.load();
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
        title: 'FORM TAMBAH SUB RAP PENGENDALIAN',
        closeAction: 'hide',
        width: 500,
        height: 170,
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

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
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/edit.png',
            handler: function(grid, rowIndex, colIndex){
                var rec = storeanalisa.getAt(rowIndex);
                id = rec.get('id');
                harga_satuan = rec.get('harga_satuan');
                koefisien = rec.get('koefisien');
                editanalisa(id,harga_satuan,koefisien);
            }},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/delete.png',
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
            emptyText: 'Pilih..',
            width: 300
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
        model: 'mdl_cb',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummycb
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
        model: 'mdl_cb',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummycb
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
        model: 'mdl_cb',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummycb
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