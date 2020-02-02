<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of folder
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldSources extends JFormFieldList{
	protected $type = 'Sources';
	public function setup(SimpleXMLElement $element, $value, $group = null) {
		$return = parent::setup($element, $value, $group);

		if ($return) {
			//$this->filter  = (string) $this->element['filter'];
		}

		return $return;
	}
	protected function getOptions()
	{
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('JDEFAULT'));
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('yandexmapssource');
		$names = array();
		$data2 = $dispatcher->trigger('onGetName', array(&$names));
		foreach ($names as $id=>$name) {
			$options[] = JHtml::_('select.option', $id, JText::_($name));
		}
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
