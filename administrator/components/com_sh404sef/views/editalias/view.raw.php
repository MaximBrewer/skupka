<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.view');

class Sh404sefViewEditalias extends ShlMvcView_Base
{
	public function display($tpl = null)
	{
		// declare docoument mime type
		$document = JFactory::getDocument();
		$document->setMimeEncoding('text/xml');

		$this->refreshAfter = JFactory::getApplication()->input->getCmd('refreshafter');

		// call helper to prepare response xml file content
		$response = Sh404sefHelperGeneral::prepareAjaxResponse($this);

		// echo it
		echo $response;
	}
}
