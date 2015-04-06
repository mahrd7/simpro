<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
    <script type="text/javascript" language="JavaScript" src="../../assets/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript">

        Ext.require([
            '*'
            ]);

        var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';  

        Ext.define('mdl', {
            extend: 'Ext.data.Model',
            fields: [
            {name: 'daftar_risiko_id', mapping: 'daftar_risiko_id'},
            {name: 'no_spk', mapping: 'no_spk'},
            {name: 'konteks', mapping: 'konteks'},
            {name: 'akibat', mapping: 'akibat'},            
            {name: 'penyebab', mapping: 'penyebab'},
            {name: 'kemungkinan_terjadi', mapping: 'kemungkinan_terjadi'},
            {name: 'faktor_positif', mapping: 'faktor_positif'},
            {name: 'tingkat_akibat', mapping: 'tingkat_akibat'},            
            {name: 'tingkat_kemungkinan', mapping: 'tingkat_kemungkinan'},       
            {name: 'tingkat_risiko', mapping: 'tingkat_risiko'},
            {name: 'tingkat_akibat_no', mapping: 'tingkat_akibat_no'},            
            {name: 'tingkat_kemungkinan_no', mapping: 'tingkat_kemungkinan_no'},       
            {name: 'tingkat_risiko_no', mapping: 'tingkat_risiko_no'},
            {name: 'prioritas', mapping: 'prioritas'},  
            {name: 'ar_id', mapping: 'ar_id'},
            {name: 'tgl', mapping: 'tgl'},
            {name: 'user_id', mapping: 'user_id'},
            {name: 'status', mapping: 'status'},
            {name: 'status_risiko', mapping: 'status_risiko'}
            ]
        });

var datajenis = [
{"text":"Rendah / Kecil","value":"0"},
{"text":"Menengah / Sedang","value":"1"},
{"text":"Tinggi / Besar","value":"2"}
];

Ext.onReady(function() {

    var storejenis_akibat = Ext.create('Ext.data.Store', {
        fields: [
        {name: 'text'},
        {name: 'value'},
        ],
        remoteFilter: true,
        autoLoad: true,
        proxy: { 
            type: 'ajax', 
            url: '<?=base_url();?>mutu_resiko/get_tingkat_akibat', 
            reader: { 
                root: 'data',
                type: 'json' 
            } 
        }
    });

    var storejenis_kemungkinan = Ext.create('Ext.data.Store', {
        fields: [
        {name: 'text'},
        {name: 'value'},
        ],
        remoteFilter: true,
        autoLoad: true,
        proxy: { 
            type: 'ajax', 
            url: '<?=base_url();?>mutu_resiko/get_tingkat_kemungkinan', 
            reader: { 
                root: 'data',
                type: 'json' 
            } 
        } 
    });

    var storejenis_risiko = Ext.create('Ext.data.Store', {
        fields: [
        {name: 'text'},
        {name: 'value'},
        ],
        remoteFilter: true,
        autoLoad: true,
        proxy: { 
            type: 'ajax', 
            url: '<?=base_url();?>mutu_resiko/get_tingkat_risiko', 
            reader: { 
                root: 'data',
                type: 'json' 
            } 
        }
    });
    
    var store = Ext.create('Ext.data.Store', {
        model: 'mdl',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,        
        proxy: {
           type: 'ajax',
           url: '<?php echo base_url() ?>mutu_resiko/getdata/daftar_resiko',
           reader: {
               type: 'json',
               root: 'data'
           }
       }
   });

    var grid = Ext.create('Ext.grid.Panel', {
      title: 'Daftar Resiko',
      id:'button-grid',
      store: store,
      columns: [
      {text: "IDENTIFIKASI RISIKO", flex:1, sortable: false,
      columns:[
      {text: "KONTEKS / PERISTIWA", flex:1, sortable: true, dataIndex: 'konteks'},
      {text: "PENYEBAB UTAMA RISIKO", flex:1, sortable: true, dataIndex: 'penyebab'},
      {text: "AKIBAT", flex:1, sortable: true, dataIndex: 'akibat'},
      {text: "KEMUNGKINAN TERJADI", flex:1, sortable: true, dataIndex: 'kemungkinan_terjadi'}
      ]
  },
  {text: "FAKTOR POSITIF YANG ADA (UNTUK MENGENDALIKAN RISIKO)", flex:1, sortable: true, dataIndex: 'faktor_positif'},
  {text: "TINGKAT AKIBAT", flex:1, sortable: true, dataIndex: 'tingkat_akibat'},
  {text: "TINGKAT KEMUNGKINAN", flex:1, sortable: true, dataIndex: 'tingkat_kemungkinan'},
  {text: "TINGKAT RISIKO", flex:1, sortable: true, dataIndex: 'tingkat_risiko'},
  {text: "PRIORITAS RISIKO", flex:1, sortable: true, dataIndex: 'prioritas'},
  {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
  handler: function(grid, rowIndex, colIndex){
    var rec = store.getAt(rowIndex);
    if (rec.get('status') == "0" && rec.get('status_risiko') == "Close" ){
       Ext.Msg.alert( "Status", "Maaf Daftar Risiko Telah di APPROVE, Anda harus menghapus approval untuk Acces Kontrol!")

   }else{
       frmedit.getForm().findField('editid').setValue(rec.get('daftar_risiko_id'));
       frmedit.getForm().findField('editkonteks').setValue(rec.get('konteks'));
       frmedit.getForm().findField('editpenyebab').setValue(rec.get('penyebab'));
       frmedit.getForm().findField('editakibat').setValue(rec.get('akibat'));
       frmedit.getForm().findField('editkemungkinan_terjadi').setValue(rec.get('kemungkinan_terjadi'));
       frmedit.getForm().findField('editfaktor_positif').setValue(rec.get('faktor_positif'));
       frmedit.getForm().findField('edittingkat_akibat').setValue(rec.get('tingkat_akibat_no'));
       frmedit.getForm().findField('edittingkat_kemungkinan').setValue(rec.get('tingkat_kemungkinan_no'));
       frmedit.getForm().findField('edittingkat_risiko').setValue(rec.get('tingkat_risiko_no'));
       frmedit.getForm().findField('editprioritas').setValue(rec.get('prioritas'));
       frmedit.getForm().findField('editar_id').setValue(rec.get('ar_id'));
       winedit.show();
   }
}
}
],
columnLines: true,
width: '100%',
height: '100%',
bbar: Ext.create('Ext.toolbar.Paging', {
   pageSize: 50,
   store: store,
   displayInfo: true
})
});
store.load();
grid.render(document.body);

var frmedit = Ext.create('Ext.form.Panel', {     
    url: '<?php echo base_url() ?>mutu_resiko/editdata/daftar_risiko',
    id:'frmedit',
    bodyStyle: 'padding:5px 5px 0',
    width: '100%',
    autoScroll: true,
    frame: false,
    items: [{
        xtype:'textfield',
        fieldLabel: 'Id',
        anchor: '-5',
        name: 'editid',
        hidden: true
    },{
        xtype:'textfield',
        fieldLabel: 'ar_id',
        anchor: '-5',
        name: 'editar_id',
        hidden: true
    },{
        xtype:'textfield',
        fieldLabel: 'KONTEKS / PERISTIWA',
        anchor: '-5',
        name: 'editkonteks',
        afterLabelTextTpl: required,
        allowBlank: false,
        readOnly:true
    },{
        xtype:'textarea',
        fieldLabel: ' PENYEBAB UTAMA RISIKO',
        anchor: '-5',
        name: 'editpenyebab',
        afterLabelTextTpl: required,
        allowBlank: false
    },{
        xtype:'textfield',
        fieldLabel: 'AKIBAT',
        anchor: '-5',
        name: 'editakibat',
        afterLabelTextTpl: required,
        allowBlank: false,
        readOnly:true
    },{
        xtype:'numberfield',
        fieldLabel: 'KEMUNGKINAN TERJADI',
        anchor: '-5',
        name: 'editkemungkinan_terjadi',
        afterLabelTextTpl: required,
        allowBlank: false
    },{
        xtype:'textarea',
        fieldLabel: 'FAKTOR POSITIF YANG ADA',
        anchor: '-5',
        name: 'editfaktor_positif',
        afterLabelTextTpl: required,
        allowBlank: false
    },{
        xtype:'combobox',
        fieldLabel: 'TINGKAT AKIBAT',
        anchor: '-5',
        name: 'edittingkat_akibat',
        store: storejenis_akibat,
        valueField: 'value',
        displayField: 'text',
        typeAhead: true,
        queryMode: 'local',
        emptyText: 'Pilih..',
        afterLabelTextTpl: required,
        allowBlank: false
    },{
        xtype:'combobox',
        fieldLabel: 'TINGKAT KEMUNGKINAN',
        anchor: '-5',
        name: 'edittingkat_kemungkinan',
        store: storejenis_kemungkinan,
        valueField: 'value',
        displayField: 'text',
        typeAhead: true,
        queryMode: 'local',
        emptyText: 'Pilih..',
        afterLabelTextTpl: required,
        allowBlank: false
    },{
        xtype:'combobox',
        fieldLabel: 'TINGKAT RISIKO',
        anchor: '-5',
        name: 'edittingkat_risiko',
        store: storejenis_risiko,
        valueField: 'value',
        displayField: 'text',
        typeAhead: true,
        queryMode: 'local',
        emptyText: 'Pilih..',
        afterLabelTextTpl: required,
        allowBlank: false
    },{
        xtype:'textfield',
        fieldLabel: ' PRIORITAS RISIKO',
        anchor: '-5',
        name: 'editprioritas',
        afterLabelTextTpl: required,
        allowBlank: false
    }],
    buttons: ['->', {
        text: 'Save',
        handler: function() {                    
            var form = this.up('form').getForm();
            if(form.isValid()){
                form.submit({
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Master Data','Insert successfully..!');
                        form.reset();                           
                        store.load();
                    }
                });                            
                winedit.hide();
            }  

        }
    }, {
        text: 'Cancel',
        handler: function(){
            winedit.hide();
        }
    }]
});

var winedit = Ext.create('Ext.Window', {
    title: 'Edit',
    modal: true,
    closeAction: 'hide',
    width: 500,
    height: 400,
    layout: 'fit',
    items: frmedit
});
});
</script>

</head>
<body>
    <div id="form-ct"></div>
</body>
</html>