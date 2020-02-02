<?php
//https://web.archive.org/web/20130323001500/http://www.gen-x-design.com/archives/making-restful-requests-in-php/
class RestRequestUlozenka
{
	protected $url;
	protected $url_request;
	protected $verb;
	protected $requestBody;
	protected $requestLength;
	protected $username;
	protected $password;
	protected $acceptType;
	protected $responseBody;
	protected $responseInfo;
	protected $extraHeader; 
	public function __construct ($url = null, $verb = 'GET', $requestBody = null, $url_request='')
	{
		$this->url				= $url;
		//$this->url = 'api.ulozenka.cz/v3'; 
		$this->url_request 		= $url_request; 
		$this->verb				= $verb;
		$this->requestBody		= $requestBody;
		$this->requestLength	= 0;
		$this->username			= null;
		$this->password			= null;
		$this->acceptType		= 'application/json';
		$this->responseBody		= null;
		$this->responseInfo		= null;
		
		
		
		if ($this->requestBody !== null)
		{
			$this->buildPostBody();
		}
	}
	
	public function flush ()
	{
		$this->requestBody		= null;
		$this->requestLength	= 0;
		$this->verb				= 'GET';
		$this->responseBody		= null;
		$this->responseInfo		= null;
	}
	
	public function execute ()
	{
		$ch = curl_init();
		$this->setAuth($ch);
		
		try
		{
			switch (strtoupper($this->verb))
			{
				case 'GET':
					$this->executeGet($ch);
					break;
				case 'POST':
					$this->executePost($ch);
					break;
				case 'PUT':
					$this->executePut($ch);
					break;
				case 'DELETE':
					$this->executeDelete($ch);
					break;
				default:
					throw new InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
			}
		}
		catch (InvalidArgumentException $e)
		{
			curl_close($ch);
			throw $e;
		}
		catch (Exception $e)
		{
			curl_close($ch);
			throw $e;
		}
		
	}
	
	public function buildPostBody (&$data = null)
	{
		$data = ($data !== null) ? $data : $this->requestBody;
		
		
		
		if (!is_array($data))
		{
			throw new InvalidArgumentException('Invalid data input for postBody.  Array expected');
		}
		
		if (empty($this->extraHeader)) $this->extraHeader = array(); 
		
		if (isset($data['X-Shop']))
		{
			$this->extraHeader['X-Shop'] = $data['X-Shop']; 
		}
		
		if (isset($data['X-Key']))
		{
			$this->extraHeader['X-Key'] = $data['X-Key']; 
		}
		
		//$data = http_build_query($data, '', '&');
		$data = json_encode($data); 
		
	
		$this->requestBody = $data;
	}
	
	protected function executeGet (&$ch)
	{		
		$this->doExecute($ch);	
	}
	
	protected function executePost (&$ch)
	{
		if (!is_string($this->requestBody))
		{
			$this->buildPostBody();
		}
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		$this->doExecute($ch);	
	}
	
	protected function executePut (&$ch)
	{
		if (!is_string($this->requestBody))
		{
			$this->buildPostBody();
		}
		
		$this->requestLength = strlen($this->requestBody);
		
		$fh = fopen('php://memory', 'rw');
		fwrite($fh, $this->requestBody);
		rewind($fh);
		
		curl_setopt($ch, CURLOPT_INFILE, $fh);
		curl_setopt($ch, CURLOPT_INFILESIZE, $this->requestLength);
		curl_setopt($ch, CURLOPT_PUT, true);
		
		$this->doExecute($ch);
		
		fclose($fh);
	}
	
	protected function executeDelete (&$ch)
	{
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		
		$this->doExecute($ch);
	}
	
	protected function doExecute (&$curlHandle)
	{
		
		$this->setCurlOpts($curlHandle);
		$this->responseBody = curl_exec($curlHandle);
		$this->responseInfo	= curl_getinfo($curlHandle);
		
		$this->error = curl_error($curlHandle);
		curl_close($curlHandle);
		
		
	}
	
	protected function setCurlOpts (&$curlHandle)
	{
	   
	    //curl_setopt($curlHandle,CURLOPT_HTTPHEADER, $this->extraHeader);
	
	
	    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
		
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 4000);
		curl_setopt($curlHandle, CURLOPT_URL, $this->url.$this->url_request);
		curl_setopt($curlHandle, CURLOPT_ENCODING , "gzip");
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		/*
		
		*/
		//'Content-Type: application/json; charset=utf-8',
		//'Content-Length: ' . strlen($this->requestBody),
		$extra = array(
		
		'Accept' => $this->acceptType); 
		 if (!empty($this->extraHeader))
		 {
			 foreach ($this->extraHeader as $k=>$v)
			 {
				 $extra[$k] = $v; 
			 }
		 }
		 $c_e = array(); 
		 foreach ($extra as $k=>$v)
		 {
			 $c_e[] = $k.':'.$v; 
		 }
		
		
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $c_e); 
		
		//curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array ());
	}
	
	protected function setAuth (&$curlHandle)
	{
		if ($this->username !== null && $this->password !== null)
		{
			curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($curlHandle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}
	}
	
	public function getAcceptType ()
	{
		return $this->acceptType;
	} 
	
	public function setAcceptType ($acceptType)
	{
		$this->acceptType = $acceptType;
	} 
	
	public function getPassword ()
	{
		return $this->password;
	} 
	
	public function setPassword ($password)
	{
		$this->password = $password;
	} 
	
	public function getResponseBody ()
	{
		return $this->responseBody;
	} 
	
	public function getResponseInfo ()
	{
		return $this->responseInfo;
	} 
	
	public function getUrl ()
	{
		return $this->url;
	} 
	
	public function setUrl ($url)
	{
		$this->url = $url;
	} 
	
	public function getUsername ()
	{
		return $this->username;
	} 
	
	public function setUsername ($username)
	{
		$this->username = $username;
	} 
	
	public function getVerb ()
	{
		return $this->verb;
	} 
	
	public function setVerb ($verb)
	{
		$this->verb = $verb;
	} 
}
