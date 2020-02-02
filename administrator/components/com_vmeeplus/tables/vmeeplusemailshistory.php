<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

class TableVmeePlusEmailsHistory extends JTable
{
	/** @var int */
	var $id			= null;
	/** @var string */
	var $unique_id = null;
	/** @var string */
	var $type	= null;
	/** @var int */
	var $rule_id	= null;
	/** @var int */
	var $date	= null;
	/** @var int */
	var $order_id	= null;
	/** @var int */
	var $user_id	= null;
	/** @var string */
	var $open	= 'no';
	/** @var string */
	var $click_through	= 'no';
	/** @var double */
	var $generated_income = null;
	/** @var int */
	var $template_id = null;
	/** @var string */
	var $status	= '';
	/** @var string */
	var $email	= '';
	
	function __construct( &$_db )
	{
		parent::__construct( '#__vmee_plus_emails_history', 'id', $_db );
	}
}
?>
