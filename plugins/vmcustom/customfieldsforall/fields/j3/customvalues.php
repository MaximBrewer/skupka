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

Class JFormFieldCustomvalues extends JFormField{
	
	protected $type = 'customvalues';

	
	protected function getInput()
	{	ini_set('display_errors',true);
		$fieldname='cf_val';
		$jinput=JFactory::getApplication()->input;
		$virtuemart_custom_id=$jinput->get('virtuemart_custom_id',array(),'ARRAY');
		if(is_array($virtuemart_custom_id))$virtuemart_custom_id=end($virtuemart_custom_id);
		$customfield=Customfield::getInstance($virtuemart_custom_id);
		$custom_params=$customfield->getCustomfieldParams($virtuemart_custom_id);
		$custom_params['single_entry']=!empty($custom_params['is_price_variant'])?true:false;
		$renderFields=new RenderFields;
		$html=$renderFields->fetchCustomvalues($fieldname,$virtuemart_custom_id,$this->value,$row=0,$custom_params);
		return $html;
	}
	
	protected function getLabel(){
		$title='';
		if(empty($this->value))$title='<strong>'.JText::_($this->element['label']).'</strong><br />'.JText::_($this->element['description']);
		$html='<label data-original-title="<strong>'.JText::_($this->element['label']).'</strong><br />'.JText::_($this->element['description']).'" id="params_display_type-lbl" for="params_'.$this->element['name'].'" class="hasTooltip" title="'.$title.'">'.JText::_($this->element['label']).'</label>';
		return $html;
	}
}
