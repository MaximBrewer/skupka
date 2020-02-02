<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


try{
	
		//include item files
		$pathIncludes = JPATH_ADMINISTRATOR."/components/com_unitegallery/includes.php";
		require_once $pathIncludes;
		
		
		$galleryID = $params->get("galleryid");
		if($galleryID == "empty")
			$galleryID = null;
		
		if(empty($galleryID))
			UniteFunctionsUG::throwError("Please select a gallery from module->general settings. Right now no gallery selected");
		
		$catID = $params->get("categoryid");
		
		echo HelperUG::outputGallery($galleryID, $catID, "id");
	
	}catch(Exception $e){
		$message = "<b>Unite Gallery Error:</b><br><br> ".$e->getMessage();
		
		$operations = new UGOperations();
		$operations->putModuleErrorMessage($message);
	}
	
?>	