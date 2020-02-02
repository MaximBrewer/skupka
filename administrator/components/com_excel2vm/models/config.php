<?php


defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
require_once (dirname(__FILE__).DS."updateTable.php");


class Excel2vmModelConfig extends JModelLegacy {
	public $pagination;

	function __construct() {
		parent :: __construct();


		$this->params = JComponentHelper :: getParams("com_excel2vm");
		$debug=$this->params->get('db_debug',0);
		$this->_db->debug($debug);
		$this->table = new updateTable("#__excel2vm", "id");
		$this->core = new core();

		$this->config=$this->core->getConfig();
		$this->active_fields =$this->core->active_fields;
		$this->profile =$this->core->profile;
		$tables=$this->_db->getTableColumns("#__excel2vm_fields", false);
				$this->is_cherry=$this->is_cherry();
	}

	function getCurrencies(){
		$this->_db->setQuery("SELECT virtuemart_currency_id, currency_name FROM #__virtuemart_currencies ORDER BY virtuemart_currency_id");
		return $this->_db->loadObjectList('virtuemart_currency_id');
	}

	function getLanguages(){
		require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."config.php");
		$VmConfig=VmConfig::loadConfig();
		$VmConfig=$VmConfig->_params;

		/*$temp=explode("_",$VmConfig['vmlang']);

		$this->default_lang=$temp[0]."-".strtoupper($temp[1]);*/

		foreach($VmConfig['active_languages'] as $name){
		   $xmlfile= JPATH_ADMINISTRATOR .DS.'language'.DS.$name.DS.$name.".xml";
		   $xml = JFactory::getXML($xmlfile);
		   $title=$xml->name;
		   $languages[]=JHTML::_('select.option',  $name, $title, 'element', 'name' );
		}

		if(count(@$languages))return $languages;
		try{
		  $this->_db->setQuery("SELECT lang_code as element,title as name FROM #__languages ORDER BY title");
		  $languages=$this->_db->loadObjectList();
		}
		catch(Exception $e){
		   $this->_db->setQuery("SELECT DISTINCT element, name FROM #__extensions WHERE type='language'AND enabled=1 ORDER BY name");
		   $languages=$this->_db->loadObjectList();
		}

		if($languages){
		   $rus=false;
		   foreach($languages as $key => $v){
			  if($v->element=='ru-RU'){
				 $rus=true;
			  }
		   }
		   if(!$rus){
			   $languages[]=JHTML::_('select.option',  'ru-RU', "Russian", 'element', 'name' );
		   }
		   return $languages;
		}
		else{
		   $languages[]=JHTML::_('select.option',  'en-GB', "English", 'element', 'name' );
		   $languages[]=JHTML::_('select.option',  'ru-RU', "Russian", 'element', 'name' );
		   return $languages;
		}
	}

	function getGroups(){
		$this->_db->setQuery("SELECT virtuemart_shoppergroup_id, CONCAT(shopper_group_name,' (ID:',virtuemart_shoppergroup_id,')') as shopper_group_name FROM #__virtuemart_shoppergroups WHERE published = '1' ORDER BY `default` DESC , virtuemart_shoppergroup_id ASC");
		$groups=$this->_db->loadObjectList();
		echo $this->_db->getErrorMsg();
		if(!$groups){
		   $this->_db->setQuery("SELECT shopper_group_id as virtuemart_shoppergroup_id, CONCAT(shopper_group_name,' (ID:',shopper_group_id,')') as shopper_group_name FROM #__virtuemart_shoppergroups ORDER BY `default` DESC,shopper_group_id");
			$groups=$this->_db->loadObjectList();
		}
		array_unshift($groups,JHTML::_('select.option',  '',JText::_('CHOOSE'), 'virtuemart_shoppergroup_id', 'shopper_group_name' ));

		return $groups;

	}

	function getCategoryList($selected_cat){
		 if(!file_exists(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."shopfunctions.php")){
			 JError::raiseError('',"Установите VirtueMart 2 - 3");
			  return false;

		}
		else{
			require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."shopfunctions.php");
			try{
				return ShopFunctions::categoryListTree((array)$selected_cat);
			}
			catch(Exception $e){
							  return false;
			}

	}

		/*if($parent_id==0)$this->list[]=JHTML::_('select.option',  '0', JText::_('ALL'), 'category_child_id', 'category_name' );

		   $this->_db->setQuery("SELECT cc.category_child_id, category_name,slug,category_description as product_desc, (SELECT COUNT(id) FROM #__virtuemart_product_categories WHERE virtuemart_category_id = cc.category_child_id) as products
								  FROM #__virtuemart_category_categories as cc
								  LEFT JOIN  #__virtuemart_categories_".$this->config->sufix." as c ON c.virtuemart_category_id = cc.category_child_id
								  WHERE category_name IS NOT NULL AND category_name !='' AND cc.category_parent_id ='$parent_id'
								  ORDER BY cc.category_child_id");
			 $categories=$this->_db->loadObjectList('category_child_id');

			 if(!$categories)
			 	return false;

			 foreach($categories as $id => $cat){
				 $this->list[]=JHTML::_('select.option',  $id,(!$parent_id?'':$prefix).$cat->category_name." ($cat->products)", 'category_child_id', 'category_name' );
				 $this->getCategoryList($id,'&nbsp;.&nbsp;'.$prefix);
			 }
			 return $this->list;*/
	}

	function delete_profile(){
		$this->_db->setQuery("SELECT COUNT(id) FROM #__excel2vm");
		if($this->_db->loadResult() < 2){
			return JText::_('YOU_CAN_NOT_DELETE_THE_LAST_PROFILE');
		}
		if($this->table->delete($this->profile))
			return JText::_('PROFILE_DELETED');
		else
			return JText::_('AN_ERROR_OCCURRED_WHILE_DELETING_A_PROFILE');
	}

	function getActive() {
		$this->active_fields=$this->active_fields?$this->active_fields:1;
		$query = "SELECT *
				FROM #__excel2vm_fields
				WHERE id IN({$this->active_fields})
				ORDER BY FIELD(id,{$this->active_fields})";

		return $this->_getList($query);
	}

	function getInactive() {
		$query = "SELECT *
				FROM #__excel2vm_fields
				WHERE id NOT IN({$this->active_fields})
				ORDER BY id";

		return $this->_getList($query);
	}

	function save_config() {
	   $_POST['path']=substr($_POST['path'],-1)!='/'?$_POST['path'].'/':$_POST['path'];
	   $_POST['thumb_path']=substr($_POST['thumb_path'],-1)!='/'?$_POST['thumb_path'].'/':$_POST['thumb_path'];
	   $_POST['last']=((int)$_POST['last'] ==0 AND $_POST['last']!='все')?'все':$_POST['last'];

	   $active=$_POST['fields_list'];
	   unset($_POST['fields_list']);
	   unset($_POST['option']);
	   unset($_POST['task']);
	   unset($_POST['view']);

	   $config=serialize((object)$_POST);

	   $this->_db->setQuery("UPDATE #__excel2vm SET config=".$this->_db->Quote($config).",active='$active' WHERE default_profile=1");
	   $this->_db->Query();

	   echo JText::_('DATA_UPDATED_SUCCESSFULLY');
	   exit();
	}

	function delete_field(){
		$id=JRequest::getVar('id', '', '', 'int');
		$this->_db->setQuery("DELETE FROM #__excel2vm_fields WHERE id = $id");
		$this->_db->Query();
		exit();
	}

		function extra(){
		$id=JRequest::getVar('id', '', '', 'int');
		if(!$id){
			$this->_db->setQuery("SELECT DISTINCT custom_parent_id FROM #__virtuemart_customs ORDER BY custom_parent_id");
			$parents=$this->_db->loadColumn();
			if(count($parents)==1 AND $parents[0]==0){				 $list=$this->_getList("SELECT virtuemart_custom_id, custom_title FROM #__virtuemart_customs WHERE custom_title NOT IN('COM_VIRTUEMART_RELATED_PRODUCTS','COM_VIRTUEMART_RELATED_CATEGORIES')  AND field_type NOT IN('P','G') ORDER BY virtuemart_custom_id ");
			   $select= JHTML::_('select.genericlist',$list,'id','size="1" style="width:280px"','virtuemart_custom_id','custom_title');
			}
			elseif(!count($parents)){
				echo "<h3>Создайте настраиваемые поля через VM</h3>";
				exit();
			}
			else{
				$groups=array();

				foreach($parents as $parent_id){
					$this->_db->setQuery("SELECT custom_title FROM #__virtuemart_customs WHERE virtuemart_custom_id = $parent_id");
					$parent_name=$this->_db->loadResult();
					if(!$parent_name){
						$parent_name="Вне групп";
					}
					$groups[$parent_name]['items']=$this->_getList("SELECT virtuemart_custom_id as value, custom_title as text FROM #__virtuemart_customs WHERE custom_parent_id = $parent_id AND custom_title NOT IN('COM_VIRTUEMART_RELATED_PRODUCTS','COM_VIRTUEMART_RELATED_CATEGORIES')  AND field_type NOT IN('P','G')ORDER BY virtuemart_custom_id ");

				}

				$select=JHtml::_('select.groupedlist', $groups, 'id');
			}

			echo "<h3>". JText::_('SELECT_THE_TYPE_OF_CUSTOM_FIELD') .":</h3>";
			echo $select;
			echo '<input type="hidden" name="task" value="extra" />';
			echo '<br /><input type="button" onclick="add_field_form()" value="'.JText::_('CREATE') .'" />';

			exit();
		}

		$this->_db->setQuery("SELECT custom_title,is_cart_attribute FROM #__virtuemart_customs WHERE virtuemart_custom_id = $id");
		$extra=$this->_db->loadObject();

		@$obj->id=$this->getNewId();
		$obj->extra_id=$id;
		$obj->title=$extra->custom_title.($extra->is_cart_attribute?"($obj->id)":"");
		$obj->name="extra_{$obj->id}";
				$obj->example=JText::_('CUSTOM_FIELD_VALUE') ." ($obj->id);". JText::_('CUSTOM_FIELD_VALUE') ." ($obj->id)";
		$obj->type=$extra->is_cart_attribute?'extra-cart':'extra';
		$this->_db->insertObject("#__excel2vm_fields",$obj);
		echo json_encode($obj);
		exit();
	}

	function user_field(){
	  $user = JFactory::getUser();
	  if(!in_array(8,$user->groups)){
		   exit();
	  }
	  $user_field_name=JRequest::getVar('user_field_name', '', '', 'string');
	  $user_field_title=JRequest::getVar('user_field_title', '', '', 'string');
	  if(!$user_field_name AND !$user_field_title){
		  echo '<p style="margin:10px 5px 5px 5px">Название поля (произвольное):</p>
				<input type="text" name="user_field_title" value="" /><br>
				<p style="margin:10px 5px 5px 5px">Системное имя (как в базе данных):</p>
				<input type="text" name="user_field_name" value="" /><br>
				';
				echo '<input type="hidden" name="task" value="user_field" />';
				echo '<br /><input type="button" onclick="add_field_form()" value="'.JText::_('CREATE') .'" />';
		  exit();
	  }
	  else{
		  if(!$user_field_name){
			  @$obj->title="error";
			  $obj->msg="Не указано системное имя поля";
			  echo json_encode($obj);
			  exit();

		  }
		  if(!$user_field_title){
			  @$obj->title="error";
			  $obj->msg="Не указано название поля";
			  echo json_encode($obj);
			  exit();
		  }
		  if(!preg_match("#^[a-z]{1}[a-z0-9]+#",$user_field_name)){
			  @$obj->title="error";
			  $obj->msg="Некорректное системное имя поля. Допускаются символы английского алфавита в нижнем реестре и цифры";
			  echo json_encode($obj);

			  exit();
		  }
		  else{
			  $this->_db->setQuery("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE `TABLE_NAME` = '".$this->_db->getPrefix()."virtuemart_products' AND `COLUMN_NAME` = ".$this->_db->Quote($this->_db->escape($user_field_name))."");
			  if(!$this->_db->loadResult()){
				   $this->_db->setQuery("ALTER TABLE #__virtuemart_products ADD `".$this->_db->escape($user_field_name)."` TEXT NULL DEFAULT NULL");
				   $this->_db->Query();
			  }
			  $obj->title=$this->_db->escape($user_field_title);
			  $obj->id=$this->getNewId();
			  $obj->extra_id=0;
			  $obj->name=$this->_db->escape($user_field_name);

			  $obj->example="Значение пользовательского поля;Значение пользовательского поля";
			  $obj->type='user-field';
			  $this->_db->insertObject("#__excel2vm_fields",$obj);
			  echo json_encode($obj);

			  exit();
		  }
		   exit();
	  }

	}

	function multi(){
		$id=JRequest::getVar('id', '', '', 'int');
		$select_field_type=JRequest::getVar('select_field_type', '', '', 'string');
		$clabel=JRequest::getVar('clabel', '', '', 'string');

		$types['product_name'] = JHTML::_('select.option',  'product_name', "Название товара", 'value', 'text' );
		$types['product_sku'] = JHTML::_('select.option',  'product_sku', "Артикул", 'value', 'text' );
		$types['slug'] = JHTML::_('select.option',  'slug', "Псевдоним", 'value', 'text' );
		$types['product_length'] = JHTML::_('select.option',  'product_length', "Длина", 'value', 'text' );
		$types['product_width'] = JHTML::_('select.option',  'product_width', "Ширина", 'value', 'text' );
		$types['product_height'] = JHTML::_('select.option',  'product_height', "Высота", 'value', 'text' );
		$types['product_weight'] = JHTML::_('select.option',  'product_weight', "Вес", 'value', 'text' );
		$types['clabels'] = JHTML::_('select.option',  'clabels', "Пользовательская метка", 'value', 'text' );
		if(!$id){
			$list=$this->_getList("SELECT virtuemart_custom_id, custom_title FROM #__virtuemart_customs WHERE field_type = 'C'");
			echo "<p style='margin:5px'>Выберите поле Multi-Variant:</p>";
			echo JHTML::_('select.genericlist',$list,'id','size="1" style="width:280px"','virtuemart_custom_id','custom_title');
			echo "<p style='margin:10px 5px 5px 5px'>Выберите тип характеристики:</p>";
			echo JHTML::_('select.genericlist',$types,'select_field_type','size="1" style="width:280px"','value','text');

			echo '
			<div id ="clabel_wrapper" style="display:none">
				<p style="margin:10px 5px 5px 5px">Введите пользовательскую метку:</p>
				<input type="text" name="clabel" value="" />
			</div>
			';
			echo '<input type="hidden" name="task" value="multi" />';
			echo '<br /><input type="button" onclick="add_field_form()" value="'.JText::_('CREATE') .'" />';

			exit();
		}

		$this->_db->setQuery("SELECT custom_title FROM #__virtuemart_customs WHERE virtuemart_custom_id = $id");
		$custom_title=$this->_db->loadResult();

		@$obj->id=$this->getNewId();
		@$extra->id=$id;
		$extra->type=$select_field_type;
		$extra->clabel=$select_field_type=='clabels'?$clabel:"";

		$obj->extra_id=json_encode($extra);
		$obj->title=$custom_title." (".($extra->clabel?$extra->clabel:@$types[$select_field_type]->text).")";
		$obj->name="multi_{$obj->id}";
				$obj->example=JText::_('Multi-variant') ." ($obj->id);". JText::_('Multi-variant') ." ($obj->id)";
		$obj->type='multi';
		$this->_db->insertObject("#__excel2vm_fields",$obj);
		echo json_encode($obj);
		exit();
	}

		function profile_list($data_only=false){
		  $list=$this->_getList("SELECT id, profile FROM #__excel2vm ORDER BY id");
		  if($data_only)return $list;
		  array_unshift($list,JHTML::_( 'select.option', '', JText::_('ADD_NEW'),'id','profile' ));

		  echo "<h3>". JText::_('SELECT_AN_EXISTING_PROFILE_OR_CREATE_A_NEW_ONE') .":</h3>";
		  echo JHTML::_('select.genericlist',$list,'profile_id','size="1" id="profile_id" style="width:280px"','id','profile',1);
		  echo '<input type="hidden" name="task" value="create_profile" />';
		  echo '<br /><span style="display:none" id="create_new_profile"><strong>'.JText::_('ENTER_THE_NAME_OF_THE_NEW_PROFILE') .'</strong><br /><input type="text" id="profile" name="profile" value="" /></span>';
		  echo '<br /><input type="button" id="create_profile_form" value="'.JText::_('SAVE') .'" />';
		  exit();

	 }

		 function create_profile(){
		 $profile=JRequest::getVar('new_profile_name', '', '', 'string');
		 $profile_id=JRequest::getVar('profile_id_value', '', '', 'int');
		 if($profile){
			 $this->table->reset();

			 $this->_db->setQuery("UPDATE #__excel2vm SET default_profile = 0");
			 $this->_db->Query();

			 $this->_db->setQuery("SELECT id FROM #__excel2vm WHERE profile='$profile'");
			 $id=$this->_db->loadResult();
			 if($id){
				$this->table->id=$id;
				$this->table->default_profile=1;
				$this->table->update();
				echo sprintf(JText::_('PROFILE_S_EXISTS'),$profile);
			 }
			 else{
				 $this->table->id='';
				 $this->table->profile=$profile;
				 $this->table->default_profile=1;
				 $this->table->insert();
				 echo sprintf(JText::_('PROFILE_S_ADDED'),$profile); ;
			 }
			 $this->save_config();
		 }elseif($profile_id){
			 $this->change_profile();
			 echo JText::_('PROFILE_IS_SAVED_AND_SET_AS_DEFAULT');
			 $this->save_config();
		 }

		 exit();
	}

	function change_profile(){
		$profile_id=JRequest::getVar('profile_id_value', '', '', 'int');

		$this->_db->setQuery("UPDATE #__excel2vm SET default_profile = 0");
		$this->_db->Query();
		$this->table->reset();
		$this->table->id=$profile_id;
		$this->table->default_profile=1;
		$this->table->update();
		$this->config=$this->core->getConfig();
		$this->active_fields =$this->core->active_fields;
		$this->profile =$this->core->profile;
	}

		function extra_price(){

		$id=JRequest::getVar('id', '', '', 'int');
		if(!$id){
			$list=$this->_getList("SELECT id, title FROM #__excel2vm_fields WHERE type = 'extra-cart' AND id NOT IN(SELECT extra_id FROM #__excel2vm_fields WHERE type = 'extra-price')");
			if(count($list)==0){
				echo "<h3>". JText::_('FIRST_YOU_NEED_TO_ADD_AN_CUSTOM_FIELD') ."</h3>";
				exit();
			}
			echo "<h3>Выберите доп. поле, к кторому будет привязана цена:</h3>";
			echo JHTML::_('select.genericlist',$list,'id','size="1" style="width:280px"','id','title');
			echo '<input type="hidden" name="task" value="extra_price" />';
			echo '<br /><input type="button" onclick="add_field_form();" value="'.JText::_('SAVE') .'" />';

			exit();
		}

		$this->_db->setQuery("SELECT title FROM #__excel2vm_fields WHERE id = $id");
		@$obj->title=$this->_db->loadResult().JText::_('EXTRA_FIELD_ATTRIBUTE_PRICE');
		$obj->id=$this->getNewId();
		$obj->extra_id=$id;
		$obj->name="extra_price_{$id}";
				$obj->example=JText::_('PRICE_FOR_CUSTOM_FIELD') ." ($id);". JText::_('PRICE_FOR_CUSTOM_FIELD') ." ($id)";
		$obj->type='extra-price';
		$this->_db->insertObject("#__excel2vm_fields",$obj);
		echo json_encode($obj);
		exit();
	}

		function price(){
		$id=JRequest::getVar('id', '', '', 'int');
		$start=JRequest::getVar('start', '', '', 'int');
		$end=JRequest::getVar('end', '', '', 'int');
		$lang = JFactory::getLanguage();
		$lang->load('com_virtuemart');

		if(!$id AND !$start AND !$end){

			$list=$this->_getList("SELECT virtuemart_shoppergroup_id,shopper_group_name
								   FROM #__virtuemart_shoppergroups
								   ");
			foreach($list as &$v){
				 $v->shopper_group_name=JText::_($v->shopper_group_name);
			}

			echo "<h3 style='margin-bottom: 6px;'>";
			echo JText::_('CHOOSE_SHOPPER_GROUP');
			echo JHTML::tooltip(JText::_('GROUPS_HINT'),'','',"<span class='ui-icon ui-icon-info2' style='float: right; margin-right: .3em;'></span>");
			echo"</h3>";

			array_unshift($list,JHTML::_( 'select.option', '0', JText::_('FOR_ALL'),'virtuemart_shoppergroup_id','shopper_group_name'));
			echo JHTML::_('select.genericlist',$list,'id','size="1" style="width:280px"','virtuemart_shoppergroup_id','shopper_group_name');
			echo "<h3 style='margin-bottom: 6px;'>";
			echo JText::_('RANGE');
			echo ":";
			echo JHTML::tooltip(JText::_('RANGE_HINT'),'','',"<span class='ui-icon ui-icon-info2' style='float: right; margin-right: .3em;'></span>");
			echo "</h3>";
			echo '<input class="text_area" type="text" name="start"  size="3" maxlength="250" value="" /> <span style="display: inline-block;margin: 6px 5px 0 1px;">-</span> <input class="text_area" type="text" name="end"  size="3" maxlength="250" value="" />';
			echo '<input type="hidden" name="task" value="price" />';
			echo '<br /><br /><input type="button" onclick="add_field_form();" value="'.JText::_('SAVE') .'" />';
			exit();
		}

		$this->_db->setQuery("SELECT shopper_group_name FROM #__virtuemart_shoppergroups WHERE virtuemart_shoppergroup_id = $id");

		$group=$this->_db->loadResult();
		if(!$group)$group='FOR_ALL';
		$group=JText::_($group);
		@$extra_data->virtuemart_shoppergroup_id=$id;
		$extra_data->price_quantity_start=$start;
		$extra_data->price_quantity_end=$end;
		@$obj->title=JText::_('COST_PRICE')."($group;$start-$end)";
		$obj->id=$this->getNewId();
		$obj->extra_id=json_encode($extra_data);
		$obj->name="price_{$id}_{$start}_{$end}";
				$obj->example="200;300";
		$obj->type='price';
		$this->_db->setQuery("REPLACE INTO #__excel2vm_fields SET title='$obj->title',id='$obj->id',extra_id='$obj->extra_id',name='$obj->name',example='$obj->example',type='$obj->type'");
		$this->_db->Query();
		unset($obj->extra_id);
		echo json_encode($obj);
		exit();
	}

		function empty_field(){
		$new_id=$this->getNewId();
				$this->_db->setQuery("INSERT INTO #__excel2vm_fields SET id=$new_id,name='empty_{$new_id}',title='EMPTY_COLUMN',type='empty',example='EMPTY;EMPTY'");
		$this->_db->Query();
		echo $new_id;
		exit();
	}

	function custom_field(){
		$title_id=$this->getNewId();
				$this->_db->setQuery("INSERT INTO #__excel2vm_fields SET id=$title_id,name='custom_title_{$title_id}',title='".JText::_('CUSTOM_COLUMN')." ($title_id) ".JText::_('CUSTOM_COLUMN_TITLE')."',type='custom',example='SOME TITLE;SOME TITLE',extra_id='$title_id'");
		$this->_db->Query();
		@$ids->title=$title_id;

		$new_id=$this->getNewId();
		$this->_db->setQuery("INSERT INTO #__excel2vm_fields SET id=$new_id,name='custom_units_{$title_id}',title='".JText::_('CUSTOM_COLUMN')." ($title_id) ".JText::_('CUSTOM_COLUMN_UNITS')."',type='custom',example='SOME UNITS;SOME UNITS',extra_id='$title_id'");
		$this->_db->Query();
		@$ids->units=$new_id;

		$new_id=$this->getNewId();
		$this->_db->setQuery("INSERT INTO #__excel2vm_fields SET id=$new_id,name='custom_value_{$title_id}',title='".JText::_('CUSTOM_COLUMN')." ($title_id) ".JText::_('CUSTOM_COLUMN_VALUE')."',type='custom',example='SOME VALUE;SOME VALUE',extra_id='$title_id'");
		$this->_db->Query();
		@$ids->value=$new_id;
		echo json_encode($ids);
		exit();
	}

	function getNewId(){
	   $this->_db->setQuery("SELECT MAX(id) FROM #__excel2vm_fields");
	   $new_id=$this->_db->loadResult()+1;
	   if($new_id<1000){
		  $new_id=1000;
	   }
	   return $new_id;
	}

	function getNewOrdering(){
	   $this->_db->setQuery("SELECT MAX(ordering) FROM #__excel2vm_fields WHERE status = 0");
	   return $this->_db->loadResult()+1;
	}

	function export_profile($save=false){
		$export['config']=$this->config;
		$this->_db->setQuery("SELECT id FROM #__excel2vm_fields WHERE type !='default' AND type !='price' AND id IN ($this->active_fields)");
		$export['empty']=$this->_db->loadColumn();

		$this->_db->setQuery("SELECT id,extra_id FROM #__excel2vm_fields WHERE type ='price' AND id IN ($this->active_fields)");
		$export['price']=$this->_db->loadObjectList('id');

		$export['fields']=explode(",",$this->active_fields);
		$export=serialize($export);
		$signature=md5($export.'15dgt328jupievpw9ar8');
		$export=base64_encode(serialize(array($export,$signature)));
		if(!$save){
			header('Content-disposition: attachment; filename=profile.txt');
			header('Content-type: text/plain');
			echo $export;
			exit();
		}
		else{
			return @file_put_contents(JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'profile.txt',$export);
		}

	}

	function import_profile(){

		$user = JFactory::getUser();
		if(!in_array(8,$user->groups)){
			return "Вы не можете импортировать профиль";
		}
		$profile_file=$_FILES['profile_file'];
		if(!isset($profile_file['name']))
			return JText::_('SPECIFY_THE_PROFILE_FILE');

		if(substr($profile_file['name'],-3)!='txt')
			return JText::_('PROFILE_FILE_MUST_HAVE_THE_EXTENSION_TXT');
		$profile=file_get_contents($profile_file['tmp_name']);
		$profile=unserialize(base64_decode($profile));
		if(md5($profile[0].'15dgt328jupievpw9ar8')!=$profile[1])
			return JText::_('THE_FILE_IS_DAMAGED.');
		$profile=unserialize($profile[0]);

		$active_fields_list = array();
		foreach($profile['fields'] as $key=>$field_id){
			if(in_array($field_id,$profile['empty'])){
				$empty_id=0;
				$this->_db->setQuery("SELECT id FROM #__excel2vm_fields WHERE type ='empty' AND id NOT IN (".(empty($active_fields_list)?0:implode(",",$active_fields_list)).") ORDER BY id LIMIT 0,1");
				$empty_id=$this->_db->loadResult();

				if(!$empty_id){
					$empty_id=$this->getNewId();
					$this->_db->setQuery("INSERT INTO #__excel2vm_fields SET id=$empty_id,name='empty_{$empty_id}',title='EMPTY_COLUMN',type='empty',example='EMPTY;EMPTY'");
					$this->_db->Query();
				}
				$active_fields_list[$key] = $empty_id;
			}
			elseif(@isset($profile['price'][$field_id])){
				$price_id=0;
				$this->_db->setQuery("SELECT id FROM #__excel2vm_fields WHERE type ='price' AND extra_id = '{$profile['price'][$field_id]->extra_id}' ORDER BY id LIMIT 0,1");
				$price_id=$this->_db->loadResult();

				if(!$price_id){
					$price_id=$this->getNewId();
					$data=json_decode($profile['price'][$field_id]->extra_id);

					$this->_db->setQuery("SELECT shopper_group_name FROM #__virtuemart_shoppergroups WHERE virtuemart_shoppergroup_id = {$data->virtuemart_shoppergroup_id}");

					$group=$this->_db->loadResult();
					if(!$group)$group=JText::_('FOR_ALL');
					$title=JText::_('COST_PRICE')."($group;{$data->price_quantity_start}-{$data->price_quantity_end})";
					$this->_db->setQuery("INSERT INTO #__excel2vm_fields SET id=$price_id,name='price_{$data->virtuemart_shoppergroup_id}_{$data->price_quantity_start}_{$data->price_quantity_end}',title='$title',type='price',example='200;300',extra_id = '{$profile['price'][$field_id]->extra_id}'");
					$this->_db->Query();
				}
				$active_fields_list[$key] = $price_id;
			}
			else
				$active_fields_list[$key] = $field_id;
		}
		$this->_db->setQuery("SELECT id FROM #__excel2vm WHERE profile = '{$profile['config']->profile_name}'");
		$profile_id = $this->_db->loadResult();
		if($profile_id){		   $this->_db->setQuery("UPDATE #__excel2vm SET active = '".implode(",",$active_fields_list)."', config = ".$this->_db->Quote(serialize($profile['config']))." WHERE id = $profile_id");
		   $this->_db->Query();
		   $msg=sprintf(JText::_('PROFILE_S_UPDATED'),$profile['config']->profile_name);
		}
		else{
			$this->_db->setQuery("INSERT INTO #__excel2vm SET active = '".implode(",",$active_fields_list)."', config = ".$this->_db->Quote(serialize($profile['config'])).", profile = ".$this->_db->Quote($profile['config']->profile_name)." ");
			$this->_db->Query();
			$profile_id=$this->_db->insertid();
			$msg=sprintf(JText::_('PROFILE_S_ADDED'),$profile['config']->profile_name);
		}
		JRequest::setVar('profile_id_value', $profile_id);
		$this->change_profile();
		return $msg;

	}

		function test_timeout(){
	   sleep(300);
	   exit();
	}

	function browser_timeout(){
		$value=JRequest::getVar('value', 0, 'get', 'int');
		$this->_db->setQuery("SELECT params FROM #__extensions WHERE element = 'com_excel2vm'");
		$params=$this->_db->loadResult();
		if($params){		   $params=json_decode($params);
		   $params->timeout=$value;
		   $params=json_encode($params);
		   $this->_db->setQuery("UPDATE #__extensions SET params='$params' WHERE element = 'com_excel2vm'");
		   $this->_db->Query();
		}
		else{			$this->_db->setQuery("SELECT params FROM #__components WHERE option = 'com_excel2vm'");
			$params=$this->_db->loadResult();
			$params=explode("\n",$params);
			foreach($params as $key => $param){
			   if(strstr($param,'timeout')){
				  $params['key']='timeout='.$value;
				  $ok=1;
				  break;
			   }
			}
			if(!$ok){
			   $params[]='timeout='.$value;
			}
			$params=implode("\n",$params);
			$this->_db->setQuery("UPDATE #__components SET params='$params' WHERE option = 'com_excel2vm'");
			$this->_db->Query();
		}

		exit();
	}

	function is_cherry(){
	  $this->_db->setQuery("SHOW TABLES LIKE '".$this->_db->getPrefix()."fastseller_product_type'");
	  if($this->_db->loadResult()){
		  return 1;
	  }
	  $this->_db->setQuery("SHOW TABLES LIKE '".$this->_db->getPrefix()."vm_product_type_parameter%'");
	  if($this->_db->loadResult()){
		  return 2;
	  }
	  return false;
	}

	function getCherryParams(){
		$prefix=$this->is_cherry==1?"fastseller":"vm";

		$this->_db->setQuery("SELECT CONCAT(p.product_type_id,'_',parameter_name) as value,CONCAT(parameter_label,' (',product_type_name,')') as text FROM #__{$prefix}_product_type_parameter as p LEFT JOIN #__{$prefix}_product_type as t ON t.product_type_id = p.product_type_id ");


		$list=$this->_db->loadObjectList('value');
		foreach($list as $value=>$data){
		   $this->_db->setQuery("SELECT id FROM #__excel2vm_fields WHERE type='cherry' AND name = '$value'");
		   if($this->_db->loadResult())
				unset($list[$value]);
		}
		return $list;
	}

	function cherry_field(){
		$value=JRequest::getVar('value', '', '', 'string');
		$list=$this->getCherryParams();
		if(!$value){

			if(!$list){
			   echo "<h3>". JText::_('Создайте фильтр и параметры для него через компонент Fast Seller') ."</h3>";
			   exit();
			}
			echo "<h3>". JText::_('Выберите параметр') .":</h3>";
			echo JHTML::_('select.genericlist',$list,'value','size="1" style="width:280px"','value','text');
			echo '<input type="hidden" name="task" value="cherry_field" />';
			echo '<br /><input type="button" onclick="add_field_form()" value="'.JText::_('CREATE') .'" />';

			exit();
		}
		if(!isset($list[$value])){
			@$obj->title="error";
			$obj->msg="Не найден параметр";
			echo json_encode($obj);
			exit();
		}
		$data=$list[$value];
		$temp=explode("_",$data->value);
		@$obj->id=$this->getNewId();
		$obj->extra_id=json_encode($temp);
		$obj->title=$data->text;
		$obj->example="Большой;Маленький";
		$obj->name=$value;
		
		$obj->type='cherry';
		$this->_db->insertObject("#__excel2vm_fields",$obj);
		echo json_encode($obj);
		exit();
	}

	function sync(){
		$resp=new stdClass();
		$resp->insert=new stdClass();
		$resp->del=array();
		$resp->insert->ecf=array();
		$resp->insert->ep=array();
		$resp->insert->ef=array();
		$field_list=$this->_getList("SELECT virtuemart_custom_id, custom_title,is_cart_attribute FROM #__virtuemart_customs WHERE custom_title NOT IN('COM_VIRTUEMART_RELATED_PRODUCTS','COM_VIRTUEMART_RELATED_CATEGORIES') AND field_type != 'P'");

		$extra_field_list=$this->_getList("SELECT * FROM #__excel2vm_fields WHERE type IN('extra','extra-cart')");

		$new=0;
		$old=0;
		$ecf_index=0;
		$ep_index=0;
		$ef_index=0;

		foreach($field_list as $f){
			if($f->is_cart_attribute){
				$this->_db->setQuery("SELECT id FROM #__excel2vm_fields WHERE type = 'extra-cart' AND extra_id = '$f->virtuemart_custom_id'");
				$ecf_id=$this->_db->loadResult();
				if(!$ecf_id){
					$example=JText::_('CUSTOM_FIELD_VALUE') ." ($f->virtuemart_custom_id);". JText::_('CUSTOM_FIELD_VALUE') ." ($f->virtuemart_custom_id)";
					$ecf_id=$this->getNewId();
					$this->_db->setQuery("INSERT INTO #__excel2vm_fields SET id = '$ecf_id', name = 'extra_{$f->virtuemart_custom_id}', title=".$this->_db->Quote("{$f->custom_title}({$f->virtuemart_custom_id})").", type='extra-cart', example='$example',extra_id='{$f->virtuemart_custom_id}'");
					$this->_db->Query();

					@$resp->insert->ecf[$ecf_index]->id=$ecf_id;
					$resp->insert->ecf[$ecf_index]->title="{$f->custom_title}({$ecf_id})";
					$new++;
					$ecf_index++;
				}
				if($ecf_id){
				   $this->_db->setQuery("SELECT id FROM #__excel2vm_fields WHERE type = 'extra-price' AND extra_id = '$ecf_id'");
				   $ep_id=$this->_db->loadResult();
				   if(!$ep_id){
						$ep_id=$this->getNewId();
						$example=JText::_('PRICE_FOR_CUSTOM_FIELD') ." ($ecf_id);". JText::_('PRICE_FOR_CUSTOM_FIELD') ." ($ecf_id)";
						$this->_db->setQuery("INSERT INTO #__excel2vm_fields SET id = '$ep_id',name = 'extra_price_{$ecf_id}', title=".$this->_db->Quote("{$f->custom_title}({$f->virtuemart_custom_id})".JText::_('EXTRA_FIELD_ATTRIBUTE_PRICE')).", type='extra-price', example='$example',extra_id='{$ecf_id}'");
						$this->_db->Query();

						@$resp->insert->ep[$ep_index]->id=$ep_id;
						$resp->insert->ep[$ep_index]->title="{$f->custom_title}({$ecf_id})".JText::_('EXTRA_FIELD_ATTRIBUTE_PRICE');
						$new++;
						$ep_index++;
					}
				}
			}
			else{
				$this->_db->setQuery("SELECT id FROM #__excel2vm_fields WHERE type = 'extra' AND extra_id = '$f->virtuemart_custom_id'");
				$ef_id=$this->_db->loadResult();
				if(!$ef_id){
					$ef_id=$this->getNewId();
					$example=JText::_('CUSTOM_FIELD_VALUE') ." ($f->virtuemart_custom_id);". JText::_('CUSTOM_FIELD_VALUE') ." ($f->virtuemart_custom_id)";
					$this->_db->setQuery("INSERT INTO #__excel2vm_fields SET id='$ef_id', name = 'extra_{$f->virtuemart_custom_id}', title=".$this->_db->Quote($f->custom_title).", type='extra', example='$example',extra_id='{$f->virtuemart_custom_id}'");
					$this->_db->Query();

					@$resp->insert->ef[$ef_index]->id=$ef_id;
					@$resp->insert->ef[$ef_index]->title="{$f->custom_title}({$ef_id})";
					$new++;
					$ef_index++;
				}
			}

		}
		foreach($extra_field_list as $efl){
			$this->_db->setQuery("SELECT virtuemart_custom_id FROM #__virtuemart_customs WHERE virtuemart_custom_id = $efl->extra_id");
			if(!$this->_db->loadResult()){
				 $this->_db->setQuery("DELETE FROM #__excel2vm_fields WHERE id = $efl->id");
				 $this->_db->Query();
				 $resp->del[]= $efl->id;
				 $old++;
				 if($efl->type=='extra-cart'){
					 $this->_db->setQuery("SELECT id FROM #__excel2vm_fields WHERE extra_id=$efl->id AND type='extra-price'");
					 $extra_price_id=$this->_db->loadResult();
					 if($extra_price_id){
						 $this->_db->setQuery("DELETE FROM #__excel2vm_fields WHERE id = $extra_price_id");
						 $this->_db->Query();
						 $resp->del[]= $extra_price_id;
						 $old++;
					 }
				 }
			}
		}

		$resp->html="Добавлено полей: <b>$new</b><br>Удалено полей: <b>$old</b>";


		echo json_encode($resp);
		exit();
	}
}
