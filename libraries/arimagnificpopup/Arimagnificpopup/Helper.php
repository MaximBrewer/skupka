<?php
/*
 * ARI Magnific Popup
 *
 * @package		ARI Magnific Popup
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2010 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */

namespace Arimagnificpopup;

defined('_JEXEC') or die;

use JURI, JHtml, JText, JFactory, Arisoft\Parameters\Helper as ParametersHelper, Arisoft\Template\Template as Template;
use Joomla\Utilities\ArrayHelper as ArrayHelper;

class Helper
{
    static private $isLoaded = false;

    static private $defaultJsOptions = array(
        'type' => '',

        'midClick' => false,

        'preloader' => true,

        'closeOnContentClick' => false,

        'closeOnBgClick' => true,

        'closeBtnInside' => true,

        'showCloseBtn' => true,

        'enableEscapeKey' => true,

        'modal' => false,

        'alignTop' => false,

        'gallery' => array(
            'enabled' => false,

            'navigateByImgClick' => true
        ),

        'fx' => array(
            'fxClose' => '',

            'fxOpen' => ''
        )
    );

    static private $defaultJsExtOptions = array(
        'youtube' => array(
            'params' => array(
                'rel' => '1',

                'autoplay' => '1'
            )
        )
    );

    static public function init($params)
    {
        if (self::$isLoaded)
            return ;

        $uri = JURI::root(true) . '/media/arimagnificpopup/';

        if ((bool)$params->get('loadJQuery', true))
        {
            $jqNoConflict = (bool)$params->get('jQueryNoConflict', true);

            JHtml::_('jquery.framework', $jqNoConflict);
        }

        $assetKey = bin2hex(ARIMAGNIFICPOPUP_VERSION);

        $doc = JFactory::getDocument();

        $doc->addStyleSheet($uri . 'css/magnificpopup.css?v=' . $assetKey);
        $doc->addScript($uri . 'js/jquery.magnificpopup.js?v=' . $assetKey);
        $doc->addScript($uri . 'js/jquery.magnificpopup.ext.js?v=' . $assetKey);

        $lang = JFactory::getLanguage();
        $lang->load('plg_system_arimagnificpopup', JPATH_ADMINISTRATOR);

        $treeParams = ParametersHelper::flatParametersToTree($params);
        $customJsOptions = ArrayHelper::getValue($treeParams, 'ext', array());
        $jsOptions = ParametersHelper::getUniqueOverrideParameters(self::$defaultJsExtOptions, $customJsOptions);
        $jsOptions['messages'] = array(
            'tClose' => JText::_('PLG_ARIMAGNIFICPOPUP_MESSAGE_CLOSE'),

            'tLoading' => JText::_('PLG_ARIMAGNIFICPOPUP_MESSAGE_LOADING'),

            'gallery' => array(
                'tPrev' => JText::_('PLG_ARIMAGNIFICPOPUP_MESSAGE_GALLERYPREV'),

                'tNext' => JText::_('PLG_ARIMAGNIFICPOPUP_MESSAGE_GALLERYNEXT'),

                'tCounter' => JText::_('PLG_ARIMAGNIFICPOPUP_MESSAGE_GALLERYCOUNTER')
            ),

            'image' => array(
                'tError' => JText::_('PLG_ARIMAGNIFICPOPUP_MESSAGE_IMAGEERROR')
            ),

            'ajax' => array(
                'tError' => JText::_('PLG_ARIMAGNIFICPOPUP_MESSAGE_AJAXERROR')
            )
        );

        $doc->addScriptDeclaration(
            sprintf(
                ';ARIMagnificPopupHelper.init(%1$s);',
                json_encode($jsOptions)
            )
        );

        $customStyles = $params->get('customstyles');

        if ($customStyles)
            $doc->addStyleDeclaration($customStyles);

        self::$isLoaded = true;
    }

    static public function initInstance($elSelector, $params)
    {
        self::init($params);

        $treeParams = ParametersHelper::flatParametersToTree($params);

        $jsOptions = self::getJsOptions($treeParams);

        $initEvent = 'domready';
        $initCode = sprintf(
            'ARIMagnificPopupHelper.initPopup("%1$s", %2$s);',
            $elSelector,
            json_encode($jsOptions)
        );

        $doc = JFactory::getDocument();
        if ($initEvent == 'onload')
        {
            $doc->addScriptDeclaration(
                sprintf(
                    ';jQuery(window).on("load", function() { var $ = window["jQueryARI"] || jQuery; %1$s });',
                    $initCode
                )
            );
        }
        else
        {
            $doc->addScriptDeclaration(
                sprintf(
                    ';(window["jQueryARI"] || jQuery)(function($) { %1$s });',
                    $initCode
                )
            );
        }
    }

    static public function transformElementsToPopup($params, $content)
    {
        $linkSelectors = array();

        if ((bool)$params->get('convertLinks_byclass_enabled'))
        {
            $classes = $params->get('convertLinks_byclass_classes');
            if ($classes)
            {
                $classes = json_decode($classes, true);
                foreach ($classes as $classItem)
                {
                    $classItem = trim($classItem['class']);
                    if (empty($classItem))
                        continue ;

                    $classItem = preg_split('/\s+/i', $classItem);
                    $linkSelectors[] = 'A.' . join('.', $classItem);
                }
            }
        }

        $needTransform = count($linkSelectors) > 0;

        if ($needTransform)
        {
            self::init($params);

            $transformOptions = array(
                'convertLinks' => array(
                    'selectors' => $linkSelectors
                )
            );

            JFactory::getDocument()->addScriptDeclaration(
                sprintf(
                    ';ARIMagnificPopupHelper.convertElements(%1$s);',
                    json_encode($transformOptions)
                )
            );
        }

        return $content;
    }

    static public function getMediaGalleryParameters($params, $type = null)
    {
        $treeParams = ParametersHelper::flatParametersToTree($params);
        $mediaGalleryParams = ArrayHelper::getValue($treeParams, 'mediagallery', array());

        $mediaGalleryParams['paging'] = array();

        if ($type)
        {
            $typeParams = ArrayHelper::getValue($treeParams, $type, array());
            $typeMediaGalleryParams = ArrayHelper::getValue($typeParams, 'mediagallery', array());
            $mediaGalleryParams = array_merge_recursive($mediaGalleryParams, $typeMediaGalleryParams);
        }

        return $mediaGalleryParams;
    }

    static public function getDefaultTitle($params)
    {
        $title = '';
        if (!(bool)$params->get('nametotitle_enabled'))
            return $title;

        $textTransform = $params->get('nametotitle_transform');
        if (empty($textTransform))
            return '{$baseFileName}';

        if ($textTransform == '_advanced')
        {
            $title = $params->get('nametotitle_transformtemplate');
        }
        else
        {
            $title = array('$baseFileName', 'normalize_str');

            if (Template::isFilterRegistered($textTransform))
                $title[] = $textTransform;

            $title = '{' . join('|', $title) . '}';
        }

        return $title;
    }

    static private function getJsOptions($treeParams)
    {
        $customJsOptions = ArrayHelper::getValue($treeParams, 'opt', array());
        $jsOptions = ParametersHelper::getUniqueOverrideParameters(self::$defaultJsOptions, $customJsOptions);

        return $jsOptions;
    }
}