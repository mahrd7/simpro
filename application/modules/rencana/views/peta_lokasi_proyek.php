<!DOCTYPE html>
<html>
<head>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY0kkJiTPVd2U7aTOAwhc9ySH6oHxOIYM&sensor=false"></script>
<script>
var myCenter=new google.maps.LatLng(-6.78035,107.7241);

function initialize()
{
	var mapProp = {
	  center:myCenter,
	  zoom:5,
	  mapTypeId:google.maps.MapTypeId.ROADMAP
	};

	var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);

	var marker=new google.maps.Marker({
	  position:myCenter,
	});

	marker.setMap(map);

	var infowindow = new google.maps.InfoWindow({
	  content:"Hello World!"
	  });

	google.maps.event.addListener(marker, 'click', function() {
	  infowindow.open(map,marker);
	});
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>
</head>

<body>
<div id="googleMap" style="width:500px;height:380px;"></div>
</body>
</html>

Ext.Loader.loadScriptFile('https://www.google.com/jsapi',function(){
    google.load("maps", "3", {
        other_params:"key=AIzaSyBX9mIZ_YEXJUegZymZLMiCDiwDGdg8sxM&sensor=false",
        callback : function(){
            // Google Maps are loaded. Place your code here
        }
    });
},Ext.emptyFn,null,false);
