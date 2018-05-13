<?php
require_once "DataBase.php";
class Model_Game extends Model{
	private $db;
	public $master;
	public $iAmMaster;
	public $countPl;
	
	public function __construct(){
		parent::__construct();
		$this->db = DataBase::getInstance();
		$this->master = $this->getMaster();
		$this->iAmMaster = $this->iAmMasterF();
		$this->countPl = $this->countPlayers();		
	}
	private function iAmMasterF(){
		if($this->user == $this->master)
			return 1;
		return 0;		
	}
	private function getMaster(){
		$arr = $this->db->sel("SELECT user FROM game WHERE player='$this->user'");		
		if(!$arr)
			return 0;
		return $arr[0][0];
	}
	private function countPlayers(){
		$arr = $this->db->sel("SELECT player FROM game WHERE status='3' AND user='$this->master'");
		if(!$arr)
			return 0;		
		return count($arr);
	}
	public function updateSession(){		
		$t = time();		
		$this->db->ins("UPDATE session SET lastTime='$t' WHERE login='$this->user'");		
	}
	public function getBank(){	
		$arr = $this->db->sel("SELECT bank FROM game WHERE player='$this->user'");
		return $arr[0][0];
	}
	public function getBonus(){	
		$arr = $this->db->sel("SELECT bonus FROM game WHERE player='$this->user'");
		return $arr[0][0];
	}
	public function startGame(){	
		if($this->iAmMaster){
			$this->db->ins("DELETE FROM game WHERE user='$this->user' AND status!='1'");
			$this->db->ins("UPDATE game SET status='3' WHERE user='$this->user'");				
			$countPlayers = $this->countPlayers();			
			$this->db->ins("UPDATE game SET bank=bet*'$countPlayers' WHERE user='$this->user'");
			$this->master = $this->getMaster();
			$this->iAmMaster = $this->iAmMasterF();
			$this->countPl = $this->countPlayers();			
			$this->updateTurn();			
		}
	}
	private function updateTurn(){ 
		$this->countPl = $this->countPlayers();
		$arr = $this->db->sel("SELECT status, progress, turn, player FROM game WHERE user='$this->master'ORDER BY turn");
		$turn = 1;		
		$progGet = 1;			
		foreach($arr as $a){			
			if($a[1] == 1){
				$progGet = $a[2];
				if($progGet > $this->countPl)
					$progGet = 1;
			}			
			if($a[0] == 3){
				$this->db->ins("UPDATE game SET turn='$turn' WHERE player='$a[3]'");
				$turn++;
			}
			if($a[0] == 4){
				$this->db->ins("UPDATE game SET progress='0', turn='0' WHERE status='4' AND player='$a[3]'");
			}
		}		
		$this->db->ins("UPDATE game SET progress='1' WHERE turn='$progGet' AND user='$this->master'");
	}
	public function loadPlayers(){
		$arr = $this->db->sel("SELECT player, status, progress, pts, sum, total, user, dash FROM game WHERE user='$this->master' ORDER BY turn");
		if(!$arr)//status=4 якщо гравець вибув з гри
			return array();
		return $arr;
	}
	public function updateChat(){		
		$res = $this->db->sel("SELECT fromUser, toUser, message, dateMess, user FROM chat WHERE user='$this->user' ORDER BY dateMess DESC");
		$this->db->ins("DELETE FROM chat WHERE user='$this->user' AND id NOT IN	(SELECT id FROM chat WHERE user='$this->user' ORDER BY dateMess DESC LIMIT 30)");
		return $res;	
	} 		
	public function remPlayerGame(){
		$user = $_POST["user"];
		$this->db->ins("UPDATE game SET status='4' WHERE player='$user'");
		$this->updateTurn();
		
	}
	public function breakGame(){	
		if($this->iAmMaster){
			$this->db->ins("DELETE FROM game WHERE user='$this->master'");
		} else {			
			$this->db->ins("UPDATE game SET status='4' WHERE player='$this->user'");
			$this->db->ins("UPDATE players SET coins=coins-bet WHERE login='$this->user'");
			$this->updateTurn();			
		}
	}
	public function getMove(){
		$data = $this->db->sel("SELECT diceValue, progress, stage, endTurn FROM game WHERE player='$this->user'");		
		if(!$data)
			return 0;
		return $data[0];
	}
	public function getStage(){
		$data = $this->db->sel("SELECT stage FROM game WHERE player='$this->user'");		
		if(!$data)
			return 0;
		return $data[0][0];
	}
	public function getImgDice($dice){					
		$res = imagecreatetruecolor(400, 400);
		imageColorTransparent($res ,0);			
		$move = array();
		for($i=0;$i<strlen($dice);$i++){			
			$r = $dice[$i];
			switch($r){
				case 1:
				$pic = imagecreatefrompng('Views/img/i1.png'); 
				break;
				case 2:
				$pic = imagecreatefrompng('Views/img/i2.png'); 
				break;
				case 3:
				$pic = imagecreatefrompng('Views/img/i3.png'); 
				break;
				case 4:
				$pic = imagecreatefrompng('Views/img/i4.png'); 
				break;
				case 5:
				$pic = imagecreatefrompng('Views/img/i5.png'); 
				break;
				case 6:
				$pic = imagecreatefrompng('Views/img/i6.png'); 
				break;
			}			
			$angle = rand(0, 45);
			$pic = imagerotate($pic, $angle, 0);			
			$coord = $this->checkImgDice(rand(0, 308), rand(0, 308), $move);		
			$rx = $coord[0];
			$ry = $coord[1];				 
			array_push($move, array($rx, $ry));						
			imagecopy($res, $pic, $rx, $ry, 0, 0, 92, 92);						
			imagedestroy($pic);							
		}		
		header("Content-type: image/png");
		imagepng($res);
		imagedestroy($res);		
	}
	private function checkImgDice($rx, $ry, $move){		
		foreach($move as $m){
			if(($rx > ($m[0]-92)) && ($rx < ($m[0]+92)) && ($ry > ($m[1]-92)) && ($ry < ($m[1]+92))){
				$rx = $m[0]+92;
				if($rx > 307){
					$rx = 0;
					$ry = $m[1]+92;	
					if($ry > 307){
						$ry = 0;
						$rx = 0;
					}					
				}
			$coord = $this->checkImgDice(rand(0, 308), rand(0, 308), $move);		
			$rx = $coord[0];
			$ry = $coord[1];
			}
		}
		return array($rx, $ry);
	}
	public function newDiceValue($cnt){
		$dice = "";
		for($i=1;$i<=$cnt;$i++){
			$dice = $dice.rand(1, 6);
		}		
		$this->db->ins("UPDATE game SET diceValue='$dice' WHERE user='$this->master'");
		return $dice;
	}
	public function move(){
		$arr = $this->db->sel("SELECT turn FROM game WHERE player='$this->user'");
		$turn = $arr[0][0];		
		$turn++;		
		if($turn > $this->countPl)
			$turn = 1;
		$this->db->ins("UPDATE game SET progress='0' WHERE player='$this->user'");
		$this->db->ins("UPDATE game SET progress='1', countDice='5' WHERE turn='$turn' AND user='$this->master'");		
	}
	public function sortition(){
		$dice = $this->newDiceValue(5);		
		$val = 0;
		for($i = 0;$i<(strlen($dice));$i++){
			$val += $dice[$i];			
		}
		$data = $this->db->sel("SELECT pts FROM game WHERE user='$this->master'");
		foreach($data as $d){
			if($val == $d[0]){
				return 0;
			}			
		}	
		$t = $this->db->sel("SELECT turn FROM game WHERE player='$this->user'");
		$t = $t[0][0];
		$this->db->ins("UPDATE game SET pts='$val' WHERE player='$this->user'");
		$data = $this->db->sel("SELECT player FROM game WHERE user='$this->master' AND pts='0' AND status='3'");
		if(!$data){		
			return 2;
		}
		return 1;
	}
	public function endSortition(){
		$data = $this->db->sel("SELECT player, pts FROM game WHERE user='$this->master' ORDER BY pts DESC");
		$turn = 1;
		foreach($data as $d){
			$this->db->ins("UPDATE game SET turn='$turn', progress='0', stage='1', pts='0' WHERE player='$d[0]'");
			$turn++;
		}
		$this->db->ins("UPDATE game SET progress='1' WHERE turn='1' AND user='$this->master'");
	}		
	public function gameProcess(){		
		$endTurn = 0;
		$arr = $this->db->sel("SELECT sum, dash, total, turn, stage, countDice, bonus FROM game WHERE player='$this->user'");		
		$stage = $arr[0][4];
		$countDice = $arr[0][5];
		if($stage == 2){			
			$countDice = 5;
		}
		if($countDice <1)
			$endTurn = 1;		
		$turn = $arr[0][3];
		$dash = $arr[0][1];
		$total = $arr[0][2];
		$sum = $arr[0][0];		
		$dice = $this->newDiceValue($countDice);
		$res = $this->toCountValue($dice);
		$val = $res[0];
		$count = $res[1];
		$bonus = $arr[0][6] + $res[2];		
		$sum = $sum+$val;
		switch ($stage){
		case 1:
			if($sum >= 50)
				$stage = 2;			
			$countDice = $countDice - $count;			
			break;
		case 2:
			if($val == 0)
				$stage = 1;
			if($val > 0)
				$stage = 3;
			break;
		}	
		if($val == 1000){
			$total = 1000;			
		}
		if($val==0){
			$endTurn = 1;
			$dash++;		
			$sum = 0;			
			if($dash >= 3){
				$dash = 0;
				if($stage == 3)
					$total -= 50;
			}
		} else {
			$dash = 0;
		}
		if(($total <300) && ($total >= 200))
			if($sum+$total >300){
				$sum = 0;
				$endTurn = 1;
			}
		if(($total <700) && ($total >= 600))
			if($sum+$total >700){
				$sum = 0;
				$endTurn = 1;
			}
		if(($total <1000) && ($total >= 900))
			if($sum+$total >1000){
				$sum = 0;
				$endTurn = 1;
			}
		
		if($total == 555){
			$total = 0;
			$endTurn = 1;
		}		
		$this->db->ins("UPDATE game SET pts='$val', sum='$sum',	dash='$dash', countDice='$countDice', total='$total', stage='$stage', endTurn='$endTurn', diceValue='$dice', bonus='$bonus'	WHERE player='$this->user'");
		echo "bonus = ".$bonus;
	}
	private function checkLider($total,$sum){
		$old = $total - $sum;		
		$arr = $this->db->sel("SELECT player, total FROM game WHERE user='$this->master' AND player != '$this->user'");
		foreach($arr as $a){			
			if(($old<$a[1]) && ($total>$a[1])){
				$t = $a[1] - 50;
				$this->db->ins("UPDATE game SET total='$t' WHERE player='$a[0]'");
			}				
		}
	}	
	public function endMove(){
		$arr = $this->db->sel("SELECT total, sum FROM game WHERE player='$this->user'");
		$total = $arr[0][0];
		$sum = $arr[0][1];
		if(($total >=200)&&($total < 300)){
			if(($total+$sum) == 300){
				$total += $sum;
			}
		} else {
			if(($total >=600)&&($total < 700)){
				if(($total+$sum) == 700){
					$total += $sum;
				}
			} else {
				if(($total >=900)&&($total < 1000)){
					if(($total+$sum) == 1000){
						$total += $sum;
						$this->win();
					}
				} else {
					if(($total+$sum) <= 1000)
						$total += $sum;
				}
			}
		}
		if($total == 555){
			$total = 0;
		}			
		/* if($total >= 100){
			$this->win();
			$total = 1000;
			
		} */
		$this->checkLider($total, $sum);		
		$this->db->ins("UPDATE game SET total='$total', sum='0', pts='0', endTurn='0' WHERE player='$this->user'");		
	}
	private function win(){		
		$bank = $this->getBank();
		$res = $this->db->sel("SELECT player, bet FROM game WHERE user='$this->user'");
			foreach($res as $a){
				$this->db->ins("UPDATE players SET coins=coins-'$a[1]' WHERE login='$a[0]'");
			}
		$this->db->ins("UPDATE players SET wins=wins+1, coins=coins+'$bank' WHERE login='$this->user'");
		$arr = $this->db->sel("SELECT player, bonus FROM game WHERE user='$this->user' AND status='3'");
		foreach($arr as $a){			
			$this->db->ins("UPDATE players SET coins=coins+'$a[1]' WHERE login='$a[0]'");
		}
	}
	private function toCountValue($dice){
		$val = 0;
		$count = 0;
		$bonus = 0;
		$bank = $this->getBank();
		$arr = array_pad(array(), 7, 0);
		for($i=0;$i<strlen($dice);$i++){
			$arr[$dice[$i]]++;
		}
		if(($arr[1]==1)&&($arr[2]==1)&&($arr[3]==1)&&($arr[4]==1)&&($arr[5]==1)){
			$val += 125;
			$count = 5;
			$bonus = $bank * 5 / 100;
			return array($val, $count, $bonus);
		}
		if(($arr[6]==1)&&($arr[2]==1)&&($arr[3]==1)&&($arr[4]==1)&&($arr[5]==1)){
			$val += 250;
			$count = 5;
			$bonus = $bank * 5 / 100;
			return array($val, $count, $bonus);
		}
		if($arr[1]==1){
			$val += 10;
			$count += 1;
		}
		if($arr[1]==2){
			$val += 20;
			$count += 2;
		}
		if($arr[1]==3){
			$val += 100;
			$count += 3;
		}
		if($arr[1]==4){
			$val += 200;
			$count += 4;
			$bonus = $bank * 10 / 100;
		}
		if($arr[1]==5){
			$val += 1000;
			$count += 5;
			$bonus = $bank;
		}		
		if($arr[2]==3){
			$val += 20;
			$count += 3;
		}
		if($arr[2]==4){
			$val += 40;
			$count += 4;
			$bonus = $bank * 10 / 100;
		}
		if($arr[2]==5){
			$val += 200;
			$count += 5;
			$bonus = $bank * 50 / 100;
		}
		
		if($arr[3]==3){
			$val += 30;
			$count += 3;
		}
		if($arr[3]==4){
			$val += 60;
			$count += 4;
			$bonus = $bank * 10 / 100;
		}
		if($arr[3]==5){
			$val += 300;
			$count += 5;
			$bonus = $bank * 50 / 100;
		}
		
		if($arr[4]==3){
			$val += 40;
			$count += 3;
		}
		if($arr[4]==4){
			$val += 80;
			$count += 4;
			$bonus = $bank * 10 / 100;
		}
		if($arr[4]==5){
			$val += 400;
			$count += 5;
			$bonus = $bank * 50 / 100;
		}
		
		if($arr[5]==1){
			$val += 5;
			$count += 1;
		}
		if($arr[5]==2){
			$val += 10;
			$count += 2;
		}
		if($arr[5]==3){
			$val += 50;
			$count += 3;
		}
		if($arr[5]==4){
			$val += 100;
			$count += 4;
			$bonus = $bank * 10 / 100;
		}
		if($arr[5]==5){
			$val += 500;
			$count += 5;
			$bonus = $bank * 50 / 100;
		}
		
		if($arr[6]==3){
			$val += 60;
			$count += 3;
		}
		if($arr[6]==4){
			$val += 120;
			$count += 4;
			$bonus = $bank * 10 / 100;
		}
		if($arr[6]==5){
			$val += 600;		
			$count += 5;
			$bonus = $bank * 50 / 100;
		}				
		return array($val, $count, $bonus);	
	}	
}
?>