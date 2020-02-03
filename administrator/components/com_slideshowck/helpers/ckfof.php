<?php
namespace Slideshowck;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use \Slideshowck\CKInput;
use \Slideshowck\CKText;

/**
 * CK Development Framework layer
 */
class CKFof {

	static $keepMessages = false;

	static protected $environment = 'com_slideshowck'; // for joomla only

	static protected $input;

	public static function loadHelper($name) {
		require_once(SLIDESHOWCK_PATH . '/helpers/ck' . $name . '.php');
	}

	public static function getInput() {
		if (empty(self::$input)) {
			self::$input = new CKInput();
		}
		return self::$input;
	}

	public static function userCan($task, $environment = false) {
		$environment = $environment ? $environment : self::$environment;
		$user = Factory::getUser();
		switch ($task) {
			case 'edit' :
			default :
				return $user->authorise('core.edit', $environment);
			break;
			case 'create' :
				return $user->authorise('core.create', $environment);
			break;
			case 'manage' :
				return $user->authorise('core.manage', $environment);
			break;
			case 'admin' :
				return $user->authorise('core.admin', $environment);
			break;
		}
	}

	public static function getUser($id = 0) {
		if ($id) {
			return $user = Factory::getUser($id);
		}
		return $user = Factory::getUser();
	}

	public static function isAdmin() {
		return Factory::getApplication()->isClient('administrator') ;
	}

	public static function isSite() {
		return Factory::getApplication()->isClient('site') ;
	}

	public static function _die($msg = '') {
		$msg = $msg ? $msg : CKText::_('You do not have sufficient permissions to access this page.');
		jexit($msg);
	}

	public static function getCurrentUri() {
		$uri = \JFactory::getURI();
		return $uri->toString();
	}

	public static function redirect($url = '', $msg = '', $type = '') {
		if (! $url) {
			$url = self::getCurrentUri();
		}
		if ($msg) {
			self::enqueueMessage($msg, $type);
		}
		Factory::getApplication()->redirect($url);
		// If the headers have been sent, then we cannot send an additional location header
		// so we will output a javascript redirect statement.
		/*if (headers_sent())
		{
			self::$keepMessages = true;
			echo "<script>document.location.href='" . str_replace("'", '&apos;', $url) . "';</script>\n";
		}
		else
		{
			self::$keepMessages = true;
			// All other browsers, use the more efficient HTTP header method
			header('HTTP/1.1 303 See other');
			header('Location: ' . $url);
			header('Content-Type: text/html; charset=UTF-8');
		}*/
	}

	/**
	 * 
	 * @param type $msg
	 * @param type $type
    'message' (ou vide) - vert
    'notice' - bleu
    'warning' - jaune
    'error' - rouge
	 */
	public static function enqueueMessage($msg, $type = 'message') {
		// add the information message
		Factory::getApplication()->enqueueMessage($msg, $type);
	}

	public static function displayMessages() {
		// manage the information messages
		// not needed in joomla
	}

	public static function getToken($name = '') {
		return \JFactory::getSession()->getFormToken() . '=1';
	}

	public static function renderToken($name = '') {
		echo \JHtml::_('form.token');
	}

	public static function checkToken($token = '') {
		if (! \JSession::checkToken()) {
			$msg = CKText::_('Invalid token');
			jexit($msg);
		}
	}

	public static function checkAjaxToken($token = '') {
		// check the token for security
		if (! \JSession::checkToken('get')) {
			$msg = CKText::_('JINVALID_TOKEN');
			echo '{"result": "0", "message": "' . $msg . '"}';
			return false;
		}
		return true;
	}

	public static function getDbo() {
		return Factory::getDbo();
	}

	public static function dbQuote($name) {
		$db = self::getDbo();
		return $db->quoteName($name);
	}

	public static function dbLoadObjectList($query) {
		$db = self::getDbo();
		// $query = $db->getQuery(true);
		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	public static function dbLoadObject($query) {
		$db = self::getDbo();
		// $query = $db->getQuery(true);
		$db->setQuery($query);
		$results = $db->loadObject();

		return $results;
	}

	public static function dbLoadResult($query) {
		$db = self::getDbo();
		// $query = $db->getQuery(true);
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	public static function dbExecute($query) {
		$db = self::getDbo();
		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	}

	public static function dbLoadColumn($tableName, $column) {
		$db = self::getDbo();
		$query = $db->getQuery(true);
		$query->select($column);
		$query->from($tableName);

		$db->setQuery($query);
		$result = $db->loadColumn();

		return $result;
	}

	public static function dbCheckTableExists($tableName) {
		$db = self::getDbo();
		$tablesList = $db->getTableList();

		$tableName = str_replace('#__', $db->getPrefix(), $tableName);
		$tableExists = in_array($tableName, $tablesList);
		return $tableExists;
	}

	public static function dbLoadTable($tableName) {
		$db = self::getDbo();
		$tableName = self::getTableName($tableName);
		$query = "DESCRIBE  " . $tableName;
		$db->setQuery($query);
		$columns = $db->loadObjectList();

		$table = new \stdClass();
		foreach ($columns as $col) {
			$table->{$col->Field} = '';
		}

		return $table;
	}

	public static function dbLoad($tableName, $id) {
		// if no existing row, then load empty table
		if ($id == 0) return self::dbLoadTable($tableName);

		$db = self::getDbo();
		$query = "SELECT * FROM " . $tableName . " WHERE id = " . (int)$id;
		$db->setQuery($query);
		$result = $db->loadAssoc();

		if (! $result) return self::dbLoadTable($tableName);

		$result = self::convertArrayToObject($result);

		return $result;
	}

	public static function dbBindData($table, $data) {
		if (is_object($table)) $table = self::convertObjectToArray($table);
		if (is_object($data)) $data = self::convertObjectToArray($data);

		foreach ($table as $col => $val) {
			if (isset($data[$col])) $table[$col] = $data[$col];
		}

		return $table;
	}

	public static function getTableName($tableName) {
		return $tableName;
	}

	public static function getTableStructure($tableName) {
		$db = self::getDbo();
		$query = "SHOW COLUMNS FROM " . $tableName;
		$db->setQuery($query);

		return $db->loadObjectList('Field');
	}

	public static function dbStore($tableName, $data, $format = array()) {
		$db = self::getDbo();
		if (is_object($data)) $data = self::convertObjectToArray($data);

		// Create a new query object.
		$query = $db->getQuery(true);

		if ((int)$data['id'] === 0) {
			$columns = array();
			$values = array();
			$fields = self::dbLoadTable($tableName);

			$columsData = self::getTableStructure($tableName);

			foreach($fields as $key => $val) {
				$columns[] = $key;
				if (isset($data[$key])) {
					$values[] = is_numeric($data[$key]) ? $data[$key] : $db->quote($data[$key]);
				} else {
					if (strpos($columsData[$key]->Type, 'int') === 0 || strpos($columsData[$key]->Type, 'tinyint') === 0) {
						$values[] = '0';
					} else if (strpos($columsData[$key]->Type, 'date') === 0) {
						$values[] = $db->quote('0000-00-00 00:00:00');
					} else {
						$values[] = $db->quote('');
					}
				}
			}

			// Prepare the insert query.
			$query
				->insert($db->quoteName($tableName))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));

			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			if ($db->execute()) {
				$id = $db->insertid();
			} else {
				return false;
			}
		} else {
			// Fields to update.
			$fields = self::dbLoadTable($tableName);
			$fieldsToInsert = array();
			foreach($fields as $key => $val) {
				if (isset($data[$key])) {
					$value = is_numeric($data[$key]) ? $data[$key] : $db->quote($data[$key]);
				} else {
					$value = $db->quote('');
				}
				$fieldsToInsert[] = $db->quoteName($key) . ' = ' . $value;
			}

			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('id') . ' = ' . $data['id']
			);

			$query->update($db->quoteName($tableName))->set($fieldsToInsert)->where($conditions);

			// Set the query using our newly populated query object and execute it.
			$db->setQuery($query);
			if ($db->execute()) {
				$id = $data['id'];
			} else {
				return false;
			}
		}

		return $id;
	}

	public static function dbDelete($tableName, $id) {
		$db = CKFof::getDbo();

		$query = $db->getQuery(true);

		$conditions = array(
			$db->quoteName('id') . ' = ' . (int) $id
//			, $db->quoteName('profile_key') . ' = ' . $db->quote('custom.%')
		);

		$query->delete($db->quoteName($tableName));
		$query->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();

		return $result;
	}

	public static function convertObjectToArray($data) {
		return (array) $data;
	}

	public static function convertArrayToObject(array $array, $class = 'stdClass', $recursive = true)
	{
		$obj = new $class;

		foreach ($array as $k => $v)
		{
			if ($recursive && is_array($v))
			{
				$obj->$k = static::toObject($v, $class);
			}
			else
			{
				$obj->$k = $v;
			}
		}

		return $obj;
	}

	public static function dump($anything){
			echo "<pre>";
				var_dump($anything);
			echo "</pre>";
	}

	public static function print_r($anything){
		echo "<pre>";
				print_r($anything);
			echo "</pre>";
	}

	public static function addToolbarTitle($title, $image = '') {
		\JToolBarHelper::title($title, $image);
	}

	private static function getToolbar() {
		// Get the toolbar object instance
		$bar = \JToolBar::getInstance('toolbar');
		return $bar;
	}

	public static function addToolbarButton($name, $html, $id) {
		$bar = self::getToolbar();
		$bar->appendButton($name, $html, $id);
	}

	public static function addToolbarPreferences() {
		$bar = self::getToolbar();
		// add the options of the component
		if (self::userCan('admin')) {
			\JToolBarHelper::preferences(self::$environment);
		}
	}

	private static function getFileName($file) {
		$f = explode('/', $file);
		$fileName = end($f);
		$f = explode('.', $fileName);
		$ext = end($f);
		$fileName = str_replace('.' . $ext, '', $fileName);

		return $fileName;
	}

	public static function loadScriptDeclaration($js) {
		echo '<script>' . $js . '</script>';
	}

	public static function loadScriptDeclarationInline($js) {
		echo '<script>' . $js . '</script>';
	}

	public static function loadScript($file) {
		$doc = Factory::getDocument();
		$doc->addScript($file);
	}

	public static function loadScriptInline($file) {
		echo '<script src="' . $file . '"></script>';
	}

	public static function loadStyleDeclaration($css) {
		$doc = Factory::getDocument();
		$doc->addStyleDeclaration($css);
	}

	public static function loadStyleDeclarationInline($css) {
		echo '<style>' . $css . '</style>';
	}

	public static function loadStylesheet($file) {
		$doc = Factory::getDocument();
		$doc->addStylesheet($file);
	}

	public static function loadStylesheetInline($file) {
		echo '<link href="' . $file . '"" rel="stylesheet" />';
	}

	public static function error($msg) {
		throw new \Exception($msg);
	}

	public static function triggerEvent($name, $e = array()) {
		if (version_compare(JVERSION,'4') < 1) {
			$dispatcher = \JEventDispatcher::getInstance();
			return $dispatcher->trigger($name, $e);
		} else {
			return Factory::getApplication()->triggerEvent($name, $e);
		}
	}

	public static function importPlugin($group) {
		if (version_compare(JVERSION,'4') < 1) {
			\JPluginHelper::importPlugin($group);
		} else {
			PluginHelper::importPlugin($group);
		}
	}
}
