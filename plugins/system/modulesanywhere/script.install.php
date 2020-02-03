<?php
/**
 * @package         Modules Anywhere
 * @version         7.9.0
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2020 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;

require_once __DIR__ . '/script.install.helper.php';

class PlgSystemModulesAnywhereInstallerScript extends PlgSystemModulesAnywhereInstallerScriptHelper
{
	public $name           = 'MODULES_ANYWHERE';
	public $alias          = 'modulesanywhere';
	public $extension_type = 'plugin';

	public function uninstall($adapter)
	{
		$this->uninstallPlugin($this->extname, 'editors-xtd');

		$this->enableCoreEditorPlugin();
	}

	public function onBeforeInstall($route)
	{
		if ( ! parent::onBeforeInstall($route))
		{
			return false;
		}

		$this->showDivMessage();

		return true;
	}

	public function onAfterInstall($route)
	{
		$this->fixOldParams();
		$this->disableCoreEditorPlugin();

		return parent::onAfterInstall($route);
	}

	private function fixOldParams()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('extension_id'))
			->select($this->db->quoteName('params'))
			->from('#__extensions')
			->where($this->db->quoteName('element') . ' = ' . $this->db->quote('modulesanywhere'))
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote('plugin'))
			->where($this->db->quoteName('folder') . ' = ' . $this->db->quote('system'));
		$this->db->setQuery($query);

		$plugin = $this->db->loadObject();

		if (empty($plugin) || empty($plugin->params))
		{
			return;
		}

		$params = json_decode($plugin->params);

		if (empty($params))
		{
			return;
		}

		if (isset($params->handle_core_tags) || ! isset($params->handle_loadposition))
		{
			return;
		}

		$params->handle_core_tags = $params->handle_loadposition;
		unset($params->handle_loadposition);

		$params = json_encode($params);

		$query->clear()
			->update('#__extensions')
			->set($this->db->quoteName('params') . ' = ' . $this->db->quote($params))
			->where($this->db->quoteName('extension_id') . ' = ' . $this->db->quote($plugin->extension_id));
		$this->db->setQuery($query);
		$this->db->execute();
	}

	private function showDivMessage()
	{
		$installed_version = $this->getVersion($this->getInstalledXMLFile());

		if ($installed_version && version_compare($installed_version, 7, '<'))
		{
			JFactory::getApplication()->enqueueMessage(
				'Modules Anywhere no longer supports the <code>{div}</code> tags.<br>'
				. 'If you are using these, you will need to replace them with normal html <code>&lt;div&gt;</code> tags.<br><br>'
				. 'If you still need this functionality, you will need to downgrade to Modules Anywhere v6.0.6.'
				, 'warning'
			);
		}
	}

	private function disableCoreEditorPlugin()
	{
		$query = $this->getCoreEditorPluginQuery()
			->set($this->db->quoteName('enabled') . ' = 0')
			->where($this->db->quoteName('enabled') . ' = 1');
		$this->db->setQuery($query);
		$this->db->execute();

		if ( ! $this->db->getAffectedRows())
		{
			return;
		}

		JFactory::getApplication()->enqueueMessage(JText::_('Joomla\'s own "Module" editor button has been disabled'), 'warning');
	}

	private function enableCoreEditorPlugin()
	{
		$query = $this->getCoreEditorPluginQuery()
			->set($this->db->quoteName('enabled') . ' = 1')
			->where($this->db->quoteName('enabled') . ' = 0');
		$this->db->setQuery($query);
		$this->db->execute();

		if ( ! $this->db->getAffectedRows())
		{
			return;
		}

		JFactory::getApplication()->enqueueMessage(JText::_('Joomla\'s own "Module" editor button has been re-enabled'), 'warning');
	}

	private function getCoreEditorPluginQuery()
	{
		return $this->db->getQuery(true)
			->update('#__extensions')
			->where($this->db->quoteName('element') . ' = ' . $this->db->quote('module'))
			->where($this->db->quoteName('folder') . ' = ' . $this->db->quote('editors-xtd'))
			->where($this->db->quoteName('custom_data') . ' NOT LIKE ' . $this->db->quote('%modulesanywhere_ignore%'));
	}
}
