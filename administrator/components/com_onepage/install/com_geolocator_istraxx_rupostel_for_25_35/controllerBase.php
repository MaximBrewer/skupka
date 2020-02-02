<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* GeoLocator is free software released under GNU/GPL  This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* This php file was created by www.rupostel.com team
*/

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// no direct access
defined('_JEXEC') or die('Restricted access');
 
jimport( 'joomla.application.component.controller' );
 
class JControllerBase extends JControllerLegacy
{
    function __construct($default = array())
   {
     parent::__construct($default); 
     $this->registerTask('save', 'save'); 
	 $this->registerTask('install', 'install'); 
   }
    //function getViewName() { JError::raise(500,"getViewName() not implemented"); } /* abstract */

    //function getModelName() { JError::raise(500,"getModelName() not implemented"); } /* abstract */

    function getLayoutName() { return 'default'; }
	
    function display($cachable = false, $urlparams = array())
    {      
        $doc = JFactory::getDocument();
		$viewType = $doc->getType();
        $view = $this->getView( ucfirst($this->getViewName()), $viewType);

        $viewn = ucfirst($this->getViewName());
        if (!empty($viewn))
        {
           $view = $this->getView( ucfirst($viewn), $viewType);
           $model = $this->getModel($viewn);
           $view->setModel($model, true);
        }

        $view->setLayout($this->getLayoutName());
        $view->display();
    }	
	
} 
