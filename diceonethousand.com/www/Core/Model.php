<?php
class Model{
	public $lang;
	public $user;
	public $login;	
	protected $timeAgo;
	private $timeCookie;
	function __construct(){
		$this->timeAgo = time()-60*5;
		$this->timeCookie = time()+60*5;
		if (!isset($_COOKIE["lang"])){
			$this->lang = "en";				
		} else {
			$this->lang = $_COOKIE["lang"];			
		}
		if (isset($_COOKIE["user"])){						
			$this->user = $_COOKIE["user"];
		} else {
			$this->user = "";
		}		
		if (!isset($_COOKIE["login"])){
			$this->login = 0;			
		} else {
			$this->login = $_COOKIE["login"];
		}	
	}	
}
?>