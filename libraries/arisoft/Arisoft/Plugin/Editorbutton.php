<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */
namespace Arisoft\Plugin;

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

use JPlugin, JFactory, JURI, JHtml, JText, JPluginHelper, JRegistry, JObject, JVersion;

abstract class Editorbutton extends JPlugin
{
    private $name = null;

    protected $jsHelper = null;

    protected $tag = null;

    protected $btnName = null;

    protected $textPrefix = null;

    protected $contentPlgName = '';

    protected $contentPlgGroup = 'content';

    protected $contentPlgIgnoreParams = array();

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $this->loadLanguage();

        $plgName = $this->getName();
        if (is_null($this->jsHelper))
        {
            $this->jsHelper = ucfirst($plgName) . 'JEditorButton';
        }

        if (is_null($this->textPrefix))
        {
            $this->textPrefix = 'PLG_EDITOR_XTD_' . strtoupper($plgName);
        }
    }
	
	protected function getMediaPrefix()
	{
		return $this->getName();
	}

    protected function createButton($name)
    {
		$plgName = $this->getName();
		
        $button = new JObject();
        $button->modal = false;
        $button->class = 'btn btn-jeditor-' . $name . '-' . $plgName;
        $button->link = '#';
        $button->text = JText::_($this->textPrefix . '_LABEL_BTN');
        $button->name = $this->btnName . ' btn-jeditor-marker-' . $name . '-' . $plgName;

        return $button;
    }

    protected function registerScripts($name)
    {
        $doc = JFactory::getDocument();

        $editorAssetsBaseUri = JURI::root(true) . '/media/arisoft/editorbutton/';

        JHtml::_('jquery.framework', true);
        JHtml::_('bootstrap.modal');

        $doc->addScript($editorAssetsBaseUri . 'js/jquery.arijeditorbutton.js');

        $jsOptions = $this->getJsOptions($name);
        $doc->addScriptDeclaration(
            sprintf(
                ';jQuery(document).ready(function($) { $(document).ariJEditorButton(jEditorButtonPrepareOptions(%2$s, window["%3$s"])); });',
                '.btn-jeditor-' . $name,
                json_encode($jsOptions),
                $this->jsHelper
            )
        );

        $this->registerCustomScripts($name);
    }

    protected function registerCustomScripts($name)
    {

    }

    protected function getJsOptions($name)
    {
        $isLegacy = version_compare((new JVersion())->getShortVersion(), '3.5', '<');
        $mediaManagerLink = JURI::root(true) . '/administrator/index.php?option=com_media&view=images&tmpl=component&asset=' . $this->getName() . '&folder=';

        $options = array(
            'editorId' => $name,

            'name' => $this->getName(),

            'tag' => $this->tag,

            'messages' => $this->getClientMessages(),

            'params' => $this->getContentPluginParams(),

            'modalClass' => 'modal-jeditor-' . $this->getName(),

            'legacy' => $isLegacy,

            'rootUri' => JURI::root(true),

            'mediaManagerLink' => $mediaManagerLink
        );

        return $options;
    }

    protected function getClientMessages()
    {
        return array(
            'header' => JText::_($this->textPrefix . '_LABEL_MODALTITLE'),

            'reset' => JText::_($this->textPrefix . '_LABEL_BTNRESET'),

            'close' => JText::_($this->textPrefix . '_LABEL_BTNCLOSE'),

            'insertCode' => JText::_($this->textPrefix . '_LABEL_BTNINSERTCODE')
        );
    }

    protected function getContentPluginParams()
    {
        $plg = JPluginHelper::getPlugin($this->contentPlgGroup, $this->contentPlgName);
        $params = null;

        if ($plg)
        {
            $params = new JRegistry($plg->params);
            $params = $params->toArray();

            foreach ($this->contentPlgIgnoreParams as $ignoreKey)
            {
                if (isset($params[$ignoreKey]))
                    unset($params[$ignoreKey]);
            }
        }
        else
        {
            $params = array();
        }

        return $params;
    }

    public function onDisplay($name)
    {
        $this->registerScripts($name);

        $button = $this->createButton($name);

        return $button;
    }

    public function getName()
    {
        if (is_null($this->name))
        {
            $r = null;

            if (!preg_match('/plgButton(.*)/i', get_class($this), $r))
            {
                throw new Exception('Could not get plugin name.', 500);
            }

            $this->name = strtolower($r[1]);
        }

        return $this->name;
    }
}