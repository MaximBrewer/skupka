<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_slideshowck/helpers/defines.php';

use Slideshowck\CKInput;
use Slideshowck\CKFof;
use Slideshowck\CKText;

/**
 * Helper Class.
 */
class SlideshowckHelper {

	static $cssreplacements;

	/*
	 * Load the JS and CSS files needed to use CKBox
	 *
	 * Return void
	 */
	public static function loadCkbox() {
		$doc = JFactory::getDocument();
		JHtml::_('jquery.framework', true);
//		$doc->addScript(JUri::root(true) . '/media/jui/js/jquery.min.js');
		$doc->addStyleSheet(SLIDESHOWCK_MEDIA_URI . '/assets/ckbox.css');
		$doc->addScript(SLIDESHOWCK_MEDIA_URI . '/assets/ckbox.js');
	}

	/*
	 * Load the JS and CSS files needed to use CKBox
	 *
	 * Return void
	 */
//	public static function loadCKFramework() {
//		$doc = JFactory::getDocument();
//		$doc->addScript(JUri::root(true) . '/media/jui/js/jquery.min.js');
//		$doc->addStyleSheet(SLIDESHOWCK_MEDIA_URI . '/assets/ckframework.css');
//	}

	/*
	 * Load the JS and CSS files needed to use CKBox
	 *
	 * Return void
	 */
	/*public static function loadInlineCKFramework() {
	?>
		<script src="<?php echo JUri::root(true) ?>/media/jui/js/jquery.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="<?php echo JUri::root(true) ?>/components/com_slideshowck/assets/font-awesome.min.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo SLIDESHOWCK_MEDIA_URI ?>/assets/ckframework.css" type="text/css" />
	<?php
	}*/
	
	/**
	 * Convert a hexa decimal color code to its RGB equivalent
	 *
	 * @param string $hexStr (hexadecimal color value)
	 * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
	 * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
	 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
	 */
	static function hex2RGB($hexStr, $opacity) {
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
			return false; //Invalid hex color code
		}
		$rgbacolor = "rgba(" . $rgbArray['red'] . "," . $rgbArray['green'] . "," . $rgbArray['blue'] . "," . ($opacity / 100) . ")";

		return $rgbacolor;
	}

	/**
	 * Test if there is already a unit, else add the px
	 *
	 * @param string $value
	 * @return string
	 */
	public static function testUnit($value) {
		if ((stristr($value, 'px')) OR (stristr($value, 'em')) OR (stristr($value, '%'))) {
			return $value;
		}

		if ($value == '') {
			$value = 0;
		}

		return $value . 'px';
	}

	/**
	 * Remove special character
	 */
	public static function cleanName($path) {
		return preg_replace('/[^a-z0-9]/i', '_', $path);
	}

	public static function formatPath($p) {
		return trim(str_replace("\\", "/", $p), "/");
	}

	/**
	 * Get a subtring with the max length setting.
	 *
	 * @param string $text;
	 * @param int $length limit characters showing;
	 * @param string $replacer;
	 * @return tring;
	 */
	public static function substring($text, $length = 100, $replacer = '...', $isStrips = true, $stringtags = '') {
	
		if($isStrips){
			$text = preg_replace('/\<p.*\>/Us','',$text);
			$text = str_replace('</p>','<br/>',$text);
			$text = strip_tags($text, $stringtags);
		}
		
		if(function_exists('mb_strlen')){
			if (mb_strlen($text) < $length)	return $text;
			$text = mb_substr($text, 0, $length);
		}else{
			if (strlen($text) < $length)	return $text;
			$text = substr($text, 0, $length);
		}
		
		return $text . $replacer;
	}

	/*
	* update the table
	*/
	/*public static function createTableOptions() {
		$sqlsrc = SLIDESHOWCK_PATH . '/sql/updates/2.4.0.sql';
		$query = file_get_contents($sqlsrc);
		$db = JFactory::getDbo();
		$db->setQuery($query);
		if (!$db->execute()) {
			echo '<p class="alert alert-danger">Error during table options creation</p>';
		} else {
			echo '<p class="alert alert-success">Table options successfully created</p>';
		}
	}*/

	/**
	 * Get the name of the style
	 */
	public static function getStyleNameById($id) {
		if (! $id) return '';
		// Create a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.name');
		$query->from($db->quoteName('#__slideshowck_styles') . ' AS a');
		$query->where('(a.state IN (0, 1))');
		$query->where('a.id = ' . (int)$id);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();

		return $results;
	}

	/**
	 * Create the list of all modules published as Object
	 *
	 * $file string the image path
	 * $x integer the new image width
	 * $y integer the new image height
	 *
	 * @return Boolean True on Success
	 */
	static function resizeImage($file, $x, $y = '', $thumbpath = 'th', $thumbsuffix = '_th') {

		if (!$file)
			return;

		$thumbext = explode(".", $file);
		$thumbext = end($thumbext);
		$thumbfile = str_replace(basename($file), $thumbpath . "/" . basename($file), $file);
		$thumbfile = str_replace("." . $thumbext, $thumbsuffix . "." . $thumbext, $thumbfile);
		
		$filetmp = JPATH_ROOT . '/' . $file;
		$filetmp = str_replace("%20", " ", $filetmp);
		if (! file_exists($filetmp))
			return $file;
		$size = getimagesize($filetmp);

		if ($size[0] > $size[1] || !$y) // paysage
		{
			$y = $x * $size[1] / $size[0];
		} else 
		{
			$x = $y * $size[0] / $size[1];
		}

		
		if ($size) {
			if (JFile::exists($thumbfile)) {
				return $thumbfile;
			}
			
			$thumbfolder = str_replace(basename($file), $thumbpath . "/", $filetmp);
			if (!JFolder::exists($thumbfolder)) { 
				JFolder::create($thumbfolder);
				JFile::copy(JPATH_ROOT . '/modules/mod_slideshowck/index.html', $thumbfolder . 'index.html' );
			}

			if ($size['mime'] == 'image/jpeg') {
				$img_big = imagecreatefromjpeg($filetmp); # On ouvre l'image d'origine
				$img_new = imagecreate($x, $y);
				# création de la miniature
				$img_mini = imagecreatetruecolor($x, $y) or $img_mini = imagecreate($x, $y);
				// copie de l'image, avec le redimensionnement.
				imagecopyresized($img_mini, $img_big, 0, 0, 0, 0, $x, $y, $size[0], $size[1]);

				imagejpeg($img_mini, JPATH_ROOT . '/' . $thumbfile);
			} elseif ($size['mime'] == 'image/png') {
				$img_big = imagecreatefrompng($filetmp); # On ouvre l'image d'origine
				$img_new = imagecreate($x, $y);
				# création de la miniature
				$img_mini = imagecreatetruecolor($x, $y) or $img_mini = imagecreate($x, $y);
				// copie de l'image, avec le redimensionnement.
				imagecopyresized($img_mini, $img_big, 0, 0, 0, 0, $x, $y, $size[0], $size[1]);

				imagepng($img_mini, JPATH_ROOT . '/' . $thumbfile);
			} elseif ($size['mime'] == 'image/gif') {
				$img_big = imagecreatefromgif($filetmp); # On ouvre l'image d'origine
				$img_new = imagecreate($x, $y);
				# création de la miniature
				$img_mini = imagecreatetruecolor($x, $y) or $img_mini = imagecreate($x, $y);
				// copie de l'image, avec le redimensionnement.
				imagecopyresized($img_mini, $img_big, 0, 0, 0, 0, $x, $y, $size[0], $size[1]);

				imagegif($img_mini, JPATH_ROOT . '/' . $thumbfile);
			}
			//echo 'Image redimensionnée !';
		}

		return $thumbfile;
	}

	/*
	 * Make empty slide object
	 */
	public static function initItem() {
		$item = new stdClass();
		$item->image = null;
		$item->link = null;
		$item->title = null;
		$item->text = null;
		$item->more = array();
		$item->alignment = null;
		$item->time = null;
		$item->target = 'default';
		$item->video = null;
		$item->texttype = null;
		$item->articleid = null;

		return $item;
	}

	/*
	 * Convert an old item to the new convention
	 */
	public static function legacyUpdateItem(&$item) {
		$newItem = self::initItem();
		foreach ($newItem as $key => $value) {
			if (!isset($item->$key)) $item->$key = $value;
		}
		$item->image = $item->imgname;
		$item->link = $item->imglink;
		$item->time = $item->imgtime;
		$item->thumb = $item->imgthumb;
		$item->title = $item->imgtitle;
		$item->text = strip_tags($item->imgcaption);
	}

	/*
	 * Convert an item to the old convention to render in a V1 layout
	 * To call manually in the layout file
	 */
	public static function legacyItemForV1Layout(&$item) {
		$newItem = self::initItem();
		foreach ($newItem as $key => $value) {
			if (!isset($item->$key)) $item->$key = $value;
		}
		$item->imgname = $item->image;
		$item->imglink = $item->link;
		$item->imgtime = $item->time;
		$item->imgthumb = $item->imgname;
//		$item->imgthumb = $item->thumb;
		$item->imgtitle = $item->title;
		$item->imgcaption = $item->text;

		$item->imgalignment = $item->alignment;
		$item->imgtime = $item->time;
		$item->imgtarget = $item->target;
		$item->imgvideo = $item->video;
		$item->article = null;
	}

	/**
	 * Set the correct video link
	 *
	 * $videolink string the video path
	 *
	 * @return string the new video path
	 */
	static function setVideolink($videolink) {
		// youtube
		if (stristr($videolink, 'youtu.be')) {
			$videolink = str_replace('youtu.be', 'www.youtube.com/embed', $videolink);
		} else if (stristr($videolink, 'www.youtube.com') AND !stristr($videolink, 'embed')) {
			$videolink = str_replace('youtube.com', 'youtube.com/embed', $videolink);
		}

		$videolink .= ( stristr($videolink, '?')) ? '&wmode=transparent' : '?wmode=transparent';

		return $videolink;
	}

	/**
	 * Set the correct video link
	 *
	 * $videolink string the video path
	 *
	 * @return string the new video path
	 */
	static function setImageUrl($url) {
		if (strpos($url, 'http') !== 0) {
			$url = JUri::root(true) . '/' . trim($url, '/');
		}

		return $url;
	}

	/**
	 * Truncates text blocks over the specified character limit and closes
	 * all open HTML tags. The method will optionally not truncate an individual
	 * word, it will find the first space that is within the limit and
	 * truncate at that point. This method is UTF-8 safe.
	 *
	 * @param   string   $text       The text to truncate.
	 * @param   integer  $length     The maximum length of the text.
	 * @param   boolean  $noSplit    Don't split a word if that is where the cutoff occurs (default: true).
	 * @param   boolean  $allowHtml  Allow HTML tags in the output, and close any open tags (default: true).
	 *
	 * @return  string   The truncated text.
	 *
	 * @since   11.1
	 */
	public static function truncate($text, $length = 0, $noSplit = true, $allowHtml = true) {
		if ($length == 0) return '';
		// Check if HTML tags are allowed.
		if (!$allowHtml) {
			// Deal with spacing issues in the input.
			$text = str_replace('>', '> ', $text);
			$text = str_replace(array('&nbsp;', '&#160;'), ' ', $text);
			$text = JString::trim(preg_replace('#\s+#mui', ' ', $text));

			// Strip the tags from the input and decode entities.
			$text = strip_tags($text);
			$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

			// Remove remaining extra spaces.
			$text = str_replace('&nbsp;', ' ', $text);
			$text = JString::trim(preg_replace('#\s+#mui', ' ', $text));
		}

		// Truncate the item text if it is too long.
		if ($length > 0 && JString::strlen($text) > $length) {
			// Find the first space within the allowed length.
			$tmp = JString::substr($text, 0, $length);

			if ($noSplit) {
				$offset = JString::strrpos($tmp, ' ');
				if (JString::strrpos($tmp, '<') > JString::strrpos($tmp, '>')) {
					$offset = JString::strrpos($tmp, '<');
				}
				$tmp = JString::substr($tmp, 0, $offset);

				// If we don't have 3 characters of room, go to the second space within the limit.
				if (JString::strlen($tmp) > $length - 3) {
					$tmp = JString::substr($tmp, 0, JString::strrpos($tmp, ' '));
				}
			}

			if ($allowHtml) {
				// Put all opened tags into an array
				preg_match_all("#<([a-z][a-z0-9]*)\b.*?(?!/)>#i", $tmp, $result);
				$openedTags = $result[1];
				$openedTags = array_diff($openedTags, array("img", "hr", "br"));
				$openedTags = array_values($openedTags);

				// Put all closed tags into an array
				preg_match_all("#</([a-z]+)>#iU", $tmp, $result);
				$closedTags = $result[1];

				$numOpened = count($openedTags);

				// All tags are closed
				if (count($closedTags) == $numOpened) {
					return $tmp . '...';
				}
				$tmp .= '...';
				$openedTags = array_reverse($openedTags);

				// Close tags
				for ($i = 0; $i < $numOpened; $i++) {
					if (!in_array($openedTags[$i], $closedTags)) {
						$tmp .= "</" . $openedTags[$i] . ">";
					} else {
						unset($closedTags[array_search($openedTags[$i], $closedTags)]);
					}
				}
			}

			$text = $tmp;
		}

		return $text;
	}

	static function getArticle($item) {
		// load the content articles file
		$com_path = JPATH_SITE . '/components/com_content/';
		require_once $com_path . 'router.php';
		require_once $com_path . 'helpers/route.php';
		JModelLegacy::addIncludePath($com_path . '/models', 'ContentModel');
		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		// Get an instance of the generic articles model
		$articles = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
		// Set application parameters in model
		$app = JFactory::getApplication();
//		$appParams = $app->getParams();
		$articles->setState('params', JComponentHelper::getParams('com_content'));
//		$articles->setState('params', $appParams);
		$articles->setState('filter.published', 1);
		$articles->setState('filter.article_id', $item->slidearticleid);
		$items2 = $articles->getItems();
		$item->article = $items2[0];
		$item->text = JHTML::_('content.prepare', $item->article->introtext);
		$item->title = $item->article->title;
		// set the item link to the article depending on the user rights
		if ($access || in_array($item->article->access, $authorised)) {
			// We know that user has the privilege to view the article
			$item->slug = $item->article->id . ':' . $item->article->alias;
			$item->catslug = $item->article->catid ? $item->article->catid . ':' . $item->article->category_alias : $item->article->catid;
			$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
		} else {
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$menuitems = $menu->getItems('link', 'index.php?option=com_users&view=login');
			if (isset($menuitems[0])) {
				$Itemid = $menuitems[0]->id;
			} elseif (JRequest::getInt('Itemid') > 0) {
				$Itemid = JRequest::getInt('Itemid');
			}
			$item->link = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $Itemid);
		}
		return $item;
	}

	/**
	 * List the replacement between the tags and the real final CSS rules
	 */
	public static function getCssReplacement() {
		if (! empty(self::$cssreplacements)) return self::$cssreplacements;

		self::$cssreplacements = Array(
			'[container]' => ''
			,'[slide]' => '.cameraSlide img'
			,'[caption]' => '.camera_caption > div'
			,'[title]' => '.camera_caption_title'
			,'[text]' => '.camera_caption_desc'
			,'[button]' => 'a.camera-button'
			,'[buttonhover]' => 'a.camera-button:hover'
//			,'[thumbs]' => '.camera_pag_ul li img'
			,'[paginationdotthumbs]' => '.camera_pag_ul li img'
		);

		return self::$cssreplacements;
	}

	/**
	 * Get the CSS of the style
	 * @id - the style ID
	 */
	public static function getStyleLayoutcss($id) {
		if (! $id) return '';

		// Create a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.layoutcss');
		$query->from($db->quoteName('#__slideshowck_styles') . ' AS a');
		$query->where('(a.state IN (0, 1))');
		$query->where('a.id = ' . (int)$id);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$result = $db->loadResult();

		self::makeCssReplacement($result);

		return $result;
	}

	public static function makeCssReplacement(&$css) {
		$cssreplacements = self::getCssReplacement();
		foreach ($cssreplacements as $tag => $rep) {
			$css = str_replace($tag, $rep, $css);
		}
//		return $css;
	}

	public static function getProMessage() {
		$html = '<div class="ckinfo"><i class="fas fa-info"></i><a href="https://www.joomlack.fr/en/joomla-extensions/slideshow-ck" target="_blank">' . JText::_('SLIDESHOWCK_ONLY_PRO') . '</a></div>';
	
		return $html;
	}

	public static function getProPreview($imgName) {
		$html = '<div class="ckpropreview">'
					. '<img src="' . SLIDESHOWCK_MEDIA_URL . '/images/proonly/' . $imgName . '" />'
					. '<a href="https://www.joomlack.fr/en/joomla-extensions/slideshow-ck" target="_blank">' . JText::_('SLIDESHOWCK_ONLY_PRO') . '</a>'
				. '</div>';
	
		return $html;
	}
}
