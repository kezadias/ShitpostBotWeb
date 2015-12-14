function translate($err){
	switch($err){
		case ';failed-not-logged-in':
			return 'You need to be logged in to do this.';
			
		case ';failed-invalid-credentials':
			return 'Username or password was incorrect.';
			
		case ';failed-username-invalid':
			return 'Username invalid.';
			
		case ';failed-password-invalid':
			return 'Password invalid.';
			
		case ';failed-username-taken':
			return 'Username already taken.';
			
		case ';failed-user-doesnt-exist':
			return "User doesn't exist.";
			
		case ';failed-user-already-admin':
			return 'User already an admin.';
			
		case ';failed-insufficient-permissions':
			return 'You do not have permission to do this.';
			
		case ';failed-too-fast':
			return 'You just signed up! Please wait a bit before you sign up again.';
			
		default:
			return 'Unhandled error: '+$err;
	}
}

$(document).ready(function(){
	$('#logout').click(function(){
		$.get('logout.php', function(){
			location.reload();
		});
	});
});