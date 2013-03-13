<?
echo "running cron job";
ini_set("display_errors","on");
foreach(glob("includes/*.class.php") as $class_filename) {
  require_once($class_filename);
}

function start($parser,$element_name,$element_attrs){
  if($element_name=="ROW")
    CorporationInfo::updateCorporation($element_attrs['NAME'],$element_attrs['CHARACTERID']);
}

function stop($parser,$element_name){}

function parse($data){
  $parser = xml_parser_create();
  xml_set_element_handler($parser,"start","stop");
  xml_parse($parser,$data);
  xml_parser_free($parser);
}

$names = CorporationInfo::getNames();
$query = "";
foreach($names as $name)
  $query.=",".$name;
$query = urlencode(substr($query,1));

$data = file_get_contents("https://api.eveonline.com/eve/CharacterID.xml.aspx?names=$query");
parse($data);

?>