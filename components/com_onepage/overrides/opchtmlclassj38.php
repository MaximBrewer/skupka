<?php
/**
 * Overrided portion of JDocumentRaw class for OPC2 on Virtuemart 3 and Joomla 3.8
 *
 * This class was overrided to support addCustomScript and other non raw header insertion from raw view in ajax
 * Later update will include synchronization of the added scripts and css with already generated header data
 *
 * @package One Page Checkout for VirtueMart 3
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
*/

 // Check to ensure this file is included in Joomla!
namespace Joomla\CMS\Document;

defined('JPATH_PLATFORM') or die('Restricted access');

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

jimport('joomla.utilities.utility');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'opchtmlclassj38.php'); 
class JDocumentOpchtmlclass extends HtmlDocument
{
	
	public $_links = array();

	public $_custom = array();

	
	public $template = null;

	public $baseurl = null;

	public $params = null;

	public $_file = null;

	protected $_template = '';

	protected $_template_tags = array();

	protected $_caching = null;

	private $_html5 = null;

	public function __construct($options = array())
	{
		parent::__construct($options);
		$this->_type = 'raw';
	}
	
	

	
	public function getHeadData()
	{
		$data = array();
		$data['title']       = $this->title;
		$data['description'] = $this->description;
		$data['link']        = $this->link;
		$data['metaTags']    = $this->_metaTags;
		$data['links']       = $this->_links;
		$data['styleSheets'] = $this->_styleSheets;
		$data['style']       = $this->_style;
		$data['scripts']     = $this->_scripts;
		$data['script']      = $this->_script;
		$data['custom']      = $this->_custom;
		$data['scriptText']  = \JText::script();

		return $data;
	}

	public function resetHeadData($types = null)
	{
		if (is_null($types))
		{
			$this->title        = '';
			$this->description  = '';
			$this->link         = '';
			$this->_metaTags    = array();
			$this->_links       = array();
			$this->_styleSheets = array();
			$this->_style       = array();
			$this->_scripts     = array();
			$this->_script      = array();
			$this->_custom      = array();
		}

		if (is_array($types))
		{
			foreach ($types as $type)
			{
				$this->resetHeadDatum($type);
			}
		}

		if (is_string($types))
		{
			$this->resetHeadDatum($types);
		}

		return $this;
	}

	
	private function resetHeadDatum($type)
	{
		switch ($type)
		{
			case 'title':
			case 'description':
			case 'link':
				$this->{$type} = '';
				break;

			case 'metaTags':
			case 'links':
			case 'styleSheets':
			case 'style':
			case 'scripts':
			case 'script':
			case 'custom':
				$realType = '_' . $type;
				$this->{$realType} = array();
				break;
		}
	}

	public function setHeadData($data)
	{
		
		return;

	}

	public function mergeHeadData($data)
	{
		
		return;
	
	}

	public function addHeadLink($href, $relation, $relType = 'rel', $attribs = array())
	{
		return $this;
	}

	public function addFavicon($href, $type = 'image/vnd.microsoft.icon', $relation = 'shortcut icon')
	{
		return $this;
	}

	public function addCustomTag($html)
	{
		return $this;
	}

	public function isHtml5()
	{
		return $this->_html5;
	}

	public function setHtml5($state)
	{
		
	}

	public function getBuffer($type = null, $name = null, $attribs = array())
	{
		// If no type is specified, return the whole buffer
		if ($type === null)
		{
			return parent::$_buffer;
		}

		$title = (isset($attribs['title'])) ? $attribs['title'] : null;

		if (isset(parent::$_buffer[$type][$name][$title]))
		{
			return parent::$_buffer[$type][$name][$title];
		}

		$renderer = $this->loadRenderer($type);
		$this->setBuffer($renderer->render($name, $attribs, null), $type, $name, $title);

		return parent::$_buffer[$type][$name][$title];
	}

	public function setBuffer($content, $options = array())
	{
		// The following code is just for backward compatibility.
		if (func_num_args() > 1 && !is_array($options))
		{
			$args = func_get_args();
			$options = array();
			$options['type'] = $args[1];
			$options['name'] = (isset($args[2])) ? $args[2] : null;
			$options['title'] = (isset($args[3])) ? $args[3] : null;
		}

		parent::$_buffer[$options['type']][$options['name']][$options['title']] = $content;

		return $this;
	}

	public function parse($params = array())
	{
		return $this->_fetchTemplate($params)->_parseTemplate();
	}

	public function render($caching = false, $params = array())
	{
		$this->_caching = $caching;

		if (empty($this->_template))
		{
			$this->parse($params);
		}

		$data = $this->_renderTemplate();
		parent::render();

		return $data;
	}

	public function countModules($condition)
	{
		return 0; 
	}

	public function countMenuChildren()
	{
		static $children;

		if (!isset($children))
		{
			$db = \JFactory::getDbo();
			$app = \JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$children = 0;

			if ($active)
			{
				$query = $db->getQuery(true)
					->select('COUNT(*)')
					->from('#__menu')
					->where('parent_id = ' . $active->id)
					->where('published = 1');
				$db->setQuery($query);
				$children = $db->loadResult();
			}
		}

		return $children;
	}

	protected function _loadTemplate($directory, $filename)
	{
		$contents = '';

		// Check to see if we have a valid template file
		if (file_exists($directory . '/' . $filename))
		{
			// Store the file path
			$this->_file = $directory . '/' . $filename;

			// Get the file content
			ob_start();
			require $directory . '/' . $filename;
			$contents = ob_get_contents();
			ob_end_clean();
		}

		// Try to find a favicon by checking the template and root folder
		$icon = '/favicon.ico';

		foreach (array($directory, JPATH_BASE) as $dir)
		{
			if (file_exists($dir . $icon))
			{
				$path = str_replace(JPATH_BASE, '', $dir);
				$path = str_replace('\\', '/', $path);
				$this->addFavicon(Uri::base(true) . $path . $icon);
				break;
			}
		}

		return $contents;
	}

	
	protected function _fetchTemplate($params = array())
	{
		// Check
		$directory = isset($params['directory']) ? $params['directory'] : 'templates';
		$filter = \JFilterInput::getInstance();
		$template = $filter->clean($params['template'], 'cmd');
		$file = $filter->clean($params['file'], 'cmd');

		if (!file_exists($directory . '/' . $template . '/' . $file))
		{
			$template = 'system';
		}

		if (!file_exists($directory . '/' . $template . '/' . $file))
		{
			$file = 'index.php';
		}

		// Load the language file for the template
		$lang = \JFactory::getLanguage();

		// 1.5 or core then 1.6
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
			|| $lang->load('tpl_' . $template, $directory . '/' . $template, null, false, true);

		// Assign the variables
		$this->template = $template;
		$this->baseurl = Uri::base(true);
		$this->params = isset($params['params']) ? $params['params'] : new Registry;

		// Load
		$this->_template = $this->_loadTemplate($directory . '/' . $template, $file);

		return $this;
	}

	
	protected function _parseTemplate()
	{
		$matches = array();

		if (preg_match_all('#<jdoc:include\ type="([^"]+)"(.*)\/>#iU', $this->_template, $matches))
		{
			$template_tags_first = array();
			$template_tags_last = array();

			// Step through the jdocs in reverse order.
			for ($i = count($matches[0]) - 1; $i >= 0; $i--)
			{
				$type = $matches[1][$i];
				$attribs = empty($matches[2][$i]) ? array() : \JUtility::parseAttributes($matches[2][$i]);
				$name = isset($attribs['name']) ? $attribs['name'] : null;

				// Separate buffers to be executed first and last
				if ($type == 'module' || $type == 'modules')
				{
					$template_tags_first[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
				}
				else
				{
					$template_tags_last[$matches[0][$i]] = array('type' => $type, 'name' => $name, 'attribs' => $attribs);
				}
			}
			// Reverse the last array so the jdocs are in forward order.
			$template_tags_last = array_reverse($template_tags_last);

			$this->_template_tags = $template_tags_first + $template_tags_last;
		}

		return $this;
	}

	
	protected function _renderTemplate()
	{
		$replace = array();
		$with = array();

		foreach ($this->_template_tags as $jdoc => $args)
		{
			$replace[] = $jdoc;
			$with[] = $this->getBuffer($args['type'], $args['name'], $args['attribs']);
		}

		return str_replace($replace, $with, $this->_template);
	}

}