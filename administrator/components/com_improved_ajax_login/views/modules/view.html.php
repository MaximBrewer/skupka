<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Improved_ajax_login.
 */
class Improved_ajax_loginViewModules extends JViewLegacy
{

	public function display($tpl = null)
	{
    header('Location: '.JRoute::_('index.php?option=com_modules'.
      '&filter_search=&filter_state=&filter_position=&filter_access='.
      '&filter_language=&filter_module=mod_improved_ajax_login', false));
    exit;
	}

}
