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

class SlideshowckControllerAjax extends CKController {

	function __construct() {
		// security check
		if (! CKFof::checkAjaxToken()) exit;
		
		parent::__construct();
		
		$plugin = $this->input->get('plugin', '', 'cmd');
		$task = $this->input->get('task', '', 'cmd');

		if ($plugin) {
			if (file_exists(SLIDESHOWCK_PLUGINS_PATH . '/' . $plugin . '/helper/helper_' . $plugin . '.php')) {
				require_once(SLIDESHOWCK_PLUGINS_PATH . '/' . $plugin . '/helper/helper_' . $plugin . '.php');
				$className = 'SlideshowckHelpersource' . ucfirst($plugin);
				//SlideshowckHelpersourceArticles
				$class = new $className();
				if (method_exists($class, $task)) {
					$class::$task();
					exit;
				}
			}
		}
		die;
	}
}
