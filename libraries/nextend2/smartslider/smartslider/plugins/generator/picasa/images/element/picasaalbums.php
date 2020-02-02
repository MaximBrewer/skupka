<?php

N2Loader::import('libraries.form.element.list');

class N2ElementPicasaAlbums extends N2ElementList
{

    function fetchElement() {
        /** @var N2GeneratorInfo $info */
        $info          = $this->_form->get('info');
        $client        = $info->getConfiguration()->getApi();
		if($client->getAuth()->isAccessTokenExpired()){
			N2Message::error('Your API isn\'t set. Please follow the instructions of <a href="https://smartslider3.helpscoutdocs.com/article/1187-picasa-generator" target="_blank">this documentation</a>.');
			return null;
		} else {
			$http = new Google_Http_Request(
				'https://picasaweb.google.com/data/feed/api/user/default',
				'GET',
				array(
					'Content-Type' => 'application/json; charset=UTF-8',
					'Accept' => '*/*'
				),
				null
			);
			$request = $client->getAuth()->authenticatedRequest($http);
		  
			$code = $request->getResponseHttpCode();
			$body = $request->getResponseBody();
			if($code == 403){
				N2Message::error('Your API isn\'t set. Please follow the instructions of <a href="https://smartslider3.helpscoutdocs.com/article/1187-picasa-generator" target="_blank">this documentation</a>.');
			}
			
			$sxml_albumlist = @simplexml_load_string($body);

			$this->_xml->addChild('option', 'Please select')
					   ->addAttribute('value', 0);

			if ($sxml_albumlist === false) {
				N2Message::notice("There are no albums for this user.");
			} else {
				foreach ($sxml_albumlist->entry as $album_list) {
					if (count($album_list)) {
						$value = str_replace("https://picasaweb.google.com/data/entry/api", "", $album_list->id);
						$this->_xml->addChild('option', " - " . $album_list->title)
								   ->addAttribute('value', $value);
					}
				}
			}
			return parent::fetchElement();
		}
    }
}