var validTypes = ['png', 'jpg', 'jpeg'];

$(document).ready(function(){
	
	$('form').submit(function(e){//on submission..
		var file = $('#upload').val();
		var isValid = true;
		
		if(file == ''){//check if there's no file selected
			//throw an error and cancel submission event.
			alert('Select a file before proceeding!');
			isValid = false;
		} else{
			var type = /[^\.]*$/.exec(file).toString().toLowerCase();//get the filetype
			if($.inArray(type, validTypes) == -1){ //if the filetype isn't in the list of valid types
				alert(type + ' is an invalid type! Please use ' + validTypes.join(', ').replace(/,([^,]*)$/, " or$1."));
				isValid = false;
			}
		}
		
		if(!isValid){
			$('#upload').css({'background': '#fee'})
			e.preventDefault();
		}
	});
	
});