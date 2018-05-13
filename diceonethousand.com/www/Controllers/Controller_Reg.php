<?php
class Controller_Reg extends Controller{	
	function __construct(){			
		$this->model = new Model_Reg;
		$this->view = new View($this->model->lang);
	}
	public function action_login(){		
		echo $this->view->generatePage("login");
	}
	public function action_checkLogin(){
		echo $this->model->checkLogin();
	}
	public function action_checkRegLogin(){
		echo $this->model->checkRegLogin();	
	}
	public function action_checkPass(){
		echo $this->model->checkPass();
	}
	public function action_checkCaptcha(){		
		echo $this->model->checkCaptcha();
	}	
	public function action_reg(){
		$this->model->reg();	 
		$mes = $this->view->generatePage("mail");	
		$this->model->sendMail($mes);
		echo $this->view->generatePage("youGotEmail");
	}	
	public function action_getCaptcha(){		
		$this->model->getCaptcha();
	}	
	public function action_activator($user){		
		$this->model->activator($user);
	}	
	public function action_checkActivate(){		
		echo $this->model->checkActivate();
	}
}	
?>