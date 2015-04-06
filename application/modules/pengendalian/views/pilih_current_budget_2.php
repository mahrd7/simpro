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
            },
            {
                name: 'month_name_new',
                mapping: 'month_name_new'
            }
         ]
    });

    Ext.define('get_value', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'value', mapping: 'value'}
         ]
    });

    Ext.define('get_value_2', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'value', mapping: 'value'},
            {name: 'tgl_akhir', mapping: 'tgl_akhir'}
         ]
    });

    var store_get_last_tgl_kontrak_kini = Ext.create('Ext.data.Store', {
        model: 'get_value_2',
        autoLoad: false,        
         proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/get_last_tgl_kontrak_kini',
             reader: {
                 type: 'json',
                 root: 'data'
             }
         }
    });

    var store_get_status_approve = Ext.create('Ext.data.Store', {
        model: 'get_value',
        autoLoad: false,        
         proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/get_status_approve_cb',
             reader: {
                 type: 'json',
                 root: 'data'
             }
         }
    });

    var store_get_status_tanggal = Ext.create('Ext.data.Store', {
        model: 'get_value',
        autoLoad: false,        
         proxy: {
             type: 'ajax',
             url: '<?php echo base_url() ?>pengendalian/get_status_tgl_cb',
             reader: {
                 type: 'json',
                 root: 'data'
             }
         }
    });

Ext.onReady(function() {

var store_cost_to_go = Ext.create('Ext.data.Store', {
        model: 'analisa_cost_to_go',
        pageSize: 50,     
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/get_tanggal_cb',
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
        store: store_cost_to_go,
        columns: [
            {text: "MULAI", flex:1, sortable: true, dataIndex: 'month_name'},
            {text: "AKHIR", flex:1, sortable: true, dataIndex: 'month_name_new'},
            {text: "STATUS", flex:1, sortable: true, dataIndex: 'status'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid,rowIndex,colIndex){
                rec = store_cost_to_go.getAt(rowIndex);
                tgl_rab = rec.get('tgl_rab');                
                kunci = rec.get('kunci');
                window.location='<?php echo base_url() ?>pengendalian/current_budget/'+kunci+'/'+tgl_rab;
            }
            },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid,rowIndex,colIndex){
                rec = store_cost_to_go.getAt(rowIndex);
                tgl_rab = rec.get('tgl_rab'); 
                status = rec.get('kunci');

                if (store_cost_to_go.getRange().length > 1) {
                    if (status == 'close') {
                                    Ext.Msg.alert('Informasi','<center>Approval sudah terkunci</center>');
                    } else {
                        Ext.MessageBox.confirm('Delete', 'Apakah anda akan menghapus item ini?',function(resbtn){
                                if(resbtn == 'yes')
                                {
                                    Ext.Ajax.request({
                                        url: '<?=base_url();?>pengendalian/delete_data/currentbudgetall',
                                        method: 'POST',
                                        params: {
                                            'tgl_rab':tgl_rab
                                        },                              
                                        success: function() {
                                        store_cost_to_go.load(); 
                                        Ext.Msg.alert( "Status", "Delete successfully..!", function(){ 
                                        });                                         
                                        },
                                        failure: function() {
                                        }
                                    });                                                                                     
                                }
                        });
                    }
                } else {
                    Ext.Msg.alert('Informasi','Bulan pertama tidak boleh dihapus')
                }               
                }
            }
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Tambah Data',
                tooltip:'Tambah Data',
                handler: function(records, options, success){
                    store_get_status_approve.load({
                        callback: function(records, options, success){
                            if (store_cost_to_go.getRange().length > 0) {
                                if (records[0].data.value == 'open') {
                                    Ext.Msg.alert('Informasi','<center>Maaf sebelum membuat LAPORAN BULANAN bulan berikutnya,<br>LAPORAN BULANAN bulan sebelumnya harus di APPROVE terlebih dahulu<br><br>Terima Kasih</center>');
                                } else {           
                                    store_get_status_tanggal.load({
                                        callback: function(records, options, success){
                                            if (records[0].data.value == 'open') {
                                                Ext.Msg.alert('Informasi','<center>Tidak dapat menambahkan lagi data Current Budget</center>');
                                            } else {
                                                store_get_last_tgl_kontrak_kini.load({
                                                    callback: function(records, options, success){
                                                        tgl = records[0].data.value;
                                                        tgl_akhir = records[0].data.tgl_akhir;
                                                        Ext.MessageBox.confirm('Tambah Data', 'Apakah anda ingin menambah Current Budget?',function(resbtn){
                                                            if(resbtn == 'yes')
                                                            {
                                                                Ext.Ajax.request({
                                                                    url: '<?=base_url();?>pengendalian/add_data/currentbudget',
                                                                    method: 'POST',
                                                                    params: {
                                                                        'tgl_rab':tgl,
                                                                        'tgl_akhir':tgl_akhir
                                                                    },                              
                                                                    success: function() {
                                                                    store_cost_to_go.load(); 
                                                                    Ext.Msg.alert( "Status", "Add successfully..!", function(){ 
                                                                    });                                         
                                                                    },
                                                                    failure: function() {
                                                                    }
                                                                });                                                                                     
                                                            }
                                                        });
                                                    }
                                                });
                                            }
                                        }
                                    }) ;                       
                                }
                            }
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
        }],
        height: '100%',
        title: 'Current Budget'
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
