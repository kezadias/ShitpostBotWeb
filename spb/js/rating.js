$(document).ready(function(){
	$('.rate').click(function(){
		var thisButton = $(this);
		var thisNum = $(this).parent().find('.num');
		$(this).parent().find('.rate').removeClass('selected');
		var id = thisButton.attr('code');
		var type = thisButton.attr('type');
		var rating = thisButton.attr('rating');
		$.get('do-rating.php?id='+id+'&t='+type+'&r='+rating, function(data){
			if(data.match(/\;success\(-?[0-9]{1,20}\)/g)){
				thisButton.addClass('selected');
				thisNum.text(data.match(/-?[0-9]{1,20}/));
			} else{
				alert(translate(data));
			}
		});
	});
});