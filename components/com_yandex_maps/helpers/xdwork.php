<?php
defined('_JEXEC') or die;

abstract class JHtmlXDWork {
	static public $imagepath = "/media/com_yandex_maps/images/organizations/";
	static public $_includemodeinline = false;

	static public function includejs($url, $body = false){
		if (!$body) {
			if (!self::$_includemodeinline) {
				Jhtml::script($url);
			} else {
				echo '<script type="text/javaScript" src="'.$url.'"></script>';
			}
		} else {
			if (!self::$_includemodeinline) {
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration($url);
			} else {
				echo '<script type="text/javaScript">'.$url.'</script>';
			}
		}
	}

	static function includecss($url){
		if (!self::$_includemodeinline) {
			Jhtml::stylesheet($url);
		} else {
			echo '<link rel="stylesheet" href="'.$url.'" type="text/css" />';
		}
	}

	static function includemodeinline($enable){
		self::$_includemodeinline = $enable;
	}

	static function coordinate($x){
		return preg_replace(array('#[^0-9\.]#', '#,#'), array('', '.') , $x);
	}

	static function isAjax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}

	static public function isAssoc($array) {
		return is_array($array) and (bool)count(array_filter(array_keys($array), 'is_string'));
	}

	static public $extensions = ['png','jpg','jpeg','gif'];

	static public function upload($name = 'files') {
		$result = array('res'=>1,'files'=>array(),'error'=>array());
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');
		$app = JFactory::getApplication();
		$input = $app->input;
		$_file= $input->files->get('jform');
		if (!isset($_file[$name])) {
			return $result;
		}
		$files = $_file[$name];
		if (self::isAssoc($files)) {
			$files = array($files);
		};
		$folder = self::imagepath();

		if (!is_dir($folder)) {
			JFolder::create($folder, 0777);
		}
		if (is_array($files)) {
			foreach ($files as $file) {
				if (!empty($file) and !empty($file['tmp_name']) and $file['size']) {
					if (!empty($file['error'])) {
						$result['error'][] = JText::_('Произошла ошибка ').$file['error'];
						continue;
					}

					$extension = JFile::getExt($file['name']);
					
					$filename = JFile::makeSafe(JFilterOutput::stringURLSafe(basename($file['name'],'.'.$extension)).'.'.$extension);

					if (!in_array(strtolower($extension), self::$extensions)) {
						$result['error'][] = JText::_('Файл не является изображением');
						continue;
					}

					do{
						$randname = md5($filename.rand(100, 200).rand(100, 200).rand(100, 200)).'.'.$extension;
					}while(file_exists($folder . '/' . $randname));

					if (!JFile::upload($file['tmp_name'], $folder . '/' . $randname)) {
						$result['error'][] = JText::_('Ошибка загрузки файла. Возможно файл слишком большой');
						continue;
					}
					
					
					$result['files'][] = array(
						$filename,
						$randname,
						filesize($folder . '/' . $randname)
					);
				
				}
			}
		}
		return $result;
	}

	static public function autocomplete() {
		static $autocomplete = true;
		if ($autocomplete) {
			self::includejs(JURI::root().'media/com_yandex_maps/js/autocomplete/jquery.autocomplete.js');
			self::includejs(JURI::root().'media/com_yandex_maps/js/search.js');
			self::includecss(JURI::root().'media/com_yandex_maps/js/autocomplete/jquery.autocomplete.css');
			$autocomplete = false;
		}
	}

	static public function yapi() {
		static $included = true;

		if ($included) {
			$lang = in_array(JFactory::getLanguage()->getTag(),['ru-RU','en-US','tr-TR','uk-UA']) ? JFactory::getLanguage()->getTag():'en-US';
			$params = JComponentHelper::getParams('com_yandex_maps');

			self::includejs('window.YANDEX_MAPS_API_KEY = "' . $params->get('api_key','') .  '";', true);

			self::includejs('https://api-maps.yandex.ru/2.1/?' .
                'lang='. $lang .
                ($params->get('debug_mode',0) ? '&mode=debug' : '') .
                ($params->get('api_key','') ? '&apikey=' . $params->get('api_key','') : '') .
                ''
            );

			$included = false;
		}
	}
	static public function owlslider () {
        self::includecss(JURI::root().'media/com_yandex_maps/js/owl-carousel/owl.carousel.css');
        self::includecss(JURI::root().'media/com_yandex_maps/js/owl-carousel/owl.theme.css');
        self::includejs(JURI::root().'media/com_yandex_maps/js/owl-carousel/owl.carousel.min.js');
        self::includejs('
        jQuery(function() {
            jQuery(".owl-carousel").owlCarousel();
        });
        ', true);
    }
	static public function frontjs () {
		static $included = true;
		if ($included) {
			self::yapi();

            jimport('joomla.version');
            $version = new JVersion();
            
			if (version_compare($version->RELEASE, '3.1') < 0 or JComponentHelper::getParams('com_yandex_maps')->get('include_jquery', 1)) {
				self::includejs(JURI::root().'media/com_yandex_maps/js/jquery.min.js');
			} else {
                JHtml::_('jquery.framework');
				self::includejs('(jQuery!==undefined) && (XDjQuery = jQuery)', true);
			}

			$empty = '';
			if (!file_exists(JPATH_BASE.'/media/com_yandex_maps/css/custom.css')) {
				jimport('joomla.filesystem.file');
				JFile::write(JPATH_BASE.'/media/com_yandex_maps/css/custom.css', $empty);
			}
			if (!file_exists(JPATH_BASE.'/media/com_yandex_maps/js/custom.js')) {
				jimport('joomla.filesystem.file');
				JFile::write(JPATH_BASE.'/media/com_yandex_maps/js/custom.js',$empty);
			}
			if (!file_exists(JPATH_BASE.'/media/com_yandex_maps/css/frontend2.css')) {
				self::includecss(JURI::root().'media/com_yandex_maps/css/frontend.css');
			} else {
				self::includecss(JURI::root().'media/com_yandex_maps/css/frontend2.css');
			}
			self::includecss(JURI::root().'media/com_yandex_maps/css/custom.css');
			self::includejs(JURI::root().'media/com_yandex_maps/js/frontend.js');
			self::includejs(JURI::root().'media/com_yandex_maps/js/custom.js');

            self::owlslider();

			$included = false;
		}
	}
	static public function mask() {
		static $mask = true;
		if ($mask) {
			self::includejs(JURI::root().'media/com_yandex_maps/js/maskedinput/jquery.mask.min.js');
			$mask = false;
		}
	}
	static public function kladru() {
		static $kladru = true;
		if ($kladru) {
			$params = JComponentHelper::getParams('com_yandex_maps');
			if (!$params->get('registration_organization_address_input_view',1)) {
				self::includecss(JURI::root().'media/com_yandex_maps/js/kladr/jquery.kladr.min.css');
				self::includejs(JURI::root().'media/com_yandex_maps/js/kladr/jquery.kladr.min.js');
			}
			$kladru = false;
		}
	}
	static public function datetimepicker() {
		static $datetimepicker = true;
		if ($datetimepicker) {
			self::includecss(JURI::root().'media/com_yandex_maps/js/datetimepicker/jquery.datetimepicker.css');
			self::includejs(JURI::root().'media/com_yandex_maps/js/datetimepicker/jquery.datetimepicker.js');
			$datetimepicker = false;
		}
	}
	static public function dialog() {
		static $dialog = true;
		if ($dialog) {
			self::includecss(JURI::root().'media/com_yandex_maps/js/dialog/jquery.dialog.css');
			self::includejs(JURI::root().'media/com_yandex_maps/js/dialog/jquery.dialog.js');
			$dialog = false;
		}
	}
	static public function registration($include_phoenix = false) {
		static $included = false;
		if (!$included) {
			jhtml::_('xdwork.kladru');// датапикер для формы ввода даты
			jhtml::_('xdwork.datetimepicker');// датапикер для формы ввода даты
			jhtml::_('xdwork.dialog');// всплывашки
			jhtml::_('xdwork.mask'); // маска для телефонов и дат
			if ($include_phoenix) {				
				// сохранение данных формы в браузере
				self::includejs(JURI::root().'media/com_yandex_maps/js/phoenix/jquery.phoenix.min.js');
			}
			self::includejs(JURI::root().'media/com_yandex_maps/js/registration.js');
			$included = true;
		}
	}
	static public function imagepath (){return JPATH_ROOT . self::$imagepath;}
	static public function imageurl (){return self::$imagepath;}

	static public function organization (&$organization){
		if ($organization->organization_object_id) {
			static $allparams = array();
			if (!isset($allparams[$organization->map_id])) {
				$allparams[$organization->map_id] = JComponentHelper::getParams('com_yandex_maps');
				$model = JModelLegacy::getInstance('Maps', 'Yandex_MapsModel')->model($organization->map_id);
				if ($model->id) {
					$buf = new JRegistry();
					$buf->loadString($model->_data->params);
					$allparams[$organization->map_id]->merge($buf);
				}
			}
			ob_start();
			$params = $allparams[$organization->map_id];
			include JPATH_ROOT.'/components/com_yandex_maps/views/object/tmpl/organization.php';
			return $organization->organization_compile = ob_get_clean();
		}
	}

	static public function description (&$object, $map = false){
		$data = self::tpl(['object'=>&$object, 'map'=>$map], 'views/object/tmpl/description.php');

        return $object->description = $data;
	}

	static public function tpl ($data, $path){
		return self::includePHP($path, true, $data);
	}

	static public function getUniqueName($source_file){
		$path = str_replace(JPATH_ROOT,'', dirname($source_file));
		return preg_replace(array('#[\/\\\\]#',), array('-',), $path);
	}

	/**
	 * Режет фото на заданные размеры.
	 *
	 * @param	number	$newWidth		Размер по ширине
	 * @param	number	$newHeight		Размер по высоте
	 * @param	string	$originalFile	Путь до исходного изображения
	 * @param	string	$targetPath		Путь до результирующего изображения
	 *
	 * @return  string Путь до созданного файла
	 */
    static public function resizecrop($source_file, $dst_dir, $max_width, $max_height,  $quality = 80){
        $newFile = $dst_dir.$max_width.'x'.$max_height.self::getUniqueName($source_file).'-'.basename($source_file);
        if (file_exists($newFile) and filemtime($newFile)>=filemtime($source_file)) {
            return $newFile;
        }

        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];

        switch($mime){
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 9;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                break;

            default:
                return false;
                break;
        }

        $dst_img = imagecreatetruecolor($max_width, $max_height);

        switch ($mime) {
            case 'image/png':
                $background = imagecolorallocate($dst_img, 0, 0, 0);
                imagecolortransparent($dst_img, $background);
                imagealphablending($dst_img, false);
                imagesavealpha($dst_img, true);
                break;

            case 'image/gif':
                $background = imagecolorallocate($dst_img, 0, 0, 0);
                imagecolortransparent($dst_img, $background);
                break;
        }

        $src_img = $image_create($source_file);

        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        if($width_new > $width){
            $h_point = (($height - $height_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }else{
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }

        $image($dst_img, $newFile, $quality);

        if($dst_img)imagedestroy($dst_img);
        if($src_img)imagedestroy($src_img);
        return $newFile;
    }


    static public function includePHP($file, $buffering = false, $variables = false) {
		$app    = JFactory::getApplication();

		$path = JPATH_ROOT.'/templates/'.$app->getTemplate().'/html/com_yandex_maps/'.$file;

		if (!file_exists($path)) {
			$path = JPATH_ROOT.'/components/com_yandex_maps/'.$file;
            if (!file_exists($path)) {
                $path =  JPATH_ROOT.'/'.$file;
                if (!file_exists($path)) {
                    $path =  $file;
                    if (!file_exists($path)) {
                        return '';
                    }
                }
            }
		}

		if (!$buffering) {
            return $path;
        }

		if ($variables) {
            extract($variables);
        }

		ob_start();

		include $path;

		return ob_get_clean();
	}
	static public function resize($originalFile, $targetPath, $newWidth, $newHeight,  $quality = 80) {
		$newFile = $targetPath.$newWidth.'x'.$newHeight.self::getUniqueName($originalFile).'-'.basename($originalFile);
		if (file_exists($newFile)  and filemtime($newFile)>=filemtime($originalFile)) {
			return $newFile;
		}

		$info = getimagesize($originalFile);
		$mime = $info['mime'];

		switch ($mime) {
				case 'image/jpeg':
						$image_create_func = 'imagecreatefromjpeg';
						$image_save_func = 'imagejpeg';
						$new_image_ext = 'jpg';
						break;

				case 'image/png':
						$image_create_func = 'imagecreatefrompng';
						$image_save_func = 'imagepng';
						$new_image_ext = 'png';
						$quality = 9;
						break;

				case 'image/gif':
						$image_create_func = 'imagecreatefromgif';
						$image_save_func = 'imagegif';
						$new_image_ext = 'gif';
						break;

				default: 
						throw Exception('Unknown image type.');
		}

		$img = $image_create_func($originalFile);
		list($width, $height) = getimagesize($originalFile);

		$tmp = imagecreatetruecolor($newWidth, $newHeight);
		
		switch ($mime) {
		case 'image/png':
			$background = imagecolorallocate($tmp, 0, 0, 0);
			imagecolortransparent($tmp, $background);
			imagealphablending($tmp, false);
			imagesavealpha($tmp, true);
			break;

		case 'image/gif':
			$background = imagecolorallocate($tmp, 0, 0, 0);
			imagecolortransparent($tmp, $background);
			break;
		}
		
		imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

		
		$image_save_func($tmp, $newFile, $quality);
		return $newFile;
	}
	static public function thumb($img, $width, $height = 0, $mode = 0, $quality = 80) {
		$path = realpath(JPATH_ROOT . '/' . $img);
		if (!trim($path) or !is_file($path)) {
			$path = realpath(JPATH_ROOT .'/media/com_yandex_maps/images/nophoto.png');
		}
		
		list($imgWidth, $imgHeight) = getimagesize($path);
		jimport('joomla.filesystem.folder');
		if (!is_dir(JPATH_ROOT .'/media/com_yandex_maps/thumbs/')) {
			JFolder::create(JPATH_ROOT .'/media/com_yandex_maps/thumbs/', 0777);
		}
		switch ($mode) {
		// по большей из сторон под размер заданного квадрата
		case 0:
			$height = $width;
			if ($imgWidth > $imgHeight) {
				$ratio = $width/$imgWidth;
				$height = (int)($ratio*$imgHeight);
			} else {
				$ratio = $height/$imgHeight;
				$width = (int)($ratio*$imgWidth);
			}
			if ($imgWidth>$width or $imgHeight>$height) {
				$path = self::resize($path, realpath(JPATH_ROOT .'/media/com_yandex_maps/thumbs/').'/', $width, $height, $quality);
			}
		break;
		case 1:
			if ($imgWidth>$width or $imgHeight>$height) {
				$path = self::resizecrop($path, realpath(JPATH_ROOT .'/media/com_yandex_maps/thumbs/').'/', $width, $height, $quality);
			}
		break;
        // под ширину
		case 2:
			if ($imgWidth > $width) {
				$ratio = $width/$imgWidth;
				$height = (int)($ratio*$imgHeight);
				$path = self::resize($path, realpath(JPATH_ROOT .'/media/com_yandex_maps/thumbs/').'/', $width, $height, $quality);
			}
		break;
        // под высоту
		case 3:
			if ($imgHeight > $height) {
				$ratio = $height/$imgHeight;
				$width = (int)($ratio*$imgHeight);
				$path = self::resize($path, realpath(JPATH_ROOT .'/media/com_yandex_maps/thumbs/').'/', $width, $height, $quality);
			}
		break;
		}
		
		return preg_replace('#[/]+#','/', str_replace(array(JPATH_ROOT . DIRECTORY_SEPARATOR, '\\'), array('', '/'), $path));
	}
	static public function sendMail($subject, $body, $with_admins = true, $mails = array()) {
		$params = JComponentHelper::getParams('com_yandex_maps');
		$config = JFactory::getConfig();
		$mailer = JFactory::getMailer();
		$mailer->Encoding = 'base64';
		$mailer->setSender(array(
			$params->get('mailfrom', $config->get('config.mailfrom')), 
			$params->get('fromname', $config->get('config.fromname'))
		));
        
		if (is_string($mails) or count($mails)) {
            $mailer->addRecipient(is_array($mails) ? $mails : explode(',', $mails));
        }

		if ($with_admins) {
			$users = jFactory::getDBO()->setQuery('SELECT email FROM #__users WHERE sendEmail=1')->loadColumn();
            $mailer->addRecipient($users);
		}

		$mailer->setSubject(sprintf($subject, $params->get('fromname',$config->get('config.fromname'))));
		$mailer->isHTML(true);
		$mailer->setBody($body);
		return $mailer->Send();
	} 
    
    static public function modules($position) {
        $document = JFactory::getDocument();
        $renderer = $document->loadRenderer('modules');
        $options = array('style' => 'raw');
        return $renderer->render($position, $options, null);
    }
    static public function address($name = 'location', $value = null, $config = array()) {
        $config = (object)array_merge(['autocomplete' => 1, 'width' => 300, 'height' => 300, 'autoinit' => 1], $config);
        JFormHelper::addFieldPath(JPATH_ROOT . '/administrator/components/com_yandex_maps/models/fields/');
        $form = new JForm('name');
        JHtml::addIncludePath(JPATH_ROOT.'/administrator/components/com_yandex_maps/helpers/html');
        $address = JFormHelper::loadFieldType('address', true);
        $address->setForm($form);
        $address->setup(simplexml_load_string('<field autoinit="'.$config->autoinit.'" autocomplete="'.$config->autocomplete.'" width="'.$config->width.'"  height="'.$config->height.'"/>'), null);
        $address->id = $name;
        $address->name = $name;
        $address->value = $value;
        return $address->getInput();
    } 
    static public function jstext($list, $exclude = array()) {
        if (is_string($list)) {
            $files = array(str_replace('%lang%', JFactory::getLanguage()->getTag(), $list), str_replace('%lang%', 'ru-RU', $list));

            foreach($files as $file) {
                if (file_exists($file)) {
                    $keys = array_keys(parse_ini_file($file));
                    if ($keys && is_array($keys)) {
                        return self::jstext($keys, $exclude);
                    }
                }
            }

            return JText::script($list);
        }
        foreach ($list as $item) {
            if (!in_array($item, $exclude)) {
                JText::script($item);
            }
        }
    }
}