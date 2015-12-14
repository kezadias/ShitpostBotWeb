$(document).ready(function(){
	
	$('.remove-admin').click(function(){
		var id = $(this).attr('code');
		$.get('manage-admin.php?action=delete&id='+id, function(data){
			if(data != ''){
				alert(data);
			} else{
				location.reload();
			}
		});
	});
	
	$('.add-admin').click(function(){
		var username = $('#username').val();
		var canReview = $('#canReview').is(":checked") ? 'y' : 'n';
		var canMakeAdmin = $('#canMakeAdmin').is(":checked") ? 'y' : 'n';
		$.get('manage-admin.php?action=recruit&username='+username+'&canReview='+canReview+'&canMakeAdmin='+canMakeAdmin, function(data){
			if(data != ''){
				alert(data);
			} else{
				location.reload();
			}
		});
	});
	
});