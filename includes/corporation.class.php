<?php
require_once("database.class.php");
require_once("dbobject.class.php");

class CorporationInfo extends DBObject{

  public static function selectAll(){
    return DBObject::makeObjectsFromQuery("SELECT * FROM CorporationInfo","CorporationInfo");
  }

  public static function getCorporation($corporationName){
    $db = Database::getDatabase();
    $result = $db->query("SELECT id FROM CorporationInfo WHERE LOWER(corporationName)=LOWER('{1}')",$corporationName);
    if (mysql_num_rows($result)>0){
      $row = mysql_fetch_array($result,MYSQL_ASSOC);
      return new CorporationInfo($row['id']);
    }else{
      $corp = new CorporationInfo;
      $corp->setField("corporationName",$corporationName);
      $corp->save();
      return $corp;
    }
  }

  public static function getNames(){
    $db = Database::getDatabase();
    $names = array();
    $result = $db->query("SELECT corporationName FROM CorporationInfo WHERE ccpId IS NULL");
    while($row = mysql_fetch_array($result,MYSQL_ASSOC))
      array_push($names,$row['corporationName']);
    return $names;
  }

  public static function updateCorporation($name,$ccpId){
    $db = Database::getDatabase();
    $result = $db->query("SELECT id FROM CorporationInfo WHERE LOWER(corporationName)=LOWER('{1}')",$name);
    if (mysql_num_rows($result)>0){
      $row = mysql_fetch_array($result,MYSQL_ASSOC);
      $corp = new CorporationInfo($row['id']);
      $corp->setField('corporationName',$name);
      $corp->setField('ccpId',$ccpId);
      $corp->update();
    }
  }
}
?>