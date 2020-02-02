<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class TableVmeePlusTemplates extends JTable
{
	/** @var int */
	var $id				= null;
	/** @var string */
	var $trigger_id			= '';
	/** @var string */
	var $name				= '';
	/** @var string */
	var $subject			= '';
	/** @var string */
	var $body				= '';
	/** @var int */
//	var $type				= '';
	/** @var string */
//	var $CC			= '';
	/** @var string */
//	var $BCC			= '';
	/** @var int */
	var $isDefault		= 0;
	/** @var int */
//	var $enabled		= 0;

	function __construct( &$_db )
	{
		parent::__construct( '#__vmee_plus_templates', 'id', $_db );
	}
	

	function check()
	{
		return true;
	}
}
?>
