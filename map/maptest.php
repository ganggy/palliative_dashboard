<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>Google Map Test by Deawx</title>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>

<style>
html {
  height: auto;
}

body {
  height: auto;
  margin: 0;
  padding: 0;
}
#map {
  height: auto;
  position: absolute;
  bottom:0;
  left:0;
  right:0;
  top:0;
}

@media print {
  #map {
    height: 950px;
  }
}
</style>
</head>
<body>
<div id="map"></div>

<script type="text/javascript">   
 var locations = [   
    ['สี่แยก หนองครก', 15.09234482843765, 104.30643081665039,'https://icon-icons.com/icons2/583/PNG/48/map-pin_icon-icons.com_55133.png', 1],  
	['วิทยาลัยเทคนิคศรีสะเกษ', 15.10769597211894, 104.3216872215271,'https://icon-icons.com/icons2/583/PNG/48/map-pin_icon-icons.com_55133.png', 2], 
	['โรงพยาบาลจังหวัดศรีสะเกษ', 15.11544363118582, 104.32016372680664,'https://icon-icons.com/icons2/583/PNG/48/map-pin_icon-icons.com_55133.png', 3]
	];    
var map = new google.maps.Map(document.getElementById('map'), {      
zoom: 13,      
center: new google.maps.LatLng(15.105355046221582, 104.31570053100586),      
mapTypeId: google.maps.MapTypeId.ROADMAP   
});    
var infowindow = new google.maps.InfoWindow();    
var marker, i;    for (i = 0; i < locations.length; i++) {        
marker = new google.maps.Marker({        
position: new google.maps.LatLng(locations[i][1], locations[i][2]),       
map: map,
icon: locations[i][3]
});     

google.maps.event.addListener(marker, 'click', (function(marker, i) {        
return function() {         
 infowindow.setContent(locations[i][0]);          
 infowindow.open(map, marker);        
 }      
 })
 (marker, i));    
 }  
 </script>
</body>
</html>
