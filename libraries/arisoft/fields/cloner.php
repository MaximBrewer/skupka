<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/../loader.php';

use \Arisoft\Joomla\Fields\Field as Field;
use \Arisoft\Parameters\Helper as ParametersHelper;
use \Arisoft\Joomla\Helper as JoomlaHelper;

class JFormFieldCloner extends Field
{
	public $type = 'Сloner';

	public function getInput()
	{
//		return $this->fetchElement($this->element['name'], $this->value, $this->element, $this->name);
//	function fetchElement($name, $value, &$node, $control_name)
		$this->includeAssets();

        $value = $this->value;
		$id = str_replace(array('[', ']'), array('_', ''), $this->name);
		$layout = $this->getLayout($id);
		$cssFile = (string)$this->element['css_file'];
		$keyField = (string)$this->element['key_field'];

		$document = JFactory::getDocument();

		if (!empty($cssFile))
			$document->addStyleSheet(JURI::root(true) . $cssFile);

		if ($value && $this->name == 'refField_params')
			$value = html_entity_decode($value);

		$document->addScriptDeclaration(sprintf(
			';jQuery(document).ready(function($) { var cloner = $("#%1$s_cloner").ariCloner({}, {"%2$s": %3$s}); ARIJoomlaHelper.registerOnSubmitHandler(function() { var data = $("#%1$s_cloner").ariCloner().getData(true); $("#%1$s").val(JSON.stringify(data)); }); });',
			$id,
            $keyField,
//			json_encode(array('hiddenId' => $id, 'keyField' => $keyField)),
//			json_encode($this->getClonerOptions()),
			$value ? addcslashes($value, "\n\r") : 'null'
		));

		return $layout 
			. '<input type="hidden" name="' . $this->name . '" id="' . $id . '" value="' . str_replace('"', '&quot;', $value) . '" />';
	}
	
	private function getClonerOptions()
	{
        $node = $this->element;
		$attrs = $node->attributes();
		$optAttrs = array();
		foreach ($attrs as $key => $value)
		{
			if (strpos($key, 'opt_') !== 0)
				continue;
				
			$optAttrs[$key] = JText::_($value);
		}

		$keyField = (string)$node['key_field'];
		$params = ParametersHelper::flatParametersToTree($optAttrs);
		$clonerOptions = ParametersHelper::getUniqueOverrideParameters(
			array(
				'numFormat' => '#{$num}.',
				'enableNumFormat' => true,
				'defaultItemCount' => 3,
				'message' => array(
					'removeConfirm' => 'Are you sure you want to remove this item?',
					'removeAllConfirm' => 'Are you sure you want to remove all items?'
				)
			), 
			isset($params['opt']) ? $params['opt'] : array(),
			true);
			
		$clonerOptions = count($clonerOptions) > 0 ? $clonerOptions : array();
		
		$clonerOptions['keyField'] = $keyField;
		
		return $clonerOptions;
	}
	
	protected function getLayout($id)
	{
		$layout = '';
        $node = $this->element;
		if (!isset($node->layout))
			return $layout;

		$layout = (string)$node->layout[0];
		$layout = str_replace('{$id}', $id . '_cloner', $layout);
		
		$layout = preg_replace_callback(
            '/@@(.+?)@@/i',
            function ($matches)
            {
                return !empty($matches[1]) ? JText::_($matches[1]) : '';
            },
            $layout
        );
			
		return $layout;
	}

	protected function includeAssets()
	{
		static $loaded;
		
		if ($loaded)
			return ;

        JoomlaHelper::registerJsHelper();

		$uri = JURI::root(true). '/media/arisoft/';
			
		$document = JFactory::getDocument();

		$document->addScript($uri . 'cloner/cloner.js');
        $document->addStyleSheet($uri . 'cloner/cloner.css');

		$loaded = true;
	}
}