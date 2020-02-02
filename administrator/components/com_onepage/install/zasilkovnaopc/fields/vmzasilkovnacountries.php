<?php
defined('_JEXEC') or die();

 
 jimport('joomla.form.formfield');

class JFormFieldVmZasilkovnaCountries extends JFormField {

    /**
     * Element name
     * @access  protected
     * @var     string
     */
    var $_name = 'countries';
	protected function getInput() {
		$this->multiple = true;
		$name = $this->name; 
		if (substr($name, -2) !== '[]') $name .= '[]'; 
		$value = $this->value; 
		$db = JFactory::getDBO(); 
		$q = 'select country_2_code as value, country_name as text from #__virtuemart_countries where country_2_code = \'SK\' or country_2_code = \'CZ\' limit 0,2'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		$control_name = 'params'; 
		
		$options = array(); 
	
		foreach ($res as $v) {
			$options[] = JHtml::_('select.option', strtolower($v['value']), JText::_($v['text']));
		}
		
		
		 $class = 'multiple="true" size="10"  ';
        return JHTML::_('select.genericlist', $options, $name , $class, 'value', 'text', $value, $control_name . $name);
	}
	
    

}