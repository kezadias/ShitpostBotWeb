$(document).ready(function(){
	$('.review').click(function(e){
		var btn = $(this);
		var id = $(this).attr('code');
		var type = $(this).attr('type');
		var state = $(this).attr('state');
		$.get('do-review.php?id='+id+'&t='+type+'&s='+state, function(data){
			if(data == ';success'){
				var ret = btn.attr('return');
				if(typeof ret !== typeof undefined && ret !== false){
					window.location.href = ret;
				} else{
					location.reload();
				}
			}else{
				alert(translate(data));
			}
		});
	});
	
	$('.review-skip').click(function(){
		location.reload();
	});
});