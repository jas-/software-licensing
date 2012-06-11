<?php
unset($_SESSION);
if (!empty($_GET['a'])){
 $_SESSION['token']=$_GET['a'];
 header('Location: '.$_SERVER['HTTP_REFERER']);
}
if (!empty($_SESSION['token'])){
 $m='<div class="success">Re-authentication successful</div>';
}
?>
<link rel="stylesheet" type="text/css" href="http://sso.dev:8080/views/default/css/styles.css" media="screen,projection" />
<script src="http://sso.dev:8080/views/default/js/jquery.min.js" type="text/javascript"></script>
<script src="http://sso.dev:8080/views/default/js/core.min.js"></script>
<script>
var $j = jQuery.noConflict();
$j(document).ready(function(){
 $j('#auth').pidCrypt({
  callback:function(){_message(this);_redirect(this);},
  preCallback:function(){_load();},
  errCallback:function(){_error();}
 });
});
</script>
<div id="form" class="remote rounder gradient">
 <h2>Authenticate</h2>
 <p>Please provide username & password</p>
 <div id="message" class="rounder gradient"><?php echo $m; ?></div>
 <form id="auth" name="authenticate" method="post" action="http://sso.dev:8080/?nxs=proxy/authenticate">
  <label for="email">Email: </label>
   <input type="email" id="email" name="email" value="" placeholder="Enter email address" required="required" /><span class="required">*</span>
  <label for="password">Password: </label>
   <input type="password" id="password" name="password" value="" placeholder="Enter passphrase" required="required" /><span class="required">*</span>
  <input type="submit" value="Authenticate" id="submit-button" />
  <a href="http://sso.dev:8080/?nxs=proxy/register">Register</a> | <a href="http://sso.dev:8080/?nxs=proxy/reset">Forgot username?</a>
 </form>
</div>