<?php

/**
* @package   Shortcode Ultimate
* @author    BdThemes http://www.bdthemes.com
* @copyright Copyright (C) BdThemes Ltd
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class Su_Shortcode_marker extends Su_Shortcodes {

    static $marker = array();
    static $tab_count = -1;

    function __construct() {
        parent::__construct();
    }   

    public static function marker($atts = null, $content = null) {
        $atts = su_shortcode_atts(array(
            'image'         => 'http://lorempixel.com/400/300/food/',
            'scroll_reveal' => '',
            'class'         => ''
            ), $atts, 'marker');


        // Initioal Variables
        $id = uniqid('su_marker_');
        $css = array();

        // Get Css in $css variable
        //$css[] = '#'.$id.'{left: '.$atts['from_left'].'%; top: '.$atts['from_top'].'%}';


        // Output HTML
        $return = '<div id="'.$id.'"'.su_scroll_reveal($atts).' class="su-marker '. su_ecssc($atts) . '">     
                    <img src="' . image_media($atts['image']) . '" alt="" />               
                    '. su_do_shortcode( $content ) .'
                </div>';
        
        // Add CSS in head
        suAsset::addString('css', implode("\n", $css));
        suAsset::addFile('css', 'marker.css', __FUNCTION__);
        return $return;
    }
}
