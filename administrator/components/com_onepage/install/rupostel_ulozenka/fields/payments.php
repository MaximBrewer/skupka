<?php
/**
 * @version		
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.form.formfield');

class JFormFieldPayments extends JFormField {

    /**
     * Element name
     * @access	protected
     * @var		string
     */
    var $_name = 'payments';

 public static function setVMLANG() {
	 
	  if (!class_exists('VmConfig'))
		    {
			     if (!class_exists('VmConfig'))
				require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
				VmConfig::loadConfig ();

			}
			if(!defined('VMLANG'))		
			if (method_exists('VmConfig', 'setdbLanguageTag')) {
			   VmConfig::setdbLanguageTag();
			}
	 
    
	if ((!defined('VMLANG')) && (!empty(VmConfig::$vmlang))) {
	  define('VMLANG', VmConfig::$vmlang); 
	}
		
 }
    function getInput() {
		self::setVMLANG(); 
		//$name, $value, &$node, $control_name
		$name = $this->name; 
		if (substr($name, -2) !== '[]') $name .= '[]'; 
		$value = $this->value; 
		$control_name = 'params'; 
		
        $db =  JFactory::getDBO();

        $query = 'SELECT `virtuemart_paymentmethod_id` AS value, `payment_name` AS text FROM `#__virtuemart_paymentmethods_'.VMLANG.'`
               		WHERE 1 '; 
        ;

        $db->setQuery($query);
        $fields = $db->loadObjectList();
	    


	    $class = 'multiple="true" size="10"  ';
        return JHTML::_('select.genericlist', $fields, $name , $class, 'value', 'text', $value, $control_name . $name);
    }

}