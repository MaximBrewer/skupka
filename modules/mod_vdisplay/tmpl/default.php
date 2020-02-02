<?php 
/*------------------------------------------------------------------------

# mod_vdisplay - 

# ------------------------------------------------------------------------

# author    Team WDMtech

# copyright Copyright (C) 2013 wwww.wdmtech.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.wdmtech.com

# Technical Support:  Forum - http://www.wdmtech..com/support-forum

-----------------------------------------------------------------------*/
// No direct access Itemid
defined('_JEXEC') or die; 
$document = JFactory::getDocument();
$document->addStylesheet(JURI::root().'modules/mod_vdisplay/css/style.css');
$display_menu_id= JFactory::getApplication()->input->get('displayid',0);
if(!empty($display_menu_id))
{
	$displayid=$display_menu_id;
}
else{
    $displayid= $params->get('displayid');
}
?>
<script>
function formRest()
{
	$hd('.radioCheck').val('');
	$hd(".textSearch").val('');
	$hd(".dropSearch").val('');
	$hd("#likeSearch").val('');
	$hd(".boxCheck").prop( "checked", false );
	var myarray = new Array();
	$hd(".boxCheck").each(function() {
		myarray.push($hd(this).attr("name"));
		var nameCheck = $hd(this).attr("name");
	});
	    var nameArray=Array.from(new Set(myarray));
	for(var i=0;i<nameArray.length;i++)
	{
	    $hd('#moduleForm').append('<input type="hidden" class="boxCheck" name="'+nameArray[i]+'" value="" >');
	}
	
}
</script>
<?php 
$mainframe = JFactory::getApplication();
$context = 'com_vdata.display.list.';
$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
$searchPhrase= $mainframe->getUserStateFromRequest( $context.'searchPhrase', 'searchPhrase',	'',	'array' );
$searchSubstr= $mainframe->getUserStateFromRequest( $context.'searchSubstr', 'searchSubstr',	'',	'array' );
$searchRadio= $mainframe->getUserStateFromRequest( $context.'searchRadio', 'searchRadio',	'',	'array' );
$searchCheckbox= $mainframe->getUserStateFromRequest( $context.'searchCheckbox', 'searchCheckbox',	'',	'array' );
//print_r($SubstringValue); jexit();
$searchDrop= $mainframe->getUserStateFromRequest( $context.'searchDrop', 'searchDrop',	'',	'array' );
$likeSearch= $mainframe->getUserStateFromRequest( $context.'likeSearch', 'likeSearch',	'',	'string' );
$likeSearchValue= $mainframe->getUserStateFromRequest( $context.'likeSearchValue', 'likeSearchValue',	'',	'string' );
$url=JRoute::_('index.php?option=com_vdata&view=display&layout=items&displayid='.$displayid);
?>
<form action="<?php echo $url; ?>" method="post" name="moduleForm" id="moduleForm">
<div class="vdata_display">
<?php 

if(!empty($dataResult)){
		  
	for($j=0;$j<count($morefilterkey);$j++)
	{
		if(in_array($morefilterkey[$j],$arrFieldTypeValue)){
		  $FieldsValues=$arrayField['value'][$morefilterkey[$j]];
		}
		else{
			$FieldsValues=$dataEmptyValues[$morefilterkey[$j]];
		}
		//$FieldsValues=$arrayField['value'][$morefilterkey[$j]];
		//print_r($dataEmptyValues[$morefilterkey[$j]]);
		if (strpos($morefilterkey[$j], '.') !== FALSE)
		{
			$arrayFilterKey=explode(".",$morefilterkey[$j]);
			
			$FieldTitle=explode("#__",$arrayFilterKey[0]);
			$filterFieldTitle=$arrayFilterKey[1];
			$moreFilterTitle=$FieldTitle[1].' '.$filterFieldTitle;
		}
		else
		{
		  $moreFilterTitle=$morefilterkey[$j];
		}
		?>
		<div class="addMoreField">
		<?php
	$fieldsTypes=$arryTypes[$j][0];
	switch($fieldsTypes)
	{
	    case 'typetext':
	    echo '<div class="typeTextContent"><label class="labelTitle">'.ucfirst($moreFilterTitle).'</label>'; 
		if($FieldsValues[0]=='substr'){?>
		
		<input type="text" class="textSearch" name="search<?php echo ucfirst($FieldsValues[0]);?>[<?php echo $morefilterkey[$j]; ?>][]" placeholder="<?php echo ucfirst($FieldsValues[0]).' String' ?>" value="<?php if(!empty($searchSubstr)){
		foreach($searchSubstr as $k=>$valStr){
	       if($searchSubstr[$k][0]!=''){
				if($k==$morefilterkey[$j])
		        {
			        echo $searchSubstr[$k][0];
				}
				else
				{
					echo "";
				}
	        }
	    }
		}
		?>" >
		<?php
		}
		if($FieldsValues[0]=='phrase'){?>
		<input type="text" class="textSearch" name="search<?php echo ucfirst($FieldsValues[0]);?>[<?php echo $morefilterkey[$j]; ?>][]" placeholder="<?php echo ucfirst($FieldsValues[0]).' String' ?>" 
        value="<?php if(!empty($searchPhrase)){
		foreach($searchPhrase as $k=>$valStr){
	       if($searchPhrase[$k][0]!=''){
				if($k==$morefilterkey[$j])
		        {
			        echo $searchPhrase[$k][0];
				}
				else
				{
					echo "";
				}
	        }
	    }
		}
		?>"		> 	
		<?php } echo '</div>';
		break;

		case 'typeradio':
		echo '<div class="typeRadioContent"><label class="labelTitle">'.ucfirst($moreFilterTitle).'</label>';
		foreach($FieldsValues as $value)
		{ if (strpos($value, '|') !== FALSE)
			{
				$Pipevalue=explode("|",$value);
				$value=$Pipevalue[0];
				$valueTitle=$Pipevalue[1];
			}
			else{
				$value=$value;
				$valueTitle=$value;
			}
		?>
		&nbsp;<input type="radio" class="radioCheck" name="searchRadio[<?php echo $morefilterkey[$j]; ?>]" value="<?php echo $value; ?>" <?php if(!empty($searchRadio)){
			foreach($searchRadio as $k=>$radioValue){
			if($k==$morefilterkey[$j])
		        {
			        if($value==$radioValue){ echo "checked='checked'"; }
				}
				else
				{
					echo "";
				}
		} } 
			?>>&nbsp;<?php echo $valueTitle; ?>		
		<?php }echo '</div>'; 
		break;

		case 'typecheck':
		echo '<div class="typeCheckContent"><label class="labelTitle">'.ucfirst($moreFilterTitle).'</label>'; 
		foreach($FieldsValues as $value)
		{ 
		if (strpos($value, '|') !== FALSE)
			{
				$Pipevalue=explode("|",$value);
				$value=$Pipevalue[0];
				$valueTitle=$Pipevalue[1];
			}
			else{
				$value=$value;
				$valueTitle=$value;
			}
		?>
		&nbsp;<input type="checkbox" class="boxCheck" name="searchCheckbox[<?php echo $morefilterkey[$j]; ?>][]"value="<?php echo $value; ?>" 
		<?php if(!empty($searchCheckbox)){
			foreach($searchCheckbox as $k=>$checkValue)
            {
				if($k==$morefilterkey[$j])
				{
					if(in_array($value,$checkValue)){
					echo "checked='checked'"; 
					}
				} } }
             ?>>&nbsp;
		<?php echo $valueTitle;	
		} echo '</div>';
		break;

		case 'typedrop':
		echo '<div class="typeDropContent"><label class="labelTitle">'.ucfirst($moreFilterTitle).'</label>';
		echo '<select name="searchDrop['.$morefilterkey[$j].']" class="dropSearch">';
		echo '<option value="">Select '.$moreFilterTitle.'</option>';
		foreach($FieldsValues as $value)
		{ ?>
		<option value="<?php echo $value; ?>"<?php 
		if(!empty($searchDrop)){
		foreach($searchDrop as $k=>$valStr){
		if($valStr!=''){
			if($valStr==$value){
		echo "selected='selected'"; } }
		} }
		?>><?php echo $value; ?></option>		
		<?php } 
		echo '</select></div>';
		break;
	}	echo '</div>';
	}
		if(!empty($allData->likesearch)){
		echo '<input type="text" name="likeSearch" id="likeSearch"placeholder="Keyword Search" value="'.$likeSearch.'">'; }
		?>
		 <?php echo JHTML::_( 'form.token' ); ?>

		<input type="hidden" value="<?php echo $allData->likesearch; ?>" name="likeSearchValue">
		<button type="submit" class="btn hasTip" title="<?php echo JText::_('SEARCH');?>"><?php echo JText::_('SEARCH');?></button>
		<button onclick="formRest();this.form.submit(); " class="btn hasTip" title="<?php echo JText::_('CLEAR');?>"><?php echo JText::_('CLEAR');?></button>
		<?php
 } ?>
 </div>
</form>