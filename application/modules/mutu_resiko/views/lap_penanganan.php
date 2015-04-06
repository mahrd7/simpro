<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript">
Ext.require([
    '*'
]);

     

	Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
        	{name: 'penanganan_risiko_id', mapping: 'penanganan_risiko_id'},
            {name: 'proyek_id', mapping: 'proyek_id'},
            {name: 'risiko', mapping: 'risiko'},            
            {name: 'nilai_risiko', mapping: 'nilai_risiko'},
            {name: 'tgl', mapping: 'tgl'},
            {name: 'user_id', mapping: 'user_id'},
            {name: 'realisasi_tindakan', mapping: 'realisasi_tindakan'},
            {name: 'rencana_penanganan', mapping: 'rencana_penanganan'},
            {name: 'tingkat_risiko', mapping: 'tingkat_risiko'},
            {name: 'tingkat_risiko_id', mapping: 'tingkat_risiko_id'},
            {name: 'status_risiko', mapping: 'status_risiko'},
            {name: 'status_risiko_id', mapping: 'status_risiko_id'},
            {name: 'realisasi_sisa_risiko', mapping: 'realisasi_sisa_risiko'},
            {name: 'realisasi_sisa_risiko_id', mapping: 'realisasi_sisa_risiko_id'},
            {name: 'target_sisa_risiko', mapping: 'target_sisa_risiko'},
            {name: 'target_sisa_risiko_id', mapping: 'target_sisa_risiko_id'},
            {name: 'pic', mapping: 'pic'},            
            {name: 'biaya_memitigasi', mapping: 'biaya_memitigasi'},
            {name: 'biaya_sisa_risiko', mapping: 'biaya_sisa_risiko'},
            {name: 'tgl_aak', mapping: 'tgl_aak'},
            {name: 'ar_id', mapping: 'ar_id'},
            {name: 'total', mapping: 'total'},
            {name: 'status', mapping: 'status'}
         ]
    });

Ext.onReady(function() {

    var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';


	var store = Ext.create('Ext.data.Store', {
        id: 'store',
        model: 'mdl',
        remoteSort: true,
        pageSize: 50,
        autoLoad: false,
        
     proxy: {
         type: 'ajax',
         url: '<?php echo base_url() ?>mutu_resiko/getdata2/lap_penanganan/<?php echo $bln;?>/<?php echo $thn;?>/',
         reader: {
             type: 'json',
             root: 'data'			 
         }
     },
	 //params:{bln:'test',thn:'test'}
    });
	store.load();
    var grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        store: store,
        autoscroll: true,
        layout: 'fit',
        title: 'Laporan Penanganan / Pengendalian Risiko (FM-MR03)',
        columns: [
            {text: "RISIKO", width:300, sortable: true, dataIndex: 'risiko'},
            {text: "SEBELUM TINDAK LANJUT",
            	columns: [            		
            		{text: "TINGKAT RISIKO", width:120, sortable: true, dataIndex: 'tingkat_risiko'},
            		{text: "NILAI RISIKO (Rp.)", width:120, sortable: true, dataIndex: 'nilai_risiko'}
            	]
        	},
            {text: "HASIL TINDAK LANJUT",
            	columns: [            		
            		{text: "REALISASI TINDAKAN S/D SAAT INI", width:280, sortable: true, dataIndex: 'realisasi_tindakan'},
            		{text: "BIAYA UNTUK MEMITIGASI (Rp.)", width:180, sortable: true, dataIndex: 'biaya_memitigasi'},
            		{text: "BIAYA SISA RISIKO (Rp.)", width:180, sortable: true, dataIndex: 'biaya_sisa_risiko'},
            		{text: "TOTAL NILAI RISIKO (Rp.)", width:180, sortable: true, dataIndex: 'total'},
            		{text: "PENANGGUNG JAWAB (PIC)", width:180, sortable: true, dataIndex: 'pic'}
            	]
        	},
            {text: "TARGET TINGKAT SISA RISIKO", width:190, sortable: true, dataIndex: 'target_sisa_risiko'},
            {text: "REALISASI TINGKAT SISA RISIKO (*)", width:220, sortable: true, dataIndex: 'realisasi_sisa_risiko'},
            {text: "STATUS RISIKO (OPEN/ CLOSE)", width:190, sortable: true, dataIndex: 'status_risiko'},
            {text: "Kontrol",
        	columns:[ 
        	{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
				if(rec.get('penanganan_risiko_id')!="bawah"){
				if (rec.get('status') == "0" && rec.get('status_risiko') == "Close" ){
					Ext.Msg.alert( "Status", "Maaf Laporan Bulanan telah di APPROVE, Anda harus menghapus approval untuk Acces Kontrol!")
                
				}else{
					frmedit.getForm().findField('editid').setValue(rec.get('penanganan_risiko_id'));
					frmedit.getForm().findField('editrisiko').setValue(rec.get('risiko'));
					frmedit.getForm().findField('edittingkat_risiko').setValue(rec.get('tingkat_risiko_id'));
					frmedit.getForm().findField('editnilai_risiko').setValue(rec.get('nilai_risiko'));
					frmedit.getForm().findField('editrealisasi_tindakan').setValue(rec.get('realisasi_tindakan'));
					frmedit.getForm().findField('editbiaya_memitigasi').setValue(rec.get('biaya_memitigasi'));
					frmedit.getForm().findField('editbiaya_sisa').setValue(rec.get('biaya_sisa_risiko'));
					frmedit.getForm().findField('editpic').setValue(rec.get('pic'));
					frmedit.getForm().findField('edittarget_tingkat_risiko').setValue(rec.get('target_sisa_risiko_id'));
					frmedit.getForm().findField('editrealisasi_tingkat_sisa').setValue(rec.get('realisasi_sisa_risiko_id'));
					frmedit.getForm().findField('editstatus_risiko').setValue(rec.get('status_risiko_id'));
					frmedit.getForm().findField('edittgl_aak').setValue('<?php echo date('Y-m-d',strtotime($thn."-".$bln."-01")) ?>');
					winedit.show();
				}
				}
            },
            getClass: function(v, meta, record) {  
                var id_r = record.get('penanganan_risiko_id');
                if(id_r == "bawah") {                                                                      
                    return 'x-hide-display';
                }
            }
            },{text: "",xtype: 'actioncolumn', width:25,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
				var id = rec.get('penanganan_risiko_id');
				if(rec.get('penanganan_risiko_id')!="bawah"){
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
					if(resbtn == 'yes')
					{
						Ext.Ajax.request({
							url: '<?=base_url();?>mutu_resiko/deletedata/lap_penanganan',
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
				}else{
					
				}
            },
            getClass: function(v, meta, record) {  
                var id_r = record.get('penanganan_risiko_id');
                if(id_r == "bawah") {                                                                      
                    return 'x-hide-display';
                }
            }
        }
        	]}
        ],
        columnLines: true,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
            	text: 'Tambah Data',
            	handler: function(){
					frmadd.getForm().findField('tgl_aak').setValue('<?php echo date('Y-m-d',strtotime($thn."-".$bln."-01")) ?>');
                    winadd.show();
            	}
            },'-',{
                text:'Print',
                tooltip:'Print',
                handler: function(){

                }
            },'-',{
                text:'Kembali',
                tooltip:'Kembali',
                handler: function(){
                    var url ='<?php echo base_url(); ?>mutu_resiko/page/pilih_lap_penanganan';
                    // console.log(url);
                    window.location=url;
                }
            }]
        },{
            dock: 'bottom',
            xtype: 'toolbar',
            items: [                
            /*'NILAI RISIKO yang terjadi : ',{
			xtype: 'label',
			forId: 'myFieldId',
			text: 'My Awesome Field',
			margins: '0 0 0 10'
		}*/
            ]
        }],
        
        bbar: Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: store,
                             displayInfo: true
                     }),
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
    

    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>mutu_resiko/insertdata/lap_penanganan',
        bodyStyle: 'padding:5px 5px 0',
        width: 500,
        autoScroll: true,
        frame: false,
            items: [{
                xtype:'textfield',
                fieldLabel: '',
                anchor: '-5',
                name: 'tgl_aak',
				hidden:true,
            },{
                xtype:'textarea',
                fieldLabel: 'RISIKO',
                anchor: '-5',
                name: 'risiko',
            afterLabelTextTpl: required,
            allowBlank: false
            },
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'TINGKAT RISIKO',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_tingkat_risiko', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Tingkat Risiko...',
				name: 'tingkat_risiko',
				typeAhead: true,
				triggerAction: 'all',
				enableKeyEvents:true,							
				selectOnFocus:true,							
				displayField: 'text',
				valueField: 'value',
				anchor: '-5',
				listeners: {
					 'select': function(combo, row, index) {
					},
				},
			}),
			{
                xtype:'textarea',
                fieldLabel: 'NILAI RISIKO',
                anchor: '-5',
                name: 'nilai_risiko',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'REALISASI TINDAKAN S/D SAAT INI',
                anchor: '-5',
                name: 'realisasi_tindakan',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'numberfield',
                fieldLabel: 'BIAYA UNTUK MEMITIGASI (Rp.)',
                anchor: '-5',
                name: 'biaya_memitigasi',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'numberfield',
                fieldLabel: ' BIAYA SISA RISIKO (Rp.)',
                anchor: '-5',
                name: 'biaya_sisa',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'PENANGGUNG JAWAB (PIC)',
                anchor: '-5',
                name: 'pic',
            afterLabelTextTpl: required,
            allowBlank: false
            },
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'TARGET TINGKAT SISA RISIKO',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_sisa_risiko', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Tingkat Sisa Risiko...',
				name: 'target_tingkat_risiko',
				typeAhead: true,
				triggerAction: 'all',
				enableKeyEvents:true,							
				selectOnFocus:true,							
				displayField: 'text',
				valueField: 'value',
				anchor: '-5',
				listeners: {
					 'select': function(combo, row, index) {
					},
				},
			}),
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'REALISASI TINGKAT SISA RISIKO (*)',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_sisa_risiko', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Realisasi Tingkat Risiko...',
				name: 'realisasi_tingkat_sisa',
				typeAhead: true,
				triggerAction: 'all',
				enableKeyEvents:true,							
				selectOnFocus:true,							
				displayField: 'text',
				valueField: 'value',
				anchor: '-5',
				listeners: {
					 'select': function(combo, row, index) {
					},
				},
			}),
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'STATUS RISIKO (OPEN/ CLOSE)',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_status_risiko', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Status Risiko...',
				name: 'status_risiko',
				typeAhead: true,
				triggerAction: 'all',
				enableKeyEvents:true,							
				selectOnFocus:true,							
				displayField: 'text',
				valueField: 'value',
				anchor: '-5',
				listeners: {
					 'select': function(combo, row, index) {
					},
				},
			})],
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
        url: '<?php echo base_url() ?>mutu_resiko/editdata/lap_penanganan',
        bodyStyle: 'padding:5px 5px 0',
        width: 500,
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
                fieldLabel: '',
                anchor: '-5',
                name: 'edittgl_aak',
				hidden:true,
            },{
                xtype:'textarea',
                fieldLabel: 'RISIKO',
                anchor: '-5',
                name: 'editrisiko',
            afterLabelTextTpl: required,
            allowBlank: false
            },
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'TINGKAT RISIKO',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_tingkat_risiko', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Tingkat Risiko...',
				name: 'edittingkat_risiko',
				typeAhead: true,
				triggerAction: 'all',
				enableKeyEvents:true,							
				selectOnFocus:true,							
				displayField: 'text',
				valueField: 'value',
				anchor: '-5',
				listeners: {
					 'select': function(combo, row, index) {
					},
				},
			}),{
                xtype:'textarea',
                fieldLabel: 'NILAI RISIKO',
                anchor: '-5',
                name: 'editnilai_risiko',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'REALISASI TINDAKAN S/D SAAT INI',
                anchor: '-5',
                name: 'editrealisasi_tindakan',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'numberfield',
                fieldLabel: 'BIAYA UNTUK MEMITIGASI (Rp.)',
                anchor: '-5',
                name: 'editbiaya_memitigasi',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'numberfield',
                fieldLabel: 'BIAYA SISA RISIKO (Rp.)',
                anchor: '-5',
                name: 'editbiaya_sisa',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'PENANGGUNG JAWAB (PIC)',
                anchor: '-5',
                name: 'editpic',
            afterLabelTextTpl: required,
            allowBlank: false
            },
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'TARGET TINGKAT SISA RISIKO',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_sisa_risiko', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Tingkat Sisa Risiko...',
				name: 'edittarget_tingkat_risiko',
				typeAhead: true,
				triggerAction: 'all',
				enableKeyEvents:true,							
				selectOnFocus:true,							
				displayField: 'text',
				valueField: 'value',
				anchor: '-5',
				listeners: {
					 'select': function(combo, row, index) {
					},
				},
			}),
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'REALISASI TINGKAT SISA RISIKO (*)',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_sisa_risiko', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Realisasi Tingkat Risiko...',
				name: 'editrealisasi_tingkat_sisa',
				typeAhead: true,
				triggerAction: 'all',
				enableKeyEvents:true,							
				selectOnFocus:true,							
				displayField: 'text',
				valueField: 'value',
				anchor: '-5',
				listeners: {
					 'select': function(combo, row, index) {
					},
				},
			}),
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'STATUS RISIKO (OPEN/ CLOSE)',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_status_risiko', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Status Risiko...',
				name: 'editstatus_risiko',
				typeAhead: true,
				triggerAction: 'all',
				enableKeyEvents:true,							
				selectOnFocus:true,							
				displayField: 'text',
				valueField: 'value',
				anchor: '-5',
				listeners: {
					 'select': function(combo, row, index) {
					},
				},
			})],
        buttons: ['->', {
            text: 'Update',
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

    var winadd = Ext.create('Ext.Window', {
        title: 'Tambah',
        closeAction: 'hide',
        width: 500,
        height: 400,
        layout: 'fit',
        modal: true,
        items: frmadd 
    });

    var winedit = Ext.create('Ext.Window', {
        title: 'Edit',
        closeAction: 'hide',
        width: 500,
        height: 400,
        layout: 'fit',
        modal: true,
        items: frmedit 
    });
});
</script>

</head>
<body>
<div id="form-ct"></div>
</body>
</html>