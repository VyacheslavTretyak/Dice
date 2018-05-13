<?php
class Model_Main extends Model{
	function __construct(){
		parent::__construct();			
	}	
	public function getListLang(){		
		$lang = array();
		if ($handle = opendir("Views/lang/")) {			
			while (false !== ($file = readdir($handle))) { 
				$a = str_replace(".ini", "", $file);
				if(($a != ".") && ($a != "..")){
					array_push($lang, $a);
				}					
			}			
		}	
		$curLang = $this->lang;
		$arr = array($curLang, $lang);		
		echo json_encode($arr);
	}	
}
?>