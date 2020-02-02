<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

defined('JPATH_BASE') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 */
class JFormFieldShlibpluginlink extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'Shlibpluginlink';

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 */
	public function __get($name)
	{

		switch ($name)
		{
			case 'element':
				return $this->$name;
				break;
		}

		$value = parent::__get($name);
		return $value;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		$text = JText::_('COM_SH404SEF_CONFIGURE_SHLIB_PLUGIN');
		$link = '<span class = "btn sh404sef-textinput"><a href="' . Sh404sefHelperGeneral::getShLibPluginLink() . '" target="_blank">' . $text . '</a></span>';
		return $link;

		$html = '';
		$class = $this->element['class'] ? (string) $this->element['class'] : '';

		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTip' : '';
		$class = $this->required == true ? $class . ' required' : $class;

		// Add the opening label tag and main attributes attributes.
		$field = '<fieldset id="' . $this->id . ' class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		$label = '<label for=""';
		if (!empty($this->description))
		{
			$label .= ' title="'
				. htmlspecialchars(trim($text, ':') . '::' . ($this->translateDescription ? JText::_($this->description) : $this->description),
					ENT_COMPAT, 'UTF-8') . '"';
		}

		// Add the label text and closing tag.
		$label .= "></label>";
		$field .= '><span class = "sh404sef-additionaltext">'  . $link . '</span></fieldset>';
		$html[] = $field;

		return implode('', $html);
	}
}
