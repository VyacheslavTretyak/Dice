<?php
require_once "View.php";
require_once "Model.php";
require_once "Controller.php";
class Routing{	
	static function execute(){
		$controllerName = 'Main';
		$actionName = 'home';
		$parametr = false;
		$piecesOfUrl = explode('/', $_SERVER['REQUEST_URI']);
		$userAgent = $_SERVER["HTTP_USER_AGENT"];
		$res = array();
		preg_match('/Mobile/i', $userAgent, $res);
		if($res) 
			header("location: http://www.m.diceonethousand.com");
		if (!empty($piecesOfUrl[1])){
			$controllerName = $piecesOfUrl[1];
		}
		if (!empty($piecesOfUrl[2])){
			$action = $piecesOfUrl[2];	
			$actionName = $action;
			$end = strpos($action, "?");
			if($end !== false){
				$actionName = substr($action, 0, $end);
				$parametr = substr($action, $end+1);				
			}
		}				
		$modelName = 'Model_' . $controllerName;
		$controllerName = 'Controller_' . $controllerName;
		$actionName = 'action_' . $actionName;
		$fileWithModel = $modelName.".php";
		$fileWithModelPath	= "Models/" . $fileWithModel;
		if (file_exists($fileWithModelPath)){
			require $fileWithModelPath;			
		} else {
			die (" error in file_exists Model in ".$fileWithModelPath);	
		}	
		$fileWithController = $controllerName.".php";
		$fileWithControllerPath = "Controllers/".$fileWithController;
		if(file_exists($fileWithControllerPath)){			
			require $fileWithControllerPath;			
		} else {
			die (" error in file_exists Controller".$fileWithControllerPath);			
		}			
		$controller = new $controllerName;
		$action = $actionName;			
		if(method_exists($controller, $action)){
			call_user_func(array($controller, $action), $parametr);			
		} else {
			die (" error in file_exists ");		
		}
	}
}
Routing::execute();
?>