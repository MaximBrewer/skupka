<?php

/**
* @package   Shortcode Ultimate
* @author    BdThemes http://www.bdthemes.com
* @copyright Copyright (C) BdThemes Ltd
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class Su_Shortcode_faq extends Su_Shortcodes {

    function __construct() { parent::__construct(); }

    public static function faq($atts = null, $content = null) {
        $atts = su_shortcode_atts(array(
            'id'                => uniqid('suf'),
            'source'            => '',
            'limit'             => 20,
            'order'             => 'created',
            'order_by'          => 'desc',
            'loading_animation' => 'default',
            'filter'            => 'yes',
            'filter_animation'  => 'sequentially',
            'display_speed'     => 200,
            'link_to_article'   => 'no',
            'target'            => 'self', // blank|self
            'scroll_reveal'     => '',
            'class'             => ''
                ), $atts, 'faq');

        $slides     = (array) Su_Tools::get_slides($atts);
        $intro_text = '';
        $title      = '';    
        $output     = [];
        $lang       = JFactory::getLanguage(); 
        $lang->load('plg_system_bdthemes_shortcodes', JPATH_ADMINISTRATOR);

        $link_to_article = ($atts['link_to_article'] == 'yes') ? 'link-to-article' : '';


        if ( count($slides) ) {
            
            $output[] = '
            <div'.su_scroll_reveal($atts).' id="'. $atts['id'] . '" class="su-faq '.su_ecssc($atts). '" 
                data-scid="' . $atts['id'] . '" 
                data-loading_animation="'.$atts['loading_animation'].'"
                data-filter_animation="'.$atts['filter_animation'].'" data-display_speed="'.$atts['display_speed'].'">';
                if ($atts['filter'] == 'yes') {
                    $output[] = ' 
                    
                    <div id="' . $atts['id'] . '_filter" class="cbp-l-filters-underline">                   
                        <div data-filter="*" class="cbp-filter-item-active cbp-filter-item">
                            '.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_ALL_ITEMS').'
                        </div>';
                        $category = array();
                        foreach ((array) $slides as $slide) {
                            if (in_array($slide['category'], $category) ) {
                                continue;
                            }
                            $category[] = $slide['category'];
                            $output[] = '<div class="cbp-filter-item" data-filter=".' . str_replace(' ', '-', strtolower($slide['category'])).'">'.$slide['category'].'</div>';
                        }

                    $output[] ='</div>';
                }
                $output[] ='<div id="' . $atts['id'] . '_container" class="cbp cbp-l-grid-faq">';

            $limit = 1;
            foreach ((array) $slides as $slide) {
                
                // Title condition
                $title = $slide['title'];
                $icon = $title ? explode('|| fa-', $title) : array();
                if (count($icon) == 2){
                    $title = trim($icon[0]);
                    $icon = '<i class="fa fa-'.trim($icon[1]).'"></i> ';
                } else {
                    $title = $slide['title'];
                    $icon = '<i class="fa fa-question-circle"></i>';
                }
                $isReadmore = $slide['fulltext'] ? '<div class="su-readmore"><a href="'.$slide['link'].'">'.JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_READMORE').'</a></div>' : '';

                $category = str_replace(' ', '-', strtolower($slide['category']));

                $html_content = 
                $output[] = '
                    <div class="cbp-item '.$category.'">
                        <div class="cbp-caption">
                            <div class="cbp-caption-defaultWrap '.$link_to_article.'" data-url="' . $slide['link'] . '" data-target="' . $atts['target'] . '">
                                '.$icon. $title .'
                            </div>';

                            $output[] = $atts['link_to_article'] != 'yes' ? '<div class="cbp-caption-activeWrap">
                                <div class="cbp-l-caption-body">
                                    '.su_do_shortcode($slide['introtext']).$isReadmore.'
                                </div>
                            </div>' : '';

                        $output[] = '</div>

                    </div>';
                if ($limit++ == $atts['limit']) break;
            }
            $output[] = '<div class="clearfix"></div></div></div>';


            suAsset::addFile('css', 'cubeportfolio.min.css');
            suAsset::addFile('js', 'cubeportfolio.min.js');
            
            suAsset::addFile('css', 'faq.css', __FUNCTION__);
            suAsset::addFile('js', 'faq.js', __FUNCTION__);

            return implode('', $output);

        } else {
            return su_alert_box(JText::_('PLG_SYSTEM_BDTHEMES_SHORTCODES_FAQ_ERROR'), 'warning');
        }
            
    }   
}
