<!-- location template state -->
<script>
function initialize() {
 var myOptions = {
  zoom: 3,
  center: new google.maps.LatLng({$latitude},{$longitude}),
  mapTypeId: google.maps.MapTypeId.HYBRID
 }
 var map = new google.maps.Map(document.getElementById('map'), myOptions);
 var marker = new google.maps.Marker({
  position: new google.maps.LatLng({$latitude},{$longitude}),
  map: map,
  title:"User location"
 });
 google.maps.event.addListener(marker, 'dragstart', function() {
  updateMarkerAddress('Dragging...');
 });
 google.maps.event.addListener(marker, 'drag', function() {
  updateMarkerStatus('Dragging...');
  updateMarkerPosition(marker.getPosition());
 });
 google.maps.event.addListener(marker, 'dragend', function() {
  updateMarkerStatus('Drag ended');
  geocodePosition(marker.getPosition());
 });
}
function loadScript() {
  var script = document.createElement("script");
  script.type = "text/javascript";
  script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyDnhBqXMD17Y5OS_Li5zwyLIAL2HuGyRoc&sensor=false&callback=initialize";
  document.body.appendChild(script);
}
window.onload = loadScript;
</script>
<div id="form" class="rounder gradient">
 <h2>Location</h2>
 <p>The location is used for certificate authentication</p>
 <div id="map" style="width:95%; height:400px"></div>
</div>
<!-- location template end -->