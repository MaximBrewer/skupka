<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Provides a grouped list select field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldCkgroupedList extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'CkgroupedList';

	/**
	 * Method to get the field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 *
	 * @since   11.1
	 * @throws  UnexpectedValueException
	 */
	protected function getGroups()
	{
		$groups = array();
		$label = 0;

		foreach ($this->element->children() as $element)
		{
			switch ($element->getName())
			{
				// The element is an <option />
				case 'option':
					// Initialize the group if necessary.
					if (!isset($groups[$label]))
					{
						$groups[$label] = array();
					}

					$disabled = (string) $element['disabled'];
					$disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');

					// Create a new option object based on the <option /> element.
					$tmp = JHtml::_(
						'select.option', ($element['value']) ? (string) $element['value'] : trim((string) $element),
						JText::alt(trim((string) $element), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text',
						$disabled
					);

					// Set some option attributes.
					$tmp->class = (string) $element['class'];

					// Set some JavaScript option attributes.
					$tmp->onclick = (string) $element['onclick'];

					// Add the option.
					$groups[$label][] = $tmp;
					break;

				// The element is a <group />
				case 'group':
					// Get the group label.
					if ($groupLabel = (string) $element['label'])
					{
						$label = JText::_($groupLabel);
					}

					// Initialize the group if necessary.
					if (!isset($groups[$label]))
					{
						$groups[$label] = array();
					}

					// Iterate through the children and build an array of options.
					foreach ($element->children() as $option)
					{
						// Only add <option /> elements.
						if ($option->getName() != 'option')
						{
							continue;
						}

						$disabled = (string) $option['disabled'];
						$disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');

						// Create a new option object based on the <option /> element.
						$tmp = JHtml::_(
							'select.option', ($option['value']) ? (string) $option['value'] : JText::_(trim((string) $option)),
							JText::_(trim((string) $option)), 'value', 'text', $disabled
						);

						// Set some option attributes.
						$tmp->class = (string) $option['class'];

						// Set some JavaScript option attributes.
						$tmp->onclick = (string) $option['onclick'];

						// Add the option.
						$groups[$label][] = $tmp;
					}

					if ($groupLabel)
					{
						$label = count($groups);
					}
					break;

				// Unknown element type.
				default:
					throw new UnexpectedValueException(sprintf('Unsupported element %s in JFormFieldGroupedList', $element->getName()), 500);
			}
		}

		reset($groups);

		return $groups;
	}

	/**
	 * Method to get the field input markup fora grouped list.
	 * Multiselect is enabled by using the multiple attribute.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$html = array();
		$attr = '';
		$icon = $this->element['icon'];
		$suffix = $this->element['suffix'];

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= $this->disabled ? ' disabled' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';

		// Initialize JavaScript field attributes.
		$attr .= ' style="width:150px;' . $this->element['styles'] . '"';
		$attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		// Get the field groups.
		$groups = (array) $this->getGroups();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ($this->readonly)
		{
			$html[] = $icon ? '<div style="display:inline-block;vertical-align:top;margin-top:5px;width:20px;"><img src="' . $this->getPathToElements() . '/images/' . $icon . '" style="margin-right:5px;" /></div>' : '<div style="display:inline-block;width:20px;"></div>';
			$html[] = JHtml::_(
				'select.groupedlist', $groups, null,
				array(
					'list.attr' => $attr, 'id' => $this->id, 'list.select' => $this->value, 'group.items' => null, 'option.key.toHtml' => false,
					'option.text.toHtml' => false
				)
			);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}

		// Create a regular list.
		else
		{
			$html[] = $icon ? '<div style="display:inline-block;vertical-align:top;width:20px;"><img src="' . $this->getPathToElements() . '/images/' . $icon . '" style="margin-right:5px;" /></div>' : '<div style="display:inline-block;width:20px;"></div>';
			$html[] = JHtml::_(
				'select.groupedlist', $groups, $this->name,
				array(
					'list.attr' => $attr, 'id' => $this->id, 'list.select' => $this->value, 'group.items' => null, 'option.key.toHtml' => false,
					'option.text.toHtml' => false
				)
			);
		}

		return implode($html);
	}
	
	protected function getPathToElements() {
		$localpath = dirname(__FILE__);
		$rootpath = JPATH_ROOT;
		$httppath = trim(JURI::root(), "/");
		$pathtoelements = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
		return $pathtoelements;
	}
}
