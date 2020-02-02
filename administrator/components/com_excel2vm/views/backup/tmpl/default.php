<?php

defined('_JEXEC') or die('Restricted access');
$view=JRequest :: getVar('view','excel2vm','','string');
$mainframe = JFactory::getApplication();
$sitename=$mainframe->getCfg('sitename');
$doc = JFactory::getDocument();
$doc->addScript(JURI::base()."components/com_excel2vm/js/backup.js");
$params = JComponentHelper :: getParams("com_excel2vm");


function getSize($bytes){
   if($bytes<1024)
   	  return $bytes." B<br>";
   elseif($bytes<1024*1024)
   	  return round($bytes/1024)." KB<br>";
   else
   	  return round($bytes/(1024*1024),2)." MB<br>";
}
?>
<style type="text/css">
.table-striped td{
	border: #CCCCCC 1px solid!important;
	text-align: center;
}
.ui-icon-closethick{
  top: 0!important;
  left: 0!important;
}

</style>
<?php $notify_show=$params->get('notify_show','fold');
$notify_hide=$params->get('notify_hide','explode');

if($notify_show=='none')$notify_show='';
if($notify_hide=='none')$notify_hide='';
$jtext_array=array('BACKUP_SUCCESSFULL','THE_OPERATION_WAS_SUCCESSFUL','ERROR_OCCURED');
?>
<script type="text/javascript">
var $notify_show = '<?php echo  $notify_show ?>';
var $notify_hide = '<?php echo  $notify_hide ?>';
var $SERVER_NAME = "<?php echo  $_SERVER["SERVER_NAME"] ?>";
<?php foreach($jtext_array as $jtext){
echo 'var $jtext_'.$jtext." = '".JText::_($jtext)."';\n";
}
?>
</script>


</script>
<div id="dialog" style="z-index: 10" title=""></div>

<div id="dialog-form" title="Очистка">
	<p class="validateTips">Выберите параметры очистки</p>
	<form>
	<input id="products" name="products" type="checkbox" <?php echo  @$_COOKIE['b_products']?'checked="true"':'' ?>> - <label style="display:inline" for="products">Товары</label><br>
	<input id="cats" name="cats" type="checkbox" <?php echo  @$_COOKIE['b_cats']?'checked="true"':'' ?>> - <label style="display:inline"  for="cats">Категории и Товары</label><br>

	<input id="manufacturers" name="manufacturers" type="checkbox" <?php echo  @$_COOKIE['b_manufacturers']?'checked="true"':'' ?>> - <label style="display:inline"  for="manufacturers">Производители</label><br>
	<input id="customs" name="customs" type="checkbox" <?php echo  @$_COOKIE['b_customs']?'checked="true"':'' ?>> - <label style="display:inline"  for="customs">Настраиваемые поля</label><br>
	<input id="customs_profile" name="customs_profile" type="checkbox" <?php echo  @$_COOKIE['b_customs_profile']?'checked="true"':'' ?>> - <label style="display:inline"  for="customs_profile">Произвольные поля в профиле</label><br>
	<input id="empty_profile" name="empty_profile" type="checkbox" <?php echo  @$_COOKIE['b_empty_profile']?'checked="true"':'' ?>> - <label style="display:inline"  for="empty_profile">Пустые поля в профиле</label><br>
    <input id="images" name="images" type="checkbox" <?php echo  @$_COOKIE['b_images']?'checked="true"':'' ?>> - <label style="display:inline"  for="images">Все файлы изображений</label><br>
    <input id="backups" name="backups" type="checkbox" <?php echo  @$_COOKIE['b_backups']?'checked="true"':'' ?>> - <label style="display:inline"  for="backups">Все резервные копии</label><br>
    <input id="loaded" name="loaded" type="checkbox" <?php echo  @$_COOKIE['b_loaded']?'checked="true"':'' ?>> - <label style="display:inline"  for="loaded">Все импортированные файлы</label><br>
    <input id="exported" name="exported" type="checkbox" <?php echo  @$_COOKIE['b_exported']?'checked="true"':'' ?>> - <label style="display:inline"  for="exported">Все экспортированные файлы</label><br>
	</form>
</div>
<center><div id="loader" style="width: 100%; height: 220px;position:absolute;display: none"><center><img src="<?php echo  JURI::base()."components/com_excel2vm/assets/images/loader.gif" ?>"></center></div></center>
<center>

	<form action="" name="adminForm" id="adminForm" method="POST">

	<table class="table table-striped" style=" width: 900px;" cellpadding="0" cellspacing="0" border="1">
		<thead>
		    <tr><th class="title" colspan="6"><?php echo  JText::_('BACKUPS') ?></th></tr>
			<tr style="font-size: 14px; color: #0000FF ">
				<th class="title">ID</th >
				<th class="title"><?php echo  JText::_('FILE_NAME') ?></th >
				<th class="title"><?php echo  JText::_('SIZE') ?></th >
				<th class="title"><?php echo  JText::_('DATE') ?></th >
				<th class="title"><?php echo  JText::_('DELETE') ?></th >
				<th class="title"><?php echo  JText::_('RECOVER_BUTTON') ?></th >

			</tr>
	   </thead>
	   <tbody>
<?php
if($this->list):
	$i=0;
	foreach($this->list as $l):
		$link="components/com_excel2vm/backup/".$l->file_name;
		$i++;

		?>
			  <tr id="<?php echo $l->backup_id ?>" class='row<?php echo $i%2 ?>'>

			     <td><?php echo $l->backup_id ?></td>
			     <td><a href="<?php echo $link ?>" target="_blank"><?php echo $l->file_name ?></a></td>
			     <td><?php echo getSize($l->size) ?></td>
			     <td><?php echo $l->date2 ?></td>
                 <td><li style="display: inline-block" class="ui-state-default ui-corner-all"><span title="Удалить" rel="<?php echo $l->backup_id ?>" class="ui-icon ui-icon-circle-close"></span></li></td>
                 <td><li style="display: inline-block" class="ui-state-default ui-corner-all"><span title="<?php echo  JText::_('RECOVER_BUTTON') ?>" rel="<?php echo $l->backup_id ?>" class="ui-icon ui-icon-arrowreturnthick-1-w"></span></li></td>

			  </tr>
		<?php
	endforeach;
endif;

?>
	   </tbody>


	   </table>
	   <input type="hidden" name="boxchecked" value="0" />
       <input type="hidden" name="filter_order" value="<?php echo JRequest :: getVar('filter_order','','','string') ?>" />
	   <input type="hidden" name="filter_order_Dir" value="<?php echo JRequest :: getVar('filter_order_Dir','','','string') ?>" />
       <input type="hidden" name="option" value="com_excel2vm" />
       <input type="hidden" name="view" value="<?php echo @$view ?>" />

	   <input type="hidden" name="task" value="" />

	</form>

</center>


