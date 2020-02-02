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
$user = JFactory::getUser();

$app = JFactory::getApplication();
$context = 'com_vdata.display.list.';
$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
$filter_type = $app->getUserStateFromRequest( $context.'filter_type', 'filter_type', -1, 'int' );
jimport( 'joomla.methods' ); ?>

<?php 

$refParameter = JFactory::getApplication()->input->getArray(array());
//print_r($refParameter);jexit();
$displayid = JFactory::getApplication()->input->get('displayid');
$replacekey=array();
//$formActionUrl=JRoute::_('index.php?option=com_vdata&view=display&layout=items&displayid='.$displayid);
?>
<script>
function goBack() {
    window.history.back();
}
$hd(document).ready(function () {
$hd('select').chosen({"disable_search_threshold":0,"search_contains": true, "allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});  
});
</script>
<?php 
if(!empty($this->itemtemplate))
	{
		echo '<div class="profileTitle"><h2>'.$this->itemtemplate->title.'</h2></div>';
	}
?>
<form action="<?php echo JURI::current(); ?>" method="post" name="adminForm" id="adminForm">
<?php
	if(empty($this->itemslist))
	{
		 ?>
		<div class="notFoudRecord"><?php echo JText::_('RECORD_NOT_FOUND'); ?></div>
		<?php 
	}
	else
	{
		$arrKey=array();
		$templaterp=$this->itemtemplate->itemlisttmpl;
		foreach( $this->itemslist[0] as $k=>$v ){
		$replacekey[]= "{".$k."}";  }
		if ((strpos($templaterp, '@vdata-') !== FALSE))
		{
		    $twoProfile="@vdata-";
			$position = strpos($templaterp, $twoProfile); 
			$findValue=substr($templaterp, $position+strlen($twoProfile));
			$findValArray=explode('"',$findValue);
			$FinalValues=explode('-',$findValArray[0]);
			$relationKey=$FinalValues[1];
			$arrKey[]=$relationKey;
			$replacekey[]="@vdata-".$FinalValues[0]."-".$FinalValues[1];
		}
		    $replacekey[]="@vdata";
		  ?>
		<!--<div class="backBtn">  <button onclick="goBack()">< ?php echo JText::_('GO_BACK');?></button></div>--->
		<div class="vdata_column vdata_column_<?php echo $this->itemtemplate->norowitem;?>">
<?php		  
		//print_r($arrKey);
		for($i=0;$i<count($this->itemslist);$i++){
			$uniqueKey=$this->itemtemplate->uniquekey;
			$row=$this->itemslist[$i];
			$replacevalue=(array)$this->itemslist[$i];
			if ((strpos($templaterp, '@vdata-') !== FALSE))
			{			
				$builtUrl=$this->twoProfiles;					
				$towProfileUrl=JRoute::_($builtUrl['urls'].$row->$uniqueKey);
				array_push($replacevalue,$towProfileUrl);
			}
			$url=JRoute::_('index.php?option=com_vdata&view=display&layout=item&displayid='.$displayid.'&id='.$row->$uniqueKey);
			array_push($replacevalue,$url);
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
			echo str_replace(array_values($replacekey), array_values($replacevalue), $templaterp); ?>
			
			<?php
		}
 echo '</div>';		
	}
?>

<?php echo $this->pagination->getListFooter(); ?>
<?php echo $this->pagination->getLimitBox(); ?>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="display" />
<input type="hidden" name="filter_order" value="<?php //echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php //echo $this->lists['order_Dir']; ?>" /> 
</form>