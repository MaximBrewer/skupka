<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
if (!defined( 'DS' )) {
	define( 'DS', DIRECTORY_SEPARATOR );
}
class com_ttfspInstallerScript
{
	public function __construct($installer)
	{
		$this->installer = $installer;
	}
	public function preflight($action, $adapter)
	{
		return true;
	}

	public function update()
	{
		return true;
	}

	public function install($adapter)
	{
		return true;
	}

	public function postflight($action, $adapter)
	{
	define('__ROOTINSTALL__', dirname(__FILE__));
	require_once(__ROOTINSTALL__.'/install.ttfsp.php');
	com_install();	
		return true;
	}

	public function uninstall($adapter)
	{
require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."uninstall.ttfsp.php" );
com_uninstall();
		return true;
	}

}
