<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
	  
jimport('joomla.form.formfield');
class JFormFieldAvaitostatus extends JFormField
{
  protected $type = 'avaitostatus';
  function getInput()
	{
		
		//debug_zval_dump($this->form->data->get('module')); die(); 
		//var_dump($this->value); var_dump($this->value['custom_field']); die(); 
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		VmConfig::loadJLang('com_virtuemart');
		VmConfig::loadJLang('plg_vmpsplugin', false);
		
		$db = JFactory::getDBO(); 
		$q = 'select virtuemart_custom_id, custom_title from #__virtuemart_customs where published = 1 and field_type = "S" and show_title = 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (empty($res)) return; 
		
		$nm = 'id_'.JFile::makeSafe($this->name); 
		$this->name = str_replace('[]', '', $this->name); 
		$selected_virtuemart_custom_id = 0; 
		$html = '<select name="'.$this->name.'[select]" id="'.$nm.'_select">'; 
		$html .= '<option value="">'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option>'; 
		foreach ($res as $row) {
			
			$row['virtuemart_custom_id'] = (int)$row['virtuemart_custom_id']; 
			
			
			
			$html .= '<option '; 
			if (is_array($this->value)) {
			if (isset($this->value['select'])) {
				$this->value['select'] = (int)$this->value['select']; 
			if ($this->value['select'] === $row['virtuemart_custom_id']) {
				$html .= ' selected="selected" '; 
				$selected_virtuemart_custom_id = (int)$row['virtuemart_custom_id'];
			}
			}
			}
			else {
				if (isset($this->value->select)) {
					$this->value->select = (int)$this->value->select; 
				if ($this->value->select === $row['virtuemart_custom_id']) {
				$html .= ' selected="selected" '; 
				$selected_virtuemart_custom_id = (int)$row['virtuemart_custom_id'];
			}
				}
			}
			$html .= ' value="'.$row['virtuemart_custom_id'].'">'.htmlspecialchars(JText::_($row['custom_title'])).'</option>'; 
		}
		$html .= '</select>'; 
		$html .= '<label for="'.$nm.'_select">Choose custom field to load product values and click save to load them. Set time number which will be used to compare statuses against delivery time and the longest will be selected during the order status update.</label>'; 
		
		// Create an array to allow orderlinestatuses to be translated
			// We'll probably want to put this somewhere in ShopFunctions...
			$orderStatusModel=VmModel::getModel('orderstatus');
			$orderStates = $orderStatusModel->getOrderStatusList();
			$_orderStatusList = array();
			foreach ($orderStates as $orderState) {
				//$_orderStatusList[$orderState->virtuemart_orderstate_id] = $orderState->order_status_name;
				//When I use update, I have to use this?
				$_orderStatusList[$orderState->order_status_code] = JText::_($orderState->order_status_name);
			}
		
		if (!empty($selected_virtuemart_custom_id)) {
			$q = 'select customfield_value from #__virtuemart_product_customfields where virtuemart_custom_id = '.(int)$selected_virtuemart_custom_id.' group by customfield_value'; 
			$db->setQuery($q); 
			$db->query(); 
			$res = $db->loadAssocList(); 
			$res2 = array(); 
			$res2[0] = ''; 
			if (!empty($res)) {
				foreach ($res as $k=>$v) {
					$res2[] = $v; 
				}
			$i = 0; 
			foreach ($res2 as $k=>$row) {
				$i++;
				$html .= '<fieldset>'; 
				$lbl = $row['customfield_value']; 
				if (empty($lbl)) $lbl = 'Default: If customfield not found or empty';
				$html .= '<label><em>'.JText::_('COM_ONEPAGE_DROPSHIP_CUSTOMFIELD_TITLE').':</em>'.JText::_($lbl).'</label>'; 
				$ind = md5($row['customfield_value']); 
				if (is_array($this->value)) {
				if (isset($this->value[$ind])) $val = $this->value[$ind]; 
				}
				else 
				if (is_object($this->value)) {
						if (isset($this->value->$ind)) $val = $this->value->$ind; 
					}
					
					$html .= '<label for="'.$nm.'"><em>'.JText::_('COM_VIRTUEMART_ORDERSTATUS').':</em></label>'; 
					$html .= '<select name="'.$this->name.'['.$ind.']" id="'.$nm.'" />'; 
					$html .= '<option value="">'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option>'; 
					foreach ($_orderStatusList as $key=>$o) {
						$key = (string)$key; 
						$val = (string)$val; 
						
						$html .= '<option '; 
						if ($val === $key) $html .= ' selected="selected" '; 
						
						//$html .= ' value="'.$key.'">'.$o.'</option>'; 
						$html .= ' value="'.$key.'">'.JText::_($o).'</option>'; 
				//$html .= '<input type="text" value="'.$val.'" name="'.$this->name.'['.$ind.']" id="'.$nm.'" />'; 
					}
					$html .= '</select>'; 
					
					$ind2 = $ind.'_days';
						if (is_array($this->value)) {
				if (isset($this->value[$ind2])) $val = $this->value[$ind2]; 
				}
				else 
				if (is_object($this->value)) {
						if (isset($this->value->$ind2)) $val = $this->value->$ind2; 
					}
					
					
					$html .= '<label for="'.$nm.'_num"><em>'.JText::_('COM_ONEPAGE_DROPSHIP_NUMERIC_REPRESENTATION').':</em></label>';
					$html .= '<input type="number" value="'.$val.'" name="'.$this->name.'['.$ind2.']" id="'.$nm.'_num" placeholder="Number of days or time equivalent to be used to compare timescales" />'; 
					
					$html .= '</fieldset>'; 
			}
			}
		}
		$html .= '<div style="margin-bottom:200px;">&nbsp;</div>'; 
		$html .= '<br />'; 
		  return $html; 
		  
		  
		  
		
	
	}
	private function getArray($value) {
	   $a = explode(',', $value); 
	   foreach ($a as $k=>$v) {
	     $a[$k] = trim($v); 
	   }
	   return $a; 
	}
	
	
}

