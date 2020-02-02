<?php
/*------------------------------------------------------------------------

# mod_vdisplay - 

# ------------------------------------------------------------------------

# author    Team WDMtech

# copyright Copyright (C) 2013 wwww.wdmtech.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.wdmtech.com

# Technical Support:  Forum - http://www.wdmtech..com/support-forum

-----------------------------------------------------------------------*/
 
// No direct access
defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';
    $dataResult = modVdisplayHelper::getDisplaySearch($params);
        if(!empty($dataResult)){
            $allData=$dataResult[0];
		    $morefilterkey=json_decode($allData->morefilterkey);
			$arraycheck=array();
			$arrayField=json_decode($allData->fieldtype,true);
			$arrayColumns=array();
			$arryTypes=array();
			$arrayFieldType=array();
			foreach($arrayField['type'] as $keys=>$fieldNames)
			{
			$arrayColumns[]=$keys;
			$arryTypes[]=$fieldNames;
			foreach($fieldNames as $fName)
			{
			  $arrayFieldType[]=$fName;
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
			$arrayEmptyFields=array_diff($morefilterkey,$arrFieldTypeValue);
			
			$dataEmptyValues = modVdisplayHelper::getEmptySearch($arrayEmptyFields,$allData->profileid,$allData->qry);
			
			//echo "<pre>";print_r($dataEmptyValues);jexit();
				
        }
require JModuleHelper::getLayoutPath('mod_vdisplay', $params->get('layout', 'default'));

?>