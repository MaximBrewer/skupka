<?php
/*------------------------------------------------------------------------
 * Netbase Virtuemart Multiupload Plugin
* author : Netbase Team
* copyright Copyright (C) 2012 www.cms-extensions.net All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: www.cms-extensions.net
* Technical Support:  Forum - www.cms-extensions.net
-------------------------------------------------------------------------*/



if (file_exists(dirname(__FILE__) . '/bootstrap.php')) {
	ob_start();
	require_once dirname(__FILE__) . '/bootstrap.php';
	$bootstrap_output = ob_get_contents();
	ob_clean();
}
//insert data into media table
if (!class_exists( 'VmConfig' ))
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
/*------------------------------------------------------------------------*/

class qqUploadedFileXhr {


	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	public function checkPathCreateFolders($path){

		jimport('joomla.filesystem.folder');

		$elements = explode(DS,$path);
		$examine = JPATH_ROOT;
		foreach($elements as $piece){
			$examine = $examine.DS.$piece;
			if(!JFolder::exists($examine)){
				JFolder::create($examine);
				//echo('create folder for resized image '.$examine);
			}
		}
	}


	function save($path,$filename) {
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		if ($realSize != $this->getSize()){
			return false;
		}

		$target = fopen($path, "w");
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);


		$width 		= VmConfig::get('img_width', 90);
		$height 	= VmConfig::get('img_height', 90);
		$maxsize 	= false;
		$bgred 		= 255;
		$bggreen 	= 255;
		$bgblue 	= 255;

		jimport('joomla.filesystem.file');

		$file_name		 = JFile::makeSafe($filename);



		$file_name_thumb = JFile::stripExt($filename).'_'.$width.'x'.$height.'.'.JFile::getExt($filename);



		$media_product_path				= VmConfig::get('media_product_path','images/stories/virtuemart/product/');
		$media_product_path 			= str_replace('/',DS,$media_product_path);
		$media_product_path				= JPATH_ROOT.DS.$media_product_path;
		//$media_product_path				= JFolder::makeSafe($media_product_path);
		$this->checkPathCreateFolders($media_product_path);
		$media_product_resized_path		= $media_product_path . 'resized' .DS ;
		//$media_product_resized_path		= JFolder::makeSafe($media_product_resized_path);
		$this->checkPathCreateFolders($media_product_resized_path);
		//echo $media_product_path . $filename;
		// echo $media_product_resized_path.$file_name_thumb;
		if(JFile::exists($media_product_path . $filename)) {
			if (!class_exists('Img2Thumb')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'img2thumb.php');
			$createdImage = new Img2Thumb($media_product_path . $filename, $width, $height, $media_product_resized_path.$file_name_thumb, $maxsize, $bgred, $bggreen, $bgblue);

			if($createdImage){
				$this->json->thumb_url = $media_product_resized_path.$file_name_thumb;
			} else {
				$this->json->status = 0;
			}
		} else {
			echo $this->json->error  = 'Couldnt create thumb, file not found '.$fullSizeFilenamePath;
			$this->json->status = 0;
		}
		$virtuemart_product_ids = JRequest::getVar('virtuemart_product_id', array(), 'default', 'array');
		if(!empty($virtuemart_product_ids)) $virtuemart_product_id = $virtuemart_product_ids[0];
		$token = JRequest::getVar('token');



		$data = array(
				"searchMedia" => "",
				"media_published" => 1,
				"file_title" => $filename,
				"file_description" => "",
				"file_meta" => "",
				"file_url" => VmConfig::get('media_product_path','images/stories/virtuemart/product/').$filename,
				"file_url_thumb" => "",
				"media_roles" => "file_is_displayable",
				"media_action" => "upload",
				"file_is_product_image" => 1,
				"active_media_id" => 0,
				"virtuemart_media_id" => 0,
				"id" => 0,
				"virtuemart_product_id" => $virtuemart_product_id,
				$token => 1,
				"virtuemart_vendor_id" => 1,
				"file_mimetype" => "",
				"file_type" => "product"
		);


		$model = VmModel::getModel('Media');
		$table = $model->getTable('medias');
		$table->bind($data);
		if (!$table->store()) {
			$this->json->error = $table->getError().'<br />';
			return false;
		}

		$virtuemart_media_id = $table->virtuemart_media_id;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->insert('#__virtuemart_product_medias');
		$query->set('virtuemart_media_id = '.$db->quote($virtuemart_media_id).',virtuemart_product_id ='.$db->quote($virtuemart_product_id));

		$db->setQuery($query);
		$db->query();
		if ($db->getErrorNum())
		{
			$this->json->error = JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $db->getErrorNum(), $db->getErrorMsg()).'<br />';
			return false;
		}

		$query = $db->getQuery(true);
		$query->update('#__virtuemart_medias')
//			->set($db->nq('published').' = 1')
			->set('published = 1')
			->where('virtuemart_media_id = '.$db->quote($virtuemart_media_id));


		$db->setQuery($query);
		$db->query();
		if ($db->getErrorNum())
		{
			$this->json->error = JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $db->getErrorNum(), $db->getErrorMsg()).'<br />';
			return false;
		}
		return true;
	}


	function getName() {
		return $_GET['qqfile'];
	}


	function getSize() {
		if (isset($_SERVER["CONTENT_LENGTH"])){
			return (int)$_SERVER["CONTENT_LENGTH"];
		} else {
			throw new Exception('Getting content length is not supported.');
		}
	}
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {
	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	function save($path) {

		if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
			return false;
		}
		return true;
	}
	function getName() {
		return $_FILES['qqfile']['name'];
	}
	function getSize() {
		return $_FILES['qqfile']['size'];
	}
}

class qqFileUploader {
	private $allowedExtensions = array();
	private $sizeLimit = 10485760;
	private $file;

	function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){
		$allowedExtensions = array_map("strtolower", $allowedExtensions);

		$this->allowedExtensions = $allowedExtensions;
		$this->sizeLimit = $sizeLimit;

		$this->checkServerSettings();

		if (isset($_GET['qqfile'])) {
			$this->file = new qqUploadedFileXhr();
		} elseif (isset($_FILES['qqfile'])) {
			$this->file = new qqUploadedFileForm();
		} else {
			$this->file = false;
		}
	}

	private function checkServerSettings(){
		$postSize = $this->toBytes(ini_get('post_max_size'));
		$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

		if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
			$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
			die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
		}
	}

	private function toBytes($str){
		$val = trim($str);
		$last = strtolower($str[strlen($str)-1]);
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
		return $val;
	}

	/**
	 * Returns array('success'=>true) or array('error'=>'error message')
	 */

	function handleUpload($uploadDirectory, $replaceOldFile = FALSE){

		if (!is_writable($uploadDirectory)){
			return array('error' => "Server error. Upload directory isn't writable.");
		}

		if (!$this->file){
			return array('error' => 'No files were uploaded.');
		}

		$size = $this->file->getSize();

		if ($size == 0) {
			return array('error' => 'File is empty');
		}

		if ($size > $this->sizeLimit) {
			return array('error' => 'File is too large');
		}

		$pathinfo = pathinfo($this->file->getName());
		$filename = $pathinfo['filename'];
		//$filename = md5(uniqid());
		$ext = $pathinfo['extension'];
		//приводим к ижнему регистру
		$ext=mb_strtolower($ext);

		if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
			$these = implode(', ', $this->allowedExtensions);
			return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
		}

		if(!$replaceOldFile){
			/// don't overwrite previous files that were uploaded
			while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
				$filename .= rand(10, 99);
			}
		}
		if ($this->file->save($uploadDirectory . $filename . '.' . $ext,$filename.'.'.$ext)){
			return array('success'=>true);
		} else {
			return array('error'=> 'Could not save uploaded file.' .
					'The upload was cancelled, or server error encountered');
		}
	}
}

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array();
// max file size in bytes
$sizeLimit = 10 * 1024 * 1024;



$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);



$plugin = JPluginHelper::getPlugin('system','virtuemart_multiupload');
$vmConfig = VmConfig::loadConfig();
$media_product_path	= JPATH_BASE . DS . $vmConfig->get('media_product_path',JPATH_BASE.'/images/stories/virtuemart/product/');

//path for upload image
$result = $uploader->handleUpload($media_product_path);



header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
