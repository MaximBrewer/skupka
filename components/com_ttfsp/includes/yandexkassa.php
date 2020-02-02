<?php
	
	class Utils {

    	public static function formatDate(\DateTime $date) {
        	$performedDatetime = $date->format("Y-m-d") . "T" . $date->format("H:i:s") . ".000" . $date->format("P");
			return $performedDatetime;
    	}

    	public static function formatDateForMWS(\DateTime $date) {
       	 	$performedDatetime = $date->format("Y-m-d") . "T" . $date->format("H:i:s") . ".000Z";
	   	 	return $performedDatetime;
    	}
	}
	
	function checkMD5($request, $params) {
        $str = $request['action'] . ";" .
            $request['orderSumAmount'] . ";" . $request['orderSumCurrencyPaycash'] . ";" .
            $request['orderSumBankPaycash'] . ";" . $request['shopId'] . ";" .
            $request['invoiceId'] . ";" . $request['customerNumber'] . ";" . $params['yandex_kassa_password'];
        
        $md5 = strtoupper(md5($str));

        if ($md5 != strtoupper($request['md5'])) {

            return false;
        }
  
        
        return true;
    }
    
    function buildResponse($params, $functionName, $invoiceId, $result_code, $message = null) {
        
            $performedDatetime = Utils::formatDate(new DateTime());
            $response = '<?xml version="1.0" encoding="UTF-8"?><' . $functionName . 'Response performedDatetime="' . $performedDatetime .
                '" code="' . $result_code . '" ' . ($message != null ? 'message="' . $message . '"' : "") . ' invoiceId="' . $invoiceId . '" shopId="' . $params['yandex_kassa_shopid'] . '"/>';
    
            return $response;
       
    }
    
    function sendResponse($responseBody) {
	 
        header("HTTP/1.0 200");
        header("Content-Type: application/xml");
        echo $responseBody;
        exit;
    }

	
?>