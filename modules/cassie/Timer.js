var msUntilStart;
var msUntilEnd;

$(document).ready(function() {
	var start = new Date();
	msUntilStart = start.getTime();
	//alert("<?php echo $_SESSION["timeListIndex"]; ?>");
});

window.onbeforeunload = function(){	  			
	var end = new Date();
	msUntilEnd = end.getTime();
	$.get( "timelogger.php", {time: msUntilEnd-msUntilStart});
}
