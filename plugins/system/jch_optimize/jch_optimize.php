<?php
/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
 * optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

if (!defined('JCH_PLUGIN_DIR'))
{
        define('JCH_PLUGIN_DIR', dirname(__FILE__) . '/');
}

if (!defined('JCH_VERSION'))
{
        define('JCH_VERSION', '5.4.2');
}

include_once(dirname(__FILE__) . '/jchoptimize/loader.php');

class plgSystemJCH_Optimize extends JPlugin
{

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

                if (!defined('JCH_DEBUG'))
                {
                        define('JCH_DEBUG', ($this->params->get('debug', 0) && JDEBUG));
                }
	}

	protected function isPluginDisabled()
	{
		$app = JFactory::getApplication();
                $user   = JFactory::getUser();
                
                $menuexcluded = $this->params->get('menuexcluded', array());
                $menuexcludedurl = $this->params->get('menuexcludedurl', array());

		return (!$app->isClient('site') 
                        || ($app->input->get('jchbackend', '', 'int') == 1)
                        || ($app->get('offline', '0') && $user->get('guest'))
                        || $this->isEditorLoaded()
                        || in_array($app->input->get('Itemid', '', 'int'), $menuexcluded)
			|| JchOptimizeHelper::findExcludes($menuexcludedurl, JchPlatformUri::getInstance()->toString()));
	}
        /**
         * 
         * @return boolean
         * @throws Exception
         */
        public function onAfterRender()
        {
                if ($this->isPluginDisabled())
                {
                        return false;
                }

                if ($this->params->get('log', 0))
                {
                        error_reporting(E_ALL & ~E_NOTICE);
                }

		$app = JFactory::getApplication();
                $sHtml = $app->getBody();

		
		if (!JchOptimizeHelper::validateHtml($sHtml))
		{
			return false;
		}

                if ($app->input->get('jchbackend') == '2')
                {
                        echo $sHtml;
                        while (@ob_end_flush());
                        exit;
                }

                try
                {
                        loadJchOptimizeClass('JchOptimize');

                        $sOptimizedHtml = JchOptimize::optimize($this->params, $sHtml);
                }
                catch (Exception $ex)
                {
                        JchOptimizeLogger::log($ex->getMessage(), JchPlatformSettings::getInstance($this->params));

                        $sOptimizedHtml = $sHtml;
                }

                $app->setBody($sOptimizedHtml);
        }

        /**
         * Gets the name of the current Editor
         * 
         * @staticvar string $sEditor
         * @return string
         */
        protected function isEditorLoaded()
        {
		$aEditors = JPluginHelper::getPlugin('editors');

		foreach($aEditors as $sEditor)
		{
			if(class_exists('plgEditor' . $sEditor->name, false))
			{
				return true;
			}
		}

		return false;
        }

        /**
         * 
         */
        public function onAjaxGarbagecron()
        {
                return JchOptimizeAjax::garbageCron(JchPlatformSettings::getInstance($this->params));
        }

        /**
         * 
         */
        public function onAjaxGetmultiselect()
        {
                $aData  = JchPlatformUtility::get('data', '', 'array');

                $params = JchPlatformPlugin::getPluginParams();
                $oAdmin = new JchOptimizeAdmin($params, TRUE);
		$oHtml  = new JchPlatformHtml($params);

		try
		{
			$sHtml = $oHtml->getOriginalHtml();
			$oAdmin->getAdminLinks($sHtml);
		}
		catch(Exception $e)
		{
		}

		$response = array();

		foreach($aData as $sData)
		{
			$options = $oAdmin->prepareFieldOptions($sData['type'], $sData['param'], $sData['group'], false);
			$response[$sData['id']] = new JchOptimizeJson($options);
		}

		return new JchOptimizeJson($response);

        }

	/**
	 * Provide a hash for the default page cache plugin's key based on type of browser detected by Google font
	 *
	 *
	 * @return string $hash 	Calculated hash of browser type
	 */
	public function onPageCacheGetKey()
	{
		$browser = JchOptimizeBrowser::getInstance();
		$hash = $browser->getFontHash();

		return $hash;
	}

	public function onJchCacheExpired()
	{
		return JchPlatformCache::deleteCache();
	}

        ##<procode>##

        /**
         * 
         */
        public function onAfterDispatch()
        {
		if($this->params->get('pro_lazyload', '0') && !$this->isPluginDisabled())
		{
                        JHtml::script('plg_jchoptimize/pro-ls.loader.js', FALSE, TRUE);

			if ($this->params->get('pro_lazyload_effects', '0'))
			{
				JHtml::stylesheet('plg_jchoptimize/pro-ls.effects.css', array(), TRUE);
				JHtml::script('plg_jchoptimize/pro-ls.loader.effects.js', false, true);
			}

			if ($this->params->get('pro_lazyload_autosize', '0'))
			{
				JHtml::script('plg_jchoptimize/pro-ls.autosize.js', false, true);
			}

                        JHtml::script('plg_jchoptimize/pro-lazysizes.js', FALSE, TRUE);
		}
        }

        /**
         * 
         * @param type $url
         * @param type $headers
         * @return boolean
         */
        public function onInstallerBeforePackageDownload(&$url, &$headers)
        {
                $uri = JUri::getInstance($url);

                // I don't care about download URLs not coming from our site
                $host = $uri->getHost();
                if ($host != 'www.jch-optimize.net')
                {
                        return true;
                }

                // Get the download ID
                $dlid = trim($this->params->get('pro_downloadid', ''));

                // If the download ID is invalid, return without any further action
                if (!preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
                {
                        return true;
                }

                // Append the Download ID to the download URL
                if (!empty($dlid))
                {
                        $uri->setVar('dlid', $dlid);
                        $url = $uri->toString();
                }

                return true;
        }

        /**
         * 
         * @return string
         */
        public function onAjaxFiletree()
        {
                return JchOptimizeAjax::fileTree();
        }

        /**
         * 
         */
        public function onAjaxOptimizeimages()
        {
                return JchOptimizeAjax::optimizeImages();
        }

        /**
         * 
         * @param type $arr
         */
        ##</procode>##
}
