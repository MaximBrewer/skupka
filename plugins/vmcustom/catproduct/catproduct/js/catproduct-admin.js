/**
 *
 * @Author SM planet - smplanet.net
 * @package VirtueMart
 * @subpackage custom
 * @copyright Copyright (C) 2012-2014 SM planet - smplanet.net. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.org
 */
jQuery(document).ready(function() {
	jQuery("input[name^='custom_param[0][add_parent_to_table]']:checkbox").change(function() {
		if(jQuery(this).is(":checked")) {
			jQuery(".show_for_parent").show();
		} else {
			jQuery(".show_for_parent").hide();
		}
	});
	jQuery("input[name^='customfield_params[0][add_parent_to_table]']:checkbox").change(function() {
		if(jQuery(this).is(":checked")) {
			jQuery(".show_for_parent").show();
		} else {
			jQuery(".show_for_parent").hide();
		}
	});
});
 
function catproduct_add_attached () {
	attached_count2 = attached_count;
	html = '';
	html += '<fieldset id="catproduct_attach_fieldset'+attached_count+'"><legend>'+attached_fieldset_text+'</legend>';
	html += '<table class="admintable">';
	html += '<tr><td class="key">'+attached_enable_text+'</td><td>';
	html += '<input type="hidden" name="custom_param['+catproduct_row+'][enable_attached_products_array]['+attached_count2+']" value="0" />';
	html += '<input id="custom_param['+catproduct_row+'][enable_attached_products_array]['+attached_count2+']" type="checkbox" ';
	html += 'name="custom_param['+catproduct_row+'][enable_attached_products_array]['+attached_count2+']" value="1" />';
	html += '</td></tr><tr><td class="key">'+attached_enable_title_text+'</td><td>';
	html += '<input type="hidden" name="custom_param['+catproduct_row+'][enable_title_for_attached_array]['+attached_count2+']" value="0" />';
	html += '<input  id="custom_param['+catproduct_row+'][enable_title_for_attached_array]['+attached_count2+']" type="checkbox" ';
	html += 'name="custom_param['+catproduct_row+'][enable_title_for_attached_array]['+attached_count2+']" value="1" />';
	html += '</td></tr><tr><td class="key">'+attached_title_text+'</td><td>';
	html += '<input type="text"  class="inputbox" id="custom_param['+catproduct_row+'][title_for_attached_products_array]['+attached_count2+']" ';
	html += 'name="custom_param['+catproduct_row+'][title_for_attached_products_array]['+attached_count2+']" size="37" maxlength="255" value="" />';
	html += '</td></tr><tr><td class="key">'+attached_layout_text+'</td><td>';
	html += '<select id="custom_param'+catproduct_row+'attached_products_layout_array'+attached_count+'" name="custom_param['+catproduct_row+'][attached_products_layout_array]['+attached_count2+']">';
	html += attached_fileselect;
	html += '</select>';
	html += '</td></tr><tr><td class="key">'+attached_show_qty_text+'</td><td>';
	html += '<input type="text"  class="inputbox" id="custom_param['+catproduct_row+'][attached_products_def_qty_array]['+attached_count2+']" ';
	html += 'name="custom_param['+catproduct_row+'][attached_products_def_qty_array]['+attached_count2+']" size="37" maxlength="255" value="" />';
	html += '</td></tr><tr class="cp-hide"><td class="key">'+attached_def_qty_text+'</td><td>';
	html += '<input type="hidden" name="custom_param['+catproduct_row+'][attached_products_show_qty_array]['+attached_count2+']" value="0" />';
	html += '<input id="custom_param['+catproduct_row+'][attached_products_show_qty_array]['+attached_count2+']" type="checkbox" ';
	html += 'name="custom_param['+catproduct_row+'][attached_products_show_qty_array]['+attached_count2+']" value="1" />';	
	html += '</td></tr><tr><td class="key">'+attached_finder_text+'</td><td>';
	html += '<select id="custom_param'+catproduct_row+'id_sku_for_attached_products_array'+attached_count+'" name="custom_param['+catproduct_row+'][id_sku_for_attached_products_array]['+attached_count2+']">';
	html += '<option value="id">Product ID</option><option value="sku">Product SKU</option>';
	html += '</select></td></tr><tr><td class="key">'+attached_product_text+'</td><td>';
	html += '<input type="text"  class="inputbox" id="custom_param['+catproduct_row+'][attached_products_array]['+attached_count2+']" ';
	html += 'name="custom_param['+catproduct_row+'][attached_products_array]['+attached_count2+']" size="37" maxlength="255" value="" />';
	html += '</td></tr></table><div  style="float:right;" class="catproduct-button" ';
	html += 'onclick="catproduct_remove_attached('+attached_count+')" style="float:right;">'+attached_remove_text+'</div></fieldset>';
	
	jQuery(".testiranje").append(html);
	attached_count += 1;
}

function catproduct_add_attachedVM3 () {
	attached_count2 = attached_count;
	html = '';
	html += '<fieldset id="catproduct_attach_fieldset'+attached_count+'"><legend>'+attached_fieldset_text+'</legend>';
	html += '<table class="admintable">';
	html += '<tr><td class="key">'+attached_enable_text+'</td><td>';
	html += '<input type="hidden" name="customfield_params['+catproduct_row+'][enable_attached_products_array]['+attached_count2+']" value="0" />';
	html += '<input id="customfield_params['+catproduct_row+'][enable_attached_products_array]['+attached_count2+']" type="checkbox" ';
	html += 'name="customfield_params['+catproduct_row+'][enable_attached_products_array]['+attached_count2+']" value="1" />';
	html += '</td></tr><tr><td class="key">'+attached_enable_title_text+'</td><td>';
	html += '<input type="hidden" name="customfield_params['+catproduct_row+'][enable_title_for_attached_array]['+attached_count2+']" value="0" />';
	html += '<input  id="customfield_params['+catproduct_row+'][enable_title_for_attached_array]['+attached_count2+']" type="checkbox" ';
	html += 'name="customfield_params['+catproduct_row+'][enable_title_for_attached_array]['+attached_count2+']" value="1" />';
	html += '</td></tr><tr><td class="key">'+attached_title_text+'</td><td>';
	html += '<input type="text"  class="inputbox" id="customfield_params['+catproduct_row+'][title_for_attached_products_array]['+attached_count2+']" ';
	html += 'name="customfield_params['+catproduct_row+'][title_for_attached_products_array]['+attached_count2+']" size="37" maxlength="255" value="" />';
	html += '</td></tr><tr><td class="key">'+attached_layout_text+'</td><td>';
	html += '<select id="customfield_params'+catproduct_row+'attached_products_layout_array'+attached_count+'" name="customfield_params['+catproduct_row+'][attached_products_layout_array]['+attached_count2+']">';
	html += attached_fileselect;
	html += '</select>';
	html += '</td></tr><tr><td class="key">'+attached_show_qty_text+'</td><td>';
	html += '<input type="text"  class="inputbox" id="customfield_params['+catproduct_row+'][attached_products_def_qty_array]['+attached_count2+']" ';
	html += 'name="customfield_params['+catproduct_row+'][attached_products_def_qty_array]['+attached_count2+']" size="37" maxlength="255" value="" />';
	html += '</td></tr><tr class="cp-hide"><td class="key">'+attached_def_qty_text+'</td><td>';
	html += '<input type="hidden" name="customfield_params['+catproduct_row+'][attached_products_show_qty_array]['+attached_count2+']" value="0" />';
	html += '<input id="customfield_params['+catproduct_row+'][attached_products_show_qty_array]['+attached_count2+']" type="checkbox" ';
	html += 'name="customfield_params['+catproduct_row+'][attached_products_show_qty_array]['+attached_count2+']" value="1" />';	
	html += '</td></tr><tr><td class="key">'+attached_finder_text+'</td><td>';
	html += '<select id="customfield_params'+catproduct_row+'id_sku_for_attached_products_array'+attached_count+'" name="customfield_params['+catproduct_row+'][id_sku_for_attached_products_array]['+attached_count2+']">';
	html += '<option value="id">Product ID</option><option value="sku">Product SKU</option>';
	html += '</select></td></tr><tr><td class="key">'+attached_product_text+'</td><td>';
	html += '<input type="text"  class="inputbox" id="customfield_params['+catproduct_row+'][attached_products_array]['+attached_count2+']" ';
	html += 'name="customfield_params['+catproduct_row+'][attached_products_array]['+attached_count2+']" size="37" maxlength="255" value="" />';
	html += '</td></tr></table><div  style="float:right;" class="catproduct-button" ';
	html += 'onclick="catproduct_remove_attached('+attached_count+')" style="float:right;">'+attached_remove_text+'</div></fieldset>';
	
	jQuery(".testiranje").append(html);
	attached_count += 1;
}

function catproduct_remove_attached(fieldset_id) {
	jQuery("#catproduct_attach_fieldset"+fieldset_id).remove();
}