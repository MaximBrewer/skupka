<?php
/*
 *
 * @package		ARI Magnific Popup
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/libraries/arimagnificpopup/loader.php';

use Arisoft\Plugin\Editorbutton as EditorButtonPlugin;

class plgButtonArimagnificpopup extends EditorButtonPlugin
{
    protected $tag = 'popup';

    protected $btnName = 'expand';

    protected $contentPlgName = 'arimagnificpopup';

    protected $contentPlgGroup = 'system';

    protected function getJsOptions($name)
    {
        $params = $this->getContentPluginParams();
        $tag = !empty($params['plugintag']) ? $params['plugintag'] : 'popup';

        $options = parent::getJsOptions($name);

        $options['useMediaModal'] = true;
        $options['tag'] = $tag;
        $options['modalTemplate'] = file_get_contents(JPATH_ROOT . '/media/arimagnificpopup/editor/tpl/modal_body.tpl');

        return $options;
    }

    protected function registerCustomScripts($name)
    {
        $doc = JFactory::getDocument();

        $editorAssetsBaseUri = JURI::root(true) . '/media/arimagnificpopup/editor/';
		$sysAssetsBaseUri = JURI::root(true) . '/media/arisoft/';

        $doc->addStyleSheet($editorAssetsBaseUri . 'css/style.css');
        $doc->addScript($sysAssetsBaseUri . 'vue/vue.min.js');
        $doc->addScript($sysAssetsBaseUri . 'cloner/cloner.js');
        $doc->addScript($editorAssetsBaseUri . 'js/script.js');
    }

    protected function getClientMessages()
    {
        return array_merge(
            parent::getClientMessages(),
            array(
                'contentType' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_CONTENTTYPE'),

                'contentTypeTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_CONTENTTYPETIP'),

                'itemAdd' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_ADDITEM'),

                'itemRemove' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_REMOVEITEM'),

                'itemUp' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_ITEMUP'),

                'itemDown' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_ITEMDOWN'),

                'article' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_ARTICLE'),

                'content' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_CONTENT'),

                'url' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_URL'),

                'videoId' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_VIDEOID'),

                'imageFolder' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_IMAGEFOLDER'),

                'linkTypeLbl' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKTYPE'),

                'layoutTypeLbl' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LAYOUTTYPE'),

                'linkImage' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKIMAGE'),

                'linkText' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKTEXT'),

                'select' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_SELECT'),

                'remove' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_REMOVE'),

                'enabled' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_ENABLED'),

                'yes' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_YES'),

                'no' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_NO'),

                'openAfter' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_OPENAFTER'),

                'closeAfter' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_CLOSEAFTER'),

                'seconds' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_SECONDS'),

                'pluginUsage' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_PLUGINUSAGE'),

                'open' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_OPEN'),

                'always' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_ALWAYS'),

                'once' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_ONCE'),

                'onceSession' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_ONCESESSION'),

                'linkTypeTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKTYPETIP'),

                'linkTextTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKTEXTTIP'),

                'linkImageTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKIMAGETIP'),

                'articleTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_ARTICLETIP'),

                'articlePlaceholder' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_ARTICLEPLACEHOLDER'),

                'contentTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_CONTENTTIP'),

                'urlTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_URLTIP'),

                'urlPlaceholder' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_URLPLACEHOLDER'),

                'imageFolderTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_IMAGEFOLDERTIP'),

                'imageFolderPlaceholder' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_IMAGEFOLDERPLACEHOLDER'),

                'ytVideoTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_YTVIDEOTIP'),

                'ytVideoPlaceholder' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_YTVIDEOPLACEHOLDER'),

                'vimeoVideoTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_VIMEOVIDEOTIP'),

                'vimeoVideoPlaceholder' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_VIMEOVIDEOPLACEHOLDER'),

                'splashTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_SPLASHTIP'),

                'splashSessionTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_SPLASHSESSIONTIP'),

                'splashOpenTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_SPLASHOPENTIP'),

                'splashCloseTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_SPLASHCLOSETIP'),

                'splashHideContent' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_SPLASHHIDECONTENT'),

                'splashHideContentTip' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_SPLASHHIDECONTENTTIP'),

                'linkType' => array(
                    'text' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKTYPE_TEXT'),

                    'image' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKTYPE_IMAGE'),

                    'preview' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKTYPE_PREVIEW'),

                    'videoPreviewNote' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKTYPE_VIDEOPREVIEWNOTE')
                ),

                'layoutType' => array(
                    'gallery' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LINKGALLERY'),

                    'videoGalleryNote' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_LAYOUTTYPE_VIDEOGALLERYNOTE')
                ),

                'types' => array(
                    'article' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_TYPE_ARTICLE'),

                    'inline' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_TYPE_INLINE'),

                    'url' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_TYPE_URL'),

                    'gallery' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_TYPE_GALLERY'),

                    'youtube' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_TYPE_YOUTUBE'),

                    'vimeo' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_LABEL_TYPE_VIMEO'),
                ),

                'errors' => array(
                    'selectArticle' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_ERROR_SELECTARTICLE'),

                    'linkText' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_ERROR_LINKTEXT'),

                    'linkImage' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_ERROR_LINKIMAGE'),

                    'content' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_ERROR_CONTENT'),

                    'selectUrl' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_ERROR_SELECTURL'),

                    'selectFolder' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_ERROR_SELECTFOLDER'),

                    'selectVideo' => JText::_('PLG_EDITOR_XTD_ARIMAGNIFICPOPUP_ERROR_SELECTVIDEO'),
                )
            )
        );
    }
}