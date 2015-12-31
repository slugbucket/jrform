<?php
function list_users( $xml_src = "", $form_attrs = "" ) {
  $items_per_page = 10;
  $offset = 10;

/*
 * N O T  Y E T  I M P L E M E N T E D
 *
  if( ! $user_dom = domxml_open_file( $xml_src ) ) {
    die( "Cannot find XML source dcument, $xml_src.<br>\n" );
  }
  $userlist = $user_dom->document_element();
  $forms_ary = $userlist->get_elements_by_tagname( 'itemlist' );
 */
  $uss = "Surname";
  if( $form_attrs['surname'] ) { $uss = $form_attrs['surname']; }
  $srch_form_str = " <div align=\"left\">
<form action=\"" . $_SERVER[PHP_SELF] . "\" method=\"post\" name=\"search\">Search
<input type=\"text\" name=\"surname\" value=\"" . $uss . "\" class=\"textbox\" onClick='this.value=\"\"';>
<input type=\"hidden\" name=\"ftype\" value=\"user\" class=\"textbox\">
<input type=\"submit\" name=\"find\" value=\"Go\" class=\"button\">
</form>
</div>";
  $nustr = "<a href=\"" . $_SERVER[PHP_SELF] . "?ftype=user&action=new\">New user</a><br>\n";
  $rlstr = "<a href=\"" . $_SERVER[PHP_SELF] . "?ftype=user\">Return to user list</a><br>\n";
  $where = "u_board = 'school'";
  if( $form_attrs['surname'] ) {
    $where .= " AND fullname REGEXP '" . $form_attrs['surname'] . "'";
  }
  $qry_ary['user_list'] =
           "SELECT id AS userid, name AS uname, u_role, " .
           "DATE_FORMAT( last_updated, \"%e %b %Y\" ) as last_update, " .
           "fullname AS fname, email " .
           "FROM users " .
           "WHERE " . $where . " " .
           "ORDER BY name";
//           "LIMIT( $start, $items_per_page )";
//  echo "DEBUG: Running query: " . $qry_ary['user_list'] . ".<br>\n";

  /* How many users and pages do we have */
  $mysql_res = mysql_query( $qry_ary['user_list'] );
  $num_users = mysql_numrows( $mysql_res );
  $num_pages = ( ( $num_users % $items_per_page ) == 0 ) ?
               floor( $num_users / $items_per_page ) :
               floor( $num_users / $items_per_page ) + 1;

  /* Get the page of users to display */
  if( ! $form_attrs[start] ) { $cur_page = 0; }
  else { $cur_page = $form_attrs[start];      }
  if( $cur_page > $num_pages ) { $cur_page = 0; }

  if( $items_per_page > $num_users ) { $items_per_page = $num_users; }
  elseif( ($cur_page+1) * $items_per_page > $num_users ) {
    $items_per_page = $num_users - ( ( $num_pages - 1 ) * $items_per_page );
  }

  /* Format the page list links */
  $pg_list_str = "<div class=\"userlist\" align=\"center\">";
  if( $cur_page > 0 ) {
    $pg_list_str .= "<a href=\"" . $_SERVER[PHP_SELF] .
      "?ftype=user&start=0\">First</a>" .
      "&nbsp;&nbsp;\n" .
      "<a href=\"" . $_SERVER[PHP_SELF] . "?ftype=user&start=" . ($cur_page-1) .
      "\">Previous</a>\n";
  } else {
    $pg_list_str .= "First&nbsp;&nbsp;Previous\n";
  }
  for( $i = 1; $i <= $num_pages; ++$i ) {
    if( $cur_page != ($i-1) ) {
      $pg_list_str .= "&nbsp;<a href=\"" . $_SERVER[PHP_SELF] . "?ftype=user&start=" .
      ($i-1) . "\">" . $i . "</a>&nbsp;";
    } else {
      $pg_list_str .= $i;
    }
  }
  if( ($cur_page+1) < $num_pages ) {
    $pg_list_str .= "<a href=\"" . $_SERVER[PHP_SELF] .
      "?ftype=user&start=" . ($cur_page+1) . "\">Next</a>\n" .
      "&nbsp;&nbsp;\n" .
      "<a href=\"" . $_SERVER[PHP_SELF] .
      "?ftype=user&start=" . ($num_pages-1) . "\">Last</a>";
  } else {
    $pg_list_str .= "Next&nbsp;&nbsp;Last<br>\n";
  }
  $pg_list_str .= "</div>\n";

  echo $srch_form_str;
  echo "<a href=\"" . $_SERVER[PHP_SELF] . "?ftype=user&action=new\">New user</a><br>\n";
  if( $num_users ) {
    echo $pg_list_str;
    echo "<table cellspacing=\"0\" cellpadding=\"0\" width=\"90%\" border=\"0\">\n";
    echo "<tr><th align=\"left\">Username</th><th align=\"left\">Full name</th><th align=\"left\">Email</th></tr>\n";
    mysql_data_seek( $mysql_res, $cur_page * $offset );
    for( $i = 0; $i < $items_per_page; ++$i ) {
      $row = mysql_fetch_array( $mysql_res, MYSQL_ASSOC );
      echo "<tr>" .
           "<td align=\"left\"><a href=\"$_SERVER[PHP_SELF]?ftype=user&userid=" . $row[userid] .
           "\">" . $row[uname] . "</a></td>\n";
      echo "<td align=\"left\">". $row[fname] . "</td>\n";
      echo "<td align=\"left\">". $row[email] . "</td>\n";
      echo "</tr>\n";
    }
    echo "</table>\n";
    echo $pg_list_str;
  } else {
    echo "<p>There are no users matching the search criteria.</p>\n";
  }
  echo $nustr;
  if( $form_attrs['surname'] ) {
    echo $rlstr;
  }
  
}

/*
 * Function to add a new user to the database
 * Returns: 0 success
 *          1 failure
 */
function add_user( $form_attrs = "" ) {
  global $errors;

  /* Upate the database */
  $mysql_ins_str = "INSERT INTO users ( name, fullname, u_board, password, " .
    "question, email, u_role) VALUES( ";
  if( $form_attrs['uname'] ) {
    $mysql_ins_str .= "'" . $form_attrs['uname'] . "' ";
  } else {
    $errors['uname'] = "Must specify a login name for the user.";
    return( 1 );
  }
  if( $form_attrs['fname'] ) {
    $mysql_ins_str .= ", '" . $form_attrs['fname'] . "' ";
  } else {
    $errors['fname'] = "Must specify a full name for the user.";
    return( 1 );
  }
  $mysql_ins_str .= ", 'school'";
  /* Only include password if passed by form */
  if( $form_attrs['password'] ) {
    $mysql_ins_str .= ", md5('" . $form_attrs['password'] . "') ";
  } else {
    $errors['password'] = "Must specify a password for the user.";
    return( 1 );
  }
  $mysql_ins_str .= ", '" . $form_attrs['hint'] . "' ";
  $mysql_ins_str .= ", '" . $form_attrs['email'] . "' ";
  $mysql_ins_str .= ", '" . substr( $form_attrs['role'], 0, 3 ) . "' ";
  $mysql_ins_str .= ")";
  mysql_query( $mysql_ins_str );
  if( mysql_insert_id() ) {
    $errors[status] = "<p>Update successfully completed.</p>\n";
    return( 0 );
  } else {
    $errors[status] = "<p>An error occurred that prevented the update from  being completed.</p>\n";
    return( 1 );
  }
}
/*
 * Function to modify an existing database entry
 * Returns: 0 success
 *          1 failure
 */
function modify_user( $form_attrs = "" ) {
  global $errors;

  /* Upate the database */
  $mysql_upd_str = "UPDATE users SET ";
  if( $form_attrs['userid'] ) {
    $mysql_upd_str .= "id = '" . $form_attrs['userid'] . "'";
    $mysql_where_str .= " WHERE id = '" . $form_attrs['userid'] . "'";
  } else {
    $errors['userid'] = "Cannot update non-existent user.";
    return( 1 );
  }
  if( $form_attrs['uname'] ) {
    $mysql_upd_str .= ", name = '" . $form_attrs['uname'] . "' ";
  } else {
    $errors['uname'] = "Must specify a login name for the user.";
    return( 1 );
  }
  if( $form_attrs['fname'] ) {
    $mysql_upd_str .= ", fullname = '" . $form_attrs['fname'] . "' ";
  } else {
    $errors['fname'] = "Must specify a full name for the user.";
    return( 1 );
  }
  /* Only include password if passed by form */
  if( $form_attrs['password'] ) {
    $mysql_upd_str .= ", password = md5('" . $form_attrs['password'] . "') ";
  }
  if( $form_attrs['hint'] ) {
    $mysql_upd_str .= ", question = '" . $form_attrs['hint'] . "' ";
  }
  if( $form_attrs['email'] ) {
    $mysql_upd_str .= ", email = '" . $form_attrs['email'] . "' ";
  }
  $mysql_upd_str .= ", u_role = '" . substr( $form_attrs['role'], 0, 3 ) . "' ";
  $mysql_upd_str .= $mysql_where_str;
// echo "Applying update: $mysql_upd_str.<br>\n";
  if( mysql_query( $mysql_upd_str ) ) {
    $errors[status] = "<p>Update successfully completed.</p>\n";
    return( 0 );
  } else {
    $errors[status] = "<p>An error occurred that prevented the update from  being completed.</p>\n";
    return( 1 );
  }
}
?>
