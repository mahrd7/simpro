<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript" language="JavaScript" src="../assets/js/jquery-1.9.1.min.js"></script>
<style type="text/css">
.icon-new {
    background: url(<?php echo base_url(); ?>assets/images/new-icon.png) no-repeat 0 -1px;
}
</style>
<script type="text/javascript">

Ext.require([
    '*'
]);

    var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';

    var rec;
    Ext.define('analisa', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_analisa_asat', mapping: 'id_analisa_asat'},
            {name: 'proyek_id', mapping: 'proyek_id'},
            {name: 'id', mapping: 'id'},
            {name: 'alat', mapping: 'alat'},
            {name: 'unit', mapping: 'unit'},
            {name: 'tgl_awal', mapping: 'tgl_awal'},
            {name: 'tgl_akhir', mapping: 'tgl_akhir'},
            {name: 'jumlah', mapping: 'jumlah'}
         ]
    });

    Ext.define('analisaparent', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'id_sch_proyek', mapping: 'id_sch_proyek'},
            {name: 'tgl_sch_parent', mapping: 'tgl_sch_parent'},
            {name: 'jumlah_parent', mapping: 'jumlah_parent'}
         ]
    });

    var storemutu = Ext.create('Ext.data.Store', {
        model: 'analisa',
        autoLoad: false,        
        proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>pengendalian/getsch/peralatan',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    storeparent = Ext.create('Ext.data.Store', {
        model: 'analisaparent',
        proxy: {
            type: 'ajax',
            url: '<?php echo base_url(); ?>pengendalian/getschdetail/peralatan',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        autoLoad: false
    });

Ext.onReady(function() {
    storemutu.load();

    var urls = '<?php echo base_url(); ?>pengendalian/getsch/peralatan';
    var grid4 = Ext.create('Ext.grid.Panel', {
        store: storemutu,
        columns: [
            {text: "Id", width: 80, sortable: true, dataIndex: 'id'},
            {text: "Alat", width: 120, sortable: true, dataIndex: 'alat'},
            {text: "Unit", width: 120, sortable: true, dataIndex: 'unit'},
            {text: "Jumlah", width: 120, sortable: true, dataIndex: 'jumlah'},
            {text: "Tanggal Awal", width: 120, sortable: true, dataIndex: 'tgl_awal'},
            {text: "Tanggal Akhir", width: 120, sortable: true, dataIndex: 'tgl_akhir'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: false,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
                rec = storemutu.getAt(rowIndex);
                
                var proyek_id = rec.get('proyek_id');
                var id_analisa_asat = rec.get('id_analisa_asat');
                var tahap_kendali_name = rec.get('alat');
                var unit_id = rec.get('unit_id');
                var unit = rec.get('unit');
                var tgl_awal = rec.get('tgl_awal');
                var tgl_akhir = rec.get('tgl_akhir');
                var jumlah = rec.get('jumlah');

                formuraian(proyek_id,id_analisa_asat,tahap_kendali_name,unit_id,unit,tgl_awal,tgl_akhir, jumlah);
            }
            },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: false,icon:'<?=base_url();?>assets/images/add.gif',
            handler: function(grid, rowIndex, colIndex){
                rec = storemutu.getAt(rowIndex);

                var proyek_id = rec.get('proyek_id');
                var tahap_kendali_id = rec.get('id_analisa_asat');
                var tahap_kendali_name = rec.get('alat');

                addbobot(proyek_id,tahap_kendali_id, tahap_kendali_name);
            }
            },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: false,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
            rec = storemutu.getAt(rowIndex);
            var proyek_id = rec.get('proyek_id');
            var tahap_kendali_id = rec.get('id_analisa_asat');

                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                    if(resbtn == 'yes')
                    {
                        Ext.Ajax.request({
                             url: '<?php echo base_url(); ?>pengendalian/deletesch/peralatan',
                                method: 'POST',
                                params: {
                                    'proyek_id' :  proyek_id,
                                    'id_analisa_asat' : tahap_kendali_id
                                },                              
                            success: function() {
                            storemutu.load();     
                            Ext.Msg.alert( "Status", "Delete successfully..!"); 
                            document.getElementById('gantframe').contentWindow.location.reload();                                 
                            },
                            failure: function() {
                            Ext.Msg.alert( "Status", "No Respond..!"); 
                            }
                        });                                                                                        
                    }
                });
            }
        }
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
            /*
            {
                text:'Tambah Schedule',
                tooltip:'Tambah Schedule',
                handler: function(){
                    frmaddfirst();
                }
            }, '-', 
            */
            {
                text:'Print',
                tooltip:'Print'
            },'-',
            {
                    text:'Open In New Tab',
                    iconCls:'icon-new',
                    handler:function(){
                        window.open(document.URL,'_blank');
                    }
                }
            ]
        }],
        width: '100%',
        height: '100%',
        frame: true,
        title: 'Penggunaan Peralatan'
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
            title: 'Data Penggunaan Peralatan',       
            width: '100%',
            height: '50%',            
            border: 0,
            layout: 'fit',
            autoScroll: true,
            items: [grid4]
        },{
            region: 'west',
            layout: 'fit',
            title: 'Gantt Penggunaan Peralatan',
            width: '100%',
            height: '50%',
            border: 0,
            html: '<iframe src="<?php echo base_url() ?>pengendalian/schedule_cart/peralatan" border=0 width="100%" height="100%" id="gantframe"></iframe>'
        }]
    });

function frmaddfirst(){
    var urls = '<?php echo base_url(); ?>pengendalian/insertschproyek/peralatan';
    var formdoc = Ext.widget({
        xtype: 'form',
        layout: 'form',
        url: urls,
        frame: false,
        bodyPadding: '5 5 0',
        width: 350,
        fieldDefaults: {
            msgTarget: 'side'
        },
        items: [
        {
            xtype: 'textfield',
            fieldLabel: 'Alat',
            afterLabelTextTpl: required,
            name: 'alat',
            allowBlank: false
        },{
            xtype: 'textfield',
            fieldLabel: 'Unit',
            afterLabelTextTpl: required,
            name: 'unit',
            allowBlank: false
        },{
            xtype: 'datefield',
            fieldLabel: 'Tanggal Awal',
            afterLabelTextTpl: required,
            name: 'tgl_awal',
            format: 'd-M-Y',
            submitFormat: 'Y-m-d',
            allowBlank: false
        },{
            xtype: 'datefield',
            fieldLabel: 'Tanggal Akhir',
            afterLabelTextTpl: required,
            name: 'tgl_akhir',
            format: 'd-M-Y',
            submitFormat: 'Y-m-d',
            allowBlank: false
        },{
            xtype: 'numberfield',
            fieldLabel: 'Jumlah',
            afterLabelTextTpl: required,
            name: 'jumlah',
            allowBlank: false
        }],

        buttons: [{
            text: 'Save',
            handler: function() {

                if (formdoc.getForm().isValid()){
                    formdoc.getForm().submit({
                        success: function(fp, o) {                          
                            storemutu.load();
                            formdoc.getForm().reset(); 
                            Ext.MessageBox.alert('Schedule Proyek','Insert successfully..!');
                            document.getElementById('gantframe').contentWindow.location.reload();
                        },
                        failure: function(fp, o){
                            Ext.MessageBox.alert('Master Data','Failed..!');  
                            formdoc.getForm().reset();                        
                        }
                    });
                    winadddoc.hide();
                }
            }
        },{
            text: 'Cancel',
            handler: function() {
               winadddoc.hide();
            }
        }]
    });

    var winadddoc = Ext.create('Ext.Window', {
        title: 'Penggunaan Peralatan',
        height: 250,
        width: 500,
        layout: 'fit',
        items: formdoc,
        modal:true
    }).show();
}

function getsendjson(id,uraian){

    storeparent.load({
        params:{
            'id':id
        }
    });

    gridparent = Ext.create('Ext.grid.Panel', {
        frame:false,
        store: storeparent,
        selModel: {
            selType: 'cellmodel'
        },plugins: [
        Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToEdit: 2
        })
        ],
        columns: [
            {text: "Id", width: 80, sortable: true, dataIndex: 'id'},
            {text: "Tanggal", width: 120, sortable: true, dataIndex: 'tgl_sch_parent'},
            {text: "Jumlah", width: 120, sortable: true, dataIndex: 'jumlah_parent',editor: 'textfield'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
                recp = storeparent.getAt(rowIndex);
                var idparent = recp.get('id');

                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                    if(resbtn == 'yes')
                    {
                        Ext.Ajax.request({
                             url: '<?=base_url();?>pengendalian/deleteschparent/peralatan',
                                method: 'POST',
                                params: {
                                    'id' :  idparent
                                },                              
                            success: function() {
                            Ext.Msg.alert( "Status", "Delete successfully..!"); 
                            storeparent.load({
                                params:{
                                    'id':id
                                }
                            });  
                            storemutu.load();
                            document.getElementById('gantframe').contentWindow.location.reload();                              
                            },
                            failure: function() {
                            Ext.Msg.alert( "Status", "No Respond..!"); 
                            }
                        });                                                                                        
                    }
                });
            }
            }
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
            {
                text:'Close',
                handler: function(){
                winparent.hide();
                }
            }]
        },
        {
            xtype: 'toolbar',
            dock: 'top',
            items: [
            /*
            {
                text:'Tambah Bobot',
                tooltip:'Tambah Bobot',
                handler: function(){
                    frma(id);
                }
            }
            */
            ]
        }],
        width: '100%',
        height: '100%'
    });

    winparent = Ext.create('Ext.Window', {
        title: 'Schedule Proyek'+uraian,
        height: 300,
        width: 400,
        layout: 'fit',
        items: gridparent,
        modal:true
    }).show();
}

function frma(id){
    var urls = '<?php echo base_url(); ?>pengendalian/insertdetailsch/peralatan';

    var fromdetailunit = Ext.widget({
        xtype: 'form',
        layout: 'form',
        url: urls,
        frame: false,
        bodyPadding: '5 5 0',
        width: '100%',
        height: '100%',
        fieldDefaults: {
            msgTarget: 'side'
        },
        items: [
        {
            xtype: 'textfield',
            fieldLabel: 'id',
            afterLabelTextTpl: required,
            name: 'id',
            allowBlank: false,
            value: id,
            hidden: true
        },
        {
            xtype: 'datefield',
            fieldLabel: 'Tanggal',
            afterLabelTextTpl: required,
            name: 'tgl',
            format: 'd-M-Y',
            submitFormat: 'Y-m-d',
            allowBlank: false
        },
        {
            xtype: 'numberfield',
            fieldLabel: 'Bobot',
            afterLabelTextTpl: required,
            name: 'bobot',
            allowBlank: false
        }],

        buttons: [{
            text: 'Save',
            handler: function() { 

                if (fromdetailunit.getForm().isValid()){
                    fromdetailunit.getForm().submit({
                        success: function(fp, o) {                          
                            storemutu.load();
                            storeparent.load({
                                params:{
                                    'id':id
                                }
                            });
                            fromdetailunit.getForm().reset(); 
                            Ext.MessageBox.alert('Schedule Proyek','Insert successfully..!');
                            document.getElementById('gantframe').contentWindow.location.reload();
                        },
                        failure: function(fp, o){
                            Ext.MessageBox.alert('Master Data','Failed..!');  
                            fromdetailunit.getForm().reset();                          
                        }
                    });
                    winaddUnit.hide();
                }
            }
        },{
            text: 'Cancel',
            handler: function() {
               winaddUnit.hide();
            }
        }]
    });

    var winaddUnit = Ext.create('Ext.Window', {
        title: 'Tambah Unit',
        height: 250,
        width: 500,
        layout: 'fit',
        items: fromdetailunit,
        modal:true
    }).show();
}
        
});


function formuraian(proyek_id, id_analisa_asat, tahap_kendali_name, unit_id, unit, tgl_awal, tgl_akhir, jumlah){

    var urls = '<?php echo base_url(); ?>pengendalian/insertschproyek/peralatan';
    var formdoc = Ext.widget({
        xtype: 'form',
        layout: 'form',
        url: urls,
        frame: false,
        bodyPadding: '5 5 0',
        width: 350,
        fieldDefaults: {
            msgTarget: 'side'
        },
        items: [
        {
            xtype: 'hiddenfield',
            name: 'proyek_id',
            value: proyek_id
        },
        {
            xtype: 'hiddenfield',
            name: 'id_analisa_asat',
            value: id_analisa_asat
        },
        // {
        //     xtype: 'hiddenfield',
        //     name: 'unit_id',
        //     value: unit_id
        // },
        {
            xtype: 'textfield',
            fieldLabel: 'Alat',
            afterLabelTextTpl: required,
            name: 'alat',
            value: tahap_kendali_name,
            allowBlank: false
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Unit',
            afterLabelTextTpl: required,
            name: 'unit',
            value: unit,
            allowBlank: false
        },{
            xtype: 'datefield',
            fieldLabel: 'Tanggal Awal',
            afterLabelTextTpl: required,
            name: 'tgl_awal',
            format: 'd-M-Y',
            submitFormat: 'Y-m-d',
            value: tgl_awal,
            allowBlank: false
        },{
            xtype: 'datefield',
            fieldLabel: 'Tanggal Akhir',
            afterLabelTextTpl: required,
            name: 'tgl_akhir',
            format: 'd-M-Y',
            submitFormat: 'Y-m-d',
            value:tgl_akhir,
            allowBlank: false
        },{
            xtype: 'numberfield',
            fieldLabel: 'Jumlah',
            afterLabelTextTpl: required,
            name: 'jumlah',
            value: jumlah,
            allowBlank: false
        }],

        buttons: [{
            text: 'Save',
            handler: function() {

                if (formdoc.getForm().isValid()){
                    formdoc.getForm().submit({
                        success: function(fp, o) {                          
                            storemutu.load();
                            formdoc.getForm().reset(); 
                            Ext.MessageBox.alert('Schedule Proyek','Insert successfully..!');
                            document.getElementById('gantframe').contentWindow.location.reload();
                        },
                        failure: function(fp, o){
                            Ext.MessageBox.alert('Master Data','Failed..!');  
                            formdoc.getForm().reset();                        
                        }
                    });
                    winadddoc.hide();
                }
            }
        },{
            text: 'Cancel',
            handler: function() {
               winadddoc.hide();
            }
        }]
    });

    var winadddoc = Ext.create('Ext.Window', {
        title: 'Schedule Penggunaan Peralatan',
        height: 250,
        width: 500,
        layout: 'fit',
        items: formdoc,
        modal:true
    }).show();
}


function addbobot(proyek_id, tahap_kendali_id, tahap_kendali_name){

    storeparent.load({
        params:{
            'proyek_id':proyek_id,
            'id_analisa_asat':tahap_kendali_id
        }
    });

    grid = Ext.create('Ext.grid.plugin.RowEditing',{
        clicksToEdit: 2,
        listeners:{
            afteredit: function(rec,obj) {
                var selectedNode = gridparent.getSelectionModel().getSelection();
                data = selectedNode[0].data;

                console.log(data);

                id = data.id;
                jumlah_parent = data.jumlah_parent

                Ext.Ajax.request({
                     url: '<?=base_url();?>pengendalian/update_bobot_sch_parent/peralatan',
                        method: 'POST',
                        params: {
                            'id' :  id,
                            'jumlah_parent': jumlah_parent
                            },                              
                    success: function() {
                    Ext.Msg.alert( "Status", "Update successfully..!"); 
                    storeparent.load({
                        params:{
                            'proyek_id':proyek_id,
                            'id_analisa_asat':tahap_kendali_id
                        }
                    });

                    document.getElementById('gantframe').contentWindow.location.reload();                                     
                    },
                    failure: function() {
                    Ext.Msg.alert( "Status", "No Respond..!"); 
                    }
                });

            }   
        }
    });

    //grid.on('edit', function(edit, e) {
    //    e.record.commit();
    //});

    gridparent = Ext.create('Ext.grid.Panel', {
        frame:false,
        store: storeparent,
        selModel: {
            selType: 'cellmodel'
        },plugins: [ grid ],
        columns: [
            {text: "Id", width: 80, sortable: true, dataIndex: 'id'},
            {text: "Tanggal", width: 120, sortable: true, dataIndex: 'tgl_sch_parent'},
            {text: "Jumlah", width: 120, sortable: true, dataIndex: 'jumlah_parent',editor: 'textfield'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
                recp = storeparent.getAt(rowIndex);
                var idparent = recp.get('id');

                Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
                    if(resbtn == 'yes')
                    {
                        Ext.Ajax.request({
                             url: '<?=base_url();?>pengendalian/deleteschparent/peralatan',
                                method: 'POST',
                                params: {
                                    'id' :  idparent
                                },                              
                            success: function() {
                            Ext.Msg.alert( "Status", "Delete successfully..!"); 
                            storeparent.load({
                                params:{
                                    'proyek_id':proyek_id,
                                    'id_analisa_asat':tahap_kendali_id
                                }
                            });
                            storemutu.load();
                            document.getElementById('gantframe').contentWindow.location.reload();                              
                            },
                            failure: function() {
                            Ext.Msg.alert( "Status", "No Respond..!"); 
                            }
                        });                                                                                        
                    }
                });
            }
            }
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
            {
                text:'Close',
                handler: function(){
                winparent.hide();
                }
            }]
        },
        {
            xtype: 'toolbar',
            dock: 'top',
            items: [
            /*
            {
                text:'Tambah Bobot',
                tooltip:'Tambah Bobot',
                handler: function(){
                    frma(id);
                }
            }
            */
            ]
        }],
        width: '100%',
        height: '100%'
    });

    winparent = Ext.create('Ext.Window', {
        title: 'Bobot Penggunaan Peralatan '+tahap_kendali_name,
        height: 300,
        width: 400,
        layout: 'fit',
        items: gridparent,
        modal:true
    }).show();
}
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>
