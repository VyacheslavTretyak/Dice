<?php
require_once "DataBase.php";
class Model_Data extends Model{	
	private $db;
	public function __construct(){
		parent::__construct();		
		$this->db = DataBase::getInstance();
	}	
	public function checkLogin(){		
		$val = $_POST["val"];		
		$arrPl = $this->db->sel("SELECT login FROM players WHERE login = '$val'");		
		$arrSe = $this->db->sel("SELECT login FROM session WHERE login = '$val'");
		if((!$arrPl) && (!$arrSe))
			return $val;
		return "0";
	}
	public function updateSessionStart(){		
		$t = time();		
		$ses = $this->db->sel("SELECT login FROM session WHERE login='$this->user'");
		if(!$ses)
			$this->db->ins("INSERT INTO session(login, lastTime) VALUES('$this->user', '$t')");
		else
			$this->db->ins("UPDATE session SET lastTime='$t' WHERE login='$this->user'");		
		$this->db->ins("DELETE FROM session WHERE lastTime<'$this->timeAgo'");
		$this->db->ins("DELETE FROM chat WHERE user NOT IN (SELECT login FROM players) AND user NOT IN (SELECT login FROM session)");
		$this->db->ins("DELETE FROM game WHERE user NOT IN (SELECT login FROM session)");
		$arr = $this->db->sel("SELECT player FROM game WHERE user='$this->user'");
		if(count($arr) <= 1)
			$this->db->ins("DELETE FROM game WHERE user='$this->user'");
		return $this->timeAgo;
	}
	public function updateSession(){		
		$t = time();		
		$this->db->ins("INSERT INTO session(login, lastTime) VALUES('$this->user', '$t')");		
	}
	public function loadFriends(){		
		if(!$this->login)
			return array();				
		$arr = $this->db->sel("SELECT friend FROM friends WHERE login = '$this->user' AND friend IN (SELECT login FROM session)");
		$pl = array();
		foreach($arr as $a){
			array_push($pl, $a[0]);
		}
		$user = $this->db->sel("SELECT player, status FROM game");
		foreach($user as $key=>$u){			
			$s = array_search($u[0], $pl);	
			if($s !== false)
				$arr[$s][1] = $u[1];			
		}	
		$wins = $this->db->sel("SELECT login, wins FROM players");
		foreach($wins as $w){
			$s = array_search($w[0], $pl);			
			if($s !== false)
				$arr[$s][2] = $w[1];
		}				
		return $arr;
	}
	public function loadUsers($preg){			
		if($this->login)
			$l = " AND login NOT IN (SELECT friend FROM friends WHERE login='$this->user')";
		else
			$l = "";		
		if($preg == '')			
			$s = "SELECT login FROM session WHERE login!='$this->user'".$l;			
        else			
            $s = "SELECT login FROM session WHERE login!='$this->user' AND login REGEXP '$preg'".$l;
        $arr = $this->db->sel($s);
		$pl = array();
		foreach($arr as $a){
			array_push($pl, $a[0]);
		}
		$user = $this->db->sel("SELECT player, status FROM game");
		foreach($user as $u){			
			$s = array_search($u[0], $pl);	
			if($s !== false)
				$arr[$s][1] = $u[1];			
		}		
		$wins = $this->db->sel("SELECT login, wins FROM players");
		foreach($wins as $w){
			$s = array_search($w[0], $pl);			
			if($s !== false)
				$arr[$s][2] = $w[1];
		}			
		return $arr;
	}	
	public function addFriend(){		
		$fr = $_POST["user"];
		$this->db->ins("INSERT INTO friends(login, friend) VALUES('$this->user', '$fr')");		
	}
	public function remFriend(){		
		$fr = $_POST["user"];
		$this->db->ins("DELETE FROM friends WHERE friend='$fr' AND login='$this->user'");		
	}		
	public function getCoins(){
		if($this->login){
			$res = $this->db->sel("SELECT coins FROM players WHERE login='$this->user'");
			return $res[0][0];
		}
		return -1;
	}
	public function getInvites(){
		$arr = $this->db->sel("SELECT user, bet FROM game WHERE player='$this->user' AND status='0' AND user IN (SELECT login FROM session)");
		if(!$arr){
			return array();
		}
		return $arr;
	}
	public function getAnswer(){
		$master = $this->getMaster();
		$arr = $this->db->sel("SELECT player, status FROM game WHERE user='$master'	AND player IN (SELECT login FROM session)");
		if(!$arr)
			return array();
		return $arr;
	}
	public function getMaster(){
		$arr = $this->db->sel("SELECT user FROM game WHERE player='$this->user'");
		if(!$arr)
			return 0;
		return $arr[0][0];
	}
	public function getStatus(){ 
		$arr = $this->db->sel("SELECT status FROM game WHERE player='$this->user'");
		if(!$arr)//status = 3 : гравець в грі
			return 0;
		return $arr;
	}
	public function updateChat(){		
		$res = $this->db->sel("SELECT fromUser, toUser, message, dateMess, user FROM chat WHERE user='$this->user' ORDER BY dateMess DESC");
		$this->db->ins("DELETE FROM chat WHERE user='$this->user' AND id NOT IN	(SELECT id FROM chat WHERE user='$this->user' ORDER BY dateMess DESC LIMIT 30)");
		return $res;	
	} 	
}
?>