<?php
/*-------------------------------------------------------------------------
# plg_dojoloader - Offlajn Dojo Loader
# -------------------------------------------------------------------------
# @ author    Roland Soos
# @ copyright Copyright (C) 2014 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.filesystem.folder' );

if(version_compare(JVERSION,'3.0.0','ge')) require_once(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'offlajnjoomla3compat'.DIRECTORY_SEPARATOR.'offlajnjoomla3compat.php');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'loader.php' );

class plgSystemDojoloader extends JPlugin {
  
  var $cache = 0;

	//function plgSystemDojoloader(& $subject) {
  function __construct(& $subject) {
		parent::__construct($subject);
 	}
  
  function onAfterRender(){
    foreach(@DojoLoader::getInstance(null) AS $loader){
      $loader->build();
    }
  }
  
  function customBuild(){
    $document = JFactory::getDocument();
    foreach(@DojoLoader::getInstance(null) AS $loader){
      $document->addScript($loader->_build());
    }
  }
  
}