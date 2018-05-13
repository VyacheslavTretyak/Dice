<?php
class Controller_Test extends Controller{	
	function __construct(){			
		$this->model = new Model_Test;
		$this->view = new View($this->model->lang);		
	}
	public function action_test1(){
		echo $this->view->generatePage("test");
	}
}
?>