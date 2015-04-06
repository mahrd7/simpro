Ext.define('SF.view.incident.Map', {
    extend: 'Ext.ux.GMapPanel',
    alias: 'widget.incidentmap',

    store: 'MapIncidents',

    updateMap: function() {

        var records = Ext.getStore('MapIncidents').data.items;
        
        var length = records.length;
        var myArray = [];
        var record, marker;
        
        for (var i = 0; i < length; i++) {
            record = records[i].data;
            
            marker = {
                lat: record.lat,
                lng: record.lng,
                marker: {title: record.incident},
                listeners: {click: this.displayItem}
            }
        
            myArray.push(marker);
        }
        
        this.markers = myArray;
        console.log(this.markers);
        
        this.addMarkers(myArray);
        if(this.markerClusterer) {
            this.markerClusterer.clearMarkers();
        }
        markerClusterer = new MarkerClusterer(this.getMap(), this.cache.marker);
    }
});