var msUntilStart;
var msUntilEnd;

jQuery(document).ready(function($) {
	var start = new Date();
	msUntilStart = start.getTime();
	//alert("<?php echo $_SESSION["timeListIndex"]; ?>");
	//alert("js pyorii!");
});

window.onbeforeunload = function(){
	var end = new Date();
	msUntilEnd = end.getTime();
	jQuery.get( "timer", {time: msUntilEnd-msUntilStart});
}
