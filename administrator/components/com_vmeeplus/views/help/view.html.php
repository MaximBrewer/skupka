<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/


defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class vmeeProViewHelp extends Jview
{
	
	function __construct($config = array()){
		parent::__construct();
	}
	
	function display($tpl = null){
		$this->setToolbar($tpl);

		parent::display($tpl);
	}
	
	private function setToolbar($tpl){
		JRequest::setVar( 'hidemainmenu', 1 );
    	JToolBarHelper::title( VMEE_PRO_TITLE.'<span style="margin-right:5px; margin-left:5px;"> | </span><span style="font-weight: normal;">Help and Support </span>', 'interamind_logo' );
		$doc = JFactory::getDocument();
		$root = JUri::root().'/administrator/templates/khepri';
		$style = '.icon-32-icon-32-refresh 	{ background-image: url(' . $root . '/images/toolbar/icon-32-refresh.png); }';
		$doc->addStyleDeclaration( $style );
		JToolBarHelper::preferences( 'com_vmeeplus',650 );
		JToolBarHelper::back();
		
    }
}
?>

