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
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

class Sh404sefModelEditalias extends Sh404sefClassBaseeditmodel
{

	protected $_context = 'sh404sef.editalias';
	protected $_defaultTable = 'aliases';
	protected $_alias = null;

	/**
	 * Create or update a record to
	 * DB from POST data
	 *
	 * Overriden from base model
	 * to also save metas and aliases
	 *
	 * @param array $dataArray an array holding data to save. If empty, $_POST is used
	 *
	 * @return integer id of created or updated record
	 */
	public function save($dataArray = null)
	{
		// use parent save method to save the url itself, from default values
		$this->_data = is_null($dataArray) ? JRequest::get('post') : $dataArray;

		// save the non-sef/sef pair data
		$savedId = $this->_save();

		// return savedId of the url, will have
		// been set to 0 if something wrong happened
		// while saving either url, meta data or aliase
		return $savedId;
	}

	/**
	 * Save an alias to the database
	 *
	 * @param integer $type force url type, used when saving a custom url
	 *
	 * @return int
	 */
	private function _save($type = Sh404sefHelperGeneral::COM_SH404SEF_URLTYPE_ALIAS)
	{
		// check for bad data
		if (empty($this->_data['newurl']))
		{
			return 0;
		}

		// get required tools
		jimport('joomla.database.table');
		$row = JTable::getInstance($this->_defaultTable, 'Sh404sefTable');

		// let table save record
		$row->save($this->_data['newurl']);

		// collect errors
		$error = $row->getError();
		if (!empty($error))
		{
			$this->setError($error);

			return 0;
		}

		// return what should be a non-zero id
		return $row->id;
	}

	/**
	 * Get a list of aliases from their ids,
	 * passed as params
	 *
	 * @param array $ids
	 *
	 * @internal param \of $array integer $ids the list of aliases id to fetch
	 *
	 * @return array of objects as read from db
	 */
	public function getByIds($ids = array())
	{
		$aliases = array();

		if (empty($ids))
		{
			return $aliases;
		}

		// select element where id is in list supplied
		try
		{
			$aliases = ShlDbHelper::selectObjectList($this->_getTableName(), array('*'), $this->_db->quoteName('id') . ' in (?)', ShlDbHelper::arrayToQuotedList($ids));
		}
		catch (Exception $e)
		{

		}

		// return result
		return $aliases;
	}

	/**
	 * Returns the default full table name on
	 * which this model operates
	 */
	protected function _getTableName()
	{
		return '#__sh404sef_aliases';
	}

	/**
	 * Delete a list of aliases from their ids,
	 * passed as params
	 *
	 * @param array $ids
	 *
	 * @internal param \of $array integer $ids the list of aliases id to delete
	 *
	 * @return boolean true if success
	 */
	public function deleteByIds($ids = array())
	{
		if (empty($ids))
		{
			return false;
		}

		try
		{
			// delete detailed stats
			$aliases = ShlDbHelper::selectColumn($this->_getTableName(), 'alias',
				$this->_db->qn('id') . ' in (' . ShlDbHelper::arrayToIntValList($ids) . ')');
			ShlDbHelper::deleteIn('#__sh404sef_hits_aliases', 'url', $aliases);


			// delete aliases
			ShlDbHelper::deleteIn($this->_getTableName(), 'id', $ids, ShlDbHelper::INTEGER);
		}
		catch (Exception $e)
		{
			$this->setError('Internal database error # ' . $e->getMessage());
			return false;
		}

		return true;
	}

}
