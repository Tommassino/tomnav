<?
require_once("database.class.php");
require_once("dbobject.class.php");

$staticClassType = array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>"highsec",8=>"lowsec",9=>"nullsec");

class StaticInfo extends DBObject{

  public static function selectAll(){
    return DBObject::makeObjectsFromQuery("SELECT * FROM StaticInfo","StaticInfo");
  }

  public function getType(){
    global $staticClassType;
    return $staticClassType[$this->getField('type')];
  }

}
?>

