<?php
class SelectElement {
  var $elemType = "select";
  var $elemName;
  var $options = array(); /* Array with the select <option> list */
  var $dbFields; /* What fields to select from a database */
  var $dbTables; /* What db tables to select from */
  var $dbWhere = "";
  var $dbOrderby = "";
  var $optValOrd = 0; /* the ordinal of the field to put in the option value */
  var $optDispOrd = 1; /* the ordinal of the field to display for the option */
  var $selectedText = ""; /* option item value to mark as selected */
  var $selectedIndex = ""; /* Ordinal of the selected item in the list */
  var $multiple = 1; /* Turn off multiple select lists */
  var $size = 1; /* default items to display */
  var $readonly; /* Object is readonly */
  var $disabled; /* Object is disabled */
  var $dbSubQuery; /* subquery to run to get list of values to display */
  var $subVars = array(); /* items to match for multiple selesct */
  var $preStr; /* What to display before the select */
  var $postStr; /* Text to display after the select */
  var $class; /* Stylesheet to be applied to element */

  function SelectElement( $name = "", $attrsAry ) {
    if( $name == "" ) { return( false ); }
    $this->setProperty( 'elemName', $name );
    if( is_array( $attrsAry ) ) { $this->dropdownPopulate( $attrsAry ); }
  }

  /*
   * Method to return a property value
   */
  function getProperty( $prop = "" ) {
    if( !$prop ) { return( "" ); }
    return( $this->$prop );
  }

  /*
   * Method to set the value of a particular property
   */
  function setProperty( $prop = "", $value = "" ) {
    if( !$prop ) { return( false ) ; }
    if( $prop == "elemValue" ) {
      $count = 1;
      foreach( array_keys( $this->options ) as $key ) {
#echo "DEBUG: SelectElement::setProperty: Checking selected $value against $key.<br>\n";
        if( $key == $value ) {
          $this->selectedIndex = $count;
          $this->selectedText = $value;
        }
        $count++;
      }
      return( true );
    }
    if( $prop == "dbSubQuery" ) { /* Refresh list of selected items */
      $this->$dbSubQuery = array();
      $this->$dbSubQuery = $value;
      $this->subQuery();
    }
    $this->$prop = $value;
    return( true );
  }

  function subQuery() {
    /* Run a query to find list of values to include in multiple select list */
    if( $this->multiple > 1 && $this->dbSubQuery ) {
      $sublist = mysql_query( $this->dbSubQuery );
      while ($mysql_row = mysql_fetch_array ($sublist, MYSQL_NUM)) {
        $this->subVars[] = $mysql_row[0];
      }
      mysql_free_result ($sublist);
    } else { return( false ); }
    return( true );
  }

  function showElement() {
    $select_str = $this->preStr;
    $select_str .= "<select name=\"" . $this->elemName . "\"";
    if( $this->multiple > 1 ) {
      $select_str .= " multiple size=\"" . $this->multiple . "\"";
    }
    if( $this->class ) { $select_str .= " class=\"" . $this->class . "\""; }
    $select_str  .= ">\n";
    $count = 1;
    /* Step through each stored option */
    foreach( array_keys( $this->options ) as $form_opt ) {
      $select_str .= "<option value=\"" . $form_opt . "\"";
      /* Check to see if multiple options can be selected */
      if( $this->multiple > 1 ) {
        foreach( $this->subVars as $sel ) {
          if( $sel == $form_opt ) {
            $select_str .= " selected";
          }
        }
      } else {
        if( $count == $this->selectedIndex ) { $select_str .= " selected"; }
      }
      $select_str .= ">" . $this->options[$form_opt] . "</option>\n";
      $count++;
    }
    $select_str .= "</select>\n";
    $select_str .= $this->postStr;
    return( $select_str );
  }

  function toString() { /* Synonym for showElement() */
    return( $this->showElement() );
  }

  /*
   * Function to return a dropdown select list from a database query
   * $name:  the name to give to the select form element (required)
   * $fields: the fields to select in the query (required)
   * $tables: the tables to query from (required)
   * $where: any where clause for the query (optional)
   * $orderby: any ordering for the query results (optional)
   * $val_disp: the ordinal of the field to put in the option value
   * $sel_disp: the ordinal of the field to display for the option
   * $selected: the select value to be marked as selected; * means check against the $subq list
   *   only the first field from the sublist is used for the match
   * multiple: 0 disables multiple selections; 1 enables
   * size: number of items to display in the list view
   * $subq: subquery to run to get list of values to  
   * $pre_str: string to include after the select but before the values.
   * $post_str: string to include after the values but before the /select.
   * If the method is passed an array, populate the options through the
   * array.
   */ 
  function dropdownPopulate( $attrsAry = "" ) {
    if( is_array( $attrsAry ) ) {
      while( list( $attr, $val )  = each( $attrsAry ) ) {
        $this->options[$attr] = $val;
      }
      /* Check whether $selectedText has been set. If it has, check the options
       * array and set $selectedIndex accordingly
       TODO */
      return( true );
    }
    $mysql_qry_str = "SELECT " . $this->dbFields . " FROM " . $this->dbTables;
    if( $this->dbWhere ) {
      $mysql_qry_str .= " WHERE " . $this->dbWhere;
    }
    if( $this->dbOrderby ) {
      $mysql_qry_str .= " ORDER BY " . $this->dbOrderby;
    }
    $mysql_qry = mysql_query ($mysql_qry_str) or die("Dropdown Database query failed.\n" . mysql_error() );
    $count = 1;
    while( $mysql_row = mysql_fetch_array( $mysql_qry, MYSQL_NUM ) ) {
      $val  = $mysql_row[$this->optValOrd];
      $disp = $mysql_row[$this->optDispOrd];
      $this->options[$val] = $disp;
    }
    mysql_free_result ($mysql_qry);
    return( true );
  }
}
?>
