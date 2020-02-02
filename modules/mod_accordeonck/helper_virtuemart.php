<?php
/**
 * @copyright	Copyright (C) 2012 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Accordeonmenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

class modAccordeonckvirtuemartHelper {

    static $_activeitem;

    static function getVmCategories($rootcategory_id, $level, $vmcategorydepth) {
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $active_category_id = $app->input->get('virtuemart_category_id', '0');
                
        $query = "SELECT *, #__virtuemart_categories.virtuemart_category_id as id, #__virtuemart_category_categories.category_parent_id as parent, #__virtuemart_categories.ordering as ordering, ".$level." as level"
                ." FROM ((((#__virtuemart_categories"
                ." LEFT OUTER JOIN #__virtuemart_category_medias"
                ." ON #__virtuemart_categories.virtuemart_category_id = #__virtuemart_category_medias.virtuemart_category_id)"
                ." LEFT OUTER JOIN #__virtuemart_medias"
                ." ON #__virtuemart_category_medias.virtuemart_media_id = #__virtuemart_medias.virtuemart_media_id)"
				." INNER JOIN #__virtuemart_category_categories"
                ." ON #__virtuemart_categories.virtuemart_category_id = #__virtuemart_category_categories.category_child_id)"
				." INNER JOIN #__virtuemart_categories_".VMLANG
                ." ON #__virtuemart_categories.virtuemart_category_id = #__virtuemart_categories_".VMLANG.".virtuemart_category_id)"
				." WHERE #__virtuemart_categories.published = 1"
                ." AND #__virtuemart_category_categories.category_parent_id = " . $rootcategory_id
				." ORDER BY #__virtuemart_categories.ordering ASC";
                
        $db->setQuery($query);

        if ($db->query()) {
            $rows = $db->loadObjectList('id');
        } else {
            echo '<p style="color:red;font-weight:bold;">Error loading SQL data : loading the Virtuemart categories in Maximenu CK</p>';
            return false;
        }
        $items = array();
        foreach ($rows as $row) {
                if ($row->id == $active_category_id) self::$_activeitem = $row;
                $row->level = $level;
                $items[] = $row;
                if ($vmcategorydepth == 0 
                    || $level < ($vmcategorydepth)) $items = array_merge($items, self::getVmCategories($row->virtuemart_category_id, $level+1, $vmcategorydepth));
        }
        
        return $items;
    }
    
    
	/**
	 * Get a list of the menu items.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 */
    static function getItems(&$params, $rootcategory_id, $level) {

        $app = JFactory::getApplication();
        
        // load the virtuemart library
        if (!class_exists( 'VmConfig' )) require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
        $config= VmConfig::loadConfig();

        // initialize some variables
        $vmcategorydepth = $params->get('vmcategorydepth', '0');
        $active_category_id = $app->input->get('virtuemart_category_id', '0');
        $usevmsuffix = $params->get('usevmsuffix', '0');
		$vmimagesuffix = $params->get('vmimagesuffix', '_mini');
		$usevmimages = $params->get('usevmimages', '0');
		$vmcategoryroot = $params->get('vmcategoryroot', '0');
        
        // manage the module cache
        $user = JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();
		asort($levels);
		$key = 'accodeonmenuckvirtuemart_items'.$params.implode(',', $levels).'.'.$active_category_id;
		$cache = JFactory::getCache('mod_accordeonck', '');
        
		if (!($items = $cache->get($key)))
		{
            $items = self::getVmCategories($rootcategory_id, $level, $vmcategorydepth);    
            $cache->store($items, $key);
        }     

        // get the active path
        $activepath = self::getActivePath($items);
        
        foreach ($items as $i => &$item) {
            
            // variables definition
            $item->desc = '';
            $item->colwidth = '';
            $item->tagcoltitle = 'none';
			$item->tagclass = '';
            $item->leftmargin = '';
            $item->topmargin = '';
			$item->submenuwidth = '';
            $item->ftitle = stripslashes(htmlspecialchars($item->category_name));
            $item->content = '';
            $item->rel = '';
            $item->browserNav = '';
            $item->menu_image = '';
            $item->columnwidth = '';
            $item->anchor_css = '';
			$item->anchor_title = '';
			$item->type = '';
            $item->name = $item->ftitle;
            $item->params = new JRegistry();
            $item->flink = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $item->id);
            $item->deeper = false;
            $item->shallower = false;
            $item->level_diff = 0;
            $item->isactive = false;

            if (isset($items[$i-1])) {
                $items[$i-1]->deeper = ($item->level > $items[$i-1]->level);
                $items[$i-1]->shallower = ($item->level < $items[$i-1]->level);
                $items[$i-1]->level_diff = ($items[$i-1]->level - $item->level);
				//if ($items[$i-1]->deeper AND $params->get('layout', 'default') != '_:flatlist') $items[$i-1]->classe .= " parent";
            }            

            // test if it is the last item
			$item->is_end = !isset($items[$i + 1]);

            // manage item class
            $item->classe = 'item-'.$item->id;
            if (isset($active_category_id) && $active_category_id == $item->id) {
                $item->classe .= ' current';
            }
            if (in_array($item->id, $activepath)) {
                $item->classe .= ' active';  
                $item->isactive = true;
            }

            // search for parameters in the category description
            $patterns = "#{maximenu}(.*){/maximenu}#Uis";
            $result = preg_match($patterns, stripslashes($item->category_description), $results);
            if (isset($results[1])) {
                $vmparams = explode('|', $results[1]);
                for ($j = 0; $j < count($vmparams); $j++) {
                    $item->desc = stristr($vmparams[$j], "desc=") ? str_replace('desc=', '', $vmparams[$j]) : $item->desc;
                    // $item->colwidth = stristr($vmparams[$j], "col=") ? str_replace('col=', '', $vmparams[$j]) : $item->colwidth;
                    // $item->tagcoltitle = stristr($vmparams[$j], "taghtml=") ? str_replace('taghtml=', '', $vmparams[$j]) : $item->tagcoltitle;
					// $item->tagclass = stristr($vmparams[$j], "tagclass=") ? ' '.str_replace('tagclass=', '', $vmparams[$j]) : $item->tagclass;
                    // $item->leftmargin = stristr($vmparams[$j], "leftmargin=") ? str_replace('leftmargin=', '', $vmparams[$j]) : $item->leftmargin;
                    // $item->topmargin = stristr($vmparams[$j], "topmargin=") ? str_replace('topmargin=', '', $vmparams[$j]) : $item->topmargin;
					// $item->submenucontainerwidth = stristr($vmparams[$j], "submenuwidth=") ? str_replace('submenuwidth=', '', $vmparams[$j]) : $item->submenuwidth;
                }
            }
			
			// $item->classe .= ' ' . $item->tagclass;
			// manage tag encapsulation
			// $item->tagcoltitle = $item->params->set('maximenu_tagcoltitle', $item->taghtml);
			
            // manage images
            if (!$usevmsuffix) $vmimagesuffix = '';
            
            if ($usevmimages) {
				$imageurl = $item->file_url ? explode(".",$item->file_url): '';
				$imagelocation = isset($imageurl[0]) ? $imageurl[0] : '';
				$imageext = isset($imageurl[1]) ? $imageurl[1] : '';
                if (JFile::exists(JPATH_ROOT . DS. $imagelocation . $vmimagesuffix . '.' . $imageext)) {
					$item->menu_image = $imagelocation . $vmimagesuffix . '.' . $imageext;					
				}
            }
			
			// manage columns
            /*if ($item->colwidth) {
				$item->colonne = true;
                $parentItem = self::getParentItem($item->parent, $items);

                if (isset($parentItem->submenuswidth)) {
                    $parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($item->colwidth);
                } else {
                    $parentItem->submenuswidth = strval($item->colwidth);
                }
                if (isset($items[$i-1]) AND $items[$i-1]->deeper) {
					$items[$i-1]->columnwidth = $item->colwidth;
				} else {
					$item->columnwidth = $item->colwidth;
				}
            }
			if (isset($parentItem->submenucontainerwidth) AND $parentItem->submenucontainerwidth) $parentItem->submenuswidth = $parentItem->submenucontainerwidth;
			*/
			// $lastitem = $i;
        }
		
		// give the correct deep infos for the last item
		if (isset($items[$i])) {
			$items[$i]->level_diff	= ($items[$i]->level - 1);
		}

        return $items;
    }

    static function getParentItem($id, $items) {
        foreach ($items as $item) {
            if ($item->id == $id)
                return $item;
        }
    }
    
    static function getActivePath($items) {
        $rootlevel = $items[0]->level;
        $active = self::$_activeitem;
        if (!isset($active->id)) return array();
        $activepath = array();
        foreach ($items as $i => $item) {
            if ($item->id == $active->id) {
                $activepath[] = $item->id;
                while ($item->parent
                        && $item->level > $rootlevel) {
                    $i--;
                    if ($items[$i]->id == $item->parent) {
                        $item = $items[$i];
                        $activepath[] = $item->id;
                    }
                }
                break;
            }
        }
        
        return $activepath;
    }

}

?>