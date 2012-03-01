<!-- main body template start -->
<script type="text/javascript">
 var $j = jQuery.noConflict();
 $j(document).ready(function(){
  $j('#users').pidCrypt({
   appID:'{$token}',
   callback:function(){ _message(this); },
   preCallback:function(){ _load(); }
  });
 });
</script>
<script>
function initialize() {
  var myOptions = {
    zoom: 8,
    center: new google.maps.LatLng({$lat},{$lon}),
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
<div id="full" class="margins main">
 <div id="form" class="rounder gradient">
  <h2>User management</h2>
  <p>Adding, editing and deleting users as easy as 1,2,3</p>
  <div id="message"></div>
  <form id="users" name="userManagement" method="post" action="?nxs=proxy/users">
   <label for="email">Email: </label>
    <input type="email" id="email" name="email" value="" placeholder="johndoe@example.com" required="required" /><span class="required">*</span><br />
   <label for="password">Password: </label>
    <input type="password" id="password" name="password" value="" placeholder="********" required="required" /><span class="required">*</span><br />
   <label for="confirm">Confirm: </label>
    <input type="password" id="confirm" name="confirm" value="" placeholder="********" required="required" /><span class="required">*</span><br />
   <br/><hr style="width: 95%"/><br/>
   <label for="organizationalName">Organization: </label>
    <input type="text" id="organizationalName" name="organizationalName" value="" placeholder="Surfs Up LLC" required="required" /><span class="required">*</span><br />
   <label for="organizationalUnitName">Department: </label>
    <input type="text" id="organizationalUnitName" name="organizationalUnitName" value="" placeholder="Department of Kowabunga" required="required" /><span class="required">*</span><br />
   <label for="localityName">City: </label>
    <input type="text" id="localityName" name="localityName" value="" placeholder="San Diego" required="required" /><span class="required">*</span><br />
   <label for="stateOrProvinceName">State: </label>
    <input type="text" id="stateOrProvinceName" name="stateOrProvinceName" value="" placeholder="California" required="required" /><span class="required">*</span><br />
   <label for="countryName">Country: </label>
    <input type="text" id="countryName" name="countryName" value="" placeholder="United States" required="required" /><span class="required">*</span><br />
   <input type="submit" value="Add User" id="submit-button" name="add" />
   <input type="submit" value="Edit User" id="submit-button" name="edit" />
   <input type="submit" value="Delete User" id="submit-button" name="del" />
   <br/><br/><hr style="width: 95%"/><br/>
   <div id="map" style="width:95%; height:400px"></div>
  </form>
 </div>
</div>
<!-- main body template end -->