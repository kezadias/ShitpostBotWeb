$(document).ready(function(){
	$('#sort').click(function(){
		var by = $('#gallery-ordering').val();
		var dir = $('#gallery-dir').val();
		window.location.href = '?by='+by+'&dir='+dir;
	});
});