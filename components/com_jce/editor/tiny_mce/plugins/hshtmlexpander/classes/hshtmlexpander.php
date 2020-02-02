<?php
/**
 * @version		$Id: hshtmlexpander.php 58 2011-02-18 12:40:41Z happy_noodle_boy $
 * @package      JCE
 * @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
 * @author		Ryan Demmer
 * @license      GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
// no direct access
defined('_JEXEC') or die('RESTRICTED');
// Set flag that this is an extension parent
DEFINE('_WF_EXT', 1);

// Load class dependencies
wfimport('editor.libraries.classes.plugin');
wfimport('editor.libraries.classes.extensions.popups');

class WFHsHtmlExpanderPlugin extends WFEditorPlugin
{
	/*
	 *  @var varchar
	 */
	var $extensions = array();

	var $popups 	= array();

	var $tabs 		= array();

	/**
	 * Constructor activating the default information of the class
	 *
	 * @access	protected
	 */
	function __construct()
	{
		parent::__construct();
	}
	/**
	 * Returns a reference to a plugin object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $hsHtmlExpander = &HsHtmlExpander::getInstance();</pre>
	 *
	 * @access	public
	 * @return	JCE  The editor object.
	 * @since	1.5
	 */
	static public function &getInstance()
	{
		static $instance;

		if (!is_object($instance)) {
			$instance = new WFHsHtmlExpanderPlugin();
		}
		return $instance;
	}

	public function display()
	{
		parent::display();

		$document = WFDocument::getInstance();

		$document->addScript(array('hshtmlexpander'), 'plugins');
		$document->addStyleSheet(array('hshtmlexpander'), 'plugins');

		$settings = $this->getSettings();

		$document->addScriptDeclaration('HsHtmlExpanderDialog.settings='.json_encode($settings).';');

		$tabs = WFTabs::getInstance(array(
			'base_path' => WF_EDITOR_PLUGIN
		));

		// Add tabs
		$tabs->addTab('expander', 1);
		$tabs->addTab('options',  1);
		$tabs->addTab('html',  1);
		$tabs->addTab('flash',  1);
		$tabs->addTab('caption',  1);
		$tabs->addTab('heading',  1);
		$tabs->addTab('overlay',  1);

		// Load Popups instance
		$popups = WFPopupsExtension::getInstance(array(
      		'text' => false
		));

		$popups->display();
	}

	function getSettings() {
		$profile = $this->getProfile();

		$settings = array(
		    'file_browser' => $this->getParam('file_browser', 1) && in_array('browser', explode(',', $profile->plugins)),
		    'attributes' => array(
		        'target' => $this->getParam('attributes_target', 1),
		        'anchor' => $this->getParam('attributes_anchor', 1)
		    )
		);

		return parent::getSettings($settings);
	}

	public function getDefaults($defaults = array())
	{
		$defaults = array(
			'targetlist' => 'default'
			);
			return parent::getDefaults($defaults);
	}
}
