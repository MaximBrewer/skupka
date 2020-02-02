<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-3/Plugins/JoomQuickicon/trunk/joomgallery.php $
// $Id: joomgallery.php 4106 2013-02-20 21:38:54Z erftralle $
/******************************************************************************\
**   JoomGallery Plugin 'JoomQuickicon'                                       **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2012 - 2013 JoomGallery::ProjectTeam                       **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html                            **
\******************************************************************************/

defined('_JEXEC') or die;

/**
 * Joomgallery Plugin 'JoomQuickicon'
 *
 * This plugin integrates a JoomGallery icon into the control panel of the
 * back end.
 *
 * @package     JoomGallery
 * @since       2.0
 */
class plgQuickiconJoomGallery extends JPlugin
{
  /**
   * Constructor
   *
   * @param       object  $subject The object to observe
   * @param       array   $config  An array that holds the plugin configuration
   *
   * @since       2.0
   */
  public function __construct(& $subject, $config)
  {
    parent::__construct($subject, $config);
    $this->loadLanguage();
  }

  /**
   * Returns an icon definition for a JoomGallery icon.
   *
   * @param  $context The calling context
   * @return array    A list of icon definition associative arrays, consisting of the
   *                  keys link, image, text and access.
   * @since  2.0
   */
  public function onGetIcons($context)
  {
    // Check wether JoomGallery is installed
    $file = JPATH_ROOT.'/administrator/components/com_joomgallery/includes/defines.php';
    if(file_exists($file))
    {
      require_once $file;
    }
    else
    {
      return;
    }

    if(    $context == trim($this->params->get('cfg_context', 'mod_quickicon'))
        &&
           JFactory::getUser()->authorise('core.manage', _JOOM_OPTION)
      )
    {
      return array(array(
                          'link' => 'index.php?option='._JOOM_OPTION,
                          'image' => 'pictures',
                          'text' => JText::_('PLG_QUICKICON_JOOMGALLERY_TXT'),
                          'id' => 'plg_quickicon_joomgallery'
                        )
                  );
    }
    else
    {
      return;
    }
  }
}