<?php
require_once("database.class.php");
require_once("dbobject.class.php");

$types = array(1=>"Ladar",2=>"Gravimetric",3=>"Unknown",4=>"Radar",5=>"Magnetometric");

class SystemSignature extends DBObject{

  public static function selectAll(){
    return DBObject::makeObjectsFromQuery("SELECT * FROM SystemSignature","SystemSignature");
  }

  public static function findSignature($locusId, $sigId){
    $db = Database::getDatabase();
    $result = $db->query("SELECT id FROM SystemSignature WHERE locusId='{1}' AND sigId='{2}'",$locusId,$sigId);
    if (mysql_num_rows($result)>0){
      $row = mysql_fetch_array($result,MYSQL_ASSOC);
      return new SystemSignature($row["id"]);
    }
  }

  public function setType($type){
    global $types;
    if($id = array_search($type,$types))
      $this->setField('sigtype',$id);
  }

  public function getType(){
    global $types;
    return $types[$this->getField('sigtype')];
  }

  public function isOld(){
    $updated = new DateTime($this->getField('updated'));
    $updated->modify('+1 day');
    $date = new DateTime();
    return $date->diff($updated,false)->invert==1;
  }
}
?>
