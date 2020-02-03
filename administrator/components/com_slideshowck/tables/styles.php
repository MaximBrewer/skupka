<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

// No direct access
defined('_JEXEC') or die;

class SlideshowckTableStyles extends JTable {

	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db) {
		$this->setColumnAlias('published', 'state');
		parent::__construct('#__slideshowck_styles', 'id', $db);
	}
}
