<?php 

/**
* @package   Shortcode Ultimate
* @author    BdThemes http://www.bdthemes.com
* @copyright Copyright (C) BdThemes Ltd
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class Su_Shortcode_instagram_config extends Su_Data {

    function __construct() {
        parent::__construct();
    }
    static function get_config() {

        return array(
            'name'  => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_INSTAGRAM'),
            'desc' => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_INSTAGRAM_DESC'),
            'type'  => 'single',
            'group' => 'content other',
            'icon' => 'instagram',
            'atts'  => array(
                'instagram_id' => array(
                    'default' => '',
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_USER'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_USER_DESC'),
                ),
                'limit' => array(
                    'type'    => 'slider',
                    'min'     => 1,
                    'max'     => 100,
                    'step'    => 1,
                    'default' => 12,
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ITEM_LIMIT'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ITEM_LIMIT_DESC')
                ),
                'large' => array(
                    'type'    => 'slider',
                    'min'     => 1,
                    'max'     => 10,
                    'step'    => 1,
                    'default' => 4,
                    'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_LARGE_DEVICE_ITEM'),
                    'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_LARGE_DEVICE_ITEM_DESC'),
                    'child'   => array(
                        'medium' => array(
                            'type'    => 'slider',
                            'min'     => 1,
                            'max'     => 5,
                            'step'    => 1,
                            'default' => 3,
                            'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_MEDIUM_DEVICE_ITEM'),
                            'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_MEDIUM_DEVICE_ITEM_DESC')
                        ),
                        'small' => array(
                            'type'    => 'slider',
                            'min'     => 1,
                            'max'     => 5,
                            'step'    => 1,
                            'default' => 2,
                            'name'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SMALL_DEVICE_ITEM'),
                            'desc'    => JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_SMALL_DEVICE_ITEM_DESC')
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
            )
        );
    }

}
