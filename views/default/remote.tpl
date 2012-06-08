<link rel="stylesheet" type="text/css" href="{$templates}/css/styles.css" media="screen,projection" />
<script src="{$templates}/js/jquery.min.js" type="text/javascript"></script>
<script src="{$templates}/js/core.min.js"></script>
<script>
var $j = jQuery.noConflict();
$j(document).ready(function(){
 $j('#auth').pidCrypt({appID:'{$token}'});
});
</script>
<div id="form" class="remote rounder gradient">
 <h2>Authenticate</h2>
 <p>Please provide username & password</p>
 <div id="message"></div>
 <form id="auth" name="authenticate" method="post" action="{$server}/?nxs=proxy/authenticate">
  <label for="email">Email: </label>
   <input type="email" id="email" name="email" value="" placeholder="Enter email address" required="required" /><span class="required">*</span>
  <label for="password">Password: </label>
   <input type="password" id="password" name="password" value="" placeholder="Enter passphrase" required="required" /><span class="required">*</span>
  <input type="submit" value="Authenticate" id="submit-button" />
  <a href="{$server}/?nxs=proxy/register">Register</a> | <a href="{$server}/?nxs=proxy/reset">Forgot username?</a>
 </form>
</div>
<!--div id="login"><script type="text/javascript">window.onload=function(){var a='http://sso.dev:8080';var b=document.createElement('script');b.setAttribute('src', a+'/views/default/js/jquery.min.js');document.getElementById('login').appendChild(b);var c=document.createElement('script');c.setAttribute('src', a+'/views/default/js/remote-core.min.js');document.getElementById('login').appendChild(c);}</script></div-->