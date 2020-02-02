<?php
defined('_JEXEC') or die('Restricted access');
$view=JRequest :: getVar('view','excel2vm','','string');
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root()."administrator/components/com_excel2vm/assets/sorter.css");
$doc->addScript(JURI::base()."components/com_excel2vm/js/import.js");
if(substr(JVERSION,0,1)==3){
    JHtml::_('bootstrap.tooltip');
}
else{
    JHTML::_('behavior.tooltip');
}
$post_max_size=ini_get('post_max_size');
$upload_max_filesize=ini_get('upload_max_filesize');
$max_size=(int)$post_max_size<(int)$upload_max_filesize?$post_max_size:$upload_max_filesize;
$params = JComponentHelper :: getParams("com_excel2vm");
$debug=$params->get('debug',0);
$limit_messages=$params->get('limit_messages',1);
$reimport_time=(int)$params->get('reimport_time',10);
$reimport_time++;
if($reimport_time>15){
     $reimport_time==15;
}
$reimport_num=(int)$params->get('reimport_num',10);
$reimport_num--;
$inputCookie  = JFactory::getApplication()->input->cookie;
$show_results = $inputCookie->get('showResults', 1);
function getSize($bytes){
   if($bytes<1024)
   	  return $bytes." B<br>";
   elseif($bytes<1024*1024)
   	  return round($bytes/1024)." KB<br>";
   else
   	  return round($bytes/(1024*1024),2)." MB<br>";
}

$total_fields = count($this->fields);
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
 <style type="text/css">
fieldset.panelform{
  width: 550px!important;
}
#uploaded_files_table{
   margin: 5px auto;
}
#uploaded_files_table td,#uploaded_files_table th{
   text-align: center;
   padding:4px;
}
#uploaded_files_table label{
  width:100%!important;
  max-width: 100%!important;
}
 </style>
<?php $jtext_array=array('IMPORT_ERROR','IMPORT_CONTINUE','IMPORT_OF','SERVER_LAST_RESPONSE','SECONDS_AGO','TIME_LEFT','SECONDS','RATE','ROWS_PER_SECOND','MEMORY_USAGE','MB','FROM','START_IMPORT');
?>
 <script type="text/javascript">
 var $save_checked= <?php echo @$_GET['checked']?'true':'false' ?>;
 var $uploaded_files= <?php echo count($this->uploaded_files) ?>;
 var $reimport_time= <?php echo $reimport_time ?>;
 var $reimport_num= <?php echo $reimport_num ?>;
 var $JURI_root= '<?php echo JURI::root() ?>';
 <?php foreach($jtext_array as $jtext){
echo 'var $jtext_'.$jtext." = '".JText::_($jtext)."';\n";
}
?>
 </script>

 <?php  if($this->versions->new_version AND str_replace(".","",$this->versions->my_version)<str_replace(".","",$this->versions->new_version)){
    echo '<pre style="text-align:left; font-size:16px;font-weight: bold;">';
    echo "<a href='".JURI::root()."administrator/index.php?option=com_excel2vm&view=support' target='_blank'>Доступна новая версия! - {$this->versions->new_version}</a><br>";
    echo $this->versions->description;
    echo '</pre>';
 }
 ?>
<div style="position:relative">
 <h3><?php echo JText::_('CURRENT_PROFILE') ?>: <span style="font-weight: bold; color: #006633" id="current_profile"><?php echo $this->config->profile_name ?></span></h3>
  <div id="version" style="position: absolute; top:5px;right: 5px"><?php echo JText::_('JVERSION') ?>: <?php echo $this->versions->my_version ?><br><span id="reimport_counter"></span></div>
</div>
  <form action="index.php?option=com_excel2vm" method="POST">
	<h3><?php echo JText::_('CHANGE_TO') ?>: <?php echo JHTML::_('select.genericlist',$this->profiles,  'profile_id', 'size="1" onchange="this.form.submit()"','id','profile',$this->config->profile_id) ?></h3>
	<input type="hidden" name="task" value="change_profile" />
</form>

 <?php


 if($this->config->price_hint):
switch ($this->config->price_template) {
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
  case 5:$categories=array('','');
  break;
  case 6:$categories=array('','');
		if(!isset($this->fields['path']))echo '<font size="2" color="#FF0000">'.JText::_('YOU_DID_NOT_SPECIFY_THE_COLUMN_NUMBER_CATEGORY').'</font>';
  break;
  case 7:$categories=array('','');
		if(!isset($this->fields['path']))echo '<font size="2" color="#FF0000">'.JText::_('YOU_DID_NOT_SPECIFY_THE_COLUMN_NUMBER_CATEGORY').'</font>';
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
	 	echo "<td class=\"ui-state-highlight center bold\">".getNameFromNumber($i)." (".($i+1).")</td>";
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

<style type="text/css">
.wide{
	width: 300px!important;
	max-width: 300px!important;
	text-align: left;
}
.small{width: 80px!important;max-width: 100px!important;}

</style>
<h1 align="center"><?php echo JText::_('IMPORT') ?></h1>
<fieldset class="panelform" style="width:800px;margin: 10px auto;">
<?php echo JHtml::_('sliders.start', 'import-sliders', array('useCookie'=>1)); ?>
<?php echo JHtml::_('sliders.panel', JText::_('ENTER_THE_XLS_FILE_ON_YOUR_COMPUTER'), 'local'); ?>
<form id="upload_form" action="index.php" method="POST" enctype="multipart/form-data">

	<input type="hidden" name="option" value="com_excel2vm" />
	<input type="hidden" name="task" value="upload" />




	<center>
    <?php echo JHTML::tooltip('Для увеличения лимита, Вам необходимо увеличить параметры <b>post_max_size</b> и <b>upload_max_filesize</b> в настройках сервера', 'Максимальный размер файла загрузки - '.$max_size,'','<span style="color: #CC6600; font-weight: bold;text-decoration:none; border-bottom:#F00 1px dashed;">Максимальный размер файла загрузки - '.$max_size.'</span>'); ?>
   <?php if(!is_writable(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls')): ?>
        <br><br><?php echo JHTML::tooltip('Для того, чтобы прайс мог быть загружен на сервер, необходимо на папку /administrator/components/com_excel2vm/xls/ установить права 775 или 777 (если уже установлено 775 или 755)', 'Папка НЕ доступна для записи','','<span style="color: #CC0000; font-weight: bold;text-decoration:none; border-bottom:#F00 1px dashed;">Внимание! Папка <b>/administrator/components/com_excel2vm/xls/</b> НЕ доступна для записи</span>'); ?>
   <?php endif; ?>
   <br>
    <input id="xls_file" name="xls_file[]" type="file" size="30" style="margin: 5px 5px 5px 173px" multiple=""/><?php echo JHTML::tooltip (JText::_('MULTI_UPLOAD_HINT'), JText::_('MULTI_UPLOAD'), 'tooltip.png'); ?>
    </center>
</form>
<?php echo JHtml::_('sliders.panel', "<strong>".JText::_('OR')."</strong> ".JText::_('SELECT_THE_DOWNLOADED'), 'loaded'); ?>
<form id="import_form" action="index.php" method="POST"  enctype="multipart/form-data">

<input type="hidden" name="option" value="com_excel2vm" />
<input type="hidden" name="task" value="import" />

                <table style="border-collapse: collapse" id="uploaded_files_table" class="tablesorter">
                <thead>
                  <tr>
                    <th>Импорт</th>
                    <th>Файл</th>
                    <th style="width: 62px">Размер</th>
                    <th>Дата</th>
                    <th></th>
                    <th><img style="cursor: pointer" title="Удалить все"  class="delete_all" src="<?php echo JURI::base() ?>/components/com_excel2vm/assets/images/delete.png" width="16" height="16" alt=""></th>
                  </tr>
                </thead>
                <tbody id="uploaded_files_tbody">
                <?php foreach($this->uploaded_files as $key=>$f): ?>
                  <tr id="row_<?php echo $key ?>">
                    <td><input name="uploaded_file[]" id="uploaded_file_<?php echo $key ?>" type="checkbox" value="<?php echo $f->file ?>" style="margin-left: 14px"></td>
                    <td><label for="uploaded_file_<?php echo $key ?>"><?php echo $f->file ?></label></td>
                    <td><?php echo getSize($f->size) ?></td>
                    <td><?php echo date("Y-m-d H:i",$f->time) ?></td>
                    <td><a href="index.php?option=com_excel2vm&task=download&file=<?php echo $f->file ?>"><img src="<?php echo JURI::base() ?>/components/com_excel2vm/assets/images/download.png" width="16" height="16" alt=""></a></td>
                    <td><img style="cursor: pointer" rel="<?php echo $key ?>" file="<?php echo $f->file ?>"  class="delete" src="<?php echo JURI::base() ?>/components/com_excel2vm/assets/images/delete.png" width="16" height="16" alt=""></td>
                  </tr>

                <?php endforeach; ?>
                </tbody>
              </table>

       <?php echo JHtml::_('sliders.end'); ?>
       <?php echo JHtml::_('sliders.start', 'images-sliders', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1)); ?>
       <?php echo JHtml::_('sliders.panel', "Импорт изображений", 'image'); ?>
       <ul  class="adminformlist" style="text-align: center; margin: 10px auto; width: 200px">
		<li><label class="wide" for="zip_file"><?php echo JText::_('ENTER_YOUR_ZIP_FILE_WITH_THE_IMAGES_ON_YOUR_COMPUTER') ?>:</label>&nbsp;<input id="zip_file" name="zip_file" type="file" size="30" /><br /><br /></li>
        <li><label  class="wide" for="make_thumb"><?php echo JText::_('MAKE_THUMBS') ?>?</label>
			<fieldset class="radio  btn-group btn-group-yesno">
                	<?php echo JHTML::_('select.booleanlist',  'make_thumb', '', $inputCookie->get('thumb_make_thumb', 0)) ?>
			</fieldset>
		 </li>

        <li style="clear: left;width: 210px;margin: 5px auto;">


		 <label class="small"><?php echo JText::_('HEIGHT') ?>:</label><input name="height" type="text" size="5" value="<?php echo $inputCookie->get('thumb_height', ''); ?>" /><br />
		 <label class="small"><?php echo JText::_('WIDTH') ?>:</label><input name="width" type="text" size="5" value="<?php echo $inputCookie->get('thumb_width', 200); ?>" /><br />
		 <label class="small"><?php echo JText::_('PREFIX') ?>:</label> <input name="prefix" type="text" size="8" value="<?php echo $inputCookie->get('thumb_prefix', 'thumb_'); ?>" /><br />
		 <label class="small"><?php echo JText::_('SUFIX') ?>:</label> <input name="sufix" type="text" size="8" value="<?php echo $inputCookie->get('thumb_sufix', '_200x200'); ?>" /><br />



		</li>
	<br  clear="both"/>

   </ul>
   <?php echo JHtml::_('sliders.end'); ?>

   <ul  class="adminformlist" style="text-align: center; margin: 10px auto; width: 400px">
        <li style="width: 400px;margin: 0 auto"><input id="show_results" name="show_results" type="checkbox" value="1" <?php echo $show_results?'checked="checked"':'' ?> >&nbsp;<label for="show_results" style="width:380px!important;max-width:380px!important;float:none!important;display: inline;line-height: 21px;">Вывести таблицу со всеми товарами после окончания</label> </li><br>
        <li style="width: 110px;margin: 0 auto"><input style="float: none" type="button" id="import_button" value="<?php echo JText::_('START_IMPORT') ?>" /></li>
   <li style="width: 110px;margin: 0 auto"><input style="float: none;display:none" type="button" id="abort_button" value="<?php echo JText::_('ABORT_IMPORT') ?>" /></li>
   </ul>


</form>
</fieldset>
    <h3 id="import_started" style="display: none" align="center"><?php echo JText::_('IMPORT_STARTED') ?>...</h3>
    <div id="statistics">
	    <div id="progresspercent"></div>
	    <div id="progressbar"></div>
        <div id="time_left"></div>
        <div id="speed"></div>
        <div id="step"></div>
        <div id="memory"></div>
		<br />
		<span style='font-size: 18px;display: inline-block;text-align: left; margin: 15px 0;width: 500px;'>
		    <?php echo JText::_('THE_IMPORT_FILE') ?>: <strong id="filename"></strong><br />
			<?php echo JText::_('IMPORTED_ROWS') ?>:<strong id="row">0</strong> <?php echo JText::_('FROM') ?> <strong id="total_row">0</strong> <br />
			<?php echo JText::_('IMPORT_TAKES') ?>: <strong id="duration">0</strong><br />
			<?php echo JText::_('NEW_PRODUCTS') ?>: <strong id="new">0</strong><br />
			<?php echo JText::_('UPDATED_PRODUCTS') ?>: <strong id="up">0</strong><br />
			<?php echo JText::_('NEW_CATEGORIES') ?>: <strong id="new_cat">0</strong><br />
			<?php echo JText::_('UPDATED_CATEGORIES') ?>: <strong id="up_cat">0</strong><br />
			<?php echo JText::_('CURRENT_CATEGORY') ?>: <strong id="category"></strong><br />
			<?php echo JText::_('CURRENT_PRODUCT') ?>: <strong id="product"></strong>
		</span>
	</div>
    <div id="results" style="text-align: center"></div>

    <div id="last_response"></div>

