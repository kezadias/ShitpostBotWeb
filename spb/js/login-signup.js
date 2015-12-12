$(document).ready(function(){
	$('#login-signup').submit(function(e){
		e.preventDefault();
		var page = $('#login-signup').attr('action');
		$.post(page, {'spb-user': $('#spb-user').val(), 'spb-pass': $('#spb-pass').val()}, function(data){
			if(data == ';success'){
				window.location.href = 'index.php';
			}else{
				alert(data);
			}
		});
	});
	
});