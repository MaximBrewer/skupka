<?php
/**
 *
 * The basic view file
 *
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @since		1.9.5
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
// Load the view framework
jimport('joomla.application.component.view');

/**
 * The basic view class
 *
 * @author Sakis Terz
 * @since 1.0
 */
class CustomfiltersViewOptimizer extends JViewLegacy
{

    /**
     * Display the view
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Create the Toolbar
     */
    public function addToolbar()
    {
        JToolBarHelper::title(JText::_('COM_CUSTOMFILTERS'), 'custom_filters');
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $this->document->setTitle(JText::_('COM_CUSTOMFILTERS_OPTIMIZER'));
        $this->document->addScript(JURI::base() . 'components/com_customfilters/assets/js/general.js');
        return $this;
    }
}