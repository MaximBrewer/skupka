<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimportaccordionmenu('nextend.accordionmenu.treebase');

class NextendTreeMagento extends NextendTreebase {

    function NextendTreeMagento(&$menu, &$module, &$data) {
        parent::NextendTreebase($menu, $module, $data);
        $this->initConfig();
        $this->_usecache = Mage::app()->useCache('collections');
    }

    function initConfig() {
        parent::initConfig();

        $displaynum = explode('|*|', $this->_data->get('displaynum', '0|*|0|*|0'));
        $this->_config['displaynum'] = intval($displaynum[0]);
        $this->_config['displaynumzero'] = intval($displaynum[1]);
        $this->_config['displaynumchilds'] = intval($displaynum[2]);
        
        $root = intval($this->_data->get('rootcategory',0));
        
        if($root == 0){
            $this->_config['rootid'] = Mage::app()->getStore()->getRootCategoryId();
        }else{
            $this->_config['rootid'] = $root;
        }
        
        $this->_config['order'] = $this->_data->get('order', 0);
    }
    
    function getAllItems() {
        $this->tmpAllItems = false;
        $this->tmpAllItems = array();
        $model = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId());
        $categories = $model->getCategories($this->_config['rootid'], 0, true, false, true);
        if (Mage::helper('catalog/category_flat')->isEnabled()) {

            $this->flatToFlat($categories, $this->_config['rootid']);
            if($this->_config['order'] == 'name'){
                uasort ( $this->tmpAllItems , array($this, 'order') );
            }
        }else{
            $k = array_keys($categories->getNodes());
            $parent = $categories[$k[0]]->getParent();
            $this->treeToFlat($parent);
        }
        return $this->tmpAllItems;
    }
    
    function flatToFlat($categories, $parentid){
        foreach($categories AS $category){
            $item = new stdClass();
            $item->id = $category->getId();
            $item->name = $category->getName();
            if($parentid == $this->_config['rootid']){
               $item->parent = 0;
            }else{
                $item->parent = $parentid;
            }
            $cat = Mage::getModel('catalog/category')->load($item->id);
            $item->url = $cat->getUrl();
            if($this->_config['displaynum']){
                $item->productnum = $this->getProductCount($cat);
            }else{
                $item->productnum = 0;
            }
            $this->tmpAllItems[$item->id] = $item;
            
            $this->flatToFlat($category->getChildrenCategories(), $item->id);
        }
    }
    
    function treeToFlat($parent){
        foreach($parent->getAllChildNodes() AS $category){
            $item = new stdClass();
            $item->id = $category->getId();
            $item->name = $category->getName();
            if($category->getParent()->getId() == $this->_config['rootid']){
               $item->parent = 0;
            }else{
                $item->parent = $category->getParent()->getId();
            }
            $cat = Mage::getModel('catalog/category')->load($item->id);
            $item->url = $cat->getUrl();
            if($this->_config['displaynum']){
                $item->productnum = $this->getProductCount($cat);
            }else{
                $item->productnum = 0;
            }
            $this->tmpAllItems[$item->id] = $item;
        }
        if($this->_config['order'] == 'name'){
            uasort ( $this->tmpAllItems , array($this, 'order') );
        }
    }
    
    function getProductCount($category){
        $prodCollection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($category);
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($prodCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($prodCollection);
        return $prodCollection->count();
    }
    
    function order($a, $b){
        return strcmp($a->name, $b->name);
    }
    
    function getActiveItem(){
        $currentCategory = Mage::registry('current_category');
        if(is_object($currentCategory)){
            $id = $currentCategory->getId();
            if($id > 0){
                $active = new stdClass();
                $active->id = (int)$id;
                return $active;
            }
        }
        return null;
    }
    
    function getItemsTree(){
        return $this->getItems();
    }
    
    function filterItem($item) {
        $item->nname = stripslashes($item->name);
        $item->nname = '<span>' . $item->nname . '</span>';
        
        if ($this->_config['displaynum'] && ($item->productnum != 0 || $this->_config['displaynumzero']) && (!$this->_config['displaynumchilds'] || ($this->_config['displaynumchilds'] && !$item->p))){
          $item->nname.= '<span class="nextend-productnum">'.$item->productnum.'</span>'; 
        }
        
        if (!$this->_config['parentlink'] && $item->p) {
            $item->nname = '<a href="#" onclick="return false;">'.$item->nname.'</a>';
        }else{
            $item->nname = '<a href="'.$item->url.'">'.$item->nname.'</a>';
        }
    }

}