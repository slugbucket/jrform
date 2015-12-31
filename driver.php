<?php
/*
 * Clean up the global arrays so that slashes have been added to all incoming
 * requests
 */
function addslash( $val ) {
  return( is_array( $val ) ? array_map( 'gpcAddslash', $val ) : addslashes( $val ) );
}
function addslashGpc () {
  foreach( array('POST', 'GET', 'REQUEST', 'COOKIE' ) as $gpc ) {
    $GLOBALS["_$gpc"] = array_map( 'addslash', $GLOBALS["_$gpc"] );
  }
}
if( !get_magic_quotes_gpc() ) {
  addslashGpc();
}

/*
 * Identify and store the submitted form values to be retained and
 * re-displayed on the form
 */
$form_attrs['userid']   = $_GET['userid'];
if( !$form_attrs['userid'] ) { $form_attrs['userid'] = $_POST['userid']; }
$form_attrs['ftype']    = $_GET['ftype'];
if( !$form_attrs['ftype'] ) { $form_attrs['ftype'] = $_POST['ftype']; }
$form_attrs['action']   = $_GET['action'];
if( !$form_attrs['action'] ) { $form_attrs['action'] = $_POST['action']; }
$form_attrs['uname']    = $_POST['uname'];
$form_attrs['fname']    = $_POST['fname'];
$form_attrs['question'] = $_POST['question'];
$form_attrs['email']    = $_POST['email'];
$form_attrs['role']     = $_POST['role'];
$form_attrs['hint']     = $_POST['hint'];
$form_attrs['password'] = $_POST['password'];
$form_attrs['surname'] = $_POST['surname'];
$form_attrs['start'] = $_GET['start'];
$errors = array(); /* Where we store our errors */
include "userform.php";

/*
 * User processing functions
 * If this gets too complicated include in a separate file.
 */
/* Default to user action if formtype not specified */
if( $form_attrs['ftype'] == "user" ) {
  /* Basic sanity check of form values */
  if( $form_attrs['userid'] && $form_attrs['action'] != "new"
                            && $form_attrs['action'] != "add" ) {
    $forms = make_form( "users.xml", $form_attrs, "" );
  }
  /*
   * Identify and process any recognised actions, giving an error if an
   * unknown action is requested
   */
    if( $form_attrs['action'] == "update" ) { /* process the form data */
      $rc = modify_user( $form_attrs );
      /* Check for what needs to be done with errors */
      echo $errors[status];
      list_users( "users.xml", $form_attrs );
      if( $rc ) { /* Re-display the form */
        include "templates/user_table.php";
      }
    } elseif( $form_attrs['surname'] ) {
      list_users( "users.xml", $form_attrs );
    } elseif( $form_attrs['action'] == "add" ) { /* process the form data */
      $rc = add_user( $form_attrs );
      /* Check for what needs to be done with errors */
      if( $rc ) { /* Re-display the form */
        $forms = make_form( "newuser.xml", $form_attrs, "" );
        include "templates/user_table.php";
      } else {
        if( $_POST['reload'] == "yes" ) {
          $forms = make_form( "newuser.xml", "", "" );
          include "templates/user_table.php";
        } else {
          list_users( "users.xml", $form_attrs );
        }
      }
    } elseif( $form_attrs['action'] == "new" ) { /* Display new user form */
      $forms = make_form( "newuser.xml", $form_attrs, "" );
      /*
       * Make sure that the action value that led to this form being displayed
       * (action=new) is not retained
       */
      $forms[user]->setElementAttrById( "action", "elemValue", "add" );
      include "templates/user_table.php";
    } elseif( $forms[user] ) { /* There was not an error with user detail form */
      include "templates/user_table.php";
    } elseif( !$form_attrs['userid'] && !$form_attrs['action'] ) {
      list_users( "users.xml", $form_attrs );
    } elseif( $form_attrs['action'] != "modify" ) { /* && $form_attrs[action] */
      echo "Invalid action requested.<br>\n";
      list_users( "users.xml", $form_attrs );
    } else { /* elseif( $userid ) */
     echo "Cannot find user record matching the request.<br>\n";
     list_users( "users.xml", $form_attrs );
    } /* Else list_users() */
} else { /* No form type specified - just list all the users */
  list_users( "users.xml", $form_attrs );
}

?>
