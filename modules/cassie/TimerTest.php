<?php
session_start();
?>

<!DOCTYPE html>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script>					
//			var msUntilStart;
//			var msUntilEnd;

//			$(document).ready(function() {
//				var start = new Date();
//				msUntilStart = start.getTime();
				//alert("<?php echo $_SESSION["timeListIndex"]; ?>");
//			});
			
//			window.onbeforeunload = function(){	  			
//				var end = new Date();
//				msUntilEnd = end.getTime();
//				$.get( "timelogger.php", {time: msUntilEnd-msUntilStart});
//			}			
		</script>
	</head>
	<body>
		<?php
			echo "<pre>";
			print_r($_SESSION);
			echo "</pre>";
		?>
		<h1>Vierailujen kestot:</h1>
		<?php		
			echo "vierailu1: " . $_SESSION["page1_time"] . " millisekuntia<br>";
			echo "vierailu2: " . $_SESSION["page2_time"] . " millisekuntia<br>"; 
			echo "vierailu3: " . $_SESSION["page3_time"] . " millisekuntia<br>"; 
			echo "vierailu4: " . $_SESSION["page4_time"] . " millisekuntia<br>"; 
			echo "vierailu5: " . $_SESSION["page5_time"] . " millisekuntia<br>"; 
			echo "yhteensä: " . $_SESSION["totalTime"] . " sekuntia<br>"; 		
		?>
	</body>
</html>
