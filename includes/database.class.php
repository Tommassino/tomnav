<?
require_once("config.php");

class Database{
   private static $instance;
   private $link;
   private $ctime;

   private function __construct(){
      global $database_host,$database_login,$database_password,$database_name;
      $ctime = microtime();
      $this->link = mysql_connect($database_host, $database_login, $database_password)
         or die('Server connection not possible '.$database_host."@".$database_login);
      mysql_select_db($database_name, $this->link)
         or die('Database connection not possible '.$database_name.'<br />'.mysql_error($this->link));
      mysql_set_charset("utf8",$this->link);
   }

   private function debug($query){
      global $debug;
      if(!$debug)
         return;
      echo "$query<br />";
      if(mysql_errno($this->link)!=0){
         echo $query.": ".mysql_error($this->link)."<br />";
         die();
      }
   }

   public function error(){
      return mysql_errno($this->link)!=0 ? mysql_error($this->link) : null;
   }

   public function query($query){
      $arg_list = func_get_args();
      for ($i = 1; $i < count($arg_list); $i++)
        $query=str_replace("{{$i}}",mysql_real_escape_string($arg_list[$i]),$query);
      $result = mysql_query($query, $this->link);
      $this->debug($query);
      return $result;
   }

   public function execute($query){
      call_user_func_array(array(&$this,"query"),func_get_args());
   }

   public function query_row($query){
      $result = call_user_func_array(array(&$this,"query"),func_get_args());
      if (!$result || mysql_num_rows($result) == 0)
         return null;
      return mysql_fetch_assoc($result);
   }

   public function close(){
      mysql_close();
   }

   public function getTime(){
      return microtime()-$this->ctime;
   }

   public static function getDatabase(){
      if(!self::$instance)
         self::$instance = new Database();
      return self::$instance;
   }
}
?>
