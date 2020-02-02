<?php
/**
 * @package     CSVI
 * @subpackage  Plugin.Fieldcopy
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die;

/**
 * Copies a field into 1 or more other fields.
 *
 * @package     CSVI
 * @subpackage  Plugin.Combine
 * @since       6.0
 */
class PlgCsvirulesFieldcombine extends RantaiPluginDispatcher
{
	/**
	 * The unique ID of the plugin
	 *
	 * @var    string
	 * @since  6.0
	 */
	private $id = 'csvifieldcombine';

	/**
	 * Return the name of the plugin.
	 *
	 * @return  array  The name and ID of the plugin.
	 *
	 * @since   6.0
	 */
	public function getName()
	{
		return array('value' => $this->id, 'text' => 'CSVI Field Combine');
	}

	/**
	 * Method to get the name only of the plugin.
	 *
	 * @param   string  $plugin  The ID of the plugin
	 *
	 * @return  string  The name of the plugin.
	 *
	 * @since   6.0
	 */
	public function getSingleName($plugin)
	{
		if ($plugin === $this->id)
		{
			return 'CSVI Field Combine';
		}
	}

	/**
	 * Method to get the field options.
	 *
	 * @param   string  $plugin   The ID of the plugin
	 * @param   array   $options  An array of settings
	 *
	 * @return  string  The rendered form with plugin options.
	 *
	 * @since   6.0
	 *
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	public function getForm($plugin, $options = array())
	{
		if ($plugin === $this->id)
		{
			// Load the language files
			$lang = JFactory::getLanguage();
			$lang->load('plg_csvirules_fieldcombine', JPATH_ADMINISTRATOR, 'en-GB', true);
			$lang->load('plg_csvirules_fieldcombine', JPATH_ADMINISTRATOR, null, true);

			// Add the form path for this plugin
			JForm::addFormPath(JPATH_PLUGINS . '/csvirules/fieldcombine/');

			// Load the helper that renders the form
			$helper = new CsviHelperCsvi;

			// Instantiate the form
			$form = JForm::getInstance('fieldcombine', 'form_fieldcombine');

			// Bind any existing data
			$form->bind(array('pluginform' => $options));

			// Create some dummies
			$input = new JInput;

			// Set the correct fields on page loaded
			JFactory::getDocument()->addScriptDeclaration(
<<<JS
			jQuery(function() {
				var method = jQuery('#pluginform_operation');
				
				if (method.val() === 'combine')
				{
					Csvi.showFields(0, '#calculate'); 
					Csvi.showFields(1, '#combine');
					method.trigger('change'); 
				}
				else if (method.val() === 'calculate')
				{
					Csvi.showFields(1, '#calculate'); 
					Csvi.showFields(0, '#combine');
				}
			});
JS
			);

			// Render the form
			return $helper->renderCsviForm($form, $input);
		}
	}

	/**
	 * Run the rule.
	 *
	 * @param   string            $plugin    The ID of the plugin.
	 * @param   object            $settings  The plugin settings set for the field.
	 * @param   array             $field     The field being process.
	 * @param   CsviHelperFields  $fields    A CsviHelperFields object.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function runRule($plugin, $settings, $field, CsviHelperFields $fields)
	{
		if ($plugin == $this->id)
		{
			// Perform the combine
			if (!empty($settings))
			{
				// Check if we have a source value
				if ($settings->source)
				{
					if (!isset($settings->combine_empty))
					{
						$settings->combine_empty = 0;
					}

					// Load the friendly field names that need to be combined
					$updates = explode(',', $settings->source);

					// Load all field values and combine them
					$values          = array();
					$fieldcounter    = array();
					$calculatedValue = 0;

					foreach ($updates as $update)
					{
						// Clean any spaces
						$update = trim($update);

						if (!isset($fieldcounter[$update]))
						{
							$fieldcounter[$update] = 0;
						}

						$fieldcounter[$update]++;

						// Get the default value if needed
						$fieldDefaultValue = $fields->getField($update);

						$value = $fields->get($update, $fieldDefaultValue->default_value, $fieldcounter[$update]);

						if ($settings->combine_empty || $value)
						{
							$values[] = $value;
						}
					}

					// Find the field to update
					$oldFields = $fields->getFields();
					$target    = strtolower($settings->target);

					// Check if we have any decimals
					if (!isset($settings->decimals))
					{
						$settings->decimals = 2;
					}

					// If user wants to perform arithmetic operation then do it
					if ($settings->calculate && $settings->operation === 'calculate')
					{
						$calculatedValue = $this->performOperation($values, $settings->calculate, $settings->decimals);
					}

					foreach ($oldFields as $column_header => $oldField)
					{
						if (is_array($oldField))
						{
							// Make it a single dimensional array
							$oldField = reset($oldField);
						}

						if ($oldField)
						{
							$columnHeader = '';

							if (isset($oldField->column_header))
							{
								$columnHeader = $oldField->column_header;
							}

							// For import purposes, check the XML node as well, so we can be more flexible
							if ($columnHeader === '' && isset($oldField->xml_node))
							{
								$columnHeader = $oldField->xml_node;
							}

							$haystack = array(strtolower($columnHeader), strtolower($oldField->field_name));

							if (in_array($target, $haystack))
							{
								if ($calculatedValue || $settings->operation === 'calculate')
								{
									$fields->updateField($oldField, $calculatedValue);
								}
								else
								{
									$fields->updateField($oldField, implode($settings->combine_character, $values));
								}

								// Let's end
								break;
							}
						}
					}
				}
			}
		}
	}

	/**
	 * A function to perform needed operation on values
	 *
	 * @param   array    $values    The values to perform operation on.
	 * @param   string   $operator  The arithmetic operataion
	 * @param   integer  $decimals  The number of decimals
	 *
	 * @return  string  performed value.
	 *
	 * @since   7.10.0
	 */
	private function performOperation($values, $operator, $decimals = 2)
	{
		// Check if we have any values
		if (count($values) === 0)
		{
			return 0;
		}

		// Clean up the array keys
		rsort($values);

		// Get the base value
		$result = $values[0];
		unset($values[0]);

		foreach ($values as $value)
		{
			$cleanValue = $this->cleanNumber($value);

			if (is_numeric($cleanValue) || is_float($cleanValue))
			{
				switch ($operator)
				{
					case 'multiplication':
						$result *= $cleanValue;
						break;
					case 'addition':
						$result += $cleanValue;
						break;
					case 'subtraction':
						$result -= $cleanValue;
						break;
					case 'division':
						$result /= $cleanValue;
						break;
				}
			}
		}

		return number_format($result, $decimals);
	}

	/**
	 * A function to clean values
	 *
	 * @param   mixed  $value  The value to be cleaned
	 *
	 * @return  float  cleaned value.
	 *
	 * @since   7.10.0
	 */
	private function cleanNumber($value)
	{
		$filter = new JFilterInput;
		$clean      = str_replace(",", ".", $value);
		$lastpos    = strrpos($clean, '.');
		$value      = str_replace('.', '', substr($clean, 0, $lastpos)) . substr($clean, $lastpos);

		return $filter->clean($value, 'float');
	}
}
