<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<style type="text/css">
p {
    margin:5px;
}

.footer {
font-size: 10px;
font-family: 'Arial'
}

.icon-new {
    background: url(<?php echo base_url(); ?>assets/images/new-icon.png) no-repeat 0 -1px;
}
.new-tab {
    background-image:url(<?php echo base_url(); ?>assets/images/new_tab.gif) !important;
}

.icon-add {
    background-image:url(<?php echo base_url(); ?>assets/images/add.gif) !important;
}

.icon-del {
    background-image:url(<?php echo base_url(); ?>assets/images/delete.png) !important;
}
.icon-copy {
    background-image:url(<?php echo base_url(); ?>assets/images/copy.png ) !important;
}
.icon-paste {
    background-image:url(<?php echo base_url(); ?>assets/images/paste.png ) !important;
}

.tabs {
    background-image:url(<?php echo base_url(); ?>assets/images/tabs.gif ) !important;
}

.icon-back {
    background-image:url(<?php echo base_url(); ?>assets/images/back.png) !important;
}

.icon-table {
    background-image:url(<?php echo base_url(); ?>assets/images/table.png) !important;
}

.icon-print {
    background-image:url(<?php echo base_url(); ?>assets/images/print.png) !important;
}

.icon-reload {
    background-image:url(<?php echo base_url(); ?>assets/images/reload.png) !important;
}

.task .x-grid-cell-inner {
    padding-left: 15px;
}
.x-grid-row-summary .x-grid-cell-inner {
    font-weight: bold;
    font-size: 11px;
}

.icon-grid {
    background: url(<?php echo base_url(); ?>assets/images/grid.png) no-repeat 0 -1px;
}

.msg .x-box-mc {
    font-size:14px;
}

#msg-div {
    position:absolute;
    left:35%;
    top:10px;
    width:300px;
    z-index:999999;
}

#msg-div .msg {
    border-radius: 8px;
    -moz-border-radius: 8px;
    background: #F6F6F6;
    border: 2px solid #ccc;
    margin-top: 2px;
    padding: 10px 15px;
    color: #555;
}

#msg-div .msg h3 {
    margin: 0 0 8px;
    font-weight: bold;
    font-size: 15px;
}

#msg-div .msg p {
    margin: 0;
}   
</style>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript">

    Ext.require([
        '*'
    ]);

	Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'subbidang_nama', mapping: 'subbidang_nama'},
            {name: 'kode_meterial', mapping: 'kode_meterial'},
            {name: 'jenis_material', mapping: 'jenis_meterial'},
            {name: 'satuan', mapping: 'satuan'},            
            {name: 'volume', mapping: 'volume'},
            {name: 'harga_satuan', mapping: 'harga_satuan'},
            {name: 'jumlah_harga', mapping: 'jumlah_harga'}
        ]
    });

    Ext.define('mdl_combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value'}
         ]
    });

    Ext.define('mdl_sumber_daya', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'kode', mapping: 'kode'},
            {name: 'nama', mapping: 'nama'},            
            {name: 'spesifikasi', mapping: 'spesifikasi'}
         ]
    });

    var storeskbdn = Ext.create('Ext.data.Store', {
        model: 'mdl',
        pageSize: 100,
        remoteFilter: true,
        autoLoad: false,      
        groupField: 'subbidang_nama',  
        proxy: {
            type: 'ajax',            
            extraParams: {
                sort: ''
            },
            url: '<?php echo base_url() ?>rbk/get_data_skbdn',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var storesumberdaya = Ext.create('Ext.data.Store', {
        remoteSort: true,
        // pageSize: 50,
        autoLoad: false,
        model: 'mdl_sumber_daya',
        proxy: {
            type: 'ajax',
            extraParams: {
                sort : '',
                cbosort : '500'
            },
            url: '<?php echo base_url() ?>rbk/get_sumberdaya',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var storesubbidang = Ext.create('Ext.data.Store', {
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/get_subbidang',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

    Ext.onReady(function() {

        storeskbdn.load();

        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToEdit: 2,        
            listeners: {
                afteredit: function(rec,obj) {
                    var selectedNode = grid.getSelectionModel().getSelection();
                    data = selectedNode[0].data;

                    id = data.id;
                    volume = data.volume;
                    harga_satuan = data.harga_satuan;

                    Ext.Ajax.request({
                         url: '<?=base_url();?>rbk/edit/skbdn',
                            method: 'POST',
                            params: {
                                'id' :  id,
                                'volume': volume,
                                'harga_satuan': harga_satuan
                                },                              
                        success: function() {
                        storeskbdn.load();
                        Ext.Msg.alert( "Status", "Update successfully..!");                                    
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
            id: 'subbidang_nama'
        }),
        groups = storeskbdn.getGroups();

        var grid = Ext.create('Ext.grid.Panel', {
            title: 'Rencana Penggunaan SKBDN(FM-SKBDN-T01)',
            store: storeskbdn,
            features: [groupingFeature], 
            plugins: [rowEditing],
            columns: [
                {text: "KODE MATERIAL", flex:1, sortable: true, dataIndex: 'kode_meterial'},
                {text: "JENIS MATERIAL", flex:1, sortable: true, dataIndex: 'jenis_material'},
                {text: "SATUAN", flex:1, sortable: true, dataIndex: 'satuan'},
                {text: "VOLUME", flex:1, sortable: true, dataIndex: 'volume',
                    editor:{
                        xtype:'numberfield'
                    }
                },
                {text: "HARGA SATUAN", flex:1, sortable: true, dataIndex: 'harga_satuan',
                    renderer: Ext.util.Format.numberRenderer('Rp 00,000'),
                    editor:{
                        xtype:'numberfield'
                    }
                },
                {text: "JUMLAH HARGA", flex:1, sortable: true, dataIndex: 'jumlah_harga',
                    renderer: Ext.util.Format.numberRenderer('Rp 00,000')},
                {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
                    handler: function(grid, rowIndex, colIndex){
                    	var rec = storeskbdn.getAt(rowIndex);
                    	var id = rec.get('id');
        				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
        					if(resbtn == 'yes')
        					{
        						Ext.Ajax.request({
        							url: '<?=base_url();?>rbk/delete/skbdn',
        							method: 'POST',
        							params: {
        								'id' :  id
        							},								
        							success: function() {
                                    storeskbdn.load();
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
            columnLines: true,
            width: '100%',
            height: '100%',
           	bbar: Ext.create('Ext.PagingToolbar', {
    				store: storeskbdn,
                    pageSize: 100,
    				displayInfo: true,
    				displayMsg: 'Displaying data {0} - {1} of {2}',
    				emptyMsg: "No data to display",
    			}),
           	dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                itemId:'toolbar1',
                items: [
                // {
                //     xtype: 'datefield',
                //     fieldLabel: 'Tanggal Awal ',
                //     labelWidth: 75,
                //     itemId:'tanggal_awal',
                //     editable: false,
                //     format: 'M, d/Y',
                //     value: new Date(),
                //     listeners:{
                //         change:function(val){
                //             value = val.getValue();
                //             storeskbdn.proxy.setExtraParam('tgl_awal', value);
                //             storeskbdn.load();
                //         }
                //     }
                // },'-','s/d','-',{
                //     xtype: 'datefield',
                //     fieldLabel: 'Tanggal Akhir ',
                //     labelWidth: 78,
                //     itemId:'tanggal_akhir',
                //     editable: false,
                //     format: 'M, d/Y',
                //     value: new Date(),
                //     listeners:{
                //         change:function(val){
                //             value = val.getValue();
                //             storeskbdn.proxy.setExtraParam('tgl_akhir', value);
                //             storeskbdn.load();
                //         }
                //     }
                // },'-',
                {
                    fieldLabel: 'Search Jenis Material ',
                    labelWidth: 140,
                    xtype: 'textfield',
                    name:'sort',
                    itemId:'sort',
                    listeners:{
                        change: function(val){
                            value = val.getValue();
                            storeskbdn.proxy.setExtraParam('sort', value);
                            storeskbdn.load();
                            // console.log(sort);
                        }
                    }
                },'-',{
                    text:'Export SKBDN',
                    iconCls:'icon-print',
                    handler:function(){
                        Ext.MessageBox.confirm('Export', 'Apakah anda akan meng-Export item ini?',function(resbtn){
                            if(resbtn == 'yes')
                            {
                                window.location='<?=base_url()?>rbk/print_data/skbdn';                                                                             
                            }
                        });
                    }
                },'-',
                {
                    text:'Open In New Tab',
                    iconCls:'icon-new',
                    handler:function(){
                        window.open(document.URL,'_blank');
                    }
                }]
            },{
                xtype: 'toolbar',
                dock: 'bottom',
                items: [{
                    text:'Tambah Data',
                    handler: function(){
                        tambahanalisa();
                    }
                }]
            }]
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
                items: grid
            }]
        });
    });

function tambahanalisa(){

    var grid = Ext.create('Ext.grid.Panel', {
        store: storesumberdaya,
        autoscroll: true,
        frame: false,
        selModel: Ext.create('Ext.selection.CheckboxModel',{
            checkOnly: true
        }),
        columns: [
            {text: "KODE MATERIAL", flex:1, sortable: true, dataIndex: 'kode'},
            {text: "NAMA MATERIAL", flex:1, sortable: true, dataIndex: 'nama'},
            {text: "SPESIFIKASI", flex:1, sortable: true, dataIndex: 'spesifikasi'}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            itemId:'toolbar2',
            items: [{
                fieldLabel: 'Subbidang ',
                labelWidth: 100,
                width: 300,
                xtype: 'combobox',
                name: 'cbosort',
                itemId:'cbosort',
                store: storesubbidang,
                valueField: 'value',
                displayField: 'text',
                queryMode: 'local',
                emptyText: 'Pilih..',
                editable:false,
                listeners:{
                    change: function(){
                        data = grid.getComponent('toolbar2');
                        data.getComponent('sort').setValue('');
                        sort = data.getComponent('sort').getValue();
                        cbosort = data.getComponent('cbosort').getValue();
                        storesumberdaya.proxy.setExtraParam('sort', sort);
                        storesumberdaya.proxy.setExtraParam('cbosort', cbosort);
                        storesumberdaya.load();
                        // console.log(sort);
                    }
                }
            },'-',{
                fieldLabel: 'Search',
                labelWidth: 40,
                xtype: 'textfield',
                name:'sort',
                itemId:'sort',
                listeners:{
                    change: function(){
                        data = grid.getComponent('toolbar2');
                        sort = data.getComponent('sort').getValue();
                        cbosort = data.getComponent('cbosort').getValue();
                        storesumberdaya.proxy.setExtraParam('sort', sort);
                        storesumberdaya.proxy.setExtraParam('cbosort', cbosort);
                        storesumberdaya.load();
                        // console.log(sort);
                    }
                }
            }
            // ,{
            //     text:'Go>>',
            //     handler: function(){

            //     }
            // }
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

                    for (i = 0; i < selectedNode.length; i++) {
                        // console.log(selectedNode[i].data.id);
                        Ext.Ajax.request({
                            url: '<?=base_url();?>rbk/insert/skbdn',
                            method: 'POST',
                            params: {
                                'id':selectedNode[i].data.id
                            },                              
                        success: function() {                         
                        },
                        failure: function() {
                            Ext.Msg.alert( "Status", "No Respond..!"); 
                        }
                        });                                     
                    }
                    Ext.Msg.alert( "Status", "Insert successfully..!");
                    storeskbdn.load();
                }
            },{
                text:'Cancel',
                tooltip:'Cancel',
                handler: function(){
                    winadd.hide();
                }
            }]
        }],
        listeners:{
            beforerender: function(){                
                data = grid.getComponent('toolbar2');
                data.getComponent('sort').setValue('');
                data.getComponent('cbosort').setValue('500');
            }
        }
        // ,
        // bbar: [Ext.create('Ext.toolbar.Paging', {
        //     pageSize: 50,
        //     store: storesumberdaya,
        //     displayInfo: true,
        //     displayMsg: 'Displaying records {0} - {1} of {2}',
        //     emptyMsg: "No topics to display"
        // })]
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
</script>
