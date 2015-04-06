<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

<script type="text/javascript">

Ext.require([
    '*'
]);

	Ext.define('analisa_daftar_analisa', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'tgl_rab',
                mapping: 'tgl_rab'
            },
            {
                name: 'proyek',
                mapping: 'proyek'
            },
            {
                name: 'year',
                mapping: 'year'
            },
            {
                name: 'month',
                mapping: 'month'
            },
            {
                name: 'status',
                mapping: 'status'
            },
            {
                name: 'month_name',
                mapping: 'month_name'
            }
         ]
    });

    Ext.define('mdl_combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value'}
         ]
    });
    
    var storeproyek = Ext.create('Ext.data.Store', {
        model: 'mdl_combo',
        remoteSort: true,
        autoLoad: true,
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/get_proyek_daftar_analisa',
         reader: {
             type: 'json',
             root: 'data'
         }
        } 
    });

Ext.onReady(function() {

var store_daftar_analisa = Ext.create('Ext.data.Store', {
        id: 'store_daftar_analisa',
        model: 'analisa_daftar_analisa',
        pageSize: 50,     
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/get_tanggal_daftar_analisa',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        remoteFilter: true,
        autoLoad: false
    });
    store_daftar_analisa.load();

var grid_daftar_analisa = Ext.create('Ext.grid.Panel', {
        id:'grid_daftar_analisa',
        store: store_daftar_analisa,
        columns: [
            {text: "BULAN", flex:1, sortable: true, dataIndex: 'month_name'},
            {text: "TAHUN", flex:1, sortable: true, dataIndex: 'year'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid,rowIndex,colIndex){
                rec = store_daftar_analisa.getAt(rowIndex);
                tgl_rab = rec.get('tgl_rab');
                proyek = rec.get('proyek');
                window.location='<?php echo base_url() ?>rbk/daftar_analisa/'+tgl_rab+'?proyek='+proyek;
            }
        }
        ],
        columnLines: true,        
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: 'Nama Proyek : '
            },{
                xtype: 'combobox',
                id: 'cboshort',
                store: storeproyek,
                valueField: 'value',
                displayField: 'text',
                typeAhead: true,
                queryMode: 'local',
                emptyText: 'Pilih..',
                listeners: {
                    change: function(){
                        store_daftar_analisa.load({
                            params:{
                                'proyek':Ext.getCmp('cboshort').value
                            }
                        });
                    }
                }
            }]
        }],
        height: '100%',
        title: 'DAFTAR ANALISA',
        renderTo: Ext.getBody()
    });

});
</script>

</head>
<body>
</body>
</html>
