<?php
require_once("database.class.php");
require_once("dbobject.class.php");

class CharacterInfo extends DBObject{

  public static function selectAll(){
    return DBObject::makeObjectsFromQuery("SELECT * FROM CharacterInfo","CharacterInfo");
  }

  public static function getCharacter($characterId, $characterName){
    $db = Database::getDatabase();
    $result = $db->query_row("SELECT id FROM CharacterInfo WHERE id='{1}'",$characterId);
    if (!$result){
      $ch = new CharacterInfo;
      $ch->setField('characterName',$characterName);
      $ch->setField('id',$characterId);
      $ch->save();
      return $ch;
    }
    return new CharacterInfo($characterId);
  }
}
?>
