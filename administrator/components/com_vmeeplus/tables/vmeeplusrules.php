<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class TableVmeePlusRules extends JTable
{
	/** @var int */
	var $id				= null;
	/** @var string */
	var $name			= '';
	/** @var int */
	var $trigger_id		= null;
	/** @var int */
	var $template_id	= null;
	/** @var string */
	var $toList			= '';
	/** @var string */
	var $ccList			= '';
	/** @var string */
	var $bccList		= '';
	/** @var int */
	var $isEmailToAdmins = 0;
	/** @var int */
	var $isEmailToStoreAdmins = 0;
	/** @var string */
	var $parameters		= '';
	/** @var int */
	var $enabled		= 1;
	/** @var string */
	var $from		= '';
	/** @var string */
	var $fromName	= '';
	
	/** @var string */
	var $attachments	= '';
	
	function __construct( &$_db )
	{
		parent::__construct( '#__vmee_plus_rules', 'id', $_db );
	}
}
?>
