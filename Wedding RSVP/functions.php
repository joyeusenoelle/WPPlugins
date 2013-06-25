<?php

if(!function_exists('dirFilter')) {
	function dirFilter($dir, $exclude = array()) {
		$j = 0;
		$dirf = array();
		for($i = 0; $i < sizeof($dir); $i++) {
			if(!is_dir($dir[$i]) && substr($dir[$i],0,1) != "." && substr($dir[$i],-4) != ".bak") {
				if(sizeof($exclude) != 0) {
					foreach($exclude as $out) {
						if($dir[$i] == $out) {
							continue 2;
						}
					}
				}
				$dirf[$j] = $dir[$i];
				$j++;
			}
		}
		return $dirf;
	}
}

if(!function_exists('getFileExtension')) {
	function getFileExtension($str) {
	
		$i = strrpos($str,".");
		if (!$i) { return ""; }
	
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
	
		return $ext;
	
	}
}

?>