<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

jimport( 'joomla.application.component.view' );

class vmeeProViewtemplate extends JViewLegacy {

	function __construct($config = array()){
		parent::__construct();
		JPluginHelper::importPlugin('vmee');
		$this->dispatcher = JDispatcher::getInstance();
	}
	
	function display($tpl = null)   {
		$request = JRequest::get();
		$layout = isset($request['layout']) ? $request['layout'] : 'default';
		$this->setLayout($layout);
		$ruleModel = $this->getModel('rule');
		$triggerList = $ruleModel->getTriggerList();
		$name = '';
		if($layout == 'add'){
			JToolBarHelper::apply('createNew', 'Apply');
			JToolBarHelper::cancel('cancel', 'Cancel');
			//get triger list
			$id = '-1';
			$name = '';
			$subject = '';
			$body = '';
			$isDefault = '';
			$trigger_id='';
		}
		else{
			$this->setToolbar();
			$model = $this->getTemplateModel();
			$ids_arr = JFactory::getApplication()->input->get('template_id',null,'RAW');
			$template_id = is_array($ids_arr) ? $ids_arr[0] : $ids_arr;
			$templateData = $model->getTemplateData($template_id);
			$id = $templateData->id;
			$name = $templateData->name;
			$subject = $templateData->subject;
			$body = $templateData->body;
			$trigger = $templateData->trigger_id;
			$templateOrientation = $triggerList[$trigger]['orientation'];
			
			//call the tags plugin and get their available tags
			$allAvailableTagsDescArr = $this->dispatcher->trigger('getAvailableTags',array($templateOrientation));
			
			//reorder available tags
			$orderedTags = array();
			$unorderedTags = array();
			foreach ($allAvailableTagsDescArr as $availableTags)
			{
				foreach ($availableTags as $section){
					if(isset($section['order']) && $section['order'] >= 0){
						$orderedTags[$section['order']] = $section;
					}
					else{
						$unorderedTags[] = $section;
					}
				}
			}
			
			$tmpArr = array_merge($orderedTags, $unorderedTags);
			$this->assignRef('available_tags',$tmpArr);
		}
		$this->assignRef('id',$id);
		$this->assignRef('templateName',$name);
		$this->assignRef('subject',$subject);
		$this->assignRef('body',$body);
		$this->assignRef('triggerList', $triggerList);
		$this->assignRef('trigger_id', $trigger);

		parent::display();
	}

	private function setToolbar(){
		JRequest::setVar( 'hidemainmenu', 1 );
		JToolBarHelper::title( VMEE_PRO_TITLE.' - <span style="color:marron;font-weight: normal;"><small> [edit mode] </small></span>', 'interamind_logo' );
		JToolBarHelper::apply('apply', 'Apply');
		JToolBarHelper::save('save', 'Save');
		JToolBarHelper::cancel('cancel', 'Cancel');
		JToolBarHelper::preferences( 'com_vmeeplus',650 );
		JToolBarHelper::custom("help", "help", "help", "Help", false);
	}

	/**
	 * @return vmeeProModelTemplate
	 */
	private function getTemplateModel() {
		$model = $this->getModel();
		return $model;
	}
}
