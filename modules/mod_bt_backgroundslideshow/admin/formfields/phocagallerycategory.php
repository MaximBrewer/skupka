<?php

/**
 * @package 	formfields
 * @version		1.0
 * @created		Apr 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/modules/mod_bt_backgroundslideshow/helpers/helper.php';
$helper = new BTBgSlideShowHelper();
if ($helper->checkPhocaComponent()) {
    if (!class_exists('PhocaGalleryLoader')) {
        @require_once( JPATH_ADMINISTRATOR . '/components/com_phocagallery/libraries/loader.php');
    }
    phocagalleryimport('phocagallery.render.renderadmin');
	phocagalleryimport('phocagallery.html.category');
}
jimport('joomla.form.formfield');
/*
 * This trick allows us to extend the correct class, based on whether it's Joomla! 1.5 or 1.6
 */

class JFormFieldPhocaGalleryCategory extends JFormField {

    protected $type = 'phocagallerycategory';

    protected function getInput() {
        $helper = new BTBgSlideShowHelper();
        if ($helper->checkPhocaComponent()) {
            $db = JFactory::getDBO();
            //build the list of categories
            $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
                    . ' FROM #__phocagallery_categories AS a'
                    . ' WHERE a.published = 1'
                    . ' ORDER BY a.ordering';
            $db->setQuery($query);
            $phocagallerys = $db->loadObjectList();

            // TODO - check for other views than category edit
            $view = JRequest::getVar('view');
            $catId = -1;
            if ($view == 'phocagalleryc') {
                $id = $this->form->getValue('id'); // id of current category
                if ((int) $id > 0) {
                    $catId = $id;
                }
            }
            $tree = array();
            $text = '';
			if(method_exists('PhocaGalleryRenderAdmin', 'CategoryTreeOption')){
				$tree = PhocaGalleryRenderAdmin::CategoryTreeOption($phocagallerys, $tree, 0, $text, $catId);
			}else if(method_exists('PhocaGalleryCategory', 'CategoryTreeOption')){
				$tree = PhocaGalleryCategory::CategoryTreeOption($phocagallerys, $tree, 0, $text, $catId);
			}else{
				$class = $this->element['class'] ? (string) $this->element['class'] : '';
				return "<div class='{$class}'>".JText::_('MOD_BTBGSLIDESHOW_PHOCA_ALERT')."</div>";
			}
            array_unshift($tree, JHTML::_('select.option', '', '- ' . JText::_('MOD_BTBGSLIDESHOW_PHOCA_ALL_CATEGORIES') . ' -', 'value', 'text'));
            // Initialize JavaScript field attributes.
            $class = $this->element['class'] ? (string) $this->element['class'] : '';
            $attr = '';
            $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
            $attr .= ' class="inputbox ' . $class . '"';
            $document = JFactory::getDocument();
            $document->addCustomTag('<script type="text/javascript">
                                        function changeCatid() {
                                              var catid = document.getElementById(\'jform_catid\').value;
                                              var href = document.getElementById(\'pgselectytb\').href;
                                            href = href.substring(0, href.lastIndexOf("&"));
                                            href += \'&catid=\' + catid;
                                            document.getElementById(\'pgselectytb\').href = href;
                                        }
                                        </script>');
            return JHTML::_('select.genericlist', $tree, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
        } else {
            $class = $this->element['class'] ? (string) $this->element['class'] : '';
            return "<div class='{$class}'>".JText::_('MOD_BTBGSLIDESHOW_PHOCA_ALERT')."</div>";
        }
    }

}

?>