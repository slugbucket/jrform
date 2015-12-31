<div align="left">
<?php
echo $forms[search]->showForm( "head" );
echo $forms[search]->showForm( "ftype" );
echo $forms[search]->showForm( "surname" );
echo $forms[search]->showForm( "find" );
echo $forms[search]->showForm( "tail" );
?>
</div>
<?php
echo $forms[user]->showForm( "head" );
if( $forms[user]->hasElement( "userid" ) ) {
  echo $forms[user]->showForm( "userid" );
}
echo $forms[user]->showForm( "action" );
echo $forms[user]->showForm( "ftype" );
if( $forms[user]->hasElement( "reload" ) ) {
  echo $forms[user]->showForm( "reload" );
}
?>
<table cellpadding="0" cellspacing="5" border="0" width="100%">
<tr>
<?php if( $errors[uname] ) { ?>
<tr><td colspan="2"><?php echo $errors['uname']; ?></td></tr>
  <?php } ?>
 <td>User Name</td><td><?php echo $forms[user]->showForm( "uname" ); ?></td>
</tr>
<?php if( $errors[fname] ) { ?>
<tr><td colspan="2"><?php echo $errors['fname']; ?></td></tr>
  <?php } ?>
<tr>
 <td>Full Name</td><td><?php echo $forms[user]->showElement( "fname" ); ?></td>
</tr>
<tr>
 <td>Email address</td><td><?php echo $forms[user]->showForm( "email" ); ?></td>
</tr>
<?php if( $errors[password] ) { ?>
<tr><td colspan="2"><?php echo $errors['password']; ?></td></tr>
  <?php } ?>
<tr>
 <td>Password</td><td><?php echo $forms[user]->showForm( "password" ); ?></td>
</tr>
<tr>
 <td>Password Hint</td><td><?php echo $forms[user]->showForm( "4", "4" ); ?></td>
</tr>
<tr>
 <td>Permissions</td><td><?php echo $forms[user]->showForm( "5", "5" ); ?></td>
</tr>
<?php if( $form_attrs[action] != "new" && $form_attrs[action] != "add" ) { ?>
<tr>
 <td>Last Updated</td><td><?php echo $forms[user]->showForm( "last_updated" ); ?></td>
</tr>
<?php } ?>
<tr>
 <td align="right"><?php echo $forms[user]->showForm( "7", "7" ); ?></td>
 <td><?php echo $forms[user]->showForm( "8", "8" ); ?></td>
</tr>
</table>
<?php echo $forms[user]->showForm( "tail" ); ?>
<a href="users.php?ftype=user">Return to user list</a>
