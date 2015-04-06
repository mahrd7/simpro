<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript">

Ext.require([
    '*'
]);

	Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping:'rpbk_id'},
        	{name: 'kode_rap', mapping: 'kode_rap'},
            {name: 'subbidang_name', mapping: 'subbidang_name'},
            {name: 'detail_material_id', mapping: 'detail_material_id'},
            {name: 'detail_material_kode', mapping: 'detail_material_kode'},
            {name: 'detail_material_nama', mapping: 'detail_material_nama'}, 
            {name: 'detail_material_satuan', mapping: 'detail_material_satuan'},           
            {name: 'keterangan', mapping: 'keterangan'},
            {name: 'jumlah_volume', mapping: 'jumlah_volume', type:'float'},
            {name: 'rata_harga_satuan', mapping: 'rata_harga_satuan', type:'float'},
            {name: 'jumlah_harga', mapping: 'jumlah_harga', type:'float'},
            {name: 'total_volume_rpbk_lalu', mapping: 'total_volume_rpbk_lalu', type:'float'},
            {name: 'rpbk_rrk1', mapping: 'rpbk_rrk1', type:'float'}
         ]
    });

    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        groupField: 'subbidang_name',        
        proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/get_data_rpbk',
             reader: {
                 type: 'json',
                 root: 'data'
             }
        }
    });

Ext.onReady(function() {
    store.load({
        params:{
            'tgl_rab':'<?php echo $tgl_rab ?>'
        }
    });

    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToEdit: 2,        
        listeners: {
            afteredit: function(rec,obj) {
                var selectedNode = grid.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                id = data.id;
                harga = data.rpbk_rrk1;
                detail_material = data.detail_material_id;
                detail_material_kode = data.detail_material_kode;
                kode_rap = data.kode_rap;

                Ext.Ajax.request({
                     url: '<?=base_url();?>pengendalian/update_rpbk',
                        method: 'POST',
                        params: {
                            'id' :  id,
                            'harga': harga,
                            'tgl_rab':'<?php echo $tgl_rab ?>',
                            'detail_material' : detail_material,
                            'detail_material_kode' : detail_material_kode,
                            'kode_rap' : kode_rap
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    store.load({
                        params:{
                            'tgl_rab':'<?php echo $tgl_rab ?>',
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
        groups = store.getGroups();

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        autoscroll: true,
        features: [groupingFeature],         
        plugins: [rowEditing],
        title: 'EDIT RENCANA POSISI BIAYA KONSTRUKSI bulan <?php echo $bln1 ?> tahun <?php echo $thn ?>',
        columns: [
            {text: "KODE RAP", width:80, sortable: true, dataIndex: 'kode_rap', locked:true},
            {text: "KODE", width:120, sortable: true, dataIndex: 'detail_material_kode', locked:true},
            {text: "URAIAN PEKERJAAN", width:180, sortable: true, dataIndex: 'detail_material_nama', locked:true},
            {text: "SAT", width:40, sortable: true, dataIndex: 'detail_material_satuan', locked:true},
            {text: "KETERANGAN", width:120, sortable: true, dataIndex: 'keterangan', locked:true},
            {text: "RAPA KINI",
            columns: [            
            {text: "VOLUME", width:70, sortable: true, dataIndex: 'jumlah_volume'},
            {text: "H.SATUAN", width:80, sortable: true, dataIndex: 'rata_harga_satuan'},
            {text: "JUMLAH", width:60, sortable: true, dataIndex: 'jumlah_harga'},
            ]
        },

            {text: "TOTAL BK S/D BLN <?php echo $bln_1 ?>", width:160, sortable: true, dataIndex: 'total_volume_rpbk_lalu'},
            {text: "BK BLN <?php echo $bln1 ?>(Harga)", width:150, sortable: true, dataIndex: 'rpbk_rrk1',
            editor:
            {
                xtype: 'textfield'
            }
        }
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Reload',
                tooltip:'Reload',
                handler: function(){
                
                }
            },'-',{
                text:'Print',
                tooltip:'Print',
                handler: function(){
                
                }
            }]
        },{
            xtype: 'toolbar',
            dock: 'bottom',
            items:[
                'Total : '
            ]
        }],
        height: '100%'
        // ,
        // renderTo: Ext.getBody()
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
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>