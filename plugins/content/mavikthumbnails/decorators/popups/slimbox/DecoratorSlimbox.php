<?php
/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * Plugin automatic replaces big images to thumbnails.
 */

namespace Plugin\Content\MavikThumbnails;

defined('_JEXEC') or die();

/**
 * Decorator for Slimbox (on Mootools)
 */
class DecoratorSlimbox extends DecoratorAbstract
{
    public function __construct(&$plugin)
    {
        $this->type = 'slimbox';
        parent::__construct($plugin);
    }
    
    public function addHeader()
    {
        parent::addHeader();
        
        $base = \JUri::base(true);
        \JHtml::_('behavior.framework');
        $document = \JFactory::getDocument();
        $document->addScript($base.'/media/plg_content_mavikthumbnails/slimbox/js/slimbox.js');
        if (\JFactory::getLanguage()->isRTL()) {
            $document->addStyleSheet($base.'/media/plg_content_mavikthumbnails/slimbox/css/slimbox-rtl.css');
        } else {
            $document->addStyleSheet($base.'/media/plg_content_mavikthumbnails/slimbox/css/slimbox.css');
        }
    }
}
