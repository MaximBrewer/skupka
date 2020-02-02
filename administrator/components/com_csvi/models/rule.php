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
 * Rule model.
 *
 * @package     CSVI
 * @subpackage  Rule
 * @since       6.0
 */
class CsviModelRule extends JModelAdmin
{
	/**
	 * The database class
	 *
	 * @var    JDatabaseDriver
	 * @since  6.0
	 */
	protected $db;

	/**
	 * Holds the input class
	 *
	 * @var    JInput
	 * @since  6.6.0
	 */
	protected $input;

	/**
	 * Public class constructor
	 *
	 * @param   array  $config  The configuration array
	 *
	 * @throws  Exception
	 *
	 * @since   4.0
	 */
	public function __construct($config = array())
	{
		parent::__construct();

		$this->db    = JFactory::getDbo();
		$this->input = JFactory::getApplication()->input;
	}

	/**
	 * Get the form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success | False on failure.
	 *
	 * @since   4.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_csvi.rule', 'rule', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The data for the form..
	 *
	 * @since   4.0
	 *
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		// Update locked_by field
		$this->updateLockedByUser(true);

		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_csvi.edit.rule.data', array());

		if (0 === count($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Validate the plugin forms data.
	 *
	 * @param   string  $plugin  The name of the plugin.
	 * @param   array   $data    The data to filter.
	 *
	 * @return  array|bool  Filtered data | false if there is a validation error.
	 *
	 * @since   6.6.1
	 */
	public function validateData($plugin, $data)
	{
		// Set the return data
		$validData = $data;

		// Get the folder name
		$folder = false;

		if (substr($plugin, 0, 4) == 'csvi')
		{
			$folder = substr($plugin, 4);
		}

		if ($folder)
		{
			// Load the language files
			$lang = JFactory::getLanguage();
			$lang->load('plg_csvirules_' . $folder, JPATH_ADMINISTRATOR, 'en-GB', true);
			$lang->load('plg_csvirules_' . $folder, JPATH_ADMINISTRATOR, null, true);

			// Add the form path for this plugin
			JForm::addFormPath(JPATH_PLUGINS . '/csvirules/' . $folder . '/');

			// Instantiate the form
			$form = JForm::getInstance($folder, 'form_' . $folder);

			// Clean the data
			$validData = $form->filter($data);

			// Validate the data
			$return = $form->validate($data, 'pluginform');

			// Check for an error.
			if ($return instanceof Exception)
			{
				$this->setError($return->getMessage());

				return false;
			}

			// Check the validation results.
			if ($return === false)
			{
				// Get the validation messages from the form.
				foreach ($form->getErrors() as $message)
				{
					$this->setError($message);
				}

				return false;
			}
		}

		return $validData;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   6.6.0
	 *
	 * @throws  Exception
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		// Get the plugin parameters
		$item->pluginform = json_decode($item->plugin_params, true);

		return $item;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   3.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 * @throws  UnexpectedValueException
	 */
	public function save($data)
	{
		// Get the complete posted data
		$postData = $this->input->post->getArray(array(), null, 'raw');

		// Validate the form data
		try
		{
			$postData = $this->validateData($data['plugin'], $postData);
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		if (isset($postData['pluginform']))
		{
			$data['plugin_params'] = json_encode($postData['pluginform']);
		}

		// Update locked_by field after saving
		$this->updateLockedByUser(false);

		return parent::save($data);
	}

	/**
	 * Copy of a rule to a new one.
	 *
	 * @param   array  $ruleIds  The ID of the rule to copy.
	 * @param   array  $data     The new data to store.
	 *
	 * @return  bool  True on success | False on failure.
	 *
	 * @since   6.6.0
	 *
	 * @throws  Exception
	 * @throws  CsviException
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 * @throws  UnexpectedValueException
	 */
	public function createCopy($ruleIds, $data = array())
	{
		if (!is_array($ruleIds))
		{
			$ruleIds = (array) $ruleIds;
		}

		foreach ($ruleIds as $ruleId)
		{
			// Select and create a copy of rule
			$table = $this->getTable('Rule');
			$table->load($ruleId);
			$table->set('name', $table->get('name') . ' copy');

			if ($data)
			{
				$table->bind($data);
			}

			$table->set('csvi_rule_id', 0);

			if (!$table->store())
			{
				throw new CsviException(JText::sprintf('COM_CSVI_CANNOT_COPY_RULE', $table->getError()));
			}
		}

		return true;
	}

	/**
	 * Method to get a template list where rule is applied.
	 *
	 * @param   integer  $ruleId  The id of the rule.
	 *
	 * @return  mixed    Array with found templates
	 *
	 * @since   7.7.0
	 */
	public function getTemplatesList($ruleId)
	{
		if (!$ruleId)
		{
			return false;
		}

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('template_name'))
			->select($this->db->quoteName('templatefields.csvi_template_id', 'template_id'))
			->from($this->db->quoteName('#__csvi_templates', 'templates'))
			->leftJoin(
				$this->db->quoteName('#__csvi_templatefields', 'templatefields')
				. ' ON ' . $this->db->quoteName('templatefields.csvi_template_id') . ' = ' . $this->db->quoteName('templates.csvi_template_id')
			)
			->leftJoin(
				$this->db->quoteName('#__csvi_templatefields_rules', 'templatefields_rules')
				. ' ON ' . $this->db->quoteName('templatefields_rules.csvi_templatefield_id') . ' = ' . $this->db->quoteName('templatefields.csvi_templatefield_id')
			)
			->where($this->db->quoteName('templatefields_rules.csvi_rule_id') . ' = ' . (int) $ruleId);
		$this->db->setQuery($query);

		if (!$templates = $this->db->loadObjectList())
		{
			$templates = array();
		}

		return $templates;
	}

	/**
	 * Method to update locked by user
	 *
	 * @param bool $setUser Set if user id has to be updated
	 *
	 * @since   7.8.0
	 */
	public function updateLockedByUser($setUser = true)
	{
		$ruleId = $this->input->get('csvi_rule_id', false);
		$user   = JFactory::getUser();
		$id     = 0;
		$lockedDate = $this->db->getNullDate();

		if ($setUser && $ruleId)
		{
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('locked_by'))
				->from($this->db->quoteName('#__csvi_rules'))
				->where($this->db->quoteName('csvi_rule_id') . ' = ' . (int) $ruleId);
			$this->db->setQuery($query);
			$alreadySetUser = $this->db->loadResult();

			// Check if the user checked out is the same else throw error
			if ($alreadySetUser && $alreadySetUser !== $user->id)
			{
				$url = 'index.php?option=com_csvi&view=rules';
				JFactory::getApplication()->redirect($url,
					JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED',JText::_('JLIB_APPLICATION_ERROR_CHECKOUT_USER_MISMATCH')), 'error');
			}

			// Set check in user id and date
			$id         = $user->id;
			$jdate      = JFactory::getDate('now', 'UTC');
			$lockedDate = $jdate->format('Y-m-d H:i:s');
		}

		if ($ruleId)
		{
			$query = $this->db->getQuery(true)
				->update($this->db->quoteName('#__csvi_rules'))
				->set($this->db->quoteName('locked_by') . ' = ' . (int) $id)
				->set($this->db->quoteName('locked_on') . ' = ' . $this->db->quote($lockedDate))
				->where($this->db->quoteName('csvi_rule_id') . ' = ' . (int) $ruleId);

			$this->db->setQuery($query)->execute();
		}
	}

	/**
	 * Reset the locked by and locked on for rules plugins
	 *
	 * @param array $ids Rules ids to check-in
	 *
	 * @return  False if something goes wrong true otherwise.
	 *
	 * @since   7.8.0
	 */
	public function resetLocked($ids)
	{
		if (!is_array($ids))
		{
			$ids = (array) $ids;
		}

		foreach ($ids as $id)
		{
			$query = $this->db->getQuery(true)
				->update($this->db->quoteName('#__csvi_rules'))
				->set($this->db->quoteName('locked_by') . ' = 0')
				->set($this->db->quoteName('locked_on') . ' = ' . $this->db->quote($this->db->getNullDate()))
				->where($this->db->quoteName('csvi_rule_id') . ' = ' . (int) $id);

			$this->db->setQuery($query);

			if (!$this->db->execute())
			{
				return false;
			}
		}

		return true;
	}
}
