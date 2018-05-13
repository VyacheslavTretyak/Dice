<?php
class Controller_Main extends Controller{	
	function __construct(){			
		$this->model = new Model_Main;
		$this->view = new View($this->model->lang);
	}
	public function action_home(){
		echo $this->view->generatePage("header");		
	}
	public function action_loadLang(){
		$this->model->getListLang();		
	}
	public function action_changeLang(){		
		$this->model->changeLang();		
	}
	public function action_get(){		
		$page = $_POST["page"];			
		echo $this->view->generatePage($page);		
	}
	public function action_getLogin(){
		echo $this->model->login;		
	}
	public function action_getUser(){
		echo $this->model->user;
	}		
}
?>