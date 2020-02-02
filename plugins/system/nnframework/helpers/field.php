<?php
/**
 * @package         NoNumber Framework
 * @version         18.12.3953
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Form\FormField as JFormField;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/parameters.php';
require_once __DIR__ . '/text.php';

class NNFormField extends JFormField
{
	public $type           = 'Field';
	public $db             = null;
	public $max_list_count = 0;
	public $params         = null;

	public function __construct($form = null)
	{
		$this->db = JFactory::getDbo();

		$parameters           = NNParameters::getInstance();
		$params               = $parameters->getPluginParams('nnframework');
		$this->max_list_count = $params->max_list_count;
	}

	protected function getInput()
	{
		return false;
	}

	protected function getOptions()
	{
		// This only returns 1 option!!!
		if (empty($this->element->option))
		{
			return [];
		}

		$option = $this->element->option;

		$fieldname = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname);
		$value     = (string) $option['value'];
		$text      = trim((string) $option) ? trim((string) $option) : $value;

		return [
			[
				'value' => $value,
				'text'  => '- ' . JText::alt($text, $fieldname) . ' -',
			],
		];
	}

	public function get($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}

	function getOptionsByList($list, $extras = [], $levelOffset = 0)
	{
		$options = [];
		foreach ($list as $item)
		{
			$options[] = $this->getOptionByListItem($item, $extras, $levelOffset);
		}

		return $options;
	}

	function getOptionByListItem($item, $extras = [], $levelOffset = 0)
	{
		$name = trim($item->name);

		foreach ($extras as $key => $extra)
		{
			if (empty($item->{$extra}))
			{
				continue;
			}

			if ($extra == 'language' && $item->{$extra} == '*')
			{
				continue;
			}

			if (in_array($extra, ['id', 'alias']) && $item->{$extra} == $item->name)
			{
				continue;
			}

			$name .= ' [' . $item->{$extra} . ']';
		}

		$name = NNText::prepareSelectItem($name, isset($item->published) ? $item->published : 1);

		$option = JHtml::_('select.option', $item->id, $name, 'value', 'text', 0);

		if (isset($item->level))
		{
			$option->level = $item->level + $levelOffset;
		}

		return $option;
	}

	function getOptionsTreeByList($items = [], $root = 0)
	{
		// establish the hierarchy of the menu
		// TODO: use node model
		$children = [];

		if ( ! empty($items))
		{
			// first pass - collect children
			foreach ($items as $v)
			{
				$pt   = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : [];
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = JHtml::_('menu.treerecurse', $root, '', [], $children, 9999, 0, 0);

		// assemble items to the array
		$options = [];
		if ($this->get('show_ignore'))
		{
			if (in_array('-1', $this->value))
			{
				$this->value = ['-1'];
			}
			$options[] = JHtml::_('select.option', '-1', '- ' . JText::_('NN_IGNORE') . ' -', 'value', 'text', 0);
			$options[] = JHtml::_('select.option', '-', '&nbsp;', 'value', 'text', 1);
		}

		foreach ($list as $item)
		{
			$item->treename = NNText::prepareSelectItem($item->treename, $item->published, '', 1);

			$options[] = JHtml::_('select.option', $item->id, $item->treename, 'value', 'text', 0);
		}

		return $options;
	}

	public function prepareText($string = '')
	{
		$string = trim($string);

		if ($string == '')
		{
			return '';
		}

		switch (true)
		{
			// Old fields using var attributes
			case (JText::_($this->get('var1'))):
				$string = $this->sprintf_old($string);
				break;

			// sprintf format (comma separated)
			case (strpos($string, ',') !== false):
				$string = $this->sprintf($string);
				break;

			// Normal language string
			default:
				$string = JText::_($string);
		}

		return $this->fixLanguageStringSyntax($string);
	}

	public function fixLanguageStringSyntax($string = '')
	{
		$string = trim(NNText::html_entity_decoder($string));
		$string = str_replace('&quot;', '"', $string);
		$string = str_replace('span style="font-family:monospace;"', 'span class="nn_code"', $string);

		return $string;
	}

	public function sprintf($string = '')
	{
		$string = trim($string);

		if (strpos($string, ',') === false)
		{
			return $string;
		}

		$string_parts = explode(',', $string);
		$first_part   = array_shift($string_parts);

		if ($first_part !== strtoupper($first_part))
		{
			return $string;
		}

		$first_part = preg_replace('/\[\[%([0-9]+):[^\]]*\]\]/', '%\1$s', JText::_($first_part));

		array_walk($string_parts, 'NNFormField::jText');

		return vsprintf($first_part, $string_parts);
	}

	public function jText(&$string)
	{
		$string = JText::_($string);
	}

	public function sprintf_old($string = '')
	{
		// variables
		$var1 = JText::_($this->get('var1'));
		$var2 = JText::_($this->get('var2'));
		$var3 = JText::_($this->get('var3'));
		$var4 = JText::_($this->get('var4'));
		$var5 = JText::_($this->get('var5'));

		return JText::sprintf(JText::_(trim($string)), $var1, $var2, $var3, $var4, $var5);
	}
}
