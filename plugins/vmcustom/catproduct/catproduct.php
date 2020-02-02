<?php
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;
/**
 *
 * @author SM planet - smplanet.net
 * @package VirtueMart
 * @subpackage custom
 * @copyright Copyright (C) 2012-2014 SM planet - smplanet.net. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 **/

if (!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');

class plgVmCustomCatproduct extends vmCustomPlugin {


	function __construct(& $subject, $config) {
		parent::__construct($subject, $config);

		$varsToPush = array(	'use_default'=>array(0.0,'bool'),	
								'use_default2'=>array(0.0,'bool'),	
								'sorting_field'=>array('default','string'),
								'layout_field'=>array('default.php','string'),
								'show_image'=>array(0,'bool'),
								'show_id'=>array(0.0,'bool'),
						    	'show_sku'=>array(0.0,'bool'),
						    	'show_name'=>array(0.0,'bool'),
								'show_s_desc'=>array(0.0,'bool'),
								'show_weight'=>array(0.0,'bool'),
								'show_sizes'=>array(0.0,'bool'),
								'show_stock'=>array(0.0,'bool'),
								'show_min_qty'=>array(0.0,'bool'),
								'show_max_qty'=>array(0.0,'bool'),
								'show_step_qty'=>array(0.0,'bool'),
								'show_basePrice'=>array(0.0,'bool'),
								'show_basePriceWithTax'=>array(0.0,'bool'),
								'show_salesPrice'=>array(0.0,'bool'),
								'show_taxAmount'=>array(0.0,'bool'),
								'show_priceWithoutTax'=>array(0.0,'bool'),
								'show_discountAmount'=>array(0.0,'bool'),
								'show_sum_weight'=>array(0.0,'bool'),
								'show_sum_basePrice'=>array(0.0,'bool'),
								'show_sum_basePriceWithTax'=>array(0.0,'bool'),
								'show_sum_salesPrice'=>array(0.0,'bool'),
								'show_sum_taxAmount'=>array(0.0,'bool'),
								'show_sum_priceWithoutTax'=>array(0.0,'bool'),
								'show_sum_discountAmount'=>array(0.0,'bool'),
								'show_total_weight'=>array(0.0,'bool'),
								'show_total_basePrice'=>array(0.0,'bool'),
								'show_total_basePriceWithTax'=>array(0.0,'bool'),
								'show_total_salesPrice'=>array(0.0,'bool'),
								'show_total_taxAmount'=>array(0.0,'bool'),
								'show_total_priceWithoutTax'=>array(0.0,'bool'),
								'show_total_discountAmount'=>array(0.0,'bool'),
								'enable_attached_products_array'=>array('','array'),
								'enable_title_for_attached_array'=>array('','array'),
								'title_for_attached_products_array'=>array('', 'array'),
								'id_sku_for_attached_products_array'=>array('','array'),
								'attached_products_array'=>array('','array'),
								'attached_products_layout_array'=>array('','array'),
								'attached_products_def_qty_array'=>array('','array'),
								'attached_products_show_qty_array'=>array('','array'),
								'use_max_min_quantity'=>array(0,'bool'),
								'use_box_quantity'=>array(0,'bool'),
								'update_prices'=>array(0,'bool'),
								'attached_array'=>array(0,'bool'),
								'add_parent_to_table'=>array(0,'bool'),
								'add_parent_from_original'=>array(0,'bool'),
								'do_not_show_child'=>array(0,'bool'),
								'add_plugin_support'=>array(0,'integer'),
								'enable_cs'=>array(0,'bool'),
								'c_string_1'=>array('','string'),
								'c_string_2'=>array('','string'),
								'c_string_3'=>array('','string'),
								'c_string_4'=>array('','string'),
								'c_string_5'=>array('','string'),
								'hide_original_addtocart'=>array('css','string'),
								'original_addtocart_css'=>array('.addtocart-area','string'),
								'orig_add_area'=>array('.productdetails-view .addtocart-area','string'),
								'override'=>array(0,'bool'),
								'layout_field_children'=>array('default.php','string'),
								'layout_field_parent'=>array('default.php','string'),
								'def_qty_children'=>array('0','string'),
								'def_qty_parent'=>array('0','string'),
								'show_qty_children'=>array('0','string'),
								'show_qty_parent'=>array('0','string')
		);
								/*	REMOVED FROM V2.9.9
								'enable_attached_products'=>array(0.0,'bool'),
								'enable_title_for_attached'=>array(0.0,'bool'),
								'title_for_attached_products'=>array('','string'),
								'id_sku_for_attached_products'=>array('','string'),
								'attached_products'=>array('','string'),*/
		if(!defined('VM_VERSION') or VM_VERSION < 3){
			$this->setConfigParameterable ('custom_params', $varsToPush);
		} else {
			$this->setConfigParameterable ('customfield_params', $varsToPush);
		}

	}

	// get product param for this plugin on edit
	function plgVmOnProductEdit($field, $product_id, &$row,&$retValue) {
		if ($field->custom_element != $this->_name) return '';
		if (!class_exists ('CatproductFunction')) {
			require(JPATH_PLUGINS . DS . 'vmcustom' . DS . 'catproduct' . DS . 'catproduct' . DS . 'helpers' . DS . 'catproductfunctions.php');
		}
		
		$cat_helper = new CatproductFunction();
		//$global_params = $cat_helper->getGlobalParameters($group->custom_params);
		
		if(!defined('VM_VERSION') or VM_VERSION < 3){
			$this->parseCustomParams ($field);
			$paramName = 'custom_param';
		} else {
			$paramName = 'customfield_params';
		}
		$host = JURI::root();
		$document = JFactory::getDocument();
		$document->addScript($host."/plugins/vmcustom/catproduct/catproduct/js/catproduct-admin.js");
		$document->addStylesheet($host."/plugins/vmcustom/catproduct/catproduct/css/catproduct-admin.css");
	
		$html = '';
		$hide = '';
		// get array for sorting
		$sorting = $cat_helper->getArrayForSorting();
		// array for attached select
		$field_attached = $cat_helper->getArrayForAttachedSelect();

		// get layout files
		$layout_f = $cat_helper->getFilesInFolder("../plugins/vmcustom/catproduct/catproduct/tmpl/");
		
		// get layout files for groups
		$layout_g = $cat_helper->getFilesInFolder("../plugins/vmcustom/catproduct/catproduct/tmpl/group_layouts/");
		
		//fill $hideaddtocart
		$hideaddtocart = $cat_helper->getHideaddtocart(); 			
			
		$handle_plugins = $cat_helper->getHandlePlugins(); 
		
		// get catproduct manifest 
		$file = JPATH_PLUGINS . DS . 'vmcustom' . DS . 'catproduct' . DS .'catproduct.xml';
		$xml = simplexml_load_file($file);
		$version = '';
		if (isset($xml->version[0])) $version = "Catproduct version: ".$xml->version[0]." - ";
		
		$html .='
			<fieldset  class="testiranje">
				<legend>'.$version. JText::_('CATPRODUCT_FIELDSET1_TITLE') . '</legend>';
		
		$html2 = '';
		
		if ($field->use_default2 != 1) {
			$html2 .=   '<tr><td class="key">'. JText::_('CATPRODUCT_CHOOSE_LAYOUT') .'</td><td>'
					.JHTML::_ ('select.genericlist', $layout_f, $paramName.'['.$row.'][layout_field]', 'aaa', 'value', 'text', $field->layout_field).'</td></tr>
					<tr><td class="key">'. JText::_('CATPRODUCT_CHOOSE_LAYOUT_CHILDREN') .'</td><td>'
					.JHTML::_ ('select.genericlist', $layout_g, $paramName.'['.$row.'][layout_field_children]', 'aaa', 'value', 'text', $field->layout_field_children).'</td></tr>'
					.'<tr><td class="key">'. JText::_('CATPRODUCT_DEFAULT_QTY_CHILDREN') .'</td>'.
					'<td><input type="text" class="inputbox" id="'.$paramName.'['.$row.'][def_qty_children]" name="'.$paramName.'['.$row.'][def_qty_children]" 
					size="5" maxlength="10" value="'.$field->def_qty_children.'"></td></tr>'.
					'<tr><td class="key">'. JText::_('CATPRODUCT_HIDE_ADDTOCART') .'</td><td>
					'.JHTML::_ ('select.genericlist', $hideaddtocart, $paramName.'['.$row.'][hide_original_addtocart]', 'aaa', 'value', 'text', $field->hide_original_addtocart).'</td></tr>
					'.VmHTML::row('input',JText::_('CATPRODUCT_ORIGINAL_ADDTOCART_CSS'), $paramName.'['.$row.'][original_addtocart_css]',$field->original_addtocart_css).'
					'.VmHTML::row('input',JText::_('CATPRODUCT_ORIGINALADDTOCARTAREACLASS'), $paramName.'['.$row.'][orig_add_area]',$field->orig_add_area);		
		} else $html .='<p><strong>Use_default is set to yes in plug-in configuration</strong></p>';
		if ($field->use_default == 1) {
			$hide = "display:none;";
			$html .='<p><strong>Use_default for product fields is set to yes in plug-in configuration</strong></p>';
		}
		
		$add_for_parent = "display:none;";
		if ($field->add_parent_to_table == '1') $add_for_parent = '';

		$html .='
					<fieldset>
					<legend>'. JText::_('CATPRODUCT_FIELDSET6_TITLE') .'</legend>
					<table class="admintable"><tr><td class="key">'. JText::_('CATPRODUCT_CHOOSE_SORTING') .'</td><td>
					'.JHTML::_ ('select.genericlist', $sorting, $paramName.'['.$row.'][sorting_field]', '', 'value', 'text', $field->sorting_field).'</td></tr>
					'.$html2.'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_OVERRIDE_CHILD_SETTINGS'), $paramName.'['.$row.'][override]',$field->override).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_UPDATE_PRICES'), $paramName.'['.$row.'][update_prices]',$field->update_prices).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_USE_MAX_MIN'), $paramName.'['.$row.'][use_max_min_quantity]',$field->use_max_min_quantity).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_USE_BOX_QUANTITY'), $paramName.'['.$row.'][use_box_quantity]',$field->use_box_quantity).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_ADDPARENTTOTABLE'), $paramName.'['.$row.'][add_parent_to_table]',$field->add_parent_to_table);
		if ($field->use_default2 != 1) {			
		$html .=	'<tr class="show_for_parent" style="'.$add_for_parent.'"><td class="key">'. JText::_('CATPRODUCT_CHOOSE_LAYOUT_PARENT') .'</td><td>'
					.JHTML::_ ('select.genericlist', $layout_g, $paramName.'['.$row.'][layout_field_parent]', 'aaa', 'value', 'text', $field->layout_field_parent).'</td></tr>'
					.'<tr class="show_for_parent" style="'.$add_for_parent.'"><td class="key">'. JText::_('CATPRODUCT_DEFAULT_QTY_PARENT') .'</td>'.
					'<td><input type="text" class="inputbox" id="'.$paramName.'['.$row.'][def_qty_parent]" name="'.$paramName.'['.$row.'][def_qty_parent]" 
					size="5" maxlength="10" value="'.$field->def_qty_parent.'"></td></tr>';
		}			
		$html .=	VmHTML::row('checkbox',JText::_('CATPRODUCT_ADDPARENTFROMORIGINAL'), $paramName.'['.$row.'][add_parent_from_original]',$field->add_parent_from_original).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_DO_NOT_SHOW_CHILD'), $paramName.'['.$row.'][do_not_show_child]',$field->do_not_show_child).'
					<tr><td class="key">'. JText::_('CATPRODUCT_ADD_CUSTOMFIELDS_SUPPORT') .'</td><td>
					'.JHTML::_ ('select.genericlist', $handle_plugins, $paramName.'['.$row.'][add_plugin_support]', 'aaa', 'value', 'text', $field->add_plugin_support).'
					</table>
					</fieldset>';
					
					if (isset($field->enable_cs) && $field->enable_cs == 1) {
					
		$html .='	<fieldset>
					<legend>'. JText::_('CATPRODUCT_FIELDSET7_TITLE') .'</legend>
					<table class="admintable">
					'.VmHTML::row('input',JText::_('CATPRODUCT_CUSTOMSTRING_TITLE_1'), $paramName.'['.$row.'][c_string_1]',$field->c_string_1).'
					'.VmHTML::row('input',JText::_('CATPRODUCT_CUSTOMSTRING_TITLE_2'), $paramName.'['.$row.'][c_string_2]',$field->c_string_2).'
					'.VmHTML::row('input',JText::_('CATPRODUCT_CUSTOMSTRING_TITLE_3'), $paramName.'['.$row.'][c_string_3]',$field->c_string_3).'
					'.VmHTML::row('input',JText::_('CATPRODUCT_CUSTOMSTRING_TITLE_4'), $paramName.'['.$row.'][c_string_4]',$field->c_string_4).'
					'.VmHTML::row('input',JText::_('CATPRODUCT_CUSTOMSTRING_TITLE_5'), $paramName.'['.$row.'][c_string_5]',$field->c_string_5).'
					</table>
					</fieldset>';
					}
					
		$html .='	<fieldset style='.$hide.'>
					<legend>'. JText::_('CATPRODUCT_FIELDSET2_TITLE') .'</legend>
					<table class="admintable">
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SHOWIMAGE'), $paramName.'['.$row.'][show_image]',$field->show_image).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SHOWID'), $paramName.'['.$row.'][show_id]',$field->show_id).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SHOWSKU'), $paramName.'['.$row.'][show_sku]',$field->show_sku).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SHOWNAME'), $paramName.'['.$row.'][show_name]',$field->show_name).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SHOWSDESC'), $paramName.'['.$row.'][show_s_desc]',$field->show_s_desc).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_WEIGHT'), $paramName.'['.$row.'][show_weight]',$field->show_weight).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SIZES'), $paramName.'['.$row.'][show_sizes]',$field->show_sizes).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_STOCK'), $paramName.'['.$row.'][show_stock]',$field->show_stock).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SHOW_MIN_QTY'), $paramName.'['.$row.'][show_min_qty]',$field->show_min_qty).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SHOW_MAX_QTY'), $paramName.'['.$row.'][show_max_qty]',$field->show_max_qty).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SHOW_STEP_QTY'), $paramName.'['.$row.'][show_step_qty]',$field->show_step_qty).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_BASEPRICE'), $paramName.'['.$row.'][show_basePrice]',$field->show_basePrice).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_BASEPRICEWITHTAX'), $paramName.'['.$row.'][show_basePriceWithTax]',$field->show_basePriceWithTax).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_PRICEWITHOUTTAX'), $paramName.'['.$row.'][show_priceWithoutTax]',$field->show_priceWithoutTax).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SALESPRICE'), $paramName.'['.$row.'][show_salesPrice]',$field->show_salesPrice).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_TAXAMOUNT'), $paramName.'['.$row.'][show_taxAmount]',$field->show_taxAmount).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_DISCOUNTAMOUNT'), $paramName.'['.$row.'][show_discountAmount]',$field->show_discountAmount).'
					</table>
					</fieldset>
					<fieldset style='.$hide.'>
					<legend>'. JText::_('CATPRODUCT_FIELDSET3_TITLE') .'</legend>
					<table class="admintable">
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SUM_WEIGHT'), $paramName.'['.$row.'][show_sum_weight]',$field->show_sum_weight).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SUM_BASEPRICE'), $paramName.'['.$row.'][show_sum_basePrice]',$field->show_sum_basePrice).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SUM_BASEPRICEWITHTAX'), $paramName.'['.$row.'][show_sum_basePriceWithTax]',$field->show_sum_basePriceWithTax).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SUM_PRICEWITHOUTTAX'), $paramName.'['.$row.'][show_sum_priceWithoutTax]',$field->show_sum_priceWithoutTax).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SUM_SALESPRICE'), $paramName.'['.$row.'][show_sum_salesPrice]',$field->show_sum_salesPrice).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SUM_TAXAMOUNT'), $paramName.'['.$row.'][show_sum_taxAmount]',$field->show_sum_taxAmount).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_SUM_DISCOUNTAMOUNT'), $paramName.'['.$row.'][show_sum_discountAmount]',$field->show_sum_discountAmount).'
					</table>
					</fieldset>
					<fieldset style='.$hide.'>
					<legend>'. JText::_('CATPRODUCT_FIELDSET4_TITLE') .'</legend>
					<table class="admintable">
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_TOTAL_WEIGHT'), $paramName.'['.$row.'][show_total_weight]',$field->show_total_weight).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_TOTAL_BASEPRICE'), $paramName.'['.$row.'][show_total_basePrice]',$field->show_total_basePrice).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_TOTAL_BASEPRICEWITHTAX'), $paramName.'['.$row.'][show_total_basePriceWithTax]',$field->show_total_basePriceWithTax).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_TOTAL_PRICEWITHOUTTAX'), $paramName.'['.$row.'][show_total_priceWithoutTax]',$field->show_total_priceWithoutTax).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_TOTAL_SALESPRICE'), $paramName.'['.$row.'][show_total_salesPrice]',$field->show_total_salesPrice).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_TOTAL_TAXAMOUNT'), $paramName.'['.$row.'][show_total_taxAmount]',$field->show_total_taxAmount).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_TOTAL_DISCOUNTAMOUNT'), $paramName.'['.$row.'][show_total_discountAmount]',$field->show_total_discountAmount).'
					</table>
					</fieldset>';
					
	//	if (isset($field->attached_array) && $field->attached_array == 1) {
			$handle_quantity = array(
				array("value" => "0", "text" => "Hide all"),
				array("value" => "1", "text" => "Show all"),
				array("value" => "2", "text" => "Show only input field"),
				array("value" => "3", "text" => "Show only notice")
			);
			
			
			$i=0;
			$enable_attached_products_array = (array) $field->enable_attached_products_array;
			$enable_title_for_attached_array = (array) $field->enable_title_for_attached_array;
			$title_for_attached_products_array = (array) $field->title_for_attached_products_array;
			$id_sku_for_attached_products_array = (array) $field->id_sku_for_attached_products_array;
			$attached_products_array = (array) $field->attached_products_array;
			$attached_products_layout_array = (array) $field->attached_products_layout_array;
			$attached_products_def_qty_array = (array) $field->attached_products_def_qty_array;
			$attached_products_show_qty_array = (array) $field->attached_products_show_qty_array;
			
			foreach($enable_attached_products_array as $blabla) {
					$html2 = '<tr class="cp-hide"><td class="key">'. JText::_('CATPRODUCT_SHOW_QTY_ATTACHED') .'</td><td>'
					.JHTML::_ ('select.genericlist', $handle_quantity, $paramName.'['.$row.'][attached_products_show_qty_array]['.$i.']', 'aaa', 'value', 'text', current($attached_products_show_qty_array)).'</td></tr>';
					
					$html .='<fieldset id="catproduct_attach_fieldset'.$i.'">
					<legend>'. JText::_('CATPRODUCT_FIELDSET5_TITLE') .'</legend>
					<table class="admintable">
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_ENABLE_ATTACHED_PRODUCTS'), $paramName.'['.$row.'][enable_attached_products_array]['.$i.']',$blabla).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_ENABLE_ATTACHED_TITLE'), $paramName.'['.$row.'][enable_title_for_attached_array]['.$i.']',current($enable_title_for_attached_array)).'
					'.VmHTML::row('input',JText::_('CATPRODUCT_TITLE_ATTACHED_PRODUCTS'), $paramName.'['.$row.'][title_for_attached_products_array]['.$i.']',current($title_for_attached_products_array)).'
					<tr><td class="key">'. JText::_('CATPRODUCT_CHOOSE_LAYOUT_ATTACHED') .'</td><td>'
					.JHTML::_ ('select.genericlist', $layout_g, $paramName.'['.$row.'][attached_products_layout_array]['.$i.']', 'aaa', 'value', 'text', current($attached_products_layout_array)).'</td></tr>
					'.VmHTML::row('input',JText::_('CATPRODUCT_DEF_QTY_ATTACHED'), $paramName.'['.$row.'][attached_products_def_qty_array]['.$i.']',current($attached_products_def_qty_array)).'
					'.$html2.'
					<tr><td class="key">'.JText::_('CATPRODUCT_ATTACHED_SEARCH').'</td><td>
					'.JHTML::_ ('select.genericlist', $field_attached, $paramName.'['.$row.'][id_sku_for_attached_products_array]['.$i.']', '', 'value', 'text', current($id_sku_for_attached_products_array)).'
					</td></tr>
					'.VmHTML::row('input',JText::_('CATPRODUCT_ATTACHED_PRODUCTS'), $paramName.'['.$row.'][attached_products_array]['.$i.']',current($attached_products_array)).'
					</table>
					<div style="float:right;" class="catproduct-button" onclick="catproduct_remove_attached(\''.$i.'\')" >'.JText::_('CATPRODUCT_ATTACHED_ARRAY_REMOVE').'</div>
					</fieldset>';
					
				next($enable_title_for_attached_array);
				next($title_for_attached_products_array);
				next($id_sku_for_attached_products_array);
				next($attached_products_array);
				next($attached_products_layout_array);
				next($attached_products_def_qty_array);
				next($attached_products_show_qty_array);
				$i++;
			}
	/*'attached_products_layout_array'=>array('','array'),
								'attached_products_def_qty_array'=>array('','array'),
								'attached_products_show_qty_array'=>array('','array'),*/
								
			$javascript = "<script type='text/javascript'>";
			$javascript .= "var attached_fieldset_text = '".JText::_('CATPRODUCT_FIELDSET5_TITLE')."';";
			$javascript .= "var attached_enable_text = '".JText::_('CATPRODUCT_ENABLE_ATTACHED_PRODUCTS')."';";
			$javascript .= "var attached_enable_title_text = '".JText::_('CATPRODUCT_ENABLE_ATTACHED_TITLE')."';";
			$javascript .= "var attached_title_text = '".JText::_('CATPRODUCT_TITLE_ATTACHED_PRODUCTS')."';";
			$javascript .= "var attached_layout_text = '".JText::_('CATPRODUCT_CHOOSE_LAYOUT_ATTACHED')."';";
			$javascript .= "var attached_def_qty_text = '".JText::_('CATPRODUCT_DEF_QTY_ATTACHED')."';";
			$javascript .= "var attached_show_qty_text = '".JText::_('CATPRODUCT_SHOW_QTY_ATTACHED')."';";
			$javascript .= "var attached_product_text = '".JText::_('CATPRODUCT_ATTACHED_PRODUCTS')."';";
			$javascript .= "var attached_finder_text = '".JText::_('CATPRODUCT_ATTACHED_SEARCH')."';";
			$javascript .= "var attached_remove_text = '".JText::_('CATPRODUCT_ATTACHED_ARRAY_REMOVE')."';";
			$javascript .= "var attached_count=".$i.";";
			$javascript .= "var catproduct_row=".$row.";";
			$javascript1 = "";
			foreach ( $layout_g as $layout_s) {
				$javascript1 .= "<option value=\"".$layout_s['value']."\">".$layout_s['text']."</option>";	
			}
			$javascript .= "var attached_fileselect = '".$javascript1."';";
			$javascript .= "</script>";
			
	/*	}
		else {
		$html .='	<fieldset>
					<legend>'. JText::_('CATPRODUCT_FIELDSET5_TITLE') .'</legend>
					<table class="admintable">
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_ENABLE_ATTACHED_PRODUCTS'), $paramName.'['.$row.'][enable_attached_products]',$field->enable_attached_products).'
					'.VmHTML::row('checkbox',JText::_('CATPRODUCT_ENABLE_ATTACHED_TITLE'), $paramName.'['.$row.'][enable_title_for_attached]',$field->enable_title_for_attached).'
					'.VmHTML::row('input',JText::_('CATPRODUCT_TITLE_ATTACHED_PRODUCTS'), $paramName.'['.$row.'][title_for_attached_products]',$field->title_for_attached_products).'
					<tr><td class="key">'.JText::_('CATPRODUCT_ATTACHED_SEARCH').'</td><td>
					'.JHTML::_ ('select.genericlist', $field_attached, $paramName.'['.$row.'][id_sku_for_attached_products]', '', 'value', 'text', $field->id_sku_for_attached_products).'
					</td></tr>
					'.VmHTML::row('input',JText::_('CATPRODUCT_ATTACHED_PRODUCTS'), $paramName.'['.$row.'][attached_products]',$field->attached_products).'
					</table>
					</fieldset>';
			
		}*/
		$html .='
			</fieldset>
			';
			
			
		$html .= $javascript;	
		if(!defined('VM_VERSION') or VM_VERSION < 3){
			$html .= '<div class="catproduct-button" onClick="catproduct_add_attached()">'.JText::_('CATPRODUCT_ATTACHED_ARRAY_ADD').'</div>';
		} else {
			$html .= '<div class="catproduct-button" onClick="catproduct_add_attachedVM3()">'.JText::_('CATPRODUCT_ATTACHED_ARRAY_ADD').'</div>';
		}
		
		//if (isset($field->attached_array) && $field->attached_array == 1) {
			
		//}
		$retValue .= $html;
		$row++;
		return true ;
	}

	function plgVmOnDisplayProductVariantFE($field,&$idx,&$group) {
    }
    function plgVmOnDisplayProductFE($product,&$idx,&$group) {
		// if not Catproduct return ''
		if ($group->custom_element != $this->_name) return '';
		// get parameters
		$parametri = json_decode($group->custom_param, true);
		$this->DisplayCatproduct($product,$group,$parametri);
		return true;
	
	}
	function plgVmOnDisplayProductFEVM3($product,&$group) {
		// if not Catproduct return ''
		if ($group->custom_element != $this->_name) return '';
		// get parameters
		$parametri = (array)($group);
		$this->DisplayCatproduct($product,$group,$parametri);
		return true;
	}
	
   function DisplayCatproduct($product,&$group,$parametri) {
		
		if (!class_exists ('CatproductFunction')) {
			require(JPATH_PLUGINS . DS . 'vmcustom' . DS . 'catproduct' . DS . 'catproduct' . DS . 'helpers' . DS . 'catproductfunctions.php');
		}
		$cat_helper = new CatproductFunction();
		// Get the global defaults, so we can pass them to and use them in tmpl/default.php
		$global_params = $cat_helper->getGlobalParameters($group->custom_params);
		
		// nekateri parametri iz vm - poglej, èe jih je smiselno uporabiti!
		$show_prices = VmConfig::get('show_prices', 1);
		// poglej, kje se da dobiti kolièino. Mislim, da je smiselno to uporabiti -> nastavi default kolièino. 
		// Kolièino se uporabi pri nalaganju artikla in nalaganju cen
		
		// if default parameters set, fix current one
		if ($global_params['use_default2'] == 1) {
			$parametri['hide_original_addtocart'] = $global_params['hide_original_addtocart'];
			$parametri['original_addtocart_css'] = $global_params['original_addtocart_css'];
			$parametri['orig_add_area'] = $global_params['orig_add_area'];
			$parametri['layout_field'] = $global_params['layout_field'];
			$parametri['sorting_field'] = $global_params['sorting_field'];
			$parametri['def_qty_parent'] = $global_params['def_qty_parent'];
			$parametri['def_qty_children'] = $global_params['def_qty_children'];	
		}
	
		// parameter for min max box quantity
		if (isset($parametri['use_max_min_quantity'])) $use_min_max = $parametri['use_max_min_quantity']; else $use_min_max = 0;
		if (isset($parametri['use_box_quantity'])) $use_box_quantity = $parametri['use_box_quantity']; else $use_box_quantity = 0;
		
		// get produc model
		$products = Array();
		$productModel = VmModel::getModel ('product');
		
		//var_dump($parametri['override']);
		//var_dump($product->product_parent_id);
		$original_product = $product;
		if (isset($product->product_parent_id) && $product->product_parent_id <> 0) {
			$product = $productModel->getProduct($product->product_parent_id, true);
		}
		// this prevent virtuemart overriding plugins for child products
		/*if (isset($parametri['override']) && $parametri['override'] == 1) {
			if (isset($product->product_parent_id) && $product->product_parent_id <> 0) {
				$product = $productModel->getProduct($product->product_parent_id, true);
			}
		}*/
		
		// get childrens
		$uncatChildren = $productModel->getProductChildIds($product->virtuemart_product_id);
		
		// hide addtocart button and customfields on category page or in module
		$document = JFactory::getDocument();
		if (isset($parametri["hide_original_addtocart"]) && ($parametri["hide_original_addtocart"] == "js" || $parametri["hide_original_addtocart"] == "css")) {
			$document->addScriptDeclaration('jQuery(document).ready(function() {
				jQuery("input[name^=virtuemart_product_id][value='.$product->virtuemart_product_id.']").parents(".addtocart-area").css("display","none");
				/*.addtocart-area
				jQuery("input[name^=virtuemart_product_id][value='.$product->virtuemart_product_id.']").parent().children(".product-fields").css("display","none");*/
			});');
		}

		// check if virtuemart_product_id is in url and if it's the same as the one in Catproduct, else return ''
		// this prevent Catproduct to be loaded in modules or category view because of some issues
		$shown_product_id = JRequest::getInt('virtuemart_product_id');
		if (!in_array($shown_product_id, $uncatChildren) && $shown_product_id != $product->virtuemart_product_id) return '';
		
		
		// hide original addtocart button if javascript is ussed for hiding		
		if (isset($parametri["hide_original_addtocart"]) && $parametri["hide_original_addtocart"] == "js") {
			if (isset($parametri["original_addtocart_css"]) && $parametri["original_addtocart_css"] <> '') {
				$document->addScriptDeclaration('jQuery(document).ready(function() {
					jQuery("input[name^=virtuemart_product_id][value='.$product->virtuemart_product_id.']").closest(".productdetails").find("'.$parametri["original_addtocart_css"].'").css("display","none");
				});');			
			} else {
				$document->addScriptDeclaration('jQuery(document).ready(function() {
					jQuery("input[name^=virtuemart_product_id][value='.$product->virtuemart_product_id.']").closest(".productdetails").find(".addtocart-area").css("display","none");
				});');
			}
		}
		
		// set counter
		$i = 0;
		$last_group = '';
		$groupid = 0;
		$parent_product = $this->getCatproductProduct($product->virtuemart_product_id, $product, $use_min_max, $use_box_quantity);
		
		$cat_helper->CP_addParent ($parent_product, $parametri, $i, $products);

		$cat_helper->CP_getChildren ($uncatChildren, $parametri, $product, $use_min_max, $use_box_quantity, $groupid, $i, $products);
		
		$cat_helper->CP_getAttached ($parametri, $product, $use_min_max, $use_box_quantity, $groupid, $i, $products, $original_product);

		// if dynamic price update is enabled
		if (isset($parametri["update_prices"]) && $parametri["update_prices"] == '1') {
			$updateprice = 1;
		}
		else {
			$updateprice = 0;
		}
		
		//add some JS
		//$vendorM = VmModel::getModel('currency');
		//	$style = $vendorM->getData((int)self::$_instance->_currency_id);
			
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();
		$price_format2 = $cat_helper->getCurrencyCP();
		if (isset($price_format2->currency_positive_style)) $price_format = $price_format2->currency_positive_style; else $price_format = "{number} {symbol}";
		if (isset($price_format2->currency_decimal_place)) $decimal_places = $price_format2->currency_decimal_place; else $decimal_places = 2;
		if (isset($price_format2->currency_decimal_symbol)) $decimal_symbol = $price_format2->currency_decimal_symbol; else {
			if(method_exists($currency, "getDecimalSymbol")) $decimal_symbol = $currency->getDecimalSymbol(); else $decimal_symbol = ","; }
		if (isset($price_format2->currency_thousands)) $thousand_symbol = $price_format2->currency_thousands; else {
			if(method_exists($currency, "getThousandsSeperator")) $thousand_symbol = $currency->getThousandsSeperator(); else $thousand_symbol = "."; }

		//$price_format = $currency->getPositiveFormat();
		if (strpos($price_format,' ') !== false) {
			$price_format_space = ' ';
		}
		else {
			$price_format_space = '';
		}
		$price_format = preg_replace("/{/", "", $price_format);
		$price_format1 = explode("}", $price_format);
	
		if($price_format1[0] == "symbol") {
			$price_format = '0';
		}
		else if ($price_format1[1] == "symbol") {
			$price_format = '1';
		}
		else {
			$price_format = '1';
		}
		
		$addparentfromoriginal = '0';
		if (isset($parametri["add_parent_from_original"]) && $parametri["add_parent_from_original"] <> '') {
			$addparentfromoriginal = $parametri["add_parent_from_original"];
		}
		$orig_add_area = '.productdetails-view .addtocart-area';
		if (isset($parametri["orig_add_area"]) && $parametri["orig_add_area"] <> '') {
			$orig_add_area = $parametri["orig_add_area"];
		}
		
		$stockhandle = VmConfig::get('stockhandle','none');
		$check_stock = 0;
		if ($stockhandle == 'disableadd' || $stockhandle == 'disableit_children' || $stockhandle == 'disableit') { $check_stock = 1; }
		
		$document->addScriptDeclaration('
		// set this if it doesnt come from vm! 
		if (typeof vmCartText == "undefined") vmCartText = "'. JText::_('CATPRODUCT_WAS_ADDED_TO_YOUR_CART') .'" ;
		
		jQuery(document).ready(function() {
			if (typeof emptyQuantity == "function") { 
				emptyQuantity();
			}
			jQuery(".cell_customfields input").click(function () {
				row = jQuery(this).parents(".row_article").attr("id");
				row = row.replace("row_article_","");
				getPrice(row);
			});
			jQuery(".cell_customfields select").change(function () {
				row = jQuery(this).parents(".row_article").attr("id");
				row = row.replace("row_article_","");
				getPrice(row);
			});
			jQuery(".cell_parent_customfields input").click(function () {
				jQuery(".row_article").each(function() { getPrice(jQuery(this).attr("id").replace("row_article_","")); });
			});
			jQuery(".cell_parent_customfields select").change(function () {
				jQuery(".row_article").each(function() { getPrice(jQuery(this).attr("id").replace("row_article_","")); });
			});
			/* this is to remove chosen selects from VM */
			jQuery(".vm-chzn-select").each(function(){ 
				jQuery(this).show().removeClass("chzn-done");
				jQuery(this).next().remove();
				return jQuery(this);
		    });
		});
		var symbol_position = '.$price_format.';
		var symbol_space = "'.$price_format_space.'";
		var thousandssep = "'.$thousand_symbol.'";
		var decimalsymbol = "'.$decimal_symbol.'";
		var decimalplaces = "'.$decimal_places.'";
		var updateprice = '.$updateprice.';
		var noproducterror = "'.JText::_('CATPRODUCT_NO_PRODUCT').'";
		var noquantityerror = "'.JText::_('CATPRODUCT_NO_QUANTITY').'";
		var addparentoriginal = '.$addparentfromoriginal.';
		var mainproductname = "'.str_replace('"',"''",$product->product_name).'"; 
		var originaladdtocartareclass = "'.$orig_add_area.'";
		var checkstock = "'.$check_stock.'";
		jQuery(document).ready(function() {
			if (addparentoriginal == 1) { 
				jQuery(".addtocart-bar").find(".quantity-input.js-recalculate").bind("propertychange keyup input paste", function(event) {
					FindMainProductPrice ();
				});
				jQuery(".addtocart-bar").find(".quantity-plus").click(function() {
					FindMainProductPrice ();
				});
				jQuery(".addtocart-bar").find(".quantity-minus").click(function() {
					FindMainProductPrice ();
				});
				
				FindMainProductPrice ();
			}
		
		jQuery("#catproduct_form a.ask-a-question").unbind();
		jQuery("#catproduct_form a.ask-a-question").click( function(){
				id = jQuery("#product_id_"+jQuery("#catproduct_form a.ask-a-question").parents(".row_article").attr("id").replace("row_article_","")).val();
				jQuery.fancybox({
				href: "/spletna-trgovina2/index.php?option=com_virtuemart&view=productdetails&task=askquestion&tmpl=component&virtuemart_product_id="+id,
				type: "iframe",
				height: "550"
			});
			return false ;
		});
		});
		');
		
		// hide original addtocart button if css is used for hidding
		if (isset($parametri["hide_original_addtocart"]) && $parametri["hide_original_addtocart"] == "css") {
			if (isset($parametri["original_addtocart_css"]) && $parametri["original_addtocart_css"] <> '') {
				$document->addStyleDeclaration($parametri["original_addtocart_css"].' {display:none;}');
			} else {
				$document->addStyleDeclaration('.addtocart-area {display:none;}');
			}
		}

		// get layout page
		if (isset($parametri["layout_field"])) {
			$layout = substr($parametri["layout_field"], 0, -4);
		}
		else {
			$layout = "default";
		}
		if($layout == '') $layout = 'default';

		// set everything back to normal
		$virtuemart_product_id = $productModel->setId ($product->virtuemart_product_id);
		$productModel->product = $original_product;
		$global_params["show_prices"] = $cat_helper->checkShowPrices();
		$group->display .=  $this->renderByLayout($layout,array($products,&$idx,&$group,$global_params, $product, $parent_product) );

		return true;
	}
	
	// show quantitybutton
	/* $cssclasses = Array ("span" => "quantity-box", 
							"input" => "quantity-input js-recalculate", 
							"spancontrols" => "quantity-controls js-recalculate",
							"plus" => "quantity-controls quantity-plus", 
							"minus" => "quantity-controls quantity-minus");		
		$styles = Array ("span" => "", 
						"input" => "", 
						"spancontrols" => "",
						"plus" => "", 
						"minus" => "");	
		$function:  0 - Hide all
					1 - Show all
					2 - Show only input field
					3 - Show only notice
	*/
	function showQuantityButtons ($function = 1, $def_qty = 0, $i = 0, $min_order_level = 0, $max_order_level = 0, $product_box = 0 , $cssclasses = '',  $styles = '') {
		if (!is_array($cssclasses)) $cssclasses = Array ("span" => "quantity-box", 
														"input" => "quantity-input js-recalculate", 
														"spancontrols" => "quantity-controls js-recalculate",
														"plus" => "quantity-controls quantity-plus", 
														"minus" => "quantity-controls quantity-minus");	
		if (!is_array($styles)) $styles = Array ("span" => "", 
												"input" => "", 
												"spancontrols" => "",
												"plus" => "", 
												"minus" => "");	
		/*<span class="quantity-box"><input class="quantity-input js-recalculate" size="2" id="quantity_'.$i.'" name="quantity[]" value="0" type="text" onblur=\'changeQuantity('.$i.', "input", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\'></span>
		<span class="quantity-controls js-recalculate"><input class="quantity-controls quantity-plus" onclick=\'changeQuantity('.$i.', "plus", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' type="button">
		<input class="quantity-controls quantity-minus" onclick=\'changeQuantity('.$i.', "minus", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' type="button"></span>
			*/
		switch ($function) {
			case 0:
				$html = '';
				break;
			case 1:
				$html = '';
				$html .= '<span class="'.$cssclasses["span"].' style="'.$styles["span"].'">';
				$html .= '<input size="2" id="quantity_'.$i.'" name="quantity[]" value="0" type="text" onblur=\'changeQuantity('.$i.', "input", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' class="'.$cssclasses["input"].'" style="'.$styles["input"].'">';
				$html .= '</span>';
				$html .= '<span class="'.$cssclasses["spancontrols"].'" style="'.$styles["spancontrols"].'">';
				$html .= '<input onclick=\'changeQuantity('.$i.', "plus", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' type="button" class="'.$cssclasses["plus"].'" style="'.$styles["plus"].'">';
				$html .= '<input onclick=\'changeQuantity('.$i.', "minus", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' type="button" class="'.$cssclasses["minus"].'" style="'.$styles["minus"].'">';
				$html .= '</span>';
				break;
			case 2:
				$html = '';
				$html .= '<span class="'.$cssclasses["span"].' style="'.$styles["span"].'">';
				$html .= '<input size="2" id="quantity_'.$i.'" name="quantity[]" value="0" type="text" onblur=\'changeQuantity('.$i.', "input", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' class="'.$cssclasses["input"].'" style="'.$styles["input"].'">';
				$html .= '</span>';
				break;
			case 3:
				$html = '';
				$html .= '<span class="'.$cssclasses["span"].' style="'.$styles["span"].'">';
				/*$html .= '<input id="quantity_'.$i.'" name="quantity[]" value="'.$def_qty.'" type="hidden" >';
				$html .= $def_qty;*/
				$html .= '<input size="2" id="quantity_'.$i.'" name="quantity[]" value="0" type="text" class="'.$cssclasses["input"].'" style="'.$styles["input"].'" disabled>';
				$html .= '</span>';
				break;
			default:
				$html = '';
				$html .= '<span class="'.$cssclasses["span"].' style="'.$styles["span"].'">';
				$html .= '<input size="2" id="quantity_'.$i.'" name="quantity[]" value="0" type="text" onblur=\'changeQuantity('.$i.', "input", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' class="'.$cssclasses["input"].'" style="'.$styles["input"].'">';
				$html .= '</span>';
				$html .= '<span class="'.$cssclasses["spancontrols"].'" style="'.$styles["spancontrols"].'">';
				$html .= '<input onclick=\'changeQuantity('.$i.', "plus", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' type="button" class="'.$cssclasses["plus"].'" style="'.$styles["plus"].'">';
				$html .= '<input onclick=\'changeQuantity('.$i.', "minus", '.$min_order_level.', '.$max_order_level.', '.$product_box .')\' type="button" class="'.$cssclasses["minus"].'" style="'.$styles["minus"].'">';
				$html .= '</span>';
		}
		return $html;
	}
	function getCustomfields ($artikel_cel) {
			$customfieldsModel = VmModel::getModel ('Customfields');
			$customfields = $customfieldsModel->getCustomEmbeddedProductCustomFields ($artikel_cel['allIds']);


		// odstrani catproduct iz customfield da se prepreèi loop
		if (!empty($customfields)) {
			foreach ($customfields as  $k => $custom) {
				if (isset($custom->virtuemart_custom_id)) {
					if ($custom->custom_element == "catproduct") {
						unset($customfields[$k]);
					}
				}
			}
		}
		// poklièi funkije za prikaz customfieldov 
	/*	if ($customfields){
			if (!class_exists ('vmCustomPlugin')) {
				require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
			}
			$customfieldsModel = VmModel::getModel ('Customfields');
			$customfieldsModel -> displayProductCustomfieldFE ($artikel_cel, $customfields);
		}*/
		
		// spremeni customfielde da ustrezajo vm
	    if (!empty($customfields)) {
			foreach ($customfields as $k => $custom) {
				if (!empty($custom->layout_pos)) {
					$customfieldsSorted[$custom->layout_pos][] = $custom;
					unset($customfields[$k]);
				}
			}
			$customfieldsSorted['normal'] = $customfields;
			unset($customfields);
        }
		return $customfieldsSorted;
	}
	
	function getCatproductProduct ($product_id = 0, $product, $use_min_max, $use_box_quantity) {
		if ($product_id == 0) return;
		
		$products = Array ();
		$productModel = VmModel::getModel ('product');
		$vendorModel = VmModel::getModel ('vendor');
		$mediaModel = VmModel::getModel('media');
		
		if (!class_exists ('CatproductFunction')) {
			require(JPATH_PLUGINS . DS . 'vmcustom' . DS . 'catproduct' . DS . 'catproduct' . DS . 'helpers' . DS . 'catproductfunctions.php');
		}
		$cat_helper = new CatproductFunction();
		
		$artikel_cel = $this->getProduct($product_id);
		//var_dump($artikel_cel);
		if (!$artikel_cel) return false;
		if ($artikel_cel->published == 0) return false;
		
		if(!defined('VM_VERSION') or VM_VERSION < 3){ } else {
		// dodaj podatke o customfields
		if (empty($artikel_cel->customfields)) {
			$customfieldsModel = VmModel::getModel ('Customfields');
			$artikel_cel->customfields = $customfieldsModel->getCustomEmbeddedProductCustomFields ($artikel_cel->allIds);
		}
		}

		// odstrani catproduct iz customfield da se prepreèi loop
		if (!empty($artikel_cel->customfields)) {
			foreach ($artikel_cel->customfields as  $k => $custom) {
				if (isset($custom->virtuemart_custom_id)) {
					if ($custom->custom_element == "catproduct") {
						unset($artikel_cel->customfields[$k]);
					}
				}
			}
		}
		// poklièi funkije za prikaz customfieldov 
		if (!empty($artikel_cel->customfields)) {
		if ($artikel_cel->customfields){
			if (!class_exists ('vmCustomPlugin')) {
				require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');
			}
			$customfieldsModel = VmModel::getModel ('Customfields');
			$customfieldsModel -> displayProductCustomfieldFE ($artikel_cel, $artikel_cel->customfields);
		}
		}
		
		//$products['customfields'] = (array) $artikel_cel->customfields;
		// spremeni customfielde da ustrezajo vm
	    if (!empty($artikel_cel->customfields)) {
			foreach ($artikel_cel->customfields as $k => $custom) {
				if (!empty($custom->layout_pos)) {
					$artikel_cel->customfieldsSorted[$custom->layout_pos][] = (array)$custom;
					unset($artikel_cel->customfields[$k]);
				}
			}
			$artikel_cel->customfieldsSorted['normal'] = $artikel_cel->customfields;
			unset($artikel_cel->customfields);
        }

		$prices = $productModel->getPrice ($artikel_cel, array(), 1);
		if (isset($artikel_cel->product_parent_id)) {
			$artikel_cel_parent = $this->getProductSingleCP($artikel_cel->product_parent_id);
			// fix customfields here
			/*foreach ($artikel_cel->customfieldsCart as $display) {
				$display->display = str_replace("custom_drop".$artikel_cel->product_parent_id, "custom_drop".$artikel_cel->virtuemart_product_id, $display->display);
				
				// for easycheckbox				
				$checkbox = json_decode(array_shift(array_values($display->options))->custom_param);
				if (isset($checkbox->custom_checkbox)) {
					$c_settings = explode(',', $checkbox->custom_checkbox);
					$i = 1;
					foreach ($c_settings as $c_settings111) {
						$display->display = str_replace("custom_checkbox".$i."_".$artikel_cel->product_parent_id, "custom_checkbox".$i."_".$artikel_cel->virtuemart_product_id, $display->display);
						$i++;
					}
				}
			}*/
		}
		//var_dump($artikel_cel->customfields);
		//print_r ($artikel_cel);
		// get product image
		$productModel->addImages($artikel_cel);
		if (isset($artikel_cel->virtuemart_media_id)) {
			$media_id = $artikel_cel->virtuemart_media_id;
		} else if (isset($artikel_cel_parent->virtuemart_media_id)) {
			$media_id = $artikel_cel_parent->virtuemart_media_id;
		} else if (isset($product->virtuemart_media_id)) {
			$media_id = $product->virtuemart_media_id;
		}
			else {
		$media_id = 0;
		}
		if($media_id <> 0) {
			$products['images'] = $mediaModel->createMediaByIds($media_id,'','',0);
		}
		$askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $artikel_cel->virtuemart_product_id . '&virtuemart_category_id=' . $artikel_cel->virtuemart_category_id . '&tmpl=component', FALSE);
		$artikel_cel->askquestion_url = $askquestion_url;

		$products['qparam'] = $cat_helper->max_min_box_quantity((array) $artikel_cel,$use_min_max,$use_box_quantity);
		$products['vendor'] = $vendorModel->getVendor ($product_id);
		$products['child'] = (array) $artikel_cel;
		$products['prices'] = $prices;
	
		return $products;
	}
/*	function plgVmOnViewCartModule( $product,$row,&$html) {
		return $this->plgVmOnViewCart($product,$row,$html);
    }

	function plgVmOnViewCart($product,$row,&$html) {
		return true;
    }

	function plgVmDisplayInOrderBE($item, $row, &$html) {
    }

	function plgVmDisplayInOrderFE($item, $row, &$html) {
    }

	public function plgVmOnStoreInstallPluginTable($psType) {
	}*/

	function plgVmDeclarePluginParamsCustomVM3(&$data){
		return $this->declarePluginParams('custom', $data);
	}
	
	function plgVmDeclarePluginParamsCustom($psType,$name,$id, &$data){
		return $this->declarePluginParams('custom', $name, $id, $data);
	}

	function plgVmGetTablePluginParams($psType, $name, $id, &$xParams, &$varsToPush){
		return $this->getTablePluginParams($psType, $name, $id, $xParams, $varsToPush);
	}
	
	function plgVmSetOnTablePluginParamsCustom($name, $id, &$table){
		return $this->setOnTablePluginParams($name, $id, $table);
	}

	function plgVmOnDisplayEdit($virtuemart_custom_id,&$customPlugin){
		return $this->onDisplayEditBECustom($virtuemart_custom_id,$customPlugin);
	}

/*	public function plgVmCalculateCustomVariant($product, &$productCustomsPrice,$selected){
	}

	public function plgVmDisplayInOrderCustom(&$html,$item, $param,$productCustom, $row ,$view='FE'){
	}

	public function plgVmCreateOrderLinesCustom(&$html,$item,$productCustom, $row ){
	}*/
	function plgVmOnSelfCallFE($type,$name,&$render) {
		$render->html = '';
	}

	function getProductSingleCP ($virtuemart_product_id = NULL, $front = TRUE, $quantity = 1, $withParent=false,$virtuemart_shoppergroup_ids=0) {
		$productModel = VmModel::getModel ('product');
		$db = JFactory::getDBO();
		
		//$this->fillVoidProduct($front);
		if (!empty($virtuemart_product_id)) {
			$virtuemart_product_id = $productModel->setId ($virtuemart_product_id);
		}

		if($virtuemart_shoppergroup_ids===0){
			$usermodel = VmModel::getModel ('user');
			$currentVMuser = $usermodel->getCurrentUser ();
			if(!is_array($currentVMuser->shopper_groups)){
				$virtuemart_shoppergroup_ids = (array)$currentVMuser->shopper_groups;
			} else {
				$virtuemart_shoppergroup_ids = $currentVMuser->shopper_groups;
			}
		}
		
		$virtuemart_shoppergroup_idsString = 0;
		if(!empty($virtuemart_shoppergroup_ids) and is_array($virtuemart_shoppergroup_ids)){
			$virtuemart_shoppergroup_idsString = implode('',$virtuemart_shoppergroup_ids);
		} else if(!empty($virtuemart_shoppergroup_ids)){
			$virtuemart_shoppergroup_idsString = $virtuemart_shoppergroup_ids;
		}		
		
		$front = $front?TRUE:0;
		
		//		if(empty($this->_data)){
		if (!empty($productModel->_id)) {

			$joinIds = array('virtuemart_manufacturer_id' => '#__virtuemart_product_manufacturers', 'virtuemart_customfield_id' => '#__virtuemart_product_customfields');
			//$joinIds = array();
			
			$product = $productModel->getTable ('products');
		
			$product->load ($productModel->_id, 0, 0, $joinIds);
			//	print_r($product->virtuemart_product_id );
			$product->allIds = array();
			
			$xrefTable = $productModel->getTable ('product_medias');
			$product->virtuemart_media_id = $xrefTable->load ((int)$productModel->_id);

			// Load the shoppers the product is available to for Custom Shopper Visibility
			
			$shoppergroups = array();
			if ($productModel->_id > 0) {
				$q = 'SELECT `virtuemart_shoppergroup_id` FROM `#__virtuemart_product_shoppergroups` WHERE `virtuemart_product_id` = "' . (int)$productModel->_id . '"';
				$db->setQuery($q);
				$shoppergroups = $db->loadResultArray ();
			}

			$product->shoppergroups = $shoppergroups;; // $productModel->getProductShoppergroups ($productModel->_id);

			$usermodel = VmModel::getModel ('user');
			$currentVMuser = $usermodel->getCurrentUser ();
			if(!is_array($currentVMuser->shopper_groups)){
				$virtuemart_shoppergroup_ids = (array)$currentVMuser->shopper_groups;
			} else {
				$virtuemart_shoppergroup_ids = $currentVMuser->shopper_groups;
			}

			if (!empty($product->shoppergroups) and $front) {
				if (!class_exists ('VirtueMartModelUser')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
				}
				$commonShpgrps = array_intersect ($virtuemart_shoppergroup_ids, $product->shoppergroups);
				if (empty($commonShpgrps)) {
					vmdebug('getProductSingle creating void product, usergroup does not fit ',$product->shoppergroups);
					return false; //$productModel->fillVoidProduct ($front);
				}
			}
			if(!defined('VM_VERSION') or VM_VERSION < 3){ } else {
				$productModel->getRawProductPrices($product,$quantity,$virtuemart_shoppergroup_ids,$front,$withParent);
			}
			$productModel->_nullDate = $db->getNullDate();
			$jnow = JFactory::getDate();
			//$productModel->_now = $jnow->toMySQL();
			$q = 'SELECT * FROM `#__virtuemart_product_prices` WHERE `virtuemart_product_id` = "'.$productModel->_id.'" ';

			if($front){
				if(count($virtuemart_shoppergroup_ids)>0){
					$q .= ' AND (';
					$sqrpss = '';
					foreach($virtuemart_shoppergroup_ids as $sgrpId){
						$sqrpss .= ' `virtuemart_shoppergroup_id` ="'.$sgrpId.'" OR ';
					}
					$q .= substr($sqrpss,0,-4);
					$q .= ' OR `virtuemart_shoppergroup_id` IS NULL OR `virtuemart_shoppergroup_id`="0") ';
				}
				$quantity = (int)$quantity;
				$q .= ' AND ( (`product_price_publish_up` IS NULL OR `product_price_publish_up` = "' . $db->getEscaped($productModel->_nullDate) . '" OR `product_price_publish_up` <= "' .$db->getEscaped($productModel->_now) . '" )
		        AND (`product_price_publish_down` IS NULL OR `product_price_publish_down` = "' .$db->getEscaped($productModel->_nullDate) . '" OR product_price_publish_down >= "' . $db->getEscaped($productModel->_now) . '" ) )';
				$q .= ' AND( (`price_quantity_start` IS NULL OR `price_quantity_start`="0" OR `price_quantity_start` <= '.$quantity.') AND (`price_quantity_end` IS NULL OR `price_quantity_end`="0" OR `price_quantity_end` >= '.$quantity.') )';
			} else {
				$q .= ' ORDER BY `product_price` DESC';
			}

			$db->setQuery($q);
			$product->prices = $db->loadAssocList();
			$err = $db->getErrorMsg();
			if(!empty($err)){
				vmError('getProductSingle '.$err);
			} else {
				//vmdebug('getProductSingle getPrice query',$q);
				//vmdebug('getProductSingle ',$product->prices);
			}

			if(count($product->prices)===0){
				//vmdebug('my prices count 0');
				$prices = array(
					'virtuemart_product_price_id' => 0
					,'virtuemart_product_id' => 0
					,'virtuemart_shoppergroup_id' => null
					,'product_price'         => null
					,'override'             => null
					,'product_override_price' => null
					,'product_tax_id'       => null
					,'product_discount_id'  => null
					,'product_currency'     => null
					,'product_price_vdate'  => null
					,'product_price_edate'  => null
					,'price_quantity_start' => null
					,'price_quantity_end'   => null
				);
				$product = (object)array_merge ((array)$prices, (array)$product);

			} else
			if(count($product->prices)===1){
				//vmdebug('my prices count 1',$prices[0]);
				$product = (object)array_merge ((array)$product->prices[0], (array)$product);
			} else if ( $front and count($product->prices)>1 ) {
				foreach($product->prices as $price){

					if(empty($price['virtuemart_shoppergroup_id'])){
						if(empty($emptySpgrpPrice))$emptySpgrpPrice = $price;
					} else if(in_array($price['virtuemart_shoppergroup_id'],$virtuemart_shoppergroup_ids)){
						$spgrpPrice = $price;
						//$product = (object)array_merge ((array)$price, (array)$product);
						break;
					}
					//$product = (object)array_merge ((array)$price, (array)$product);
				}

				if(!empty($spgrpPrice)){
					$product = (object)array_merge ((array)$spgrpPrice, (array)$product);
				}
				else if(!empty($emptySpgrpPrice)){
					$product = (object)array_merge ((array)$emptySpgrpPrice, (array)$product);
				} else {
					vmWarn('COM_VIRTUEMART_PRICE_AMBIGUOUS');
					$product = (object)array_merge ((array)$product->prices[0], (array)$product);
				}

			}

			if(!isset($product->product_price)) $product->product_price = null;
			if(!isset($product->product_override_price)) $product->product_override_price = null;
			if(!isset($product->override)) $product->override = null;

			if (!empty($product->virtuemart_manufacturer_id)) {
				$mfTable = $productModel->getTable ('manufacturers');
				$mfTable->load ((int)$product->virtuemart_manufacturer_id);
				$product = (object)array_merge ((array)$mfTable, (array)$product);
			}
			else {
				$product->virtuemart_manufacturer_id = array();
				$product->mf_name = '';
				$product->mf_desc = '';
				$product->mf_url = '';
			}
			// - do tle
			$product->categoryItem = $productModel->getProductCategories ($productModel->_id, FALSE);
			// Load the categories the product is in
			//$product->categories = $this->getProductCategories ($this->_id, $front);
			$categories = array();
			if ($productModel->_id > 0) {
				$q = 'SELECT pc.`virtuemart_category_id` FROM `#__virtuemart_product_categories` as pc';
				if ($front) {
					$q .= ' LEFT JOIN `#__virtuemart_categories` as c ON c.`virtuemart_category_id` = pc.`virtuemart_category_id`';
				}
				$q .= ' WHERE pc.`virtuemart_product_id` = ' . (int)$virtuemart_product_id;
				if ($front) {
					$q .= ' AND `published`=1';
				}
				$db->setQuery ($q);
				$categories = $db->loadResultArray ();
			}

			$product->categories = $categories; //$this->getProductCategories ($productModel->_id, FALSE); //We need also the unpublished categories, else the calculation rules do not work
			
			if (!empty($product->categories) and is_array ($product->categories) and !empty($product->categories[0])) {
				$product->virtuemart_category_id = $product->categories[0];
				$q = 'SELECT `ordering`,`id` FROM `#__virtuemart_product_categories`
					WHERE `virtuemart_product_id` = "' . $productModel->_id . '" and `virtuemart_category_id`= "' . $product->virtuemart_category_id . '" ';
				$db->setQuery ($q);
				// change for faster ordering
				$ordering = $db->loadObject ();
				if (!empty($ordering)) {
					$product->ordering = $ordering->ordering;
					//What is this? notice by Max Milbers
					$product->id = $ordering->id;
				}

			}
			if (empty($product->virtuemart_category_id)) {

				if (isset($product->categories[0])) {
					$product->virtuemart_category_id = $product->categories[0];
				}
				else {
					$product->virtuemart_category_id = 0;
				}

			}

			if (!empty($product->categories[0])) {
				$virtuemart_category_id = 0;
				if ($front) {
					if (!class_exists ('shopFunctionsF')) {
						require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
					}
					$last_category_id = shopFunctionsF::getLastVisitedCategoryId ();
					if (in_array ($last_category_id, $product->categories)) {
						$virtuemart_category_id = $last_category_id;

					}
					else {
						$virtuemart_category_id = JRequest::getInt ('virtuemart_category_id', 0);
					}
				}
				if ($virtuemart_category_id == 0) {
					if (array_key_exists ('0', $product->categories)) {
						$virtuemart_category_id = $product->categories[0];
					}
				}

				$catTable = $productModel->getTable ('categories');
				$catTable->load ($virtuemart_category_id);
				$product->category_name = $catTable->category_name;
			}
			else {
				$product->category_name = '';
			}

			if (!$front) {
// 				if (!empty($product->virtuemart_customfield_id ) ){
				$customfields = VmModel::getModel ('Customfields');
				//$product->customfields = $customfields->getCustomEmbeddedProductCustomFields ($product->allIds);
				//var_dump($product->customfields);
				//$product->customfields = $customfields->getproductCustomslist ($productModel->_id);

				if (empty($product->customfields) and !empty($product->product_parent_id)) {
					//$product->customfields = $this->productCustomsfieldsClone($product->product_parent_id,true) ;
					$product->customfields = $customfields->getproductCustomslist ($product->product_parent_id, $productModel->_id);
					$product->customfields_fromParent = TRUE;
				}

			}
			else {

				// Add the product link  for canonical
				$productCategory = empty($product->categories[0]) ? '' : $product->categories[0];
				$product->canonical = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $productModel->_id . '&virtuemart_category_id=' . $productCategory;
				$product->link = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $productModel->_id . '&virtuemart_category_id=' . $productCategory);

				//only needed in FE productdetails, is now loaded in the view.html.php
				//				Load the neighbours
				//				$product->neighbours = $this->getNeighborProducts($product);

				// Fix the product packaging
				if ($product->product_packaging) {
					$product->packaging = $product->product_packaging & 0xFFFF;
					$product->box = ($product->product_packaging >> 16) & 0xFFFF;
				}
				else {
					$product->packaging = '';
					$product->box = '';
				}

				// Load the vendor details
				//				if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
				//				$product->vendor_name = VirtueMartModelVendor::getVendorName($product->virtuemart_vendor_id);

				// set the custom variants
				//vmdebug('getProductSingle id '.$product->virtuemart_product_id.' $product->virtuemart_customfield_id '.$product->virtuemart_customfield_id);
				
				// TLELE PAGLEJ TULE KA JE ZA NASLIDN VM!!!
				
				//if (!empty($product->virtuemart_customfield_id)) {

					$customfields = VmModel::getModel ('Customfields');
					
					// Load the custom product fields
					// THIS MUST BE COMMENT!!!!!
					//$product->customfields = $customfields->getProductCustomsField ($product);
					
					//$product->customfieldsRelatedCategories = $customfields->getProductCustomsFieldRelatedCategories ($product);
					//$product->customfieldsRelatedProducts = $customfields->getProductCustomsFieldRelatedProducts ($product);
					//  custom product fields for add to cart
					///$product->customfieldsCart = $customfields->getProductCustomsFieldCart ($product);
					//$child = $productModel->getProductChilds ($productModel->_id);
					//$product->customsChilds = $customfields->getProductCustomsChilds ($child, $productModel->_id);
				//}
			
// 				vmdebug('my product ',$product);

				// Check the stock level
				if (empty($product->product_in_stock)) {
					$product->product_in_stock = 0;
				}

				//TODO OpenGlobal add here the stock of parent, conditioned by $product->customfields type A
				/*				if (0 == $product->product_parent_id) {
					$q = 'SELECT SUM(IFNULL(children.`product_in_stock`,0)) + p.`product_in_stock` FROM `#__virtuemart_products` p LEFT OUTER JOIN `#__virtuemart_products` children ON p.`virtuemart_product_id` = children.`product_parent_id`
						WHERE p.`virtuemart_product_id` = "'.$this->_id.'"';
					$this->_db->setQuery($q);
					// change for faster ordering
					$product->product_in_stock = $this->_db->loadResult();
				}*/
				// Get stock indicator
				//				$product->stock = $this->getStockIndicator($product);

			}

		}
		else {
			$product = new stdClass();
			return false;
			//return $productModel->fillVoidProduct ($front);
		}
		//		}

		$productModel->product = $product;
		return $product;
	}

function getProduct ($virtuemart_product_id = NULL, $front = TRUE, $withCalc = TRUE, $onlyPublished = TRUE, $quantity = 1,$customfields = TRUE,$virtuemart_shoppergroup_ids=0) {

		if (!isset($virtuemart_product_id)) {
				return FALSE;
		}
		/*if (isset($virtuemart_product_id)) {
			$virtuemart_product_id = $this->setId ($virtuemart_product_id);
		}*/
		
		if($virtuemart_shoppergroup_ids !=0 and is_array($virtuemart_shoppergroup_ids)){
			$virtuemart_shoppergroup_idsString = implode('',$virtuemart_shoppergroup_ids);
		} else {
			$virtuemart_shoppergroup_idsString = $virtuemart_shoppergroup_ids;
		}


		$front = $front?TRUE:0;
		$withCalc = $withCalc?TRUE:0;
		$onlyPublished = $onlyPublished?TRUE:0;
		$customfields = $customfields?TRUE:0;
		
		$productKey = $virtuemart_product_id.$front.$onlyPublished.$quantity.$virtuemart_shoppergroup_idsString.$withCalc.$customfields;
		
		static $_products = array();
		   // vmdebug('$productKey, not from cache : '.$productKey);
		if (array_key_exists ($productKey, $_products)) {
			//vmdebug('getProduct, take from cache : '.$productKey);
			return $_products[$productKey];
		} else if(!$customfields or !$withCalc){
			$productKeyTmp = $virtuemart_product_id.$front.$onlyPublished.$quantity.$virtuemart_shoppergroup_idsString.TRUE.TRUE.TRUE;
			if (array_key_exists ($productKeyTmp, $_products)) {
				//vmdebug('getProduct, take from cache full product '.$productKeyTmp);
				return $_products[$productKeyTmp];
			}
		}
			//$productModel = VmModel::getModel ('product');
			//$child = $productModel->getProductSingle ($virtuemart_product_id, $front,$quantity);
			$child = $this->getProductSingleCP ($virtuemart_product_id, $front,$quantity);
			if (!$child) {
				vmdebug('Catproduct child is not allowed for this shoppergroup');
				return FALSE;
			}
			if (!$child->published && $onlyPublished) {
				vmdebug('getProduct child is not published, returning zero');
				return FALSE;
			}
			//var_dump($child->virtuemart_vendor_id);
			if ($child->virtuemart_vendor_id == 0 && $child->product_name == "") {
				vmdebug('getProduct child does not exist, return false');
				//echo "12345";
				return FALSE;
			}
			if(!isset($child->orderable)){
				$child->orderable = TRUE;
			}
			//store the original parent id
			$pId = $child->virtuemart_product_id;
			$ppId = $child->product_parent_id;
			$published = $child->published;
			if(!empty($pId)) $child->allIds[] = $pId;
			
			//$this->product_parent_id = $child->product_parent_id;

			$i = 0;

			//Check for all attributes to inherited by parent products
			while (!empty($child->product_parent_id)) {
				if(!empty($child->product_parent_id)) $child->allIds[] = $child->product_parent_id;
				$parentProduct = $this->getProductSingleCP ($child->product_parent_id, $front,$quantity);
				if ($child->product_parent_id === $parentProduct->product_parent_id) {
					vmError('Error, parent product with virtuemart_product_id = '.$parentProduct->virtuemart_product_id.' has same parent id like the child with virtuemart_product_id '.$child->virtuemart_product_id);
					break;
				}
				$attribs = get_object_vars ($parentProduct);

				foreach ($attribs as $k=> $v) {
					if ('product_in_stock' != $k and 'product_ordered' != $k) {// Do not copy parent stock into child
						if (strpos ($k, '_') !== 0 and empty($child->$k)) {
							$child->$k = $v;
// 							vmdebug($child->product_parent_id.' $child->$k',$child->$k);
						}
					}
				}
				$i++;
				if ($child->product_parent_id != $parentProduct->product_parent_id) {
					$child->product_parent_id = $parentProduct->product_parent_id;
				}
				else {
					$child->product_parent_id = 0;
				}

			}

			//vmdebug('getProduct Time: '.$runtime);
			$child->published = $published;
			$child->virtuemart_product_id = $pId;
			$child->product_parent_id = $ppId;

			/*if ($withCalc) {
				$child->prices = $this->getPrice ($child, array(), 1);
				//vmdebug(' use of $child->prices = $this->getPrice($child,array(),1)');
			}*/

			/*if (empty($child->product_template)) {
				$child->product_template = VmConfig::get ('producttemplate');
			}*/

		if(!empty($child->canonCatId) ) {
			// Add the product link  for canonical
			$child->canonical = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id . '&virtuemart_category_id=' . $child->canonCatId;
		} else {
			$child->canonical = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id;
		}
		//$child->canonical = JRoute::_ ($child->canonical,FALSE);
		if(!empty($child->virtuemart_category_id)) {
			//$child->link = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id . '&virtuemart_category_id=' . $child->virtuemart_category_id, FALSE);
			$child->link = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id . '&virtuemart_category_id=' . $child->virtuemart_category_id;
		} else {
			$child->link = $child->canonical;
		}
			
			$child->quantity = $quantity;
			
$child->addToCartButton = false;
		if(empty($child->categories)) $child->categories = array();
		$stockhandle = VmConfig::get('stockhandle', 'none');
		//vmdebug(' $stockhandle '.$stockhandle.' '.$child->slug,$child->product_in_stock,$child->product_ordered);
		$app = JFactory::getApplication ();
		if ($app->isSite () and $stockhandle == 'disableit' and ($child->product_in_stock - $child->product_ordered) <= 0) {
			vmdebug ('STOCK 0', VmConfig::get ('use_as_catalog', 0), VmConfig::get ('stockhandle', 'none'), $child->product_in_stock);
			//$_products[$productKey] = false;
			return false;
		} else {


			$product_available_date = substr($child->product_available_date,0,10);
			$current_date = date("Y-m-d");
			if (($child->product_in_stock - $child->product_ordered) < 1) {
				if ($product_available_date != '0000-00-00' and $current_date < $product_available_date) {
					$child->availability = vmText::_('COM_VIRTUEMART_PRODUCT_AVAILABLE_DATE') .': '. JHtml::_('date', $child->product_available_date, vmText::_('DATE_FORMAT_LC4'));
				} else if ($stockhandle == 'risetime' and VmConfig::get('rised_availability') and empty($child->product_availability)) {
					$child->availability =  (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability'))) ? JHtml::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability', '7d.gif'), VmConfig::get('rised_availability', '7d.gif'), array('class' => 'availability')) : vmText::_(VmConfig::get('rised_availability'));

				} else if (!empty($child->product_availability)) {
					$child->availability = (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . $child->product_availability)) ? JHtml::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . $child->product_availability, $child->product_availability, array('class' => 'availability')) : vmText::_($child->product_availability);
				}
			}
			else if ($product_available_date != '0000-00-00' and $current_date < $product_available_date) {

				$child->availability = vmText::_('COM_VIRTUEMART_PRODUCT_AVAILABLE_DATE') .': '. JHtml::_('date', $child->product_available_date, vmText::_('DATE_FORMAT_LC4'));

			}
		}
		

		return $child;
}

}