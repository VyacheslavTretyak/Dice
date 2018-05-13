<?php
class Controller_Data extends Controller{	
	function __construct(){			
		$this->model = new Model_Data;
		$this->view = new View($this->model->lang);
	}		
	public function action_checkLogin(){
		$user = $this->model->checkLogin();
		$this->model->user = $user;	
		echo $user;		
	}
	public function action_updateStart(){
		$preg = $_POST["preg"];	
		
		$arr = array();
		$this->model->updateSession();
		$arr["tplInvite"] = $this->view->generatePage("invite");
		$arr["tplListInvited"] = $this->view->generatePage("listInvitedPlayers");
		$arr["buttonRem"] = $this->view->generatePage("buttonRem");
		$arr["buttonAdd"] = $this->view->generatePage("buttonAdd");
		$arr["buttonChat"] = $this->view->generatePage("buttonChat"); 
		$arr["tplPlayer"] = $this->view->generatePage("player");
		
		$arr["whoInvite"] = $this->model->getInvites();
		$arr["answer"] = $this->model->getAnswer();
		$arr["myStatus"] = $this->model->getStatus();	
		if($this->model->user == $this->model->getMaster())
			$arr["iAmMaster"] = 1;
		else
			$arr["iAmMaster"] = 0;			
		$arr["user"] = $this->model->user;	
		$arr["login"] = $this->model->login;			
		$arr["users"] = $this->model->loadUsers($preg);
		$arr["friends"] =  $this->model->loadFriends();			
		$arr["chat"] = $this->model->updateChat();
		//print_r($arr);
		echo json_encode($arr);
	}	
	public function action_addFriend(){
		$this->model->addFriend();		
	}
	public function action_remFriend(){
		$this->model->remFriend();
	}	
	public function action_getLogin(){
		$this->model->updateSessionStart();
		$login = $this->model->login;
		$coins = $this->model->getCoins();
		echo json_encode(array($login, $coins));
	}	
}
?>