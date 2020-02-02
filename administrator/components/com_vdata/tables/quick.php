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


class TableQuick extends JTable
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
   
    function TableQuick( &$db ) {
		$db_base = JFactory::getDbo();
		$tablename = JFactory::getApplication()->input->get('table_name','', 'RAW');
		
		$column_name = JFactory::getApplication()->input->get('column_name','', 'RAW');
		
        parent::__construct($tablename, $column_name, $db);
    }
	
}
