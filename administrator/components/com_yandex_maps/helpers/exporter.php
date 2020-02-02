<?php
defined("_JEXEC") or die("Access deny");
jimport('joomla.filesystem.folder');
include_once dirname(__FILE__).'/csv.io.class.php';
class yandex_maps_export {
	static function exportTable($table, $path) {
		$db = JFactory::getDbo();
		$items = $db->setQuery('select * from #__yandex_maps_'.$table)->loadRowList();
		foreach ($items as $item) {
			foreach ($item as &$value) {
				$value = utf8_decode($value);
			}
		}
		$csv = new csv();
		$csv->write($path.'/'.$table.'.csv', $items);
	}
	static function start($params) {
		set_time_limit(0);
		$path = dirname(__FILE__).'/tmp';
		if (!is_dir($path)) {
			JFolder::create($path, '0755');
		}
		$res = false; $zip = null;
		if (class_exists('ZipArchive')) {
			$zip = new ZipArchive;
			$res = $zip->open($path.'/.exportzip', ZipArchive::CREATE);
		}

		$tables = array('maps', 'categories', 'objects', 'organizations', 'category_to_map', 'object_to_category');
		
		foreach ($tables as $table) {
			if ((int)$params[$table]) {
				self::exportTable($table, $path);
				if ($res) {
					$zip->addFile($path.'/'.$table.'.csv', $table.'.csv');
				} else {
					file_put_contents($path.'/.exportdat', '#$#$|'.$table.'|$#$#'.file_get_contents($path.'/'.$table.'.csv'), FILE_APPEND);
				}
			}
		}
		if ((int)$params['settings']) {
			$setting = JComponentHelper::getParams('com_yandex_maps')->toString();
			if ($res) {
				$zip->addFromString('settings.json', $setting);
			} else {
				file_put_contents($path.'/.exportdat', '#$#$|settings|$#$#'.$setting, FILE_APPEND);
			}
		}
		if ($res && $zip) {
			//$zip->setPassword('klintistvud');
			$zip->close();
		}

		//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: " . filesize($path.'/.export'.($res ? 'zip' : 'dat')));
		header("Content-type: application/".($res ? 'zip' : 'octet-stream'));
		header("Content-Disposition: attachment; filename=export.yandex.maps-".date('d.m.Y-H-i').".".($res ? 'zip' : 'dat'));
		header('Content-Transfer-Encoding: binary');
		//header('Expires: 0');
		if ($fd = fopen($path.'/.export'.($res ? 'zip' : 'dat'), 'rb')) {
			while (!feof($fd)) {
				echo fread($fd, 1024);
			}
			fclose($fd);
		}
		@unlink($path.'/.export'.($res ? 'zip' : 'dat'));
		@unlink($path.'/maps.csv');
		@unlink($path.'/categories.csv');
		@unlink($path.'/objects.csv');
		exit;
	}
}