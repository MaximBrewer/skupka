<?php
/*
 * ARI Magnific Popup Joomla! plugin
 *
 * @package		ARI Magnific Popup
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
*/

defined('_JEXEC') or die('Restricted access');

class plgSystemArimagnificpopup extends JPlugin
{
	private $executed = false;

    private $isLibLoaded = null;

	function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	public function onAfterRender()
	{
		$app = JFactory::getApplication();
		$jInput = $app->input;

		$option = $jInput->request->getString('option');
		$view = $jInput->request->getString('view');
		$task = $jInput->request->getString('task');
		if (
			($option == 'com_content' && $view == 'form' && $jInput->request->getString('layout') == 'edit')
			||
			($option == 'com_k2' && $view == 'item' && $task == 'edit')
			||
			($option == 'com_k2' && $view == 'item' && $task == 'save')
			||
			($option == 'com_comprofiler' && $task == 'userDetails')
			)
			return ;

		$this->prepareContent();
	}

    private function loadLibrary()
    {
        if (!is_null($this->isLibLoaded))
            return $this->isLibLoaded;

        $this->loadLanguage('', JPATH_ADMINISTRATOR);
        $loaderPath = JPATH_ROOT . '/libraries/arimagnificpopup/loader.php';

        if (!@file_exists($loaderPath))
        {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_ARIMAGNIFICPOPUP_WARNING_LIBNOTINSTALLED'), 'error');
            $this->isLibLoaded = false;
        }
        else
        {
            require_once $loaderPath;

            $this->isLibLoaded = true;
        }

        return $this->isLibLoaded;
    }

	private function prepareContent()
	{
		if ($this->executed)
			return ;

		$this->executed = true;
		$app = JFactory::getApplication();

		$doc = JFactory::getDocument();
		$docType = $doc->getType();

		if ($app->isAdmin() || $docType !== 'html' || !$this->loadLibrary())
			return ;

		$content = $app->getBody();

		$bodyPos = stripos($content, '<body');
		$preContent = '';
		if ($bodyPos > -1)
		{
			$preContent = substr($content, 0, $bodyPos);
			$content = substr($content, $bodyPos);
		}

        $includesManager = new Arisoft\Joomla\Document\Includesmanager;

        $result = null;
        $content = $this->convertPluginTags($content, $result);
        $content = \Arimagnificpopup\Helper::transformElementsToPopup($this->params, $content);

        if ($result)
        {
            $app->setBody(
                preg_replace('/<\/head\s*>/i', '$0', $preContent . $content, 1)
            );

            $includes = $includesManager->getDifferences();
            Arisoft\Joomla\Document\Helper::addCustomTagsToDocument($includes);
        }
	}

    private function convertPluginTags($content, &$result)
    {
        $params = $this->params;
        $plgTag = $params->get('plugintag', 'popup');



        if (strpos($content, '{' . $plgTag) === false)
        {
            $result = false;
            return $content;
        }

        $result = true;
        $plg = new Arimagnificpopup\Plugin\Content($params, $plgTag);



        return $plg->parse($content);
    }
}