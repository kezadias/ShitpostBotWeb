$(document).ready(function(){
	$('.review').click(function(e){
		var id = $(this).attr('code');
		var type = $(this).attr('type');
		var state = $(this).attr('state');
		$.get('do-review.php?id='+id+'&t='+type+'&s='+state, function(data){
			if(data == ';success'){
				location.reload();
			}else{
				alert(translate(data));
			}
		});
	});
	
	$('.review-skip').click(function(){
		location.reload();
	});
});