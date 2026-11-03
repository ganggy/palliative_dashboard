<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <script src="http://code.jquery.com/jquery-latest.min.js"</script>
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <script>
      var map;
      function initMap() {
		  var mapOptions ={
        
          center: {lat: 17.850074, lng: 103.569965},
          zoom: 15,
        }
		var maps = new google.maps.Map(document.getElementById("map"),mapOptions);
		var marker, info;
		$.getJSON( "Jsondata.php", function(jsonObj){
			$.each(jsonObj, function(i,item){
				marker = nem google.maps.Marker({
					position: new google.maps.LatLng(item.lat,item.lng),
					map: maps,
					});
					info = new google.maps.InfoWindow();
					google.maps.event.addListener(marker, 'click', (function(marker, i){
						return function() {
							info.setContent(item.name);
							info.open(maps,marker);
						}
					})(marker, i));
			});
		}
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbUirp-8aEH-i9K3Zxn4WCzPSvURlV1bc&callback=initMap"
    async defer></script>
  </body>
</html>