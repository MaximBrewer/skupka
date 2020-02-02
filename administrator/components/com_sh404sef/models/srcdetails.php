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

class Sh404sefModelSrcdetails extends Sh404sefClassBaselistmodel
{

	protected $_context      = 'srcdetails';
	protected $_defaultTable = '#__sh404sef_urls_src';

	/**
	 * Layout value
	 *
	 * @var string
	 */
	private $_layout = 'default';

	/**
	 * Object holding the url record
	 * for which we are handling src
	 *
	 * @var object
	 */
	private $_url = null;

	/**
	 * Read a url object from DB
	 *
	 * @param integer $id
	 */
	public function getUrl($id = 0)
	{
		if (is_null($this->_url))
		{
			$id = empty($id) ? $this->_getstate('url_id') : $id;
			$this->_url = JTable::getInstance('urls', 'Sh404sefTable');
			$this->_url->load($id);
		}

		return $this->_url;
	}

	/**
	 * Make the url with id = $cid the main url
	 * in case of duplicates. Also set the previous
	 * main url as secondary, swapping their rank
	 *
	 * @param integer $urlId id of the page for which details should be deleted
	 */
	public function purgeDetails($urlId)
	{
		// read targetUrl
		$url = $this->getUrl($urlId);

		// collect errors
		$error = $url->getError();
		if (!empty($error))
		{
			$this->setError($error . ' (trying to delete src details)');
			return false;
		}

		ShlDbHelper::delete($this->_defaultTable, array('url' => $url->newurl, 'routed_url' => $url->oldurl));
	}

	/**
	 * Hook to protected method to reset model internal cached data
	 * used after changing context for instance
	 */
	public function resetData()
	{
		// clean data, total and pagination, as we need them rebuilt
		$this->_data = null;
		$this->_total = null;
		$this->_pagination = null;
	}

	/**
	 * Hook to protected method to read latest state
	 */
	public function updateContextData()
	{
		$this->_updateContextData();
	}

	/**
	 * Gets alist of current filters and sort options which have
	 * been applied when building up the data
	 *
	 * @override
	 * @return object the list ov values as object properties
	 */
	public function getDisplayOptions()
	{
		$options = parent::getDisplayOptions();

		// get additional options vs base class

		// internal vs external
		$options->filter_component = $this->_getState('filter_component');
		$options->filter_language = $this->_getState('filter_language');

		// return cached instance
		return $options;
	}

	protected function _buildListSelect($options)
	{
		// array to hold select clause parts
		$select = array();
		$sql = 'select u1.*';

		$select[] = $sql;

		// add from  clause
		$select[] = 'from ' . $this->_defaultTable . ' as u1';

		// aggregate clauses
		$select = (count($select) ? implode(' ', $select) : '');

		return $select;
	}

	protected function _buildListWhere($options)
	{
		// get set of filters applied to the current view
		$filters = $this->getDisplayOptions();

		// array to hold where clause parts
		$where = array();

		// search for requested URL
		$url = $this->getUrl();
		// we could use this to hide duplicates (adding a "collapse by duplicate" filter)
		// overly complex from UI and concept standpoint
		//$where[] = $this->_db->qn('u1.url') . ' = ' . $this->_db->q($url->newurl);
		$where[] = $this->_db->qn('u1.routed_url') . ' = ' . $this->_db->q($url->oldurl);

		// add search all urls term if any
		if (!empty($filters->search_all))
		{
			$searchTerm = $this->_cleanForQuery(JString::strtolower($filters->search_all));
			$where[] = '('
				. 'LOWER(' . $this->_db->qn('u1.source_url') . ') LIKE ' . $this->_db->q('%' . $searchTerm . '%')
				. ' or '
				. 'LOWER(' . $this->_db->qn('u1.source_routed_url') . ') LIKE ' . $this->_db->q('%' . $searchTerm . '%')
				. ')';
		}

		// components check
		if (!empty($filters->filter_component))
		{
			$where[] = "LOWER(u1.source_url)  LIKE '%option=" . $this->_cleanForQuery($filters->filter_component) . "%'";
		}

		// language check
		if (!empty($filters->filter_language))
		{
			$where[] = "LOWER(u1.source_url)  LIKE '%lang=" . $this->_cleanForQuery($filters->filter_language) . "%'";
		}

		// aggregate clauses
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	protected function _getTableName()
	{
		return $this->_defaultTable;
	}

	/**
	 * Provides context data definition, to be used by context handler
	 * Should be overriden by descendant
	 */
	protected function _getContextDataDef()
	{
		$contextData = parent::_getContextDataDef();

		// define context data to be retrieved. Cannot be done at class level,
		// as some default values are dynamic
		$addedContextData = array(
			//URL id
			array('name' => 'url_id', 'html_name' => 'url_id', 'default' => 0, 'type' => 'int')
			, array('name' => 'filter_order', 'html_name' => 'filter_order', 'default' => 'source_routed_url', 'type' => 'string')
			, array('name' => 'filter_component', 'html_name' => 'filter_component', 'default' => '', 'type' => 'string')
			, array('name' => 'filter_language', 'html_name' => 'filter_language', 'default' => '', 'type' => 'string')
		);

		return array_merge($contextData, $addedContextData);
	}

}
