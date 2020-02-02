<?php
/*-------------------------------------------------------------------------
# plg_offlajnparams - Offlajn Params
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2016 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined( '_JEXEC' ) or die( 'Restricted access' );

$app = JFactory::getApplication();
if (!$app->isAdmin()) return;

function deleteTmpDir($dirPath) {
  if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') $dirPath .= '/';
  $files = glob($dirPath . '*', GLOB_MARK);
  foreach ($files as $file) {
    is_dir($file) ? deleteTmpDir($file) : unlink($file);
  }
  rmdir($dirPath);
}

$get = JRequest::get('GET');
$files = JRequest::get('FILES');

// create redirect
unset($get['task']);
$redirect = 'index.php?'.http_build_query($get);
// file extension check
if (isset($files['offlajnimport']) && preg_match('/\.zip$/i', $files['offlajnimport']['name'])) {

	// create tmp folder
	$tmp = tempnam(sys_get_temp_dir(), '');
	if (file_exists($tmp)) unlink($tmp);
	mkdir($tmp);

	// unzip to tmp folder
	jimport('joomla.filesystem.archive');
	$zip = JArchive::getAdapter('zip');
	$zip->extract($files['offlajnimport']['tmp_name'], $tmp);

	if (file_exists($tmp.'/module.json')) {

		// add images
		$images = array();
		if (file_exists($tmp.'/images.json')) {
			$images = json_decode( file_get_contents($tmp.'/images.json') );
			foreach ($images as $img) {
				$from = $tmp.'/images/'.$img->file;
				$to = JPATH_SITE.$img->path.$img->file;
				if (preg_match('/\.(png|bmp|gif|jpg)$/i', $to) && !preg_match('/[\/\\]\.\.+[\/\\]/'))
					file_exists($to) or rename($from, $to);
			}
		}
		// get new params
		$params = json_decode(file_get_contents($tmp.'/module.json'), true);
		if ($params) {
			// get current params
			$db = JFactory::getDbo();
			$id = (int) $get['id'];
			$db->setQuery("SELECT id, params FROM #__modules WHERE id = $id LIMIT 1");
			$module = $db->loadObject();
			if ($module && @$module->id > 0) {
				// override with new params
				$p = json_decode($module->params, true);
				if (!is_array($p)) $p = array();
				foreach ($params as $tabName => $tab) {
					if (is_array($tab)) {
						if (!isset($p[$tabName])) $p[$tabName] = array();
						foreach ($tab as $param => $val) {
							if (is_array($val)) {
								if (!isset($p[$tabName][$param])) $p[$tabName][$param] = array();
								foreach ($val as $key => $value) {
									$p[$tabName][$param][$key] = $value;
								}
							} else {
								if ($param == 'custom_css') // custom CSS fix
									$val = preg_replace('/([#\.]\S+?)'.$params['originalId'].'/', '${1}'.$module->id, $val);
								$p[$tabName][$param] = $val;
							}
						}
					}
				}
				// update img paths
				$root = JURI::root();
				foreach ($images as $img) {
					// combine check
					$img->param = explode('[', $img->param);
					$index = isset($img->param[1]) ? (int) $img->param[1] : 0;
					$img->param = $img->param[0];
					// update img param
					$ip = explode('/', $img->param);
					$imgparam = count($ip) == 2 ? $p[ $ip[0] ][ $ip[1] ] : $p[ $ip[0] ][ $ip[1] ][ $ip[2] ];
					$parts = explode('|*|', $imgparam);
					$parts[$index] = preg_replace('/([^:])\/\/+/', '$1/', $root . $img->path . $img->file);
					count($ip) == 2 ? ($p[ $ip[0] ][ $ip[1] ] = implode('|*|', $parts)) : ($p[ $ip[0] ][ $ip[1] ][ $ip[2] ] = implode('|*|', $parts));
				}
				// update params
				$module->params = json_encode($p);
				$module->showtitle = (int) $params['showtitle'];
				$res = $db->updateObject('#__modules', $module, 'id');

				if ($res) {
					$app->enqueueMessage(JText::_('Module successfully imported'));
				} else $app->enqueueMessage(JText::_('Database error during update'), 'warning');
			} else $app->enqueueMessage(JText::_('Module not found, please first save the module'), 'warning');
		} else $app->enqueueMessage(JText::_('Corrupt file: module.json'), 'warning');
	} else $app->enqueueMessage(JText::_('File not found: module.json'), 'warning');
	deleteTmpDir($tmp);
} else $app->enqueueMessage(JText::_('Invalid file extension'), 'warning');

$app->redirect($redirect);