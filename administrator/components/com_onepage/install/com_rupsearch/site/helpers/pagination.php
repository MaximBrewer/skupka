<?php
defined('_JEXEC') or die;


require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 
RupHelper::getIncludes(); 
/*
if (!class_exists('VmPagination'))
{
	
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmpagination.php'); 
}
*/


class rupPagination {

	private $_perRow = 5;
	public $limitstart = null;
	public $limit = null;
	public $total = null;
	public $prefix = null;
	protected $_viewall = false;
	protected $_additionalUrlParams = array();
	function __construct($total, $limitstart, $limit, $perRow=5){
		if($perRow!==0){
			$this->_perRow = $perRow;
		}
		//parent::__construct($total, $limitstart, $limit);
	}

	

	function setSequence($sequence){
		$this->_sequence = $sequence;
	}

	function getLimitBox($sequence=0)
	{
		return ''; 
	}
	
	public function vmOrderUpIcon ($i, $ordering = true, $task = 'orderup', $alt = 'JLIB_HTML_MOVE_UP', $enabled = true, $checkbox = 'cb') {
		return '';
		
	}
	public function vmOrderDownIcon ($i, $ordering, $n, $condition = true, $task = 'orderdown', $alt = 'JLIB_HTML_MOVE_DOWN', $enabled = true, $checkbox = 'cb') {
		return '';
	}
	public function getData()
	{
		return array();
	}
	function getPagesLinks()
	{
		return ''; 
	}
	function getPagesCounter()
	{
		return ''; 
	}
	public function getListFooter()
	{
		return ''; 
	}
	function getResultsCounter()
	{
		return ''; 
	}
	public function getRowOffset($index)
	{
		return $index + 1 + $this->limitstart;
	}


}
