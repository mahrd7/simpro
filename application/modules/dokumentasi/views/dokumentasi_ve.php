<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
    <script type="text/javascript">

        Ext.require([
            '*'
            ]);

        var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';



        Ext.define('analisabln', {
            extend: 'Ext.data.Model',
            fields: [
            {name: 'value', mapping: 'value', type:'string'},
            {name: 'text', mapping: 'text', type:'string'}
            ]
        });

        var storebln = Ext.create('Ext.data.Store', {
            model: 'analisabln',
            pageSize: 50,  
            remoteFilter: true,
            autoLoad: true,
            
            proxy: {
               type: 'ajax',
               url: '<?php echo base_url() ?>dokumentasi/getbulan',
               reader: {
                   type: 'json',
                   root: 'data'
               }
           }
       });

        var storethn = Ext.create('Ext.data.Store', {
            model: 'analisabln',
            pageSize: 50,  
            remoteFilter: true,
            autoLoad: true,
            
            proxy: {
               type: 'ajax',
               url: '<?php echo base_url() ?>dokumentasi/gettahun',
               reader: {
                   type: 'json',
                   root: 'data'
               }
           }
       });

        Ext.define('analisadoc', {
            extend: 'Ext.data.Model',
            fields: [
            {name: 'foto_no', mapping: 'foto_no'},
            {name: 'file', mapping: 'file'},
            {name: 'foto_proyek_tgl', mapping: 'foto_proyek_tgl'},
            {name: 'foto_proyek_judul', mapping: 'foto_proyek_judul'},
            {name: 'foto_proyek_keterangan', mapping: 'foto_proyek_keterangan'},
            {name: 'no_spk', mapping: 'no_spk'},
            {name: 'tglmonth', mapping: 'tglmonth'},
            {name: 'tglyear', mapping: 'tglyear'}
            ]
        });


        var storedoc = Ext.create('Ext.data.Store', {
            model: 'analisadoc',
            pageSize: 50,  
            remoteFilter: true,
            autoLoad: false,
            
            proxy: {
               type: 'ajax',
               url: '<?php echo base_url() ?>dokumentasi/getdok/dokumentasi_ve',
               reader: {
                   type: 'json',
                   root: 'data'
               }
           }
       });

        Ext.onReady(function() {
            
            storedoc.load();

            Ext.QuickTips.init();

    ////////////////////////////////////////////////////////////////////////////////////////
    // Grid 4
    ////////////////////////////////////////////////////////////////////////////////////////
    var grid4 = Ext.create('Ext.grid.Panel', {
        store: storedoc,
        columns: [
        {text: "Foto Bulan", width: 120, sortable: true, dataIndex: 'foto_proyek_tgl'},
        {text: "Foto", width: 350, sortable: true, dataIndex: 'foto_proyek_judul',editor: 'textfield',
        renderer:function(value){
            val = '<center><img src=<?php echo base_url() ?>uploads/'+value+' width="200px" height="150px"></center>';
            return val;
        },
        listeners:{
            click:function(a,b,c){
                rec = storedoc.getAt(c);
                var file = rec.get('foto_proyek_judul');
                fnViewFile(file);
            }
        }
    },
    {text: "Keterangan", width: 120, sortable: true, dataIndex: 'foto_proyek_keterangan'},
    {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
    handler: function(grid, rowIndex, colIndex){        
        rec = storedoc.getAt(rowIndex);
        var id = rec.get('foto_no');
        var file = rec.get('foto_proyek_judul');
        Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
         if(resbtn == 'yes')
         {
            Ext.Ajax.request({
               url: '<?=base_url();?>dokumentasi/deletedok/dokumentasi_ve',
               method: 'POST',
               params: {
                  'id':id,
                  'file':file
              },								
              success: function() {
               storedoc.load();
               Ext.Msg.alert( "Status", "Delete successfully..!", function(){	
               });											
           },
           failure: function() {
           }
       });			   																			
        }
    });
    }
}
,
{text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/cog.gif',
handler: function(grid, rowIndex, colIndex){            
    rec = storedoc.getAt(rowIndex); 
    id = rec.get('foto_no');
    file = rec.get('foto_proyek_judul');
    foto = rec.get('foto_proyek_tgl');
    tgl = rec.get('foto_proyek_judul');
    ket = rec.get('foto_proyek_keterangan');
    no_spk = rec.get('no_spk');
    tglmonth = rec.get('tglmonth');
    tglyear = rec.get('tglyear');
    dokedit(id,file,foto,tgl,ket,no_spk,tglmonth,tglyear);
}}
],
columnLines: true,

        // inline buttons
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Tambah Data',
                tooltip:'Tambah Data',
                handler: function(){                    
                    winadddoc.show();
                }
            }]
        }],

        width: '100%',
        height: '100%',
        frame: true,
        title: 'Dokumentasi',
        iconCls: 'icon-grid',
        renderTo: Ext.getBody()
    });

      // console.log(urls);
      var formdoc = Ext.widget({
        xtype: 'form',
        layout: 'form',
        //title: 'Insert Dokumentasi',
        url: '<?php echo base_url(); ?>dokumentasi/insertdok/dokumentasi_ve',
        frame: false,
        bodyPadding: '5 5 0',
        width: 350,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 75
        },
        items: [{
            xtype: 'combobox',
            fieldLabel: 'Bulan',
            name: 'blndoc',
            store: storebln,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih Bulan...',
            allowBlank: false,
            value: new Date().getMonth().toString()
        },{
            xtype: 'combobox',
            fieldLabel: 'Tahun',
            name: 'thndoc',
            store: storethn,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Select a state...',
            allowBlank: false,
            value: new Date().getFullYear().toString()
        },{
            xtype: 'textfield',
            fieldLabel: 'Keterangan',
            afterLabelTextTpl: required,
            name: 'ketdoc',
            allowBlank: false
        },{
            xtype: 'filefield',
            emptyText: 'Select an image',
            afterLabelTextTpl: required,
            fieldLabel: 'Photo',
            name: 'photo-path',
            buttonText: 'pilih file',
            allowBlank: false
        }],

        buttons: [{
            text: 'Save',
            handler: function(){
                var form = this.up('form').getForm();                
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        enctype: 'multipart/form-data',
                        waitMsg: 'Uploading your photo...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Dokumentasi Foto','Upload successfully..!');
                            storedoc.load();
                        }
                    });
                    winadddoc.hide();
                }
            }
        }
        ,{
            text: 'Cancel',
            handler: function() {
             winadddoc.hide();
         }
     }]
 });


var winadddoc = Ext.create('Ext.Window', {
    title: 'Dokumentasi',
    closeAction: 'hide',
    height: 270,
    width: 400,
    layout: 'fit',
    items: formdoc
});

});

function dokedit(id,file,foto,tgl,ket,no_spk,tglmonth,tglyear){
    var formedit = Ext.widget({
        xtype: 'form',
        layout: 'form',
        //title: 'Insert Dokumentasi',
        url: '<?php echo base_url(); ?>dokumentasi/insertdok/dokumentasi_foto',
        frame: false,
        bodyPadding: '5 5 0',
        width: 350,
        fieldDefaults: {
            msgTarget: 'side',
            labelWidth: 75
        },
        items: [{
            xtype: 'hidden',
            fieldLabel: 'Id',
            afterLabelTextTpl: required,
            name: 'id',
            allowBlank: false,
            value: id
        },{
            xtype: 'combobox',
            fieldLabel: 'Bulan',
            name: 'blndoc',
            store: storebln,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Pilih Bulan...',
            value: tglmonth,
            allowBlank: false
        },{
            xtype: 'combobox',
            fieldLabel: 'Tahun',
            name: 'thndoc',
            store: storethn,
            valueField: 'value',
            displayField: 'text',
            typeAhead: true,
            queryMode: 'local',
            emptyText: 'Select a tahun...',
            value: tglyear,
            allowBlank: false
        },{
            xtype: 'textfield',
            fieldLabel: 'Keterangan',
            afterLabelTextTpl: required,
            name: 'ketdoc',
            allowBlank: false,
            value: ket
        },{
            xtype: 'filefield',
            emptyText: 'Select an image',
            fieldLabel: 'Photo',
            name: 'photo-path',
            buttonText: 'pilih file'
        }],

        buttons: [{
            text: 'Update',
            handler: function(){            
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        enctype: 'multipart/form-data',
                        waitMsg: 'Uploading your photo...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Dokumentasi Foto','Upload successfully..!');
                            storedoc.load();
                        }
                    });
                    windocedit.hide();
                }
            }
        }
        ,{
            text: 'Cancel',
            handler: function() {
             windocedit.hide();
         }
     }]
 });


var windocedit = Ext.create('Ext.Window', {
    title: 'Dokumentasi',
    closeAction: 'hide',
    height: 270,
    width: 400,
    layout: 'fit',
    modal:true,
    items: formedit
}).show();
}

function fnViewFile(filename)
{
    var panelFile = Ext.create('Ext.panel.Panel', { 
        layout: 'fit',
        items: Ext.create('Ext.Img', {
            src: '<?=base_url();?>uploads/'+filename,
        })
    });
    var winViewFile = Ext.create('Ext.Window', {
        title: 'View File',
        closeAction: 'hide',
        height: '90%',
        width: '60%',
        layout: 'fit',
        modal: true,
        items: panelFile
    }).show();     
}
</script>

</head>
<body>
    <div id="form-ct"></div>
</body>
</html>
