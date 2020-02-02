<?php

/**
 * @package 	formfields
 * @version	1.2
 * @created	Aug 2012
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support     Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license     http://bowthemes.com/terms-and-conditions.html
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');
jimport('joomla.html.parameter');
require_once JPATH_ROOT . '/modules/mod_bt_backgroundslideshow/helpers/images.php';

class JFormFieldAjax extends JFormField {

    protected $type = 'ajax';
    private $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    private $saveDir = '';
    private $result = array('success' => false, 'message' => '');
    private $items = array();
	private $moduleID = 0;
	
    protected function getInput() {
        /**
         * Lấy các ảnh đã có 
         */
        $this->params = $this->form->getValue('params');
        $items = json_decode(base64_decode($this->params->gallery));
        foreach ($items as $item) {
            $this->items[] = $item->file;
        }
        $this->saveDir = JPATH_ROOT .'/modules/mod_bt_backgroundslideshow/images';
        if (JRequest::get('post') && JRequest::getString('action')) {
			$obLevel = ob_get_level();
			while ($obLevel > 0 ) {
				ob_end_clean();
				$obLevel --;
            }
            echo self::doPost();
            exit;
        }
    }

    private function doPost() {

        /**
         * xử lý dành cho load photset của flickr và album của photoset
         */
        $this->moduleID = JRequest::getInt('id', 0);
        $validated = false;
        if (JRequest::getString('action') == 'load_options') {
            $arrOptions = array();
            //danh cho source flickr
            if (JRequest::getString('flickrAPI')) {
				$arrOptions = $this->getFlickrPhotosets();
               
            } else if (JRequest::getString('picasaUserID')) {
				$arrOptions = $this->getPicasaAlbums();
            } else if (JRequest::getString('yt_user')){
				$arrOptions = $this->getYoutubePlaylists();
			}
            if (count($arrOptions) > 0) {
                $this->result['success'] = true;
                $this->result['options'] = $arrOptions;
            }
            return json_encode($this->result);
        }
        /**
         * Nếu không phải là lấy thông tin bài viết (chỉ có ở admin)
         */ 
		else if (JRequest::getString('action') != 'get_article') {
            //kiem tra quyen ghi cua cac thu muc trong thu muc images
            if (!$this->isWritable()) {
                return json_encode($this->result);
            } else {
                /**
                 * Check login & permission
                 */
                $isAdmin = JFactory::getApplication()->isAdmin();
                if (!$isAdmin) {
                    $this->result['message'] = JText::_('JERROR_ALERTNOAUTHOR');
                    $validated = false;
                    return json_encode($this->result);
                } else {
                    /**
                     * Xử lý dành riêng cho uploadify
                     */
                    //nếu là uploadify
                    if (JRequest::getString('action') == 'uploadify' && !empty($_FILES)) {
						return $this->uploadify();
                    } else {
                        //lay tham so
                        $feedTitle = true;
                        $getLimit = JRequest::getInt('get_limit', false);

                        /**
                         * Xử lý danh cho get list ảnh
                         * Nếu không phải là upload file và delete ( tuc la lay danh sach các file ảnh có thể có)
                         */
                        if (JRequest::getString('action') == 'get') {
                            //array photo getted
                            $photos = array();
                            //nếu source là joomla folder
                            if (JRequest::getString('jFolderPath')) {
                                $this->getImagesFromJFolder($photos);
                            }
                            //danh cho source flikr
                            if (JRequest::getString('flickrAPI')) {
								$this->getImagesFromFlickr($photos);
                            }
                            //if source is picasa
                            if (JRequest::getString('picasaUserID')) {
                                $this->getImagesFromPicasa($photos);
                            }
                            //nếu source là phoca
                            if (JRequest::getString('phoca_catid') !== '') {
                                $this->getImagesFromPhoca($photos);
                            }//end of phoca
                            //if source is joom gallery
                            if (JRequest::getString('jgallery_catid') !== '') {
                                $this->getImagesFromJoomGallery($photos);
                            }//end of jqallery
							
							if (JRequest::getString('playlist_id') !== ''){
								$this->getVideoFromPlaylistId($photos);
							}
							
							if (JRequest::getString('video_id') !== ''){
								$this->getVideo($photos);
							}
							
                            /**
                             * Xử lý ảnh source photo đã lấy về
                             */
                            if ($getLimit) {
                                $photos = array_slice($photos, 0, $getLimit);
                            }
                            if (count($photos) > 0) {
                                $this->result['success'] = true;
                                $this->result['files'] = json_encode($photos);
								
                            } else {
                                if (!isset($this->result['message']) || $this->result['message'] == '')
                                    $this->result["message"] = JText::_('MOD_BTBGSLIDESHOW_ERROR_NO_IMAGE');
                            }
                            return json_encode($this->result);
                        } else if (JRequest::getString('action') == 'upload' && JRequest::getString('btfile')) {
                            return $this->upload();
                        } else if (JRequest::getString('action') == 'delete') {
                           return $this->delete();
                        }
                    }
                }
            }
        } else if (JRequest::getString('action') == 'get_article') {
			return $this->getArticle();
            
        }
    }
	
	/**
	 * Handle ajax request to get Flickr Photosets
	 */ 
	function getFlickrPhotosets(){
		$arrOptions = array();
		$params = array(
			'api_key' => JRequest::getString('flickrAPI'),
			'format' => 'php_serial'
		);

		if (!JRequest::getString('photosetid')) {
			//lay user id tu username
			$params['method'] = 'flickr.people.findByEmail';
			$params['find_email'] = JRequest::getString('flickrUserID');
			$encoded_params = array();
			foreach ($params as $k => $v) {
				$encoded_params[] = urlencode($k) . '=' . urlencode($v);
			}
			$url = "http://api.flickr.com/services/rest/?" . implode('&', $encoded_params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$rsp = curl_exec($ch);					
			curl_close($ch);
			$objRSP = unserialize($rsp);
			if ($objRSP['stat'] == 'ok') {
				$params['user_id'] = $objRSP['user']['id'];
			}
			$params['method'] = 'flickr.photosets.getList';
			$encoded_params = array();
			foreach ($params as $k => $v) {
				$encoded_params[] = urlencode($k) . '=' . urlencode($v);
			}
			$url = "http://api.flickr.com/services/rest/?" . implode('&', $encoded_params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$rsp = curl_exec($ch);					
			curl_close($ch);
			$objRSP = unserialize($rsp);
			if ($objRSP['stat'] == 'ok') {
				foreach ($objRSP['photosets']['photoset'] as $photoSet) {
					$objOption = new stdClass();
					$objOption->value = $photoSet['id'];
					$objOption->text = $photoSet['title']['_content'];
					$arrOptions[] = $objOption;
				}
			} else {
				$this->result['message'] = $objRSP['message'];
			}
		} else {
			$params['method'] = 'flickr.photosets.getInfo';
			$params['photoset_id'] = JRequest::getString('photosetid');
			$encoded_params = array();
			foreach ($params as $k => $v) {
				$encoded_params[] = urlencode($k) . '=' . urlencode($v);
			}
			$url = "http://api.flickr.com/services/rest/?" . implode('&', $encoded_params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$rsp = curl_exec($ch);					
			curl_close($ch);
			$objRSP = unserialize($rsp);
			if ($objRSP['stat'] == 'ok') {
				$objOption = new stdClass();
				$objOption->value = JRequest::getString('photosetid');
				$objOption->text = $objRSP['photoset']['title']['_content'];
				$arrOptions[] = $objOption;
			} else {
				$this->result['message'] = $objRSP['message'];
			}
		}
		if (count($arrOptions) > 0) {
			$arrOptions = array_reverse($arrOptions);
			$objOption = new stdClass();
			$objOption->value = 0;
			$objOption->text = JText::_('MOD_BTBGSLIDESHOW_FLICKR_ALL_PHOTOSETS');
			$arrOptions[] = $objOption;
			$arrOptions = array_reverse($arrOptions);
		}
		return $arrOptions;
	}
	
	/**
	 * Handle ajax request to get Picasa Albums
	 */
	function getPicasaAlbums(){
		$arrOptions = array();
		// build feed URL
		$userid = JRequest::getString('picasaUserID');
		$feedURL = 'http://picasaweb.google.com/data/feed/api/user/' . $userid . '?alt=rss&kind=album';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $feedURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$tmp = curl_exec($ch);
		curl_close($ch);
		@$sxml = simplexml_load_string($tmp);
		if (isset($sxml) && $sxml) {
			foreach ($sxml->channel->item as $entry) {
				$guid = (string) $entry->guid;
				$albumID = substr($guid, strrpos($guid, '/') + 1, strrpos($guid, '?') - 1 - strrpos($guid, '/'));
				$objOption = new stdClass();
				$objOption->value = $albumID;
				$objOption->text = (string) $entry->title;
				$arrOptions[] = $objOption;
			}
			if (count($arrOptions) > 0) {
				$arrOptions = array_reverse($arrOptions);
				$objOption = new stdClass();
				$objOption->value = 0;
				$objOption->text = JText::_('MOD_BTBGSLIDESHOW_PICASA_ALL_ALBUMS');
				$arrOptions[] = $objOption;
				$arrOptions = array_reverse($arrOptions);
			}
		} else {
			$this->result['message'] = 'Unable to find user with email ' . $userid . '@gmail.com';
		}
		return $arrOptions;
	}
	
	
	/**
	 * Handle ajax request to get article infomation
	 */
	function getArticle(){
		// Check login & permission
		$user = JFactory::getUser();
		if (!$user->id) {
			$this->result['message'] = JText::_('MOD_BTBGSLIDESHOW_ERROR_NOT_LOGIN');
			$this->result['success'] = false;
			return json_encode($this->result);
		} else {
			if (JRequest::getString('article_id')) {
				$id = JRequest::getString('article_id');
				require_once JPATH_ROOT . '/modules/mod_bt_backgroundslideshow/helpers/helper.php';
				$helper = new BTBgSlideShowHelper();
				if (JRequest::getString('k2') && JRequest::getString('k2') == 1) {
					$article = $helper->getK2ArticleInfo($id);
					$desc = $helper->truncate_string($article[0], 300);
					require_once JPATH_ROOT . '/components/com_k2/helpers/route.php';
					$this->result['success'] = true;
					$this->result['link'] = K2HelperRoute::getItemRoute($id, $article[1]);
					$this->result['desc'] = strip_tags($desc, '<a><i><b><u><strong>');
				} else {
					require_once JPATH_SITE . '/components/com_content/helpers/route.php';
					$article = $helper->getArticleInfo($id);
					$desc = $helper->truncate_string($article[0], 300);
					$articleSlug = $id . ':' . $article[1];
					$catSlug = $article[2] . ':' . $article[3];

					$this->result['success'] = true;
					$this->result['link'] = ContentHelperRoute::getArticleRoute($articleSlug, $catSlug);
					$this->result['desc'] = strip_tags($desc, '<a><i><b><u><strong>');
				}
				return json_encode($this->result);
			} else {
				$result['message'] = JText::_('Have some errors.');
				$result['success'] = false;
				return json_encode($this->result);
			}
		}
	}
	
	/**
	 * Handle uploadify ajax request
	 */
	function uploadify(){
		$validated = true;
		$file = $_FILES['Filedata']['tmp_name'];

		$objFile = new stdClass();
		$extension = explode('.', $_FILES['Filedata']['name']);
		$extension = strtolower($extension[count($extension) - 1]);
		if (in_array($extension, $this->allowedExtensions)) {
			$hashedName = md5($this->moduleID . '-' . 'upload-' . substr($_FILES['Filedata']['name'], 0, strrpos($_FILES['Filedata']['name'], '.')));
			$filename = "{$hashedName}.{$extension}";
			if (!in_array($filename, $this->items)) {

				if (!JFile::upload($file, "{$this->saveDir}/tmp/original/{$filename}")) {
					$this->result['message'] = JText::_('MOD_BTBGSLIDESHOW_ERROR_COULD_NOT_SAVE');
					$validated = false;
				} else {
					BTImageHelper::resize($this->saveDir . "/tmp/original/{$filename}", $this->saveDir . "/tmp/manager/{$filename}", 128, 96);
					$objFile->filename = $filename;
					$objFile->title = $filename;
				}
			} else {
				$this->result['message'] = JText::_('MOD_BTBGSLIDESHOW_FILE_EXISTED');
				$validated = false;
			}
		} else {
			$this->result['message'] = JText::_('MOD_BTBGSLIDESHOW_FILE_EXTENSION_INVALID');
			$validated = false;
		}
		if ($validated) {
			$this->result["success"] = true;
			$this->result["files"] = $objFile;
		}
		return json_encode($this->result);
	}
	
	/**
	 * Get images from Joomla Folder
	 */
	function getImagesFromJFolder(& $photos){
		if(!is_array($photos)) $photos = array();
		//lay tham so jFolderPath
		$jFolderPath = JPATH_ROOT . '/' . JRequest::getString('jFolderPath');
		$JFolderURI = JURI::root() . JRequest::getString('jFolderPath');
		//check validated folder
		if (is_dir($jFolderPath)) {
			$open = opendir($jFolderPath);
			$filename = readdir($open);
			while ($filename !== false) {
				//check validated file
				if (filetype($jFolderPath .'/'. $filename) == "file") {
					$file = $JFolderURI . '/' . $filename;
					$fileInfo = pathinfo($file);
					$hashedName = md5($this->moduleID . '-' . 'jfolder-' . JRequest::getString('jFolderPath') . '-' . $fileInfo['filename']);
					if (
							$file
							&& in_array(strtolower($fileInfo["extension"]), $this->allowedExtensions)
							&& !JFile::exists($this->saveDir . "/tmp/manager/{$hashedName}.{$fileInfo["extension"]}")
							&& !in_array($hashedName . '.' . $fileInfo["extension"], $this->items)
					) {
						$objFile = new stdClass();
						$objFile->file = $file;
						$objFile->title = $fileInfo['filename'] ;
						$objFile->source = 'jfolder-' . JRequest::getString('jFolderPath');
						$photos[] = $objFile;
					}
				}
				$filename = readdir($open);
			}
			closedir($open);
		} else {
			$this->result['message'] = JText::_('MOD_BTBGSLIDESHOW_ERROR_DIRECTORY');
			$validated = false;
		}
	}
	
	/**
	 * Get Images From Flickr
	 */
	function getImagesFromFlickr(& $photos){
		if(!is_array($photos)) $photos = array();
		$params = array(
			'api_key' => JRequest::getString('flickrAPI'),
			'format' => 'php_serial'
		);
		$arrPhotoSetIDs = array();
		if (!JRequest::getString('photosetid')) {
			//lay user id tu username
			$params['method'] = 'flickr.people.findByEmail';
			$params['find_email'] = JRequest::getString('flickrUserID');
			$encoded_params = array();
			foreach ($params as $k => $v) {
				$encoded_params[] = urlencode($k) . '=' . urlencode($v);
			}
			$url = "http://api.flickr.com/services/rest/?" . implode('&', $encoded_params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$rsp = curl_exec($ch);					
			curl_close($ch);
			$objRSP = unserialize($rsp);
			if ($objRSP['stat'] == 'ok') {
				$params['user_id'] = $objRSP['user']['id'];
			}
			$params['method'] = 'flickr.photosets.getList';
			$encoded_params = array();
			foreach ($params as $k => $v) {
				$encoded_params[] = urlencode($k) . '=' . urlencode($v);
			}
			$url = "http://api.flickr.com/services/rest/?" . implode('&', $encoded_params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$rsp = curl_exec($ch);					
			curl_close($ch);
			$objRSP = unserialize($rsp);
			if ($objRSP['stat'] == 'ok') {
				foreach ($objRSP['photosets']['photoset'] as $photoSet) {
					$arrPhotoSetIDs[$photoSet['id']] = $photoSet['title']['_content'];
				}
			} else {
				$this->result["message"] = $objRSP['message'];
			}
		} else {
			$params['method'] = 'flickr.photosets.getInfo';
			$params['photoset_id'] = JRequest::getString('photosetid');
			$encoded_params = array();
			foreach ($params as $k => $v) {
				$encoded_params[] = urlencode($k) . '=' . urlencode($v);
			}
			$url = "http://api.flickr.com/services/rest/?" . implode('&', $encoded_params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$rsp = curl_exec($ch);					
			curl_close($ch);
			$objRSP = unserialize($rsp);
			if ($objRSP['stat'] == 'ok') {
				$arrPhotoSetIDs[JRequest::getString('photosetid')] = $objRSP['photoset']['title']['_content'];
			} else {
				$this->result["message"] = $objRSP['message'];
			}
		}
		foreach ($arrPhotoSetIDs as $photoSetID => $photoSetName) {
			$params['method'] = 'flickr.photosets.getPhotos';
			$params['photoset_id'] = $photoSetID;
			$encoded_params = array();
			foreach ($params as $k => $v) {
				$encoded_params[] = urlencode($k) . '=' . urlencode($v);
			}
			$url = "http://api.flickr.com/services/rest/?" . implode('&', $encoded_params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$rsp = curl_exec($ch);					
			curl_close($ch);
			$objRSP = unserialize($rsp);

			if ($objRSP['stat'] == 'ok') {
				foreach ($objRSP['photoset']['photo'] as $photo) {
					if (isset($photo['originalsecret']) && isset($photo['originalformat'])) {
						$file = "http://farm" . $photo['farm'] . ".static.flickr.com/" . $photo['server'] . "/" . $photo->id . "_" . $photo['originalsecret'] . "_o." . $photo['originalformat'];
					} else {
						$file = "http://farm" . $photo['farm'] . ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'] . "_b.jpg";
					}
					$fileInfo = pathinfo($file);
					$hashedName = md5($this->moduleID . '-' . 'flickr-' . $photoSetName . '-' . $fileInfo['filename']);
					if (
						$file
						&& !JFile::exists($this->saveDir . "/tmp/manager/{$hashedName}.{$fileInfo["extension"]}")
						&& !in_array($hashedName . '.' . $fileInfo["extension"], $this->items)
					) {
						$objFile = new stdClass();
						$objFile->file = $file;
						$objFile->title = $photo['title'];
						$objFile->source = 'flickr-' . $photoSetName;
						$photos[] = $objFile;
					}
				}
			} else {
				$this->result["message"] = $objRSP['message'];
			}
		}
	}
	
	/**
	 * Get images from Picasa
	 */
	function getImagesFromPicasa(& $photos){
		if(!is_array($photos)) $photos = array();
		// build feed URL
		$userid = JRequest::getString('picasaUserID');
		$arrFeedURLs = array();
		if (JRequest::getString('albumid')) {
			$arrFeedURLs[] = 'http://picasaweb.google.com/data/feed/base/user/' . $userid . '/albumid/' . JRequest::getString('albumid') . '?alt=rss';
		} else {
			$feedURL = 'http://picasaweb.google.com/data/feed/api/user/' . $userid . '?alt=rss&kind=album';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $feedURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$tmp = curl_exec($ch);
			curl_close($ch);
			@$sxml = simplexml_load_string($tmp);
			if (isset($sxml) && $sxml) {
				foreach ($sxml->channel->item as $entry) {
					$guid = (string) $entry->guid;
					$albumID = substr($guid, strrpos($guid, '/') + 1, strrpos($guid, '?') - 1 - strrpos($guid, '/'));
					$arrFeedURLs[] = 'http://picasaweb.google.com/data/feed/base/user/' . $userid . '/albumid/' . $albumID . '?alt=rss';
				}
			} else {
				$this->result["success"] = false;
				$this->result['message'] = 'Unable to find user with email ' . $userid . '@gmail.com';
			}
		}
		foreach ($arrFeedURLs as $feedURL) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $feedURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$tmp = curl_exec($ch);
			curl_close($ch);
			@$sxml = simplexml_load_string($tmp);
			if ($sxml) {
				foreach ($sxml->channel->item as $entry) {
					$media = $entry->children('http://search.yahoo.com/mrss/');
					$file = (string) $media->group->content->attributes()->url;
					$fileInfo = pathinfo($file);
					$hashedName = md5($this->moduleID . '-' . 'picasa-' . (string) $sxml->channel->title . '-' . $fileInfo['filename']);
					if (
							$file
							&& !JFile::exists($this->saveDir . "/tmp/manager/{$hashedName}.{$fileInfo["extension"]}")
							&& !in_array($hashedName . '.' . $fileInfo["extension"], $this->items)
					) {
						$objFile = new stdClass();
						$objFile->file = $file;
						$objFile->title = (string) $entry->title;
						$objFile->source = 'picasa-' . (string) $sxml->channel->title;
						$photos[] = $objFile;
					}
				}
			}
		}
	}
	
	/**
	 * Get images from Phoca Gallery Component
	 */
	function getImagesFromPhoca(& $photos){
		if(!is_array($photos)) $photos = array();
		require_once JPATH_ROOT . '/modules/mod_bt_backgroundslideshow/helpers/helper.php';
		$helper = new BTBgSlideShowHelper();
		if (!$helper->checkPhocaComponent()) {
			$this->result["message"] = JText::_('MOD_BTBGSLIDESHOW_COM_PHOCA_NOT_EXIST');
		} else {
			$rs = $helper->getPhocaPhotos(JRequest::getString('phoca_catid'));

			if (count($rs) > 0) {
				foreach ($rs as $photo) {
					$file = JURI::root() . "images/phocagallery/" . $photo->filename;
					$fileInfo = pathinfo($file);
					$hashedName = md5($this->moduleID . '-' . 'phoca-' . $photo->cat_name . '-' . $fileInfo['filename']);
					if (
							$file
							&& !JFile::exists($this->saveDir . "/tmp/manager/{$hashedName}.{$fileInfo["extension"]}")
							&& !in_array($hashedName . '.' . $fileInfo["extension"], $this->items)
					) {
						$objFile = new stdClass();
						$objFile->file = $file;
						$objFile->title = $photo->title;
						$objFile->source = 'phoca-' . $photo->cat_name;
						$photos[] = $objFile;
					}
				}
			}
		}
	}
	
	/**
	 * Get images from JoomGallery Component
	 */
	function getImagesFromJoomGallery(& $photos){
		if(!is_array($photos)) $photos = array();
		require_once JPATH_ROOT . '/modules/mod_bt_backgroundslideshow/helpers/helper.php';
		$helper = new BTBgSlideShowHelper();
		if (!$helper->checkJGalleryComponent()) {
			$this->result["success"] = false;
			$this->result["message"] = JText::_('MOD_BTBGSLIDESHOW_COM_JOOMGALLERY_NOT_EXIST');
		} else {
			$rs = $helper->getJoomGalleryPhotos(JRequest::getString('jgallery_catid'));
			if (count($rs) > 0) {
				foreach ($rs as $photo) {
					$file = JURI::root() . "images/joomgallery/originals/" . $photo->cat_name . '/' . $photo->filename;
					$fileInfo = pathinfo($file);
					$hashedName = md5($this->moduleID . '-' . 'jgallery-' . $photo->cat_name . '-' . $fileInfo['filename']);
					if (
							$file
							&& !JFile::exists($this->saveDir . "/tmp/manager/{$hashedName}.{$fileInfo["extension"]}")
							&& !in_array($hashedName . '.' . $fileInfo["extension"], $this->items)
					) {
						$objFile = new stdClass();
						$objFile->file = $file;
						$objFile->title = $photo->title;
						$objFile->source = 'jgallery-' . $photo->cat_name;
						$photos[] = $objFile;
					}
				}
			}
		}
	}
	
	/**
	 * Upload image
	 */
	function upload(){
		$file = JRequest::getString('btfile');
		$source = JRequest::getString('source');
		$videoId = JRequest::getString('videoid');
		$fileInfo = pathinfo($file);
		$objFile = new stdClass();
		$hashedName = md5($this->moduleID . '-' . $source . '-' . $fileInfo['filename']);
		if($videoId) $hashedName = md5($this->moduleID . '-' . $source . '-' . $videoId);
		$filename = $hashedName . '.' . $fileInfo["extension"];
		if (JFile::exists($this->saveDir . "/tmp/manager/{$filename}") || in_array($filename, $this->items)){
			$this->result['message'] = JText::_('MOD_BTBGSLIDESHOW_FILE_EXISTED');
			$validated = false;
		}else {
			if(JRequest::getString('remote')){
				BTImageHelper::resize($file, $this->saveDir . "/tmp/manager/{$filename}", 128, 96);
					$objFile->filename = $filename;
					$objFile->title = (JRequest::getString('title')) ? JRequest::getString('title') : '';
					$objFile->remote = $file;
					if($videoId) $objFile->videoId = $videoId;
					$validated = true;
			}else{
				//neu chua co file nay trong thu muc original thi load file do vao trong thu muc tmp
				if (!copy($file, "{$this->saveDir}/tmp/original/{$filename}")) {
					$this->result['message'] = JText::_('MOD_BTBGSLIDESHOW_ERROR_COULD_NOT_SAVE');
					$validated = false;
				} else {
					BTImageHelper::resize($this->saveDir . "/tmp/original/{$filename}", $this->saveDir . "/tmp/manager/{$filename}", 128, 96);
					$objFile->filename = $filename;
					$objFile->title = (JRequest::getString('title')) ? JRequest::getString('title') : '';
					if($videoId) $objFile->videoId = $videoId;  
					$validated = true;
				}
			}
		}
		
		if ($validated) {
			$this->result['success'] = true;
			$this->result['files'] = $objFile;
		}
		return json_encode($this->result);
	}
	
	/**
	 * Delete image
	 */
	function delete(){
		$file = JRequest::getString('file');                            
		try {
			JFile::delete($this->saveDir . '/manager/' . $file);
			JFile::delete($this->saveDir . '/original/' . $file);
			JFile::delete($this->saveDir . '/slideshow/' . $file);
			JFile::delete($this->saveDir . '/thumbnail/' . $file);
			JFile::delete($this->saveDir . '/tmp/manager/' . $file);
			JFile::delete($this->saveDir . '/tmp/original/' . $file);
			$this->result['success'] = true;
			$objFile = new stdClass();
			$objFile->filename = $file;
			$this->result["files"] = $objFile;
		} catch (Exception $ex) {
			$this->result['message'] = $ex->getMessage();
			$validated = false;
		}
		return json_encode($this->result);
	}
	
	/**
	 * Get video from playlist
	 */
	function getVideoFromPlaylistId(& $videos){
		if(!is_array($videos)) $videos = array();
		$playlistId = JRequest::getString('playlist_id');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, 'http://gdata.youtube.com/feeds/api/playlists/' . $playlistId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$tmp = curl_exec($ch);
		curl_close($ch);
		@$pl_info = simplexml_load_string($tmp);
		
        if ($pl_info) {
            $entries = $pl_info->entry;
            foreach ($entries as $entry) {
                $links = $entry->link;
                foreach ($links as $link) {
                    if ($link['rel'] == "related") {
                        $parser_link = explode("/", $link['href']);
                        $videoid = $parser_link[count($parser_link) - 1];
                        if ($videoid != "") {
						
							$hashedName = md5($this->moduleID . '-youtube-' . $videoid);
							$filename = $hashedName . '.jpg';
							
							if (!JFile::exists($this->saveDir . '/tmp/manager/' . $filename) && !JFile::exists($this->saveDir . '/manager/' . $filename)) {
								
								$urlFeed = 'http://gdata.youtube.com/feeds/api/videos/' . $videoid;
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_HEADER, 0);
								curl_setopt($ch, CURLOPT_URL, $urlFeed);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								$tmp = curl_exec($ch);
								curl_close($ch);
								@$videoInfo = simplexml_load_string($tmp);
								if($videoInfo){								
									$videoObj = new stdClass();
									$videoObj->file = 'http://img.youtube.com/vi/' . $videoid . '/0.jpg';
									$videoObj->title = (string) $videoInfo->title ;
									$videoObj->source = 'youtube';
									$videoObj->videoId = $videoid;
									$videos[] = $videoObj;
									
								}
							}
                        }
                    }
					
                }
			
            }
			
        }	
	
	}
	
	/**
	 * Get video from playlist
	 */
	function getVideo(& $videos){
		if(!is_array($videos)) $videos = array();
		if (JRequest::getString('video_id') != "") {
			$videoid = JRequest::getString('video_id');
			$hashedName = md5($this->moduleID . '-youtube-' . $videoid);
			$filename = $hashedName . '.jpg';
			if (!JFile::exists($this->saveDir . '/tmp/manager' . $filename) && !JFile::exists($this->saveDir . '/manager' . $filename)) {
				
				
				$urlFeed = 'http://gdata.youtube.com/feeds/api/videos/' . $videoid;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_URL, $urlFeed);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$tmp = curl_exec($ch);
				curl_close($ch);
				@$videoInfo = simplexml_load_string($tmp);
				if($videoInfo){								
					$videoObj = new stdClass();
					$videoObj->file = 'http://img.youtube.com/vi/' . $videoid . '/0.jpg';
					$videoObj->title = (string) $videoInfo->title ;
					$videoObj->source = 'youtube';
					$videoObj->videoId = $videoid;
					$videos[] = $videoObj;
					
				}
			}
		}
	}
	
	/**
	 * Check writable permission 
	 */
	function isWritable(){
		if (
			!is_writable($this->saveDir .'/tmp')
			|| !is_writable($this->saveDir . '/slideshow')
			|| !is_writable($this->saveDir . '/thumbnail')
			|| !is_writable($this->saveDir . '/manager')
			|| !is_writable($this->saveDir . '/original')
		) {
			$this->result['message'] = JText::_('MOD_BTBGSLIDESHOW_ERROR_SAVE_DIR_NOT_WRITABLE');
			return false;
		}
		return true;
	}
}

?>