// vim: sw=4:ts=4:nu:nospell:fdc=4
/*global Ext, Example, WebPage */
/**
 * Saki's Examples Application
 *
 * @author    Ing. Jozef SakÃ¡loÅ¡
 * @copyright (c) 2008, by Ing. Jozef SakÃ¡loÅ¡
 * @date      10. April 2008
 * @version   $Id: examples.js 156 2009-09-19 23:31:02Z jozo $
 */
 
Ext.ns('Example');

Ext.state.Manager.setProvider(new Ext.state.CookieProvider);
Ext.BLANK_IMAGE_URL = 'ext/resources/images/default/s.gif';

Example.root = './';
Example.tree = [{
	// {{{
	 text:'Application Design'
	,id:'design'
	,children:[{
		 text:'Complex Data Binding'
		,id:'databind'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'databind.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'databind.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'BindMgr Source'
			,href:'js/Ext.ux.data.BindMgr.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'databind.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Components Communication'
		,id:'compcomm'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'compcomm.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'compcomm.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'compcomm.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		},{
			 text:'CSS Source'
			,href:'css/compcomm.css'
			,iconCls:'icon-css'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Production/Development Switch'
		,id:'prodswitch'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'prodswitch.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'PHP Source'
			,href:'prodswitch.html'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		},{
			 text:'Makefile'
			,href:'js/Makefile.prodswitch'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'filelist Source'
			,href:'js/filelist'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'CSS Source'
			,href:'css/prodswitch.css'
			,iconCls:'icon-css'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Simple Message Bus'
		,id:'simplebus'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'simplebus.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'simplebus.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'simplebus.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Ext.ux.MsgBus Plugin'
		,id:'msgbus'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'msgbus.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'Example Source'
			,href:'msgbus.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'Plugin Source'
			,href:'js/Ext.ux.MsgBus.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'msgbus.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	}]
	// }}}
},{
	// {{{
	 text:'Drag &amp; Drop'
	,id:'dd'
	,children:[{
		 text:'Drag &amp; Drop Between Grids'
		,id:'ddgrids'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'ddgrids.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'ddgrids.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'ddgrids.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Drag from Grid to Tree'
		,id:'grid2treedrag'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'grid2treedrag.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'grid2treedrag.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'grid2treedrag.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Drag from Tree to Div'
		,id:'tree2divdrag'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'tree2divdrag.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'tree2divdrag.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'tree2divdrag.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Free Drag with State'
		,id:'freedrag'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'freedrag.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'freedrag.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'freedrag.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		},{
			 text:'CSS Source'
			,href:'css/freedrag.css'
			,iconCls:'icon-css'
			,source:true
			,leaf:true
		}]
	}]
	// }}}
},{
	// {{{
	 text:'Form'
	,id:'form'
	,children:[{
		 text:'CheckTreePanel in Form'
		,id:'treeinform'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'treeinform.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'treeinform.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'CheckTreePanel Source'
			,href:'js/Ext.ux.tree.CheckTreePanel.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'PHP Source'
			,href:'formloadsubmit.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'treeinform.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Column Layout in Form'
		,id:'formcol'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'formcol.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'formcol.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'formcol.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Combo with Remote Store'
		,id:'combo'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'combo.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'combo.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'combo.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		},{
			 text:'process-request.php'
			,href:'process-request.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		},{
			 text:'csql.php'
			,href:'classes/csql.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Displaying Form Submit Errors'
		,id:'formerrors'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'formerrors.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'formerrors.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'PHP Source'
			,href:'formerrors.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'formerrors.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		},{
			 text:'CSS Source'
			,href:'./css/formerrors.css'
			,iconCls:'icon-css'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Downloading Files'
		,id:'download'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'download.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'download.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'download.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		},{
			 text:'PHP Source'
			,href:'download.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Fieldsets In 2 Columns Layout'
		,id:'fs2col'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'fs2col.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'fs2col.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'fs2col.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Form Fields Anchoring'
		,id:'formanchor'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'formanchor.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'formanchor.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'formanchor.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Form Load And Submit'
		,id:'formloadsubmit'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'formloadsubmit.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'formloadsubmit.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'PHP Source'
			,href:'formloadsubmit.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'formloadsubmit.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		},{
			 text:'CSS Source'
			,href:'./css/formloadsubmit.css'
			,iconCls:'icon-css'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Image In Form'
		,id:'imginform'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'imginform.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'imginform.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'imginform.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Populate Combo on Form Load'
		,id:'popcombo'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'popcombo.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'popcombo.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'Ext.ux.form.PopCombo Source'
			,href:'js/Ext.ux.form.PopCombo.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'PHP Source'
			,href:'popcombo.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'popcombo.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Tabs in Form'
		,id:'tabsinform'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'tabsinform.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'tabsinform.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'PHP Source'
			,href:'tabsinform.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'tabsinform.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		},{
			 text:'CSS Source'
			,href:'./css/tabsinform.css'
			,iconCls:'icon-css'
			,source:true
			,leaf:true
		}]
	}]
	// }}}
},{
	// {{{
	 text:'Grid'
	,id:'grid'
	,children:[{
		 text:'Displaying 1:n Data in QuickTips'
		,id:'one2many'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'one2many.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'one2many.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'Request PHP Source'
			,href:'process-request.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		},{
			 text:'Class PHP Source'
			,href:'classes/csql.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Grid In An Accordion'
		,id:'gridinacc'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'gridinacc.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'gridinacc.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'gridinacc.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Grid In Card Layout (ext3)'
		,id:'gridincard'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'gridincard.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'gridincard.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'gridincard.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Grid In An Inactive Tab'
		,id:'gridintab'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'gridintab.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'gridintab.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'gridintab.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Grid In Border Layout'
		,id:'gridinbl'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'gridinbl.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'gridinbl.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'PHP Source'
			,href:'get-grid-data.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'gridinbl.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Using Grid rowBody'
		,id:'rowbody'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'rowbody.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'rowbody.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'rowbody.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		},{
			 text:'CSS Source'
			,href:'css/rowbody.css'
			,iconCls:'icon-css'
			,source:true
			,leaf:true
		}]
	}]
	// }}}
},{
	// {{{
	 text:'Layouts'
	,id:'layout'
	,children:[{
		 text:'Dynamically Adding Tabs'
		,id:'dyntab'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'dyntab.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'dyntab.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'dyntab.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Simplest Border Layout'
		,id:'simplestbl'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'simplestbl.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'simplestbl.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'simplestbl.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Simple Table Layout'
		,iconCls:'icon-bulb'
		,id:'tblayout'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'tblayout.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'tblayout.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'tblayout.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		},{
			 text:'CSS Source'
			,href:'./css/tblayout.css'
			,iconCls:'icon-css'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Simple Viewport'
		,iconCls:'icon-bulb'
		,id:'simplevp'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'simplevp.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'simplevp.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'simplevp.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Tab Panel in Accordion'
		,iconCls:'icon-bulb'
		,id:'tabinacc'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'tabinacc.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'tabinacc.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'tabinacc.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	}]
	// }}}
},{
	// {{{
	 text:'Panel/Window'
	,id:'panel'
	,children:[{
		 text:'Simple Window/Panel autoLoad'
		,id:'autoload'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'autoload.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'autoload.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'Loaded Content Source'
			,href:'autoload-content.php'
			,iconCls:'icon-php'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'autoload.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Handling Item Clicks'
		,id:'itemclick'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'itemclick.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'itemclick.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'CSS Source'
			,href:'css/itemclick.css'
			,iconCls:'icon-css'
			,source:true
			,leaf:true
		}]
	}]
	// }}}
},{
	// {{{
	 text:'State'
	,id:'state'
	,children:[{
		 text:'Asynchronous Tree State'
		,id:'treestate'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'treestate.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'treestate.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'Plugin Source'
			,href:'js/Ext.ux.state.TreePanel.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'treestate.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Keeping An Accordion State'
		,id:'accstate'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'accstate.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'accstate.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'accstate.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Keeping Tab and Window State'
		,id:'tabstate'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'tabstate.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'tabstate.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'tabstate.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	},{
		 text:'Keeping Tab State Using Plugin'
		,id:'tabstate2'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'tabstate2.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'JavaScript Source'
			,href:'tabstate2.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'Plugin Source'
			,href:'js/Ext.ux.state.TabPanel.js'
			,iconCls:'icon-script'
			,source:true
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'tabstate2.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	}]
	// }}}
},{
	// {{{
	 text:'CSS'
	,id:'css'
	,children:[{
		 text:'3 Columns Layout Using CSS'
		,id:'layout3c'
		,iconCls:'icon-bulb'
		,children:[{
			 text:'Run example'
			,href:Example.root + 'layout3c.html'
			,iconCls:'icon-run'
			,leaf:true
		},{
			 text:'HTML Source'
			,href:'layout3c.html'
			,iconCls:'icon-html'
			,source:true
			,leaf:true
		}]
	}]
	// }}}
},{
	 text:'About'
	,id:'about'
	,href:'./about.html'
	,iconCls:'icon-info'
	,leaf:true
}]
 
// application main entry point
Ext.onReady(function() {
 
    Ext.QuickTips.init();

	// {{{
	// create viewport
	var vp = new Ext.Viewport({
		 layout:'border'
		,minWidth:600
		,items:[{
			 region:'north'
			,xtype:'examplenorth'
			,pageTitle:Ext.fly('page-title').dom.innerHTML
		},{
			 region:'west'
			,layout:'ux.row'
			,width:260
			,minWidth:220
			,maxWidth:300
			,collapsible:true
			,collapseMode:'mini'
			,split:true
			,items:[{
				 id:'tree'
//			 	,region:'center'
				,rowHeight:1
				,layout:'fit'
				,autoScroll:true
				,title:'Examples'
				,border:false
				,xtype:'arraytree'
				,defaultTools:false
				,sort:false
				,bodyStyle:'padding:5px'
				,rootConfig:{
					 text:'About'
					,id:'root'
				}
				,rootVisible:false
				,children:Example.tree
				,singleExpand:true
			},{
				 height:240
				,id:'detail'
				,border:false
				,bodyStyle:'padding:4px'
				,title:'Details'
				,autoScroll:true
			},{
				 border:false
				,region:'south'
				,height:88
				,contentEl:'west-south-content'
			}]
		},{
			 region:'center'
			,id:'iframe'
			,xtype:'iframepanel'
			,border:true
			,defaultSrc:'about.html'
			,title:'&#160;'
		}]
	});
	// }}}

	var tree = Ext.getCmp('tree');
	var iframe = Ext.getCmp('iframe');
	var detailEl = null;
	var currentEx = null;

	function showDetail(ex) {
		if(!detailEl) {
			detailEl = Ext.getCmp('detail').body.createChild({tag:'div'});
		}
		Ext.state.Manager.set('ex', ex);
		if(ex !== currentEx) {
			var detailSrc = Ext.getDom('detail-' + ex);
			if(detailSrc) {
				detailEl.hide().update(detailSrc.innerHTML).slideIn('t');
				currentEx = ex;
			}
		}
	} // eo function showDetail

	// {{{
	// load iframe and detail on tree click
	tree.on({
		click:{stopEvent:true, fn:function(n, e) {
			e.stopEvent();
			// handle detail
			if(n.parentNode && n.parentNode.id) {
				if(Ext.fly('detail-' + n.parentNode.id)) {
					showDetail(n.parentNode.id);
				}
			}
			if(n.id) {
				if(Ext.fly('detail-' + n.id)) {
					showDetail(n.id);
				}
			}

			// handle iframe
			if(n.attributes.href) {
				if(n.attributes.source) {
					var src = 'source.php?file=' + n.attributes.href;
				}
				else {
					var src = n.attributes.href;
				}
				iframe.setSrc.defer(350, iframe, [src, true]);
			}

			// handle text click (toggle collapsed)
			if(!n.isLeaf()) {
				n.toggle();
			}
		}}
	});
	// }}}
	// {{{
	// handle theme switching within the iframe
	var themeCombo = vp.items.itemAt(0).themeCombo;
	themeCombo.setValue = themeCombo.setValue.createSequence(function(val) {
		var iframeExt = iframe.iframe.getWindow().Ext;
		if(iframeExt) {
			iframeExt.util.CSS.swapStyleSheet(this.themeVar, this.cssPath + val);
		}
	});

	iframe.on('documentloaded', function() {
		// set theme
		themeCombo.setValue(themeCombo.getValue());

		// set title/permalink
		var iw = this.iframe.getWindow();
		var loc = iw.location;
		var title = '';
		if('/source.php' !== loc.pathname) {
			title = 'Direct Link: <a href="' + loc.protocol + '//' + loc.host;
			title += '?ex=' + loc.pathname.replace(/(^\/|\.html$)/g, '') + '" target="_blank"';
			title += ' qtip="Use this link if you want to bookmark this example"';
			title += '>';
			title += iw.Ext.fly('page-title').dom.innerHTML + '</a>';
		}
		else {
			title = iw.Ext.fly('page-title').dom.innerHTML;
		}
		this.setTitle(title);
	});
	// }}}
	// {{{
	// permalink handling
	var page = Ext.urlDecode(window.location.search.substr(1));
	if(page && page.ex) {
		var node = tree.getNodeById(page.ex);
		if(node) {
			tree.collapseAll();
			node.parentNode.expand(false, false, function() {node.expand()});
		}
		showDetail(page.ex);
//		console.info(page.ex);
		iframe.setSrc.defer(350, iframe, [Example.root + page.ex + '.html',true]);
	}
	else {
		var ex = Ext.state.Manager.get('ex', 'root');
		showDetail(ex);
	}
	// }}}

}); // eo function onReady
 
// eof
