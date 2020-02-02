<?php
/**
 * @version		$Id: displaytypes.php 2014-08-07 11:12 sakis Terz $
 * @package		customfieldsforall
 * @copyright	Copyright (C)2014 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
require_once(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmcustom'.DIRECTORY_SEPARATOR.'customfieldsforall'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'customfield.php');

/**
 *
 * Class that generates a filter list
 * @author Sakis Terzis
 */
Class JElementDisplaytypes extends JElement{

	
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
		$html=$renderFields->fetchDisplaytypes($fieldname, $virtuemart_custom_id,$value);		
		return $html;
	}
}