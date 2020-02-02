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
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

Class Sh404sefControllerPageids extends Sh404sefClassBasecontroller {

  protected $_context = 'com_sh404sef.pageids';
  protected $_defaultModel = 'urls';
  protected $_defaultView = 'pageids';
  protected $_defaultController = 'pageids';
  protected $_defaultTask = '';
  protected $_defaultLayout = 'default';

  protected $_returnController = 'pageids';
  protected $_returnTask = '';
  protected $_returnView = 'pageids';
  protected $_returnLayout = 'default';

  /**
   * Redirect to a confirmation page showing in
   * a popup window
   */
  public function confirmdelete() {

    // find and store edited item id
    $cid = JRequest::getVar('cid', array(0), 'default', 'array');

    // Set the view name and create the view object
    $viewName = 'confirm';
    $document =JFactory::getDocument();
    $viewType = $document->getType();
    $viewLayout = JRequest::getCmd( 'layout', $this->_defaultLayout );

    $view = $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->basePath));

    // push url id(s) into the view
    $view->cid = $cid;

    // tell it what to display
    $view->mainText = JText::sprintf( 'COM_SH404SEF_CONFIRM_PAGEID_DELETION', count($cid));

    // and who's gonna handle the request
    $view->actionController = $this->_defaultController;

    // and then what to do
    $view->task = 'confirmeddeletepageids';

    // Get/Create the model
    if ($model = $this->getModel( $this->_defaultModel, 'Sh404sefModel')) {
      // store initial context in model
      $model->setContext( $this->_context);

      // Push the model into the view (as default)
      $view->setModel($model, true);

    }

    // Set the layout
    $view->setLayout($viewLayout);

    // Display the view
    $view->display();

  }

  /**
   * Handles confirmation for "Purge urls" action
   *
   */
  public function confirmpurge() {

    // use actual method shared with "purge selected" feature
    $this->_doConfirmPurge( 'all');

  }

  /**
   * Handles confirmation for "Purge selected urls" action
   *
   */
  public function confirmpurgeselected() {

    // use actual method shared with "purge" feature
    $this->_doConfirmPurge( 'selected');

  }

  public function confirmeddeletepageids() {

    // Check for request forgeries
    JSession::checkToken() or jexit( 'Invalid Token' );

    // find and store edited item id
    $cid = JRequest::getVar('cid', array(0), 'default', 'array');

    // check invalid data
    if (!is_array( $cid ) || count( $cid ) < 1 || $cid[0] == 0) {
      $this->setRedirect( $this->_getDefaultRedirect(), JText::_( 'COM_SH404SEF_SELECT_ONE_PAGEID'));
    }

    // now perform pageid deletion
    // get the model to do it, actually
    // Get/Create the model
    $error = '';
    if ($model = $this->getModel( 'pageids', 'Sh404sefModel')) {
      // store initial context in model
      $model->setContext( $this->_context);

      // call the delete method on our list
      $model->deleteByIds( $cid);

      // check errors and enqueue them for display if any
      $error = $model->getError();
      if (!empty($error)) {
        $this->enqueuemessages( $error, 'error');

        // clear success message, as we have just queued some error messages
        $status = '';
      }

    }

    if (version_compare(JVERSION, '3.0', 'ge'))
    {
    	// V3: we redirect to the close page, as ajax is not used anymore to save
    	$failure = array('url' => 'index.php?option=com_sh404sef&c=pageids&view=confirm&layout=refresh&tmpl=component', 'message' => $error);
    	$success = array('url' => 'index.php?option=com_sh404sef&c=pageids&view=confirm&layout=refresh&tmpl=component',
    					'message' => JText::_('COM_SH404SEF_OPERATION_COMPLETED'));
    	if (!empty($error))
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
    	// standard display
    	$this->display();
    }

  }

  /**
   * Handles actions confirmed through the confirmation box
   */
  public function confirmedpurgepageids() {

    // Check for request forgeries
    JSession::checkToken() or jexit( 'Invalid Token' );

    // collect type of purge to make
    $type = JRequest::getCmd( 'delete_type');

    switch($type) {
      case 'all':
        break;
      case 'selected':
        break;
      default:
        $this->setError('Invalid data');
        return;
        break;
    }

    // now perform meta data deletion
    // get the model to do it, actually
    // Get/Create the model
    if ($model = $this->getModel( 'pageids', 'Sh404sefModel')) {
      // store initial context in model
      $model->setContext( 'com_sh404sef.pageids.pageids.default');

      // call the delete method on our list
      $model->purgePageids( $type);

      // check errors and enqueue them for display if any
      $error = $model->getError();
      if (!empty($error)) {
        $this->setError( $error);
      }

    }
    // return result to caller
    $this->display();
  }

  public function import() {

  }

  public function export() {

  }

  /**
   * Redirect to a confirmation page showing in
   * a popup window
   */
  private function _doConfirmPurge( $type = 'allmeta') {

    // Set the view name and create the view object
    $viewName = 'confirm';
    $document =JFactory::getDocument();
    $viewType = $document->getType();
    $viewLayout = JRequest::getCmd( 'layout', $this->_defaultLayout );

    $view = $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->basePath));

    // and who's gonna handle the request
    $view->actionController = $this->_defaultController;

    // Get/Create the model
    if ($model = $this->getModel( 'pageids', 'Sh404sefModel')) {
      // store context of the main url view in the model
      $model->setContext( 'com_sh404sef.pageids.pageids.default');

      // Push the model into the view (as default)
      $view->setModel($model, true);

    }

    // tell it what to display
    // we only purge automatic sef urls, count that
    $numberOfPageids = $model->getPageIdsCount( $type);

    // if nothing to do, say so and return to main page
    if (empty( $numberOfPageids)) {
      $view->redirectTo = $this->_getDefaultRedirect();
      $view->message = JText::_('COM_SH404SEF_NORECORDS');
    } else {

      // calculate the message and some hidden data to be passed
      // through the confirmation box
      switch ($type) {
        case 'selected':
          $mainText  = JText::sprintf( 'COM_SH404SEF_CONFIRM_PAGEID_DELETION', $numberOfPageids);
          break;
        case 'all':
          $mainText  = JText::sprintf( 'COM_SH404SEF_CONFIRM_PAGEID_DELETION', $numberOfPageids);
        default:
          break;
      }

      // and then what to do
      $view->task = 'confirmedpurgepageids';
      $hiddenText = '<input type="hidden" name="delete_type" value="' . $type . '" />';

      // push that into the view
      $view->mainText = $mainText;
      $view->hiddenText = $hiddenText;
    }
    // Set the layout
    $view->setLayout($viewLayout);

    // Display the view
    $view->display();

  }


}
