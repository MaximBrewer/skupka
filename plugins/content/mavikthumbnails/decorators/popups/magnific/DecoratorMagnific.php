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
 * Decorator for Magnific Popup
 */
class DecoratorMagnific extends DecoratorAbstract
{
    public function __construct(&$plugin)
    {
        $this->type = 'magnific';
        parent::__construct($plugin);
    }
    
    /**
     * Add code in header of document 
     */    
    public function addHeader()
    {
        parent::addHeader();
        
        $base = \JUri::base(true);
        \JHtml::_('jquery.framework');
        $document = \JFactory::getDocument();
        if (JDEBUG) {
            $document->addScript($base.'/media/plg_content_mavikthumbnails/magnific/jquery.magnific-popup.js');
        } else {
            $document->addScript($base.'/media/plg_content_mavikthumbnails/magnific/jquery.magnific-popup.min.js');
        }
        $document->addStyleSheet($base.'/media/plg_content_mavikthumbnails/magnific/magnific-popup.css');        
    }
    
    /**
     * Action for each item
     */
    public function item()
    {
        if(!$this->headerAdded) { return; }

        if (!isset($this->plugin->item->id)) {
            $this->plugin->item->id = uniqid();
        }
        $document = \JFactory::getDocument();
        $class = 'magnific-popup-'.$this->plugin->item->id;
        $document->addScriptDeclaration("
            jQuery(document).ready(
                function(){
                    jQuery('.{$class}').magnificPopup({
                        type:'image',
                        gallery: {
                            enabled:true,
                            preload: [1,2]
                        }
                    });
                }
            )
        ");
    }    
}
