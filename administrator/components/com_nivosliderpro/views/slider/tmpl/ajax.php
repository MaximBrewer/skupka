<?php

	$action = UniteFunctions::getPostVariable("action");
	$data = UniteFunctions::getPostVariable("data");
	$response = array("action"=>$action,"success"=>true);
	
	try{		
		switch($action){
			case "get_bullets_html":
				$setName = UniteFunctions::getVal($data, "setName");
				$html = HelperNivoPro::getBulletsHtml($setName);
				$response["bullets_html"] = $html;
			break;
			default:
				UniteFunctions::throwError("Wrong action: <b>$action</b>");
			break;
		}
	}catch(Exception $e){
		$message = $e->getMessage();		
		UniteFunctions::jsonErrorResponse($message);
	}
	
	UniteFunctions::jsonResponse($response);

?>