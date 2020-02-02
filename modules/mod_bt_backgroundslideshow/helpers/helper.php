<?php

/**
 * @package 	helpers
 * @version		1.0
 * @created		Apr 2012
 * @author		BowThemes
 * @email		support@bowthems.com
 * @website		http://bowthemes.com
 * @support		Forum - http://bowthemes.com/forum/
 * @copyright	Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */
class BTBgSlideShowHelper {

    public static function getPhocaPhotos($catid = 0) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = 'SELECT pg.filename as filename, pg.title as title, pgc.title as cat_name
                  FROM #__phocagallery as pg LEFT JOIN #__phocagallery_categories as pgc
                  ON pg.catid = pgc.id';
        if ($catid != 0)
            $query .= ' WHERE pg.catid = ' . $catid;
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getPhocaCategories() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = 'SELECT id, title FROM #__phocagallery_categories';
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function checkPhocaComponent() {
        $path = JPATH_ADMINISTRATOR . '/components/com_phocagallery';
        if (is_dir($path) && file_exists(JPATH_ADMINISTRATOR .'/components/com_phocagallery/libraries/loader.php')) {
            return true;
        } else {
            return false;
        }
    }

    public static function checkJGalleryComponent() {
        $path = JPATH_ADMINISTRATOR . '/components/com_joomgallery';
        if (is_dir($path) && file_exists(JPATH_ADMINISTRATOR . '/components/com_joomgallery/includes/defines.php')) {
            return true;
        } else {
            return false;
        }
    }

    public static function getJoomGalleryPhotos($jcatid = 0) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = 'SELECT jg.imgtitle as title, jg.imgfilename as filename, jgc.catpath as cat_name';
        $query.= ' FROM #__joomgallery as jg LEFT JOIN #__joomgallery_catg as jgc ON jg.catid = jgc.cid';
        $query.= ( $jcatid != 0) ? ' WHERE jg.catid = ' . $jcatid : '';
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getJoomlaGalleryCategories() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = 'SELECT cid, name FROM #__joomgallery_catg';
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function getPhotos($params) {
        $photos = $params->get('gallery');
        $photos = json_decode(base64_decode($photos));
        switch ($params->get('display_order', 'ordering')) {
            case 'title_asc':
                for ($i = 0; $i < count($photos) - 1; $i++) {
                    for ($j = $i + 1; $j < count($photos); $j++) {
                        $temp = $photos[$i];
                        if (strcmp($photos[$i]->title, $photos[$j]->title) > 0) {
                            $photos[$i] = $photos[$j];
                            $photos[$j] = $temp;
                        }
                    }
                }

                break;
            case 'title_desc' :
                for ($i = 0; $i < count($photos) - 1; $i++)
                    for ($j = $i + 1; $j < count($photos); $j++) {
                        $temp = $photos[$i];
                        if (strcmp($photos[$i]->title, $photos[$j]->title) < 0) {
                            $photos[$i] = $photos[$j];
                            $photos[$j] = $temp;
                        }
                    }
                break;
            case 'random':
                shuffle($photos);
                break;
			case 'first-random':
				$tempArray = array_slice($photos,1);
                shuffle($tempArray);
				$photos = array_merge(array($photos[0]),$tempArray);
                break;
            default:
                break;
        }
        return $photos;
    }

    function fetchHead($params){
		$document	= &JFactory::getDocument();
		$header = $document->getHeadData();
		$loadJquery = $params->get('load_jquery', 1);
		$mainframe = JFactory::getApplication();
		$template = $mainframe->getTemplate();
		foreach($header['scripts'] as $scriptName => $scriptData)
		{
			  if(substr_count($scriptName,'/jquery'))
			  {
					$loadJquery = false;
			  }
		}
		//Add js
		if($loadJquery)
		{
			$document->addScript(JURI::root().'modules/mod_bt_backgroundslideshow/tmpl/js/jquery.min.js');
		}
		
		if(file_exists(JPATH_BASE.'/templates/'.$template.'/html/mod_bt_backgroundslideshow/css/style.css'))
		{
			$document->addStyleSheet(  JURI::root().'templates/'.$template.'/html/mod_bt_backgroundslideshow/css/style.css');
		}
		else{
			$document->addStyleSheet(JURI::root().'modules/mod_bt_backgroundslideshow/tmpl/css/style.css');
		}
		
        if(file_exists(JPATH_BASE.'/templates/'.$template.'/html/mod_bt_backgroundslideshow/js/default.css'))
		{
			$document->addScript(  JURI::root().'templates/'.$template.'/html/mod_bt_backgroundslideshow/js/default.css');
		}
		else{
			$document->addScript(JURI::root().'modules/mod_bt_backgroundslideshow/tmpl/js/default.js');
		}  
            
	}

    public static function getArticleInfo($id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = 'SELECT a.introtext as introtext, a.alias as article_alias, a.catid as catid, c.alias as cat_alias '
                .'FROM #__content as a LEFT JOIN #__categories as c ON a.catid = c.id WHERE a.id = '.$id;
        $db->setQuery($query);
        $article = $db->loadRow();
        return $article;
    }
    public static function checkK2Component(){
        $path = JPATH_ROOT . '/components/com_k2';
        if (is_dir($path)) {
            return true;
        } else {
            return false;
        }
    }
    public static function getK2ArticleInfo($id){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query = 'SELECT introtext, catid FROM #__k2_items WHERE id = '.$id;
        $db->setQuery($query);
        $article = $db->loadRow();
        return $article;
    }

    public function truncate_string($input, $number_character) {
        if (strlen($input) > $number_character) {
            for ($k = $number_character; $k > 0; $k--) {
                if (( $input[$k] == ' ' || $input[$k] == ',' || $input[$k] == '.'))
                    break;
            }
            $result = substr($input, 0, $k);
            $result.="... ";
        }
        else {
            $result = $input;
        }
        return $result;
    }

}

?>
