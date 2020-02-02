/**
 * Copyright breakdesigns.net
 */
if (typeof CustomfieldsForAll === "undefined") {
	var CustomfieldsForAll = {
		handleForms : function(forms) {
			forms.each(function() {
				var form = jQuery(this);
				var addtocart = form.find('input.addtocart-button');
				addtocart.off('click');
				// addtocart.unbind();
				addtocart.click(function(e) {
					var form = jQuery(this).parents('form');
					CustomfieldsForAll.checkRequired(form);
					return false;
				});
			});
		},

		checkRequired : function(form) {
			var required_fields = form.find('.cf4all_required');
			var emptyFound = false;

			jQuery
					.each(
							required_fields,
							function() {
								var field = jQuery(this);
								var radios_checked = field
										.find('.cf4all_radio:checked').length;
								var checkboxes_checked = field
										.find('.cf4all_checkbox:checked').length;
								var select_selected = field
										.find('select option:selected').length;
								if (select_selected > 0)
									var sel_value = field.find(
											'select option:selected').attr(
											'value');
								if (radios_checked == 0
										&& checkboxes_checked == 0
										&& (select_selected == 0
												|| typeof sel_value == 'undefined' || sel_value == 0)) {
									emptyFound = true;
									CustomfieldsForAll.displayMsg(field);
								}
							});
			if (emptyFound == false) {
				Virtuemart.sendtocart(form);
				return;
			}
			return false;
		},
		eventHandler : function() {
			jQuery('.cf4all_wrapper input').click(function() {
				var required = jQuery(this).parents('.cf4all_required');
				if (required)
					CustomfieldsForAll.hideMsg(required);
			});

			jQuery('.cf4all_wrapper select').change(function() {
				var required = jQuery(this).parents('.cf4all_required');
				if (required)
					CustomfieldsForAll.hideMsg(required);
			});
		},

		displayMsg : function(el) {
			el.find('span.cf4all_error_msg').css('display', 'inline-block');
		},
		hideMsg : function(el) {
			el.find('span.cf4all_error_msg').css('display', 'none');
		}
	}

}

jQuery(document).ready(function($) {
	CustomfieldsForAll.eventHandler();
	var forms = jQuery("form.product");
	setTimeout(function() {
		CustomfieldsForAll.handleForms(forms);
	}, 13);

});