<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<script>
function goBack() {
    window.history.back();
}
</script>
<?php
$user = JFactory::getUser();
$app = JFactory::getApplication();
$context = 'com_vdata.display.list.';
$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
$filter_type = $app->getUserStateFromRequest( $context.'filter_type', 'filter_type', -1, 'int' );
jimport( 'joomla.methods' );
$replacekey=array();
?>
<div class="backBtn">  <button class="btn" onclick="goBack()"><?php echo JText::_('GO_BACK');?></button></div>
<?php
if(!empty($this->item[0])){
foreach( $this->item[0] as $k=>$v ){
$replacekey[]= "{".$k."}";  } ?>

<div class="itemDetail">
<?php
for($i=0;$i<count($this->item);$i++){
		$templaterp=$this->itemtemplate->itemdetailtmpl;
		$row=$this->item[$i];
		$replacevalue=(array)$this->item[$i];
		if(!empty($this->itemtemplate->profileid)){
		$fields=$this->paramsValue->fields;
		$defidValue=array();
		$totalItems=(array)$this->itemslist;
		foreach($fields as $key=>$profile){
		if($profile->data=="defined")	
		{
			$expo_fields='';
			if(!empty($profile->default)){
				JPluginHelper::importPlugin('vdata');
				$dispatcher = JEventDispatcher::getInstance();
				$defidValue[$key]=$dispatcher->trigger('getDefinedValue', array($profile->default,$fields,(array)$totalItems[$i],$expo_fields));
			}
		}
			
		}
		foreach($defidValue as $defKey=>$defineValue)
		{
			$replacevalue=array_replace($replacevalue,array($defKey => $defineValue[0]));
		}
		}
		echo str_replace(array_values($replacekey), array_values($replacevalue), $templaterp); 
}
}
else
{
	?>
	<div class="notFoudRecord"><?php echo JText::_('RECORD_NOT_FOUND'); ?></div>
<?php 
}
?>
</div>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="display" />
<input type="hidden" name="filter_order" value="<?php //echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php //echo $this->lists['order_Dir']; ?>" />