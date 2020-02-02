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
defined( '_JEXEC' ) or die( 'Restricted access' );

class  VdataControllerDisplay extends VdataController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		$this->model = $this->getModel('display');
		JFactory::getApplication()->input->set( 'view', 'display' );
		// Register Extra tasks
		$this->registerTask( 'add', 'edit' );
	}
	
	function display($cachable = false, $urlparams = false){
		
		$user = JFactory::getUser();
		$canViewSchedule = $user->authorise('core.access.cron', 'com_vdata');
		
		if(!$canViewSchedule){
			$msg = JText::_( 'ALERT_AUTHORIZATION_ERROR' );
			$this->setRedirect( 'index.php?option=com_vdata', $msg , 'error');
		}
			
		parent::display();
	}
	
	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JFactory::getApplication()->input->set( 'view', 'display' );
		JFactory::getApplication()->input->set( 'layout', 'form'  );
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		
		if($this->model->store()) {
			$msg = JText::_( 'DISPLAY_SAVED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=display', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage( $this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=display');
		}

	}
	
	function apply()
	{
		if($this->model->store()) {
			$msg = JText::_( 'DISPLAY_SAVED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=display&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0), $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=display&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0) );
		}

	}
	
	
	
	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		if($this->model->delete()) {
			$msg = JText::_( 'RECORDS_DELETED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=display', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=display');
		}
		
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$session = JFactory::getSession();
		$session->clear('columns');
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vdata&view=display', $msg );
	}
	/**
	 * Method to Dropdown Profiles  record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getProfiles(){
		JSession::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}');
		$input = JFactory::getApplication()->input;
		$profileid = $input->get('profileid', 0);
		$obj = new stdClass();
		$obj->result = 'success';
		$profiles = $this->model->getProfiles();
		$obj->html = '<option value="">'.JText::_('SELECT_PROFILE').'</option>';
		$obj->html .= '<option value="-1">'.JText::_('CREATE_PROFILE').'</option>';
		if(empty($profiles)){
			$obj->html .= '<option value="-1">'.JText::_('CREATE_PROFILE').'</option>';
			jexit(json_encode($obj));
		}
		foreach($profiles as $profile){
			$obj->html .= '<option value="'.$profile->id.'"';
			if($profileid && ($profileid==$profile->id)){
				$obj->html .= 'selected="selected"';
			}
			$obj->html .= '>'.$profile->title.'</option>';
			
		}
		
		jexit(json_encode($obj));
	}
	
	function publish(){
		$cid = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
		$model = $this->getModel('display');
		$model->publishList($cid, 1);
		$this->setRedirect('index.php?option=com_vdata&view=display');
		
	}
	
	function unpublish(){
		$cid = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
		$model = $this->getModel('display');
		$model->publishList($cid, 0);
		$this->setRedirect('index.php?option=com_vdata&view=display');
	}
	/**
	 * Method to Onchange Profilesfields  record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function Profilesfields(){
		$db = JFactory::getDbo();
		$obj = new stdClass();
		$obj->result = 'success';
		$obj->uniqueKey='';
		$obj->uniqueAlias ='';
		$cid = JFactory::getApplication()->input->get('eid', 0, 'int');
		$profileid = JFactory::getApplication()->input->get('id',0, 'int');
		if(!empty($cid)){
			$query1 = 'select count(*) from #__vd_display where id='.(int)$cid;
			$query1 .= ' and profileid='.(int)$profileid;
			$db->setQuery($query1);
			$count=$db->loadResult();
			if($count>0)
			{
				
				$query1 = 'select * from #__vd_display where id='.(int)$cid;
				$db->setQuery($query1);
				$item=$db->loadObjectList();
				$itemInfo=$item[0];
				$obj->uniqueKey .=$itemInfo->uniquekey;
				$obj->uniqueAlias .=$itemInfo->uniquealias;
				if(!empty($itemInfo->likesearch)){
				$obj->likeSearch =explode(",",$itemInfo->likesearch); }
				$defaultFields=$this->defaultFieldEdit($cid);
				//print_r($defaultFields);jeixt();
				$obj->tableTR=$this->addMoreFilter($defaultFields,$itemInfo);
				//print_r($obj->tableTR);jexit();
					
			}
			else{
				$obj->uniqueKey .='';
				$obj->uniqueAlias .='';
			}
		}
		$profiles = $this->model->getProfilesfields();
		//echo "<pre>";print_r(json_decode($profiles->params));jexit();
		$obj->html = '<option value="">'.JText::_('SELECT_UNIQUE_KEY').'</option>';
		$obj->likeall = '<option value="">'.JText::_('SELECT_LIKE_SEARCH').'</option>';
		$obj->like ='';
		$params=json_decode($profiles->params);
		if(property_exists($params,'table')){
			$maintable=$params->table;
			if(property_exists($params,'fields')){
				$fields=$params->fields;
				$refTables = array();
				$refCol = array();
				$obj->htmldes ='<optgroup label="'.$maintable.'(Main table)">';
				foreach($fields as $key=>$profile){
						if($profile->data=='include'){
										$obj->htmldes .= '<option value="'.$key.'">'.$key.'</option>';
						}			
						if($profile->data=="reference"){
							if(!in_array($profile->table,$refTables)){
								$refTables[]=$profile->table;
								if(!in_array($profile->reftext,$refCol)){
									$refCol[$profile->table]=$profile->reftext;
								}
								
						 
							} 
						}	
						if($profile->data=="defined"){
							$obj->like .= '<option value="'.$key.'">'.$key.'</option>'; 
						}
					
				}
				foreach($refTables as $table){
					$obj->like .='<optgroup label="'.$table.'(Reference)">';
					foreach($refCol[$table] as $colname){
					$obj->like .= '<option value="'.$table.'.'.$colname.'">'.$colname.'</option>'; 
					}
					
				}
			$obj->likeall .=$obj->htmldes;
			$obj->likeall .=$obj->like;
			$obj->html    .=$obj->htmldes;
				
			}
			else{
				$all_fields=$this->quickFields($maintable);
				$obj->fields_op ='<optgroup label="'.$maintable.' (Main table)">';
				foreach($all_fields as $field_key){
				$obj->fields_op .= '<option value="'.$field_key.'">'.$field_key.'</option>'; 
				}
				$obj->likeall .=$obj->fields_op;
				$obj->html    .=$obj->fields_op;
			}
		}
		jexit(json_encode($obj));
	}
	
	/**
	 * Method to Onchange Profilesfields  record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function ProfilesFieldqry(){
		$db = JFactory::getDbo();
		$obj = new stdClass();
		$obj->result = 'success';
		$obj->result='success';
		$obj->uniqueKey='';
		$obj->uniqueAlias ='';
		$obj->unKey ='';
		$obj->unAlias ='';
		$obj->likeSearches='';
		
		$cid = JFactory::getApplication()->input->get('eid', 0, 'int');
		$qry = JFactory::getApplication()->input->get('qry',0, 'string');
		if(!empty($cid)){
			$query1 = 'select count(*) from #__vd_display where id='.(int)$cid;
			$query1 .= ' and qry="'.$qry.'"';
			$db->setQuery($query1);
			$count=$db->loadResult();
			if($count>0)
			{
				$query1 = 'select * from #__vd_display where id='.(int)$cid;
				$db->setQuery($query1);
				$item=$db->loadObject();
				$itemInfo=$item;
				$obj->uniqueKey .=$itemInfo->uniquekey;
				$obj->uniqueAlias .=$itemInfo->uniquealias;
				if(!empty($itemInfo->likesearch)){
				$obj->likeSearch =explode(",",$itemInfo->likesearch); }
				$defaultFields=$this->defaultFieldEdit($cid);
				$obj->tableTR=$this->addMoreFilter($defaultFields,$itemInfo);
				
			}
			
		}
		$obj->html = '<option value="">'.JText::_('SELECT_UNIQUE_KEY').'</option>';
		$profiles = $this->model->getProfilesQry();
		$profileArray=(array)$profiles;
		foreach($profileArray as $key=>$values)
		{
			if(!empty($obj->uniqueKey) && !empty($obj->uniqueAlias)){
				if($obj->uniqueKey==$key){
				  $obj->unKey .= '<option value="'.$key.'" selected="selected">'.$key.'</option>';
				}
				else{
				  $obj->unKey .= '<option value="'.$key.'" >'.$key.'</option>';
				}			
				if($obj->uniqueAlias==$key){
				  $obj->unAlias .= '<option value="'.$key.'" selected="selected">'.$key.'</option>';
				}
				else{
				  $obj->unAlias .= '<option value="'.$key.'" >'.$key.'</option>';
				}
              if(!empty($obj->likeSearch)){				
				if(in_array($key,$obj->likeSearch))
				{
				  $obj->likeSearches .= '<option value="'.$key.'" selected="selected">'.$key.'</option>';
				}
				else{
					$obj->likeSearches .= '<option value="'.$key.'" >'.$key.'</option>';
				}
			  }
			  $obj->html .= '<option value="'.$key.'">'.$key.'</option>';
			}
			else{
				$obj->html .= '<option value="'.$key.'">'.$key.'</option>';
			}
		 
		//$obj->unAlias= '<option value="'.$key.'">'.$key.'</option>'; 
		
		}
		//print_r($obj);jexit();
		jexit(json_encode($obj));
	}
	/**
	 * Method to Onchange List Item  record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function TemplateList(){
		
		$obj = new stdClass();
		$obj->result = 'success';
		$obj->html='';
		$template = $this->model->getTemplatelist();
		$obj->html .=$template;
		jexit(json_encode($obj));
	}
	/**
	 * Method to Onchange Detail Layout  record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function DetailTemplate(){
		
		$obj = new stdClass();
		$obj->result = 'success';
		$obj->html='';
		$template = $this->model->getTemplateDetail();
		$obj->html .=$template;
		jexit(json_encode($obj));
	}
	
	function addMoreFilter($defaultFields,$itemInfo)
	{
		$trTable=array();
		$morefilterkey=json_decode($itemInfo->morefilterkey,true);
		$fieldArrayKeys=array('typetext','typeradio','typecheck','typedrop');
		$fieldArrayValues=array('Text Field','Radio Type','Check Box','Drop Down');
		$fieldArray=array_combine($fieldArrayKeys,$fieldArrayValues);
		$arrayField=array(); 
		if(isset($itemInfo->fieldtype)){
		$arrayField=json_decode($itemInfo->fieldtype,true); }
		$arrayColumns=array();
		$arryTypes=array();
		$arrayFieldType=array();
		if(isset($arrayField)){
		foreach($arrayField['type'] as $keys=>$fieldNames)
		{
			$arrayColumns[]=$keys;
			$arryTypes[]=$fieldNames;
			foreach($fieldNames as $fName)
			{
			$arrayFieldType[]=$fName;
			}

		}
		}
			$arrFieldTypes=array();
			for($i=0;$i<count($arryTypes);$i++)
			{
			  $arrFieldTypes[]=$arryTypes[$i][0];
			}

			$arrFieldTypeValue=array();
			foreach($arrayField['value'] as $arrayKey=>$arrayVal)
			{
			  $arrFieldTypeValue[]=$arrayKey;
			}
			
		for($j=0;$j<count($morefilterkey);$j++)
		{	
				if(in_array($morefilterkey[$j],$arrFieldTypeValue)){
				  $FieldsValues=$arrayField['value'][$morefilterkey[$j]];
				}
				else{
					$FieldsValues=array();
				}
				//print_r($arrayField['value'][$morefilterkey[0]]);jexit();
		$trTable[]='<tr class="editHideTr">';
		$trTable[]='<td id="'.$j.'"><select name="morefilterkey[]" class="select_filter_field  chosenSelect">';
		
		foreach($defaultFields->likeSearch as $k=>$v)
				{
					
					foreach($v as $key=>$value){
						foreach($value as $valsearch)
						{
							
							if($k=='refrTable')
							{
								$explodeTable=explode("#__",$key);
							    $fieldName=ucfirst($explodeTable[1]).' '.$valsearch;
							 $optvalue=$key.'.'.$valsearch;
							if($morefilterkey[$j]==$optvalue)
							{
								$trTable[]='<option value="'.$optvalue.'"  selected="selected" >'.$fieldName.'</option>';
							}
							else{
							$trTable[]='<option value="'.$optvalue.'">'.$fieldName.'</option>';
							}
							}
							else
							{ 
						       if($morefilterkey[$j]==$valsearch)
								{
									$trTable[]='<option value="'.$valsearch.'" selected="$selected">'.$valsearch.'</option>';
								}
								else{
									$trTable[]='<option value="'.$valsearch.'">'.$valsearch.'</option>';
								}
							
							 
							}
						}
					}
				}
		
		$trTable[]='</select></td><td> ';
		$trTable[]='<div id="add_newTd_'.$j.'" style="display:block;">';
		$trTable[]='<select name="fieldtype[type]['.$arrayColumns[$j].'][]" class="select_filter_type chosenSelect">';
		

		foreach($fieldArray as $fieldsKey=>$fieldsvalue)
		{
          if($fieldsKey==$arrFieldTypes[$j]){ 
		   $trTable[]='<option value="'.$fieldsKey.'" selected="selected">'.$fieldsvalue.'</option>';
		   }
		   else{
			  $trTable[]='<option value="'.$fieldsKey.' " >'.$fieldsvalue.'</option>';
		   }
	
		
		 }

		
		$trTable[]='</select>';

		
		$fieldsTypes=$arryTypes[$j][0];
		switch($fieldsTypes)
		{
		

		case 'typetext':
        $strSearchKeys=array('phrase','substr');
		$strSearchValues=array('Extract Phrase Search','Sub String Search');
		$strSearchType=array_combine($strSearchKeys,$strSearchValues);
		
		$trTable[]='<div class="select_filter_option" style="display:inline-block;">
		<select name="fieldtype[value]['.$arrayColumns[$j].'][]" class="chosenSelect">';
		
		if(isset($FieldsValues)){
			 
				foreach($strSearchType as $strSearchKeys=>$strSearchValues)	{
					
					if($strSearchKeys==$FieldsValues[0]){
					$trTable[]='<option value="'.$strSearchKeys.'" selected="selected" >'.$strSearchValues.'</option>';
					}
					else{
					$trTable[]='<option value="'.$strSearchKeys.'">'.$strSearchValues.'</option>';	
					}
				} 
			
		} else{ 
         foreach($strSearchType as $strSearchKeys=>$strSearchValues)	{		
		$trTable[]='<option value="'.$strSearchKeys.'"  >'.$strSearchValues.'</option>';
		}  
		}
		$trTable[]='</select></div>';
		
		break;

		case 'typeradio':
		
		$trTable[]='<div class="select_filter_option" style="display:inline-block;">
		<select name="fieldtype[value]['.$arrayColumns[$j].'][]" id="" class="js-example-tags " multiple>';
		
		if(!empty($FieldsValues)){
		foreach($FieldsValues as $options){
		$trTable[]='<option value="'.$options.'" selected="selected">'.$options.'</option>';
		 } }else{  
		 $trTable[]='<option value=""></option>';
		} 

		$trTable[]='</select></div>';
		
		break;

		case 'typecheck':
		
		$trTable[]='<div class="select_filter_option" style="display:inline-block;">
		<select name="fieldtype[value]['.$arrayColumns[$j].'][]" id="" class="js-example-tags" multiple>';
		
		if(!empty($FieldsValues)){
		foreach($FieldsValues as $options){
		$trTable[]='<option value="'.$options.'" selected="selected">'.$options.'</option>';
		 }  }else{  $trTable[]='<option value=""></option>';
		} 
		$trTable[]='</select></div>';
		
		break;

		case 'typedrop':
		
		$trTable[]='<div class="select_filter_option" style="display:inline-block;">
		<select name="fieldtype[value]['.$arrayColumns[$j].'][]"  class="js-example-tags" multiple>';
		
		if(!empty($FieldsValues)){
		foreach($FieldsValues as $options){
		$trTable[]='<option value="'.$options.'" selected="selected" >'.$options.'</option>';
	 } }else{  $trTable[]='<option value=""></option>';
		}  
		$trTable[]='</select></div>';
		
		break;
		} 
		$trTable[]='</div></td>
		<td><a href="#" class="remove_field btn btn-danger"><i class="icon-delete"></i> '.JText::_("REMOVE").'</a></td>
		</tr>';

		
		}
		$trTable1=implode('',$trTable);
		return $trTable1;
	}
	
	function defaultFieldEdit($cid)
	{
		 $db = JFactory::getDbo();
		$objt=new stdClass();
		$q='select qry,profileid from #__vd_display where id='.(int)$cid;
		$db->setQuery($q);
		$result=$db->loadObject();
		$profileDt=$result->profileid;
		$qryDt=$result->qry;
		if(!empty($qryDt)){
			$query =$qryDt;
			$trp=$db->setQuery($query);
			$profiles=$db->loadObject();
			$profileArray=(array)$profiles;
			$fieldsArray=array();
			foreach($profileArray as $key=>$values)
			{
				$fieldsArray[]=$key;
			}
			$mainArray=array();
			$mainArray['table']=$fieldsArray;
			$mainTable=array();
			$mainTable['mainTable']=$mainArray;
			$objt->includekey=$mainArray;
			$objt->likeSearch=$mainTable;
		//print_r($obj->likeSearch);jexit();
		}
		if(!empty($profileDt)){
			$db=$this->model->getDbc();
			$query='select params from #__vd_profiles where id='.$db->quote($profileDt);
			$db->setQuery($query);
			$allfield=$db->loadResult();
			$allfield1=json_decode($allfield);
			if(property_exists($allfield1,'table')){
				$maintable=$allfield1->table;
				$arraymain=array();
				if(property_exists($allfield1,'fields')){
					$fields=(array)$allfield1->fields;
					$allkey=array();
					$arrayRef=array();
					$defArray=array();
					$refTables = array();
					$refCol = array();
					foreach($fields as $key=>$value)
					{
							if($value->data=='include'){
								$allkey[]=$key;
							}
							if($value->data=="reference"){
								if(!in_array($value->table,$refTables)){
									$refTables[]=$value->table;
									if(!in_array($value->reftext,$refCol)){
										$refCol[$value->table]=$value->reftext;
									}
								} 
							}
							if($value->data=="defined"){
								$defArray[$maintable]=$key;
							}
					}
					foreach($refTables as $table){
						$arrayRef[$table]=$refCol[$table];
					}
					$keySearch=array();
					$arraymain[$maintable]=$allkey;
					$keySearch['mainTable']=$arraymain;
					$keySearch['refrTable']=$arrayRef;
					//$obj=new stdClass();
					$objt->includekey=$arraymain;
					$objt->likeSearch=$keySearch;
				}
				else{
					$arraymain[$maintable]=$this->quickFields($maintable);
					$keySearch['mainTable']=$arraymain;
					$objt->includekey=$arraymain;
					$objt->likeSearch=$keySearch;
				}
			}
		}
		return $objt;
	}
	function quickFields($main_table)
	{
	     $fields_array=array();
		 $db = JFactory::getDbo();
		 $query='SHOW COLUMNS FROM '.$main_table ;
		 $db->setQuery($query);
		 $result=$db->loadObjectList();
		 foreach($result as $result_value)
		 {
			 $fields_array[]=$result_value->Field;
		 }
		 return $fields_array;
	}
	
}