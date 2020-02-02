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
class DecoratorHighslide extends DecoratorAbstract
{
    public function __construct(&$plugin)
    {
        $this->type = 'highslide';
        parent::__construct($plugin);
    }
    
    public function addHeader()
    {
        parent::addHeader();
        
        $base = \JUri::base(true);
        $document = \JFactory::getDocument();

        if(JDEBUG) {
            $document->addScript($base.'/media/plg_content_mavikthumbnails/highslide/highslide-with-gallery.js');
        } else {
            $document->addScript($base.'/media/plg_content_mavikthumbnails/highslide/highslide-with-gallery.packed.js');
        }

        $document->addStyleSheet($base.'/media/plg_content_mavikthumbnails/highslide/highslide.css');
        $document->addScriptDeclaration('
           hs.graphicsDir = "'.$base.'/media/plg_content_mavikthumbnails/highslide/graphics/";
            hs.align = "center";
            hs.outlineType = "rounded-white";
            hs.numberPosition = "caption";
            hs.dimmingOpacity = 0.75;
            hs.showCredits = false;
            hs.transitions = ["expand", "crossfade"];
        ');
        
    }
    
    public function item()
    {
        if(!$this->headerAdded) { return; }

        if (!isset($this->plugin->item->id)) {
            $this->plugin->item->id = uniqid();
        }
        $document = \JFactory::getDocument();
        $document->addScriptDeclaration('
            hs.addSlideshow({
               slideshowGroup: "'.$this->plugin->item->id.'",
               interval: 3000,
               repeat: false,
               useControls: true,
               fixedControls: true,
               overlayOptions: {
                  opacity: .6,
                  position: "midle center",
                  hideOnMouseOut: true
               },
               thumbstrip: {
                   position: "bottom center",
		   mode: "horizontal",
		   relativeTo: "viewport"
               }
            });
        ');
    }
}
