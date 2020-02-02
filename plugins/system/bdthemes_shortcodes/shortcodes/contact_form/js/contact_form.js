var sucf_name = '',
	sucf_email = '',
	sucf_vemail = '',
	sucf_subject = '',
	sucf_message = '',
	sucf_success = '',
	sucf_error = '';

function resetForm(id) {
	jQuery('#' + id).each(function() {
		this.reset();
	});
	if (jQuery('.su-note').is(':visible')) {
		jQuery('.su-note').slideUp(400);
	}
}

function isValidEmail(email) {
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!filter.test(email)) {
		return false;
	} else {
		return true;
	}
}

function onErrorMessage(msgId,msgType, msgText) {
	
	selector='#'+msgId+' .su-note';
	jQuery(selector).removeClass('message-info');
	jQuery(selector).removeClass('su-note-info');
	jQuery(selector).removeClass('su-note-warning');
	jQuery(selector).removeClass('su-note-success');
	jQuery(selector).removeClass('su-note-danger');

	if (msgType == 'info') {
		jQuery(selector).addClass('su-note-info');
	} else if (msgType == 'warning') {
		jQuery(selector).addClass('su-note-warning');
	} else if (msgType == 'success') {
		jQuery(selector).addClass('su-note-success');
	} else if (msgType == 'danger') {
		jQuery(selector).addClass('su-note-danger');
	}
	if (jQuery(selector).is(':visible')) {
		jQuery(selector + ' .su-note-inner').html(msgText);
	} else {
		jQuery(selector + ' .su-note-inner').html(msgText);
		jQuery(selector).slideDown(400);
	}
}

function onContactSubmit(id, formAction, formData) {
        onErrorMessage(id, 'info', "Sending email, please wait...");
	jQuery.ajax({
		'type' : 'POST',
		'url' : formAction,
		'data' : formData,
		'success' : function(response) {
			if (response == 1) {
				jQuery('#'+id + ' form').each(function() {
					this.reset();
				});
				onErrorMessage(id, 'success', sucf_success);
				jQuery('body').animate({
					opacity : 1
				}, 1600, function() {
					if (jQuery('#'+id+' .su-note').is(':visible')) {
						jQuery('#'+id+' .su-note').slideUp(400);
					}
				});

			} else {
				onErrorMessage(id, 'warning', sucf_error);
			}
		}
	});
}


jQuery(document).ready(function($) {

	$('.su-contact_form').each(function () {

		var data       = $(this).data(),
			id         = $(this).attr('id'),
			submit     = $('#'+id).find('input[name="contact_us_submit"]');

		//jQuery(document).off('click', 'input[name="contact_us_submit"]');
		jQuery(document).on('click', 'input[name="contact_us_reset"]', function(e) {
			resetForm("contact_us_form");
		});
		jQuery(submit).on('click', function(e) {
			var name       = encodeURI($('#'+id).find('.cf-name').val()),
			email      = encodeURI($('#'+id).find('.cf-email').val()),
			message    = $('#'+id).find('.cf-message').val(),
			form       = $('#'+id).find('form[name="contact_us_form"]'),
			formData   = form.serialize(),
			subject    = encodeURI($('#'+id).find('.cf-subject').val()),
			formAction = form.attr('action');
			console.log(message);
			isVal = false;
			vEmail = isValidEmail(email);
			if (name == '') {
				onErrorMessage(id,'danger', sucf_name);
			} else if (email == '') {
				onErrorMessage(id, 'danger', sucf_email);
			} else if (!isNaN(email) || vEmail == false) {
				onErrorMessage(id, 'danger', sucf_vemail);
			} else if (subject == '') {
				onErrorMessage(id, 'danger', sucf_subject);
			} else if (message === '') {
				onErrorMessage(id, 'danger', sucf_message);
			} else{
				isVal = true;
			}
			if (isVal != false) {
				onContactSubmit(id, formAction, formData);
			}
		});

	});
});
