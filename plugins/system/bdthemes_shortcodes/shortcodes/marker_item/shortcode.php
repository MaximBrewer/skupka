<?php 

/**
* @package   Shortcode Ultimate
* @author    BdThemes http://www.bdthemes.com
* @copyright Copyright (C) BdThemes Ltd
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class Su_Shortcode_marker_item extends Su_Shortcodes {

    function __construct() {
        parent::__construct();
    }   
    public static function marker_item($atts = null, $content = null) {
        $atts = su_shortcode_atts(array(
            'tooltip_title'    => '',
            'tooltip_text'     => '',
            'tooltip_style'    => 'yellow',
            'tooltip_behavior' => 'hover',
            'icon'             => 'icon: map-marker',
            'icon_color'       => '#000',
            'icon_size'        => '25',
            'x'                => '10',
            'y'                => '10',
            'link'             => '',
            'target'           => 'self',
            'scroll_reveal'    => '',
            'class'            => ''
            ), $atts, 'marker_item');


        // Initioal Variables
        $id = uniqid('su_marker_item_');
        $radius ='';
        $css = array();
        
        if (strpos($atts['icon'], '/') !== false) {
            $atts['icon'] = '<img src="' . image_media($atts['icon']) . '" alt="" width="' . $atts['icon_size'] . '" height="' . $atts['icon_size'] . '" />';
        }
        // Line Icon
        elseif (strpos($atts['icon'], 'licon:') !== false) {
            suAsset::addFile('css', 'linea.css');
            $atts['icon'] = '<i class="li li-' . trim(str_replace('licon:', '', $atts['icon'])) . '"></i>';
        }
        // Font-Awesome icon
        elseif (strpos($atts['icon'], 'icon:') !== false) {
            $atts['icon'] = '<i class="fa fa-' . trim(str_replace('icon:', '', $atts['icon'])) . '"></i>';
        }

        // Get Css in $css variable
        $css[] = '#'.$id.'{left: '.$atts['x'].'%; top: '.$atts['y'].'%}';
        $css[] = '#'.$id.' i {font-size: '.$atts['icon_size'].'px; color: '.$atts['icon_color'].';}';

        
        // Add CSS in head
        suAsset::addString('css', implode("\n", $css));

        $content     = $atts['tooltip_text'] ? su_do_shortcode($atts['tooltip_text']) : su_do_shortcode($content);
        $target      = ( $atts['target'] === 'blank') ? 'target="_blank"' : 'target="_self"';
        $marker_icon = $atts['link'] ? '<a href="'.$atts['link'].'" '. $target .'><span class="su-icon ">'.$atts['icon'].'</span></a>' : '<span class="su-icon ">'.$atts['icon'].'</span>';


        // Output HTML
        $return = '<div id="'.$id.'"'.su_scroll_reveal($atts).' class="su-marker-item '. su_ecssc($atts) . '">                    
                    '. su_do_shortcode(
                        '[tooltip title="'.$atts['tooltip_title'] .'" style="'.$atts['tooltip_style'] .'" behavior="'.$atts['tooltip_behavior'] .'" text="'. $content .'"]
                            '.$marker_icon.'
                        [/tooltip]'
                    ) .'
                </div>';
        return $return;
    }
}
