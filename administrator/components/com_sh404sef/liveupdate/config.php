<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'com_sh404sef';
	var $_extensionTitle		= 'sh404SEF';
	var $_updateURL				= 'https://u1.weeblr.com/public/direct/sh404sef/update/com_sh404sef_full.ini';
	var $_requiresAuthorization	= false; // we use installer plugin for that
	var $_versionStrategy		= 'vcompare';
	var $_storageAdapter		= 'file';
	var $_storageConfig			= array(
		'extensionName'	=> 'com_sh404sef',
		'key'			=> 'liveupdate'
	);

}
