<?php

/**
* @package   Shortcode Ultimate
* @author    BdThemes http://www.bdthemes.com
* @copyright Copyright (C) BdThemes Ltd
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class Su_Shortcode_marker_config extends Su_Data {

    function __construct() {
        parent::__construct();
    }
    static function get_config() {
        
        return array(
            'name'  => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_MARKER'),
            'type'  => 'wrap',
            'group' => 'box',
            'atts'  => array(
                'image' => array(
                    'type'    => 'upload',
                    'default' => 'http://lorempixel.com/400/300/food/',
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_PHOTO'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_PHOTO_DESC')
                ),
                
                'scroll_reveal' => array(
                    'default' => '',
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SCROLL_REVEAL'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SCROLL_REVEAL_DESC')
                ),
                'class' => array(
                    'default' => '',
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_CLASS'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_CLASS_DESC')
                )
            ),
            'content' => sprintf ('%s',"[%prefix_marker_item icon=\"icon: map-marker\" from_left=\"25\" from_top=\"50\" tooltip_title=\"Marker Tooltip Title 1\" tooltip_style=\"bootstrap\" link=\"#\"]Marker Tooltip Description 1[/%prefix_marker_item]\n[%prefix_marker_item icon=\"icon: map-marker\" from_left=\"50\" from_top=\"50\" tooltip_title=\"Marker Tooltip Title 2\" tooltip_style=\"yellow\" link=\"#\"]Marker Tooltip Description 2[/%prefix_marker_item]\n[%prefix_marker_item icon=\"icon: map-marker\" from_left=\"75\" from_top=\"50\" tooltip_title=\"Marker Tooltip Title 3\" tooltip_style=\"tipsy\" link=\"#\"]Marker Tooltip Description 3[/%prefix_marker_item]"),
            'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_MARKER_DESC'),
            'icon'    => 'map-marker'
        );
    }

}
