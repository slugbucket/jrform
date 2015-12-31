<?php
/*
 * Class definition an methods to implement at textarea input box
 */
class Textarea {
  var $elemName; /* Required */
  var $elemValue;
  var $disabled;
  var $cols = 30;
  var $readonly;
  var $rows = 10;
  var $wrap; /* Netscape only */
  var $tabindex;
  var $onblur;
  var $onclick;
  var $onchange;
  var $onfocus;
  var $onselect;
  var $id;
  var $class;
  var $preStr = "";
  var $postStr = "";

  function Textarea( $tname = "" ) {
    if( !$tname ) { die( "Error: Cannot create unnamed textarea object." ); }
    $this->elemName = $tname;
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
   * Method to display the form element. It returns a blank string for hidden
   * input elements because they should be displayed with the
   * showHidden() method.
   */
  function showElement( ) {
    if( $this->elemName == "" ) { return( "" ); }
    $elemStr = $this->preStr . "<textarea " . "name=\"" . $this->elemName . "\" " .
               "cols=\"" . $this->cols . "\" rows=\"" . $this->rows . "\"";
    if( $this->id )       { $elemStr .= " id=\"" . $this->id . "\""; }
    if( $this->class )    { $elemStr .= " class=\"" . $this->class . "\""; }
    if( $this->disabled ) { $elemStr .= " disabled"; }
    if( $this->onclick )  { $elemStr .= " onClick='" . $this->onclick . "';"; }
    if( $this->readonly ) { $elemStr .= " readonly"; }
    $elemStr .= ">" . $this->elemValue . "</textarea>\n" . $this->postStr;
    return( $elemStr );
  }

  function toString() { /* Synonym for showElement() */
    return( $this->showElement() );
  }
}
?>
