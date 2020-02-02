<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

Class Sh404sefControllerEditnotfound extends Sh404sefClassBaseeditcontroller
{

	protected $_context = 'com_sh404sef.editnotfound';
	protected $_editController = 'editnotfound';
	protected $_editView = 'editnotfound';
	protected $_editLayout = 'default';
	protected $_defaultModel = 'editnotfound';
	protected $_defaultView = 'editnotfound';

	protected $_returnController = 'urls';
	protected $_returnTask = '';
	protected $_returnView = 'default';
	protected $_returnLayout = 'view404';

	/**
	 * Handle creating a redirect from the 404 requests
	 * manager. 
	 */
	public function newredirect()
	{

		// hide the main menu
		JRequest::setVar('hidemainmenu', 1);

		// find and store edited item id . should be 0, as this is a new url
		$cid = JRequest::getVar('cid', array(0), 'default', 'array');
		$this->_id = $cid[0];

		// need to get the view to push the url data into it
		$viewName = JRequest::getWord('view');
		if (empty($viewName))
		{
			JRequest::setVar('view', $this->_defaultView);
		}

		$document = JFactory::getDocument();

		$viewType = $document->getType();
		$viewName = JRequest::getCmd('view');
		$this->_editView = $viewName;
		$viewLayout = JRequest::getCmd('layout', $this->_defaultLayout);

		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath));

		// Call the base controller to do the rest
		$this->display();

	}

	public function save()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		// save incoming data
		$this->_editData = JRequest::get('post');

		// find and store edited item id
		$this->_id = JRequest::getInt('id');

		// perform saving of incoming data
		$savedId = $this->_doSave($this->_editData);

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			// V3: we redirect to the close page, as ajax is not used anymore to save
			$failure = array('url' => 'index.php?option=com_sh404sef&c=editnotfound&view=editnotfound&tmpl=component',
				'message' => JText::sprintf('JERROR_SAVE_FAILED', $this->getError()));
			$success = array('url' => 'index.php?option=com_sh404sef&tmpl=component&c=editnotfound&view=editnotfound&layout=refresh',
				'message' => JText::_('COM_SH404SEF_ELEMENT_SAVED'));
			if (empty($savedId))
			{
				// Save failed, go back to the screen and display a notice.
				$this->setRedirect(JRoute::_($failure['url'], false), $failure['message'], 'error');
				return false;
			}

			$this->setRedirect(JRoute::_($success['url'], false), $success['message'], 'message');
			return true;
		}
		else
		{
			// error ?
			if (empty($savedId))
			{
				// edit again with same data
				$errorMsg = $this->getError();
				$errorMsg = empty($errorMsg) ? $this->_getMessage('failure') : $errorMsg;
				$this->setError($errorMsg);
				JRequest::setVar('c', $this->_editController);
				JRequest::setVar('task', $this->_editTask);
				JRequest::setVar('cid', array($this->_id));
				// in case of error, if this is an ajax call,
				// we simply return the error to the caller
				$isAjax = JRequest::getInt('shajax') == 1;
				if (!$isAjax)
				{
					// if not ajax, we edit again the same page
					$this->edit();
					return false;
				}
			}
			// saved, no need to keep them
			$this->_editData = null;
			// display response
			$this->display();
		}
	}
}
