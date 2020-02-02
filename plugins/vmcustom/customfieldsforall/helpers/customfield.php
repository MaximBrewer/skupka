<?php
/**
 * @version		$Id: customfield.php 2013-07-18 18:26 sakis Terz $
 * @package		customfieldsforall
 * @copyright	Copyright (C)2013-2014 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
require(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmcustom'.DIRECTORY_SEPARATOR.'customfieldsforall'.DIRECTORY_SEPARATOR.'tables'.DIRECTORY_SEPARATOR.'customvalue.php');
require_once(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmcustom'.DIRECTORY_SEPARATOR.'customfieldsforall'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'filter.php');
if(!class_exists('VmCompatibility'))require_once(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmcustom'.DIRECTORY_SEPARATOR.'customfieldsforall'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmcompatibility.php');

/**
 *
 * Class that contains the necessary functions used by the customfield
 * @package		customfieldsforall
 * @author 		Sakis Terz
 *
 */
Class Customfield{
	//the name of the fields in the product, different name in VM2 and VM3
	protected $_paramName='customfield_params';
	protected static $_customparams=array();
	protected static $_customvalues=array();
	//the stored customvalue_ids to a specific product
	protected static $_product_customvalue_ids=array();
	protected static $_customStored=array();
	protected static $_isCustomSetInProduct=array();
	protected $_custom_id;
	protected static $instances;

	/**
	 * Constructor
	 *
	 * @param 	int $_custom_id
	 * @since	1.0
	 */
	public function __construct($_custom_id){
		$this->_custom_id=(int)$_custom_id;
		if(!defined('VM_VERSION'))define('VM_VERSION', '2.0');
		if(version_compare(VM_VERSION, '2.9','lt')){
			$this->_paramName = 'plugin_param';
		} else {
			$this->_paramName = 'customfield_params';
		}

	}

	/**
	 * Get the singleton customfield instance
	 *
	 * @param int $custom_id
	 */
	public static function getInstance($custom_id){
		if(empty(self::$instances[$custom_id])){
			self::$instances[$custom_id]=new Customfield($custom_id);
		}
		return self::$instances[$custom_id];
	}

	/**
	 * Returns all the values (labels,id,etc..) of a custom field
	 * @since	1.0
	 * @param 	string $fields
	 */
	public function getCustomValues($fields='*'){

		if((int)$this->_custom_id==0 || $this->_custom_id==NULL)return array();
		$fields_key=md5($fields.$this->_custom_id);

		if(empty(self::$_customvalues[$fields_key])){

			$fields2= $fields;
			//the query should always get the `customsforall_value_id. Its used for sorting the array
			if($fields!='*' && $fields!='customsforall_value_id')$fields2.=' ,customsforall_value_id';
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select($fields2);
			$query->from($db->quoteName('#__virtuemart_custom_plg_customsforall_values'));
			$query->where('virtuemart_custom_id='.(int)$this->_custom_id);
			$query->order('ordering ASC');
			$db->setQuery($query);
			$results=$db->loadObjectList();
			//vmdebug('Cf4all getCustomValues query: ',(string)$query);
			$new_results=array();

			foreach($results as $res){
				if(strpos($fields, '*')!==false|| strpos($fields, ',')!==false)$new_results[$res->customsforall_value_id]=$res;  //object, multiple fields
				else $new_results[$res->customsforall_value_id]=$res->$fields; //sinle field
				//vmdebug('Cf4all $new_results: ',$new_results[$res->customsforall_value_id]);

			}

			self::$_customvalues[$fields_key]=$new_results;
		}
		return self::$_customvalues[$fields_key];
	}

	/**
	 * Get and returns data about a custom value also about the price variant for a specific product when product_id is used
	 *
	 * @param 	int $custom_value_id
	 * @param	int $product_id
	 * @since	1.0
	 */
	public static function getCustomValue($custom_value_id=0, $product_id=0, $value_product_id=0){
		$vmCompatibility=VmCompatibility::getInstance();
		$db=JFactory::getDbo();
		$q=$db->getQuery(true);
		$q->select('*');
		$q->from('#__virtuemart_custom_plg_customsforall_values AS cf');
		//from 1.7 and then get the price from the virtuemart_product_customfields table
		if($product_id){
			$q->select('cf_p.'.$vmCompatibility->getColumnName('custom_price').' AS custom_price');
			$q->leftJoin('#__virtuemart_product_custom_plg_customsforall AS cf_pr ON cf.customsforall_value_id=cf_pr.customsforall_value_id');
			$q->innerJoin('#__virtuemart_product_customfields  AS cf_p ON cf_pr.customfield_id=cf_p.virtuemart_customfield_id');
			$q->where('cf_pr.virtuemart_product_id='.(int)$product_id);
		}
		if($value_product_id){
			$q->select('cf_p.'.$vmCompatibility->getColumnName('custom_price').' AS custom_price');
			$q->leftJoin('#__virtuemart_product_custom_plg_customsforall AS cf_pr ON cf.customsforall_value_id=cf_pr.customsforall_value_id');
			$q->innerJoin('#__virtuemart_product_customfields  AS cf_p ON cf_pr.customfield_id=cf_p.virtuemart_customfield_id');
			$q->where('cf_pr.id='.(int)$value_product_id);
		}
		if($custom_value_id)$q->where('cf.customsforall_value_id='.(int)$custom_value_id);
		$db->setQuery($q);
		$result=$db->loadObject();
		return $result;
	}



	/**
	 * Returns all the value ids(or othe fields) of a product for a specific custom field
	 * @since	1.0
	 * @param 	mixed 	$product_id array or int
	 * @param	string	the fields to retutn
	 */
	public function getProductCustomValues($product_id,$fields='*',$customfield_id=0){
		$customfield_id=(int)$customfield_id;
		$key=md5($product_id.'_'.$this->_custom_id.'_'.$customfield_id.'_'.$fields);
		if(empty(self::$_product_customvalue_ids[$key])){
			if(isset($customfield_id)){
				//if we have to get all the fields then we need the compatibility for the price
				if($fields=='*'){
					$vmCompatibility=VmCompatibility::getInstance();
					$fields.=' ,'.$vmCompatibility->getColumnName('custom_price').' AS custom_price';
				}
				$db=JFactory::getDbo();
				$query=$db->getQuery(true);
				$query->select($fields);
				$query->from('`#__virtuemart_product_custom_plg_customsforall` AS p_cf');
				$query->innerJoin('`#__virtuemart_custom_plg_customsforall_values` AS cf ON p_cf.customsforall_value_id=cf.customsforall_value_id');
				$query->leftJoin('#__virtuemart_product_customfields  AS cf_p ON p_cf.customfield_id=cf_p.virtuemart_customfield_id');
				if(!is_array($product_id))$query->where('p_cf.virtuemart_product_id='.(int)$product_id);
				else $query->where('p_cf.virtuemart_product_id IN('.implode(',', $product_id).')');
				if($customfield_id){
                    $query->where('p_cf.customfield_id='.$customfield_id);
                    $query->order('p_cf.customfield_id, cf.ordering ASC');
				}
                else $query->order('cf.ordering ASC');

				$query->where('cf.virtuemart_custom_id='.(int)$this->_custom_id);

				$db->setQuery($query); //echo (string)$query;
				if(strpos($fields, '*')!==false || strpos($fields, ',')!==false){
					$results=$db->loadObjectList();
				}
				//single field
				else $results=$db->loadColumn();

			}else $results=array(0);
			
			//vmdebug('query :', (string)$query);
			self::$_product_customvalue_ids[$key]=$results;
		}
		return self::$_product_customvalue_ids[$key];
	}

	/**
	 * Stores/updates the custom values table
	 *
	 * @param array $data_array
	 * @since	1.0
	 */
	public function store($data_array,$set_ordering=true){
		JTable::addIncludePath(JPATH_PLUGINS.'/vmcustom/customfieldsforall/tables/customvalue.php');
		$app=JFactory::getApplication();
		$db=JFactory::getDbo();
		$ordering=1;
		//$filterInput=JFilterInput::getInstance();
		$filterInput=CustomfieldsForAllFilter::getInstance(); //filter

		$stored_ids=array();
		$custom_params=$this->getCustomfieldParams($this->_custom_id);
		$data_type=$custom_params['data_type'];
		$existing_values=$this->getCustomValues('customsforall_value_name');
		$newly_inserted=array(); //stores all the new inserted values then check for duplicates between the new values

		foreach ($data_array as $data_row){
			//Null values are not allowed
			$value_name=$data_row['customsforall_value_name'];
			$data_type2=$data_type;
			if(is_array($value_name))$data_type2=$data_type.'-array';
			else $value_name=trim($value_name);

			$data_row['customsforall_value_name']=$filterInput->clean($value_name,$data_type2);
			//vmdebug('Cf4all to be stored Values: ',$data_row['customsforall_value_name']);
			$customsforall_value_label=trim($data_row['customsforall_value_label']);
			$data_row['customsforall_value_label']=$filterInput->clean($customsforall_value_label,'string');
			$is_new_and_exist=false;

			if(empty($data_row['customsforall_value_id']) && (in_array($data_row['customsforall_value_name'],$existing_values) || in_array($data_row['customsforall_value_name'], $newly_inserted)))
			{
				$is_new_and_exist=true;
			}

			if(!empty($data_row['customsforall_value_name']) && $is_new_and_exist==false){
				$newly_inserted[]=$data_row['customsforall_value_name'];
				$row =& JTable::getInstance('Customvalue', 'Table');
				if($set_ordering)$data_row['ordering']=$ordering;
				$data_row['virtuemart_custom_id']=$this->_custom_id;

				if (!$row->bind($data_row)) {
					vmdebug('CF4All Error binding: ',$row->getError());
					return;
				}

				if (!$row->check()) {
					vmdebug('CF4All Error Checking: ',$row->getError());
					return;
				}
				if ($row->store()) {
					$stored_ids[]=$row->customsforall_value_id;
					//vmdebug('CF4All Inserted custom value ids: ',$row->customsforall_value_id);
					$ordering++;
				}else vmdebug('CF4All Error Inserting Values: ',$row->getError());
			}else{
				//is invalid
				if($is_new_and_exist){
					$msg=JText::sprintf('PLG_CUSTOMSFORALL_NOTICE_VALUE_EXIST_CANNOT_SAVED',$value_name);
					$app->enqueueMessage($msg,'notice');
				}
				if(!empty($data_row['customsforall_value_name'])){
					$msg=JText::sprintf('PLG_CUSTOMSFORALL_NOTICE_VALUE_INVALID_CANNOT_SAVED',$value_name);
					$app->enqueueMessage($msg,'notice');
				}
			}
		}
		return $stored_ids;
	}


	/**
	 * Deletes the custom values and their connections
	 *
	 * @param 	array $ids
	 * @since	1.0
	 */
	public function delete($ids){
		JTable::addIncludePath(JPATH_PLUGINS.'/vmcustom/customfieldsforall/tables/customvalue.php');
		$db=JFactory::getDbo();
		$q=$db->getQuery(true);

		foreach ($ids as $id){
			$row =& JTable::getInstance('Customvalue', 'Table');
			if (!$row->delete($id)){
				vmdebug('Error deleting Custom Value: ',$id.' '.$row->getError());
			}else{
				//if deleted successfully clean also the connections with other tables
				$q->delete('#__virtuemart_product_custom_plg_customsforall');
				$q->where('customsforall_value_id='.(int)$id);
				$db->setQuery($q);
				if(!$db->query()){
					vmdebug('CF4All Error deleting Value\'s connections: ',$id.' '.$row->getError());
				}
				$q->clear();
			}
		}
	}



	/**
	 * Store the product-custom value id associations
	 * Multi
	 *
	 * @param 	array 	$customvalue_ids used by that product - the id of the cf4all value
	 * @param 	int 	$product_id
	 * @param	int 	$customfield_id represents the row of the record from the table virtuemart_product_customfields
	 * @since	1.0
	 */
	public function storeProductValues($value_ids,$product_id, $customfield_id){
		if(empty($customfield_id))return;
		$value_ids=array_filter($value_ids);
		$counter=count($value_ids);
		$db=JFactory::getDbo();
		foreach ($value_ids as $id){
			//check if the combination product_id - custom_value_id exist in the db table
			$q=$db->getQuery(true);
			$q->select('customsforall_value_id,customfield_id');
			$q->from('#__virtuemart_product_custom_plg_customsforall');
			$q->where('customsforall_value_id='.(int)$id);
			$q->where('virtuemart_product_id='.(int)$product_id.' LIMIT 1');
			//$q->where('row='.(int)$row);
			$db->setQuery($q);
			//vmdebug((string)$q);
			$result=$db->loadObject();

			//vmdebug('CF4All result:',$result);
			if(empty($result)){//does not exist, so insert it
				$this->storeProductValue($id,$product_id,$customfield_id);
			}else{
				//exists for that product but with another customfield record. Updated it and put it here
				if($result->customfield_id !=$customfield_id)$this->storeProductValue($id,$product_id,$customfield_id,'update');
			}
			unset($id);
		}
	}

	/**
	 * Store the product-custom value id assoc
	 * Single
	 *
	 * @param int $value_id
	 * @param int $product_id
	 * @since	1.0
	 */
	protected function storeProductValue($value_id,$product_id,$customfield_id,$type='insert'){
		$db=JFactory::getDbo();
		$q=$db->getQuery(true);


		if($type=='insert'){
			$recordObject=new stdClass();
			$recordObject->customsforall_value_id=(int)$value_id;
			$recordObject->virtuemart_product_id=(int)$product_id;
			$recordObject->customfield_id=(int)$customfield_id;
			$db->insertObject('#__virtuemart_product_custom_plg_customsforall', $recordObject);

		}else{
			$q->update('#__virtuemart_product_custom_plg_customsforall');
			$q->set('customfield_id='.(int)$customfield_id);
			$q->where('customsforall_value_id='.(int)$value_id);
			$q->where('virtuemart_product_id='.(int)$product_id);
			//vmdebug((string)$q);
			$db->setQuery($q);
			if(!$db->query()){
				vmdebug('CF4All Error updating product connection: ',$value_id.' '.$db->getErrorMsg());
				return false;
			}
		}


		return true;
	}

	/**
	 * Delete the product's custom values which are other than the supplied $value_ids
	 *
	 * @param	int 	$product_id
	 * @param 	array 	$value_ids
	 * @since	1.0
	 */
	public static function deleteProductValues($product_id,$value_ids=array()){

		if(count($value_ids>0)){
			$value_ids=array_filter($value_ids);
			JArrayHelper::toInteger($value_ids); //sanitize them
		}

		$db=JFactory::getDbo();
		$q="
			DELETE prd_v FROM #__virtuemart_product_custom_plg_customsforall AS prd_v
			INNER JOIN #__virtuemart_custom_plg_customsforall_values AS val ON val.customsforall_value_id=prd_v.customsforall_value_id
			WHERE prd_v.virtuemart_product_id=".(int)$product_id;

		if(count($value_ids)>0)$q.=" AND prd_v.customsforall_value_id NOT IN(".implode(',',$value_ids).")";
		$db->setQuery($q);

		if(!$db->query()){
			vmdebug('CF4All Error deleting product customforall connections: ',$db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Delete all the connections of a product for using custom ids.
	 * Delete all the values which do not belong to the supplied fields (custom ids)
	 *
	 * @param	int 	$product_id
	 * @param 	array 	$value_ids
	 * @since	1.0
	 */
	public static function deleteAllProductAssociations($product_id,$custom_ids_to_product=false){

		if(!empty($custom_ids_to_product)){
			$custom_ids_to_product=array_filter($custom_ids_to_product);
			JArrayHelper::toInteger($custom_ids_to_product); //sanitize them
		}

		$db=JFactory::getDbo();
		$where=array();

		if(!empty($custom_ids_to_product) && is_array($custom_ids_to_product))$where[]="val.virtuemart_custom_id NOT IN(".implode(',',$custom_ids_to_product).")";
		$where[]="prd_v.virtuemart_product_id=".(int)$product_id;

		$q="
			DELETE prd_v FROM #__virtuemart_product_custom_plg_customsforall AS prd_v
			INNER JOIN #__virtuemart_custom_plg_customsforall_values AS val ON val.customsforall_value_id=prd_v.customsforall_value_id
			WHERE ".implode(" AND ", $where);

		$db->setQuery($q);
		if(!$db->query()){
			vmdebug('CF4All Error deleting all product connections: ',$value_id.' '.$db->getErrorMsg());
			return false;
		}
		return true;

	}



	/**
	 * Get and return an array which contains only a specified field from an associative array
	 *
	 * @param 	array $array
	 * @param 	string $field
	 * @since	1.0
	 */
	public static function getField($array,$field='customsforall_value_id'){
		$new_array=array();
		foreach ($array as $el){
			$new_array[]=$el[$field];
		}
		return $new_array;
	}



	/**
	 * Gets an assoc array and checks if the value of each array element exists in a db table
	 * If exists unset it.
	 * The returned array is usefull for insertion in the db of new rows.
	 * 
	 * @param 	array $array - An assoc array containing the same fields with the given db table
	 * @param 	string $table - The db table to which it refers
	 * @param 	string $comparison_field - The array key and the db field name to compare
	 * @since	1.0
	 */
	public function findExistingUnsetDuplicates(&$array,$comparison_field='customsforall_value_name'){
		/*Atm it works only for the 'virtuemart_custom_plg_customsforall_values' tabele as it is using its getCustomValues function.
		 * To be used for other tables in the future select query should be called here
		 */
		$db_rows=$this->getCustomValues($comparison_field);
		vmdebug('CF4All existing custom values ', implode(',',$db_rows));
		$existing_ids=array();

		if(empty($db_rows))return  $existing_ids;   //nothing in the db so all are valid

		foreach ($array as $key=>&$el){
			if(is_array($el[$comparison_field]))$el2=implode('|', $el[$comparison_field]);
			else $el2=trim($el[$comparison_field]);

			$db_custom_value_id=array_search($el2, $db_rows);

			if($db_custom_value_id!==false){
				$existing_ids[]=$db_custom_value_id;
				vmdebug('CF4All Custom Value exists in the db: ',$el[$comparison_field]);
				unset($array[$key]);
			}
		}
		return $existing_ids;
	}
	
	/**
	 * Gets an array of objects and checks for duplicates
	 * Returns only uniques records based on the $comparison_field
	 * 
	 * @param array 	$array
	 * @param string 	$comparison_field
	 * 
	 * @since	3.0
	 */
	public function array_unique($array, $comparison_field='id'){
		$new_array=array();
		if(!empty($array)){			
			foreach ($array as $el){
				if(!isset($new_array[$el->$comparison_field]))$new_array[$el->$comparison_field]=$el;
			}
		}
		return $new_array;
	}

	/**
	 * Function that checks if this is the 1st customforall in this product form
	 *
	 * @param int $custom_id
	 * @param int $row
	 * @param array $data
	 * @since	1.0
	 */
	public function isFirstCustomOfType($row,$data){
		$jinput=JFactory::getApplication()->input;
		$custom_plugins=$jinput->get($this->_paramName,array(),'array');
		$first='';
		//$is_first=false;

		foreach($custom_plugins as $key=>$plg){
			$this_plg_custom_id=$data['field'][$key]['virtuemart_custom_id'];
			if(isset($plg['customfieldsforall'])){
				$first=$plg['customfieldsforall']['row'];
				break;
			}
		}
		if($first==$row)return true;
		else return false;
	}

	/**
	 * Get the params of a plugin with a given id
	 *
	 * @param int $custom_id
	 * @since 1.0
	 */
	public function getCustomfieldParams($custom_id){
		if(empty($custom_id))return array();
		if(empty (self::$_customparams[$custom_id])){
			$db=JFactory::getDbo();
			$q=$db->getQuery(true);
			$q->select('custom_params');
			$q->from('#__virtuemart_customs');
			$q->where('virtuemart_custom_id='.(int)$custom_id);
			$db->setQuery($q);
			$custom_params=$db->loadResult();

			if(empty($custom_params))return false;
			$custom_param_array=explode('|', $custom_params);
			$params_array=array();
			foreach ($custom_param_array as $var){
				$values=explode('=',$var);

				if(isset($values[0])&& isset($values[1])){
					$params_array[$values[0]]=json_decode($values[1]);//removes the double quotes
				}
				unset($values);
			}
			self::$_customparams[$custom_id]=$params_array;
		}
		return self::$_customparams[$custom_id];
	}


	/**
	 * Check if any customforall is set for this product
	 *
	 * @param int $product_id
	 * @since	1.0
	 */
	public function isCustomSetInProduct($product_id){
		if(!isset(self::$_isCustomSetInProduct[$product_id])){
			$jinput=JFactory::getApplication()->input;
			$custom_plugins=$jinput->get($this->_paramName,array(),'array');
			$found=false;

			foreach($custom_plugins as $key=>$plg){
				//check if a customforall plugin exists in the product
				if(isset($plg['customfieldsforall']))$found=true;
			}
			self::$_isCustomSetInProduct[$product_id]=$found;
		}
		return self::$_isCustomSetInProduct[$product_id];
	}

	/**
	 * Check if there is any customforall already stored for this product
	 * The function is usefull for scripts loading where we should use different approach when there is a storage
	 * And different when the custom field is created on the fly with ajax
	 *
	 * @param 	int $product_id
	 * @since	1.0
	 */
	public function isCustomStoredInProduct($product_id){
		$key=$product_id;
		if(empty(self::$_customStored[$key])){
			$db=JFactory::getDbo();
			$q=$db->getQuery(true);
			$q->select('1');
			$q->from('#__virtuemart_product_custom_plg_customsforall AS prd_forall');
			$q->innerJoin('#__virtuemart_product_customfields AS prd_customs ON prd_customs.virtuemart_customfield_id=prd_forall.customfield_id');
			$q->where('prd_forall.virtuemart_product_id='.(int)$product_id.' LIMIT 1');
			$db->setQuery($q);
			//vmdebug('Query',(string)$q);
			self::$_customStored[$key]=$db->loadResult();
		}
		return self::$_customStored[$key];
	}

	/**
	 * If the current record has no customfield we should get that from the db (virtuemart_product_customfields)
	 * New records has no customfield id as this is not included in the POST variabe (from the product form)
	 * Although since vm stores first the customfield to the product_customfield table and then calls the plugins
	 * we can get it using its position
	 *
	 * @param 	int $custom_id
	 * @param 	int $product_id
	 * @param 	int $position - The position of our customfield record
	 * @since	1.0
	 */
	function getVmProductCustomfieldId($product_id,$position){
		$db=JFactory::getDbo();
		$q=$db->getQuery(true);
		$q->select('virtuemart_customfield_id');
		$q->from('#__virtuemart_product_customfields');
		$q->where('virtuemart_product_id='.(int)$product_id);
		$q->where('virtuemart_custom_id='.(int)$this->_custom_id);
		$q->order('ordering ASC');
		$db->setQuery($q);
		//vmdebug('Query',(string)$q);
		$custom_field_ids=$db->loadObjectList();

		foreach ($custom_field_ids as $counter=>$cf){
			//vmdebug('Cf4all virtuemart_customfield_id for position:'.$position.' is: ',$cf->virtuemart_customfield_id);
			if($counter==$position)return $cf->virtuemart_customfield_id;
		}
		return false;
	}

	/**
	 * Loads the necessary scripts and filesfor the customfields to the page
	 *
	 * @since	1.0
	 */
	public function loadStylesScripts(){

		$jinput=JFactory::getApplication()->input;
		$view=$jinput->get('view','','STRING');
		$scripts_loaded=$jinput->get('scripts_loaded',false,'BOOLEAN');

		//if the scripts are not already loaded
		if(!$scripts_loaded){
			$is_custom_view=false;
			$is_stored=false;

			if(!empty($virtuemart_custom_id) && $view=='custom')$is_custom_view=true;
			else{
				$virtuemart_product_id=$jinput->get('virtuemart_product_id',array(),'ARRAY');
				$virtuemart_product_id=end($virtuemart_product_id);
				$is_stored=$this->isCustomStoredInProduct($virtuemart_product_id);
			}

			/*
			 * If there is a record in the custom view we can load it to the document
			 * if there is already a record to the product we can load it to the document
			 */
			if((!empty($this->_custom_id) && $is_custom_view) || (!$is_custom_view && $is_stored)){
				$color_script_uri=JURI::root().'plugins/vmcustom/customfieldsforall/assets/js/jscolor/jscolor.js';
				$css_be_uri=JURI::root().'plugins/vmcustom/customfieldsforall/assets/css/customsforall_be.css';
				$document=JFactory::getDocument();
				$document->addScript($color_script_uri);
				$document->addStyleSheet($css_be_uri);
				$jinput->set('scripts_loaded',true);
			}
		}
	}

	/**
	 * Get the product id based on the customfield_id of the table virtuemart_product_customfields
	 *
	 * @param 	int $customfield_id
	 * @since	1.4.0
	 */
	function getProductFromCustomfield_id($customfield_id){
		$db=JFactory::getDbo();
		$q=$db->getQuery(true);
		$q->select('virtuemart_product_id');
		$q->from('#__virtuemart_product_customfields');
		$q->where('virtuemart_customfield_id='.(int)$customfield_id);
		$db->setQuery($q);
		//vmdebug('Query',(string)$q);
		$product_id=$db->loadResult();
		return $product_id;
	}


	/**
	 * Generates the html code for the custom values
	 *
	 * This function can be used by any page that displays static/non selectable custom fields
	 * e.g. Product Details page, Cart, Orders, Invoices
	 *
	 * @param 	object 	$option The custom value object containing the value and the name
	 * @param	string	The display type
	 * @return	string	The html code of a custom option
	 */
	public static function displayCustomValue($option, $display_type='button', $class='cf4all_color_btn_medium', $inline_css=false,$display_color_label=false){
		if(empty($option->customsforall_value_name))return;
		$filterInput=CustomfieldsForAllFilter::getInstance(); //filter
		$style='';
		$label='';
		$outside_label='';

		if($display_type=='color' || $display_type=='color_multi'){
			$custom_value_name_multi=explode('|', $option->customsforall_value_name);
			$label_html='';
			$count_multi_values=count($custom_value_name_multi);
			$width=100/$count_multi_values;

			foreach($custom_value_name_multi as $custom_value_name){
				//validate hex color values
				$color=$filterInput->checkNFormatColor($custom_value_name);
				if(empty($color))return;
				$label_style='background-color:'.$color.'; width:'.$width.'%; display:inline-block';
				$label_html.='<div class="cf4all_inner_value" style="'.$label_style.'"></div>';
			}
			if($inline_css)$style.='height: 0.9em; font-weight: 500; border: 1px solid #474949; color:#ffffff; text-shadow:-1px 1px #444444; display:inline-block;';
			$outside_label='';
			if($display_color_label){
				if(!empty($option->customsforall_value_label)){
					$outside_label.=JText::_($option->customsforall_value_label);
					if($count_multi_values==1) $outside_label.=' ('.$option->customsforall_value_name.')';
				}
				else $outside_label.='('.$option->customsforall_value_name.')';
			}
			$class.=' cf4all_color_btn';
			$label=$label_html;
		}else{
			$class='';//class used only for colors. Otherwise it will be text following the styling of every text
			$label=JText::_($option->customsforall_value_name);
		}
		if(!empty($outside_label))$outside_label='<span class="cf4all_outside_label">'.$outside_label.'</span>';
		if($style)$style='style="'.$style.'"';
		$html='<span class="cf4all_option '.$class.'" id="cf4all_option_'.$option->customsforall_value_id.'" '.$style.'>'.$label.'</span> '.$outside_label;
		return $html;
	}
}