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
$editor = JFactory::getEditor();
$profileid = JFactory::getApplication()->input->getInt('profileid', 0);

?>

<script type="text/javascript">
  jQuery(function() {
	  if (jQuery('.date').length>0) {
    jQuery( ".date" ).datetimepicker({
	lang:'en',
	timepicker:false,
	format:'Y-m-d',
	formatDate:'Y-m-d'
	
	}); }
	 if (jQuery('.datetime').length>0) {
	 jQuery( ".datetime" ).datetimepicker({
	lang:'en',
	format:'Y-m-d H:i:s',
	formatDate:'Y-m-d H:i:s'
	
	}); }
	if (jQuery('.time').length>0) {
	jQuery( ".time" ).datetimepicker({
	lang:'en',
	datepicker:false,
	format:'H:i:s',
	step:1
	
	}); }
  });
 
</script>

<div id="vdatapanel">

<form action="index.php?option=com_vdata&view=quick" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'Update' ); ?></legend>
	<table class="adminform table table-striped markers">
<?php
$db = JFactory::getDbo();
//print_r($this->rowvalue->delimiter);
for($i=0;$i<count($this->rowinfo);$i++){
$row = 	$this->rowinfo[$i];

    $column = $row->Field;
	$default = 'Default';
	$value = isset($this->rowvalue->$column)?$this->rowvalue->$column:$row->$default;
if($row->Key=='PRI'){

	echo '<tr><td><lable>'.$row->Field.'[PRI]</lable></td>';
	echo '<td><lable><input id="field_1_3" class="textfield" readonly="readonly" type="text" value="'.$value.'" name="'.$row->Field.'"></lable></td></tr>';
    echo '<input id="field_1_3" class="textfield" type="hidden" value="'.$row->Field.'" name="primarycolumn">';	
}else{
if(substr($row->Type,0,strpos($row->Type,'('))=='int'){
	echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	
	echo '<td><lable><input id="field_1_3" class="textfield" type="text" size="'.substr($row->Type,strpos($row->Type,'(')+1,((strpos($row->Type,')'))-(strpos($row->Type,'(')+1))).'" value=\''.$value.'\' name="'.$row->Field.'"></lable></td></tr>';
}
elseif(substr($row->Type,0,strpos($row->Type,'('))=='tinyint'||substr($row->Type,0,strpos($row->Type,'('))=='boolean'){
echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield" type="text" size="'.substr($row->Type,strpos($row->Type,'(')+1,((strpos($row->Type,')'))-(strpos($row->Type,'(')+1))).'" value=\''.$value.'\' name="'.$row->Field.'"></lable></td></tr>';	
}
elseif(substr($row->Type,0,strpos($row->Type,'('))=='smallint'){
	echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield" type="text" size="'.substr($row->Type,strpos($row->Type,'(')+1,((strpos($row->Type,')'))-(strpos($row->Type,'(')+1))).'" value=\''.$value.'\' name="'.$row->Field.'"></lable></td></tr>';
}
elseif(substr($row->Type,0,strpos($row->Type,'('))=='mediumint'){
	echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield" type="text" size="'.substr($row->Type,strpos($row->Type,'(')+1,((strpos($row->Type,')'))-(strpos($row->Type,'(')+1))).'" value=\''.$value.'\' name="'.$row->Field.'"></lable></td></tr>';
}
elseif(substr($row->Type,0,strpos($row->Type,'('))=='varchar'){
     echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield" type="text" size="'.substr($row->Type,strpos($row->Type,'(')+1,((strpos($row->Type,')'))-(strpos($row->Type,'(')+1))).'" value=\''.$value.'\' name="'.$row->Field.'"></lable></td></tr>';	
}
elseif(substr($row->Type,0,strpos($row->Type,'('))=='bigint'){
	echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield" type="text" size="'.substr($row->Type,strpos($row->Type,'(')+1,((strpos($row->Type,')'))-(strpos($row->Type,'(')+1))).'" value=\''.$value.'\' name="'.$row->Field.'"></lable></td></tr>';
}
elseif(substr($row->Type,0,strpos($row->Type,'('))=='decimal'){
	echo '<lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield" type="text" size="'.substr($row->Type,strpos($row->Type,'(')+1,((strpos($row->Type,')'))-(strpos($row->Type,',')+1))).'" value=\''.$value.'\' name="'.$row->Field.'"></lable></td></tr>';
}
elseif(substr($row->Type,0,strpos($row->Type,'('))=='float'){
echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield" type="text" size="'.substr($row->Type,strpos($row->Type,'(')+1,((strpos($row->Type,')'))-(strpos($row->Type,',')+1))).'" value=\''.$value.'\' name="'.$row->Field.'"></lable></td></tr>';	
}
elseif(substr($row->Type,0,strpos($row->Type,'('))=='double'){
	
}
elseif(substr($row->Type,0,strpos($row->Type,'('))=='bit'){
echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield" type="text" size="'.substr($row->Type,strpos($row->Type,'(')+1,strpos($row->Type,')')).'" value="" name="'.$row->Field.'"></lable></td></tr>';	
}
elseif(substr($row->Type,0,strpos($row->Type,'('))=='datetime'){
	echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield datetime" type="text" value="'.$value.'" name="'.$row->Field.'"></lable></td></tr>';	
} 
elseif(substr($row->Type,0,strpos($row->Type,'('))=='timestamp 	'){
	echo '<tr><td><lable>'.$row->Field.'z</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield timestamp" type="text" value="'.$value.'" name="'.$row->Field.'"></lable></td></tr>';	
}

	
elseif($row->Type=='text'){
	echo '<tr><td><lable>'.$row->Field.'</label></td>';
	echo '<td><lable>'.$editor->display( $row->Field, $value, '400', '200', '20', '5', true, null, null, null).'</label></td></tr>';
	
}	
elseif($row->Type=='tinytext'){
echo '<tr><td><lable>'.$row->Field.'</label></td>';
    echo '<td><lable>'.$editor->display( $row->Field, $value, '400', '200', '20', '5', true, null, null, null).'</label></td></tr>';
	
}
elseif($row->Type=='mediumtext'){
echo '<tr><td><lable>'.$row->Field.'</label></td>';
	echo '<td><lable>'.$editor->display( $row->Field, $value, '400', '200', '20', '5', true, null, null, null).'</label></td></tr>';
}
elseif($row->Type=='longtext'){
	echo '<tr><td><lable>'.$row->Field.'</label></td>';
	echo '<td><lable>'.$editor->display( $row->Field, $value, '400', '200', '20', '5', true, null, null, null).'</label></td></tr>';
}

elseif($row->Type=='date'){
echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield date" type="text" value="'.$value.'" name="'.$row->Field.'"></lable></td></tr>';		
} 	
elseif($row->Type=='datetime'){
echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield datetime" type="text" value="'.$value.'" name="'.$row->Field.'"></lable></td></tr>';		
} 
elseif($row->Type=='time'){
	echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield time" type="text" value="'.$value.'" name="'.$row->Field.'"></lable></td></tr>';	
} 
elseif($row->Type=='year'){
    echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield" type="text" size="8" value="'.$value.'" name="'.$row->Field.'"></lable></td></tr>';		
}
elseif($row->Type=='blob' || $row->Type=='longblob'|| $row->Type=='mediumblob'|| $row->Type=='tinyblob'){
    echo '<tr><td><lable>'.$row->Field.'</lable></td>'; 
	echo '<td><lable><input id="field_1_3" class="textfield" type="file" size="8" name="'.$row->Field.'">
	
	</lable></td></tr>';		
}	
}
}
?>
</table>
	</fieldset>
	</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); 
$array = JFactory::getApplication()->input->get('cid',  0, '', 'array');
$column_name = JFactory::getApplication()->input->get('column_name','');
$table_name = JFactory::getApplication()->input->get('main_table_name','');

?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="quick" />

<input type="hidden" name="primerykey" value="<?php echo (int)$array[0]; ?>" />
<input type="hidden" name="column_name" value="<?php echo $column_name; ?>" />
<input type="hidden" name="table_name" value="<?php echo $table_name; ?>" />

</form>
</div>