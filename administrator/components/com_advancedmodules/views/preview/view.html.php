<?php
/**
 * @package         Advanced Module Manager
 * @version         7.5.1PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Modules component
 *
 * @static
 * @since  1.6
 */
class AdvancedModulesViewPreview extends JViewLegacy
{
	public function display($tpl = null)
	{
		$editor = JFactory::getConfig()->get('editor');

		$this->editor = JEditor::getInstance($editor);

		parent::display($tpl);
	}
}
