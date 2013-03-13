<?php
require_once("database.class.php");
require_once("dbobject.class.php");

class PosIntel extends DBObject{

  public static function selectAll(){
    return DBObject::makeObjectsFromQuery("SELECT * FROM PosIntel","PosIntel");
  }

  public function getAuthor(){
    return new CharacterInfo($this->getField('characterId'));
  }

  public function getCorporation(){
    $id = $this->getField('corporationId');
    if($id==null)
      return null;
    return new CorporationInfo($this->getField('corporationId'));
  }

  public static function findPos($locusId, $location){
    $db = Database::getDatabase();
    $result = $db->query("SELECT id FROM PosIntel WHERE locusId='{1}' AND location='{2}'",$locusId,$location);
    if (mysql_num_rows($result)>0){
      $row = mysql_fetch_array($result,MYSQL_ASSOC);
      return new PosIntel($row["id"]);
    }
  }
}
?>
