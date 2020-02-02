<?php
defined('_JEXEC') or die('Restricted access');
$view=JRequest :: getVar('view','export','','string');
$inputCookie  = JFactory::getApplication()->input->cookie;
$params = JComponentHelper :: getParams("com_excel2vm");
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base()."components/com_excel2vm/assets/chosen.css");
$doc->addScript(JURI::base()."components/com_excel2vm/js/chosen.jquery.min.js");
$doc->addScript(JURI::base()."components/com_excel2vm/js/export.js");



$limit_messages=$params->get('limit_messages',1);
function getSize($bytes){
   if($bytes<1024)
   	  return $bytes." B<br>";
   elseif($bytes<1024*1024)
   	  return round($bytes/1024)." KB<br>";
   else
   	  return round($bytes/(1024*1024),2)." MB<br>";
}


     $memory_limits[] = JHTML::_('select.option',  '0.2', "20%", 'value', 'text' );
     $memory_limits[] = JHTML::_('select.option',  '0.3', "30%", 'value', 'text' );
     $memory_limits[] = JHTML::_('select.option',  '0.4', "40%", 'value', 'text' );
     $memory_limits[] = JHTML::_('select.option',  '0.5', "50%", 'value', 'text' );
     $memory_limits[] = JHTML::_('select.option',  '0.6', "60%", 'value', 'text' );
     $memory_limits[] = JHTML::_('select.option',  '0.7', "70%", 'value', 'text' );
     $memory_limits[] = JHTML::_('select.option',  '0.8', "80%", 'value', 'text' );
     $memory_limits[] = JHTML::_('select.option',  '0.9', "90%", 'value', 'text' );

     $cat_ordering[] = JHTML::_('select.option',  'category_child_id', JText::_('BY_ID'), 'value', 'text' );
     $cat_ordering[] = JHTML::_('select.option',  'category_name', JText::_('ALPHABETICALLY'), 'value', 'text' );

     $cat_ordering[] = JHTML::_('select.option',  'vc.ordering', JText::_('BY_ORDERING'), 'value', 'text' );

     $product_statuses[] = JHTML::_('select.option',  '-1', 'Все', 'value', 'text' );
     $product_statuses[] = JHTML::_('select.option',  '0', 'Неопубликованные', 'value', 'text' );
     $product_statuses[] = JHTML::_('select.option',  '1', 'Опубликованные', 'value', 'text' );

     $children_export_statuses[] = JHTML::_('select.option',  '0', 'Не экспортировать', 'value', 'text' );
     $children_export_statuses[] = JHTML::_('select.option',  '1', 'Экспортировать', 'value', 'text' );
     $selected_cat=@unserialize(urldecode(JRequest::getVar('c_category', 'cookie', '0', 'string')));
     $selected_man=@unserialize(urldecode(JRequest::getVar('c_man', 'cookie', '0', 'string')));

     // Получаем выпадающий список
     $list = '<select name="category" style="float: none;width: 160px;" size="1"><option val="0">Все</option>'.$this->categories.'</select>';
     //$list =JHTML::_('select.genericlist',$this->categories,'category','style="float: none;width: 160px;" size="1" ','category_child_id','category_name',is_array($selected_cat)?$selected_cat[0]:$selected_cat);

if($this->manufacturers){
    $man_list = JHTML::_('select.genericlist',$this->manufacturers,'manufacturer_id[]','data-placeholder="Выберите производителя" class="chosen-select" multiple style="float: none;width: 220px;" size="1" ','virtuemart_manufacturer_id','mf_name',$selected_man?$selected_man:0);


}

if($this->config->price_hint):
function getNameFromNumber($num) {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return getNameFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
}
$total_fields = count($this->fields);

//Уведомления о лимитах
$max_execution_time=ini_get('max_execution_time');
$memory_limit=(int)ini_get('memory_limit');

$max_execution_time_min=60;
$memory_limit_min=128;
if((($max_execution_time<$max_execution_time_min AND $max_execution_time!=0) OR ($memory_limit<$memory_limit_min AND $memory_limit !=0)) AND $limit_messages){
    echo  JHtml::_('sliders.start', 'limit_warnings');
    if($max_execution_time<$max_execution_time_min AND $max_execution_time!=0){
        echo JHtml::_('sliders.panel',"Внимание! Рекомендуется увеличить параметр <b>max_execution_time</b> до $max_execution_time_min с." , 'max_execution_time');
        echo "<p>На Вашем сервере установлен низкий <b>лимит времени на исполнение скрипта</b> - <b>{$max_execution_time} c</b>, которого недостаточно для полноценной работы компонента. Рекомендуется создать в корне сайта файл <b>.htaccess</b> (если его у Вас еще нет) и добавить в него следующую строку:</p>
              <p><b>php_value max_execution_time $max_execution_time_min</b></p>
              <p>Если Вы все еще видите это сообщение, то Вам необходимо обратиться в тех. поддержку хостинга для увеличения параметра <b>max_execution_time</b> до <b>{$max_execution_time_min} с</b>.</p>
              <p><i>Эти уведомления можно скрыть в <b>Настройках</b> компонента на вкладке <b>Отладка</b></i></p>
              ";
    }
    if($memory_limit<$memory_limit_min AND $memory_limit !=0){
        echo JHtml::_('sliders.panel',"Внимание! Рекомендуется увеличить параметр <b>memory_limit</b> до {$memory_limit_min}M" , 'memory_limit');
        echo "<p>На Вашем сервере установлен низкий <b>лимит на использование оперативной памяти</b> - <b>{$memory_limit}M</b>, которого может быть недостаточно для полноценной работы компонента. Рекомендуется создать в корне сайта файл <b>.htaccess</b> (если его у Вас еще нет) и добавить в него следующую строку:</p>
              <p><b>php_value memory_limit {$memory_limit_min}M</b></p>
              <p>Если Вы все еще видите это сообщение, то Вам необходимо обратиться в тех. поддержку хостинга для увеличения параметра <b>memory_limit</b> до <b>{$memory_limit_min}M</b></p>
              <p><i>Эти уведомления можно скрыть в <b>Настройках</b> компонента на вкладке <b>Отладка</b></i></p>
              ";
    }
    echo JHtml::_('sliders.end');
}
?>
 <?php  if($this->versions->new_version AND str_replace(".","",$this->versions->my_version)<str_replace(".","",$this->versions->new_version)){ 
    echo '<pre style="text-align:left; font-size:16px;font-weight: bold;">';
    echo "<a href='".JURI::root()."administrator/index.php?option=com_excel2vm&view=support' target='_blank'>Доступна новая версия! - {$this->versions->new_version}</a><br>";
    echo $this->versions->description;
    echo '</pre>';
 }
 ?>
<div style="position:relative">
 <h3><?php echo JText::_('CURRENT_PROFILE') ?>: <span style="font-weight: bold; color: #006633" id="current_profile"><?php echo $this->config->profile_name ?></span></h3>
  <div id="version" style="position: absolute; top:5px;right: 5px"><?php echo JText::_('JVERSION') ?>: <?php echo $this->versions->my_version ?></div>
</div>
<form action="index.php?option=com_excel2vm&view=export" method="POST">
	<h3><?php echo JText::_('CHANGE_TO') ?>: <?php echo JHTML::_('select.genericlist',$this->profiles,  'profile_id', 'size="1" onchange="this.form.submit()"','id','profile',$this->config->profile_id) ?></h3>
	<input type="hidden" name="task" value="change_profile" />
</form>
<?php switch ($this->config->price_template) {
  case 1:
         $categories=array('1.'.JText::_('HOUSEHOLD_APPLIANCES'),'1.1.'.JText::_('REFRIGERATORS'));
  break;
  case 2:
         $categories=array(JText::_('HOUSEHOLD_APPLIANCES'),$this->config->simbol.JText::_('REFRIGERATORS'));
  break;

  case 3:
         $categories=array(JText::_('HOUSEHOLD_APPLIANCES'),JText::_('REFRIGERATORS').$this->config->simbol);
  break;

  case 4:
         $categories=array(JText::_('HOUSEHOLD_APPLIANCES'),JText::_('REFRIGERATORS'));
		 foreach($this->fields as $a ){
            if($a->name=='path'){
               $path=$a->ordering;
            }
    	}
		if(!isset($path))echo '<font size="2" color="#FF0000">'.JText::_('YOU_DID_NOT_SPECIFY_THE_COLUMN_NUMBER_CATEGORY').'</font>';
  break;
  case 5:
         $categories=array('','');
		 echo '<strong><font size="3" color="#FF0000">'.JText::_('WRONG_METHOD').'</font></strong>';
case 6:
         $categories=array('','');
		 if(!isset($this->fields['path']))echo '<font size="2" color="#FF0000">'.JText::_('YOU_DID_NOT_SPECIFY_THE_COLUMN_NUMBER_CATEGORY').'</font>';


         $list = '<select name="category[]" data-placeholder="Выберите категорию" class="chosen-select" multiple style="float: none;width: 220px;" size="1" >'.$this->categories.'</select>';
         //$list = JHTML::_('select.genericlist',$this->categories,'category[]','data-placeholder="Выберите категорию" class="chosen-select" multiple style="float: none;width: 220px;" size="1" ','category_child_id','category_name',$selected_cat);
  break;
  case 7:
        $categories=array('','');
		if(!isset($this->fields['path']))echo '<font size="2" color="#FF0000">'.JText::_('YOU_DID_NOT_SPECIFY_THE_COLUMN_NUMBER_CATEGORY').'</font>';
        $list = '<select name="category[]" data-placeholder="Выберите категорию" class="chosen-select" multiple style="float: none;width: 220px;" size="1" >'.$this->categories.'</select>';
        //$list = JHTML::_('select.genericlist',$this->categories,'category[]','data-placeholder="Выберите категорию" class="chosen-select" multiple style="float: none;width: 220px;" size="1" ','category_child_id','category_name',$selected_cat);
  break;
  case 8:
         $categories=array(JText::_('HOUSEHOLD_APPLIANCES'),JText::_('REFRIGERATORS').$this->config->simbol);
  break;
}
?>

<h3><?php echo JText::_('PRICE_EXAMPLE') ?>:</h3>
<table class="table table-striped" align="center" border="1">


  <tr class="title">
     <?php if($this->config->price_template==8): ?>
     <td rowspan="6" style="width: 67px; padding: 0"><img src="./components/com_excel2vm/assets/images/ierarh.jpg" width="67" height="153" style="border: 0" alt=""></td>
	 <?php endif; ?>
     <td class="ui-state-highlight center bold"><?php echo JText::_('LINE_NOMBER') ?></td>
     <?php      for($i=0;$i<$total_fields;$i++)
	 	echo "<td class=\"ui-state-highlight center bold\">".getNameFromNumber($i)."(".($i+1).")</td>";
     ?>
  </tr>
  <tr class="title">
     <th class="ui-state-highlight"></th>
     <?php foreach($this->fields as $f)echo "<th class=\"title\">".JText::_($f->title)."</th>"; ?>
  </tr>

<?php if(!in_array($this->config->price_template,array(5,6,7))): ?>
  <tr>
      <td class="ui-state-highlight center bold"><?php echo $this->config->first++ ?></td>
      <?php       for($i=0;$i<$total_fields;$i++)
	  	 if($this->config->cat_col == $i+1)
		 	echo "<td>".$categories[0]."</td>";
		 elseif(@$path == $i+1)
		    echo "<td>1</td>";
		 else
		 	echo "<td>&nbsp;</td>";
      ?>
  </tr>
  <tr>
      <td class="ui-state-highlight center bold"><?php echo $this->config->first++ ?></td>
      <?php       for($i=0;$i<$total_fields;$i++)
	  	 if($this->config->cat_col == $i+1)
		 	echo "<td>".$categories[1]."</td>";
		 elseif(@$path == $i+1)
		    echo "<td>1.1</td>";
		 else
		 	echo "<td>&nbsp;</td>";
      ?>
  </tr>
<?php  endif; ?>
  <tr>
      <td class="ui-state-highlight center bold"><?php echo $this->config->first++ ?></td>
      <?php 	  foreach($this->fields as $key => $f){
          $this->fields[$key]->example = explode(';',$f->example);
		  if($this->config->price_template==6 AND $f->name=='path')
		  	 $this->fields[$key]->example=array(1,2);
		  elseif($this->config->price_template==7 AND $f->name=='path')
		     $this->fields[$key]->example=array(JText::_('HOUSEHOLD_APPLIANCES'),JText::_('REFRIGERATORS'));
          echo "<td>".JText::_($this->fields[$key]->example[0])."</td>";
	  }
	  ?>
  </tr>

  <tr>
      <td class="ui-state-highlight center bold"><?php echo $this->config->first++ ?></td>
      <?php 	  foreach($this->fields as $key => $f)
          echo "<td>".JText::_($this->fields[$key]->example[1])."</td>";

	  ?>
  </tr>
</table>
<?php endif; ?>
<?php $jtext_array=array('ERROR_OCCURED','ERROR_OCCURED_EXPORT','OUT_OF_RAM1','OUT_OF_RAM2','OUT_OF_RAM3','DOWNLOAD_ALL_PARTS',);
?>
<script>

<?php foreach($jtext_array as $jtext){
echo 'var $jtext_'.$jtext." = '".JText::_($jtext)."';\n";
}
?>
</script>
<style type="text/css">
.wide{
	width: 430px!important;
	max-width: 460px!important;
	text-align: left;
}
.small{width: 80px!important;max-width: 100px!important;}
.panelform label{
	max-width: 50%!important;
	width: 160px!important;
	text-align: right;
}

</style>

<div id="exported_files_wrapper">
<?php echo JHtml::_('sliders.start', 'export-sliders', array('useCookie'=>1)); ?>
<?php echo JHtml::_('sliders.panel', "Экспортированные файлы", 'export_files'); ?>
<table style="border-collapse: collapse" id="uploaded_files_table" class="tablesorter">
                <thead>
                  <tr>
                    <th>Файл</th>
                    <th style="width: 62px">Размер</th>
                    <th>Дата</th>
                    <th></th>
                    <th><img style="cursor: pointer" title="Удалить все"  class="delete_all" src="<?php echo JURI::base() ?>/components/com_excel2vm/assets/images/delete.png" width="16" height="16" alt=""></th>
                  </tr>
                </thead>
                <tbody id="uploaded_files_tbody">
                <?php foreach($this->files as $key=>$f): ?>
                  <tr id="row_<?php echo $key ?>">
                    <td><label for="uploaded_file_<?php echo $key ?>"><?php echo $f->file ?></label></td>
                    <td><?php echo getSize($f->size) ?></td>
                    <td><?php echo date("Y-m-d H:i",$f->time) ?></td>
                    <td><a href="index.php?option=com_excel2vm&view=export&task=download&file=<?php echo $f->file ?>"><img src="<?php echo JURI::base() ?>/components/com_excel2vm/assets/images/download.png" width="16" height="16" alt=""></a></td>
                    <td><img title="Удалить файл <?php echo $f->file ?>" style="cursor: pointer" rel="<?php echo $key ?>" file="<?php echo $f->file ?>"  class="delete" src="<?php echo JURI::base() ?>/components/com_excel2vm/assets/images/delete.png" width="16" height="16" alt=""></td>
                  </tr>

                <?php endforeach; ?>
                </tbody>
</table>
<?php echo JHtml::_('sliders.end'); ?>
</div>

<h1 align="center"><?php echo JText::_('EXPORT') ?></h1>
<form id="export_form" action="index.php" method="POST" enctype="multipart/form-data">
<fieldset class="panelform" style="width:400px;margin: 10px auto;">


	<input type="hidden" id="part" name="part" value="0" />


        <label><?php echo JText::_('FILE_FORMAT') ?>:</label>
		<select style="float: none" name="csv" id="csv" size="1">
			<option <?php echo $inputCookie->get('c_csv', 2)==1?'selected="selected"':'' ?> value="1">xls</option>
			<option <?php echo $inputCookie->get('c_csv', 2)==2?'selected="selected"':'' ?> value="2">xlsx</option>
			<option <?php echo $inputCookie->get('c_csv', 2)==0?'selected="selected"':'' ?> value="0">csv</option>
		</select>
        <br />

        <?php if($this->price_label): ?>
         <label>Метка прайса:</label>
            <?php echo JHTML::_('select.genericlist',$this->price_labels,'price_label','size="1" style="float: none;width: 220px;','value','text'); ?>
		 <br />

        <?php endif; ?>
        <label><?php echo JText::_('CATEGORY') ?>:</label>
            <?php echo $list ?>
		<br />
        <?php if($this->manufacturers): ?>
        <label>Производитель:</label>
            <?php echo $man_list ?>
		<br />
        <?php endif; ?>
		<label><?php echo JText::_('LIMIT_RAM') ?>:</label>
        <?php echo JHTML::_('select.genericlist',$memory_limits,'memory_limit','style="float: none;" size="1" ','value','text',$inputCookie->get('c_memory_limit', "0.7")) ?>

		<br />
		<label><?php echo JText::_('CATEGORY_ORDER') ?>:</label>

        <?php echo JHTML::_('select.genericlist',$cat_ordering,'order','style="float: none;" size="1" ','value','text',$inputCookie->get('c_order', "category_child_id")) ?>

		<br />
        <label>Статус товаров:</label>
        <?php echo JHTML::_('select.genericlist',$product_statuses,'product_status','style="float: none;" size="1" ','value','text',$inputCookie->get('c_product_status', "-1")) ?>

		<br />
        <label>Дочерние товары:</label>
        <?php echo JHTML::_('select.genericlist',$children_export_statuses,'children_export','style="float: none;" size="1" ','value','text',$inputCookie->get('c_children_export', "0")) ?>

		<br />
	   <label>	<?php echo JText::_('ROWS_LIMIT') ?>:</label> <input type="text" name="row_limit" id="row_limit" value="<?php echo $inputCookie->get('c_row_limit', "0") ?>" style="float: none"/> <br />


       <div id="paht_wrapper" <?php echo (!array_key_exists("file_url",$this->fields) AND !array_key_exists("file_url_thumb",$this->fields))?'style="display:none"':'' ?>>
		     <label><?php echo JText::_('PATH_TO_IMAGES_EXPORT') ?>:</label>
		     <fieldset style="width:150px;margin: 5px; text-align: left;">
             <input type="radio" <?php echo $inputCookie->get('c_image_path', 0)==0?'checked="checked"':'' ?> name="image_path" value="0" checked="checked" style="float: none"/> - <?php echo JText::_('FILE_NAME_ONLY') ?><br />
             <input type="radio" <?php echo $inputCookie->get('c_image_path', 0)==1?'checked="checked"':'' ?> name="image_path" value="1" style="float: none"/> - <?php echo JText::_('RELATIVE_PATH') ?><br />
             <input type="radio" <?php echo $inputCookie->get('c_image_path', 0)==2?'checked="checked"':'' ?> name="image_path" value="2" style="float: none"/> - <?php echo JText::_('ABSOLUTE_PATH') ?><br />
			 </fieldset>
		</div>

   <br style="clear:both " />
   <center><input style="float: none" type="button" id="export_button" value="<?php echo JText::_('START_EXPORT') ?>" />
   <br><br><input style="float: none;display:none" type="button" id="re_export_button" value="Продолжить с места обрыва" />
   </center>

</fieldset>
</form>
    <h3 id="import_started" style="display: none" align="center"><?php echo JText::_('EXPORT_STARTED') ?></h3>
    <div id="statistics" >
		<br />
		<span style='font-size: 18px;display: inline-block;text-align: left; margin: 15px 0;'>
			<?php echo JText::_('EXPORTED_ROWS') ?>:<strong id="row">0</strong><br />
			<?php echo JText::_('EXPORT_LASTS') ?>: <strong id="duration">0</strong><br />
			<?php echo JText::_('EXPORTED_CATEGORIES') ?>: <strong id="cat">0</strong><br />
			<?php echo JText::_('EXPORTED_PRODUCTS') ?>: <strong id="product">0</strong><br />
			<?php echo JText::_('CURRENT_CATEGORY') ?>: <strong id="current_cat"></strong><br />
			<?php echo JText::_('CURRENT_PRODUCT') ?>: <strong id="current_product"></strong><br />
			<?php echo JText::_('MEMORY_USAGE') ?>: <strong id="memory"></strong><br />
			<?php echo JText::_('CURRENT_OPERATION') ?>: <strong id="status"></strong><br />

		</span>
	</div>
    <div id="links" style="text-align: center"></div>
