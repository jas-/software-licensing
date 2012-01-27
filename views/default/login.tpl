<script type="text/javascript">
 var $j = jQuery.noConflict();
 $j(document).ready(function(){
  $j('#authenticate').AJAX({appID:'{$token}',strict:true});
 });
</script>
<div id="authenticate" class="rounder gradient">
 <h2>Authenticate</h2>
 <p>Please login to view active software licenses</p>
 <form id="auth" name="authenticate" method="post" action="?nxs=dashboard">
  <label for="email">Email: </label>
   <input type="email" id="email" name="email" value="" placeholder="Enter email address" required="required" /><span class="required">*</span>
  <label for="password">Password: </label>
   <input type="password" id="password" name="password" value="" placeholder="Enter passphrase" required="required" /><span class="required">*</span>
  <input type="submit" value="Authenticate" id="submit-button" />
  <a href="">Register</a> | <a href="">Forgot username?</a>
 </form>
</div>