Ext.define('SF.store.MapIncidents', {
    extend: 'Ext.data.Store',
    model: 'SF.model.Incident',
    autoLoad: true,

    proxy: {
    	type: 'ajax',
    	api: {
    		read: 'data/incidents.json'
    	},
    	reader: {
    		type: 'json',
    		root: 'incidents',
    		successProperty: 'success'
    	}
    }
});