<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die(); 

class VdataViewDisplay extends VDView
{
    function display($tpl = null)
    {
		$mainframe = JFactory::getApplication();
		$context = 'com_vdata.display.list.';
		$layout = JFactory::getApplication()->input->get('layout');
		$user  = JFactory::getUser();
		 if(!$user->authorise('core.access.display', 'com_vdata')){
			$msg = JText::_('VDATA_LOGIN_ALERT');
			$mainframe->redirect(JRoute::_('index.php?option=com_users&view=login'), $msg);
		}
		//$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'id', 'cmd' );
		//$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		$searchPhrase= $mainframe->getUserStateFromRequest( $context.'searchPhrase', 'searchPhrase',	'',	'array' );
		$searchSubstr= $mainframe->getUserStateFromRequest( $context.'searchSubstr', 'searchSubstr',	'',	'array' );
		$searchRadio= $mainframe->getUserStateFromRequest( $context.'searchRadio', 'searchRadio',	'',	'array' );
		$searchCheckbox= $mainframe->getUserStateFromRequest( $context.'searchCheckbox', 'searchCheckbox',	'',	'array' );
		$searchDrop= $mainframe->getUserStateFromRequest( $context.'searchDrop', 'searchDrop',	'',	'array' );
		$likeSearch= $mainframe->getUserStateFromRequest( $context.'likeSearch', 'likeSearch',	'',	'string' );
		$likeSearchValue= $mainframe->getUserStateFromRequest( $context.'likeSearchValue', 'likeSearchValue',	'',	'string' );
		$this->itemtemplate=$this->get('ItemTemplate');
		$this->twoProfiles=$this->get('TwoProfilesMap');
		$this->itemslist = $this->get('Items');
		$this->paramsValue=$this->get('ParamsValue');
		if($layout=='items'){
		//print_r($this->itemslist);jexit();	
		  $this->pagination = $this->get('Pagination');
		}
		if($layout=='item'){
			$this->item = $this->get('Item');
			$eventItem=new stdClass();
			$eventItem->text = $this->itemtemplate->itemdetailtmpl;
			$eventItem->params=new stdClass();
			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$dispatcher->trigger('onContentPrepare', array ('com_vdata.display', &$eventItem, &$eventItem->params, 0));
			$this->itemtemplate->itemdetailtmpl = $eventItem->text;
		}
     	// Table ordering.
		//$this->lists['order_Dir'] = $filter_order_Dir;
		//$this->lists['order']     = $filter_order;
		$this->lists['searchPhrase']     = $searchPhrase;
		$this->lists['searchSubstr']     = $searchSubstr;
		$this->lists['searchCheckbox']     = $searchCheckbox;
		$this->lists['searchRadio']     = $searchRadio;
		$this->lists['searchDrop']     = $searchDrop;
		$this->lists['likeSearch']     = $likeSearch;
		$this->lists['likeSearchValue']     = $likeSearchValue;
		parent::display($tpl);
    }
}
