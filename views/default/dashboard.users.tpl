<!-- main body template start -->
<div id="full" class="margins main">
 <div id="form" class="rounder gradient">
  <h2>User management</h2>
  <p>Adding, editing and deleting users as easy as 1,2,3</p>
  <div id="message"></div>
  <form id="users" name="users" method="post" action="?nxs=proxy/users">
   <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="" placeholder="johndoe@example.com" required="required" /><span class="required">*</span>
   <label for="password">Password:</label>
    <input type="password" id="password" name="password" value="" placeholder="********" required="required" /><span class="required">*</span>
   <label for="confirm">Confirm:</label>
    <input type="password" id="confirm" name="confirm" value="" placeholder="********" required="required" /><span class="required">*</span>
   <input type="submit" value="Add User" id="submit-button" name="add" />
   <input type="submit" value="Edit User" id="submit-button" name="edit" />
   <input type="submit" value="Delete User" id="submit-button" name="del" />
  </form>
 </div>
</div>
<!-- main body template end -->