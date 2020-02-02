<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
ini_set('log_errors', 'On');
ini_set('error_log', JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'error.txt');

require_once (dirname(__FILE__) . DS . "updateTable.php");

class Excel2vmModelExcel2vm extends JModelLegacy {
	public $pagination;

	function __construct($cron=false) {
		parent :: __construct();
		
		$this->params=JComponentHelper::getParams('com_excel2vm');

		ini_set("max_execution_time",$this->params->get('max_execution_time',300));
		ini_set("upload_max_filesize",$this->params->get('post_max_size',20)."M");
		ini_set("post_max_size",$this->params->get('post_max_size',20)."M");

		$db_debug=$this->params->get('db_debug',0);
		$this->debug=$this->params->get('debug',0);
		/*if($debug){
			require_once (dirname(__FILE__) . DS . "db_debug.php");
			$full_debug=JRequest::getVar('full_debug', 'cookie', 0, 'int');
			$this->_db=new JDatabaseMySQLbak($full_debug,$full_debug);
		}*/
		$this->core = new core($cron);
		$this->cron=$cron;
		$this->cron_file_dir=$this->params->get('directory_path');
		if(substr($this->cron_file_dir,-1)!=DS){
		   $this->cron_file_dir.= DS;
		}
		$this->_db->debug($db_debug);
		$this->chunk_on=$this->params->get('chunk_on',1);
		$this->chunkSize = $this->params->get('chunk_size',1000);
		$this->create_custom_fields = $this->params->get('create_custom_fields',0);
		$this->exclude = $this->params->get('exclude',0);
		$this->custom_clear = $this->params->get('custom_clear','-');
		$this->csv_field_delimiter = $this->params->get('csv_field_delimiter',';');
		$this->csv_row_delimiter = $this->params->get('csv_row_delimiter','');
		$this->csv_convert = $this->params->get('csv_convert',1);
		$this->sku_cache = $this->params->get('sku_cache',0);
		$this->gtin_cache = $this->params->get('gtin_cache',0);
		$this->mpn_cache = $this->params->get('mpn_cache',0);
		$this->name_cache = $this->params->get('name_cache',0);
		$this->desc_nl2br = $this->params->get('desc_nl2br',0);
		$this->price_label = $this->params->get('price_label',0);
		$this->productid_cache = $this->params->get('productid_cache',0);
		$this->images_rename = $this->params->get('images_rename',0);
		$this->images_products_default = $this->params->get('images_products_default');
		$this->images_categories_default = $this->params->get('images_categories_default');
		$this->images_timeout = $this->params->get('images_timeout',5);
		$this->config_table = new updateTable("#__excel2vm", "id", 1);

				$this->config =$this->core->getConfig();
		if(@$this->config->images_products_default){
			$this->images_products_default=$this->config->images_products_default;
		}
		if(@$this->config->images_categories_default){
			$this->images_categories_default=$this->config->images_categories_default;
		}


		$this->active_fields =$this->core->active_fields;
		$this->active = $this->getActive();

		$cron_start=@file_get_contents(dirname(__FILE__)."/cron_import_start.txt");
		if($this->cron AND $cron_start){
			$this->reimport=1;
		}
		else{
			$this->reimport=JRequest::getVar('reimport', 0, '', 'int');
		}

		$this->show_results=(int)JRequest::getVar('show_results', '', '', 'int');

		$this->first_row=$this->config->first;
		$this->is_cherry=$this->is_cherry();
				$this->default_object=@file_get_contents(dirname(__FILE__)."/catprod_default.txt");
		$this->default_articles_object=@file_get_contents(dirname(__FILE__)."/articles_default.txt");
		
				$this->trans = array("а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "yo", "ж" => "zh", "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "kh", "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sh", "ы" => "y", "э" => "e", "ю" => "yu", "я" => "ya", "А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d", "Е" => "e", "Ё" => "yo", "Ж" => "zh", "З" => "z", "И" => "i", "Й" => "j", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n", "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t", "У" => "u", "Ф" => "f", "Х" => "kh", "Ц" => "ts", "Ч" => "ch", "Ш" => "sh", "Щ" => "sh", "Ы" => "y", "Э" => "e", "Ю" => "yu", "Я" => "ya", "ь" => "", "Ь" => "", "ъ" => "", "Ъ" => "","/" =>"-","\\" =>"","-" =>"-",":" =>"-","(" =>"-",")" =>"-","." =>"","," =>"",'"'=>"-",'>'=>"-",'<'=>"-",'+'=>"-",'«'=>'','»'=>'',"'"=>"","і"=>"i","ї"=>"yi","І"=>"i","Ї"=>"yi","є"=>"e","Є"=>"e");

		$this->backup_tables_array = array("#__virtuemart_categories", "#__virtuemart_categories_".$this->config->sufix, "#__virtuemart_category_categories", "#__virtuemart_products", "#__virtuemart_products_".$this->config->sufix, "#__virtuemart_product_medias", "#__virtuemart_product_prices", "#__virtuemart_customs","#__virtuemart_product_customfields", "#__virtuemart_product_categories","#__virtuemart_product_manufacturers","#__virtuemart_manufacturers","#__virtuemart_manufacturers_".$this->config->sufix,"#__virtuemart_medias");
		$this->chekTables();
		$this->category_list=$this->category_list();
		$this->manufacturers_list=$this->manufacturers_list();
		$user = JFactory::getUser();
		$this->user_id=$user->id;
		$this->need_profiler=0;
		$this->chek_indexes();
	}

	function getNameFromNumber($num) {
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return $this->getNameFromNumber($num2 - 1) . $letter;
		} else {
			return $letter;
		}
	}


	function setCookies(){
	  $inputCookie  = JFactory::getApplication()->input->cookie;
	  $inputCookie->set('showResults',$this->show_results, time()+(365*24*3600));
	  $inputCookie->set('thumb_height',$_POST['height'], time()+(365*24*3600));
	  $inputCookie->set('thumb_width',$_POST['width'], time()+(365*24*3600));
	  $inputCookie->set('thumb_prefix',$_POST['prefix'], time()+(365*24*3600));
	  $inputCookie->set('thumb_sufix',$_POST['sufix'], time()+(365*24*3600));
	  $inputCookie->set('thumb_make_thumb',$_POST['make_thumb'], time()+(365*24*3600));
	}
	function category_list(){
		$this->_db->setQuery("SELECT r.virtuemart_category_id,category_name
						  FROM #__virtuemart_categories_".$this->config->sufix." as r
						  LEFT JOIN #__virtuemart_categories as c ON c.virtuemart_category_id = r.virtuemart_category_id
						  WHERE c.virtuemart_category_id IS NOT NULL AND r.virtuemart_category_id NOT IN ($this->exclude)");
		return $this->_db->loadObjectList('virtuemart_category_id');
	}

	function manufacturers_list(){
		$this->_db->setQuery("SELECT r.virtuemart_manufacturer_id,mf_name
						  FROM #__virtuemart_manufacturers_".$this->config->sufix." as r
						  LEFT JOIN #__virtuemart_manufacturers as m ON m.virtuemart_manufacturer_id = r.virtuemart_manufacturer_id
						  WHERE m.virtuemart_manufacturer_id IS NOT NULL");
		return $this->_db->loadObjectList('virtuemart_manufacturer_id');
	}

	function searchCategory($product_name){
		$product_name_words=explode(" ",$product_name);
		foreach($product_name_words as $key=>$product_name_word){
			$product_name_words[$key]=$this->_strtolower(strtr($product_name_word,array('"'=>'',"'"=>"",'`'=>'')));
			if(strlen($product_name_word)<4)
				unset($product_name_words[$key]);
		}

		foreach($this->category_list as $cat){
			$cat_words=explode(" ",$cat->category_name);
			foreach($cat_words as $cat_word){
				if(strlen($cat_word)<4)continue;
				foreach($product_name_words as $product_name_word){
					if(preg_match("#^".mb_substr($this->_strtolower($cat_word),0,4)."#",$product_name_word)){
						return $cat->virtuemart_category_id;
					}

				}
			}
		}

		$this->_db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_categories_".$this->config->sufix." WHERE category_name ='{$this->config->extra_category}'");
		$extra_category_id=$this->_db->loadResult();
		if($extra_category_id)
			return $extra_category_id;

		else{

			$this->_db->setQuery("INSERT INTO #__virtuemart_categories
							  SET
							  virtuemart_vendor_id=1,
							  published=1,
							  created_on='$this->m_date',
							  created_by='$this->user_id',
							  modified_on='$this->m_date',
							  modified_by='$this->user_id',
							  category_template='$this->brows',
							  products_per_row='$this->per_row',
							  category_product_layout='$this->flypage'");
			$this->_db->Query();
			$extra_category_id=$this->_db->insertid();

			$this->_db->setQuery("INSERT INTO #__virtuemart_categories_".$this->config->sufix." SET virtuemart_category_id=$extra_category_id, category_name='".$this->config->extra_category ."',category_description = '{$this->config->extra_category}', slug = '" . $extra_category_id.'-'.$this->translit($this->config->extra_category) . "'");
			$this->_db->Query();

			$this->_db->setQuery("INSERT INTO #__virtuemart_category_categories SET category_parent_id='0', category_child_id='$extra_category_id'");
			$this->_db->Query();
			return $extra_category_id;
		}

	}

	function searchManufacturer($product_name){
		$product_name_words=explode(" ",$product_name);
		foreach($product_name_words as $key=>$product_name_word){
			$product_name_words[$key]=$this->_strtolower(strtr($product_name_word,array('"'=>'',"'"=>"",'`'=>'')));
			if(strlen($product_name_word)<2)
				unset($product_name_words[$key]);
		}
		if(count($this->manufacturers_list)<100)
			foreach($this->manufacturers_list as $mf){
				$mf_words=explode(" ",$mf->mf_name);
				foreach($mf_words as $mf_word){
					if(strlen($mf_word)<2)continue;
					foreach($product_name_words as $product_name_word){
						if(preg_match("#^".mb_substr($this->_strtolower($mf_word),0,4)."#",$product_name_word) OR $this->_strtolower($mf_word)==$product_name_word)
							return $mf->virtuemart_manufacturer_id;
					}
				}
			}
		return false;

	}

	function change_profile(){
		$profile_id=JRequest::getVar('profile_id', '', '', 'int');
		$this->_db->setQuery("UPDATE #__excel2vm SET default_profile = 0");
		$this->_db->Query();
		$this->config_table->reset();
		$this->config_table->id=$profile_id;
		$this->config_table->default_profile=1;
		$this->config_table->update();
	}

	function profile_list(){
		  $list=$this->_getList("SELECT id, profile FROM #__excel2vm ORDER BY id");
		  return $list;
	}

	function getActive() {
		$xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.xml');
		$this->vm_version=(string)$xml->version;
		$this->is_vm_version_3=(substr($this->vm_version,0,1)==3 OR substr($this->vm_version,0,3)=='2.9')?true:false;
		$this->active_fields=$this->active_fields?$this->active_fields:1;
		$query = "SELECT *
				FROM #__excel2vm_fields
				WHERE id IN({$this->active_fields})
				ORDER BY FIELD(id,{$this->active_fields})";
		$this->_db->setQuery($query);
		$list=$this->_db->loadObjectList('name');
		$i=0;
		foreach($list as $key=> $l){
		   $i++;
		   if($this->is_vm_version_3 AND $key=='category_product_layout'){
				$key='layout';
				$l->name='layout';
		   }
		   $l->ordering=$i;
		   $list2[$key]=$l;

		}

		return $list2;
	}

	function translit($text) {
		$trans=strtolower(strtr($text, $this->trans));
		$trans=str_replace('"',"",$trans);
		$trans=str_replace("'","",$trans);
		$trans=str_replace("2quot;","",$trans);
		$trans=str_replace("quot;","",$trans);
						$trans = str_replace(" ","-",$trans);
		$trans = preg_replace("/[^0-9a-z\-]/","",$trans);
		while(strstr($trans,"--"))
			$trans = str_replace("--","-",$trans);
		$trans =  preg_replace('/[\x00-\x2C\x7B-\xFF]/', '', $trans);


		return $trans;
	}
	function chekTables(){

		$query="CREATE TABLE IF NOT EXISTS `#__virtuemart_categories_".$this->config->sufix."` (
				  `virtuemart_category_id` int(1) unsigned NOT NULL AUTO_INCREMENT,
				  `category_name` char(180) NOT NULL DEFAULT '',
				  `category_description` varchar(19000) NOT NULL DEFAULT '',
				  `metadesc` char(128) NOT NULL DEFAULT '',
				  `metakey` char(128) NOT NULL DEFAULT '',
				  `slug` char(192) NOT NULL DEFAULT '',
				  PRIMARY KEY (`virtuemart_category_id`),
				  UNIQUE KEY `slug` (`slug`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Language ".$this->config->sufix." for categories' AUTO_INCREMENT=1 ";
		$this->_db->setQuery($query);
		$this->_db->Query();

		$query="CREATE TABLE IF NOT EXISTS `#__virtuemart_products_".$this->config->sufix."` (
				  `virtuemart_product_id` int(1) unsigned NOT NULL AUTO_INCREMENT,
				  `product_name` char(180) NOT NULL DEFAULT '',
				  `product_s_desc` varchar(2048) NOT NULL DEFAULT '',
				  `product_desc` varchar(19000) NOT NULL DEFAULT '',
				  `metadesc` char(128) NOT NULL DEFAULT '',
				  `metakey` char(128) NOT NULL DEFAULT '',
				  `slug` char(192) NOT NULL DEFAULT '',
				  PRIMARY KEY (`virtuemart_product_id`),
				  UNIQUE KEY `slug` (`slug`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Language ".$this->config->sufix." for products' AUTO_INCREMENT=1 ";
		$this->_db->setQuery($query);
		$this->_db->Query();

		$query="CREATE TABLE IF NOT EXISTS `#__virtuemart_manufacturers_".$this->config->sufix."` (
				  `virtuemart_manufacturer_id` int(1) unsigned NOT NULL AUTO_INCREMENT,
				  `mf_name` char(180) NOT NULL DEFAULT '',
				  `mf_email` char(255) NOT NULL DEFAULT '',
				  `mf_desc` varchar(19000) NOT NULL DEFAULT '',
				  `mf_url` char(255) NOT NULL DEFAULT '',
				  `slug` char(192) NOT NULL DEFAULT '',
				  PRIMARY KEY (`virtuemart_manufacturer_id`),
				  UNIQUE KEY `slug` (`slug`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Language ".$this->config->sufix." for manufacturers' AUTO_INCREMENT=1";
		$this->_db->setQuery($query);
		$this->_db->Query();
	}


	function backup($tables) {
		$tables = (array) $tables;
		$backup_filename="virtuemart_backup_".date("d.m.Y_H_i_s").".sql";
		$fp = fopen(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$backup_filename , "a" );


			foreach($tables as $table){
			   $table = str_replace('#__',$this->_db->getPrefix(),$table);

			   $fields_list_array=$this->_db->getTableColumns($table);
			   $fields_list=array();
			   foreach($fields_list_array as $key=> $field){
				   $fields_list[]=$key;
			   }
			   $this->_db->setQuery("SELECT COUNT(*) FROM `{$table}`");
			   $total=$this->_db->loadResult();
			   fwrite($fp,"TRUNCATE TABLE `{$table}`;\n");
			   $i=0;
			   for(;;){
				   if($i>=$total)break;
				   $this->_db->setQuery("SELECT * FROM `{$table}`",$i,200);
				   $data=$this->_db->loadAssocList();
				   $i+=200;
				   if(!$data)break;
				   if(count($fields_list)){
					   fwrite($fp,"INSERT INTO `{$table}` (`".implode("`,`",$fields_list)."`) VALUES\n");
				   }
				   else{
					   fwrite($fp,"INSERT INTO `{$table}` VALUES\n");
				   }

				   $rows=array();

				   foreach($data as $key=> $row ){
				   	   $fields=array();
					   foreach($row as $field){
						   $field=str_replace(";\n",";",$field);
						   $fields[]=$this->_db->Quote($field);
					   }
					   $rows[]="(".implode(",",$fields).")";
				   }
				   if(count($rows)){
					   fwrite($fp,implode(",\n",$rows).";\n\n");
				   }
				   else
					   fwrite($fp,";\n\n");

			   }


			}

			fclose($fp);
			$size=filesize(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$backup_filename);
			if($size){
				$this->_db->setQuery("INSERT INTO #__excel2vm_backups SET file_name = '$backup_filename',size='$size'");
				$this->_db->Query();
				return true;
			}
			else
				return false;

	}
		function backup2($tables) {
		$tables = (array) $tables;
		array_walk($tables,create_function('&$val','$val = str_replace("#__","'.$this->_db->getPrefix().'",$val);'));
		$backup_filename="virtuemart_backup_".date("d.m.Y_H_i_s").".gz";
		$mainframe = JFactory::getApplication();
		$command = "mysqldump -h".$mainframe->getCfg('host')." -u".$mainframe->getCfg('user')." -p".$mainframe->getCfg('password')." ".$mainframe->getCfg('db')." ".implode(" ",$tables)." | gzip -9> ".JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$backup_filename;
		system($command,$output);
		if($output===0){
			$size=filesize(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'backup'.DS.$backup_filename);
			$this->_db->setQuery("INSERT INTO #__excel2vm_backups SET file_name = '$backup_filename',size='$size'");
			$this->_db->Query();
		}
	}



	function prepare($cells,$level=0,$cat=false,$csv=false) {

		$i = $csv?0:-1;
		foreach ($this->active as $f) {
			$i++;
			/*@$row[$f->name] = str_replace("'",'`',trim($cells[$i]));
			@$row[$f->name] = str_replace("\n","",$row[$f->name]);*/

			

			if($cat AND $this->config->cat_col==$i){
				$row['category_name']=$cells[$i];
				continue;
			}

			if($cat AND $this->config->cat_id_col==$i AND $this->config->price_template==6){
				$row['virtuemart_category_id']=$cells[$i];
				continue;
			}

						if($f->name!='product_s_desc' AND $f->name!='product_desc')
				@$row[$f->name] = trim(str_replace("\n","",$cells[$i]));
			else
				@$row[$f->name]= trim($cells[$i]);
			if(!isset($row[$f->name]) OR @$row[$f->name]=='')unset($row[$f->name]);




		}

		if(!$row)return false;
		$row['level']=$level;
		return $row;
	}

	function setVMsettings() {
		require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."config.php");
		$VmConfig=VmConfig::loadConfig();
		$VmConfig=$VmConfig->_params;

		$this->brows = @$VmConfig['categorytemplate'];
		$this->per_row = @$VmConfig['products_per_row'];
		$this->flypage = @$VmConfig['productlayout'];
		$this->img_width = @$VmConfig['img_width'];
		$this->img_height = @$VmConfig['img_height'];
		$this->config->product_lwh_uom=@$VmConfig['lwh_unit_default'];
		$this->config->product_weight_uom=@$VmConfig['weight_unit_default'];

		$this->m_date = date("Y-m-d H:i:s");


		$this->vm_product = new updateTable("#__virtuemart_products", "virtuemart_product_id");
		$this->vm_product_lang = new updateTable("#__virtuemart_products_".$this->config->sufix, "virtuemart_product_id");
		$this->vm_category = new updateTable("#__virtuemart_categories", "virtuemart_category_id");
		$this->vm_category_lang = new updateTable("#__virtuemart_categories_".$this->config->sufix, "virtuemart_category_id");
		$this->vm_product_categories = new updateTable("#__virtuemart_product_categories", "virtuemart_product_id");
		$this->vm_medias = new updateTable("#__virtuemart_medias", "virtuemart_media_id");
		$this->vm_product_medias = new updateTable("#__virtuemart_product_medias", "virtuemart_product_id");
		$this->vm_category_medias = new updateTable("#__virtuemart_category_medias", "virtuemart_category_id");
		$this->price_table = new updateTable("#__virtuemart_product_prices", "virtuemart_product_price_id");
		$this->extra_table = new updateTable("#__virtuemart_product_customfields", "virtuemart_customfield_id");
		$this->log_table = new updateTable("#__excel2vm_log", "log_id");
						$xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.xml');
		$this->vm_version=(string)$xml->version;
		$this->is_vm_version_3=(substr($this->vm_version,0,1)==3 OR substr($this->vm_version,0,3)=='2.9')?true:false;
		$this->fieldname_custom_value=$this->is_vm_version_3?'customfield_value':'custom_value';
		$this->fieldname_custom_price=$this->is_vm_version_3?'customfield_price':'custom_price';

		$this->_db->setQuery("SELECT f.name,f.extra_id,vc.field_type,vc.custom_value,vc.custom_element,vc.custom_params,vc.custom_parent_id, vc.is_list
							  FROM #__excel2vm_fields as f
							  LEFT JOIN #__virtuemart_customs as vc ON vc.virtuemart_custom_id=f.extra_id
							  WHERE f.id IN ($this->active_fields) AND f.type = 'extra' ORDER BY FIELD(f.id,{$this->active_fields})");
		$this->extra = $this->_db->loadObjectList();

		$this->_db->setQuery("SELECT id,name,extra_id
							  FROM #__excel2vm_fields
							  WHERE id IN ($this->active_fields) AND type = 'custom' ORDER BY FIELD(id,{$this->active_fields})");
		$this->custom_fields = $this->_db->loadObjectList('name');

				$this->_db->setQuery("SELECT f.name,f.extra_id
							  FROM #__excel2vm_fields as f

							  WHERE f.id IN ($this->active_fields) AND f.type = 'cherry' ORDER BY FIELD(f.id,{$this->active_fields})");
		$this->cherry = $this->_db->loadObjectList();

		$this->_db->setQuery("SELECT f.name,f.extra_id,f2.name as price,vc.field_type,vc.custom_value,vc.custom_element,vc.custom_parent_id, vc.custom_params,vc.is_list
							  FROM #__excel2vm_fields f
							  LEFT JOIN #__excel2vm_fields as f2 ON f2.extra_id = f.id AND f2.id IN ($this->active_fields) AND f2.type='extra-price'
							  LEFT JOIN #__virtuemart_customs as vc ON vc.virtuemart_custom_id=f.extra_id
							  WHERE f.id IN ($this->active_fields) AND f.type = 'extra-cart' ORDER BY FIELD(f.id,{$this->active_fields})");
		$this->extra_cart = $this->_db->loadObjectList();

		$this->_db->setQuery("SELECT virtuemart_custom_id FROM #__virtuemart_customs WHERE field_type = 'R' ORDER BY virtuemart_custom_id",0,1);
		$this->related_custom_id=$this->_db->loadResult();

	}


	function upload(){
		$success=0;
		$errors=array("Неизвестная ошибка","Размер прайса превысил максимально допустимый размер, который задан директивой upload_max_filesize конфигурационного файла php.ini. Обратитесь в тех. поддержку хостинга с просьбой увеличить лимит","Размер загружаемого файла превысил значение MAX_FILE_SIZE, указанное в HTML-форме","Загружаемый файл был получен только частично. Это может быть связано с нестабильным интернет-соединением или с проблемами на хостинге. Повторите попытку позже","Файл не был загружен","","Отсутствует временная папка","Не удалось записать файл на диск. Проверьте, достаточно ли места на диске","PHP-расширение остановило загрузку файла");
		$xls_dir=JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'xls';
		$perms=substr(sprintf('%o', fileperms($xls_dir)), -4);
		if((!is_readable($xls_dir) OR !is_writable($xls_dir)) AND DS=='/'){
			echo "<b><font color='#FF0000'>Прайс не может быть загружен, т.к. на папку $xls_dir установлены права - $perms. Установите права - 755</font></b>";
			exit();
		}
		$total_files=count(@$_FILES['xls_file']['name']);
		for($i=0;$i<$total_files;$i++){
			$file_type=strtolower(pathinfo($_FILES['xls_file']['name'][$i], PATHINFO_EXTENSION));
			if (!in_array($file_type,array('xls','csv','xlsx'))) {
				echo "<b><font color='#FF0000'>".JText::_('WRONG_FILE_FORMAT')."</font></b>";
				exit ();
			}
			if (!JFile :: upload($_FILES['xls_file']['tmp_name'][$i], $xls_dir . DS . $_FILES['xls_file']['name'][$i])) {
				if(!$_FILES['xls_file']['error'][$i]){
					 echo "<b><font color='#FF0000'>".JText::_('ERROR_DURING_UPLOAD')." - Ошибка не известна. Проверьте, достаточно ли места на сервере</font></b>";
				}
				else{
					echo "<b><font color='#FF0000'>".JText::_('ERROR_DURING_UPLOAD')." - ".$errors[$_FILES['xls_file']['error'][$i]]."</font></b>";
				}
				exit ();
			}
			else{
			  $success++;
			}
		}
		if($success==$total_files){
		  echo "Ok";
		}
		else{
			echo "<b><font color='#FF0000'>Загружено файлов: $success из $total_files</font></b>";
		}
	}


	function type($cells,$csv=false) {

		$i=$csv?0:1;

		foreach($this->active as $a ){
			if(in_array($a->name,array('file_url','file_url_thumb','file_meta','product_desc','slug','virtuemart_vendor_id','published','metakey','customtitle','metadesc','category_template','category_layout','category_product_layout','layout','ordering','img2','img3','img4','img5','img6','img7','img8','img9','img10'))){
			   if($a->ordering-$i==$this->config->cat_col){
					continue;
			   }
			   unset($cells[$a->ordering-$i]);			}

			if($a->type=='empty' AND $a->ordering-$i!=$this->config->cat_col){
				unset($cells[$a->ordering-$i]);
			}
		}

		switch ($this->config->price_template) {
		  case 1:
		  case 2:
		  case 3:
		  case 8:
				foreach ($cells as $key => $cell) {
					$cell = trim($cell);

					if (empty ($cell) AND $cell!==0){
						if(!(strlen($cell)==1 AND is_string($cell) AND $cell==="0")){
						   unset ($cells[$key]);
						}
					}

				}

				if (count($cells) == 1 AND @$cells[$this->config->cat_col]){
					return "category";
				}
				elseif (count($cells) > 1){
					return "product";
				}

		  break;
		  case 4:
				$col=$this->active['path']->ordering;
				if($col){
					if(@$cells[$col-$i])
						return "category";
					else
						return "product";
				}
				else{
					echo '<font color="#FF0000">'. JText::_('YOU_DID_NOT_SPECIFY_THE_COLUMN_NUMBER_CATEGORY').'!</font>';
					exit();
				}
		  break;
		  case 5:
				 return "product";
		  break;
		  case 6:

				foreach ($cells as $key => $cell) {
					$cell = trim($cell);
					if (empty ($cell))
						unset ($cells[$key]);
				}



				if(@$cells[$this->config->cat_col] AND @$cells[$this->config->cat_id_col] AND $this->config->cat_id_col_original>0){ 
					if(count($cells) == 2){
						return "category";
					}
					elseif(count($cells) == 3){
						$col=$this->active['path']->ordering;
						if($col){
							if(@$cells[$col-$i])
								return "category";
							else
								return "product";
						}
					}
					else{
					  return "product";
					}
				}
				else{
					return "product";
				}

		  break;
		  case 7:
				 return "product";
		  break;
		}

	}

	function get_mem(){
		if( function_exists("memory_get_usage") ) {
				$mem_usage = memory_get_usage(true);
				return round($mem_usage/1048576,2);
		 }
		 else return false;
	}

	function get_mem_total(){
		$mem=ini_get("memory_limit");
		if(strstr($mem,"M"))return (float)$mem;
		else{
			return round($mem/1048576,2);
		}
	}

	function get_mem_peak(){
		if( function_exists("memory_get_peak_usage") ) {
				$mem_usage = memory_get_peak_usage(true);
				return round($mem_usage/1048576,2);
		 }
		 else return false;
	}
	function updateStat($not_interrupt=false,$force_timeout=false) {
		if (time() - @$this->last_upd > 1) {
			$this->last_upd = time();
			@$data->cur_row=@$this->row-1;
			$data->num_row=@$this->numRow;
			$data->pn=(int)@$this->stat['pn'];
			$data->pu=(int)@$this->stat['pu'];
			$data->cn=(int)@$this->stat['cn'];
			$data->cu=(int)@$this->stat['cu'];
			$data->time=time() - $this->start_time;
			$data->cur_time=time();
			$data->cur_cat=@$this->current['category'];
			$data->cur_prod=@$this->current['product'];
			$data->mem=$this->get_mem();
			$data->mem_total=$this->mem_total;
			$data->mem_peak=$this->get_mem_peak();
			$data->filename=@$this->filename;
			$data->file_index=@$this->file_index == @$this->total_files?@$this->total_files-1:$this->file_index;
			$data->total_files=@$this->total_files;
			$data->status=@$this->status;
			$data->timeout=0;
			if($this->check_abort<time() AND !$not_interrupt){
				 if(file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'abort.txt')){
				 	$this->response();
				 }
				 $this->check_abort=time()+10;
			}
			if((time()>=$this->timeout AND !$not_interrupt) OR $force_timeout){
				$data->timeout=1;
				$data->cur_row++;
				if($this->cron){
					file_put_contents(dirname(__FILE__)."/cron_import_start.txt",$data->cur_row);
					$this->cron_log("Импорт завершен по таймауту. Строка - $data->cur_row");
				}
				else{
					echo "timeout";
				}

			}
			file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'log.txt', json_encode($data));
			if((time()>=$this->timeout AND !$not_interrupt) OR $force_timeout){
				@$dump->category_levels=$this->category_levels;
				$dump->level=@$this->level;
				$dump->last_child=@$this->last_child;
				$dump->last_parent=@$this->last_parent;
				$dump->last_path=@$this->last_path;
				$dump->last_parrent_array=@$this->last_parrent_array;
				$dump->tree=@$this->tree;

				file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS."xls-dump.txt",serialize($dump));

				exit();
			}

		}
	}

	function log($type,$vm_id,$title){
		@$this->stat[$type]++;
		if($this->show_results){
			$this->log_table->type=$type;
			$this->log_table->vm_id=$vm_id;
			$this->log_table->title=$title;
			$this->log_table->row=$this->row;
			$this->log_table->insert();
		}

	}


	function import() {
		$mtime=filemtime(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'log.txt');
		file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'abort.txt',0);

		$max_execution_time=ini_get('max_execution_time');

		if(!$max_execution_time){
		   $max_execution_time=300;
		}
		$this->timeout=time()+$max_execution_time-5;
				if(file_exists(JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'error.txt')){
				if(filesize(JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'error.txt')>2*1024*1024){
					file_put_contents(JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'error.txt','');
			  }
		}


		$this->check_abort=time()+10;
		if(!$this->cron){
		   $this->setCookies();
		}

		$lock = fopen(dirname(__FILE__).DS.'lock.run', 'w');
		if (!flock($lock, LOCK_EX | LOCK_NB)){
		   header('HTTP/1.1 502 Gateway Time-out');
		   jexit();
		}

		/*if(time() - $mtime < 10 AND !$this->reimport){
			header('HTTP/1.1 502 Gateway Time-out');
		   jexit();
		}   */
				$this->mem_total=$this->get_mem_total();
		$this->real_start_time=time();

		if($this->cron){
			$this->path_to_file=$this->cron_file_dir;
		}
		else{
			$this->path_to_file=JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'xls' . DS;
		}
		if($this->reimport){ 			$this->_db->setQuery("SELECT vm_id FROM #__excel2vm_log WHERE type = 'cn' OR type = 'cu' ORDER BY log_id DESC",0,1);
			$this->category_id=$this->_db->loadResult();
			$log=json_decode(file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'log.txt'));
			$_FILES['xls_file']=unserialize(@file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'uploaded_files.txt'));
			$this->start_time=time()-$log->time;

			$this->file_index=$log->file_index;
			$this->first_row=$log->cur_row;
			if($this->first_row<$this->config->first){
			   $this->first_row=$this->config->first;
			}

			$stat_type=array('pn','pu','cn','cu');
			foreach($stat_type as $type){
			   @$this->stat[$type]=$log->$type;
			}

			$dump=file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS."xls-dump.txt");
			@$dump=unserialize($dump);
			file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'report.txt',date("Y-m-d H:i:s")." - Перезапуск. Данные:\n	 Категория - $this->category_id\n	 Первая строка - $this->first_row\n	 Лог - ".print_r($log,true)."\n	 Дамп - ".print_r($dump,true)."\n\n",FILE_APPEND);


			if(@$dump->level)$this->level=$dump->level;
			if(@$dump->last_child)$this->last_child=$dump->last_child;
			if(@$dump->last_parent)$this->last_parent=$dump->last_parent;
			if(@$dump->last_path)$this->last_path=$dump->last_path;
			if(@$dump->category_levels)$this->category_levels=$dump->category_levels;
			if(@$dump->last_parrent_array)$this->last_parrent_array=$dump->last_parrent_array;
			if(@$dump->tree)$this->tree=$dump->tree;

			if($this->config->images_import_method){
				$this->images_collection=unserialize(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'images_collection.txt');
			}
			if($this->need_profiler){
				$this->profiler = new JProfiler();
				$this->profiler_log('Reimport');
			}
		}
		else{
			file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'report.txt',date("Y-m-d H:i:s")." - Начало импорта\n");
			if($this->need_profiler){
				$this->profiler = new JProfiler();
				file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS."profiler.txt","");
				$this->profiler_log('Begin');

			}
			//$this->immport();
			if($this->config->unpublish){ 				if(@$this->config->unpublish_resume){					 if(!@$this->config->unpublish_categories OR (count(@$this->config->unpublish_categories)==1 AND @$this->config->unpublish_categories[0]==0)){
						$this->_db->setQuery("UPDATE #__virtuemart_products as p
											  SET published = 0
											");
						$this->_db->Query();
					}
					else{
						JArrayHelper::toInteger($this->config->unpublish_categories);
						$cats=implode(",",$this->config->unpublish_categories);
						$this->_db->setQuery("UPDATE #__virtuemart_products as p
											  LEFT JOIN #__virtuemart_product_categories as c USING(virtuemart_product_id)
											  SET published = 0
											  WHERE virtuemart_category_id NOT IN($cats)
											  ");
						$this->_db->Query();
					}
				}
				else{					   if(!@$this->config->unpublish_categories OR (count(@$this->config->unpublish_categories)==1 AND @$this->config->unpublish_categories[0]==0)){
						$this->_db->setQuery("UPDATE #__virtuemart_products SET published = 0");
						$this->_db->Query();
					}
					else{
						JArrayHelper::toInteger($this->config->unpublish_categories);
						$cats=implode(",",$this->config->unpublish_categories);
						$this->_db->setQuery("UPDATE #__virtuemart_products as p
											  LEFT JOIN #__virtuemart_product_categories as c USING(virtuemart_product_id)
											  SET published = 0
											  WHERE virtuemart_category_id IN($cats)
											  ");
						$this->_db->Query();
					}
				}


			}

			
			if(@$this->config->reset_stock){ 
				if(@$this->config->reset_resume){					 if(!@$this->config->reset_categories OR (count(@$this->config->reset_categories)==1 AND @$this->config->reset_categories[0]==0)){
											}
					else{
						JArrayHelper::toInteger($this->config->reset_categories);
						$cats=implode(",",$this->config->reset_categories);
						$this->_db->setQuery("UPDATE #__virtuemart_products as p
											  LEFT JOIN #__virtuemart_product_categories as c USING(virtuemart_product_id)
											  SET product_in_stock = 0
											  WHERE virtuemart_category_id NOT IN($cats)
											  ");
						$this->_db->Query();
					}
				}
				else{					if(!@$this->config->reset_categories OR (count(@$this->config->reset_categories)==1 AND @$this->config->reset_categories[0]==0)){
						$this->_db->setQuery("UPDATE #__virtuemart_products SET product_in_stock = 0");
						$this->_db->Query();
					}
					else{
						JArrayHelper::toInteger($this->config->reset_categories);
						$cats=implode(",",$this->config->reset_categories);
						$this->_db->setQuery("UPDATE #__virtuemart_products as p
											  LEFT JOIN #__virtuemart_product_categories as c USING(virtuemart_product_id)
											  SET product_in_stock = 0
											  WHERE virtuemart_category_id IN($cats)
											  ");
						$this->_db->Query();
					}
				}


			}
						if($this->config->delete_related){
				$this->_db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE virtuemart_custom_id = 1");
				$this->_db->Query();
			}
						$this->file_index=0;
			$this->_db->setQuery("TRUNCATE TABLE #__excel2vm_log");
			$this->_db->Query();
			$this->_db->setQuery("TRUNCATE TABLE #__excel2vm_multy");
			$this->_db->Query();
			$this->start_time=time();
			$stat_type=array('pn','pu','cn','cu');
			foreach($stat_type as $type){
				@$this->stat[$stat_type]=0;
			}

						if ($this->config->auto_backup AND !$this->reimport){
				$this->status=JText::_('BACKUP_OF_TABLES');
				$this->updateStat();
				if($this->config->backup_type)
					$this->backup2($this->backup_tables_array);
				else{
					$this->backup($this->backup_tables_array);
				}
			}
		}


				$uploaded_file = JRequest :: getVar('uploaded_file', '', '', 'array');

		if (@$_FILES['zip_file']['name'])
			$this->load_img();


		if($this->cron AND $this->import_file_name){ 
			 $this->total_files=1;
		}
		elseif (count($uploaded_file) AND $uploaded_file[0]!='') {
			$this->total_files=count($uploaded_file);

		}

		else {
			echo "<b><font color='#FF0000'>". JText::_('UNKNOWN_FILE_IMPORT')."!</font></b>";
			exit ();
		}

		$this->setVMsettings();
		$this->status="";
		$this->profiler_log( __LINE__." - preparations finish" );
		for($this->file_index;$this->file_index<$this->total_files;$this->file_index++):

			if($this->cron AND $this->import_file_name){
				$this->filename=$filename=$this->import_file_name;
			}
			else
				$this->filename=$filename=$uploaded_file[$this->file_index];

			$this->last_upd = time() - 1;
						$file_type=substr($filename,-4);
			if($file_type=='.csv'){
				$time=time();
				$handle2 = fopen ($this->path_to_file . $filename, "r");
				$this->numRow=0;
				$last=(int)($this->config->last);
				while (!feof($handle2)) {
				  fgets($handle2,8096);
				  $this->numRow++;
				  if($this->numRow>$last AND $last>0){
					  $this->numRow=$last;
					  break;
				  }
				}
				fclose($handle2);
				unset($handle2);


				$handle = fopen ($this->path_to_file . $filename, "r");

				$this->row=0;
				while (!feof ($handle)) {
					 $this->row++;

					 $cells = fgets($handle,8096);
					 if($this->create_custom_fields AND $this->row==1){						 if($this->csv_convert)
						 	$headers= explode($this->csv_field_delimiter,iconv('WINDOWS-1251', 'UTF-8', $cells));
						 else
						 	$headers= explode($this->csv_field_delimiter, $cells);

						 for($i=0;$i<count($headers);$i++){
	  					 	if($i==0)$headers[$i]=str_replace($this->csv_row_delimiter,'',$headers[$i]);
	  					 	if($i==count($headers)-1)$headers[$i]=str_replace($this->csv_row_delimiter,'',$headers[$i]);
							  $headers[$i]=str_replace('%3B',';',$headers[$i]);
	  					 }
						 array_unshift($headers,0);
						 unset($headers[0]);

						 $total_cols=count($headers);
						 $last_active=count($this->active);


						 if($total_cols>$last_active){
							 $this->_db->setQuery("SELECT active FROM #__excel2vm WHERE default_profile = 1");
							 $fields_ids=explode(",",$this->_db->loadResult());


							 foreach($headers as $key=> $extra_field_name){
								 if($key<=$last_active){
									 continue;
								 }
								 if(!trim($extra_field_name)){
									 $fields_ids[]=$this->insertEmpty();
									 continue;
								 }

								 $virtuemart_custom_id=$this->getCustomFieldID($extra_field_name);
								 $import_field_id=$this->getImportFieldID($virtuemart_custom_id,$extra_field_name);								$name_index=0;
								 while(in_array($import_field_id,$fields_ids)){
									 $name_index++;
									 $virtuemart_custom_id=$this->getCustomFieldID($extra_field_name."-".$name_index);
									 $import_field_id=$this->getImportFieldID($virtuemart_custom_id,$extra_field_name."-".$name_index);
								 }
								 $fields_ids[]=$import_field_id;

							 }

							 $active=implode(",",$fields_ids);
							 $this->_db->setQuery("UPDATE #__excel2vm SET active = '$active' WHERE default_profile = 1");
							 $this->_db->Query();
							 $this->active_fields=$active;

							 $this->active = $this->getActive();
							 $this->setVMsettings();
							 unset($headers);
							 unset($fields_ids);
						 }

					 }
					 if($this->row<$this->first_row)continue;
					 if($this->row >= $this->numRow)break;
					 if(empty($cells))continue;

					 if($this->csv_convert)
					 	$cells_array= explode($this->csv_field_delimiter,iconv('WINDOWS-1251', 'UTF-8', $cells));
					 else
					 	$cells_array= explode($this->csv_field_delimiter, $cells);
					 for($i=0;$i<count($cells_array);$i++){
					 	if($i==0)$cells_array[$i]=str_replace($this->csv_row_delimiter,'',$cells_array[$i]);
					 	if($i==count($cells_array)-1)$cells_array[$i]=str_replace($this->csv_row_delimiter,'',$cells_array[$i]);
						$cells_array[$i]=str_replace('%3B',';',$cells_array[$i]);
					 }

					 array_unshift($cells_array,0);
					 unset($cells_array[0]);

					 switch ($this->type($cells_array,true)) {
						case 'product' :

							$this->insertProduct($this->prepare($cells_array,false,false,true));

							break;
						case 'category' :
							$this->insertCategory($this->prepare($cells_array,false,true));
							break;
					}
					$this->updateStat();

				}
				fclose ($handle);
			}
			elseif($file_type=='.xls' OR $file_type=='xlsx'){
					if($this->file_index==0){
						$this->first_row--;
						$this->config->cat_col--;
						if($this->config->cat_id_col){
						   $this->config->cat_id_col--;
						}
					}

					/*require_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'libraries' . DS . 'reader.php');					$data = new Spreadsheet_Excel_Reader();
					$data->setOutputEncoding('UTF-8');
					$data->read($this->path_to_file . $filename);*/
					require_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'libraries' . DS . 'PHPExcel'. DS .'IOFactory.php');
					$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_in_memory_gzip;
										$cacheSettings = array( 'memoryCacheSize'  => '8MB');
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
					$objReader = PHPExcel_IOFactory::createReader($file_type=='.xls'?'Excel5':'Excel2007');

					$this->numRow= 65536;
					if(is_numeric($this->config->last)){
						$this->numRow=$this->config->last;
						if($this->chunk_on){							if($this->config->last<$this->chunkSize)
							   $this->chunkSize=$this->config->last;
						}
					}


					/*$chunkFilter = new chunkReadFilter();
					$objReader->setReadFilter($chunkFilter);*/
					$worksheetList = $objReader->listWorksheetNames($this->path_to_file . $filename) ;

					$worksheetName=$worksheetList[0];
					$objReader->setLoadSheetsOnly($worksheetName);
					

					$last_col_latter=$this->getNameFromNumber(count($this->active)-1);

					$this->row=$this->first_row;

					if($this->config->images_import_method){
						 if($this->chunk_on){							 $chunkFilter = new chunkReadFilter();
							 $objReader->setReadFilter($chunkFilter);
							 $chunkFilter->setRows($this->row,10);
						 }
						 $objPHPExcel2 = $objReader->load($this->path_to_file . $filename);

						 $this->extractImages($objPHPExcel2);

						 unset($objPHPExcel2);

					}

					$data=$objReader->listWorksheetInfo($this->path_to_file . $filename);

					$total_rows=$data[0]['totalRows'];
					if(!$total_rows){
						 echo '<br><span style="color: #CC0000">Внимание! Отсутствуют данные на листе "'.$worksheetName.'". Компонент считывает данные только с первого листа!</span><br>';
					}


										if($this->create_custom_fields){
						$total_cols=$data[0]['totalColumns'];
						$last_active=count($this->active);
						if($total_cols>$last_active){
							 $objReader = PHPExcel_IOFactory::createReader($file_type=='.xls'?'Excel5':'Excel2007');
							 $chunkFilter = new chunkReadFilter();
							 $objReader->setReadFilter($chunkFilter);
							 $chunkFilter->setRows(1,2);
							 $objReader->setLoadSheetsOnly($worksheetName);
							 $objReader->setReadDataOnly(true);
							 $objPHPExcel = $objReader->load($this->path_to_file . $filename);
							 $data= $objPHPExcel->getActiveSheet()->rangeToArray($this->getNameFromNumber($last_active)."1:".$this->getNameFromNumber($total_cols)."1",null,true,true,false);
							 $headers=$data[0];
							 $this->_db->setQuery("SELECT active FROM #__excel2vm WHERE default_profile = 1");
							 $fields_ids=explode(",",$this->_db->loadResult());
							 foreach($headers as $extra_field_name){
								 if(!trim($extra_field_name)){
									 $fields_ids[]=$this->insertEmpty();
									 continue;
								 }
								 $virtuemart_custom_id=$this->getCustomFieldID($extra_field_name);
								 $import_field_id=$this->getImportFieldID($virtuemart_custom_id,$extra_field_name);								$name_index=0;
								 while(in_array($import_field_id,$fields_ids)){
									 $name_index++;
									 $virtuemart_custom_id=$this->getCustomFieldID($extra_field_name."-".$name_index);
									 $import_field_id=$this->getImportFieldID($virtuemart_custom_id,$extra_field_name."-".$name_index);
								 }
								 $fields_ids[]=$import_field_id;

							 }

							 $active=implode(",",$fields_ids);
							 $this->_db->setQuery("UPDATE #__excel2vm SET active = '$active' WHERE default_profile = 1");
							 $this->_db->Query();
							 $this->active_fields=$active;
							 $this->setVMsettings();
							 $this->active = $this->getActive();
							 $last_col_latter=$this->getNameFromNumber($total_cols);
							 unset($headers);
							 unset($fields_ids);
							 unset($objPHPExcel);
							 unset($objReader);
							 unset($chunkFilter);

						}

					}


					if(!$this->chunk_on){						 $this->chunkSize=$total_rows;
					}
					unset($objReader);


					for ($startRow = $this->first_row; $startRow < $this->numRow; $startRow += $this->chunkSize) {


						 $objReader = PHPExcel_IOFactory::createReader($file_type=='.xls'?'Excel5':'Excel2007');

						 if($this->chunk_on){							 $chunkFilter = new chunkReadFilter();
							 $objReader->setReadFilter($chunkFilter);
							 $chunkFilter->setRows($startRow,($this->chunkSize>$this->numRow-$startRow)?($this->numRow-$startRow+1):($this->chunkSize+1));
						 }


						 $objReader->setLoadSheetsOnly($worksheetName);




						 						 if($this->config->price_template!=8)
						 	$objReader->setReadDataOnly(true);
						 file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'report.txt',date("Y-m-d H:i:s")." - Началось считывание данных. Память - ".$this->get_mem()."\n",FILE_APPEND);
						 $before_read_time=time();
						 try{
						   $objPHPExcel = $objReader->load($this->path_to_file . $filename);
						 }
						 catch(Exception $e){
							 echo "<span style=\"color: #CC0000;font-weight:bold\">Ошибка при чтении данных листа: ".$e->getMessage()."</span>";exit();
						 }
						 if(!$objPHPExcel->getActiveSheet()){
							 echo "<span style=\"color: #CC0000;font-weight:bold\">Ошибка при чтении данных листа. На листе возможны ошибки. Скопируйте лист в новый файл и сохраните его в формате xlsx. Импортируйте новый файл</span>";exit();
						 }
						  file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'report.txt',date("Y-m-d H:i:s")." - Считывание данных завершено. Память - ".$this->get_mem()."\n",FILE_APPEND);
												  $total_read_time=time()-$before_read_time;

												  if($this->numRow>$total_rows)$this->numRow=$total_rows;
						 if($this->chunk_on){							$end_row=$startRow+$this->chunkSize>$this->numRow?$this->numRow:$startRow+$this->chunkSize;
						 }
						 else{
							 $end_row=$this->numRow;
						 }
												  try{
							$all_cells= $objPHPExcel->getActiveSheet()->rangeToArray("A".($startRow+1).":".$last_col_latter.($end_row),null,true,true,false);
						 }
						 catch(Exception $e){
							echo "<span style=\"color: #CC0000;font-weight:bold\">Ошибка при чтении данных листа: ".$e->getMessage()."</span>";exit();
						 }
						 $total_rows2=count($all_cells);
						 for($i=0;$i<$total_rows2;$i++) {
							$cells=$all_cells[$i];
						 	$this->row++;
							unset($all_cells[$i]);

							$level=$this->config->price_template==8?$objPHPExcel->getActiveSheet()->getRowDimension($this->row)->getOutlineLevel():0;

							switch ($this->type($cells)) {
								case 'product' :
									$this->insertProduct($this->prepare($cells,$level));

									break;
								case 'category' :
									$this->insertCategory($this->prepare($cells,$level,true));

									break;
							}

							$this->updateStat();
							unset($cells);
						}

						$objPHPExcel->disconnectWorksheets();
						unset($objPHPExcel);
						unset($objReader);
						$this->last_upd = - 2;
												if((time()+$total_read_time+2)>=$this->timeout AND $total_rows-$this->row>1 AND !$this->cron){
							$this->updateStat(false,true);
						}
						else{
							$this->updateStat();
						}

					 }
			}
			if($this->reimport){
			   $this->reimport=0;
			   $this->first_row=$this->config->first;
			}

		endfor;
		$this->last_upd = - 2;
		$this->row++;
		$this->updateStat(true);

				if($this->related_custom_id){
			$this->_db->setQuery("SELECT product_id, sku FROM #__excel2vm_related_products");
			$data=$this->_db->loadObjectList();
			if(count($data)){
				foreach($data as $r){
					$this->_db->setQuery("SELECT p.virtuemart_product_id
									  FROM #__virtuemart_products as p
									  LEFT JOIN #__virtuemart_products_".$this->config->sufix." as r ON r.virtuemart_product_id = p.virtuemart_product_id
									  WHERE product_sku='$r->sku' AND r.virtuemart_product_id IS NOT NULL");
					$related_id = $this->_db->loadResult();
					if($related_id){
						 $this->_db->setQuery("INSERT INTO #__virtuemart_product_customfields SET virtuemart_custom_id = {$this->related_custom_id}, virtuemart_product_id = '{$r->product_id}', {$this->fieldname_custom_value} = ".$this->_db->Quote($related_id).", customfield_params =  ".$this->_db->Quote('wPrice=0|wImage=1|wDescr=0|width="'.$this->img_width.'"|height="'.$this->img_height.'"|'));
						 $this->_db->Query();
					}
					else{
						echo JText::_('SKU_NOT_FOUND').":$r->sku<br>";
					}
				}
				$this->_db->setQuery("TRUNCATE TABLE #__excel2vm_related_products");
				$this->_db->Query();
			}
		}


				$this->bind_multivars();

		@unlink(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS."xls-dump.txt","");
		@unlink(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'uploaded_files.txt');
		@unlink(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'images_collection.txt');
		file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'report.txt',date("Y-m-d H:i:s")." - Импорт завершен.\n",FILE_APPEND);
		if(!$this->cron){
			$this->response();
			exit ();
		}
		else{
			file_put_contents(dirname(__FILE__)."/cron_import_start.txt",0);
		}

	}
	function insertCategory($row) {

		$mark_up = 1;
		switch ($this->config->price_template) {
		  case 1:				 $cell = trim($row['category_name']);
				if (!preg_match("/^\d{1,4}\./", $cell) AND $this->last_parent) {					$cell = $this->last_parent . '.' .++$this->last_child . '.' . $cell;
					$mark_up = 0;
				}
		  break;
		  case 2:								 $this->prev_level=$this->level?$this->level:1;
				$this->level=1;
				while($row['category_name'][0]==$this->config->simbol){
					$this->level++;
					$row['category_name']=substr($row['category_name'],1);
				}

				if($this->prev_level>$this->level){					 @$this->category_levels[$this->prev_level]=0;
				}

				@$this->category_levels[$this->level]++;


				$prefix = '';
				for($l=1;$l<=$this->level;$l++)
					$prefix.=$this->category_levels[$l]?$this->category_levels[$l].'.':'';
				$cell = $prefix . trim($row['category_name']);

		  break;

		  case 3:								 $this->prev_level=$this->level?$this->level:1;
				$this->level=1;
				while($row['category_name'][strlen($row['category_name'])-1]==$this->config->simbol){
					$this->level++;
					$row['category_name']=substr($row['category_name'],0,-1);
				}
				if($this->prev_level>$this->level){					 @$this->category_levels[$this->prev_level]=0;
				}

				@$this->category_levels[$this->level]++;
				$prefix = '';
				for($l=1;$l<=$this->level;$l++)
					$prefix.=$this->category_levels[$l]?$this->category_levels[$l].'.':'';
				$cell = $prefix . trim($row['category_name']);
		  break;

		  case 4: 
				 $cell = $row['path'].'.'.$row['category_name'];

		  break;

		}
		if($this->config->price_template==6){			  $cell = trim($this->escape($row['category_name']));
			$bak=$row['virtuemart_category_id'].".".$cell;
			$parent_id=@$row['path']?$row['path']:0;

		}

		elseif($this->config->price_template!=8){
			$bak = $this->escape($cell);
			$temp = explode(".", $cell);
			while (preg_match("/^\d{1,4}$/", trim($temp[0])) AND count($temp)>1) {
				$path[] = (int) array_shift($temp);
			}
			$cell = implode('.', $temp);
			if ($mark_up) {
	
				$this->last_parent = implode('.', @$path);
				$this->last_child = 0;
			}
			$this->last_path = implode('.',@$path);

				$path_bak=$path;
			if(isset($path))
				$nomber = array_pop($path);

			if (empty ($path)) {
				$parent_id = 0;
			}
			else {

				$parent_id=$this->tree[implode('.', $path)];
			}
			$cell = trim($this->escape($cell));
		}
		else{			$parent_id= (int)@$this->last_parrent_array[$row['level']-1];
			$cell = trim($this->escape($row['category_name']));
			$bak=$cell;
			for($i=1;$i<=$row['level'];$i++){
				if($i==1)
					$bak="|_".$bak;
				else
					$bak="|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$bak;
			}

			$this->_db->setQuery("SELECT MAX(c.ordering) FROM #__virtuemart_categories as c
								  LEFT JOIN #__virtuemart_category_categories as cc ON cc.category_child_id = c.virtuemart_category_id
								  WHERE cc.category_parent_id = $parent_id");
			$nomber=$this->_db->loadResult()+1;
		}
		if($cell=='')return false;
		$cell=stripslashes($cell);

		if(@$row['virtuemart_category_id']){
			$query = "SELECT r.virtuemart_category_id
					  FROM #__virtuemart_categories_".$this->config->sufix." as r
					  LEFT JOIN #__virtuemart_categories as c ON c.virtuemart_category_id = r.virtuemart_category_id
					  WHERE category_name=".$this->_db->Quote($cell)." AND c.virtuemart_category_id IS NOT NULL AND r.virtuemart_category_id = ".$this->_db->Quote($row['virtuemart_category_id'])."";
		}
		else{
			if(is_int($parent_id)){
				 $query = "SELECT r.virtuemart_category_id
					  FROM #__virtuemart_categories_".$this->config->sufix." as r
					  LEFT JOIN #__virtuemart_categories as c ON c.virtuemart_category_id = r.virtuemart_category_id
					  LEFT JOIN #__virtuemart_category_categories as cc ON cc.category_child_id = c.virtuemart_category_id
					  WHERE category_name=".$this->_db->Quote($cell)."
					  AND c.virtuemart_category_id IS NOT NULL
					  AND cc.category_parent_id=$parent_id";
			}
			else{
				$query = "SELECT r.virtuemart_category_id
					  FROM #__virtuemart_categories_".$this->config->sufix." as r
					  LEFT JOIN #__virtuemart_categories as c ON c.virtuemart_category_id = r.virtuemart_category_id
					  WHERE category_name=".$this->_db->Quote($cell)." AND c.virtuemart_category_id IS NOT NULL";
			}

		}

		$this->_db->setQuery($query,0,1);
		$this->category_id = (int)$this->_db->loadResult();


		$this->vm_category->reset(1);
		$this->vm_category_lang->reset(1);
		$this->vm_category->bind($row);

		$this->vm_category_lang->bind($row);
				$this->vm_category_lang->category_description=isset($row['product_desc'])?$row['product_desc']:NULL;
		$this->vm_category_lang->category_name=$cell;
		if(@$row['modified_on']){
			$this->vm_category->modified_on=date("Y-m-d H:i:s",strtotime($row['modified_on']));
			$this->vm_category->modified_on=is_numeric($row['modified_on'])?date("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($row['modified_on'])):date("Y-m-d H:i:s",strtotime($row['modified_on']));
		}
		else{
			$this->vm_category->modified_on=$this->m_date;
		}

		if ($this->category_id) {
			$this->vm_category->modified_by=$this->user_id;
			$this->vm_category->virtuemart_category_id=$this->category_id;
			$this->vm_category_lang->virtuemart_category_id=$this->category_id;
			$this->vm_category->update();
			$this->vm_category_lang->update();
			$this->log('cu',$this->category_id,stripslashes($bak));			 if((int)@$row['ordering']){
				$this->_db->setQuery("UPDATE #__virtuemart_category_categories SET ordering=".$this->_db->Quote((int)$row['ordering'])." WHERE category_child_id = $this->category_id");
				$this->_db->Query();
			}
		}
		else {			$this->vm_category->virtuemart_vendor_id=isset($row['virtuemart_vendor_id'])?$row['virtuemart_vendor_id']:1;
			if(@$row['created_on']){
				$this->vm_category->created_on=date("Y-m-d H:i:s",strtotime($row['created_on']));
				$this->vm_category->created_on=is_numeric($row['created_on'])?date("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($row['created_on'])):date("Y-m-d H:i:s",strtotime($row['created_on']));
			}
			else{
				 $this->vm_category->created_on=$this->m_date;
			}

			$this->vm_category->modified_by=$this->user_id;
			$this->vm_category->created_by=$this->user_id;
			$this->vm_category->category_template=$this->vm_category->category_template?$this->vm_category->category_template:$this->brows;
			$this->vm_category->category_product_layout=$this->vm_category->category_product_layout?$this->vm_category->category_product_layout:$this->flypage;
			$this->vm_category->products_per_row=$this->vm_category->products_per_row?$this->vm_category->products_per_row:$this->per_row;
			$this->vm_category->category_layout=$this->vm_category->category_layout?$this->vm_category->category_layout:NULL;
			$this->vm_category->published=isset($row['published'])?$row['published']:1;
			@$this->vm_category->ordering=(int)$row['ordering']?(int)$row['ordering']:$nomber;


			$this->category_id = $this->vm_category->insert();

			$this->vm_category_lang->slug= isset($row['slug'])?$row['slug']:$this->getAlias(stripslashes($cell),$this->category_id,false,false);

			$this->vm_category_lang->virtuemart_category_id= $this->category_id;

			$this->vm_category_lang->insert();

			$ordering=(int)@$row['ordering'];

			$query = "INSERT INTO #__virtuemart_category_categories SET category_parent_id='$parent_id', category_child_id='$this->category_id'".($ordering?", ordering = '$ordering'":"");
			$this->_db->setQuery($query);
			$this->_db->Query();
			$this->log('cn',$this->category_id,stripslashes($bak));		 }
		if($this->config->price_template!=8){
			$this->tree[implode('.', $path_bak)]= $this->category_id;
		}
		else{			 $this->last_parrent_array[$row['level']]=$this->category_id;
		}

		@$this->product_order = 0;

				if(!@$row['file_url'] AND @$this->images_categories_default){
			$row['file_url']= $this->images_categories_default;
		}
		if(isset($row['file_url'])){


			@$file_url=$row['file_url'];
		}
		if(isset($row['file_url_thumb'])){
			@$file_url_thumb=$row['file_url_thumb'];
		}

		if (@$file_url OR @$file_url_thumb) {
			$this->vm_medias->reset(1);
			$this->vm_category_medias->reset(1);
			if(@$file_url){
				if(strstr($file_url,'http://') OR strstr($file_url,'https://')){
						$this->vm_medias->file_url=$this->get_images_http($file_url,$this->category_id,false,true);					   if(!$file_url_thumb AND $this->vm_medias->file_url){
							$thumb_path=str_replace("/product","/category",$this->config->thumb_path);
							$temp=explode("/",$this->vm_medias->file_url);
							$thumb_name=end($temp);
							if($_POST['prefix']){
								$thumb_name=$_POST['prefix'].$thumb_name;
							}
							if($_POST['sufix']){
							   $temp=explode(".",$thumb_name);
							   $temp[count($temp)-2]=$temp[count($temp)-2].$_POST['sufix'];
							   $thumb_name=implode(".",$temp);
							}
							if(!$this->resizeImageMagic(JPATH_ROOT.DS.$this->vm_medias->file_url,(int)$_POST['width'],(int)$_POST['height'],0,0,JPATH_ROOT.DS.$thumb_path.$thumb_name,100)){
								$error=error_get_last();
								echo "Ошибка создания миниатюры категории ".(@$cell)." - ".$error['message']."<br>";
							}
							$this->vm_medias->file_url_thumb=$thumb_path.$thumb_name;
						}
				}
				else{
					 $this->vm_medias->file_url=$file_url;
				}
			}
			else{
				$this->vm_medias->file_url=NULL;
			}

			if(@$file_url_thumb){
				if(strstr($file_url_thumb,'http://') OR strstr($file_url_thumb,'https://')){
						$this->vm_medias->file_url_thumb=$this->get_images_http($file_url_thumb,$this->category_id,false,false);
				}
				else{
					 $this->vm_medias->file_url_thumb=$file_url_thumb;
				}
			}
			else{
				$this->vm_medias->file_url_thumb=NULL;
			}





			$this->vm_medias->published = 1;

			if(strstr($this->vm_medias->file_url,JURI::root())){
			   $this->vm_medias->file_url=str_replace(JURI::root(),'',$this->vm_medias->file_url);
			}

			if(strstr($this->vm_medias->file_url_thumb,JURI::root())){
			   $this->vm_medias->file_url_thumb=str_replace(JURI::root(),'',$this->vm_medias->file_url_thumb);
			}
			$this->vm_medias->file_title = $this->category_id.'-'.$this->translit($cell);
			$this->vm_medias->file_mimetype = 'image/jpeg';			$this->vm_medias->file_type = 'category';
			$this->vm_medias->file_is_product_image = 0;


						/*if($this->vm_medias->file_url_thumb){
				if(!in_array(substr($this->vm_medias->file_url_thumb,-3),array('jpg','gif','png','bmp','jpeg'))){
					$this->vm_medias->file_url_thumb .='.jpg';
				}
			}

			if($this->vm_medias->file_url){
				if(!in_array(substr($this->vm_medias->file_url,-3),array('jpg','gif','png','bmp','jpeg'))){
					$this->vm_medias->file_url.='.jpg';
				}
			}*/


			$this->_db->setQuery("SELECT virtuemart_media_id
								  FROM #__virtuemart_category_medias as p
								  WHERE virtuemart_category_id = $this->category_id");
			$virtuemart_media_ids = $this->_db->loadColumn();
			if(count($virtuemart_media_ids)){
				$this->_db->setQuery("DELETE FROM #__virtuemart_category_medias WHERE virtuemart_media_id IN(".implode(",",$virtuemart_media_ids).")");
				$this->_db->execute();
				$this->_db->setQuery("DELETE FROM #__virtuemart_medias WHERE virtuemart_media_id IN(".implode(",",$virtuemart_media_ids).")");
				$this->_db->execute();
			}

			$this->vm_category_medias->virtuemart_media_id = $this->vm_medias->insert();
			$this->vm_category_medias->virtuemart_category_id = $this->category_id;
			$this->vm_category_medias->insert();

		}
		$this->current['category']=$bak;
	}

	function insertProduct($row) {
				$this->profiler_log( __LINE__." - product. Row - ".$this->row);

		if(empty($row))return;
		if($this->config->price_template==6 AND !isset($this->active['path'])){
			echo '<font color="#FF0000">Не указана колонка для ID категорий! ("Номер категории")</font>';
			exit();
		}

		if(@$row['modified_on']){
			$row['modified_on']=is_numeric($row['modified_on'])?gmdate("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($row['modified_on'])):date("Y-m-d H:i:s",strtotime($row['modified_on']));
		}
		else{
			$row['modified_on']=$this->m_date;
		}



		$row['modified_by'] = $this->user_id;


		if(@$row['product_name']){
		   $row['product_name']=htmlspecialchars($row['product_name']);
		}

		
		/*if(isset($row['product_desc']))
		   $row['product_desc']=str_replace("'",'"',stripslashes(str_replace("\n","<br />",$row['product_desc'])));
		if(isset($row['product_s_desc']))
		   $row['product_s_desc']=str_replace("'",'"',stripslashes(str_replace("\n","<br />",$row['product_s_desc'])));*/



		$new=0;
		$cancel=0;

		$virtuemart_product_id=$this->identity($row,$new,$cancel);

		$row['virtuemart_product_id']= $virtuemart_product_id?$virtuemart_product_id:NULL;
		if($cancel){			 return false;
		}

		if(!$new AND !$this->config->is_update){
			return false;
		}


		if($virtuemart_product_id AND @$row['delete']==1){						  $this->_db->setQuery("DELETE FROM #__virtuemart_products WHERE virtuemart_product_id = '$virtuemart_product_id'");
			 $this->_db->Query();

			 $this->_db->setQuery("DELETE FROM #__virtuemart_products_".$this->config->sufix." WHERE virtuemart_product_id = '$virtuemart_product_id'");
			 $this->_db->Query();

			 $this->_db->setQuery("DELETE FROM #__virtuemart_product_categories WHERE virtuemart_product_id = '$virtuemart_product_id'");
			 $this->_db->Query();

			 $this->_db->setQuery("DELETE FROM #__virtuemart_product_medias WHERE virtuemart_product_id = '$virtuemart_product_id'");
			 $this->_db->Query();

			 $this->_db->setQuery("DELETE FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id = '$virtuemart_product_id'");
			 $this->_db->Query();

			 $this->_db->setQuery("DELETE FROM #__virtuemart_product_prices WHERE virtuemart_product_id = '$virtuemart_product_id'");
			 $this->_db->Query();

			 $this->_db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = '$virtuemart_product_id'");
			 $this->_db->Query();
						  return false;
		}
		elseif(@$row['delete']==1){
			return false;
		}

						list($category_id,$category_ids)=$this->getCategoryID_and_IDs($row);
		
		if(!$category_id AND !@$this->config->create_without_category AND !@$virtuemart_product_id){
			 return false;		}
		if (!$this->config->create AND !@$virtuemart_product_id){
			return false;		}


				/*if($virtuemart_product_id){
			 $this->_db->setQuery("SELECT vm_id FROM #__excel2vm_log WHERE vm_id = $virtuemart_product_id AND type IN ('pn','pu')");
			 if($this->_db->loadResult()){ 						$this->product_order++;

				if ($this->category_id AND $this->config->change_category) {
					$this->_db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id='$virtuemart_product_id'");
					$product_category_xref = $this->_db->loadColumn();

					$this->vm_product_categories->reset(1);
					$this->vm_product_categories->virtuemart_product_id = $virtuemart_product_id;
					$this->vm_product_categories->virtuemart_category_id = $this->category_id;
					$this->vm_product_categories->ordering = $this->product_order;

					if (count($product_category_xref) > 0) {						if (in_array($this->category_id, $product_category_xref)) {							$this->_db->setQuery("UPDATE #__virtuemart_product_categories SET ordering = $this->product_order WHERE virtuemart_category_id= $this->category_id AND virtuemart_product_id = $virtuemart_product_id");
							$this->_db->Query();
						}
						elseif ($this->config->multicategories)							$this->vm_product_categories->insert();
						else
							$this->vm_product_categories->update();					}
					else						$this->vm_product_categories->insert();
				}
				return;
			 }
		}*/

		$this->vm_product->reset(1);
		$this->vm_product_lang->reset(1);

		$this->vm_product->bind($row);

		$this->vm_product_lang->bind($row);
				if($this->price_label){			 if(!property_exists($this->vm_product,"price_label")){
				 $this->_db->setQuery("ALTER TABLE #__virtuemart_products ADD `price_label` varchar(256)");
				 $this->_db->Query();

				 unset($this->vm_product);
				 $this->vm_product=new updateTable("#__virtuemart_products", "virtuemart_product_id");
			 }
			 $this->vm_product->price_label=$this->filename;
		}

		if($this->desc_nl2br){			/*if($this->vm_product_lang->product_s_desc){
			   $this->vm_product_lang->product_s_desc=nl2br($this->vm_product_lang->product_s_desc);
			}*/

			if($this->vm_product_lang->product_desc){
			   $this->vm_product_lang->product_desc=nl2br($this->vm_product_lang->product_desc);
			}
		}

		 
		if(@$row['parent_sku']){			$this->_db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_products WHERE product_sku=".$this->_db->Quote($row['parent_sku'])."");
		   $parent_id = $this->_db->loadResult();
		   if($parent_id)$this->vm_product->product_parent_id=$parent_id;
		}
				if (@$virtuemart_product_id AND !$new) {						if(isset($row['slug']))
				$this->vm_product_lang->slug=$row['slug'];
			/*else
				$this->vm_product_lang->slug=$this->getAlias(stripslashes(@$row['product_name']),$virtuemart_product_id,stripslashes(@$row['product_sku']));*/

						if(isset($row['min_order_level']) OR isset($row['max_order_level']) OR isset($row['step_order_level']) OR isset($row['product_box'])){
				$this->_db->setQuery("SELECT product_params FROM #__virtuemart_products WHERE virtuemart_product_id = $virtuemart_product_id");
				$params=$this->_db->loadResult();
						if($params){
					$params=explode("|",$params);

					foreach($params as $key => $p){
						$temp=explode("=",$p);
						if($temp[0])
							$params_array[$temp[0]]=str_replace('"','',$temp[1]);
					}
					$new_params='min_order_level="'.(isset($row['min_order_level'])?$row['min_order_level']:@$params_array['min_order_level']).'"|';
					$new_params.='max_order_level="'.(isset($row['max_order_level'])?$row['max_order_level']:@$params_array['max_order_level']).'"|';
					$new_params.='step_order_level="'.(isset($row['step_order_level'])?$row['step_order_level']:@$params_array['step_order_level']).'"|';
					$new_params.='product_box="'.(isset($row['product_box'])?$row['product_box']:@$params_array['product_box']).'"|';

					$this->vm_product->product_params=$new_params;

				}
				else{
					$params=array();
					if(@$row['min_order_level'])
						 $params[]='min_order_level="'.$row['min_order_level'].'"';
					if(@$row['max_order_level'])
						 $params[]='max_order_level="'.$row['max_order_level'].'"';
					if(@$row['step_order_level'])
						 $params[]='step_order_level="'.$row['step_order_level'].'"';
					if(@$row['product_box'])
						 $params[]='product_box="'.$row['product_box'].'"';
					if(count($params))
						$this->vm_product->product_params=implode("|",$params)."|";

				}
			}

						if(!isset ($row['published']) AND $this->config->published_old>-1){
				 $this->vm_product->published=$this->config->published_old;
			}


			$this->vm_product->update();

			
			$this->_db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_products_".$this->config->sufix." WHERE virtuemart_product_id=".$this->_db->Quote($virtuemart_product_id)."");
			if($this->_db->loadResult()){
				try{
					$this->vm_product_lang->update();
				}
				catch(Exception $e){
				   $msg= $e->getMessage();
				   if(strstr($msg,"for key 'slug'")){
					   echo "<br>Дублирование псевдонима '{$this->vm_product_lang->slug}' у товара <b>{$this->vm_product_lang->product_name}</b> в строке $this->row<br>";
					   $this->vm_product_lang->slug=NULL;
					   $this->vm_product_lang->update();
				   }
				   else{
					 echo "<br>Ошибка при обновлении товара <b>{$this->vm_product_lang->product_name}</b> в строке $this->row.<br>$msg<br>";
				   }

				}

			}
			else{
								$auto_slug = $this->getAlias(stripslashes(@$row['product_name']),$virtuemart_product_id,stripslashes(@$row['product_sku']));
								if(!$this->vm_product_lang->slug)
					$this->vm_product_lang->slug=$auto_slug;
									try{
					$this->vm_product_lang->insert();
				}
				catch(Exception $e){
					$msg= $e->getMessage();
					 if(strstr($msg,"for key 'slug'")){
						 echo "<br>Дублирование псевдонима '{$this->vm_product_lang->slug}' у товара <b>{$this->vm_product_lang->product_name}</b> в строке $this->row<br>";
						 $this->vm_product_lang->slug=$auto_slug;
						 $this->vm_product_lang->insert();
					 }
					 else{
					   echo "<br>Ошибка при обновлении товара <b>{$this->vm_product_lang->product_name}</b> в строке $this->row.<br>$msg<br>";
					 }
				}

							}
						$this->log('pu',$virtuemart_product_id,$this->vm_product_lang->product_name ? $this->vm_product_lang->product_name : $this->vm_product->product_sku);			 $this->current['product']= $this->vm_product_lang->product_name ? $this->vm_product_lang->product_name : $this->vm_product->product_sku;
		}
		else {			$new=1;
			if(@$row['created_on']){
				$this->vm_product->created_on=is_numeric($row['created_on'])?gmdate("Y-m-d H:i:s",PHPExcel_Shared_Date::ExcelToPHP($row['created_on'])):date("Y-m-d H:i:s",strtotime($row['created_on']));
			}
			else{
				$this->vm_product->created_on=$this->m_date;
			}

			$this->vm_product->created_by = $this->user_id;

			if($this->vm_product->product_in_stock===NULL){
				if(!isset($this->config->product_in_stock_default)){
					$this->config->product_in_stock_default=10;
				}
				$this->vm_product->product_in_stock=(int)$this->config->product_in_stock_default;
			}


			if (!@$row['product_sku']) {				   if(@$row['slug'])
				   		$this->vm_product->product_sku=$row['product_sku']=substr($row['slug'],0,64);
				   elseif(@$row['product_name'])
				   		$this->vm_product->product_sku=$row['product_sku']=substr($this->translit(trim($row['product_name'])),0,64) ;
				   elseif(@$this->last_path)
				   		$this->vm_product->product_sku=$row['product_sku']  = str_replace('.','-', $this->last_path) . '-' . (@$this->product_order + 1);

			}
									$params=array();
			if(@$row['min_order_level'])
					 $params[]='min_order_level="'.$row['min_order_level'].'"';
			if(@$row['max_order_level'])
					 $params[]='max_order_level="'.$row['max_order_level'].'"';
			if(@$row['step_order_level'])
					 $params[]='step_order_level="'.$row['step_order_level'].'"';
			if(@$row['product_box'])
					 $params[]='product_box="'.$row['product_box'].'"';
			if(count($params))
				$this->vm_product->product_params=implode("|",$params)."|";


						$this->vm_product->published= !isset ($row['published'])?$this->config->published:$row['published'];
			$this->vm_product->product_lwh_uom = @$row['product_lwh_uom'] ? $row['product_lwh_uom'] : $this->config->product_lwh_uom;
			$this->vm_product->product_weight_uom = @$row['product_weight_uom'] ? $row['product_weight_uom'] : $this->config->product_weight_uom;


			$this->vm_product->created_by=$this->user_id;
			$virtuemart_product_id = $row['virtuemart_product_id']=$this->vm_product->insert();
			$this->vm_product_lang->virtuemart_product_id = $virtuemart_product_id;

			$auto_slug = $this->getAlias(stripslashes(@$row['product_name']),$virtuemart_product_id,stripslashes(@$row['product_sku']));
			if(!$this->vm_product_lang->slug)
				$this->vm_product_lang->slug=$auto_slug;
			try{
					 $this->vm_product_lang->insert();
			}
			catch(Exception $e){
					 $msg= $e->getMessage();
					 if(strstr($msg,"for key 'slug'")){
						 echo "<br>Дублирование псевдонима '{$this->vm_product_lang->slug}' у товара <b>{$this->vm_product_lang->product_name}</b> в строке $this->row<br>";
						 $this->vm_product_lang->slug=$auto_slug;
						 $this->vm_product_lang->insert();
					 }
					 else{
					   echo "<br>Ошибка при добавлении товара <b>{$this->vm_product_lang->product_name}</b> в строке $this->row.<br>$msg<br>";
					 }
			}
			$this->log('pn',$virtuemart_product_id,$this->vm_product_lang->product_name ? $this->vm_product_lang->product_name : $this->vm_product->product_sku);			 if($this->name_cache){
			   $this->temp_product_table_by_name[$this->vm_product_lang->product_name]=$virtuemart_product_id;
			}
			if($this->sku_cache){
			   $this->temp_product_table[$this->vm_product->product_sku]=$virtuemart_product_id;
			}
			if($this->gtin_cache AND $this->vm_product->product_gtin){
			   $this->temp_product_table_by_gtin[$this->vm_product->product_gtin]=$virtuemart_product_id;
			}
			if($this->mpn_cache AND $this->vm_product->product_mpn){
			   $this->temp_product_table_by_mpn[$this->vm_product->product_mpn]=$virtuemart_product_id;
			}
			if($this->productid_cache){
				$this->temp_productID_table[]=$virtuemart_product_id;
			}
			$this->current['product']= $this->vm_product_lang->product_name ? $this->vm_product_lang->product_name : $this->vm_product->product_sku;
		}
		
		@$this->product_order++;
		if (($this->config->change_category OR $new) AND !@$row['parent_sku'] AND !@$row['product_parent_id']) {

			if(@$category_id AND count($category_ids)<=1){
				$this->_db->setQuery("SELECT id,ordering FROM #__virtuemart_product_categories WHERE virtuemart_product_id = '$virtuemart_product_id' AND `virtuemart_category_id` = '$category_id'");
				$product_categories_obj=$this->_db->loadObject();

				if (!$this->config->multicategories){
					$this->_db->setQuery("DELETE FROM #__virtuemart_product_categories WHERE virtuemart_product_id ='$virtuemart_product_id'");
					$this->_db->Query();
				}

				if(@$row['ordering']){
					$ordering=(int)$row['ordering'];
				}
				elseif(@$product_categories_obj->ordering){
					$ordering= $product_categories_obj->ordering;
				}
				else{
					$ordering=@$this->product_order;
				}

				if(@$product_categories_obj->id){					$this->_db->setQuery("REPLACE INTO `#__virtuemart_product_categories` (`id`, `virtuemart_product_id`, `virtuemart_category_id`,`ordering`) VALUES ($product_categories_obj->id, '$virtuemart_product_id', '$category_id', '$ordering')");
					$this->_db->Query();
				}
				else{					$this->_db->setQuery("REPLACE INTO `#__virtuemart_product_categories` (`id`, `virtuemart_product_id`, `virtuemart_category_id`,`ordering`) VALUES (NULL, '$virtuemart_product_id', '$category_id', '$ordering')");
					$this->_db->Query();
				}

			}
			elseif(count($category_ids)>1){				$this->_db->setQuery("DELETE FROM #__virtuemart_product_categories WHERE virtuemart_product_id ='$virtuemart_product_id'");
				$this->_db->Query();
				foreach($category_ids as $category_id){
					$category_id=(int)trim($category_id);
					if($category_id){
						$this->_db->setQuery("SELECT id,ordering FROM #__virtuemart_product_categories WHERE virtuemart_product_id = '$virtuemart_product_id' AND `virtuemart_category_id` = '$category_id'");
						$product_categories_obj=$this->_db->loadObject();

						if(@$row['ordering']){
							$ordering=(int)$row['ordering'];
						}
						else{
							$ordering=@$this->product_order;
						}

						if(!$product_categories_obj){								$this->_db->setQuery("REPLACE INTO `#__virtuemart_product_categories` (`id`, `virtuemart_product_id`, `virtuemart_category_id`,`ordering`) VALUES (NULL, '$virtuemart_product_id', '$category_id', '$ordering')");
								$this->_db->Query();
						}
					}
				}
			}
		}
		elseif(@$row['parent_sku'] OR @$row['product_parent_id']){			  $this->_db->setQuery("DELETE FROM `#__virtuemart_product_categories` WHERE virtuemart_product_id = '$virtuemart_product_id'");
			 $this->_db->Query();
		}
				$this->images_import($row,$virtuemart_product_id,$new);
				/*if(!@$row['virtuemart_manufacturer_id'] AND !@$row['mf_name']){
		   $row['virtuemart_manufacturer_id']=$this->searchManufacturer($row['product_name']);
		}*/
				if(@$row['virtuemart_manufacturer_id']==$this->custom_clear OR @$row['mf_name']==$this->custom_clear){
		   $this->_db->setQuery("DELETE FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id=$virtuemart_product_id");
		   $this->_db->Query();
		}
		elseif (@$row['virtuemart_manufacturer_id'] OR @$row['mf_name']) {
			if (!@$row['virtuemart_manufacturer_id']) {				$this->_db->setQuery("SELECT virtuemart_manufacturer_id FROM #__virtuemart_manufacturers_".$this->config->sufix." WHERE mf_name=".$this->_db->Quote($row['mf_name']));
				$row['virtuemart_manufacturer_id'] = $this->_db->loadResult();

			}

			if (!$row['virtuemart_manufacturer_id'] AND $row['mf_name']) {				$this->_db->setQuery("INSERT INTO #__virtuemart_manufacturers SET virtuemart_manufacturer_id=NULL, published=1");
				$this->_db->Query();
				$row['virtuemart_manufacturer_id'] = $this->_db->insertid();


				$this->_db->setQuery("INSERT INTO #__virtuemart_manufacturers_".$this->config->sufix." SET mf_name=".$this->_db->Quote($row['mf_name']).", slug = " . $this->_db->Quote($this->translit($row['mf_name'])."_".$row['virtuemart_manufacturer_id']).",virtuemart_manufacturer_id = '{$row['virtuemart_manufacturer_id']}'");
				$this->_db->Query();

			}
			$row['virtuemart_manufacturer_id']=(int)$row['virtuemart_manufacturer_id'];
			$this->_db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id=$virtuemart_product_id");
			if ($this->_db->loadResult()) {
				$this->_db->setQuery("UPDATE #__virtuemart_product_manufacturers SET virtuemart_manufacturer_id='{$row['virtuemart_manufacturer_id']}' WHERE virtuemart_product_id=$virtuemart_product_id");
				$this->_db->Query();
			}
			else {
				$this->_db->setQuery("INSERT INTO #__virtuemart_product_manufacturers SET virtuemart_product_id=$virtuemart_product_id, virtuemart_manufacturer_id='{$row['virtuemart_manufacturer_id']}'");
				$this->_db->Query();
			}
		}
				$this->_db->setQuery("SELECT name,extra_id FROM #__excel2vm_fields WHERE id IN ($this->active_fields) AND type = 'price'");
		$prices = $this->_db->loadObjectList();
		if ($prices) {
			$this->price_table->reset(1);
			$this->price_table->bind($row);
			if($this->config->spec_price_clear){				$this->_db->setQuery("DELETE FROM #__virtuemart_product_prices WHERE virtuemart_product_id = $virtuemart_product_id");
				$this->_db->Query();
			}
			if (isset($row['currency'])){
				$this->_db->setQuery("SELECT virtuemart_currency_id FROM #__virtuemart_currencies WHERE currency_code_3 = ".$this->_db->Quote($row['currency'])."");
				if($this->_db->loadResult())
					$this->price_table->product_currency = $this->_db->loadResult();
				else
					$this->price_table->product_currency = $this->config->currency;
			}
			else
				$this->price_table->product_currency = $this->config->currency;

			if(isset($row['product_override_price'])){
				$this->price_table->product_override_price = str_replace(",", ".", floatval($row['product_override_price']));
				if(isset($row['override'])){
					$this->price_table->override=(int)$row['override'];
				}
				elseif($row['product_override_price']==0){
					$this->price_table->override=0;
				}
				else{
					$this->price_table->override=1;
				}

			}
			elseif(isset($row['override'])){
				 $this->price_table->override=(int)$row['override'];
			}

			foreach ($prices as $p) {
				if (@ $row[$p->name]) {
					$this->price_table->product_price = str_replace(",", ".", floatval($row[$p->name]))*$this->config->currency_rate;
					if($this->price_table->product_price==0)continue;
					$this->price_table->bind(json_decode($p->extra_id));
					if($this->config->spec_price_clear){
						$this->price_table->insert();
					}
					else{
						$this->_db->setQuery("SELECT virtuemart_product_price_id FROM #__virtuemart_product_prices WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_shoppergroup_id = '{$this->price_table->virtuemart_shoppergroup_id}' AND price_quantity_start = '{$this->price_table->price_quantity_start}' AND price_quantity_end = '{$this->price_table->price_quantity_end}'");
						$this->price_table->virtuemart_product_price_id = $this->_db->loadResult();
						if ($this->price_table->virtuemart_product_price_id)
							$this->price_table->update();
						else
							$this->price_table->insert();
					}

				}
			}
		}
				if (isset($row['product_price']) OR isset($row['product_override_price']) OR isset($row['product_discount_id'])) {

			$this->price_table->reset(1);
			$this->price_table->bind($row);



			if (isset($row['product_price'])){
				$this->price_table->product_price = $this->str2float($row['product_price'])*$this->config->currency_rate;

				if (isset($row['currency'])){
					$this->_db->setQuery("SELECT virtuemart_currency_id FROM #__virtuemart_currencies WHERE currency_code_3 = ".$this->_db->Quote($row['currency'])."");
					if($this->_db->loadResult())
						$this->price_table->product_currency = $this->_db->loadResult();
					else
						$this->price_table->product_currency = $this->config->currency;
				}
				else{
					$this->price_table->product_currency = $this->config->currency;
				}

			}


			if(isset($row['product_override_price'])){
				$this->price_table->product_override_price = str_replace(",", ".", floatval($row['product_override_price']))*$this->config->currency_rate;
				if(isset($row['override'])){
					$this->price_table->override=(int)$row['override'];
				}
				elseif($row['product_override_price']==0){
					$this->price_table->override=0;
				}
				else
					$this->price_table->override=1;
			}
			elseif(isset($row['override'])){
				 $this->price_table->override=(int)$row['override'];
			}



			$this->price_table->price_quantity_start=0;
			$this->price_table->price_quantity_end=0;
			$this->price_table->virtuemart_shoppergroup_id=0;

			$this->_db->setQuery("SELECT virtuemart_product_price_id FROM #__virtuemart_product_prices WHERE virtuemart_product_id = $virtuemart_product_id AND price_quantity_start = 0 AND price_quantity_end =0 AND (virtuemart_shoppergroup_id = 0 OR virtuemart_shoppergroup_id IS NULL)");
			$this->price_table->virtuemart_product_price_id = $this->_db->loadResult();

			if ($this->price_table->virtuemart_product_price_id)
				$this->price_table->update();
			else
				$this->price_table->insert();

		}
				if($this->custom_fields){

			 foreach($this->custom_fields as $name =>$field){
				 if($name=='custom_title_'.$field->id){
					 if(!isset($this->custom_fields['custom_value_'.$field->id]) OR !isset($row['custom_value_'.$field->id])){
						  continue;
					 }
					 if(!isset($row['custom_title_'.$field->id])){
						continue;
					 }
					 $custom_title=trim($row['custom_title_'.$field->id]);
					 $ordering=$this->active['custom_title_'.$field->id]->ordering;
					 if(!$custom_title)continue;
					 if(isset($row['custom_units_'.$field->id])){
						 $custom_value=$row['custom_value_'.$field->id]." ".$row['custom_units_'.$field->id];
					 }
					 else{
						 $custom_value=$row['custom_value_'.$field->id];
					 }
					 $custom_field_id=$this->getCustomFieldID($custom_title);
					 if(!$custom_field_id)continue;
					 if($this->config->extra_fields_clear){						 $this->_db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE virtuemart_custom_id = $custom_field_id AND virtuemart_product_id = $virtuemart_product_id");
						 $this->_db->Query();
					 }
					 else{
					   $this->_db->setQuery("SELECT virtuemart_customfield_id FROM #____virtuemart_product_customfields WHERE virtuemart_custom_id = $custom_field_id AND virtuemart_product_id = $virtuemart_product_id AND {$this->fieldname_custom_value} = ".$this->_db->Quote($custom_value)."");
					   if($this->_db->loadResult()){
						  continue;
					   }
					 }

					$this->_db->setQuery("INSERT INTO #__virtuemart_product_customfields SET virtuemart_product_id = $virtuemart_product_id, virtuemart_custom_id = $custom_field_id, {$this->fieldname_custom_value} = ".$this->_db->Quote($custom_value).", 	ordering= $ordering");
					$this->_db->Query();
				 }
			 }


		}

		$this->extra_table->reset(1);
		$this->extra_table->bind($row);

				if ($this->extra) {
			foreach ($this->extra as $e) {
				@$etra_value_counter[$e->extra_id]++;
				if (!empty($row[$e->name]) AND $row[$e->name]!=$this->custom_clear) {
					$ordering=$this->active[$e->name]->ordering;
					if($this->config->extra_fields_clear AND $etra_value_counter[$e->extra_id]==1AND $e->custom_element!='customfieldsforall'){						 $this->_db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE virtuemart_custom_id = $e->extra_id AND virtuemart_product_id = $virtuemart_product_id");
						 $this->_db->Query();
					}
					if($e->field_type=='E' AND $e->custom_element=='param'){
														if($e->custom_parent_id){
								$this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = '$virtuemart_product_id' AND virtuemart_custom_id = $e->custom_parent_id");
								if(!$this->_db->loadResult()){
									$this->_db->setQuery("INSERT INTO #__virtuemart_product_customfields SET
														  virtuemart_product_id = '$virtuemart_product_id',
														  virtuemart_custom_id = $e->custom_parent_id,
														  modified_on = NOW(),
														  modified_by = '$this->user_id',
														  ordering='$ordering'");
									$this->_db->Query();
								}
							}

														$this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = '$virtuemart_product_id' AND virtuemart_custom_id = $e->extra_id");

							if(!$this->_db->loadResult()){
									$this->_db->setQuery("INSERT INTO #__virtuemart_product_customfields SET
														  virtuemart_product_id = '$virtuemart_product_id',
														  virtuemart_custom_id = $e->extra_id,
														  {$this->fieldname_custom_value}='param',
														  modified_on = NOW(),
														  modified_by = '$this->user_id',
														  ordering='$ordering'");
									$this->_db->Query();
							}

							$custom_params_tmp = explode('|',$e->custom_params);
							$custom_params = array();
							foreach($custom_params_tmp as $k => $v){
								preg_match("/^([^=]*)=(.*)|/i",$v, $res);
								$custom_params[@$res[1]] = json_decode(@$res[2]);
							}

							$custom_type=$custom_params['ft'];
							$custom_values=explode("|",$row[$e->name]);
							
							$this->_db->setQuery("DELETE FROM #__virtuemart_product_custom_plg_param_ref WHERE virtuemart_product_id = '$virtuemart_product_id' AND virtuemart_custom_id = '$e->extra_id'");
							$this->_db->Query();


							foreach($custom_values as $val){

								if($custom_type=='int'){									$this->_db->setQuery("INSERT INTO #__virtuemart_product_custom_plg_param_ref SET virtuemart_product_id = '$virtuemart_product_id', virtuemart_custom_id = '$e->extra_id', intval = ".$this->_db->Quote($val).",val=0");
									$this->_db->Query();

								}
								else{									
									$custom_value_id=$this->getCustomValueId($e->extra_id, $val);

									if(!$custom_value_id){										$this->_db->setQuery("SELECT MAX(t.ordering) FROM #__virtuemart_product_custom_plg_param_values as t WHERE t.virtuemart_custom_id = '$e->extra_id'");
										$value_ordering=$this->_db->loadResult()+1;

										$this->_db->setQuery("INSERT INTO #__virtuemart_product_custom_plg_param_values
															  SET virtuemart_custom_id = '$e->extra_id',
															  value = ".$this->_db->Quote($val).",
															  ordering = $value_ordering,
															  published = 1");
										$this->_db->Query();
										$custom_value_id=$this->_db->insertid();
										$this->temp_custom_value_id_table[$e->extra_id."_". $val]=$custom_value_id;


									}

									$this->_db->setQuery("INSERT INTO #__virtuemart_product_custom_plg_param_ref SET virtuemart_product_id = '$virtuemart_product_id', virtuemart_custom_id = '$e->extra_id', intval = '0',val='$custom_value_id'");

									$this->_db->Query();


								}

							}

					}



					elseif($e->field_type=='E' AND $e->custom_element=='customfieldsforall'){														$this->_db->setQuery("
							SELECT virtuemart_customfield_id
							FROM #__virtuemart_product_customfields
							WHERE virtuemart_product_id = '$virtuemart_product_id'
							AND virtuemart_custom_id = $e->extra_id");
							$virtuemart_customfield_id=$this->_db->loadResult();
							if(!$virtuemart_customfield_id){
									$this->_db->setQuery("INSERT INTO #__virtuemart_product_customfields
														  SET
														  virtuemart_product_id = '$virtuemart_product_id',
														  virtuemart_custom_id = $e->extra_id,
														  {$this->fieldname_custom_value}='customfieldsforall',
														  modified_on = NOW(),
														  modified_by = '$this->user_id',
														  customfield_params ='$e->custom_params',
														  ordering='$ordering'");
									$this->_db->Query();
									$virtuemart_customfield_id=$this->_db->insertid();
							}


							$custom_values=explode("|",$row[$e->name]);
							
							$this->_db->setQuery("
							SELECT  virtuemart_customfield_id
							FROM #__virtuemart_product_customfields
							WHERE virtuemart_product_id = '$virtuemart_product_id'
							AND virtuemart_custom_id = '$e->extra_id'");
							$old_virtuemart_customfield_id=$this->_db->loadColumn();
							if(count($old_virtuemart_customfield_id)){
								$this->_db->setQuery("
								DELETE
								FROM #__virtuemart_product_custom_plg_customsforall
								WHERE
								customfield_id IN(".implode(",",$old_virtuemart_customfield_id).")
								AND virtuemart_product_id = '$virtuemart_product_id'");
								$this->_db->Query();
							}

							foreach($custom_values as $val){
																		if(strstr($e->custom_params,"color_hex")){
										 $custom_value_id=$this->getCustomValueId_CFFA_hex($e->extra_id, $val);
									}
									else{
										 $custom_value_id=$this->getCustomValueId_CFFA($e->extra_id, $val);
									}


									if(!$custom_value_id){										$this->_db->setQuery("
										SELECT MAX(ordering)
										FROM #__virtuemart_custom_plg_customsforall_values
										WHERE virtuemart_custom_id = '$e->extra_id'");
										$value_ordering=$this->_db->loadResult()+1;

										if(strstr($e->custom_params,"color_hex")){
											$this->_db->setQuery("INSERT
																  INTO #__virtuemart_custom_plg_customsforall_values
																  SET virtuemart_custom_id = '$e->extra_id',
																  customsforall_value_label  = ".$this->_db->Quote($val).",
																  customsforall_value_name  = 'ffffff',
																  ordering = $value_ordering");
											$this->_db->Query();
											$custom_value_id=$this->_db->insertid();
											$this->temp_custom_value_id_table_cffa_hex[$e->extra_id."_". $val]=$custom_value_id;
										}
										else{
											$this->_db->setQuery("INSERT
																  INTO #__virtuemart_custom_plg_customsforall_values
																  SET virtuemart_custom_id = '$e->extra_id',
																  customsforall_value_name  = ".$this->_db->Quote($val).",
																  ordering = $value_ordering");
											$this->_db->Query();
											$custom_value_id=$this->_db->insertid();
											$this->temp_custom_value_id_table_cffa[$e->extra_id."_". $val]=$custom_value_id;
										}

									}

									$this->_db->setQuery("
									INSERT
									INTO #__virtuemart_product_custom_plg_customsforall
									SET
									virtuemart_product_id = '$virtuemart_product_id',
									customfield_id = '$virtuemart_customfield_id',
									customsforall_value_id='$custom_value_id'");
									$this->_db->Query();




							}

					}



					elseif($e->field_type=='E' AND $e->custom_element=='articles'){
					   $article_data=explode("|",$row[$e->name]);
					   $articles=str_replace(".",",",$article_data[0]);
					   $article_type=@$article_data[1];
					   if(!in_array($article_type,array('title','intro','full')))
					   		$article_type='title';
					   if($this->checkArticlesVersion()){
						  /*$articles_obj->articles=explode(",",$articles);
						  $articles_obj->showas=$article_type;

						  $this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id AND custom_value = 'articles'");
						  $virtuemart_customfield_id=$this->_db->loadResult();

						  $this->extra_table->virtuemart_custom_id = $e->extra_id;
						  $this->extra_table->custom_value = 'articles';
						  $this->extra_table->custom_param = json_encode($articles_obj);
						  $this->extra_table->ordering = $ordering;*/
						  $articles_obj=json_decode($this->default_articles_object);
						  $row[$e->name]=str_replace('articles','"articles"',$row[$e->name]);
						  $row[$e->name]=str_replace('k2items','"k2items"',$row[$e->name]);
						  $from_cell_object=json_decode("{".$row[$e->name]."}");

						  if(@$from_cell_object->articles OR @$from_cell_object->k2items){
						  	if(@$from_cell_object->articles)
								$articles_obj->articles=$from_cell_object->articles;
							if(@$from_cell_object->k2items)
								$articles_obj->k2items=$from_cell_object->k2items;

							$this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id AND {$this->fieldname_custom_value} = 'articles'");
							$virtuemart_customfield_id=$this->_db->loadResult();

							$this->extra_table->virtuemart_custom_id = $e->extra_id;
							$this->extra_table->{$this->fieldname_custom_value} = 'articles';
							$this->extra_table->custom_param = json_encode($articles_obj);
							$this->extra_table->ordering = $ordering;
							if (!$virtuemart_customfield_id) {
								  $this->extra_table->insert();
							}
							else{
								  $this->extra_table->virtuemart_customfield_id=$virtuemart_customfield_id;
								  $this->extra_table->update();
							}
						  }

					   }
					   else{
						   $this->_db->setQuery("SELECT id FROM #__virtuemart_product_custom_plg_articles WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id");
						   $plg_custom_id=$this->_db->loadResult();
						   $this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id AND {$this->fieldname_custom_value} = 'articles'");
							if (!$this->_db->loadResult()) {
								$this->extra_table->virtuemart_custom_id = $e->extra_id;
								$this->extra_table->{$this->fieldname_custom_value} = 'articles';
								$this->extra_table->ordering = $ordering;
								$this->extra_table->insert();
							}
							if($plg_custom_id)
								$this->_db->setQuery("UPDATE #__virtuemart_product_custom_plg_articles SET articles =".$this->_db->Quote($articles).",showas = '$article_type' WHERE id = $plg_custom_id");
							else
								$this->_db->setQuery("INSERT INTO #__virtuemart_product_custom_plg_articles SET virtuemart_product_id = $virtuemart_product_id, virtuemart_custom_id = $e->extra_id,articles =".$this->_db->Quote($articles).",showas = '$article_type'");
							$this->_db->Query();
					   }


					}


					elseif($e->field_type=='E' AND $e->custom_element=='catproduct'){
					   $default_object=@json_decode($this->default_object);

					   $price_data=explode("|",$row[$e->name]);
					   foreach($price_data as $param_data){
					   	   $temp=explode(":",$param_data);
						   $property=trim(@$temp[0]);
						   $default_object->$property=trim(@$temp[1]);
					   }

					   $this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id AND {$this->fieldname_custom_value} = 'catproduct'");
						$virtuemart_customfield_id=$this->_db->loadResult();
						if (!$virtuemart_customfield_id) {
							$this->extra_table->virtuemart_custom_id = $e->extra_id;
							$this->extra_table->{$this->fieldname_custom_value} = 'catproduct';
							$this->extra_table->custom_param = json_encode($default_object);
							$this->extra_table->ordering = $ordering;
							$this->extra_table->insert();
						}
						else{
							$this->extra_table->virtuemart_customfield_id = $virtuemart_customfield_id;
							$this->extra_table->virtuemart_custom_id = $e->extra_id;
							$this->extra_table->custom_param = json_encode($default_object);
							$this->extra_table->ordering = $ordering;
							$this->extra_table->update();
						}
					}



					elseif($e->field_type=='E' AND $e->custom_element!='fast_buy'){
					   $this->_db->setQuery("SELECT id FROM #__virtuemart_product_custom_plg_param WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id");
					   $plg_custom_id=$this->_db->loadResult();
					   $this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id AND {$this->fieldname_custom_value} = ".$this->_db->Quote($e->{$this->fieldname_custom_value}));
						if (!$this->_db->loadResult()) {
							$this->extra_table->virtuemart_custom_id = $e->extra_id;
							$this->extra_table->{$this->fieldname_custom_value} = $e->{$this->fieldname_custom_value};
							$this->extra_table->ordering = $ordering;
							$this->extra_table->insert();
						}
						if($plg_custom_id)
							$this->_db->setQuery("UPDATE #__virtuemart_product_custom_plg_param SET virtuemart_product_id = $virtuemart_product_id, virtuemart_custom_id = $e->extra_id,value=".$this->_db->Quote($row[$e->name])." WHERE id = $plg_custom_id");
						else
							$this->_db->setQuery("INSERT INTO #__virtuemart_product_custom_plg_param SET virtuemart_product_id = $virtuemart_product_id, virtuemart_custom_id = $e->extra_id,value=".$this->_db->Quote($row[$e->name])."");
						$this->_db->Query();
					}

					elseif($e->field_type=='M'){

					   $this->_db->setQuery("SELECT virtuemart_media_id FROM #__virtuemart_medias WHERE file_title = ".$this->_db->Quote($row[$e->name])."");
					   $media_id=$this->_db->loadResult();

					   if(!$media_id AND (int)$row[$e->name])$media_id=$row[$e->name];


					   if(!$media_id)continue;
					   $this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id AND {$this->fieldname_custom_value} = '$media_id'");
						if (!$this->_db->loadResult()) {
							$this->extra_table->virtuemart_custom_id = $e->extra_id;
							$this->extra_table->{$this->fieldname_custom_value} = $media_id;
							$this->extra_table->ordering = $ordering;
							$this->extra_table->insert();
						}
					}
					else{
						if($e->field_type=='I')
							 $row[$e->name]=sprintf("%.2f",$row[$e->name]);
						if($e->is_list){							 $list=explode(";",$e->custom_value);
							 if(!in_array($row[$e->name],$list)){								 $list[]=$row[$e->name];
								 $this->_db->setQuery("UPDATE #__virtuemart_customs SET custom_value = '".implode(";",$list)."' WHERE virtuemart_custom_id = $e->extra_id");
								 $this->_db->Query();
							 }
						}
						if($this->config->extra_fields_clear){							$virtuemart_customfield_id=false;
						}
						else{

							$this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id AND {$this->fieldname_custom_value} = ".$this->_db->Quote($row[$e->name]));
							$virtuemart_customfield_id=$this->_db->loadResult();

						}


						if (!$virtuemart_customfield_id) {
							$row[$e->name]=str_replace("_x000D_","<br>\n",$row[$e->name]);

							$this->_db->setQuery("SELECT  product_parent_id FROM #__virtuemart_products WHERE virtuemart_product_id = ".$this->_db->Quote($virtuemart_product_id)."");
							$product_parent_id=$this->_db->loadResult();
							if($product_parent_id){								  $override=0;
								$this->_db->setQuery("
								SELECT virtuemart_customfield_id
								FROM #__virtuemart_product_customfields
								WHERE virtuemart_product_id = $product_parent_id
								AND virtuemart_custom_id = $e->extra_id
								");
								$override=$this->_db->loadResult();
								$this->extra_table->override= (int)$override;
							}


							if(in_array(strtolower($e->field_type),array('s','i','y'))){
								$this->extra_table->virtuemart_custom_id = $e->extra_id;

								$this->extra_table->{$this->fieldname_custom_value} = $row[$e->name];

								$this->extra_table->ordering = $ordering;
								$this->extra_table->insert();

							}
							else{
								$this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id");
								$virtuemart_customfield_id=$this->_db->loadResult();
								if(!$virtuemart_customfield_id){
									$this->extra_table->virtuemart_custom_id = $e->extra_id;
									$this->extra_table->{$this->fieldname_custom_value} = $row[$e->name];
									$this->extra_table->ordering = $ordering;
									$this->extra_table->insert();
								}
								else{
									$this->_db->setQuery("UPDATE #__virtuemart_product_customfields SET {$this->fieldname_custom_value} = ".$this->_db->Quote($row[$e->name])." WHERE virtuemart_customfield_id = $virtuemart_customfield_id");

									$this->_db->Query();
								}
							}

						}
					}

				}
				elseif(@$row[$e->name]==$this->custom_clear){
					$this->_db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE virtuemart_custom_id = $e->extra_id AND virtuemart_product_id = $virtuemart_product_id");
					$this->_db->Query();
				}
			}
		}

				if ($this->extra_cart) {

			$this->extra_table->reset(1);
			/*echo "<br>";
			echo $this->profiler->mark( 'custom_start' );
			echo "<br>";*/
			foreach ($this->extra_cart as $e) {
				@$etra_cart_value_counter[$e->extra_id]++;

				if (!empty($row[$e->name]) AND $row[$e->name]!=$this->custom_clear) {
					$ordering=$this->active[$e->name]->ordering;
					if($this->config->extra_fields_clear AND $etra_cart_value_counter[$e->extra_id]==1 AND $e->custom_element!='customfieldsforall'){						 $this->_db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE virtuemart_custom_id = $e->extra_id AND virtuemart_product_id = $virtuemart_product_id");
						 $this->_db->Query();
					}


					if($e->field_type=='E' AND $e->custom_element=='param'){														if($e->custom_parent_id){
								$this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = '$virtuemart_product_id' AND virtuemart_custom_id = $e->custom_parent_id");
								if(!$this->_db->loadResult()){
									$this->_db->setQuery("INSERT INTO #__virtuemart_product_customfields SET
														  virtuemart_product_id = '$virtuemart_product_id',
														  virtuemart_custom_id = $e->custom_parent_id,
														  modified_on = NOW(),
														  modified_by = '$this->user_id',
														  ordering='$ordering'");
									$this->_db->Query();
								}
							}

														$this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = '$virtuemart_product_id' AND virtuemart_custom_id = $e->extra_id");

							if(!$this->_db->loadResult()){
									$this->_db->setQuery("INSERT INTO #__virtuemart_product_customfields SET
														  virtuemart_product_id = '$virtuemart_product_id',
														  virtuemart_custom_id = $e->extra_id,
														  {$this->fieldname_custom_value}='param',
														  modified_on = NOW(),
														  modified_by = '$this->user_id',
														  ordering='$ordering'");
									$this->_db->Query();
							}

							$custom_params_tmp = explode('|',$e->custom_params);
							$custom_params = array();
							foreach($custom_params_tmp as $k => $v){
								preg_match("/^([^=]*)=(.*)|/i",$v, $res);
								$custom_params[@$res[1]] = json_decode(@$res[2]);
							}

							$custom_type=$custom_params['ft'];
							$custom_values=explode("|",$row[$e->name]);
														$this->_db->setQuery("DELETE FROM #__virtuemart_product_custom_plg_param_ref WHERE virtuemart_product_id = '$virtuemart_product_id' AND virtuemart_custom_id = '$e->extra_id'");
							$this->_db->Query();
							foreach($custom_values as $val){

								if($custom_type=='int'){									$this->_db->setQuery("INSERT INTO #__virtuemart_product_custom_plg_param_ref SET virtuemart_product_id = '$virtuemart_product_id', virtuemart_custom_id = '$e->extra_id', intval = '$val',val=0");
									$this->_db->Query();

								}
								else{																		$this->_db->setQuery("SELECT id FROM #__virtuemart_product_custom_plg_param_values WHERE   virtuemart_custom_id = '$e->extra_id' AND value = '$val'");
									$custom_value_id=$this->_db->loadResult();

									if(!$custom_value_id){										$this->_db->setQuery("SELECT MAX(t.ordering) FROM #__virtuemart_product_custom_plg_param_values as t WHERE t.virtuemart_custom_id = '$e->extra_id'");
										$value_ordering=$this->_db->loadResult()+1;

										$this->_db->setQuery("INSERT INTO #__virtuemart_product_custom_plg_param_values
															  SET virtuemart_custom_id = '$e->extra_id',
															  value = '$val',
															  ordering = $value_ordering,
															  published = 1");
										$this->_db->Query();
										$custom_value_id=$this->_db->insertid();

									}

									$this->_db->setQuery("INSERT INTO #__virtuemart_product_custom_plg_param_ref SET virtuemart_product_id = '$virtuemart_product_id', virtuemart_custom_id = '$e->extra_id', intval = '0',val='$custom_value_id'");

									$this->_db->Query();

								}
							}

					}


					elseif($e->field_type=='E' AND $e->custom_element=='customfieldsforall'){
							if($etra_cart_value_counter[$e->extra_id]==1){								 $this->_db->setQuery("
								SELECT virtuemart_customfield_id
								FROM #__virtuemart_product_customfields
								WHERE virtuemart_product_id = '$virtuemart_product_id'
								AND virtuemart_custom_id = $e->extra_id");
								$old_virtuemart_customfield_id=$this->_db->loadColumn();

								if(count($old_virtuemart_customfield_id)){
									$this->_db->setQuery("
									DELETE
									FROM #__virtuemart_product_customfields
									WHERE virtuemart_customfield_id IN (".implode(",",$old_virtuemart_customfield_id).")
									");
									$this->_db->Query();

									$this->_db->setQuery("
									DELETE
									FROM #__virtuemart_product_custom_plg_customsforall
									WHERE
									customfield_id IN(".implode(",",$old_virtuemart_customfield_id).")");
									$this->_db->Query();

								}
							}


														$custom_price=$this->str2float(@$row[$e->price])*$this->config->currency_rate;

							$this->_db->setQuery("INSERT INTO #__virtuemart_product_customfields
														  SET
														  virtuemart_product_id = '$virtuemart_product_id',
														  virtuemart_custom_id = $e->extra_id,
														  {$this->fieldname_custom_value}='customfieldsforall',
														  {$this->fieldname_custom_price}=".$this->_db->Quote($custom_price).",
														  modified_on = NOW(),
														  modified_by = '$this->user_id',
														  customfield_params ='$e->custom_params',
														  ordering='$ordering'");
							$this->_db->Query();
							$virtuemart_customfield_id=$this->_db->insertid();

														if(strstr($e->custom_params,"color_hex")){
								 $custom_value_id=$this->getCustomValueId_CFFA_hex($e->extra_id, $row[$e->name]);
							}
							else{
								 $custom_value_id=$this->getCustomValueId_CFFA($e->extra_id, $row[$e->name]);
							}


							if(!$custom_value_id){										$this->_db->setQuery("
										SELECT MAX(ordering)
										FROM #__virtuemart_custom_plg_customsforall_values
										WHERE virtuemart_custom_id = '$e->extra_id'");
								$value_ordering=$this->_db->loadResult()+1;

								if(strstr($e->custom_params,"color_hex")){
									$this->_db->setQuery("
									INSERT
									INTO #__virtuemart_custom_plg_customsforall_values
									SET virtuemart_custom_id = '$e->extra_id',
									customsforall_value_label  = ".$this->_db->Quote($row[$e->name]).",
									customsforall_value_name  = 'ffffff',
									ordering = $value_ordering");
									$this->_db->Query();
									$custom_value_id=$this->_db->insertid();
									$this->temp_custom_value_id_table_cffa_hex[$e->extra_id."_". $row[$e->name]]=$custom_value_id;
								}
								else{
									$this->_db->setQuery("
									INSERT
									INTO #__virtuemart_custom_plg_customsforall_values
									SET virtuemart_custom_id = '$e->extra_id',
									customsforall_value_name  = ".$this->_db->Quote($row[$e->name]).",
									ordering = $value_ordering");
									$this->_db->Query();
									$custom_value_id=$this->_db->insertid();
									$this->temp_custom_value_id_table_cffa[$e->extra_id."_". $row[$e->name]]=$custom_value_id;
								}

							}

							$this->_db->setQuery("
									INSERT
									INTO #__virtuemart_product_custom_plg_customsforall
									SET
									virtuemart_product_id = '$virtuemart_product_id',
									customfield_id = '$virtuemart_customfield_id',
									customsforall_value_id='$custom_value_id'");
							$this->_db->Query();

					}


					else{
						if($e->field_type=='M'){
							$this->_db->setQuery("SELECT virtuemart_media_id FROM #__virtuemart_medias WHERE file_title = '{$row[$e->name]}'");
						   $media_id=$this->_db->loadResult();
						   if(!$media_id AND (int)$row[$e->name])$media_id=$row[$e->name];
						   if(!$media_id)continue;
						   $this->extra_table->{$this->fieldname_custom_value} = $media_id;
						   $this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id AND {$this->fieldname_custom_value} = '$media_id'");
						}
						else{							$this->extra_table->{$this->fieldname_custom_value} = $row[$e->name];
							$this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_custom_id = $e->extra_id AND {$this->fieldname_custom_value} = ".$this->_db->Quote($row[$e->name]));
						}
						$virtuemart_customfield_id=$this->_db->loadResult();

						$this->extra_table->{$this->fieldname_custom_price} = $this->str2float(@$row[$e->price])*$this->config->currency_rate;

						$this->extra_table->virtuemart_custom_id = $e->extra_id;
						$this->extra_table->virtuemart_product_id = $virtuemart_product_id;

						$this->extra_table->ordering=$ordering;


						$this->_db->setQuery("SELECT  product_parent_id FROM #__virtuemart_products WHERE virtuemart_product_id = ".$this->_db->Quote($virtuemart_product_id)."");
						$product_parent_id=$this->_db->loadResult();
						if($product_parent_id){							  $override=0;
							$this->_db->setQuery("
							SELECT virtuemart_customfield_id
							FROM #__virtuemart_product_customfields
							WHERE virtuemart_product_id = $product_parent_id
							AND virtuemart_custom_id = $e->extra_id
							");
							$override=$this->_db->loadResult();

							$this->extra_table->override= (int)$override;
						}


						if (!$virtuemart_customfield_id) {
							$this->extra_table->virtuemart_customfield_id=0;
							$this->extra_table->insert();

													}
						else{
							$this->extra_table->virtuemart_customfield_id=$virtuemart_customfield_id;
							$this->extra_table->update();

													}
						$this->extra_table->virtuemart_customfield_id=0;
					}

				}
				elseif(@$row[$e->name]==$this->custom_clear){
					$this->_db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE virtuemart_custom_id = $e->extra_id AND virtuemart_product_id = $virtuemart_product_id");
					$this->_db->Query();
				}

			}
			/*echo "<br>";
			echo $this->profiler->mark( 'custom_finish' );
			echo "<br>";*/
		}
		if ($this->cherry) {
		foreach ($this->cherry as $e){
			$data=json_decode($e->extra_id);
			$type=array_shift($data);
			$param_name=implode("_",$data);
			$value=@$row[$e->name];
			if(!$value)
				continue;
			$prefix=$this->is_cherry==1?"fastseller":"vm";
			$field_id=$this->is_cherry==1?"id":"product_id";
			$this->_db->setQuery("SELECT parameter_values,product_type_id FROM #__{$prefix}_product_type_parameter WHERE product_type_id = $type AND parameter_name = '$param_name' ");
			$params_data=$this->_db->loadObject();
			if(!$params_data->product_type_id)
				continue;
			$params_values=@explode(";",$params_data->parameter_values);
			if(!in_array($value,$params_values)){
			   $params_values[]=$value;
			   $params_values=implode(";",$params_values);
			   $this->_db->setQuery("UPDATE #__{$prefix}_product_type_parameter SET parameter_values='$params_values' WHERE product_type_id = $type AND parameter_name = '$param_name'");
			   $this->_db->Query();
			}


			if($value==$this->custom_clear){
				$this->_db->setQuery("UPDATE #__{$prefix}_product_type_{$type} SET `$param_name`= NULL WHERE product_id = $virtuemart_product_id");
				$this->_db->Query();
			}
			$this->_db->setQuery("SELECT {$field_id} FROM #__{$prefix}_product_type_{$type} WHERE product_id = $virtuemart_product_id");
			$cherry_id=$this->_db->loadResult();
			if($cherry_id){
				$this->_db->setQuery("UPDATE #__{$prefix}_product_type_{$type} SET `$param_name`= '$value' WHERE {$field_id} = {$cherry_id}");
				$this->_db->Query();
			}
			else{
			  $this->_db->setQuery("INSERT INTO #__{$prefix}_product_type_{$type} SET `$param_name`= '$value',product_id = $virtuemart_product_id");
			  $this->_db->Query();
			}

			$this->_db->setQuery("SELECT product_id FROM #__{$prefix}_product_product_type_xref WHERE product_id=$virtuemart_product_id AND product_type_id = $type");
			if(!$this->_db->loadResult()){
				$this->_db->setQuery("INSERT INTO #__{$prefix}_product_product_type_xref SET product_id=$virtuemart_product_id, product_type_id = $type");
				$this->_db->Query();
			}


		}
	}
		if($this->related_custom_id){

		if(@$row['related_products']){
			$this->extra_table->reset();
			$this->extra_table->virtuemart_custom_id = $this->related_custom_id;
			$this->extra_table->virtuemart_product_id = $virtuemart_product_id;
			$ids=explode("|",$row['related_products']);

			$this->_db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = '$virtuemart_product_id' AND virtuemart_custom_id = {$this->related_custom_id}");
			$this->_db->Query();

			foreach($ids as $id ){
				$this->extra_table->{$this->fieldname_custom_value} = $id;
				$this->extra_table->customfield_params = 'wPrice=0|wImage=1|wDescr=0|width="'.$this->img_width.'"|height="'.$this->img_height.'"';
				$this->extra_table->insert();
			}
			$this->extra_table->reset();
		}
		if(@$row['related_products_sku']){
						$this->_db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = '$virtuemart_product_id' AND virtuemart_custom_id = {$this->related_custom_id}");
			$this->_db->Query();

			$this->extra_table->reset();
			$this->extra_table->virtuemart_custom_id = $this->related_custom_id;
			$this->extra_table->virtuemart_product_id = $virtuemart_product_id;
			$sku_array=explode("|",$row['related_products_sku']);
			foreach($sku_array as $sku ){
				$sku=$this->escape($sku);
				$this->_db->setQuery("SELECT p.virtuemart_product_id
								  FROM #__virtuemart_products as p
								  LEFT JOIN #__virtuemart_products_".$this->config->sufix." as r ON r.virtuemart_product_id = p.virtuemart_product_id
								  WHERE product_sku='$sku' AND r.virtuemart_product_id IS NOT NULL");
				$related_id = $this->_db->loadResult();
				if($related_id){
					 $this->extra_table->{$this->fieldname_custom_value} = $related_id;
					 $this->extra_table->insert();
				}
				else{
					$this->_db->setQuery("INSERT INTO #__excel2vm_related_products SET product_id = '$virtuemart_product_id', sku = '$sku'");
					$this->_db->Query();
				}
			}
			$this->extra_table->reset();
		}
	}
				if(@$row['shoppergroup_id']){
			$ids=explode("|",$row['shoppergroup_id']);
			foreach($ids as $id ){
				$this->_db->setQuery("SELECT id FROM #__virtuemart_product_shoppergroups WHERE virtuemart_product_id = $virtuemart_product_id AND virtuemart_shoppergroup_id = '$id'");
				if(!$this->_db->loadResult()){
					 $this->_db->setQuery("INSERT INTO #__virtuemart_product_shoppergroups SET virtuemart_product_id = $virtuemart_product_id,virtuemart_shoppergroup_id = '$id'");
					 $this->_db->Query();
				}
			}
		}

						foreach($this->active as $f){

		   if($f->type=='multi'){
			   if(@empty($row[$f->name])){
				   continue;
			   }

			   $extra_data=json_decode($f->extra_id);
			   if(!empty($row['parent_sku']) OR !empty($row['product_parent_id'])){					if(!empty($row['product_parent_id'])){
					   $parent_id=$row['product_parent_id'];
				   }
				   else{
					   $parent_id=$this->get_productId_by_sku($row['parent_sku']);
				   }
			   }
			   else{					 $parent_id = $virtuemart_product_id;
			   }
			   $parent_id=(int)$parent_id;
			   if(!$parent_id){					continue;
			   }

			   $this->_db->setQuery("INSERT INTO #__excel2vm_multy
									 SET
									 parent_id = '$parent_id',
									 custom_field_id = '$extra_data->id',
									 child_id = '$virtuemart_product_id',
									 type = '$extra_data->type',
									 clabel = '$extra_data->clabel',
									 value = ".$this->_db->Quote(@$row[$f->name])."
									 ");
			   $this->_db->Query();
		   }
		}
		$this->profiler_log( __LINE__.". Product End. Row - $this->row");
	}

	function getCategoryID_and_IDs($row){
			$category_ids=array();
			$category_id=0;
			if(!@$this->category_id AND $this->config->price_template ==5){
			   $category_id=$this->searchCategory($row['product_name']);
			}
			elseif($this->config->price_template ==6){
			   $category_id=(int)@$row['path'];
			   $category_ids=explode(",",str_replace(".",",",@$row['path']));

			}
			elseif($this->config->price_template ==7){			   $virtuemart_vendor_id=isset($row['virtuemart_vendor_id'])?$row['virtuemart_vendor_id']:1;


			   $category_ids=array();
			   if(@$row['path']){
				   $category_paths=explode($this->config->category_delimiter,$row['path']);
				   foreach($category_paths as $category_path){
				   	   $this->current['category']=$category_path;
					   $path=explode($this->config->level_delimiter,$category_path);

					   $parrent_id=0;
					   foreach($path as $category_name){
							if(!trim($category_name))
								continue;
							$check_parrent=count($path)>1?1:0;
					   		$category_id=$this->getCategoryID($category_name,$parrent_id,$check_parrent);
							if(!$category_id){
								$category_id=$this->createCategory($category_name,$virtuemart_vendor_id,$parrent_id);
								@$this->temp_category_ids["{$category_name}_{$parrent_id}_{$check_parrent}"]=$category_id;
					   		}
					   		$parrent_id=$category_id;
					   }
					   $category_ids[]=$category_id;
				   }
			   }

			}
			elseif($this->config->price_template ==8){								$category_id=$this->category_id;
			}
			elseif(@$this->category_id){
				$category_id=$this->category_id;
			}
		return array($category_id,$category_ids);
	}

		function images_import($row,$virtuemart_product_id,$new){
	  if($this->config->images_import_method AND isset($this->active['file_url'])){
			$coordinates=$this->getNameFromNumber($this->active['file_url']->ordering-1).($this->row);
			if(isset($this->images_collection[$coordinates])){
				 $row['file_url']=$this->images_collection[$coordinates]->name;
			}
		}

		if(@$row['file_url'] AND (@$row['img2'] OR @$row['img3'] OR @$row['img4']  OR @$row['img6'] OR @$row['img7'] OR @$row['img8'] OR @$row['img9'] OR @$row['img10'])){
			for($z=2;$z<=10;$z++){
				if(@$row['img'.$z]){
				   $row['file_url'].="|".$row['img'.$z];
				}
			}

		}
		if(!@$row['file_url'] AND @$this->images_products_default){
			$row['file_url']= $this->images_products_default;
		}
		if(!@$row['file_url'] AND @$this->config->unpublish_image){
			$this->_db->setQuery("UPDATE #__virtuemart_products SET published = 0 WHERE virtuemart_product_id = '$row[virtuemart_product_id]'");
			$this->_db->Query();
		}
		elseif(@$row['file_url']==$this->custom_clear){
			$this->_db->setQuery("SELECT  virtuemart_media_id FROM #__virtuemart_product_medias WHERE virtuemart_product_id = '$virtuemart_product_id'");
			$virtuemart_media_ids=$this->_db->loadColumn();
			if(count($virtuemart_media_ids)){
				$this->_db->setQuery("DELETE FROM #__virtuemart_medias WHERE virtuemart_media_id IN(".implode(",",$virtuemart_media_ids).")");
				$this->_db->Query();
			}
			$this->_db->setQuery("DELETE FROM #__virtuemart_product_medias WHERE virtuemart_product_id = '$virtuemart_product_id'");
			$this->_db->Query();
		}
		elseif (@$row['file_url'] OR @$row['file_url_thumb']) {
			if(isset($row['file_url'])){
			   $row['file_url']=str_replace(",","|",$row['file_url']);
						   }

			if(strstr($row['file_url'],'http://') OR strstr($row['file_url'],'https://')){				  if(!$new AND $this->config->images_load==0){					  return false;
				 }
				 if(!$new AND $this->config->images_load==1){					  $this->_db->setQuery("SELECT COUNT(*) FROM #__virtuemart_product_medias WHERE virtuemart_product_id = '$virtuemart_product_id'");
					  if($this->_db->loadResult()>0){							return false;
					  }
				 }
			}

			$file_url_array=isset($row['file_url'])?explode('|',$row['file_url']):array();
			$file_meta_array=isset($row['file_meta'])?explode('|',$row['file_meta']):array();
			$file_description_array=isset($row['file_description'])?explode('|',$row['file_description']):array();
			if(!@$row['file_url_thumb'] AND @$_POST['make_thumb']){
				$thumb_path=$this->config->thumb_path;
				$images=explode("|",$row['file_url']);
				foreach($images as &$image){
					$temp=explode("/",$image);
					$thumb_name=end($temp);

					if($_POST['prefix']){
						$thumb_name=$_POST['prefix'].$thumb_name;
					}

					if($_POST['sufix']){
						$thumb_name_parts=explode(".",$thumb_name);
						$thumb_name_parts[count($thumb_name_parts)-2]=$thumb_name_parts[count($thumb_name_parts)-2].$_POST['sufix'];
						$thumb_name=implode(".",$thumb_name_parts);
					}

										if(!file_exists(JPATH_ROOT.DS.$this->config->thumb_path.DS.$_POST['prefix'].$temp2['value'])){
						 if(substr($image,0,4)!='http'){							  if(file_exists(JPATH_ROOT.DS.$this->config->path.DS.$image)){

								  $this->resizeImageMagic(JPATH_ROOT.DS.$this->config->path.DS.$image,(int)$_POST['width'],(int)$_POST['height'],0,0,JPATH_ROOT.DS.$thumb_path.$thumb_name,100);

							  }
						 }

					}
					$image= $thumb_name;


				}
				$row['file_url_thumb']=implode("|",$images);

			}
			$file_url_thumb_array=isset($row['file_url_thumb'])?explode('|',$row['file_url_thumb']):array();
			$max=count($file_url_array)>=count($file_url_thumb_array)?count($file_url_array):count($file_url_thumb_array);

						$this->_db->setQuery("SELECT  virtuemart_media_id FROM #__virtuemart_product_medias WHERE virtuemart_product_id = '$virtuemart_product_id'");
			$virtuemart_media_ids=$this->_db->loadColumn();

			if(count($virtuemart_media_ids)){
				$this->_db->setQuery("DELETE FROM #__virtuemart_medias WHERE virtuemart_media_id IN(".implode(",",$virtuemart_media_ids).")");
				$this->_db->Query();
			}

			$this->_db->setQuery("DELETE FROM #__virtuemart_product_medias WHERE virtuemart_product_id = '$virtuemart_product_id'");
			$this->_db->Query();

			for($i=0;$i<$max;$i++){
				$this->vm_medias->reset(1);
				$this->vm_product_medias->reset(1);
				$this->vm_product_medias->virtuemart_product_id=$virtuemart_product_id;

				$row['file_url']=(@$file_url_array[$i])?$file_url_array[$i]:NULL;
				$row['file_url_thumb']=(@$file_url_thumb_array[$i])?$file_url_thumb_array[$i]:NULL;
				$row['file_meta']=(@$file_meta_array[$i])?$file_meta_array[$i]:NULL;
				$row['file_description']=(@$file_description_array[$i])?$file_description_array[$i]:NULL;

				if(!$row['file_url'] AND !$row['file_url_thumb']){
				   continue;
				}

				$this->vm_medias->bind($row);
				$this->vm_medias->published = 1;

				$this->vm_medias->file_title = $virtuemart_product_id."-".(stripslashes(@$row['product_name'] ? $this->translit($row['product_name']) : $this->translit($row['product_sku'])))."_".$i;
				$this->vm_medias->file_mimetype = 'image/jpeg';				$this->vm_medias->file_type = 'product';
				$this->vm_medias->file_is_product_image = 1;

				if(strstr($row['file_url'],JURI::root().$this->config->path)){
				   	$this->vm_medias->file_url=str_replace(JURI::root(),'',$row['file_url']);
				}
				elseif(strstr($row['file_url'],'http://') OR strstr($row['file_url'],'https://')){
					$this->vm_medias->file_url=$this->get_images_http($row['file_url'],$virtuemart_product_id,true,true,array('index'=>$i,'sku'=>$row['product_sku']));
					if(!$row['file_url_thumb'] AND $this->vm_medias->file_url){
							$thumb_path=$this->config->thumb_path;
							$temp=explode("/",$this->vm_medias->file_url);
							$thumb_name=end($temp);

							if($_POST['prefix']){
								$thumb_name=$_POST['prefix'].$thumb_name;
							}

							if($_POST['sufix']){
							   $temp=explode(".",$thumb_name);
							   $temp[count($temp)-2]=$temp[count($temp)-2].$_POST['sufix'];
							   $thumb_name=implode(".",$temp);
							}

							if(!$this->resizeImageMagic(JPATH_ROOT.DS.$this->vm_medias->file_url,(int)$_POST['width'],(int)$_POST['height'],0,0,JPATH_ROOT.DS.$thumb_path.$thumb_name,100)){
								if($this->debug){
									$error=error_get_last();
									echo "Ошибка создания миниатюры товара ".(@$row['product_name'])." - ".$error['message'].". Строка - ".$error['line']." - ".JPATH_ROOT.DS.$this->vm_medias->file_url. "<br>";
								}

							}
							else{
								$this->vm_medias->file_url_thumb=$thumb_path.$thumb_name;
							}

						}
				}
				elseif(!strstr($row['file_url'],$this->config->path)){
					$this->vm_medias->file_url=$this->config->path . $row['file_url'];
				}

				if(strstr($row['file_url_thumb'],JURI::root())){
				   	$this->vm_medias->file_url_thumb=str_replace(JURI::root(),'',$row['file_url_thumb']);
				}
				elseif(strstr($row['file_url_thumb'],'http://') OR strstr($row['file_url_thumb'],'https://')){
					$this->vm_medias->file_url_thumb=$this->get_images_http($row['file_url_thumb'],$virtuemart_product_id,true,false,array('index'=>$i,'sku'=>$row['product_sku']));
				}
				elseif(!strstr($row['file_url_thumb'],$this->config->path) AND $row['file_url_thumb']){
					$this->vm_medias->file_url_thumb=$this->config->thumb_path . $row['file_url_thumb'];
				}


				if($this->vm_medias->file_url OR $this->vm_medias->file_url_thumb){
					if($row['file_url'] AND !$row['file_url_thumb'] AND @$_POST['make_thumb']){
						$temp_path=explode("/",$this->vm_medias->file_url);
						$thumb_temp=array_pop($temp_path);
						if($_POST['sufix']){
							$thumb_temp=explode(".",$thumb_temp);

						   $thumb_temp[count($thumb_temp)-2]=$thumb_temp[count($thumb_temp)-2].$_POST['sufix'];
						   $thumb_temp=implode(".",$thumb_temp);

						}
						$this->vm_medias->file_url_thumb=$this->config->thumb_path . $_POST['prefix'] .$thumb_temp;

					}

						 				/*if($this->vm_medias->file_url_thumb){
						if(!in_array(substr($this->vm_medias->file_url_thumb,-3),array('jpg','gif','png','bmp','jpeg'))){
							$this->vm_medias->file_url_thumb .='.jpg';
						}
					}

					if($this->vm_medias->file_url){
						if(!in_array(substr($this->vm_medias->file_url,-3),array('jpg','gif','png','bmp','jpeg'))){
							$this->vm_medias->file_url.='.jpg';
						}
					}*/

					if(@$this->vm_medias->file_url AND $this->config->unpublish_image){
						if (!file_exists(JPATH_ROOT.DS.$this->vm_medias->file_url)) {
							 $this->_db->setQuery("UPDATE #__virtuemart_products SET published = 0 WHERE virtuemart_product_id = '$row[virtuemart_product_id]'");
							 $this->_db->Query();
						}
					}

					$this->_db->setQuery("SELECT m.virtuemart_media_id
										  FROM #__virtuemart_product_medias as p
										  LEFT JOIN #__virtuemart_medias as m ON m.virtuemart_media_id = p.virtuemart_media_id
										  WHERE virtuemart_product_id = $virtuemart_product_id AND file_url = ".$this->_db->Quote($this->vm_medias->file_url)."");
					$media_id=$this->_db->loadResult();
					if (!$media_id) {
						$this->vm_product_medias->virtuemart_media_id = $this->vm_medias->insert();
						$this->vm_product_medias->ordering = $i+1;
						$this->vm_product_medias->insert();
					}
					else{
						 $this->vm_medias->virtuemart_media_id=$media_id;
						 $this->vm_medias->update();
					}
				}

			}
		}
		elseif((@$row['file_meta'] OR @$row['file_description']) AND !$new){
			$this->_db->setQuery("SELECT virtuemart_media_id FROM #__virtuemart_product_medias WHERE virtuemart_product_id = '$virtuemart_product_id'");
			$virtuemart_media_ids=$this->_db->loadColumn();
			$file_meta_array=explode("|",@$row['file_meta']);
			$file_description_array=explode("|",@$row['file_description']);
			if(count($virtuemart_media_ids)){
				foreach($virtuemart_media_ids as $key=> $virtuemart_media_id){
					$this->vm_medias->reset(1);
					$this->vm_medias->virtuemart_media_id=$virtuemart_media_id;
					if(count($file_meta_array))
						$this->vm_medias->file_meta=isset($file_meta_array[$key])?$file_meta_array[$key]:$file_meta_array[0];
					if(count($file_description_array))
						$this->vm_medias->file_description=isset($file_description_array[$key])?$file_description_array[$key]:$file_description_array[0];
					$this->vm_medias->update();
				}
			}
		}
	}

	function response() {
		if(!$this->show_results){
			echo "<h2 style='color:green'>Импорт завершен</h2>";
			exit();
		}
		$this->_db->setQuery("SELECT COUNT(log_id) as num,type FROM #__excel2vm_log GROUP BY type");
		$stat=$this->_db->loadObjectList('type');
		$not_updated=array();
		if($this->params->get('not_updated_report')){
			$this->_db->setQuery("SELECT virtuemart_product_id, product_name FROM #__virtuemart_products_".$this->config->sufix." as p LEFT JOIN #__excel2vm_log as l ON l.vm_id = p.virtuemart_product_id AND type IN ('pn','pu') WHERE vm_id IS NULL");
			$not_updated=$this->_db->loadObjectList();
		}

		$response = "<table cellspacing='0' cellpadding='0' border='1' style='margin:5px auto;text-align: left;'><tr>";
		if (isset ($stat['cn']->num))
			$response .= "<th style='padding:10px; font-size:18px'>".JText::_('NEW_CATEGORIES')."</th>";
		if (isset ($stat['cu']->num))
			$response .= "<th style='padding:10px; font-size:18px'>".JText::_('UPDATED_CATEGORIES')."</th>";
		if (isset ($stat['pn']->num))
			$response .= "<th style='padding:10px; font-size:18px'>".JText::_('NEW_PRODUCTS')."</th>";
		if (isset ($stat['pu']->num))
			$response .= "<th style='padding:10px; font-size:18px'>".JText::_('UPDATED_PRODUCTS')."</th>";
		if (count($not_updated))
			$response .= "<th style='padding:10px; font-size:18px'>Остальные товары</th>";
		$response .= "</tr><tr>";
		if (isset ($stat['cn']->num)) {
			$this->_db->setQuery("SELECT vm_id,title FROM #__excel2vm_log WHERE type='cn' ORDER BY log_id");
			$data=$this->_db->loadObjectList('vm_id');
			$response .= "<td style='padding:10px;' valign='top'>";
			foreach ($data as $key => $item){
				$this->_db->setQuery("SELECT COUNT(*) FROM #__virtuemart_product_categories WHERE virtuemart_category_id  = $key");
				$num_products=(int)$this->_db->loadResult();
				$response .= "<a target='_blank' href='index.php?option=com_virtuemart&view=category&task=edit&cid=$key'>$item->title</a> <a target='_blank' href='index.php?option=com_virtuemart&view=product&virtuemart_category_id=$key'><b>($num_products)</b></a><br />";
			}

			$response .= "</td>";
		}

		if (isset ($stat['cu']->num)) {
			$this->_db->setQuery("SELECT vm_id,title FROM #__excel2vm_log WHERE type='cu' ORDER BY log_id");
			$data=$this->_db->loadObjectList('vm_id');
			$response .= "<td style='padding:10px;' valign='top'>";
			foreach ($data as $key => $item) {
				$this->_db->setQuery("SELECT COUNT(*) FROM #__virtuemart_product_categories WHERE virtuemart_category_id  = $key");
				$num_products=(int)$this->_db->loadResult();
				$response .= "<a target='_blank' href='index.php?option=com_virtuemart&view=category&task=edit&cid=$key'>$item->title</a> <a target='_blank' href='index.php?option=com_virtuemart&view=product&virtuemart_category_id=$key'><b>($num_products)</b></a><br />";
			}

			$response .= "</td>";
		}

		if (isset ($stat['pn']->num)) {
			$this->_db->setQuery("SELECT vm_id,title FROM #__excel2vm_log WHERE type='pn' ORDER BY log_id");
			$data=$this->_db->loadObjectList('vm_id');
			$response .= "<td style='padding:10px;' valign='top'>";
			foreach ($data as $key => $item)
				$response .= "<a target='_blank' href='index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id=$key'>$key.$item->title</a><br />";
			$response .= "</td>";
		}

		if (isset ($stat['pu']->num)) {
			$this->_db->setQuery("SELECT vm_id,title FROM #__excel2vm_log WHERE type='pu' ORDER BY log_id");
			$data=$this->_db->loadObjectList('vm_id');
			$response .= "<td style='padding:10px;' valign='top'>";
			foreach ($data as $key => $item)
				$response .= "<a target='_blank' href='index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id=$key'>$key.$item->title</a><br />";
			$response .= "</td>";
		}
		if (count($not_updated)){
			$response .= "<td style='padding:10px;' valign='top'>";
			foreach ($not_updated as $item)
				$response .= "<a target='_blank' href='index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id=$item->virtuemart_product_id'>$item->virtuemart_product_id.$item->product_name</a><br />";
			$response .= "</td>";
		}
		$response .= "</tr></table>";
		$this->_db->setQuery("OPTIMIZE TABLE #__virtuemart_product_customfields");
		$this->_db->Query();
		echo "$response";
		exit();
	}

	function load_img() {
		$errors=array("Неизвестная ошибка","Размер архива превысил максимально допустимый размер, который задан директивой upload_max_filesize конфигурационного файла php.ini. Обратитесь в тех. поддержку хостинга с просьбой увеличить лимит","Размер загружаемого файла превысил значение MAX_FILE_SIZE, указанное в HTML-форме","Загружаемый файл был получен только частично. Это может быть связано с нестабильным интернет-соединением или с проблемами на хостинге. Повторите попытку позже","Файл не был загружен","","Отсутствует временная папка","Не удалось записать файл на диск. Проверьте, достаточно ли места на диске","PHP-расширение остановило загрузку файла");
		$real_path = str_replace('/', DS, $this->config->path);
		$thumb_real_path = str_replace('/', DS, $this->config->thumb_path);

		require_once JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'libraries' . DS . 'pclzip.lib.php';		$post = JRequest :: get('post');

		$image_ext = array('gif', 'jpg', 'png', 'bmp','peg');
		if(!move_uploaded_file($_FILES['zip_file']['tmp_name'], JPATH_ROOT . DS . 'tmp' . DS . 'temp.zip')){
			echo "<br /><b><font color='#FF0000'>Ошибка загрузки архива с изображениями!".$errors[$_FILES['zip_file']['error']]."</font></b><br />";
			return false;
		}
		if (isset($_FILES['zip_file']['type']) AND substr($_FILES['zip_file']['name'],-3)!='zip') {
			echo "<br /><b><font color='#FF0000'>".JText::_('IMAGES_MUST_BE_PACKED_IN_ZIP_ARCHIVE')."!</font></b><br />";
			return false;
		}

		$zip = new PclZip(JPATH_ROOT . DS . 'tmp' . DS . 'temp.zip');

		$files = $zip->listContent();

		if (count($files) > 0) {
			foreach ($files as $f) {
				$ext = strtolower(substr($f['filename'], - 3));
				if (in_array($ext, $image_ext)) {
					$list = $zip->extract(PCLZIP_OPT_PATH, JPATH_ROOT . DS . $real_path, PCLZIP_OPT_BY_NAME, $f['filename']);
					if ($list != 0)
						$answer[] = "<li><a target='_blank' href='" . JURI :: root() . $this->config->path . "{$f['filename']}'>{$f['filename']}</a></li>";
					else
						$answer[] = "<li>ERROR : " . $zip->errorInfo(true) . "</li>";

					if ($post['make_thumb']){
						$thumb_name=$f['filename'];
						if($post['sufix']){
							$thumb_temp=explode(".",$thumb_name);
							$thumb_temp[count($thumb_temp)-2]=$thumb_temp[count($thumb_temp)-2].$post['sufix'];
							$thumb_name=implode(".",$thumb_temp);

						}
						$temp=explode("/",$thumb_name);
						$thumb_name=array_pop($temp);
						$this->ResizeImage(JPATH_ROOT . DS . $real_path . $f['filename'], JPATH_ROOT . DS . $thumb_real_path . $post['prefix'] . $thumb_name, $post['width'], $post['height']);
					}

				}
			}
			echo "<h3 class='spoiler'>".JText::_('IMPORTED_IMAGES')."</h3><br />";
			echo "<span id='spoiler_span' style='display:none'><ol style='text-align:left;display: inline-block;'>" . (implode('', $answer)) . "</ol></span><br />";
			unlink(JPATH_ROOT . DS . 'tmp' . DS . 'temp.zip');
		}
		else
			echo "<br /><b><font color='#FF0000'>".JText::_('ARCHIVE_IS_EMPTY')."</font></b><br />";

	}

	function escape($string){
	   if(method_exists($this->_db , "escape"))
	   		return $this->_db->escape($string);
	   elseif(method_exists($this->_db , "getEscaped"))
	   		return $this->_db->getEscaped($string);
	   else
	   		return mysql_escape_string($string);
	}
/*
	function immport() {
		$fail = JText::_('ERROR_LICENSE_DEWELOPER');
		$t = explode('/', JURI :: root());
		$d = $t[2];
		if(substr($d,0,4)=='www.')$d=substr($d,4);

		@ $k = file_get_contents(dirname(__FILE__) . DS . 'key.txt');

		if(!strstr($d,'localhost')){
			$token=sha1('ho3tj4gut95liwfvngg9'.urlencode($d));
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://php-programmist.ru/license.php?token={$token}&domain=".urlencode($d)."&key=$k");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_REFERER, JURI :: root());
			curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
			$data=curl_exec($ch);
			$error=curl_error($ch);
			curl_close($ch);

			if(@$data){
				if(@$data==sha1('ex064qarf45dw46bon61'.urlencode($d))){
					echo JText::_('WRONG_LICENSE') . $fail;
					exit ();
				}
				elseif(@$data==sha1('46dyidv2cgcy98dj2n08'.urlencode($d))){
					return true;
				}
			}
			if ($k == sha1("IhlBl#kjW{$d}YYZ3*MW6U2")) {
				return true;
			}
			else{
				  echo JText::_('WRONG_LICENSE') . $fail;
				  exit ();
			}

		}

	}
*/
	/**
	* Resize image Magic
	* @param string path image
	* @param int width
	* @param int height
	* @param int (0 - show full foto, 1 - cut foto )
	* @param int (2 - fill $color or fill transparent, 1 - fill $color, 0 - not fill)
	* @param string save to file (if empty - print image)
	* @param int quality (0,100)
	* @param int $color_fill (0xffffff - white)
	* @param int interlace - enable / disable
	*/
	function resizeImageMagic($img, $w, $h, $thumb_flag = 0, $fill_flag = 1, $name = "", $qty = 85, $color_fill = 0xffffff, $interlace = 1){
		ini_set("memory_limit", "256M");
		$new_w = $w;
		$new_h = $h;
		$path = pathinfo($img);
		$ext = $path['extension'];
		$ext = strtolower($ext);

		$imagedata = @getimagesize($img);

		$img_w = $imagedata[0];
		$img_h = $imagedata[1];

		if (!$img_w && !$img_h) return 0;

		if (!$w){
			$w = $new2_w = $h * ($img_w/$img_h);
			$new2_h = $h;
		}elseif (!$h){
			$h = $new2_h = $w * ($img_h/$img_w);
			$new2_w = $w;
		}else{

			if ($img_h*($new_w/$img_w) > $new_h){
				$new2_w=$img_w*$new_h/$img_h;
				$new2_h=$new_h;
			}else{
				$new2_w=$new_w;
				$new2_h=$img_h*$new_w/$img_w;
			}

			if ($thumb_flag){
				if ($img_h*($new_w/$img_w) < $new_h){
					$new2_w = $img_w*$new_h/$img_h;
					$new2_h = $new_h;
				}else{
					$new2_w = $new_w;
					$new2_h = $img_h*$new_w/$img_w;
				}
			}

			if (!$thumb_flag && !$fill_flag){
				$new2_w = $w;
				$new2_h = $h;
			}
		}

		if ( ($ext=="jpg") or ($ext=="jpeg") ){
			$image = imagecreatefromjpeg($img);
		}elseif ($ext=="gif"){
			$image = imagecreatefromgif($img);
		}elseif ($ext=="png"){
			$image = imagecreatefrompng($img);
		}else{
			return 0;
		}

		$thumb = imagecreatetruecolor($w, $h);

		if ($fill_flag){
			if ($fill_flag==2){
				if ($ext=="png"){
					imagealphablending($thumb, false);
					imagesavealpha($thumb, true);
					$trnprt_color = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
					imagefill($thumb, 0, 0, $trnprt_color);
				}elseif($ext=="gif"){
					$trnprt_color = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
					imagefill($thumb, 0, 0, $trnprt_color);
					imagecolortransparent($thumb, $trnprt_color);
					imagetruecolortopalette($thumb, true, 256);
				}else{
					imagefill($thumb, 0, 0, $color_fill);
				}
			}else{
				imagefill($thumb, 0, 0, $color_fill);
			}
		}

		if ($thumb_flag){

			imagecopyresampled ($thumb, $image, ($w-$new2_w)/2, ($h-$new2_h)/2, 0, 0, $new2_w, $new2_h, $imagedata[0], $imagedata[1]);

		}elseif ($fill_flag){

			if ($new2_w<$w) imagecopyresampled ($thumb, $image, ($w-$new2_w)/2, 0, 0, 0, $new2_w, $new2_h, $imagedata[0], $imagedata[1]);
			if ($new2_h<$h) imagecopyresampled ($thumb, $image, 0, ($h-$new2_h)/2, 0, 0, $new2_w, $new2_h, $imagedata[0], $imagedata[1]);
			if ($new2_w==$w && $new2_h==$h) imagecopyresampled ($thumb, $image, 0, 0, 0, 0, $new2_w, $new2_h, $imagedata[0], $imagedata[1]);

		}else{

			$thumb = @imagecreatetruecolor($new2_w, $new2_h);
			if ($ext=="png"){
				imagealphablending($thumb, false);
				imagesavealpha($thumb, true);
			}
			if ($ext=="gif"){
				$trnprt_color = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
				imagefill($thumb, 0, 0, $trnprt_color);
				imagecolortransparent($thumb, $trnprt_color);
				imagetruecolortopalette($thumb, true, 256);
			}
			imagecopyresampled ($thumb, $image, 0, 0, 0, 0, $new2_w, $new2_h, $imagedata[0], $imagedata[1]);

		}

		if ($interlace){
			imageinterlace($thumb, 1);
		}

		if ($ext=="png") {
			if (phpversion()>='5.1.2'){
				imagepng($thumb, $name, 10-max(intval($qty/10),1));
			}
			else{
				imagepng($thumb, $name);
			}
		}
		if ($ext=="gif"){
			if ($name)
				imagegif($thumb, $name);
			else
				imagegif($thumb);
		}
		if (($ext=="jpg")or($ext=="jpeg")) imagejpeg($thumb, $name, $qty);

		return 1;
	}

	function ResizeImage($image_from, $image_to, $fitwidth = 450, $fitheight = 450, $quality = 75) {
		global $php_inc;
		$os = $originalsize = getimagesize($image_from);
		$fitwidth = $fitwidth ? $fitwidth : 100000;
		$fitheight = $fitheight ? $fitheight : 100000;
		if ($originalsize[2] != 1 && $originalsize[2] != 2 && $originalsize[2] != 3 && $originalsize[2] != 6 && ($originalsize[2] < 9 or $originalsize[2] > 12))
			return false;

		if ($originalsize[0] > $fitwidth or $originalsize[1] > $fitheight) {
			$h = getimagesize($image_from);

			if (($h[0] / $fitwidth) > ($h[1] / $fitheight))
				$fitheight = $h[1] * $fitwidth / $h[0];
			else
				$fitwidth = $h[0] * $fitheight / $h[1];

			if ($os[2] == 2 or ($os[2] >= 9 && $os[2] <= 12))
				$i = ImageCreateFromJPEG($image_from);
			if ($os[2] == 3)
				$i = ImageCreateFromPng($image_from);
			if ($os[2] == 1)
				$i = ImageCreateFromGif($image_from);

			$o = ImageCreateTrueColor($fitwidth, $fitheight);
			imagecopyresampled($o, $i, 0, 0, 0, 0, $fitwidth, $fitheight, $h[0], $h[1]);
			imagejpeg($o, $image_to, $quality);
			chmod($image_to, 0777);
			imagedestroy($o);
			imagedestroy($i);
			return 2;
		}
		if ($originalsize[0] <= $fitwidth && $originalsize[1] <= $fitheight) {
			if ($os[2] == 2 or ($os[2] >= 9 && $os[2] <= 12))
				$i = ImageCreateFromJPEG($image_from);
			if ($os[2] == 3)
				$i = ImageCreateFromPng($image_from);
			if ($os[2] == 1)
				$i = ImageCreateFromGif($image_from);

			$o = ImageCreateTrueColor($originalsize[0], $originalsize[1]);
			imagecopyresampled($o, $i, 0, 0, 0, 0, $originalsize[0], $originalsize[1], $h[0], $h[1]);
			imagejpeg($i, $image_to, $quality);
			chmod($image_to, 0777);
			return 1;
		}
	}

	function str2float($string){
	   $string = trim($string);
	   $string=str_replace(",",".",$string);
	   $string=preg_replace("#[^.\d-]#","",$string);
	   return (float)$string;
	}

	function _strtolower($string){
		$small = array('а','б','в','г','д','е','ё','ж','з','и','й',
					   'к','л','м','н','о','п','р','с','т','у','ф',
					   'х','ч','ц','ш','щ','э','ю','я','ы','ъ','ь',
					   'э', 'ю', 'я');
		$large = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й',
					   'К','Л','М','Н','О','П','Р','С','Т','У','Ф',
					   'Х','Ч','Ц','Ш','Щ','Э','Ю','Я','Ы','Ъ','Ь',
					   'Э', 'Ю', 'Я');
		return str_replace($large, $small, $string);
	}

	function _strtoupper($string){
		$small = array('а','б','в','г','д','е','ё','ж','з','и','й',
						'к','л','м','н','о','п','р','с','т','у','ф',
						'х','ч','ц','ш','щ','э','ю','я','ы','ъ','ь',
						'э', 'ю', 'я','a', 'b', 'c', 'd', 'e', 'f',
						'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
						'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
						'y', 'z');
		$large = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й',
						'К','Л','М','Н','О','П','Р','С','Т','У','Ф',
						'Х','Ч','Ц','Ш','Щ','Э','Ю','Я','Ы','Ъ','Ь',
						'Э', 'Ю', 'Я','A', 'B', 'C', 'D', 'E', 'F',
						'G', 'H', 'I', 'L', 'K', 'L', 'M', 'N', 'O',
						'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
						'Y', 'Z');
		return str_replace($small, $large, $string);
	}

	function genAlias($name,$id,$sku,$template=false,$sep="-"){
		if($name)
			$name=$this->translit($name);
		if($sku)
			$sku=$this->translit($sku);
		if(!$template)$template=$this->config->alias_template;
		switch ($template) {
		  case 1:

				 if($name)
				 	$alias= $name;
				 elseif($sku){
					 $alias= $this->genAlias($name,$id,$sku,10,$sep);
				 }
				 else{
				 	/*echo '<span style="font-size: 14px;color:red">'.JText::_('ALIAS_COULD_NOT_BE_GENERATED').($this->row+1)." (".$name.$sep.$id.$sep.$sku.')</span>';

					exit();*/
					$alias=$id.$sep.rand(1111111111,9999999999);
				 }
		  break;
		  case 2:
				  $alias= $id.$sep.$this->genAlias($name,$id,$sku,1,$sep);
		  break;
		  case 3:
				  $alias= $this->genAlias($name,$id,$sku,1,$sep).$sep.$id;
		  break;
		  case 4:
				  if($sku AND $name){
					 $alias=  $sku.$sep.$name;
				  }
		  break;
		  case 5:
				 if($sku AND $name){
					 $alias=  $name.$sep.$sku;
				  }

		  break;
		  case 6:
				 if($sku){
				 	$alias= $sku.$sep.$this->genAlias($name,$id,$sku,2,$sep);
				 }
		  break;
		  case 7:
				  if($sku AND $name)
				 	$alias= $id.$sep.$sku.$sep.$name;

		  break;
		  case 8:
				  if($sku AND $name)
				 	$alias= $name.$sep.$sku.$sep.$id;
		  break;
		  case 9:
				  if($sku AND $name)
				 	$alias= $name.$sep.$id.$sep.$sku;
		  break;
		  case 10:
				  if($sku)
				 	$alias= $sku;
		  break;
		  case 11:
				  if($id)
				 	$alias= $id;
		  break;

		  $alias= $this->genAlias($name,$id,$sku,2,$sep);
		}
		if(!$alias)
			$alias=$this->genAlias($name,$id,$sku,2,$sep);
		while(substr($alias,-1)=='-')
			$alias=substr($alias,0,-1);
		return  $alias;
	}

	function getAlias($name,$id,$sku,$product=true,$template=false,$sep="-"){

		$alias=$this->genAlias($name,$id,$sku,$template,$sep);

		if($product){
		  for(;;){
			 $this->_db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_products_".$this->config->sufix." WHERE slug='$alias'");
			 if($this->_db->loadResult() AND $this->_db->loadResult()!=$id){
				 $alias=$alias.$sep.rand(1111111111,9999999999);
			 }
			 else{
				 return $alias;
			 }
		  }
		}
		else{
			for(;;){
			  $this->_db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_categories_".$this->config->sufix." WHERE slug='$alias'");
			  if($this->_db->loadResult() AND $this->_db->loadResult()!=$id)
  					$alias=$alias.$sep.rand(1111111111,9999999999);
			  else
					return $alias;
			}
		}

	}

	function extractImages($objPHPExcel){
		/*echo '<pre>';
		print_r($objPHPExcel->getActiveSheet()->getDrawingCollection());
		echo '</pre>';
		exit();*/
		$drawing_array=$objPHPExcel->getActiveSheet()->getDrawingCollection();
		unset($objPHPExcel);
		foreach ($drawing_array as $key=> $drawing) {

			if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
				/*echo '<pre>';
				print_r($drawing);
				echo '</pre>';
				exit();*/


				ob_start();
				call_user_func(
					$drawing->getRenderingFunction(),
					$drawing->getImageResource()
				);
				$imageContents = ob_get_contents();
				ob_end_clean();
				$name=md5($imageContents).".".($drawing->getMimeType()=='image/png'?'png':'jpg');
				$this->images_collection[$drawing->getCoordinates()]->name=$name;
				file_put_contents(JPATH_ROOT . DS .str_replace('/', DS, $this->config->path).DS.$name,$imageContents);

			}
			if ($drawing instanceof PHPExcel_Worksheet_Drawing) {
				$extension=$drawing->getExtension();
				$imageContents=file_get_contents($drawing->getPath());
				$name=md5($imageContents).".".($extension=='png'?'png':'jpg');
				$this->images_collection[$drawing->getCoordinates()]->name=$name;
				file_put_contents(JPATH_ROOT . DS .str_replace('/', DS, $this->config->path).DS.$name,$imageContents);
			}
		}
		file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS."images_collection.txt",serialize(@$this->images_collection));


	}

	function getVersion(){
		$xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_excel2vm'.DS.'excel2vm.xml');
		return (string)$xml->version;
	}

	function checkArticlesVersion(){
		if(isset($this->ArticlesVersion))return $this->ArticlesVersion;
		$xml=JFactory::getXML(JPATH_ROOT .DS.'plugins'.DS.'vmcustom'.DS.'articles'.DS.'articles.xml');
		$version=(string)$xml->version;
		$temp=explode(".",$version);
		if($temp[0]>1){
			$this->ArticlesVersion=true;
		}
		elseif($temp[1]>=3){
			$this->ArticlesVersion=true;
		}
		else{
			$this->ArticlesVersion=false;
		}
		return $this->ArticlesVersion;
	}

	function createCategory($category_name,$virtuemart_vendor_id,$parent_id=0){
		if(!trim($category_name)){
		   return 0;
		}
		$this->_db->setQuery("INSERT INTO #__virtuemart_categories SET
							  virtuemart_vendor_id='$virtuemart_vendor_id',
							  published=1,
							  created_on='$this->m_date',
							  created_by='$this->user_id',
							  modified_on='$this->m_date',
							  modified_by='$this->user_id',
							  category_template='$this->brows',
							  products_per_row='$this->per_row',
							  category_product_layout='$this->flypage'");
		$this->_db->Query();
		$category_id = $this->_db->insertid();

		$slug=$category_id.'-'.$this->translit($category_name);
		$query = "INSERT INTO #__virtuemart_categories_".$this->config->sufix." SET virtuemart_category_id=$category_id, category_name=".$this->_db->Quote($category_name).",category_description = ".$this->_db->Quote($category_name).", slug = ".$this->_db->Quote($slug)."";
		$this->_db->setQuery($query);
		$this->_db->Query();
		$query = "INSERT INTO #__virtuemart_category_categories SET category_parent_id='$parent_id', category_child_id='$category_id'";
		$this->_db->setQuery($query);
		$this->_db->Query();
		$this->log('cn',$category_id,$category_name);
		return  $category_id;
	}

	function getCategoryID($category_name,$parrent=0,$check_parrent=false){

		if(isset($this->temp_category_ids["{$category_name}_{$parrent}_{$check_parrent}"])){
			 $category_id=$this->temp_category_ids["{$category_name}_{$parrent}_{$check_parrent}"];
		}
		else{
			$where=$check_parrent?" AND cc.category_parent_id = $parrent":"";
			$this->_db->setQuery("SELECT r.virtuemart_category_id
							  FROM #__virtuemart_categories_".$this->config->sufix." as r
							  LEFT JOIN #__virtuemart_categories as c ON c.virtuemart_category_id = r.virtuemart_category_id
							  LEFT JOIN #__virtuemart_category_categories as cc ON c.virtuemart_category_id = cc.category_child_id
							  WHERE category_name=".$this->_db->Quote($category_name)." AND c.virtuemart_category_id IS NOT NULL
							  $where
							  ORDER BY category_parent_id ASC
							  LIMIT 0,1");
			$category_id=$this->_db->loadResult();
			$this->temp_category_ids["{$category_name}_{$parrent}_{$check_parrent}"]=$category_id;
		}


		return $category_id;
	}

	function get_files(){

	  $uploaded_files=JFolder::files( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls' );
	  $data=array();
	  foreach($uploaded_files as $key => $file ){
		 if(in_array(substr($file,-4),array('.xls','.csv','xlsx'))){
			  @$data[$key]->file=$file;
			  $data[$key]->size=$size=filesize(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls'.DS.$file);
			  $data[$key]->time=filemtime(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls'.DS.$file);
		 }
	  }

	  $index = array();
	  foreach($data as $a) $index[] = $a->time;
		 array_multisort($index, $data);
	  krsort($data);
	  return $data;
	}


	function update_files(){
		$data=$this->get_files();
		$files='';
		foreach($data as $key=>$f){
				$files.='<tr id="row_'.$key.'">';
				  $files.='<td><input name="uploaded_file[]" id="uploaded_file_'.$key.'" type="checkbox" value="'.$f->file.'" style="margin-left: 14px"></td>';
				  $files.='<td><label for="uploaded_file_'.$key.'">'.$f->file.'</label></td>';
				  $files.='<td>'.$this->getSize($f->size).'</td>';
				  $files.='<td>'.date("Y-m-d H:i",$f->time).'</td>';
				  $files.='<td><a href="index.php?option=com_excel2vm&task=download&file='.$f->file.'"><img src="'.JURI::base().'/components/com_excel2vm/assets/images/download.png" width="16" height="16" alt=""></a></td>';
				  $files.='<td><img style="cursor: pointer" rel="'.$key.'" file="'.$f->file.'"  class="delete" src="'.JURI::base().'/components/com_excel2vm/assets/images/delete.png" width="16" height="16" alt=""></td>';
				$files.='</tr>';
		}
		echo $files;
	}



	function download(){
	  $file=$_GET['file'];
	  if(!$file)exit();
	  $uploaded_files=JFolder::files( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls' );
	  foreach($uploaded_files as $key => $f ){
		 if(!in_array(substr($f,-4),array('.xls','.csv','xlsx'))){
			  unset($uploaded_files[$key]);
		 }
	  }
	  if(!in_array($file,$uploaded_files)){
		echo "Файл не найден";
		exit();
	  }
	  $mainframe = JFactory::getApplication();
	  $mainframe->redirect(JURI::base()."/components/com_excel2vm/xls/".$file);
	}

	function delete(){
	  $file=$_GET['file'];
	  if(!$file)exit();
	  $uploaded_files=JFolder::files( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls' );
	  foreach($uploaded_files as $key => $f ){
		 if(!in_array(substr($f,-4),array('.xls','.csv','xlsx'))){
			  unset($uploaded_files[$key]);
		 }
	  }
	  if(!in_array($file,$uploaded_files)){
		echo "Файл не найден";
		exit();
	  }
	  if(unlink(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls'.DS.$file)){
		exit();
	  }
	  else{
		 print_r(error_get_last());
		 exit();
	  }

	}

	function delete_all(){
	  jimport( 'joomla.filesystem.file' );
	  jimport( 'joomla.filesystem.folder' );
	  $uploaded_files=JFolder::files( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls' );
	  foreach($uploaded_files as $key => $f ){
		 if(in_array(substr($f,-4),array('.xls','.csv','xlsx','.zip'))){
			  if(!unlink(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'xls'.DS.$f)){
				  print_r(error_get_last());
				  exit();
			  }
		 }
	  }
	}

	function get_productId_by_sku($sku){
	  if(!$this->sku_cache){
		  $this->_db->setQuery("SELECT virtuemart_product_id
								  FROM #__virtuemart_products WHERE product_sku = ".$this->_db->Quote($sku)."");
		  return $this->_db->loadResult();
	  }

	  if(!@$this->temp_product_table){
				  $this->_db->setQuery("SELECT virtuemart_product_id,product_sku
								  FROM #__virtuemart_products");
		 $this->temp_product_table=array_combine($this->_db->loadColumn(1),$this->_db->loadColumn(0));
			   }
	  if(isset($this->temp_product_table[$sku])){
		return $this->temp_product_table[$sku];
	  }
	  else{
		return false;
	  }
	}

	function get_productId_by_name($name){
	   if(!@$this->temp_product_table_by_name){
				  $this->_db->setQuery("SELECT virtuemart_product_id,product_name
								  FROM #__virtuemart_products_".$this->config->sufix);
		 $this->temp_product_table_by_name=array_combine($this->_db->loadColumn(1),$this->_db->loadColumn(0));
			   }
	  if(isset($this->temp_product_table_by_name[$name])){
		return $this->temp_product_table_by_name[$name];
	  }
	  else{
		return false;
	  }

	}

	function get_productId_by_gtin($gtin){
	   if(!@$this->temp_product_table_by_gtin){
				  $this->_db->setQuery("SELECT virtuemart_product_id,product_gtin
								  FROM #__virtuemart_products");
		 $this->temp_product_table_by_gtin=array_combine($this->_db->loadColumn(1),$this->_db->loadColumn(0));
			   }
	  if(isset($this->temp_product_table_by_gtin[$gtin])){
		return $this->temp_product_table_by_gtin[$gtin];
	  }
	  else{
		return false;
	  }

	}

	function get_productId_by_mpn($mpn){
	   if(!@$this->temp_product_table_by_mpn){
				  $this->_db->setQuery("SELECT virtuemart_product_id,product_mpn
								  FROM #__virtuemart_products");
		 $this->temp_product_table_by_mpn=array_combine($this->_db->loadColumn(1),$this->_db->loadColumn(0));
			   }

	  if(isset($this->temp_product_table_by_mpn[$mpn])){
		return $this->temp_product_table_by_mpn[$mpn];
	  }
	  else{
		return false;
	  }

	}

	function is_productId_new($virtuemart_product_id){
	  if(!@$this->temp_productID_table){
				  $this->_db->setQuery("SELECT virtuemart_product_id
								  FROM #__virtuemart_products");
				  $this->temp_productID_table=$this->_db->loadColumn();
	  }
	  return !in_array($virtuemart_product_id,$this->temp_productID_table);
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

	function getFileForCron(){
		$files=JFolder::files( $this->cron_file_dir);
		$new_array=array();
		foreach($files as $file){
		   $ext=pathinfo($file, PATHINFO_EXTENSION);
		   if(!in_array($ext,array('xls','xlsx','csv'))){
				continue;
		   }
		   $time=filemtime($this->cron_file_dir.$file);
		   $new_array[$time]=$file;
		}
		krsort($new_array);
		return array_shift($new_array);
	}

	function cron_log($msg){
	  $fp = fopen( dirname(__FILE__).DS."cron_log.txt" , "a" );
	  fwrite($fp, date("Y-m-d H:i:s")." - ".$msg."\r\n");
	  fclose($fp);
	  echo "$msg<br>";
	}

	function getSize($bytes){
	   if($bytes<1024)
	   	  return $bytes." B<br>";
	   elseif($bytes<1024*1024)
	   	  return round($bytes/1024)." KB<br>";
	   else
	   	  return round($bytes/(1024*1024),2)." MB<br>";
	}

	function getCustomValueId($custom_id, $value){
	   if(!@$this->temp_custom_value_id_table){
		   file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'report.txt',date("Y-m-d H:i:s")." - Перед кэшированием Значений доп. полей. Память - ".$this->get_mem()."\n",FILE_APPEND);

		   $this->_db->setQuery("SELECT id, CONCAT(virtuemart_custom_id,'_',value) FROM #__virtuemart_product_custom_plg_param_values");

		   $this->temp_custom_value_id_table=array_combine($this->_db->loadColumn(1),$this->_db->loadColumn(0));
		   file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'report.txt',date("Y-m-d H:i:s")." - После кэширования Значений доп. полей. Память - ".$this->get_mem()."\n",FILE_APPEND);
		}
		if(isset($this->temp_custom_value_id_table[$custom_id."_".$value])){
		  return $this->temp_custom_value_id_table[$custom_id."_".$value];
		}
		else{
		  return false;
		}

	}

	function getCustomValueId_CFFA($custom_id, $value){

	   if(!@$this->temp_custom_value_id_table_cffa){
		   
		   $this->_db->setQuery("SELECT customsforall_value_id, CONCAT(virtuemart_custom_id,'_',customsforall_value_name)
		   FROM #__virtuemart_custom_plg_customsforall_values");

		   $this->temp_custom_value_id_table_cffa=array_combine($this->_db->loadColumn(1),$this->_db->loadColumn(0));
				   }
		if(isset($this->temp_custom_value_id_table_cffa[$custom_id."_".$value])){
		  return $this->temp_custom_value_id_table_cffa[$custom_id."_".$value];
		}
		else{
		  return false;
		}

	}

	function getCustomValueId_CFFA_hex($custom_id, $value){

	   if(!@$this->temp_custom_value_id_table_cffa_hex){
		   
		   $this->_db->setQuery("SELECT customsforall_value_id, CONCAT(virtuemart_custom_id,'_',customsforall_value_label)
		   FROM #__virtuemart_custom_plg_customsforall_values");

		   $this->temp_custom_value_id_table_cffa_hex=array_combine($this->_db->loadColumn(1),$this->_db->loadColumn(0));
				   }
		if(isset($this->temp_custom_value_id_table_cffa_hex[$custom_id."_".$value])){
		  return $this->temp_custom_value_id_table_cffa_hex[$custom_id."_".$value];
		}
		else{
		  return false;
		}

	}

	function getCustomFieldID($title){
		$this->_db->setQuery("SELECT virtuemart_custom_id FROM #__virtuemart_customs WHERE custom_title = ".$this->_db->Quote($title)."");
		$virtuemart_custom_id=$this->_db->loadResult();
		if(!$virtuemart_custom_id){
		   $this->_db->setQuery("INSERT INTO #__virtuemart_customs SET custom_title = ".$this->_db->Quote($title).", field_type = 'S'");
		   $this->_db->Query();
		   $virtuemart_custom_id=$this->_db->insertid();
		}
		return $virtuemart_custom_id;
	}

	function getImportFieldID($extra_id,$title){
		$this->_db->setQuery("SELECT id FROM #__excel2vm_fields WHERE extra_id = $extra_id AND type='extra'");
		$id=$this->_db->loadResult();
		if(!$id){
		   $this->_db->setQuery("SELECT MAX(id) FROM #__excel2vm_fields");
		   @$obj->id=$id=$this->_db->loadResult()+1;
		   $obj->extra_id=$extra_id;
		   $obj->title=$title;
		   $obj->name="extra_{$obj->id}";

		   $obj->example=JText::_('CUSTOM_FIELD_VALUE') ." ($obj->id);". JText::_('CUSTOM_FIELD_VALUE') ." ($obj->id)";
		   $obj->type='extra';
		   $this->_db->insertObject("#__excel2vm_fields",$obj);
		}
		return $id;
	}

	function insertEmpty(){
		 $this->_db->setQuery("SELECT MAX(id) FROM #__excel2vm_fields");
		 $empty_id=$id=$this->_db->loadResult()+1;

		 $this->_db->setQuery("INSERT INTO #__excel2vm_fields SET id=$empty_id,name='empty_{$empty_id}',title='EMPTY_COLUMN',type='empty',example='EMPTY;EMPTY'");
		 $this->_db->Query();
		 return $this->_db->insertid();
	}

	function bind_multivars(){
		$this->_db->setQuery("SELECT DISTINCT custom_field_id FROM #__excel2vm_multy");
		$custom_field_ids=$this->_db->loadColumn();
		foreach($custom_field_ids as $custom_field_id){
			$this->_db->setQuery("SELECT custom_params FROM #__virtuemart_customs WHERE	virtuemart_custom_id = $custom_field_id");
			$custom_params_template=explode("|",$this->_db->loadResult());
			$this->_db->setQuery("SELECT DISTINCT  	parent_id FROM #__excel2vm_multy WHERE custom_field_id = '$custom_field_id'");
			$parent_ids= $this->_db->loadColumn();
			foreach($parent_ids as $parent_id){
			   $variant_data=array();
			   $selectoptions=array();
			   $options=array();

							  $this->_db->setQuery("SELECT DISTINCT CONCAT(`type`,'-',`clabel`) FROM #__excel2vm_multy WHERE custom_field_id = '$custom_field_id' AND parent_id = '$parent_id'");
			   $type_labels=$this->_db->loadColumn();
			   $i=0;
			   foreach($type_labels as $key=> $tl){
				   $temp=explode("-",$tl);
				   $type= $temp[0];
				   $clabel= $temp[1];
				   unset($temp);
				   $this->_db->setQuery("SELECT DISTINCT value FROM #__excel2vm_multy WHERE custom_field_id = '$custom_field_id' AND parent_id = '$parent_id' AND  `type` = '$type' AND `clabel` = '$clabel'");

				   $values=$this->_db->loadColumn();
				   if(count($values)==1 AND empty($values[0])){
						continue;
				   }
				   @$selectoptions[$i]->voption= $type;
				   $selectoptions[$i]->clabel= $clabel;
				   $selectoptions[$i]->values= implode('{placeholder}',$values);
				   $i++;
			   }



							  $this->_db->setQuery("SELECT DISTINCT child_id FROM #__excel2vm_multy WHERE custom_field_id = '$custom_field_id' AND parent_id = '$parent_id'");
			   $child_ids=$this->_db->loadColumn();
			   if($child_ids[0]){
				   $this->_db->setQuery("SELECT COUNT(value) FROM #__excel2vm_multy WHERE custom_field_id = '$custom_field_id' AND parent_id = '$parent_id' AND child_id = '$child_ids[0]'");
				   $num_variants=$this->_db->loadResult();
				   $options[$parent_id] = array_fill(0,$num_variants,"0");
			   }

			   foreach($child_ids as $child_id){

				   $this->_db->setQuery("SELECT value FROM #__excel2vm_multy WHERE custom_field_id = '$custom_field_id' AND parent_id = '$parent_id' AND child_id = '$child_id' ORDER BY mv_id");
				   $options[$child_id]=$this->_db->loadColumn();

				   $this->_db->setQuery("SELECT value, type FROM #__excel2vm_multy WHERE custom_field_id = '$custom_field_id' AND parent_id = '$parent_id' AND child_id = '$child_id' AND type !='clabels'");
				   $product_child_data=$this->_db->loadObjectList();
				   if(count($product_child_data)){
					   $q="UPDATE #__virtuemart_products as p
							LEFT JOIN #__virtuemart_products_".$this->config->sufix." as r
							ON r.virtuemart_product_id = p.virtuemart_product_id
							SET";
					   $set=array();
					   foreach($product_child_data as $p){
						  $set[]="`$p->type` = ".$this->_db->Quote($p->value);
					   }
					   $q.=implode(", ",$set)." WHERE p.virtuemart_product_id = '$child_id'";
					   $this->_db->setQuery($q);
					   $this->_db->Query();
				   }

			   }


			   $variant_data[0]="usecanonical=0";
			   $variant_data[1]="showlabels=0";
			   $variant_data[2]="browseajax=0";
			   $variant_data[3]="sCustomId=0";
			   $variant_data[4]="selectType=0";

			   $variant_data[5]="selectoptions=".json_encode($selectoptions);
			   $variant_data[6]="clabels=0";
			   $variant_data[7]="options=".json_encode($options);

			   $custom_field_param=implode("|",$variant_data);
			   $custom_field_param=str_replace('{placeholder}','\r\n',$custom_field_param);

			   $this->_db->setQuery("SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = '$parent_id' AND virtuemart_custom_id = '$custom_field_id'");
			   $virtuemart_customfield_id=$this->_db->loadResult();

			   if($virtuemart_customfield_id){
					 $this->_db->setQuery("UPDATE #__virtuemart_product_customfields
										   SET
										   virtuemart_product_id = '$parent_id',
										   virtuemart_custom_id = '$custom_field_id',
										   {$this->fieldname_custom_value} = NULL,
										   customfield_params = ".$this->_db->Quote($custom_field_param )."
										   WHERE virtuemart_customfield_id = $virtuemart_customfield_id
										   ");
					 $this->_db->Query();
			   }
			   else{
					 $this->_db->setQuery("INSERT INTO #__virtuemart_product_customfields
										   SET
										   virtuemart_product_id = '$parent_id',
										   virtuemart_custom_id = '$custom_field_id',
										   {$this->fieldname_custom_value} = NULL,
										   customfield_params = ".$this->_db->Quote($custom_field_param )."
										   ");
					 $this->_db->Query();
			   }

			}
		}
		$this->_db->setQuery("TRUNCATE TABLE #__excel2vm_multy");
		$this->_db->Query();
	}

	function get_images_http($file_url,$id,$is_product=true,$is_full=true,$rename_params=array()){

		$file_url_ext=pathinfo($file_url, PATHINFO_EXTENSION);
		$extensions=array('jpg','jpeg','gif','png','bmp');

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, str_replace(" ","%20",$file_url));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, $this->images_timeout);
				curl_setopt($ch, CURLOPT_REFERER, "http://google.com/");
				curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
				$file=curl_exec($ch);
				$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$error=curl_error($ch);
				curl_close($ch);

			
			if($response_code!=200){
				if($this->debug){
					echo "Ошибка при скачивании изображения $file_url. Код ответа сервера - ".$response_code.". Ошибка - ".$error."<br>";
				}

				return NULL;
			}
			if(!$file)return NULL;
			$temp_path = explode("/", $file_url);
			$image_name = end($temp_path);
			$image_name = strtolower($image_name);
			$ext=pathinfo($image_name, PATHINFO_EXTENSION);


			if(substr($ext,0,3)=='php'){
				 if(strlen($ext)>3){
					 $query=substr($ext,3);
					 $query=str_replace(array("?","&","="," "),"_",$query);
					 $image_name= str_replace(".$ext", '', $image_name);
					 $image_name.=$query.".jpg";
				 }
				 else{
					 $image_name = str_replace(".$ext", '.jpg', $image_name);
				 }
			}
			elseif(strstr($file_url,"?")){
				foreach($extensions as $ext){
					$str_pos=strpos($file_url,".$ext");
					if($str_pos){
						$temp_name=substr($file_url,0,$str_pos+strlen(".$ext"));

						preg_match("/[0-9a-zA-Z_]*.$ext/",$temp_name,$matches);

						if(strlen(@$matches[0])>strlen(".$ext")+1){
							$image_name=@$matches[0];
							break;
						}

					}
				}

				if(!$image_name){
					$image_name2=strstr($image_name,"=");

					if(strlen($image_name2)>1){
						$image_name=substr($image_name2,1);

					}
					else{
						$image_name = substr(strstr($image_name,"?"),1);
					}
					unset($image_name2);
					$image_name=str_replace(array("?","&","="," "),"_",$image_name);
					if(!in_array($ext,array('jpg','gif','bmp','png'))){
						$image_name = str_replace(".$ext", '.jpg', $image_name);
					}
				}

			}
			elseif(!in_array($ext,array('jpg','gif','bmp','png'))){
				$image_name = str_replace(".$ext", '.jpg', $image_name);
				$ext='jpg';
			}

			$file_name=$image_name;

			if($is_full){
			   $path=$this->config->path;
			}
			else{
				$path=$this->config->thumb_path;
			}

			if(!$is_product){
				$path=str_replace("/product","/category",$path);
			}
			if(substr($path,-1)!="/"){
				 $path.="/";
			}
			$put_path=str_replace("/",DS,$path);
			$file_name=str_replace(array("?","&","="," "),"_",$file_name);

			if($this->images_rename AND $is_product AND $rename_params['sku']){				 $file_name=$rename_params['sku']."_".($rename_params['index']+1).".".$ext;
			}

			if(!file_put_contents(JPATH_ROOT.DS.$put_path.$file_name,$file)){
				$error=error_get_last();
				echo "Ошибка при записи изображения $file_url - ".$error['message']."<br>";
			}

			unset($file);
			return $path.$file_name;

	}

	function chek_indexes(){
		$this->_db->setQuery("SELECT count(*) FROM information_schema.tables WHERE table_name = '".$this->_db->getPrefix()."virtuemart_product_custom_plg_param_values'");
		if($this->_db->loadResult()){
			 $this->_db->setQuery("SHOW INDEX FROM ".$this->_db->getPrefix()."virtuemart_product_custom_plg_param_values");
			 $indexes= $this->_db->loadObjectList();
			 $index_exist=false;
			 foreach($indexes as $key => $v){
				if($v->Key_name=='virtuemart_custom_id'){
				   $index_exist=true;
				}
			 }
			 if(!$index_exist){
				 $this->_db->setQuery("ALTER TABLE ".$this->_db->getPrefix()."virtuemart_product_custom_plg_param_values ADD INDEX `virtuemart_custom_id` (virtuemart_custom_id)");
				 $this->_db->Query();
			 }
		}

		$this->_db->setQuery("SELECT count(*) FROM information_schema.tables WHERE table_name = '".$this->_db->getPrefix()."virtuemart_product_custom_plg_param_ref'");
		if($this->_db->loadResult()){
			 $this->_db->setQuery("SHOW INDEX FROM ".$this->_db->getPrefix()."virtuemart_product_custom_plg_param_ref");
			 $indexes= $this->_db->loadObjectList();
			 $index_exist=false;
			 foreach($indexes as $key => $v){
				if($v->Key_name=='virtuemart_product_id'){
				   $index_exist=true;
				}
			 }
			 if(!$index_exist){
				 $this->_db->setQuery("CREATE INDEX `virtuemart_product_id` ON ".$this->_db->getPrefix()."virtuemart_product_custom_plg_param_ref(virtuemart_product_id)");
				 $this->_db->Query();

			 }
		}

	}

	function profiler_log($mark){
		if(!$this->need_profiler)return false;
		file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS."profiler.txt",date("H:i:s")." - ".$this->profiler->mark($mark)."\n",FILE_APPEND);
	}

	function identity($row,&$new,&$cancel){
		if(!isset($this->config->key_field)){
		   $this->config->key_field=0;
		}
		switch($this->config->key_field) {
			  case '0':						@$virtuemart_product_id=(int)$row['virtuemart_product_id'];
					   if(!$virtuemart_product_id){
						   $virtuemart_product_id=$this->extra_identity($row,$cancel);
					   }
					   if(!$virtuemart_product_id){
						   $new=1;
						   return 0;
					   }
					   else{
						   if($this->productid_cache){
								$new= $this->is_productId_new($virtuemart_product_id);

						   }
						   else{
								$this->_db->setQuery("
								SELECT virtuemart_product_id
								FROM #__virtuemart_products
								WHERE virtuemart_product_id = '$virtuemart_product_id'");
								$new=($this->_db->loadResult())?0:1;
						   }
						   return $virtuemart_product_id;
					   }

			  break;
			  case '1':					   if(@!$row['product_sku']){
						   $virtuemart_product_id=$this->extra_identity($row,$cancel);
					  }
					  else{
							if($this->sku_cache){
								 $virtuemart_product_id = $this->get_productId_by_sku($row['product_sku']);
							}
							else{
								 $this->_db->setQuery("SELECT p.virtuemart_product_id
												  FROM #__virtuemart_products as p
												  WHERE product_sku='".$this->escape($row['product_sku'])."'");
								 $virtuemart_product_id = $this->_db->loadResult();
							}
					  }

			  break;
			  case '2':						 if(!@$row['product_name']){
							$virtuemart_product_id=$this->extra_identity($row,$cancel);
						}
						else{
							if($this->name_cache){
								  $virtuemart_product_id = $this->get_productId_by_name($row['product_name']);
							}
							else{
								$this->_db->setQuery("SELECT p.virtuemart_product_id
					  								  FROM #__virtuemart_products as p
					  								  LEFT JOIN #__virtuemart_products_".$this->config->sufix." as r ON r.virtuemart_product_id = p.virtuemart_product_id
					  								  WHERE product_name='".$this->escape($row['product_name'])."' AND r.virtuemart_product_id IS NOT NULL ORDER BY p.virtuemart_product_id",0,1);
					  			$virtuemart_product_id = $this->_db->loadResult();
							}
							
							if($virtuemart_product_id AND !$this->config->update_without_sku){
								 $cancel="Update by Name Restricted";
								 return 0;							}
							if(!$virtuemart_product_id AND @!$row['product_sku'] AND !$this->config->create_without_sku){
								 $cancel="Creation by Name Restricted";
								 return 0;							}
						}
			  break;
			  case '3':						if(!@$row['product_gtin']){
							$virtuemart_product_id=$this->extra_identity($row,$cancel);
					   }
					   else{
						   if($this->gtin_cache){
								 $virtuemart_product_id = $this->get_productId_by_gtin($row['product_gtin']);
						   }
						   else{
								$this->_db->setQuery("SELECT virtuemart_product_id
					  								  FROM #__virtuemart_products
					  								  WHERE product_gtin='".$this->escape($row['product_gtin'])."'
													  ORDER BY virtuemart_product_id",0,1);
					  			$virtuemart_product_id = $this->_db->loadResult();
						   }
					   }
			  break;
			  case '4':						if(!@$row['product_mpn']){
							$virtuemart_product_id=$this->extra_identity($row,$cancel);
					   }
					   else{
						   if($this->mpn_cache){
								 $virtuemart_product_id = $this->get_productId_by_mpn($row['product_mpn']);
						   }
						   else{
								$this->_db->setQuery("SELECT virtuemart_product_id
					  								  FROM #__virtuemart_products
					  								  WHERE product_mpn='".$this->escape($row['product_mpn'])."'
													  ORDER BY virtuemart_product_id",0,1);
					  			$virtuemart_product_id = $this->_db->loadResult();
						   }
					   }
			  break;

			}

			if(!$virtuemart_product_id){
				$new=1;
				return 0;
			}
			else{
				$new=0;
				return $virtuemart_product_id;
			}
	}

	function extra_identity($row,&$cancel){
		if(!@$row['virtuemart_product_id'] AND @$row['product_sku']){						if($this->sku_cache){
				 $virtuemart_product_id = $this->get_productId_by_sku($row['product_sku']);
			}
			else{
				 $this->_db->setQuery("SELECT p.virtuemart_product_id
								  FROM #__virtuemart_products as p
								  WHERE product_sku='".$this->escape($row['product_sku'])."'");
				 $virtuemart_product_id = $this->_db->loadResult();
			}
			if(!$virtuemart_product_id){
				return 0;
			}
			return $virtuemart_product_id;
					}
		elseif(!@$row['virtuemart_product_id'] AND @$row['product_name']){						if($this->name_cache){
				  $virtuemart_product_id = $this->get_productId_by_name($row['product_name']);
			}
			else{
				$this->_db->setQuery("SELECT p.virtuemart_product_id
	  								  FROM #__virtuemart_products as p
	  								  LEFT JOIN #__virtuemart_products_".$this->config->sufix." as r ON r.virtuemart_product_id = p.virtuemart_product_id
	  								  WHERE product_name='".$this->escape($row['product_name'])."' AND r.virtuemart_product_id IS NOT NULL ORDER BY p.virtuemart_product_id",0,1);
	  			$virtuemart_product_id = $this->_db->loadResult();
			}
			
			if($virtuemart_product_id AND !$this->config->update_without_sku){
				 $cancel="Update by Name Restricted";
				 return 0;			}
			if(!$virtuemart_product_id AND !$this->config->create_without_sku){
				 $cancel="Creation by Name Restricted";
				 return 0;			}
			if(!$virtuemart_product_id){
			   return 0;
			}
			return $virtuemart_product_id;
		}
		elseif(@$row[virtuemart_product_id]){			return $row[virtuemart_product_id];

		}
		return 0;
	}
}
