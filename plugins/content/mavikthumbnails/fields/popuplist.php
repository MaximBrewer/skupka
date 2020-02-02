<?php
/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die();

jimport('joomla.filesystem.folder');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldPopupList extends \JFormFieldList
{
    protected $type = 'PopupList';
    
    protected function getOptions()
    {
        $options = array();
        $files = \JFolder::files(JPATH_PLUGINS.'/content/mavikthumbnails/decorators/popups', '.*\.xml$', true, true);
        foreach ($files as $file) {
            $xml = simplexml_load_file($file);
            $options[] = JHtml::_('select.option', $xml->name, $xml->title);
        }
        
        reset($options);
        return $options;
    }
}