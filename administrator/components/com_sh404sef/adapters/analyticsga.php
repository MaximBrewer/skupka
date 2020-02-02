<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

jimport('joomla.application.application');

/**
 * Implement Google analytics handling
 *
 * @author shumisha
 *
 */
class Sh404sefAdapterAnalyticsga extends Sh404sefClassBaseanalytics
{
	protected $_endPoint = 'https://www.googleapis.com/analytics/v2.4/';

	/**
	 * Get tracking snippet
	 *
	 */
	public function getSnippet()
	{
		// should we insert tracking code snippet ?
		if (!$this->_shouldInsertSnippet())
		{
			return '';
		}

		//$config->analyticsType = 'uga';
		switch (Sh404sefFactory::getConfig()->analyticsEdition)
		{
			case 'uga':
				$snippet = $this->_getSnippetUga();
				break;
			case 'gtm':
				$snippet = $this->_getSnippetGtm();
				break;
			default:
				$snippet = '';
				break;
		}

		return $snippet . "\n";
	}

	/**
	 * Get Universal Analytics tracking snippet
	 *
	 */
	protected function _getSnippetUga()
	{
		// get config
		$config = Sh404sefFactory::getConfig();
		$pageInfo = Sh404sefFactory::getPageInfo();

		// in case of 404, we use a custom page url so that 404s can also be tracked in GA
		$customUrl = !empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404 ? '/__404__' : '';

		$displayData = array();
		$displayData['tracking_code'] = trim($config->analyticsUgaId);
		$displayData['custom_domain'] = 'auto';
		$displayData['custom_url'] = $customUrl;
		$displayData['anonymize'] = !empty($config->analyticsEnableAnonymization);
		$displayData['enable_display_features'] = !empty($config->analyticsEnableDisplayFeatures);
		$displayData['enable_enhanced_link_attr'] = !empty($config->analyticsEnableEnhancedLinkAttr);

		$snippet = ShlMvcLayout_Helper::render('com_sh404sef.analytics.snippet_uga', $displayData);

		return $snippet;
	}

	/**
	 * Get Google Tags manager snippet
	 *
	 */
	protected function _getSnippetGtm()
	{
		// get config
		$config = Sh404sefFactory::getConfig();

		$displayData = array();
		$displayData['tracking_code'] = trim($config->analyticsGtmId);

		// finalize snippet : add user tracking code
		$snippet = ShlMvcLayout_Helper::render('com_sh404sef.analytics.snippet_gtm', $displayData);

		return $snippet;
	}

	protected function _fetchAccountsList()
	{

		$hClient = Sh404sefHelperAnalytics::getHttpClient();
		$hClient->resetParameters($clearAll = true);

		// build the request
		$config = Sh404sefFactory::getConfig();
		if (empty($config->analyticsUgaId) && $config->analyticsEdition == 'uga')
		{
			throw new Sh404sefExceptionDefault('Analytics: no universal analytics web property ID set!');
		}
		if ($config->analyticsEdition == 'gtm' && empty($config->analyticsUgaId))
		{
			throw new Sh404sefExceptionDefault('Analytics: using Google tags manager, but no universal web property ID set!');
		}

		// @TODO: handle gtm
		if (empty($config->analyticsUgaId))
		{
			throw new Sh404sefExceptionDefault('Analytics: no analytics web property ID set!');
		}

		$accountIdBits = explode('-', trim($config->analyticsUgaId));
		if (empty($accountIdBits) || count($accountIdBits) < 3)
		{
			$msg = JText::_('COM_SH404SEF_ERROR_CHECKING_ANALYTICS') . '<br /><b>Invalid account Id fetching accounts list</b>';
			ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, JText::sprintf('COM_SH404SEF_ERROR_CHECKING_ANALYTICS', $msg));
			throw new Sh404sefExceptionDefault($msg);
		}
		else
		{
			$accoundId = $accountIdBits[1];
			ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, 'Analytics: fetching account list with id: ' . $accoundId);
		}

		// set target API url
		$uri = $this->_endPoint . 'management/accounts/' . $accoundId . '/webproperties/' . trim($config->analyticsUgaId) . '/profiles';
		ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, 'Analytics: fetching account list at: ' . $uri);
		$hClient->setUri($uri);

		// make sure we use GET
		$hClient->setMethod(Zendshl_Http_Client::GET);

		// set headers required by Google Analytics
		$headers = array('GData-Version' => 2, 'Authorization' => 'Bearer ' . Sh404sefHelperAnalytics_auth::getAccessToken());
		ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, 'Analytics: fetching account list with headers: ' . print_r($headers, true));

		$hClient->setHeaders($headers);

		// establish connection with available methods
		$adapters = array('Zendshl_Http_Client_Adapter_Curl', 'Zendshl_Http_Client_Adapter_Socket');
		$rawResponse = null;

		// perform connect request
		foreach ($adapters as $adapter)
		{
			try
			{
				$hClient->setAdapter($adapter);
				$response = $hClient->request();
				$body = $response->getBody();
				break;
			}
			catch (Exception $e)
			{
				// we failed, let's try another method
				ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, 'Analytics: fetching account list comm exception: ' . $e->getMessage());
			}
		}

		ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, 'Analytics: fetching account list response: ' . print_r($response, true));

		// handle any error
		Sh404sefHelperAnalytics::handleConnectResponseErrors($response);

		// analyze response
		// check if authentified
		Sh404sefHelperAnalytics::verifyAuthResponse($response);
		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($response->getBody());
		if ($xml === false)
		{
			$error = libxml_get_last_error();
			$error = is_object($error) && !empty($error->message) ? $error->message : 'Unknown XML error';
			$msg = 'Analytics: fetching account list invalid XML response: ' . $error;
			ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, $msg);
			throw new Sh404sefExceptionDefault($msg);
		}

		if (!empty($xml->entry))
		{
			foreach ($xml->entry as $entry)
			{
				$account = new StdClass();
				$bits = explode('/', (string) $entry->id);
				$account->id = array_pop($bits);
				$account->title = str_replace('Google Analytics Profile ', '', (string) $entry->title);
				$account->title = str_replace('Google Analytics View (Profile) ', '', $account->title);
				$this->_accounts[] = clone ($account);
			}
		}

		ShlSystem_Log::debug('sh404sef', '%s::%d: %s', __METHOD__, __LINE__, 'Analytics: fetched accounts: ' . print_r($this->_accounts, true));
	}

	/**
	 * prepare html filters to allow user to select the way she likes
	 * to view reports
	 */
	protected function _prepareFilters()
	{
		// array to hold various filters
		$filters = array();

		// find if we must display all filters. On dashboard, only a reduced set
		$allFilters = $this->_options['showFilters'] == 'yes';

		// select account to retrieve data for (or rather, profile
		$customSubmit = ' onchange="shSetupAnalytics({' . ($allFilters ? '' : 'showFilters:\'no\'') . '});"';

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$select = '<div class="btn-group">';
			$select .= Sh404sefHelperHtml::buildSelectList($this->_accounts, $this->_options['accountId'], 'accountId', $autoSubmit = false,
				$addSelectAll = false, $selectAllTitle = '', $customSubmit);
			$select .= '</div>';
			$filters[] = $select;

			// dashboard only has account selection, no room for anything else
			// only shows main selection drop downs on analytics view
			if ($allFilters)
			{
				$select = '<div class="btn-group">';
				$select .= '<label for="startDate">' . JText::_('COM_SH404SEF_ANALYTICS_START_DATE') . '</label>';
				$select .= JHTML::_('calendar', $this->_options['startDate'], 'startDate', 'startDate', '%Y-%m-%d', array('class' => 'class="textinput"'));
				$select .= '</div>';
				$filters[] = $select;

				$select = '<div class="btn-group">';
				$select .= '<label for="endDate">' . JText::_('COM_SH404SEF_ANALYTICS_END_DATE') . '</label>';
				$select .= JHTML::_('calendar', $this->_options['endDate'], 'endDate', 'endDate', '%Y-%m-%d', array('class' => 'class="textinput"'));
				$select .= '</div>';
				$filters[] = $select;

				// select groupBy (day, week, month)
				$select = '<div class="btn-group">';
				$select .= '<label for="groupBy">' . JText::_('COM_SH404SEF_ANALYTICS_GROUP_BY') . '</label>';
				$select .= Sh404sefHelperAnalytics::buildAnalyticsGroupBySelectList($this->_options['groupBy'], 'groupBy', $autoSubmit = false,
					$addSelectAll = false, $selectAllTitle = '', $customSubmit);
				$select .= '</div>';
				$filters[] = $select;

				// add a click to update link
				$filters[] = '<div class="row-fluid center analytics-filters-wrapper">' . ShlHtmlBs_Helper::button(JText::_('COM_SH404SEF_CHECK_ANALYTICS'), 'primary', '', 'javascript: shSetupAnalytics({forced:1'
					. ($allFilters ? '' : ',showFilters:\'no\'') . '});') . '</div>';
			}
			else
			{
				// on dashboard, there is no date select, so we must display the date range
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_DATE_RANGE') . '&nbsp;<div class="largertext">'
					. $this->_options['startDate'] . '&nbsp;&nbsp;>>&nbsp;&nbsp;' . $this->_options['endDate'] . '</div>';
			}
		}
		else
		{
			$select = Sh404sefHelperHtml::buildSelectList($this->_accounts, $this->_options['accountId'], 'accountId', $autoSubmit = false,
				$addSelectAll = false, $selectAllTitle = '', $customSubmit);
			$filters[] = JText::_('COM_SH404SEF_ANALYTICS_ACCOUNT') . ':&nbsp;' . $select;

			// dashboard only has account selection, no room for anything else
			// only shows main selection drop downs on analytics view
			if ($allFilters)
			{
				// select start date
				$select = JHTML::_('calendar', $this->_options['startDate'], 'startDate', 'startDate', '%Y-%m-%d', array('class' => 'class="textinput"'));
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_START_DATE') . ':&nbsp;' . $select;

				// select end date
				$select = JHTML::_('calendar', $this->_options['endDate'], 'endDate', 'endDate', '%Y-%m-%d', array('class' => 'class="textinput"'));
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_END_DATE') . ':&nbsp;' . $select;

				// select groupBy (day, week, month)
				$select = Sh404sefHelperAnalytics::buildAnalyticsGroupBySelectList($this->_options['groupBy'], 'groupBy', $autoSubmit = false,
					$addSelectAll = false, $selectAllTitle = '', $customSubmit);
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_GROUP_BY') . ':&nbsp;' . $select;

				// add a click to update link
				$filters[] = '&nbsp;<a href="javascript: void(0);" onclick="javascript: shSetupAnalytics({forced:1'
					. ($allFilters ? '' : ',showFilters:\'no\'') . '});" > [' . JText::_('COM_SH404SEF_CHECK_ANALYTICS') . ']</a>';
			}
			else
			{
				// on dashboard, there is no date select, so we must display the date range
				$filters[] = '&nbsp;' . JText::_('COM_SH404SEF_ANALYTICS_DATE_RANGE') . '&nbsp;<div class="largertext">'
					. $this->_options['startDate'] . '&nbsp;&nbsp;>>&nbsp;&nbsp;' . $this->_options['endDate'] . '</div>';
			}
		}
		// use layout to render
		return $filters;
	}
}
