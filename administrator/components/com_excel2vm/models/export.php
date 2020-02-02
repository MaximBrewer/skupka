<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
ini_set('log_errors', 'On');
ini_set('error_log', JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'export_errors.txt');
require_once (dirname(__FILE__) . DS . "updateTable.php");

class Excel2vmModelExport extends JModelLegacy {
	public $pagination;

	function __construct($cron=false) {
		parent :: __construct();

		$params = JComponentHelper :: getParams("com_excel2vm");
		$debug=$params->get('db_debug',0);
		/*if($debug){
			require_once (dirname(__FILE__) . DS . "db_debug.php");
			$full_debug=JRequest::getVar('full_debug', 'cookie', 0, 'int');
			$this->_db=new JDatabaseMySQLbak($full_debug,$full_debug);
		}*/
		$this->_db->debug($debug);
		$this->browser_timeout=$params->get('timeout',60)?$params->get('timeout',60):60;
		$this->config_table = new updateTable("#__excel2vm", "id", 1);
		$this->core = new core($cron);
		$this->cron=$cron;
		$this->config =$this->core->getConfig();
		$this->active_fields =$this->core->active_fields;
		$this->item_id =$this->core->item_id;
		$this->active = $this->getActive();
		$this->part=JRequest::getVar('part', 0, '', 'int');
		$this->file_type=JRequest::getVar('csv', 0, '', 'int');
		$this->filename=JRequest::getVar('filename', 0, '', 'string');
		$this->csv=$this->file_type>0?0:1;
		$this->csv_field_delimiter = $params->get('csv_field_delimiter',';');
		$this->csv_row_delimiter = $params->get('csv_row_delimiter','');
		$this->csv_convert = $params->get('csv_convert',1);
		$this->price_label = $params->get('price_label',0);
		$this->custom_fields = $params->get('custom_fields',0);

		$this->export_query_size = (int)$params->get('export_query_size',1000);
		if(!$this->export_query_size)$this->export_query_size=1000;
		$this->row_limit=JRequest::getVar('row_limit', 0, '', 'int')-1;
		$this->children_export=JRequest::getVar('children_export', '', 0, 'int');
		$this->product_status=JRequest::getVar('product_status', -1, '', 'int');

		@$this->manufacturers=(array)$_POST['manufacturer_id'];
		JArrayHelper::toInteger($this->manufacturers);
		$this->manufacturers=implode(",",$this->manufacturers);

		$this->image_path=JRequest::getVar('image_path', 0, '', 'int');
		if(is_array(@$_REQUEST['category'])){
			if(count($_REQUEST['category'])==1){
				 $this->category=(int)$_REQUEST['category'][0];
			}
			elseif(count($_REQUEST['category'])>1){
				 $this->category=$_REQUEST['category'];
			}
			else{
			  $this->category=0;
			}
		}
		else{
			$this->category=(int)@$_REQUEST['category'];
		}


		$this->order=JRequest::getVar('order', 'category_child_id', '', 'string');
		$this->is_cherry=$this->is_cherry();
		$mainframe = JFactory::getApplication();
		 $this->sef=$mainframe->getCfg('sef');
		 $this->sef_rewrite=$mainframe->getCfg('sef_rewrite');
		 $this->sef_suffix=$mainframe->getCfg('sef_suffix')?'.html':'';
		 require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."config.php");
		 $VmConfig=VmConfig::loadConfig();
		 $VmConfig=$VmConfig->_params;

		 $vm_seo_sufix=$VmConfig['seo_sufix'];
		 $this->sef_suffix=$vm_seo_sufix.$this->sef_suffix;


		$this->export_directory_path=JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export';
		if($cron){
		   $this->export_directory_path=$params->get('export_directory_path');
		   $this->file_type=$params->get('csv',0);
		   $this->csv=$this->file_type>0?0:1;

		   $this->order=$params->get('order','category_child_id');
		   $this->product_status=$params->get('product_status',-1);
		   $this->image_path=$params->get('image_path',0);
		   $this->children_export=$params->get('children_export',0);
		   $this->category=$params->get('category',0);
		   $this->filename=$params->get('filename');


		}

			}

	function getPriceLabels(){
		 $labels_list[]=JHTML::_('select.option',  0, JText::_('ALL'), 'value', 'text' );
		 $this->_db->setQuery("SELECT COUNT(*) FROM information_schema.columns
	WHERE column_name = 'price_label' AND table_name = '".$this->_db->getPrefix()."virtuemart_products'");
		 if(!$this->_db->loadResult()){
			 $this->_db->setQuery("ALTER TABLE #__virtuemart_products ADD `price_label` varchar(256)");
			 $this->_db->Query();
		 }

		 $this->_db->setQuery("SELECT DISTINCT `price_label` FROM #__virtuemart_products WHERE `price_label` IS NOT NULL");
		 $labels=$this->_db->loadColumn();
		 if(count($labels)){
		   $labels_list[]=JHTML::_('select.option',  0, JText::_('ALL'), 'value', 'text' );
			foreach($labels as $l){

				$labels_list[]=JHTML::_('select.option',  $l, $l, 'value', 'text' );
			}
		 }
		 else{
			 $labels_list[]=JHTML::_('select.option',  0, JText::_('ALL'), 'value', 'text' );
		 }
		 return $labels_list;
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
	  $inputCookie->set('c_csv',@$_POST['csv'], time()+(365*24*3600));
	  $inputCookie->set('c_memory_limit',@$_POST['memory_limit'], time()+(365*24*3600));
	  $inputCookie->set('c_order',@$_POST['order'], time()+(365*24*3600));
	  $inputCookie->set('c_product_status',@$_POST['product_status'], time()+(365*24*3600));
	  $inputCookie->set('c_row_limit',@$_POST['row_limit'], time()+(365*24*3600));
	  $inputCookie->set('c_image_path',@$_POST['image_path'], time()+(365*24*3600));
	  $inputCookie->set('c_children_export',@$_POST['children_export'], time()+(365*24*3600));
	  $inputCookie->set('c_category',serialize(@$_POST['category']), time()+(365*24*3600));
	  $inputCookie->set('c_man',serialize(@$_POST['manufacturer_id']), time()+(365*24*3600));
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

	function getManufacturers(){
	  try{
		 $this->_db->setQuery("SELECT virtuemart_manufacturer_id, mf_name FROM #__virtuemart_manufacturers_".$this->config->sufix." ORDER BY `mf_name`");
		 return $this->_db->loadObjectList();
	  }
	  catch(Exception $e){
		 return false;
	  }
	}

	function getCategoryList($parent_id=0){
		if(!file_exists(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."shopfunctions.php")){
			 JError::raiseError('',"Установите VirtueMart 2 - 3");
			  return false;

		}
		else{
			require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."helpers".DS."shopfunctions.php");
			$selected_cat=@unserialize(urldecode(JRequest::getVar('c_category', 'cookie', '0', 'string')));
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

	function getCategoryChildrenById($parent_id=0){

		$this->_db->setQuery("SELECT category_child_id
								  FROM #__virtuemart_category_categories
								  WHERE category_parent_id ='$parent_id'
								  ORDER BY category_child_id");

		$categories=$this->_db->loadColumn();

		if(!$categories)
			return false;

		foreach($categories as $cat){
			$this->new_tree[]=$cat;
			$this->getCategoryChildrenById($cat);
		}
		return true;
	}

	function profile_list(){
		  $list=$this->_getList("SELECT id, profile FROM #__excel2vm ORDER BY id");
		  return $list;
	}

		function get_export_file(){
		$this->part++;
		if(!$this->csv){
			$mtime=filemtime(JPATH_BASE."/components/com_excel2vm/export/export".(date("Y_m_d"))."_part{$this->part}.xls");
			if(time()-$mtime <60){
				$resp->text="{$this->part}.".JText::_('DOWNLOAD_EXPORTED_DATA')." - ".JText::_('PART')." {$this->part}";
				$resp->link=JURI::base()."/components/com_excel2vm/export/export".(date("Y_m_d"))."_part{$this->part}.xls";
				$resp->finish=file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS."finish.txt");
			}
			else{
				$resp->text='No';
				$resp->finish=0;
			}
			echo json_encode($resp);
			exit();
		}
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

	function shift($array){
		$i=1;
		foreach($array as $val){
			$array2[$i]=$val;
			$i++;
		}
		return $array2;
	}

	function getChildren($obj){

			 $this->_db->setQuery("SELECT cc.category_child_id, category_name,file_url,file_url_thumb,slug,category_description as product_desc,vc.virtuemart_vendor_id,vc.published,vc.category_product_layout,vc.category_layout,vc.category_template,vc.ordering,c.customtitle,c.metakey,c.metadesc
							  FROM #__virtuemart_category_categories as cc
							  LEFT JOIN  #__virtuemart_categories_".$this->config->sufix." as c ON c.virtuemart_category_id = cc.category_child_id
							  LEFT JOIN  #__virtuemart_categories as vc ON vc.virtuemart_category_id = cc.category_child_id
							  LEFT JOIN  #__virtuemart_category_medias as cm ON cc.category_child_id = cm.virtuemart_category_id
							  LEFT JOIN  #__virtuemart_medias as m ON m.virtuemart_media_id = cm.virtuemart_media_id
							  WHERE cc.category_parent_id=$obj->category_child_id
							  AND category_name IS NOT NULL
							  AND category_name !=''
							  GROUP BY cc.category_child_id
							  ORDER BY $this->order");


		$result=$this->_db->loadObjectList();
		$this->new_tree[]=$obj;
		if(count($result)){
			$result=$this->shift($result);
			foreach($result  as $key => $child ){
				switch ($this->config->price_template) {
				  case 1:
					   $child->path=$obj->path.$key.'.';
				  break;
				  case 2:
					   $child->path=$obj->path.$this->config->simbol;
				  break;
				  case 3:
					   $child->path=$obj->path.$this->config->simbol;
				  break;
				  case 4:
					   $child->path=$obj->path.'.'.$key;
				  break;
				}
				$child->level=$obj->level+1;
				$this->getChildren($child);
			}
		}

		return $obj;

	}
	function export(){

				$lock = fopen(dirname(__FILE__).DS.'lock2.run', 'w');
		if (!flock($lock, LOCK_EX | LOCK_NB)){
		   header('HTTP/1.1 502 Gateway Time-out');
		   jexit();
		}
		if(!$this->part){
			if(file_exists(JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'export_errors.txt')){
				if(filesize(JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'export_errors.txt')>2*1024*1024){
					file_put_contents(JPATH_ROOT.DS.'components'.DS.'com_excel2vm'.DS.'export_errors.txt','');
			  }
			}

		}
		if(!$this->csv AND $this->file_type==2){
			$this->new_lib=true;
		}
		else{
			$this->new_lib=false;
		}

				$this->_db->setQuery("SELECT virtuemart_custom_id FROM #__virtuemart_customs WHERE field_type = 'R' ORDER BY virtuemart_custom_id",0,1);
		$this->related_custom_id=$this->_db->loadResult();

				$this->_db->setQuery("SELECT name,extra_id
				FROM #__excel2vm_fields
				WHERE id IN({$this->active_fields}) AND type='multi'
				");
		$this->is_multi=$this->_db->loadObjectList();
		$uniq_multy=array();
		if($this->is_multi){
			foreach($this->is_multi as $m){
				$temp=json_decode($m->extra_id);
				@$multi[$m->name]->id=$temp->id;
				@$multi[$m->name]->type=$temp->type;
				@$multi[$m->name]->clabel=$temp->clabel;
				$uniq_multy[]=$temp->id;
				unset($temp);
			}
			$this->uniq_multy=array_unique($uniq_multy);
			$this->is_multi=$multi;
			unset($multi);
			unset($uniq_multy);
		}

		//$this->check();
		file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS."finish.txt",0);
		$this->setCookies();
				$max_execution_time=ini_get('max_execution_time');
		if($max_execution_time==0){
			$max_execution_time=40;
		}
		elseif($max_execution_time>60){
			 $max_execution_time-=30;
		}
		else{
			$max_execution_time=$max_execution_time/2;
		}
		$this->timeout=time()+$max_execution_time;

		$component = JComponentHelper::getComponent( 'com_excel2vm' );
		$params = JComponentHelper :: getParams("com_excel2vm");

		$this->mem_limit= substr(ini_get('memory_limit'),0,-1)*1024*1024 * JRequest::getVar('memory_limit', 0.7, '', 'float');
				if(ini_get('memory_limit')==-1)$this->mem_limit=120*1024*1024;
		$this->start_time = $this->last_upd = time();

		$xml=JFactory::getXML(JPATH_ADMINISTRATOR .DS.'components'.DS.'com_virtuemart'.DS.'virtuemart.xml');
		$this->vm_version=(string)$xml->version;
		$this->is_vm_version_3=(substr($this->vm_version,0,1)==3 OR substr($this->vm_version,0,3)=='2.9')?true:false;
		$this->fieldname_custom_value=$this->is_vm_version_3?'customfield_value':'custom_value';
		$this->fieldname_custom_price=$this->is_vm_version_3?'customfield_price':'custom_price';


				$this->_db->setQuery("
			SELECT virtuemart_custom_id, field_type,custom_value,custom_element,custom_params
			FROM #__virtuemart_customs");
		$this->extra=$this->_db->loadObjectList('virtuemart_custom_id');


		if(!$this->csv){
						if(!$this->new_lib){
				require_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'libraries' . DS . 'PHPExcel.php');				require_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'libraries' . DS . 'PHPExcel' . DS . 'IOFactory.php');
												$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_in_memory_serialized;	 			$cacheSettings = array( 'memoryCacheSize'  => '8MB');
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

				$this->objPHPExcel = new PHPExcel();
				$this->objPHPExcel->setActiveSheetIndex(0);
				$this->getActiveSheet=$this->objPHPExcel->getActiveSheet();
				$this->getActiveSheet->setShowSummaryBelow(false);
			}
			else{
				require_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm' . DS . 'libraries' . DS . 'xlsx.php');
				$this->xlsxWriter=new XLSXWriter();
				$this->xlsxWriter->setAuthor('Excel2VM');
				$this->xlsxWriter->writeSheetHeader('Sheet1',array(),true);
			}

		}
		else{
			$price_label=($this->price_label AND $_REQUEST['price_label'])?$_REQUEST['price_label']:"";
			if($price_label){
				$price_label=str_replace(".".pathinfo($price_label, PATHINFO_EXTENSION),"",$price_label);
			}
			$file_name=$price_label?$price_label:"export".(date("Y-m-d_H:i:s"));
			if(!$this->part){
				file_put_contents($this->export_directory_path.DS.$file_name.'.csv','');
			}

			$this->csv_file=fopen($this->export_directory_path.DS.$file_name.'.csv','a');
		}

		$this->row=1;

		if(in_array($this->config->price_template,array(1,2,3,4,8))){			if(!$this->part){ 				
				

				$where="cc.category_parent_id=0";

				$this->_db->setQuery("SELECT cc.category_child_id, category_name,file_url,file_url_thumb,slug,category_description as product_desc,vc.virtuemart_vendor_id,vc.published,vc.category_product_layout,vc.category_layout,vc.category_template,vc.ordering,c.customtitle,c.metakey,c.metadesc
									  FROM #__virtuemart_category_categories as cc
									  LEFT JOIN  #__virtuemart_categories_".$this->config->sufix." as c ON c.virtuemart_category_id = cc.category_child_id
									  LEFT JOIN  #__virtuemart_categories as vc ON vc.virtuemart_category_id = cc.category_child_id
									  LEFT JOIN  #__virtuemart_category_medias as cm ON cc.category_child_id = cm.virtuemart_category_id
								  	  LEFT JOIN  #__virtuemart_medias as m ON m.virtuemart_media_id = cm.virtuemart_media_id
									  WHERE $where AND category_name IS NOT NULL
									  GROUP BY cc.category_child_id
									  ORDER BY $this->order");

				$tree=$this->_db->loadObjectList();
				$tree = $this->shift($tree);


				foreach($tree as $key => $obj  ){
				   $obj->level=0;
				   switch ($this->config->price_template) {
					  case 1:
						   $obj->path=$key.'.';
					  break;
					  case 2:
						   $obj->path='';
					  break;
					  case 3:
						   $obj->path='';
					  break;
					  case 4:
						   $obj->path=$key;
					  break;
					}
				   $this->getChildren($obj);
				}
				$this->print_headers();

				@$this->log->cat=0;
				$this->log->product=0;
				$this->log->start_time=time();
				$this->log->status=JText::_('COLLECTING_DATA');
				$this->log->currant_index=0;
			}
			else{								$this->new_tree = unserialize(file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS.'category_bak.txt'));
								$this->log = unserialize(file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS.'log_bak.txt'));
				$this->log->status=JText::_('COLLECTING_DATA');
				$this->print_new_headers();
								
			}

						if($this->category){
				$parent_categories_ids=array();
				$this->_db->setQuery("SELECT category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id = '$this->category'");
				$category_parent_id=$this->_db->loadResult();
				while($category_parent_id>0){
					$parent_categories_ids[]=$category_parent_id;
					$this->_db->setQuery("SELECT category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id = '$category_parent_id'");
					$category_parent_id=$this->_db->loadResult();
				}
			}
			$num_categories=count($this->new_tree);
			for($this->log->currant_index;$this->log->currant_index<$num_categories;$this->log->currant_index++){
							$category=$this->new_tree[$this->log->currant_index];
			   			   			   if($this->category){
				   if(in_array($category->category_child_id,$parent_categories_ids)){
					   $this->exportCategory($category);
					   continue;
				   }
				   elseif($category->category_child_id==$this->category){
					   $this->log->currant_path=@$category->path;
				   }
				   elseif(isset($this->log->currant_path)){
					   $path_len=strlen($this->log->currant_path);
					   if(substr($category->path,0,$path_len)!=$this->log->currant_path)
					   		continue;
				   }
				   else{
					   continue;
				   }
			   }
			   $this->exportCategory($category);
			   			   $start=@$this->log->currant_product_index[$this->log->currant_index]?$this->log->currant_product_index[$this->log->currant_index]:0;
			   $products=$this->getProductsByCategory($category->category_child_id,$start);
							  $total_products=$this->products_total[$category->category_child_id];

			   if($total_products > $start+$this->export_query_size){
				   $this->exportProducts($products);
				   $start+=$this->export_query_size;
				   while($start < $total_products){
					   $products=$this->getProductsByCategory($category->category_child_id,$start);
					   $this->exportProducts($products);
					   $start+=$this->export_query_size;
				   }
			   }
			   else{
				   if(!$products)continue;
				   else $this->exportProducts($products);
			   }

			}
		}
		elseif(in_array($this->config->price_template,array(6,7))){
			if(!$this->part){ 				$this->print_headers();
				@$this->log->cat=0;
				$this->log->product=0;
				$this->log->start_time=time();
				$start=0;

			}
			else{				$this->row--;
				$this->log = unserialize(file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS.'log_bak.txt'));
				$start=@$this->log->currant_product_index;

			}
			$this->log->status=JText::_('COLLECTING_DATA');

			for(;;){

				 $products=$this->getProducts($start);

				 if(!count($products)){
					break;
				 }
				 $this->exportProducts($products);
				 $start+=$this->export_query_size;
				 $this->updateStat();
			}
		}


		$price_label=($this->price_label AND $_REQUEST['price_label'])?$_REQUEST['price_label']:"";
		if($price_label){
			$price_label=str_replace(".".pathinfo($price_label, PATHINFO_EXTENSION),"",$price_label);
		}
		if(@$this->filename){
			$file_name_base=$this->filename;
		}
		else{
			$file_name_base= "export".(date("Y-m-d_H:i:s"));
		}


		$this->part++;
		if(!$this->csv){
			$file_name=$price_label?$price_label:$file_name_base;
			$this->log->status=JText::_('CREATING_EXCEL_FILE');
			$this->last_upd-=2;
			$this->updateStat();

			if($this->file_type == 1){
				$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5');
				$file_extension=".xls";
				$objWriter->setPreCalculateFormulas(false);
			}
			else{
				$file_extension=".xlsx";
			}

			if($this->part>1){

				 $file_name.= "_part{$this->part}";
			}

			if(!$this->new_lib){
				 $objWriter->save($this->export_directory_path.DS.$file_name.$file_extension);
			}
			else{
				$this->xlsxWriter->writeToFile($this->export_directory_path.DS.$file_name.$file_extension);
			}



				@$resp->text="{$this->part}.".JText::_('DOWNLOAD_EXPORTED_DATA')." ($this->row ".JText::_('ROWS').")".($this->part>1?" - ".JText::_('PART')." {$this->part}":"");
				$resp->link=JURI::base()."/components/com_excel2vm/export/".$file_name.$file_extension;
				$resp->finish=1;
				$resp->filename=$file_name_base;
				$this->log->status=JText::_('EXPORT_FINISHED');
				$this->last_upd-=2;
				$this->updateStat();
				file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS."finish.txt",1);
				echo json_encode($resp);


		}
		else{
			$temp=array(1,2);
			$this->print_csv($temp,true);
			fclose($this->csv_file);
			@$resp->text=JText::_('DOWNLOAD_EXPORTED_DATA');
			$resp->link=JURI::base()."/components/com_excel2vm/export/".$file_name.".csv";
			$resp->finish=1;
			echo json_encode($resp);
		}




		$this->last_upd-=2;
		$this->updateStat();
		if(!$this->cron){
			jexit();
		}
		else{
			$this->cron_log("Экспортировано строк - {$this->row}. ОП - ".$this->get_mem_total());
		}

	}

	function getProducts($start=0){
		if(isset($this->active['virtuemart_manufacturer_id']) OR isset($this->active['mf_name']) OR $this->manufacturers){
			$mf_fields='mf_name,man.virtuemart_manufacturer_id,';
			$mf_tables="LEFT JOIN #__virtuemart_product_manufacturers as pm ON p.virtuemart_product_id = pm.virtuemart_product_id
						LEFT JOIN #__virtuemart_manufacturers_".$this->config->sufix." as man ON pm.virtuemart_manufacturer_id = man.virtuemart_manufacturer_id";
		}
		else{
			$mf_fields='';
			$mf_tables="";
		}

		if(isset($this->active['currency'])){
			$currency_fields='currency_code_3 as product_currency,';
			$currency_tables="LEFT JOIN #__virtuemart_currencies as c ON c.virtuemart_currency_id = pr.product_currency";
		}
		else{
			$currency_fields='';
			$currency_tables="";
		}

		$where='';
		if($this->category){
			if(is_array($this->category)){
				 $where="AND pc.virtuemart_category_id IN (".implode(",",$this->category).")";
			}
			else{
				$where="AND pc.virtuemart_category_id IN (".$this->_db->Quote($this->category).")";
			}
		}

		$where2=$this->manufacturers?" AND pm.virtuemart_manufacturer_id IN ($this->manufacturers)":"";

		$having=$this->product_status>-1?" HAVING p.published = $this->product_status":"";

		$price_label=($this->price_label AND $_REQUEST['price_label'])?" AND p.price_label = ".$this->_db->Quote($this->_db->escape($_REQUEST['price_label'])):"";

		$this->_db->setQuery("SELECT COUNT(virtuemart_product_id) FROM #__virtuemart_products");
		$total_products=$this->_db->loadResult();

		$order=$total_products>10000?"":"ORDER BY pc.virtuemart_category_id,pc.ordering";

		$this->_db->setQuery("SELECT p.*,pl.*,{$mf_fields} {$currency_fields} product_price,product_override_price,pc.virtuemart_category_id,product_tax_id,product_discount_id,GROUP_CONCAT(pc.virtuemart_category_id SEPARATOR ',') as path,pc.ordering
		   						 FROM #__virtuemart_products as p
								 LEFT JOIN #__virtuemart_products_".$this->config->sufix." as pl ON p.virtuemart_product_id = pl.virtuemart_product_id
								 LEFT JOIN #__virtuemart_product_categories as pc ON p.virtuemart_product_id = pc.virtuemart_product_id
								 $mf_tables
								 LEFT JOIN #__virtuemart_product_prices as pr ON pr.virtuemart_product_id = p.virtuemart_product_id AND (virtuemart_shoppergroup_id = 0 OR virtuemart_shoppergroup_id IS NULL) AND (price_quantity_end = 0 OR price_quantity_end IS NULL) AND (price_quantity_start = 0 OR price_quantity_start IS NULL)
								 $currency_tables

								 WHERE product_parent_id = 0
								 $where
								 $price_label
								 $where2
								 GROUP BY p.virtuemart_product_id
								 $having
								 $order",$start,$this->export_query_size);
				$result=$this->_db->loadObjectList();
		if(!$this->children_export){
		   return $result;
		}
		foreach($result as $product){
				  if($this->is_multi){					   $this->_db->setQuery("
					   SELECT p.virtuemart_custom_id, customfield_params
					   FROM #__virtuemart_product_customfields as p
					   LEFT JOIN #__virtuemart_customs as c USING(virtuemart_custom_id)
					   WHERE field_type = 'C' AND virtuemart_product_id = '$product->virtuemart_product_id' AND  p.virtuemart_custom_id IN (".implode(",",$this->uniq_multy).")");
					   $multivariant_data=$this->_db->loadObjectList('virtuemart_custom_id');

					   if($multivariant_data){
						   foreach($multivariant_data as $mvd){
								$temp=explode("|",$mvd->customfield_params);
								foreach($temp as $tv){
									 if(substr($tv,0,strlen("selectoptions="))=="selectoptions="){
										 $selectoptions=json_decode(str_replace("selectoptions=","",$tv));
										 continue;
									 }
									 if(substr($tv,0,strlen("options="))=="options="){
										 $options=json_decode(str_replace("options=","",$tv));
										 continue;
									 }
								}



								$product_variant_data=array();
								$field_var_array=array();
								foreach($selectoptions as $key => $so){
									 foreach($this->is_multi as $field_name=> $m){
										if($m->type == $so->voption AND $m->clabel == $so->clabel){
											$field_var_array[$key]=$field_name;
										}
									 }
								}

								foreach($options as $product_id =>$o){
									$product_variant_data[$product_id]=array_combine($field_var_array,$o);
								}

						   }

													  if(isset($product_variant_data[$product->virtuemart_product_id])){
								foreach($product_variant_data[$product->virtuemart_product_id] as $property => $value){
									$product->$property=$value;
								}
						   }
					   }
				  }


				  $result2[]=$product;
				  if(!$product->virtuemart_product_id)continue;
				  $this->_db->setQuery("SELECT p.*,pl.*,{$mf_fields} {$currency_fields} product_price,product_override_price,pc.virtuemart_category_id,product_tax_id,product_discount_id,GROUP_CONCAT(pc.virtuemart_category_id SEPARATOR ',') as path,pc.ordering
		   						 FROM #__virtuemart_products as p
								 LEFT JOIN #__virtuemart_products_".$this->config->sufix." as pl ON p.virtuemart_product_id = pl.virtuemart_product_id
								 LEFT JOIN #__virtuemart_product_categories as pc ON p.virtuemart_product_id = pc.virtuemart_product_id
								 $mf_tables
								 LEFT JOIN #__virtuemart_product_prices as pr ON pr.virtuemart_product_id = p.virtuemart_product_id AND (virtuemart_shoppergroup_id = 0 OR virtuemart_shoppergroup_id IS NULL) AND (price_quantity_end = 0 OR price_quantity_end IS NULL) AND (price_quantity_start = 0 OR price_quantity_start IS NULL)
								 $currency_tables

								 WHERE product_parent_id = '$product->virtuemart_product_id' $price_label

								 GROUP BY p.virtuemart_product_id
								 $having
								 ");
				  $children=$this->_db->loadObjectList();

				  if($children){

				  	  foreach($children as $key => $child){
				  	  	 if(empty($child))continue;
						 if(!$child->product_sku)
						 	$child->product_sku=$product->product_sku."_".$key;
						 $child->parent_sku=$product->product_sku;

						 if($this->is_multi){
														if(isset($product_variant_data[$child->virtuemart_product_id])){
								foreach($product_variant_data[$child->virtuemart_product_id] as $property => $value){
									$child->$property=$value;
								}
						   }
						 }


						 $result2[]=$child;
				  	  }

				  }

		}

		unset($result);



		return @$result2;
	}

	function exportCategory($category){

		$this->log->current_cat=$category->category_name;
		   $this->log->cat++;
		   $this->log->row++;

		   $cells=array_fill(1,count($this->active),'');

		   if(isset($this->active['file_url'])){
		   	   $cells[$this->active['file_url']->ordering]=$this->imagePath('category',$category,false);
		   }
		   if(isset($this->active['file_url_thumb'])){
			   $cells[$this->active['file_url_thumb']->ordering]=$this->imagePath('category',$category,true);
		   }

		   if(isset($this->active['category_template'])){
			   $cells[$this->active['category_template']->ordering]=$category->category_template;
		   }

		   if(isset($this->active['category_layout'])){
			   $cells[$this->active['category_layout']->ordering]=$category->category_layout;
		   }

		   if(isset($this->active['category_product_layout'])){
			   $cells[$this->active['category_product_layout']->ordering]=$category->category_product_layout;
		   }
		   if(isset($this->active['ordering'])){
			   $cells[$this->active['ordering']->ordering]=$category->ordering;
		   }
		   if(isset($this->active['metadesc'])){
			   $cells[$this->active['metadesc']->ordering]=$category->metadesc;
		   }
		   if(isset($this->active['metakey'])){
			   $cells[$this->active['metakey']->ordering]=$category->metakey;
		   }
		   if(isset($this->active['customtitle'])){
			   $cells[$this->active['customtitle']->ordering]=$category->customtitle;
		   }
		   if(@$category->slug AND isset($this->active['slug']))$cells[$this->active['slug']->ordering]=$category->slug;
		   if(@$category->virtuemart_vendor_id AND isset($this->active['virtuemart_vendor_id']))$cells[$this->active['virtuemart_vendor_id']->ordering]=$category->virtuemart_vendor_id;
		   if(@$category->product_desc AND isset($this->active['product_desc']))$cells[$this->active['product_desc']->ordering]=$category->product_desc;
		   if(isset($this->active['published']))$cells[$this->active['published']->ordering]=$category->published;


		   switch ($this->config->price_template) {
			  case 1:
				   $cells[$this->config->cat_col]= $category->path.$category->category_name;
			  break;
			  case 2:
				   $cells[$this->config->cat_col]= $category->path.$category->category_name;
			  break;
			  case 3:
				   $cells[$this->config->cat_col]= $category->category_name.$category->path;
			  break;
			  case 4:

				   if(isset($this->active['path'])){
					   $cells[$this->config->cat_col]= $category->category_name;
					   $cells[$this->active['path']->ordering]= $category->path;
				   }
				   else{
					   $cells[$this->config->cat_col]= $category->path.$category->category_name;
				   }
			  break;
			  case 5:
				   echo JText::_('WRONG_METHOD');
				   exit();
			  break;
			  case 8:
					$cells[$this->config->cat_col]= $category->category_name;
			  break;
			}
		   $this->row++;

		   if(!$this->csv){
			   if(!$this->new_lib){
				   foreach($cells as $col => $value ){
					   if(empty($col))continue;
					   $cell_name=$this->getNameFromNumber($col-1).$this->row;

				   		   				   if($this->config->price_template==8){
						   $this->getActiveSheet->getRowDimension($this->row)->setOutlineLevel($category->level);
						   @$this->stat->currant_level=$category->level;
					   }

					   @$this->getActiveSheet->getCellByColumnAndRow($col-1,$this->row)->setValueExplicit($value,PHPExcel_Cell_DataType::TYPE_STRING);
					   if($this->config->cat_col==$col)
					   		$this->getActiveSheet->getStyle($cell_name)->getFont()->setBold(true);
				   }
			   }
			   else{
				   if($this->config->price_template==8){
						   $this->xlsxWriter->writeSheetRow('Sheet1', $cells,$category->level);
						   @$this->stat->currant_level=$category->level;
				   }
				   else{

					   $this->xlsxWriter->writeSheetRow('Sheet1', $cells);
				   }

			   }
		   }
		   else
		   		$this->print_csv($cells);

	}

	function getProductsByCategory($category_child_id,$start=0){
		$where2=$this->product_status>-1?" HAVING p.published = $this->product_status":"";
		$where3=$this->manufacturers?" AND pm.virtuemart_manufacturer_id IN ($this->manufacturers)":"";

											 /*if(@$this->config->price_quantity_start OR @$this->config->price_quantity_end){
					 if((int)$this->config->price_quantity_start==0){
						 $where[]="((price_quantity_start = 0 OR price_quantity_start IS NULL) OR price_quantity_end = '{$this->config->price_quantity_end}')";
					 }
					 elseif((int)$this->config->price_quantity_end==0){
						 $where[]="((price_quantity_end = 0 OR price_quantity_end IS NULL) OR price_quantity_start = '{$this->config->price_quantity_start}')";
					 }

			   }*/

							  /*$this->_db->setQuery("SELECT COUNT(virtuemart_product_id) FROM #__virtuemart_products");
			   $total_products=$this->_db->loadResult();
			   $order=$total_products>10000?"":"ORDER BY pc.ordering";*/
			   $order="ORDER BY pc.ordering";

			   $where="(pr.virtuemart_shoppergroup_id IS NULL OR pr.virtuemart_shoppergroup_id = 0)";
			   $price_label=($this->price_label AND $_REQUEST['price_label'])?" AND p.price_label = ".$this->_db->Quote($this->_db->escape($_REQUEST['price_label'])):"";

			   $this->_db->setQuery("SELECT SQL_CALC_FOUND_ROWS p.*,pl.*,mf_name,man.virtuemart_manufacturer_id,product_price,currency_code_3 as product_currency,product_override_price,pc.virtuemart_category_id,product_tax_id,product_discount_id,pc.ordering
		   						 FROM #__virtuemart_products as p
								 LEFT JOIN #__virtuemart_products_".$this->config->sufix." as pl ON p.virtuemart_product_id = pl.virtuemart_product_id
								 LEFT JOIN #__virtuemart_product_categories as pc ON p.virtuemart_product_id = pc.virtuemart_product_id
								 LEFT JOIN #__virtuemart_product_manufacturers as pm ON p.virtuemart_product_id = pm.virtuemart_product_id
								 LEFT JOIN #__virtuemart_manufacturers_".$this->config->sufix." as man ON pm.virtuemart_manufacturer_id = man.virtuemart_manufacturer_id
								 LEFT JOIN #__virtuemart_product_prices as pr ON pr.virtuemart_product_id = p.virtuemart_product_id AND (virtuemart_shoppergroup_id = 0 OR virtuemart_shoppergroup_id IS NULL) AND (price_quantity_end = 0 OR price_quantity_end IS NULL) AND (price_quantity_start = 0 OR price_quantity_start IS NULL)
								 LEFT JOIN #__virtuemart_currencies as c ON c.virtuemart_currency_id = pr.product_currency

		   						 WHERE pc.virtuemart_category_id = $category_child_id
								 $where3
								 AND product_parent_id = 0
								 AND $where  $price_label

								 GROUP BY p.virtuemart_product_id  $where2
								 $order",$start,$this->export_query_size);



					  $result=$this->_db->loadObjectList();

		   $this->_db->setQuery("SELECT FOUND_ROWS()");

		   $this->products_total[$category_child_id]=$this->_db->loadResult();
					  if(!$this->children_export){
			   return $result;
		   }
		   	   foreach($result as $product){
				  if($this->is_multi){					   $this->_db->setQuery("
					   SELECT p.virtuemart_custom_id, customfield_params
					   FROM #__virtuemart_product_customfields as p
					   LEFT JOIN #__virtuemart_customs as c USING(virtuemart_custom_id)
					   WHERE field_type = 'C' AND virtuemart_product_id = '$product->virtuemart_product_id' AND  p.virtuemart_custom_id IN (".implode(",",$this->uniq_multy).")");
					   $multivariant_data=$this->_db->loadObjectList('virtuemart_custom_id');

					   if($multivariant_data){
						   foreach($multivariant_data as $mvd){
								$temp=explode("|",$mvd->customfield_params);
								foreach($temp as $tv){
									 if(substr($tv,0,strlen("selectoptions="))=="selectoptions="){
										 $selectoptions=json_decode(str_replace("selectoptions=","",$tv));
										 continue;
									 }
									 if(substr($tv,0,strlen("options="))=="options="){
										 $options=json_decode(str_replace("options=","",$tv));
										 continue;
									 }
								}

								$product_variant_data=array();
								$field_var_array=array();
								foreach($selectoptions as $key => $so){
									 foreach($this->is_multi as $field_name=> $m){
										if($m->type == $so->voption AND $m->clabel == $so->clabel){
											$field_var_array[$key]=$field_name;
										}
									 }
								}

								foreach($options as $product_id =>$o){

									$product_variant_data[$product_id]=array_combine($field_var_array,$o);
								}

						   }

													  if(isset($product_variant_data[$product->virtuemart_product_id])){
								foreach($product_variant_data[$product->virtuemart_product_id] as $property => $value){
									$product->$property=$value;
								}
						   }
					   }
				  }


				  $result2[]=$product;
				  if(!$product->virtuemart_product_id)continue;

				  $this->_db->setQuery("SELECT p.*,pl.*,product_sku,mf_name,man.virtuemart_manufacturer_id,product_price,currency_code_3 as product_currency,product_override_price,pc.virtuemart_category_id,product_tax_id,product_discount_id,pc.ordering
		   						 FROM #__virtuemart_products as p
								 LEFT JOIN #__virtuemart_products_".$this->config->sufix." as pl ON p.virtuemart_product_id = pl.virtuemart_product_id
								 LEFT JOIN #__virtuemart_product_categories as pc ON p.virtuemart_product_id = pc.virtuemart_product_id
								 LEFT JOIN #__virtuemart_product_manufacturers as pm ON p.virtuemart_product_id = pm.virtuemart_product_id
								 LEFT JOIN #__virtuemart_manufacturers_".$this->config->sufix." as man ON pm.virtuemart_manufacturer_id = man.virtuemart_manufacturer_id
								 LEFT JOIN #__virtuemart_product_prices as pr ON pr.virtuemart_product_id = p.virtuemart_product_id AND (virtuemart_shoppergroup_id = 0 OR virtuemart_shoppergroup_id IS NULL) AND (price_quantity_end = 0 OR price_quantity_end IS NULL) AND (price_quantity_start = 0 OR price_quantity_start IS NULL)
								 LEFT JOIN #__virtuemart_currencies as c ON c.virtuemart_currency_id = pr.product_currency

		   						 WHERE product_parent_id = '$product->virtuemart_product_id' AND $where $price_label
								 GROUP BY p.virtuemart_product_id  $where2
								 ");

				  $children=$this->_db->loadObjectList();
				  if($children){

				  	  foreach($children as $key => $child){
				  	  	 if(empty($child))continue;
						 if(!$child->product_sku)
						 	$child->product_sku=$product->product_sku."_".$key;
						 $child->parent_sku=$product->product_sku;

						 if($this->is_multi){
														if(isset($product_variant_data[$child->virtuemart_product_id])){
								foreach($product_variant_data[$child->virtuemart_product_id] as $property => $value){
									$child->$property=$value;
								}
						   }
						 }


						 $result2[]=$child;
				  	  }

				  }

		   	   }

			   unset($result);




		   return @$result2;
	}

	function exportProducts($products){
		$total_products=count($products);

		for($z=0;$z<$total_products;$z++){
			   $p=$products[$z];
			   $this->log->row++;
		   	   $this->log->product++;
			   if(in_array($this->config->price_template,array(6,7))){
					@$this->log->currant_product_index++;
			   }
			   else{
					@$this->log->currant_product_index[$this->log->currant_index]++;
			   }

			   $this->log->current_product=$p->product_name;

			   $this->row++;
			   if($this->config->price_template==8 AND !$this->new_lib){
					   $this->getActiveSheet->getRowDimension($this->row)->setOutlineLevel($this->stat->currant_level+1);

			   }

			   if($this->csv)
				   $csv_product = array();

			   if((@$this->active['file_url'] OR @$this->active['file_url_thumb'] OR @$this->active['file_meta'] OR @$this->active['file_description'])AND @$p->virtuemart_product_id){

						$this->_db->setQuery("SELECT file_url, file_url_thumb, file_meta,file_description  FROM #__virtuemart_product_medias as pmed
									 		 LEFT JOIN #__virtuemart_medias as med ON pmed.virtuemart_media_id = med.virtuemart_media_id
											 WHERE pmed.virtuemart_product_id = $p->virtuemart_product_id
											 ORDER BY pmed.ordering");
						$p->file_url = @implode("|",$this->_db->loadColumn(0));
				   		$p->file_url_thumb = @implode("|",$this->_db->loadColumn(1));
				   		$p->file_meta = @implode("|",$this->_db->loadColumn(2));
				   		$p->file_description = @implode("|",$this->_db->loadColumn(3));




			   }

			   if($this->custom_fields){
				   $this->_db->setQuery("
				   SELECT pc.customfield_value, c.custom_title
				   FROM #__virtuemart_product_customfields as pc
				   LEFT JOIN #__virtuemart_customs as c ON pc.virtuemart_custom_id = c.virtuemart_custom_id
				   WHERE pc.virtuemart_product_id = $p->virtuemart_product_id
				   AND field_type IN('S','I')
				   ORDER BY pc.ordering
				   ");
				   $custom_fields=$this->_db->loadObjectList();

				   foreach($custom_fields as $v){

					  foreach($this->active as $a){
						  if($a->type=='custom' AND strstr($a->name,'custom_title') AND !$p->{$a->name}){
							  $p->{$a->name}=$v->custom_title;
							  $extra_id = $a->id;

							  break;
						  }
					  }

					  foreach($this->active as $a2){
						  if($a2->type=='custom' AND $a2->name =='custom_value_'.$extra_id){
							  $p->{$a2->name}=$v->customfield_value;

							  break;
						  }
					  }
				   }
			   }

			   foreach($this->active as $a ){
				   if(!$p->virtuemart_product_id)continue;
			   	   $extra_price_ordering=false;
									  $p->{$a->name}=htmlspecialchars_decode($p->{$a->name});
				   $p->{$a->name}=str_replace('&#34;','"',$p->{$a->name});

				   if($a->name == 'file_url'){
			   	   	  $p->file_url=$this->imagePath('product',$p,false);
			   	   }

			   	   elseif($a->name == 'file_url_thumb'){
			   	   	  $p->file_url_thumb=$this->imagePath('product',$p,true);
			   	   }
				   elseif($a->name == 'product_s_desc' OR $a->name == 'product_desc'){
					  if($this->csv){
						  $p->{$a->name}=str_replace("\n",'',$p->{$a->name});
						  $p->{$a->name}=str_replace("\r",'',$p->{$a->name});

				   	  }
					  /*else{						  $p->{$a->name}=str_replace("<br />","\n",$p->{$a->name});
						  $p->{$a->name}=str_replace("<br>","\n",$p->{$a->name});
						  $p->{$a->name}=str_replace("<br >","\n",$p->{$a->name});
					  }*/
			   	   }

				   elseif($a->name == 'path'){
					  if($this->config->price_template==6)
					  	$p->{$a->name}=$p->path;
					  elseif($this->config->price_template==7){
							$p->{$a->name}=$this->createCategoryPath($p->path);
					  }
					  $this->log->current_cat= @$p->path;
			   	   }

				   elseif($a->name == 'product_url_path'){


						if($this->sef)
							$p->{$a->name}=JURI::root().($this->sef_rewrite?"":"index.php/").$this->get_slug_path($p->virtuemart_product_id)."/".$p->slug.$this->sef_suffix;
						else
							$p->{$a->name}=JURI::root()."index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=".$p->virtuemart_product_id."&virtuemart_category_id=".$this->get_slug_path($p->virtuemart_product_id);


				   }
				   elseif($a->name == 'currency'){
						$p->{$a->name}=$p->product_currency;
				   }
				   elseif($a->name == 'product_price'){
						$p->{$a->name}=round($p->product_price/$this->config->currency_rate,2);
				   }


				   elseif(in_array($a->name,array('product_box','min_order_level','step_order_level','max_order_level')) AND @$p->product_params){
					  $params=explode("|",$p->product_params);
					  foreach($params as $param){
					  	if(strstr($param,$a->name)){
					  	   $matches=array();
						   preg_match('/"\S+"/',$param,$matches);
						   if(@$matches[0])
						   		$p->{$a->name}=substr($matches[0],1,-1);
						   break;
					  	}
					  }


			   	   }
				   /*if($a->name == 'product_price'){
						  $this->_db->setQuery("SELECT product_price FROM #__virtuemart_product_prices WHERE virtuemart_product_id = $p->virtuemart_product_id");

						  $p->{$a->name}=$this->_db->loadResult();

			   	   }*/
				   elseif($a->type == 'price'){					   $spec_price_data=json_decode($a->extra_id);

					   $this->_db->setQuery("SELECT product_price FROM #__virtuemart_product_prices WHERE  	virtuemart_product_id = $p->virtuemart_product_id AND virtuemart_shoppergroup_id = $spec_price_data->virtuemart_shoppergroup_id AND price_quantity_start = $spec_price_data->price_quantity_start AND price_quantity_end = $spec_price_data->price_quantity_end");
					   $p->{$a->name}=round($this->_db->loadResult()/$this->config->currency_rate,2);
				   }
				   elseif($a->type == 'cherry'){					   $cherry_data=json_decode($a->extra_id);
					   $type=array_shift($cherry_data);
					   $param_name=implode("_",$cherry_data);
					   $prefix=$this->is_cherry==1?"fastseller":"vm";

					   $this->_db->setQuery("SELECT `$param_name` FROM #__{$prefix}_product_type_{$type} WHERE  	product_id = $p->virtuemart_product_id");
					   $p->{$a->name}=$this->_db->loadResult();

				   }

				   elseif($a->type == 'extra'){
						  $custom=$this->extra[$a->extra_id];

						  if($custom->field_type=='E' AND $custom->custom_element=='param'){							$custom_params_tmp = explode('|',$custom->custom_params);
							$custom_params = array();
							foreach($custom_params_tmp as $k => $v){
								preg_match("/^([^=]*)=(.*)|/i",$v, $res);
								$custom_params[@$res[1]] = json_decode(@$res[2]);
							}

							$custom_type=$custom_params['ft'];
							if($custom_type=='int'){								$this->_db->setQuery("SELECT intval
													  FROM #__virtuemart_product_custom_plg_param_ref
													  WHERE virtuemart_product_id = $p->virtuemart_product_id
													  AND virtuemart_custom_id = $a->extra_id");
								$p->{$a->name}=$this->_db->loadResult();
							}
							else{
								$this->_db->setQuery("SELECT value
													  FROM #__virtuemart_product_custom_plg_param_values as v
													  LEFT JOIN #__virtuemart_product_custom_plg_param_ref as r ON v.id = r.val
													  WHERE virtuemart_product_id = $p->virtuemart_product_id
													  AND r.virtuemart_custom_id = $a->extra_id
													  ORDER BY ordering");

								$p->{$a->name}=implode("|",$this->_db->loadColumn());
							}

						  }
						  elseif($custom->field_type=='E' AND $custom->custom_element=='customfieldsforall'){								if(strstr($custom->custom_params,"color_hex")){
									$this->_db->setQuery("
									SELECT customsforall_value_label
									FROM #__virtuemart_custom_plg_customsforall_values as v
									LEFT JOIN #__virtuemart_product_custom_plg_customsforall as r
									ON v.customsforall_value_id = r.customsforall_value_id
									LEFT JOIN #__virtuemart_product_customfields as p
									ON p.virtuemart_customfield_id=r.customfield_id
									WHERE p.virtuemart_product_id = $p->virtuemart_product_id
									AND p.virtuemart_custom_id = $a->extra_id
									ORDER BY p.ordering");
								}
								else{
									$this->_db->setQuery("
									SELECT customsforall_value_name
									FROM #__virtuemart_custom_plg_customsforall_values as v
									LEFT JOIN #__virtuemart_product_custom_plg_customsforall as r
									ON v.customsforall_value_id = r.customsforall_value_id
									LEFT JOIN #__virtuemart_product_customfields as p
									ON p.virtuemart_customfield_id=r.customfield_id
									WHERE p.virtuemart_product_id = $p->virtuemart_product_id
									AND p.virtuemart_custom_id = $a->extra_id
									ORDER BY p.ordering");
								}


								$p->{$a->name}=implode("|",$this->_db->loadColumn());


						  }
						  elseif($custom->field_type=='E' AND $custom->custom_element=='articles'){								if($this->checkArticlesVersion()){
									$this->_db->setQuery("SELECT custom_param FROM #__virtuemart_product_customfields
													 WHERE virtuemart_product_id = $p->virtuemart_product_id
													 AND virtuemart_custom_id = $a->extra_id
													 LIMIT 0,1");
									$data=@json_decode($this->_db->loadResult());
									/*if($data->articles){
									   $p->{$a->name}=implode(",",$data->articles);
									   if($data->showas!='title')
											$p->{$a->name}.="|".$data->showas;
									}*/
									$temp=array();
									if(@$data->articles){
									   $temp[]="articles:".json_encode($data->articles);
									}
									if(@$data->k2items){
									   $temp[]="k2items:".json_encode($data->k2items);
									}
									$p->{$a->name}=str_replace('"',"",implode(",",$temp));
								}
								else{
									$this->_db->setQuery("SELECT articles,showas
														  FROM #__virtuemart_product_custom_plg_articles
														  WHERE virtuemart_product_id = $p->virtuemart_product_id
														  AND virtuemart_custom_id = $a->extra_id
														  LIMIT 0,1");
									$data=$this->_db->loadObject();
									$p->{$a->name}=$data->articles;
									if($data->showas!='title')
										$p->{$a->name}.="|".$data->showas;
								}

						  }
						  elseif($custom->field_type=='E' AND $custom->custom_element=='catproduct'){
								$this->_db->setQuery("SELECT custom_param FROM #__virtuemart_product_customfields
													 WHERE virtuemart_product_id = $p->virtuemart_product_id
													 AND virtuemart_custom_id = $a->extra_id
													 LIMIT 0,1");
								$data=@json_decode($this->_db->loadResult());

								if($data){
									$default_object=file_get_contents(dirname(__FILE__)."/catprod_default.txt");
					   				$default_object=@json_decode($default_object);
									if($default_object){
										foreach(get_object_vars($data) as $key=> $cat_prod_param){
											if($default_object->$key==$cat_prod_param)
												unset($data->$key);
										}
									}
								}
								if(!empty($data)){
									$data=json_encode($data);
									$data=str_replace('"','',$data);
									$data=str_replace(',','|',$data);
									$data=substr($data,1,-1) ;
									$p->{$a->name}=$data;
								}
						  }
						  else{

						  if(@$product_extra_cache_product_id!=$p->virtuemart_product_id){

							  $this->_db->setQuery("
										SELECT DISTINCT(virtuemart_custom_id)
										FROM #__virtuemart_product_customfields
										WHERE virtuemart_product_id =$p->virtuemart_product_id
										");
							  $product_extra_cache=$this->_db->loadColumn();
							  $product_extra_cache_product_id=$p->virtuemart_product_id;
						  }
						  if(in_array($a->extra_id,$product_extra_cache)){

								  $this->_db->setQuery("SELECT {$this->fieldname_custom_value} FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $p->virtuemart_product_id AND virtuemart_custom_id = $a->extra_id ORDER BY ordering");

								  $extra=$this->_db->loadColumn();
								  if(count($extra)>0){
								  	  if($custom->field_type=='M'){										  $this->_db->setQuery("SELECT file_title  FROM #__virtuemart_medias WHERE virtuemart_media_id = '".@$extra[(int)$extra_value[$a->extra_id]]."'");
										  @$p->{$a->name}=$this->_db->loadResult();
								  	  }
									  else
									  	  @$p->{$a->name}=$extra[(int)$extra_value[$a->extra_id]];
									  @$extra_value[$a->extra_id]++;
								  }
						  }

						}

			   	   }

				   elseif($a->type == 'extra-cart'){
						  $this->_db->setQuery("
						  SELECT {$this->fieldname_custom_value}, {$this->fieldname_custom_price}
						  FROM #__virtuemart_product_customfields
						  WHERE virtuemart_product_id = $p->virtuemart_product_id
						  AND virtuemart_custom_id = $a->extra_id
						  ORDER BY ordering");
						  $extra=$this->_db->loadObjectList();

						  $this->_db->setQuery("SELECT field_type,custom_value,custom_element,custom_params FROM #__virtuemart_customs WHERE  virtuemart_custom_id = $a->extra_id");
						  $custom=$this->_db->loadObject();

						  if($custom->field_type=='E' AND $custom->custom_element=='param'){							$custom_params_tmp = explode('|',$custom->custom_params);
							$custom_params = array();
							foreach($custom_params_tmp as $k => $v){
								preg_match("/^([^=]*)=(.*)|/i",$v, $res);
								$custom_params[@$res[1]] = json_decode(@$res[2]);
							}

							$custom_type=$custom_params['ft'];
							if($custom_type=='int'){								$this->_db->setQuery("SELECT intval
													  FROM #__virtuemart_product_custom_plg_param_ref
													  WHERE virtuemart_product_id = $p->virtuemart_product_id
													  AND virtuemart_custom_id = $a->extra_id");
								$p->{$a->name}=$this->_db->loadResult();
							}
							else{
								$this->_db->setQuery("SELECT value
													  FROM #__virtuemart_product_custom_plg_param_values as v
													  LEFT JOIN #__virtuemart_product_custom_plg_param_ref as r ON v.id = r.val
													  WHERE virtuemart_product_id = $p->virtuemart_product_id
													  AND r.virtuemart_custom_id = $a->extra_id
													  ORDER BY ordering");

								$p->{$a->name}=implode("|",$this->_db->loadColumn());
							}

						  }
						  elseif($custom->field_type=='E' AND $custom->custom_element=='customfieldsforall'){
								if(strstr($custom->custom_params,"color_hex")){
									$this->_db->setQuery("
									SELECT customsforall_value_label
									FROM #__virtuemart_custom_plg_customsforall_values as v
									LEFT JOIN #__virtuemart_product_custom_plg_customsforall as r
									ON v.customsforall_value_id = r.customsforall_value_id
									LEFT JOIN #__virtuemart_product_customfields as p
									ON p.virtuemart_customfield_id=r.customfield_id
									WHERE p.virtuemart_product_id = $p->virtuemart_product_id
									AND p.virtuemart_custom_id = $a->extra_id
									ORDER BY p.ordering",intval(@$extra_cart_value[$a->extra_id]),1);
								}
								else{
									$this->_db->setQuery("
									SELECT customsforall_value_name
									FROM #__virtuemart_custom_plg_customsforall_values as v
									LEFT JOIN #__virtuemart_product_custom_plg_customsforall as r
									ON v.customsforall_value_id = r.customsforall_value_id
									LEFT JOIN #__virtuemart_product_customfields as p
									ON p.virtuemart_customfield_id=r.customfield_id
									WHERE p.virtuemart_product_id = $p->virtuemart_product_id
									AND p.virtuemart_custom_id = $a->extra_id
									ORDER BY p.ordering",intval(@$extra_cart_value[$a->extra_id]),1);
								}


								$p->{$a->name}=$this->_db->loadResult();

								$extra_price_ordering = @$this->active['extra_price_'.$a->id]->ordering;
								$price= @$extra[(int)$extra_cart_value[$a->extra_id]]->{$this->fieldname_custom_price};

								@$extra_cart_value[$a->extra_id]++;



						  }
						  elseif(count($extra)>0){
							  if($custom->field_type=='M'){									  $this->_db->setQuery("SELECT file_title  FROM #__virtuemart_medias WHERE virtuemart_media_id = '".@$extra[(int)$extra_cart_value[$a->extra_id]]->{$this->fieldname_custom_value}."'");
									  @$p->{$a->name}=$this->_db->loadResult();
							  }
							  else{
								  @$p->{$a->name}=$extra[(int)$extra_cart_value[$a->extra_id]]->{$this->fieldname_custom_value};
							  }

							  $extra_price_ordering = @$this->active['extra_price_'.$a->id]->ordering;
							  $price= @$extra[(int)$extra_cart_value[$a->extra_id]]->{$this->fieldname_custom_price};
							  $price=round($price/$this->config->currency_rate,2);
							  @$extra_cart_value[$a->extra_id]++;
						  }

			   	   }

				   elseif($a->name == 'related_products' AND $this->related_custom_id){  					   $this->_db->setQuery("SELECT {$this->fieldname_custom_value} FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = $p->virtuemart_product_id AND virtuemart_custom_id = {$this->related_custom_id}");

					   $p->{$a->name}=implode('|',$this->_db->loadColumn());
			   	   }

				   elseif($a->name == 'related_products_sku' AND $this->related_custom_id){
					   $this->_db->setQuery("SELECT p.product_sku
					   						FROM #__virtuemart_product_customfields as c
											LEFT JOIN #__virtuemart_products as p ON p.virtuemart_product_id = c.{$this->fieldname_custom_value}
											WHERE c.virtuemart_product_id = $p->virtuemart_product_id AND virtuemart_custom_id = {$this->related_custom_id}");

					   $p->{$a->name}=implode('|',$this->_db->loadColumn());
			   	   }

				   elseif($a->name == 'shoppergroup_id'){
					   $this->_db->setQuery("SELECT virtuemart_shoppergroup_id FROM #__virtuemart_product_shoppergroups WHERE virtuemart_product_id = $p->virtuemart_product_id");
					   $p->{$a->name}=implode('|',$this->_db->loadColumn());
			   	   }


				   if(!$this->csv){
					   if(!$this->new_lib){
						   if(in_array($a->name,array('product_sku','path','slug'))){
							  @$this->getActiveSheet->getCellByColumnAndRow($a->ordering-1,$this->row)->setValueExplicit($p->{$a->name},PHPExcel_Cell_DataType::TYPE_STRING);

						   }
					   	   elseif($a->type != 'extra-price'){
								@$this->getActiveSheet->setCellValueByColumnAndRow($a->ordering-1,$this->row,$p->{$a->name},PHPExcel_Cell_DataType::TYPE_STRING);
					   	   }
						   if($extra_price_ordering)
								 $this->getActiveSheet->setCellValueByColumnAndRow(($extra_price_ordering-1),$this->row,$price);
					   }
					   else{
						   if($a->type != 'extra-price')
					   			@$row[$a->ordering]=$p->{$a->name};
	  					   if($extra_price_ordering)
	  					   		@$row[$extra_price_ordering]=$price;
					   }
				   }
				   else{
				   	   if($a->type != 'extra-price')
					   		@$csv_product[$a->ordering]=$p->{$a->name};
					   if($extra_price_ordering)
					   		@$csv_product[$extra_price_ordering]=$price;
				   }
			   }

			   if($this->csv){
				  $this->print_csv($csv_product);
			   }

			   if($this->new_lib){
					 if($this->config->price_template==8 ){
							$this->xlsxWriter->writeSheetRow('Sheet1', $this->normalize_row($row),$this->stat->currant_level+1);
					 }
					 else{
						 $this->xlsxWriter->writeSheetRow('Sheet1', $this->normalize_row($row));
					 }

			   }
			   unset($extra_value);
			   unset($extra_cart_value);
			   unset($extra_price_ordering);
			   unset($price);
			   unset($p);
			   unset($products[$z]);
			   unset($product_extra_cache);
							  $this->updateStat();

							  if($this->csv AND $this->timeout < time()){
					$this->updateStat(array('status'=>'timeout'));
					file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS.'category_bak.txt',serialize(@$this->new_tree));
					$temp=array(1,2);
					$this->print_csv($temp,true);
					jexit();
			   }




			   if(!$this->csv AND (memory_get_usage(true) > $this->mem_limit OR ($this->row_limit>0 AND $this->row > $this->row_limit) OR ( $this->timeout < time()))){

			   	   unset($products);
			   	   $this->log->status=JText::_('CREATING_EXCEL_FILE');
				   $this->last_upd-=2;
				   $this->updateStat();
			   	   $this->part++;

				   $price_label=($this->price_label AND $_REQUEST['price_label'])?$_REQUEST['price_label']:"";
				   if($price_label){
					  $price_label=str_replace(".".pathinfo($price_label, PATHINFO_EXTENSION),"",$price_label);
				   }
				   if(@$this->filename){
						$file_name_base=$this->filename;
					}
					else{
						$file_name_base= "export".(date("Y-m-d_H:i:s"));
				   }
				   $file_name=$price_label?$price_label:$file_name_base;

				   if(!$this->new_lib){
					   if($this->file_type == 1){
							$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5');
							$file_extension=".xls";
					   }
					   else{
							$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
							$file_extension=".xlsx";
					   }
					   $objWriter->setPreCalculateFormulas(false);



						   				   $objWriter->save($this->export_directory_path.DS.$file_name.'_part'.$this->part.$file_extension);
				   }
				   else{
					   $file_extension=".xlsx";
					   $this->xlsxWriter->writeToFile($this->export_directory_path.DS.$file_name.'_part'.$this->part.$file_extension);
				   }

				   @$resp->text="{$this->part}.".JText::_('DOWNLOAD_EXPORTED_DATA')." ($this->row ".JText::_('ROWS').") - ".JText::_('PART')." {$this->part}";
				   $resp->link=JURI::base()."/components/com_excel2vm/export/".$file_name."_part{$this->part}".$file_extension;
				   $resp->finish=0;
				   $resp->filename=$file_name_base;
				   echo json_encode($resp);

				   file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS.'category_bak.txt',serialize(@$this->new_tree));
				   jexit();
			   }

		   }
	}

	function updateStat($params=array()) {
		if (time() - $this->last_upd > 1 OR count($params)) {
			$this->last_upd = time();
			@$data->row=@$this->log->row;
			$data->cat=$this->log->cat;
			$data->product=$this->log->product;
			$data->current_cat=str_replace(',','.',@$this->log->current_cat);
			$data->current_product=str_replace(',','.',@$this->log->current_product);
			$data->time=time()-$this->log->start_time;
			$data->mem=$this->get_mem();
			$data->status=@$this->log->status;
			if(count($params)){
				foreach($params as $key=>$value ){
					$data->$key=$value;
				}
			}
									 file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'. DS . 'export_log.txt', json_encode($data));
			 file_put_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS.'log_bak.txt',serialize($this->log));
		}
	}
	function get_mem(){
		if( function_exists("memory_get_usage") ) {
				$mem_usage = memory_get_usage(true);
				return round($mem_usage/1048576,2)." Mb";
		 }
		 else return false;
	}

	function get_mem_total(){
		if( function_exists("memory_get_peak_usage") ) {
				$mem_usage = memory_get_peak_usage(true);
				return round($mem_usage/1048576,2)." Mb";
		}
		else return false;
	}
	/*function get_slug_path($virtuemart_product_id){
		$this->_db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id = $virtuemart_product_id");
		$parrent_category_id=$this->_db->loadResult();
		if(!$this->sef)return $parrent_category_id;
		if($parrent_category_id==@$this->last_parrent_category_id)
			return $this->last_path;
		else
		   $this->last_parrent_category_id=$parrent_category_id;
		for(;;){
			if(!$parrent_category_id)break;

			$this->_db->setQuery("SELECT `path` FROM #__menu WHERE link = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=$parrent_category_id'");
			$path=$this->_db->loadResult();
			if($path){
			   $this->last_path=$path.'/'.implode("/",$slug);
			   return $this->last_path;
			}

			$this->_db->setQuery("SELECT slug,category_parent_id FROM #__virtuemart_categories_".$this->config->sufix." as c
								  LEFT JOIN #__virtuemart_category_categories as cc ON cc.category_child_id = c.virtuemart_category_id
								  WHERE c.virtuemart_category_id = $parrent_category_id");
			$data=$this->_db->loadObject();
			$slug[]=$data->slug;
			$parrent_category_id=$data->category_parent_id;
		}
		$this->last_path=$this->item_id->path.'/'.implode("/",$slug);
		return $this->last_path;
	}*/
	function get_slug_path($virtuemart_product_id){
		$path='';
		$this->_db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id = $virtuemart_product_id");
		$parrent_category_id=$this->_db->loadResult();
		if(!$this->sef)return $parrent_category_id;
		if(!$parrent_category_id)return 0;
		$parent=$parrent_category_id;
		while(!$path){
			$this->_db->setQuery("SELECT `path` FROM #__menu WHERE link LIKE 'index.php?option=com_virtuemart&view=category&virtuemart_category_id={$parent}%' ORDER BY id",0,1);
			$path=$this->_db->loadResult();
			if(!$path){
				 $this->_db->setQuery("SELECT category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id = '$parent'");
				 $parent=$this->_db->loadResult();
				 if(!$parent){
					 break;
				 }
			}

		}

		if(!$path){
			   $this->_db->setQuery("SELECT `path` FROM #__menu WHERE link LIKE 'index.php?option=com_virtuemart%' AND client_id = 0 AND published = 1 ORDER BY id",0,1);
			   $path=$this->_db->loadResult();

		}
		else{
			return $path;
		}

		if(!$path){
			   $path="component/virtuemart";
		}

		while($parrent_category_id){
			$this->_db->setQuery("SELECT slug FROM #__virtuemart_categories_".$this->config->sufix." as c
									  WHERE c.virtuemart_category_id = $parrent_category_id");
			$slug[]=$this->_db->loadResult();
			$this->_db->setQuery("SELECT category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id = '$parrent_category_id'");
			$parrent_category_id=$this->_db->loadResult();
		}
		$slug=array_reverse($slug);

		return $path.'/'.implode("/",$slug);
	}

	function print_csv(& $row,$force_print=false){
		if($force_print AND count(@$this->csv_buffer)){
			@fwrite($this->csv_file,implode("",$this->csv_buffer));
			unset($this->csv_buffer);
			return false;
		}

		if(empty($row)){
		   return false;
		}
		$row=(array)$row;
		for($i=1;$i<=count($row);$i++){
		   if(!isset($row[$i]))continue;
		   $row[$i]=str_replace(';','%3B',$row[$i]);
		   $row[$i]=str_replace("\n",'',$row[$i]);
		   $row[$i]=str_replace("\r",'',$row[$i]);
		}
		if($this->csv_convert)
			$csv_row=iconv("UTF-8","WINDOWS-1251",$this->csv_row_delimiter.implode($this->csv_field_delimiter,$row)) .$this->csv_row_delimiter."\n";

		else
			$csv_row=$this->csv_row_delimiter.implode($this->csv_field_delimiter,$row) .$this->csv_row_delimiter."\n";

		unset($row);

		if($csv_row!=$this->csv_row_delimiter.$this->csv_row_delimiter."\n"){
			@$this->csv_buffer[]=$csv_row;
		}
		else{
		  return false;
		}
		if(count(@$this->csv_buffer)>10){
			 @fwrite($this->csv_file,implode("",$this->csv_buffer));
			 unset($this->csv_buffer);
			 return false;
		}
	}
/*
	function check() {
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
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
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
	function imagePath($type,$obj,$thumb=false){
		$file_array=$thumb?explode("|",$obj->file_url_thumb):explode("|",$obj->file_url);

		foreach($file_array as $path){
			$base=$thumb?$this->config->thumb_path:$this->config->path;
			$base=$type=='category'?str_replace("product","category",$base):$base;
			$url=strstr($path,"http://");
			$temp=explode("/",$path);
			$name=array_pop($temp);
			$name_array[]=$name;
			if($url){
			   $temp=array_slice($temp,3);
			   $relative=implode("/",$temp);
			   $relative=str_replace($base,"",$relative);

			   $relative=strlen($name)>4?($relative."/".$name):'';
			   $relative_array[]=$relative;
			   $absolute_array[]=$url;
			}
			else{
				$relative=implode("/",$temp);
				$relative=strlen($name)>4?str_replace($base,"",$path):'';
				$relative_array[]=$relative;
				$absolute_array[]=strlen($name)>4?(JURI::root().$path):'';

			}
		}

		if($type=='category'){			 $this->_db->setQuery("SELECT file_url".($thumb?"_thumb":"")." FROM #__virtuemart_category_medias as cm
								  LEFT JOIN  #__virtuemart_medias as m ON m.virtuemart_media_id = cm.virtuemart_media_id
								  WHERE cm.virtuemart_category_id = $obj->category_child_id ORDER BY cm.ordering");
			  switch ($this->image_path) {
			   	   	case 0:
						   return str_replace($base,'',$this->_db->loadResult());
			   	   	 break;
					 case 1:
					 		return $this->_db->loadResult();
					 break;
					 case 2:
							return JURI::root().$this->_db->loadResult();
			   	   	 break;
			   	   }
		}
		else{
			 switch ($this->image_path) {
			   	 case 0:
					   return implode("|",$name_array);
			   	 break;
				 case 1:
					   return implode("|",$relative_array);
			   	 break;
				 case 2:
					   return implode("|",$absolute_array);
			   	 break;
			}
	   }


	}

	function zip(){

		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.archive' );
		$parts=JRequest::getVar('parts', '', '', 'int')+1;
		$mark=$this->filename;
		$extension=$_GET['file_type']==1?"xls":"xlsx";
		$archive = JPATH_COMPONENT.DS.'export'.DS.'export'.$mark.'.zip';
		$zip = &JArchive::getAdapter('zip');

		for($i=1;$i<=$parts;$i++){
			$f['name'] = $mark.'_part'.$i.'.'.$extension;
			$f['data'] = file_get_contents( JPATH_COMPONENT.DS.'export'.DS.$f['name'] );
			$data_file_array[] =$f;
		}
		$zip->create($archive, $data_file_array);

		header("Location: ".JURI::base()."components/com_excel2vm/export/".'export'.$mark.'.zip');

		exit();

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

	/*function getPriceData($product_id,$field){
		 if(@$this->price_data->virtuemart_product_id)
		 $this->_db->setQuery("SELECT * FROM #__virtuemart_product_prices WHERE virtuemart_product_id = $product_id ORDER BY virtuemart_product_price_id LIMIT 0,1");
		 $this->price_data=$this->_db->loadObject();
		 return $this->price_data->$field;
	}*/

	function print_headers(){

		if(!$this->csv){
			if(!$this->new_lib){
				foreach($this->active as $a ){					$this->getActiveSheet->setCellValueByColumnAndRow($a->ordering-1,$this->row,JText::_($a->title));
				}
			}
			else{
				$headers=array();
				foreach($this->active as $a ){					$headers[]= JText::_($a->title);
				}
				$this->xlsxWriter->writeSheetRow('Sheet1', $headers);
			}
		}
		else{
			$headers=array();
			foreach($this->active as $a ){				$headers[$a->ordering]=JText::_($a->title);
			}
			$this->print_csv($headers);
		}
	}

	function print_new_headers(){
		$this->print_headers();

		$curant_category_id=$this->new_tree[$this->log->currant_index]->category_child_id;
		$parent_categories_ids=array();
		$this->_db->setQuery("SELECT category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id = '$curant_category_id'");
		$category_parent_id=$this->_db->loadResult();
		while($category_parent_id>0){
			$parent_categories_ids[]=$category_parent_id;
			$this->_db->setQuery("SELECT category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id = '$category_parent_id'");
			$category_parent_id=$this->_db->loadResult();
		}

		foreach($this->new_tree as $category){
			if(in_array($category->category_child_id,$parent_categories_ids)){
				$this->exportCategory($category);
			}
		}

	}

	function createCategoryPath($category_ids){

		$path_array=array();
		$category_ids=explode(",",$category_ids);
		$category_ids=array_unique($category_ids);
		foreach($category_ids as $category_id){
			if(isset($this->cat_path_chache[$category_id])){
				$path_array[]=$this->cat_path_chache[$category_id];
				continue;
			}
			$path=array();
			$cid=$category_id;
			for(;;){
				if(!$cid)break;
				$this->_db->setQuery("SELECT category_name
									  FROM #__virtuemart_categories_".$this->config->sufix."
									  WHERE virtuemart_category_id =  $cid");
				$category_name=$this->_db->loadResult();
				if(!$category_name){
					break;
				}
				$path[]=$category_name;
				$this->_db->setQuery("SELECT category_parent_id FROM #__virtuemart_category_categories WHERE category_child_id = '$cid'");

				$cid=$this->_db->loadResult();
				if(!$cid){
					break;
				}

			}

			krsort($path);
			$path_array[]=implode($this->config->level_delimiter,$path);
			@$this->cat_path_chache[$category_id]=implode($this->config->level_delimiter,$path);
			unset($path);
		}
		return implode($this->config->category_delimiter,$path_array);
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

	function get_files(){
	  jimport( 'joomla.filesystem.file' );
	  jimport( 'joomla.filesystem.folder' );
	  $uploaded_files=JFolder::files( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export' );
	  $data=array();
	  foreach($uploaded_files as $key => $file ){
		 if(in_array(substr($file,-4),array('.xls','.csv','xlsx','.zip'))){
			  @$data[$key]->file=$file;
			  $data[$key]->size=$size=filesize(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS.$file);
			  $data[$key]->time=filemtime(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS.$file);
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
				  $files.='<td><label for="uploaded_file_'.$key.'">'.$f->file.'</label></td>';
				  $files.='<td>'.$this->getSize($f->size).'</td>';
				  $files.='<td>'.date("Y-m-d H:i",$f->time).'</td>';
				  $files.='<td><a href="index.php?option=com_excel2vm&view=export&task=download&file='.$f->file.'"><img src="'.JURI::base().'/components/com_excel2vm/assets/images/download.png" width="16" height="16" alt=""></a></td>';
				  $files.='<td><img style="cursor: pointer" rel="'.$key.'" file="'.$f->file.'"  class="delete" src="'.JURI::base().'/components/com_excel2vm/assets/images/delete.png" width="16" height="16" alt=""></td>';
				$files.='</tr>';
		}
		echo $files;
	}

	function download(){
	  jimport( 'joomla.filesystem.file' );
	  jimport( 'joomla.filesystem.folder' );
	  $file=$_GET['file'];
	  if(!$file)exit();
	  $uploaded_files=JFolder::files( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export' );
	  foreach($uploaded_files as $key => $f ){
		 if(!in_array(substr($f,-4),array('.xls','.csv','xlsx','.zip'))){
			  unset($uploaded_files[$key]);
		 }
	  }
	  if(!in_array($file,$uploaded_files)){

		echo "Файл $file не найден";
		exit();
	  }
	  $mainframe = JFactory::getApplication();
	  $mainframe->redirect(JURI::base()."/components/com_excel2vm/export/".$file);
	}

	function delete(){
	  jimport( 'joomla.filesystem.file' );
	  jimport( 'joomla.filesystem.folder' );
	  $file=$_GET['file'];
	  if(!$file)exit();
	  $uploaded_files=JFolder::files( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export' );
	  foreach($uploaded_files as $key => $f ){
		 if(!in_array(substr($f,-4),array('.xls','.csv','xlsx','.zip'))){
			  unset($uploaded_files[$key]);
		 }
	  }
	  if(!in_array($file,$uploaded_files)){
		echo "Файл не найден";
		exit();
	  }
	  if(unlink(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS.$file)){
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
	  $uploaded_files=JFolder::files( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export' );
	  foreach($uploaded_files as $key => $f ){
		 if(in_array(substr($f,-4),array('.xls','.csv','xlsx','.zip'))){
			  if(!unlink(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_excel2vm'.DS.'export'.DS.$f)){
				  print_r(error_get_last());
				  exit();
			  }
		 }
	  }
	}

	 function getSize($bytes){
	   if($bytes<1024)
	   	  return $bytes." B<br>";
	   elseif($bytes<1024*1024)
	   	  return round($bytes/1024)." KB<br>";
	   else
	   	  return round($bytes/(1024*1024),2)." MB<br>";
	}

	function normalize_row($row){
	   $new_row=array();
	   $total=count($this->active);
	   for($i=1;$i<=$total;$i++){
		   if(isset($row[$i])){
			   $new_row[$i]=$row[$i];
		   }
		   else{
			   $new_row[$i]='';
		   }
	   }
	   return $new_row;
	}

	function cron_log($msg){
	  $fp = fopen( dirname(__FILE__).DS."cron_export_log.txt" , "a" );
	  fwrite($fp, date("Y-m-d H:i:s")." - ".$msg."\r\n");
	  fclose($fp);
	  echo "$msg<br>";
	}
}
