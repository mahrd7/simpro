<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<style type="text/css">
.icon-new {
    background: url(<?php echo base_url(); ?>assets/images/new-icon.png) no-repeat 0 -1px;
}
</style>
<script type="text/javascript">

Ext.require([
    '*'
]);

    Ext.define('tgl_lpf', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'bln',
                mapping: 'bln'
            },
            {
                name: 'blnnama',
                mapping: 'blnnama'
            },
            {
                name: 'thn',
                mapping: 'thn'
            },
            {
                name: 'tahap_tanggal_kendali',
                mapping: 'tahap_tanggal_kendali'
            },
            {
                name: 'status',
                mapping: 'status'
            },
            {
                name: 'kunci',
                mapping: 'kunci'
            }
         ]
    });

    var store_tgl = Ext.create('Ext.data.Store', {
        id: 'store_tgl',
        model: 'tgl_lpf',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,        
         proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/get_tanggal_rencana_kontrak_terkini',
             reader: {
                 type: 'json',
                 root: 'data'
             }
         }
    });
    store_tgl.load();

Ext.onReady(function() {

var grid_lpf = Ext.create('Ext.grid.Panel', {
        id:'grid_lpf',
        store: store_tgl,
        columns: [
            {text: "BULAN", flex:1, sortable: true, dataIndex: 'blnnama'},
            {text: "TAHUN", flex:1, sortable: true, dataIndex: 'thn'},
            {text: "STATUS", flex:1, sortable: true, dataIndex: 'status'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid,rowIndex,colIndex){
                rec = store_tgl.getAt(rowIndex);
                tgl_rab = rec.get('tahap_tanggal_kendali');                
                kunci = rec.get('kunci');
                window.location='<?php echo base_url() ?>pengendalian/rencana_kontrak_kini/'+kunci+'/'+tgl_rab;
            }
        }
        ],
        columnLines: true,
        height: '100%',
        title: 'RENCANA KONTRAK KINI',
        dockedItems: [{
                xtype: 'toolbar',
                items: [
                {
                    text:'Open In New Tab',
                    iconCls:'icon-new',
                    handler:function(){
                        window.open(document.URL,'_blank');
                    }
                }
                ]
            }],
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
            items: grid_lpf
        }]
    });
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>
