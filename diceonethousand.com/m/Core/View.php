<?php
class View{	
	private $symbol = "**";
	private $lang = array();
	public function __construct($l){
		$lang = parse_ini_file("Views/lang/".$l.".ini");
		if(!$lang)
			$lang = array();		
		$this->lang = $lang;
	}
	public function generatePage($tpl){		
		$s = file_get_contents("Views/tpl/".$tpl.".tpl");			
		$offset = 0;
		$len = strlen($this->symbol);
		$arr = array();
		while($pos = strpos($s, $this->symbol, $offset)){				
			$end = strpos($s, $this->symbol, $pos+$len);
			$sub = substr($s, $pos+$len, $end-$pos-$len);			
			$offset = $end+$len;			
			array_push($arr, $sub);
		}			
		foreach ($arr as $a){					
			$r = array_key_exists($a, $this->lang);
			if(!$r)
				$i = "???".$a."???";
			else 
				$i = $this->lang[$a];					
			$s = str_replace($this->symbol.$a.$this->symbol, $i, $s);
		}		
		return $s;
	}
}
?>