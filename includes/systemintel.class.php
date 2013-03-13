<?php
require_once("database.class.php");
require_once("dbobject.class.php");

class SystemIntel extends DBObject{

  public static function selectAll(){
    return DBObject::makeObjectsFromQuery("SELECT * FROM SystemIntel","SystemIntel");
  }

  public function getAuthor(){
    return new CharacterInfo($this->getField('characterId'));
  }
}
?>
