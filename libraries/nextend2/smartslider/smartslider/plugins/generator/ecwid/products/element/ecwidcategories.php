<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEcwidcategories extends N2ElementList
{

    function fetchElement() {
        $configuration = $this->_form->get('info')->getConfiguration();

        $isConfigured = $configuration->checkStoreID();

        if ($isConfigured) {

            $storeID = $configuration->getStoreID();

            $categoriesJSON = @file_get_contents("http://app.ecwid.com/api/v1/" . $storeID . "/categories");
            $categoryList   = json_decode($categoriesJSON);

            for ($i = 0; $i < count($categoryList); $i++) {
                if (property_exists($categoryList[$i], "parentId")) {
                    $menuItems[$i]            = new StdClass;
                    $menuItems[$i]->parent_id = $categoryList[$i]->parentId;
                } else {
                    $menuItems[$i]            = new StdClass;
                    $menuItems[$i]->parent_id = 0;
                }
                $menuItems[$i]->id    = $categoryList[$i]->id;
                $menuItems[$i]->title = $categoryList[$i]->name;
            }

            $children = array();
            if (isset($menuItems) && $menuItems) {
                foreach ($menuItems as $v) {
                    $pt   = $v->parent_id;
                    $list = isset($children[$pt]) ? $children[$pt] : array();
                    array_push($list, $v);
                    $children[$pt] = $list;
                }
            } else {
                $html = "There are no categories for this store, please check the ID: " . $storeID;
                return $html;
            }

            $sorted_array = $this->makeMenu($menuItems);

            $this->_xml->addChild('option', 'All')->addAttribute('value', 0);

            if (is_array($sorted_array) && count($sorted_array)) {
                foreach ($sorted_array AS $item) {
                    $this->_xml->addChild('option', $item->title)->addAttribute('value', $item->id);
                }
            }
            $this->_value = $this->_form->get($this->_name, $this->_default);
            $html         = parent::fetchElement();
        }
        return $html;
    }

    // reordering array with makeMenu function, then build a tree; source needs array with these objects in the values: parent_id, id, title
    private function buildTree($elements, $returned = null, $parentId = 0) {
        if ($returned != null) $branch[] = $returned;
        for ($i = 0; $i < count($elements); $i++) {
            if ($elements[$i]->parent_id == $parentId) {
                $branch[] = $this->buildTree($elements, $elements[$i], $elements[$i]->id);
            }
        }
        return $branch;
    }

    private function makeMenu($menuItems) {
        $sort         = $this->buildTree($menuItems);
        $sorted_array = array();
        $array_obj    = new RecursiveIteratorIterator(new RecursiveArrayIterator($sort));
        foreach ($array_obj as $key => $value) {
            if ($key == 'id') {
                for ($i = 0; $i < count($menuItems); $i++) {
                    if ($menuItems[$i]->id == $value) {
                        array_push($sorted_array, $menuItems[$i]);
                    }
                }
            }
        }

        if (is_array($sorted_array) && count($sorted_array)) {
            foreach ($sorted_array AS $item) {
                if (!isset($pre[$item->id])) {
                    if (isset($pre[$item->parent_id])) {
                        $pre[$item->id] = $pre[$item->parent_id] . '- ';
                    } else {
                        $pre[$item->id] = '- ';
                    }
                }
                $item->title = $pre[$item->id] . htmlspecialchars($item->title);
            }
        }
        return $sorted_array;
    }

}
