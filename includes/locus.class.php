<?php
require_once("database.class.php");
require_once("dbobject.class.php");

$effectTranslate = array(0=>"None",1=>"Magnetar",2=>"Red Giant",3=>"Pulsar",4=>"Wolf Rayet",5=>"Cata. Variable",6=>"Black Hole");
$effectValues = array(
  1=>array("10%","19%","27%","34%","41%","50%"),
  2=>array("25%","44%","55%","68%","85%","100%"),
  3=>array("10%","18%","22%","27%","34%","50%")
);
$effectDetails = array(
  0=>array(),
  1=>array(
    "Damage"=>2,
    "Missile Explosion Velocity"=>-1,
    "Drone Velocity"=>-1,
    "Targeting Range"=>-1,
    "Tracking Speed"=>-1
  ),
  2=>array(
    "Heat Damage"=>-3,
    "Overload Bonus"=>2,
    "Smart Bomb Range"=>2,
    "Smart Bomb Damage"=>2
  ),
  3=>array(
    "Shield HP"=>2,
    "Armor Resists"=>-3,
    "Capacitor Recharge"=>-1,
    "Targeting Range"=>2,
    "Signature Size"=>-2
  ),
  4=>array(
    "Armor Resists"=>3,
    "Shield Resists"=>-3,
    "Small Weapon Damage"=>2,
    "Signature Size"=>1
  ),
  5=>array(
    "Repair Amount"=>-1,
    "Shield Boost Amount"=>-1,
    "Shield Transfer Amount"=>2,
    "Remote Repair Amount"=>2,
    "Capacitor Capacity"=>2,
    "Capacitor Recharge"=>2
  ),
  6=>array(
    "Missile Velocity"=>-1,
    "Ship Velocity"=>2,
    "Drone Control Range"=>-1,
    "Inertia"=>-2,
    "Lock Range"=>-1,
    "Falloff"=>-1
  )
);

class Locus extends DBObject{

  private $statics = null;

  public static function selectAll(){
    return DBObject::makeObjectsFromQuery("SELECT * FROM Locus","Locus");
  }

  public function getStatics(){
    if($this->statics != null)
      return $this->statics;
    $this->statics=array();
    $db = Database::getDatabase();
    $result = $db->query("SELECT staticId FROM StaticLink WHERE locusId='{1}'",$this->getField('id'));
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      array_push($this->statics,new StaticInfo($row['staticId']));
    }
    return $this->statics;
  }

  public function getPosIntel(){
    $intel = array();
    $db = Database::getDatabase();
    $result = $db->query("SELECT id FROM PosIntel WHERE locusId='{1}' ORDER BY updated DESC",$this->getField('id'));
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      array_push($intel,new PosIntel($row['id']));
    }
    return $intel;
  }

  public function getIntel($type){
    $intel = array();
    $db = Database::getDatabase();
    $result = $db->query("SELECT id FROM SystemIntel WHERE locusId='{1}' AND type='{2}' ORDER BY updated DESC",$this->getField('id'),$type);
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      array_push($intel,new SystemIntel($row['id']));
    }
    return $intel;
  }

  public function getSignatures(){
    $sigs = array();
    $db = Database::getDatabase();
    $result = $db->query("SELECT id FROM SystemSignature WHERE locusId='{1}' ORDER BY updated DESC",$this->getField('id'));
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      array_push($sigs,new SystemSignature($row['id']));
    }
    return $sigs;
  }

  public static function getLocus($locus){
    $db = Database::getDatabase();
    $row = $db->query_row("SELECT id FROM Locus WHERE name='{1}'",$locus);
    return new Locus($row['id']);
  }

  public function getEffect(){
    global $effectTranslate;
    return $effectTranslate[$this->getField('effect')];
  }
  public function getShipKillHistory(){
    return SystemHistory::getSystemHistory($this->getField('id'),'1');
  }
  public function getNpcKillHistory(){
    return SystemHistory::getSystemHistory($this->getField('id'),'2');
  }
  public function getPodKillHistory(){
    return SystemHistory::getSystemHistory($this->getField('id'),'3');
  }
}

// <EOF>
?>
