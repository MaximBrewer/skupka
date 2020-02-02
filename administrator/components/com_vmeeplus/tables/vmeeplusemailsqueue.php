<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class TableVmeePlusEmailsQueue extends JTable
{
	/** @var int */
	var $id			= null;
	/** @var string */
	var $to = null;
	/** @var string */
	var $cc	= null;
	/** @var string */
	var $bcc	= null;
	/** @var string */
	var $subject	= null;
	/** @var string */
	var $body	= null;
	/** @var string */
	var $embedded_images	= null;
	/** @var string */
	var $from_name	= null;
	/** @var string */
	var $from_email	= null;
	/** @var int */
	var $date = null;
	/** @var int */
	var $priority = null;
	
	function __construct( &$_db )
	{
		parent::__construct( '#__vmee_plus_emails_queue', 'id', $_db );
	}
}
?>
