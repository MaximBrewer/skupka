<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

class Sh404sefHelperSecurity
{

	private static $_counters = array();

	public static function updateSecStats()
	{
		$shNum = 12 * (intval(date('Y')) - 2000) + intval(date('m'));
		$shFileName = JPATH_ROOT . '/logs/sh404sef/sec/log_' . date('Y') . '-' . date('m') . '-sh404SEF_security_log.' . $shNum . '.log.php';
		$fileIsThere = file_exists($shFileName) && is_readable($shFileName);
		self::_shResetSecStats();
		if ($fileIsThere)
		{
			self::_shReadSecStatsFromFile($shFileName);
		}
		self::$_counters['shSecCurMonth'] = date('M') . '-' . date('Y');
		self::$_counters['shSecLastUpdated'] = time();
		// get a configuration model object
		$model = ShlMvcModel_Base::getInstance('configuration', 'Sh404sefModel');
		if (is_callable(array($model, 'save')))
		{
			$model->setValues(self::$_counters);
			// update in memory config object
			$sefConfig = &Sh404sefFactory::getConfig(true);
		}
	}

	private static function _shResetSecStats()
	{
		self::$_counters['shSecCurMonth'] = '';
		self::$_counters['shSecLastUpdated'] = '';
		self::$_counters['shSecTotalAttacks'] = 0;
		self::$_counters['shSecTotalConfigVars'] = 0;
		self::$_counters['shSecTotalBase64'] = 0;
		self::$_counters['shSecTotalScripts'] = 0;
		self::$_counters['shSecTotalStandardVars'] = 0;
		self::$_counters['shSecTotalImgTxtCmd'] = 0;
		self::$_counters['shSecTotalIPDenied'] = 0;
		self::$_counters['shSecTotalUserAgentDenied'] = 0;
		self::$_counters['shSecTotalFlooding'] = 0;
		self::$_counters['shSecTotalPHP'] = 0;
		self::$_counters['shSecTotalPHPUserClicked'] = 0;
	}

	private static function _shReadSecStatsFromFile($shFileName)
	{
		$logFile = fopen($shFileName, 'r');
		if ($logFile)
		{
			while (!feof($logFile))
			{
				$line = fgets($logFile, 4096);
				self::_shDecodeSecLogLine($line);
			}
			fClose($logFile);
		}
	}

	private static function _shDecodeSecLogLine($line)
	{
		$sefConfig = &Sh404sefFactory::getConfig();

		// skip comments
		if (substr($line, 0, 1) == '#')
		{
			return;
		}
		if (preg_match('/[0-9]{2}\-[0-9]{2}\-[0-9]{2}/', $line))
		{ // this is not header or comment line
			self::$_counters['shSecTotalAttacks']++;
			$bits = explode("\t", $line);
			switch (substr($bits[2], 0, 15))
			{
				case 'Flooding':
					self::$_counters['shSecTotalFlooding']++;
					break;
				case 'Caught by Honey':
					self::$_counters['shSecTotalPHP']++;
					break;
				case 'Honey Pot but u':
					self::$_counters['shSecTotalPHPUserClicked']++;
					break;
				case 'Var not numeric':
				case 'Var not alpha-n':
				case 'Var contains ou':
					self::$_counters['shSecTotalStandardVars']++;
					break;
				case 'Image file name':
					self::$_counters['shSecTotalImgTxtCmd']++;
					break;
				case '<script> tag in':
					self::$_counters['shSecTotalScripts']++;
					break;
				case 'Base 64 encoded':
					self::$_counters['shSecTotalBase64']++;
					break;
				case 'mosConfig_var i':
					self::$_counters['shSecTotalConfigVars']++;
					break;
				case 'Blacklisted IP':
					self::$_counters['shSecTotalIPDenied']++;
					break;
				case 'Blacklisted use':
					self::$_counters['shSecTotalUserAgentDenied']++;
					break;
				default: // if not one of those, then it's a 404, don't count it as an attack
					self::$_counters['shSecTotalAttacks']--;
					break;

			}
		}
	}

}
