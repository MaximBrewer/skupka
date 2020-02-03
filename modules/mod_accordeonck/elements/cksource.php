<?php
/**
 * @copyright	Copyright (C) 2016 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */

defined('JPATH_PLATFORM') or die;

require_once dirname(__FILE__) . '/cklist.php';
class JFormFieldCksource extends JFormFieldCklist
{

	protected $type = 'cksource';

	function __construct($form = null) {
		parent::__construct($form);
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
							'select.option', (string) $option['value'], JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text', ((string) $option['disabled'] == 'true')
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		// load the custom plugins
		// if (JPluginHelper::isEnabled('system', 'accordeonckparams')) {
			JPluginHelper::importPlugin('accordeonmenuck');
			$dispatcher = JEventDispatcher::getInstance();
			$sources = $dispatcher->trigger('onAccordeonmenuckGetSourceName');

			if (count($sources)) {
				foreach ($sources as $source) {
					$tmp = JHtml::_(
									'select.option', (string) $source, JText::_(trim((string) ucfirst($source)), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text', '0'
					);
					// Add the option object to the result set.
					$options[] = $tmp;
				}
			}
		// }

		reset($options);

		return $options;
	}
}
