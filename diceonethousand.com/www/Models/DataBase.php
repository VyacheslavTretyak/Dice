<?php
class DataBase{	
	private $server = "diceonet.mysql.ukraine.com.ua";
    private $user = "diceonet_db";
    private $mo = "zqbtMWkq";
    private $db = "diceonet_db";
	private $conect;
	private static $instance;
	public static function getInstance() {        
        if (null === self::$instance) {            
            self::$instance = new self();
        }        
        return self::$instance;
    }
	private function __construct(){
		$this->init();
	}
	public function __destruct(){		
		$this->close();
		unset($inctance);
	}
	private function init(){
		$this->conect = new mysqli($this->server, $this->user, $this->mo, $this->db);
		if ($this->conect->connect_error)
			exit("error connection mysql!");	
	}
    private function close(){
		$this->conect->close();
	}
	public function sel($s){	
		$q = $this->conect->query($s);		
		$res = array();		
		if($q != NULL){			
			while($r = $q->fetch_row()){				
				array_push($res, $r);
			}
		} else {			
			$res = false;
		}
		return $res;
	}
	public function ins($s){
		return $this->conect->query($s);		
	}
}
?>