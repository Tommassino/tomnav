<?php // content="text/plain; charset=utf-8"
date_default_timezone_set("GMT");

function kill_graph($data,$label,$color,$savefile){
  require_once ('jpgraph/jpgraph.php');
  require_once ('jpgraph/jpgraph_bar.php');

  // Create the graph. These two calls are always required
  $graph = new Graph(400,200,'auto');
  $graph->SetScale("textlin");
  $graph->SetBox(false);
  $graph->ygrid->SetFill(false);
  $graph->yaxis->HideLine(true);
  $graph->yaxis->HideTicks(false,false);

//  $graph->xaxis->title->SetColor("white");
//  $graph->yaxis->title->SetColor("white");
  $graph->xaxis->SetColor("white");
  $graph->yaxis->SetColor("white");
  $graph->SetColor("black");
  $graph->SetMarginColor("black");
  $graph->SetFrame(true,"black",1);
  $graph->title->SetColor("white");

  $bplot = new BarPlot($data);

  $graph->Add($bplot);

  $bplot->SetColor("black");
  $bplot->SetFillColor($color);
  $bplot->SetWidth(1.0);

  $graph->title->Set($label);

  $graph->img->SetImgFormat('png');

  $graph->Stroke($savefile);
}

function get_graph_array($objarr){
  $date = new DateTime();
  $data = array();
  $k = 0;
  for($i=0;$i<24;$i++){
    $date->modify('-1 hour');
    if(isset($objarr[$k])){
      $current = $objarr[$k];
      $current_date = new DateTime($current->getField('timestamp'));
      if($date->diff($current_date,false)->invert!=1){
        $k++;
        array_push($data,$current->getField('data'));
      }else
        array_push($data,0);
    }else
      array_push($data,0);
  }
  return $data;
}

if(isset($_GET['id']) && isset($_GET['type'])){
  $id=$_GET['id'];
  $type=$_GET['type'];

  $filename = "graph/".$id."_".$type.".png";
  if(file_exists($filename)){
    $hr = date("H",filemtime ($filename));
    if(date("H",(new DateTime)->getTimestamp())==$hr){
      header('Content-type: image/png');
      readfile($filename);
      die();
    }
  }
  require_once("locus.class.php");
  require_once("systemhistory.class.php");

  $locus = new Locus($id);
  $obarr = null;
  $label = 'default';
  switch($_GET['type']){
    case 0: $obarr = $locus->getShipKillHistory(); $label="Ship Kills"; break;
    case 1: $obarr = $locus->getNpcKillHistory(); $label="NPC Kills"; break;
    case 2: $obarr = $locus->getPodKillHistory(); $label="Pod Kills"; break;
  }

  $data = get_graph_array($obarr);
  kill_graph($data,$label,"#cc1111",$filename);

  header('Content-type: image/png');
  readfile($filename);
}
?>
