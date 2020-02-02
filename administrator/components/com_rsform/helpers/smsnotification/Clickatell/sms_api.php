<?php
/**
 * CLICKATELL SMS API
 *
 * This class is meant to send SMS messages via the Clickatell gateway
 * and provides support to authenticate to this service and also query
 * for the current account balance. This class use the fopen or CURL module
 * to communicate with the gateway via HTTP/S.
 *
 * For more information about CLICKATELL service visit http://www.clickatell.com
 *
 * @version   1.3d
 * @package   sms_api
 * @author    Aleksandar Markovic <mikikg@gmail.com>
 * @copyright Copyright � 2004, 2005 Aleksandar Markovic
 * @link      http://sourceforge.net/projects/sms-api/ SMS-API Sourceforge project page
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

class ClickatellHttp
{

	protected $api_id;
	protected $user;
	protected $password;
	protected $use_ssl = false;
	protected $balance_limit = 0;
	protected $sending_method = "fopen";
	protected $curl_use_proxy = false;
	protected $curl_proxy = "http://127.0.0.1:8080";
	protected $curl_proxyuserpwd = "login:secretpass";
	protected $callback = 0;
	protected $session;

	function __construct($api_id, $user, $password, $use_ssl)
	{
		$this->use_ssl = $use_ssl;

		if ($this->use_ssl)
		{
			$this->base   = "http://api.clickatell.com/http";
			$this->base_s = "https://api.clickatell.com/http";
		}
		else
		{
			$this->base   = "http://api.clickatell.com/http";
			$this->base_s = $this->base;
		}


		$this->api_id   = $api_id;
		$this->user     = $user;
		$this->password = $password;

		$this->_auth();
	}

	/**
	 * Authenticate SMS gateway
	 *
	 * @return mixed  "OK" or script die
	 * @access private
	 */
	function _auth()
	{
		$comm          = sprintf("%s/auth?api_id=%s&user=%s&password=%s", $this->base_s, $this->api_id, $this->user, $this->password);
		$this->session = $this->_parse_auth($this->_execgw($comm));
	}

	/**
	 * Query SMS credis balance
	 *
	 * @return integer  number of SMS credits
	 * @access public
	 */
	function getbalance()
	{
		$comm = sprintf("%s/getbalance?session_id=%s", $this->base, $this->session);

		return $this->_parse_getbalance($this->_execgw($comm));
	}

	/**
	 * Send SMS message
	 *
	 * @param to   mixed  The destination address.
	 * @param from mixed  The source/sender address
	 * @param text mixed  The text content of the message
	 *
	 * @throws Exception
	 * @return mixed  "OK" or script die
	 * @access public
	 */
	function send($to = null, $from = null, $text = null)
	{
		/* Check SMS credits balance */
		if ($this->getbalance() < $this->balance_limit)
		{
			throw new Exception(JText::_('RSFP_SMSNOTIFICATION_ERROR_CREDIT'));
		};

		/* Check SMS $text length */
		if (strlen($text) > 459)
		{
			throw new Exception(JText::_('RSFP_SMSNOTIFICATION_ERROR_LIMIT_CHARS'));
		}

		/* Does message need to be concatenate */
		if (strlen($text) > 160)
		{
			$concat = "&concat=3";
		}
		else
		{
			$concat = "";
		}

		/* Check $to and $from is not empty */
		if (empty ($to))
		{
			throw new Exception(JText::_('RSFP_SMSNOTIFICATION_ERROR_INVALID_TO'));
		}
		if (empty ($from))
		{
			throw new Exception(JText::_('RSFP_SMSNOTIFICATION_ERROR_INVALID_FROM'));
		}

		/* Reformat $to number */
		$cleanup_chr = array("+", " ", "(", ")", "\r", "\n", "\r\n");
		$to          = str_replace($cleanup_chr, "", $to);

		$url = '%s/sendmsg?session_id=%s&to=%s&from=%s&text=%s&callback=%s%s';
		if ((bool) RSFormProHelper::getConfig('smsnotification.clickatellmo'))
		{
			$url = '%s/sendmsg?session_id=%s&to=%s&from=%s&mo=1&text=%s&callback=%s%s';
		}

		/* Send SMS now */
		$comm = sprintf($url,
			$this->base,
			$this->session,
			rawurlencode($to),
			rawurlencode($from),
			rawurlencode($text),
			$this->callback,
			$concat
		);

		return $this->_parse_send($this->_execgw($comm));
	}

	/**
	 * Execute gateway commands
	 *
	 * @access private
	 */
	function _execgw($command)
	{
		if ($this->sending_method == "curl")
		{
			return $this->_curl($command);
		}
		if ($this->sending_method == "fopen")
		{
			return $this->_fopen($command);
		}
		throw new Exception(JText::_('RSFP_SMSNOTIFICATION_UNSUPPORTED_SENDING'));
	}

	/**
	 * CURL sending method
	 *
	 * @access private
	 */
	function _curl($command)
	{
		$this->_chk_curl();
		$ch = curl_init($command);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		if ($this->curl_use_proxy)
		{
			curl_setopt($ch, CURLOPT_PROXY, $this->curl_proxy);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->curl_proxyuserpwd);
		}
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	/**
	 * fopen sending method
	 *
	 * @access private
	 */
	function _fopen($command)
	{
		$result  = '';
		$handler = @fopen($command, 'r');
		if ($handler)
		{
			while ($line = @fgets($handler, 1024))
			{
				$result .= $line;
			}
			fclose($handler);

			return $result;
		}
		else
		{
			die ("Error while executing fopen sending method!<br>Please check does PHP have OpenSSL support and check does PHP version is greater than 4.3.0.");
		}
	}

	/**
	 * Parse authentication command response text
	 *
	 * @access private
	 */
	function _parse_auth($result)
	{
		$session = substr($result, 4);
		$code    = substr($result, 0, 2);
		if ($code != "OK")
		{
			throw new Exception($result);
		}

		return $session;
	}

	/**
	 * Parse send command response text
	 *
	 * @access private
	 */
	function _parse_send($result)
	{
		$code = substr($result, 0, 2);
		if ($code != "ID")
		{
			throw new Exception($result);
		}

		return $code;
	}

	/**
	 * Parse getbalance command response text
	 *
	 * @access private
	 */
	function _parse_getbalance($result)
	{
		$result = substr($result, 8);

		return (int) $result;
	}

	/**
	 * Check for CURL PHP module
	 *
	 * @access private
	 */
	function _chk_curl()
	{
		if (!extension_loaded('curl'))
		{
			throw new Exception(JText::_('RSFP_SMSNOTIFICATION_UNSUPPORTED_CURL'));
		}
	}
}
