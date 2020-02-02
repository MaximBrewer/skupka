<?php
/**
 * @version       1.0
 * @package       RSform!Pro 1.51.0
 * @copyright (C) 2007-2012 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

/**
 * Class RSFPSmsNotification
 */
class RSFPSmsNotification
{
	/**
	 * The used sms service (Clickatell, Clockwork, Smsglobal, Twilio , Nexmo)
	 */
	protected $type;
	/**
	 * Array that holds the API credentials
	 *
	 * @var array
	 */
	protected $credentials;
	/**
	 *
	 * The actual messages
	 *
	 */
	protected $data;
	/**
	 *
	 * The information from the form submission (placeholders, values, etc)
	 *
	 * @var
	 */
	protected $args;
	/**
	 * @var
	 */
	protected $sender;
	/**
	 * RSFPSmsNotification constructor.
	 *
	 * @param $type
	 * @param $credentials
	 * @param $data
	 * @param $args
	 */
	public function __construct($type, $credentials, $data, $args)
	{
		$this->type        = $type;
		$this->credentials = $credentials;
		$this->args        = $args;
		$this->data        = new JRegistry($data);

		$this->loadService();
	}

	/**
	 * @return array
	 */
	public function sendMessages()
	{
		/**
		 * We create the messages
		 */
		$messages = $this->buildMessage();
		$log      = array();

		/**
		 * if there is no message, we return the empty array so we can handle it in
		 * Plugins/plg_rsfpsmsnotification/rsfpsmsnotification.php
		 */
		if (empty($messages) || $this->type === 'none')
		{
			return $log;
		}

		/**
		 * We have 2 types of SMS messages (ADMIN - USER), and we need to sending/errors for each one
		 */
		foreach ($messages as $message)
		{

			/**
			 * We create a new array that holds the INDIVIDUAL LOG
			 */
			$status = array(
				'type'          => $message['type'],
				'to'            => $message['to'],
				'error_message' => false,
				'status'        => false
			);

			/**
			 * If the SMS Body is present, we try to send it
			 */
			if ($message['message'] !== '')
			{
				try
				{
					/**
					 * send the individual log and message
					 * and catch it by reference so it can
					 * be edited.
					 */
					$this->_sendMessage($status, $message);
				} catch (Exception $e)
				{
					$status['error_message'] = $e->getMessage();
				}

			}
			/**
			 * In case the SMS does not have any text in the body,
			 * we add an error message to the log, which is handled
			 * in Plugins\plg_rsfpsmsnotification\rsfpsmsnotification.php
			 */
			else
			{
				$status['error_message'] = JText::sprintf('RSFP_SMSNOTIFICATION_ERROR_NO_MESSAGES', $message['type']);
			}

			/**
			 * After the process is finished, populate the main log with the individual logs
			 */
			$log[] = $status;
		}

		/**
		 * return it
		 */
		return $log;
	}

	/**
	 * @param $status
	 * @param $message
	 *
	 * @throws Exception
	 */
	protected function _sendMessage(&$status, &$message)
	{

		/**
		 * We need to handle the sending of the messages for all the services.
		 * Both arguments are passed by reference so we can edit the INDIVIDUAL LOG ($status)
		 * and in twilio's case, we need to alter the $message['to'] due to the fact
		 * that it requires a '+' character in front of the number
		 */

		switch ($this->type)
		{
			case 'clockwork':
				$result = $this->sender->send($message);
				if (!empty($result['success']))
				{
					$status['status'] = true;
				}
				else
				{
					$status['error_message'] = $result['error_message'];
				}
				break;
			case 'twilio':
				$message['to'] = '+' . ltrim($message['to'], '+');
				$result        = $this->sender->account->messages->sendMessage(
					$message['from'],
					$message['to'],
					$message['message']
				);

				if (isset($result->sid))
				{
					$status['status'] = true;
				}
				break;
			case 'smsglobal':
				$http = JHttpFactory::getHttp();

				$params = array(
					'action'   => 'sendsms',
					'user'     => rawurlencode($this->credentials['user']),
					'password' => rawurlencode($this->credentials['password']),
					'to'       => rawurlencode($message['to']),
					'from'     => rawurlencode($message['from']),
					'text'     => rawurlencode($message['message'])
				);
				$prefix = 'http';

				if (RSFormProHelper::getConfig('smsnotification.usessl'))
				{
					$prefix = 'https';
				};

				$url              = $prefix . '://www.smsglobal.com.au/http-api.php?' . http_build_query($params);
				$response         = $http->get($url, array(), 3);
				$explode_response = explode('SMSGlobalMsgID:', $response->body);

				$i = 0;
				foreach ($explode_response as $exploded)
				{
					$explode_response[$i] = preg_replace('/\s+/', '', $exploded);
					$i++;
				}

				$explode_response = array_filter($explode_response);

				if ($response->code != 200)
				{
					throw new Exception ('Connection error');
				}

				if (count($explode_response) == 2)
				{
					$status['status'] = true;
				}
				else
				{
					$status['error_message'] = $response->body;
				}
				break;
			/**
			 * Clickatell and Nexmo do not have any other way to check if the SMS
			 * is sent, so we need to catch the error and change the $status['status']
			 * accordingly.
			 */
			case 'clickatell':
				try
				{
					$result = $this->sender->send(
						$message['to'],
						$message['from'],
						$message['message']
					);

				} catch (Exception $e)
				{
					$status['error_message'] = $e->getMessage();
				}

				if (!$status['error_message'])
				{
					$status['status'] = true;
				}
				break;
			case 'nexmo':
				try
				{
					$result = $this->sender->sendText(
						$message['to'],
						$message['from'],
						$message['message']
					);
					if (isset($result->error))
					{
						throw new Exception ($result->error);
					}
				} catch (Exception $e)
				{
					$status['error_message'] = $e->getMessage();
				}

				if (!$status['error_message'])
				{
					$status['status'] = true;
				}
				break;

			case 'mainsmsservis':
				try
				{

					$result = $this->sender->sendSMS(
						$message['to'],
						$message['message'],
						$message['from']
						
					);
                    
                    // $response = $this->sender->getResponse ();
					// var_dump($response);
					// var_dump($message['message']);
					// var_dump($this->sender->getBalance ());
					// var_dump($result);
					// die();
                     

					if (isset($result->error))
					{
						throw new Exception ($result->error);
					}
				} catch (Exception $e)
				{
					$status['error_message'] = $e->getMessage();
				}

				if (!$status['error_message'])
				{
					$status['status'] = true;
				}
				break;


		}

	}

	/**
	 * Check what messages are enabled.
	 *
	 * @return array
	 */
	protected function messagesEnabled()
	{
		return array_filter(array(
			'user_sms'  => $this->data->get('user_sms'),
			'admin_sms' => $this->data->get('admin_sms'),
		));

	}

	/**
	 * Load the service we are going to use, send SMS and return the logs (success/fail)
	 *
	 *
	 * @return array
	 */
	protected function loadService()
	{
		/**
		 * Override the switch statement
		 */
		$enabled = $this->messagesEnabled();

		if (empty($enabled))
		{
			$this->type = 'none';
		}

		/**
		 * The array holds the files needed to be included depending on the services.
		 */
		$services = array(
			'clockwork'  => JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/smsnotification/Clockwork/class-Clockwork.php',
			'twilio'     => JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/smsnotification/Twilio/Twilio.php',
			'clickatell' => JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/smsnotification/Clickatell/sms_api.php',
			'nexmo'      => JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/smsnotification/Nexmo/NexmoMessage.php',
		    'mainsmsservis'      => JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/smsnotification/Mainsmsservis/mainsms.class.php'

		);

		/**
		 * we need to make a new verification here (avoid Illegal Offset error), SMSGlobal does not require any files.
		 */
		if (array_key_exists($this->type, $services))
		{
			require_once $services[$this->type];
		}

		/**
		 * Instantiate the objects
		 */
		switch ($this->type)
		{
			case 'clockwork':
				$options = array();
				if (!RSFormProHelper::getConfig('smsnotification.usessl'))
				{
					$options['ssl'] = false;
				};
				$this->sender = new Clockwork($this->credentials['api_key'], $options);
				break;
			case 'twilio':
				$this->sender = new Services_Twilio($this->credentials['sid'], $this->credentials['token']);
				break;
			case 'clickatell':
				$this->sender = new ClickatellHttp($this->credentials['apiid'], $this->credentials['user'], $this->credentials['password'], (bool) RSFormProHelper::getConfig('smsnotification.usessl'));

				break;
			case 'nexmo':
				$this->sender = new NexmoMessage($this->credentials['key'], $this->credentials['secret']);
				if (RSFormProHelper::getConfig('smsnotification.usessl'))
				{
					$this->sender->ssl_verify = true;
				};
				break;

//			********************новый сервис******************
			case 'mainsmsservis':
				$this->sender = new MainSMS($this->credentials['project'], $this->credentials['key'], false,false);
				break;


			default:
				$this->sender = null;
				break;
		}
	}

	/**
	 * Build the message based on the information from the backend and the values returned from placeholders
	 *
	 * @return array
	 */
	protected function buildMessage()
	{
		$data = $this->data;
		$args = $this->args;

		$messages = array();

		if ((bool) $data->get('admin_sms'))
		{
			$messages[] = array(
				'type'    => 'admin sms',
				'to'      => str_replace($args['placeholders'], $args['values'], RSFormProHelper::htmlEscape($data->get('admin_to'))),
				'from'    => str_replace($args['placeholders'], $args['values'], RSFormProHelper::htmlEscape($data->get('admin_from'))),
				'message' => str_replace($args['placeholders'], $args['values'], RSFormProHelper::htmlEscape($data->get('admin_text'))),
			);
		}

		if ((bool) $data->get('user_sms'))
		{
			$messages[] = array(
				'type'    => 'user sms',
				'to'      => str_replace($args['placeholders'], $args['values'], RSFormProHelper::htmlEscape($data->get('user_to'))),
				'from'    => str_replace($args['placeholders'], $args['values'], RSFormProHelper::htmlEscape($data->get('user_from'))),
				'message' => str_replace($args['placeholders'], $args['values'], RSFormProHelper::htmlEscape($data->get('user_text'))),
			);
		}

		return $messages;
	}

	/**
	 * Creates the $credentials array, used to connect to different api's
	 *
	 * @param $type
	 *
	 * @return array
	 */
	public static function verifyKey($type)
	{
		/**
		 * create a new array that holds the RSForm!Pro Config Input Fields
		 */
		$credentials = array(
			'clockwork'  => array(
				'api_key' => 'smsnotification.clockworkkey',
			),
			'twilio'     => array(
				'sid'   => 'smsnotification.twiliosid',
				'token' => 'smsnotification.twiliotoken'
			),
			'smsglobal'  => array(
				'user'     => 'smsnotification.smsglobaluser',
				'password' => 'smsnotification.smsglobalpassword'
			),
			'clickatell' => array(
				'apiid'    => 'smsnotification.clickatellapiid',
				'user'     => 'smsnotification.clickatellusername',
				'password' => 'smsnotification.clickatellpassword'
			),
			'nexmo'      => array(
				'key'    => 'smsnotification.nexmokey',
				'secret' => 'smsnotification.nexmosecret'
			),
			'mainsmsservis'      => array(
				'project'    => 'smsnotification.mainsmsservisproject',
				'key' => 'smsnotification.mainsmsserviskey'
			)
		);

		$returnArray = array();

		/**
		 * Depending on what service we are using, we add the credentials to the array that will be returned
		 */
		foreach ($credentials[$type] as $property => $value)
		{
			$returnArray[$property] = RSFormProHelper::getConfig($value);
		}

		/**
		 * In case there are input fields that hold no value, we remove them
		 */
		$returnArray = array_filter($returnArray);

		/**
		 * At this current moment, we don't know if the credentials are all defined
		 * so we declare the status as false
		 */
		$returnArray['status'] = false;

		/**
		 * We need to count the elements of the array and see if they match the
		 * initial array. We need to add 1 element to the original array to
		 * compensate for the 'status' element. If they match, we change the status
		 * to 'true'.
		 */
		if (count($returnArray) == (count($credentials[$type]) + 1))
		{
			$returnArray['status'] = true;
		}

		return $returnArray;
	}

}