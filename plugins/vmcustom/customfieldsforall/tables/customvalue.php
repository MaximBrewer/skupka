<?php
/**
 * @version		$Id: customvalues.php 2013-07-2 20:32 sakis Terz $
 * @package		customfieldsforall
 * @copyright	Copyright (C)2013 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

class TableCustomvalue extends JTable   {
	/**
	 * Constructor
	 *
	 * @since   1.0
	 */
	public function __construct(&$_db)
	{
		parent::__construct('#__virtuemart_custom_plg_customsforall_values', 'customsforall_value_id', $_db);

	}
	
	public function check()
	{			
		$k = $this->_tbl_key;//the primary key
		$this->virtuemart_custom_id=(int)$this->virtuemart_custom_id;		
		$this->ordering=(int)trim($this->ordering);
		//vmdebug('Ordering for: '.$this->customsforall_value_id.' is equal:',$this->ordering);
		
		if(empty($this->ordering)){ 
			//get the max ordering and use it for that record if its new
			if (!$this->$k){
				$db=$this->getDbo();
				$table_name=$db->quoteName($this->getTableName());
				$q="SELECT MAX(ordering) FROM $table_name WHERE virtuemart_custom_id=$this->virtuemart_custom_id";
				$db->setQuery($q);
				$ordering=(int)$db->loadResult();
				
				if($ordering){
					$this->ordering=$ordering+=1;
				}
			}
		}
		return true;
	}
}