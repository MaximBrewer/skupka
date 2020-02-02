<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2015
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.487
 * @date		2016-03-15
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die;

if (defined(JVERSION) && version_compare(JVERSION, '3', 'ge'))
{
	class ShlMvcLayout_File extends JLayoutFile
	{
	}
}
else
{
	/**
	 * Base class for rendering a display layout
	 * loaded from from a layout file
	 *
	 * @since       0.2.1
	 */
	class ShlMvcLayout_File extends ShlMvcLayout_Base
	{
		/**
		 * @var    string  Dot separated path to the layout file, relative to base path
		 * @since  0.2.1
		 */
		protected $layoutId = '';

		/**
		 * @var    string  Base path to use when loading layout files
		 * @since  0.2.1
		 */
		protected $basePath = null;

		/**
		 * @var    string  Full path to actual layout files, after possible template override check
		 * @since  0.2.2
		 */
		private $fullPath = null;

		/**
		 * Method to instantiate the file-based layout.
		 *
		 * @param   string  $layoutId  Dot separated path to the layout file, relative to base path
		 * @param   string  $basePath  Base path to use when loading layout files
		 *
		 * @since   3.0
		 */
		public function __construct($layoutId, $basePath = '')
		{
			$this->layoutId = $layoutId;
			$this->basePath = empty($basePath) ? JPATH_ROOT . '/layouts' : rtrim($basePath, DIRECTORY_SEPARATOR);
		}

		/**
		 * Method to render the layout.
		 *
		 * @param   object  $displayData  Object which properties are used inside the layout file to build displayed output
		 *
		 * @return  string  The necessary HTML to display the layout
		 *
		 * @since   3.0
		 */
		public function render($displayData)
		{
			$layoutOutput = '';

			// Check possible overrides, and build the full path to layout file
			$path = $this->getPath();

			// If there exists such a layout file, include it and collect its output
			if (!empty($path))
			{
				ob_start();
				include $path;
				$layoutOutput = ob_get_contents();
				ob_end_clean();
			}

			return $layoutOutput;
		}

		/**
		 * Method to finds the full real file path, checking possible overrides
		 *
		 * @return  string  The full path to the layout file
		 *
		 * @since   3.0
		 */
		protected function getPath()
		{
			if (is_null($this->fullPath) && !empty($this->layoutId))
			{
				$rawPath = str_replace('.', '/', $this->layoutId) . '.php';
				$fileName = basename($rawPath);
				$filePath = dirname($rawPath);

				$possiblePaths = array(JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/layouts/' . $filePath,
					$this->basePath . '/' . $filePath);

				$this->fullPath = JPath::find($possiblePaths, $fileName);
			}

			return $this->fullPath;
		}
	}
}
