<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/js/extjs/resources/css/ext-all.css" />
<style type="text/css">
p {
    margin:5px;
}

.footer {
font-size: 10px;
font-family: 'Arial'
}


.new-tab {
    background-image:url(<?php echo base_url(); ?>assets/images/new_tab.gif) !important;
}

.icon-add {
    background-image:url(<?php echo base_url(); ?>assets/images/add.gif) !important;
}

.icon-del {
    background-image:url(<?php echo base_url(); ?>assets/images/delete.png) !important;
}
.icon-copy {
    background-image:url(<?php echo base_url(); ?>assets/images/copy.png ) !important;
}
.icon-paste {
    background-image:url(<?php echo base_url(); ?>assets/images/paste.png ) !important;
}

.tabs {
    background-image:url(<?php echo base_url(); ?>assets/images/tabs.gif ) !important;
}

.icon-back {
    background-image:url(<?php echo base_url(); ?>assets/images/back.png) !important;
}

.icon-table {
    background-image:url(<?php echo base_url(); ?>assets/images/table.png) !important;
}

.icon-print {
    background-image:url(<?php echo base_url(); ?>assets/images/print.png) !important;
}

.icon-reload {
    background-image:url(<?php echo base_url(); ?>assets/images/reload.png) !important;
}

.task .x-grid-cell-inner {
    padding-left: 15px;
}
.x-grid-row-summary .x-grid-cell-inner {
    font-weight: bold;
    font-size: 11px;
}

.icon-grid {
    background: url(<?php echo base_url(); ?>assets/images/grid.png) no-repeat 0 -1px;
}

.msg .x-box-mc {
    font-size:14px;
}

#msg-div {
    position:absolute;
    left:35%;
    top:10px;
    width:300px;
    z-index:999999;
}

#msg-div .msg {
    border-radius: 8px;
    -moz-border-radius: 8px;
    background: #F6F6F6;
    border: 2px solid #ccc;
    margin-top: 2px;
    padding: 10px 15px;
    color: #555;
}

#msg-div .msg h3 {
    margin: 0 0 8px;
    font-weight: bold;
    font-size: 15px;
}

#msg-div .msg p {
    margin: 0;
}   
</style>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/extjs/ext-all.js"></script>
<script type="text/javascript">

    Ext.require([
        '*'
    ]);

    Ext.onReady(function() {

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
                html: '<iframe src="<?php echo base_url() ?>rbk/surat_pernyataan_home" border=0 width="100%" height="100%" id="gantframe"></iframe>',
                dockedItems:[
                    {
                        xtype:'toolbar',
                        dock:'top',
                        items:[
                            {
                                text:'Export Excel',
                                iconCls:'icon-print',
                                handler:function(){
                                    Ext.MessageBox.confirm('Export', 'Apakah anda akan meng-Export data ini?',function(resbtn){
                                        if(resbtn == 'yes')
                                        {
                                            window.location='<?=base_url()?>rbk/print_surat_pernyataan/surat_pernyataan';                                                                     
                                        }
                                    });
                                }
                            }
                        ]
                    }
                ]
            }]
        });

    });


</script>
