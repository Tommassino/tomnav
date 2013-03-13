<?php
require_once("database.class.php");
abstract class DBObject{
   private $data;

   public function __construct($id=null){
      if($id!=null)
         $this->fromDB($id);
   }

   protected function fromDB($id){
      $db = Database::getDatabase();
      $this->data = $db->query_row("SELECT * FROM ".get_called_class()." WHERE id=".$id);
      if(count($this->data) == 0)
        throw new Exception("Failed to load ".get_called_class()." of id $id from DB");
   }

   protected function fromData($data){
      $this->data = $data;
   }

   public function getData(){
      return $this->data;
   }

   public function getId(){
      return $this->data['id'];
   }

   public function getField($name){
      global $debug;
      if(isset($this->data[$name]))
         return $this->data[$name];
      if($debug)
         echo "Accessing inexistent field $name";
   }

   public function setField($name,$value){
      $this->data[$name]=$value;
   }

   public function update(){
      $query = "UPDATE ".get_called_class()." SET ";
      $id=1;
      $args=array();
      foreach($this->data as $key=>$value){
         if($key!="id"){
            if($value == NULL)
               $query .= "`$key`=NULL,";
            else{
               $query .= "`$key`='{{$id}}',";
               array_push($args,$value);
               $id++;
            }
         }
      }
      $query = substr($query,0,-1)." WHERE id=".$this->data['id'];
      array_unshift($args,$query);
      $db = Database::getDatabase();
      call_user_func_array(array(&$db,"query"),$args);
   }

   public function save(){
      $query = "INSERT INTO ".get_called_class()." (";
      foreach($this->data as $key=>$value){
         $query .= "`$key`,";
      }
      $query = substr($query,0,-1).") VALUES (";
      $id=1;
      $args = array();
      foreach($this->data as $key=>$value){
         if($value == NULL)
            $query .= "NULL,";
         else{
            $query .= "'{{$id}}',";
            array_push($args,$value);
            $id++;
         }
      }
      $query = substr($query,0,-1).")";
      $db = Database::getDatabase();
      array_unshift($args,$query);
      call_user_func_array(array(&$db,"query"),$args);
      $this->data['id']=mysql_insert_id();
   }

   public function delete(){
     $db = Database::getDatabase();
     $db->query("DELETE FROM ".get_called_class()." WHERE `id`='{1}'",$this->getField('id'));
   }

   public static abstract function selectAll();

   protected static function makeObjectsFromQuery($query, $table){
      $db = Database::getDatabase();
      $result = $db->query($query);
      if($result == null)
         return null;
      $d = array();
      $i = 0;
      while($row = mysql_fetch_assoc($result)){
         $in = new $table;
         $in->fromData($row);
         $d[$i]=$in;
         $i++;
      }
      return $d;
   }

   protected static function selectCount($table, $constrains){
      $query = "SELECT COUNT(*) AS c FROM $table WHERE $constrains";
      $db = Database::getDatabase();
      $result = $db->query($query);
      if($result == null)
         return 0;
      if($row = mysql_fetch_assoc($result))
         return $row['c'];
      return 0;
   }
}
?>
