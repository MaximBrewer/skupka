<?php

defined ('_JEXEC') or die();
jimport('joomla.form.formfield');
class JFormFieldSendcloudshipping extends JFormFieldList {

	
	var $_name = 'sendcloudshipping';

	function getOptions () { 
	
		//$name, $value, &$node, $control_name
		$helper_file = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'helper.php'); 
		require_once($helper_file); 
		
		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->getVar('cid');
		if (is_array($cid))
		{
			$cid = reset($cid); 
		}
		$cid = (int)$cid; 
		
		
		$options = array(); 
		$name = $this->name; 
		$value = $this->value; 
		$control_name = 'params'; 
		
		
		$obj = SendcloudHelper::getFirstMethod($cid); 
		
		
		 
		 
		$methods = SendcloudHelper::getShippingMethods($obj); 

		
		
		
		
		
		
		$html = ''; 
		
		
		if (!empty($methods))
		 {
		    
				
			
				
		   $k=1;
		   foreach ($methods as $p)
		     {
								$c = array(); 
								foreach ($p['countries'] as $c2)
								{
									$c[] = $c2['iso_2']; 
								}
								$chtml = ' ('.implode(',', $c).')'; 
								$option_name = $p['name'].$chtml; 
								$option_value = $id = $p['id']; 
								$extra = SendcloudHelper::getShippingMethods($obj, $id); 
								
								
								$options[] = JHtml::_('select.option', $option_value, $option_name);
								
								
								
								
			 }
		 }
		
		return $options; 
	}

}