<?php
defined ('_JEXEC') or die();
jimport( 'joomla.html.parameter.element.list' );

class JElementCurrencyselect extends JElementList {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'currencyselect';

	function fetchElement ($name, $value, &$node, $control_name) {
		//$name, $value, &$node, $control_name
		$options = array(); 
		//$name = $this->name; 
		//$value = $this->value; 
	
	$db = JFactory::getDBO(); 
	$q = 'select * from #__virtuemart_currencies where currency_code_3 = "EUR" or currency_code_3 = "CZK"'; 
	$db->setQuery($q); 
	$cc = $db->loadAssocList(); 
	
		   foreach ($cc as $c)
		     {
								
								$option_name = $c['currency_code_3'];
								$option_value = $c['virtuemart_currency_id']; 
								
								$options[] = JHtml::_('select.option', $option_value, $option_name);
								
								
								
								
			 }
		 return JHTML::_('select.genericlist', $options, $control_name . '[' . $name . '][]', $class, 'value', 'text', $value, $control_name . $name);
		//return $options; 
	}

}