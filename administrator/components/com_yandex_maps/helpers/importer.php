<?php
defined("_JEXEC") or die("Access deny");
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
include_once dirname(__FILE__).'/csv.io.class.php';
class yandex_maps_import {
	static function isValidJSON() {
		call_user_func_array('json_decode',func_get_args());
		return (json_last_error()===JSON_ERROR_NONE);
	}
	static function importSettings($contents) {
		if (self::isValidJSON($contents)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__extensions AS a');
			$query->set('a.params = ' . $db->quote((string)$contents));
			$query->where('a.element = "com_yandex_maps"');
			$db->setQuery($query);
			$db->query();
			echo '<div class="alert"><strong>Настройки</strong> обновлены</div>';
		} else {
			echo '<div class="alert alert-danger"><strong>Ошибка</strong> не валидный файл настроек</div>';
		}
	}
	static function importTable($table, $file_stream, $params) {
		$csv = new csv();
		$items = $csv->read(false, $file_stream);
		
		$db = JFactory::getDbo();
		$partion = 1000; $query = null;
		if ((int)$params['mode']===-1) {
			JFactory::getDbo()->setQuery('truncate '.$db->quoteName('#__yandex_maps_'.$table))->execute();
		}
		$values_count = 0; $all_count = 0;
		foreach ($items as $i=>$item) {
			if ($i===0 || ($i % $partion === 0)) {
				if ($query and $values_count) {
					$values_count = 0;
					$db->setQuery($query);
					$db->execute();
					unset($query);
				}
				$query = $db->getQuery(true);
				$query
					->insert($db->quoteName('#__yandex_maps_'.$table));
			}

			switch ((int)$params['mode']) {
				case -1:break;
				case 0:
					$value[0] = null;
				break;	
				case 1:break;	
				case 2:
					if ($id = JFactory::getDbo()->setQuery('select id from '.$db->quoteName('#__yandex_maps_'.$table).' where id='.(int)$item[0].' limit 1')->loadResult()) {
						continue 2;
					}
				break;	
			}		
			foreach ($item as &$value) {
				if ($value!==null) {
					$value = $db->quote($value);
				}
			}
			$query
				->values(implode(',', $item));
			$all_count++;
			$values_count++;
		}

		if ($query and $values_count) {
			$db->setQuery($query);
			$db->execute();
			unset($query);
		}
		echo '<div class="alert"><strong>'.$table.'</strong> Вставленных записей: '.$all_count.'</div>';
		unset($items);
	}
	static function start($params) {
		set_time_limit(0);
		$path = JPATH_ROOT.'/tmp';
		if (!is_dir($path)) {
			JFolder::create($path, '0755');
		}
		$app = JFactory::getApplication();
		$input = $app->input;
		$_file = $input->files->get('jform', array(), 'raw');
		
		if ($_file and count($_file['import'])) {
			$file = $_file['import']['file'];
			if ($file and !$file['error']) {
				$extension = JFile::getExt($file['name']);
				$randname = md5($file['name'].rand(100, 200).rand(100, 200).rand(100, 200)).'.'.$extension;

				if (JFile::upload($file['tmp_name'], $path .'/'. $randname, false, true)) {
					switch ($extension) {
						case 'dat':
							$data = explode('#$#$|', file_get_contents($path .'/'. $randname));
							foreach($data as $_item) {
								if (preg_match('/(maps|categories|objects|settings|category_to_map|object_to_category|organizations)\|\$#\$#(.*)$/ism', $_item, $item)) {
									if ((int)$params[$item[1]]) {
										if ($item[1]=='settings') {
											self::importSettings($item[2]);
										} else {
											file_put_contents($path.'/data.csv', $item[2]);
											self::importTable($item[1], fopen($path.'/data.csv', "r"), $params);
										}
									}
								}
							}
						break;
						case 'zip':
							if (class_exists('ZipArchive')) {
								$zip = new ZipArchive;
								$res = $zip->open($path .'/'. $randname);
								if ($res) {
									for ($i = 0; $i < $zip->numFiles; $i++) {
										$filename = $zip->getNameIndex($i);
										if (preg_match('#(maps|categories|objects|settings|category_to_map|object_to_category|organizations)\.(csv|json)#', $filename, $list)) {
											$fp = $zip->getStream($filename);
											if ((int)$params[$list[1]]) {
												if ($list[2]=='csv') {
													self::importTable($list[1], $fp, $params);
												} else {
													$contents = '';
													while (!feof($fp)) {
														$contents .= fread($fp, 2);
													}
													self::importSettings($contents);	
												}
											}
										}
									}
								} else {
									echo '<div class="alert alert-error">Архив не может быть распакован</div>';
								}
							} else {
								echo '<div class="alert alert-error">Расширение ZipArchive не установлена</div>';
							}
						break;
					}
				} else {
					echo '<div class="alert alert-error">Загрузка файла не удалась</div>';
				}
			} else {
				echo '<div class="alert alert-error">Загрузка файла не удалась</div>';
			}
		} else {
			echo '<div class="alert alert-error">Загрузка файла не удалась</div>';
		}
	}
}