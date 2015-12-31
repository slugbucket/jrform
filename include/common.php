<?php
// +----------------------------------------------------------------------
// | PHP Source                                                           
// +----------------------------------------------------------------------
// | Copyright (C) 2004 by Julian Rawcliffe <jujuberry@ntlworld.com>
// +----------------------------------------------------------------------
// |
// | Copyright: See COPYING file that comes with this distribution
// +----------------------------------------------------------------------
//
/*
 * This function takes a MySQL timestamp and converts it to a string of the
 * form:
 * dd Mon YYYY hh:mm:ss
 * $mid_str is optional text to display between the date and time
 */
function dbtime2str( $time = "0000-00-00 00:00:00", $mid_str = "" ) {
  if( !$time ) { return( "" ); }
  $date_str = date( "d M Y", strtotime( substr( $time, 0, 10 ) ) );
  $date_str .= $mid_str .substr( $time, 11 );
  return( $date_str );
}

function dbtime2date( $time = "0000-00-00 00:00:00", $mid_str = "" ) {
  if( !$time ) { return( "" ); }
  $date_str = date( "d M Y", strtotime( substr( $time, 0, 10 ) ) );
  return( $date_str );
}
?>
