<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map_canvas { height: 100% }
    </style>
 <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript">
      function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng(59.930506,30.361061),
          zoom: 10,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);
	
		var point1 = new google.maps.LatLng(59.930506,30.361061);
	  	var marker = new google.maps.Marker({
  position: point1, map: map, title: 'Метро Площадь Восстания!'
});     
		
var contentString = '<div class="gmnoprint" style="position: relative; left: 0px; top: 0px; overflow: hidden; z-index: 10; width: 344px; height: 129px;">Title</div>';
var infowindow = new google.maps.InfoWindow({
    content: contentString
}); 	

/*google.maps.event.addListener(marker, 'click', function() {
   document.location='http://office-planet.ru';
});
*/

google.maps.event.addListener(marker, 'click', function() {
    infowindow.open(map,marker);
});

}
</script>
  </head>
  <body onload="initialize()">
    <div id="map_canvas" style="width:100%; height:100%"></div>
  </body>
</html>
