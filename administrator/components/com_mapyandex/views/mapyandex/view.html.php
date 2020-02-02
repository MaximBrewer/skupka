<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 */
defined( '_JEXEC' ) or die();
jimport( 'joomla.application.component.view' );
mapyandeximport( 'mapyandex.render.renderinfo' );
mapyandeximport( 'mapyandex.render.renderadmin' );

class mapyandexViewMapyandex extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $tmpl;
	
	function untextnl($text)
	{
		$text = str_replace("<br />","\r\n",$text);
		return $text;
	}
	
	function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$this->form	= $this->get('Form');
		$this->state = $this->get('State');
		$tmpl = array();
		$this->params = JComponentHelper::getParams( 'com_mapyandex' );
		$this->MapYandexRenderAdmin = new MapYandexRenderAdmin();
		$this->MapYandexRenderInfo = new MapYandexRenderInfo();
		
		$document = JFactory::getDocument();
		$jsLink = JURI::base(true);
		if (!$app->isAdmin()) {
			$tUri = JURI::base();

			$jsLink = JURI::base(true).'/administrator';
		}

		$uri		= JFactory::getURI();



		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_mapyandex/assets/themes/base/jquery.ui.all.css' );
		$document->addStyleSheet( JURI::root(true).'/administrator/components/com_mapyandex/assets/mapyandex.css' );
		$document->addScript($jsLink . '/components/com_mapyandex/assets/js/jquery-1.7.2.min.js');

		
		// prepare the cSS
			$css = '.icon-48-mapyandexallmaps {
				background: url("'.JURI::root(true).'/media/com_mapyandex/colorpicker/images/icon-48-png") 0 0 no-repeat;
			}';
			
			// add the CSS to the document

		$document->addStyleDeclaration($css);


		JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX' ), 'mapyandexallmaps' );
		
		$data['layout'] = JRequest::getVar('layout');

		if($data['layout'] == 'form') { 
			JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
			JToolBarHelper::cancel( 'cancel','COM_MAPYANDEX_CANCEL' );
		}
		else if($data['layout'] == 'formedit') {

			JToolBarHelper::apply('apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('save', 'JTOOLBAR_SAVE');
			JToolBarHelper::cancel( 'cancel','COM_MAPYANDEX_CANCEL' );
	
		} else {
			JToolBarHelper::title(   JText::_( 'COM_MAPYANDEX' ), 'mapyandexallmaps' );
			JToolBarHelper::preferences('com_mapyandex', '460');
	
			
			
		}
		
		// interrogate the model
		$this->foobar = $this->get('Foobar');
		if(empty($this->foobar)) {
			require_once JPATH_COMPONENT.DS.'models'.DS.'mapyandexallmaps.php';
			$mapset = new MapYandexModelMapYandexAllMaps();
			$this->foobar = $mapset->getDefaultSettings();
		
		}
		

		
		$this->allroute = $this->get('AllRoute');

		$this->assignRef( 'tmpl', $tmpl );
		
		$this->metka = $this->get('Metka');
		
		$pageNav = $this->get('Reviews');
		$this->assignRef('pageNav', $pageNav);

		$params = JComponentHelper::getParams( 'com_mapyandex' );
		$this->key = $params->get( 'key');		
		
		$this->setLayout('row');
		
		parent::display($tpl);
	}
}
?>