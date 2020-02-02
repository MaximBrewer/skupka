<?php
/**
 * @version		$Id: view.html.php RuposTel.com
 * @copyright	Copyright (C) 2005 - 2011 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JViewNumbering extends OPCView
{
	
	public function display($tpl = null)
	{
		
	    $this->model = $this->getModel();
		$this->assignRef('model', $this->model); 
		parent::display($tpl);
		
	}

}
