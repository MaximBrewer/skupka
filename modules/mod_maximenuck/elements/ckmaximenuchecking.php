<?php

/**
 * @copyright	Copyright (C) 2014 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.form');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
// error_reporting(0);

class JFormFieldCkmaximenuchecking extends JFormField {

	protected $type = 'ckmaximenuchecking';

	protected function getInput() {
		$imgpath = JUri::root(true) . '/modules/mod_maximenuck/elements/images/';
		$js_checking = '';

		// check if the maximenu params component is installed
		if ( file_exists(JPATH_ROOT . '/administrator/components/com_maximenuck/maximenuck.php') ) {
			$com_params_text = '<img src="' . $imgpath . 'accept.png"  />' . JText::_('MOD_MAXIMENUCK_COMPONENT_PARAMS_INSTALLED');

			$js_checking = '<script>
			jQuery(document).ready(function (){
				jQuery(\'.maximenuckchecking\').each(function(i ,el){
				if (jQuery(el).attr(\'data-name\')) {
					jQuery.ajax({
						type: "POST",
						url: \'' . JUri::root(true) . '/administrator/index.php?option=com_maximenuck&task=check_update\',
						data: {
							name: jQuery(el).attr(\'data-name\'),
							type: jQuery(el).attr(\'data-type\'),
							folder: jQuery(el).attr(\'data-folder\')
						}
					}).done(function(response) {
						response = response.trim();
						if ( response.substring(0,7).toLowerCase() == \'error\' ) {
							alert(response);
							// show_ckmodal(response);
						} else {
							jQuery(el).append(response);
						}
					}).fail(function() {
						//alert(Joomla.JText._(\'CK_FAILED\', \'Failed\'));
					});
				}
				});
			});
		</script>';
		} else {
			$com_params_text = '<img src="' . $imgpath . 'cross.png"  />' . JText::_('MOD_MAXIMENUCK_COMPONENT_PARAMS_NOT_INSTALLED');
		}

		// check if the maximenu params plugin (old method) is installed
		if ( file_exists(JPATH_ROOT . '/plugins/system/maximenuckparams/maximenuckparams.php') ) {
			require_once(JPATH_ROOT . '/plugins/system/maximenuckparams/maximenuckparams.php');
			if (! method_exists('plgSystemMaximenuckparams', 'check_version') ){
				$plugin_params_text = '<img src="' . $imgpath . 'exclamation.png"  />' . JText::_('MOD_MAXIMENUCK_PLUGIN_PARAMS_INSTALLED_BUT_OBSOLETE');
			} else {
				$plugin_params_text = '<img src="' . $imgpath . 'accept.png"  />' . JText::_('MOD_MAXIMENUCK_PLUGIN_PARAMS_INSTALLED');
				if (! JPluginHelper::isEnabled('system', 'maximenuckparams') ) {
					$plugin_params_text .= '<img src="' . $imgpath . 'error.png"  />' . '<a href="index.php?option=com_plugins&filter_folder=system&filter_search=maximenu" class="modal" rel="{handler: \'iframe\', size: {x: 900, y: 550}}">' . JText::_('MOD_MAXIMENUCK_ACTIVATE_PLUGIN') . '</a>';
				}
			}
		} else {
			$plugin_params_text = '<img src="' . $imgpath . 'cross.png"  />' . JText::_('MOD_MAXIMENUCK_PLUGIN_PARAMS_NOT_INSTALLED');
		}

		// check for mobile menu ck instead of maximenu mobile
		// if mobile menu ck active and not maximenu mobile, then hide maximenu mobile options
		if (file_exists(JPATH_ROOT . '/plugins/system/mobilemenuck/mobilemenuck.php')) {
//			$db = JFactory::getDbo();
//			$db->setQuery("SELECT enabled FROM #__extensions WHERE `element` = 'maximenuckmobile' AND `type` = 'plugin'");
//			$enabled = $db->loadResult();
//			if (! $enabled) {
//				$plugin_mobile_text = '<style>a[href*="mobileparams"], #attrib-maximenu_mobileparams { display: none !important; }</style>';
//			}
			$plugin_mobile_text = '<img src="' . $imgpath . 'accept.png"  />' . JText::_('MOD_MAXIMENUCK_MOBILE_MENU_INSTALLED');
			if (! JPluginHelper::isEnabled('system', 'mobilemenuck') ) {
				$plugin_mobile_text .= '<img src="' . $imgpath . 'error.png"  />' . '<a href="' . JUri::root(true) . '/administrator/index.php?option=com_plugins&filter_folder=system&filter_search=mobile+menu" class="modal" rel="{handler: \'iframe\', size: {x: 900, y: 550}}">' . JText::_('MOD_MAXIMENUCK_ACTIVATE_PLUGIN') . '</a>';
			}
		} else 
		// check if the maximenu mobile plugin is installed
		if ( file_exists(JPATH_ROOT . '/plugins/system/maximenuckmobile/maximenuckmobile.php') ) {
			$plugin_mobile_text = '<img src="' . $imgpath . 'accept.png"  />' . JText::_('MOD_MAXIMENUCK_PLUGIN_MOBILE_INSTALLED');
			if (! JPluginHelper::isEnabled('system', 'maximenuckmobile') ) {
				$plugin_mobile_text .= '<img src="' . $imgpath . 'error.png"  />' . '<a href="' . JUri::root(true) . '/administrator/index.php?option=com_plugins&filter_folder=system&filter_search=maximenu" class="modal" rel="{handler: \'iframe\', size: {x: 900, y: 550}}">' . JText::_('MOD_MAXIMENUCK_ACTIVATE_PLUGIN') . '</a>';
			}
		} else {
			$plugin_mobile_text = '<img src="' . $imgpath . 'cross.png"  />' . JText::_('MOD_MAXIMENUCK_MOBILE_MENU_NOT_INSTALLED') . '. ' . JText::_('MOD_MAXIMENUCK_CHECK_MOBILEMENUCK') . ' <a href="https://www.joomlack.fr/en/joomla-extensions/mobile-menu-ck" target="_blank">Mobile Menu CK</a>';
		}
		

		// to inform that you can add some themes
		$themes_text = '<img src="' . $imgpath . 'information.png"  />' . JText::_('MAXIMENUCK_WIZARD_MENU_DOWNLOAD_THEMES');

		$html = '<style>.maximenuckchecking {background:#efefef;border: none;
    border-radius: 3px;
    color: #333;
    font-weight: normal;
	line-height: 24px;
    padding: 5px;
	margin: 3px 0;
    text-align: left;
    text-decoration: none;
	min-width: 300px;
	box-sizing: border-box;
    }
	.maximenuckchecking img {
	margin: 5px;
    }</style>';
		$html .= '<div class="maximenuckchecking" data-name="maximenuck" data-type="component" data-folder="">' . $com_params_text . '</div>';
		$html .= '<div class="maximenuckchecking" data-name="maximenuckparams" data-type="plugin" data-folder="system">' . $plugin_params_text . '</div>';
		$html .= '<div class="maximenuckchecking" data-name="maximenuckmobile" data-type="plugin" data-folder="system">' . $plugin_mobile_text . '</div>';
		$html .= '<div class="maximenuckchecking">' . $themes_text . '</div>';
		$maximenuparamspath = JPATH_SITE .'/administrator/components/com_maximenuck/maximenuck.xml';
		if ( file_exists($maximenuparamspath) ) {
			if ($this->isOutdated('4.1.1', $maximenuparamspath)) {
				$html .= '<div class="maximenuckchecking"><img src="' . $imgpath . 'exclamation.png"  />' . JText::_('MAXIMENUCK_MAXIMENUCKPARAMS_OUTDATED') . ' : 4.1.1.'
						. '<br />'
						. JText::_('MAXIMENUCK_MAXIMENUCKPARAMS_CURRENTVERSION') . ' : ' . $this->getCurrentVersion($maximenuparamspath)
						. '</div>';
			}
		}
		$html .= $js_checking;
		return $html;
	}

	protected function getLabel() {

		return '';
	}
	
	/*
	 * Get a variable from the manifest file.
	 * 
	 * @return the current version
	 */
	private function getCurrentVersion($xmlpath) {
		// get the version installed
		$installed_version = false;
//		$xmlpath = JPATH_SITE .'/administrator/components/com_pagebuilderck/pagebuilderck.xml';
		if (! $xml_installed = JFactory::getXML($xmlpath)) {
			// die;
		} else {
			$installed_version = (string)$xml_installed->version;
		}

		return $installed_version;
	}

	/**
	 * Check if you have the latest version
	 * 
	 * @return boolean, true if outdated
	 */
	private function isOutdated($version, $xmlpath) {
		return version_compare($version, $this->getCurrentVersion($xmlpath) ) > 0;
	}
}

