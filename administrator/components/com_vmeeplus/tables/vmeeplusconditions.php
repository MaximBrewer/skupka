<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/


class TableVmeePlusConditions extends JTable
{
	/** @var int */
	var $id			= null;
	/** @var int */
	var $rule_id	= null;
	/** @var string */
	var $cond_type	= null;
	/** @var string */
	var $operator	= '';
	/** @var string */
	var $value		= '';
	/** @var string */
	var $text_value	= '';
	
	function __construct( &$_db )
	{
		parent::__construct( '#__vmee_plus_conditions', 'id', $_db );
	}
}
?>
