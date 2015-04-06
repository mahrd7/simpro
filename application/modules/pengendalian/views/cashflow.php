<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<style type="text/css">

.price-fall .total .x-grid-cell-inner {
    background-color: #ffffff;
}

.price-fall_2 .total .x-grid-cell-inner {
    background-color: #ffffff;
}

.price-rise .total .x-grid-cell-inner {
    background-color: #ffffff;
}

.blue .total .x-grid-cell-inner {
    background-color: rgb(71, 163, 255);
}

.price-fall .x-change-cell .x-grid-cell-inner {
    background-color: #E4FF27;
}

.price-fall_2 .x-change-cell .x-grid-cell-inner {
    background-color: #E4FF27;
}

.price-rise .x-change-cell .x-grid-cell-inner {
    background-color: #E4FF27;
}

.blue .x-change-cell .x-grid-cell-inner {
    background-color: rgb(71, 163, 255);
}

.price-fall .x-change-cell_2 .x-grid-cell-inner {
    background-color: #E4FF27;
}

.price-fall_2 .x-change-cell_2 .x-grid-cell-inner {
    background-color: #E4FF27;
}

.price-rise .x-change-cell_2 .x-grid-cell-inner {
    background-color: #ffffff;
}

.blue .x-change-cell_2 .x-grid-cell-inner {
    background-color: rgb(71, 163, 255);
}

.price-fall .x-change-cell_3 .x-grid-cell-inner {
    background-color: #ffffff;
}

.price-fall_2 .x-change-cell_3 .x-grid-cell-inner {
    background-color: #E4FF27;
}

.price-rise .x-change-cell_3 .x-grid-cell-inner {
    background-color: #ffffff;
}

.blue .x-change-cell_3 .x-grid-cell-inner {
    background-color: rgb(71, 163, 255);
}
</style>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
        <?php 
            $pages = $this->uri->segment(5); 
        ?>
<script type="text/javascript">

Ext.require([
    '*'
]);

	Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'kode', mapping: 'kode'},
            {name: 'uraian', mapping: 'uraian'},
            {name: 'current_cash_budget', mapping: 'current_cash_budget'},
            {name: 'realisasi_lalu', mapping: 'realisasi_lalu'},
            {name: 'realisasi_sekarang', mapping: 'realisasi_sekarang'},
            {name: 'realisasi_kini', mapping: 'realisasi_kini'},
            {name: 'proyeksi1', mapping: 'proyeksi1'},
            {name: 'proyeksi2', mapping: 'proyeksi2'},
            {name: 'proyeksi3', mapping: 'proyeksi3'},
            {name: 'proyeksi4', mapping: 'proyeksi4'},
            {name: 'jumlah', mapping: 'jumlah'},
            {name: 'sisa', mapping: 'sisa'},
            {name: 'sbp', mapping: 'sbp'},
            {name: 'spp', mapping: 'spp'}
         ]
    });

    var store = Ext.create('Ext.data.Store', {
        model: 'mdl',
        remoteSort: true,
        autoLoad: false,        
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url() ?>pengendalian/get_data_cashflow',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var storebln = Ext.create('Ext.data.Store', {
        autoLoad: false,
        fields: ['text','value'], 
        proxy: { 
            type: 'ajax', 
            url: '<?=base_url();?>laporan/getbulan',
            reader: { 
                root: 'data',
                type: 'json' 
            } 
        }
    });

    var storethn = Ext.create('Ext.data.Store', {
        autoLoad: false,
        fields: ['text','value'], 
        proxy: { 
            type: 'ajax', 
            url: '<?=base_url();?>laporan/gettahun',
            reader: { 
                root: 'data',
                type: 'json' 
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
        clicksToEdit: 2
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        plugins: [rowEditing],
        autoscroll: true,
        viewConfig: {
            markDirty: false
        },
        title: 'CASHFLOW PROYEK <?php echo $bln; ?> Tahun <?php echo $thn; ?>',
        columns: [
            {text: "NO", width:50, sortable: true, dataIndex: 'kode', tdCls: 'total'},
            {text: "URAIAN", width:300, sortable: true, dataIndex: 'uraian', tdCls: 'total'},
            {text: "CURRENT CASH BUDGET", width:150, sortable: true, dataIndex: 'current_cash_budget',
                tdCls: 'x-change-cell_3',
                editor:{
                    xtype:'numberfield'
                }
            },
            {text: "REALISASI BULAN <?php echo $bln ?>",
                columns:[                
                {text: "SD.BULAN LALU", width:100, sortable: true, dataIndex: 'realisasi_lalu', tdCls: 'total'},
                {text: "BULAN <?php echo $bln ?>", width:120, sortable: true, dataIndex: 'realisasi_sekarang', 
                    tdCls: 'x-change-cell_2',
                    editor:{
                        xtype:'numberfield'
                    }
                },
                {text: "SD.BULAN <?php echo $bln ?>", width:120, sortable: true, dataIndex: 'realisasi_kini', tdCls: 'total'}
                ]
            },
            {text: "PROYEKSI",
                columns:[                
                {text: "BULAN <?php echo $bln1 ?>",
                    columns:[
                        {text: "1 - 15", width:120, sortable: true, dataIndex: 'proyeksi1',
                            tdCls: 'x-change-cell',
                            editor:{
                                xtype:'numberfield'
                            }
                        },
                        {text: "16 - 31", width:120, sortable: true, dataIndex: 'proyeksi2',
                            tdCls: 'x-change-cell',
                            editor:{
                                xtype:'numberfield'
                            }
                        }
                    ]
                },
                {text: "BULAN <?php echo $bln2 ?>",
                    columns:[
                        {text: "1 - 15", width:120, sortable: true, dataIndex: 'proyeksi3',
                            tdCls: 'x-change-cell',
                            editor:{
                                xtype:'numberfield'
                            }
                        },
                        {text: "16 - 31", width:120, sortable: true, dataIndex: 'proyeksi4',
                            tdCls: 'x-change-cell',
                            editor:{
                                xtype:'numberfield'
                            }
                        }
                    ]
                }
                ]
            },
            {text: "JUMLAH", width:100, sortable: true, dataIndex: 'jumlah', tdCls: 'total'},
            {text: "SISA", width:100, sortable: true, dataIndex: 'sisa', tdCls: 'total'}
        ],
        viewConfig: {
        getRowClass: function(record, index) {

                var c = record.get('id');

                if ('<?php echo $pages ?>' != 'report') {
                    if (c == 5 || c == 6 || c == 7 || c == 8 || c == 9 || c == 10) {
                        return 'price-fall';
                    }

                    if (c == 12 || c == 13 || c == 14 || c == 15 || c == 16 || c == 17) {
                        return 'price-rise';
                    }

                    if (c == 21 || c == 22 || c == 25 || c == 26) {
                        return 'price-fall_2';
                    }
                }

                if (c == 11 || c == 19 || c == 20 || c == 23 || c == 24 || c == 27 || c == 28 || c == 29 || c == 30){
                    return 'blue';
                }
                
            }
        },
        columnLines: true,
        dockedItems: [{
            dock: 'bottom',
            xtype: 'toolbar',
            itemId:'toolbar1',
            items: [{
            	text: 'Simpan',
            	tooltip: 'Simpan',
            	handler: function(){
                    // console.log(store);
                    data = grid.getComponent('toolbar2');
                    spp = data.getComponent('spp').value;
                    sbp = data.getComponent('sbp').value;
                    var data = new Array();
                    var dat = new Array();
                    for (i = 1; i <= 16; i++) {
                        switch (i){
                            case 1:
                                rowindex = '5';
                            break;
                            case 2:
                                rowindex = '6';
                            break;
                            case 3:
                                rowindex = '7';
                            break;
                            case 4:
                                rowindex = '8';
                            break;
                            case 5:
                                rowindex = '9';
                            break;
                            case 6:
                                rowindex = '10';
                            break;
                            case 7:
                                rowindex = '12';
                            break;
                            case 8:
                                rowindex = '13';
                            break;
                            case 9:
                                rowindex = '14';
                            break;
                            case 10:
                                rowindex = '15';
                            break;
                            case 11:
                                rowindex = '16';
                            break;
                            case 12:
                                rowindex = '17';
                            break;
                            case 13:
                                rowindex = '21';
                            break;
                            case 14:
                                rowindex = '22';
                            break;
                            case 15:
                                rowindex = '25';
                            break;
                            case 16:
                                rowindex = '26';
                            break;
                        }
                        rec = store.getAt(rowindex);
                        realisasi=rec.get('realisasi_sekarang');
                        proyeksi1=rec.get('realisasi_kini');
                        proyeksi2=rec.get('proyeksi1');
                        proyeksi3=rec.get('proyeksi2');
                        proyeksi4=rec.get('proyeksi3');
                        proyeksi5=rec.get('proyeksi4');
                        current_cash_budget=rec.get('current_cash_budget');

                        // data['realisasi']=realisasi;       
                        // data['proyeksi1']=proyeksi1;
                        // data['proyeksi2']=proyeksi2;
                        // data['proyeksi3']=proyeksi3;       
                        // data['proyeksi4']=proyeksi4;
                        // data['proyeksi5']=proyeksi5;
                        // data['current_cash_budget']=current_cash_budget;

                        // dat[i] = data;
                        Ext.Ajax.request({
                             url: '<?=base_url();?>pengendalian/insert_cashflow',
                                method: 'POST',
                                params: {
                                    'ket_id':i,
                                    'currentbudget':current_cash_budget,
                                    'realisasi':realisasi,
                                    'proyeksi1':proyeksi1,
                                    'proyeksi2':proyeksi2,
                                    'proyeksi3':proyeksi3,
                                    'proyeksi4':proyeksi4,
                                    'proyeksi5':proyeksi5,
                                    'spp':spp,
                                    'sbp':sbp,
                                    'tgl_rab':'<?php echo $tgl_rab ?>'
                                },                              
                            success: function() {                                
                            },
                            failure: function() {
                            Ext.Msg.alert( "Status", "No Respond..!"); 
                            }
                        });
                        
                    };

                    // console.log(i+'-'+realisasi+'+'+proyeksi1+'+'+proyeksi2+'+'+proyeksi3+'+'+proyeksi4+'+'+proyeksi5);
                        
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    store.load({
                        params:{
                            'tgl_rab':'<?php echo $tgl_rab ?>'
                        }
                    }); 
            	}
            },'-',{
                text:'Batal',
                tooltip:'Batal',
                handler: function(){
                    var url ='<?php echo base_url(); ?>pengendalian/pilih_cashflow';
                    window.location=url;
                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            itemId:'toolbar2',
            items: [                
                'SALDO BANK PROYEK : ',
                {xtype:'numberfield', name:'sbp', itemId:'sbp', minValue:0},
                '-',
                'SALDO PENJAMINAN PELAKSANAAN : ',
                {xtype:'numberfield', name:'spp', itemId:'spp', minValue:0}
            ]
        },{
            dock: 'top',
            xtype: 'toolbar',
            itemId:'toolbar_top',
            items: [                
                {
                    xtype: 'combo',
                    name: 'cbo_bln',
                    allowBlank: false,
                    store: storebln,
                    itemId: 'cbo_bln',
                    fieldLabel: 'Periode ',
                    labelWidth: 50,
                    emptyText: 'Pilih..',
                    displayField: 'text',
                    typeAhead: true,
                    anchor: '100%',
                    valueField: 'value',
                    listeners:{
                        beforerender : function(e){
                            storebln.load();
                            e.setValue(<?php echo $bln_no ?>);
                        }
                    }
                },
                {
                    xtype: 'combo',
                    name: 'cbo_thn',
                    allowBlank: false,
                    store: storethn,
                    itemId: 'cbo_thn',
                    width: 80,
                    emptyText: 'Pilih..',
                    displayField: 'text',
                    typeAhead: true,
                    anchor: '100%',
                    valueField: 'value',
                    listeners:{
                        beforerender : function(e){
                            storethn.load();
                            e.setValue(<?php echo $thn ?>);
                        }
                    }
                },'-',
                {
                    text:'Go>>',
                    handler: function(){
                        data_top = grid.getComponent('toolbar_top');
                        bl = data_top.getComponent('cbo_bln').getValue();
                        th = data_top.getComponent('cbo_thn').getValue();
                        bln_new = '<?php echo sprintf("%07s","'+bl+'") ?>';
                        // console.log(bln_new);
                        window.location = '<?php echo base_url(); ?>pengendalian/cashflow/kunci/'+th+'-'+bln_new+'-01/report';
                    }
                },'-',
                {
                    text:'Print'
                }
            ]
        }],
        width: '100%',
        height: '100%',
        listeners:{
            beforerender: function(){
                data = grid.getComponent('toolbar2');
                data_a = grid.getComponent('toolbar1');
                data_top = grid.getComponent('toolbar_top');
                if ('<?php echo $pages ?>' == 'report') {
                    data.hide();
                    data_a.hide();
                } else {
                    data_top.hide();
                }
                store.load({
                    params:{
                        'tgl_rab':'<?php echo $tgl_rab ?>'
                    },
                    callback: function(a){
                        spp = data.getComponent('spp').setValue(a[26].data.spp);
                        sbp = data.getComponent('sbp').setValue(a[26].data.sbp);
                    }
                });
            },
            beforeedit: function(edit,e){
                if ('<?php echo $pages ?>' == 'report') {
                    return false;
                } else {
                    var f = e.record.get('id');
                    if (f == 21 || f == 22 || f ==  25 || f ==  26){
                        grid.columns[2].getEditor().setDisabled(false);
                    } else {
                        grid.columns[2].getEditor().setDisabled(true);
                    } 

                    if (f == 12 || f == 13 || f == 14 || f == 15 || f == 16 || f == 17) {
                        grid.columns[3].items.items[1].getEditor().setDisabled(true);
                    } else {
                        grid.columns[3].items.items[1].getEditor().setDisabled(false);
                    }

                    if (f == 0 || f == 1 || f == 2 || f == 3 || f == 4 || f == 11 || f == 19 || f == 20 || f == 23 || f == 24 || f == 27 || f == 28 || f == 29 || f == 30) {
                        return false;
                    }
                }
            }
        }
        // ,
        // renderTo: Ext.getBody()
        // ,
       	// bbar: [Ext.create('Ext.toolbar.Paging', {
        //                      pageSize: 50,
        //                      store: store,
        //                      displayInfo: true
        //              })
        // ]
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