<?php
require_once "DataBase.php";
class Model_Chat extends Model{	
	private $db;
	public function __construct(){
		parent::__construct();		
		$this->db = DataBase::getInstance();
	}
	public function sendMess(){
		$to = $_POST["to"];
		$mess = $_POST["mess"];
		$dateMess = time();
		$this->db->ins("INSERT INTO chat(
			fromUser, toUser, message, dateMess, user)
			VALUES
			('$this->user', '$to', '$mess', '$dateMess', '$this->user')");
		$this->db->ins("INSERT INTO chat(
			fromUser, toUser, message, dateMess, user)
			VALUES
			('$this->user', '$to', '$mess', '$dateMess', '$to')");
	}	
	public function remMess(){
		$date = $_POST["mess"];
		$this->db->ins("DELETE FROM chat WHERE dateMess='$date' AND user='$this->user'");
	}
}
?> 