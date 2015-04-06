<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript" language="JavaScript" src="../../assets/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript">

Ext.require([
    '*'
]);

	Ext.define('mdl', {
        extend: 'Ext.data.Model',
        fields: [
        	{name: 'ar_id', mapping: 'ar_id'},
            {name: 'proyek_id', mapping: 'proyek_id'},
            {name: 'risiko', mapping: 'risiko'},
            {name: 'akibat', mapping: 'akibat'},            
            {name: 'analisis', mapping: 'analisis'},
            {name: 'rencana_penanganan', mapping: 'rencana_penanganan'},
            {name: 'batas_waktu', mapping: 'batas_waktu'},
            {name: 'keputusan', mapping: 'keputusan'},
            {name: 'tingkat_akibat', mapping: 'tingkat_akibat'},            
            {name: 'tingkat_akibat_id', mapping: 'tingkat_akibat_id'},            
            {name: 'tingkat_kemungkinan', mapping: 'tingkat_kemungkinan'},       
            {name: 'tingkat_kemungkinan_id', mapping: 'tingkat_kemungkinan_id'},       
            {name: 'tingkat_risiko', mapping: 'tingkat_risiko'},
            {name: 'tingkat_risiko_id', mapping: 'tingkat_risiko_id'},
            {name: 'sisa_risiko_id', mapping: 'sisa_risiko_id'},  
            {name: 'sisa_risiko', mapping: 'sisa_risiko'},  
            {name: 'pic', mapping: 'pic'},
            {name: 'tgl', mapping: 'tgl'},
            {name: 'user_id', mapping: 'user_id'},
			{name: 'status', mapping: 'status'},
			{name: 'status_risiko', mapping: 'status_risiko'}
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
         url: '<?php echo base_url() ?>mutu_resiko/getdata/identifikasi_resiko',
         reader: {
             type: 'json',
             root: 'data'
         }
     }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        id:'button-grid',
        store: store,
        autoScroll: true,
        columns: [
            {text: "RISIKO", flex:1, sortable: true, dataIndex: 'risiko'},
            {text: "AKIBAT", flex:1, sortable: true, dataIndex: 'akibat'},
            {text: "ANALISIS AKIBAT YANG DIPERTIMBANGKAN & FAKTOR POSITIF YANG ADA", flex:1, sortable: true, dataIndex: 'analisis'},
            {text: "SEBELUM TINDAKAN", flex:1, sortable: false,
            columns:[
            	{text: "TINGKAT AKIBAT", flex:1, sortable: true, dataIndex: 'tingkat_akibat'},
            	{text: "TINGKAT KEMUNGKINAN", flex:1, sortable: true, dataIndex: 'tingkat_kemungkinan'},
            	{text: "TINGKAT RISIKO", flex:1, sortable: true, dataIndex: 'tingkat_risiko'}
            ]
        },
            {text: "RENCANA PENANGANAN RISIKO", flex:1, sortable: true, dataIndex: 'rencana_penanganan'},
            {text: "TARGET TINGKAT SISA RISIKO SETELAH TINDAK LANJUT", flex:1, sortable: true, dataIndex: 'sisa_risiko'},
            {text: "BATAS WAKTU", flex:1, sortable: true, dataIndex: 'batas_waktu'},
            {text: "PENANGGUNG JAWAB (PIC)", flex:1, sortable: true, dataIndex: 'pic'},
            {text: "KEPUTUSAN / TANGGAPAN DAN RENCANA TINDAKAN", flex:1, sortable: true, dataIndex: 'keputusan'},
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/accept.gif',
            handler: function(grid, rowIndex, colIndex){
                var rec = store.getAt(rowIndex);
				if (rec.get('status') == "0" && rec.get('status_risiko') == "Close" ){
					Ext.Msg.alert( "Status", "Maaf Analisis Risiko telah di APPROVE, Anda harus menghapus approval untuk Acces Kontrol!")
                
				}else{
					frmedit.getForm().findField('editid').setValue(rec.get('ar_id'));
					frmedit.getForm().findField('editrisiko').setValue(rec.get('risiko'));
					frmedit.getForm().findField('editakibat').setValue(rec.get('akibat'));
					frmedit.getForm().findField('editanalisis').setValue(rec.get('analisis'));
					frmedit.getForm().findField('edittingkat_akibat').setValue(rec.get('tingkat_akibat_id'));
					frmedit.getForm().findField('edittingkat_kemungkinan').setValue(rec.get('tingkat_kemungkinan_id'));
					frmedit.getForm().findField('edittingkat_risiko').setValue(rec.get('tingkat_risiko_id'));
					frmedit.getForm().findField('editrencana_penanganan').setValue(rec.get('rencana_penanganan'));
					frmedit.getForm().findField('editsisa_risiko').setValue(rec.get('sisa_risiko_id'));
					frmedit.getForm().findField('editbatas_waktu').setValue(rec.get('batas_waktu'));
					frmedit.getForm().findField('editpic').setValue(rec.get('pic'));
					frmedit.getForm().findField('editkeputusan').setValue(rec.get('keputusan'));
					winedit.show();
				}
            }
        },
            {text: "",xtype: 'actioncolumn', width:25,  sortable: true,icon:'<?=base_url();?>assets/images/delete.gif',
            handler: function(grid, rowIndex, colIndex){
            	var rec = store.getAt(rowIndex);
            	var id = rec.get('ar_id');
				Ext.MessageBox.confirm('Delete item', 'Apakah anda akan menghapus item ini?',function(resbtn){
							if(resbtn == 'yes')
							{
								Ext.Ajax.request({
									url: '<?=base_url();?>mutu_resiko/deletedata/identifikasi_resiko',
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
        }
        ],
        columnLines: true,
        width: '100%',
       	bbar: Ext.create('Ext.toolbar.Paging', {
                             pageSize: 50,
                             store: store,
                             displayInfo: true
                     }),
       	dockedItems: [{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
            {
                text:'Tambah',
                handler: function(){
                    winadd.show();
                }
            }]
        }],
        // renderTo: Ext.getBody()
    });
    store.load();
    

    var frminfo = Ext.create('Ext.form.Panel', { 
        id:'frminfo ',
        bodyStyle: 'padding:5px 5px 0',
        width: '100%',
        frame: false,
        items: [{
            xtype: 'component',
            html: '<table><tr><td>Unit Kerja/Unit Usaha&nbsp&nbsp</td><td> : <?php echo $divisi_name; ?> - <strong><?php echo $proyek; ?></strong>  </td></tr><td>Nilai Proyek </td><td> : <?php echo $nilai_kontrak_non_ppn; ?></td></tr><td>Waktu Pelaksanaan </td><td> : <?php echo date("d-m-Y",strToTime($mulai)); ?> s/d <?php echo date("d-m-Y",strToTime($berakhir)); ?></td></tr><td>Sasaran </td><td> : <?php echo $sasaran; ?></td></tr></table>',
            style: 'margin: 10px;'
        }],
        autoScroll: true
    });

    var frmadd = Ext.create('Ext.form.Panel', {     
        url: '<?php echo base_url() ?>mutu_resiko/insertdata/identifikasi_resiko',
        id:'frmadd ',
        bodyStyle: 'padding:5px 5px 0',
        width: 500,
        autoScroll: true,
        frame: false,
            items: [{
                xtype:'textarea',
                fieldLabel: 'RISIKO',
                anchor: '-5',
                name: 'risiko',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'AKIBAT',
                anchor: '-5',
                name: 'akibat',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'ANALISIS AKIBAT YANG DIPERTIMBANGKAN & FAKTOR POSITIF',
                anchor: '-5',
                name: 'analisis',
            afterLabelTextTpl: required,
            allowBlank: false
            },
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'TINGKAT AKIBAT',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_tingkat_akibat', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Tingkat Akibat...',
				name: 'tingkat_akibat',
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
				fieldLabel: 'TINGKAT KEMUNGKINAN',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_tingkat_kemungkinan', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Tingkat Kemungkinan...',
				name: 'tingkat_kemungkinan',
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
                fieldLabel: 'RENCANA PENANGANAN RISIKO',
                anchor: '-5',
                name: 'rencana_penanganan',
            afterLabelTextTpl: required,
            allowBlank: false
            },
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'TARGET TINGKAT SISA RISIKO SETELAH TINDAK LANJUT',
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
				emptyText: 'Pilih Sisa Risiko...',
				name: 'sisa_risiko',
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
                fieldLabel: 'BATAS WAKTU',
                anchor: '-5',
                name: 'batas_waktu',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'PENANGGUNG JAWAB (PIC)',
                anchor: '-5',
                name: 'pic',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'KEPUTUSAN / TANGGAPAN DAN RENCANA TINDAKAN',
                anchor: '-5',
                name: 'keputusan',
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
                            Ext.MessageBox.alert('Identifikasi Risiko','Insert successfully..!');
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
        url: '<?php echo base_url() ?>mutu_resiko/editdata/identifikasi_resiko',
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
                xtype:'textarea',
                fieldLabel: 'RISIKO',
                anchor: '-5',
                name: 'editrisiko',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'AKIBAT',
                anchor: '-5',
                name: 'editakibat',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'ANALISIS AKIBAT YANG DIPERTIMBANGKAN & FAKTOR POSITIF',
                anchor: '-5',
                name: 'editanalisis',
            afterLabelTextTpl: required,
            allowBlank: false
            },
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'TINGKAT AKIBAT',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_tingkat_akibat', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Tingkat Akibat...',
				name: 'edittingkat_akibat',
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
				fieldLabel: 'TINGKAT KEMUNGKINAN',
				allowBlank: false,				
				afterLabelTextTpl: required,
				store: { 
					fields: ['value','text'],
					pageSize: 50,
					autoLoad: true,					
					proxy: { 
						type: 'ajax', 
						url: '<?=base_url();?>mutu_resiko/get_tingkat_kemungkinan', 
						reader: { 
							root: 'data',
							type: 'json' 
						} 
					} 
				},
				value: '',							
				emptyText: 'Pilih Tingkat Kemungkinan...',
				name: 'edittingkat_kemungkinan',
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
			}),
			{
                xtype:'textarea',
                fieldLabel: 'RENCANA PENANGANAN RISIKO',
                anchor: '-5',
                name: 'editrencana_penanganan',
            afterLabelTextTpl: required,
            allowBlank: false
            },
			Ext.create('Ext.form.ComboBox', {
				fieldLabel: 'TARGET TINGKAT SISA RISIKO SETELAH TINDAK LANJUT',
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
				emptyText: 'Pilih Sisa Risiko...',
				name: 'editsisa_risiko',
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
                fieldLabel: 'BATAS WAKTU',
                anchor: '-5',
                name: 'editbatas_waktu',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'PENANGGUNG JAWAB (PIC)',
                anchor: '-5',
                name: 'editpic',
            afterLabelTextTpl: required,
            allowBlank: false
            },{
                xtype:'textarea',
                fieldLabel: 'KEPUTUSAN / TANGGAPAN DAN RENCANA TINDAKAN',
                anchor: '-5',
                name: 'editkeputusan',
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
                            Ext.MessageBox.alert('Identifikasi Risiko','Update successfully..!');
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
        items: frmadd 
    });

    var winedit = Ext.create('Ext.Window', {
        title: 'Edit',
        closeAction: 'hide',
        width: 500,
        height: 400,
        layout: 'fit',
        items: frmedit
    });

    var viewport = Ext.create('Ext.Viewport', {
        layout: {
            type: 'border',
        },
        border: 0,
        defaults: {
            split: true
        },
        items: [{
            region: 'north',      
            width: '100%',         
            border: 0,
            layout: 'fit',
            items: frminfo,
            height:'30%'
        },{
            region: 'north',
            layout: 'fit',
            width: '100%',
            height: '70%',
            border: 0,
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