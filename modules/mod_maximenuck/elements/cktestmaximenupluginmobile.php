<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

class JFormFieldCktestmaximenupluginmobile extends JFormField {

    protected $type = 'cktestmaximenupluginmobile';

    protected function getInput() {
        return ' ';
    }

    protected function getLabel() {
		$app = JFactory::getApplication();
		$html = array();
		$class = $this->element['class'] ? (string) $this->element['class'] : '';

		$style = $this->element['style'];

		// check for mobile menu ck instead of maximenu mobile
		// if mobile menu ck active and not maximenu mobile, then hide maximenu mobile options
		if (function_exists('loadMobileMenuCK')) {
			$db = JFactory::getDbo();
			$db->setQuery("SELECT enabled FROM #__extensions WHERE `element` = 'maximenuckmobile' AND `type` = 'plugin'");
			$enabled = $db->loadResult();
			if (! $enabled) {
				$html[] = '<style>a[href*="mobileparams"], #attrib-maximenu_mobileparams { display: none !important; }</style>';
				return implode('', $html);
			}
		}
        $html[] = '<div class="maximenuckchecking">';
        // $html[] = '<span class="before"></span>';
        // $html[] = '<span class="' . $class . '">';
        if ((string) $this->element['hr'] == 'true') {
            $html[] = '<hr class="' . $class . '" />';
        } else {
            $label = '';
            // Get the label text from the XML element, defaulting to the element name.
            $text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
            $text = $this->translateLabel ? JText::_($text) : $text;
            // Test to see if the patch is installed
            $testpatch = $this->testPatch('maximenuckmobile');
            $text = $testpatch ? $testpatch : $text;
            // set the icon
            $icon = $this->element['icon'];

            // Build the class for the label.
            $class = !empty($this->description) ? 'hasTip' : '';
            $class = $this->required == true ? $class . ' required' : $class;

            // Add the opening label tag and main attributes attributes.
            // $label .= '<label id="' . $this->id . '-lbl" class="' . $class . '"';

            // If a description is specified, use it to build a tooltip.
            if (!empty($this->description)) {
                $label .= ' title="' . htmlspecialchars(trim($text, ':') . '::' .
                    ($this->translateDescription ? JText::_($this->description) : $this->description), ENT_COMPAT, 'UTF-8') . '"';
            }

            // Add the label text and closing tag.
            // $label .= $styles . '>';
            $label .= $icon ? '<img src="' . $this->getPathToImages() . '/images/' . $icon . '" style="margin-right:5px;" />' : '';
            $label .= $text;
			// $label .= '</label>';
            $html[] = $label;
        }
        // $html[] = '</span>';
        // $html[] = '<span class="after"></span>';
        $html[] = '</div>';
        return implode('', $html);
    }

    protected function getPathToImages() {
        $localpath = dirname(__FILE__);
        $rootpath = JPATH_ROOT;
        $httppath = trim(JURI::root(), "/");
        $pathtoimages = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
        return $pathtoimages;
    }

    protected function getTitle() {
        return $this->getLabel();
    }

	protected function testPatch($component) {
		if (JFile::exists(JPATH_ROOT . '/plugins/system/' . $component .'/'. $component . '.php')
				&& JPluginHelper::isEnabled('system',$component)) {
			$this->element['icon'] = 'accept.png';
			return JText::_('MOD_MAXIMENUCK_SPACER_' . strtoupper($component) . '_PATCH_INSTALLED');
		} else {
			$this->element['icon'] = 'cross.png';
			return JText::_('MOD_MAXIMENUCK_CHECK_MOBILEMENUCK') . ' <a href="https://www.joomlack.fr/en/joomla-extensions/mobile-menu-ck" target="_blank">Mobile Menu CK</a>';
		}
		return false;
	}
}