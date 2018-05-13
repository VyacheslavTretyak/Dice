<?php
class Controller_Chat extends Controller{	
	function __construct(){			
		$this->model = new Model_Chat;
		$this->view = new View($this->model->lang);
	}	
	public function action_sendMess(){
		$this->model->sendMess();
	}
	public function action_remMess(){
		$this->model->remMess();
	}	
}
?>