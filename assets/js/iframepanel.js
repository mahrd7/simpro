Ext.define('onlineplus.common.IframePanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.iframePanel',

    /**
     * iframe source url
     */
    //src: Ext.isIE && Ext.isSecure ? Ext.SSL_SECURE_URL : 'about:blank',
    src: 'about:blank',

    /**
     * Loading text for the loading mask
     */
    loadingText: 'Loading ...',

    /**
     * Loading configuration (not implemented)
     */
    loadingConfig: null,

    /**
     * Overwrites renderTpl for iframe inclusion
     */
    renderTpl: [
        // If this Panel is framed, the framing template renders the docked items round the frame
        '{% this.renderDockedItems(out,values,0); %}',
        // This empty div solves an IE6/7/Quirks problem where the margin-top on the bodyEl
        // is ignored. Best we can figure, this is triggered by the previousSibling being
        // position absolute (a docked item). The goal is to use margins to position the
        // bodyEl rather than left/top since that allows us to avoid writing a height on the
        // panel and the body. This in turn allows CSS height to expand or contract the
        // panel during things like portlet dragging where we want to avoid running a ton
        // of layouts during the drag operation.
        (Ext.isIE6 || Ext.isIE7 || Ext.isIEQuirks) ? '<div></div>' : '',
        '<div id="{id}-body" class="{baseCls}-body<tpl if="bodyCls"> {bodyCls}</tpl>',
            ' {baseCls}-body-{ui}<tpl if="uiCls">',
                '<tpl for="uiCls"> {parent.baseCls}-body-{parent.ui}-{.}</tpl>',
            '</tpl>"<tpl if="bodyStyle"> style="{bodyStyle}"</tpl>>',
            '<iframe src="{src}" width="100%" height="100%" frameborder="0"></iframe>',
            '{%this.renderContainer(out,values);%}',
        '</div>',
        '{% this.renderDockedItems(out,values,1); %}'
    ],

    /**
     * overwritten, data method for the renderTemplate
     * updated for 4.1
     */
    initRenderData: function() {
        var me = this,
            data = me.callParent({
                src: this.getSource()
            });

        me.initBodyStyles();

        me.protoBody.writeTo(data);
        delete me.protoBody;

        return data;
    },

    /**
     *  Delegates afterRender event
     */
    initComponent: function() {
        this.callParent(arguments);
        this.on('afterrender', this.onAfterRender, this, {});
    },

    /**
     * Gets the iframe element
     */
    getIframe: function() {
        return this.getTargetEl().child('iframe');
    },

    /**
     * Gets the iframe source url
     *
     * @return {String} iframe source url
     */
    getSource: function() {
        return this.src;
    },

    /**
     * Sets the iframe source url
     *
     * @param {String} source url
     * @param {String} loading text or empty
     * @return void
     */
    setSource: function(src, loadingText) {
        this.src = src;
        var f = this.getIframe();
        if (loadingText || this.loadingText) {
            this.body.mask(loadingText || this.loadingText);
        }

        f.dom.src = src;
    },

    /**
     * Reloads the iFrame
     */
    resetUrl: function() {
        var f = this.getIframe();
        f.dom.src = this.src;
    },

    /**
     * Fired on panel's afterrender event
     * Delegates iframe load event
     */
    onAfterRender: function() {
        var f = this.getIframe();
        f.on('load', this.onIframeLoaded, this, {});
    },

    /**
     * Fired if iframe url is loaded
     */
    onIframeLoaded: function() {
        if (this.loadingText) {
            this.body.unmask();
        }
    }

});