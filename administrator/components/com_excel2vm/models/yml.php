<?php

 defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
ini_set('log_errors', 'On');
ini_set('error_log', JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'yml_error.txt');

require_once (dirname(__FILE__) . DS . "updateTable.php");

class Excel2vmModelYml extends JModelLegacy {
	public $pagination;

	function __construct($cron=false) {
		parent::__construct();
		
		$this->params=JComponentHelper::getParams('com_excel2vm');

		ini_set("max_execution_time",$this->params->get('max_execution_time',300));
		ini_set("upload_max_filesize",$this->params->get('post_max_size',20)."M");
		ini_set("post_max_size",$this->params->get('post_max_size',20)."M");

		$this->debug=$this->params->get('db_debug',0);
		$this->delivery=$this->params->get('delivery',1);
		$this->stock=$this->params->get('stock',0);
		$this->pickup=$this->params->get('pickup',1);
		$this->manufacturer_warranty=$this->params->get('manufacturer_warranty',1);
		$this->yml_available=$this->params->get('yml_available',0);
		$this->store=$this->params->get('store',1);
		$this->yml_description=$this->params->get('yml_description',0);
		$this->cut_description=$this->params->get('cut_description',0);
		$this->show_old_price=$this->params->get('show_old_price',0);
		$this->price_round=$this->params->get('price_round',2);
		$this->local_delivery_cost=$this->params->get('local_delivery_cost',0);
		$this->sales_notes=mb_substr(trim($this->params->get('sales_notes')),0,100);
		$this->delivery_options=trim($this->params->get('delivery_options'));
		/*if($debug){
			require_once (dirname(__FILE__) . DS . "db_debug.php");
			$full_debug=JRequest::getVar('full_debug', 'cookie', 0, 'int');
			$this->_db=new JDatabaseMySQLbak($full_debug,$full_debug);
		}*/
		$this->core = new core();
		$this->cron_yml=$cron;
		$this->core->cron_yml=$cron;
		$this->_db->debug($this->debug);

		$this->config_table = new updateTable("#__excel2vm", "id", 1);
		$this->config =$this->core->getConfig();

		$xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.xml');
		$this->vm_version=(string)$xml->version;
		$this->is_vm_version_3=(substr($this->vm_version,0,1)==3 OR substr($this->vm_version,0,3)=='2.9')?true:false;
		$this->fieldname_custom_value=$this->is_vm_version_3?'customfield_value':'custom_value';
		$this->fieldname_custom_price=$this->is_vm_version_3?'customfield_price':'custom_price';

								
												
				$this->trans = array("а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "yo", "ж" => "j", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "sh", "ы" => "y", "э" => "e", "ю" => "u", "я" => "ya", "А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d", "Е" => "e", "Ё" => "yo", "Ж" => "j", "З" => "z", "И" => "i", "Й" => "y", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n", "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t", "У" => "u", "Ф" => "f", "Х" => "h", "Ц" => "c", "Ч" => "ch", "Ш" => "sh", "Щ" => "sh", "Ы" => "y", "Э" => "e", "Ю" => "u", "Я" => "ya", "ь" => "", "Ь" => "", "ъ" => "", "Ъ" => "","/" =>"-","\\" =>"","-" =>"-",":" =>"-","(" =>"-",")" =>"-","." =>"","," =>"",'"'=>"-",'>'=>"-",'<'=>"-",'+'=>"-",'«'=>'','»'=>'',"'"=>"","і"=>"i","ї"=>"yi","І"=>"i","Ї"=>"yi","є"=>"e","Є"=>"e");

		$this->backup_tables_array = array("#__virtuemart_categories", "#__virtuemart_categories_".$this->config->sufix, "#__virtuemart_category_categories", "#__virtuemart_products", "#__virtuemart_products_".$this->config->sufix, "#__virtuemart_product_medias", "#__virtuemart_product_prices", "#__virtuemart_customs","#__virtuemart_product_customfields", "#__virtuemart_product_categories","#__virtuemart_product_manufacturers","#__virtuemart_manufacturers","#__virtuemart_manufacturers_".$this->config->sufix);

						$user = JFactory::getUser();
		$this->user_id=$user->id;
		$this->need_profiler=0;

	}

	function translit($text) {
		$trans=strtolower(strtr($text, $this->trans));
		$trans = str_replace(" ","-",$trans);
		while(strstr($trans,"--"))
			$trans = str_replace("--","-",$trans);
		$trans =  preg_replace('/[\x00-\x2C\x7B-\xFF]/', '', $trans);


		return $trans;
	}

	function category_list(){
		$this->_db->setQuery("SELECT r.virtuemart_category_id,category_name
						  FROM #__virtuemart_categories_".$this->config->sufix." as r
						  LEFT JOIN #__virtuemart_categories as c ON c.virtuemart_category_id = r.virtuemart_category_id
						  WHERE c.virtuemart_category_id IS NOT NULL");
		return $this->_db->loadObjectList('virtuemart_category_id');
	}

	function manufacturers_list(){
		$this->_db->setQuery("SELECT r.virtuemart_manufacturer_id,mf_name
						  FROM #__virtuemart_manufacturers_".$this->config->sufix." as r
						  LEFT JOIN #__virtuemart_manufacturers as m ON m.virtuemart_manufacturer_id = r.virtuemart_manufacturer_id
						  WHERE m.virtuemart_manufacturer_id IS NOT NULL");
		return $this->_db->loadObjectList('virtuemart_manufacturer_id');
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

	function getYmlConfig(){
		 $this->_db->setQuery("SELECT * FROM #__excel2vm_yml WHERE id = 1");
		 $conf_data=$this->_db->loadObject();
		 @$params=json_decode($conf_data->params);
		 @$params->yml_export_path=$conf_data->yml_export_path;
		 @$params->yml_import_path=$conf_data->yml_import_path;
		 return $params;
	}

	function getYmlExportConfig(){
		 $this->_db->setQuery("SELECT export_params FROM #__excel2vm_yml WHERE id = 1");
		 return @json_decode($this->_db->loadResult());
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
	


	function yml_export(){
		 ob_start();
		 $mainframe = JFactory::getApplication();
		 $this->sef=$mainframe->getCfg('sef');
		 $this->sef_rewrite=$mainframe->getCfg('sef_rewrite');
		 $this->sef_suffix=$mainframe->getCfg('sef_suffix')?'.html':'';
		 $this->row=JRequest::getVar('row', '0', '', 'int');
		 $this->start_time=time();
		 $this->timeout=time()+$this->params->get('max_execution_time',300)-5;
		 $this->last_upd=time()-4;
		 require(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."config.php");
		 $VmConfig=VmConfig::loadConfig();
		 $VmConfig=$VmConfig->_params;

		 $vm_seo_sufix=$VmConfig['seo_sufix'];
		 $this->sef_suffix=$vm_seo_sufix.$this->sef_suffix;

		 //$this->immport();
		 $this->is_cherry=$this->is_cherry();
		 if($this->cron_yml){
			 $this->_db->setQuery("SELECT yml_export_path FROM #__excel2vm_yml WHERE id = 1");
			 $yml_export_path=$this->_db->loadResult();
		 }
		 else{
			 $yml_export_path=JRequest::getVar('yml_export_path', 'post', '', 'string');
		 }

		 if(!$yml_export_path){
			 $yml_export_path=JPATH_ROOT.DS."ymarket.xml";
		 }
		 $dir=dirname($yml_export_path);
		 if(!file_exists($dir)){
			 $this->print_answer("Папка $dir не существует. Проверьте правильность пути");
		 }

		 if(!is_writable($dir)){
		   $this->print_answer("Папка $dir не доступна на запись. Проверьте права на эту папку");
		 }

		 $ext=pathinfo($yml_export_path, PATHINFO_EXTENSION);
		 if($ext!='xml'){
			 $yml_export_path = str_replace(".$ext",".xml",$yml_export_path);
		 }
		 $this->live_site = str_replace('administrator/components/com_excel2vm/models/', '', JURI::root());
		 $alternative_domain=$this->params->get('alternative_domain');
		 if($alternative_domain){
			if(substr($alternative_domain,0,7)!='http://'){
				$alternative_domain='http://'.$alternative_domain;
			}
			if(substr($alternative_domain,-1)!='/'){
				$alternative_domain=$alternative_domain.'/';
			}
			$this->live_site=$alternative_domain;
		 }
		 if(count($_POST)){
			 $export_params['currency']=JRequest::getVar('currency', '0', 'post', 'int');
			 $export_params['user_group']=JRequest::getVar('user_group', '2', 'post', 'int');
			 $export_params['export_resume']=JRequest::getVar('export_resume', '0', 'post', 'int');
			 $export_params['export_categories']=JRequest::getVar('export_categories', array(), 'post', 'array');
			 $export_params['export_manufacturers']=JRequest::getVar('export_manufacturers', array(), 'post', 'array');
			 JArrayHelper::toInteger($export_params['export_categories']);
			 JArrayHelper::toInteger($export_params['export_manufacturers']);

			 $this->_db->setQuery("INSERT INTO #__excel2vm_yml SET id = 1, yml_export_path = ".$this->_db->Quote($yml_export_path).", export_params=".$this->_db->Quote(json_encode($export_params))." ON DUPLICATE KEY UPDATE yml_export_path = ".$this->_db->Quote($yml_export_path).", export_params=".$this->_db->Quote(json_encode($export_params))."");
			 $this->_db->Query();
		 }



		 $this->yml_file=$yml_export_path;

		 $this->export_config=$this->getYmlExportConfig();

		 if($this->export_config->currency){
		   $this->_db->setQuery("SELECT vendor_currency FROM #__virtuemart_vendors WHERE virtuemart_vendor_id = 1");
		   $this->default_currency=$this->_db->loadResult();
		   if($this->default_currency!=$this->export_config->currency){
			   $this->_db->setQuery("UPDATE #__virtuemart_vendors SET vendor_currency = '{$this->export_config->currency}' WHERE virtuemart_vendor_id = 1");
			   $this->_db->execute();
		   }
		}
		if(!$this->row){			 file_put_contents($yml_export_path,"");
			 copy(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'libraries'.DS.'shops.dtd',$dir.DS.'shops.dtd');
			 $this->yml_file_init();

			 $this->print_categories();
			 if($this->local_delivery_cost AND !$this->delivery_options){
				 $this->insert_tag("local_delivery_cost",$this->local_delivery_cost,2);
			 }

			 if($this->delivery_options){
				 $delivery_options=explode("\n",$this->delivery_options);
				 if(count($delivery_options) AND !empty($delivery_options[0])){
					   $this->insert_tag("delivery-options","",2,1);
						  foreach($delivery_options as $v){
							 if(empty($v)){
								 continue;
							 }

							 $option_attrs=explode(";",$v);
							 if(count($option_attrs)==2){
								 $this->insert_tag("option",'',3,0,array("cost"=>$option_attrs[0],"days"=>$option_attrs[1]));
							 }
							 elseif(count($option_attrs)==3){
								 $this->insert_tag("option",'',3,0,array("cost"=>$option_attrs[0],"days"=>$option_attrs[1],"order-before"=>$option_attrs[2]));
							 }
						  }

					   $this->insert_tag("delivery-options","",2,2);
				 }
			 }

			 $this->insert_tag("offers","",2,1);
		 }
		 else{
			 $log_data=file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'yml-export-log.txt');
			 $log_data=json_decode($log_data);
			 $this->start_time=$log_data->start_time;

			 $this->_db->setQuery("SELECT vendor_name,vendor_currency,vendor_accepted_currencies FROM #__virtuemart_vendors WHERE virtuemart_vendor_id = 1");
			 $this->vendor=$this->_db->loadObject();

			 $this->_db->setQuery("SELECT currency_code_3 FROM #__virtuemart_currencies WHERE virtuemart_currency_id ='{$this->vendor->vendor_currency}'");
			 $this->vendor_currency=$this->_db->loadResult();
		 }


		 $this->print_products();

		 $this->insert_tag("offers","",2,2);
		 $this->yml_file_end();

		 $this->updateExportStat($this->total_products,$this->total_products,1);

		 if($this->real_time){
			 header('Content-Type: application/xml; charset=utf-8');
			 echo file_get_contents($yml_export_path);
			 exit();
		 }

		 $link=str_replace(DS,'/',str_replace(JPATH_ROOT.DS,JURI::root(),$yml_export_path));

		 if($this->cron_yml){
			 $link=str_replace('/administrator/components/com_excel2vm/models','',$link);
		 }

		 $this->print_answer("Ссылка на XML - <a href='$link' target='_blank'>$link</a>",1);


	}

	function yml_sanitize($string){
		$string =  preg_replace('/[\x00-\x08\xB\xC\xE-\x1F]/', '', $string);
		return htmlspecialchars(trim($string));
	}

	function yml_file_wright($string,$level=0,$force=false){
		$tabs='';
		for($i=0;$i<$level;$i++){
		   $tabs.="\t";
		}
		$this->buffer.=$tabs.$string."\n";
		if(strlen($this->buffer)>(1024*1024) OR $force){
			 file_put_contents($this->yml_file,$this->buffer,FILE_APPEND);
			 $this->buffer='';
		}
	}

	function yml_file_init(){
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'calculationh.php');

		$this->version = new JVersion();
		$this->version = substr($this->version->getShortVersion(), 0, 1);

		VmConfig::loadConfig();

		if ($this->version == 3) {
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'product.php');
			$this->vm_model = new VirtueMartModelProduct();
		}
		$this->calculator = calculationHelper::getInstance();

		@$this->buffer='';

		$this->yml_file_wright('<'.'?xml version="1.0" encoding="UTF-8"?'.'>');
		$this->yml_file_wright('<!DOCTYPE yml_catalog SYSTEM "shops.dtd">');
		$this->yml_file_wright('<yml_catalog date="'.date("Y-m-d H:i").'">');
		$this->yml_file_wright('<shop>',1);

		try{
			$this->_db->setQuery("SELECT vendor_store_name FROM #__virtuemart_vendors_".$this->config->sufix." WHERE virtuemart_vendor_id = 1");
			$name=$this->_db->loadResult();
		}
		catch(Exception $e){
		   $this->print_answer($this->_db->getErrorMsg());
		}

		$this->_db->setQuery("SELECT vendor_name,vendor_currency,vendor_accepted_currencies FROM #__virtuemart_vendors WHERE virtuemart_vendor_id = 1");
		$this->vendor=$this->_db->loadObject();
		$company=$this->vendor->vendor_name;

		$company=$company?$company:"Укажите название магазина в настройках VM";
		$name=$name?$name:"Укажите название магазина в настройках VM";

		$this->insert_tag("name",mb_substr($name, 0, 20, 'UTF-8'),2);
		$this->insert_tag("company",$company,2);
		$this->insert_tag("url",$this->live_site,2);
		$this->print_currencies();


	}

	function yml_file_end(){
		$this->yml_file_wright('</shop>',1);
		$this->yml_file_wright('</yml_catalog>',0,true);
	}

	function print_currencies(){
		$vendor=$this->vendor;
		$this->insert_tag("currencies","",2,1);
		if(!$vendor->vendor_currency){
		   $this->insert_tag("currency","",3,3,array("id"=>"RUR","rate"=>1,"plus"=>0));
		}
		else{
			/*if($vendor->vendor_accepted_currencies){
				$this->_db->setQuery("SELECT virtuemart_currency_id, currency_code_3 FROM #__virtuemart_currencies WHERE virtuemart_currency_id IN ($vendor->vendor_accepted_currencies)");
				$this->currencies=$this->_db->loadObjectList('virtuemart_currency_id');

				foreach($this->currencies as $v){
					$rate=$v->virtuemart_currency_id==$vendor->vendor_currency?1:"CBRF";
					$this->insert_tag("currency","",3,3,array("id"=>$v->currency_code_3,"rate"=>$rate,"plus"=>0));
				}

			}
			else{*/
				$this->_db->setQuery("SELECT currency_code_3 FROM #__virtuemart_currencies WHERE virtuemart_currency_id ='$vendor->vendor_currency'");
				$currency=$this->_db->loadResult();
				$rate=in_array($currency,array("RUR","RUB","UAH","BYN","KZT"))?1:"CBRF";
				if($rate!=1){
				   $this->insert_tag("currency","",3,3,array("id"=>"RUR","rate"=>1,"plus"=>0));
				}
				$this->insert_tag("currency","",3,3,array("id"=>$currency,"rate"=>$rate,"plus"=>0));
				$this->currencies[$vendor->vendor_currency]->virtuemart_currency_id=$vendor->vendor_currency;
				$this->currencies[$vendor->vendor_currency]->currency_code_3=$currency;
				$this->vendor_currency=$currency;
		   
		}
		$this->insert_tag("currencies","",2,2);

	}

	function insert_tag($tag_name,$value,$level,$type=0,$atribs=array()){
		$atributes='';
		if(count($atribs)){
		   foreach($atribs as $key => $v){
			   $atributes.=' '.$key.' = "'.$this->yml_sanitize($v).'"';
		   }
		}
		switch($type) {
			  case 0:
					$this->yml_file_wright('<'.$tag_name.$atributes.'>'.$this->yml_sanitize($value).'</'.$tag_name.'>',$level);
			  break;
			  case 1:
					$this->yml_file_wright('<'.$tag_name.$atributes.'>',$level);
			  break;
			  case 2:
				   $this->yml_file_wright('</'.$tag_name.'>',$level);
			  break;
			  case 3:
				   $this->yml_file_wright('<'.$tag_name.$atributes.' />',$level);
			  break;
			}

	}

	function print_answer($msg,$success=false){
		ob_get_flush();
		if($this->cron_yml){
			echo nl2br($msg);
			file_put_contents(dirname(__FILE__)."/cron_yml_log.txt",date("Y-m-d H:i:s")." - $msg\n",FILE_APPEND);
			exit();
		}
		else{
			 header("Content-Type: content=text/html; charset=utf-8");
			 @$answer->msg=$msg;
			 @$answer->status=$success?"ok":"error";
			 echo json_encode($answer);
			 exit();
		}

	}

	function check_timeout($row){
		if(time()>=$this->timeout AND !$this->cron_yml){			 if($this->export_config->currency){
				  $this->_db->setQuery("UPDATE #__virtuemart_vendors SET vendor_currency = '$this->default_currency' WHERE virtuemart_vendor_id = 1");
				  $this->_db->execute();
			}
			ob_get_flush();
			$this->yml_file_wright('',0,true);
			header("Content-Type: content=text/html; charset=utf-8");
			@$answer->row=$row+1;
			@$answer->status="timeout";
			echo json_encode($answer);
			exit();
		}
	}

	function getCategories($cat_id,&$cat_array=array()){

	   if(!$cat_id){
			return false;
	   }

	   $this->_db->setQuery("SELECT category_child_id
					  FROM #__virtuemart_category_categories
					  WHERE category_parent_id = $cat_id
					  ");
	   $children=$this->_db->loadColumn();
	   foreach($children as $id){
		   $cat_array[]=$id;
		   $this->getCategories($id,$cat_array);
	   }
	   return $cat_array;

	}

	function getManufacturers(){
		$this->_db->setQuery("SELECT virtuemart_manufacturer_id, mf_name FROM #__virtuemart_manufacturers_".$this->config->sufix." ORDER BY mf_name");
		return $this->_db->loadObjectList();
	}

	function print_categories(){
		try{
			$query = 'SELECT a.category_parent_id, a.category_child_id, b.category_name
			FROM #__virtuemart_category_categories a
			RIGHT JOIN #__virtuemart_categories_'.$this->config->sufix.' b ON b.virtuemart_category_id = a.category_child_id
			WHERE a.category_child_id IS NOT NULL
			ORDER BY a.category_child_id';
			$this->_db->setQuery($query);

			$rows = $this->_db->loadObjectList();
		}
		catch(Exception $e){
		   $this->print_answer($this->_db->getErrorMsg());
		}
		$this->insert_tag("categories","",2,1);

		foreach ($rows as $row) {
			$cat_parent_id = $row->category_parent_id;
			$cat_child_id = $row->category_child_id;
			$cat_name = $row->category_name;
			if ($cat_name == '') {
				continue;
			}
			$params=array();
			if($cat_parent_id>0){
			   $params["parentId"]=$cat_parent_id;
			}
			$params["id"]=$cat_child_id;

			$this->insert_tag("category",$cat_name,3,0,$params);
		}
		$this->insert_tag("categories","",2,2);
	}

	function print_products(){
	   require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."models".DS."product.php");
	   $vm_model=new VirtueMartModelProduct();



	   $filter='';
	   if(@count($this->export_config->export_categories)){
		   if($this->export_config->export_resume){
				$filter=" AND g.virtuemart_category_id NOT IN(".implode(",",$this->export_config->export_categories).")";
		   }
		   else{
				$filter=" AND g.virtuemart_category_id IN(".implode(",",$this->export_config->export_categories).")";
		   }
	   }

	   if(@count($this->export_config->export_manufacturers)){
			$filter.=" AND f.virtuemart_manufacturer_id IN(".implode(",",$this->export_config->export_manufacturers).")";
	   }

	   if(!$this->export_config->user_group OR $this->export_config->user_group==2){
		   $price_filter=" AND  virtuemart_shoppergroup_id IN (0,2)";
	   }

	   else{
		   $price_filter=" AND  virtuemart_shoppergroup_id = {$this->export_config->user_group}";
	   }


	   $i=$this->row;
	   for(;;){
		  $query = '
		  SELECT
		  SQL_CALC_FOUND_ROWS
		  DISTINCT a.virtuemart_product_id,
		  a.product_weight,
		  a.product_length,
		  a.product_width,
		  a.product_height,
		  a.product_parent_id,
		  a.product_sku,
		  a.virtuemart_vendor_id,
		  a.product_in_stock,
		  b.product_name,
		  b.product_desc,
		  d.product_tax_id,
		  d.product_discount_id,
		  d.product_price,
		  d.product_override_price,
		  d.override,
		  d.product_currency,
		  e.mf_name,
		  e.virtuemart_manufacturer_id,
		  GROUP_CONCAT(g.virtuemart_category_id) as virtuemart_category_id
		  FROM (#__virtuemart_product_categories g
		  LEFT JOIN (#__virtuemart_product_prices d
					 RIGHT JOIN ((#__virtuemart_product_manufacturers f RIGHT JOIN #__virtuemart_products a ON f.virtuemart_product_id = a.virtuemart_product_id)
					 LEFT JOIN #__virtuemart_manufacturers_'.$this->config->sufix.' e ON f.virtuemart_manufacturer_id = e.virtuemart_manufacturer_id
					 LEFT JOIN #__virtuemart_products_'.$this->config->sufix.' b ON b.virtuemart_product_id = a.virtuemart_product_id) ON d.virtuemart_product_id = a.virtuemart_product_id '.$price_filter.') ON g.virtuemart_product_id = a.virtuemart_product_id)
		 WHERE a.published = 1 AND d.product_price > 0 AND b.product_name <> \'\' '.$filter.'
		 GROUP BY a.virtuemart_product_id';
		 try{
			 $this->_db->setQuery($query,$i,500);

			 $rows = $this->_db->loadObjectList();


		 }
		 catch(Exception $e){
			$this->print_answer( $this->_db->getErrorMsg());
		 }

		 $i=$i+500;
		 $this->_db->setQuery("SELECT FOUND_ROWS()");
		 $this->total_products = $this->_db->loadResult();

		 if(!$rows){
			 break;
		 }
		 $total_rows=count($rows);
		 for($a=0;$a<$total_rows;$a++){
			$product_name= $rows[$a]->product_name;
			if(!$product_name){
				continue;
			}
			$product_id = $rows[$a]->virtuemart_product_id;

			$product_cat_ids = explode(",",$rows[$a]->virtuemart_category_id);


			$product_cat_id=$product_cat_ids[0];
			/*if ($version == 3) {
				$prices=$this->vm_model->getRawProductPrices($rows[$a], 0, array(1), 1);
			}
			else{
				$prices = $this->calculator->getProductPrices($rows[$a]);
			}*/


			$offer_params=array();

			if($rows[$a]->mf_name){
				 $offer_params['type']="vendor.model";
			}
			$offer_params['id']=$product_id;
			if($this->yml_available){
				$offer_params['available']= 'true';
			}
			else{
				$offer_params['available']=$rows[$a]->product_in_stock > 0 ? 'true' : 'false';
			}

			$this->insert_tag("offer",'',3,1,$offer_params);
			$url = str_replace('/'.'/index.php','/index.php', $this->live_site.$this->urlMarketEncode('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product_id.'&virtuemart_category_id='.$product_cat_id));

			$this->insert_tag("url",$url,4);

			$this->_db->setQuery("SELECT * FROM #__virtuemart_product_prices WHERE virtuemart_product_id = $product_id $price_filter",0,1);
			$rows[$a]->allPrices[0]=$this->_db->loadAssoc();

			$this->_db->setQuery("SELECT  virtuemart_category_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id = $product_id");
			$rows[$a]->categories=$this->_db->loadColumn();
			$rows[$a]->selectedPrice=0;
			$p=$vm_model->getPrice($rows[$a],1);

			$product_price = $p['salesPrice'];

			$this->insert_tag("price",round($product_price,$this->price_round),4);
			if($this->show_old_price AND $p['basePrice']>$product_price){
				$this->insert_tag("oldprice",round($p['basePrice'],$this->price_round),4);
			}
			

			$this->insert_tag("currencyId",$this->vendor_currency,4);
			foreach($product_cat_ids as  $product_cat_id){
				 $this->insert_tag("categoryId",$product_cat_id,4);
			}
			$this->insert_tag("delivery",$this->delivery?"true":"false",4);


			if ($rows[$a]->mf_name) {
				$this->insert_tag("vendor",$rows[$a]->mf_name,4);
				$this->insert_tag("model",$product_name,4);
				$this->insert_tag("vendorCode",$rows[$a]->product_sku,4);
			}
			else{
				$this->insert_tag("name",$product_name,4);
			}



			$this->getImages($product_id);

			$this->insert_tag("manufacturer_warranty",$this->manufacturer_warranty?"true":"false",4);
			if($this->pickup>0){
				$this->insert_tag("pickup",$this->pickup?"true":"false",4);
			}
			if($this->store>0){
				$this->insert_tag("store",$this->store?"true":"false",4);
			}


			if($this->sales_notes){
				$this->insert_tag("sales_notes",$this->sales_notes,4);
			}



			if ($rows[$a]->product_desc) {
				if($this->cut_description){
					switch ($this->yml_description) {
					  case 0:
							$this->insert_tag("description",$this->substr(htmlspecialchars($rows[$a]->product_desc),175),4);
					  break;
					  case 1:
							$this->insert_tag("description",$this->substr(strip_tags($rows[$a]->product_desc),175),4);
					  break;
					  case 2:
							$this->insert_tag("description","<![CDATA[".$this->substr($rows[$a]->product_desc,163)."]]>",4);
					  break;
					}
				}
				else{
					switch ($this->yml_description) {
						case 0:
							  $this->insert_tag("description",htmlspecialchars($rows[$a]->product_desc),4);
						break;
						case 1:
							  $this->insert_tag("description",strip_tags($rows[$a]->product_desc),4);
						break;
						case 2:
							  $this->insert_tag("description","<![CDATA[".$rows[$a]->product_desc."]]>",4);
						break;
				   }
				}


			}

			if($this->stock){
				$this->insert_tag("stock",$rows[$a]->product_in_stock,4);
			}


			if($rows[$a]->product_weight > 0){
				 $this->insert_tag("weight",$rows[$a]->product_weight,4);
			}

			if($rows[$a]->product_length > 0 AND $rows[$a]->product_width > 0 AND $rows[$a]->product_height > 0 ){
				 $this->insert_tag("dimensions",$rows[$a]->product_length."/".$rows[$a]->product_width."/".$rows[$a]->product_height,4);
			}

						$this->_db->setQuery("SELECT {$this->fieldname_custom_value}
				   FROM #__virtuemart_product_customfields as pc
				   WHERE virtuemart_product_id = $product_id AND virtuemart_custom_id = 1
				   ");

			$related = $this->_db->loadColumn();
			if(count($related)){
			   $this->insert_tag("rec",implode(",",$related),4);
			}
			
			$this->_db->setQuery("SELECT pc.{$this->fieldname_custom_value},custom_title,pc. 	virtuemart_custom_id
				   FROM #__virtuemart_product_customfields as pc
				   LEFT JOIN #__virtuemart_customs as c ON c.virtuemart_custom_id = pc.virtuemart_custom_id
				   WHERE virtuemart_product_id = $product_id AND c.virtuemart_custom_id > 2
				   ");

			$customs = $this->_db->loadObjectList();

			if(count($customs)){
					  foreach($customs as $c){
													  if($c->{$this->fieldname_custom_value}=='param'){
								try{
									$this->_db->setQuery("SELECT val,intval, value
									FROM #__virtuemart_product_custom_plg_param_ref as r
									LEFT JOIN #__virtuemart_product_custom_plg_param_values as v USING( virtuemart_custom_id)
									WHERE virtuemart_product_id = $product_id AND r.virtuemart_custom_id = '$c->virtuemart_custom_id'");
									$param_data=$this->_db->loadObject();
									if(!$param_data->val){
										$c->{$this->fieldname_custom_value}=$param_data->intval;
									}
									elseif($param_data->value){
										 $c->{$this->fieldname_custom_value}=$param_data->value;
									}
									else{
									  continue;
									}
								}
								 catch(Exception $e){
									continue;
								}

						   }
						   if(empty($c->{$this->fieldname_custom_value})){
							   continue;
						   }
						   $this->insert_tag("param",$c->{$this->fieldname_custom_value},4,0,array("name"=>$c->custom_title));
					  }
			}

			if($this->is_cherry){				 $prefix=$this->is_cherry==1?"fastseller":"vm";
				$field_id=$this->is_cherry==1?"id":"product_id";
			}

			$this->insert_tag("offer",'',3,2);

			$this->updateExportStat($i-500+$a,$this->total_products);

			unset($rows[$a]);
			$this->check_timeout($i-500+$a);
		 }
	  }

	  if($this->export_config->currency){
			$this->_db->setQuery("UPDATE #__virtuemart_vendors SET vendor_currency = '$this->default_currency' WHERE virtuemart_vendor_id = 1");
			$this->_db->execute();
	  }
	}

	function urlMarketEncode($url) {
		if($this->sef){
		   $url_parsed=parse_url($url);
		   parse_str($url_parsed['query'],$query);
		   $url= ($this->sef_rewrite?"":"index.php/"). $this->get_slug_path($query['virtuemart_product_id']);
		}

		/*$url_arr = explode('/', $url);
		$url_st = '';
		foreach ($url_arr as $st) {
			$url_st .= '/'.urlencode($st);
		}*/
		$url_st= preg_replace("#(?<!^http:)/{2,}#i","/",$url);
				return $url_st;
	}

	function get_slug_path($virtuemart_product_id){
		$this->_db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id = $virtuemart_product_id");
		$parrent_category_id=$this->_db->loadResult();

		if(!$parrent_category_id)return 0;

		if(substr(JVERSION,0,1)==2){
			 return "component/virtuemart/view/productdetails/virtuemart_product_id/{$virtuemart_product_id}/virtuemart_category_id/{$parrent_category_id}".$this->sef_suffix;
		}

		$parent=$parrent_category_id;
		$home=false;
		$catergory_path='';
		while(!$catergory_path){
			$this->_db->setQuery("SELECT `path`, home FROM #__menu WHERE link LIKE 'index.php?option=com_virtuemart&view=category&virtuemart_category_id={$parent}%' ORDER BY id",0,1);
			$menu=$this->_db->loadObject();
			$catergory_path=$menu->path;
			if($menu->home){
				$catergory_path='';
				$home=true;
				break;
			}
			if(!$catergory_path){
				 $this->_db->setQuery("SELECT category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id = '$parent'");
				 $parent=$this->_db->loadResult();
				 if(!$parent){
					 break;
				 }
			}

		}

		if(!$catergory_path AND !$home){
			   $this->_db->setQuery("SELECT `path`, home FROM #__menu WHERE link LIKE 'index.php?option=com_virtuemart&view=virtuemart%' AND client_id='0' ORDER BY id",0,1);
			   $menu=$this->_db->loadObject();
			   if(!$menu){
			   		$this->_db->setQuery("SELECT `path`, home FROM #__menu WHERE link LIKE 'index.php?option=com_virtuemart&view=category%' AND client_id='0' ORDER BY id",0,1);
			   		$menu=$this->_db->loadObject();
			   }
			   $catergory_path=$menu->path;
			   if($menu->home){
					$catergory_path='';
					$home=true;
			   }
			   $parts=array();
			   $cat_id= $parrent_category_id;
			   for(;;){
				   if(!$cat_id){
					  break;
				   }
				   $this->_db->setQuery("SELECT slug FROM #__virtuemart_categories_".$this->config->sufix." as c
								  WHERE c.virtuemart_category_id = $cat_id");
				   $parts[]=$this->_db->loadResult();
				   $this->_db->setQuery("SELECT category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id = $cat_id");
				   $cat_id=$this->_db->loadResult();
			   }
			   $parts=array_reverse($parts);
			   $slug=implode("/",$parts);
			   while(strstr($slug,"/"."/")){
				  $slug=str_replace("/"."/",'/',$slug);
			   }
			   unset($parts);
			   if($catergory_path){
				   $catergory_path.='/'.$slug;
			   }
			   else{
				   $catergory_path=$slug;
			   }

		}


		if(!$catergory_path AND !$home){
			   $catergory_path="component/virtuemart";
			   $this->_db->setQuery("SELECT slug FROM #__virtuemart_categories_".$this->config->sufix." as c
								  WHERE c.virtuemart_category_id = $parrent_category_id");
			   $slug=$this->_db->loadResult();
			   $catergory_path.='/'.$slug;
		}

		$this->_db->setQuery("SELECT slug FROM #__virtuemart_products_".$this->config->sufix."
								  WHERE virtuemart_product_id = $virtuemart_product_id");
		$product_slug=$this->_db->loadResult();
		if($catergory_path){
			return $catergory_path.'/'.$product_slug.$this->sef_suffix;  ;
		}
		else{
			return $product_slug.$this->sef_suffix;
		}

	}


	function getImages($id) {
		$query = 'SELECT a.file_url FROM #__virtuemart_medias a JOIN #__virtuemart_product_medias b ON b.virtuemart_media_id = a.virtuemart_media_id WHERE a.published = 1 AND b.virtuemart_product_id = '.$id.' ORDER BY b.ordering, b.id LIMIT 10';
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		if ($rows) {
			foreach ($rows as $row) {
				$this->insert_tag("picture",$this->live_site.str_replace(' ', '%20', $row->file_url),4);
			}
		}
	}

	
	function yml_import(){
		 $lock = fopen(dirname(__FILE__).DS.'yml.run', 'w');
		 if (!flock($lock, LOCK_EX | LOCK_NB)){
			 header('HTTP/1.1 502 Gateway Time-out');
  		   jexit();
		 }
		 ob_start();
		 $this->start_time=time();
		 $this->check_abort=time()+10;
		 $max_execution_time=ini_get('max_execution_time');
		 $max_execution_time=$max_execution_time?$max_execution_time:300;
		 $this->timeout=time()+$max_execution_time-5;
		 $this->mem_total=$this->get_mem_total();

		 require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."config.php");
		 $VmConfig=VmConfig::loadConfig();
		 $VmConfig=$VmConfig->_params;

		 $this->brows = @$VmConfig['categorytemplate'];
		 $this->per_row = @$VmConfig['products_per_row'];
		 $this->flypage = @$VmConfig['productlayout'];


		 $this->reimport=JRequest::getVar('reimport', 'post', '', 'int');
		 if($this->reimport){
			 @$data=json_decode(file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'yml-log.txt'));
			 @$this->stat['pn']=(int)$data->pn;
			 @$this->stat['pu']=(int)$data->pu;
			 @$this->stat['cn']=(int)$data->cn;
			 @$this->stat['cu']=(int)$data->cu;
			 @$this->counter= (int)$data->cur_row;
			 $this->start_time=(int)time()-$data->time;
		 }
		 else{
			$this->counter=1;
			//$this->immport();
		 }
		 if($this->cron_yml){
			 $this->_db->setQuery("SELECT yml_import_path,params FROM #__excel2vm_yml WHERE id = 1");
			 $conf_data=$this->_db->loadObject();
			 $yml_import_path=$conf_data->yml_import_path;
			 @$this->import_config=json_decode($conf_data->params);
		 }
		 else{
			 $yml_import_path=JRequest::getVar('yml_import_path', 'post', '', 'string');
			 @$this->import_config=$_POST;
			 unset($this->import_config['reimport']);
			 unset($this->import_config['yml_import_path']);
			 JArrayHelper::toInteger($this->import_config);
			 $this->import_config=(object)$this->import_config;
			 $this->_db->setQuery("INSERT INTO #__excel2vm_yml SET id = 1, params = ".$this->_db->Quote(json_encode($this->import_config))." ON DUPLICATE KEY UPDATE params = ".$this->_db->Quote(json_encode($this->import_config))."");
			 $this->_db->Query();

		 }

		 if(!$yml_import_path)$this->print_answer("Не указан файл для импорта");

		 $xml=JFactory::getXML($yml_import_path);

		 if(!$xml){
			$xml_errors = libxml_get_errors();
			if(count($xml_errors)){
			  $xml_errors_list=array();
			  foreach($xml_errors as $key => $v){
				 $xml_errors_list[]=($key+1).") Строка - $v->line; Столбец - $v->column;  $v->message";
			  }
			  $xml_errors_list=implode("\n\r",$xml_errors_list);

			}
			$this->print_answer("Файл импорта не может быть прочитан т.к. содержит ошибки:\n\r".$xml_errors_list);
		 }




		 $this->_db->setQuery("INSERT INTO #__excel2vm_yml SET id = 1, yml_import_path = ".$this->_db->Quote($yml_import_path)." ON DUPLICATE KEY UPDATE yml_import_path = ".$this->_db->Quote($yml_import_path)."");
		 $this->_db->Query();

		 $this->check_currencies($xml->shop->currencies->currency);

		 if(!$this->reimport){
			 $this->categories($xml->shop->categories->category);
		 }

		 if(method_exists($xml->shop->offers->offer,"count")){
			  $this->numRow=$xml->shop->offers->offer->count();
		 }
		 else{
			 echo "<span style=\"color: #CC0000\">Импортируемый файл не соответствует формату YML</span>";
			 exit();
		 }



		 $this->products($xml->shop->offers->offer);
		 $this->end_import();
		 exit();
	}

	function check_currencies($currencies){
		$this->curr_preset=array();
		$this->curr_preset['RUB']=json_decode('{"currency_name":"Russian ruble","currency_code_3":"RUB","currency_numeric_code":643}');
		$this->curr_preset['RUR']=json_decode('{"currency_name":"Russian ruble","currency_code_3":"RUB","currency_numeric_code":643}');
		$this->curr_preset['BYR']=json_decode('{"currency_name":"Belarusian ruble","currency_code_3":"BYR","currency_numeric_code":974}');
		$this->curr_preset['KZT']=json_decode('{"currency_name":"Kazakhstani tenge","currency_code_3":"KZT","currency_numeric_code":398}');
		$this->curr_preset['UAH']=json_decode('{"currency_name":"Ukrainian hryvnia","currency_code_3":"UAH","currency_numeric_code":980}');
		$this->curr_preset['USD']=json_decode('{"currency_name":"United States dollar","currency_code_3":"USD","currency_numeric_code":840}');
		$this->curr_preset['EUR']=json_decode('{"currency_name":"Euro","currency_code_3":"EUR","currency_numeric_code":978}');

		if(!isset($currencies)){
			 return false;
		}

		foreach($currencies as $key => $c){
			$attr=$c->attributes();
			$id=(string)$attr->id;
			if(isset($this->curr_preset[$id])){
				$this->_db->setQuery("SELECT virtuemart_currency_id FROM #__virtuemart_currencies WHERE currency_code_3 = ".$this->_db->Quote($this->curr_preset[$id]->currency_code_3));
				$virtuemart_currency_id=$this->_db->loadResult();
				if(!$virtuemart_currency_id){
					$this->_db->setQuery("INSERT INTO #__virtuemart_currencies SET
					currency_code_3 = ".$this->_db->Quote($this->curr_preset[$id]->currency_code_3).",
					currency_name = ".$this->_db->Quote($this->curr_preset[$id]->currency_name).",
					currency_numeric_code = ".$this->_db->Quote($this->curr_preset[$id]->currency_numeric_code));
					$this->_db->Query();
					$virtuemart_currency_id=$this->_db->insertid();
				}
				$this->curr_preset[$id]->virtuemart_currency_id=$virtuemart_currency_id;
			}

		}
	}

	 function categories($categories){
		foreach($categories as $category){
		  $attr=$category->attributes();
		  $cat_id=(int)$attr['id'];
		  $parent_id=(int)$attr['parentId'];
		  $cat_name=(string)$category;
		  $alias=$this->translit($cat_name);
		  $this->_db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_categories WHERE virtuemart_category_id = $cat_id");
		  if(!$this->_db->loadResult()){
			 $this->_db->setQuery("INSERT INTO #__virtuemart_categories
							  SET virtuemart_category_id = $cat_id,
							  category_template='$this->brows',
							  products_per_row='$this->per_row',
							  category_product_layout='$this->flypage',
							  created_on = NOW()
							  ");
			 $this->_db->Query();
			 @$this->stat['cn']++;
		  }
		  else{
			 @$this->stat['cu']++;
		  }

		  try{
				$this->_db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_categories_".$this->config->sufix." WHERE virtuemart_category_id = $cat_id");
				$virtuemart_category_id=$this->_db->loadResult();
		  }
		  catch(Exception $e){
				$this->print_answer($this->_db->getErrorMsg());
		  }

		  if(!$virtuemart_category_id){
					try{
						$this->_db->setQuery("
							  INSERT INTO #__virtuemart_categories_".$this->config->sufix."
							  SET
							  virtuemart_category_id = $cat_id,
							  category_name = ".$this->_db->Quote($cat_name).",
							  slug = ".$this->_db->Quote($alias)."");
						$this->_db->Query();
					}
					catch(Exception $e){
					   if(strstr($this->_db->getErrorMsg(),"Duplicate entry")){
						   try{
							  $this->_db->setQuery("
							  INSERT INTO #__virtuemart_categories_".$this->config->sufix."
							  SET
							  virtuemart_category_id = $cat_id,
							  category_name = ".$this->_db->Quote($cat_name).",
							  slug = ".$this->_db->Quote($cat_id."_".$alias)."");
						   }
						   catch(Exception $e){
							  $this->print_answer($this->_db->getErrorMsg());
						   }

					   }
					   else{
						   $this->print_answer($this->_db->getErrorMsg());
					   }

					}
		  }
		  else{
				try{
					 $this->_db->setQuery("UPDATE #__virtuemart_categories_".$this->config->sufix." SET  category_name = ".$this->_db->Quote($cat_name)." WHERE virtuemart_category_id = $cat_id");
					 $this->_db->Query();
				}
				catch(Exception $e){
					  $this->print_answer($this->_db->getErrorMsg());
				}

		  }



		  $this->_db->setQuery("SELECT id FROM #__virtuemart_category_categories WHERE category_child_id = $cat_id");
		  if(!$this->_db->loadResult()){
			 $this->_db->setQuery("INSERT INTO #__virtuemart_category_categories SET category_parent_id = $parent_id, category_child_id = $cat_id");
			 $this->_db->Query();
		  }

		}
	}

	function products($products){


		for($this->counter;$this->counter<=$this->numRow;$this->counter++){
				 $p=$products[$this->counter-1];


				  $product_id=$this->getProductID($p,$new);
				  if(!$product_id)continue;

				  if($p->vendor){
					 $manufacturer_id= $this->getManufacturer((string)$p->vendor);
					 $this->assign_manufacturer($product_id,$manufacturer_id);
				  }

				  $this->assign_category($product_id,$p->categoryId,$new);
				  $this->assign_price($product_id,(float)$p->price,(string)$p->currencyId);
				  $this->assign_related($product_id,@explode(",",(string)@$p->rec));

				  if($this->import_config->images_mode==2 OR ($this->import_config->images_mode==1 AND $new)){
					 $this->assign_pictures($product_id,(array)$p->picture,$p);
				  }

				  $this->assign_params($product_id,$p->param);

				  $this->updateStat();

		}
	}

	function getManufacturer($mf_name){
		$this->_db->setQuery("SELECT virtuemart_manufacturer_id FROM #__virtuemart_manufacturers_".$this->config->sufix." WHERE mf_name=".$this->_db->Quote($mf_name));
		$virtuemart_manufacturer_id = $this->_db->loadResult();
		if(!$virtuemart_manufacturer_id){
			$this->_db->setQuery("INSERT INTO #__virtuemart_manufacturers SET virtuemart_manufacturer_id=NULL, published=1");
				$this->_db->Query();
				$virtuemart_manufacturer_id= $this->_db->insertid();


				$this->_db->setQuery("INSERT INTO #__virtuemart_manufacturers_".$this->config->sufix." SET mf_name=".$this->_db->Quote($mf_name).", slug = " . $this->_db->Quote($this->translit($mf_name)."_".$virtuemart_manufacturer_id).",virtuemart_manufacturer_id = '{$virtuemart_manufacturer_id}'");
				$this->_db->Query();
		}
		return $virtuemart_manufacturer_id;
	}

	function getProductID($p,&$new){

		$attr=$p->attributes();
		$product_id=(int)@$attr->id;
		$product_sku=(string)@$p->vendorCode;
		$product_name=(string)@$p->name;
		if(!$product_name){
			$product_name=(string)@$p->model;
		}
		@$this->current_product=$product_name?$product_name:$product_sku;
		$published= (string) @$attr->available;
		$published=($published=='true')?1:0;
		$full_desc=htmlspecialchars_decode((string)@$p->description);
		$product_weight=(float)@$p->weight;
		$dimensions=(string)@$p->dimensions;
		if($dimensions){
		   $dimensions=explode("/",$dimensions);
		   if(count($dimensions)==3){
			   $product_length=(float)@$dimensions[0];
			   $product_width=(float)@$dimensions[1];
			   $product_height=(float)@$dimensions[2];
		   }

		}

		switch($this->import_config->identity) {
	  	  case 0:
				if(!$product_id)return false;
				$new=$this->is_productId_new($product_id);
	  	  break;
	  	  case 1:
				if(!$product_sku)return false;
				$product_id=$this->get_productId_by_sku($product_sku);
				$new=!$product_id?true:false;
	  	  break;
	  	  case 2:
				if(!$product_name)return false;
				$product_id=$this->get_productId_by_name($product_name);
				$new=!$product_id?true:false;
	  	  break;
		  default: return false;

	  	}

		if($new AND !$this->import_config->is_create)return false;
		if(!$new AND !$this->import_config->is_update)return false;

		$product_id=$product_id?$product_id:NULL;
		if(!isset($this->config->product_in_stock_default)){
					$this->config->product_in_stock_default=10;
		}
		$this->_db->setQuery("
				INSERT INTO #__virtuemart_products
				SET
				virtuemart_product_id = '$product_id',
				product_sku=".$this->_db->Quote($product_sku).",
				product_weight=".$this->_db->Quote(@$product_weight).",
				product_length=".$this->_db->Quote(@$product_length).",
				product_width=".$this->_db->Quote(@$product_width).",
				product_height=".$this->_db->Quote(@$product_height).",
				product_in_stock=".$this->_db->Quote((int)$this->config->product_in_stock_default).",
				published=".$this->_db->Quote($published).",
				created_on = NOW()
				ON DUPLICATE KEY UPDATE
				product_sku=".$this->_db->Quote($product_sku).",
				product_weight=".(@$product_weight?$this->_db->Quote($product_weight):'product_weight').",
				product_length=".(@$product_length?$this->_db->Quote($product_length):'product_length').",
				product_width=".(@$product_width?$this->_db->Quote($product_width):'product_width').",
				product_height=".(@$product_height?$this->_db->Quote($product_height):'product_height').",
				published=".$this->_db->Quote($published).",
				modified_on = NOW() ");
				$this->_db->Query();
				$product_id=$product_id?$product_id:$this->_db->insertid();


		try{

				$alias=$this->getAlias($product_name,$product_id,$product_sku);

				$this->_db->setQuery("
				INSERT INTO #__virtuemart_products_".$this->config->sufix."
				SET
				virtuemart_product_id = '$product_id',
				product_name = ".$this->_db->Quote($product_name).",
				product_desc = ".$this->_db->Quote($full_desc).",
				slug = ".$this->_db->Quote($alias)."
				ON DUPLICATE KEY UPDATE
				product_name = ".$this->_db->Quote($product_name).",
				product_desc = ".($full_desc?$this->_db->Quote($full_desc):'product_desc')."
				");
				$this->_db->Query();

		}
		catch(Exception $e){
			   $this->print_answer($this->_db->getErrorMsg());
		}

		if($new){
			@$this->stat['pn']++;
			switch($this->import_config->identity) {
				  case 0:
					  $this->temp_productID_table[]=$product_id;
				  break;
				  case 1:
					  $this->temp_product_table[$product_id]=$product_sku;
				  break;
				  case 2:
					  $this->temp_product_table_by_name[$product_id]=$product_name;
				  break;


		   }
		}
		else{
			@$this->stat['pu']++;
		}
		return $product_id;
	}

	function is_productId_new($virtuemart_product_id){
	  if(!$virtuemart_product_id)return true;
	  if(!@$this->temp_productID_table){
				  $this->_db->setQuery("SELECT virtuemart_product_id
								  FROM #__virtuemart_products");
				  $this->temp_productID_table=$this->_db->loadColumn();
	  }
	  return !in_array($virtuemart_product_id,$this->temp_productID_table);
	}

	function get_productId_by_sku($sku){
	  if(!@$this->temp_product_table){
				  $this->_db->setQuery("SELECT virtuemart_product_id,product_sku
								  FROM #__virtuemart_products");
		 $this->temp_product_table=array_combine($this->_db->loadColumn(0),$this->_db->loadColumn(1));
			   }

	  return array_search($sku,$this->temp_product_table);
	}

	function get_productId_by_name($name){
	   if(!@$this->temp_product_table_by_name){
				  $this->_db->setQuery("SELECT virtuemart_product_id,product_name
								  FROM #__virtuemart_products_".$this->config->sufix);
		 $this->temp_product_table_by_name=array_combine($this->_db->loadColumn(0),$this->_db->loadColumn(1));
			   }
	  return array_search($name,$this->temp_product_table_by_name);
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

	function end_import(){
	   $this->last_upd-=2;
	   $this->counter--;
	   $this->updateStat();

	   $msg="Категорий создано: ".(int)@$this->stat['cn']."\n";
	   $msg.="Категорий обновлено: ".(int)@$this->stat['cu']."\n";
	   $msg.="Товаров создано: ".(int)@$this->stat['pn']."\n";
	   $msg.="Товаров обновлено: ".(int)@$this->stat['pu']."\n";
	   $this->print_answer($msg,true);
	}

	function assign_manufacturer($product_id,$manufacturer_id){
		$this->_db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id={$product_id}");
		if ($this->_db->loadResult()) {
			$this->_db->setQuery("UPDATE #__virtuemart_product_manufacturers SET virtuemart_manufacturer_id='{$manufacturer_id}' WHERE virtuemart_product_id={$product_id}");
			$this->_db->Query();
		}
		else {
			 $this->_db->setQuery("INSERT INTO #__virtuemart_product_manufacturers SET virtuemart_product_id={$product_id}, virtuemart_manufacturer_id='$manufacturer_id'");
			 $this->_db->Query();
		}
	}

	function assign_category($product_id,$category_ids,$new){
		if(!$this->config->change_category AND !$new){
		   return false;
		}
		$category_ids=(array)$category_ids;

		$this->_db->setQuery("SELECT id,ordering,virtuemart_category_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id = '{$product_id}' AND `virtuemart_category_id` IN(".implode(",",$category_ids).")");
		$product_categories_obj=$this->_db->loadObjectList('virtuemart_category_id');

		if (!$this->config->multicategories){
			$this->_db->setQuery("DELETE FROM #__virtuemart_product_categories WHERE virtuemart_product_id ='{$product_id}'");
			$this->_db->Query();
		}
		foreach($category_ids as $category_id){

			$ordering=isset($product_categories_obj[$category_id]->ordering)?$product_categories_obj[$category_id]->ordering:$this->counter;

			if(isset($product_categories_obj[$category_id]->id)){				$this->_db->setQuery("REPLACE INTO `#__virtuemart_product_categories` (`id`, `virtuemart_product_id`, `virtuemart_category_id`,`ordering`) VALUES ({$product_categories_obj[$category_id]->id}, '$product_id', '$category_id', '$ordering')");
				$this->_db->Query();
			}
			else{				$this->_db->setQuery("REPLACE INTO `#__virtuemart_product_categories` (`id`, `virtuemart_product_id`, `virtuemart_category_id`,`ordering`) VALUES (NULL, '$product_id', '$category_id', '$ordering')");
				$this->_db->Query();
			}
		}

	}

	function assign_price($product_id,$price,$currency){
		if(!$price){
		   return;
		}
		$price = $this->str2float($price);
		$virtuemart_currency_id=(int)@$this->curr_preset[$currency]->virtuemart_currency_id;
		if(!$virtuemart_currency_id){
			$this->_db->setQuery("SELECT virtuemart_currency_id FROM #__virtuemart_currencies WHERE currency_code_3 = ".$this->_db->Quote($this->curr_preset[$currency]->currency_code_3));
			$virtuemart_currency_id=$this->_db->loadResult();
			if(!$virtuemart_currency_id){
					$this->_db->setQuery("INSERT INTO #__virtuemart_currencies SET
					currency_code_3 = ".$this->_db->Quote($this->curr_preset[$currency]->currency_code_3).",
					currency_name = ".$this->_db->Quote($this->curr_preset[$currency]->currency_name).",
					currency_numeric_code = ".$this->_db->Quote($this->curr_preset[$currency]->currency_numeric_code));
					$this->_db->Query();
					$virtuemart_currency_id=$this->_db->insertid();
			}
			$this->curr_preset[$currency]->virtuemart_currency_id=$virtuemart_currency_id;
		}
		$this->_db->setQuery("
		SELECT virtuemart_product_price_id
		FROM #__virtuemart_product_prices
		WHERE virtuemart_product_id = {$product_id}
		AND price_quantity_start = 0
		AND price_quantity_end =0
		AND (virtuemart_shoppergroup_id = 0 OR virtuemart_shoppergroup_id IS NULL)");
		$virtuemart_product_price_id = $this->_db->loadResult();
		if($virtuemart_product_price_id){
			$this->_db->setQuery("UPDATE #__virtuemart_product_prices SET product_price = '$price', product_currency = '$virtuemart_currency_id' WHERE virtuemart_product_price_id = $virtuemart_product_price_id");
			$this->_db->Query();
		}
		else{
			$this->_db->setQuery("INSERT INTO #__virtuemart_product_prices SET product_price = '$price', product_currency = '$virtuemart_currency_id', virtuemart_product_id = $product_id");
			$this->_db->Query();
		}
	}

	function str2float($string){
	   $string = trim($string);
	   $float='';
	   for($i=0;$i<strlen($string);$i++){
	   	 if(ord($string[$i])==44 OR ord($string[$i])==46)$float.=".";
		 if((ord($string[$i])>=48 AND ord($string[$i])<=57) OR ord($string[$i])==45)$float.=$string[$i];
	   }
	   return (float)$float;
	}

	function assign_related($product_id,$related){
	   if(@empty($related[0])){
		  return false;
	   }
	   $this->_db->setQuery("DELETE FROM #__virtuemart_product_customfields WHERE  virtuemart_product_id = $product_id AND  virtuemart_custom_id = 1");
	   $this->_db->Query();

	   foreach($related as $key => $v){
		   if(!$v){
			   continue;
		   }
		   $this->_db->setQuery("INSERT INTO #__virtuemart_product_customfields SET  virtuemart_product_id = $product_id,  virtuemart_custom_id = 1, {$this->fieldname_custom_value} = '$v'");
		   $this->_db->Query();
	   }
	}

	function assign_pictures($product_id,$pictures,$p){
	   if(@empty($pictures[0])){
		  return false;
	   }

	   $product_sku=(string)@$p->vendorCode;
	   $product_name=(string)@$p->model;

	   $this->_db->setQuery("SELECT virtuemart_media_id FROM #__virtuemart_product_medias WHERE virtuemart_product_id = '$product_id'");
	   $old_virtuemart_media_id =$this->_db->loadColumn();
	   if(count($old_virtuemart_media_id)){
		   $this->_db->setQuery("DELETE FROM #__virtuemart_product_medias WHERE virtuemart_product_id = '$product_id'");
		   $this->_db->Query();

		   $this->_db->setQuery("DELETE FROM #__virtuemart_medias WHERE virtuemart_media_id IN('".implode("','",$old_virtuemart_media_id)."')");
		   $this->_db->Query();
	   }


	   foreach($pictures as $key => $v){
		   $file_url=$this->get_images_http($v,$product_id);
		   if(!$file_url){
			  continue;
		   }
		   $this->_db->setQuery("
		   INSERT INTO #__virtuemart_medias
		   SET
		   file_url = ".$this->_db->Quote($file_url).",
		   file_is_product_image = 1,
		   file_type='product',
		   file_mimetype = 'image/jpeg',
		   published = 1,
		   file_title = ".$this->_db->Quote($product_id."-".(stripslashes($product_name ? $this->translit($product_name) : $this->translit($product_sku)))."_".$key )."
		   ");
		   $this->_db->Query();

		   $virtuemart_media_id=$this->_db->insertid();
		   $this->_db->setQuery("
		   INSERT INTO #__virtuemart_product_medias
		   SET
		   virtuemart_media_id = $virtuemart_media_id,
		   virtuemart_product_id = '$product_id',
		   ordering='$key'
			");
			$this->_db->Query();

	   }

	}

	function get_images_http($file_url,$id){
		$file_url_ext=pathinfo($file_url, PATHINFO_EXTENSION);
		if(strstr($file_url_ext,"?")){
		   $file_url_ext=substr($file_url_ext,0,strpos($file_url_ext,"?"));
		}
		$extensions=array('jpg','jpeg','gif','png','bmp');

		if(in_array($file_url_ext,$extensions)){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, str_replace(' ', '%20', $file_url));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$file=curl_exec($ch);
			$error=curl_error($ch);
			$code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($code!=200){
				@$this->errors[]="Строка - $this->counter. Изображение $file_url не загружено. Код ответа - $code.".($file?" Ошибка - $file":"");
				return NULL;
			}
			if(!$file){
			   @$this->errors[]="Строка - $this->counter. Изображение $file_url не загружено.".($error?" Ошибка - $error":"");
			   return NULL;
			}
			if(!$file)return NULL;
			$temp_path=explode("/",$file_url);
			$file_name=strtolower(end($temp_path));
			if(strstr($file_name,"?")){
			   $file_name=substr($file_name,0,strpos($file_name,"?"));
			}
			$file_name=str_replace(".".$file_url_ext,"",$file_name);
			$file_name=$this->translit($file_name);
			$file_name=$id.'_'.$file_name.".".$file_url_ext;


			if(strstr($file_name,'.jpeg')){
				$file_name=str_replace('.jpeg','.jpg',$file_name);
			}
			$pos=strpos($file_name,'?');
			if($pos){
				$file_name=substr($file_name,0,$pos);
			}


			$path=$this->config->path;

			if(substr($path,-1)!="/"){
				 $path.="/";
			}
			$put_path=str_replace("/",DS,$path);


			file_put_contents(JPATH_ROOT.DS.$put_path.$file_name,$file);
			unset($file);
			return $path.$file_name;
		}else{
		  return NULL;
		}
	}

	function assign_params($product_id,$params){
		if(!$params){
		   return false;
		}
		$this->_db->setQuery("
		DELETE FROM #__virtuemart_product_customfields
		WHERE virtuemart_custom_id >2 AND virtuemart_product_id = {$product_id}");
		$this->_db->Query();
		foreach($params as $param){
			$param_attr=$param->attributes();
			$virtuemart_custom_id=$this->getCustomFieldID($param_attr->name);
			$value=(string)$param;
			if($param_attr->unit){
			   $value.=" ".$param_attr->unit;
			}
			$this->_db->setQuery("
			INSERT INTO #__virtuemart_product_customfields
			SET
			virtuemart_product_id = {$product_id},
			virtuemart_custom_id = $virtuemart_custom_id,
			{$this->fieldname_custom_value} = ".$this->_db->Quote($value)."
			");
			$this->_db->Query();
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

	function updateStat($not_interrupt=false) {
		if (time() - @$this->last_upd > 1) {
			$this->last_upd = time();
			@$data->cur_row=@$this->counter;
			$data->num_row=@$this->numRow;
			$data->pn=(int)@$this->stat['pn'];
			$data->pu=(int)@$this->stat['pu'];
			$data->cn=(int)@$this->stat['cn'];
			$data->cu=(int)@$this->stat['cu'];
			$data->time=time() - $this->start_time;
			$data->cur_time=time();

			$data->cur_prod=@$this->current_product;
			$data->mem=$this->get_mem();
			$data->mem_total=$this->mem_total;
			$data->mem_peak=$this->get_mem_peak();

			$data->timeout=0;
			if($this->check_abort<time() AND !$not_interrupt){
				 if(@file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'yml-abort.txt')){
					file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'yml-abort.txt',0);
				 	$this->end_import();
				 }

			}
			if(time()>=$this->timeout AND !$not_interrupt){
				$data->timeout=1;
				$data->cur_row++;
				if($this->cron_yml){
				   $max_execution_time=ini_get('max_execution_time');
				   $need=round($data->num_row/$data->cur_row*$max_execution_time);
				   $this->print_answer("Импорт остановлен из-за таймуата. Импортировано {$data->cur_row} строк из {$data->num_row}. Для завершения импорта в автоматическом режиме необходимо, чтобы значение max_execution_time было не меньше, чем $need сек.");
				}
				@$answer->status='timeout';
				if(@count($this->errors)){
					$answer->errors=implode("<br>",$this->errors);
				}
				else{
					$answer->errors='';
				}
				echo json_encode($answer);
			}
			file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'yml-log.txt', json_encode($data));
			if(time()>=$this->timeout AND !$not_interrupt){
				exit();
			}

		}
	}

	function updateExportStat($cur_row,$total_products,$is_end=0) {
		if (time() - @$this->last_upd > 1 OR $is_end) {
			$this->last_upd = time();
			@$data->cur_row=$cur_row;
			$data->num_row=$total_products;
			$data->start_time= $this->start_time;
			$data->time=time() - $this->start_time;
			$data->cur_time=time();
			$data->is_end=$is_end;
			file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'yml-export-log.txt', json_encode($data));
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
/*
	function immport() {
		$fail = JText::_('ERROR_LICENSE_DEWELOPER');
		$t = explode('/', JURI::root());
		$d = $t[2];
		if(substr($d,0,4)=='www.')$d=substr($d,4);

		@ $k = file_get_contents(dirname(__FILE__) . DS . 'key.txt');

		if(!strstr($d,'localhost')){
			$token=sha1('ho3tj4gut95liwfvngg9'.urlencode($d));
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://php-programmist.ru/license.php?token={$token}&domain=".urlencode($d)."&key=$k");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_REFERER, JURI::root());
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

	function substr($string,$len){
		if(strlen($string)>$len){
			$string=mb_substr($string,0,$len-5)."...";
		}

		return $string;

	}

	function getCurrencies(){
		$this->_db->setQuery("SELECT virtuemart_currency_id, currency_name FROM #__virtuemart_currencies ORDER BY virtuemart_currency_id");
		$currencies=$this->_db->loadObjectList('virtuemart_currency_id');
		array_unshift($currencies,JHTML::_('select.option',  '0', "Валюта по-умолчанию", 'virtuemart_currency_id', 'currency_name' ));
		return  $currencies;
	}

	function getCategoryList($selected_cat){
		 if(!file_exists(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."shopfunctions.php")){
			 JError::raiseError('',"Установите VirtueMart 2 - 3");
			  return false;

		}
		else{
			require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."config.php");
			require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."shopfunctions.php");
			$VmConfig=VmConfig::loadConfig();
			try{
				return ShopFunctions::categoryListTree((array)$selected_cat);
			}
			catch(Exception $e){
			  echo $e->getMessage();
			   return false;
			}

		}
	}

	function getGroups(){
		$lang = JFactory::getLanguage();
		$lang->load('com_virtuemart');

		$this->_db->setQuery("SELECT virtuemart_shoppergroup_id, shopper_group_name
		FROM #__virtuemart_shoppergroups WHERE published = '1' ORDER BY `default` DESC , virtuemart_shoppergroup_id ASC");
		$groups=$this->_db->loadObjectList();

		if(!$groups){
		   $this->_db->setQuery("SELECT shopper_group_id as virtuemart_shoppergroup_id, shopper_group_name FROM #__virtuemart_shoppergroups ORDER BY `default` DESC,shopper_group_id");
			$groups=$this->_db->loadObjectList();
		}

		foreach($groups as &$v){
			$v->shopper_group_name=JText::_($v->shopper_group_name);
		}

		return $groups;

	}

}