<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript">

        Ext.require([
            '*'
        ]);

	Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'no', mapping: 'no'},
            {name: 'jenis_material', mapping: 'jenis_material'},
            {name: 'nama_supplier', mapping: 'nama_supplier'},            
            {name: 'satuan', mapping: 'satuan'},
            {name: 'harga_satuan', mapping: 'harga_satuan'},
            {name: 'SuratPenawaran', mapping: 'SuratPenawaran'},
            {name: 'rekanan_yang diusulkan', mapping: 'SuratPenawaran'},
            {name: 'keterangan', mapping: 'keterangan'}
        
         ]
    });

    Ext.define('mdlrbk', {
        extend: [
            {name: 'no', mapping: 'no'},
            {name: 'jenis_material', mapping: 'jenis_material'},
            {name: 'nama_supplier', mapping: 'nama_supplier'},            
            {name: 'satuan', mapping: 'satuan'},
            {name: 'harga_satuan', mapping: 'harga_satuan'},
            {name: 'SuratPenawaran', mapping: 'SuratPenawaran'},
            {name: 'rekanan_yang diusulkan', mapping: 'SuratPenawaran'},
            {name: 'keterangan', mapping: 'keterangan'}
        
         ]
    });

    Ext.define('mdlrbk', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'jenis_material', mapping: 'jenis_material'}
         ]
    });

function gridchecklist(kode){
    var storealat = Ext.create('Ext.data.Store', {
        id: 'storechecklist',
        model: 'mdlrbk',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>rbk/getdatasearch/checklist?param='+kode,
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });
    storechecklist.load();

    var storechecklist = Ext.create('Ext.data.Store', {
        id: 'storechecklist',
        model: 'mdlrbk',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>admin/getdatasearch/alat',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });
    storetoko.load();

    var gridchecklist = Ext.create('Ext.grid.Panel', {
        id:'gridalat',
        store: storealat,
        columns: [
             {name: 'no', mapping: 'no'},
            {name: 'jenis_material', mapping: 'jenis_material'},
            {name: 'nama_supplier', mapping: 'nama_supplier'},            
            {name: 'satuan', mapping: 'satuan'},
            {name: 'harga_satuan', mapping: 'harga_satuan'},
            {name: 'SuratPenawaran', mapping: 'SuratPenawaran'},
            {name: 'rekanan_yang diusulkan', mapping: 'SuratPenawaran'},
            {name: 'keterangan', mapping: 'keterangan'}
                 
                          ],
        columnLines: true,
        width: '100%',
        height: '100%',
        bbar: Ext.create('Ext.PagingToolbar', {
                store: storechecklist,
                displayInfo: true,
                displayMsg: 'Displaying data {0} - {1} of {2}',
                emptyMsg: "No data to display",
            }),
        dockedItems: [{
            xtype: 'toolbar',
            
            dock: 'bottom',
            items: [
            {
                text:'Edit',
                handler: function(){
                    wineditanalisa.show();
                }
            },{
                text:'Cancel',
                handler: function(){
                    winanalisa.hide();
                }
            }]
        }],
        listeners:{
                beforerender:function(){
                    storechecklist.load();
                }
            }
    });

    var sm = Ext.create('Ext.selection.CheckboxModel');
    var grideditchecklist = Ext.create('Ext.grid.Panel', {
        id:'grideditalat',
        store: storechecklist,
        selModel: sm,
        columns: [
             {name: 'no', mapping: 'no'},
            {name: 'jenis_material', mapping: 'jenis_material'},
            {name: 'nama_supplier', mapping: 'nama_supplier'},            
            {name: 'satuan', mapping: 'satuan'},
            {name: 'harga_satuan', mapping: 'harga_satuan'},
            {name: 'SuratPenawaran', mapping: 'SuratPenawaran'},
            {name: 'rekanan_yang diusulkan', mapping: 'SuratPenawaran'},
            {name: 'keterangan', mapping: 'keterangan'}
                 
        ],
        columnLines: true,
        width: '100%',
        height: '100%',
        bbar: Ext.create('Ext.PagingToolbar', {
                store: storechecklist,
                displayInfo: true,
                displayMsg: 'Displaying data {0} - {1} of {2}',
                emptyMsg: "No data to display",
            }),
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
            {
                text:'Add',
                handler: function(){
                }
            },{
                text:'Cancel',
                handler: function(){
                    wineditchecklist.hide();
                }
            }]
        }],
        listeners:{
                beforerender:function(){
                    storechecklist.load();
                }
            }
    });

    winanalisa = Ext.create('Ext.Window', {
        title: 'CHECKLIST DOKUMEN',
        closeAction: 'hide',
        width: 400,
        height: 300,
        layout: 'fit',
        items: gridchecklist 
    }).show();

    wineditanalisa = Ext.create('Ext.Window', {
        title: 'CHECKLIST DOKUMEN',
        closeAction: 'hide',
        width: 400,
        height: 300,
        layout: 'fit',
        items: gridchecklist
    });
}

Ext.onReady(function() {
     
    var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        pageSize: 50,  
        remoteFilter: true,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>admin/getdata/checklistdocument',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });
    store.load();

    var grid = Ext.create('Ext.grid.Panel', {
        id:'button-grid',
        title: 'FM-CDPR',
        store: store,
        columns: [
            {text: "no", flex:1, sortable: true, dataIndex: 'no'},
            {text: "jenis_peralatan_kendaraan", flex:1, sortable: true, dataIndex: 'jenis_peralatan_kendaraan'},
             {text: "spesofikasi_peralatan_kendaraann", flex:1, sortable: true, dataIndex: 'spesofikasi_peralatan_kendaraan'},
              {text: "volume", flex:1, sortable: true, dataIndex: 'volume'},
              {text: "satuan", flex:1, sortable: true, dataIndex: 'satuan'},
               {text: "harga_satuan", flex:1, sortable: true, dataIndex: 'harga_satuan'},
                {text: "jumlah_harga", flex:1, sortable: true, dataIndex: 'jumlah_harga'},
            
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('jenis_peralatan');
				var kode = rec.get('jenis_peralatan');
				var nama = rec.get('_satuan');
				// Ext.get('edit ').setValue( nama);
				frmedit.getForm().findField('editid').setValue(id);
            	frmedit.getForm().findField('editalat').setValue(kode);
				frmedit.getForm().findField(satuan).setValue(nama);
            	winedit.show();
            }
        },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('no_id');
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>admin/deletedata/skbdn',
									method: 'POST',
									params: {
										'id' :  id
									},								
									success: function() {
									store.load();
									Ext.Msg.alert( "Status", "Delete successfully..!", function(){	
									});											
									},
									failure: function() {
									}
								});			   																			
							}
				});
            }
        },
        	{text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/application_go.png',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
                var kode = rec.get('jenis_alat');
                gridalat(kode);
            }
        }
        ],
        columnLines: true,
        width: '100%',
        height: '100%',
       	bbar: Ext.create('Ext.PagingToolbar', {
				store: store,
                pageSize: 50,
				displayInfo: true,
				displayMsg: 'Displaying data {0} - {1} of {2}',
				emptyMsg: "No data to display",
			}),
       	dockedItems: [
        
        {
            xtype: 'toolbar',
            dock: 'top',
            items: [
            {
                text:'Tambah Data',
                handler: function(){
                	winadd.show();
                }
            }]
        }],
        listeners:{
				beforerender:function(){
					store.load();
				}
			}
    });
    grid.render(document.body);

   var frmadd = Ext.create('Ext.form.Panel', {    	
        url: '<?php echo base_url() ?>rbk/insertdata/alat',
        id:'frmadd ',
        bodyStyle: 'padding:5px 5px 0',
        width: '130%',
        autoScroll: true,
        fieldDefaults: {
           // labelAlign: 'top',
           // msgTarget: 'side'
        },
        defaults: {
            border: false,
            xtype: 'panel',
            flex: 1,
           // layout: 'anchor'
        },

        layout: 'hbox',
        items: [{
            items: [{
                xtype:'textfield',
                fieldLabel: 'Jenis Peralatan / Kendaraan',
                anchor: '-5',
                name: 'Jenis_peralatan_kendaraan'
            },{
                xtype:'textfield',
                fieldLabel: 'Spesifikasi Peralatan / Kendaraan',
                anchor: '-5',
                name: 'spesifikasi_peralatan_kendaraan'
            },{
                xtype:'textfield',
                fieldLabel: 'Volume',
                anchor: '-5',
                name: 'volume'
            }, {
                xtype:'textfield',
                fieldLabel: 'Satuan',
                anchor: '-5',
                name: 'satuan'
            
            },{
                xtype:'textfield',
                fieldLabel: 'Harga Satuan',
                anchor: '-5',
                name: 'harga_satuan'
            },
        ]
        }],
        buttons: ['->', {
            text: 'Save',
            handler: function() {                    
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        success: function(fp, o) {
                            Ext.MessageBox.alert('rbk','Insert successfully..!');
                        	form.reset();                        	
                    		store.load();
                        }
                    });                            
            		winadd.hide();
                }  

            }
        }, {
            text: 'Cancel',
            handler: function(){
            	winadd.hide();
            }
        }]
    });
	
    var frmedit = Ext.create('Ext.form.Panel', {    	
        url: '<?php echo base_url(); ?>rbk/editdata/alat',
        id:'frmedit ',
        bodyStyle: 'padding:5px 5px 0',
        width: '100%',
        autoScroll: true,
        fieldDefaults: {
            //labelAlign: 'top',
            //msgTarget: 'side'
        },
        defaults: {
            border: false,
            xtype: 'panel',
            flex: 1,
           // layout: 'anchor'
        },

        layout: 'hbox',
        items: [{
            items: [{
                xtype:'textfield',
                fieldLabel: 'Jenis Peralatan / Kendaraan',
                anchor: '-5',
                name: 'Jenis_peralatan_kendaraan'
            },{
                xtype:'textfield',
                fieldLabel: 'Spesifikasi Peralatan / Kendaraan',
                anchor: '-5',
                name: 'spesifikasi_peralatan_kendaraan'
            },{
                xtype:'textfield',
                fieldLabel: 'Volume',
                anchor: '-5',
                name: 'volume'
            }, {
                xtype:'textfield',
                fieldLabel: 'Satuan',
                anchor: '-5',
                name: 'Satuan'
            
            },{
                xtype:'textfield',
                fieldLabel: 'Harga Satuan',
                anchor: '-5',
                name: 'harga_satuan'
            }
        ]
        }],
        buttons: ['->', {
            text: 'Save',
            handler: function() {                    
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        success: function(fp, o) {
                            Ext.MessageBox.alert('rbk','Update successfully..!');
                        	form.reset();                        	
                    		store.load();
                        }
                    });                            
            		winedit.hide();
                }  

            }
        }, 
        {
            text: 'Cancel',
            handler: function(){
            	winedit.hide();
            }
        }],
    
    });

    winadd = Ext.create('Ext.Window', {
        title: 'Form Tambah',
        closeAction: 'hide',
		width: 300,
		height: 250,
		layout: 'fit',
        items: frmadd 
    });

    winedit = Ext.create('Ext.Window', {
        title: 'Edit',
        closeAction: 'hide',
		width: 300,
		height: 200,
		layout: 'fit',
        items: frmedit 
    });
  

});

</script>

<div style="padding: 10px;">
<h2 align="center">RINCIAN RENCANA PERALATAN, KENDRAAN & INVESTASI EXTRA COMBTABLE</h2>
</div>
<div id="form-ct"></div>
