<?php
defined('_JEXEC') or die;
class Yandex_MapsControllerRegistration extends Yandex_MapsController{
	public function add($cachable = null, $urlparams = null){
		header('Content-Type:text/html; charset=utf-8');
		parent::display($cachable, $urlparams);
		jFactory::getApplication()->close();
	}
	private function getUserId($email){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear()
			->select($db->quoteName(array('id')))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('email') . ' = ' . $db->quote($email));
		$db->setQuery($query);

		try {
			$res = $db->loadResult();
			return $res ? $res : 0 ;
		} catch (RuntimeException $e) {
			$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);
			return false;
		}
	}
	private function newUser($email, $login, $password){
		$result = (object)array('status'=>0, 'error_message'=>'');

		if (JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0) {
			$result->error_message = 'Запрет на регистрацию';
			return $result;
		}
		
		JFactory::getLanguage()->load('com_users', JPATH_SITE, 'ru-RU', true);

		$app	= JFactory::getApplication();
		JForm::addFormPath(JPATH_ROOT . '/components/com_users/models/forms');
		require_once(JPATH_ROOT.'/components/com_users/models/registration.php');
		$model = new UsersModelRegistration();

		// Get the user data.
		$requestData = array();
		$requestData['email2'] = @$requestData['email1'] = $email;
		$requestData['password2'] = @$requestData['password1'] = $password;
		$requestData['username'] = $requestData['name'] = preg_replace('#[^\w\-]#u','_', $email);
		
		// Validate the posted data.
		$form	= $model->getForm();

		if (!$form) {
			$result->error_message = $model->getError();
			return $result;
		}
		
		$data	= $model->validate($form, $requestData);

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();
			$result->error_message = '';
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$trace = $errors[$i]->getTrace();
					$attrs = $trace[0]['args'][0]->attributes();
					$field = 'name';
					foreach($attrs as $attr) {
						$field = $attr;
						break;
					}
					$result->error_message.= $errors[$i]->getMessage().'<br>'."\n";
				} else {
					$result->error_message.= $errors[$i].'<br>'."\n";
				}
			}

			return  $result;
		}

		// Attempt to save the data.
		$return	= $model->register($data);

		// Check for errors.
		if ($return === false) {
			$result->error_message = JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError());
			return  $result;
		}

		// Flush the data from the session.
		$app->setUserState('com_users.registration.data', null);

		if ($return === 'adminactivate') {
			$result->error_message = JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY');
		} elseif ($return === 'useractivate') {
			$result->error_message = JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE');
		} else {
			$result->error_message =  JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS');
			
		}
		
		$result->status = 1;
		return  $result;
	}
	private function checkUserInfo() {
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$isroot = $user->get('isRoot');
		$id = $app->input->get('id', 0 , 'INT');
		if (!$id) {
			JError::raiseError(404, "Объект не найден");
		}
		$model = JModelLegacy::getInstance('Objects', 'Yandex_MapsModel')->model((int)$id);
		if (!$model->id) {
			JError::raiseError(404, "Объект не найден");
		}
		if (!$user->id or !$isroot) {
			$message = "Вы должны иметь права Администратора для этого действия";
			$url = 'index.php?option=com_users&view=login&return=' . base64_encode(jRoute::_('index.php?task=registration.remove&id='.$id));
			$app->redirect($url, $message);
			jexit();
		}
		return $model;
	}
	public function remove() {
		$app = JFactory::getApplication();
		$model = $this->checkUserInfo();
		$model->delete();
		$app->redirect('index.php?option=com_yandex_maps&task=map&id='.$model->map->id);
		jexit();
	}
	public function activation() {
		$app = JFactory::getApplication();
		$model = $this->checkUserInfo();
		$model->active  = $model->active ? 0 : 1;
		$model->save();
		$app->redirect('index.php?option=com_yandex_maps&task=map&id='.$model->map->id, 'Объект '. ($model->active ? 'активирован' : 'деактивирован'));
		jexit();
	}

	public function save() {
		$result = (object)array('status'=>0, 'error_message'=>'Произошла неизвестная ошибка');
		if (!JSession::checkToken()) {
			$result->error_message = JText::_('JINVALID_TOKEN').' Пожалуйста обновите страницу.';
			jExit(json_encode($result));
		}
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_yandex_maps');
		if ($app->input->get('map_id', 0, 'INT')) {
			$model = JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->model($app->input->get('map_id', 0, 'INT'));
			if ($model->id) {
				$buf = new JRegistry();
				$buf->loadString($model->_data->params);
				$params->merge($buf);
			}
		}

		$dataInput = $this->input->get('jform', array(), 'ARRAY');
		$session = jFactory::getSession();

		if (!$session->get('map_user_id') and !JFactory::getUser()->id and !empty($dataInput['organization_email']) and !empty($dataInput['organization_password1'])) {
			$newuser = $this->newUser($dataInput['organization_email'], $dataInput['organization_email'], $dataInput['organization_password1']);
			if (!$newuser->status) {
				jExit(json_encode($newuser));
			} else {
				$session->set('map_user_id', $this->getUserId($dataInput['organization_email']));
			}
		}

		$data = array();
		$address = array();
		$address_has = false;

		foreach ($dataInput as $key=>$value) {
			switch ($key) {
				case 'organization_image2':
				case 'organization_image':
				case 'organization_icon':
				case 'organization_password2':
				case 'organization_password1':
				case 'organization_fact_and_legal_equal':
				break;
				case 'organization_address':
				case 'organization_address_legal':
					if ($address_has and $key=='organization_address') {
						break;
					}
					$address = $value;
					$data[$key] = json_encode($value);
					if (isset($dataInput['organization_fact_and_legal_equal']) and isset($dataInput['organization_address_legal'])) {
						$data['organization_address'] = $data['organization_address_legal'];
						$address_has = true;
					}
				break;
				case 'organization_service_delivery_variants':
				case 'organization_shedule_days':
					$data[$key] = implode(',', $value);
				break;
				default:
					if ($params->get('registration_'.$key, 1)>1 and empty($value)) {
						switch ($key){
							case 'organization_start_in':
							case 'organization_end_in':
								if (!isset($dataInput['organization_shedule_24'])) {
									$result->error_message = 'Не заполнены поля - начала и конца рабочего дня';
									jExit(json_encode($result));
								}
						}
					}
					if (!isset($data[$key])) {
						$data[$key] = $value;
					}
				break;
			}
		}

		$icon = $params->get('object_iconImageHref', "media/com_yandex_maps/images/organizations/arrow.png");

		if (empty($dataInput['organization_icon']) or !$dataInput['organization_icon']) {
			if ($params->get('registration_organization_icon_some_file', 1)) {
				$file = jHtml::_('xdwork.upload','organization_icon_file');
				if (!$file['error']){
					if (count($file['files'])) {
						$icon = "media/com_yandex_maps/images/organizations/". $file['files'][0][1];
					}
				} else {
					$result->error_message = implode("\n<br>", $file['error']);
					jExit(json_encode($result));
				}				
			}
		} else {
			if (file_exists(JPATH_ROOT . "/media/com_yandex_maps/images/organization_icons/". $dataInput['organization_icon'])) {
				$icon = "media/com_yandex_maps/images/organization_icons/". $dataInput['organization_icon'];
			}
		}

		$data = (object)$data;
		
		if (!empty($address['lat']) or $this->input->get('lat',0)) {
			$object = $this->getModel('Objects');
			if ($params->get('registration_organization_use_addres_position', 0)) {
				$object->lat = jhtml::_('xdwork.coordinate', $address['lat']?:$this->input->get('lat', 0, 'RAW'));
				$object->lan = jhtml::_('xdwork.coordinate', $address['lan']?:$this->input->get('lan', 0, 'RAW')) ;
			} else {
				$object->lat = jhtml::_('xdwork.coordinate', $this->input->get('lat', 0, 'RAW')?:$address['lat']);
				$object->lan = jhtml::_('xdwork.coordinate', $this->input->get('lan', 0, 'RAW')?:$address['lan']);
			}
	
			$object->coordinates = '['.$object->lat.','.$object->lan.']';
			if ($this->input->get('map_id',0)) {
				$category = JModelLegacy::getInstance('Categories', 'Yandex_MapsModel')->findNearest(array($object->lat, $object->lan, $this->input->get('map_id',0)));
			} else {
				$category = JModelLegacy::getInstance('Categories', 'Yandex_MapsModel')->findNearest((int)$params->get('registration_organization_category_source', 0) ? (int)$params->get('registration_organization_category_id') : array($object->lat, $object->lan));
			}

			//$object->map_id = $category->map_id;
			$object->categoryids = array($category->id);
	
			if (!empty($dataInput['organization_name'])) {
				$object->title =  $dataInput['organization_name'];
			}
			$sizes = explode(',', $params->get('registration_organization_icon_some_file_size', '16,16'));
			if (!$sizes[0]) {$sizes[0] = 16;}
			if (!$sizes[1]) {$sizes[1] = 16;}
			$object->type = 'placemark';
			$object->modified_time = $object->created_time = time();
			if ($this->input->get('zoom', 0, 'INT')) {
				$object->zoom = $this->input->get('zoom', 0, 'INT');
			} else {
				$object->zoom = (!empty($address['zoom']) and intval($address['zoom'])) ? intval($address['zoom']) : 12;
			}
			if (!$object->title) {
				$object->title = 'Undefined';
			}
			$object->options = '{"iconLayout":"default#image","iconImageHref":"'.jhtml::_('xdwork.thumb', $icon, $sizes[0], $sizes[1], 1).'","iconImageSize":['.$sizes[0] .','. $sizes[1].'],"iconImageOffset":[-'.round($sizes[0]/2) .',-'. round($sizes[0]/2).']}';
			$object->properties = '{"metaType":"Point","iconContent":"","balloonContent":""}';
			$object->ordering = 0;
			$object->active = (int)$params->get('registration_organization_moderation', 1); // премодерация по умолчанию
			$object->params = '{}';
			$object->create_by = JFactory::getUser()->id ? JFactory::getUser()->id : $session->get('map_user_id', 0);

			if ($object->save()) {
				$result->object_id = $data->organization_object_id = $object->id;
			} else {
				$result->error_message = implode("\n<br>", array_values($object->error));
				jExit(json_encode($result));
			}
		} else {
			jExit(json_encode($result));
		}
	
		$organization = $this->getModel('Organizations');
		$organization->_attributes = (array)$data;
		$organization->save(true);
		
		$result->error_message = 'Объект добавлен на сайт. <a href="'.JRoute::_('index.php?option=com_yandex_maps&task=object&id='.$object->slug).'">Метка</a> добавлена в <a href="'.JRoute::_('index.php?option=com_yandex_maps&task=map&id='.$category->map->slug).'">карту</a>';

		if (!(int)$params->get('registration_organization_moderation', 1)) {
			$result->error_message = 'Объект добавлен в базу. После модерации, он будет добавлен на сайт';
		}

		if ($params->get('registration_organization_send_notification_to_admin', 1)) {
			$body = 'Объект добавлен на сайт. <a href="'.JRoute::_('index.php?option=com_yandex_maps&task=object&id='.$object->slug, true, -1).'">Метка</a> добавлена в <a href="'.JRoute::_('index.php?option=com_yandex_maps&task=map&id='.$category->map->slug, true, -1).'">карту</a>';
			
			$body.= jhtml::_('xdwork.organization', $this->getModel('Objects')->model($object->id));
			$body.= '<hr>';
			if (!$object->active) {
				$body.= '<br>Для активации пройдите по ссылке <a href="'.JRoute::_('index.php?option=com_yandex_maps&task=registration.activation&id='.$object->id, true, -1).'">Включить</a>';
			} else {
				$body.= '<br>Для деактивации пройдите по ссылке <a href="'.JRoute::_('index.php?option=com_yandex_maps&task=registration.activation&id='.$object->id, true, -1).'">Отключить</a>';
			}
			$body.= '<br>Для удаления пройдите по ссылке <a href="'.JRoute::_('index.php?option=com_yandex_maps&task=registration.remove&id='.$object->id, true, -1).'">Удалить</a>';
			jhtml::_('xdwork.sendmail', 'Добавлен новый объект на сайте %s', $body);
		}

		$result->status = 1;
		jExit(json_encode($result));
		exit;
	}
}