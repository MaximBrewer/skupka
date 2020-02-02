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
JHtml::_('behavior.tooltip');
$input = JFactory::getApplication()->input;
$user = JFactory::getUser();
$session = JFactory::getSession();
$document = JFactory::getDocument();
$document->addStylesheet(JURI::root().'media/com_vdata/css/select2.min.css');
$document->addScript(JURI::root().'media/com_vdata/js/select2.min.js');
?>
<script type="text/javascript">
	
	Joomla.submitbutton = function(task) {
		if (task == 'cancel') {
			Joomla.submitform(task, document.getElementById('adminForm'));	} 
	else {
			var form = document.adminForm;
			if(form.title.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_TITLE'); ?>");
				return false;
			}
			if(form.qry.value != "")	{
				  var str = form.qry.value; 
				  var strlower=str.toLowerCase();
				  var n = strlower.search("select");
				  if(n!=0){
						alert("<?php echo JText::_('PLZ_ENTER_ONLY_SELECT_QUERY'); ?>");
						return false;}
				
			}
			if(form.profileid.value ==0 && form.qry.value == "")	{
				alert("<?php echo JText::_('PLZ_ENTER_PROFILE'); ?>");
				return false;
			}
			if(form.uniquekey.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_UKEY'); ?>");
				return false;
			}
			if(form.uniquealias.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_UNID'); ?>");
				return false;
			}
			
			if(form.norowitem.value == "")	{
				alert("<?php echo JText::_('PLZ_SELECT_RITEM'); ?>");
				return false;
			}
			tinyMCE.execCommand('mceToggleEditor', true, 'listtmpl');                  
			tinyMCE.execCommand('mceToggleEditor', true, 'detailtmpl');
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
$hd(document).ready(function () {
	$hd('select.chosenSelect').chosen({"disable_search_threshold":0,"search_contains": true, "allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	
	$hd(".js-example-tags").select2({
		tags: true
	})
/************************************************************************************************
              AFTER SELECTED PROFILE ID
*************************************************************************************************/		
	$hd("#profileid").on("change",function(){
		var id=$hd("#profileid").val();
		var eid=$hd("#eid").val();
		if(id!=0){
		$hd.ajax({
			url:'index.php?option=com_vdata&view=display',
			type:'POST',
			dataType:'json',
			data:{'task':'Profilesfields',id:id,eid:eid},
			beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function(){
				$hd(".loading").hide();
			},
			success:function(res){
				
				if(res.result == 'success'){
					//alert(res.html);
					    $hd("#uniquekey").html(res.html).trigger('liszt:updated');
					    $hd("#uniquealias").html(res.html).trigger('liszt:updated');
						$hd("#likesearch").html(res.likeall).trigger('liszt:updated');
						$hd("#itemDetailField").html(res.likeall).trigger('liszt:updated');
					    $hd("#itemsListField").html(res.likeall).trigger('liszt:updated');
				
					if(res.uniqueKey!='' && res.uniqueAlias!='')
					{
							 				
						$hd('#uniquekey').find('option[value="'+ res.uniqueKey +'"]').attr('Selected', 'Selected').trigger('liszt:updated');
                        $hd('#uniquealias').find('option[value="'+ res.uniqueAlias +'"]').attr('Selected', 'Selected').trigger('liszt:updated');
						var values=res.likeSearch;
						if(values!=''){
						$hd.each(values, function(index, element)
						{
						  $hd('#likesearch').find('option[value="'+ element +'"]').attr('Selected', 'Selected').trigger('liszt:updated');
						});
						}
						//$hd('.input_fields_wrap').eq(1).html(res.tableTR);
						$hd('.input_fields_wrap tr:first').after(res.tableTR).trigger('liszt:updated');
						$hd('select.chosenSelect').chosen({"disable_search_threshold":0,"search_contains": true, "allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
						$hd(".js-example-tags").select2({
						tags: true

						})
					}
					else if(res.uniqueKey==''){
					$hd(".editHideTr").remove(); 
					
					}
					
				}
				
			},
			error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);				  
			}
		});
		}
	});
	/************************************************************************************************
              AFTER QRY SELECTED
*************************************************************************************************/	
	$hd("#qry").blur(function(){
		var qry=$hd("#qry").val();
		var eid=$hd("#eid").val();
		var profileid=$hd("#profileid").val();
		if(qry!='' && profileid==0){
		$hd.ajax({
			url:'index.php?option=com_vdata&view=display',
			type:'POST',
			dataType:'json',
			data:{'task':'ProfilesFieldqry',qry:qry,eid:eid},
			beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function(){
				$hd(".loading").hide();
			},
			success:function(res){
				if(res.result == 'success'){
					$hd(".editHideTr").remove();
					$hd("#uniquekey").html(res.html).trigger('liszt:updated');
					$hd("#uniquealias").html(res.html).trigger('liszt:updated');
					$hd("#likesearch").html(res.html).trigger('liszt:updated');
					$hd("#itemDetailField").html(res.html).trigger('liszt:updated');
					$hd("#itemsListField").html(res.html).trigger('liszt:updated');
					if(res.unKey!='' &&res.unAlias!=''){
					$hd(".editHideTr").remove().trigger('liszt:updated');
					$hd("#uniquekey").html(res.unKey).trigger('liszt:updated');
					$hd("#uniquealias").html(res.unAlias).trigger('liszt:updated');
					$hd("#likesearch").html(res.likeSearches).trigger('liszt:updated');
					$hd("#itemDetailField").html(res.html).trigger('liszt:updated');
					$hd("#itemsListField").html(res.html).trigger('liszt:updated');
					$hd('.input_fields_wrap tr:first').after(res.tableTR).trigger('liszt:updated').trigger('liszt:updated');
					$hd('select.chosenSelect').chosen({"disable_search_threshold":0,"search_contains": true, "allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
					$hd(".js-example-tags").select2({
						tags: true

						})
					}					
					else if(res.uniqueKey==''){
					$hd(".editHideTr").remove(); 
					}
					
					
				}
				
			},
			error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);				  
			}
		});
		}
	});
/************************************************************************************************
              ITEM LIST TEMPLATE SELECTION
*************************************************************************************************/		
	$hd("#listtmplid").on("change",function(){
		var id=$hd("#listtmplid").val();
		$hd.ajax({
			url:'index.php?option=com_vdata&view=display',
			type:'POST',
			dataType:'json',
			data:{'task':'TemplateList',id:id},
			beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function(){
				$hd(".loading").hide();
			},
			success:function(res){
				if(res.result == 'success'){
					$hd('#list_parent iframe').contents().find('body').html(res.html);
				}
				
			},
			error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);				  
			}
		});
	});
/************************************************************************************************
              ITEM DETAIL TEMPLATE SELECTION
*************************************************************************************************/	
	$hd("#detailtmplid").on("change",function(){
		var id=$hd("#detailtmplid").val();
		$hd.ajax({
			url:'index.php?option=com_vdata&view=display',
			type:'POST',
			dataType:'json',
			data:{'task':'DetailTemplate',id:id},
			beforeSend: function()	{
				$hd(".loading").show();
			},
			complete: function(){
				$hd(".loading").hide();
			},
			success:function(res){
				if(res.result == 'success'){
					
					$hd('#detail_parent iframe').contents().find('body').html(res.html);
					return false;
				}
				
			},
			error: function(jqXHR, textStatus, errorThrown)	{
				alert(textStatus);				  
			}
		});
	});
	
	$hd(function() {
        $hd("[name=keyword]").click(function(){
		var rvalue=$hd(this).val();   
		if(rvalue==1) { $hd("#searchyes").show(); }
		else{ $hd("#searchyes").hide(); }
    });
	
 });
 /************************************************************************************************
              ADD AND REMOVE MORE DYNAMIC CREATE TABLE TR
*************************************************************************************************/
	
var max_fields      = 500; //maximum input boxes allowed
var wrapper         = $hd(".input_fields_wrap"); //Fields wrapper
var add_button      = $hd(".add_field_button"); //Add button ID
var x = 1; //initlal text box count
var y=parseInt($hd('.select_filter_field').length);
$hd(add_button).click(function(e){ //on add input button click
	e.preventDefault();
	//var morefilterkey      = $hd("#likesearch").html();
	var morefilterkey =$hd($hd('#likesearch').clone().find(':selected').removeAttr("selected").end()).html();
	if(x < max_fields){ //max input box allowed
	$hd(wrapper).append('<tr><td id='+y+'><select name="morefilterkey[]" class="select_filter_field chosenSelect" >'+ morefilterkey+'</select></td><td><div id="add_newTd_'+y+'" style="display:none;"></div></td><td><a href="#" class="remove_field btn btn-danger"><i class="icon-delete"></i> Remove</a></td> </tr>'); //add input box
		$hd('select.chosenSelect').chosen({"disable_search_threshold":0,"search_contains": true, "allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
	y++;
	x++;
}
});
$hd(wrapper).on("click",".remove_field", function(e){ //user click on remove text
	e.preventDefault(); 
	$hd(this).parent().parent().remove(); 
	y--;
	x--;
})	
/************************************************************************************************
              ADD MORE AND DYNAMIC CREATE FOR FIELDS SELECTION
*************************************************************************************************/	
$hd(document).on('change','.select_filter_field',function(){	
	var keyName1=$hd(this).parent().parent().find('.select_filter_field').val();
	var tdId=$hd(this).parent().attr('id');
	var morefiltertype = '<option value="0"><?php echo JText::_('SELECT_FIELD'); ?></option><option value="typetext"><?php echo JText::_('TEXTFIELD'); ?></option><option value="typeradio"><?php echo JText::_('RADIOBUTTON'); ?></option><option value="typecheck"><?php echo JText::_('CHECKBOX'); ?></option><option value="typedrop"><?php echo JText::_('DROPDOWN'); ?></option>';
	if ($hd("#add_newTd_"+tdId).css('display') === 'none') {
	$hd("#add_newTd_"+tdId).css("display","block");
	$hd("#add_newTd_"+tdId).prepend('<select name="fieldtype[type]['+keyName1+'][]" class="select_filter_type chosenSelect">'+morefiltertype+'</select><div class="select_filter_option" style="display:inline-block;"></div>');
	}
	if ($hd("#add_newTd_"+tdId).css('display') === 'block') {
	$hd("#add_newTd_"+tdId).html('<select name="fieldtype[type]['+keyName1+'][]" class="select_filter_type chosenSelect">'+morefiltertype+'</select><div class="select_filter_option" style="display:inline-block;"></div>');
	}
	$hd('select.chosenSelect').chosen({"disable_search_threshold":0,"search_contains": true, "allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"});
});
/************************************************************************************************
              ADD MORE AFTER SELECT FIELD TYPES
*************************************************************************************************/
$hd(document).on('change','.select_filter_type',function(){
	var keyName=$hd(this).parent().parent().parent().find('.select_filter_field').val();
	var val = $hd('option:selected',this).val();
	var tdd1=parseInt($hd('.select_filter_type').length);
	var tdd=tdd1-1;
	if(val=='typetext'){
	var fieldStrtype = '<option value="substr"><?php echo JText::_('SUB_STRING_SEARCH'); ?></option><option value="phrase"><?php echo JText::_('EXTRACT_PHRASE_SEARCH'); ?></option>';
	$hd(this).parent().find('div.select_filter_option').html('<select name="fieldtype[value]['+keyName+'][]" class="chosenSelect">'+fieldStrtype +'</select>').fadeIn();	
	$hd('select.chosenSelect').chosen({"disable_search_threshold":0,"search_contains": true, "allow_single_deselect":true,"placeholder_text_multiple":"Select some options","placeholder_text_single":"Select an option","no_results_text":"No results match"}); 
	}
	else if(val=='typeradio'){
	$hd(this).parent().find('div.select_filter_option').html('<select name="fieldtype[value]['+keyName+'][]" id="" class="js-example-tags" multiple></select>').fadeIn();
	}
	else if(val=='typecheck'){ $hd(this).parent().find('div.select_filter_option').html('<select name="fieldtype[value]['+keyName+'][]" id="" class="js-example-tags" multiple></select>').fadeIn();
	}
	else if(val=='typedrop'){ $hd(this).parent().find('div.select_filter_option').html('<select name="fieldtype[value]['+keyName+'][]" id="" class="js-example-tags" multiple></select>').fadeIn();
	}

	$hd(".js-example-tags").select2({
	tags: true

	})
});
/************************************************************************************************
              ITEM LIST EDITOR FOR SELECT FIELDS NAME
*************************************************************************************************/	
 $hd("#itemsListField").on("change",function(){
	 var itemsField=$hd(this).val();
	  if (typeof(jInsertEditorText) != 'undefined'){
		jInsertEditorText('{'+itemsField+'}', 'itemlisttmpl');
	   return false;
	}
 }); 
/************************************************************************************************
              DETAIL ITEM EDITOR FOR SELECT FIELDS NAME
*************************************************************************************************/
 $hd("#itemDetailField").on("change",function(){
	 var itemField=$hd(this).val();
	  if (typeof(jInsertEditorText) != 'undefined'){
		jInsertEditorText('{'+itemField+'}', 'itemdetailtmpl');
	   return false;
	}
 });
 
});


</script>
<style>
.select_filter_option{
	display:none;
}
</style>
<div id="vdatapanel">

<form action="<?php echo 'index.php?option=com_vdata&view=display';?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'DISPLAY_DETAILS' ); ?></legend>
<table id="cron_tbl" class="adminform table table-striped">
	<tr>
		<td><label class="hasTip required" title="<?php echo JText::_('DISPLAY_TITLE_DESC');?>"><?php echo JText::_('TITLE'); ?></label></td>
		<td><input type="text" name="title" id="title" class="inputbox required" value="<?php echo $this->item->title; ?>" size="50" /></td>
	</tr>
	<tr id="unqid"><td width="200"><label class="hasTip required" title="<?php echo JText::_('UNIQUE_PROFILE_DESC');?>"><?php echo JText::_('UNIQUE_PROFILE'); ?></label></td>
	<td><input type="text" name="uidprofile" value="<?php echo $this->item->uidprofile;?>" /></td></tr>
	<tr>
		<td width="200"><label class="hasTip required" title="<?php echo JText::_('DISPLAY_PROFILE_DESC');?>"><?php echo JText::_('DIS_PROFILE'); ?></label></td>
		<td>
		<select name="profileid" id="profileid" class="chosenSelect">
		<option value="0"><?php echo JText::_('SELECT_PROFILE'); ?></option>
		<?php for($i=0;$i<count($this->profiles);$i++) :?>
		<option value="<?php echo $this->profiles[$i]->id; ?>" <?php if($this->profiles[$i]->id==$this->item->profileid){ echo 'selected=""selected"';} ?>>
		<?php echo $this->profiles[$i]->title; ?>
		</option>
		<?php endfor; ?>
		</select>
		<span id="or">
		<?php echo JText::_('OR');?>
		<textarea name="qry"  id="qry" rows="5" cols="50" placeholder="<?php echo JText::_('COM_VDATA_EXPORT_QRY_PLACEHOLDER');?>"><?php if(!empty($this->item->qry)) {echo $this->item->qry;}elseif(!empty($this->qry)){echo $this->qry;}?></textarea>
		</span>
		</td>
	</tr>
	<tr><td><label class="hasTip required" title="<?php echo JText::_('DISPLAY_UNIQUE_KEY_DESC');?>"><?php echo JText::_('UNIQUE_KE'); ?></label></td>
	
	<td><select name="uniquekey" id="uniquekey" class="chosenSelect"> 
    <?php 
	if(!empty($this->item->uniquekey)){
		foreach($this->defaultfields->includekey as $v)
		{
			foreach($v as $value){ ?>
			<option value="<?php echo $value;?>" <?php if($this->item->uniquekey==$value){ echo "selected='selected'" ;}?>><?php echo $value; ?></option>
			<?php
			}
		}
    }
 else{ ?>
	<option value=""><?php echo JText::_('SELECT_KEY'); ?></option> <?php  } ?></select></td></tr>
	<tr><td><label class="hasTip required" title="<?php echo JText::_('DISPLAY_UNIQUE_ALIAS_DESC');?>"><?php echo JText::_('UNIQUE_ALIAS'); ?></label></td>
	<td><select name="uniquealias" id="uniquealias" class="chosenSelect"> 
    <?php if(!empty($this->item->uniquealias)){
			foreach($this->defaultfields->includekey as $v)
			{
				foreach($v as $value){ ?>
				<option value="<?php echo $value;?>"<?php if($this->item->uniquealias==$value){ echo "selected='selected'" ;}?>><?php echo $value; ?></option>
				<?php
				}
			} 
		}else{ ?>
	<option value=""><?php echo JText::_('SELECT_UNIQUE_ID'); ?></option> <?php  } ?>	
	</select></td></tr>
<!--************************************************************************************************
             KEYWORD SEARCH
*************************************************************************************************-->	

 <tr>
    <td> <label class="hasTip required" title="<?php echo JText::_('KEYWORD_SEARCH_DESC');?>"><?php echo JText::_('KEYWORD_SEARCH'); ?></label></td>
	<td colspan="2" class="r_button">
	<div class="radio btn-group">
	<label for="keychecked" class="radio"><?php echo JText::_('COM_DATA_OPTION_DISABLE'); ?></label>
	<input class="btn" type="radio" name="keyword" value="1" id="keychecked" <?php if($this->item->keyword==1){ echo 'checked="checked"'; }?>	/>
	<label for="keychecked1" class="radio"><?php echo JText::_('COM_VDATA_OPTION_DISABLE'); ?>	
	<input class="btn" type="radio" name="keyword" id="keychecked1" value="0" <?php if($this->item->keyword==0){ echo 'checked="checked"'; }?> /> 
	</div>
	</td>
	</tr> 
  <tr <?php if($this->item->keyword==1){ echo '';}else{echo 'style="display:none;"';} ?> id="searchyes">
    <td><label class="hasTip required" title="<?php echo JText::_('LIKE_SEARCH_DESC');?>"> <?php echo JText::_('LIKE_SEARCH'); ?></label></td>
    <td>
	<select name="likesearch[]" id="likesearch" multiple class="chosenSelect"> 
   <option value=""><?php echo JText::_('SELECT_KEYWORD');?></option>		
     <?php 
	 if(!empty($this->item->id)){ 
			$array=explode(",",$this->item->likesearch);
			foreach($this->defaultfields->likeSearch as $k=>$v)
			{
				foreach($v as $key=>$value){
					foreach($value as $val)
					{
						if($k=='refrTable')
						{ $optvalue=$key.'.'.$val;
						?>
						<option value="<?php echo $optvalue;?>" <?php if(in_array($optvalue,$array)){ echo "selected='selected'";}?>><?php echo $val;?></option>
						<?php 
						}
						else
						{ ?>
						<option value="<?php echo $val;?>"<?php if(in_array($val,$array)){ echo "selected='selected'";}?>><?php echo $val;?></option>
						<?php 
						}
					}
				}
			} 
        }
		?></select>
	</td>
  </tr>
<!--************************************************************************************************
             ADD MORE FILTER SEARCH
*************************************************************************************************-->	
<tr><td><label class="hasTip required" title="<?php echo JText::_('MORE_FIELD_SEARCH_DESC');?>"><?php echo JText::_('MORE_FIELD_SEARCH'); ?></label></td>
<td>
<table class="input_fields_wrap">
<tr><td colspan="3"> <button class="add_field_button btn btn-success"><i class="icon-new"></i> <?php echo JText::_('ADD_MORE_FILTER'); ?></button></td></tr>
<?php if(!empty($this->item->morefilterkey)){
$morefilterkey=json_decode($this->item->morefilterkey,true);
$fieldArrayKeys=array('typetext','typeradio','typecheck','typedrop');
$fieldArrayValues=array('Text Field','Radio Type','Check Box','Drop Down'); 
$fieldArray=array_combine($fieldArrayKeys,$fieldArrayValues);;
$arrayField=array(); 
if(isset($this->item->fieldtype)){
$arrayField=json_decode($this->item->fieldtype,true); }
$arrayColumns=array();
$arryTypes=array();
$arrayFieldType=array();
if(isset($arrayField)){
foreach($arrayField['type'] as $keys=>$fieldNames)
{
	$arrayColumns[]=$keys;
	$arryTypes[]=$fieldNames;
	foreach($fieldNames as $fName)
	{
	$arrayFieldType[]=$fName;
	}

}
}
	$arrFieldTypes=array();
	for($i=0;$i<count($arryTypes);$i++)
	{
	  $arrFieldTypes[]=$arryTypes[$i][0];
	}
	$arrFieldTypeValue=array();
	foreach($arrayField['value'] as $arrayKey=>$arrayVal)
	{
	  $arrFieldTypeValue[]=$arrayKey;
	}
	
for($j=0;$j<count($morefilterkey);$j++)
{	
		if(in_array($morefilterkey[$j],$arrFieldTypeValue)){
		  $FieldsValues=$arrayField['value'][$morefilterkey[$j]];
		}
		else{
			$FieldsValues=array();
		}

//print_r($morefilterkey[1]);
//jexit();
?>
<!--************************************************************************************************
             ADD MORE FILTER KEY
*************************************************************************************************-->	
<tr class="editHideTr">
<td id="<?php echo $j; ?>"><select name="morefilterkey[]" class="select_filter_field chosenSelect" >
<?php 
foreach($this->defaultfields->likeSearch as $k=>$v)
			{
				foreach($v as $key=>$value){
					foreach($value as $valsearch)
					{
						if($k=='refrTable')
						{
							$explodeTable=explode("#__",$key);
							$fieldName=ucfirst($explodeTable[1]).' '.$valsearch;
							$optvalue=$key.'.'.$valsearch;
						?>
						<option value="<?php echo $optvalue;?>" <?php if($morefilterkey[$j]==$optvalue){ echo "selected='selected'";}?>><?php echo $fieldName;?></option>
						<?php 
						}
						else
						{ ?>
						<option value="<?php echo $valsearch;?>"<?php if($morefilterkey[$j]==$valsearch){ echo "selected='selected'";}?>><?php echo $valsearch;?></option>
						<?php 
						}
					}
				}
			}
?>
</select></td> 
<!--************************************************************************************************
             ADD MORE FILTER FIELD TYPE
*************************************************************************************************-->	
<td>
<div id="add_newTd_<?php echo $j; ?>" style="display:block;">
<select name="fieldtype[type][<?php echo $arrayColumns[$j];?>][]" class="select_filter_type chosenSelect" >
<?php 

foreach($fieldArray as $fieldsKey=>$fieldsvalue)
{
	
?>
	<option value="<?php echo $fieldsKey;?>"<?php if($fieldsKey==$arrFieldTypes[$j]){ echo "selected='selected'"; }?>><?php echo $fieldsvalue;?></option>
<?php }

?>
</select>

<?php 
$fieldsTypes=$arryTypes[$j][0];
switch($fieldsTypes)
{
	//$FieldsValues=$arrayField['value'][$morefilterkey[$j]];
	
	case 'typetext':
	$strSearchKeys=array('phrase','substr');
	$strSearchValues=array('Extract Phrase Search','Sub String Search');
	
	$strSearchType=array_combine($strSearchKeys,$strSearchValues);
	
	?>
	<div class="select_filter_option" style="display:inline-block;">
	
	<select name="fieldtype[value][<?php echo $arrayColumns[$j];?>][]" class="chosenSelect">
	<?php 
	if(!empty($FieldsValues)){
	    foreach($FieldsValues as $options){
			foreach($strSearchType as $strSearchKeys=>$strSearchValues)	
			{
				?>
				<option value="<?php echo $strSearchKeys; ?>" <?php if($strSearchKeys==$options){
				echo "selected='selected'"; }?> ><?php  echo $strSearchValues; ?></option>
				<?php 
			} 
	    }
	}else{ 
	      foreach($strSearchType as $strSearchKeys=>$strSearchValues)	
			{   
				?>
				<option value="<?php echo $strSearchKeys; ?>"><?php  echo $strSearchValues; ?></option>
				<?php 
			} 
	}  ?>
	</select></div>
	<?php
	break;
	
	case 'typeradio':
	?>
	<div class="select_filter_option" style="display:inline-block;">
	<select name="fieldtype[value][<?php echo $arrayColumns[$j];?>][]" id="" class="js-example-tags" multiple>
	<?php 
	if(!empty($FieldsValues)){
	foreach($FieldsValues as $options){?>
	<option value="<?php echo $options; ?>" selected="selected"><?php  echo $options; ?></option>
	<?php } }else{  echo '<option value=""></option>';
	} ?>
	
	</select></div>
	<?php
	break;
	
	case 'typecheck':
	?>
	<div class="select_filter_option" style="display:inline-block;">
	<select name="fieldtype[value][<?php echo $arrayColumns[$j];?>][]" id="" class="js-example-tags" multiple>
	<?php 
	if(!empty($FieldsValues)){
	foreach($FieldsValues as $options){?>
	<option value="<?php echo $options; ?>" selected="selected"><?php  echo $options; ?></option>
	<?php }  }else{  echo '<option value=""></option>';
	} ?>
	</select>
	</div>
	<?php
	break;
	
	case 'typedrop':
	?>
	<div class="select_filter_option" style="display:inline-block;">
	<select name="fieldtype[value][<?php echo $arrayColumns[$j];?>][]"  class="js-example-tags" multiple>
	<?php 
	if(!empty($FieldsValues)){
	foreach($FieldsValues as $options){?>
	<option value="<?php echo $options; ?>" selected="selected" ><?php  echo $options; ?></option>
	<?php } }else{  echo '<option value=""></option>';
	}  ?>
	</select></div>
	<?php
	break;
} ?>
</div>
</td>
<td><a href="#" class="remove_field btn btn-danger"><i class="icon-delete"></i> <?php echo JText::_('REMOVE'); ?></a></td>
</tr>

<?php	
} 
}
?>



</table>
</td></tr>

<tr id="feed_access">
	<td width="200"><label class="hasTip required" title="<?php echo JText::_('ACCESS_DESC');?>"><?php echo JText::_('ACCESS'); ?></label></td>
	<td>
		<?php echo JHtmlAccess::level('access', $this->item->access, 'class="chosenSelect"', false);?>
	</td>
</tr>
<?php if( $user->authorise('core.edit.state', 'com_vdata') ){?>
<tr>
	<td width="200"><label class="hasTip required" title="<?php echo JText::_('STATUS_DESC');?>"><?php echo JText::_('STATUS');?></label></td>
	<td>
		<select name="state" class="chosenSelect">
			<option value="0" <?php if($this->item->state==0) echo 'selected="selected"';?>><?php echo JText::_('DISABLE');?></option>
			<option value="1" <?php if($this->item->state==1) echo 'selected="selected"';?>><?php echo JText::_('ENABLE');?></option>
		</select>
	</td>
</tr>
<tr><td><label class="hasTip required" title="<?php echo JText::_('ITEMS_PER_ROW_DESC');?>"><?php echo JText::_('ITEMS_PER_ROW'); ?></label></td>
	<td><select name="norowitem" id="norowitem" class="chosenSelect" >        
	<option value="1" <?php if($this->item->norowitem==1){ echo 'selected="selected"'; }?>>1</option>
	<option value="2" <?php if($this->item->norowitem==2){ echo 'selected="selected"'; }?>>2</option>
	<option value="3" <?php if($this->item->norowitem==3){ echo 'selected="selected"'; }?>>3</option>
	<option value="4" <?php if($this->item->norowitem==4){ echo 'selected="selected"'; }?>>4</option>
	<option value="5"<?php if($this->item->norowitem==5){ echo 'selected="selected"'; }?>>5
	</option>
	</select></td></tr>
	<tr><td><label class="hasTip required" title="<?php echo JText::_('SELECT_LIST_TEMPLATE_DESC');?>"><?php echo JText::_('SELECT_LIST_TEMPLATE'); ?></label></td>
	<td><select name="listtmplid" id="listtmplid" class="chosenSelect" >        
	
	<?php for($i=0;$i<count($this->listtemplates);$i++) { 
	if(!empty($this->item->detailtmplid)){
	$selectedId=$this->item->detailtmplid;}
	else{
		$selectedId=1;
	}
	$row=$this->listtemplates[$i]; ?>
	<option value="<?php echo $row->id;?>"<?php if($row->id==$selectedId){ echo 'selected="selected"'; }?>><?php echo $row->title; ?></option>	
	<?php } ?>
	
	
	</select></td></tr>
<tr>
 <td>
 <label class="hasTip required" title="<?php echo JText::_('INSERT_FIELDS_LIST_DEC');?>"><?php echo JText::_('INSERT_FIELDS'); ?></label> 
 <?php 

if(!empty($this->item->id))		{	
echo "<select id='itemsListField' class='chosenSelect'>";					
 foreach($this->defaultfields->likeSearch as $k=>$v)
			{
				foreach($v as $key=>$value){
					foreach($value as $valsearch)
					{
						if($k=='refrTable')
						{
							$optvalue=$key.'.'.$valsearch;
						?>
						<option value="<?php echo $optvalue ; ?>" ><?php echo $optvalue ; ?></option>
						<?php 
						}
						else
						{ ?>
						<option value="<?php echo $valsearch ; ?>" ><?php echo $valsearch ; ?></option>
						<?php 
						}
					}
				}
			}
			echo "</select>";
}
else{
	echo '<select id="itemsListField" class="chosenSelect"></select>';
}
 ?>

</td>
<td id="list_parent">
        <?php 
		/* JPluginHelper::importPlugin('content');
        $dispatcher = JDispatcher::getInstance();
		$listEditor=!empty($this->item->itemlisttmpl)?$this->item->itemlisttmpl:$this->defaultlist;
		$params = new stdClass;
        $dispatcher->trigger('onContentPrepare', array('com_vdata.display', &$listEditor, &$params, 0)); */
		$typeEditor = JFactory::getConfig()->get('editor');
		$editor = JEditor::getInstance($typeEditor);
		if(!empty($this->item->itemlisttmpl)){
		echo $editor->display('itemlisttmpl',$this->item->itemlisttmpl, '950', '275', '30', '10', true,'listtmpl'); }else{	echo $editor->display('itemlisttmpl',$this->defaultlist, '950', '275', '30', '10', true,'listtmpl'); }
        ?>
</td>
 </tr>
 <tr><td><label class="hasTip required" title="<?php echo JText::_('SELECT_DETAIL_TEMPLATE_DESC');?>"><?php echo JText::_('SELECT_DETAIL_TEM'); ?></label></td>
	<td><select name="detailtmplid" id="detailtmplid" class="chosenSelect">        
	
	<?php for($i=0;$i<count($this->detailtemplates);$i++) { 
	if(!empty($this->item->detailtmplid)){
	$selectedId=$this->item->detailtmplid;}
	else{
		$selectedId=1;
	}
	$row=$this->detailtemplates[$i]; ?>
	<option value="<?php echo $row->id;?>"<?php if($row->id==$selectedId){ echo 'selected="selected"'; }?>><?php echo $row->title; ?></option>	
	<?php } ?>
	
	
	</select></td></tr>
 <tr>
 <td>
 <label class="hasTip required" title="<?php echo JText::_('INSERT_FIELDS_DET_DEC');?>"><?php echo JText::_('INSERT_FIELDS'); ?></label> 
 <?php 

	if(!empty($this->item->id))		{	
	echo "<select id='itemDetailField' class='chosenSelect'>";

 foreach($this->defaultfields->likeSearch as $k=>$v)
			{
				foreach($v as $key=>$value){
					foreach($value as $valsearch)
					{
						if($k=='refrTable')
						{
							$optvalue=$key.'.'.$valsearch;
						?>
						<option value="<?php echo $optvalue ; ?>" ><?php echo $optvalue ; ?></option>
						<?php 
						}
						else
						{ ?>
						<option value="<?php echo $valsearch ; ?>"><?php echo $valsearch ; ?></option>
						<?php 
						}
					}
				}
			}
	echo "</select>";		
	}
	else{
		echo '<select id="itemDetailField" class="chosenSelect"></select>';
	}
 ?>
</td>
<td id="detail_parent">
		<?php 
		$editor = JFactory::getEditor();
		if(!empty($this->item->itemdetailtmpl)){
		echo $editor->display('itemdetailtmpl',$this->item->itemdetailtmpl, '950', '275', '30', '10', true,'detailtmpl'); }else{	echo $editor->display('itemdetailtmpl',$this->defaultDetail, '950', '275', '30', '10', true,'detailtmpl'); }
		?>
</td>
 </tr>
<?php }?>
</table>
</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="id" id="eid" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="<?php echo 'display';?>" />
<input type="hidden" name="st" value="1" />
</form>

</div>