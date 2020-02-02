<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

require_once(JPATH_SITE . '/components/com_content/helpers/route.php');
nextendimport('nextend.accordionmenu.joomla.treebase');

class NextendTreeJoomlacontent extends NextendTreebaseJoomla {

    function NextendTreeJoomlacontent(&$menu, &$module, &$data) {
        parent::NextendTreebase($menu, $module, $data);
        $this->initConfig();
    }

    function initConfig() {

        parent::initConfig();

        $this->_config['root'] = explode('||', $this->_data->get('root', '1'));
        if (count($this->_config['root']) == 0) {
            $this->_config['root'] = array(0);
        }

        $this->_config['categorylayout'] = $this->_data->get('categorylayout', '');

        $this->_config['showproducts'] = intval($this->_data->get('showproducts', 0));

        $this->_config['emptycategory'] = intval($this->_data->get('emptycategory', '1'));

        $this->_config['order'] = $this->_data->get('order', '0');

        $this->_config['maxitemsincat'] = intval($this->_data->get('maxitemsincat', '20'));

        $this->initMenuicon();
    }

    function getAllItems() {
        $db = JFactory::getDBO();

        $query = "SELECT DISTINCT
a.id AS id,
a.title AS name,
a.alias,
params, ";
        if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
            $query .= "IF(a.parent_id = " . $this->_config['root'][0] . ", 0 , IF(a.parent_id = 1, -1, a.parent_id)) AS parent, ";
        } else if (!in_array('1', $this->_config['root'])) {
            $query .= "IF(a.id in (" . implode(',', $this->_config['root']) . "), 0 , IF(a.parent_id = 1, -1, a.parent_id)) AS parent, ";
        } else {
            $query .= " IF(a.parent_id = 1, 0, a.parent_id) AS parent, ";
        }
        $query .= "'cat' AS typ, ";
        if ($this->_config['displaynum'] || !$this->_config['emptycategory']) {
            $query .= "(SELECT COUNT(*) FROM #__content AS ax WHERE ax.catid=a.id AND ax.state = 1 AND (ax.publish_up < NOW() OR ax.publish_down IS NULL) AND (ax.publish_down > NOW() OR ax.publish_down IS NULL) ";
            $query .= ") AS productnum";
        } else {
            $query .= "0 AS productnum";
        }

        $query .= " FROM #__categories AS a
WHERE a.published = 1 AND a.extension = 'com_content' ";


        if ($this->_config['order'] == "asc") {
            $query .= "ORDER BY name ASC";
        } else if ($this->_config['order'] == "desc") {
            $query .= "ORDER BY name DESC";
        } else {
            $query .= "ORDER BY a.lft ASC, name DESC";
        }

        $db->setQuery($query);
        $allItems = $db->loadObjectList('id');
        if ($this->_config['showproducts']) {

            $query = "
SELECT
concat(a.catid,'-',a.id) AS id,
a.id AS id2,
a.title AS name,
a.introtext AS description,
a.catid AS parent,
a.alias,
a.access,
'prod' AS typ,
0 AS productnum,
'' AS image
FROM #__content AS a
WHERE a.state = 1 ";

            if ($this->_config['order'] == "asc") {
                $query .= "ORDER BY a.title ASC";
            } else if ($this->_config['order'] == "desc") {
                $query .= "ORDER BY a.title DESC";
            } else {
                $query .= "ORDER BY a.ordering ASC, a.title DESC";
            }

            $db->setQuery($query);
            $rows = $db->loadObjectList('id');
            if ($this->_config['maxitemsincat'] > 0) {
                $cats = array();
                $keys = array_keys($rows);
                for ($x = 0; $x < count($keys); ++$x) {
                    $value = $rows[$keys[$x]];
                    if (!isset($cats[$value->parent])) {
                        $cats[$value->parent] = 0;
                    }
                    $cats[$value->parent]++;
                    if ($cats[$value->parent] > $this->_config['maxitemsincat']) {
                        unset($rows[$keys[$x]]);
                    }
                }
            }
            $allItems += $rows;
        }
        return $allItems;
    }

    function getActiveItem() {
        $db = JFactory::getDBO();
        $active = null;
        if (JRequest::getVar('option') == 'com_content') {
            $content_id = 0;
            $category_id = 0;
            if (JRequest::getVar('view') == "category") {
                $category_id = JRequest::getInt('id');
            } elseif (JRequest::getVar('view') == "article") {
                $content_id = JRequest::getInt('id');
                $query = "SELECT catid FROM #__content WHERE id=" . $content_id;
                $db->setQuery($query);
                $category_id = $db->loadResult();
            }
            if ($content_id > 0 && $this->_config['showproducts']) {
                $active = new StdClass();
                $active->id = $category_id . "-" . $content_id;
            } elseif ($category_id > 0) {
                $active = new StdClass();
                $active->id = $category_id;
            }
        }
        return $active;
    }

    function getItemsTree() {
        $items = $this->getItems();
        if ($this->_config['displaynum'] == 2 || !$this->_config['emptycategory']) {
            for ($i = count($items) - 1; $i >= 0; $i--) {
                $items[$i]->parent->productnum += $items[$i]->productnum;
            }
        }
        if (!$this->_config['emptycategory']) {
            for ($i = count($items) - 1; $i >= 0; $i--) {
                if ($items[$i]->productnum == 0 && $items[$i]->typ == 'cat') {
                    $parent = & $this->helper[$items[$i]->parent->id];
                    if ($items[$i]->lib) {
                        array_splice($parent, count($parent) - 1, 1);
                        if (count($parent) != 0) {
                            $parent[count($parent) - 1]->lib = true;
                        }
                    } else if ($items[$i]->fib) {
                        array_splice($parent, 0, 1);
                        if (count($parent) != 0) {
                            $parent[0]->fib = true;
                        }
                    } else {
                        $key = array_search($items[$i], $parent);
                        if ($key !== false) {
                            array_splice($parent, $key, 1);
                        }
                    }
                    if(count($this->helper[$items[$i]->parent->id]) == 0) $items[$i]->parent->p = false;
                    array_splice($items, $i, 1);
                }
            }
        }
        return $items;
    }

    function filterItem($item) {
        $item->nname = stripslashes($item->name);
        $item->nname = '<span>' . $item->nname . '</span>';
        if ($this->_config['displaynum'] && $item->productnum != 0) {
            $item->nname = $this->renderProductnum($item->productnum) . $item->nname;
        }
        if ($item->typ == 'cat') {
            if ($this->_config['menuiconshow']) {
                $params = new JRegistry($item->params);
                if (($image = $params->get('image', '')) != '') {
                    $this->parseIcon($item, JUri::Root(true) . '/' . $image, '');
                }
            }
            if (!$this->_config['parentlink'] && $item->p) {
                $item->nname = '<a>' . $item->nname . '</a>';
            } else {
                $item->nname = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($item->id)) . '">' . $item->nname . '</a>';
            }
        } else if ($item->typ == 'prod') {
            $id = explode("-", $item->id);
            $item->nname = '<a href="' . JRoute::_(ContentHelperRoute::getArticleRoute($id[1].':'.$item->alias, $item->parent->id.':'.$item->parent->alias)) . '">' . $item->nname . '</a>';
        }
    }
}