<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/



defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JControllerUtils extends JControllerBase
{
	
	function add_product_sku_index() {
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		
		$msg = 'Index already exists'; 
		if (!OPCmini::hasIndex('#__virtuemart_products', 'product_sku', true)) {
			$msg = OPCmini::addIndex('#__virtuemart_products', array('product_sku'), true); 
		}
		
		
		$this->setRedirect('index.php?option=com_onepage&view=utils', $msg); 
	}
	function remove_product() {
		
		if ($this->checkPerm()) {
			
			$virtuemart_product_id = JRequest::getInt('virtuemart_product_id', 0); 
			
			if (!empty($virtuemart_product_id)) {
			$model = $this->getModel('utils');
			$model->runRemoveProducts($virtuemart_product_id); 
			}
			$selected_virtuemart_product_id = JRequest::getVar('selected_virtuemart_product_id'); 
			if (!empty($selected_virtuemart_product_id)) {
			foreach ($selected_virtuemart_product_id as $k=>$v) {
				$selected_virtuemart_product_id[$k] = (int)$v; 
				if (empty($v)) {
					unset($selected_virtuemart_product_id[$k]); 
				}
			}
			}
			if (!empty($selected_virtuemart_product_id)) {
				$model = $this->getModel('utils');
				$model->runRemoveProducts($selected_virtuemart_product_id); 
			}
			
		}
		$this->setRedirect('index.php?option=com_onepage&view=utils&task=get_skus', $msg); 
	}
	
	
	
	
	//add_product_sku_index
	function get_skus() {
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		
		OPCmini::setVMLANG(); 
		ob_start(); 
		?><script>function deleteOne(id) {
			var found = false; 
		   jQuery('.selected_prods').each( function() { 
		   
		   console.log(this.value); 
		   if (parseInt(this.value) !== id) {
		   this.checked = false; } else {
			   this.checked = true; 
			   found = true; 
			   console.log('found !'); 
			   }
		   })
			   if (found) return true; 
			   return false; 
		}
		function deleteAll() {
			
		   jQuery('.selected_prods').each( function() { 
		   
		   console.log(this.value); 
		   
		   this.checked = true; 
		   })
			
			   return true; 
		}
		</script>
		<?php
		$js = ob_get_clean(); 
		
		$removeproduct = '<input type="submit" class="btn button btn-primary" value="Delete" onclick="return deleteOne({virtuemart_product_id})"/>'; 
		$db = JFactory::getDBO(); 
		$q = 'select product_sku, count(product_sku) as c from #__virtuemart_products group by product_sku having( count(product_sku) > 1)'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) {
			
			$msg .= '<form style="margin-left: 100px;" action="index.php" method="post"><input type="hidden" name="option" value="com_onepage" /><input type="hidden" name="task" value="remove_product" /><input type="hidden" name="view" value="utils" />Duplicate SKUs: <br />'.$js; 
			foreach ($res as $row) {
				
				$q = 'select virtuemart_product_id from #__virtuemart_products where product_sku = \''.$db->escape($row['product_sku']).'\''; 
				$db->setQuery($q); 
				$products = $db->loadAssocList(); 
				if (empty($row['product_sku'])) $row['product_sku'] = '(empty sku)'; 
				foreach ($products as $ro2) {
					
					$q = 'select product_name from `#__virtuemart_products_'.VMLANG.'` where virtuemart_product_id = '.$ro2['virtuemart_product_id']; 
					$db->setQuery($q); 
					$product_name = $db->loadResult(); 
					
					$q = 'select count(virtuemart_product_id) from #__virtuemart_products where product_parent_id = \''.$db->escape($ro2['virtuemart_product_id']).'\''; 
					$db->setQuery($q); 
					$count = $db->loadResult(); 
					$rm = str_replace('{virtuemart_product_id}', $ro2['virtuemart_product_id'],$removeproduct); 
					$msg .= '<input type="checkbox" class="selected_prods" name="selected_virtuemart_product_id[]" value="'.$ro2['virtuemart_product_id'].'" id="selected_'.$ro2['virtuemart_product_id'].'" /><label for="selected_'.$ro2['virtuemart_product_id'].'">'.$product_name.': '.$row['product_sku'].' (duplicates: '.$row['c'].') number of children: ('.$count.') <a href="index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id='.$ro2['virtuemart_product_id'].'">link</a></label>'.$rm.'<br />'; 
				}
				
				
				
			}
			$msg .= '<input type="submit" class="btn button btn-primary" value="Delete all selected " /><br />
			<input type="submit" class="btn button btn-primary" value="Delete all listed - DANGEROUS ! " onclick="return deleteAll()"/>
			</form>'; 
		}
		else {
			$msg = 'No duplicate SKUs found !'; 
		}
		$this->setRedirect('index.php?option=com_onepage&view=utils', $msg); 
	}
	function pair()
	{
		$data = JRequest::getVar('pair_multi_ids'); 
		$model = $this->getModel('utils');
		$groups = $model->buildProducts(); 
		ob_start(); 
		foreach ($data as $what)
		{
		 $model->pairSingle($what, $groups); 
		}
		$d = ob_get_clean(); 
		
		$cid = (int)JRequest::getVar('virtuemart_category_id', 0); 
		$min = (int)JRequest::getVar('min', 2); 
		$this->setRedirect('index.php?option=com_onepage&view=utils&childpairing=1&virtuemart_category_id='.(int)$cid.'&min='.$min, $d); 
		
	}
	function removeusers() {
		if ($this->checkPerm()) {
		$db = JFactory::getDBO(); 
		$q = 'select `id` from #__users as u, #__user_usergroup_map as m where m.user_id = u.id and m.group_id = 2 and NOT EXISTS (select `virtuemart_user_id` from #__virtuemart_userinfos as vu where vu.virtuemart_user_id = u.id) '; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		$c = 0; 		
		if (!empty($res)) {
		foreach ($res as $row) {
		$id = (int)$row['id']; 	
		$c++; 
		$q = 'delete from `#__users` where `id` = '.$id; 
		$db->setQuery($q); 
		$db->query(); 
		
		$q = 'delete from `#__user_usergroup_map` where `user_id` = '.$id; 
		$db->setQuery($q); 
		$db->query(); 
		
		}
		}
		$d = 'Removed '.$c.' users !'; 
		}
		$this->setRedirect('index.php?option=com_onepage&view=utils', $d); 
	}
	
	function removeFK($table, &$msg='') {
		
		$db = JFactory::getDBO();
		$config = JFactory::getConfig(); 		
		
		static $r; 
		if (!isset($r)) {
		$q = 'SELECT * FROM information_schema.TABLE_CONSTRAINTS where CONSTRAINT_TYPE = \'FOREIGN KEY\''; 
		$db->setQuery($q); 
		$r = $db->loadAssocList();
		if (empty($r)) $r = array(); 
		}
		
		foreach ($r as $row) {
			$tableX = $row['TABLE_NAME']; 
			if ($tableX == $table) {
				
				$key = $row['CONSTRAINT_NAME']; 
				$q = 'alter table `'.$db->escape($table).'` drop foreign key '.$db->escape($key); 
				try { 
				 $db->setQuery($q); 
				 $db->query(); 
				 
				 $msg .= $q."<br />\n"; 
				}
				catch (Exception $e) {
					$msg .= (string)$e."<br />\n"; 
				}
			}
		}
		
		
	}
	
	function searchusers() {
		if ($this->checkPerm()) {
	$db = JFactory::getDBO(); 
//$q = 'select `virtuemart_user_id` from #__virtuemart_userinfos as u, #__users as ju, #__user_usergroup_map as m where m.user_id = ju.id and m.group = 2 and u.virtuemart_user_id = ju.virtuemart_user_id '; 
$q = 'select `id`, `username`, `email` from #__users as u, #__user_usergroup_map as m where m.user_id = u.id and m.group_id = 2 and NOT EXISTS (select `virtuemart_user_id` from #__virtuemart_userinfos as vu where vu.virtuemart_user_id = u.id) '; 
$db->setQuery($q); 
$res = $db->loadAssocList(); 
$d = ''; 
if (!empty($res)) {
	$c = count($res); 
	$d .= 'Count: '.$c."<br />"; 
	foreach ($res as $row) {
		$d .= $row['id'].' '.htmlspecialchars($row['username']).' '.htmlspecialchars($row['email'])."<br />"; 
	}
}
	 if (empty($d)) $d = 'OK, no users without orders in your system !'; 

		}
		
		$this->setRedirect('index.php?option=com_onepage&view=utils', $d); 


	}
	function pairSingle()
	{
		$what = JRequest::getVar('what'); 
		$model = $this->getModel('utils');
		ob_start(); 
		$model->pairSingle($what); 
		$d = ob_get_clean(); 
		$cid = (int)JRequest::getVar('virtuemart_category_id', 0); 
		$min = (int)JRequest::getVar('min', 2); 
		$this->setRedirect('index.php?option=com_onepage&view=utils&childpairing=1&virtuemart_category_id='.(int)$cid.'&min='.$min, $d); 
	}
	
	
	
	function createchilds()
	{
		$cid = (int)JRequest::getVar('virtuemart_category_id', 0); 
		$min = (int)JRequest::getVar('min', 2); 
		$this->setRedirect('index.php?option=com_onepage&view=utils&childpairing=1&virtuemart_category_id='.(int)$cid.'&min='.$min); 
	}
	
	function to_parent_cats() {
	    $model = $this->getModel('utils');
		ob_start(); 
		$model->to_parent_cats(); 
		$d = ob_get_clean(); 
		
		$this->setRedirect('index.php?option=com_onepage&view=utils', $d); 
	}
	
	
	function removeproducts() {
	  $reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils';
	  if ($this->checkPerm()) {
		    $model = $this->getModel('utils');
			$reply = $model->removeproducts(); 
			if (empty($reply))
			$reply = JText::_('COM_ONEPAGE_OK'); 
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
	function removecategories() {
		$reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils#productcopy';
	  if ($this->checkPerm()) {
		    $model = $this->getModel('utils');
			$reply = $model->removecategories(); 
			if (empty($reply))
			$reply = JText::_('COM_ONEPAGE_OK'); 
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
	function grouproductstocategories() {
		$reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils#productcopy';
	  if ($this->checkPerm()) {
		    $model = $this->getModel('utils');
			$reply = $model->grouproductstocategories(); 
			if (empty($reply))
			$reply = JText::_('COM_ONEPAGE_OK'); 
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
	function copyproducts() {
	  $reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils#productcopy';
	  if ($this->checkPerm()) {
		    $model = $this->getModel('utils');
			$reply = $model->copyLangTable('products', 'virtuemart_product_id'); 
			if (empty($reply))
			$reply = JText::_('COM_ONEPAGE_OK'); 
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
	
	function copymanufs() {
	  $reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils#productcopy';
	  if ($this->checkPerm()) {
		    $model = $this->getModel('utils');
			$reply = $model->copyLangTable('manufacturers', 'virtuemart_manufacturer_id'); 
			if (empty($reply))
			$reply = JText::_('COM_ONEPAGE_OK'); 
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
	function copycategories() {
	  $reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils#categorycopy';
	  if ($this->checkPerm()) {
		    $model = $this->getModel('utils');
			$reply = $model->copyLangTable('categories', 'virtuemart_category_id'); 
			if (empty($reply))
			$reply = JText::_('COM_ONEPAGE_OK'); 
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
	
	function copypayments() {
	  $reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils#paymentcopy';
	  if ($this->checkPerm()) {
		    $model = $this->getModel('utils');
			$reply = $model->copyLangTable('paymentmethods', 'virtuemart_paymentmethod_id'); 
			if (empty($reply))
			$reply = JText::_('COM_ONEPAGE_OK'); 
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
	function copyshipments() {
	  $reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils#shipmentcopy';
	  if ($this->checkPerm()) {
		    $model = $this->getModel('utils');
			$reply = $model->copyLangTable('shipmentmethods', 'virtuemart_shipmentmethod_id'); 
			if (empty($reply))
			$reply = JText::_('COM_ONEPAGE_OK'); 
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
	
	function to_default_taxrules() {
	  $reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils';
	  if ($this->checkPerm()) {
			$db = JFactory::getDBO(); 
			$q = 'update `#__virtuemart_product_prices` set product_tax_id = 0 WHERE 1 limit 99999999';
			$db->setQuery($q); 
			$db->query(); 
			$reply = JText::_('COM_ONEPAGE_OK'); 
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
	 function checkPerm() {
	   $user = JFactory::getUser(); 
	   
      $isroot = $user->authorise('core.admin');	
	  
	  if (!$isroot) 
	  {
		if (!empty($_FILES))
		foreach ($_FILES as $f) {
		  if (!empty($f['tmp_name'])) {
		    if (file_exists($f['tmp_name'])) {
			  unlink($f['tmp_name']); 
			}
		  }
		}
	    $msg = JText::_('COM_ONEPAGE_PERMISSION_DENIED'); 
		JFactory::getApplication()->enqueueMessage($msg); 
		return false; 
	  }
	  
	  $iss = JFactory::getApplication()->isSite(); 
	  if (!empty($iss)) return false; 
	  
	  return true; 
   }
	
	//export_prices
	function export_prices()
	{
	  $reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils';
	  if ($this->checkPerm()) {
       $model = $this->getModel('utils');
	   $reply = $model->export_prices();
	  }
	  
	  $this->setRedirect($link, $reply);	
    
	}
	function cat_prod_copy() {
		 $reply = ''; 
		 
		 $dest_cat = (int)JRequest::getInt('dest_cat', 0); 
		 $source_cat = (int)JRequest::getInt('source_cat', 0); 
		 $action_cat = (int)JRequest::getInt('action_cat', 0); 
		 
	  $link = 'index.php?option=com_onepage&view=utils&dest_cat='.$dest_cat.'&source_cat='.$source_cat.'&action_cat='.$action_cat;
	  
	  if (!empty($dest_cat)) 
	  if ($this->checkPerm()) {
       $model = $this->getModel('utils');
	   $reply = $model->cat_prod_copy($source_cat, $dest_cat, $action_cat);
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
	function fix_img_filenames() {
	 $reply = ''; 
		 
		 
		 
	  $link = 'index.php?option=com_onepage&view=utils';
	  
	 
	  if ($this->checkPerm()) {
       $model = $this->getModel('utils');
	   $reply = $model->fix_img_filenames();
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
	
    function csv_upload_product() 
    {
		 $reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils';
	  if ($this->checkPerm()) {
       $model = $this->getModel('utils');
	   $reply = $model->csv_upload_product();
	  }
	  
	  $this->setRedirect($link, $reply);	
	}
    function csv_upload()
	{
	  $reply = ''; 
	  $link = 'index.php?option=com_onepage&view=utils';
	  if ($this->checkPerm()) {
       $model = $this->getModel('utils');
	   $reply = $model->csv_upload();
	  }
	  
	  $this->setRedirect($link, $reply);	
    
	}
	
	
     function getViewName() 
	{ 
		return 'utils';		
	} 

   function getModelName() 
	{		
		return 'utils';
	}
	function movemenu()
	{
	  $model = $this->getModel('utils');
	  $msg = $model->movemenu(); 
	  $link = 'index.php?option=com_onepage&view=utils';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	  
	}
	
	
	//vmmyisam tomyisam vminnodb toinnodb tooriginal
		function tooriginal()
	{
	  $msg = ''; 
	  $tables = $original = $definitions = array(); 
	  $this->getTables($tables, $definitions, $original); 
	  $db = JFactory::getDBO(); 
	  $prefix = $db->getPrefix(); 
	  $only_if_dif = JRequest::getVar('only_if_dif', false); 
	  foreach ($tables as $table)
	  {
	    
		
		
	    if (!isset($original[$table])) continue; 
		
		if ($only_if_dif)
	  {
	    if ($definitions[$table] === $original[$table]) continue; 
	  }
		
	    $q = 'ALTER TABLE `'.$table.'` ENGINE='.$original[$table]; 
		try
		{
			$fc0 = 'SET FOREIGN_KEY_CHECKS=0'; 
			$fc1 = 'SET FOREIGN_KEY_CHECKS=1'; 
			//SET FOREIGN_KEY_CHECKS=0; -- to disable them
			//SET FOREIGN_KEY_CHECKS=1; -- to re-enable them
			$db->setQuery($fc0); $db->query(); 
			
			
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg(); 
		
		$db->setQuery($fc1); $db->query(); 
		
		}
		catch (Exception $e)
		 {
		   $e = (string)$e; 
		 }
		 
		 //$msg .= $q."<br />\n"; 
		 
		 if (!empty($e))
		 {
		   $msg .= $e."<br />\n"; 
		 }
		 
		 
	  }
	  $link = 'index.php?option=com_onepage&view=utils';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	 
	}

	function vmmyisam()
	{
	  $msg = ''; 
	  $tables = $original = $definitions = array(); 
	  $this->getTables($tables, $definitions, $original); 
	  $db = JFactory::getDBO(); 
	  $prefix = $db->getPrefix(); 
	    $only_if_dif = JRequest::getVar('only_if_dif', false); 
	  foreach ($tables as $table)
	  {
	    if (stripos($table, $prefix.'virtuemart')!==0)  continue; 
		
			if ($only_if_dif)
	  {
	    if ($definitions[$table] === 'MyISAM') continue; 
	  }
		$table = str_replace('#__', $prefix, $table); 
		$this->removeFK($table, $msg); 
		
	    $q = 'ALTER TABLE `'.$table.'` ENGINE=MyISAM'; 
		try
		{
			
			$fc0 = 'SET FOREIGN_KEY_CHECKS=0'; 
			$fc1 = 'SET FOREIGN_KEY_CHECKS=1'; 
			//SET FOREIGN_KEY_CHECKS=0; -- to disable them
			//SET FOREIGN_KEY_CHECKS=1; -- to re-enable them
			$db->setQuery($fc0); $db->query(); 
			
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg(); 
		
			$db->setQuery($fc1); $db->query(); 
		
		}
		catch (Exception $e)
		 {
		   $e = (string)$e; 
		 }
		 
		 if (!empty($e))
		 {
		   $msg .= $e."<br />\n"; 
		 }
	  }
	  $link = 'index.php?option=com_onepage&view=utils';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	 
	}
	
	function tomyisam()
	{
	
	  $msg = ''; 
	  $tables = $original = $definitions = array(); 
	  $this->getTables($tables, $definitions, $original); 
	  $db = JFactory::getDBO(); 
	  $prefix = $db->getPrefix(); 
	  $only_if_dif = JRequest::getVar('only_if_dif', false); 
	  
	  foreach ($tables as $table)
	  {
	   // if (stripos($table, $prefix.'virtuemart')!==0)  continue; 
		
			if ($only_if_dif)
	  {
	    if ($definitions[$table] === 'MyISAM') continue; 
	  }
		
		$table = str_replace('#__', $prefix, $table); 
		$this->removeFK($table, $msg); 
		
		
		
	    $q = 'ALTER TABLE `'.$table.'` ENGINE=MyISAM'; 
		try
		{
			
			$fc0 = 'SET FOREIGN_KEY_CHECKS=0'; 
			$fc1 = 'SET FOREIGN_KEY_CHECKS=1'; 
			//SET FOREIGN_KEY_CHECKS=0; -- to disable them
			//SET FOREIGN_KEY_CHECKS=1; -- to re-enable them
			$db->setQuery($fc0); $db->query(); 
			
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg(); 
		
		
		$db->setQuery($fc1); $db->query(); 
		}
		catch (Exception $e)
		 {
		   $e = (string)$e; 
		 }
		 
		 if (!empty($e))
		 {
		   $msg .= $e."<br />\n"; 
		 }
	  }
	  
	 $link = 'index.php?option=com_onepage&view=utils';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	}
	
	function vminnodb()
	{
	  $msg = ''; 
	  $tables = $original = $definitions = array(); 
	  $this->getTables($tables, $definitions, $original); 
	  $db = JFactory::getDBO(); 
	  $prefix = $db->getPrefix(); 
	  $only_if_dif = JRequest::getVar('only_if_dif', false); 
	  foreach ($tables as $table)
	  {
	    if (stripos($table, $prefix.'virtuemart')!==0)  continue; 
		
			if ($only_if_dif)
	  {
	    if ($definitions[$table] === 'InnoDB') continue; 
	  }
		
	    $q = 'ALTER TABLE `'.$table.'` ENGINE=InnoDB'; 
		try
		{
			
			$fc0 = 'SET FOREIGN_KEY_CHECKS=0'; 
			$fc1 = 'SET FOREIGN_KEY_CHECKS=1'; 
			//SET FOREIGN_KEY_CHECKS=0; -- to disable them
			//SET FOREIGN_KEY_CHECKS=1; -- to re-enable them
			$db->setQuery($fc0); $db->query(); 
			
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg(); 
		
			$db->setQuery($fc1); $db->query(); 
		
		}
		catch (Exception $e)
		 {
		   $e = (string)$e; 
		 }
		 
		 if (!empty($e))
		 {
		   $msg .= $e."<br />\n"; 
		 }
	  }
	  
	 $link = 'index.php?option=com_onepage&view=utils';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	}
	function toinnodb()
	{
	  $db = JFactory::getDBO(); 
	  $prefix = $db->getPrefix(); 
	  $msg = ''; 
	  $tables = $original = $definitions = array(); 
	  $this->getTables($tables, $definitions, $original); 
	   $only_if_dif = JRequest::getVar('only_if_dif', false); 
	  foreach ($tables as $table)
	  {
	  
	  if ($only_if_dif)
	  {
	    if ($definitions[$table] === 'InnoDB') continue; 
	  }
	  
	    $q = 'ALTER TABLE `'.$table.'` ENGINE=InnoDB'; 
		try
		{
			
			$fc0 = 'SET FOREIGN_KEY_CHECKS=0'; 
			$fc1 = 'SET FOREIGN_KEY_CHECKS=1'; 
			//SET FOREIGN_KEY_CHECKS=0; -- to disable them
			//SET FOREIGN_KEY_CHECKS=1; -- to re-enable them
			$db->setQuery($fc0); $db->query(); 
			
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg(); 
		
		$db->setQuery($fc1); $db->query(); 
		
		}
		catch (Exception $e)
		 {
		   $e = (string)$e; 
		 }
		 
		 if (!empty($e))
		 {
		   $msg .= $e."<br />\n"; 
		 }
	  }
	  $link = 'index.php?option=com_onepage&view=utils';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	 
	}
	private function getTables(&$tables, &$definitions, &$original)
	{
	
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  $db = JFactory::getDBO(); 
	  
	  $q = 'SHOW TABLES'; 
	  $db->setQuery($q); 
	  $res = $db->loadRowList(); 
	  $ret = array(); 
	  $prefix = $db->getPrefix(); 
	  
	
	  
	  foreach ($res as $k=>$v)
	   {
	      if (isset($v[0]))
		  {
		  if (strpos($v[0], $prefix) === 0)
	      $ret[$v[0]] = $v[0]; 
		  }
	   }
	  
	  $definitions = array(); 
	  
	  foreach ($ret as $k=>$v)
	  {
	    $eng = ''; 
	    $q = 'SHOW CREATE TABLE `'.$v.'`'; 
		$db->setQuery($q); 
		$def = $db->loadAssocList(); 
		if (isset($def[0]['Create Table']))
		{
		  $ct = $def[0]['Create Table']; 
		  if (stripos($ct, '=MyISAM')!==false)
		  $eng = 'MyISAM'; 
	      else
		  if (stripos($ct, '=InnoDB')!==false)
		  $eng = 'InnoDB'; 
		  else
		  {
		    // do not touch tables that have other engines...
		    unset($ret[$v]); 
		  }
		  if (!empty($eng))
		  $definitions[$v] = $eng; 
		}
	    
	  
	  
	 
	   
	   $config = false; 
	   OPCconfig::getValue('table_updater', $v, 0, $config); 	 
	    if ($config === false)
		 {
		   // store the table defintion just once
		   OPCconfig::store('table_updater', $v, 0, $eng); 
		   $original[$v] = $eng; 
		 }
		 else
		 {
		   $original[$v] = $config; 
		 }
		 
		 
		 
		}
	  
	  $tables = $ret; 
	 
	  
	}
	
	
	
	
	function errorlog()
	{
	    $error_log = @ini_get('error_log'); 
		$maxlines = 1000; 
		if (!empty($error_log))
		if (file_exists($error_log))
            {
			   if (function_exists('apache_setenv'))
	@apache_setenv('no-gzip', 1);
	$_ENV['no-gzip'] = 1; 
	header("Content-Type: text/html"); 
	@ignore_user_abort(false);
	if (!isset($timelimit)) $timelimit = 29; 
	@ini_set('ignore_user_abort', false); 
	@set_time_limit($timelimit);
	@ini_set('output_buffering', 0); 
    @ini_set('zlib.output_compression', 0);
	echo '<html><head></head><body>'; 
	// clear any possible buffers: 
	echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); echo @ob_get_clean(); 
	
	//$handle = fopen($error_log, "r") or die("Couldn't get handle");
	$time = time(); 
	echo JText::_('COM_ONEPAGE_VIEWPHPERRORLOG_NOTE')."<br />\n"; 
	echo JText::_('COM_ONEPAGE_VIEWPHPERRORLOG_READING').' '.$error_log."<br />\n"; 
	
	$fl = fopen($error_log, "r") or die('Not found');
	for($x_pos = 0, $ln = 0, $output = ''; fseek($fl, $x_pos, SEEK_END) !== -1; $x_pos--) {
    $char = fgetc($fl);
    if ($char === "\n") {
        // analyse completed line $output[$ln] if need be
       
		
		if (stripos($output, 'Fatal')!==false)
		 {
		   $output = '<b style="color: red;">'.$output.'</b><br />'; 
		 }
		 
		 if (stripos($output, 'Warning')!==false)
		 {
		   $output = '<b style="color: blue;">'.$output.'</b><br />'; 
		 }
		if (empty($output)) continue; 
		$ln++;
	    echo $ln.': '.$output."<br />\n"; 
		$output = ''; 
		flush(); 
        continue;
        }
	 if ($char === "\r") continue; 
     $output = $char . $output; 
    
	 $now = time(); 
     if (($now - $time) > $timelimit) 
	 {
	 fclose($fl);
	 die('Timeout'); 
	 }
	  
	 if ($ln >= $maxlines) 
	 {
	 
	 fclose($fl);
	 die('Max lines reached'); 
	 }
	
	}
   fclose($fl);
	
	
    echo '</body></html>'; 
	die("\nEOF\n"); 
			}
			
			die('Not found'); 
	}

	function searchbom() {
	    $model = $this->getModel('utils');
	  $bom = "\xEF\xBB\xBF"; 
	  $search = $model->searchtext($bom, '*.php'); 
	  $session = JFactory::getSession(); 
	  $session->set('opcsearch', $search); 
	  $link = 'index.php?option=com_onepage&view=utils';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	}
	
	function searchtext()
	{
	  $model = $this->getModel('utils');
	  
	   $os = JRequest::getVar('onlysmall', false); 
	    $xc = JRequest::getVar('excludecache', false); 
	   $cs = JRequest::getVar('casesensitive', false); 
	   $searchext = JRequest::getVar('ext'); 
	   $custom_ext = JRequest::getVar('custom_ext', ''); 
	   if (!empty($custom_ext)) 
	   $searchext = $custom_ext; 
   
	   $searchw = JRequest::getVar('searchwhat', '', 'post','string',	  JREQUEST_ALLOWRAW);
	  $search = $model->searchtext($searchw, $searchext, false, $os, $xc, $cs); 
	  $session = JFactory::getSession(); 
	  $session->set('opcsearch', $search); 
	  $link = 'index.php?option=com_onepage&view=utils&ext='.urlencode($ext).'&os='.(int)$os.'&xc='.(int)$xc.'&cs='.(int)$cs.'&searchwhat='.urlencode($searchw).'&custom_ext='.urlencode($custom_ext).'#fulltextsearch';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	  
	}
	
	function searchtextObs()
	{
	  $model = $this->getModel('utils');
	  
	  $search = $model->searchtext(); 
	  $session = JFactory::getSession(); 
	  $session->set('opcsearch', $search); 
	  $link = 'index.php?option=com_onepage&view=utils';
    
      if (empty($msg)) $msg = 'O.K.';
      $this->setRedirect($link, $msg);
	  
	}
	
	function ajax()
	{
	return;
		$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();$x = @ob_get_clean();
		ob_start(); 
		$model = $this->getModel('utils');
		
		$command = JRequest::getCmd('command'); 
		
		if ($command == 'editcss')
		{
			
			$model = $this->getModel('edittheme');
			$model->updateColors(); 
			
			$file = JRequest::getCmd('file'); 
			$files = $model->getCss(); 
			foreach ($files as $f)
			{
			 if (md5($f)==$file) 
			 {
			 $myfile = $f; 
			 break; 
			  }
			}
			if (!empty($myfile))
			{
			  $myfile2 = strtolower($myfile);
			  if (substr($myfile2, -4)!='.css') return; 
		      echo file_get_contents($myfile); 
			}

			
		}
		
		if ($command == 'savecss')
		{
			 
			$file = JRequest::getCmd('file'); 
			$files = $model->getCss(); 
			foreach ($files as $f)
			{
		     
			 if (md5($f)==$file) 
			 {
			 $myfile = $f; 
			 break; 
			  }
			}
			
			if (!empty($myfile))
			{
			  $myfile2 = strtolower($myfile);
			  if (substr($myfile2, -4)!='.css') return; 
			  {
				 
			     //echo file_get_contents($myfile); 
				  //$html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'default', 'STRING', JREQUEST_ALLOWRAW);
				 $css = JRequest::getVar('css', '', 'post', 'STRING', JREQUEST_ALLOWRAW);
				 if (!empty($css))
				 {
					 $css = str_replace("\r\r\n", "\r\n", $css); 
					 $css = str_replace("\xEF\xBB\xBF", "", $css); 
					
					 JFile::write($myfile, $css); 
					 echo 'OPC_OK'; 
				 }
			  }
			}
		}
		
		if (($command == 'preview') || ($command == 'savepreview'))
		{
		
			$model = $this->getModel('edittheme');
			$model->updateColors(); 
		}
		
		if ($command == 'savepreview')
		{
			$model->createCustom(); 
			
		}
		JFactory::getApplication()->close(); 
		
	}

	
}
