<?php
require_once("database.class.php");
require_once("dbobject.class.php");

class SystemHistory extends DBObject{

  public static function selectAll(){
    return DBObject::makeObjectsFromQuery("SELECT * FROM SystemHistory","SystemHistory");
  }

  public static function getSystemHistory($systemId, $type){
   return DBObject::makeObjectsFromQuery("SELECT * FROM SystemHistory WHERE locusId='{1}' AND dataType='{2}' ORDER BY `timestamp` DESC","SystemHistory",$systemId,$type);
  }

  public static function deleteOldHistory(){
    $db = Database::getDatabase();
    $db->query("DELETE FROM SystemHistory WHERE `timestamp`<DATE_SUB(NOW(),INTERVAL 24 HOUR)");
  }
}

// <EOF>
?>
