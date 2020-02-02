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

class Sh404sefModelHitdetails extends Sh404sefClassBaselistmodel
{

	protected $_context      = 'hitdetails';
	protected $_defaultTable = '';

	/**
	 * Layout value
	 *
	 * @var string
	 */
	private $_layout = 'default';

	/**
	 * Object holding the url record
	 * for which we are handling hits
	 *
	 * @var object
	 */
	private $_url = null;

	/**
	 * Method to get lists item data
	 *
	 * @access public
	 * @param        object             holding options
	 * @param boolea $returnZeroElement . If true, and the list returned is empty, a null object will be returned (as an array)
	 * @return array
	 */
	public function getList($options = null, $returnZeroElement = false, $forcedLimitstart = null, $forcedLimit = null)
	{
		$set = $this->_setTableName();
		if ($set !== true)
		{
			$this->setError($set);
			return $this->_data;
		}

		parent::getList($options, $returnZeroElement, $forcedLimitstart, $forcedLimit);

		return $this->_data;
	}

	/**
	 * Method to get the total number of categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal($options = null)
	{
		$set = $this->_setTableName();
		if ($set !== true)
		{
			$this->setError($set);
			return $this->_total;
		}
		parent::getTotal($options);

		return $this->_total;
	}

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
			$requestType = $this->getRequestType();
			switch ($requestType)
			{
				case Sh404sefModelReqrecorder::REQUEST_404:
					$table = 'urls';
					$field = 'oldurl';
					break;
				case Sh404sefModelReqrecorder::REQUEST_ALIAS:
					$table = 'aliases';
					$field = 'alias';
					break;
				case Sh404sefModelReqrecorder::REQUEST_SHURL:
					$table = 'pageids';
					$field = 'pageid';
					break;
				default:
					$this->_url = new stdClass();
					$this->_url->oldurl = '';
					$this->_url->requested_url = '';
					return $this->_url;
					break;
			}
			$this->_url = JTable::getInstance($table, 'Sh404sefTable');
			$this->_url->load($id);
			$this->_url->requested_url = $this->_url->{$field};
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
	public function purgeDetails($urlId, $requestType)
	{
		$requestType = $this->getRequestType($requestType);
		$this->_setState('request_type', $requestType);
		$set = $this->_setTableName();
		if ($set !== true)
		{
			$this->setError($set);
			return;
		}

		// read targetUrl
		$url = $this->getUrl($urlId);
		// collect errors
		$error = $url->getError();
		if (!empty($error))
		{
			$this->setError($error . ' (trying to delete hit details)');
			return false;
		}

		ShlDbHelper::delete($this->_defaultTable, array('url' => $url->requested_url));
	}

	public function getRequestType($requestType = null)
	{
		$requestType = is_null($requestType) ? $this->_getState('request_type') : $requestType;
		switch ($requestType)
		{
			case Sh404sefModelReqrecorder::REQUEST_404:
			case Sh404sefModelReqrecorder::REQUEST_ALIAS:
			case Sh404sefModelReqrecorder::REQUEST_SHURL:
				break;
			default:
				return false;
				break;
		}

		return $requestType;
	}

	private function _setTableName()
	{
		$requestType = $this->_getState('request_type');
		if ($requestType === false)
		{
			return 'Invalid request type ' . $requestType;
		}

		$this->_defaultTable = '#__sh404sef_hits_' . $requestType;

		return true;
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
		$options->filter_hit_type = $this->_getState('filter_hit_type');
		$options->filter_collapse = $this->_getState('filter_collapse');
		$options->filter_hide_empty = $this->_getState('filter_hide_empty');

		// return cached instance
		return $options;
	}

	protected function _buildListSelect($options)
	{
		$filters = $this->getDisplayOptions();

		// array to hold select clause parts
		$select = array();
		$sql = 'select u1.*';

		if ($filters->filter_collapse != Sh404sefHelperGeneral::COLLAPSE_NONE)
		{
			$sql .= ', count(u1.id) as hits';
		}
		$select[] = $sql;

		// add from  clause
		$select[] = 'from ' . $this->_getTableName() . ' as u1';

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
		$where[] = $this->_db->qn('u1.url') . ' = ' . $this->_db->q($url->requested_url);

		// add search all urls term if any
		if (!empty($filters->search_all))
		{
			$searchTerm = $this->_cleanForQuery(JString::strtolower($filters->search_all));
			$where[] = '('
				. 'LOWER(' . $this->_db->qn('u1.referrer') . ') LIKE ' . $this->_db->q('%' . $searchTerm . '%')
				. ' or '
				. 'LOWER(' . $this->_db->qn('u1.ip_address') . ') LIKE ' . $this->_db->q('%' . $searchTerm . '%')
				. ' or '
				. 'LOWER(' . $this->_db->qn('u1.user_agent') . ') LIKE ' . $this->_db->q('%' . $searchTerm . '%')
				. ')';
		}

		// hit_type check
		if (!empty($filters->filter_hit_type))
		{
			switch ($filters->filter_hit_type)
			{
				case Sh404sefHelperUrl::IS_EXTERNAL:
					$where[] = '(u1.type = ' . $this->_db->q($filters->filter_hit_type)
						. ' or u1.type = 0)';
					break;
				case Sh404sefHelperUrl::IS_INTERNAL:
					$where[] = 'u1.type = ' . $this->_db->q($filters->filter_hit_type);
					break;
			}
		}

		if (!empty($filters->filter_hide_empty))
		{
			$where[] = "u1.referrer <> ''";
		}

		// aggregate clauses
		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	protected function _buildListGroupBy($options)
	{

		$filters = $this->getDisplayOptions();

		// build query fragment
		$groupBy = '';
		switch ($filters->filter_collapse)
		{
			case Sh404sefHelperGeneral::COLLAPSE_BY_REFERRER:
				$groupBy = 'group by ' . $this->_db->qn('u1.referrer');
				break;
			case Sh404sefHelperGeneral::COLLAPSE_BY_IP:
				$groupBy = 'group by ' . $this->_db->qn('u1.ip_address');
				break;
			case Sh404sefHelperGeneral::COLLAPSE_BY_USER_AGENT:
				$groupBy = 'group by ' . $this->_db->qn('u1.user_agent');
				break;
		}

		return $groupBy;
	}

	protected function _buildListOrderBy($options)
	{

		// get set of filters applied to the current view
		$filters = $this->getDisplayOptions();

		// build query fragment
		if (!empty($filters->filter_order))
		{
			// a little bit of custom logic when switching from one filter to another
			if ($filters->filter_collapse == Sh404sefHelperGeneral::COLLAPSE_BY_REFERRER)
			{
				// can order by referrer and hits, not by date
				if ($filters->filter_order == 'datetime')
				{
					$this->_setState('filter_order', 'referrer');
					$this->_setState('filter_order_Dir', 'ASC');
					$filters = $this->getDisplayOptions();
				}
			}
			else
			{
				// can order by referrer and date, not by hits
				if ($filters->filter_order == 'hits')
				{
					$this->_setState('filter_order', 'referrer');
					$this->_setState('filter_order_Dir', 'ASC');
					$filters = $this->getDisplayOptions();
				}
			}
			// execute sorting
			$orderBy = ' order by ' . $this->_db->qn($filters->filter_order);
			$orderBy .= ' ' . $filters->filter_order_Dir;
		}

		return $orderBy;
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
			// internal vs external
			array('name' => 'filter_hit_type', 'html_name' => 'filter_hit_type', 'default' => 0, 'type' => 'int'),
			//URL id
			array('name' => 'url_id', 'html_name' => 'url_id', 'default' => 0, 'type' => 'int'),
			//URL id
			array('name' => 'request_type', 'html_name' => 'request_type', 'default' => '', 'type' => 'string'),

			array('name' => 'filter_order', 'html_name' => 'filter_order', 'default' => 'referrer', 'type' => 'string'),

			array('name' => 'filter_collapse', 'html_name' => 'filter_collapse', 'default' => Sh404sefHelperGeneral::COLLAPSE_NONE, 'type' => 'int'),
			array('name' => 'filter_hide_empty', 'html_name' => 'filter_hide_empty', 'default' => Sh404sefHelperGeneral::SHOW_ALL_REFERRERS, 'type' => 'int')
		);

		return array_merge($contextData, $addedContextData);
	}

}
