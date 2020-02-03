<?php
/**
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

// No direct access
defined('_JEXEC') or die;

use \Slideshowck\CKController;
use \Slideshowck\CKFof;

class SlideshowckControllerStyle extends CKController {

//	public function add() {
//		$this->edit(0);
		// Redirect to the edit screen.
//		CKFof::redirect(SLIDESHOWCK_ADMIN_URL . '&view=style&layout=edit&id=0&tmpl=component&layout=modal');
//	}

//	public function edit($id = null, $appendUrl = '') {
//		parent::edit($id, '&layout=modal&tmpl=component');
//	}
//
//	public function copy() {
//		parent::edit('&layout=modal&tmpl=component');
//	}

	/*
	 * Generate the CSS styles from the settings
	 */
	public function save() {
		// security check
		if (! CKFof::checkAjaxToken()) {
			exit();
		}

		$id = $this->input->get('id', 0, 'int');

		$model = $this->getModel();
		$row = $model->getItem($id);

		// get data
		$fields = $this->input->get('fields', '', 'raw');
		$name = $this->input->get('name', '', 'string');
		if (! $name) $name = 'style' . $id;
		$layoutcss = trim($this->input->get('layoutcss', '', 'html'));
		// set data
		$row->params = $fields;
		$row->name = $name;
		$row->layoutcss = $layoutcss;

		if (! $id = $model->save($row)) {
			echo "{'result': '0', 'id': '" . $row->id . "', 'message': 'Error : Can not save the Styles !'}";
			echo($this->_db->getErrorMsg());
			exit;
		}
		echo '{"result": "1", "id": "' . $id . '", "message": "Styles saved successfully"}';
		exit;
	}

	/**
	 * copy an existing page
	 * @return void
	 */
//	function copy() {
//		$model = $this->getModel();
//		$cid = $this->input->get('cid', '', 'array');
//		$this->input->set('id', (int) $cid[0]);
//		if (!$model->copy()) {
//			$msg = JText::_('CK_COPY_ERROR');
//			$type = 'error';
//		} else {
//			$msg = JText::_('CK_COPY_SUCCESS');
//			$type = 'message';
//		}
//
//		$this->setRedirect('index.php?option=com_slideshowck&view=styles', $msg, $type);
//	}

	/*
	 * Generate the CSS styles from the settings
	 */
	public function ajaxRenderCss() {
		$fields = $this->input->get('fields', '', 'raw');
		$fields = json_decode($fields);
		$customstyles = stripslashes( $this->input->get('customstyles', '', 'string'));
		$customstyles = json_decode($customstyles);
		$customcss = $this->input->get('customcss', '', 'html');

		$css = $this->renderCss($fields, $customstyles);
		echo $css . $customcss;
		exit();
	}

	/*
	 * Render the CSS from the settings
	 */
	public function renderCss($fields, $customstyles) {
		include_once SLIDESHOWCK_PATH . '/helpers/ckstyles.php';
		$ckstyles = new \Slideshowck\CKStyles();
		$css = $ckstyles->create($fields, $customstyles);

		return $css;
	}

	/**
	 * Ajax method to save the json data into the .mmck file
	 *
	 * @return  boolean - true on success for the file creation
	 *
	 */
	public function exportParams() {
		// security check
		if (! CKFof::checkAjaxToken()) {
			exit();
		}
		// create a backup file with all fields stored in it
		$fields = $this->input->get('jsonfields', '', 'string');
		$backupfile_path = SLIDESHOWCK_PATH . '/export/exportParamsSlideshowckStyle'. $this->input->get('styleid',0,'int') .'.mmck';
		if (file_put_contents($backupfile_path, $fields)) {
			echo '1';
		} else {
			echo '0';
		}

		exit();
	}

	/**
	 * Ajax method to import the .mmck file into the interface
	 *
	 * @return  boolean - true on success for the file creation
	 *
	 */
	public function uploadParamsFile() {
		// security check
		if (! CKFof::checkAjaxToken()) {
			exit();
		}

		$file = $this->input->files->get('file', '', 'array');
		if (!is_array($file))
			exit();

		$filename = JFile::makeSafe($file['name']);

		// check if the file exists
		if (JFile::getExt($filename) != 'mmck') {
			$msg = JText::_('CK_NOT_MMCK_FILE', true);
			echo json_encode(array('error'=> $msg));
			exit();
		}

		//Set up the source and destination of the file
		$src = $file['tmp_name'];

		// check if the file exists
		if (!$src || !JFile::exists($src)) {
			$msg = JText::_('CK_FILE_NOT_EXISTS', true);
			echo json_encode(array('error'=> $msg));
			exit();
		}

		// read the file
		if (!$filecontent = JFile::read($src)) {
			$msg = JText::_('CK_UNABLE_READ_FILE', true);
			echo json_encode(array('error'=> $msg));
			exit();
		}

		// replace vars to allow data to be moved from another server
		$filecontent = str_replace("|URIROOT|", JUri::root(true), $filecontent);
//		$filecontent = str_replace("|qq|", '"', $filecontent);

//		echo $filecontent;
		echo json_encode(array('data'=> $filecontent));
		exit();
	}

	/**
	 * Ajax method to read the fields values from the selected preset
	 *
	 * @return  json - 
	 *
	 */
	function loadPresetFields() {
		// security check
		if (! CKFof::checkAjaxToken()) {
			exit();
		}

		$preset = $this->input->get('preset', '', 'string');
		$folder_path = SLIDESHOWCK_MEDIA_PATH . '/presets/';
		// load the fields
		$fields = '{}';
		if ( file_exists($folder_path . $preset. '.mmck') ) {
			$fields = @file_get_contents($folder_path . $preset. '.mmck');
			$fields = str_replace('\n','', $fields);
//			$fields = str_replace("{", "|ob|", $fields);
//			$fields = str_replace("}", "|cb|", $fields);
		} else {
			echo '{"result" : 0, "message" : "File Not found : '.$folder_path . $preset. '.mmck'.'"}';
			exit();
		}

		echo '{"result" : 1, "fields" : "'.$fields.'", "customcss" : ""}';
		exit();
	}
}