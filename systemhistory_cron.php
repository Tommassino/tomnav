<?
echo "running cron job";
ini_set("display_errors","on");
foreach(glob("includes/*.class.php") as $class_filename) {
   require_once($class_filename);
}

function start($parser,$element_name,$element_attrs){
  if($element_name=="ROW"){
    if(isset($element_attrs['SHIPJUMPS'])){
      $history = new SystemHistory;
      $history->setField('locusId',$element_attrs['SOLARSYSTEMID']);
      $history->setField('dataType','0');
      $history->setField('data',$element_attrs['SHIPJUMPS']);
      $history->save();
    }
    if(isset($element_attrs['SHIPKILLS'])){
      $history = new SystemHistory;
      $history->setField('locusId',$element_attrs['SOLARSYSTEMID']);
      $history->setField('dataType','1');
      $history->setField('data',$element_attrs['SHIPKILLS']);
      $history->save();
    }
    if(isset($element_attrs['FACTIONKILLS'])){
      $history = new SystemHistory;
      $history->setField('locusId',$element_attrs['SOLARSYSTEMID']);
      $history->setField('dataType','2');
      $history->setField('data',$element_attrs['FACTIONKILLS']);
      $history->save();
    }
    if(isset($element_attrs['PODKILLS'])){
      $history = new SystemHistory;
      $history->setField('locusId',$element_attrs['SOLARSYSTEMID']);
      $history->setField('dataType','3');
      $history->setField('data',$element_attrs['PODKILLS']);
      $history->save();
    }
  }
}

function stop($parser,$element_name){}

function parse($data){
  $parser = xml_parser_create();

  xml_set_element_handler($parser,"start","stop");

  xml_parse($parser,$data);

  xml_parser_free($parser);
}

$data = file_get_contents("https://api.eveonline.com/map/Jumps.xml.aspx");
parse($data);

$data = file_get_contents("https://api.eveonline.com/map/Kills.xml.aspx");
parse($data);

SystemHistory::deleteOldHistory();
?>
