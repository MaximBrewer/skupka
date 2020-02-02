<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


class UniteGalleryItems extends UniteElementsBaseUG{
		
	private $operations;
	private static $arrTotalItems = array();	//total items cache
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		parent::__construct();
		$this->operations = new UGOperations();
	}
	
	
	/**
	 * 
	 * update item 
	 */
	private function update($itemID,$arrUpdate){
		
		$this->db->update(GlobalsUG::$table_items,$arrUpdate,array("id"=>$itemID));
	}
	
	
	private function a____________GETTERS_____________(){}
	
	
	/**
	 * 
	 * get items by id's
	 */
	private function getItemsByIDs($itemIDs){
		$strItems = implode(",", $itemIDs);
		$tableItems = GlobalsUG::$table_items;
		$sql = "select * from {$tableItems} where id in({$strItems})";
		$arrItems = $this->db->fetchSql($sql);
		
		return($arrItems);
	}
	
	
	
	
	/**
	 * 
	 * get html of cate items
	 */
	public function getCatItemsHtml($catID){
		
		$items = $this->getCatItems($catID);
		
		$htmlItems = "";
		
		foreach($items as $item){
			$html = $item->getHtmlForAdmin();
			$htmlItems .= $html;
		}
		
		return($htmlItems);
	}
	
	
	
	
	/**
	 * get category orderby
	 */
	private function getCatOrderby($catID){
		$catID = (int)$catID;
	
		//get ordering
		$objCat = new UniteGalleryCategory();
		$objCat->initByID($catID);
	
		$sortby = $objCat->getSortby();
	
		$orderBy = "ordering";
		switch($sortby){
			case "title_asc":
				$orderBy = "title asc";
				break;
			case "title_desc":
				$orderBy = "title desc";
				break;
		}
	
		return($orderBy);
	}

	
	
	/**
	 *
	 * get max order from categories list
	 */
	public function getMaxOrder($catID){
	
		UniteFunctionsUG::validateNotEmpty($catID,"category id");
	
		$tableItems = GlobalsUG::$table_items;
		$query = "select MAX(ordering) as maxorder from {$tableItems} where catid={$catID}";
	
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
	 * get item data html for edit item
	 */
	private function getItemSettingsHtml($objItem){
	
		$settingsItem = $objItem->getObjSettings();
	
		$output = new UniteSettingsProductUG();
		$output->init($settingsItem);
		$output->setShowDescAsTips(true);
		$output->setShowSaps(false);
	
		ob_start();
		$output->draw("form_item_settings", true);
		$html = ob_get_contents();
		ob_clean();
	
		$response = array();
		$response["htmlSettings"] = $html;
	
		return($response);
	}
	
	
	/**
	 * for image, get settings html
	 * for media get data object
	 */
	public function getItemData($data){
	
		$itemID = UniteFunctionsUG::getVal($data, "itemid");
		$objItem = new UniteGalleryItem();
		$objItem->initByID($itemID);
		$itemType = $objItem->getType();
	
		switch($itemType){
			case UniteGalleryItem::TYPE_IMAGE:
				$response = $this->getItemSettingsHtml($objItem);
				break;
			default:
				$response = $objItem->getData();
			break;
		}
	
		return($response);
	}
	
	
	/**
	 *
	 * get html of categories and items.
	 */
	private function getCatsAndItemsHtml($catID){
	
		$htmlItems = $this->getCatItemsHtml($catID);
		$objCats = new UniteGalleryCategories();
		$htmlCatList = $objCats->getHtmlCatList($catID);
	
		$response = array();
		$response["htmlItems"] = $htmlItems;
		$response["htmlCats"] = $htmlCatList;
	
		return($response);
	}
	
	
	/**
	 *
	 * get category items html
	 */
	public function getCatItemsHtmlFromData($data){
	
		$catID = UniteFunctionsUG::getVal($data, "catID");
		UniteFunctionsUG::validateNumeric($catID,"category id");
		$itemsHtml = $this->getCatItemsHtml($catID);
	
		$response = array("itemsHtml"=>$itemsHtml);
	
		return($response);
	}
	
	private function a____________GET_ITEMS_____________(){}
	
	
	/**
	 * get items from array of items
	 */
	public function getItemsFromArray($arrData){
	
		$arrItems = array();
		foreach($arrData as $data){
	
			$item = new UniteGalleryItem();
			$item->initByData($data);
	
			$arrItems[] = $item;
		}
	
		return($arrItems);
	}
	
	
	/**
	 * get items array from records array
	 */
	private function getItemsFromRecords($records){
	
		$arrItems = array();
		foreach($records as $record){
			$objItem = new UniteGalleryItem();
			$objItem->initByDBRecord($record);
			$arrItems[] = $objItem;
		}
	
		return($arrItems);
	}
	
	
	/**
	 * get records of some category
	 */
	private function getCatItemsRecords($catID){
		$catID = (int)$catID;
	
		$cat = new UniteGalleryCategories();
		$cat->validateCatExist($catID);
	
		$orderBy = $this->getCatOrderby($catID);
	
		$records = $this->db->fetch(GlobalsUG::$table_items, "catid=$catID", $orderBy);
	
		return($records);
	}
	
	
	/**
	 * get paging limit
	 */
	private function getPagingLimit($totalItems, $maxItems, $minItems){
	
		$maxItems = (int)$maxItems;
		$minItems = (int)$minItems;
	
		//validate
		if($maxItems < $minItems)
			UniteFunctionsUG::throwError("Max items $minItems should be bigger then min items $minItems");
	
		if($totalItems < $maxItems)
			return($totalItems);
	
		$remain = $totalItems-$maxItems;
		if($remain > $minItems)
			return($maxItems);
	
		//take ultimate number of items
		$limit = $totalItems - $minItems;
		if($limit < $minItems)
			$limit = $minItems;
	
		return($limit);
	}
	
	
	/**
	 * get records of some category
	 */
	private function getCatItemsRecordsLimit($catID, $maxItems, $minItems){
		$catID = (int)$catID;
	
		$cat = new UniteGalleryCategories();
		$cat->validateCatExist($catID);
	
		$orderBy = $this->getCatOrderby($catID);
	
		$where = "catid=$catID";
	
		$totalItems = $this->getTotalCatItems($catID);
	
		$limit = $this->getPagingLimit($totalItems, $maxItems, $minItems);
		
		if($limit >= $totalItems)
			$records = $this->db->fetch(GlobalsUG::$table_items, $where, $orderBy);
		else
			$records = $this->db->fetchOffset(GlobalsUG::$table_items, 0, $limit, $where, $orderBy);
	
		return($records);
	}
	
	
	/**
	 * get category items with some offset and limit
	 */
	private function getCatItemsOffset($catID, $offset, $limit){
		
		$catID = (int)$catID;
		
		$cat = new UniteGalleryCategories();
		$cat->validateCatExist($catID);
		
		$orderBy = $this->getCatOrderby($catID);
		
		$where = "catid=$catID";
		
		$records = $this->db->fetchOffset(GlobalsUG::$table_items, $offset, $limit, $where, $orderBy);
		
		$arrItems = $this->getItemsFromRecords($records);
		
		return($arrItems);
	}
	
	
	/**
	 * get category items
	 */
	public function getCatItemsLimit($catID, $maxItems, $minItems){
		$catID = (int)$catID;
	
		$records = $this->getCatItemsRecordsLimit($catID, $maxItems, $minItems);
	
		$arrItems = $this->getItemsFromRecords($records);
	
		return($arrItems);
	}
	
	
	/**
	 * get total items by category
	 */
	public function getTotalCatItems($catID){
		$catID = (int)$catID;
		$key = "cat_".$catID;
		if(isset(self::$arrTotalItems[$key]))
			return(self::$arrTotalItems[$key]);
	
		$where = "catid=$catID";
	
		$totalItems = $this->db->getTotalRows(GlobalsUG::$table_items, $where);
	
		self::$arrTotalItems[$key] = $totalItems;	//cache
	
		return($totalItems);
	}
	
	
	/**
	 * get category items
	 */
	public function getCatItems($catID){
		$catID = (int)$catID;
	
		$records = $this->getCatItemsRecords($catID);
	
		$arrItems = $this->getItemsFromRecords($records);
	
		return($arrItems);
	}
	
	
	private function a____________HTML_FRONT_____________(){}
	
	/**
	 * get video add html
	 */
	public function getVideoAddHtml($type, $objItem){
	
		$addHtml = "";
		switch($type){
			case UniteGalleryItem::TYPE_YOUTUBE:
			case UniteGalleryItem::TYPE_VIMEO:
			case UniteGalleryItem::TYPE_WISTIA:
				$videoID = $objItem->getParam("videoid");
				$addHtml .= "data-videoid=\"{$videoID}\" ";
				break;
			case UniteGalleryItem::TYPE_HTML5VIDEO:
				$urlMp4 = $objItem->getParam("video_mp4");
				$urlWebm = $objItem->getParam("video_webm");
				$urlOgv = $objItem->getParam("video_ogv");
	
				$addHtml .= "data-videomp4=\"{$urlMp4}\" ";
				$addHtml .= "data-videowebm=\"{$urlWebm}\" ";
				$addHtml .= "data-videoogv=\"{$urlOgv}\" ";
	
				break;
		}
	
		return($addHtml);
	}
	
	
	/**
	 * get front html of items array
	 */
	public function getItemsHtmlFront($arrItems, $thumbSize = "", $bigImageSize="", $isTilesType = false, $thumbSizeMobile = "", $bigImageSizeMobile = ""){
	
		$tab = "						";
		$nl = "\n".$tab;
	
		$totalHTML = "";
	
		foreach($arrItems as $objItem):
	
		$urlImage = $objItem->getUrlImage($bigImageSize);
		$urlThumb = $objItem->getUrlThumb($thumbSize);
	
		//set mobile thumb and image settings
		$urlImageMobile = "";
		if(!empty($bigImageSizeMobile) && $bigImageSizeMobile != $bigImageSize){
	
			$urlImageMobile = $objItem->getUrlImage($bigImageSizeMobile);
			if($urlImageMobile === $urlImage)
				$urlImageMobile = "";
		}
	
		$urlThumbMobile = "";
		if(!empty($thumbSizeMobile) && $thumbSizeMobile != $thumbSize){
	
			$urlThumbMobile = $objItem->getUrlThumb($thumbSizeMobile);
			if($urlThumbMobile == $urlThumb)
				$urlThumbMobile = "";
		}
	
		//set other options
	
		$title = $objItem->getTitle();
		$type = $objItem->getType();
		$alt = $objItem->getAlt();
	
		$description = $objItem->getParam("ug_item_description");
	
		$enableLink = $objItem->getParam("ug_item_enable_link");
		$enableLink = UniteFunctionsUG::strToBool($enableLink);
	
		//combine description
		if($enableLink == true){
			$link = $objItem->getParam("ug_item_link");
		}
	
		$title = htmlspecialchars($title);
		$description = htmlspecialchars($description);
		$alt = htmlspecialchars($alt);
	
		$strType = "";
		if($type != UniteGalleryItem::TYPE_IMAGE){
			$strType = "data-type=\"{$type}\" ";
		}
	
		$addHtml = $this->getVideoAddHtml($type, $objItem);
	
		//set link (on tiles mode)
		$linkStart = "";
		$linkEnd = "";
		if($enableLink == true){
			$linkStart = "<a href=\"{$link}\">";
			$linkEnd = "</a>";
		}
	
		$html = "\n";
	
		//set html output
		$srcOutputType = HelperUG::getGeneralSetting("src_output_type");
		switch($srcOutputType){
			case "empty":
				$htmlSrc = "";
				$htmlDataImage = " data-image=\"{$urlImage}\"";
				$htmlDataThumb = "data-thumb=\"{$urlThumb}\"";
				break;
			case "image":
				$htmlSrc = $urlImage;
				$htmlDataImage = "";
				$htmlDataThumb = "data-thumb=\"{$urlThumb}\"";
				break;
			case "thumb":
				$htmlSrc = $urlThumb;
				$htmlDataImage = " data-image=\"{$urlImage}\"";
				$htmlDataThumb = "";
				break;
			default:
				UniteFunctionsUG::throwError("Wrong src output type: $srcOutputType");
			break;
		}
		if($linkStart)
			$html .= $nl.$linkStart;
	
		$html .= $nl."<img alt=\"{$alt}\"";
		$html .= $nl."    {$strType} src=\"{$htmlSrc}\"{$htmlDataImage}";
	
		if(!empty($urlImageMobile))
			$html .= $nl."     data-image-mobile=\"{$urlImageMobile}\"";
	
		if(!empty($htmlDataThumb))
			$html .= $nl."     {$htmlDataThumb}";
	
		if(!empty($urlThumbMobile))
			$html .= $nl."     data-thumb-mobile=\"{$urlThumbMobile}\"";
	
		$html .= $nl."     title=\"{$description}\"";
		$html .= $nl."     {$addHtml}style=\"display:none\">";
	
		if($linkEnd)
			$html .= $nl.$linkEnd;
	
		$totalHTML .= $html;
	
		endforeach;
	
		return($totalHTML);
	
	}
	
	
	/**
	 * get front html from items array
	 */
	private function getGalleryCatItemsHtmlFront($arrItems, $gallery){
	
		//get thumb size
		$thumbSize = $gallery->getParam("thumb_resolution");
		$bigImageSize = $gallery->getParam("big_image_resolution");
	
		$thumbSizeMobile = $gallery->getParam("thumb_resolution_mobile");
		$bigImageSizeMobile = $gallery->getParam("big_image_resolution_mobile");
	
		$isTilesType = $gallery->isTilesType();
		//get items html
		$htmlItems = $this->getItemsHtmlFront($arrItems, $thumbSize, $bigImageSize, $isTilesType, $thumbSizeMobile, $bigImageSizeMobile);
		
		return($htmlItems);
	}
	
	
	
	/**
	 * get loadmore
	 */
	public function getHtmlLoadmore($data){
		
		$galleryID = UniteFunctionsUG::getVal($data, "galleryID");
	
		$numLoadedItems = UniteFunctionsUG::getVal($data, "numitems",0);
		$numLoadedItems = (int)$numLoadedItems;
	
		$gallery = new UniteGalleryGallery();
		$gallery->initByID($galleryID);
		$catID = $gallery->getCatID();
		
		UniteFunctionsUG::validateNumeric($catID, "category id");
		
		$enableLoadmore = $gallery->getParam("show_loadmore");
		$enableLoadmore = UniteFunctionsUG::strToBool($enableLoadmore);
		
		if($enableLoadmore == false)
			UniteFunctionsUG::throwError("Load more feature disabled in this gallery");
		
		$maxItems = $gallery->getParam("loadmore_max_items");
		$minItems = $gallery->getParam("loadmore_min_items");
		
		$maxItems = (int)$maxItems;
		$minItems = (int)$minItems;
		
		$totalItems = $this->getTotalCatItems($catID);
		$remainingItems = $totalItems - $numLoadedItems;
		
		if($remainingItems <= 0)
			return(array());
		
		$limit = $this->getPagingLimit($remainingItems, $maxItems, $minItems);
		
		$arrItems = $this->getCatItemsOffset($catID, $numLoadedItems, $limit);
		
		//get show loadmore
		$numItems = count($arrItems);
		$totalItemsLoaded = $numLoadedItems + $numItems;
		$showLoadmore = true;
		
		
		if($totalItemsLoaded >= $totalItems)
			$showLoadmore = false;
			
		$htmlItems = $this->getGalleryCatItemsHtmlFront($arrItems, $gallery);
		
		$output = array();
		$output["show_loadmore"] = $showLoadmore;
		$output["html_items"] = $htmlItems;
		
		return($output);
	}
	
	
	/**
	 * get front html from data
	 */
	public function getHtmlFrontFromData($data){
	
		$catID = UniteFunctionsUG::getVal($data, "catid");
		$galleryID = UniteFunctionsUG::getVal($data, "galleryID");
	
		UniteFunctionsUG::validateNumeric($catID, "category id");
	
		if(empty($galleryID))
			UniteFunctionsUG::throwError("The gallery ID not given");
	
		//get thumb resolution param from the gallery
		$gallery = new UniteGalleryGallery();
		$gallery->initByID($galleryID);
	
		//validate if enable categories
		$enableCatTabs = $gallery->getParam('enable_category_tabs');
		$enableCatTabs = UniteFunctionsUG::strToBool($enableCatTabs);
	
		if($enableCatTabs == false)
			UniteFunctionsUG::throwError("The tabs functionality disabled");
	
		//check that the category id inside the params
		$params = $gallery->getParams();
		$tabCatIDs = $gallery->getParam("categorytabs_ids");
		$arrTabIDs = explode(",", $tabCatIDs);
		if(in_array($catID, $arrTabIDs) == false)
			UniteFunctionsUG::throwError("Get items not alowed for this category");
	
		//get arrItems
		$arrItems = $this->getCatItems($catID);
	
		$htmlItems = $this->getGalleryCatItemsHtmlFront($arrItems, $gallery);
	
		return($htmlItems);
	}
	
	
	
	private function a____________SETTERS_____________(){}
	
	
	/**
	 * 
	 * delete items
	 */
	private function deleteItems($arrItems){
		
		UniteFunctionsUG::validateNotEmpty($arrItems, "arrItems");
				
		//sanitize
		foreach($arrItems as $key=>$itemID)
			$arrItems[$key] = (int)$itemID;
		
		$strItems = implode($arrItems,",");
		$this->db->delete(GlobalsUG::$table_items,"id in($strItems)");
	}
	
	/**
	 * 
	 * duplciate items within same category 
	 */
	private function duplicateItems($arrItemIDs, $catID){
				
		foreach($arrItemIDs as $itemID){
			$this->copyItem($itemID);
		}
	}
	
	
	/**
	 * 
	 * copy items to some category
	 */
	private function copyItems($arrItemIDs,$catID){
		$category = new UniteGalleryCategories();		
		$category->validateCatExist($catID);
		
		foreach($arrItemIDs as $itemID){
			$this->copyItem($itemID,$catID);
		}
	}
	
	/**
	 * 
	 * move items to some category by change category id	 
	 */
	private function moveItem($itemID,$catID){
		$itemID = (int)$itemID;
		$catID = (int)$catID;
				
		$arrUpdate = array();
		$arrUpdate["catid"] = $catID;
		$this->db->update(GlobalsUG::$table_items,$arrUpdate,array("id"=>$itemID));
	}
	
	/**
	 * 
	 * move multiple items to some category
	 */
	private function moveItems($arrItemIDs, $catID){
		$category = new UniteGalleryCategories();		
		$category->validateCatExist($catID);
		
		foreach($arrItemIDs as $itemID){
			$this->moveItem($itemID, $catID);
		}
	}
	
	
	/**
	 * 
	 * save items order
	 */
	private function saveItemsOrder($arrItemIDs){
		
		//get items assoc
		$arrItems = $this->getItemsByIDs($arrItemIDs);
		$arrItems = UniteFunctionsUG::arrayToAssoc($arrItems,"id");
				
		$order = 0;
		foreach($arrItemIDs as $itemID){
			$order++;
			
			$arrItem = UniteFunctionsUG::getVal($arrItems, $itemID);
			if(!empty($arrItem) && $arrItem["ordering"] == $order)
				continue;
			
			$arrUpdate = array();
			$arrUpdate["ordering"] = $order; 
			$this->db->update(GlobalsUG::$table_items,$arrUpdate,array("id"=>$itemID));
		}

	}
	
	
	
	/**
	 * add image / images from data
	 * return items html
	 */
	private function addFromData_images($data){
	
		$catID = UniteFunctionsUG::getVal($data, "catID");
		
		$arrImages = UniteFunctionsUG::getVal($data, "urlImage");
		
		$isMultiple = false;
		if(is_array($arrImages) == true)
			$isMultiple = true;
		
		//add items, singe or multiple
		if($isMultiple == true){
		
			$itemHtml = "";
			foreach($arrImages as $item){
				$addData = array();
				$addData["catID"] = $catID;
				$urlImage = UniteFunctionsUG::getVal($item, "url");
				$urlImage = HelperUG::URLtoRelative($urlImage);
				$imageID = UniteFunctionsUG::getVal($item, "id", 0);
		
				//make thumb and store thumb address
				$addData["urlImage"] = $urlImage;
				$addData["imageID"] = $imageID;
		
				if(empty($imageID)){
					$urlThumb = $this->operations->createThumbs($urlImage);
					$addData["urlThumb"] = $urlThumb;
				}else{
					$addData["urlThumb"] = UniteProviderFunctionsUG::getThumbUrlFromImageID($imageID);
				}
				
				$addData["type"] = UniteGalleryItem::TYPE_IMAGE;
				
				
				$objItem = new UniteGalleryItem();
				$objItem->add($addData);
				$itemHtml .= $objItem->getHtmlForAdmin();
			}
		}else{
			$item = new UniteGalleryItem();
			$item->add($data);
		
			//get item html
			$itemHtml = $item->getHtmlForAdmin();
		}
		
		
		return($itemHtml);
	}

	/**
	 * add image / images from data
	 * return items html
	 */
	private function addFromData_media($data){
	
		$item = new UniteGalleryItem();
		$item->add($data);
				
		$itemHtml = $item->getHtmlForAdmin();		
		
		return($itemHtml);
	}
	
	
	
	/**
	 * 
	 * copy item to same or different category
	 * if copy to same, then the item will be duplicated 
	 */
	public function copyItem($itemID,$newCatID = -1){
		$order = $this->getMaxOrder($newCatID);
		$newOrder = $order+1;
		
		$fields_item = GlobalsUG::FIELDS_ITEMS;
		$sqlSelect = "select ".$fields_item." from ".GlobalsUG::$table_items." where id={$itemID}";
		$sqlInsert = "insert into ".GlobalsUG::$table_items." (".$fields_item.") ($sqlSelect)";
		
		$this->db->runSql($sqlInsert);
		
		$newItemID = $this->db->getLastInsertID();
		
		//update the ordering:
		$arrUpdate = array();
		$arrUpdate["ordering"] = $newOrder;
		if($newCatID != -1 && !empty($newCatID))
			$arrUpdate["catid"] = $newCatID;
		
		$this->db->update(GlobalsUG::$table_items,$arrUpdate,array("id"=>$newItemID));
	}
	
	
	
	/**
	 * 
	 * add item from data
	 */
	public function addFromData($data){
		
		$type = UniteFunctionsUG::getVal($data, "type");
		
		$catID = UniteFunctionsUG::getVal($data, "catID");
		UniteFunctionsUG::validateNumeric($catID,"category id");
		
		switch($type){
			case "image":
				$itemHtml = $this->addFromData_images($data);
			break;
			default:		//add media
				$itemHtml = $this->addFromData_media($data);
			break;
		}
		
		//get categories html
		$objCats = new UniteGalleryCategories();		
		$htmlCatList = $objCats->getHtmlCatList($catID);
		
		//output html items and cats
		$output = array();
		$output["htmlItem"] = $itemHtml;
		$output["htmlCats"] = $htmlCatList;
		
		return($output);
	} 
	
	
	/**
	 * remove items from data
	 */
	public function removeItemsFromData($data){
				
		$catID = UniteFunctionsUG::getVal($data, "catid");
		
		$itemIDs = UniteFunctionsUG::getVal($data, "arrItemsIDs");
		
		$this->deleteItems($itemIDs);
		
		$response = $this->getCatsAndItemsHtml($catID);
		
		return($response);
	}
	
	
	/**
	 * update item title
	 */
	public function updateItemTitleFromData($data){
		
		$itemID = $data["itemID"];
		$title = $data["title"];
		
		$arrUpdate = array();
		$arrUpdate["title"] = $title;
		$this->update($itemID,$arrUpdate);
	}
	
	
	/**
	 * 
	 * duplicate items
	 */
	public function duplicateItemsFromData($data){
		
		$catID = UniteFunctionsUG::getVal($data, "catID");
		
		$arrIDs = UniteFunctionsUG::getVal($data, "arrIDs");
		
		$this->duplicateItems($arrIDs, $catID);
		
		$response = $this->getCatsAndItemsHtml($catID);
		
		return($response);
	}
	
	
	/**
	 * 
	 * save items order from data
	 */
	public function saveOrderFromData($data){
		
		$catID = UniteFunctionsUG::getVal($data, "catid");
		$itemsIDs = UniteFunctionsUG::getVal($data, "items_order");
		if(empty($itemsIDs))
			return(false);
		
		//change category sortby to custom
		
		$objCat = new UniteGalleryCategory();
		$objCat->initByID($catID);
		$objCat->updateSortby(UniteGalleryCategory::SORTBY_CUSTOM);
		
		//change items order
		$this->saveItemsOrder($itemsIDs);
	}

	
	/**
	 * 
	 * copy / move items to some category 
	 * @param $data
	 */
	public function copyMoveItemsFromData($data){
		
		$targetCatID = UniteFunctionsUG::getVal($data, "targetCatID");
		$selectedCatID = UniteFunctionsUG::getVal($data, "selectedCatID");
		
		$arrItemIDs = UniteFunctionsUG::getVal($data, "arrItemIDs");
		
		UniteFunctionsUG::validateNotEmpty($targetCatID,"category id");
		UniteFunctionsUG::validateNotEmpty($arrItemIDs,"item id's");
		
		$operation = UniteFunctionsUG::getVal($data, "operation");
		
		switch($operation){
			case "copy":
				$this->copyItems($arrItemIDs, $targetCatID);
			break;
			case "move":
				$this->moveItems($arrItemIDs, $targetCatID);
			break;
			default:
				UniteFunctionsUG::throwError("Wrong operation: $operation");
			break;
		}
		
		$repsonse = $this->getCatsAndItemsHtml($selectedCatID);
		return($repsonse);
	}
	
	
	
	
	/**
	 * 
	 * update item data
	 * get html item for admin response
	 */
	public function updateItemData($data){
		$itemID = UniteFunctionsUG::getVal($data, "itemID");
		
		UniteFunctionsUG::validateNotEmpty($itemID, "item params");
		
		$item = new UniteGalleryItem();
		$item->initByID($itemID);
				
		$item->updateItemData($data);
		
		$htmlItem = $item->getHtmlForAdmin();
		
		$response = array("html_item"=>$htmlItem);
		
		return($response);
	}
	
	
	/**
	 * export category items from data
	 */
	public function exportCatItemsFromData($data){
		
		$catID = UniteFunctionsUG::getVal($data, "catid");
		
		$arrItems = $this->getCatItemsRecords($catID);
		$arrExportItems = array();
		
		foreach($arrItems as $item){
			unset($item["id"]);
			$arrExportItems[] = $item;
		}
		
		$strExport = serialize($arrExportItems);
		
		$filename = "items_export.txt";
		UniteFunctionsUG::downloadFileFromContent($strExport, $filename);
				
	}
	
	
	/**
	 * import category items from data
	 */
	public function importCatItemsFromData($data){
		
		$catID = UniteFunctionsUG::getVal($data, "catID");
		$objCat = new UniteGalleryCategories();
		$objCat->validateCatExist($catID);
		
		$arrFile = UniteFunctionsUG::getVal($_FILES, "import_file");
		if(empty($arrFile))
			UniteFunctionsUG::throwError("import file not found");
		
		$filepath = UniteFunctionsUG::getVal($arrFile, "tmp_name");
		UniteFunctionsUG::validateFilepath($filepath, "uploaded import file");
		
		$content = file_get_contents($filepath);
		
		if(empty($content))
			UniteFunctionsUG::throwError("wrong import file content");
			
		$arrRecords = @unserialize($content);
		
		if(is_array($arrRecords) == false)
			UniteFunctionsUG::throwError("wrong import file format");
		
		foreach($arrRecords as $record){
			
			$record["catid"] = $catID;
			$this->db->insert(GlobalsUG::$table_items, $record);
		}
		
		$response = $this->getCatsAndItemsHtml($catID);
		
		return($response);
	}
	
	
}

?>