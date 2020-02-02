<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
 // No direct access
defined('_JEXEC') or die('Restricted access');


class TableDisplay extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;
 
    /**
     * @var string
     */
	var $title = null;
	var $profileid  = null;
	var $qry = null;
	var $uniquekey = null;
	var $uniquealias = null;
	var $uidprofile = null;
	var $likesearch = null;
	var $morefilterkey = null;
	var $keyword= null;
	var $access = null;
	var $state = null;
	var $norowitem = null;
	var $listtmplid = null;
	var $itemlisttmpl = null;
	var $detailtmplid = null;
	var $itemdetailtmpl = null;
	var $fieldtype = null;
	
	
    function TableDisplay( &$db ) {
        parent::__construct('#__vd_display', 'id', $db);
    }
	
	function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);	
	}
	
	function check()
	{
		$this->id = intval($this->id);
		if(empty($this->title))	{
			$this->setError( JText::_('PLZ_ENTER_TITLE') );
			return false;
		}
		
		
		return parent::check();
		
	}
	
	function store($updateNulls = false)
	{	
				
		if(!parent::store($updateNulls))	{
			return false;
		}
		return true;
	}
	
	function delete($oid=null)
	{
				
		if(!parent::delete($oid))	{
			return false;
		}
		return true;
	}
	
}
