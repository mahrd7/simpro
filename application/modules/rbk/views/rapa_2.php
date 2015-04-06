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

	Ext.define('mdl_ctg', {
        extend: 'Ext.data.Model',
        fields: [
        	{name: 'tahap_kendali_id', mapping: 'tahap_kendali_id'},
            {name: 'tahap_kode_kendali', mapping: 'tahap_kode_kendali'},
            {name: 'tahap_nama_kendali', mapping: 'tahap_nama_kendali'},            
            {name: 'tahap_satuan_kendali', mapping: 'tahap_satuan_kendali'},
            {name: 'tahap_volume_kendali', mapping: 'tahap_volume_kendali'},
            {name: 'tahap_harga_satuan_kendali', mapping: 'tahap_harga_satuan_kendali'},
            {name: 'tahap_total_kendali', mapping: 'tahap_total_kendali'}
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
            {name: 'kode', mapping: 'kode'},
            {name: 'nama', mapping: 'nama'},            
            {name: 'spesifikasi', mapping: 'spesifikasi'},
            {name: 'provinsi', mapping: 'provinsi'},
            {name: 'koefisien', mapping: 'koefisien'},
            {name: 'harga', mapping: 'harga'}
         ]
    });

    Ext.define('mdl_sub_ctg_new', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'sub_nama', mapping: 'sub_nama'},
            {name: 'nama', mapping: 'nama'},
            {name: 'satuan', mapping: 'satuan'},
            {name: 'harga', mapping: 'harga', type:'float'},            
            {name: 'koefisien', mapping: 'koefisien', type:'float'},
            {name: 'total', mapping: 'total', type:'float'},
            {name: 'kode_rap', mapping: 'kode_rap'}
         ]
    });

    Ext.define('mdl_getedithargasatuan', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'kode', mapping: 'kode'},
            {name: 'nama', mapping: 'nama'},
            {name: 'keterangan', mapping: 'keterangan'},
            {name: 'harga', mapping: 'harga', type: 'float'},
            {name: 'kode_rap', mapping: 'kode_rap'},
            {name: 'sub_nama', mapping: 'sub_nama'},
            {name: 'kode_rap', mapping: 'kode_rap'}
         ]
    });

    Ext.define('mdl_combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value'}
         ]
    });

    Ext.define('mdl_combo_satuan', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value', type: 'int'}
         ]
    });

    dummyctg = [
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

    Ext.define('mdl_get_data', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'value', mapping: 'value'}
         ]
    });

    Ext.define('mdl_sub_ctg', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'tahap_kode_kendali', mapping: 'tahap_kode_kendali'},
            {name: 'tahap_nama_kendali', mapping: 'tahap_nama_kendali'},
            {name: 'tahap_satuan_kendali', mapping: 'tahap_satuan_kendali'},            
            {name: 'tahap_volume_kendali', mapping: 'tahap_volume_kendali'},
            {name: 'tahap_harga_satuan_kendali', mapping: 'tahap_harga_satuan_kendali'},
            {name: 'harga_sub', mapping: 'harga_sub'},
            {name: 'tahap_kendali_id', mapping: 'tahap_kendali_id'}
         ]
    });

    Ext.define('mdl_hs_pwd', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'datajumlah', mapping: 'datajumlah'},
            {name: 'data1', mapping: 'data1'},
            {name: 'data2', mapping: 'data2'}
         ]
    });

    var store_hs_pwd = Ext.create('Ext.data.Store', {
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        model: 'mdl_hs_pwd',
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/get_hs_pwd',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var store_check_data_induk_togo = Ext.create('Ext.data.Store', {
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        model: 'mdl_get_data',
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/cek_data_induk_togo',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var storesumberdaya = Ext.create('Ext.data.Store', {
        id: 'storeanalisa',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        model: 'mdl_sumber_daya',
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/get_data_analisa',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var store_sub_ctg = Ext.create('Ext.data.Store', {
        id: 'store_sub_ctg',
        model: 'mdl_sub_ctg',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/get_sub_ctg',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var store_sub_ctg_new = Ext.create('Ext.data.Store', {
        id: 'store_sub_ctg_new',
        model: 'mdl_sub_ctg_new',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        groupField: 'sub_nama',
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getdata_sub_ctg',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var store_get_sub_kode = Ext.create('Ext.data.Store', {
        id: 'store_get_sub_kode',
        model: 'mdl_get_data',
        remoteSort: true,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/get_sub_kode',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var store_get_stat_ehs = Ext.create('Ext.data.Store', {
        id: 'store_get_stat_ehs',
        model: 'mdl_get_data',
        remoteSort: true,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/cek_pwd_hs',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var store_get_kode = Ext.create('Ext.data.Store', {
        id: 'store_get_kode',
        model: 'mdl_get_data',
        remoteSort: true,
        autoLoad: false,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/get_kode_ctg',
         reader: {
             type: 'json',
             root: 'data'
         }
        }
    });

    var store_ctg_tree = Ext.create('Ext.data.TreeStore', {
        model: 'mdl_ctg',
        proxy: {
            type: 'ajax',
            //the store will get the content from the .json file
            url: '<?php echo base_url() ?>rbk/get_data_cost_to_go'
        },
        folderSort: false,
        remoteSort: true,
        autoLoad: false
    });

    store_ctg_tree.load({
        params:{
            'tgl_rab':'<?php echo $tgl_rab ?>'
        }
    });
    var storesatuan = Ext.create('Ext.data.Store', {
        id: 'storectg',
        model: 'mdl_combo_satuan',
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

    var storedivisi = Ext.create('Ext.data.Store', {
        id: 'storedivisi',
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getdivisicombo',
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
         url: '<?php echo base_url() ?>rbk/getproyekcombo',
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
         url: '<?php echo base_url() ?>rbk/gettanggalcombo',
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
        groupField: 'sub_nama',
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getdata_edit_hs_ctg',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

    var storesubbidang = Ext.create('Ext.data.Store', {
        id: 'storectg',
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



Ext.onReady(function() {

    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            afteredit: function(rec,obj) {
                var selectedNode = grid.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                id = data.tahap_kendali_id;
                tahap_nama_kendali = data.tahap_nama_kendali;
                tahap_satuan_kendali = data.tahap_satuan_kendali;
                tahap_volume_kendali = data.tahap_volume_kendali;
                tahap_harga_satuan_kendali = data.tahap_harga_satuan_kendali;

                Ext.Ajax.request({
                     url: '<?=base_url();?>rbk/update_ctg',
                        method: 'POST',
                        params: {
                            'id' :  id,
                            'tahap_nama_kendali': tahap_nama_kendali,
                            'tahap_satuan_kendali': tahap_satuan_kendali,
                            'tahap_volume_kendali': tahap_volume_kendali,
                            'tahap_harga_satuan_kendali': tahap_harga_satuan_kendali
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    store_ctg_tree.load({
                        params:{
                            'tgl_rab':'<?php echo $tgl_rab ?>'
                        }
                    });                                        
                    },
                    failure: function() {
                    Ext.Msg.alert( "Status", "No Respond..!"); 
                    }
                });

                // console.log(id+tahap_nama_kendali+tahap_satuan_kendali+tahap_volume_kendali+tahap_harga_satuan_kendali);
            }   
        }
    });

    var storedummy = Ext.create('Ext.data.ArrayStore', {
        model: 'dummydatast',
        data: dummydata
    });

	var storectg = Ext.create('Ext.data.Store', {
        id: 'storectg',
        model: 'mdl_ctg',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummyctg
    });

    var grid = Ext.create('Ext.tree.Panel', {
        id:'tree-panel',
        store: store_ctg_tree,
        useArrows: true,
        rootVisible: false,
        multiSelect: true,
        singleExpand: false,
        plugins: [rowEditing],
        title: 'RAPA <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/cog.gif',
            handler: function(rec, rowIndex, colIndex){
                var selectedNode = rec.store.data.items[rowIndex].data;
                data = selectedNode

                id = data.tahap_kendali_id;
                no = data.tahap_kode_kendali;

                store_sub_ctg.load({
                params: {
                    'kode': no,
                    'tgl_rab': '<?php echo $tgl_rab ?>',
                }
                });
                link(id,no,'<?php echo $tgl_rab; ?>');
            }},
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/delete.png',
            handler: function(rec, rowIndex, colIndex){
                var selectedNode = rec.store.data.items[rowIndex].data;
                data = selectedNode

                id = data.tahap_kendali_id;
                no = data.tahap_kode_kendali;

                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                            if(resbtn == 'yes')
                            {
                                                                                                                        
                            }
                });
            }},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/add.png',
            handler: function(rec, rowIndex, colIndex){
                var selectedNode = rec.store.data.items[rowIndex].data;
                data = selectedNode

                id = data.tahap_kendali_id;
                no = data.tahap_kode_kendali;
                store_sub_ctg_new.load({
                    params:{
                        'tgl_rab':'<?php echo $tgl_rab ?>',
                        'kode_kendali':no
                    }
                });
                analisa(id,no)
            }},
            {xtype: 'treecolumn', text: "KODE", width:100, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "ITEM PEKERJAAN", width:400, sortable: true, dataIndex: 'tahap_nama_kendali',
                editor:{
                    xtype:'textfield'
                }
            },
            {text: "SATUAN", width:60, sortable: true, dataIndex: 'tahap_satuan_kendali',
                editor:{
                    xtype: 'combobox',
                    store: storesatuan,
                    valueField: 'value',
                    displayField: 'text',
                    typeAhead: true,
                    queryMode: 'local',
                }
            },
            {text: "VOLUME SISA ANGGARAN", width:150, sortable: true, dataIndex: 'tahap_volume_kendali',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "HARGA SATUAN", width:120, sortable: true, dataIndex: 'tahap_harga_satuan_kendali',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "TOTAL HARGA", width:120, sortable: true, dataIndex: 'tahap_total_kendali'}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Edit Harga Satuan',
                tooltip:'Edit Harga Satuan',
                handler: function(){
                    confirmedithargasatuan();
                }
            },'-',{
                text:'Password Harga Satuan',
                tooltip:'Password Harga Satuan',
                handler: function(){
                    passwordhargasatuan();
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
                    var url ='<?php echo base_url(); ?>rbk/pilih_rapa';
                    // console.log(url);
                    window.location=url;
                }
            }]
        },
        {
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
            	text: 'Tambah Data',
            	handler: function(){
                    store_get_kode.load({
                    params: {
                        'tgl_rab': '<?php echo $tgl_rab; ?>'
                    },
                    callback: function(records, options, success){
                        kode = records[0].data.value;
                        // console.log(kode);            
                        tambahctg(kode);    
                    }
                    });
            	}
            }]
        },
        {
            dock: 'bottom',
            xtype: 'toolbar',
            items: [                
            'Total: '
            ]
        }],
        width:'100%',
        height:'100%',
        renderTo: Ext.getBody(),
       	bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: storectg,
                             displayInfo: true
                     })
        ]
    });
});

function tambahctg(kode){
    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>rbk/insert_ctg/<?php echo $tgl_rab ?>',
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
                allowBlank: false,
                value: kode
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
                            store_ctg_tree.load({
                                params:{
                                    'tgl_rab':'<?php echo $tgl_rab ?>'
                                }
                            });
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
        title: 'FORM TAMBAH RAP rbk',
        closeAction: 'hide',
        width: 500,
        height: 220,
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

function link(id,kode,tgl_rab){

        var subRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            afteredit: function(rec,obj) {
                var selectedNode = grid.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                id = data.tahap_kendali_id;
                tahap_nama_kendali = data.tahap_nama_kendali;
                tahap_satuan_kendali = data.tahap_satuan_kendali;
                tahap_volume_kendali = data.tahap_volume_kendali;
                tahap_harga_satuan_kendali = data.tahap_harga_satuan_kendali;

                Ext.Ajax.request({
                     url: '<?=base_url();?>rbk/update_ctg',
                        method: 'POST',
                        params: {
                            'id' :  id,
                            'tahap_nama_kendali': tahap_nama_kendali,
                            'tahap_satuan_kendali': tahap_satuan_kendali,
                            'tahap_volume_kendali': tahap_volume_kendali,
                            'tahap_harga_satuan_kendali': tahap_harga_satuan_kendali
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    store_ctg_tree.load({
                        params:{
                            'tgl_rab':'<?php echo $tgl_rab ?>'
                        }
                    });   
                    store_sub_ctg.load({
                        params: {
                        'kode': kode,
                        'tgl_rab': tgl_rab,
                    }
                    });                                    
                    },
                    failure: function() {
                    Ext.Msg.alert( "Status", "No Respond..!"); 
                    }
                });

                // console.log(id+tahap_nama_kendali+tahap_satuan_kendali+tahap_volume_kendali+tahap_harga_satuan_kendali);
            }   
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store_sub_ctg,
        autoscroll: true,
        frame: false,
        plugins: [subRowEditing],
        // title: 'Current Budget <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "ITEM PEKERJAAN", width:240, sortable: true, dataIndex: 'tahap_nama_kendali',
                editor:{
                    xtype: 'textfield'
                }
            },
            {text: "SATUAN", width:55, sortable: true, dataIndex: 'tahap_satuan_kendali',
                editor:{
                    xtype: 'combobox',
                    store: storesatuan,
                    valueField: 'value',
                    displayField: 'text',
                    typeAhead: true,
                    queryMode: 'local',
                }
            },
            {text: "VOLUME KONTRAK", width:150, sortable: true, dataIndex: 'tahap_volume_kendali',
                editor:{
                    xtype: 'numberfield'
                }
            },
            {text: "VOLUME TAMBAH/KURANG", width:150, sortable: true, dataIndex: 'tahap_volume_kendali_new'},
            {text: "HARGA", width:100, sortable: true, dataIndex: 'harga_sub'},{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/delete.png',
            handler: function(grid, rowIndex, colIndex){

            }}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Tambah Data',
                tooltip:'Tambah Data',
                handler: function(){                    
                    store_get_sub_kode.load({
                    params: {
                        'kode': kode,
                        'tgl_rab': tgl_rab,
                        'id': id,
                        'info' : 'rapa'
                    },
                    callback: function(records, options, success){
                        kode_induk = records[0].data.value;
                        tambahlink(kode,kode_induk,tgl_rab);
                        // console.log(kode_induk);
                        
                    }
                    });
                }
            },'-',{
                text:'Tambah Komposisi',
                tooltip:'Tambah Komposisi',
                handler: function(){

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
        }]
        // ,
        // bbar: [Ext.create('Ext.toolbar.Paging', {
        //                      pageSize: 50,
        //                      store: store,
        //                      displayInfo: true
        //              })
        // ]
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'FORM SUB RAPA',
        closeAction: 'hide',
        width: 690,
        height: 440,
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

function tambahlink(kode,kode_induk,tgl_rab){
    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>rbk/insert_sub_ctg/'+kode+'/'+tgl_rab,
        width:'100%',
        height:'100%',
        bodyStyle: 'padding:10px',
        autoScroll: true,
        frame: false,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 160
        },
            items: [{
                xtype:'textfield',
                fieldLabel: 'Kode',
                anchor: '-5',
                name: 'kode',
                afterLabelTextTpl: required,
                allowBlank: false,
                value: kode+'.'+kode_induk
            },{
                xtype:'textarea',
                fieldLabel: 'Tahap Pekerjaan',
                anchor: '-5',
                name: 'tahap_pekerjaan',
                afterLabelTextTpl: required,
                allowBlank: false
        },{
            xtype: 'combobox',
            fieldLabel: 'Satuan',
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
                allowBlank: false,
                emptyText: '(*hanya untuk vol kontrak awal,bukan addendum)'
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
                                store_ctg_tree.load({
                                    params: {
                                        'tgl_rab':'<?php echo $tgl_rab; ?>'
                                    }
                                }        
                                );  
                            store_sub_ctg.load({
                                params: {
                                'kode': kode,
                                'tgl_rab': tgl_rab,
                            }
                            });
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
        title: 'FORM TAMBAH SUB RAPA',
        closeAction: 'hide',
        width: 500,
        height: 250,
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

function tambahkomposisi(){
    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>',
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
                            storectg.load();
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
        title: 'FORM TAMBAH SUB RAP rbk',
        closeAction: 'hide',
        width: 500,
        height: 170,
        layout: 'fit',
        modal: true,
        items: frmadd 
    }).show();
}

function analisa(id,no){


    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            afteredit: function(rec,obj) {
                var selectedNode = grid.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                id_komposisi = data.id;
                harga = data.harga;
                koefisien = data.koefisien;

                Ext.Ajax.request({
                     url: '<?=base_url();?>rbk/update_analisa_ctg',
                        method: 'POST',
                        params: {
                            'id_komposisi' :  id_komposisi,
                            'harga': harga,
                            'koefisien': koefisien
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    store_sub_ctg_new.load({
                        params:{
                            'tgl_rab':'<?php echo $tgl_rab ?>',
                            'kode_kendali':no
                        }
                    });                                        
                    },
                    failure: function() {
                    Ext.Msg.alert( "Status", "No Respond..!"); 
                    }
                });

                // console.log(id+tahap_nama_kendali+tahap_satuan_kendali+tahap_volume_kendali+tahap_harga_satuan_kendali);
            }   
        }
    });

      var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
            groupHeaderTpl: '{name} : ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
            hideGroupedHeader: true,
            startCollapsed: false,
            id: 'analisa'
        }),
        groups = store_sub_ctg_new.getGroups()
        ;

    var grid = Ext.create('Ext.grid.Panel', {
        store: store_sub_ctg_new,
        frame: false,
        loadMask: true,
        features: [groupingFeature],        
        plugins: [rowEditing],
        columns: [
            {text: "SUMBER DAYA", width:240, sortable: true, dataIndex: 'nama'},
            {text: "SATUAN", width:55, sortable: true, dataIndex: 'satuan'},
            {text: "HARGA SATUAN", width:100, sortable: true, dataIndex: 'harga',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "KOEFISIEN", width:100, sortable: true, dataIndex: 'koefisien',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "JUMLAH", width:100, sortable: true, dataIndex: 'total'},
            {text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/delete.png',
            handler: function(grid, rowIndex, colIndex){
                var rec = store_sub_ctg_new.getAt(rowIndex);
                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                            if(resbtn == 'yes')
                            {
                                                                                                                        
                            }
                });
            }}
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
                text:'Copy Ke',
                tooltip:'Copy Ke',
                handler: function(){
                    copy_analisa_ke_orgn_budget();
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
                    storesumberdaya.load();
                    tambahanalisa(no);
                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [                
            'Total: '
            ]
        }]
        // bbar: [Ext.create('Ext.toolbar.Paging', {
        //                      pageSize: 50,
        //                      store: storeanalisa,
        //                      displayInfo: true
        //              })
        // ]
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

function tambahanalisa(no){

    var grid = Ext.create('Ext.grid.Panel', {
        id: 'grids',
        store: storesumberdaya,
        autoscroll: true,
        frame: false,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        columns: [
            {text: "KODE MATERIAL", flex:1, sortable: true, dataIndex: 'kode'},
            {text: "NAMA MATERIAL", width:240, sortable: true, dataIndex: 'nama'},
            {text: "SPESIFIKASI", width:100, sortable: true, dataIndex: 'spesifikasi'},
            {text: "PROPINSI", width:120, sortable: true, dataIndex: 'provinsi'},
            {text: "KOEFISIEN", width:100, sortable: true, dataIndex: 'koefisien'},
            {text: "HARGA SATUAN", width:100, sortable: true, dataIndex: 'harga'}
        ],
        columnLines: true,
        dockedItems: [{
            itemId: 'docks',
            xtype: 'toolbar',
            dock: 'top',
            items: [
            'Pilih : ',{
                xtype: 'textfield',
                itemId:'pilihshort'
            },{
            xtype: 'combobox',
            itemId: 'cboshort',
            store: storesubbidang,
            valueField: 'value',
            displayField: 'text',
            queryMode: 'local',
            emptyText: 'Pilih..'
            },{
                text: 'Go>>',
                handler: function(e){

                    form = Ext.getCmp('grids');
                    dock = form.getComponent('docks');
                    text = dock.getComponent('pilihshort').value;
                    cbo = dock.getComponent('cboshort').value;

                    storesumberdaya.load({
                        params:{
                            'text':text,
                            'cbo':cbo
                        }
                    });
                }
            }
            ]
        },{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [{
                text:'Tambah',
                tooltip:'Tambah',
                handler: function(){

                    winadd.hide();
                    
                    selectedNode = grid.getSelectionModel().getSelection();

                    Ext.Ajax.request({
                        url: '<?=base_url();?>rbk/insert_induk_togo_induk',
                        method: 'POST',
                        params: {
                            'id' :  no,
                            'tgl_rab':'<?php echo $tgl_rab ?>'
                        },                              
                        success: function() {
                        Ext.Msg.alert( "Status", "Insert successfully..!");                                       
                        },
                        failure: function() {
                        Ext.Msg.alert( "Status", "No Respond..!"); 
                        } 
                    });

                    for (i = 0; i <= selectedNode.length; i++) {
                        Ext.Ajax.request({
                            url: '<?=base_url();?>rbk/insert_induk_komposisi_togo',
                            method: 'POST',
                            params: {
                                'id' :  no,
                                'id_detail_material': selectedNode[i].data.id,
                                'kode_detail_material': selectedNode[i].data.kode,
                                'tgl_rab':'<?php echo $tgl_rab ?>'
                            },                              
                        success: function() {
                            Ext.Msg.alert( "Status", "Insert successfully..!"); 
                            store_sub_ctg_new.load({
                                params:{
                                    'tgl_rab':'<?php echo $tgl_rab ?>',
                                    'kode_kendali':no
                                }
                            });                                     
                        },
                        failure: function() {
                            Ext.Msg.alert( "Status", "No Respond..!"); 
                        }
                        });                                     
                    }
                }
            },{
                text:'Cancel',
                tooltip:'Cancel',
                handler: function(){
                    winadd.hide();
                }
            }]
        }],
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

function copy_analisa_dari_orgn_budget(){
    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl_ctg',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummyctg
    });

    var grid = Ext.create('Ext.grid.Panel', {
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
        model: 'mdl_ctg',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummyctg
    });

    var grid = Ext.create('Ext.grid.Panel', {
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
        model: 'mdl_ctg',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummyctg
    });

    var grid = Ext.create('Ext.grid.Panel', {
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

function copy_analisa_recovery(){
    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl_ctg',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        data: dummyctg
    });

    var grid = Ext.create('Ext.grid.Panel', {
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

function edit_harga_satuan(){
    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            afteredit: function(rec,obj) {
                var selectedNode = grid.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                kode_material = data.kode;
                harga = data.harga;
                keterangan = data.keterangan;

                Ext.Ajax.request({
                     url: '<?=base_url();?>rbk/update_hs_ctg',
                        method: 'POST',
                        params: {
                            'kode' :  kode_material,
                            'tgl_rab':'<?php echo $tgl_rab ?>',
                            'harga': harga,
                            'keterangan': keterangan
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    store_ctg_tree.load({
                        params:{
                            'tgl_rab':'<?php echo $tgl_rab ?>'
                        }
                    });   
                    storegetedithargasatuan.load({
                        params: {
                            tgl_rab : '<?php echo $tgl_rab ?>'
                        }
                    });                                     
                    },
                    failure: function() {
                    Ext.Msg.alert( "Status", "No Respond..!"); 
                    }
                });

                // console.log(id+tahap_nama_kendali+tahap_satuan_kendali+tahap_volume_kendali+tahap_harga_satuan_kendali);
            }   
        }
    });

    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
        groupHeaderTpl: '{name} : ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
        hideGroupedHeader: true,
        startCollapsed: false,
        id: 'analisa'
    }),
    groups = store_sub_ctg_new.getGroups();

    var grid = Ext.create('Ext.grid.Panel', {
        store: storegetedithargasatuan,
        autoscroll: true,
        frame: false,       
        features: [groupingFeature], 
        plugins: [rowEditing],
        columns: [
            {text: "KODE MATERIAL", flex:1, sortable: true, dataIndex: 'kode'},
            {text: "NAMA MATERIAL", flex:1, sortable: true, dataIndex: 'nama'},
            {text: "HARGA", flex:1, sortable: true, dataIndex: 'harga',
                editor:{
                    xtype:'numberfield'
                }
            },{text: "Keterangan", flex:1, sortable: true, dataIndex: 'keterangan',
                editor:{
                    xtype:'textfield'
                }
            },
            {text: "KODE RAP", flex:1, sortable: true, dataIndex: 'kode_rap'}
            // {text: "",xtype: 'actioncolumn', width:25, icon:'<?php echo base_url() ?>assets/images/accept.gif',
            //     handler: function(grid, rowIndex, colIndex){
            //         rec = storegetedithargasatuan.getAt(rowIndex);
            //         kode_material = rec.get('kode');
            //         kode_rap = rec.get('kode_rap');
            //         Ext.Ajax.request({
            //          url: '<?=base_url();?>rbk/update_hs_ctg_kode_rap',
            //             method: 'POST',
            //             params: {
            //                 'kode' :  kode_material,
            //                 'tgl_rab':'<?php echo $tgl_rab ?>',
            //                 'kode_rap': kode_rap
            //                 },                              
            //         success: function() {
            //         Ext.Msg.alert( "Status", "Update successfully..!"); 
            //         store_ctg_tree.load({
            //             params:{
            //                 'tgl_rab':'<?php echo $tgl_rab ?>'
            //             }
            //         });   
            //         storegetedithargasatuan.load({
            //             params: {
            //                 tgl_rab : '<?php echo $tgl_rab ?>'
            //             }
            //         });                                     
            //         },
            //         failure: function() {
            //         Ext.Msg.alert( "Status", "No Respond..!"); 
            //         }
            //     });
            //     }
            // }
        ],
        columnLines: true,
        dockedItems: [{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text: 'Reload',
                tooltip:'Reload',
                handler: function(){
                    winadd.hide();
                }
            }]
        }],
        width: '100%',
        height: '100%'
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
        width:'100%',
        height:'100%',
        bodyStyle: 'padding:5px 5px 0',
        autoScroll: true,
        frame: false,
        // url:'<?php echo base_url() ?>rbk/cek_pwd_hs',
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
                    store_get_stat_ehs.load({
                        params:{
                            'datasdm' : username,
                            'datahs' : password
                        },
                        callback: function(records){
                        status = records[0].data.value;
                        if (status == 'true') {
                            storegetedithargasatuan.load({
                                params: {
                                    tgl_rab : '<?php echo $tgl_rab ?>'
                                }
                            });
                            edit_harga_satuan();             
                            winadd.hide();
                        } else {
                            Ext.MessageBox.alert('Information','Maaf username atau password salah..!');
                            form.reset();
                        }                     
                        }
                    });           
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

function passwordhargasatuan(){
    store_hs_pwd.load();
    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            afteredit: function(rec,obj) {
                var selectedNode = grid.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                datacash = data.datajumlah;
                data45 = data.data2;
                data08 = data.data1;

                // console.log(datacash+data45+data08);
                Ext.Ajax.request({
                     url: '<?=base_url();?>rbk/update_hs_ctg_pwd',
                        method: 'POST',
                        params: {
                            'data67' : datacash,
                            'data12' : data45,
                            'data90': data08
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    store_ctg_tree.load({
                        params:{
                            'tgl_rab':'<?php echo $tgl_rab ?>'
                        }
                    });   
                    storegetedithargasatuan.load({
                        params: {
                            tgl_rab : '<?php echo $tgl_rab ?>'
                        }
                    });    
                    store_hs_pwd.load();                                 
                    },
                    failure: function() {
                    Ext.Msg.alert( "Status", "No Respond..!"); 
                    }
                });

                // console.log(id+tahap_nama_kendali+tahap_satuan_kendali+tahap_volume_kendali+tahap_harga_satuan_kendali);
            }   
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store_hs_pwd,
        autoscroll: true,
        frame: false,       
        plugins: [rowEditing],
        columns: [
            {text: "USERNAME", flex:1, sortable: true, dataIndex: 'data2', 
                editor: {
                    xtype: 'textfield'
                }
            },
            {text: "PASSWORD", flex:1, sortable: true, dataIndex: 'data1',
                editor: {
                    xtype: 'textfield'
                }
            },
            {text:"", xtype:'actioncolumn', width:25, icon:'<?php echo base_url() ?>assets/images/delete.gif'}
        ],
        columnLines: true,
        dockedItems: [{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [{
                text: 'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    winadd.hide();
                }
            }]
        }],
        width: '100%',
        height: '100%'
    });

    var winadd = Ext.create('Ext.Window', {
        title: 'PASSWORD HARGA SATUAN',
        closeAction: 'hide',
        width: '530',
        height: '90%',
        layout: 'fit',
        modal: true,
        items: grid 
    }).show();
}

</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>