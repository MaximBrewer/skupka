<?php
/**
 * @package		RuposTel Ajax search pro
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class RupsearchController extends JControllerLegacy
{
	function __construct($config = array())
	{
		
		parent::__construct($config);
	}

	
	public function display($cachable = false, $urlparams = false)
	{
		$cachable = false;
		$vName	= JRequest::getCmd('view', 'search');
		JRequest::setVar('view', $vName);
		parent::display($cachable, $urlparams);

		return $this;
	}
}
