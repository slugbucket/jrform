<?php
include "jrform.php";

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
 * Driver to configure the data structures required for the form generation
 * function.
 * It needs to create:
 * an array containing any submitted form attributes
 * an array of 'named' database queries that can populate the form if no
 *  form attributes are present. The array index strings need to match the
 *  name of a form in the XML document. Any database fields retrieved need
 * match the name of the associated element in the form.
 * In the case of SELECT dropdown form elements, the XML document will
 * identify how the database data should be merged into the form:
 *   - The query can generate all the SELECT OPTIONS which are displayed. This
 *     will override any options listed in the XML document.
 *   - The query can generate one value which becomes the selected item in the
 *     list given in the XML document.
 */
function admin_topics() {
  global $_GET;
  global $_POST;

  /*
   * Identify and store the submitted form values to be retained and
   * re-displayed on the form
   */
  $form_attrs['tid'] = $_GET['tid'];
  if( !$form_attrs['tid'] ) { $form_attrs['tid'] = $_POST['tid']; }
  $form_attrs['title'] = $_POST['title'];
  $form_attrs['owner'] = $_POST['owner'];
  $form_attrs['role'] = $_POST['role'];
  $form_attrs['approved'] = $_POST['approved'];

  /*
   * Define rules for what database queries should be used
   */
  if( $form_attrs['tid'] ) {
    $qry_ary['top_list'] =
             "SELECT topics.id, users.name as owner, t_role AS t_role, " .
             "DATE_FORMAT( topics.started, \"%e %b %Y\" ) as opened, " .
               "topics.name AS title, is_approved " .
             "FROM topics, users " .
             "WHERE topics.id = '" . $form_attrs['tid'] . "' " .
             "AND topics.t_owner = users.id";
  }
  /*
   * Create the form
   */
  $form_string = make_form( "topics.xml", "", $form_attrs, $qry_ary );
}

$form_str = admin_topics();
echo $form_str;
?>
