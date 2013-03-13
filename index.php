<?
date_default_timezone_set("GMT");
ini_set("display_errors","on");
foreach(glob("includes/*.class.php") as $class_filename) {
   require_once($class_filename);
}
include("functions.php");

function executeAction(){
   global $actions;
   foreach($_GET as $key=>$value){
      if(in_array($key,$actions))
         $key();
   }
   foreach($_POST as $key=>$value){
      if(in_array($key,$actions))
         $key();
   }
}
function includeView(){
   global $views;
   foreach($_GET as $key=>$value){
      if(in_array($key,$views))
         return "views/$key.php";
   }
   return null;
}

include("includes/authenticate.php");
if($user_info["is_guest"]!=1 && $user_info["id"]!=0){
//  print_r($user_info["groups"]);
}//else
//  header("Location: smf/?action=login");

executeAction();
$view = includeView();

if($view == null)
   require_once('views/main.php');
else
   require_once($view);
?>
