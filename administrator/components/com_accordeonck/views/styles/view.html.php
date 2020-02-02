<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;

class AccordeonckViewStyles extends JViewLegacy
{
	protected $params;
	
	protected $imagespath;
	
	protected $colorpicker_class;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$app = JFactory::getApplication();
		$input = new JInput();
		
		// load the module params
		$controller = new AccordeonckController();
		$this->params_string = $controller->loadParam($input->get('id', 0, 'int'), '', false, true, true);
		$this->params = new JRegistry($this->params_string);

		$this->imagespath = JUri::root(true) . '/administrator/components/com_accordeonck'; 
		$this->colorpicker_class = 'color {required:false,pickerPosition:\'top\',pickerBorder:2,pickerInset:3,hash:true}';

		require_once(JPATH_SITE . '/administrator/components/com_accordeonck/helpers/ckinterface.php');
		$this->interface = new CK_Interface();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
		exit();
	}
}
