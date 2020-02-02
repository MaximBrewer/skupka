<?php
/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('cklist');

class JFormFieldCkthemeslist extends JFormFieldCklist
{

	public $type = 'ckthemeslist';

	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Initialize some field attributes.
		$filter			= (string) $this->element['filter'];
		$exclude		= (string) $this->element['exclude'];
		$hideNone		= (string) $this->element['hide_none'];
		$hideDefault	= (string) $this->element['hide_default'];

		// Get the path in which to search for file options.
		$path = (string) $this->element['directory'];
		if (!is_dir($path)) {
			$path = JPATH_ROOT.'/'.$path;
		}

		// Prepend some default options based on field attributes.
		if (!$hideNone) {
			$options[] = JHtml::_('select.option', '-1', JText::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}
		if (!$hideDefault) {
			$options[] = JHtml::_('select.option', '', JText::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}

		// Get a list of folders in the search path with the given filter.
		$folders = JFolder::folders($path, $filter);

		// Build the options list from the list of folders.
		if (is_array($folders)) {
			foreach($folders as $folder) {

				// Check to see if the file is in the exclude mask.
				if ($exclude) {
					if (preg_match(chr(1).$exclude.chr(1), $folder)) {
						continue;
					}
				}

				$options[] = JHtml::_('select.option', $folder, $folder);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
	
	protected function getInput() {
		// Initialize variables.
		$html = array();
		$attr = '';
		$icon = $this->element['icon'];
		$suffix = $this->element['suffix'];

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		$attr .= ' style="width:150px;' . $this->element['styles'] . '"';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// check if the theme is for the latest version of maximenu
		$imgpath = JUri::root(true) . '/modules/mod_maximenuck/elements/images/';
		if ( ! file_exists(JPATH_ROOT . '/modules/mod_maximenuck/themes/' . $this->value . '/css/maximenuck.php') ) {
			$theme_checking_text = '<img src="' . $imgpath . 'error.png" />' . JText::_('MOD_MAXIMENUCK_THEME_OBSOLETE');
		} else {
			$theme_checking_text = '';
		}

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = $icon ? '<div style="display:inline-block;vertical-align:top;margin-top:5px;width:20px;"><img src="' . $this->getPathToElements() . '/images/' . $icon . '" style="margin-right:5px;" /></div>' : '<div style="display:inline-block;width:20px;"></div>';
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		// Create a regular list.
		else {
			$html[] = $icon ? '<div style="display:inline-block;vertical-align:top;width:20px;"><img src="' . $this->getPathToElements() . '/images/' . $icon . '" style="margin-right:5px;" /></div>' : '<div style="display:inline-block;width:20px;"></div>';
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		if ( $theme_checking_text !== '' ) {
			$html[] = '<style>.maximenuckthemechecking {background:#e1e1e1;border: none;
    border-radius: 3px;
    color: #333;
    font-weight: normal;
	line-height: 24px;
    padding: 5px;
	margin: 3px 0;
    text-align: left;
    text-decoration: none;
    }
	.maximenuckthemechecking img {
	margin: 0 5px;
    }</style>';
			$html[] = '<div class="maximenuckthemechecking">' . $theme_checking_text . '</div>';
		}

		return implode($html);
	}
}
