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

	Ext.define('mdl_ctg', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'tahap_kode_kendali', mapping: 'tahap_kode_kendali'},
            {name: 'tahap_nama_kendali', mapping: 'tahap_nama_kendali'},            
            {name: 'tahap_satuan_kendali', mapping: 'tahap_satuan_kendali'}
         ]
    });

    var store_ctg_tree = Ext.create('Ext.data.TreeStore', {
        model: 'mdl_ctg',
        proxy: {
            type: 'ajax',
            //the store will get the content from the .json file
            url: '<?php echo base_url() ?>pengendalian/get_data_daftar_analisa'
        },
        folderSort: false,
        remoteSort: true,
        autoLoad: false
    });

    store_ctg_tree.load({
        params:{
            'tgl_rab':'<?php echo $tgl_rab ?>',
            'proyek':'<?php echo $proyek ?>'
        }
    });


Ext.onReady(function() {

    var grid = Ext.create('Ext.tree.Panel', {
        id:'tree-panel',
        store: store_ctg_tree,
        useArrows: true,
        rootVisible: false,
        multiSelect: true,
        singleExpand: false,
        title: 'Daftar Analisa',
        columns: [
            {xtype: 'treecolumn', text: "KODE", width:100, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "ITEM PEKERJAAN", flex:1, sortable: true, dataIndex: 'tahap_nama_kendali'},
            {text: "SATUAN", width:100, sortable: true, dataIndex: 'tahap_satuan_kendali'}
        ],
        columnLines: true,
        height:'100%',
        renderTo: Ext.getBody(),
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    window.location='<?php echo base_url() ?>rbk/pilih_daftar_analisa/';
                }
            }]
        }],
       	bbar: [Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: store_ctg_tree,
                             displayInfo: true
                     })
        ]
    });
});

</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>