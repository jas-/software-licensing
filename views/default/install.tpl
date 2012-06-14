<!-- main body template start -->
<div id="full" class="margins main">
 <div id="form" class="rounder gradient">
  <h2>Installation wizard</h2>
  <p>
   Welcome to myTFH! My tin foil hat aims to provide decentralized, user
   administered single sign on services for any application that wishes to
   implement it.
  </p>
  <p>
   Because this is the first time running myTFH we need to perform the following
   actions.
  </p>
  <ol style="font-size:0.9em;font-style:italic">
   <li>Import the latest database schema</li>
   <li>Create the necessary stored procedures</li>
   <li>Generate a unique application wide salt</li>
   <li>Generate a default of application wide RSA keys</li>
   <li>Register all user configuration options within database</li>
   <li>Make modifications to default configuration file</li>
  </ol>
  <div id="message"></div>
  <form id="install" name="install" method="post" action="?nxs=installation">
   <label for="name">Title: </label>
    <input type="text" id="title" name="title" value="" placeholder="myTFH (My tin foil hat)" required="required" /><span class="required">*</span><br />
   <label for="template">Template: </label>
    <select name="template">
     {$templates}
    </select>
   <label></label>
    <input type="submit" value="Install" id="submit-button" />
  </form>
 </div>
</div>
<!-- main body template end -->