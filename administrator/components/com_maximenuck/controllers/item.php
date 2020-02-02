<?php
/**
 * @name		Menu Manager CK
 * @package		com_menumanagerck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

/**
 * The Menu Item Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_menumanagerck
 * @since       1.6
 */
class MaximenuckControllerItem extends JControllerForm
{

	private function stringURLSafeCK($string)
	{
		//remove any '-' from the string since they will be used as concatenaters
		$str = str_replace('-', ' ', $string);

		$lang = JFactory::getLanguage();
		$str = $lang->transliterate($str);

		// Convert certain symbols to letter representation
		$str = str_replace(array('&', '"', '<', '>'), array('a', 'q', 'l', 'g'), $str);

		// Lowercase and trim
		$str = trim(strtolower($str));

		// Remove any duplicate whitespace, and ensure all characters are alphanumeric
		$str = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $str);

		return $str;
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.6
	 */
	public function save($key = null, $urlVar = null)
	{
		// JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		if (!isset($this->input)) $this->input = new JInput();
		$data64 = $this->input->get('data64', '', 'raw');
		$datatmp = json_decode(base64_decode($data64));

		// Check for request forgeries.
		$data = $this->initData();
		$data['menutype'] = $this->input->get('menutype', '', 'string');
		if (!$data['menutype']) {
			echo 'menutypeerror';
			return false;
		}

		$app      = JFactory::getApplication();
		$model    = $this->getModel('Item', '', array());

		$data['component_id'] = isset($datatmp->component_id) ? $datatmp->component_id : '';
		$data['type'] = $datatmp->type;
		$data['link'] = $datatmp->link;
		$data['level'] = $this->input->get('parentLevel', 1, 'int') ? $this->input->get('parentLevel', 1, 'int') + 1 : $datatmp->level;
		$data['parent_id'] = $this->input->get('parentId', null, 'int') ? $this->input->get('parentId', null, 'int') : null;
		$data['title'] = $this->input->get('title', '', 'string');
		$data['alias'] = $this->input->get('alias', '', 'string');
		
		// si alias manquant on a un souci
		if (!$data['alias']) {
			echo 'aliasmissing';
			return false;
		}

		// for content article : set the article id 
		if ( ($this->input->get('type', '', 'string') == 'module' || $this->input->get('type', '', 'string') == 'alias')
			&& $this->input->get('dataId', 0, 'int')) 
		{
			$params = new JRegistry();
			if ($this->input->get('type', '', 'string') == 'module') {
				$params->set('maximenu_insertmodule', '1');
				$params->set('maximenu_module', $this->input->get('dataId', 0, 'int'));
			}
			if ($this->input->get('type', '', 'string') == 'alias') {
				$params->set('aliasoptions', $this->input->get('dataId', 0, 'int'));
			}
			$data['params'] = $params->toString();
		}

		// $data     = $this->input->post->get('jform', array(), 'array');
		$task     = $this->getTask();
		$context  = 'com_menumanagerck.edit.item';
		$recordId = $this->input->getInt('id', 0);

		// Populate the row id from the session.
		$data['id'] = $recordId;


		// Validate the posted data.
		// This post is made up of two forms, one for the item and one for params.
		$form = $model->getForm($data);
		if (!$form)
		{
			echo 'failed model getForm';
			return false;
		}

		// Attempt to save the data.
		if (!$dataid = $model->save($data))
		{
			echo 'failed model save';
			return false;
		}

		// Save succeeded, check-in the row.
		if ($model->checkin($data['id']) === false)
		{
			echo 'failed model checkin';
			return false;
		}

		// Redirect the user and adjust session state based on the chosen task.
		echo (json_encode(Array('1', $dataid)));
	}

	
	public function initData() {
		$data = Array();
		$data['parent_id'] = null;
		$data['level'] = null;
		$data['lft'] = null;
		$data['rgt'] = null;
		$data['alias'] = null;
		$data['id'] = null;
		$data['menutype'] = null;
		$data['title'] = null;
		$data['note'] = null;
		$data['path'] = null;
		$data['link'] = null;
		$data['type'] = null;
		$data['published'] = 1;
		$data['component_id'] = null;
		$data['checked_out'] = null;
		$data['checked_out_time'] = null;
		$data['browserNav'] = null;
		$data['access'] = 1;
		$data['img'] = null;
		$data['template_style_id'] = null;
		$data['params'] = array ();
		$data['home'] = null;
		$data['language'] = '*';
		$data['client_id'] = null;
		$data['request'] = 
		array (
		  'option' => null,
		  'view' => null );
		$data['menuordering'] = 0;
		
		return $data;
	}
	/**
	 * Sets the type of the menu item currently being edited.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function setType()
	{
		$app = JFactory::getApplication();
		if (!isset($this->input)) $this->input = new JInput();
		// Get the posted values from the request.
		$data = $this->input->post->get('jform', array(), 'array');
		$recordId = $this->input->getInt('id', 0);
		$type = $this->input->post->get('type', '');


		$data['component_id'] = '';
		$data['link'] = '';
		$data['level'] = 1;
		$data['type'] = $type;
		$recordId = 0;
	
		if ($type == 'module') {
			$data['type'] = 'separator';
		}
		
		
		$app->setUserState('com_menumanagerck.edit.item.type', $type);
		if ($type == 'component')
		{
			if ($this->input->post->get('dataAttribs', '') )
			{
				$dataAttribs =json_decode( base64_decode($this->input->post->get('dataAttribs', '')));
			}
			$request = Array('option' => $this->input->post->get('component', ''), 
							'view' => isset($dataAttribs->view) ? $dataAttribs->view : $this->input->post->get('view', ''),
							'layout' => isset($dataAttribs->layout) ? $dataAttribs->layout : $this->input->post->get('layout', ''),
							'id' => $this->input->getInt('dataId', 0));

			$component = JComponentHelper::getComponent($this->input->post->get('component', ''));
			$data['component_id'] = $component->id;
			$component_id = $component->id;
			
			$app->setUserState('com_menumanagerck.edit.item.link', 'index.php?' . JURI::buildQuery((array) $request));
			// $data['link'] = 'index.php?' . JURI::buildQuery((array) $request);
			$data['link'] = $this->getComponentQuery($this->input->post->get('component', ''), $this->input->post->get('view', ''), $this->input->getInt('dataId', 0), $this->input->post->get('layout', ''), $this->input->post->get('dataController', ''), $this->input->post->get('category_id', ''), $this->input->post->get('dataAttribs', ''));
		}
		// If the type is alias you just need the item id from the menu item referenced.
		elseif ($type == 'alias')
		{
			$app->setUserState('com_menumanagerck.edit.item.link', 'index.php?Itemid=');
			$data['link'] = 'index.php?Itemid=';
		}

		// unset($data['request']);
		
		if ($this->input->get('fieldtype') == 'type')
		{
			$data['link'] = $app->getUserState('com_menumanagerck.edit.item.link');
			$link = $app->getUserState('com_menumanagerck.edit.item.link');
		}

		//Save the data in the session.
		$app->setUserState('com_menumanagerck.edit.item.data', $data);

		echo base64_encode(json_encode($data));

	}
	
	private function getComponentQuery($option, $view, $id = 0, $layout = '', $controller = '', $category_id = 0, $dataAttribs = '') {
		if ($dataAttribs )
		{
			$dataAttribs =json_decode( base64_decode($dataAttribs));
		}
		$controller = isset($dataAttribs->controller) ? $dataAttribs->controller : '';
		$q = '';
		switch ($option) {
			case 'com_k2';
				switch ($view) {
					case 'itemlist';
						$q = 'index.php?option=com_k2&view=itemlist&layout=category';
						break;
					case 'item';
						$q = 'index.php?option=com_k2&view=item&layout=item';
						break;
				}
				break;
			case 'com_hikashop';
				switch ($dataAttribs->layout) {
					case 'listing';
						$q = 'index.php?option=com_hikashop&view=product&layout=listing&cid=' . $dataAttribs->category_id;
						break;
					case 'show';
						$q = 'index.php?option=com_hikashop&view=product&layout=show&product_id=' . $dataAttribs->product_id;
						break;
				}
				$id = 0;
				break;
			case 'com_jshopping';
				switch ($controller) {
					case 'products';
						$q = 'index.php?option=com_jshopping&controller=products&category_id=' . $dataAttribs->category_id;
						$id = 0;
						break;
					case 'product';
						$q = 'index.php?option=com_jshopping&controller=product&category_id=' . $dataAttribs->category_id . '&product_id=' . $dataAttribs->product_id;
						$id = 0;
						break;
				}
				break;
			case 'com_content';
			default:
				switch ($view) {
					case 'article';
						$q = 'index.php?option=com_content&view=article';
						break;
					case 'category';
						$q = 'index.php?option=com_content&view=category';
						if ($layout == 'blog')
							$q .= '&layout=blog';
						break;
					case 'featured';
						$q = 'index.php?option=com_content&view=featured';
						break;
					case 'categories';
						$q = 'index.php?option=com_content&view=categories';
						break;
				}
				break;
		}
		
		if ($id ) 
		{
			$q .= '&id=' . $id;
		}
			
		return $q;
	}
	
	public function publish($model = null)
	{
		// JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		if (!isset($this->input)) $this->input = new JInput();
		$value   = $this->input->get('state', 1, 'int');
		$id   = $this->input->get('id', null, 'array');

		$model = $this->getModel('Item', '', array());
		if (!$model->publish($id, 1-$value))
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		echo implode(",",$this->getTree($id[0]));

		exit();
	}

	public function getPath($id) {
		$items = $this->getModel('Item')->getTable($type = 'Menu', $prefix = 'JTable', $config = array())->getPath($id);
		$a = array();
		foreach($items as $item) {
			$a[] = (string) $item->id;
		}

		return $a;
	}

	public function getTree($id) {
		$items = $this->getModel('Item')->getTable($type = 'Menu', $prefix = 'JTable', $config = array())->getTree($id);
		$a = array();
		foreach($items as $item) {
			$a[] = (string) $item->id;
		}

		return $a;
	}
	
	public function showItemsList() {
		$input = new JInput();
		$component = $input->get('component', 'content');
		$view = $input->get('view', 'article');
		require_once JPATH_COMPONENT.'/helpers/menumanagerck.php';
		echo MenumanagerckHelper::getTypeItemsList($component, $view);
	}

	public function delete()
	{
		if (!isset($this->input)) $this->input = new JInput();
		// Get items to remove from the request.
		$ids = $this->input->get('id', 0, 'array');

		// Get the model.
		$model = $this->getModel();

		// Remove the items.
		if (!$message = $model->delete($ids))
		{
			echo 'Not deleted !';
			echo $message;
			// echo($model->getError());
		} else {
			echo '1';
		}
	}

	public function saveTitleAjax()
	{
		// JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN')); // pour faire �a il faut r�cup�rer tous les input du form et les serializer pour avoir le jeton
		
		// Get the input
		$input = JFactory::getApplication()->input;
		$pk   = $input->post->get('id', 0, 'int');
		$title = $input->post->get('title', '', 'string');

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->savetitle($pk, $title);

		if ($return)
		{
			echo "1";
		} else
		{
			echo "0";
		}

		// Close the application
//		JFactory::getApplication()->close();
		exit();
	}

	public function saveLevelAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pk   = $input->post->get('id', 0, 'int');
		$level = $input->post->get('level', 1, 'int');
		$parentid = $input->post->get('parentid', 1, 'int');

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->savelevel($pk, $level, $parentid);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	public function saveOrderAjax()
	{
		if (!isset($this->input)) $this->input = new JInput();
		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');
		$lft = $this->input->post->get('lft', array(), 'array');
		$rgt = $this->input->post->get('rgt', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);
		JArrayHelper::toInteger($lft);
		JArrayHelper::toInteger($rgt);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order, $lft, $rgt);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	public function checkinAjax() {
		$input = JFactory::getApplication()->input;
		$pk = $input->post->get('id', 0, 'int');

		// Get the model
		$model = $this->getModel();

		if ($model->checkin($pk) === false) echo "0";
		echo "1";
		exit();
	}
	
	public function validatePath() {
		$input = JFactory::getApplication()->input;
		$newpath = $input->post->get('newPath', null, 'array');
		if (!is_array($newpath)) return false;
		$newpath = array_reverse($newpath);
		$id = $input->post->get('id', null, 'int');

		// Get the model
		$model = $this->getModel();
		
		if (!$model->validateItemPath($newpath, $id)) {
			echo 'pathexists';
			return false;
		}
		
		echo '1';
	}
	
	public function createAlias() {
		$input = JFactory::getApplication()->input;
		$title = $input->post->get('title', '', 'string');
		$type = $input->post->get('type', '', 'string');
		
		if (!$title) return false;
		
		if ($type == 'separator'
				|| $type == 'url'
				|| $type == 'alias'
				|| $type == 'heading'
				|| $type == 'module'
				|| $title == '' ) {
				$alias = JFactory::getDate();
			} else {
				$alias = $this->stringURLSafeCK(strip_tags($title));
			}
		
		echo $alias;
	}
	
	public function saveParam($id = 0, $param = '', $value = '') {
		if (!isset($this->input)) $this->input = new JInput();
		$id = $this->input->post->get('id', $id, 'int');
		$param = $this->input->post->get('param', $param, 'string');
		$value = $this->input->post->get('value', $value, 'string');

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveparam($id, $param, $value);

		if ($return)
		{
			echo "1";
		} else {
			echo "0";
		}
		exit();
	}

}
