<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>

<script type="text/javascript">

Ext.require([
    '*'
]);

	Ext.define('analisa_current_budget', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'id',
                mapping: 'id'
            },
            {
                name: 'bulan',
                mapping: 'bulan'
            },
            {
                name: 'tahun',
                mapping: 'tahun'},
            {
                name: 'status',
                mapping: 'status'}
         ]
    });

    Ext.define('dummydatast', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'id'
            },
            {
                name: 'bulan'
            },
            {
                name: 'tahun'
            },
            {
                name: 'status'
            }
         ]
    });

    var dummydata = [
        ['1','Januari','2010','NOT APPROVE']
    ];

Ext.onReady(function() {

var storedummy = Ext.create('Ext.data.ArrayStore', {
        model: 'dummydatast',
        data: dummydata
    });

var store_current_budget = Ext.create('Ext.data.Store', {
        id: 'store_current_budget',
        model: 'analisa_current_budget',
        pageSize: 50,     
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/..',
         reader: {
             type: 'json',
             root: 'data'
         }
        },
        remoteFilter: true,
        autoLoad: false
    });
    store_current_budget.load();

var grid_current_budget = Ext.create('Ext.grid.Panel', {
        id:'grid_current_budget',
        store: storedummy,
        columns: [
            {text: "BULAN", flex:1, sortable: true, dataIndex: 'bulan'},
            {text: "TAHUN", flex:1, sortable: true, dataIndex: 'tahun'},
            {text: "STATUS", flex:1, sortable: true, dataIndex: 'status'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid,rowIndex,colIndex){
                rec = storedummy.getAt(rowIndex);
                var bln = rec.get('bulan');
                var thn = rec.get('tahun');
                window.location='<?php echo base_url() ?>pengendalian/currentbudget/'+bln+"/"+thn;
            }
        }
        ],
        columnLines: true,

        width: '100%',
        height: '100%',
        title: 'Current Budget',
        renderTo: Ext.getBody()
    });

});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>
