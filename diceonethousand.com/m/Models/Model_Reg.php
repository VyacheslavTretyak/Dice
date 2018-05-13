<?php
require_once "DataBase.php";
class Model_Reg extends Model{	
	private $db;
	public function __construct(){
		parent::__construct();		
		$this->db = DataBase::getInstance();
	}
	public function checkLogin(){			
		$val = $_POST["val"];
		$arrPl = $this->db->sel("SELECT login FROM players WHERE login = '$val'");		
		if(!$arrPl)
			return 0;
		return 1;	
	}
	public function checkRegLogin(){		
		$val = $_POST["val"];				
		$arrPl = $this->db->sel("SELECT login FROM players WHERE login = '$val'");
		if($arrPl)
			return 0;		
		$arrSe = $this->db->sel("SELECT login FROM session WHERE login = '$val'");
		if($val == $this->user)
			return 1;
		if($arrSe)
			return 0;
		return 1;
	}	
	public function checkPass(){			
		$login = $_POST["login"];
		$val = md5($_POST["pass"]);
		$arr = $this->db->sel("SELECT * FROM players WHERE login = '$login' AND pass = '$val'");
		if($arr)
			return 1;
		return 0;
	}
	public function reg(){			
		$login = $_POST["login"];
		$email = $_POST["email"];
		$pass = md5($_POST["pass"]);
		$t = time();
		$this->user = $login;
		$coins = $this->db->sel("SELECT coins FROM session WHERE login='$this->user'");
		$coins = $coins[0] + 100;
		$res = $this->db->ins("INSERT INTO players(login, 
									email, 
									pass,
									coins,
									regDate, 
									lastSession, 
									active) VALUES 
									('$login', '$email', 
									'$pass', '$coins', '$t', '$t', '0')");		
	}
	public function sendMail($mes){
		$mes = str_replace("login", $this->user, $mes);			
		$res = $this->db->sel("SELECT email FROM players WHERE login = '$this->user'");
		if($res){
			$email = $res[0][0];
			//print_r($email);
		} else 
			exit("not email in DataBase!");		
		$sub = 'Dice Registration';
		$from = 'From: game@diceonethousand.com' . "\r\n" .
				'MIME-Version: 1.0' . "\r\n" .
				'Content-type: text/html; charset=utf-8';		       
		$sendMail = Mail($email, $sub, $mes, $from); 		
	}
	public function activator($user){
		echo $user;
		$this->db->ins("UPDATE players SET active = '1' WHERE login = '$user'");
		header("Location: http://diceonethousand.com");
	}
	public function checkActivate(){			
		$login = $_POST["login"];
		$res =$this->db->sel("SELECT active FROM players WHERE login = '$login' AND active = '1'");
		if($res)
			return 1;
		return 0;
	}
	public function getCaptcha(){			
		session_start();				
		$res = imagecreatetruecolor(466, 64);    
		$c = imageColorTransparent($res ,0);		
		$rx = 0;
		$captcha = "";
		for($i=1;$i<=6;$i++){			
			$r = rand(1, 6);
			switch($r){
				case 1:
				$pic = imagecreatefromgif('Views/img/i1.gif'); 
				break;
				case 2:
				$pic = imagecreatefromgif('Views/img/i2.gif'); 
				break;
				case 3:
				$pic = imagecreatefromgif('Views/img/i3.gif'); 
				break;
				case 4:
				$pic = imagecreatefromgif('Views/img/i4.gif'); 
				break;
				case 5:
				$pic = imagecreatefromgif('Views/img/i5.gif'); 
				break;
				case 6:
				$pic = imagecreatefromgif('Views/img/i6.gif'); 
				break;
			}         
			$captcha = $captcha.strval($r);
			imagecopy($res, $pic, $rx, 0, 0, 0, 64, 64);						
			imagedestroy($pic);
			$rx += 74;
		}
		$_SESSION["captcha"] = $captcha;
		header("Content-type: image/gif");
		imagegif($res);
		imagedestroy($res);
	}
	public function checkCaptcha(){		
		session_start();		
		$val = $_POST["val"];
		$cap = $_SESSION["captcha"];		
		if($val == $cap)
			return 1;	
		return 0;		
	}
	
}
?>