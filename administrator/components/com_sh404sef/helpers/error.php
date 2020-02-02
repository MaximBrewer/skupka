<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

class Sh404sefHelperError
{

	/**
	 * Render an html document in response to a request
	 * that generated a 404 error
	 *
	 * @param Exception $error     a 404 exception
	 * @param array     $queryVars an array of query vars representing the page to display
	 * @param string    $title     an optional page title
	 */
	public static function render404ErrorDocument(& $error, $queryVars = array(), $title = '')
	{
		$queryVars = empty($queryVars) ? self::get404PageQueryVars() : $queryVars;

		if (!empty($queryVars))
		{
			$app = JFactory:: getApplication();
			foreach ($queryVars as $key => $value)
			{
				$app->input->set($key, $value);
				// needed for extensions not reading $app->input
				// but still JRequest (some plugins, at onAfterDispatch)
				JRequest::setVar($key,$value);
			}

			// kill the document, avoid Joomla fatal error
			// if request has format query var != 'html'
			JFactory::$document = null;

			// no caching on this page
			JFactory::getConfig()->set('caching', 0);

			// clean any previous output
			@ob_end_clean();
			ob_start();

			// pretend routing happened normally
			$app->triggerEvent('onAfterRoute');

			// make sure we have a 404 header
			if (!headers_sent())
			{
				JResponse::setHeader('status', '404 NOT FOUND', true);
			}

			// set Itemid if any requested, and article display params
			if (!empty($queryVars['Itemid']))
			{
				self::setItemid($queryVars['Itemid']);
			}

			// render page
			self::render($error, empty($title) ? JText::_('COM_SH404SEF_PAGE_NOT_FOUND_TITLE') : $title);
		}
		else
		{
			self::safeEcho($error->getMessage());
		}
	}

	/**
	 * Figure out the query vars that would make
	 * Joomla! display a given article
	 *
	 * @return array
	 */
	private static function get404PageQueryVars()
	{
		// get config objects
		$pageInfo = Sh404sefFactory::getPageInfo();
		$config = Sh404sefFactory::getConfig();

		// store the status
		$pageInfo->httpStatus = 404;

		// display the error page
		$vars['option'] = 'com_sh404sef';
		$vars['view'] = 'error404';
		$vars['format'] = 'html';

		// build the subtemplate name
		$tmpl = str_replace('.php', '', $config->error404SubTemplate);
		if (!empty($tmpl))
		{
			$vars['tmpl'] = $tmpl;
		}

		$languageTag = JFactory::getLanguage()->getTag();
		if (empty($languageTag))
		{
			$languageTag = JFactory::getApplication()->input->getString(JApplication::getHash('language'), null, 'cookie');
		}

		if (!empty($languageTag))
		{
			$vars['lang'] = $languageTag;
		}
		$vars['lang'] = empty($vars['lang']) ? null : Sh404sefHelperLanguage::getUrlCodeFromTag($vars['lang']);

		// find an Itemid to use
		$vars['Itemid'] = self::getNotFoundItemid($languageTag);

		return $vars;
	}

	/**
	 * Finds the user set Itemid to use for an error page
	 * (per language). Defaults to home page (per language)
	 * if none set.
	 *
	 * @param string $languageTag
	 * @return mixed
	 */
	public static function getNotFoundItemid($languageTag = '*')
	{
		if (empty($languageTag) || $languageTag == '*')
		{
			// figure out default language
			$languageTag = Sh404sefHelperLanguage::getDefaultLanguageTag();
		}

		$config = Sh404sefFactory::getConfig();
		if (empty($config->pageNotFoundItemids) || empty($config->pageNotFoundItemids[$languageTag]))
		{
			$itemid = JFactory::getApplication()->getMenu()->getDefault($languageTag)->id;
		}
		else
		{
			$itemid = $config->pageNotFoundItemids[$languageTag];
		}

		return $itemid;
	}

	/**
	 * Set a given menu item as active
	 *
	 * @param int $Itemid
	 */
	public static function setItemid($Itemid)
	{
		$menus = JFactory::getApplication()->getMenu();
		$menuItem = $menus->getItem($Itemid);
		if (!empty($menuItem))
		{
			$menus->setActive($Itemid);
			// set options for proper display
			$params = array(
				'show_title',
				'show_vote',
				'show_tags',
				'show_publish_date',
				'show_print_icon',
				'show_email_icon',
				'show_parent_category',
				'show_page_heading',
				'show_noauth',
				'show_modify_date',
				'show_item_navigation',
				'show_icons',
				'show_hits',
				'show_create_date',
				'show_category',
				'show_author'
			);
			foreach ($params as $key)
			{
				$menuItem->params->set($key, 0);
			}
			$menuItem->params->set('show_intro', 1);

			// update global variables
			JFactory::getApplication()->getParams()->set('pageclass_sfx', $menuItem->params->get('pageclass_sfx'));
		}
	}

	/**
	 * Render a regular html document
	 *
	 * @param $error
	 * @param $title
	 */
	protected static function render($error, $title)
	{
		$app = JFactory:: getApplication();
		$document = $app->loadDocument()->getDocument();

		// Get the template
		$template = $app->getTemplate(true);

		// which menu item to use
		$menu = $app->getMenu();
		$item = $menu->getActive();
		if (!empty($item))
		{
			// force the template associated with the menu item selected for our error page
			$styleId = $item->template_style_id;

			// Load styles
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('id, home, template, s.params')
				->from('#__template_styles as s')
				->where('s.client_id = 0')
				->where('e.enabled = 1')
				->join('LEFT', '#__extensions as e ON e.element=s.template AND e.type=' . $db->quote('template') . ' AND e.client_id=s.client_id');

			$db->setQuery($query);
			$templates = $db->loadObjectList('id');
			if (!empty($templates[$styleId]))
			{
				$registry = new JRegistry;
				$registry->loadString($templates[$styleId]->params);
				$template = $templates[$styleId];
				$template->params = $registry;
			}
		}

		// Store the template and its params to the config
		$app->set('theme', $template->template);
		$app->set('themeParams', $template->params);

		// render component
		defined('JPATH_COMPONENT') or define('JPATH_COMPONENT', JPATH_ROOT . '/components/com_sh404sef');
		defined('JPATH_COMPONENT_SITE') or define('JPATH_COMPONENT_SITE', JPATH_ROOT . '/components/com_sh404sef');
		require_once JPATH_ROOT . '/components/com_sh404sef/controller.php';
		$controller = new Sh404sefController(array('base_path' => JPATH_ROOT . '/components/com_sh404sef'));
		$controller->addViewPath(JPATH_ROOT . '/components/com_sh404sef/views');
		$controller->execute($app->input->get('task'));
		$componentOutput = ob_get_clean();
		$document->setBuffer($componentOutput, 'component');
		$document->setTitle($title);
		$document->setDescription('');
		$document->addHeadLink('', 'canonical');
		$document->setMetaData('robots', 'noindex,follow');

		// Trigger the onAfterDispatch event.
		$app->triggerEvent('onAfterDispatch');

		// Setup the document rendering options.
		$docOptions['template'] = $template->template;
		$templateFile = $app->input->get('tmpl', 'index');
		$docOptions['file'] = $templateFile . '.php';
		$docOptions['params'] = $app->get('themeParams');

		if ($app->get('themes.base'))
		{
			$docOptions['directory'] = $app->get('themes.base');
		}
		// Fall back to constants.
		else
		{
			$docOptions['directory'] = defined('JPATH_THEMES') ? JPATH_THEMES : (defined('JPATH_BASE') ? JPATH_BASE : __DIR__) . '/themes';
		}

		// Fix base tag
		$document->setBase(htmlspecialchars(JUri::current()));

		// kill pathway
		$newPathWay = new stdClass();
		$newPathWay->name = JText::_('COM_SH404SEF_PAGE_NOT_FOUND_PATHWAY');
		$newPathWay->link = '';
		$app->getPathway()->setPathway(array($newPathWay));

		// Parse the document.
		$document->parse($docOptions);

		// Trigger the onBeforeRender event.
		$app->triggerEvent('onBeforeRender');

		$caching = false;

		// Render the document.
		$data = $document->render($caching, $docOptions);

		// Failsafe to get the error displayed.
		if (empty($data))
		{
			self::safeEcho($error->getMessage());
		}
		else
		{
			if (version_compare(JVERSION, '3', 'ge'))
			{
				$app->setBody($data);
			}
			else
			{
				JResponse::setBody($data);
			}

			// Trigger the onAfterRender event.
			$app->triggerEvent('onAfterRender');

			echo $app->toString();
		}
	}

	/**
	 * Displays a message to screen or whatever is available
	 * in a way that should work in most situations
	 *
	 * @param $message string to display
	 */
	public static function safeEcho($message)
	{
		if (isset($_SERVER['HTTP_HOST']))
		{
			// Output as html
			echo "<br /><b>Error:</b>: $message<br />\n";
		}
		else
		{
			// Output as simple text
			if (defined('STDERR'))
			{
				fwrite(STDERR, "$message\n");
			}
			else
			{
				echo "$message\n";
			}
		}
	}
}
