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
 
if(version_compare(JVERSION,'3.0.0','ge')) 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibilityj3.php'); 
else
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibilityj2.php'); 

jimport( 'joomla.application.component.controller' );
//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'helper.php'); 

class JControllerBase extends OPCController
{
    protected $default_view = 'config';
    function getViewName() { JError::raise(500,"getViewName() not implemented"); } /* abstract */

    function getModelName() { JError::raise(500,"getModelName() not implemented"); } /* abstract */

    function getLayoutName() { return 'default'; }
	
    function display($cache = false, $urlparams=false)
    {      
		parent::display();
	   
		
		
    }	
	
} 

