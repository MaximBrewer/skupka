<?php
/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Plugin\Content\MavikThumbnails;

defined( '_JEXEC' ) or die();

/**
 * Detecting Retina-displays
 *
 */
class Retina
{
    public $plugin;
    public $context;
    public $item;
    public $itemParams;
    public $page;
    public $images;
    private static $done = false; 

    public function __construct(&$plugin, $context, &$item, &$params, $page)
    {
        $this->plugin = $plugin;
        $this->context = $context;
        $this->item = $item;
        $this->itemParams = $params;
        $this->page = $page;
    }
    
    public function process()
    {
        // Do it one time
        if (self::$done) {
            return;
        }
        
        $ratio = $this->getRatio();
           
        if (!$ratio) {
            // Detect ratio in browser
            $session = \JFactory::getSession(); 
            $session->set('mavikthumbnails.display.ratio', 1); // Set default value
            $document = \JFactory::getDocument();
            $uri = \JFactory::getURI();
            $document->addScriptDeclaration('
                var ratio = window.devicePixelRatio;
                if (ratio && ratio > 1) {
                    var form = document.createElement("form");
                    form.setAttribute("method", "post");
                    form.setAttribute("action", "'.$uri.'");
                    var field = document.createElement("input");
                    field.setAttribute("type", "hidden");
                    field.setAttribute("name", "mavikthumbnails_display_ratio");
                    field.setAttribute("value", ratio);
                    form.appendChild(field);
                    document.body = document.createElement("body");
                    document.body.appendChild(form);
                    form.submit();
                }
            ');
        }
        
        self::$done = true;
    }
    
    /**
     * Select suited ratio from availables
     * 
     * @return float
     */
    private function getRatio()
    {
        $app = \JFactory::getApplication();
        $displayRatio = $app->getUserStateFromRequest('mavikthumbnails.display.ratio', 'mavikthumbnails_display_ratio', 0);
        if (!$displayRatio) {
            return 0;
        }
        
        $ratios = explode(',', $this->plugin->params->get('retina_ratio', '2,3'));
        $ratios[] = 1;
        sort($ratios, SORT_NUMERIC);
        
        $diff = 9999;
        while (($newRatio = (float)array_shift($ratios)) && abs($newRatio - $displayRatio) <= $diff) {
            $diff = abs($newRatio - $displayRatio);
            $ratio = $newRatio;
        }
        
        return $ratio;
    }
}