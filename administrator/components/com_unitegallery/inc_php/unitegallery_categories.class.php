<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


class UniteGalleryCategories extends UniteElementsBaseUG{
	
	
	public function __construct(){
		parent::__construct();
		
	}
	
	
	/**
	 * 
	 * validate that category exists
	 */
	public function validateCatExist($catID){
		$this->getCat($catID);
	}
	
	
	
	/**
	 * 
	 * get categories list
	 */
	public function getList(){
		$tableCats = GlobalsUG::$table_categories;
		$tableItems = GlobalsUG::$table_items;
		
		$query = "select cats.*, count(items.id) as num_items from {$tableCats} as cats";
		$query .= " left join $tableItems as items on items.catid=cats.id GROUP BY cats.id order by cats.ordering";
		
		$arrCats = $this->db->fetchSql($query);
		
		return($arrCats);
	}
	
	
	/**
	 * get category list by id's string
	 */
	public function getListByIds( $ids ) {
		
		$ids = $this->db->escape($ids);
		
		$tableCats = GlobalsUG::$table_categories;
		$query = "select cats.* from {$tableCats} as cats WHERE cats.id IN(" . $ids . ")";
		$arrCats = $this->db->fetchSql($query);
		
		$arrCats = UniteFunctionsUG::arrayToAssoc($arrCats, "id");
		
		//order by IDs
		$arrIDs = explode(",", $ids);
		$arrCatsFinal = array();
		foreach($arrIDs as $id){
			if(array_key_exists($id, $arrCats))
				$arrCatsFinal[] = $arrCats[$id];
		}
		
		
		return($arrCatsFinal);
	}
	
	
	/**
	 * 
	 * get category records simple without num items
	 */
	public function getCatRecords(){
		$arrCats = $this->db->fetch(GlobalsUG::$table_categories,"","ordering");
		return($arrCats);
	}
	
	
	/**
	 * 
	 * get categories list short
	 * addtype: empty (empty category), new (craete new category)
	 */
	public function getCatsShort($addType = ""){
		
		$arrCats = $this->getCatRecords();
		$arrCatsOutput = array();
		
		switch($addType){
			case "empty":
				$arrCatsOutput[""] = __("[Not Selected]", UNITEGALLERY_TEXTDOMAIN);
			break;
			case "new":
				$arrCatsOutput["new"] = __("[Add New Category]", UNITEGALLERY_TEXTDOMAIN);
			break;
			case "component":
				$arrCatsOutput[""] = __("[From Gallery Settings]", UNITEGALLERY_TEXTDOMAIN);
			break;
		}
		
		foreach($arrCats as $cat){
			$catID = UniteFunctionsUG::getVal($cat, "id");
			$title = UniteFunctionsUG::getVal($cat, "title");
			$arrCatsOutput[$catID] = $title;
		}
		
		return($arrCatsOutput);
	}
	
	
	/**
	 * 
	 * get assoc value of category name
	 */
	public function getArrCatTitlesAssoc(){
		$arrCats = $this->getList();
		$arrAssoc = array();
		foreach($arrCats as $cat){
			$arrAssoc[$cat["title"]] = true;
		}
		return($arrAssoc);
	}
	
	
	/**
	 * 
	 * get max order from categories list
	 */
	public function getMaxOrder(){
		
		$query = "select MAX(ordering) as maxorder from ".GlobalsUG::$table_categories;
		
		///$query = "select * from ".self::TABLE_CATEGORIES;
		$rows = $this->db->fetchSql($query);
				
		$maxOrder = 0;
		if(count($rows)>0) $maxOrder = $rows[0]["maxorder"];
		
		if(!is_numeric($maxOrder))
			$maxOrder = 0;
		
		return($maxOrder);
	}
	
	
	/**
	 *
	 * get category
	 */
	public function getCat($catID){
		
		$objCat = new UniteGalleryCategory();
		$objCat->initByID($catID);
		$record = $objCat->getRecord();
		
		return($record);
	}
	
	
	/**
	 * get true/false if some category exists
	 */
	public function isCatExists($catID){
		
		UniteFunctionsUG::validateNumeric($catID, "category id");
		
		$arrCat = $this->db->fetchSingle(GlobalsUG::$table_categories,"id=$catID");
		return !empty($arrCat);		
	}
	
	
	
	
	/**
	 * 
	 * update categories order
	 */
	private function updateOrder($arrCatIDs){
		
		foreach($arrCatIDs as $index=>$catID){
			$order = $index+1;
			$arrUpdate = array("ordering"=>$order);
			$where = array("id"=>$catID);
			$this->db->update(GlobalsUG::$table_categories,$arrUpdate,$where);
		}
	}
	
	
	/**
	 * 
	 * remove category from data
	 */
	public function removeFromData($data){
		
		$objCat = $this->getCatFromData($data);
		$objCat->remove();
		
		$response = array();
		$response["htmlSelectCats"] = $this->getHtmlSelectCats();
		
		return($response);
	}
	
	/**
	 * get category object from data
	 */
	private function getCatFromData($data){
		
		$catID = UniteFunctionsUG::getVal($data, "catID");
		if(empty($catID))
			$catID = UniteFunctionsUG::getVal($data, "catid");
		
		UniteFunctionsUG::validateNotEmpty($catID,"category id");
		
		$objCat = new UniteGalleryCategory();
		$objCat->initByID($catID);
		
		return($objCat);
	}
	
	
	/**
	 * 
	 * update category from data
	 */
	public function updateFromData($data){
		
		$objCat = $this->getCatFromData($data);
		
		$title = UniteFunctionsUG::getVal($data, "title");
		
		$objCat->updateTitle($title);
		
	}
	
	
	
	/**
	 * 
	 * update order from data
	 */
	public function updateOrderFromData($data){
		$arrCatIDs = UniteFunctionsUG::getVal($data, "cat_order");
		if(is_array($arrCatIDs) == false)
			UniteFunctionsUG::throwError("Wrong categories array");
			
		$this->updateOrder($arrCatIDs);
	}
	
	
	/**
	 * 
	 * add catgory from data, return cat select html list
	 */
	public function addFromData(){
		
		$objCat = new UniteGalleryCategory();
		
		$response = $objCat->add();
				
		$html = $objCat->getHTMLAdmin();
		
		$response["htmlSelectCats"] = $this->getHtmlSelectCats();
		$response["htmlCat"] = $html;
		
		return($response);
		
	}
	
	
	/**
	 * change category sortby from data
	 */
	public function changeCatSortbyFromData($data){
		
		$objCat = $this->getCatFromData($data);
		
		$sortBy = UniteFunctionsUG::getVal($data, "sortby");
		$objCat->updateSortby($sortBy);
		
		$catID = $objCat->getID();
		$items = new UniteGalleryItems();
		$itemsHtml = $items->getCatItemsHtml($catID);
		
		//get items list
		$response = array("itemsHtml"=>$itemsHtml);
		
		return($response);
	}
	
	
	/**
	 * get list of categories 
	 */
	public function getHtmlCatList($selecteCatID = false){
		
		$arrCats = $this->getList();
		
		$html = "";
			
		foreach($arrCats as $index => $cat):
			$id = $cat["id"];
			
			$class = "";
			if($index == 0)			
				$class = "first-item";
			
			if(!empty($selecteCatID) && $id == $selecteCatID){
				if(!empty($class))
				$class .= " ";
				$class .= "selected-item";
			}
			
			if(!empty($class))
				$class = "class=\"{$class}\"";
			
			$objCat = new UniteGalleryCategory();
			$objCat->initByRecord($cat);
			
			$html .= $objCat->getHTMLAdmin($class);
			
		endforeach;
						
		return($html);
	}
	
	
	/**
	 * 
	 * get items for select categories
	 */
	public function getHtmlSelectCats(){
		
		$arrCats = $this->getList();
		
		$html = "";
		foreach($arrCats as $cat):
			$catID = $cat["id"];
			$title = $cat["title"];
			$html .= "<option value=\"{$catID}\">{$title}</option>";
		endforeach;
		
		return($html);
	}
	
	
}

?>