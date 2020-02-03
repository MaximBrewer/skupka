<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_slideshowck/helpers/ckframework.php';
require_once JPATH_ADMINISTRATOR . '/components/com_slideshowck/helpers/helper.php';

\Slideshowck\CKFramework::load();
SlideshowckHelper::loadCkbox();

JText::script('SLIDESHOWCK_ADDSLIDE');
JText::script('SLIDESHOWCK_SELECTIMAGE');
JText::script('SLIDESHOWCK_SELECT_LINK');
JText::script('SLIDESHOWCK_REMOVE2');
JText::script('SLIDESHOWCK_SELECT');
JText::script('SLIDESHOWCK_CAPTION');
JText::script('SLIDESHOWCK_USETOSHOW');
JText::script('SLIDESHOWCK_IMAGE');
JText::script('SLIDESHOWCK_VIDEO');
JText::script('SLIDESHOWCK_TEXTOPTIONS');
JText::script('SLIDESHOWCK_IMAGEOPTIONS');
JText::script('SLIDESHOWCK_LINKOPTIONS');
JText::script('SLIDESHOWCK_VIDEOOPTIONS');
JText::script('SLIDESHOWCK_ALIGNEMENT_LABEL');
JText::script('SLIDESHOWCK_TOPLEFT');
JText::script('SLIDESHOWCK_TOPCENTER');
JText::script('SLIDESHOWCK_TOPRIGHT');
JText::script('SLIDESHOWCK_MIDDLELEFT');
JText::script('SLIDESHOWCK_CENTER');
JText::script('SLIDESHOWCK_MIDDLERIGHT');
JText::script('SLIDESHOWCK_BOTTOMLEFT');
JText::script('SLIDESHOWCK_BOTTOMCENTER');
JText::script('SLIDESHOWCK_BOTTOMRIGHT');
JText::script('SLIDESHOWCK_LINK');
JText::script('SLIDESHOWCK_TARGET');
JText::script('SLIDESHOWCK_SAMEWINDOW');
JText::script('SLIDESHOWCK_NEWWINDOW');
JText::script('SLIDESHOWCK_VIDEOURL');
JText::script('SLIDESHOWCK_REMOVE');
JText::script('SLIDESHOWCK_IMPORTFROMFOLDER');
JText::script('SLIDESHOWCK_ARTICLEOPTIONS');
JText::script('SLIDESHOWCK_SLIDETIME');
JText::script('SLIDESHOWCK_CLEAR');
JText::script('SLIDESHOWCK_SELECT');
JText::script('SLIDESHOWCK_TITLE');
JText::script('SLIDESHOWCK_STARTDATE');
JText::script('SLIDESHOWCK_ENDDATE');
JText::script('SLIDESHOWCK_SAVE');
JText::script('SLIDESHOWCK_TEXT_CUSTOM');
JText::script('SLIDESHOWCK_ARTICLE');
JText::script('SLIDESHOWCK_TEXT');


require_once 'ckformfield.php';

class JFormFieldCkslidesmanager extends CKFormField {

	protected $type = 'ckslidesmanager';

	protected function getInput() {

		// loads the language files from the frontend
		$lang	= JFactory::getLanguage();
		$lang->load('com_slideshowck', JPATH_SITE . '/components/com_slideshowck', $lang->getTag(), false);
		$lang->load('com_slideshowck', JPATH_SITE, $lang->getTag(), false);

		require_once(JPATH_ROOT . '/administrator/components/com_slideshowck/helpers/defines.js.php');
		$path = 'media/com_slideshowck/assets/elements/ckslidesmanager/';
		JHtml::_('jquery.framework');
		JHtml::_('jquery.ui', array('core', 'sortable'));
		JHTML::_('script', 'media/com_slideshowck/assets/jquery-uick-custom.js');
		JHTML::_('script', 'media/com_slideshowck/assets/admin.js');
		JHTML::_('script', $path . 'ckslidesmanager.js');
		if (\Slideshowck\CKFof::isSite()) {
			JHTML::_('stylesheet', 'media/com_slideshowck/assets/front-edition.css');
		}
		
		JHTML::_('stylesheet', 'media/com_slideshowck/assets/jquery-ui.min.css');
		JHTML::_('stylesheet', $path . 'ckslidesmanager.css');

		$html = '<input name="' . $this->name . '" id="ckslides" type="hidden" value="' . $this->value . '" />'
				. '<div class="ckaddslide ckbutton ckbutton-success" onclick="javascript:ckAddSlide(false, \'top\');"><i class="far fa-plus-square"></i> ' . JText::_('SLIDESHOWCK_ADDSLIDE') . '</div>'
				. '<ul id="ckslideslist" class="ckinterface" style="clear:both;"></ul>'
				. '<div class="ckaddslide ckbutton ckbutton-success" onclick="javascript:ckAddSlide();"><i class="far fa-plus-square"></i> ' . JText::_('SLIDESHOWCK_ADDSLIDE') . '</div>';

		return $html;
	}

	protected function getLabel() {

		return '';
	}

//	protected function getArticlesList() {
//		$db = & JFactory::getDBO();
//
//		$query = "SELECT id, title FROM #__content WHERE state = 1 LIMIT 2;";
//		$db->setQuery($query);
//		$row = $db->loadObjectList('id');
//		var_dump($row);
//		return json_encode($row);
//	}

}

