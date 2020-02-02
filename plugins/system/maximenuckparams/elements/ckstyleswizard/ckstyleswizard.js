/**
 * @copyright	Copyright (C) 2012 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Slideshow CK
 * @license		GNU/GPL
 * */

//function addfromfolderck() {
//
//}
var $ck = jQuery.noConflict();

function showStylesWizardPopupCK(popup) {
	$ck('#ckoverlayer').remove();
	$ck(document.body).append('<div id="ckoverlayer" />');
	if (popup.getParent('.accordion-body'))
		popup.getParent('.accordion-body').setStyle('overflow', 'visible');
	popup.setStyle('display', 'block');
	jscolor.init();
	
	// load the menu description
	$ck('#ckpopupwizard_select_desc_area').empty().text($ck('.ckpopupwizard_select.active > .ckpopupwizard_select_desc').text());
	$ck('.ckpopupwizard_select.active').each(function(i, button) {
		showActiveOptions($ck(button));
	});

	initFieldsValues();

	// fix for bootsrap issue
	$ck('select', $ck('#ckpopupwizard')).show();
	$ck('.chzn-container', $ck('#ckpopupwizard')).hide();
	
	/*fieldName = fieldName.value.replace(/\|qq\|/g, "\"");
	var fields = JSON.decode(fieldName);
	fields.each(function(field) {

		var fieldobj = $(field['id']);

		// pour type radio
		if (fieldobj.getProperty('isradio')) {
			fieldobj.getParent().getElements('input[type=radio]').each(function(el) {
				elparent = el.getParent();
				labelbtn = elparent.getElement('label[for=' + el.id + ']');

				if (el.value == field['value']) {
					el.setProperty('checked', 'checked');
					if (el.getParent().hasClass('boutonRadio'))
						el.getParent().addClass('coche');
					if (elparent.hasClass('btn-group')) {
						labelbtn.addClass('active');
						if (el.value == 1)
							labelbtn.removeClass('btn-danger').addClass('btn-success');
						if (el.value == 0)
							labelbtn.removeClass('btn-success').addClass('btn-danger');
					}
				}
				else {
					el.removeProperty('checked');
					if (el.getParent().hasClass('boutonRadio'))
						el.getParent().removeClass('coche');
					if (elparent.hasClass('btn-group')) {
						labelbtn.removeClass('active');
						labelbtn.removeClass('btn-danger').removeClass('btn-success');
					}
				}
			});
		}
		
		// pour liste déroulante bootstrap
		if (fieldobj.tagName.toLowerCase() == 'select') { 
			if (BS_field = fieldobj.getNext('.chzn-container')) {
				BS_fieldtext =  field['value'] == 0 ? 'Default' : field['value'];
				BS_field.getElement('.chzn-single span').set('text', BS_fieldtext);
			}
		}

		fieldobj.value = field['value'];
	});*/
}

function initFieldsValues() {
	//jform_params_ / ckmaximenuwizard_
	var targetEl;
	$ck('.ckmaximenuwizard_inputbox').each(function(i, el) {
		el = $ck(el);
		targetEl = el.attr('id').replace('ckmaximenuwizard_', 'jform_params_');
		//console.log(targetEl);console.log($ck('#'+targetEl).val());
		if ($ck('#'+targetEl).length) el.val($ck('#'+targetEl).val());
		
		// champs specifiques
		if (el.attr('id') == 'ckmaximenuwizard_theme') {
			if ($ck('#jform_params_usecss').val() == 0) {
				el.val(-1);
			} else {
				el.val($ck('#'+targetEl).val())
			}
			updateRadiobuttonState($ck('#jform_params_usecss'))
		}
		
		if (el.attr('id') == 'ckmaximenuwizard_thirdparty'
		|| el.attr('id') == 'ckmaximenuwizard_layout'
		|| el.attr('id') == 'ckmaximenuwizard_menuposition'
		|| el.attr('id') == 'ckmaximenuwizard_maximenumobile_displayeffect') {
			el.parent().find('.ckpopupwizard_select').removeClass('active');
			el.parent().find('.ckpopupwizard_select[data-type="'+el.val()+'"]').addClass('active');
			showActiveOptions(el.parent().find('.ckpopupwizard_select[data-type="'+el.val()+'"]'));
		}
	});
}

function saveWizardCK(popup, fieldName, identifier) {
	popup.setStyle('display', 'none');
	if (popup.getParent('.accordion-body'))
		popup.getParent('.accordion-body').setStyle('overflow', '');
	// to store the values into the hidden field (not needed for now)
	/*var fields = JSON.encode($$("." + identifier+'_inputbox').getProperties('id', 'value'));
	fields = fields.replace(/"/g, "|qq|");
	$(fieldName).value = fields;*/

	var targetEl;
	$ck('.ckmaximenuwizard_inputbox').each(function(i, el) {
		el = $ck(el);
		targetEl = el.attr('id').replace('ckmaximenuwizard_', 'jform_params_');
		// console.log($ck('#'+targetEl)[0].tagName);
		targetEl = $ck('#'+targetEl);
		if (targetEl.length) targetEl.val(el.val());
		if (targetEl[0] && targetEl[0].tagName.toUpperCase() == 'SELECT') {
			if (targetEl.next('.chzn-container').length) {
				targetEl.next('.chzn-container').find('.chzn-single').text(targetEl.find('option[value="'+el.val()+'"]').text());
			}
		}

		// pour type radio
		if (targetEl.attr('isradio')) {
			updateRadiobuttonState(targetEl);
		}

		// champs specifiques
		if (el.attr('id') == 'ckmaximenuwizard_theme') {
			if (el.val() == -1) {
				$ck('#jform_params_usecss').val(0);
			} else {
				$ck('#jform_params_usecss').val(1);
			}
			updateRadiobuttonState($ck('#jform_params_usecss'))
		}
	});
}

function updateRadiobuttonState(targetEl) {
	targetEl.parent().find('input[type=radio]').each(function(j, el2) {
		el2 = $ck(el2);
		elparent = el2.parent();
		labelbtn = elparent.find('label[for=' + el2.attr('id') + ']');

		if (el2.val() == targetEl.attr('value')) {
			el2.attr('checked', 'checked');
			if (el2.parent().hasClass('boutonRadio'))
				el2.parent().addClass('coche');
			if (elparent.hasClass('btn-group')) {
				labelbtn.addClass('active');
				if (el2.val() == 1)
					labelbtn.removeClass('btn-danger').addClass('btn-success');
				if (el2.val() == 0)
					labelbtn.removeClass('btn-success').addClass('btn-danger');
			}
		}
		else {
			el2.removeAttr('checked');
			if (el2.parent().hasClass('boutonRadio'))
				el2.parent().removeClass('coche');
			if (elparent.hasClass('btn-group')) {
				labelbtn.removeClass('active');
				labelbtn.removeClass('btn-danger').removeClass('btn-success');
			}
		}
	});
}

function closeWizardCK(popup) {
	$ck('#ckoverlayer').remove();
	popup.setStyle('display', 'none');
	if (popup.getParent('.accordion-body'))
		popup.getParent('.accordion-body').setStyle('overflow', '');
}

function ckpopupwizard_prev() {
	if (parseInt($ck('#ckpopupwizard_slider > .inner').css('marginLeft')) <= -780) {
		$ck('#ckpopupwizard_slider > .inner').animate({'marginLeft': '+=780px'});
		var index = parseInt($ck('#ckpopupwizard_slider').attr('data-index'));
		$ck('#ckpopupwizard_slider').attr('data-index', index-1);
		$ck('.ckpopupwizard_index').removeClass('active').eq(index-1).addClass('active');
	}
}

function ckpopupwizard_next() {
	var maxwidth = ( $ck('.ckpopupwizard_slider', $ck('#ckpopupwizard_slider')).length - 1 ) * 780;
	if (parseInt($ck('#ckpopupwizard_slider > .inner').css('marginLeft')) > -maxwidth) {
		$ck('#ckpopupwizard_slider > .inner').animate({'marginLeft': '-=780px'});
		var index = parseInt($ck('#ckpopupwizard_slider').attr('data-index'));
		$ck('#ckpopupwizard_slider').attr('data-index', index+1);
		$ck('.ckpopupwizard_index').removeClass('active').eq(index+1).addClass('active');
	}
}

function showActiveOptions(button) {
	var parentbutton = $ck(button).parents('.ckpopupwizard_slider')[0];
	$ck('#ckpopupwizard_select_desc_area', $ck(parentbutton)).empty().text($ck('.ckpopupwizard_select_desc', $ck(button)).text());
	$ck('.ckpopupwizard_select', $ck(parentbutton)).removeClass('active');
	$ck(button).addClass('active');
	$ck(parentbutton).find('.ckpopupwizard_options_area > div').hide();
	$ck('#'+$ck(button).attr('data-target')).show();
	
	// $ck('.btn-group', $ck('#ckpopupwizard')).button();
}

function getOptionForWizard(optionId) {
	return $ck('#'+optionId).parents('.control-group')[0].clone();
}

function changeFieldValue(fieldId, fieldValue) {
	$ck('#'+fieldId).val(fieldValue);
}

// pour gestion editeur d'images
function jInsertEditorText(text, editor) {
	var newEl = new Element('span').set('html', text);
	var valeur = newEl.getChildren()[0].getAttribute('src');
	$(editor).value = valeur;
}

jQuery(document).ready(function (){
	/*var script = document.createElement("script");
	script.setAttribute('type', 'text/javascript');
	script.text = "Joomla.submitbutton = function(task){"
			+ "$$('.ckpopup').destroy();"
			+ "if (task == 'module.cancel' || document.formvalidator.isValid(document.id('module-form'))) {	Joomla.submitform(task, document.getElementById('module-form'));"
			+ "if (self != top) {"
			+ "window.top.setTimeout('window.parent.SqueezeBox.close()', 1000);"
			+ "}"
			+ "} else {"
			+ "alert('Invalid Form');"
			+ "}"
			+ "}";
	document.body.appendChild(script);*/
	$ck(document.body).append($ck('#ckpopupwizard'));
	// if ($ck('#jform[params][maximenuwizard]').val() != 1) showWizardPopupCK($('ckpopupwizard'));
	$ck('.hasTip').tooltip({"html": true,"container": "body"});
});




/*------------------- debut fonctionnalité ----------------*/
function make_stylesparams_preview() {
	/* test */
	// var fields = JSON.encode(document.getElements("#ckpopupstyleswizard .menustyles").getProperties('id', 'value'));
	// var fields = make_json_fields('menustyles');
	// console.log(fields);
	// fields = fields.replace(/"/g, "|qq|");
	// $('jform_params_menustyles').value = fields; // nécessaire pour mise à jour frontend
	/* fin test */
	var myurl = 'index.php?option=com_maximenuck&task=preview_module_styles';
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			menuID: 'maximenuck_previewmodule',
			menustyles: make_json_fields('menustyles'),
			level1itemnormalstyles: make_json_fields('level1itemnormalstyles'), //$ck('#jform_params_level1itemnormalstyles').val(),
			level1itemhoverstyles: make_json_fields('level1itemhoverstyles'), //$ck('#jform_params_level1itemhoverstyles').val(),
			level1itemactivestyles: $ck('#jform_params_level1itemactivestyles').val(),
			level2menustyles: $ck('#jform_params_level2menustyles').val(),
			level2itemnormalstyles: $ck('#jform_params_level2itemnormalstyles').val(),
			level2itemhoverstyles: $ck('#jform_params_level2itemhoverstyles').val(),
			level2itemactivestyles: $ck('#jform_params_level2itemactivestyles').val()
		}
	}).done(function(response) {
		jQuery('#ckpopupstyleswizard_preview > .ckstyle').html(response);
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function make_json_fields(prefix) {
	var fields = JSON.encode(document.getElements("#ckpopupstyleswizard ." + prefix).getProperties('id', 'value'));
	
	return fields.replace(/"/g, "|qq|");
}




    