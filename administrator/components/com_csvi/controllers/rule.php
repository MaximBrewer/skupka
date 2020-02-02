<?php
/**
 * @package     CSVI
 * @subpackage  Rules
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die;

/**
 * Rule Controller.
 *
 * @package     CSVI
 * @subpackage  Rules
 * @since       6.0
 */
class CsviControllerRule extends JControllerForm
{
	/**
	 * Load the plugin form.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 *
	 * @throws  Exception
	 */
	public function loadPluginForm()
	{
		// Load the plugins
		$db = JFactory::getDbo();
		$dispatcher = new RantaiPluginDispatcher;
		$dispatcher->importPlugins('csvirules', $db);
		$output = $dispatcher->trigger('getForm', array('id' => $this->input->get('plugin'), array('tmpl' => 'component')));

		// Output the form
		if (array_key_exists(0, $output))
		{
			echo $output[0];
		}

		JFactory::getApplication()->close();
	}

	/**
	 * Cancel the rule edit and return to the rules page
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  void.
	 *
	 * @since   7.8.0
	 */
	public function cancel($key = null)
	{
		// Set the end timestamp
		/** @var CsviModelRule $model */
		$model = $this->getModel();
		$model->updateLockedByUser(false);

		// Redirect back to the import page
		$this->setRedirect('index.php?option=com_csvi&view=rules');
		$this->redirect();
	}
}
