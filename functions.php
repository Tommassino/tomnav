<?
$views = array();
$actions = array('aaddintel','aaddpos','aaddsigs');

function filter($data){
  Database::getDatabase();
  $data = trim(htmlentities(strip_tags($data)));
  if(get_magic_quotes_gpc())
    $data = stripslashes($data);
  return $data;
}

foreach($_POST as $key=>$value){
  $_POST[$key]=filter($value);
}

function aaddintel(){
  if(!isset($_POST['locusid']) || !isset($_POST['type']) || !isset($_POST['data']) || !isset($_POST['characterid']) || !isset($_POST['redirect']))
    die();
  $intel = new SystemIntel;
  $intel->setField('locusId',$_POST['locusid']);
  $intel->setField('type',$_POST['type']);
  $intel->setField('data',$_POST['data']);
  $intel->setField('characterId',$_POST['characterid']);
  $intel->save();
  header("Location: ".$_POST['redirect']);
}

function aaddsigs(){
  if(!isset($_POST['data']) || !isset($_POST['locusid']) || !isset($_POST['redirect']))
    die();
  $data = $_POST['data'];
  $lines = preg_split("/\r?\n/",$data);
  $locusId = $_POST['locusid'];
  $strengthData = array();
  foreach($lines as $line){
    $matches = array();
    $i = preg_match("@^(.*)\t.*\t(.*)\t(.*)\t(.*)\t.*$@",$line,$matches);
    if($i==1){
      $sigId=$matches[1];
      $sig = SystemSignature::findSignature($locusId, $sigId);
      $news=false;
      if($sig==null){
        $sig = new SystemSignature;
        $news=true;
      }
      $sig->setField('locusId',$locusId);
      $sig->setField('sigid',$matches[1]);
      if($sig->getField('sigtype')==null)
        $sig->setType($matches[2]);
      if($sig->getField('signame')==null)
        $sig->setField('signame',$matches[3]);
      if($news)
        $sig->save();
      else
        $sig->update();
      $sigId = $sig->getField('id');
      array_push($strengthData,array("id"=>$sigId,"strength"=>$matches[4]));
    }
  }
  print_r($strengthData);
}

function aaddpos(){
  if(!isset($_POST['dscan']) || !isset($_POST['characterid']) || !isset($_POST['corporationname']) || !isset($_POST['redirect']) || !isset($_POST['locusid']))
    die();
  $AU=149597871;

  $types = array();
  $location = null;
  $distance = 200*$AU;
  $tower=null;

  $dscan = $_POST['dscan'];
  $lines = preg_split("/\r?\n/",$dscan);
  foreach($lines as $line){
    $matches = array();
    $i = preg_match("@^(.*)\t((?:(?!(Customs|Planet|Sun|Force|Corpse)).)*)\t(.*?)(km|AU|m)$@",$line,$matches);
    if($i==1){
      if($matches[2]=="Moon"){
        $c="\x02\x00";
        $dist=preg_replace(array("|,|","|[^\d\\.]|"),array(".",""),$matches[4]);
        if($matches[5]=="AU")
          $dist=$dist*$AU;
        else if($matches[5]=="m")
          $dist=$dist/1000;
        if($dist<$distance){
          $location=$matches[1];
          $distance=$dist;
        }
      }else{
        if(preg_match("@.*Control Tower.*@",$matches[2])==1)
          $tower=$matches[2];
        else{
          if(!array_key_exists($matches[2],$types))
            $types[$matches[2]]=0;
          $types[$matches[2]]++;
        }
      }
    }
  }
  if($tower==null || $location==null){
    echo "invalid input";
    return;
  }
  $location = preg_replace(array("@J[0-9]+ @","@Moon@"),"",$location);
  $spl = split("-",$location);
  $location = "P".roman_to_decimal(trim($spl[0]))."-M".trim($spl[1]);

  $intel = PosIntel::findPos($_POST['locusid'],$location);
  $newi = false;
  if($intel==null){
    $intel = new PosIntel;
    $newi = true;
  }
  $intel->setField('locusId',$_POST['locusid']);
  $intel->setField('location',$location);
  $intel->setField('posType',$tower);
  $modules = "";
  foreach($types as $key=>$value)
    $modules.="$value x $key<br />";
  $intel->setField('modules',$modules);
  $intel->setField('characterId',$_POST['characterid']);
  $corp = CorporationInfo::getCorporation(trim($_POST['corporationname']));
  $intel->setField('corporationId',$corp->getField('id'));
  if($newi)
    $intel->save();
  else
    $intel->update();
  header("Location: ".$_POST['redirect']);
}

$romans = array(
  'M' => 1000,
  'CM' => 900,
  'D' => 500,
  'CD' => 400,
  'C' => 100,
  'XC' => 90,
  'L' => 50,
  'XL' => 40,
  'X' => 10,
  'IX' => 9,
  'V' => 5,
  'IV' => 4,
  'I' => 1,
);

function roman_to_decimal($roman){
  global $romans;
  $result = 0;
  foreach($romans as $key=>$value){
    while(strpos($roman,$key)===0){
      $result += $value;
      $roman = substr($roman,strlen($key));
    }
  }
  return $result;
}
?>
