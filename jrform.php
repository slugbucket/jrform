<?php
/*
 * Load the Form object class definitions
 */
include_once "classes/Form.php";
include_once "classes/InputElement.php";
include_once "classes/SelectElement.php";
include_once "classes/Textarea.php";
/* Get a connection to the database */
include_once "connect.php";

/*
 * Function to create an HTML form based on the contents of and XML document
 * $form_name: string: optional:
 *   builds a form matching $form_name in the XML document. The default is to
 *   build forms for all the items listed in the document if this is empty.
 * $form_attrs: array: optional:
 *   Array containing get and post data items submitted by a form. This is used
 *   to override any default form data in the XML document.
 * There is a priority when filling the data for the form elements.
 *  - There is an entry i the $form_attrs array (for form re-display). 
 *  - Data from a database query. This can be subject to an internal function
 *    so that the DB data can be properli presented, e.g, timestamp to date
 *    display.
 *  - Default data from the XML document.
 * Returns: array of form objects indexed by form name.
 */
function make_form( $xml_src = "", $form_attrs = "", $queries = "", $f_name = "" ) {
  /* Read in the XML document and create a DOM object */
  #$xml_str = file_get_contents( $xml_src );
  if( ! $form_dom = domxml_open_file( $xml_src ) ) {
    die( "Cannot find XML source dcument, $xml_src.<br>\n" );
  }
  $formlist = $form_dom->document_element();
  /* Get the list of database queries */
  /* NOT YET IMPLEMENTED
  $query_ary = $formlist->get_elements_by_tagname( 'query' );
  
  for ($i = 0; $i<count($query_ary); $i++) {
  }
  */
  /* Get the list of forms */
  $forms_ary = $formlist->get_elements_by_tagname( 'form' );
  for( $i = 0; $i < count( $forms_ary ); $i++ ) {
    $xml_form     = $forms_ary[$i];
    $form_name    = $xml_form->get_attribute( "name"   );
    $form_query   = $xml_form->get_attribute( "query"  );
    $form_method  = $xml_form->get_attribute( "method" );
    $form_pre     = $xml_form->get_attribute( "pre"    );
    $form_post    = $xml_form->get_attribute( "post"   );
    $form_nodata  = $xml_form->get_attribute( "nodata" );
    $form_action  = $xml_form->get_attribute( "action" );
    /* Use this script if nothing is specified */
    if( ! $form_action ) {
      $form_action = $_SERVER[PHP_SELF];
    }
    if( $form_name == $f_name || $f_name == "" ) {
      /* Check to see if any database queries have been specified and populate
       * the form fields as appropriate.
       * For each row returned, we will need to see if the fields match any
       * form element and update the form. Queries extracting rows for inclusion
       * in a SELECT element may return numerous rows which may override the
       * options given in the XML document, or one entry may simply choose the
       * selected item.
       */
      if( $queries[$form_query] ) {
        if( ! $qry_res_ary[$form_query] ) {
//         echo "DEBUG: Running query " . $queries[$form_query] .
//         " for form, " . $form_name . ".<br>\n";
          $qry_res_ary[$form_query] = mysql_query( $queries[$form_query] );
          if( ! mysql_numrows( $qry_res_ary[$form_query] ) &&
            $form_nodata == "break" ) {
            continue;
          }
        }
      }
      /* Check what to do if the query returns no results */
      
      $form_ary[$form_name] = new Form( $form_name, $form_action, $form_method );
      $form_ary[$form_name]->setProperty( "preStr", $form_pre );
      $form_ary[$form_name]->setProperty( "postStr", $form_post );
      $input_elems  = $xml_form->get_elements_by_tagname( 'input' );
      for( $j = 0; $j < count( $input_elems ); ++$j ) {
        $xml_input              = $input_elems[$j];
        $input_order            = 0;
        $input_name             = $xml_input->get_attribute( 'name'     );
        $input_type             = $xml_input->get_attribute( 'type'     );
        $input_class            = $xml_input->get_attribute( 'class'    );
        $input_order            = $xml_input->get_attribute( 'order'    );
        $elem_attrs['class']    = $xml_input->get_attribute( 'class'    );
        $input_disabled         = $xml_input->get_attribute( 'disabled' );
        $elem_attrs['disabled'] = $xml_input->get_attribute( 'disabled' );
        $input_readonly         = $xml_input->get_attribute( 'readonly' );
        $elem_attrs['readonly'] = $xml_input->get_attribute( 'readonly' );
        $elem_attrs['preStr']   = $xml_input->get_attribute( 'pre'      );
        $elem_attrs['postStr']  = $xml_input->get_attribute( 'post'     );
        /* Retain a form submitted value rather than the XML value */
        if( $form_attrs[$input_name] ) {
          $elem_attrs['elemValue'] = $form_attrs[$input_name];
        } else {
          $elem_attrs['elemValue'] = $xml_input->get_content();
        }
        if( ! $input_order ) {
          $input_order = $input_name;
        }
        $input_elem = new InputElement( $input_type, $input_name );
        $input_elem->elemRegister( $elem_attrs );
        $form_ary[$form_name]->addElement( $input_elem, $input_order );
      }
      /*
       * Extract any dropdown (select) lists
       * The fill attribute can be one of:
       *   db: use only the values from the database, where the value attribute
       *     gives the name of the column to use for the option value, and the
       *     display attribute gives the name of the column to use for the
       *     displayed text for the option.
       *   select: The option list is populated from the XML file with the
       *     value attribute used to specify which column should be matched to
       *     provide a selected option. If the XML value attribute has no
       *     value, no match is attempted.
       *   file: The option list is populated from the database query where
       *     the value attribute gives the name of the column to use for the
       *     option value, and the display attribute gives the name of the
       *     column to use for the displayed text for the option. Any of the
       *     dropdown elements value attribute values that match the database
       *     column will be marked 'selected'.
       */
      $select_elems = $xml_form->get_elements_by_tagname( 'dropdown' );
      for( $j = 0; $j < count( $select_elems ); ++$j ) {
        $xml_select      = $select_elems[$j];
        $select_name     = $xml_select->get_attribute( 'name' );
        $select_fill     = $xml_select->get_attribute( 'fill' );
        $select_order    = $xml_select->get_attribute( 'order' );
        $select_class    = $xml_select->get_attribute( 'class' );
        $select_value    = $xml_select->get_attribute( 'value' );
        $select_multiple = $xml_select->get_attribute( 'multiple' );
        $select_disabled = $xml_select->get_attribute( 'disabled' );
        $select_size     = $xml_select->get_attribute( 'size' );
        $select_pre      = $xml_select->get_attribute( 'pre' );
        $select_post     = $xml_select->get_attribute( 'post' );
        $select_subq     = $xml_select->get_attribute( 'opt_query' );
        $select_opts     = $xml_select->get_elements_by_tagname( 'option' );

        $sel_opt_ary = array();
        for( $k = 0; $k < count( $select_opts ); ++$k ) {
          $select_option = $select_opts[$k];
          $option_val = $select_option->get_attribute( 'value' );
          $option_disp = $select_option->get_content();
          $sel_opt_ary[$option_val] = $select_option->get_content();
        }
        $select_obj = new SelectElement( $select_name, $sel_opt_ary );
        $select_obj->setProperty( "class",     $select_class    );
        $select_obj->setProperty( "multiple",  $select_multiple );
        $select_obj->setProperty( "sub_query", $select_subq     );
        $select_obj->setProperty( "size",      $select_size     );
        $select_obj->setProperty( "disabled",  $select_disabled );
        $select_obj->setProperty( "preStr",    $select_pre      );
        $select_obj->setProperty( "postStr",   $select_post     );
        /*
         * Check the database query for a field that matches the value
         * attribute for this form and, if not set via POST/GET, apply the
         * db value to the form object
         * FIXME: This is very inefficient considering that we also hack
         * through the array in the same way below, if not for the
         * same reasons.
         */
        if( $qry_res_ary[$form_query]  && ! $form_attrs[$select_name] ) {
          for( $k = 0; $k < mysql_numrows( $qry_res_ary[$form_query] ); ++$k ) {
            $qry_row = mysql_fetch_array( $qry_res_ary[$form_query], MYSQL_ASSOC );
            if( is_array( $qry_row ) ) {
              while( list( $col, $value ) = each( $qry_row ) ) {
                if( $col == $select_value ) {
                  $select_obj->setProperty( "elemValue", $value );
                }
              }
            }
          }
        }
        $form_ary[$form_name]->addElement( $select_obj, $select_order );
      }
      /*
       * Check the database query to see if there are any updates that need
       * to be applied to the form. Don't update the form object if the
       * form_attr array already contains a value, i.e., has been populated
       * by a form submission.
       */
      if( $qry_res_ary[$form_query] &&
          mysql_numrows( $qry_res_ary[$form_query] ) ) {
        /* Reset the database rows */
        mysql_data_seek( $qry_res_ary[$form_query], 0 );
        for( $k = 0; $k < mysql_numrows( $qry_res_ary[$form_query] ); ++$k ) {
          $qry_row = mysql_fetch_array( $qry_res_ary[$form_query],
                                        MYSQL_ASSOC );
          while( list( $col, $value ) = each( $qry_row ) ) {
            if( ! $form_attrs[$col] ) {
              $form_ary[$form_name]->setElementAttrById( $col, "elemValue", $value );
            }
          }
        }
        mysql_freeresult( $qry_res_ary[$form_query] );
      }
#      $form_str = $form_ary[$form_name]->showForm( "", "", $form_attrs );
#      echo $form_str;
    }
  }
  return( $form_ary );
}

?>
