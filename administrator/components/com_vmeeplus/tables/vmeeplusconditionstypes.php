<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class TableVmeePlusConditionsTypes extends JTable
{
	/** @var int */
	var $id	= null;
	/** @var string */
	var $name	= '';
	/** @var string */
	var $display_name	= '';
	/** @var string */
	var $class	= '';
	/** @var string */
	var $field		= '';
	
	function __construct( &$_db )
	{
		parent::__construct( '#__vmee_plus_cond_types', 'id', $_db );
	}
}
?>
