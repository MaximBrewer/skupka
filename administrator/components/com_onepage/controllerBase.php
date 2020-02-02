<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/ 
// no direct access
defined('_JEXEC') or die('Restricted access');
 

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
if (!class_exists('OPCController')) 
if(version_compare(JVERSION,'3.0.0','ge')) 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'joomla3'.DIRECTORY_SEPARATOR.'opccontroller.php'); 
else
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'joomla2'.DIRECTORY_SEPARATOR.'opccontroller.php'); 

//require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 
JLoader::register('OnepageHelper', dirname(__FILE__) .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'onepage.php');
class JControllerBase extends OPCController
{
    function getViewName() { JError::raise(500,"getViewName() not implemented"); } /* abstract */

    function getModelName() { JError::raise(500,"getModelName() not implemented"); } /* abstract */

    function getLayoutName() { return 'default'; }
	
    function display($cache = false, $urlparams=false)
    {      
	
	    $cview = JRequest::getVar('view', 'config'); 
        $doc = JFactory::getDocument();
		$viewType = $doc->getType();
        $view = $this->getView( ucfirst($this->getViewName()), $viewType);
        /*
        if (ucfirst($this->getViewName())=='Order_details')
        {
          $order_id = JRequest::getVar('order_id');
          if (!isset($order_id))
          {
           $view = &$this->getView( ucfirst('orders'), $viewType);
          }
         
        }
	   */
        if ($model = $this->getModel($this->getModelName()))
        {
		
         if ($this->getModelName()=='order_details')
         {
          $order_id = JRequest::getVar('order_id');
          if (!isset($order_id))
          {
           
           $model = &$this->getModel('orders');
          }
         }
            $view->setModel($model, true);
        } 
        else
        {
        $viewn = ucfirst($this->getViewName());
        if (!empty($viewn))
        {
           $view = &$this->getView( ucfirst($viewn), $viewType);
           $model = &$this->getModel($viewn);
           $view->setModel($model, true);
        }
        }
		
        $view->setLayout($this->getLayoutName());
        
		$view->display();
		
		OnepageHelper::addSubmenu($cview); 
    }	
	
} 

