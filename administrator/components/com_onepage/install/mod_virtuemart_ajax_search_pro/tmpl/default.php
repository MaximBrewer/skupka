<?php
/**
* @package mod_vm_ajax_search
*
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* VM Live Product Search is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
/**
*  Modified by RuposTel.com 25.6.2011
*  and renamed to mod_vm_ajax_search
*/
/**
 * VM Live Product Search
 *
 * Used to process Ajax searches on a Virtuemart 1.1.2 Products.
 * Based on the excellent mod_pixsearch live search module designed by Henrik Hussfelt (henrik@pixpro.net - http://pixpro.net)
 * @author		John Connolly <webmaster@GJCWebdesign.com>
 * @package		mod_vm_live_product
 * @since		1.5
 * @version     0.5
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
/* Load the virtuemart main parse code */

/*
$style = '
 #vm_ajax_search_results2'.$myid.' {margin-left:'.$params->get('offset_left_search_result').'px;margin-top:'.$params->get('offset_top_search_result').'px;}
';


$document->addStyleDeclaration($style); 		
*/
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'modules/mod_virtuemart_ajax_search_pro/css/mod_vm_ajax_search.css'); 
$css = ''; 
$max_h = $params->get('results_max_height'); 
if (!empty($max_h))
{
$css .= '
 div.res_a_s {
  max-height: '.$params->get('results_max_height').';  
 }	 

'; 
}

if (!empty($min_height)) {
$css .= '
 div.vmlpsearch'.$params->get('moduleclass_sfx', '').' {
  max-height: '.$min_height.'px;  
 }	 

'; 
}

if (empty($tw)) $tw = 272; 
$widthT = $params->get('text_width', $tw); 

if ((!empty($widthT)) && ((stripos($widthT, 'px')===false) && ((stripos($widthT, '%')===false)))) $widthT .= 'px'; 

if (!empty($widthT))
{
	$css .= '
	input#vm_ajax_search_search_str2'.$myid.' {
	   width: '.$widthT.';
	   max-width: '.$widthT.';
	}
	';
}


$results_width = $params->get('results_width', '200px'); 
if ((!empty($results_width)) && ((stripos($results_width, 'px')===false) && ((stripos($results_width, '%')===false)))) $results_width .= 'px'; 
{
	
	$css .= '
	div#vm_ajax_search_results2'.$myid.' {
	   position: '.$params->get('css_position', 'absolute').';
	   z-index: 999; '; 
	   if (!empty($results_width)) {
		   $css .= ' width: '.$results_width.';'; 
	   }
	   $css .= '
		   
	}
	';
}



$document->addStyleDeclaration($css);
$min_height = $params->get('min_height');
?>
<div class="ekon_search" >
<div name="pp_search<?php echo $myid ?>" id="pp_search2_<?php echo $myid ?>" action="<?php echo JRoute::_('index.php'); ?>" method="get">
<div class="vmlpsearch<?php echo $params->get('moduleclass_sfx'); ?>" >
<div id="results_re_2<?php echo $myid ?>">
</div>
	<div class="vm_ajax_search_pretext"><?php echo $params->get('pretext'); ?></div>
	<?php
		
		
		$search = addslashes($search);
		$include_but = $params->get('include_but', false);
		$tw = $params->get('text_box_width'); 
		$opt_search = $params->get('optional_search');
		
		$no_ajax = $params->get('no_ajax', false); 
		
	?>
	 <div class="aj_label_wrapper ajax_search_pro sourcecoast" <?php 
	   if (!empty($cat_s)) { echo ' style="min-width: 450px;" '; }
	 ?>>
	 <form id="rup_search_form<?php echo (int)$myid; ?>" action="<?php echo $action_url; ?>" name="rup_search_form">
	 
	 <?php 
	 /*
	 if (!empty($vm_cat_id)) { 
	 ?>
	 <input type="hidden" name="vm_cat_id" value="<?php echo (int)$vm_cat_id; ?>" />
	 <?php
	 } 
	 */
	 
	
	 
	 ?>
	 <input type="hidden" value="com_rupsearch" name="option" />
	 <input type="hidden" value="search" name="view" />
	 <input type="hidden" value="1" name="nosef" />
	 <input type="hidden" value="<?php echo $clang; ?>" name="lang" />
	 <input type="hidden" value="<?php echo JFactory::getLanguage()->getTag(); ?>" name="language" />
	 <input type="hidden" value="<?php echo $params->get('order_by'); ?>" name="order_by" />
	 <?php 
	 if (!empty($my_itemid)) { ?>
	 <input type="hidden" value="<?php echo $my_itemid; ?>" name="Itemid" />
	 <?php } 
	 $search_ic = $params->get('search_input_class', ''); 
	 ?>
	 <div class="input-prepend">  
      <i class="fa fa-search searchicon ajaxsearch" aria-hidden="true"></i>
	  <input placeholder="<?php echo $search;  ?>" class="inputbox_vm_ajax_search_search_str2 span2 inactive_search <?php echo $search_ic; ?>" id="vm_ajax_search_search_str2<?php echo $myid ?>" name="keyword" type="search" value="<?php 
	  $keyword = $keyword = JRequest::getVar('keyword'); 
	  if (!empty($keyword)) echo str_replace('"', '\"', $keyword); 
	 
	 ?>" autocomplete="off" <?php if (empty($no_ajax)) { ?> onblur="javascript: return search_setText('', this, '<?php echo $myid ?>');" onfocus="javascript: aj_inputclear(this, '<?php echo $params->get('number_of_products'); ?>', '<?php echo $clang; ?>', '<?php echo $myid; ?>', '<?php echo $url ?>');" onkeyup="javascript:search_vm_ajax_live(this, '<?php echo $params->get('number_of_products'); ?>', '<?php echo $clang; ?>', '<?php echo $myid; ?>', '<?php echo $url ?>', '<?php echo $params->get('order_by'); ?>'); "   <?php } ?>/>
	 <i class="fa fa-circle-o-notch fa-spin spinner ajaxsearch" data-rel="vm_ajax_search_search_str2<?php echo $myid ?>"></i>
	 <i class="fa fa-times clearable ajaxsearch" data-rel="vm_ajax_search_search_str2<?php echo $myid ?>"></i>
	 
	 <div class="search-button"><span class="icon-search"></span></div>
	</div>
	 <?php 
	 $only_current = $params->get('only_current', false); 
	 if (($only_current) && (!empty($cat_s))) {
	    ?><input type="hidden" name="vm_cat_id" value="<?php echo (int)$vm_cat_id; ?>" id="vm_cat_id<?php echo $myid ?>" />
		
		<input type="hidden" name="only_current" value="1" />
		
		<input type="hidden" name="virtuemart_category_id" value="<?php echo (int)$vm_cat_id; ?>" />
		<?php
	 }
	 else
	  if (!empty($cat_s)) { 
	     ?>
		 <select name="vm_cat_id" id="vm_cat_id<?php echo $myid ?>" class="category_search_selector" onchange="return submitSearch(this, <?php echo $myid ?>, 'vm_ajax_search_search_str2<?php echo $myid ?>');">
		  <option value=""><?php echo JText::_('MOD_VIRTUEMART_AJAX_SEARCH_PRO_SEARCH_IN_WHOLE_SHOP'); ?></option>
		  <?php if (!empty($vm_cat_id)) { ?>
		  <option selected="selected" value="<?php echo (int)$vm_cat_id; ?>"><?php echo $search_dropdown; ?></option>
		  <?php } ?>
		  <option value=""><?php echo JText::_('MOD_VIRTUEMART_AJAX_SEARCH_PRO_SEARCH_IN_SEPARATOR'); ?></option>
		  <?php foreach ($top_cats as $k=>$row) { ?>
		    <option value="<?php echo (int)$row['virtuemart_category_id']; ?>"><?php echo $row['category_name']; ?></option>
		  <?php } ?>
		 
		 </select>
		 <?php
	  }
	 ?> 
	 <div class="aj_search_radio" >
	 <?php switch ($opt_search) {  
		case 0: ?>
		<input type="checkbox" id="optional_search<?php echo (int)$myid; ?>" name="opt_search" value="1" title="<?php echo JText::_('COM_VIRTUEMART_SEARCH_TITLE_DESC');?>"/>
	<?php
			break;
		case 1:
			echo '<input type="hidden" id="optional_search'.$myid.'" name="opt_search" value="0" />';
			break;
		case 2: 
			echo '<input type="hidden" id="optional_search'.$myid.'" name="opt_search" value="1" />';
			break;
		 } ?>
	
		 
	 </div>
	    
	 
	 
		
		<input type="hidden" name="module_id" value="<?php echo (int)$module->id; ?>" />
		
		<input type="hidden" name="view" value="search" />
		<input type="hidden" name="limitstart" value="0" />
		
	
	<?php 
	$bclass = $params->get('button_class', ''); 
	if (!empty($include_but)) 
	{
	 //$st = ' style="display: block; "';
	 
	 $st = ''; 
	 echo '<button class="btn btn-primary button_ajax_search_old '.$bclass.'" name="Search" '.$st.'>'.$search.'</button>';
	 ?>
	 
	 <?php
	}
	else
    {	
	$st = 'style="display: none;"';
	//$st = ''; 
	echo '<input class="btn btn-primary button_ajax_search_old '.$bclass.'" type="submit" value="" name="Search" '.$st.'/>';
	?><?php
	}
		
		
		
		
		?>
		<input type="hidden" name="view" value="search" />
		<input type="hidden" name="option" value="com_rupsearch" />
		
		
		
		</form>
		<div id="results_position_<?php echo (int)$myid; ?>" class="results_position_x">&nbsp;</div>	
	<?php $postt = $params->get('posttext'); 
	
	if (!empty($postt))
	{
	?>
	<div class="vm_ajax_search_posttext" style="clear: both;"><?php echo $postt; ?></div>
	<?php 
	
	}
	?>
    </div>
</div>
</div>
 


</div>

<?php
if (class_exists('vmJsApi'))
	if (method_exists('vmJsApi', 'jPrice'))
		vmJsApi::jPrice();