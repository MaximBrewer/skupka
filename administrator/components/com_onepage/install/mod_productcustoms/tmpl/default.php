<?php
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_productcustoms'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 
require(JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_productcustoms'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'getget.php'); 
$datacats = PCH::getFilterCats(); 

$filter_headers_array = array(); 
$direction = 'ltr'; 
$view = 'module'; 
if (empty($moduleclass_sfx)) $moduleclass_sfx = ''; 

$html = ''; 
foreach ($datacats as $catObj) {
	
	$id = $catObj->virtuemart_category_id;
	$val = $catObj->category_name;
	$checked = ''; 
	$ind = 'virtuemart_category_id'; 
			  if ((!empty($get[$ind])) && (($get[$ind] == $id) || ((is_array($get[$ind])) && (in_array($id, $get[$ind]))))) {
			    $checked = ' checked="checked" '; 
			  }
	$path = JModuleHelper::getLayoutPath('mod_productcustoms', 'default_link_category'); 
	ob_start(); 
	require($path); 
	$html .= ob_get_clean(); 
}

if (!empty($html)) {
			$key = JText::_('COM_VIRTUEMART_CATEGORIES'); 
			$filters_html_array[$key] = $html; 
			$expanded_state[$key] = 0; 
			$filter_headers_array[$key] = JText::_('COM_VIRTUEMART_CATEGORIES'); 
			}
if(!empty($filters_html_array))
{
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_productcustoms'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 
	
	$datas = PCH::collectCustomsFromGet(); 
	
	if (!empty($datas)) {
		$nh = ''; 
		foreach ($datas as $title=>$v) {
			$html = ''; 
		    foreach ($v as $id => $val) { 
			  $ind = 'virtuemart_custom_id'; 
			  $checked = ''; 
			  if ((!empty($get[$ind])) && (($get[$ind] == $id) || ((is_array($get[$ind])) && (in_array($id, $get[$ind]))))) {
			    $checked = ' checked="checked" '; 
			  }
			  $path = JModuleHelper::getLayoutPath('mod_productcustoms', 'default_link'); 
	ob_start(); 
	require($path); 
	$html .= ob_get_clean(); 
			
		   
			
			}
			
			if (!empty($html)) {
			$key = $title; 
			$filters_html_array[$key] = $html; 
			$expanded_state[$key] = 0; 
			$filter_headers_array[$key] = $title; 
			}
	}
	}
	
/*
 * view == module is used only when the module is loaded with ajax. 
 * We want only the form to be loaded with ajax requests. 
 * The cf_wrapp_all of the primary module, will be used as the container of the ajax response   
 */
	if($view!='module'){?>
	<div id="cf_wrapp_all_<?php echo $module->id ?>" class="cf_wrapp_all">
	<?php } 
?>
<div id="XXcf_ajax_loader_<?php echo $module->id?>"></div>
<form method="get" action="<?php echo PCH::getLink($get, array()); ?>" class="cf_form<?php echo $moduleclass_sfx;?>" id="cf_form_<?php echo $module->id?>">
	<div class="allwrap">
	
    <ul class="uk-tab " data-uk-tab="{connect:'#sys_tab_content_<?php echo $module->id; ?>'}">
	<?php 
	$first = true; 
	$active = ''; 
	foreach($filters_html_array as $key=>$flt_html){
	
	if(isset($filter_headers_array[$key])) {
		//toggle state
		if(isset($expanded_state[$key])){
			if($expanded_state[$key]==1) {
				$state='show';
			}
			else {
				$state='hide';
			}
		} 
		else 
		{
		 $state='show';
		}
		
		//$filters_render_array['scriptProcesses'][]="customFilters.createToggle('".$key."','$state');";
	}
	?>
	<li <?php if ($first) if ($state === 'show') { $active = $key; echo ' class="uk-active" '; $first = false; } ?>><a href="#"><?php echo $filter_headers_array[$key]?></a></li>
	<?php
	
	
	}
	
	?> 
	</ul>
	<ul id="sys_tab_content_<?php echo $module->id; ?>" class="uk-switcher uk-margin">
	<?php
	foreach($filters_html_array as $key=>$flt_html){?> 
	
	<li <?php 
	if ($active === $key) echo ' class="uk-active" '; ?> >
	
	
	
	<div class="cf_flt_wrapper  cf_flt_wrapper_id_<?php echo $module->id?> cf_flt_wrapper_<?php echo $direction; ?>" id="cf_flt_wrapper_<?php echo $key ?>_<?php echo $module->id; ?> " role="presentation">

		<div class="cf_wrapper_inner" id="cf_wrapper_inner_<?php echo $key?>_<?php echo $module->id; ?>" role="tabpanel">
			<?php echo $flt_html?>
		</div>
	</div>
	</li>
	<?php
	}
	?>
	</ul>
	<?php
	unset($flt_html);
	
	//reset all link
	if(!empty($resetUri)){?>
	<a class="cf_resetAll_link" rel="nofollow" data-module-id="<?php echo $module->id?>" href="<?php echo JRoute::_($resetUri)?>">
		<span class="cf_resetAll_label"><?php echo JText::_('MOD_CF_RESET_ALL')?></span>
	</a>
	<?php 
	}?>
					
		<?php 
		
		if(empty($filters_html_array['virtuemart_category_id_'.$module->id]) && !empty($filters_render_array['selected_flt']['virtuemart_category_id'])) {
			foreach($filters_render_array['selected_flt']['virtuemart_category_id'] as $key=>$id){?>
				<input type="hidden" name="virtuemart_category_id[<?php echo $key?>]" value="<?php echo $id?>"/>
			<?php 
			}
		}
		
		
		if(empty($filters_html_array['virtuemart_manufacturer_id_'.$module->id]) && !empty($filters_render_array['selected_flt']['virtuemart_manufacturer_id'])) {
			foreach($filters_render_array['selected_flt']['virtuemart_manufacturer_id'] as $key=>$id){?>
				<input type="hidden" name="virtuemart_manufacturer_id[<?php echo $key?>]" value="<?php echo $id?>"/>
			<?php 
			}
		}	
				
		
		//if the keyword search does not exist we have to add it as hidden, because it may added by the search mod
		 if(empty($filters_html_array['q_'.$module->id])) {
		 	$query=!empty($filters_render_array['selected_flt']['q'])?$filters_render_array['selected_flt']['q']:'';?>
		 	<input name="q" type="hidden" value="<?php echo $query;?>"/>
		 <?php 
		 }
		
		
				
		//in case of button add some extra vars to the form
		
		?>
		
	</div>
</form>
<?php 
if($view!='module'){?>
	</div>
	<?php }

	
	
	
	


} 

