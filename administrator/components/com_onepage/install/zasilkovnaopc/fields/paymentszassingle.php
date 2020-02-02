<?php
defined('_JEXEC') or die();

/**
 *
 * @package	VirtueMart
 * @subpackage Plugins  - Elements
 * @author stAn
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */
/*
 * This class is used by VirtueMart Payment or Shipment Plugins
 * which uses JParameter
 * So It should be an extension of JElement
 * Those plugins cannot be configured througth the Plugin Manager anyway.
 */
 
jimport('joomla.form.formfield');

class JFormFieldPaymentszassingle extends JFormFieldList {

    /**
     * Element name
     * @access	protected
     * @var		string
     */
    var $_name = 'paymentszassingle';

 public static function setVMLANG() {
	 self::setVMLANG(); 
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
    function getOptions() {
		//$name, $value, &$node, $control_name
		$name = $this->name; 
		$value = $this->value; 
		$control_name = 'params'; 
		
        $db =  JFactory::getDBO();

        $query = 'SELECT `virtuemart_paymentmethod_id` AS value, `payment_name` AS text FROM `#__virtuemart_paymentmethods_'.VMLANG.'`
               		WHERE 1 '; 
        ;

        $db->setQuery($query);
        $fields = $db->loadObjectList();
	    $options = array(); 
		foreach ($fields as $k=>$row)
		{
			$options[] = JHtml::_('select.option', $row->value, $row->text);
		}
	    

	    return $options; 
    }

}