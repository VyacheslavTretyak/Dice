<?php 
require_once "DataBase.php";
class Model_Begin extends Model{
	private $db;
	public function __construct(){
		parent::__construct();		
		$this->db = DataBase::getInstance();		
	}	
	public function getCountUsers(){
		$arr = $this->db->sel("SELECT player FROM game WHERE user='$this->user'");
		return count($arr);
	}	
	public function sendInvite(){
		$user = $_POST["user"];
		$bet = $_POST["bet"];
		$code = 0;		
		$c1 = $this->db->sel("SELECT player FROM game WHERE player='$user' AND (status ='1' OR status='3')");//гравець в гр≥
		$c2 = $this->db->sel("SELECT player FROM game WHERE user='$user'");//гравець створив гру
		$c3 = $this->db->sel("SELECT player FROM game WHERE player='$this->user' AND status = '1' AND user!='$this->user'");//ви в гр≥
		$c4 = $this->db->sel("SELECT player FROM game WHERE user='$this->user' AND player='$user'");//ви вже його запросили
		$coins = $this->db->sel("SELECT coins FROM players WHERE login='$user'");//кошти гравц€
		if(!$coins){
			$coins = 0;
		} else {
			$coins = $coins[0][0];
		}
		if($coins < $bet)
			$code = 6;		
		if($c1)
			$code = 1;
		if($c2)
			$code = 2;
		if($c3)
			$code = 3;
		if($c4)
			$code = 4;
		if($this->getCountUsers() >= 6)
			$code = 5;//забагато гравц≥в		
		if(!$code){
			if($this->getCountUsers() == 0)
				$this->db->ins("INSERT INTO game (player, user, bet, status) VALUES('$this->user', '$this->user', '$bet', '1')");
			$this->db->ins("INSERT INTO game (player, user, bet) VALUES('$user', '$this->user', '$bet')");			
			//$this->db->ins("UPDATE game SET bet='$bet' WHERE user='$this->user' AND player='$this->user'");
		}		
		return $code;		
	}	
	public function cancelInvite(){		
		$user = $_POST["user"];
		$this->db->ins("UPDATE game SET status='2' WHERE user='$user' AND player='$this->user'");
	}
	public function acceptInvite(){		
		$user = $_POST["user"];		
		$this->master = $user;
		$this->db->ins("UPDATE game SET status='1' WHERE user='$this->master' AND player='$this->user'");
		$this->db->ins("DELETE FROM game WHERE player='$this->user' AND status != '1'");
	}
	public function remInvite(){		
		$user = $_POST["user"];
		if($user != $this->user){
			$this->db->ins("DELETE FROM game WHERE user='$this->user' AND player='$user'");
			if($this->getCountUsers() <= 1)
				$this->db->ins("DELETE FROM game WHERE user='$this->user'");
		}
	}
	public function checkGame(){		
		$res = $this->db->sel("SELECT bet FROM game WHERE player='$this->user'");
		if(!$res){
			//$this->db->ins("INSERT INTO game (player, user, status) VALUES('$this->user', '$this->user', '1')");
			return -1;
		}		
		return $res[0][0];
	} 
}
?>