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
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

Class Sh404sefControllerEditalias extends Sh404sefClassBaseeditcontroller
{
	protected $_context = 'com_sh404sef.editalias';
	protected $_editController = 'editalias';
	protected $_editView = 'editalias';
	protected $_editLayout = 'default';
	protected $_defaultModel = 'editalias';
	protected $_defaultView = 'editalias';

	protected $_returnController = 'aliases';
	protected $_returnTask = '';
	protected $_returnView = 'aliases';
	protected $_returnLayout = 'default';

	/**
	 * Handle creating aliases from the aliases
	 * manager.
	 */
	public function newalias()
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

		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath));

		// Call the base controller to do the rest
		$this->display();

	}

	/**
	 * Handle editing aliases from the aliases
	 * manager. cid contains the id of the
	 * alias record we want to edit. Need to be
	 * turned into that of the SEF url id,
	 * so as to be able to use the editurl view
	 * to edit all aliases for this url
	 */
	public function edit()
	{
		// hide the main menu
		JRequest::setVar('hidemainmenu', 1);

		// find and store edited item id
		$cid = JRequest::getVar('cid', array(0), 'default', 'array');
		$this->_id = $cid[0];

		// find to which url this alias record belongs to
		// get a model and ask for the matching URL record
		$model = $this->getModel('aliases', 'Sh404sefModel');
		$url = $model->getUrlByAliasId($this->_id);

		// push that as a request var, so that we fake editing an url
		if (!empty($url) && !empty($url->id))
		{
			$cid = array($url->id);
		}
		else
		{
			$cid = array(0);
		}
		Jrequest::setVar('cid', $cid);

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

		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath));

		// now we can push the url into the view
		$view->url = $url;
		// will prevent user from editing the non-sef url
		$view->noUrlEditing = true;

		// Call the base controller to do the rest
		$this->display();
	}

	/**
	 * Redirect to a confirmation page showing in
	 * a popup window
	 */
	public function confirmdelete()
	{
		// find and store edited item id
		$cid = JRequest::getVar('cid', array(0), 'default', 'array');

		// Set the view name and create the view object
		$viewName = 'confirm';
		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$viewLayout = JRequest::getCmd('layout', $this->_defaultLayout);

		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath));

		// push url id(s) into the view
		$view->cid = $cid;

		// tell it what to display
		$view->mainText = JText::sprintf('COM_SH404SEF_CONFIRM_ALIAS_DELETION', count($cid));

		// and who's gonna handle the request
		$view->actionController = $this->_editController;

		// Get/Create the model
		if ($model = $this->getModel($this->_defaultModel, 'Sh404sefModel'))
		{
			// store initial context in model
			$model->setContext($this->_context);

			// Push the model into the view (as default)
			$view->setModel($model, true);

		}

		// Set the layout
		$view->setLayout($viewLayout);

		// Display the view
		$view->display();
	}

	public function confirmed()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		// find and store edited item id
		$cid = JRequest::getVar('cid', array(0), 'default', 'array');

		// check invalid data
		if (!is_array($cid) || count($cid) < 1 || $cid[0] == 0)
		{
			$this->setRedirect($this->_getDefaultRedirect(), JText::_('COM_SH404SEF_SELECT_ALIAS'));
		}

		// now perform alias deletion
		// get the model to do it, actually
		// Get/Create the model
		$errors = '';
		if ($model = $this->getModel($this->_defaultModel, 'Sh404sefModel'))
		{
			// store initial context in model
			$model->setContext($this->_context);

			// call the delete method on our list
			$model->deleteByIds($cid);

			// check errors and enqueue them for display if any
			$errors = $model->getErrors();
			if (!empty($errors))
			{
				$this->enqueuemessages($errors, 'error');
			}
		}

		// in J3 no ajax, we have a "closing" layout
		if (version_compare(JVERSION, '3.0.', 'ge'))
		{
			// V3: we redirect to the close page, as ajax is not used anymore to save
			$failure = array('url' => 'index.php?option=com_sh404sef&tmpl=component&c=editalias&view=editurl&layout=refresh&refreshafter=8000',
				'message' => $model->getError());
			$success = array('url' => 'index.php?option=com_sh404sef&tmpl=component&c=editalias&view=editurl&layout=refresh',
				'message' => JText::_('COM_SH404SEF_ELEMENT_DELETED'));
			if (!empty($errors))
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
			// send back response through default view
			$this->display();
		}

	}

	/**
	 * Hook for the "confirmed" task, until our
	 * confirm view is a bit more flexible
	 */
	public function delete()
	{
		$this->confirmed();
	}

}
