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

define('OPC_JOOMLA_DIR', JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'joomla2'); 
define('OPCJ3', false); 

if(!class_exists('JParameter')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'parameter.php');

JLoader::register('OPCcontroller', OPC_JOOMLA_DIR.DIRECTORY_SEPARATOR.'opccontroller.php' );
JLoader::register('OPCModel', OPC_JOOMLA_DIR.DIRECTORY_SEPARATOR.'opcmodel.php' );
JLoader::register('OPCView', OPC_JOOMLA_DIR.DIRECTORY_SEPARATOR.'opcview.php' );
JLoader::register('OPCPane', OPC_JOOMLA_DIR.DIRECTORY_SEPARATOR.'opcpane.php' );
JLoader::register('OPCParameter', OPC_JOOMLA_DIR.DIRECTORY_SEPARATOR.'opcparameter.php' );
JLoader::register('OPCObj', OPC_JOOMLA_DIR.DIRECTORY_SEPARATOR.'opcobj.php' );
JLoader::register('OPCUtility', OPC_JOOMLA_DIR.DIRECTORY_SEPARATOR.'opcutility.php' );
JLoader::register('JHTMLOPC', OPC_JOOMLA_DIR.DIRECTORY_SEPARATOR.'jhtmlopc.php' );
