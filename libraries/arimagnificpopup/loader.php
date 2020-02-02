<?php
/*
 *
 * @package		ARI Libraries
 * @author		ARI Soft
 * @copyright	Copyright (c) 2016 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die;

$sysLibPath = dirname(__FILE__) . '/../arisoft/loader.php';

if (!file_exists($sysLibPath))
	JFactory::getApplication()->enqueueMessage('ARI Magnific Popup: "ARI Soft" library is not installed.', 'warning');
else
	require_once $sysLibPath;

require_once dirname(__FILE__) . '/defines.php';

JLoader::registerNamespace('Arimagnificpopup', dirname(__FILE__));