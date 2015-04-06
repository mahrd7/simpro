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

	Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'no', mapping: 'no'},
            {name: 'kode', mapping: 'kode'},
            {name: 'nama', mapping: 'nama'},
            {name: 'spesifikasi', mapping: 'spesifikasi'},
            {name: 'sumber_daya', mapping: 'sumber_daya'},
            {name: 'satuan', mapping: 'satuan'},
            {name: 'harga', mapping: 'harga'},
            {name: 'provinsi', mapping: 'provinsi'},
            {name: 'provinsi_id', mapping: 'provinsi_id'},
            {name: 'user', mapping: 'user'},
            {name: 'tanggal', mapping: 'tanggal'},
            {name: 'ip', mapping: 'ip'},
            {name: 'divisi', mapping: 'divisi'},
            {name: 'waktu', mapping: 'waktu'}
         ]
    });

     Ext.define('mdl_combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value'}
         ]
    });

    var dummymaterial =[
        {"text":"material500","value":"material500"}
    ];
    var storematerial = Ext.create('Ext.data.Store', {
        id: 'store',
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
	
	var storeprovinsi = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getprovinsi',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

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

    
    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        pageSize: 50,     
        proxy: {
			 type: 'ajax',
			 url: '<?php echo base_url() ?>rbk/getdata/daftar_item',
			 reader: {
				 type: 'json',
				 root: 'data'
			 }
		 },
        remoteFilter: true,
        autoLoad: false
    });
store.load();
Ext.onReady(ready);

function ready() {
function prosesSearch(){
		store.load({
			//method:'POST',
			params:{sumberdaya:Ext.getCmp('cmbsumberdaya').getValue(),txtitem:Ext.getCmp('txtitem').getValue()}
		});
		return;
	}
var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        title: 'DAFTAR ITEM',
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'no'},
            {text: "KODE", width:100, sortable: true, dataIndex: 'kode'},
            {text: "NAMA", width:200, sortable: true, dataIndex: 'nama'},
            {text: "SPESIFIKASI", width:100, sortable: true, dataIndex: 'spesifikasi'},
            {text: "SUMBER DAYA", width:200, sortable: true, dataIndex: 'sumber_daya'},
            {text: "SATUAN", width:100, sortable: true, dataIndex: 'satuan'},
            {text: "HARGA", width:100, sortable: true, dataIndex: 'harga'},
            {text: "PROVINSI", width:100, sortable: true, dataIndex: 'provinsi'},
            {text: "USER", width:100, sortable: true, dataIndex: 'user'},
            {text: "TANGGAL", width:100, sortable: true, dataIndex: 'tanggal'},
            {text: "IP", width:100, sortable: true, dataIndex: 'ip'},
            {text: "DIVISI", width:100, sortable: true, dataIndex: 'divisi'},
            {text: "WAKTU", width:100, sortable: true, dataIndex: 'waktu'},
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/accept.gif',
                handler:function(grid, rowIndex, colIndex){
                    rec = store.getAt(rowIndex);
                    id = rec.get('id');
                    kode = rec.get('kode');
                    nama = rec.get('nama');
                    simpro_tbl_subbidang = rec.get('sumber_daya');
                    spesifikasi = rec.get('spesifikasi');
                    satuan = rec.get('satuan');
                    harga = rec.get('harga');
                    provinsi = rec.get('provinsi');
                    edit(id,subbidang,kode,nama,spesifikasi,satuan,harga,provinsi);
                }
            },
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/delete.gif',
			handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('id');
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>rbk/deletedata/daftar_item/0/0',
									method: 'POST',
									params: {
										'id' :  id
									},								
									success: function() {
									store.load();
									Ext.Msg.alert( "Status", "Delete successfully..!", function(){	
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
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
            'NAMA ITEM :',
            {
                xtype: 'textfield',
                name: 'textsearch',
				id:'txtitem'
            },
            {
            xtype: 'combobox',
            fieldLabel: 'SUMBER DAYA',
			id:'cmbsumberdaya',
            name: 'satuan',
            store: storematerial,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..'
            },'-',{
                text: 'Go>>',
				handler : function(){
					prosesSearch();
				}
				
            }
            ]},{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text:'Tambah',
                tooltip:'Tambah',
                handler: function(){
                    tambah();
                }
            },'-',{
                text:'Print',
                tooltip:'Print',
                handler: function(){

                }
            }]
        }],
        columnLines: true,
        width: '100%',
        height: '100%',
        renderTo: Ext.getBody(),
        bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: store,
                             displayInfo: true
                     })
        ]
    });
}
function tambah(){
    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>rbk/insertdata/daftar_item',
        id:'frmadd ',
        bodyStyle: 'padding:5px 5px 0',
        autoScroll: true,
        frame: false,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 200
        },
            items: [{
            xtype: 'combobox',
            fieldLabel: 'Subbidang',
            name: 'subbidang',
            store: storematerial,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            afterLabelTextTpl: required,
                allowBlank: false
        },{
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'kode',
                afterLabelTextTpl: required,
                allowBlank: false
            },{
                xtype:'textfield',
                fieldLabel: 'Nama',
                anchor: '-5',
                name: 'nama',
                afterLabelTextTpl: required,
                allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'Spesifikasi',
                anchor: '-5',
                name: 'spesifikasi',
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
            emptyText: 'Pilih..',
                afterLabelTextTpl: required,
                allowBlank: false
        },{
                xtype:'textfield',
                fieldLabel: 'Harga',
                anchor: '-5',
                name: 'harga',
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
        title: 'FORM INPUT DETAIL SUMBER DAYA',
        closeAction: 'hide',
        width: 500,
        height: 280,
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

function edit(id,subbidang,kode,nama,spesifikasi,satuan,harga,provinsi){
    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>rbk/editdata/daftar_item',
        id:'frmadd ',
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
            xtype: 'combobox',
            fieldLabel: 'Subbidang',
            name: 'subbidang',
            store: storematerial,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
            afterLabelTextTpl: required,
                allowBlank: false,
                value: simpro_tbl_subbidang
        },{
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'kode',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: kode
            },{
                xtype:'textfield',
                fieldLabel: 'Nama',
                anchor: '-5',
                name: 'nama',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: nama
            },{
                xtype:'textarea',
                fieldLabel: 'Spesifikasi',
                anchor: '-5',
                name: 'spesifikasi',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: spesifikasi
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
                afterLabelTextTpl: required,
                allowBlank: false,
                value: satuan
        },{
                xtype:'textfield',
                fieldLabel: 'Harga',
                anchor: '-5',
                name: 'harga',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: harga
        },{
            xtype: 'combobox',
            fieldLabel: 'Provinsi',
            name: 'provinsi',
            store: storeprovinsi,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            anchor: '-5',
            emptyText: 'Pilih..',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: satuan
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
        title: 'FORM INPUT DETAIL SUMBER DAYA',
        closeAction: 'hide',
        width: 500,
        height: 280,
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