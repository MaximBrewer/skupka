<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'rest.php'); 

class postnordApi extends RestRequest {
  private $shopId; 
  private $key; 
  private $partner; 
  private $inactive; 
  private $apiurl; 
  private $rest; 
  private $defaults; 
  public $error; 
  function __construct($params)
   {
      
	  $this->key = $params->xkey; 
	  //$this->partner = (int)$params->partners; 
	  
	  /*
	  if (!isset($params->inactive))
	  $this->inactive = 0; 
	  else 
	  $this->inactive = $params->inactive; 
	  */
	  $this->inactive = 0; 
	  
	  $apiurl = $params->api_url; 
	 
	 
	  
	  
	  
	  if (empty($apiurl)) $apiurl = 'https://api2.postnord.com'; 
	  $this->apiurl = $apiurl; 
	  
	  
      $this->rest = new RestRequest($this->apiurl, 'GET', $this->defaults); 
   }
   
   function getStatus($infoUrl)
   {
	   $e = explode('/', $infoUrl); 
	   if (count($e)<2) return array(); 
	   $i = $e[count($e)-1]; 
	   if (!is_numeric($i)) return array(); 
	   
	    $this->rest->url_request = '/v3/consignments/'.$i;
	   $this->rest->acceptType = 'application/json'; 
	   $this->rest->verb = 'GET';
       $a = array(); 	   
	   $this->rest->buildPostBody($a); 
	   $this->rest->execute(); 
	   $body = $this->rest->responseBody; 
	   
	   $data = json_decode($body, true); 
	   
	   
	   
	   if (!isset($data['data'][0]['status'])) return array(); 
	   return $data['data'][0]; 
	   
   }
   
   function registerBalik($myData)
   {
	   
	   
	   $this->rest->url_request = '/v2/consignments';
	   $this->rest->acceptType = 'application/json'; 
	   $this->rest->verb = 'POST'; 
	   $this->rest->buildPostBody($myData); 
	   $this->rest->execute(); 
	   $body = $this->rest->responseBody; 
	   return $body; 
   }
   function getServices()
   {
	   //transportservices
	   	 $this->rest->url_request = '/v3/transportservices?shopId='.(int)$this->shopId; //.(int)$this->shopId.'?'.(int)$this->inactive; //.','.(int)$this->partner; 
	   $this->rest->acceptType = 'application/json'; 
	   $this->rest->verb = 'GET'; 
	   $this->rest->execute(); 
	   $body = $this->rest->responseBody; 
	   
	 
	   
	   
	   if (!empty($this->rest->error)) 
	   {
		   $this->error = $this->rest->error; 
		   
	   }
	   if (empty($body)) $body = ''; 
	   
	   return $body; 
	   
		
   }
   
    function getPobockaDetails($id, $country='SE', $lang='en')
    {
	   //transportservices
	   	 $this->rest->url_request = '/rest/businesslocation/v1/servicepoint/getServicePointInformation.json?apikey='.$this->key.'&countryCode='.$country.'&servicePointId='.$id.'&locale='.$lang;



	   //$this->rest->acceptType = 'application/json'; 
	   $this->rest->verb = 'GET'; 
	   $this->rest->execute(); 
	   $body = $this->rest->responseBody; 
	   
	  
	   
	 $info = $this->rest->getResponseInfo(); 
	 
	  
	  if (empty($info) || ($info['http_code'] === 429)) 
	  {
		  //JFactory::getApplication()->enqueueMessage($body); 
		  return false;
	  }
	  if (empty($info) || ($info['http_code'] != '200')) return false; 
	   
	   if (!empty($this->rest->error)) 
	   {
		   $this->error = $this->rest->error; 
			
		   
	   }
	   if (empty($body)) $body = ''; 
	   
	   return $body; 
	   /*
	   $pobocka = json_decode($body, true); 
	   return $pobocka['data']; 
	   */
		
   }
   
   function getPobockyByServiceId($id)
   {
	    
	   //$this->rest->url = 'https://api.postnord.cz'; 
	   $this->rest->url_request = '/v3/transportservices/'.$id.'/branches/?includeInactive=0,shopId='.(int)$this->shopId.',destinationOnly=1,registerOnly=0'; //.(int)$this->shopId.'?'.(int)$this->inactive; //.','.(int)$this->partner; 
	   $this->rest->acceptType = 'application/json'; 
	   $this->rest->verb = 'GET'; 
	   $this->rest->execute(); 
	   $info = $this->rest->getResponseInfo(); 
	   
	   $body = $this->rest->responseBody; 
	   return $body; 
   }
   
   function getPobocky($country, $lang, $zip)
    {
		
	 	 $this->rest->url_request = '/rest/businesslocation/v1/servicepoint/findByPostalCode.json?apikey='.$this->key.'&countryCode='.$country.'&postalCode='.$zip.'&locale='.$lang; 
		 
	   $this->rest->acceptType = 'application/json'; 
	   $this->rest->acceptType = 'text/html;charset=utf-8'; 
	   $this->rest->verb = 'GET'; 
	   $this->rest->execute(); 
	   $body = $this->rest->responseBody; 
	   $info = $this->rest->getResponseInfo(); 
	   
	   
	   if (empty($info) || ($info['http_code'] != '200')) return false; 
	   return $body; 

	   
	}
}