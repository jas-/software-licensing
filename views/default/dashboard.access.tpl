<!-- main body template start -->
<script type="text/javascript">
 var $j = jQuery.noConflict();
 $j(document).ready(function(){
  function _message(obj){
   var details = '';
   if (obj!=''){
    obj = (typeof obj=='object') ? JSON.parse(obj) : obj;
    $j.each(obj, function(k, v){
     if (k=='error'){
      $j('#message').html('<div class="error">'+v+'</div>').fadeIn(1000);
     }
     if (k=='warning'){
      $j('#message').html('<div class="warning">'+v+'</div>').fadeIn(1000);
     }
     if (k=='info'){
      $j('#message').html('<div class="info">'+v+'</div>').fadeIn(1000);
     }
     if (k=='success'){
      $j('#message').html('<div class="success">'+v+'</div>').fadeIn(1000);
     }
    });
   } else {
    $j('#message').html('<div class="warning">Empty response for request</div>').fadeIn(1000);
   }
  }
  function _load(path){
   // load a spinner, take path as arg
  }
  $j('#submit-button').on('click', function(){
   $j('#do').val($j(this).val().toLowerCase());
  });
  $j('#acl').pidCrypt({
   appID:'{$token}',
   callback:function(){ _message(this); },
   preCallback:function(){ _load(); }
  });
 });
</script>
<div id="full" class="margins main">
 <div id="form" class="rounder gradient">
  <h2>Manage access</h2>
  <p>Create defined lists of allowed & denied clients. Individual records get automatically added to the deny list when threat detection is reached</p>
  <div id="message"></div>
  <form id="acl" name="acl" method="post" action="?nxs=proxy/acl">
   <label for="allow">Allow: </label>
    <textarea id="allow" name="allow" value="" placeholder="192.168.0.1, 10.10.0.0/24, 192.168.10.5-192.168.10.200">{$allow}</textarea><span class="required">*</span><br />
   <label for="deny">Deny: </label>
    <textarea id="deny" name="deny" value="" placeholder="192.168.0.1, 10.10.0.0/24, 192.168.10.5-192.168.10.200">{$deny}</textarea><span class="required">*</span><br />
   <label></label>
    <input type="hidden" id="do" name="do" />
    <input type="submit" value="Add" id="submit-button" />
    <input type="submit" value="Edit" id="submit-button" />
    <input type="submit" value="Delete" id="submit-button" />
  </form>
 </div>
</div>
<!-- main body template end -->