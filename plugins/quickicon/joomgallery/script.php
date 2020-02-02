<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-3/Plugins/JoomQuickicon/trunk/script.php $
// $Id: script.php 4106 2013-02-20 21:38:54Z erftralle $
/******************************************************************************\
**   JoomGallery Plugin 'JoomQuickicon'                                       **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2012 - 2013 JoomGallery::ProjectTeam                       **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html                            **
\******************************************************************************/

defined('_JEXEC') or die;

/**
 * Installer class for the JoomGallery Plugin 'JoomQuickicon'
 *
 * @package     JoomGallery
 * @since       2.0
 */
class plgQuickiconJoomGalleryInstallerScript
{
  function install()
  { 
     // Automatically enable the plugin during installation
    $db = JFactory::getDbo();
    
    $query = $db->getQuery(true)
          ->update('#__extensions')
          ->set('enabled = 1')
          ->where("type = 'plugin'")
          ->where("element = 'joomgallery'")
          ->where("folder = 'quickicon'");
     
    $db->setQuery($query);
    $db->query();
     
    echo '<p class="alert alert-info">'. JText::_('PLG_QUICKICON_PLUGIN_ENABLED') .'</p>';    
  } 
}