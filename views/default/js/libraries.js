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
 function _load(){
  // load a spinner or something
 }
});
