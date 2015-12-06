var validTypes = ['png', 'jpg', 'jpeg'];

$(document).ready(function(){
	$('form').submit(function(e){//on submission..
		var file = $('#upload').val();
		var isValid = true;
		var error = '';
		
		if(file == ''){//check if there's no file selected
			//throw an error and cancel submission event.
			error = 'Select a file before proceeding!';
			$('#upload').addClass('validation-error');
			isValid = false;
		} else{
			var type = /[^\.]*$/.exec(file).toString().toLowerCase();//get the filetype
			if($.inArray(type, validTypes) == -1){ //if the filetype isn't in the list of valid types
				error = type + ' is an invalid type for the image! Please use ' + validTypes.join(', ').replace(/,([^,]*)$/, " or$1.");
				$('#upload').addClass('validation-error');
				isValid = false;
			}
		}
		
		file = $('#overlay').val();
		if(file != ''){
			var type = /[^\.]*$/.exec(file).toString().toLowerCase();//get the filetype
			if(type != 'png'){ //if the filetype isn't in the list of valid types
				error = type + ' is an invalid type for the overlay! Please use PNG';
				$('#overlay').addClass('validation-error');
				isValid = false;
			}
		}
		
		if(!isValid){
			e.preventDefault();
			alert(error);
		}
	});
	
	$(".type").change(function(){
		if($(this).val() && $(this).attr('id') == 'template'){
			$('.overlay').removeClass('hidden');
		} else{
			$('.overlay').addClass('hidden');
			$('#overlay').val('');
		}
	});
	
	
});