/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

var $ck = jQuery.noConflict();

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
* Render the styles from the module helper
*/
function preview_stylesparams(button) {
	if (! button) button = '#ckpopupstyleswizard_makepreview';
	add_wait_icon(button);
	var myurl = 'index.php?option=com_maximenuck&task=preview_module_styles';
	$ck.ajax({
		type: "POST",
		url: myurl,
		data: {
			menuID: 'maximenuck_previewmodule',
			menustyles: make_json_fields('menustyles'),
			level1itemnormalstyles: make_json_fields('level1itemnormalstyles'),
			level1itemhoverstyles: make_json_fields('level1itemhoverstyles'),
			level1itemactivestyles: make_json_fields('level1itemactivestyles'),
			level2menustyles: make_json_fields('level2menustyles'),
			level2itemnormalstyles: make_json_fields('level2itemnormalstyles'),
			level2itemhoverstyles: make_json_fields('level2itemhoverstyles'),
			level2itemactivestyles: make_json_fields('level2itemactivestyles'),
			level1itemnormalstylesicon: make_json_fields('level1itemnormalstylesicon'),
			level1itemhoverstylesicon: make_json_fields('level1itemhoverstylesicon'),
			level2itemnormalstylesicon: make_json_fields('level2itemnormalstylesicon'),
			level2itemhoverstylesicon: make_json_fields('level2itemhoverstylesicon'),
			headingstyles: make_json_fields('headingstyles'),
			orientation: $ck('input[name=orientation]:checked').val(),
			layout: $ck('.layoutthumb.selected').attr('data-name')
		}
	}).done(function(response) {
		response = response.trim();
		if ( response.substring(0,6).toLowerCase() != '|okck|' ) {
			 alert(response);
//			show_ckmodal(response);
		} else {
			response = response.replace(/\|okck\|/g , '');
			jQuery('#ckpopupstyleswizard_preview > .ckstyle').html(response);
			load_gfont_stylesheets();
		}
		remove_wait_icon(button);
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function load_gfont_stylesheets() {
	var gfonturl1 = $ck('#menustylestextisgfont').val() ? get_gfont_stylesheet($ck('#menustylestextgfont').val())  : '';
	var gfonturl2 = $ck('#level2itemnormalstylestextisgfont').val() ?get_gfont_stylesheet($ck('#level2itemnormalstylestextgfont').val()) : '';

	jQuery('#ckpopupstyleswizard_preview > .ckgfontstylesheet').html(gfonturl1 + gfonturl2);
}

function get_gfont_stylesheet(family) {
	if (! family) return '';
	return ("<link href='http://fonts.googleapis.com/css?family="+family+"' rel='stylesheet' type='text/css'>");
}

/**
* Encode the params in json
*/
function make_json_fields(prefix) {
	var fields = [];
	$ck('#ckpopupstyleswizard .' + prefix).each(function(i, field) {
		field = $ck(field);
		var  fieldobj = {};
		if ( field.attr('type') == 'radio' ) {
			if ( field.attr('checked') == 'checked' ) {
				fieldobj['id'] = field.attr('name');
				fieldobj['value'] = field.val();
				fields.push(fieldobj);
			}
		} else if ( field.attr('type') != 'radio' ) {
			fieldobj['id'] = field.attr('id');
			fieldobj['value'] = field.val();
			fields.push(fieldobj);
		}
	});
	fields = JSON.stringify(fields);

	return fields.replace(/"/g, "|qq|");
}

/**
* Set the params array
*/
function get_params_list() {
	var styles = new Array('menustyles'
						,'level1itemnormalstyles'
						,'level1itemnormalstylesicon'
						,'level1itemhoverstyles'
						,'level1itemactivestyles'
						,'level2menustyles'
						,'level2itemnormalstyles'
						,'level2itemnormalstylesicon'
						,'level2itemhoverstyles'
						,'level2itemactivestyles'
						,'level1itemnormalstylesicon'
						,'level1itemhoverstylesicon'
						,'level2itemnormalstylesicon'
						,'level2itemhoverstylesicon'
						,'headingstyles'
						);

	return styles;
}

/**
* Set the options array
*/
function get_options_list() {
	var options = new Array('orientation'
						);

	return options;
}

function add_wait_icon(button) {
	$ck(button).addClass('ckwait');
}

function remove_wait_icon(button) {
	$ck(button).removeClass('ckwait');
}

/**
* Save the param in the module options
*/
function save_param(id, param, value) {
	var myurl = 'index.php?option=com_maximenuck&task=save_param';
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: false,
		data: {
			id: id,
			param: param,
			value: value
		}
	}).done(function(response) {
		response = response.trim();
		if (response != '1') {
			show_ckmodal(Joomla.JText._('CK_ERROR_SAVING_PARAM', 'Error when saving the param : ' + param) + '. ' + response);
			// alert(Joomla.JText._('CK_ERROR_SAVING_PARAM', 'Error when saving the param : ' + param) + '. ' + response);
		}
		if (window.parent.document.getElementById('jform_params_'+param)) window.parent.document.getElementById('jform_params_'+param).value = value;
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

/**
* Loop through the params to save
*/
function save_stylesparams(button, id, layout) {
	if (! layout) layout = '';
	add_wait_icon(button);
	var styles_to_save = get_params_list();
	for (i=0;i<styles_to_save.length;i++) {
		save_stylesparam(id, styles_to_save[i], layout);
	}
	// save the theme option
	save_param(id, 'theme', $ck('.themethumb.selected').attr('data-name'));
	save_param(id, 'layout', '_:' + $ck('.layoutthumb.selected').attr('data-name'));
	// save all other options
	$ck('.ckoption').each(function(i, field) {
		field = $ck(field);
		if ( field.attr('type') == 'radio' ) {
			if ( field.attr('checked') == 'checked' ) {
				save_param(id, field.attr('name'), field.val());
			}
		} else if ( field.attr('type') != 'radio' ) {
			save_param(id, field.attr('id'), field.val());
		}
	});
	remove_wait_icon(button);
}

/**
* Save the param in the module options
*/
function save_stylesparam(id, param, layout) {
	if (! layout) layout = '';
	var myurl = 'index.php?option=com_maximenuck&task=save_param';
	var param_value = make_json_fields(param);
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: false,
		data: {
			id: id,
			param: param,
			value: param_value
		}
	}).done(function(response) {
		response = response.trim();
		if (response != '1') {
			show_ckmodal(Joomla.JText._('CK_ERROR_SAVING_PARAM', 'Error when saving the param : ' + param) + '. ' + response);
			// alert(Joomla.JText._('CK_ERROR_SAVING_PARAM', 'Error when saving the param : ' + param) + '. ' + response);
		}
		if (layout == 'modal') {
			if (window.parent.document.getElementById('jform_params_'+param)) window.parent.document.getElementById('jform_params_'+param).value = param_value;
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

/**
* Loop through the params to load
*/
function load_stylesparams(id) {
	add_wait_icon('#ckpopupstyleswizard_makepreview');
	var styles_to_load = get_params_list();
	for (i=0;i<styles_to_load.length;i++) {
		$ck(document.body).append('<i class="hidden-loadingstylesparam '+i+'"></i>');
		load_stylesparam(id, styles_to_load[i]);
	}
	$ck(document.body).append('<i class="hidden-loadingstylesparam '+i+'"></i>');
	load_options(id);
	// var options_to_load = get_options_list();
	// for (i=0;i<options_to_load.length;i++) {
		// set_value_to_field(options_to_load[i], value);
	// }
}

/**
* Load the param from the module options
*/
function load_options(id) {
	var myurl = 'index.php?option=com_maximenuck&task=load_param';
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: true,
		data: {
			id: id,
			param: '',
			all: true
		}
	}).done(function(response) {
		response = response.trim();
		if ( response.substring(0,5).toLowerCase() == 'error' ) {
			show_ckmodal(response);
			// alert(response);
		} else {
			if ( response.length ) {
				var options = jQuery.parseJSON(response);
				// load all the options other than the styles params
				var options_to_load = get_options_list();
				for (i=0;i<options_to_load.length;i++) {
					// compatibility for old j!2.5 orientation value
					if ( options_to_load[i] === 'orientation' && options[options_to_load[i]] != 'vertical' && options[options_to_load[i]] != 'horizontal' ) {
						options[options_to_load[i]] = ( options[options_to_load[i]] === '1' ) ? 'vertical' : options[options_to_load[i]];
					}
					set_value_to_field(options_to_load[i], options[options_to_load[i]]);
				}
			}
			if ($ck('.hidden-loadingstylesparam').length) $ck($ck('.hidden-loadingstylesparam')[0]).remove();
			// launch the preview once all settings have been applied
			if ( !$ck('.hidden-loadingstylesparam').length ) {
				// launch the preview
				preview_stylesparams('#ckpopupstyleswizard_makepreview');
				// init the colorpickers
				jscolor.init();
			}
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

/**
* Load the param from the module options
*/
function load_stylesparam(id, param) {
	var myurl = 'index.php?option=com_maximenuck&task=load_param';
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: true,
		data: {
			id: id,
			param: param
		}
	}).done(function(response) {
		response = response.trim();
		if ( response.substring(0,5).toLowerCase() == 'error' ) {
			show_ckmodal(response);
			// alert(response);
		} else if ( response.length ) {
			apply_stylesparam(param, response);
		}
		if ($ck('.hidden-loadingstylesparam').length) $ck($ck('.hidden-loadingstylesparam')[0]).remove();
		if ( !$ck('.hidden-loadingstylesparam').length ) {
			// launch the preview
			preview_stylesparams('#ckpopupstyleswizard_makepreview');
			// init the colorpickers
			jscolor.init();
		}

	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

/**
* Set the stored value for each field
*/
function apply_stylesparam(param, json_value) {
	var searchWords = ["usefont", "usemargin", "usebackground", "usegradient", "useroundedcorners", "useshadow", "useborders", "usetextshadow"];
	var fields = jQuery.parseJSON(json_value.replace(/\|qq\|/g, "\""));
	var i;

	for (i=0;i<fields.length;i++) {
		fieldobj = fields[i];
		fieldobj['id'] = fieldobj['id'].replace(param + '_', ''); // compatibility for old params
		set_value_to_field(fieldobj['id'], fieldobj['value']);
		// look if the 'use' word is present, we launch the migration script
		if (!$ck(document.body).hasClass('migration_tested'+param) && multiSearchOr(fieldobj['id'], searchWords, false)) {
			$ck(document.body).addClass('migration_tested'+param);
				$ck(document.body).addClass('yes_do_migration'+param);
		}
	}
	// if needed, we launch the migration script
	if ($ck(document.body).hasClass('yes_do_migration'+param)) {
		process_automatic_migration(param, fields, searchWords)
	} 
}

/**
* Method to look for unwanted values coming from the old version
*/
function process_automatic_migration(param, fields, searchWords) {
	var i;
	var relations = get_relations_for_migration();
	for (i=0;i<fields.length;i++) {
		fieldobj = fields[i];
		fieldobj['id'] = fieldobj['id'].replace(param + '_', ''); // compatibility for old params
		if (parameter = multiSearchOr(fieldobj['id'], searchWords, true)) {
			var prefix = fieldobj['id'].replace(parameter, '');
			if (fieldobj['value'] != '1') {
				var elements_to_reset = relations[parameter].split(',');
				for (j=0;j<elements_to_reset.length;j++) {
					$ck('#'+prefix + elements_to_reset[j]).val('');
				}
			}
		}
	}
}

/**
* Set the relations between the old activation field and the dependant fields
* for the migration script
*/
function get_relations_for_migration() {
	return { "usefont" : "fontsize,fontcolor,fontweight,descfontsize,descfontcolor,textgfont"
		, "usemargin" : "margintop,marginright,marginbottom,marginleft,paddingtop,paddingright,paddingbottom,paddingleft"
		, "usebackground": "bgcolor1,bgopacity,bgimage,bgpositionxbgpositiony,bgimagerepeat"
		, "usegradient" : "bgcolor2"
		, "useroundedcorners" : "roundedcornerstl,roundedcornerstr,roundedcornersbr,roundedcornersbl"
		, "useshadow" : "shadowcolor,shadowblur,shadowspread,shadowoffsetx,shadowoffsety,shadowinset"
		, "useborders" : "bordercolor,bordertopwidth,borderrightwidth,borderbottomwidth,borderleftwidth"
		, "usetextshadow" : "textshadowcolor,textshadowblur,textshadowoffsetx,textshadowoffsety"
		, "useparentitem" : "parentitemimage,parentitemimagepositionx,parentitemimagepositiony,parentitemimagerepeat,parentitempaddingtop,parentitempaddingright,parentitempaddingbottom,parentitempaddingleft"
		};
}

/**
* Search for a string from a list of words
* for the migration script
*/
function multiSearchOr(text, searchWords, textual){
	var searchExp = new RegExp(searchWords.join("$|"),"gi");
	if (matches = text.match(searchExp)) {
		$ck(document.body).addClass('migration_tested');
		if (textual == true) return matches[0];
		return true;
	}
	return false;
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

/**
* Get the theme from the module options and load the needed stylesheets
*/
function load_module_theme(id) {
	var myurl = 'index.php?option=com_maximenuck&task=load_param';
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: true,
		data: {
			id: id,
			param: 'theme'
		}
	}).done(function(response) {
		response = response.trim();
		if ( response.substring(0,5).toLowerCase() == 'error' ) {
			show_ckmodal(response);
			// alert(response);
			change_theme_stylesheet('blank');
		} else {
			change_theme_stylesheet(response);
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

/**
* Load the stylesheet from the module theme
*/
function change_theme_stylesheet(name) {
	$ck('.themethumb').removeClass('selected');
	$ck('.themethumb[data-name=' + name + ']').addClass('selected');
	$ck('#ckpopupstyleswizard_preview > .ckstylesheet').empty() //.append('<link rel="stylesheet" href="'+URIROOT+'/modules/mod_maximenuck/themes/'+name+'/css/moo_maximenuhck.css" type="text/css" />')
		.append('<link rel="stylesheet" href="'+URIROOT+'/modules/mod_maximenuck/themes/'+name+'/css/maximenuck.php?monid=maximenuck_previewmodule" type="text/css" />');
}

/**
* Load the php module layout
*/
function change_layout(name, orientation) {
	add_wait_icon('#ckpopupstyleswizard_makepreview');
	if (! orientation) orientation = $ck('input[name=orientation]:checked').val();
	$ck('.layoutthumb').removeClass('selected');
	$ck('.layoutthumb[data-name=' + name + ']').addClass('selected');
	var myurl = 'index.php?option=com_maximenuck&view=styles&layout=default_render_menu_module&modulelayout=' + name;
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: false,
		data: {
			orientation: orientation
		}
	}).done(function(response) {
		response = response.trim();
		if ( response.substring(0,5).toLowerCase() == 'error' ) {
			remove_wait_icon('#ckpopupstyleswizard_makepreview');
			show_ckmodal(response);
			// alert(response);
		} else {
			$ck('#ckpopupstyleswizard_preview > .inner').empty().append(response);
			remove_wait_icon('#ckpopupstyleswizard_makepreview');
			preview_stylesparams();
		}
		
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

/*
* Change the menu orientation
*/
function change_menu_orientation(orientation) {
	change_layout($ck('.layoutthumb.selected').attr('data-name'), orientation);
	if ( orientation === 'vertical' ) {
		$ck('#ckpopupstyleswizard_preview > .inner').css('width', '200px');
	} else {
		$ck('#ckpopupstyleswizard_preview > .inner').css('width', '');
	}
}

/**
* Clear all fields
*/
function clear_fields() {
	var confirm_clear = confirm('This will delete all your settings and reset the styles. Do you want to continue ?');
	if (confirm_clear == false) return;
	$ck('#ckpopupstyleswizard input').each(function(i, field) {
		field = $ck(field);
		if (field.attr('type') == 'radio') {
			field.removeAttr('checked');
		} else {
			field.val('');
			if (field.hasClass('color')) field.css('background','');
		}
	});
	// launch the preview
	preview_stylesparams('#ckpopupstyleswizard_makepreview');
}

function disable_active_styles(tab) {
	$ck(tab).find('input:not(.undisabled)').attr('disabled', 'disabled');
}

function enable_active_styles(tab) {
	$ck(tab).find('input').removeAttr('disabled');
}

function clean_gfont_name(field) {
	var myurl = 'index.php?option=com_maximenuck&task=clean_gfont_name';
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: false,
		data: {
			gfont: $ck(field).val().replace("<", "").replace(">", "")
		}
	}).done(function(response) {
		response = response.trim();
		if ( response.substring(0,5).toLowerCase() == 'error' ) {
			show_ckmodal(response);
			// alert(response);
		} else {
			$ck(field).val(response);
		}
		check_font_exists(field);
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function check_font_exists(field) {
	if (!field.value) return;
	var myurl = '//fonts.googleapis.com/css?family=' + field.value;
	$ck.ajax({
		url: myurl,
		data: {

		},
		 statusCode: {
			200: function() {
				$ck(field).next('.isgfont').val('1');
				load_gfont_stylesheets();
			}
		}
	}).done(function(response) {
		$ck(field).next('.isgfont').val('0');
	}).fail(function() {
		alert(Joomla.JText._('CK_IS_NOT_GOOGLE_FONT', 'This is not a google font, check that it is loaded in your website'));
		$ck(field).next('.isgfont').val('0');
	});
}

function check_gradient_image_conflict(from, field) {
	if ($ck(from).val()) {
		if ($ck('#'+field).val()) {
			alert('Warning : you can not have a gradient and a background image at the same time. You must choose which one you want to use');
		}
	}
}

// to toggle the fullscreen mode of the sqeezebox
function toggle_fullscreen(doc) {
	$ck(doc.SqueezeBox.win).css({
		width: '100%',
		height: '100%',
		top: '0',
		left: '0'
	});
	$ck(doc.SqueezeBox.win).find('iframe').css({
		width: '100%',
		height: '100%',
		top: '0',
		left: '0'
	});
}

/**
* Load a modal window
*/
function show_ckmodal(txt) {
	$ck(document.body).append('<div id="ckmodal"><div id="ckmodal_title">ERROR MESSAGE FROM MAXIMENU CK PARAMS</div>'+txt+'</div>');
	SqueezeBox.initialize();
	SqueezeBox.open($('ckmodal'), {
		handler: 'adopt',
		size: {x: 600, y: 400}
	});
}

/**
* Migration tool to delete the margins
*/
function delete_values_normal_state(btn) {
	var areyousure = confirm('This will delete all the margins for this item - Normal State. Continue ?');
	if (areyousure == false) return;
	var item = $ck($ck(btn).parents('li')[0]);

	init_item_params_migration(item.attr('data-id'), 'normal', btn);
}

function delete_values_hover_state(btn) {
	var areyousure = confirm('This will delete all the margins for this item - Hover State. Continue ?');
	if (areyousure == false) return;
	var item = $ck($ck(btn).parents('li')[0]);

	init_item_params_migration(item.attr('data-id'), 'hover', btn);
}

function delete_values_active_state(btn) {
	var areyousure = confirm('This will delete all the margins for this item - Active State. Continue ?');
	if (areyousure == false) return;
	var item = $ck($ck(btn).parents('li')[0]);

	init_item_params_migration(item.attr('data-id'), 'active', btn);
}

function save_item_param(id, param, value, btn, waiticon) {
	// if (!waiticon) waiticon = false;
	// if (waiticon) addckwaiticon($ck(btn));
	var myurl = "index.php?option=com_maximenuck&task=save_item_param";
	jQuery.ajax({
	type: "POST",
	url: myurl,
	async: false,
	data: {
		id: id,
		param: param,
		value:value
		}
	}).done(function(response) { 
		response = response.trim();
		if (response != '1') {
			// if (waiticon) removeckwaiticon($ck(btn), true);
			show_ckmodal(Joomla.JText._('CK_ERROR_SAVING_PARAM', 'Error when saving the param : ' + param) + '. ' + response);
		} else {
			if (value === '') value = '&nbsp;';
			$ck(btn).html(value);
			// if (waiticon) removeckwaiticon($ck(btn), false);
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
		// if (waiticon) removeckwaiticon($ck(btn), true);
	});
}

function init_item_params_migration(id, state, btn) {
	var btnrow = $ck(btn).parents()[0];
	btnrow = $ck(btnrow);
	btnrow.append('<span class="ckwait" style="width:16px;display:inline-block;"></span>');
	var myurl = "index.php?option=com_maximenuck&task=init_item_params_migration";
	jQuery.ajax({
	type: "POST",
	url: myurl,
	async: true,
	data: {
		id: id,
		state: state
		}
	}).done(function(response) { 
		response = response.trim();
		if (response != '1') {
			show_ckmodal(Joomla.JText._('CK_ERROR_SAVING_PARAM', 'Error when saving the params for the state : ' + state) + '. ' + response);
		} else {
//			if (value === '') 
				value = '&nbsp;';
			btnrow.find('.valueck').html(value);
			btnrow.find('.ckwait').remove();
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function delete_all_values() {
	var areyousure = confirm('This will delete all the margins for all items. Continue ?');
	if (areyousure == false) return;
	var items = $ck('#sortable li');

	items.each(function(i, el){
		el = $ck(el);
		$ck('.waitrow', el).append('<span class="ckwait" style="width:16px;display:inline-block;"></span>');
		var myurl = "index.php?option=com_maximenuck&task=init_item_params_migration";
		jQuery.ajax({
		type: "POST",
		url: myurl,
		async: true,
		data: {
			id: el.attr('data-id'),
			state: 'all'
			}
		}).done(function(response) { 
			response = response.trim();
			if (response != '1') {
				show_ckmodal(Joomla.JText._('CK_ERROR_SAVING_PARAM', 'Error when saving the params for the state : ' + state) + '. ' + response);
			} else {
	//			if (value === '') 
					value = '&nbsp;';
				$ck('.valueck', el).html(value);
				$ck('.ckwait', el).remove();
			}
		}).fail(function() {
			alert(Joomla.JText._('CK_FAILED', 'Failed'));
		});
	});
	

}

function get_params_fields(prefix) {
	var fields = {};
	$ck('#ckpopupstyleswizard .' + prefix).each(function(i, field) {
		field = $ck(field);
		var  fieldobj = {};
		if ( field.attr('type') == 'radio' ) {
			if ( field.attr('checked') == 'checked' ) {
				fields[field.attr('name')] = field.val();
			}
		} else if ( field.attr('type') != 'radio' ) {
			fields[field.attr('id')] = field.val();
		}
	});
	return fields;
}

function exportparams(moduleid) {
	var fields = {};
	var params_list = get_params_list();
	var params = {};
	// make a global object with all fields name and value
	for (i=0;i<params_list.length;i++) {
		$ck.extend(params, get_params_fields(params_list[i]));
	}
	
	var maximenu_styles = JSON.stringify(params);
	maximenu_styles = maximenu_styles.replace(/"/g, "|qq|");

	var myurl = 'index.php?option=com_maximenuck&task=exportParams';
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: false,
		data: {
			jsonfields: maximenu_styles,
			moduleid: moduleid
		}
	}).done(function(response) {
		if (response == 'true') {
			if ($ck('#maximenuckexportfile').length) $ck('#maximenuckexportfile').remove();
			$ck('#ckExportPageDownload').append('<div id="maximenuckexportfile"><a class="btn" target="_blank" href="'+URIROOT+'/administrator/components/com_maximenuck/export/exportParams'+moduleid+'.mmck" download="exportParams'+moduleid+'.mmck">'+Joomla.JText._('CK_DOWNLOAD', 'Download')+'</a></div>');
			CKBox.open({handler:'inline', content: 'maximenuckexportpopup', fullscreen: false, size: {x: '400px', y: '100px'}});
		} else {
			alert('test')
		}
	}).fail(function() {
		// alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
	return;
}

function importparams() {
	CKBox.open({id:'maximenuckimportbox', handler:'inline', content: 'maximenuckimportpopup', fullscreen: false, size: {x: '700px', y: '200px'}});
}

function uploadParamsFile(formData) {
	var myurl = 'index.php?option=com_maximenuck&task=uploadParamsFile';
	$ck.ajax({
		type: "POST",
		url: myurl,
		async: false,
		data: formData,
		dataType: 'json',
		processData: false,  // indique à jQuery de ne pas traiter les données
		contentType: false   // indique à jQuery de ne pas configurer le contentType
	}).done(function(response) {
//		console.log(response);
		if(typeof response.error === 'undefined')
		{
			// Success
			importParamsFile(response.data);
		} else {
			console.log('ERROR: ' + response.error);
		}
	}).fail(function() {
		// alert(Joomla.JText._('CK_FAILED', 'Failed'));
	});
}

function importParamsFile(data) {
	var fields = jQuery.parseJSON(data.replace(/\|qq\|/g, "\""));
	for (var field in fields) {
		set_value_to_field(field, fields[field])
	}

	// launch the preview
	preview_stylesparams('#ckpopupstyleswizard_makepreview');
	CKBox.close('#importPage');
}