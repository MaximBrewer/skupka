<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'rest.php'); 

class ulozenkaApi extends RestRequestUlozenka  {
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
      $this->shopId = (int)$params->shopid; 
	  $this->key = $params->xkey; 
	  $this->partner = (int)$params->partners; 
	  
	  /*
	  if (!isset($params->inactive))
	  $this->inactive = 0; 
	  else 
	  $this->inactive = $params->inactive; 
	  */
	  $this->inactive = 0; 
	  
	  $apiurl = $params->api_url; 
	 
	  $apiurl = str_replace('http://', '', $apiurl); 
	  $apiurl = str_replace('https://', '', $apiurl); 
	  $apiurl = str_replace('/v2/', '', $apiurl); 
	  
	  
	  
	  if (empty($apiurl)) $apiurl = 'api.ulozenka.cz'; 
	  
	  $this->defaults = array(); 
	  if (isset($params->shopid))
	  $this->defaults['X-Shop'] = $params->shopid;

  
  if (isset($params->xkey))
	  $this->defaults['X-Key'] = $params->xkey; 
	  $this->extraHeader = $this->defaults; 
	  
	  
	  
	  $this->apiurl = 'https://'.$apiurl; 
	  
	  
	  
      $this->rest = new RestRequestUlozenka($this->apiurl, 'POST', $this->defaults); 
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
   
    function getPobockaDetails($id)
   {
	   //transportservices
	   	 $this->rest->url_request = '/v3/branches/'.$id; //.(int)$this->shopId.'?'.(int)$this->inactive; //.','.(int)$this->partner; 
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
	   /*
	   $pobocka = json_decode($body, true); 
	   return $pobocka['data']; 
	   */
		
   }
   
   function getPobockyByServiceId($id)
   {
	    
	   //$this->rest->url = 'https://api.ulozenka.cz'; 
	   $this->rest->url_request = '/v3/transportservices/'.$id.'/branches/?includeInactive=0,shopId='.(int)$this->shopId.',destinationOnly=1,registerOnly=0'; //.(int)$this->shopId.'?'.(int)$this->inactive; //.','.(int)$this->partner; 
	   $this->rest->acceptType = 'application/json'; 
	   $this->rest->verb = 'GET'; 
	   $this->rest->execute(); 
	   $body = $this->rest->responseBody; 
	   return $body; 
   }
   
   function getPobocky()
    {
	 	 $this->rest->url_request = '/v2/branches?shopId='.(int)$this->shopId.'&includeInactive='.(int)$this->inactive; //.','.(int)$this->partner; 
	   $this->rest->acceptType = 'application/json'; 
	   $this->rest->verb = 'GET'; 
	   $this->rest->execute(); 
	   $body = $this->rest->responseBody; 
	   return $body; 
	   
	}
}