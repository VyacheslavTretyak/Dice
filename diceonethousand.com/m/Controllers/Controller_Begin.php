<?php
class Controller_Begin extends Controller{
 	function __construct(){
		$this->model = new Model_Begin;
		$this->view = new View($this->model->lang);
	}	
	public function action_sendInvite(){		
		echo $this->model->sendInvite();
	}
	public function action_cancelInvite(){
		$this->model->cancelInvite();
	}
	public function action_acceptInvite(){		
		$this->model->acceptInvite();
	}
	public function action_remInvite(){
		;$this->model->remInvite();
	}
	public function action_checkGame(){		
		echo $this->model->checkGame();
	}
}
?>