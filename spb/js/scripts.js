$(document).ready(function(){
	$('#logout').click(function(){
		$.get('logout.php', function(){
			location.reload();
		});
	});
});