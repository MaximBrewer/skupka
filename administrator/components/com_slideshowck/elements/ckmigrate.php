<?php
/**
 * @copyright	Copyright (C) 2019 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */

defined('JPATH_PLATFORM') or die;

use \Slideshowck\CKFof;
use \Slideshowck\CKFolder;
use \Slideshowck\CKFile;
use \Slideshowck\CKText;

require_once 'ckformfield.php';
require_once JPATH_ROOT . '/administrator/components/com_slideshowck/helpers/ckfof.php';
require_once JPATH_ROOT . '/administrator/components/com_slideshowck/helpers/ckfolder.php';
require_once JPATH_ROOT . '/administrator/components/com_slideshowck/helpers/ckfile.php';
require_once JPATH_ROOT . '/administrator/components/com_slideshowck/helpers/cktext.php';

class JFormFieldCkmigrate extends CKFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 */
	protected $type = 'ckmigrate';

	private $options;

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 */
	protected function getInput()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->get('id', 0, 'int');
		$doMigration = $input->get('domigration', 0, 'int');
		if (! $id) return '';

		$options = $this->getModuleOptions($id);
//var_dump($options);die;
		$params = json_decode($options->params);
		if (isset($params->slidesssource)) {
			$this->makeBackup($id, $options->params);
			if ($doMigration) {
				$this->doMigration($id, $options->params);
			} else {
				CKfof::enqueueMessage(CKText::_('SLIDESHOWCK_MIGRATION_NEEDED'), 'warning');
				CKfof::enqueueMessage('<a href="' . CKFof::getCurrentUri() . '&domigration=1">' . CKText::_('SLIDESHOWCK_MIGRATION_ACTION') . '</a>', 'warning');
			}
		}

		if ($this->isPluginEnabled()) {
			CKFof::dbExecute("UPDATE #__extensions SET enabled = 0 WHERE element = 'slideshowckparams'");
			CKfof::enqueueMessage(CKText::_('SLIDESHOWCK_PARAMS_UNPUBLISHED_INFO'), 'warning');
			CKfof::enqueueMessage('<a href="https://www.joomlack.fr/en/documentation/48-slideshow-ck/246-migration-from-slideshow-ck-version-1-to-version-2" target="_blank">' . CKText::_('SLIDESHOWCK_PARAMS_MIGRATION_LINK') . '</a>', 'warning');
			CKfof::redirect();
		}

		$this->alertObsoletePlugin($params, 'hikashop');
		$this->alertObsoletePlugin($params, 'k2');
		$this->alertObsoletePlugin($params, 'joomgallery');
	}

	protected function alertObsoletePlugin($params, $plugin) {
		if (isset($params->source) && $params->source == $plugin && $this->isPluginEnabled('slideshowck' . $plugin)) {
			CKfof::enqueueMessage(CKText::_('SLIDESHOWCK_WARNING_PLUGIN_OBSOLETE') . ' : ' . '<b>slideshowck' . $plugin . '</b>', 'warning');
			CKfof::enqueueMessage('<a href="index.php?option=com_plugins&view=plugins&filter_element=slideshowck' . $plugin . '" target="_blank">' . CKText::_('SLIDESHOWCK_DISABLE_PLUGIN') . '</a>', 'warning');
		}
	}

	protected function isPluginEnabled($plugin = 'slideshowckparams') {
		if (file_exists(JPATH_ROOT . '/plugins/system/' . $plugin)) {
			$isEnabled = CKFof::dbLoadResult("SELECT enabled FROM #__extensions WHERE element = '" . $plugin . "'");
			return (bool)$isEnabled;
		}
		return false;
	}

	protected function getModuleOptions($id) {
		if (empty($this->options)) {
			$this->options = CKFof::dbLoadObject("SELECT * FROM #__modules WHERE id = " . (int)$id);
		}
		return $this->options;
	}

	protected function doMigration($id, $params) {
		$find = array('slidesssource', 'imagetarget', 'articlelength', 'showarticletitle', 'lightboxtype', 'lightboxautolinkimages');
		$replace = array('source', 'linktarget', 'textlength', 'usetitle', 'lightbox', 'linkautoimage');
		$newparams = str_replace($find, $replace, $params);

		if ($this->isPluginEnabled('slideshowck')) {
			$paramsObj = new JRegistry($newparams);
			if ($paramsObj->get('slideshowckhikashop_enable', '0') == '1') $paramsObj->set('source', 'hikashop');
			if ($paramsObj->get('slideshowckjoomgallery_enable', '0') == '1') $paramsObj->set('source', 'joomgallery');
			if ($paramsObj->get('slideshowckk2_enable', '0') == '1') $paramsObj->set('source', 'k2');
			$newparams = json_encode($paramsObj);
		}

		$data = CKFof::dbLoad('#__modules', $id);
		$data->id = $id;
		$data->params = $newparams;

		$return = CKFof::dbStore('#__modules', $data);

		if ($return) {
			CKfof::enqueueMessage(CKText::_('SLIDESHOWCK_MIGRATION_SUCCESS'), 'success');
		} else {
			CKfof::enqueueMessage(CKText::_('SLIDESHOWCK_MIGRATION_ERROR'), 'error');
		}
		CKfof::redirect();
	}

	protected function makeBackup($id, $params) {
		$path = JPATH_ROOT . '/administrator/components/com_slideshowck/backup/';

		// create the folder
		if (! CKFolder::exists($path)) {
			CKFolder::create($path);
		}

		$exportfiledest = $path . '/backup_' . $id . '_' . date("d-m-Y-G-i-s") . '.ssck';
		CKFile::write($exportfiledest, $params);
	}
}
