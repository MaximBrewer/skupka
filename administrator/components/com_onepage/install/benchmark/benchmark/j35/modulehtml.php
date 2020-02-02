<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
//libraries/joomla/document/renderer/html/module.php
defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;

/**
 * JDocument Module renderer
 *
 * @since  3.5
 */
class JDocumentRendererHtmlModule extends JDocumentRenderer
{
	/**
	 * Renders a module script and returns the results as a string
	 *
	 * @param   string  $module   The name of the module to render
	 * @param   array   $attribs  Associative array of values
	 * @param   string  $content  If present, module information from the buffer will be used
	 *
	 * @return  string  The output of the script
	 *
	 * @since   3.5
	 */
	public function render($module, $attribs = array(), $content = null)
	{
		
		JDocumentRendererHtmlModule::startB(); 
		
		if (!is_object($module))
		{
			$title = isset($attribs['title']) ? $attribs['title'] : null;

			$module = JModuleHelper::getModule($module, $title);

			if (!is_object($module))
			{
				if (is_null($content))
				{
					if (is_string($module)) $n = $module; 
					else $n = 'Unknown module'; 
					
					JDocumentRendererHtmlModule::endB($n); 
					
					return '';
				}

				/**
				 * If module isn't found in the database but data has been pushed in the buffer
				 * we want to render it
				 */
				$tmp = $module;
				$module = new stdClass;
				$module->params = null;
				$module->module = $tmp;
				$module->id = 0;
				$module->user = 0;
			}
		}

		// Set the module content
		if (!is_null($content))
		{
			$module->content = $content;
		}

		// Get module parameters
		$params = new Registry($module->params);

		// Use parameters from template
		if (isset($attribs['params']))
		{
			$template_params = new Registry(html_entity_decode($attribs['params'], ENT_COMPAT, 'UTF-8'));
			$params->merge($template_params);
			$module = clone $module;
			$module->params = (string) $params;
		}

		// Default for compatibility purposes. Set cachemode parameter or use JModuleHelper::moduleCache from within the module instead
		$cachemode = $params->get('cachemode', 'oldstatic');

		if ($params->get('cache', 0) == 1 && JFactory::getConfig()->get('caching') >= 1 && $cachemode != 'id' && $cachemode != 'safeuri')
		{
			// Default to itemid creating method and workarounds on
			$cacheparams = new stdClass;
			$cacheparams->cachemode = $cachemode;
			$cacheparams->class = 'JModuleHelper';
			$cacheparams->method = 'renderModule';
			$cacheparams->methodparams = array($module, $attribs);
			
			$ret = JModuleHelper::ModuleCache($module, $params, $cacheparams);
			 $name = $module->module;
		     JDocumentRendererHtmlModule::endB($name); 
			return $ret; 
		}

		$ret = JModuleHelper::renderModule($module, $attribs);
		 $name = $module->module;
		 JDocumentRendererHtmlModule::endB($name); 
		return $ret; 
		
	}
	
	private static $bench; 
	public static function startB() {
	 
	  JDocumentRendererHtmlModule::$bench = array(); 
	  JDocumentRendererHtmlModule::$bench['start'] = microtime(true); 
	}
	public static function endB($name) {
	    global $bench_arr; 
	    JDocumentRendererHtmlModule::$bench['name'] = $name; 
		JDocumentRendererHtmlModule::$bench['end'] = microtime(true); 
		JDocumentRendererHtmlModule::$bench['duration'] = JDocumentRendererHtmlModule::$bench['end'] - JDocumentRendererHtmlModule::$bench['start']; 
		JDocumentRendererHtmlModule::$bench['all'] = array(); 
		/*
		if (isset($bench_arr['modules'][$name])) {
		  $bench['duration'] += $bench_arr['modules'][$name]['duration']; 
		  $bench['all'] = $bench_arr['modules'][$name]['all']; 
		  
		}
		*/
		
		
		if (isset($GLOBALS['bench_arr']['modules'][$name]))
			{
				
				
				$benchOld = $GLOBALS['bench_arr']['modules'][$name]['all']; 
				
				foreach ($benchOld as $k=>$b)
				{
					
					//$bench['duration'] += (float)$b['duration']; 
					JDocumentRendererHtmlModule::$bench['all'][] = $b; 
				}
			}
			
			$bench2 = JDocumentRendererHtmlModule::$bench; 
			unset($bench2['all']); 
			JDocumentRendererHtmlModule::$bench['all'][] = $bench2; 
		
		
		$bench_arr['modules'][$name] = JDocumentRendererHtmlModule::$bench; 
	}

	
}
