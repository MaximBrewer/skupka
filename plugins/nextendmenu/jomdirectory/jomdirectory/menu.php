<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.accordionmenu.joomla.treebase');

class NextendTreeJomDirectory extends NextendTreebaseJoomla {

    function NextendTreeJomDirectory(&$menu, &$module, &$data) {
        parent::NextendTreebase($menu, $module, $data);
        $this->initConfig();
    }

    function initConfig() {

        parent::initConfig();

        $this->_config['root'] = explode('||', $this->_data->get('root', '0'));
        if (count($this->_config['root']) == 0) {
            $this->_config['root'] = array(0);
        }

        $this->_config['showproducts'] = intval($this->_data->get('showproducts', 0));

        $this->_config['emptycategory'] = intval($this->_data->get('emptycategory', 1));

        $this->_config['order'] = $this->_data->get('order', '0');
    }

    function getAllItems() {
        $db = JFactory::getDBO();
        $query = "SELECT DISTINCT 
            a.id AS id, 
            a.name AS name,
            'category' AS typ,
            ";

        if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
            $query.="IF(a.parent_id = " . $this->_config['root'][0] . ", 0 , IF(a.parent_id = 0, -1, a.parent_id)) AS parent, ";
        } else if (!in_array('0', $this->_config['root'])) {
            $query.="IF(a.id in (" . implode(',', $this->_config['root']) . "), 0 , IF(a.parent_id = 0, -1, a.parent_id)) AS parent, ";
        } else {
            $query.="a.parent_id AS parent, ";
        }

        if ($this->_config['displaynum'] || !$this->_config['emptycategory']) {
            $query.= "(SELECT COUNT(*) FROM #__cddir_ads AS ax WHERE ax.type = a.id AND ax.published = 1 ";
            $query.= ") AS productnum";
        } else {
            $query.= "0 AS productnum";
        }

        $query.= " FROM #__cddir_categories AS a
                WHERE a.published = 1 ";
                
        if ($this->_config['order'] == "asc") {
            $query.="ORDER BY a.name ASC";
        } else if ($this->_config['order'] == "desc") {
            $query.="ORDER BY a.name DESC";
        } else {
            $query.="ORDER BY a.name ASC";
        }

        $db->setQuery($query);
        $allItems = $db->loadObjectList('id');
        if ($this->_config['showproducts']) {

            $query = "SELECT concat(a.type,'-',a.id) AS id, 
                    a.id AS id2,
                    a.ad_headline AS name,
                    a.type AS parent, 
                    'entry' AS typ, 
                    0 AS productnum
                FROM #__cddir_ads AS a
                WHERE a.published = 1 ";

            if ($this->_config['order'] == "asc") {
                $query.="ORDER BY a.ad_headline ASC";
            } else if ($this->_config['order'] == "desc") {
                $query.="ORDER BY a.ad_headline DESC";
            } else {
                $query.="ORDER BY a.ad_headline ASC";
            }

            $db->setQuery($query);
            $rows = $db->loadObjectList('id');
            $allItems += $rows;
        }
        return $allItems;
    }

    function getActiveItem() {
        $db = JFactory::getDBO();
        $active = null;
        if (JRequest::getVar('option') == 'com_jomdirectory') {
            $category_id = JRequest::getInt('type');
            $content_id = JRequest::getInt('id', -1);
            
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
                $items[$i]->parent->productnum+= $items[$i]->productnum;
            }
        }
        if (!$this->_config['emptycategory']) {
            for ($i = count($items) - 1; $i >= 0; $i--) {
                if ($items[$i]->productnum == 0 && $items[$i]->typ == 'category') {

                    $parent = &$this->helper[$items[$i]->parent->id];

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
            $item->nname = $this->renderProductnum($item->productnum).$item->nname;
        }


        if ($item->typ == 'category') {
            if (!$this->_config['parentlink'] && $item->p) {
                $item->nname = '<a>' . $item->nname . '</a>';
            } else {
                $item->nname = '<a href="' .
                        jRoute::_('index.php?option=com_jomdirectory&task=search&type='.$item->id.'&type_text='.$item->name) . '">' .
                        $item->nname .
                        '</a>';
            }
        } else if ($item->typ == 'entry') {
            $id = explode("-", $item->id);
            $item->nname = '<a href="' .
                    JRoute::_('index.php?option=com_jomdirectory&task=detailed&id='.$item->id2.'&limitstart=0&ad_headline='.$item->name.'&category='.$item->parent->name) . '">' .
                    $item->nname .
                    '</a>';
        }
    }

}

