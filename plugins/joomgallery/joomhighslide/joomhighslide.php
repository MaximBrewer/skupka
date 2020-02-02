<?php
// $HeadURL: https://joomgallery.org/svn/joomgallery/JG-3/Plugins/JoomHighslide/trunk/joomhighslide.php $
// $Id: joomhighslide.php 4189 2013-04-08 16:45:02Z chraneco $
/****************************************************************************************\
**   Plugin 'JoomHighslide' 2.0                                                         **
**   By: JoomGallery::ProjectTeam                                                       **
**   Copyright (C) 2010 - 2012 Patrick Alt                                              **
**   Released under GNU GPL Public License                                              **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look                       **
**   at administrator/components/com_joomgallery/LICENSE.TXT                            **
\****************************************************************************************/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

require_once JPATH_ADMINISTRATOR.'/components/com_joomgallery/helpers/openimageplugin.php';

/**
 * JoomGallery Highslide JS Plugin
 *
 * With this plugin JoomGallery is able to use
 * Highslide JS (http://www.highslide.com/) for displaying images.
 *
 * NOTE: Please remember that Highslide JS is licensed under the terms
 * of the 'Creative Commons Attribution-NonCommercial 2.5 License':
 *
 * http://highslide.com/#licence
 * http://creativecommons.org/licenses/by-nc/2.5/
 *
 * @package     Joomla
 * @subpackage  JoomGallery
 * @since       1.5
 */
class plgJoomGalleryJoomHighslide extends JoomOpenImagePlugin
{
  /**
   * Name of this popup box
   *
   * @var   string
   * @since 3.0
   */
  protected $title = 'Highslide JS';

  /**
   * Initializes the box by adding all necessary JavaScript and CSS files.
   * This is done only once per page load.
   *
   * Please use the document object of Joomla! to add JavaScript and CSS files, e.g.:
   * <code>
   * $doc = JFactory::getDocument();
   * $doc->addStyleSheet(JUri::root().'media/plg_exampleopenimage/css/exampleopenimage.css');
   * $doc->addScript(JUri::root().'media/plg_exampleopenimage/js/exampleopenimage.js');
   * $doc->addScriptDeclaration("    jQuery(document).ready(function(){ExampleOpenImage.init()}");
   * </code>
   *
   * or if using Mootools or jQuery the respective JHtml method:
   * <code>
   * JHtml::_('jquery.framework');
   * JHtml::_('behavior.framework');
   * </code>
   *
   * @return  void
   * @since   3.0
   */
  protected function init()
  {
    $doc = JFactory::getDocument();

   

          $doc->addScriptDeclaration('
           

           
        ');
}

  protected function getLinkAttributes(&$attribs, $image, $img_url, $group, $type)
  {
    $attribs['rel'] = 'highslide['.$group.']';
//    $attribs['onclick'] ='return hs.expand(this);';
    $attribs['class'] = 'highslide';
   $attribs['onclick'] ='return hs.expand(this);';
  }
}