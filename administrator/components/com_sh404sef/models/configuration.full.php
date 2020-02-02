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
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

/**
 * Model to read and save sh404SEF configuration from
 * #__extensions table, based on user input in a JForm
 *
 * @TODO: rewrite dynamic parts as custom form fields instead
 * of adding text to the form definition
 *
 */
class Sh404sefModelConfiguration extends ShlMvcModel_Base
{

	protected $_context = 'sh404sef.configuration';

	/**
	 * Save configuration to disk
	 * from POST data or input array of data
	 *
	 * When config will be saved to db, most of the code in this
	 * model will be removed and basemodel should handle everything
	 *
	 * @param array $data   an array holding data to save
	 * @param       integer id the com_sh404sef component id in extension table
	 *
	 * @return integer id of created or updated record
	 */
	public function save($data, $id)
	{
		// Check if the user is authorized to do this.
		if (!Sh404sefHelperAcl::userCan('sh404sef.view.configuration'))
		{
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		// instantiate a model from com_config
		$comConfigModel = Sh404sefHelperGeneral::getComConfigComponentModel('com_sh404sef',
			JPATH_ADMINISTRATOR . '/components/com_sh404sef/configuration');

		// don't save version, must be read from hardcoded value in config class
		if (isset($data["version"]))
		{
			unset($data["version"]);
		}

		// Save content of error page in our keystore table
		$activeLanguages = Sh404sefHelperLanguage::getAllInstalledLanguage(JPATH_ROOT);
		foreach ($activeLanguages as $language)
		{
			$fieldName = 'txt404_' . $language['tag'];
			if (isset($data[$fieldName]))
			{
				$this->_saveErrordocs($data[$fieldName], $language['tag']);
				unset($data[$fieldName]);
			}
		}

		// Mobile parameters will be saved both in the component parameters as well as plugin parameters
		if (isset($data['mobile_template']) || isset($data['mobile_switch_enabled']))
		{
			// get plugins details
			$plugin = JPluginHelper::getPlugin('system', 'shmobile');
			$params = new JRegistry();
			$params->loadString($plugin->params);

			// set params
			if (isset($data['mobile_switch_enabled']))
			{
				$params->set('mobile_switch_enabled', $data['mobile_switch_enabled']);
			}
			if (isset($data['mobile_template']))
			{
				$params->set('mobile_template', $data['mobile_template']);
			}
			// save
			$textParams = (string) $params;
			try
			{
				ShlDbHelper::update('#__extensions', array('params' => $textParams),
					array('element' => 'shmobile', 'folder' => 'system', 'type' => 'plugin'));
			}
			catch (Exception $e)
			{
			}
		}

		// make sure we have a default value for analytics groups
		if (!isset($data['analyticsUserGroups']))
		{
			$data['analyticsUserGroups'] = array(1);
		}

		// Google analytics oAuth handling
		// do we have a new token?
		if (!empty($data['wbgaauth_auth_token']))
		{
			// perform exchange of auth token for access and refresh tokens
			// they'll be stored in $data, to be saved with other confi elements
			try
			{
				Sh404sefHelperAnalytics_auth::exchangeTokens($data);
				// clear cache to make sure data is up to date
				// and avoid user confusion with cached, in error display
				$cache = JFactory::getCache('sh404sef_analytics');
				$cache->clean();
			}
			catch (Exception $e)
			{
				$this->setError($e->getMessage());
			}
		}
		else
		{
			if (empty($data['wbga_clearauthorization']))
			{
				// restore auth info from current values, so that they are not cleared
				// unless user wanted to clear them
				$sefConfig = Sh404sefFactory::getConfig();
				$data['wbgaauth_access_token'] = $sefConfig->wbgaauth_access_token;
				$data['wbgaauth_refresh_token'] = $sefConfig->wbgaauth_refresh_token;
				$data['wbgaauth_expires_on'] = $sefConfig->wbgaauth_expires_on;
				$data['wbgaauth_token_type'] = $sefConfig->wbgaauth_token_type;
				$data['wbgaauth_client_id_key'] = $sefConfig->wbgaauth_client_id_key;
			}
			else
			{
				$cache = JFactory::getCache('sh404sef_analytics');
				$cache->clean();
			}
		}

		// special processing for fields stored as arrays, but edited as strings
		$fields = array('shSecOnlyNumVars', 'shSecAlphaNumVars', 'shSecNoProtocolVars', 'ipWhiteList', 'ipBlackList', 'uAgentWhiteList',
		                'uAgentBlackList', 'analyticsExcludeIP');
		foreach ($fields as $field)
		{
			if (isset($data[$field]))
			{
				$data[$field] = $this->_setArrayParam($data[$field]);
			}
		}

		// Normalize the permissions
		$newPermissions = array();
		foreach ((array) $data['rules'] as $action => $ids)
		{
			// Build the rules array.
			$newPermissions[$action] = array();

			foreach ($ids as $permId => $p)
			{
				if ($p !== '')
				{
					$newPermissions[$action][$permId] = ($p == '1' || $p == 'true') ? true : false;
				}
			}
		}
		$data['rules'] = $newPermissions;

		// Attempt to save the configuration.
		$config = array('params' => $data, 'id' => $id, 'option' => 'com_sh404sef');
		$status = $comConfigModel->save($config);

		// store any error
		if (!$status)
		{
			$this->setError(JText::_('COM_SH404SEF_ERR_CONFIGURATION_NOT_SAVED') . ' ' . $comConfigModel->getError());
			ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $comConfigModel->getError());
		}

		return empty($this->_errors);
	}

	/**
	 * Prepare saving of  Error documents configuration options set
	 */
	private function _saveErrordocs($errorPagecontent, $languageTag)
	{
		try
		{
			$content = get_magic_quotes_gpc() ? stripslashes($errorPagecontent) : $errorPagecontent;
			ShlDb_Keystore::getInstance(Sh404sefClassConfig::COM_SH404SEF_KEYSTORE_TABLE_NAME)
				->put(Sh404sefClassConfig::COM_SH404SEF_KEYSTORE_KEY_404_ERROR_PAGE . '.' . $languageTag, $content);
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
		}
	}

	/**
	 * Turns a value entered by user as a string
	 * into an array, suitable for storage
	 *
	 * @param string $value input from user
	 */
	private function _setArrayParam($value)
	{
		$array = array();
		if (!empty($value))
		{
			$array = explode("\n", $value);
			foreach ($array as $k => $v)
			{
				$array[$k] = JString::trim($v);
			}
		}
		if (!empty($array))
		{
			$array = array_filter($array);
		}

		return $array;
	}

	public function getForm()
	{
		// import com_config model
		$comConfigModel = Sh404sefHelperGeneral::getComConfigComponentModel('com_sh404sef',
			JPATH_ADMINISTRATOR . '/components/com_sh404sef/configuration');
		$form = $comConfigModel->getForm();
		$component = $comConfigModel->getComponent();

		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$method = '_getByComponentField' . $this->joomlaVersionPrefix;
		// inject the by components part in the form
		$field = $this->$method();
		$form->setField($field);

		// inject the languages part in the form
		$method = '_getLanguagesField' . $this->joomlaVersionPrefix;
		$field = $this->$method();
		$form->setField($field);

		// inject the current content of the 404 error page as default value in the txt404 form field
		$contents = $this->_getErrorPageContents();
		foreach ($contents as $langTag => $field)
		{
			$form->setField($field['xml']);
			$form->setValue($field['name'], null, $field['content']);
		}

		// inject analytics group field in form
		$field = $this->_getAnalyticsGroupsField();
		$form->setField($field);
		$field = $this->_getAnalyticsDisabledGroupsField();
		$form->setField($field);

		// merge categories in jooomla tab
		$field = $this->_getCategoriesField();
		$form->setField($field);

		// Bind the form to the data.
		if ($form && $component->params)
		{
			$form->bind($component->params);
		}

		// do we need Google oAuth?
		$accessToken = $form->getValue('wbgaauth_access_token', null);
		$form->setValue('wbgaauth_auth_required', null, empty($accessToken) ? 1 : 0);

		// pass the client id we're using, so that it's stored along the corresponding access and refresh tokens
		$clientIdRecord = Sh404sefHelperAnalytics_auth::getGaAuthClientId();
		$form->setValue('wbgaauth_client_id_key', null, $clientIdRecord['key']);

		// clear all other oauth fields, we don't need them in the form
		$form->setValue('wbgaauth_access_token', null, '');
		$form->setValue('wbgaauth_refresh_token', null, '');
		$form->setValue('wbgaauth_expires_on', null, '');
		$form->setValue('wbgaauth_token_type', null, '');

		// special processing for various parameters: turn string into an array
		// security
		$form->setValue('shSecOnlyNumVars', null, implode("\n", $form->getValue('shSecOnlyNumVars', null, array())));
		$form->setValue('shSecAlphaNumVars', null, implode("\n", $form->getValue('shSecAlphaNumVars', null, array())));
		$form->setValue('shSecNoProtocolVars', null, implode("\n", $form->getValue('shSecNoProtocolVars', null, array())));
		$form->setValue('ipWhiteList', null, implode("\n", $form->getValue('ipWhiteList', null, array())));
		$form->setValue('ipBlackList', null, implode("\n", $form->getValue('ipBlackList', null, array())));
		$form->setValue('uAgentWhiteList', null, implode("\n", $form->getValue('uAgentWhiteList', null, array())));
		$form->setValue('uAgentBlackList', null, implode("\n", $form->getValue('uAgentBlackList', null, array())));
		// analytics
		$form->setValue('analyticsExcludeIP', null, implode("\n", $form->getValue('analyticsExcludeIP', null, array())));

		// read mobile params from the mobile plugin, not from the component config, which only has a copy
		$plugin = JPluginHelper::getPlugin('system', 'shmobile');
		if (!empty($plugin))
		{
			// if plugin is published...
			$params = new JRegistry();
			$params->loadString($plugin->params);
			$form->setValue('mobile_switch_enabled', null, $params->get('mobile_switch_enabled', 0));
			$form->setValue('mobile_template', null, $params->get('mobile_template', ''));
		}
		// inject a link to shLib plugin params for cache settings
		$form
			->setFieldAttribute('UrlCacheHandler', 'additionaltext',
				'<span class = "btn sh404sef-textinput"><a href="' . Sh404sefHelperGeneral::getShLibPluginLink() . '" target="_blank">'
				. JText::_('COM_SH404SEF_CONFIGURE_SHLIB_PLUGIN') . '</a></span>');
		return $form;
	}

	/*
	 * Creates the By component dynamic form field
	 */

	/**
	 * Push current error documents content
	 * values into the view for edition
	 * this is a altered version of the same
	 * method in the old config view.
	 */
	private function _getErrorPageContents()
	{
		$activeLanguages = Sh404sefHelperLanguage::getAllInstalledLanguage(JPATH_ROOT);
		$contents = array();
		foreach ($activeLanguages as $language)
		{
			// XML field def
			$langTag = $language['tag'];
			$langName = str_replace('-', '_', $language['tag']);
			$fieldName = 'txt404_' . $language['tag'];
			$xml = '';
			$xml .= '<fieldset name="page_404_' . $language['tag'] . '" label="' . JText::_('COM_SH404SEF_CONFIG_ERROR_PAGE')
				. ' ' . $langName . '" description="" groupname="COM_SH404SEF_CONFIG_ERROR_PAGE">';

			// add Itemid selection
			$xml .= '<field type="menuitem" name="languages_' . $language['tag'] . '_notFoundItemid" default="0"
               label="COM_SH404SEF_PAGE_NOT_FOUND_ITEMID" description="COM_SH404SEF_TT_PAGE_NOT_FOUND_ITEMID" size="30"
               maxlength="30">';
			$xml .= '<option value="0">' . JText::_('JNONE') . '</option>';
			$xml .= '</field>';
			// add page content editor
			$xml .= '<field name="' . $fieldName . '" type="editor" label="COM_SH404SEF_404_ERROR_PAGE_CONTENT">';

			$xml .= '</field></fieldset>';

			// existing/default content
			$content = ShlDb_Keystore::getInstance(Sh404sefClassConfig::COM_SH404SEF_KEYSTORE_TABLE_NAME)
				->get(Sh404sefClassConfig::COM_SH404SEF_KEYSTORE_KEY_404_ERROR_PAGE . '.' . $language['tag']);
			$content = empty($content) ? JText::_('COM_SH404SEF_DEF_404_MSG') : $content;

			// build return array
			$contents[$langTag] = array();
			$contents[$langTag]['name'] = $fieldName;
			$contents[$langTag]['group'] = 'COM_SH404SEF_CONFIG_ERROR_PAGE';
			$contents[$langTag]['xml'] = new SimpleXMLElement($xml);
			$contents[$langTag]['content'] = $content;
		}

		return $contents;
	}

	private function _getAnalyticsGroupsField()
	{
		$usergroups = JHtml::_('user.groups', $includeSuperAdmin = true);
		$xml = '';
		$xml .= '<fieldset name="analytics" label="COM_SH404SEF_CONFIG_ANALYTICS" description="COM_SH404SEF_CONF_ANALYTICS_HELP" groupname="COM_SH404SEF_CONFIG_ANALYTICS">';
		$xml .= '<field menu="hide" name="analyticsUserGroups" type="list" multiple="true" size="10" default="[1]" label="COM_SH404SEF_ANALYTICS_USER_GROUPS" description="COM_SH404SEF_TT_ANALYTICS_USER_GROUPS">';
		foreach ($usergroups as $usergroup)
		{
			$t = htmlspecialchars($usergroup->text, ENT_COMPAT, 'UTF-8');
			$xml .= '<option value="' . $usergroup->value . '">' . htmlspecialchars($t, ENT_COMPAT, 'UTF-8') . '</option>';
		}
		$xml .= '</field></fieldset>';
		$element = new SimpleXMLElement($xml);
		return $element;
	}

	private function _getAnalyticsDisabledGroupsField()
	{
		$usergroups = JHtml::_('user.groups', $includeSuperAdmin = true);
		$xml = '';
		$xml .= '<fieldset name="analytics" label="COM_SH404SEF_CONFIG_ANALYTICS" description="COM_SH404SEF_CONF_ANALYTICS_HELP" groupname="COM_SH404SEF_CONFIG_ANALYTICS">';
		$xml .= '<field menu="hide" name="analyticsUserGroupsDisabled" type="list" multiple="true" size="10" default="[1]" label="COM_SH404SEF_ANALYTICS_USER_GROUPS_DISABLED" description="COM_SH404SEF_TT_ANALYTICS_USER_GROUPS_DISABLED">';
		foreach ($usergroups as $usergroup)
		{
			$t = htmlspecialchars($usergroup->text, ENT_COMPAT, 'UTF-8');
			$xml .= '<option value="' . $usergroup->value . '">' . htmlspecialchars($t, ENT_COMPAT, 'UTF-8') . '</option>';
		}
		$xml .= '</field></fieldset>';
		$element = new SimpleXMLElement($xml);
		return $element;
	}

	/*
	 * Creates the Languages dynamic form field
	 */

	private function _getCategoriesField()
	{
		$catListOptions = JHtml::_('category.options', 'com_content');
		$options = '';
		foreach ($catListOptions as $cat)
		{
			// need to apply htmlspecialchars twice, as SimpleXMLElement does an
			// htmlentitydecode in the constructor, which then causes
			// an error downstream when this data is injected in the form
			$t = htmlspecialchars($cat->text, ENT_COMPAT, 'UTF-8');
			$options .= '<option value="' . $cat->value . '">' . htmlspecialchars($t, ENT_COMPAT, 'UTF-8') . '</option>';
		}
		$xml = '';
		$xml .= '<fieldset name="joomla" label="Joomla" description="" groupname="COM_SH404SEF_CONFIG_EXT">';
		$xml .= '<field menu="hide" name="shInsertContentArticleIdCatList" type="list" multiple="true" default="" label="COM_SH404SEF_INSERT_NUMERICAL_ID_CAT_LIST" description="COM_SH404SEF_TT_INSERT_NUMERICAL_ID_CAT_LIST">';
		$xml .= '<option value="">COM_SH404SEF_INSERT_NUMERICAL_ID_ALL_CAT</option>';
		$xml .= $options;
		$xml .= '</field>';
		$xml .= '<field menu="hide" name="shInsertNumericalIdCatList" type="list" multiple="true" default="" label="COM_SH404SEF_INSERT_NUMERICAL_ID_CAT_LIST" description="COM_SH404SEF_TT_INSERT_NUMERICAL_ID_CAT_LIST">';
		$xml .= '<option value="">COM_SH404SEF_INSERT_NUMERICAL_ID_ALL_CAT</option>';
		$xml .= $options;
		$xml .= '</field>';
		$xml .= '<field menu="hide" name="insertDateCatList" type="list" multiple="true" default="" label="COM_SH404SEF_INSERT_DATE_CAT_LIST" description="COM_SH404SEF_TT_INSERT_DATE_CAT_LIST">';
		$xml .= '<option value="">COM_SH404SEF_INSERT_NUMERICAL_ID_ALL_CAT</option>';
		$xml .= $options;
		$xml .= '</field>';
		$xml .= '</fieldset>';
		$element = new SimpleXMLElement($xml);
		return $element;
	}

	/**
	 * Set values in configuration record in database
	 * Optionally update current in memory configuration object
	 *
	 * @param array   $values
	 * @param boolean $reset if true, config object in memory will be reset to new values
	 * @return boolean
	 */
	public function setValues($values = array(), $reset = false)
	{

		if (empty($values))
		{
			return true;
		}

		jimport('joomla.application.component.helper');
		$component = JComponentHelper::getComponent('com_sh404sef');
		$params = new JRegistry();
		$params->loadString($component->params);

		// set values
		foreach ($values as $key => $value)
		{
			$params->set($key, $value);
		}

		// convert to json and store into db
		$textParams = $params->toString();
		try
		{
			ShlDbHelper::update('#__extensions', array('params' => $textParams), array('element' => 'com_sh404sef', 'type' => 'component'));
			if ($reset)
			{
				$config = Sh404sefFactory::getConfig($reset = true);
			}
			$status = true;
		}
		catch (Exception $e)
		{
			$status = false;
		}

		return $status;
	}

	/*
	 * Creates the Analytics groups dynamic field
	 */

	private function _getByComponentFieldJ3()
	{
		$installedComponents = Sh404sefHelperGeneral::getComponentsList();
		$xml = '';

		$xml .= '<fieldset name="by_component" label="COM_SH404SEF_CONF_TAB_BY_COMPONENT" description="" groupname="COM_SH404SEF_CONFIG">';
		foreach ($installedComponents as $name => $properties)
		{
			$xml .= '<field type="shlegend" shlrenderer="shlegend" class="text" label="' . ucfirst(str_replace('com_', '', $name)) . '"/>';
			$xml .= '<field menu="hide" name="' . $name
				. '___manageURL" type="list" default="0" label="" description="COM_SH404SEF_TT_ADV_MANAGE_URL">';
			$xml .= '<option value="0">COM_SH404SEF_USE_DEFAULT</option>
					<option value="1">COM_SH404SEF_NOCACHE</option>
					<option value="2">COM_SH404SEF_SKIP</option>
					<option value="3">COM_SH404SEF_USE_JOOMLA_ROUTER</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name
				. '___shDoNotOverrideOwnSef" type="list" default="" label="" description="COM_SH404SEF_TT_ADV_OVERRIDE_SEF">';
			$xml .= '<option value="0">COM_SH404SEF_OVERRIDE_SEF_EXT</option>
					<option value="1">COM_SH404SEF_USE_JOOMLA_PLUGIN</option>
					<option value="50">COM_SH404SEF_USE_JOOMLA_PLUGIN_WITH_MENU</option>
					<option value="30">COM_SH404SEF_USE_JOOMSEF_PLUGIN</option>
					<option value="40">COM_SH404SEF_USE_ACESEF_PLUGIN</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name
				. '___compEnablePageId" type="list" default="" label="" description="COM_SH404SEF_TT_COMP_ENABLE_PAGEID">';
			$xml .= '<option value="0">COM_SH404SEF_DISABLE_PAGEID</option>
					<option value="1">COM_SH404SEF_ENABLE_PAGEID</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" type="text" name="' . $name
				. '___defaultComponentString" default=""  label="" description="COM_SH404SEF_TT_ADV_COMP_DEFAULT_STRING" size="30" maxlength="30"/>';
		}

		$xml .= '</fieldset>';

		$element = new SimpleXMLElement($xml);

		return $element;
	}

	private function _getByComponentFieldJ2()
	{
		$installedComponents = Sh404sefHelperGeneral::getComponentsList();
		$xml = '';

		$xml .= '<fieldset name="by_component" label="COM_SH404SEF_CONF_TAB_BY_COMPONENT" description="" groupname="COM_SH404SEF_CONFIG">';
		$xml .= '<field type="spacer" class="text" label="" description="COM_SH404SEF_TT_ADV_MANAGE_URL"/>
				<field type="spacer" class="text" label="" description="COM_SH404SEF_TT_ADV_OVERRIDE_SEF"/>
				<field type="spacer" class="text" label="" description="COM_SH404SEF_TT_COMP_ENABLE_PAGEID"/>
				<field type="spacer" class="text" label="" description="COM_SH404SEF_TT_ADV_COMP_DEFAULT_STRING"/>';
		foreach ($installedComponents as $name => $properties)
		{
			$xml .= '<field menu="hide" name="' . $name . '___manageURL" type="list" default="0" label="' . str_replace('com_', '', $name)
				. '" description="">';
			$xml .= '<option value="0">COM_SH404SEF_USE_DEFAULT</option>
					<option value="1">COM_SH404SEF_NOCACHE</option>
					<option value="2">COM_SH404SEF_SKIP</option>
					<option value="3">COM_SH404SEF_USE_JOOMLA_ROUTER</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name . '___shDoNotOverrideOwnSef" type="list" default="" label="" description="">';
			$xml .= '<option value="0">COM_SH404SEF_OVERRIDE_SEF_EXT</option>
					<option value="1">COM_SH404SEF_USE_JOOMLA_PLUGIN</option>
					<option value="50">COM_SH404SEF_USE_JOOMLA_PLUGIN_WITH_MENU</option>
					<option value="30">COM_SH404SEF_USE_JOOMSEF_PLUGIN</option>
					<option value="40">COM_SH404SEF_USE_ACESEF_PLUGIN</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" name="' . $name . '___compEnablePageId" type="list" default="" label="" description="">';
			$xml .= '<option value="0">COM_SH404SEF_DISABLE_PAGEID</option>
					<option value="1">COM_SH404SEF_ENABLE_PAGEID</option>';
			$xml .= '</field>';

			$xml .= '<field menu="hide" type="text" name="' . $name
				. '___defaultComponentString" default=""  label="" description="" size="30" maxlength="30"/>';
		}

		$xml .= '</fieldset>';

		$element = new SimpleXMLElement($xml);

		return $element;
	}

	private function _getLanguagesFieldJ3()
	{
		$activeLanguages = Sh404sefHelperLanguage::getAllInstalledLanguage(JPATH_ROOT);

		$xml = '';
		$xml .= '<fieldset
		name="languages"
		label="COM_SH404SEF_CONF_TAB_LANGUAGES"
		description=""
		groupname="COM_SH404SEF_CONFIG"
		>

        <field type="shlegend" shlrenderer="shlegend" class="text" label="COM_SH404SEF_TRANSLATION_TITLE"/>';
		foreach ($activeLanguages as $language)
		{
			$xml .= '<field type="shlegend" shlrenderer="shlegend" class="text" label="' . $language['tag']
				. '"/>
			<field menu="hide" name="languages_' . $language['tag']
				. '_pageText" type="text" default="Page-&#37;s" label="COM_SH404SEF_PAGETEXT" description="COM_SH404SEF_TT_PAGETEXT"/>';
		}
		$xml .= '</fieldset>';

		$element = new SimpleXMLElement($xml);

		return $element;
	}

	private function _getLanguagesFieldJ2()
	{
		$activeLanguages = Sh404sefHelperLanguage::getAllInstalledLanguage(JPATH_ROOT);

		$xml = '';
		$xml .= '<fieldset
		name="languages"
		label="COM_SH404SEF_CONF_TAB_LANGUAGES"
		description=""
		groupname="COM_SH404SEF_CONFIG"
		>

        <field type="spacer" class="text" label="COM_SH404SEF_TRANSLATION_TITLE"/>';
		foreach ($activeLanguages as $language)
		{
			$xml .= '<field type="spacer" class="text" label="' . $language['tag'] . '"/>
			<field menu="hide" name="languages_' . $language['tag']
				. '_pageText" type="text" default="Page-&#37;s" label="COM_SH404SEF_PAGETEXT" description="COM_SH404SEF_TT_PAGETEXT"/>';
		}
		$xml .= '</fieldset>';
		$element = new SimpleXMLElement($xml);
		return $element;
	}

	public function checkJoomlaConfig()
	{
		$config = Sh404sefFactory::getConfig();
		if(empty($config->Enabled))
		{
			// no message as long as sh404SEF is disabled
			return true;
		}

		$app = JFactory::getApplication();
		// sef enabled
		$sef = $app->get('sef');
		if (empty($sef))
		{
			return false;
		}

		// html suffix
		$suffix = $app->get('sef_suffix');
		if (($suffix && empty($config->suffix))
			|| (!$suffix && !empty($config->suffix))
		)
		{
			return false;
		}

		return true;
	}

	public function checkAnalytics()
	{
		$config = Sh404sefFactory::getConfig();

		// data collection config
		if (empty($config->analyticsUgaId) && $config->analyticsEdition == 'uga')
		{
			return false;
		}
		if ($config->analyticsEdition == 'gtm' && empty($config->analyticsUgaId))
		{
			return false;
		}

		if (!$config->analyticsReportsEnabled)
		{
			return true;
		}

		$token = empty($config->wbgaauth_access_token) ? '' : $config->wbgaauth_access_token;
		if (empty($token) || empty($config->analyticsUgaId))
		{
			return false;
		}

		return true;
	}
}
