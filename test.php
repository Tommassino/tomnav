<form action="" method="post">
<textarea name="data">
JDD-250				10%	
INM-888			Unstable Wormhole	100%	
NNM-628				20%	
GNM-392				30%	
KNM-384				40%	
IMS-560				10%	
LMS-811				20%	
MMS-228				30%	
KMS-394				40%	
YCV-488				50%	
HMS-143				40%	
MLR-844				30%	
</textarea>
<input type="submit" />
</form>

<?php
if(!isset($_POST["data"]))
  die();

$sigs = array();
$data = $_POST["data"];
$lines = preg_split("/\r?\n/",$data);
$locusId=31000630;

foreach($lines as $line){
  $matches = array();
  $i = preg_match("@^(.*)\t.*\t(.*)\t(.*)\t(.*)\t.*$@",$line,$matches);
  if($i==1){
    $sigId = matches[1];
    $sig = SystemSignature::findSignature($locusId, $sigId);
    

    $sig = array();
    $sig["id"]=$matches[1];
    $sig["type"]=$matches[2];
    $sig["name"]=$matches[3];
    $sig["strength"]=$matches[4];
    array_push($sigs,$sig);
  }
}
print_r($sigs);

foreach($sigs as $sig){
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
}

?>
