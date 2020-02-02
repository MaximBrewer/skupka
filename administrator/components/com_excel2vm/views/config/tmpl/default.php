<?php
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
if(substr(JVERSION,0,1)==3){
    JHtml::_('bootstrap.tooltip');
}
else{
    JHTML::_('behavior.tooltip');
}
$params = JComponentHelper :: getParams("com_excel2vm");

JToolBarHelper::save('save_config',JText::_('SAVE') );
JToolBarHelper::save('save_profile',JText::_('SAVE_AS_PROFILE'));

JToolBarHelper::addNew('extra',JText::_('CUSTOM_FIELD'));
JToolBarHelper::addNew('extra_price',JText::_('CUSTOM_FIELD_PRICE'));
JToolBarHelper::addNew('multi',JText::_('Multi Variant'));
JToolBarHelper::addNew('price',JText::_('SPECIAL_PRICE'));
JToolBarHelper::addNew('sync',JText::_('SYNK'));
if($this->is_cherry){
    JToolBarHelper::addNew('cherry_field','Параметр Cherry Picker');
}
if($params->get('custom_fields')){
    JToolBarHelper::addNew('custom_field','Произвольное поле');
}
if($params->get('user_fields') AND in_array(8,$user->groups)){
    JToolBarHelper::addNew('user_field','Пользовательское поле');
}
JToolBarHelper::addNew('empty_field',JText::_('EMPTY_COLUMN'));
JToolBarHelper::divider();
//JToolBarHelper::custom('test_timeout','options','',JText::_('TIMEOUT_TEST'),false);
JToolBarHelper :: preferences('com_excel2vm',450);
$total_fields=count(@$this->active) + count(@$this->inactive);

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
$trash_link="<a href='javascript:void(0);' title='".JText::_('DELETE')."' class='ui-icon ui-icon-trash'>".JText::_('DELETE')."</a>";
$lang =JFactory::getLanguage();

$price_template[]= JHTML::_( 'select.option', '1', JText::_('NUMERIC_WITH_NAME') );
$price_template[]= JHTML::_( 'select.option', '4', JText::_('NUMERIC_WITHOUT_NAME') );
$price_template[]= JHTML::_( 'select.option', '2', JText::_('SPECIAL_SYMBOL_BEFORE_THE_NAME') );
$price_template[]= JHTML::_( 'select.option', '3', JText::_('SPECIAL_SYMBOL_AFTER_THE_NAME') );
$price_template[]= JHTML::_( 'select.option', '5', JText::_('CATEGORY_SEARCH_BY_KEYWORDS'));
$price_template[]= JHTML::_( 'select.option', '6', JText::_('CATEGORY_ID_FOR_EACH_PRODUCT') );
$price_template[]= JHTML::_( 'select.option', '7', JText::_('CATEGORY_NAME_FOR_EACH_PRODUCT') );
$price_template[]= JHTML::_( 'select.option', '8', JText::_('GROUPS_IN_EXCEL') );

$alias_template[]= JHTML::_( 'select.option', '1', JText::_('ALIAS_PRODUCT_NAME') );
$alias_template[]= JHTML::_( 'select.option', '2', JText::_('ALIAS_ID_PRODUCT_NAME') );
$alias_template[]= JHTML::_( 'select.option', '3', JText::_('ALIAS_PRODUCT_NAME_ID') );
$alias_template[]= JHTML::_( 'select.option', '4', JText::_('ALIAS_SKU_PRODUCT_NAME') );
$alias_template[]= JHTML::_( 'select.option', '5', JText::_('ALIAS_PRODUCT_NAME_SKU'));
$alias_template[]= JHTML::_( 'select.option', '6', JText::_('ALIAS_SKU_ID_PRODUCT_NAME') );
$alias_template[]= JHTML::_( 'select.option', '7', JText::_('ALIAS_ID_SKU_PRODUCT_NAME') );
$alias_template[]= JHTML::_( 'select.option', '8', JText::_('ALIAS_PRODUCT_NAME_SKU_ID') );
$alias_template[]= JHTML::_( 'select.option', '9', JText::_('ALIAS_PRODUCT_NAME_ID_SKU') );
$alias_template[]= JHTML::_( 'select.option', '10', JText::_('ALIAS_SKU') );
$alias_template[]= JHTML::_( 'select.option', '11', JText::_('ALIAS_ID') );

$key_field[]= JHTML::_( 'select.option', '0', 'ID товара' );
$key_field[]= JHTML::_( 'select.option', '1', 'Артикул' );
$key_field[]= JHTML::_( 'select.option', '2', 'Наименование' );
$key_field[]= JHTML::_( 'select.option', '3', 'GTIN (EAN,ISBN)' );
$key_field[]= JHTML::_( 'select.option', '4', 'MPN' );

$publish_new[]= JHTML::_( 'select.option', '0', "Не опубликован" );
$publish_new[]= JHTML::_( 'select.option', '1', "Опубликован" );

$publish_old[]= JHTML::_( 'select.option', '-1', "Не изменять" );
$publish_old[]= JHTML::_( 'select.option', '0', "Не опубликован" );
$publish_old[]= JHTML::_( 'select.option', '1', "Опубликован" );


$reset_resume[]=JHTML::_( 'select.option', '0', "В указанных категориях" );
$reset_resume[]=JHTML::_( 'select.option', '1', "Во всех, кроме указанных" );

$unpublish_resume[]=JHTML::_( 'select.option', '0', "В указанных категориях" );
$unpublish_resume[]=JHTML::_( 'select.option', '1', "Во всех, кроме указанных" );

$images_load[]= JHTML::_( 'select.option', '0', "Для новых товаров" );
$images_load[]= JHTML::_( 'select.option', '1', "Для товаров без изображений" );
$images_load[]= JHTML::_( 'select.option', '2', "Для всех товаров" );

$doc = JFactory::getDocument();
$doc->addScript(JURI::base()."components/com_excel2vm/js/chosen.jquery.min.js");
$doc->addScript(JURI::base()."components/com_excel2vm/js/config.js");
$doc->addStyleSheet(JURI::base()."components/com_excel2vm/assets/chosen.css");
$doc->addScriptDeclaration ( 'jQuery(document).ready(function(){jQuery(".chosen-select").chosen();});' );
//$list = @JHTML::_('select.genericlist',$this->categories,'unpublish_categories[]','data-placeholder="Выберите категории" class="chosen-select" multiple style="float: none;width: 220px;" size="1" ','category_child_id','category_name',@$this->config->unpublish_categories?$this->config->unpublish_categories:0);
$list = '<select name="unpublish_categories[]" data-placeholder="Выберите категорию" class="chosen-select" multiple style="float: none;width: 220px;" size="1" >'.$this->unpublish_categories.'</select>';
//$reset_list = @JHTML::_('select.genericlist',$this->categories,'reset_categories[]','data-placeholder="Выберите категории" class="chosen-select" multiple style="float: none;width: 220px;" size="1" ','category_child_id','category_name',@$this->config->reset_categories?$this->config->reset_categories:0);
$reset_list = '<select name="reset_categories[]" data-placeholder="Выберите категорию" class="chosen-select" multiple style="float: none;width: 220px;" size="1" >'.$this->reset_categories.'</select>';



$notify_show=$params->get('notify_show','fold');
$notify_hide=$params->get('notify_hide','explode');

if($notify_show=='none')$notify_show='';
if($notify_hide=='none')$notify_hide='';
$jtext_array=array('EMPTY_COLUMN','ADDED','DATA_NOT_SAVED','CUSTOM_COLUMN','CUSTOM_COLUMN_TITLE','CUSTOM_COLUMN_UNITS','CUSTOM_COLUMN_VALUE','COLUMN_CREATED','ADD_NEW','INPUT_THE_NAME_OF_THE_NEW_PROFILE','ERROR_COLUMN_DELETE','COLUMN_DELETED',);
?>
<script type="text/javascript">
var $notify_show = '<?php echo  $notify_show ?>';
var $notify_hide = '<?php echo  $notify_hide ?>';
var $trash_link = "<?php echo  $trash_link ?>";
<?php foreach($jtext_array as $jtext){
echo 'var $jtext_'.$jtext." = '".JText::_($jtext)."';\n";
}
?>
</script>

<style type="text/css">
.controls > .radio:first-child, .controls > .checkbox:first-child {
    padding-top: 0!important;
    padding-left: 13px;
}
input.ordering_input{
  width:auto!important;
}
#order_table{
  height: <?php echo  $total_fields*35+90 ?>px
}
#legend-fields li{
  padding: 2px
}
input[name="start"],input[name="end"]{
  width: 41px!important;
}


</style>
 <?php  if($this->versions->new_version AND str_replace(".","",$this->versions->my_version)<str_replace(".","",$this->versions->new_version)){
    echo '<pre style="text-align:left; font-size:16px;font-weight: bold;">';
    echo "<a href='".JURI::root()."administrator/index.php?option=com_excel2vm&view=support' target='_blank'>Доступна новая версия! - {$this->versions->new_version}</a><br>";
    echo $this->versions->description;
    echo '</pre>';
 }
 ?>
<div id="response_div" style="position:fixed; z-index: 50; top:200px; left:40%; padding: .7em; display: none" class="ui-state-highlight ui-corner-all">
				<span id="close" title="<?php echo  JText::_('CLOSE') ?>" class="ui-icon ui-icon-closethick" style="position: absolute; top: 0;right: 0; cursor: pointer"></span>
				<form action="index.php?option=com_excel2vm&view=config" id="ajax_form" method="POST">
					<p style="margin-bottom: 5px; text-align: center; font-size: 14px">
						<span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-info"></span>

						<span id="response"></span>
					</p>
				</form>
</div>

<h3><?php echo  JText::_('CURRENT_PROFILE') ?>: <span style="font-weight: bold; color: #006633" id="current_profile"><?php echo  $this->config->profile_name ?></span>
 &nbsp;|&nbsp;
 <form style="display: inline" action="index.php?option=com_excel2vm&view=config" method="POST" onsubmit="return confirm('<?php echo  JText::_('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THE_CURRENT_PROFILE') ?>');">
 <input type="submit" value="<?php echo  JText::_('DELETE') ?>" />
 <input type="hidden" name="task" value="delete_profile" />
  </form>
  &nbsp;|&nbsp;
  <form style="display: inline" action="index.php?option=com_excel2vm&view=config" method="POST">
 <input type="submit" value="<?php echo  JText::_('EXPORT_PROFILE') ?>" />
 <input type="hidden" name="task" value="export_profile" />
  </form>
  &nbsp;|&nbsp;

 <form style="display: inline" action="index.php?option=com_excel2vm&view=config" enctype="multipart/form-data" method="POST">
	 <input name="profile_file" value="" type="file" />
	 <input type="submit" value="<?php echo  JText::_('IMPORT_PROFILE') ?>" />
	 <input type="hidden" name="task" value="import_profile" />
 </form>



  </h3>
<form action="index.php?option=com_excel2vm&view=config" method="POST">
	<h3><?php echo  JText::_('CHANGE_TO') ?>: <?php echo JHTML::_('select.genericlist',$this->profiles,  'profile_id_value', 'size="1" onchange="this.form.submit()"','id','profile',$this->config->profile_id) ?></h3>

	<input type="hidden" name="task" value="change_profile" />
</form>
<div style="width: 1250px; float: left; background-color: #F4F4F4">
    <div style="width: 700px; float: left">
    	<fieldset>
    	    <legend><?php echo  JText::_('SETTING_THE_PRICE_COLUMN') ?></legend>
    		<table id="order_table">
    			<tr style="height: 30px">
    				<th><?php echo  JText::_('COLUMN_NOMBER') ?></th>
    				<th><?php echo  JText::_('ACTIVE_COLUMNS') ?></th>
    				<th><?php echo  JText::_('INACTIVE_COLUMNS') ?></th>
    			</tr>
    			<tr>
    				<td>
    					<ul style="height: <?php echo  $total_fields*32.5+18 ?>px" id="nombers">
    					    <?php     		                    for($i=0;$i<$total_fields;$i++)
    								echo "<li class=\"ui-state-highlight\">".getNameFromNumber($i). "(".($i+1).")</li>";
    						?>
    					</ul>
    				</td>
    				<td>
    		            <ul style="height: <?php echo  $total_fields*32.5+18 ?>px" id="active">
    					    <?php     		                   if($this->active)
    						   	  foreach($this->active as $f){
    						   	  	 $trash = !in_array($f->type,array('default','delete'))?$trash_link :'';
                                     echo "<li class=\"{$f->type}\" id=\"{$f->id}\">".JText::_($f->title)." $trash</li>";
    						   	  }

    						?>
    					</ul>

    				</td>
    				<td>
    					<ul style="height: <?php echo  $total_fields*32.5+18 ?>px" id="inactive">
    						<?php     		                   if($this->inactive)
    						   	  foreach($this->inactive as $f){
    						   	  	 $trash =!in_array($f->type,array('default','delete'))?$trash_link :'';
                                     echo "<li class=\"{$f->type}\" id=\"{$f->id}\">".JText::_($f->title)." $trash</li>";
    						   	  }
    						?>

    		           </ul>
    				</td>
    			</tr>
    			<tr>

                     <td></td>
    				 <td colspan="2">
                        <h3><?php echo  JText::_('LEGEND') ?>:</h3>
                        <ul id="legend-fields">
                            <li class="default" style="height: 17px"><?php echo  JText::_('STANDART_COLUMN') ?></li>
                            <li class="delete">Пометка на удаление</li>
                            <li class="price"><?php echo  JText::_('SPECIAL_PRICE') ?></li>
                            <li class="empty"><?php echo  JText::_('DO_NOT_IMPORT_COLUMN') ?></li>
    						<li class="extra"><?php echo  JText::_('CUSTOM_FIELD') ?></li>
                            <li class="extra-cart"><?php echo  JText::_('CUSTOM_FIELD_CART') ?></li>
                            <li class="extra-price"><?php echo  JText::_('PRICE_OF_CART_ATRIBUTE_VALUE') ?></li>
                            <li class="multi"><?php echo  JText::_('Multi-variant') ?></li>
                            <?php if($this->is_cherry): ?>
                            <li class="cherry"><?php echo  JText::_('Параметр Cherry Picker') ?></li>
                            <?php endif; ?>
                            <?php if($params->get('custom_fields')): ?>
                            <li class="custom"><?php echo  JText::_('Произвольные поля') ?></li>
                            <?php endif; ?>
                            <?php if($params->get('user_fields') AND in_array(8,$user->groups)): ?>
                            <li class="user-field"><?php echo  JText::_('Пользовательское поле') ?></li>
                            <?php endif; ?>

    					</ul>
    				 </td>
    			</tr>
    		</table>
    	</fieldset>
    </div>

    <div style="width: 520px; float: left; margin-left:15px ">
    	<fieldset class="panelform">
    	    <legend><?php echo  JText::_('IMPORT_EXPORT_SETTINGS') ?></legend>
             <form id="adminform" name="adminform" action="index.php" method="post">
    			<input type="hidden" name="option" value="com_excel2vm" />

    			<input type="hidden" name="view" value="config" />
             <ul  class="adminformlist">
                <fieldset class="config_groups">
                    <legend>Основные настройки</legend>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('KEY_FIELD_HINT'), JText::_('KEY_FIELD'),'',JText::_('KEY_FIELD')); ?></label>
                        <?php echo JHTML::_('select.genericlist',$key_field,  'key_field', 'size="1"','value','text', isset($this->config->key_field)?$this->config->key_field:1) ?>

                	</li>
                    <li>
                        <label>
                        <?php if(substr(JVERSION,0,1)==3): ?>
                        <span title="<?php echo  JText::_('CATEGORY_MARKUP_METHOD_HINT') ?>" data-placement="bottom" class="hasTooltip"><?php echo  JText::_('CATEGORY_MARKUP_METHOD') ?></span>
                        <?php else: ?>
                            <?php echo  JHTML::tooltip(JText::_('CATEGORY_MARKUP_METHOD_HINT'), JText::_('CATEGORY_MARKUP_METHOD'),'',JText::_('CATEGORY_MARKUP_METHOD')); ?>
                        <?php endif; ?>
                        </label>
                        <?php echo JHTML::_('select.genericlist',$price_template,  'price_template', 'size="1"','value','text', @$this->config->price_template) ?>

                	</li>
        			<li class="delimiters_li">
                        <label><?php echo  JHTML::tooltip(JText::_('LEVEL_DELIMITER_HINT'), JText::_('LEVEL_DELIMITER'),'',JText::_('LEVEL_DELIMITER')); ?></label>
                        <input class="ordering_input" type="text" name="level_delimiter"  size="32" maxlength="250" value="<?php echo  @$this->config->level_delimiter?$this->config->level_delimiter:'\\'?>" />
                	</li>
                    <li class="delimiters_li">
                        <label><?php echo  JHTML::tooltip(JText::_('CATEGORY_DELIMITER_HINT'), JText::_('CATEGORY_DELIMITER'),'',JText::_('CATEGORY_DELIMITER')); ?></label>
                        <input class="ordering_input" type="text" name="category_delimiter"  size="32" maxlength="250" value="<?php echo  @$this->config->category_delimiter?$this->config->category_delimiter:'|'?>" />
                	</li>
                    <li id="simbol_li">
                        <label><?php echo  JHTML::tooltip(JText::_('SPECIAL_SYMBOL_HINT'), JText::_('SPECIAL_SYMBOL'),'',JText::_('SPECIAL_SYMBOL')); ?></label>
                        <input class="ordering_input" type="text" name="simbol"  size="32" maxlength="250" value="<?php echo  @$this->config->simbol?$this->config->simbol:'#'?>" />
                	</li>
        			<li id="extra_category">
                        <label><?php echo  JHTML::tooltip(JText::_('CATEGORY_FOR_OTHER_PRODUCTS_HINT'), JText::_('CATEGORY_FOR_OTHER_PRODUCTS'),'',JText::_('CATEGORY_FOR_OTHER_PRODUCTS')); ?></label>
                        <input class="ordering_input" type="text" name="extra_category"  size="32" maxlength="250" value="<?php echo  @$this->config->extra_category?$this->config->extra_category:JText::_('OTHER_PRODUCTS')?>" />
                	</li>

        			<li>
                        <label><?php echo  JHTML::tooltip(JText::_('LANGUAGE_HINT'), JText::_('LANGUAGE'),'',JText::_('LANGUAGE')); ?></label>
                        <?php echo JHTML::_('select.genericlist',$this->languages,  'languege', 'size="1"','element','name', @$this->config->languege?$this->config->languege:$this->default_lang) ?>

                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('CURRENCY_HINT'), JText::_('CURRENCY'),'',JText::_('CURRENCY')); ?></label>
                        <?php echo JHTML::_('select.genericlist',$this->currencies,  'currency', 'size="1"','virtuemart_currency_id','currency_name', @$this->config->currency?$this->config->currency:150) ?>

                	</li>

                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('CURRENCY_RATE_HINT'), JText::_('CURRENCY_RATE'),'',JText::_('CURRENCY_RATE')); ?></label>
                        <input class="ordering_input" type="text" name="currency_rate"  size="32" maxlength="250" value="<?php echo  @$this->config->currency_rate?$this->config->currency_rate:1?>" />
                	</li>

        			<li>
                        <label><?php echo  JHTML::tooltip(JText::_('ALIAS_METHOD_HINT'), JText::_('ALIAS_METHOD'),'',JText::_('ALIAS_METHOD')); ?></label>
                        <?php echo JHTML::_('select.genericlist',$alias_template,  'alias_template', 'size="1"','value','text', @$this->config->alias_template?$this->config->alias_template:2) ?>

                	</li>
        			<li>
                        <label><?php echo  JHTML::tooltip(JText::_('FIRST_HINT'), JText::_('FIRST_ROW_NOMBER'),'',JText::_('FIRST_ROW_NOMBER')); ?></label>
                        <input class="ordering_input" type="text" name="first"  size="32" maxlength="250" value="<?php echo  @$this->config->first?$this->config->first:2?>" />
                	</li>

                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('LAST_HINT'), JText::_('LAST_ROW_NOMBER'),'',JText::_('LAST_ROW_NOMBER')); ?></label>
                        <input class="ordering_input" type="text" name="last"  size="32" maxlength="250" value="<?php echo  @$this->config->last?$this->config->last:'все'?>" />
                	</li>


        			<li>
                        <label><?php echo  JHTML::tooltip(JText::_('COLUMN_NUMBER_HINT'), JText::_('CATEGORY_NAME_COLUMN'),'',JText::_('CATEGORY_NAME_COLUMN')); ?></label>
                        <input class="ordering_input" type="text" name="cat_col"  size="32" maxlength="250" value="<?php echo  @$this->config->cat_col?$this->config->cat_col:1?>" />
                	</li>
                    <li  class="cat_id_col">
                        <label><?php echo  JHTML::tooltip(JText::_('CATEGORY_ID_COLUMN_HINT'), JText::_('CATEGORY_ID_COLUMN'),'',JText::_('CATEGORY_ID_COLUMN')); ?></label>
                        <input class="ordering_input" type="text" name="cat_id_col"  size="32" maxlength="250" value="<?php echo  @$this->config->cat_id_col?$this->config->cat_id_col:0?>" />
                	</li>
                </fieldset>

               <fieldset class="config_groups">
               <legend>Бэкап</legend>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('AUTO_BACKUP_HINT'), JText::_('AUTOMATIC_BACKUP_BEFORE_IMPORT'),'',JText::_('AUTOMATIC_BACKUP')); ?></label>
                        <fieldset class="radio btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'auto_backup', '', @$this->config->auto_backup) ?>
        				</fieldset>

                	</li>

        			<li>
                        <label><?php echo  JHTML::tooltip(JText::_('AUTO_BACKUP_TYPE_HINT'), JText::_('AUTOMATIC_BACKUP_TYPE'),'',JText::_('AUTOMATIC_BACKUP_TYPE')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'backup_type', '', @$this->config->backup_type,"gzip","sql") ?>
        				</fieldset>
                	</li>
                </fieldset>
                <fieldset class="config_groups">
                    <legend>Новые товары</legend>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('ONLY_UPDATE_PRODUCTS_HINT'), JText::_('ADD_NEW_PRODUCTS'),'',JText::_('ADD_NEW_PRODUCTS')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'create', '', @$this->config->create) ?>
        				</fieldset>
                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('CREATE_WITHOUT_CATEGORY_HINT'), JText::_('CREATE_WITHOUT_CATEGORY'),'',JText::_('CREATE_WITHOUT_CATEGORY')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'create_without_category', '', @$this->config->create_without_category) ?>
        				</fieldset>
                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('CREATE_WITHOUT_SKU_HINT'), JText::_('CREATE_WITHOUT_SKU'),'',JText::_('CREATE_WITHOUT_SKU')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'create_without_sku', '', @$this->config->create_without_sku) ?>
        				</fieldset>
                	</li>
                </fieldset>
                <fieldset class="config_groups">
                    <legend>Обновление товаров</legend>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('IS_UPDATE_HINT'), JText::_('IS_UPDATE'),'',JText::_('IS_UPDATE')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        <?php echo JHTML::_('select.booleanlist',  'is_update', '', isset($this->config->is_update)?$this->config->is_update:1) ?>
        				</fieldset>
                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('MULTY_CATEGORY_HINT'), JText::_('MULTI_CATEGORY_SUPPORT_TITLE'),'',JText::_('MULTI_CATEGORY_SUPPORT')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'multicategories', '', @$this->config->multicategories) ?>
        				</fieldset>
                	</li>

        			<li>
                        <label><?php echo  JHTML::tooltip(JText::_('CHANGE_CATEGORY_HINT'), JText::_('HANDLE_BELONGING_TO_THE_CATEGORIES'),'',JText::_('HANDLE_BELONGING_TO_THE_CATEGORIES')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'change_category', '', @$this->config->change_category) ?>
        				</fieldset>
                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('UPDATE_WITHOUT_SKU_HINT'), JText::_('UPDATE_WITHOUT_SKU'),'',JText::_('UPDATE_WITHOUT_SKU')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'update_without_sku', '', @$this->config->update_without_sku) ?>
        				</fieldset>
                	</li>
                </fieldset>
                <fieldset class="config_groups">
                    <legend>Статус публикации</legend>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('AUTO_UNPUBLISH_HINT'), JText::_('UNPUBLISH_PRODUCTS'),'',JText::_('UNPUBLISH_PRODUCTS')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'unpublish', '', @$this->config->unpublish) ?>
        				</fieldset>
                	</li>
                    <li class="unpublish_cats">
                        <label><?php echo  JHTML::tooltip(JText::_('AUTO_UNPUBLISH_RESUME_HINT'), JText::_('Режим'),'',JText::_('Режим')); ?></label>


                            <?php echo  JHTML::_('select.genericlist',$unpublish_resume,'unpublish_resume','size="1"','value','text',@$this->config->unpublish_resume); ?>
                	</li>
                    <li class="unpublish_cats">
                        <label><?php echo  JHTML::tooltip(JText::_('AUTO_UNPUBLISH_CAT_HINT'), JText::_('UNPUBLISH_CAT_PRODUCTS'),'',JText::_('UNPUBLISH_CAT_PRODUCTS')); ?></label>

                        	<?php echo $list ?>

                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('PUBLICATION_STATUS_HINT'), JText::_('PUBLICATION_STATUS'),'',JText::_('PUBLICATION_STATUS')); ?></label>
                        <?php echo JHTML::_('select.genericlist',$publish_new,  'published', 'size="1"','value','text', isset($this->config->published)?$this->config->published:1) ?>

                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('PUBLICATION_STATUS_OLD_HINT'), JText::_('PUBLICATION_STATUS_OLD'),'',JText::_('PUBLICATION_STATUS_OLD')); ?></label>
                        <?php echo JHTML::_('select.genericlist',$publish_old,  'published_old', 'size="1"','value','text', isset($this->config->published_old)?$this->config->published_old:-1) ?>

                	</li>

                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('UNPUBLISH_PRODUCTS_WITHOUT_IMAGE_HINT'), JText::_('UNPUBLISH_PRODUCTS_WITHOUT_IMAGE'),'',JText::_('UNPUBLISH_PRODUCTS_WITHOUT_IMAGE')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'unpublish_image', '', @$this->config->unpublish_image) ?>
        				</fieldset>
                	</li>


                </fieldset>
                <fieldset class="config_groups">
                    <legend>Количество на складе</legend>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('QUANTITY_IN_STOCK_DEFAULT_HINT'), JText::_('QUANTITY_IN_STOCK_DEFAULT'),'',JText::_('QUANTITY_IN_STOCK_DEFAULT')); ?></label>
                        <input class="ordering_input" type="text" name="product_in_stock_default"  size="15" maxlength="10" value="<?php echo  isset($this->config->product_in_stock_default)?$this->config->product_in_stock_default:10?>" />
                	</li>

                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('RESET_QUANTITY_IN_STOCK_HINT'), JText::_('RESET_QUANTITY_IN_STOCK'),'',JText::_('RESET_QUANTITY_IN_STOCK')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'reset_stock', '', @$this->config->reset_stock) ?>
        				</fieldset>
                	</li>
                    <li class="reset_cats">
                        <label><?php echo  JHTML::tooltip(JText::_('RESET_QUANTITY_IN_RESUME_HINT'), JText::_('Режим'),'',JText::_('Режим')); ?></label>


                            <?php echo  JHTML::_('select.genericlist',$reset_resume,'reset_resume','size="1"','value','text',@$this->config->reset_resume); ?>
                	</li>
                    <li class="reset_cats">
                        <label><?php echo  JHTML::tooltip(JText::_('RESET_QUANTITY_IN_STOCK_CAT_HINT'), JText::_('RESET_QUANTITY_IN_STOCK_CAT'),'',JText::_('RESET_QUANTITY_IN_STOCK_CAT')); ?></label>

                        	<?php echo $reset_list ?>

                	</li>
                </fieldset>
                <fieldset class="config_groups">
                    <legend>Очистка доп. полей</legend>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('DELETE_RELATED_HINT'), JText::_('DELETE_related'),'',JText::_('DELETE_RELATED')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'delete_related', '', @$this->config->delete_related) ?>
        				</fieldset>
                	</li>


        			<li>
                        <label><?php echo  JHTML::tooltip(JText::_('SPEC_PRICE_CLEAR_HINT'), JText::_('SPEC_PRICE_CLEAR'),'',JText::_('SPEC_PRICE_CLEAR')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'spec_price_clear', '', @$this->config->spec_price_clear) ?>
        				</fieldset>
                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('EXTRA_FIELDS_CLEAR_HINT'), JText::_('EXTRA_FIELDS_CLEAR'),'',JText::_('EXTRA_FIELDS_CLEAR')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno">
                        	<?php echo JHTML::_('select.booleanlist',  'extra_fields_clear', '', @$this->config->extra_fields_clear) ?>
        				</fieldset>
                	</li>
                </fieldset>
                <fieldset class="config_groups">
                    <legend>Изображения</legend>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('IMAGES_IMPORT_METHOD_HINT'), JText::_('IMAGES_IMPORT_METHOD'),'',JText::_('IMAGES_IMPORT_METHOD')); ?></label>
                        <fieldset class="radio  btn-group btn-group-yesno full_width">
                        	<?php echo JHTML::_('select.booleanlist',  'images_import_method', '', @$this->config->images_import_method,JText::_('IMAGES_IN_PRICELIST'),JText::_('FILE_NAME_IN_PRICELIST')) ?>
        				</fieldset>
                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip(JText::_('IMAGES_LOAD_HINT'), JText::_('IMAGES_LOAD'),'',JText::_('IMAGES_LOAD')); ?></label>
                        <?php echo  JHTML::_('select.genericlist',$images_load,'images_load','size="1"','value','text',@$this->config->images_load); ?>
                	</li>
        			<li>
                        <label><?php echo  JHTML::tooltip(JText::_('PATH_HINT'), JText::_('PATH_TO_IMAGES'),'',JText::_('PATH_TO_IMAGES')); ?></label>
                        <input class="ordering_input" type="text" name="path"  size="40" maxlength="250" value="<?php echo  @$this->config->path?$this->config->path:'images/stories/virtuemart/product/'?>" />
                	</li>

        			<li>
                        <label><?php echo  JHTML::tooltip(JText::_('THUMB_PATH_HINT'), JText::_('PATH_TO_THUMBS'),'',JText::_('PATH_TO_THUMBS')); ?></label>
                        <input class="ordering_input" type="text" name="thumb_path"  size="40" maxlength="250" value="<?php echo  @$this->config->thumb_path?$this->config->thumb_path:'images/stories/virtuemart/product/resized/'?>" />
                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip('Вы можете указать название файла изображения&#44; которое будет присваиваться всем товарам&#44; которым в прайсе не указано никакое изображение. Вы можете указать только название файла или полную ссылку на него. Данная настройка имеет более высокий приоритет, чем в глобальных настройках', 'Изображение по-умолчанию для товаров','','Изображение по-умолчанию для товаров'); ?></label>
                        <input class="ordering_input" type="text" name="images_products_default"  size="40" maxlength="250" value="<?php echo  @$this->config->images_products_default ?>" />
                	</li>
                    <li>
                        <label><?php echo  JHTML::tooltip('Вы можете указать название файла изображения&#44; которое будет присваиваться всем категориям&#44; которым в прайсе не указано никакое изображение. Вы можете указать только название файла или полную ссылку на него. Данная настройка имеет более высокий приоритет, чем в глобальных настройках', 'Изображение по-умолчанию для категорий','','Изображение по-умолчанию для категорий'); ?></label>
                        <input class="ordering_input" type="text" name="images_categories_default"  size="40" maxlength="250" value="<?php echo  @$this->config->images_categories_default ?>" />
                	</li>

                </fieldset>




                <li>
                    <label><?php echo  JHTML::tooltip(JText::_('PRICE_LIST_HINT_HINT'), JText::_('PRICE_LIST_HINT'),'',JText::_('PRICE_LIST_HINT')); ?></label>
                    <fieldset class="radio  btn-group btn-group-yesno">
                    	<?php echo JHTML::_('select.booleanlist',  'price_hint', '', @$this->config->price_hint) ?>
    				</fieldset>
            	</li>
             </ul>
    		 <input type="hidden" name="fields_list" id="fields_list" value="1,2,3" />
    		 <input type="hidden" name="new_profile_name" id="new_profile_name" value="" />
    		 <input type="hidden" name="profile_id_value" id="profile_id2" value="<?php echo  $this->config->profile_id ?>" />
    	   </form>

    	</fieldset>
    </div>
</div>

