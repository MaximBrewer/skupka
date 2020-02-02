<?php
/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die();

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('textarea');

class JFormFieldContextSettings extends \JFormField
{
    protected $type = 'ContextSettings';
    
    public function getInput() {
        
        $modalid = $this->id . '_modal';

        $icon = '<i class="icon-list"></i>';
        $str[] = '<button class="btn" id="' . $modalid . '_button" data-modal="' . $modalid . '">' . $icon . ' '. JText::_('PLG_MAVIKTHUMBNAILS_CONTEXT_SETTING') . '</button>';

        $value = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
        $str[] = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $value . '" />';

        JHtml::_('jquery.ui', array('core', 'sortable'));
        $document = JFactory::getDocument();
        $document->addScript(JUri::root(true).'/media/plg_content_mavikthumbnails/js/context_settings.js');
        $document->addScript(JUri::root(true).'/media/plg_content_mavikthumbnails/js/jquery.placeholder.js');

        $script = "jQuery(document).ready(function (){
            new jQuery.MavikthumbnailsContextsettings('$modalid', '$this->id');
        });";

        $document->addScriptDeclaration($script);
        $document->addStyleDeclaration(
            '.placeholder { color: #aaa; }'.
            '.opened .icon-slide-resize:before { content: "g"; }'.
            '.icon-slide-resize:before { content: "f"; }'
        );

        JText::script('JAPPLY', true);
        JText::script('JCANCEL', true);
        JText::script('PLG_MAVIKTHUMBNAILS_CONTEXT_CONDITIONS', true);
        JText::script('PLG_MAVIKTHUMBNAILS_CONTEXT_CONTEXT', true);
        JText::script('PLG_MAVIKTHUMBNAILS_CONTEXT_PROPERTY', true);
        JText::script('PLG_MAVIKTHUMBNAILS_CONTEXT_REQUEST', true);
        JText::script('PLG_MAVIKTHUMBNAILS_CONTEXT_CONTEXT_VALUE_LABEL', true);
        JText::script('PLG_MAVIKTHUMBNAILS_CONTEXT_PROPERTY_NAME_LABEL', true);
        JText::script('PLG_MAVIKTHUMBNAILS_CONTEXT_VALUE_LABEL', true);
        JText::script('PLG_MAVIKTHUMBNAILS_CONTEXT_REQUEST_NAME_LABEL', true);
        JText::script('PLG_MAVIKTHUMBNAILS_CONTEXT_SETTINGS', true);
        JText::script('PLG_MAVIKTHUMBNAILS_CONTEXT_NAME', true);

        return implode("\n", $str);
    }
    
}