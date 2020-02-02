<?php
class configHelper{
	static public $configuration = array();
	static function addParams($params, $i) {
		if ($params) {
			self::$configuration[$i] = clone($params);
		}
	}
	static function get($name, $default = null) {
		$result = null;
		foreach(self::$configuration as $params) {
			if ($params->get($name)!=null) {
				$result = $params->get($name);
			}
		}
		return $result === null ? $default : $result;
	}
}