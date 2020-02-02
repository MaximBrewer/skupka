<?php
/**
 * Load Module support file based on Joomla plugin Content.loadmodule
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
 * Original license
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.loadmodule
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
class OPCloadmodule
{
	 static $modules = array();
	 static $mods = array();
	/**
	 * Plugin that loads module positions within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public static function onContentPrepare($context, &$content)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}

		// simple performance check to determine whether bot should process further
		if (strpos($content, 'loadposition') === false && strpos($content, 'loadmodule') === false) {
		
			return true;
		}

		// expression to search for (positions)
		$regex		= '/{loadposition\s+(.*?)}/i';
		$style		= 'none';
		// expression to search for(modules)
		$regexmod	= '/{loadmodule\s+(.*?)}/i';
		$title		= null;
		$stylemod	= 'none';

		// Find all instances of plugin and put in $matches for loadposition
		// $matches[0] is full pattern match, $matches[1] is the position
		preg_match_all($regex, $content, $matches, PREG_SET_ORDER);
		// No matches, skip this
		if ($matches) {
			foreach ($matches as $match) {

			$matcheslist = explode(',', $match[1]);

			// We may not have a module style so fall back to the plugin default.
			if (!array_key_exists(1, $matcheslist)) {
				$matcheslist[1] = $style;
			}

			$position = trim($matcheslist[0]);
			$style    = trim($matcheslist[1]);

				$output = OPCloadmodule::_load($position, $style);
				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$content = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $content, 1);
			}
		}
		// Find all instances of plugin and put in $matchesmod for loadmodule

		preg_match_all($regexmod, $content, $matchesmod, PREG_SET_ORDER);
		// If no matches, skip this
		if ($matchesmod){
			foreach ($matchesmod as $matchmod) {

				$matchesmodlist = explode(',', $matchmod[1]);
				//We may not have a specific module so set to null
				if (!array_key_exists(1, $matchesmodlist)) {
					$matchesmodlist[1] = null;
				}
				// We may not have a module style so fall back to the plugin default.
				if (!array_key_exists(2, $matchesmodlist)) {
					$matchesmodlist[2] = $stylemod;
				}

				$module = trim($matchesmodlist[0]);
				$name   = htmlspecialchars_decode(trim($matchesmodlist[1]));
				$style  = trim($matchesmodlist[2]);
				// $match[0] is full pattern match, $match[1] is the module,$match[2] is the title
				$output = OPCloadmodule::_loadmod($module, $name, $style);
				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$content = preg_replace("|$matchmod[0]|", addcslashes($output, '\\$'), $content, 1);
			}
		}
	}

	protected function _load($position, $style = 'none')
	{
		if (!isset(self::$modules[$position])) {
			self::$modules[$position] = '';
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$modules	= JModuleHelper::getModules($position);
			$params		= array('style' => $style);
			ob_start();

			foreach ($modules as $module) {
				echo $renderer->render($module, $params);
			}

			self::$modules[$position] = ob_get_clean();
		}
		return self::$modules[$position];
	}
	// This is always going to get the first instance of the module type unless
	// there is a title.
	public static function _loadmod($module, $title, $style = 'none')
	{
		if (!isset(self::$mods[$module])) {
			self::$mods[$module] = '';
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$mod		= JModuleHelper::getModule($module, $title);
			// If the module without the mod_ isn't found, try it with mod_.
			// This allows people to enter it either way in the content
			if (!isset($mod)){
				$name = 'mod_'.$module;
				$mod  = JModuleHelper::getModule($name, $title);
			}
			$params = array('style' => $style);
			ob_start();

			echo $renderer->render($mod, $params);

			self::$mods[$module] = ob_get_clean();
		}
		return self::$mods[$module];
	}
}
