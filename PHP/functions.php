<?php

	function getRunningTime($round = 5) {
		global $starttime;
		
		$mtime = explode(' ', microtime());  
		$totaltime = $mtime[0] +  $mtime[1] - $starttime;
		
		return round($totaltime, $round);
	}
	
	function throwStatus($str, $info = "") {
		echo $str;
		if (!($info == "")) echo "::INFO::".$info;
		exit();
	}
	
?>