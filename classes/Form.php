<?php
class Form {
  var $formName = "form";
  var $formMethod = "post";
  var $formAction = "form.php";
  var $formElements = array();
  var $preStr = " ";
  var $postStr = " ";
  var $readonly; /* All associated objects are readonly */
  var $disabled; /* All associated objects are disabled */

  /*
   * Standard constructor
   * Requires:
   *   name : form name
   *   action : form action
   *   $method : form method (post/get)
   */
  function Form( $name = "", $action = "", $method = "" ) {
    if( !$name || !$action || !$method ) { return( false ); }
    if( !eregi( "post", $method ) && !eregi( "get", $method ) ) {
      echo "Error: Form method must be either <b>get</b> or <b>post</b>.<br>\n";
      return( false );
    }
    $this->formName = $name;
    $this->formAction = $action;
    $this->formMethod = $method;
    return( true );
  }

  /*
   * Method to add an already defined element to the form
   * We take the element object and a numerical reference to the item's
   * place on the form
   */
  function addElement( $element, $position = "0" ) {
    if( ! $position ) { $this->formElements[] = $element; return( true ); }
    if( isset( $this->formElements[$position] ) ) { return( false ); }
    $this->formElements[$position] = $element;
    return( true );
  }

  /*
   * Method to remove an element from the form
   */
  function deleteElement( $position ) {
    if( !$position ) { return( false ); }
    $this->formElements[$position] = NULL;
    return( true );
  }

  /*
   * Method to return a property value
   */
  function getProperty( $prop = "" ) {
    if( !$prop ) { return( false ) ; }
    return( $this->$prop );
  }

  /*
   * Method to set the value of a particular property
   */
  function setProperty( $prop = "", $value = "" ) {
    if( !$prop ) { return( false ) ; }
    $this->$prop = $value;
    return( true );
  }

  /*
   * Method to determine whether a particular element exists
   * returns: true if it exists, false if not
   */
  function hasElement( $elem = "" ) {
    if( ! $elem ) { return( false ); }
    if( isset( $this->formElements[$elem] ) ) { return( true ); }
    return( false );
  }

  /* Method to return a string containing the markup for a named form element
   * If the Form contains an object called $elemName, that objects markup
   * is returned, otherwise false is returned.
   */
  function showElement( $elemName = "" ) {
    if( ! $elemName || ! is_object( $this->formElements[$elemName] ) ) {
      return( false );
    } else {
      return( $this->formElements[$elemName]->showElement() );
    }
  }

  /*
   * Method to display the form. If the (optional) $start and $stop values
   * are supplied, then only the elements between $start and $stop are
   * displayed.
   * $formAttrs is an optional array that contains values of elements that
   * need to be changed before display.
   */
  function showForm( $start = "", $stop = "", $formAttrs = "" ) {
    /* Save any form-passed vars for re-display of form after a user error */
    if( is_array( $formAttrs ) ) {
      while( list( $key, $val ) = each( $formAttrs ) ) {
        if( $val ) { $this->setElementAttrById( $key, "elemValue", $val ); }
      }
    }
    $wholeForm = 0;
    $startStr = $this->preStr;
    $startStr .= "<form action=\"" . $this->formAction . "\"" .
      " method=\"" . $this->formMethod . "\" " .
      "name=\"" . $this->formName . "\">";
    $endStr .= "</form>\n";
    $endStr .= $this->postStr;

    /* Return a form element indexed by name */
    if( ! $stop && $this->formElements[$start] ) {
      return( $this->formElements[$start]->showElement() );
    }
    if( $start == "head" ) { return( $startStr ); }
    if( $start == "tail" ) { return( $endStr ); }
    if( !$start && !$stop ) { $wholeForm = 1; }
    if( $wholeForm ) {
      $formStr = $startStr;
#      while ( list( sort( $key ), $val ) = each( $this->formElements ) ) {
      for( $i = 1; $i <= count( $this->formElements ); ++$i ) {
#        $formStr .= $this->formElements[$key]->showElement();
        $formStr .= $this->formElements["$i"]->showElement();
      }
      $formStr .= $endStr;
    } else { /* Only display selected elements from the form */
      /* sanity check start/stop paramters */
      if( $start < 1 ) {
        return( "Form::showForm: Cannot display negative list items." );
      }
      if( $stop > count( $this->formElements ) ) {
        return("Form::showForm(): Too many list elements requested." );
      }
      if( !$stop ) { $stop = $start; }
      if( $start > $stop ) {
        return( "Form::showForm(): Cannot display form elements in reverse " .
          "order.\n" );
      }
      $formStr = "";
      for( $i = $start; $i <= $stop; $i++ ) {
        $formStr .= $this->formElements["$i"]->showElement();
      }
    }
    return( $formStr );
  }
  function toString() { /* Synonym for showForm() */
    return( $this->showForm() );
  }

  /*
   * Method to take a form element attribute name ($elemName) and set it to
   * the given value.
   * If elem has no value, return false.
   * Also return false if element setProperty failed.
   * Else return true.
   */
  function getElementAttrById( $elem = "", $property = "" ) {
    if( !$elem || !$property) {
      echo "Form->getElementAttrById: Missing element or property.<br>\n"; return( false );
    }
    for( $i=0; $i<count( $this->formElements ); $i++ ) {
      if( $this->formElements[$elem]->elemName == $elem ) {
        return( $this->formElements[$elem]->getProperty( $property ) );
      }
      if( $this->formElements[$i]->elemName == $elem ) {
        return( $this->formElements[$i]->getProperty( $property ) );
      }
    }
    return( false );
  }

  /*
   * Method to take a form element attribute name ($elemName) and set it to
   * the given value.
   * If elem has no value, return false.
   * Also return false if element setProperty failed.
   * Else return true.
   */
  function setElementAttrById( $elem = "", $property = "", $value = "" ) {
    if( !$elem || !$property) {
      echo "Missing element or property.<br>\n"; return( false );
    }
    /* Check for both named and numeric form elements */
    for( $i=0; $i<count( $this->formElements ); $i++ ) {
      if( $this->formElements[$elem]->elemName == $elem ) {
        $this->formElements[$elem]->setProperty( $property, $value );
        return( true );
      }
      if( $this->formElements[$i]->elemName == $elem ) {
        $this->formElements[$i]->setProperty( $property, $value );
        return( true );
      }
    }
    return( true );
  }
}
?>
