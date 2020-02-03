<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once 'ckformfield.php';

class CKFormField extends JFormField {

	public $mediaPath;

	public function __construct() {
		$this->mediaPath = JUri::root(true) . '/media/com_slideshowck/images/';
		// loads the language files from the frontend
		$lang	= JFactory::getLanguage();
		$lang->load('com_slideshowck', JPATH_SITE . '/components/com_slideshowck', $lang->getTag(), false);
		$lang->load('com_slideshowck', JPATH_SITE, $lang->getTag(), false);
		parent::__construct();
	}
	protected function getInput() {
		return '';
	}

	protected function getLabel() {
		return parent::getLabel();
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions() {
		$options = array();

		foreach ($this->element->children() as $option) {

			// Only add <option /> elements.
			if ($option->getName() != 'option') {
				continue;
			}

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
							'select.option', (string) $option['value'],
							JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text',
							((string) $option['disabled'] == 'true')
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
