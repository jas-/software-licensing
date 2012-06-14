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
 </div>
 <br/>
 <div id="form" class="rounder gradient">
  <h2>Application defaults</h2>
  <p>Please configure the applications default behavior</p>
  <div id="message"></div>
  <form id="install" name="install" method="post" action="?nxs=installation">
   <label for="title">Title: </label>
    <input type="text" id="title" name="title" value="" placeholder="myTFH (My tin foil hat)" required="required" /><span class="required">*</span><br />
   <label for="email">Email: </label>
    <input type="text" id="email" name="email" value="" placeholder="default@mytfh.dev" required="required" /><span class="required">*</span><br />
   <label for="timeout">Timeout: </label>
    <input type="text" id="timeout" name="timeoute" value="" placeholder="3600 = 5 minutes" required="required" /><span class="required">*</span><br />
   <label for="flogin">Login count: </label>
    <input type="text" id="flogin" name="flogin" value="" placeholder="5 (failed = IP blacklisted)" required="required" /><span class="required">*</span><br />
   <label for="template">Template: </label>
    <select id="template" name="template" required="required" style="width: 30%">
     {$tmpl}
    </select><span class="required">*</span><br/>
 </div>
 <br/>
 <div id="form" class="rounder gradient">
  <h2>MySQL credentials</h2>
  <p>Because the installation creates a new database & imports stored procedures root access is required</p>
   <label for="email">Username: </label>
    <input type="text" id="email" name="email" value="" placeholder="johndoe@example.com" required="required" /><span class="required">*</span><br />
   <label for="password">Password: </label>
    <input type="password" id="password" name="password" value="" placeholder="********" required="required" /><span class="required">*</span><br />
   <label for="confirm">Confirm: </label>
    <input type="password" id="confirm" name="confirm" value="" placeholder="********" required="required" /><span class="required">*</span><br />
 </div>
 <br/>
 <div id="form" class="rounder gradient">
  <h2>Administration account</h2>
  <p>Create a default administration user account for access to the myTFH application</p>
   <label for="email">Username: </label>
    <input type="text" id="email" name="email" value="" placeholder="johndoe@example.com" required="required" /><span class="required">*</span><br />
   <label for="password">Password: </label>
    <input type="password" id="password" name="password" value="" placeholder="********" required="required" /><span class="required">*</span><br />
   <label for="confirm">Confirm: </label>
    <input type="password" id="confirm" name="confirm" value="" placeholder="********" required="required" /><span class="required">*</span><br />
   <br/><hr style="width: 95%"/><br/>
   <label for="level">Access level: </label>
    <select id="level" name="level" required="required" style="width: 30%">
     <option id="" value="admin">admin</option>
     {$level}
    </select><span class="required">*</span><br />
   <label for="group">Group: </label>
    <select id="group" name="group" required="required" style="width: 30%">
     <option id="" value="admin">admin</option>
     {$group}
    </select><span class="required">*</span><br />
 </div>
 <br/>
 <div id="form" class="rounder gradient">
  <h2>Certificate information</h2>
  <p>Because this application implements a PKI solution the certificate details for this installation are required</p>
   <label for="organizationalName">Organization: </label>
    <input type="text" id="organizationalName" name="organizationalName" value="" placeholder="Surfs Up LLC" required="required" /><span class="required">*</span><br />
   <label for="organizationalUnitName">Department: </label>
    <input type="text" id="organizationalUnitName" name="organizationalUnitName" value="" placeholder="Department of Kowabunga" required="required" /><span class="required">*</span><br />
   <label for="localityName">City: </label>
    <input type="text" id="localityName" name="localityName" value="{$localityName}" placeholder="San Diego" required="required" /><span class="required">*</span><br />
   <label for="stateOrProvinceName">State: </label>
    <input type="text" id="stateOrProvinceName" name="stateOrProvinceName" value="{$stateOrProvinceName}" placeholder="California" required="required" /><span class="required">*</span><br />
   <label for="countryName">Country: </label>
    <input type="text" id="countryName" name="countryName" value="{$countryName}" placeholder="United States" required="required" /><span class="required">*</span><br />
   <label></label>
    <input type="submit" value="Install" id="submit-button" />
  </form>
 </div>
</div>
<!-- main body template end -->