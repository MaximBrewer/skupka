<?php

/**
* @package   Shortcode Ultimate
* @author    BdThemes http://www.bdthemes.com
* @copyright Copyright (C) BdThemes Ltd
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class Su_Shortcode_instagram extends Su_Shortcodes {

    function __construct() {
        parent::__construct();
    }   
    public static function instagram($atts = null, $content = null) {
        $atts = su_shortcode_atts(array(
            'instagram_id'  => '',
            'limit'         => 12,
            'large'         => 4,
            'medium'        => 3,
            'small'         => 2,
            'gap'           => 'yes',
            'scroll_reveal' => '',
            'class'         => ''
        ), $atts, 'instagram');


        $id                      = uniqid('suig');
        $css                     = array();
        $js                      = array();
        $classes                 = array('su-instagram', $atts['gap'], su_ecssc($atts));
        $atts['instagram_id']    = ($atts['instagram_id']) ? $atts['instagram_id'] : 'selimmw';
        
        suAsset::addFile('css', 'magnific-popup.css');
        suAsset::addFile('js', 'magnific-popup.js'); 
        
        suAsset::addFile('css', 'row-column.css');
        suAsset::addFile('css', 'photomax_trend.min.css');
        suAsset::addFile('css', 'instagram.css', __FUNCTION__);
        suAsset::addFile('js', 'jquery.instagramFeed.js', __FUNCTION__);
        
        $output = '<div id="' . $id . '"' . su_scroll_reveal($atts) . ' class="su-instagram ' . su_acssc($classes) . '"></div>';

        $output .= '
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $.instagramFeed({
                            "username"          : "' . $atts['instagram_id'] . '",
                            "container"         : "#' . $id . '",
                            "items"             : "' . $atts['limit'] . '",
                            "columns"           : "' . $atts['large'] . '",
                            "columns_tablet"    : "' . $atts['medium'] . '",
                            "columns_mobile"    : "' . $atts['small'] . '",
                        });
                    });
                </script>
        ';




        return $output;

    }
}