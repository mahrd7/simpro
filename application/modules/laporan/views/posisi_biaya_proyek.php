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
var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

Ext.require([
    '*'
]);

    Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'kode_rap', mapping: 'kode_rap'},
            {name: 'kode', mapping: 'kode'},            
            {name: 'uraian_pekerjaan', mapping: 'uraian_pekerjaan'},
            {name: 'keterangan', mapping: 'keterangan'},
            {name: 'satuan', mapping: 'satuan'},
            {name: 'rapa_a_volume', mapping: 'rapa_a_volume'},
            {name: 'rapa_a_harga_satuan', mapping: 'rapa_a_harga_satuan'},
            {name: 'rapa_a_jumlah', mapping: 'rapa_a_jumlah'},
            {name: 'rapa_k_volume', mapping: 'rapa_k_volume'},
            {name: 'rapa_k_harga_satuan', mapping: 'rapa_k_harga_satuan'},
            {name: 'rapa_k_jumlah', mapping: 'rapa_k_jumlah'},
            {name: 'bk_sd_lalu', mapping: 'bk_sd_lalu'},
            {name: 'rencana_bln_thn', mapping: 'rencana_bln_thn'},
            {name: 'tunai_volume', mapping: 'tunai_volume'},
            {name: 'tunai_jumlah', mapping: 'tunai_jumlah'},
            {name: 'hutang_volume', mapping: 'hutang_volume'},
            {name: 'hutang_jumlah', mapping: 'hutang_jumlah'},
            {name: 'antisipasi_volume', mapping: 'antisipasi_volume'},
            {name: 'antisipasi_jumlah', mapping: 'antisipasi_jumlah'},
            {name: 'total_volume', mapping: 'total_volume'},
            {name: 'total_jumlah', mapping: 'total_jumlah'},
            {name: 'sa_volume', mapping: 'sa_volume'},
            {name: 'sa_harga_satuan', mapping: 'sa_harga_satuan'},
            {name: 'sa_jumlah', mapping: 'sa_jumlah'},
            {name: 'kira_selesai_volume', mapping: 'kira_selesai_volume'},
            {name: 'kira_selesai_jumlah', mapping: 'kira_selesai_jumlah'},
            {name: 'deviasi', mapping: 'deviasi'},
            {name: 'rbk_bln_thn_volume', mapping: 'rbk_bln_thn_volume'},
            {name: 'rbk_bln_thn_jumlah', mapping: 'rbk_bln_thn_jumlah'}
         ]
    });

    Ext.define('mdl_combo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'text', mapping: 'text'},
            {name: 'value', mapping: 'value'}
         ]
    });

	var storebln = Ext.create('Ext.data.Store', {
        id: 'storebln',
        model: 'mdl_combo',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getbulan',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    storebln.load();

    var storethn = Ext.create('Ext.data.Store', {
        id: 'storethn',
        model: 'mdl_combo',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/gettahun',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    storethn.load();

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

    var dummy = [{
        "id"="",
        "kode_rap"="",
        "kode"="",     
        "uraian_pekerjaan"="",
        "keterangan"="",
        "satuan"="",
        "rapa_a_volume"="",
        "rapa_a_harga_satuan"="",
        "rapa_a_jumlah"="",
        "rapa_k_volume"="",
        "rapa_k_harga_satuan"="",
        "rapa_k_jumlah"="",
        "bk_sd_lalu"="",
        "rencana_bln_thn"="",
        "tunai_volume"="",
        "tunai_jumlah"="",
        "hutang_volume"="",
        "hutang_jumlah"="",
        "antisipasi_volume"="",
        "antisipasi_jumlah"="",
        "total_volume"="",
        "total_jumlah"="",
        "sa_volume"="",
        "sa_harga_satuan"="",
        "sa_jumlah"="",
        "kira_selesai_volume"="",
        "kira_selesai_jumlah"="",
        "deviasi"="",
        "rbk_bln_thn_volume"="",
        "rbk_bln_thn_jumlah"=""
    }
    ];

    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        pageSize: 50, 
        data: dummy
    });

Ext.onReady(function() {

    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        title: 'RAPI',
        columns: [
            {text: "KODE RAP", width:100, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "NAMA", width:400, sortable: true, dataIndex: 'tahap_nama_kendali'},
            {text: "SATUAN", width:100, sortable: true, dataIndex: 'tahap_satuan_kendali'},
            {text: "VOLUME", width:100, sortable: true, dataIndex: 'tahap_volume_kendali'},
            {text: "HARGA SATUAN", width:200, sortable: true, dataIndex: 'tahap_harga_satuan_kendali'},
            {text: "JUMLAH", width:250, sortable: true, dataIndex: 'tahap_total_kendali'},
            {text: "Keterangan", width:100, sortable: true, dataIndex: 'tahap_total_kendali'},
            {text: "KODE SIMPRO", width:100, sortable: true, dataIndex: 'tahap_total_kendali'},
            {text: "KODE SIMPRO", width:100, sortable: true, dataIndex: 'tahap_total_kendali'},{text: "KODE RAP", width:100, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "NAMA", width:400, sortable: true, dataIndex: 'tahap_nama_kendali'},
            {text: "SATUAN", width:100, sortable: true, dataIndex: 'tahap_satuan_kendali'},
            {text: "VOLUME", width:100, sortable: true, dataIndex: 'tahap_volume_kendali'},
            {text: "HARGA SATUAN", width:200, sortable: true, dataIndex: 'tahap_harga_satuan_kendali'},
            {text: "JUMLAH", width:250, sortable: true, dataIndex: 'tahap_total_kendali'},
            {text: "Keterangan", width:100, sortable: true, dataIndex: 'tahap_total_kendali'},
            {text: "KODE SIMPRO", width:100, sortable: true, dataIndex: 'tahap_total_kendali'},
            {text: "KODE SIMPRO", width:100, sortable: true, dataIndex: 'tahap_total_kendali'},{text: "KODE RAP", width:100, sortable: true, dataIndex: 'tahap_kode_kendali'},
            {text: "NAMA", width:400, sortable: true, dataIndex: 'tahap_nama_kendali'},
            {text: "SATUAN", width:100, sortable: true, dataIndex: 'tahap_satuan_kendali'},
            {text: "VOLUME", width:100, sortable: true, dataIndex: 'tahap_volume_kendali'},
            {text: "HARGA SATUAN", width:200, sortable: true, dataIndex: 'tahap_harga_satuan_kendali'},
            {text: "JUMLAH", width:250, sortable: true, dataIndex: 'tahap_total_kendali'},
            {text: "Keterangan", width:100, sortable: true, dataIndex: 'tahap_total_kendali'},
            {text: "KODE SIMPRO", width:100, sortable: true, dataIndex: 'tahap_total_kendali'},
            {text: "KODE SIMPRO", width:100, sortable: true, dataIndex: 'tahap_total_kendali'}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text:'Print',
                tooltip:'Print',
                handler: function(){

                }
            }]
        },{
            xtype: 'toolbar',
            dock: 'top',
            items: [
            'Periode : ',{
            xtype: 'combobox',
            name: 'cboshort',
            store: storebln,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih Bulan..',
            width: 120
            },{
            xtype: 'combobox',
            name: 'cboshort',
            store: storethn,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih Tahun..',
            width: 100
            },{
                text: 'Go>>'
            }]
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
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>