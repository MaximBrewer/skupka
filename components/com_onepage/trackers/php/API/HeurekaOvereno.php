<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
* 
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * Heureka Overeno service
 *
 * Provides access to Heureka Overeno service.
 * 
 * <code>  
 * try {
 *     $overeno = new HeurekaOvereno('API_KEY');
 *     // SK shops should use $overeno = new HeurekaOvereno('API_KEY', HeurekaOvereno::LANGUAGE_SK);
 *     // add product using name
 *     $overeno->addProduct('Nokia N95');
 *     // and/or add product using itemId 
 *     $overeno->addProductItemId('B1234');
 *     // send request
 *     $overeno->send();
 * } catch (HeurekaOverenoException $e) {
 *     // error should be handled
 *     print $e->getMessage();
 * }
 * </code> 
 * 
 * @author Heureka.cz <podpora@heureka.cz>
 */
class OPCHeurekaOvereno
{
    /**
     * Heureka endpoint URL
     *
     * @var string     
     */
    const BASE_URL = 'http://www.heureka.cz/direct/dotaznik/objednavka.php';
    const BASE_URL_SK = 'http://www.heureka.sk/direct/dotaznik/objednavka.php';

    /**
     * Language IDs
     *
     * @var int     
     */
    const LANGUAGE_CZ = 1;
    const LANGUAGE_SK = 2;

    /**
     * Valid response value
     *
     * @var string     
     */
    const RESPONSE_OK = 'ok';

    /**
     * Shop API key
     *
     * @var string     
     */
    private $apiKey;

    /**
     * Customer email
     *
     * @var string     
     */
    private $email;

    /**
     * Ordered products
     *
     * @var array     
     */
    private $products = array();

    /**
     * Order ID
     *
     * @var int    
     */
    private $orderId;

    /**
     * Current language identifier
     *
     * @var int     
     */
    private $languageId = 1;

    /**
     * Ordered products provided using item ID
     * 
     * @var array
     */
    private $productsItemId = array();

    /**
     * Initialize Heureka Overeno service 
     *
     * @param string $apiKey Shop API key
     * @param int $languageId Language version settings
     */
    public function __construct($apiKey, $languageId = self::LANGUAGE_CZ)
    {
        $this->setApiKey($apiKey);
        $this->languageId = $languageId;
    }
    
    /**
     * Sets API key and check well-formedness
     * 
     * @param string $apiKey Shop api key
     */
    public function setApiKey($apiKey)
    {
    //    if (preg_match('(^[0-9abcdef]{32}$)', $apiKey)) 
            $this->apiKey = $apiKey;
        /*
	{

        } else {
            throw new OverflowException('Api key ' . $apiKey . ' is invalid.');
        }
	*/
    }

    /**
     * Sets customer email
     *
     * @param string $email Customer email address
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Adds ordered products using name
     * 
     * Products names should be provided in UTF-8 encoding. The service can handle
     * WINDOWS-1250 and ISO-8859-2 if necessary
     *
     * @param string $productName Ordered product name
     */
    public function addProduct($productName)
    {
        $this->products[] = $productName;
    }

    /**
     * Adds order ID
     * 
     * @param int Order ID
     */
    public function addOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Adds ordered products using item ID
     *
     * @param string $itemId Ordered product item ID
     */
    public function addProductItemId($itemId)
    {
        $this->productsItemId[] = $itemId;
    }

    /**
     * Creates HTTP request and returns response body
     * 
     * @param string $url URL
     * @return string Response body
     */
    private function sendRequest($url)
    {
	    if (!function_exists('fsockopen')) 
		 {
		   if (class_exists('OPCloader'))
		    {
			  return OPCloader::fetchUrl($url); 
			}
			return false; 
		 }
        $parsed = parse_url($url);
        $fp = fsockopen($parsed['host'], 80, $errno, $errstr, 5);
        if (!$fp) {
            //throw new OPCHeurekaOverenoException($errstr . ' (' . $errno . ')');
	    return false;
        } else {
            $return = '';
            $out = "GET " . $parsed['path'] . "?" . $parsed['query'] . " HTTP/1.1\r\n" .
                    "Host: " . $parsed['host'] . "\r\n" .
                    "Connection: Close\r\n\r\n";
			//stAn - protect agains slow server:
            $start = microtime(true);
			fputs($fp, $out);
            while (!feof($fp)) {
                $return .= fgets($fp, 128);
				$now = microtime(true); 
				if (($now - $start) > 10) break; 
            }
            fclose($fp);
            $returnParsed = explode("\r\n\r\n", $return);

            return empty($returnParsed[1]) ? '' : trim($returnParsed[1]);
        }
    }

    /**
     * Returns domain for given language version
     *
     * @return String 
     */
    private function getUrl()
    {
        return self::LANGUAGE_CZ == (int) $this->languageId ? self::BASE_URL : self::BASE_URL_SK;
    }

    /**
     * Sends request to Heureka Overeno service and checks for valid response
     * 
     * @return boolean true
     */
    public function send()
    {
        if (empty($this->email)) {
	    return false; 
            throw new OPCHeurekaOverenoException('Customer email address not set');
        }

        // create URL
        $url = $this->getUrl() . '?id=' . $this->apiKey . '&email=' . urlencode($this->email);
        foreach ($this->products as $product) {
            $url .= '&produkt[]=' . urlencode($product);
        }
        foreach ($this->productsItemId as $itemId) {
            $url .= '&itemId[]=' . urlencode($itemId);
        }

        // add order ID
        if (isset($this->orderId)) {
            $url .= '&orderid=' . urlencode($this->orderId);
        }

        // send request and check for valid response
        $contents = $this->sendRequest($url);
        if ($contents == FALSE) {
	    return false; 
            throw new OPCHeurekaOverenoException('Unable to create HTTP request to Heureka Overeno service');
        } elseif ($contents == self::RESPONSE_OK) {
            return TRUE;
        } else {
	    return false; 
            throw new OPCHeurekaOverenoException($contents);
        }
    }

}

/**
 * Thrown when an service returns an exception
 */
class OPCHeurekaOverenoException extends Exception {};