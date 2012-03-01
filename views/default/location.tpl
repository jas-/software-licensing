<!-- location template state -->
<script>
function initialize() {
  var myOptions = {
    zoom: 3,
    center: new google.maps.LatLng({$latitude},{$longitude}),
    mapTypeId: google.maps.MapTypeId.HYBRID
  }
  var map = new google.maps.Map(document.getElementById('map'), myOptions);
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
 <p>PKCS#7 Certification information for user account</p>
 <div id="map" style="width:95%; height:400px"></div>
</div>
<!-- location template end -->