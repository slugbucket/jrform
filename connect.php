<?php
/* Connect to the dataabse */
/* Development settings
 */
$db_host   = "localhost";
$db_user   = "jkbb";
$db_pswd   = "jkbb";
$db_name   = "jkbb";
/* Live settings
$db_host   = "localhost";
$db_user   = "liveuser";
$db_pswd   = "livepswd";
$db_name   = "livedbname";
 */

$db_conn = mysql_connect ($db_host, $db_user, $db_pswd ) or
    die( "Error connecting to the database server. Please contact an administrat
or for help." );
mysql_select_db( $db_name ) or
    die( "Error connecting to the pocketmoney database. Please contact an admini
strator for help." );
?>
