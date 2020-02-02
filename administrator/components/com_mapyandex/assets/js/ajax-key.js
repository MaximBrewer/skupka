jQuery(function ($) {
	
	$('document').ready(function(){
		$("body").on('click',"#pg-update-save", function(){
		var key = $('#yandex-key').val();
		
		event.preventDefault();
			$.ajax({
				headers: {
						"Content-Type": "application/json",
						"Accept": "application/json"
				},
				type: "GET",
				url: '/administrator/index.php?option=com_mapyandex&task=ajaxsavekey&format=row',
				data: {'key':key},
				dataType: "json",
				success: function(json){
						if(json.ok) {
							$('#pg-update-save-message').text(json.text).css('color','green').fadeOut(1000);
							
						} else {
							$('#pg-update-save-message').text(json.text).css('color','red');
						}
				}
			});
		
		});


	});

});
