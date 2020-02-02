<?php
/**
 * @version		$Id: customvalues.php 2013-07-01 19:12 sakis Terz $
 * @package		customfieldsforall
 * @copyright	Copyright (C)2013 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');

defined('_JEXEC') or die('Restricted access');
if(!class_exists('Customfield'))require(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmcustom'.DIRECTORY_SEPARATOR.'customfieldsforall'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'customfield.php');
if(!class_exists('RenderFields'))require(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmcustom'.DIRECTORY_SEPARATOR.'customfieldsforall'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR.'renderFields.php');

/**
 *
 * Class that generates a custom value list
 * @todo	When VM will replace params with fields in the plugin's XML, replace that class with a JFormField Class
 * @author 	Sakis Terzis
 */
Class JElementDatatype extends JElement{
	/**
	 * Method to get the param input markup.
	 *
	 * @todo	When VM will replace params with fields replace that function with JFormField::getInput
	 * @return	string	The param input markup.
	 * @since	1.0
	 */
	function fetchElement($fieldname, $value='', &$node='', $control_name='')
	{
		$jinput=JFactory::getApplication()->input;
		$virtuemart_custom_id=$jinput->get('virtuemart_custom_id',array(),'ARRAY');		
		if(is_array($virtuemart_custom_id))$virtuemart_custom_id=end($virtuemart_custom_id);
		$renderFields=new RenderFields;
		$html=$renderFields->fetchDatatype($fieldname, $virtuemart_custom_id,$value);		
		return $html;
	}	
}
	