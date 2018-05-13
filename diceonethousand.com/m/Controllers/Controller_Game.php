<?php
class Controller_Game extends Controller{	
	function __construct(){			
		$this->model = new Model_Game;
		$this->view = new View($this->model->lang);
	}
	public function action_startGame(){
		$this->model->startGame();	
	}
	public function action_updateGame(){
		$this->model->updateSession();
		$arr = array();
		$arr["move"] = $this->model->getMove();
		
		$arr["bank"] = $this->model->getBank();	
		$arr["bonus"] = $this->model->getBonus();	
		$arr["players"] = $this->model->loadPlayers();			
		$arr["user"] = $this->model->user;
		$arr["chat"] = $this->model->updateChat();
		
		$arr["tplPlayer"] = $this->view->generatePage("playerGame");
		$arr["buttonRem"] = $this->view->generatePage("buttonRem");
		$arr["buttonChat"] = $this->view->generatePage("buttonChat");
		echo json_encode($arr);
	}	
	public function action_remPlayerGame(){
		$this->model->remPlayerGame();	
	}
	public function action_breakGame(){
		$this->model->breakGame();	
	}
	public function action_getMove(){
		echo json_encode($this->model->getMove());	
	}
	public function action_getStage(){
		echo $this->model->getStage();	
	}
	public function action_getImgDice($d){		
		$dice = $this->model->getImgDice($d);
	}	
	public function action_newDiceValue(){		
		$this->model->newDiceValue();
	}
	public function action_move(){		
		echo $this->model->move();
	}
	public function action_sortition(){		
		echo $this->model->sortition();
	}
	public function action_endSortition(){		
		$this->model->endSortition();
	}
	public function action_gameProcess(){		
		$this->model->gameProcess();
	}
	public function action_endMove(){		
		$this->model->endMove();
	}
}
?>