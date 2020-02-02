<?php
/**
 * @version		$Id: default.php
 * @package		customfilters
 * @subpackage	mod_cf_search
 * @copyright	Copyright (C) 2014-2018 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC')or die;

$document=JFactory::getDocument();

$jinput=JFactory::getApplication()->input;
$component=$jinput->get('option','','cmd');
$view=$jinput->get('view','products','cmd');
$joomla_conf=JFactory::getConfig();
$joomla_sef=$joomla_conf->get('sef');
$results_loading_mode=$params->get('results_loading_mode','http');

/*
 * The scripts are usefull only with ajax enabled
 */
if($results_loading_mode=='ajax'){
	JHtml::_('behavior.framework', true);
	//load the VM scripts and styles in pages other than VM and CF when ajax is used
	if($component!='com_customfilters' || $component!='com_virtuemart' || ($component=='com_virtuemart' && $view!='category')){
		cftools::loadScriptsNstyles();
	}

	//no need to load more scripts - everything is there
	$document->addScript(JURI::base().'modules/mod_cf_filtering/assets/general.js');

	if($params->get('use_results_ajax_spinner','')){
		$spinnerstyle='background-image:url('.JURI::base().$params->get('use_results_ajax_spinner','').') !important;';
		$spinnerstyle.='background-repeat:no-repeat !important;';
		$document->addStyleDeclaration('#cf_res_ajax_loader{'.$spinnerstyle.'}');
		$ajax_results_spinner=1;
	}else $ajax_results_spinner=0;
	$loadOtherFilteringModules=$results_loading_mode=='ajax'?1:0;

	$script="
			if(typeof customFiltersProp=='undefined')customFiltersProp=new Array();
			customFiltersProp[".$module->id."]={
				base_url:'".JURI::base()."',
				cf_direction:'".$document->getDirection()."',
				loadModule:false,
				loadOtherFilteringModules:".$loadOtherFilteringModules.",
				results_trigger:'btn',
				results_wrapper:'bd_results',
				cfjoomla_sef:'".$joomla_sef."',
				use_ajax_spinner:'0',
				use_results_ajax_spinner:'".$ajax_results_spinner."',
				results_loading_mode:'".$results_loading_mode."'
				};";
	$script2="window.addEvent('domready',function(){customFilters.assignEvents(".$module->id.");});";

	$document->addScriptDeclaration($script);
	$document->addScriptDeclaration($script2);
}

$value=!empty($cfOutputs['q'])?$cfOutputs['q']:'';
$jconfig=JFactory::getConfig();
$issef=$jconfig->get('sef');
$suffix = $params->get('moduleclass_sfx');
$module_size=$params->get('module_size','');
$btn_class='';
$input_class='';
$size=20;

if(!empty($module_size)){
	$input_class='cf-searchmod-input-'.$module_size;
	$btn_class='btn-'.$module_size;
	if($module_size=='large')$size=50;
	else if ($module_size=='small')$size=15;
}
/*
 * In case of template overrides, please do not remove or change the ids from the elements
 * They are used by our scripts
 */
?>

<form class="cf-form-search" id="cf_form_<?php echo $module->id?>" action="<?php echo JRoute::_('index.php?option=com_customfilters&view=products&Itemid='.$itemId)?>" method="get">
	<div class="cf-searchmod-wrapper<?php echo $suffix; ?>" id="cf_wrapp_all_<?php echo $module->id?>">
		<span class="input-append">
			<input name="q" id="q_<?php echo $module->id?>_0" value="<?php echo $value?>" type="search" placeholder="<?php echo JText::_('MOD_CF_SEARCH_INPUT_PLACEHOLDER')?>" maxlength="100" size="<?php echo $size?>" id="cf-searchmod-input_<?php echo $module->id?>" class="cf-searchmod-input  <?php echo $input_class?>" />

			<button type="submit" id="q_<?php echo $module->id?>_button" class="btn btn-primary cf_apply_button <?php echo $btn_class?>" title="<?php echo JText::_('MOD_CF_SEARCH_SEARCH')?>"><?php echo JText::_('MOD_CF_SEARCH_SEARCH')?></button>
		</span>
        <div class="cf_message" id="q_<?php echo $module->id?>_message"></div>
		<?php
		if(!$issef && $results_loading_mode!='ajax'):?>
		<input name="option" type="hidden" value="com_customfilters" />
		<input name="view" type="hidden" value="products" />
		<?php if(!empty($itemId)):?>
		<input name="Itemid" type="hidden" value="<?php echo $itemId?>" />
		<?php
		endif;
		endif;
		?>
	</div>
</form>

