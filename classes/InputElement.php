<?php
class InputElement {
  var $elemType; /* Required */
  var $elemName; /* Required */
  var $elemWidth = 0; /* if input = text */
  var $maxLength = 0;
  var $elemValue = "Enter value";
  var $readonly = false;
  var $disabled = false;
  var $size;
  var $tabindex;
  var $taborder;
  var $id = "";
  var $class;
  var $checked = ""; /* For radio and checkbox */
  /* Formatting options for the form element */
  var $elemText; /* Text to identify element in browser */
  var $placement = "left"; /* Where to display the elemText */
  var $readonly; /* Object is readonly */
  var $disabled; /* Object is disabled */
  var $preStr = "";
  var $postStr = "";
  var $hiddenAttrs = array( 'name'        => 'str',
                            'id'          => 'str',
                            'value'       => 'str',
                            'class'       => 'str',
                            'taborder'    => 'str',
                            'tabindex'    => 'str',
                            'style'       => 'str',
                            'onmouseover' => 'str',
                            'onmouseout'  => 'str' );
  var $checkboxAttrs = array( 'name'        => 'str',
                              'id'          => 'str',
                              'value'       => 'str',
                              'class'       => 'str',
                              'readonly'    => 'null',
                              'disabled'    => 'null',
                              'checked'     => 'null',
                              'taborder'    => 'str',
                              'tabindex'    => 'str',
                              'style'       => 'str',
                              'onmouseover' => 'str',
                              'onmouseout'  => 'str' );
  var $radioAttrs = array( 'name'        => 'str',
                           'id'          => 'str',
                           'value'       => 'str',
                           'class'       => 'str',
                           'readonly'    => 'null',
                           'disabled'    => 'null',
                           'checked'     => 'null',
                           'taborder'    => 'str',
                           'tabindex'    => 'str',
                           'style'       => 'str',
                           'onmouseover' => 'str',
                           'onmouseout'  => 'str' );

  /*
   * Basic conctructor to register the type and name, borking if either is
   * blank
   */
  function InputElement( $type = "", $name = "") {
    if( ! $type || ! $name ) { return( false ); }
    $this->elemType = $type;
    $this->elemName = $name;
    return( true );
  }

  /*
   * Method to return a property value
   */
  function getProperty( $prop = "" ) {
    if( !$prop ) { return( "" ) ; }
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
   * Method to register the form element attributes via an associative array
   * containing attribute name/value pairs
   * The possible attributes are passed as an associative array.
   */
  function elemRegister( $attrs ) {
    switch( $this->elemType ) {
      case 'image'    : return( $this->registerImage   ( $attrs ) ); break;
      case 'text'     : return( $this->registerText    ( $attrs ) ); break;
      case 'password' : return( $this->registerPassword( $attrs ) ); break;
      case 'button'   : return( $this->registerButton  ( $attrs ) ); break;
      case 'checkbox' : return( $this->registerCheckbox( $attrs ) ); break;
      case 'file'     : return( $this->registerFile    ( $attrs ) ); break;
      case 'hidden'   : return( $this->registerHidden  ( $attrs ) ); break;
      case 'radio'    : return( $this->registerRadio   ( $attrs ) ); break;
      case 'reset'    : return( $this->registerReset   ( $attrs ) ); break;
      case 'submit'   : return( $this->registerSubmit  ( $attrs ) ); break;
      default         : return( false );
    }
  }

  /*
   * Methods to set the required input element values. These methods should
   * not be called directly, but are referenced via the elemRegister() method
   */
  function registerText( $attrs ) {
    foreach( $attrs as $attrib => $value ) {
#      if( defined( $this->$attrib ) ) { $this->$attrib = $value; }
      $this->$attrib = $value;
    }
    return( true );
  }
  function registerHidden( $attrs ) {
    foreach( $attrs as $attrib => $value ) {
#      if( defined( $this->$attrib ) ) { $this->$attrib = $value; }
      $this->$attrib = $value;
    }
    return( true );
  }
  function registerPassword( $attrs ) {
    foreach( $attrs as $attrib => $value ) {
#      if( defined( $this->$attrib ) ) { $this->$attrib = $value; }
      $this->$attrib = $value;
    }
    return( true );
  }
  function registerSubmit( $attrs ) {
    foreach( $attrs as $attrib => $value ) {
#      if( defined( $this->$attrib ) ) { $this->$attrib = $value; }
      $this->$attrib = $value;
    }
    return( true );
  }
  function registerReset( $attrs ) {
    foreach( $attrs as $attrib => $value ) {
#      if( defined( $this->$attrib ) ) { $this->$attrib = $value; }
      $this->$attrib = $value;
    }
    return( true );
  }

  /*
   * Method to display the form element. It returns a blank string for hidden
   * input elements because they should be displayed with the
   * showHidden() method.
   */
  function showElement( ) {
    if( $this->elemType == "" ) { return( "" ); }
    $elemStr = $this->preStr . "<input type=\"" . $this->elemType . "\" " .
               "name=\"" . $this->elemName . "\" " .
               "value=\"" . $this->elemValue . "\"";
    if( $this->checked && $this->elemType == "checkbox" ) { $elemStr .= " checked"; }
    if( $this->class ) { $elemStr .= " class=\"" . $this->class . "\""; }
    if( $this->disabled ) { $elemStr .= " disabled"; }
    if( $this->readonly ) { $elemStr .= " readonly"; }
    if( $this->id ) { $elemStr .= " id=\"" . $this->id . "\""; }
    if( $this->size && $this->elemType == "text" ) {
      $elemStr .= " size=\"" . $this->size . "\"";
    }
    $elemStr .= ">" . $this->postStr;
    return( $elemStr );
  }

  function toString() { /* Synonym for showElement() */
    return( $this->showElement() );
  }
}
?>
