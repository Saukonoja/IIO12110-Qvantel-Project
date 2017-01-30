<!DOCTYPE html>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script>
			var start;

			$(document).ready(function() {
				alert("Document ready called!");
				start = Date.getTime();

			  $(window).unload(function() {
				  alert("Unload called!");
				  end = Date.getTime();
				  $.ajax({ 
					url: "timelogger.php",
					data: {'timeSpent': end - start}
				  })
				});
			}
		</script>
	</head>
	<body>
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
