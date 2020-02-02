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
 * @version $Id: view.html.php 1 2011-10-21 19:19 sakis $
 * @since		1.0
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
class CustomfiltersViewCustomfilters extends JViewLegacy
{

    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the view
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->displayTypes = $this->get('AllDisplayTypes');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
        $this->params = JComponentHelper::getParams('com_customfilters');
        // Get data from the model
        $model = $this->getModel();
        $this->update_id = $model->getUpdateId();
        $this->update_plugin = $model->isUpdatePluginEnabled();
        $this->needsdlid = $model->needsDownloadID();
        $this->addToolbar();
        if (count($this->items) == 0)
            JError::raiseNotice(500, JText::_('No custom fields found'), 'custom fields should exist in order to continue');
        parent::display($tpl);
    }

    public function addToolbar()
    {
        JToolBarHelper::title(JText::_('COM_CUSTOMFILTERS'), 'custom_filters');

        if (JFactory::getUser()->authorise('core.edit', 'com_customfilters'))
            JToolBarHelper::custom('customfilters.savefilters', 'save', 'save_f2.png', 'COM_CUSTOMFILTERS_SAVE', false);
        if (JFactory::getUser()->authorise('core.edit.state', 'com_customfilters')) {
            JToolBarHelper::publish('customfilters.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('customfilters.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        if (JFactory::getUser()->authorise('core.edit', 'com_customfilters')) {
            // Add the optimizer button.
            $icon = 'health';
            $height = '550';
            $width = '875';
            $top = 0;
            $left = 0;
            $onClose = '';
            $alt = 'COM_CUSTOMFILTERS_OPTIMIZER';
            $bar = JToolBar::getInstance('toolbar');
            $bar->appendButton('Popup', $icon, $alt, 'index.php?option=com_customfilters&view=optimizer&tmpl=component', $width, $height, $top, $left, $onClose);
        }

        if (JFactory::getUser()->authorise('core.admin', 'com_customfilters')) {
            JToolBarHelper::preferences('com_customfilters', '400');
        }

        $this->document->addScript(JURI::base() . 'components/com_customfilters/assets/js/chosen.jquery.min.js');
        $this->document->addStylesheet(JURI::base() . 'components/com_customfilters/assets/css/chosen.min.css');

        // add component scripts
        $this->document->addScript(JURI::base() . 'components/com_customfilters/assets/js/loadVersion.js');
        $this->document->addScript(JURI::base() . 'components/com_customfilters/assets/js/bdpopup.js');
        $this->document->addScript(JURI::base() . 'components/com_customfilters/assets/js/general.js');
        $this->document->addStylesheet(JURI::base() . 'components/com_customfilters/assets/css/bdpopup.css');

        // add choosen
        $script = 'jQuery( function($) {$(".cf-choosen-select").chosen({width:"200px",display_selected_options:false});});';
        $this->document->addScriptDeclaration($script);
    }
}