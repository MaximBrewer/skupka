<?php 

/**
* @package   Shortcode Ultimate
* @author    BdThemes http://www.bdthemes.com
* @copyright Copyright (C) BdThemes Ltd
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class Su_Shortcode_marker_item_config extends Su_Data {

    function __construct() {
        parent::__construct();
    }
    static function get_config() {

        return array(
            'name'  => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_MARKER_ITEM'),
            'type'  => 'wrap',
            'group' => 'box',
            'atts'  => array(
                'tooltip_title' => array(
                    'default' => 'Marker Tooltip Title',
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_TITLE'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_TITLE_DESC')
                ),
                'tooltip_style' => array(
                    'type'   => 'select',
                    'values' => array(
                        'light'     => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_LIGHT'),
                        'dark'      => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_DARK'),
                        'yellow'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_YELLOW'),
                        'green'     => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_GREEN'),
                        'red'       => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_RED'),
                        'blue'      => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_BLUE'),
                        'youtube'   => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_YOUTUBE'),
                        'tipsy'     => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_TIPSY'),
                        'bootstrap' => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_BOOTSTRAP'),
                        'jtools'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_JTOOLS'),
                        'tipped'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_TIPPED'),
                        'cluetip'   => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_CLUETIP'),
                    ),
                    'default' => 'yellow',
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_STYLE'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_STYLE_DESC'),
                    'child'     => array(
                        'tooltip_behavior' => array(
                            'type'   => 'select',
                            'values' => array(
                                'hover'  => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_HOVER'),
                                'click'  => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ONCLICK'),
                                'always' => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ALWAYS')
                            ),
                            'default' => 'hover',
                            'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_BEHAVIOR'),
                            'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_BEHAVIOR_DESC')   
                        )
                    )
                ),  
                'icon' => array(
                    'type'    => 'icon',
                    'default' => 'icon: map-marker',
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ICON'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ICON_DESC')
                ),  
                'icon_color' => array(
                    'type'    => 'color',
                    'default' => '#000000',
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ICON_COLOR'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ICON_COLOR_DESC')
                ),  
                'size' => array(
                    'type'    => 'slider',
                    'min'     => 5,
                    'max'     => 300,
                    'step'    => 5,
                    'default' => 25,
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ICON_SIZE'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ICON_SIZE_DESC')
                ),          
                'x' => array(
                    'type'    => 'slider',
                    'min'     => 0,
                    'max'     => 100,
                    'step'    => 1,
                    'default' => 50,
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_X_POSITION'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_X_POSITION_DESC')
                ),           
                'y' => array(
                    'type'    => 'slider',
                    'min'     => 0,
                    'max'     => 100,
                    'step'    => 1,
                    'default' => 50,
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_Y_POSITION'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_Y_POSITION_DESC')
                ),          
                'link' => array(
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_BUTTON_LINK'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_BUTTON_LINK_DESC'),
                    'child'   => array(
                        'target' => array(
                            'type' => 'select',
                            'values' => array(
                                'self'  => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SELF'),
                                'blank' => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_BLANK')
                            ),
                            'default' => 'self',
                            'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_TARGET'),
                            'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_TARGET_DESC')
                        ) 
                    )
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
            'content' => 'Marker Tooltip Description',
            'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_MARKER_ITEM_DESC'),
            'icon'    => 'map-marker'
        );
    }

}
