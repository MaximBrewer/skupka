<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */
 
// No direct access
defined('CK_LOADED') or die;

use \Slideshowck\CKController;
use \Slideshowck\CKFof;
use \Slideshowck\CKText;

class SlideshowckController extends CKController {

	static function getInstance($prefix = '') {
		return parent::getInstance('Slideshowck');
	}

	public function display($cachable = false, $urlparams = false) {
		$view = $this->input->get('view', 'about');
		$this->input->set('view', $view);

		parent::display();

		return $this;
	}

	public static function ajaxCreateFolder() {
		// security check
		if (! CKFof::checkAjaxToken()) {
			exit();
		}

		if (CKFof::userCan('create', 'com_media')) {
			$input = CKFof::getInput();
			$path = $input->get('path', '', 'string');
			$name = $input->get('name', '', 'string');

			require_once SLIDESHOWCK_PATH . '/helpers/ckbrowse.php';
			if ($result = CKBrowse::createFolder($path, $name)) {
				$msg = CKText::_('CK_FOLDER_CREATED_SUCCESS');
			} else {
				$msg = CKText::_('CK_FOLDER_CREATED_ERROR');
			}

			echo '{"status" : "' . ($result == false ? '0' : '1') . '", "message" : "' . $msg . '"}';
		} else {
			echo '{"status" : "2", "message" : "' . CKText::_('CK_ERROR_USER_NO_AUTH') . '"}';
		}
		exit;
	}

	/**
	 * Get the file and store it on the server
	 * 
	 * @return mixed, the method return
	 */
	public function ajaxAddPicture() {
		require_once SLIDESHOWCK_PATH . '/helpers/ckbrowse.php';
		CKBrowse::ajaxAddPicture();
	}

	/**
	 * Ajax method to clean the name of the google font
	 */
	public function cleanGfontName() {
		$input = new JInput();
		$gfont = $input->get('gfont', '', 'string');

		// <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
		// Open+Sans+Condensed:300
		// Open Sans
		if ( preg_match( '/family=(.*?) /', $gfont . ' ', $matches) ) {
			if ( isset($matches[1]) ) {
				$gfont = $matches[1];
			}
		}

		$gfont = str_replace(' ', '+', ucwords (trim($gfont)));
		echo trim(trim($gfont, "'"));
		die;
	}
}
