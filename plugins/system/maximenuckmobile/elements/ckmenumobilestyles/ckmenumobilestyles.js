/**
 * @name		Maximenu CK Mobile
 * @package		maximenuckmobile
 * @copyright	Copyright (C) 2015. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

$ck = jQuery.noConflict();

function add_wait_icon(button) {
	$ck(button).addClass('ckwait');
}

function remove_wait_icon(button) {
	$ck(button).removeClass('ckwait');
}

/**
* Insert image from com_media
*/
function jInsertFieldValue(value, id) {
	$ck('#'+id).val(value);
	$ck('#'+id).trigger('change');
}

function jInsertEditorText(value, id) {
	jInsertFieldValue(value, id);
}

/**
* Set the params array
*/
function get_prefixes_list() {
	var styles = new Array('menubar'
						,'menubarbutton'
						,'topbar'
						,'topbarbutton'
						,'menu'
						,'level1menuitem'
						,'level2menuitem'
						,'level3menuitem'
						,'togglericon'
						);

	return styles;
}

function preview_stylesparams(button) {
	if (! button) button = '#ckpopupstyleswizard_makepreview .ckwaiticon';
	// button = $ck(button).find('.ckwaiticon');
	add_wait_icon(button);
	var myurl = 'index.php?option=com_ajax&format=raw&plugin=Maximenuckmobile&group=system';
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			action: 'preview',
			method: 'getCss',
			menuID: 'maximenuckmobile_preview',
			menubar: make_params_fields('menubar'),
			menubarbutton: make_params_fields('menubarbutton'),
			topbar: make_params_fields('topbar'),
			topbarbutton: make_params_fields('topbarbutton'),
			menu: make_params_fields('menu'),
			level1menuitem: make_params_fields('level1menuitem'),
			level2menuitem: make_params_fields('level2menuitem'),
			level3menuitem: make_params_fields('level3menuitem'),
			togglericon: make_params_fields('togglericon'),
			customcss: $ck('#customcss_area').val()
		}
	}).done(function(response) {
		response = response.trim();
		jQuery('#ckstyles_menubar').html('<style>' + response + '</style>');
		load_gfont_stylesheets();
		var menubarbuttonhtml = get_mobilebutton_content($ck('#ckpopupstyleswizard input[name=menubarbuttoncontent]:checked').val(), $ck('#menubarbuttoncontentcustomtext').val());
		$ck('#preview-mobilebarmaximenuck .mobilebuttonmaximenuck').html(menubarbuttonhtml);
		$ck('#jform_params_maximenumobile_menubarbuttoncontent').val(menubarbuttonhtml);
		var topbarbuttonhtml = get_mobilebutton_content($ck('#ckpopupstyleswizard input[name=topbarbuttoncontent]:checked').val(), $ck('#topbarbuttoncontentcustomtext').val());
		$ck('#testmaximenu-mobile .mobilemaximenuckclose').html(topbarbuttonhtml);
		$ck('#jform_params_maximenumobile_topbarbuttoncontent').val(topbarbuttonhtml);
		remove_wait_icon(button);
	}).fail(function() {
		//alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

/* 
 * Method to store the values in the hidden field, launched before save
 */
function save_stylesparams() {
	var fields = {};
	var params_list = get_prefixes_list();
	var params = {};
	// make a global object with all fields name and value
	for (i=0;i<params_list.length;i++) {
		$ck.extend(params, get_params_fields(params_list[i]));
	}
	
	var jform_params_maximenumobile_styles = JSON.stringify(params);
	jform_params_maximenumobile_styles = jform_params_maximenumobile_styles.replace(/"/g, "|qq|");
	// store the value in the hidden field
	$ck('#jform_params_maximenumobile_styles').val(jform_params_maximenumobile_styles);
	return;
}

function load_gfont_stylesheets() {
	var params_list = get_prefixes_list();
	var gfonturls = '';
	// var standard_fonts = get_standard_fonts();
	var standard_fonts = jQuery.map( get_standard_fonts(), function( n, i ) {
		return ( n.toLowerCase() );
	});
	for (i=0;i<params_list.length;i++) {
		var fontfamilyfield = $ck('#'+params_list[i]+'fontfamily');
		if (fontfamilyfield.length && fontfamilyfield.val() && standard_fonts.indexOf(fontfamilyfield.val().toLowerCase()) == -1) {
			gfonturls += get_gfont_stylesheet($ck('#'+params_list[i]+'fontfamily').val());
		}
	}
	jQuery('#ckgfontstylesheet').html(gfonturls);
}

function get_standard_fonts() {
	var fonts = ['Times New Roman'
			, 'Helvetica'
			, 'Georgia'
			, 'Courier New'
			, 'Arial'
			, 'Verdana'
			, 'Comic Sans MS'
			, 'Tahoma', 'Segoe UI'
			, 'sans-serif'
			, 'serif'
			, 'cursive'
			];
	return fonts;
}

function get_gfont_stylesheet(family) {
	if (! family) return '';
	var familycode = family.split(' ');
	familycode = jQuery.map(familycode, function( n, i ) {
		return ( n.charAt(0).toUpperCase() + n.slice(1) );
	});
	familycode = familycode.join('+');
	return ("<link href='https://fonts.googleapis.com/css?family="+familycode+"' rel='stylesheet' type='text/css'>");
}

function clean_gfont_name(field) {
	return;
}

function check_gradient_image_conflict(from, field) {
	if ($ck(from).val()) {
		if ($ck('#'+field).val()) {
			alert('Warning : you can not have a gradient and a background image at the same time. You must choose which one you want to use');
		}
	}
}

function make_params_fields(prefix) {
	fields = get_params_fields(prefix);
	fields = JSON.stringify(fields);
	// fields = fields.replace(/"/g, "|qq|");
	return fields;
}

function get_params_fields(prefix) {
	var fields = {};
	$ck('#ckpopupstyleswizard .' + prefix).each(function(i, field) {
		field = $ck(field);
		var  fieldobj = {};
		if ( field.attr('type') == 'radio' ) {
			if ( field.attr('checked') == 'checked' ) {
//				fieldobj['id'] = field.attr('name');
//				fieldobj['value'] = field.val();
//				fields.push(fieldobj);
				fields[field.attr('name')] = field.val();
			}
		} else if ( field.attr('type') != 'radio' ) {
//			fieldobj[field.attr('id')] = field.val();
//			fieldobj['value'] = field.val();
//			fields.push(fieldobj);
			fields[field.attr('id')] = field.val();
		}
	});
	return fields;
}

function set_value_to_field(id, value) {
	var field = $ck('#' + id);
	if (!field.length) {
		if ($ck('#ckpopupstyleswizard input[name=' + id + ']').length) {
			$ck('#ckpopupstyleswizard input[name=' + id + ']').each(function(i, radio) {
				radio = $ck(radio);
				if (radio.val() == value) {
					radio.attr('checked', 'checked');
				} else {
					radio.removeAttr('checked');
				}
			});
		}
	} else {
		if (field.hasClass('color')) field.css('background',value);
		$ck('#' + id).val(value);
	}
}

function get_mobilebutton_content(value, customtextfield_value) {
	switch (value) {
		case 'hamburger':
			var content = '&#x2261;';
			break;
		case 'close':
			var content = '×';
			break;
		case 'custom' :
			var content = customtextfield_value;
			break;
		default :
		case 'none':
			var content = '';
			break;
	}
	return content;
}

function save_css_to_file_ck(task) {
	var button = '#ckpopupstyleswizard_makepreview .ckwaiticon';
	add_wait_icon(button);
	var myurl = 'index.php?option=com_ajax&format=raw&plugin=Maximenuckmobile&group=system';
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: false,
		data: {
			action: 'save',
			method: 'saveCssToFile',
			menubar: make_params_fields('menubar'),
			menubarbutton: make_params_fields('menubarbutton'),
			topbar: make_params_fields('topbar'),
			topbarbutton: make_params_fields('topbarbutton'),
			menu: make_params_fields('menu'),
			level1menuitem: make_params_fields('level1menuitem'),
			level2menuitem: make_params_fields('level2menuitem'),
			level3menuitem: make_params_fields('level3menuitem'),
			customcss: $ck('#customcss_area').val(),
			togglericon: make_params_fields('togglericon'),
			jsonfields: $ck('#jform_params_maximenumobile_styles').val()
		}
	}).done(function(response) {
		response = response.trim();
		if (response != '1') {
			alert(Joomla.JText._('CK_ERROR_SAVING_PARAM', 'Error when saving the styles'));
		} else {
			var menubarbuttonhtml = get_mobilebutton_content($ck('#ckpopupstyleswizard input[name=menubarbuttoncontent]:checked').val(), $ck('#menubarbuttoncontentcustomtext').val());
			$ck('#jform_params_maximenumobile_menubarbuttoncontent').val(menubarbuttonhtml);
			var topbarbuttonhtml = get_mobilebutton_content($ck('#ckpopupstyleswizard input[name=topbarbuttoncontent]:checked').val(), $ck('#topbarbuttoncontentcustomtext').val());
			$ck('#jform_params_maximenumobile_topbarbuttoncontent').val(topbarbuttonhtml);
			submitform_ck(task);
		}
		remove_wait_icon(button);
		
	}).fail(function() {
		// alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function submitform_ck(task) {
	var form = document.getElementById('style-form') || document.getElementById('modules-form') || document.getElementById('module-form');
	if (task == 'plugin.cancel' || document.formvalidator.isValid(form)) {
		Joomla.submitform(task, form);
		if (self != top) {
			window.top.setTimeout('window.parent.SqueezeBox.close()', 1000);
		}
	} else {
		alert('Invalid Form');
	}
}

function save_googlefonts() {
	var fonts = new Array();
	jQuery('#ckgfontstylesheet').find('link').each(function() {
		fonts.push($ck(this).attr('href'));
	});
	fontsval = JSON.stringify(fonts);
	jQuery('#jform_params_maximenumobile_googlefonts').val(fontsval);
}

function set_fields_value(fields) {
	var fields = jQuery.parseJSON(fields);
	for (field in fields) {
		set_value_to_field(field, fields[field]);
		// set the button content directly from the option
		if (field == 'menubarbuttoncontent') {
			var content = get_mobilebutton_content(fields[field], fields[field+'customtext']) 
			$ck('#preview-mobilebarmaximenuck .mobilebuttonmaximenuck').html(content);
		}
		if (field == 'topbarbuttoncontent') {
			var content = get_mobilebutton_content(fields[field], fields[field+'customtext']) 
			$ck('#testmaximenu-mobile .mobilemaximenuckclose').html(content);
		}
	}
}

/**
 * Loads the file from the preset and apply it to all fields
 */
function load_preset(name) {
	var confirm_clear = confirm(Joomla.JText._('CK_ERASE_DATA', 'This will delete all your settings and reset the styles. Do you want to continue ?'));
	if (confirm_clear == false) return;

	var button = '#ckpopupstyleswizard_makepreview .ckwaiticon';
	add_wait_icon(button);

	// remove the values for all the fields
	clear_fields();

	// ajax call to get the fields
	var myurl = 'index.php?option=com_ajax&format=raw&plugin=Maximenuckmobile&group=system';
	$ck.ajax({
		type: "POST",
		url: myurl,
		dataType: 'json',
		data: {
			method: 'loadPresetFields',
			folder: name
		}
	}).done(function(r) {
		if (r.result == 1) {
			var fields = r.fields;
			fields = fields.replace(/\|qq\|/g, '"');
			set_fields_value(fields);

			// get the value for the custom css
			load_preset_customcss(name);
		} else {
			alert('Message : ' + r.message);
			remove_wait_icon(button);
		}
		
	}).fail(function() {
		//alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});

	
}

function load_preset_customcss(name) {
	var button = '#ckpopupstyleswizard_makepreview .ckwaiticon';
	// add_wait_icon(button); // already loaded in the previous ajax function load_preset()
	// ajax call to get the custom css
	$ck.ajax({
		type: "POST",
		url: 'index.php?option=com_ajax&format=raw&plugin=Maximenuckmobile&group=system',
		data: {
			method: 'loadPresetCustomcss',
			folder: name
		}
	}).done(function(r) {
		if (r.substr(0, 7) == '|ERROR|') {
			alert('Message : ' + r);
		} else {
			$ck('#customcss_area').val(r);
			preview_stylesparams();
		}
		remove_wait_icon(button);
	}).fail(function() {
		//alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function clear_fields() {
	$ck('#ckpopupstyleswizard input,#ckpopupstyleswizard select').each(function() {
		if ( $ck(this).attr('type') == 'radio' ) {
			$ck(this).removeAttr('checked');
		} else {
			$ck(this).val('');
		}
		if ( $ck(this).hasClass('color')) {
			$ck(this).css('background', '');
		}
	});
	$ck('#customcss_area').val('');
}

function reset_styles() {
	var confirm_clear = confirm(Joomla.JText._('CK_ERASE_DATA', 'This will delete all your settings and reset the styles. Do you want to continue ?'));
	if (confirm_clear == false) return;

	clear_fields();
	preview_stylesparams();
}

/* 
 * Switch the interface for best user experience
 */
function switch_fields_area() {
	if ($ck('#jform_params_maximenumobile_theme').val() == 'custom') {
		$ck('#ckmenumobilestyles_container').show();
		$ck('#ckmenumobilestyles_alert').hide();
		preview_stylesparams();
	} else {
		$ck('#ckmenumobilestyles_container').hide();
		$ck('#ckmenumobilestyles_alert').show();
	}
	
}

function set_custom_theme() {
	$ck('#jform_params_maximenumobile_theme option[selected]').removeAttr("selected");
	$ck('#jform_params_maximenumobile_theme option[value=custom]').attr("selected", true);
	$ck('#jform_params_maximenumobile_theme').val('custom').trigger("chosen:updated").trigger("liszt:updated").trigger("change");
}

/*
 * Method to override the save function from Joomla! and save the settings before 
 */
jQuery(document).ready(function() {
	// override the save joomla function to add custom jobs
	var script = document.createElement("script");
	script.setAttribute('type', 'text/javascript');
	script.text = "Joomla.submitbutton = function(task){"
			// + "if ($ck('#jform_params_maximenumobile_theme').val() == 'custom') {"
				+ "save_stylesparams();"
				+ "save_googlefonts();"
				+ "jQuery('#jform_params_maximenumobile_customcss').val(jQuery('#customcss_area').val());"
				+ "save_css_to_file_ck(task);"
			// + "} else {"
				// + "submitform_ck(task);"
			// + "}"
			// + "var form = document.getElementById('style-form') || document.getElementById('modules-form') || document.getElementById('module-form');"
			// + "if (task == 'plugin.cancel' || document.formvalidator.isValid(form)) {"
			// + "Joomla.submitform(task, form);"
			// + "if (self != top) {"
			// + "window.top.setTimeout('window.parent.SqueezeBox.close()', 1000);"
			// + "}"
			// + "} else {"
			// + "alert('Formulaire invalide');"
			// + "}"
			+ "}";
	document.body.appendChild(script);
});