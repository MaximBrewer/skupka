<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


/**
 * Default elFinder connector
 *
 * @author Dmitry (dio) Levashov
 **/
class elFinderConnectorUG {
	/**
	 * elFinder instance
	 *
	 * @var elFinder
	 **/
	protected $elFinder;
	
	/**
	 * Options
	 *
	 * @var aray
	 **/
	protected $options = array();
	
	/**
	 * undocumented class variable
	 *
	 * @var string
	 **/
	protected $header = 'Content-Type: application/json';
	
	
	/**
	 * Constructor
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function __construct($elFinder, $debug=false) {
		
		$this->elFinder = $elFinder;
		if ($debug) {
			$this->header = 'Content-Type: text/html; charset=utf-8';
		}
	}
	
	/**
	 * Execute elFinder command and output result
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function run() {
		$isPost = $_SERVER["REQUEST_METHOD"] == 'POST';
		$src    = $_SERVER["REQUEST_METHOD"] == 'POST' ? $_POST : $_GET;
		$cmd    = isset($src['cmd']) ? $src['cmd'] : '';
		$args   = array();
		
		if (!function_exists('json_encode')) {
			$error = $this->elFinder->error(elFinderUG::ERROR_CONF, elFinderUG::ERROR_CONF_NO_JSON);
			$this->output(array('error' => '{"error":["'.implode('","', $error).'"]}', 'raw' => true));
		}
		
		if (!$this->elFinder->loaded()) {
			$this->output(array('error' => $this->elFinder->error(elFinderUG::ERROR_CONF, elFinderUG::ERROR_CONF_NO_VOL), 'debug' => $this->elFinder->mountErrors));
		}
		
		// telepat_mode: on
		if (!$cmd && $isPost) {
			$this->output(array('error' => $this->elFinder->error(elFinderUG::ERROR_UPLOAD, elFinderUG::ERROR_UPLOAD_TOTAL_SIZE), 'header' => 'Content-Type: text/html'));
		}
		// telepat_mode: off
		
		if (!$this->elFinder->commandExists($cmd)) {
			$this->output(array('error' => $this->elFinder->error(elFinderUG::ERROR_UNKNOWN_CMD)));
		}
		
		// collect required arguments to exec command
		foreach ($this->elFinder->commandArgsList($cmd) as $name => $req) {
			$arg = $name == 'FILES' 
				? $_FILES 
				: (isset($src[$name]) ? $src[$name] : '');
				
			if (!is_array($arg)) {
				$arg = trim($arg);
			}
			if ($req && (!isset($arg) || $arg === '')) {
				$this->output(array('error' => $this->elFinder->error(elFinderUG::ERROR_INV_PARAMS, $cmd)));
			}
			$args[$name] = $arg;
		}
		
		$args['debug'] = isset($src['debug']) ? !!$src['debug'] : false;
		
		$result = $this->elFinder->exec($cmd, $this->input_filter($args));
				
		$this->output($result);
	}
	
	/**
	 * Output json
	 *
	 * @param  array  data to output
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function output(array $data) {
					
		
		$header = isset($data['header']) ? $data['header'] : $this->header;
		unset($data['header']);
		if ($header) {
			if (is_array($header)) {
				foreach ($header as $h) {
					header($h);
				}
			} else {
				header($header);
			}
		}
		
		if (isset($data['pointer'])) {
			rewind($data['pointer']);
			fpassthru($data['pointer']);
			if (!empty($data['volume'])) {
				$data['volume']->close($data['pointer'], $data['info']['hash']);
			}
			exit();
		} else {
			if (!empty($data['raw']) && !empty($data['error'])) {
				exit($data['error']);
			} else {
				$json = json_encode($data);
				
				if(empty($json) && !empty($data)){
					
					$error = json_last_error();
					echo "json encode error";
					
				switch ($error) {
						case JSON_ERROR_DEPTH:
							echo ' - Maximum stack depth exceeded';
						break;
						case JSON_ERROR_STATE_MISMATCH:
							echo ' - Underflow or the modes mismatch';
						break;
						case JSON_ERROR_CTRL_CHAR:
							echo ' - Unexpected control character found';
						break;
						case JSON_ERROR_SYNTAX:
							echo ' - Syntax error, malformed JSON';
						break;
						case JSON_ERROR_UTF8:
							echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
						break;
						default:
							echo ' - Unknown error';
						break;
					}					
				}
				
				
				echo $json;
				exit();
				//exit(json_encode($data));
			}
		}
		
	}
	
	/**
	 * Remove null & stripslashes applies on "magic_quotes_gpc"
	 * 
	 * @param  mixed  $args
	 * @return mixed
	 * @author Naoki Sawada
	 */
	private function input_filter($args) {
		static $magic_quotes_gpc = NULL;
		
		if ($magic_quotes_gpc === NULL)
			$magic_quotes_gpc = (version_compare(PHP_VERSION, '5.4', '<') && get_magic_quotes_gpc());
		
		if (is_array($args)) {
			return array_map(array(& $this, 'input_filter'), $args);
		}
		$res = str_replace("\0", '', $args);
		$magic_quotes_gpc && ($res = stripslashes($res));
		return $res;
	}
}// END class 
