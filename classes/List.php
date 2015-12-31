<?php
class ItemList {
  var $template;
  /*
   * Each item in the ilist item list is an array of values
   */
  var $ilist = array();

  function ItemList() {
  }

  function addItem( $name = "", $value = "" ) {
    if( ! $name ) {
      echo "ItemList::addItem: must give an item a name.";
      return( false );
    }
    if( $this->ilist[$name] ) {
      echo "ItemList::addItem: item, $name, already exists.";
      return( false );
    }
    $this->ilist[$name] = value;
  }

  function showItem( $item = "" ) {
    if( !$item) {
      echo "ItemList::showItem(): Missing item name.";
      return( false );
    }
    return( $this->ilist[$item] );
  }
}
?>
