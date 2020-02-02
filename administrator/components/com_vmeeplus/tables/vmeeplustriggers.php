<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class TableVmeePlusTriggers extends JTable
{
	/** @var int */
	var $id				= null;
	/** @var string */
	var $trigger 			= '';
	/** @var string */
	var $display_name		= '';

	function __construct( &$_db )
	{
		parent::__construct( '#__vmee_plus_trigger_types', 'id', $_db );
	}
	
	function check()
	{
		return true;
	}
}
?>
