<?php
	session_start();
		
	$updated = false;
	
	if ($_SESSION["timeListIndex"] == 5 && $updated == false){
		echo "<script>document.getElementById('debug').value = 'in 5. if';</script>";
		$_SESSION["timeListIndex"] = 1;
		$_SESSION["page5_time"] = $_GET["time"];
		$_SESSION["tableFull"] = true;
		$updated = true;
		calcTotalTime();
	}
	if ($_SESSION["timeListIndex"] == 4 && $updated == false){
		echo "<script>document.getElementById('debug').value = 'in 4. if';</script>";
		$_SESSION["timeListIndex"] = 5;
		$_SESSION["page4_time"] = $_GET["time"];
		$updated = true;
		calcTotalTime();
	}		
	if ($_SESSION["timeListIndex"] == 3 && $updated == false){
		echo "<script>document.getElementById('debug').value = 'in 3. if';</script>";
		$_SESSION["timeListIndex"] = 4;
		$_SESSION["page3_time"] = $_GET["time"];
		$updated = true;
		calcTotalTime();
	}		
	if ($_SESSION["timeListIndex"] == 2 && $updated == false){
		echo "<script>document.getElementById('debug').value = 'in 2. if';</script>";
		$_SESSION["timeListIndex"] = 3;
		$_SESSION["page2_time"] = $_GET["time"];
		$updated = true;
		calcTotalTime();
	}		
	if ($_SESSION["timeListIndex"] == 1  && $updated == false || $_SESSION["timeListIndex"] == null){
		echo "<script>document.getElementById('debug').value = 'in 1. if';</script>";
		$_SESSION["timeListIndex"] = 2;
		$_SESSION["page1_time"] = $_GET["time"];
		calcTotalTime();
	}
	
	
	function calcTotalTime(){
		if($_SESSION["tableFull"] == true){
			$totalTime = $_SESSION["page1_time"] + $_SESSION["page2_time"] + $_SESSION["page3_time"] + $_SESSION["page4_time"] + $_SESSION["page5_time"];
			$_SESSION["totalTime"] = $totalTime;
		
			if(($totalTime/1000) <= 25){
				$_SESSION["launchHelp"] = true;
			} else {
				$_SESSION["launchHelp"] = false;
			}
		}
	}

?>
