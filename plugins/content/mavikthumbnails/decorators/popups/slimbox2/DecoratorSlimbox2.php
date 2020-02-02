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
 * Decorator for Slimbox2 (on jQuery)
 */
class DecoratorSlimbox2 extends DecoratorAbstract
{
    public function __construct(&$plugin)
    {
        $this->type = 'slimbox2';
        parent::__construct($plugin);
    }
    
    public function addHeader()
    {
        parent::addHeader();
        
        $base = \JUri::base(true);
        \JHtml::_('jquery.framework');
        $document = \JFactory::getDocument();
        $document->addScript($base.'/media/plg_content_mavikthumbnails/slimbox2/js/slimbox2.js');
        if (\JFactory::getLanguage()->isRTL()) {
            $document->addStyleSheet($base.'/media/plg_content_mavikthumbnails/slimbox2/css/slimbox2-rtl.css');
        } else {
            $document->addStyleSheet($base.'/media/plg_content_mavikthumbnails/slimbox2/css/slimbox2.css');
        }
    }
}
