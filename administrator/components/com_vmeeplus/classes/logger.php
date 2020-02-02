<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

jimport('joomla.error.log');

class emp_logger{
	static $log;

	const LEVEL_DEBUG = 'debug';
	const LEVEL_INFO = 'info';
	const LEVEL_WARNING = 'warning';
	const LEVEL_ERROR = 'error';

	/**
	 * @desc The function will add a line to the log file 'vmeepro.log.php' which is under $log_path or $tmp_path as defined in Joomla's configuration.php
	 * The function will write to the log only if the "is debug" configuration parameter in vmeepro is set to "yes".
	 * If the level is emp_logger::LEVEL_ERROR, the log line will be written regardless of the "is debug" settings.
	 * @param string $msg
	 * @param string $level - one of: emp_logger::LEVEL_ERROR, emp_logger::LEVEL_WARNING, emp_logger::LEVEL_INFO, emp_logger::LEVEL_ERROR, emp_logger::LEVEL_DEBUG
	 * @param string | array $moreData - more data to write to log
	 * @param int $order_id
	 * @param int $user_id
	 */
	static public function log($msg, $level, $moreData = null, $order_id=null, $user_id=null){
		if(emp_helper::isDemo()){
			return;
		}

		$isDebug = emp_helper::getGlobalParam('isDebug');
		if(version_compare(JVERSION,'1.7.0','ge')) {
			$options = array(
					'text_file' => 'vmeeplus.log.php',
					'text_entry_format' => "{DATE} {TIME} {LEVEL} {USER_ID} {ORDER_ID} {MESSAGE} {DATA} {FILE} {LINE}"
			);
			jimport('joomla.log.log'); // Include the log library (J1.7+)
			JLog::addLogger($options,JLog::ALL,'com_vmeeplus');
		} else {
			// Joomla! 1.6 and 1.5
			if(self::$log == null){
				$options = array(
						'format' => "{DATE}\t{TIME}\t{LEVEL}\t{USER_ID}\t{ORDER_ID}\t{COMMENT}\t{DATA}\t{FILE}\t{LINE}"
				);
				jimport('joomla.error.log'); // Include the log library
				self::$log = JLogLogger('vmeeplus.log.php', $options);
			}

		}
		/*ob_start();
		 var_dump($args);
		$argsstr = ob_get_clean();
		*/
		if($level != self::LEVEL_ERROR){
			if($isDebug){
				self::writeMessage($msg, $level,$moreData,$order_id, $user_id);
			}
		}
		elseif($level == self::LEVEL_ERROR){
			self::writeMessage($msg, $level,$moreData,$order_id, $user_id);
		}
	}

	static private function writeMessage($msg, $level, $moreData ,$order_id, $user_id){
		$data = null;
		if(!empty($moreData)){
			if(!is_array($moreData)){
				$data = (string)$moreData;
			}
			else{
				//$data = implode(',', $moreData);
				$data = self::prepareData($moreData);
			}
		}
		$location = self::locateCall();
		if(version_compare(JVERSION,'1.7.0','ge')) {
			$logEntry = new JLogEntry($msg, JLog::INFO, 'com_vmeeplus');
			$logEntry->level = $level;
			$logEntry->user_id = $user_id;
			$logEntry->order_id = $order_id;
			$logEntry->data = $data;
			$logEntry->file = $location['file'];
			$logEntry->line = $location['line'];
			JLog::add($logEntry);
		}
		else{
			self::$log->addEntry(array(
					"level" => $level,
					"user_id" => $user_id,
					"order_id" => $order_id,
					"comment" => $msg,
					"data" => $data,
					"file" => $location['file'],
					"line" => $location['line']
			));
		}
	}

	static private function locateCall(){
		$trace = debug_backtrace();
		$file   = $trace[2]['file'];
		$line   = $trace[2]['line'];
		return array('file' => $file, 'line' => $line);
	}

	/**
	 *
	 * @param array $data
	 */
	static function prepareData($data){
		$res = "";
		foreach ($data as $dataPart){
			$res .= '(';
			if(!is_array($dataPart) && (!is_object($dataPart) && !is_a($dataPart, 'stdClass'))){
				$res .= (string)$dataPart;
			}
			else{
				$res .= self::prepareData($dataPart);
			}
			$res .= ')';
		}
		return $res;
	}

}