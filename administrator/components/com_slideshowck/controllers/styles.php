<?php
/**
 * @name		Slider CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2016. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Pages list controller class.
 */
class SlideshowckControllerStyles extends JControllerAdmin {

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'style', $prefix = 'SlideshowckModel', $config = array()) {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

}