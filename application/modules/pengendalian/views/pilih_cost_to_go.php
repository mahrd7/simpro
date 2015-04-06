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

	Ext.define('analisa_cost_to_go', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'tgl_rab',
                mapping: 'tgl_rab'
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
            },
            {
                name: 'kunci',
                mapping: 'kunci'
            }
         ]
    });

Ext.onReady(function() {

var store_cost_to_go = Ext.create('Ext.data.Store', {
        id: 'store_cost_to_go',
        model: 'analisa_cost_to_go',
        pageSize: 50,     
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/get_tanggal_ctg',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        remoteFilter: true,
        autoLoad: false
    });
    store_cost_to_go.load();

var grid_cost_to_go = Ext.create('Ext.grid.Panel', {
        id:'grid_cost_to_go',
        store: store_cost_to_go,
        columns: [
            {text: "BULAN", flex:1, sortable: true, dataIndex: 'month_name'},
            {text: "TAHUN", flex:1, sortable: true, dataIndex: 'year'},
            {text: "STATUS", flex:1, sortable: true, dataIndex: 'status'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid,rowIndex,colIndex){
                rec = store_cost_to_go.getAt(rowIndex);
                tgl_rab = rec.get('tgl_rab');                
                kunci = rec.get('kunci');
                window.location='<?php echo base_url() ?>pengendalian/cost_to_go/'+kunci+'/'+tgl_rab;
            }
        }
        ],
        columnLines: true,
        height: '100%',
        title: 'Cost To Go',
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
            items: grid_cost_to_go
        }]
    });
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>
