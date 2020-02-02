<?php

/*------------------------------------------------------------------------
# BIT Vituemart Product Badges
# ------------------------------------------------------------------------
# author:    Barg-IT
# copyright: Copyright (C) 2014 Barg-IT
# @license:  http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website:   http://www.barg-it.de
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgSystemBit_vm_product_badges extends JPlugin {

    function plgSystemBit_vm_product_badges(&$subject, $config) {
        parent::__construct($subject, $config);
    }

	function onAfterRender() {

		// apply only if badges are in use on that page
		if (empty ($GLOBALS['badges_in_use']) && empty($_SESSION['badges_in_use'])) {return false;}
			
		$mainframe = JFactory::getApplication();
		if ($mainframe->isAdmin()) { return false; } // only in FE

        $document = JFactory::getDocument();
        $doctype = $document->getType();
        if ($doctype !== 'html') {return false;} // only HTML documents

		defined('DS') or define('DS', DIRECTORY_SEPARATOR);
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		$prefix=JURI::root( true );
		if ($prefix != '/') {$prefix=$prefix.'/';}

		$plugin_path = JURI::base().'plugins/system/bit_vm_product_badges/bitvmbadges/'; 
		$plugin_short_path = $prefix.'plugins/system/bit_vm_product_badges/bitvmbadges/';
		$plugin_img_path = JPATH_PLUGINS.'/system/bit_vm_product_badges/bitvmbadges/img/';
	
		// get parameters
		$multilingual = $this->params->get('multilingual','0');
		$mark_new_products = $this->params->get('handle_new','1');
		$product_new_range_days = $this->params->get('days_new','28');
		$created_modified = $this->params->get('created_modified','created_on');
		$product_new_badge_img = $this->params->get('new_badge_img','badge_new.png');
		$pos_new = $this->params->get('pos_new','topright');
		$pos_new_free_top = $this->params->get('pos_new_free_top','');
		$pos_new_free_left = $this->params->get('pos_new_free_left','');
		$mark_sale_products = $this->params->get('handle_sale','0');
		$product_sale_badge_img = $this->params->get('sale_badge_img','badge_wow.png');
		$pos_sale = $this->params->get('pos_sale','topright');
		$pos_sale_free_top = $this->params->get('pos_sale_free_top','');
		$pos_sale_free_left = $this->params->get('pos_sale_free_left','');
		$mark_discount_products = $this->params->get('handle_discount','0');
		$product_discount_badge_img = $this->params->get('discount_badge_img','badge_sale.png');
		$pos_discount = $this->params->get('pos_discount','topright');
		$pos_discount_free_top = $this->params->get('pos_discount_free_top','');
		$pos_discount_free_left = $this->params->get('pos_discount_free_left','');
		$mark_hot_products = $this->params->get('handle_hot','0');
		$product_hot_min_sales = $this->params->get('hot_min','10');
		$product_hot_badge_img = $this->params->get('hot_badge_img','badge_hot.png');
		$pos_hot = $this->params->get('pos_hot','topright');
		$pos_hot_free_top = $this->params->get('pos_hot_free_top','');
		$pos_hot_free_left = $this->params->get('pos_hot_free_left','');
		$mark_lowstock_products = $this->params->get('handle_lowstock','0');
		$product_lowstock_max = $this->params->get('lowstock_max','0');
		$product_lowstock_badge_img = $this->params->get('lowstock_badge_img','badge_lowstock.png');
		$pos_lowstock = $this->params->get('pos_lowstock','topleft');
		$pos_lowstock_free_top = $this->params->get('pos_lowstock_free_top','');
		$pos_lowstock_free_left = $this->params->get('pos_lowstock_free_left','');
		$product_ids1 = $this->params->get('individual_product_ids1','');
		$product_badge_img1 = $this->params->get('product_badge_img1','');
		$product_ids2 = $this->params->get('individual_product_ids2','');
		$product_badge_img2 = $this->params->get('product_badge_img2','');
		$product_ids3 = $this->params->get('individual_product_ids3','');
		$product_badge_img3 = $this->params->get('product_badge_img3','');
		$product_ids4 = $this->params->get('individual_product_ids4','');
		$product_badge_img4 = $this->params->get('product_badge_img4','');
		$product_ids5 = $this->params->get('individual_product_ids5','');
		$product_badge_img5 = $this->params->get('product_badge_img5','');
		$pos_product = $this->params->get('pos_product','topright');
		$pos_product_free_top = $this->params->get('pos_product_free_top','');
		$pos_product_free_left = $this->params->get('pos_product_free_left','');
		$category_ids1 = $this->params->get('individual_category_ids1','');
		$category_badge_img1 = $this->params->get('category_badge_img1','');
		$category_ids2 = $this->params->get('individual_category_ids2','');
		$category_badge_img2 = $this->params->get('category_badge_img2','');
		$category_ids3 = $this->params->get('individual_category_ids3','');
		$category_badge_img3 = $this->params->get('category_badge_img3','');
		$category_ids4 = $this->params->get('individual_category_ids4','');
		$category_badge_img4 = $this->params->get('category_badge_img4','');
		$category_ids5 = $this->params->get('individual_category_ids5','');
		$category_badge_img5 = $this->params->get('category_badge_img5','');
		$pos_category = $this->params->get('pos_category','topright');
		$pos_category_free_top = $this->params->get('pos_category_free_top','');
		$pos_category_free_left = $this->params->get('pos_category_free_left','');

		$pos_new_style = "";
		$pos_sale_style = "";
		$pos_discount_style = "";
		$pos_hot_style = "";
		$pos_lowstock_style = "";
		$pos_product_style = "";
		$pos_category_style = "";

		$mark_products_obj = "";
		$mark_products_arr = array();
		
		$db = JFactory::getDBO();

		// support multilingual badges
		$lang_prefix="";
		if ($multilingual == 1) {	
			$lang = JFactory::getLanguage();
			$lang_prefix=$lang->getTag();
		}

		// determine all new products 
		if ($mark_new_products == 1) {

			$date = JFactory::getDate(); 
			$today = $date->toSql();			
			
			$product_new_range_secs = $product_new_range_days * 86400;

			$q = "SELECT virtuemart_product_id,$created_modified FROM #__virtuemart_products WHERE published = '1' AND DATE_ADD($created_modified, INTERVAL $product_new_range_secs SECOND) >= '$today' ";
			$db->setQuery($q);
			$db->query();
			
			$rows_new = $db->loadObjectList();
			foreach ($rows_new as $row_new) {			
			$mark_products_arr[$row_new->virtuemart_product_id]='new';
			}		

			if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$product_new_badge_img)) { 
				$product_new_badge_img = $plugin_path.'img/'.$lang_prefix.'/'.$product_new_badge_img;
			}
			else {$product_new_badge_img = $plugin_path.'img/'.$product_new_badge_img;}	

			switch ($pos_new) {
				case 'topleft': $pos_new_style='top:0px;left:0px';break;
				case 'topright': $pos_new_style='top:0px;right:0px';break;
				case 'free': 
					if ($pos_new_free_top == "") {$pos_top='';} else {$pos_top='top:'.$pos_new_free_top.'px;';}
					if ($pos_new_free_left == "") {$pos_left='';} else {$pos_left='left:'.$pos_new_free_left.'px;';}
					$pos_new_style=$pos_top.$pos_left;			
			}
		}
		
		// determine all special products 
		if ($mark_sale_products == 1) {
			$q = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE published = '1' AND product_special = '1' ";
			$db->setQuery($q);
			$db->query();

			$rows_special = $db->loadObjectList();
			foreach ($rows_special as $row_special) {
				if (array_key_exists($row_special->virtuemart_product_id,$mark_products_arr) ) {
					$mark_products_arr[$row_special->virtuemart_product_id] = $mark_products_arr[$row_special->virtuemart_product_id].',sale';
				}
				else {
					$mark_products_arr[$row_special->virtuemart_product_id]='sale';
				}
			}

			if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$product_sale_badge_img)) { 
				$product_sale_badge_img = $plugin_path.'img/'.$lang_prefix.'/'.$product_sale_badge_img;
			}
			else {$product_sale_badge_img = $plugin_path.'img/'.$product_sale_badge_img;}	

			switch ($pos_sale) {
				case 'topleft': $pos_sale_style='top:0px;left:0px';break;
				case 'topright': $pos_sale_style='top:0px;right:0px';break;
				case 'free': 
					if ($pos_sale_free_top == "") {$pos_top='';} else {$pos_top='top:'.$pos_sale_free_top.'px;';}
					if ($pos_sale_free_left == "") {$pos_left='';} else {$pos_left='left:'.$pos_sale_free_left.'px;';}
					$pos_sale_style=$pos_top.$pos_left;			
			}
		}

		// determine all discounted products 
		if ($mark_discount_products == 1) {	
			$null_date = $db->getNullDate();
			$now_date1 = JFactory::getDate();
			$now_date = $now_date1->toSql();

			// determine shopper group of customer
			$usermodel = VmModel::getModel('user');
			$currentVMuser = $usermodel->getUser();
			$currentVMuserid = $currentVMuser->virtuemart_user_id;
			$virtuemart_shoppergroup_ids_arr = (array)$currentVMuser->shopper_groups;
			$virtuemart_shoppergroup_ids = join(',',$virtuemart_shoppergroup_ids_arr); 			
			
			// determine discount calculation rules (with restrictions)
			$q1="SELECT cal.virtuemart_calc_id, cc.virtuemart_category_id Kategorie, cmf.virtuemart_manufacturer_id Manufacturer, cs.virtuemart_shoppergroup_id Shoppergroup 
			     FROM #__virtuemart_calcs cal 
				 LEFT JOIN #__virtuemart_calc_categories cc ON cal.virtuemart_calc_id=cc.virtuemart_calc_id
				 LEFT JOIN #__virtuemart_calc_manufacturers cmf ON cal.virtuemart_calc_id=cmf.virtuemart_calc_id
				 LEFT JOIN #__virtuemart_calc_shoppergroups cs ON cal.virtuemart_calc_id=cs.virtuemart_calc_id
				 WHERE cal.published=1 AND LOCATE('-',cal.calc_value_mathop) > 0 AND
				 (cal.publish_up = '$null_date' OR cal.publish_up <= '$now_date') AND 
				 (cal.publish_down = '$null_date' OR cal.publish_down >= '$now_date') ";

			$db->setQuery($q1);
			$db->query();
			$discount_rules = $db->loadObjectList();
			$prod_cats12 = Array();
			
			// if no rules are defined, check for individual price overrides only
			if (empty($discount_rules)) {
				$q2="SELECT pr.virtuemart_product_id FROM #__virtuemart_product_prices pr
				 WHERE  (pr.virtuemart_shoppergroup_id=0 OR pr.virtuemart_shoppergroup_id IS NULL OR pr.virtuemart_shoppergroup_id IN ($virtuemart_shoppergroup_ids)) AND
						pr.override!=0 
				 GROUP BY pr.virtuemart_product_id";

				$db->setQuery($q2);
				$db->query();
				$prod_cats_o = $db->loadObjectList();
				
				$prod_cats12=array_merge($prod_cats12,$prod_cats_o);
			}
			else {
			foreach ($discount_rules as $discount_rule) {
				$discount_id=$discount_rule->virtuemart_calc_id; 
				$discount_category=$discount_rule->Kategorie; $cat_where=''; if ($discount_category != '') {$cat_where=" AND c.virtuemart_category_id=$discount_category";}
				$discount_mf=$discount_rule->Manufacturer; $mf_where=''; if ($discount_mf != '') {$mf_where=" AND mf.virtuemart_manufacturer_id=$discount_mf";}
				$discount_sg=$discount_rule->Shoppergroup; $sg_where='';if ($discount_sg != '') {$sg_where=" AND $discount_sg IN ($virtuemart_shoppergroup_ids) ";}
			
			// get the products for this rule which aren't overruled by more specific information 
			// OR products which explicitly are assigned that rule 
			// OR products that have a price override
			$q2="SELECT p.virtuemart_product_id FROM #__virtuemart_product_prices pr, #__virtuemart_product_categories c, #__virtuemart_products p
				 LEFT JOIN #__virtuemart_product_manufacturers mf ON p.virtuemart_product_id=mf.virtuemart_product_id
				 WHERE  (p.virtuemart_product_id=c.virtuemart_product_id OR p.product_parent_id=c.virtuemart_product_id) AND						
						p.virtuemart_product_id=pr.virtuemart_product_id AND
						(pr.virtuemart_shoppergroup_id=0 OR pr.virtuemart_shoppergroup_id IS NULL OR pr.virtuemart_shoppergroup_id IN ($virtuemart_shoppergroup_ids)) AND
						( ( pr.override!=1 AND (pr.product_discount_id = 0 OR pr.product_discount_id IS NULL)
						  ".$cat_where.$mf_where.$sg_where.") OR
						  (pr.product_discount_id > 0 AND pr.product_discount_id=$discount_id ) OR
						  (pr.override!=0 ) )
				 GROUP BY p.virtuemart_product_id";

			$db->setQuery($q2);
			$db->query();
			$one_rule_products = $db->loadObjectList();
			$prod_cats12 = array_merge($prod_cats12,$one_rule_products);
			}
			}
		
			/* now get the variants that have no individual prices */  
			if (!empty($prod_cats12)) {
				foreach ($prod_cats12 as $prod_cat) {
					$prod_cats_ids_arr[]= $prod_cat->virtuemart_product_id;
				}
				$prod_cats_ids = join(',',$prod_cats_ids_arr);
				$q3="SELECT p.virtuemart_product_id FROM #__virtuemart_products p LEFT JOIN #__virtuemart_product_prices pr using(virtuemart_product_id) WHERE p.product_parent_id IN ($prod_cats_ids) 
					 AND pr.virtuemart_product_id IS NULL";
				$db->setQuery($q3);
				$db->query();
				$prod_cats3 = $db->loadObjectList();
				$prod_cats = array_merge($prod_cats12,$prod_cats3);
			}
			else {$prod_cats = $prod_cats12;}	

			foreach ($prod_cats as $prod_cat) {
			if (array_key_exists($prod_cat->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$prod_cat->virtuemart_product_id] = $mark_products_arr[$prod_cat->virtuemart_product_id].',discount';
					}
					else {
						$mark_products_arr[$prod_cat->virtuemart_product_id]='discount';
					}
			}		
			
			if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$product_discount_badge_img)) { 
				$product_discount_badge_img = $plugin_path.'img/'.$lang_prefix.'/'.$product_discount_badge_img;
			}
			else {$product_discount_badge_img = $plugin_path.'img/'.$product_discount_badge_img;}	

			switch ($pos_discount) {
				case 'topleft': $pos_discount_style='top:0px;left:0px';break;
				case 'topright': $pos_discount_style='top:0px;right:0px';break;
				case 'free': 
					if ($pos_discount_free_top == "") {$pos_top='';} else {$pos_top='top:'.$pos_discount_free_top.'px;';}
					if ($pos_discount_free_left == "") {$pos_left='';} else {$pos_left='left:'.$pos_discount_free_left.'px;';}
					$pos_discount_style=$pos_top.$pos_left;			
			}	
		}


		// determine all bestsellers (sales of child products are summed up to main product)
		if ($mark_hot_products == 1) {
			$child_parent_array = $this->get_top_parents();

			$q = "SELECT virtuemart_product_id, product_sales FROM #__virtuemart_products p
			      WHERE published='1' AND ((p.product_parent_id > 0 AND product_sales > 0) OR p.product_parent_id = 0) ";
			$db->setQuery($q);
			$db->query();
			$rows_hot = $db->loadObjectList();

			// now, add the sales of the children 
			foreach ($rows_hot as $row_hot) {
				foreach ($child_parent_array as $c_p) {
					if ($c_p[1] ==  $row_hot->virtuemart_product_id)
       				   	{  $row_hot->product_sales = $row_hot->product_sales + $c_p[2];  }		
				}					       
			}
	
			foreach ($rows_hot as $row_hot) {
				if ($row_hot->product_sales >= $product_hot_min_sales) {			
					if (array_key_exists($row_hot->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_hot->virtuemart_product_id] = $mark_products_arr[$row_hot->virtuemart_product_id].',hot';
					}
					else {
						$mark_products_arr[$row_hot->virtuemart_product_id]='hot';
					}
				}
			}

			if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$product_hot_badge_img)) { 
				$product_hot_badge_img = $plugin_path.'img/'.$lang_prefix.'/'.$product_hot_badge_img;
			}
			else {$product_hot_badge_img = $plugin_path.'img/'.$product_hot_badge_img;}	

			switch ($pos_hot) {
				case 'topleft': $pos_hot_style='top:0px;left:0px';break;
				case 'topright': $pos_hot_style='top:0px;right:0px';break;
				case 'free': 
					if ($pos_hot_free_top == "") {$pos_top='';} else {$pos_top='top:'.$pos_hot_free_top.'px;';}
					if ($pos_hot_free_left == "") {$pos_left='';} else {$pos_left='left:'.$pos_hot_free_left.'px;';}
					$pos_hot_style=$pos_top.$pos_left;			
			}	
		}

		// determine all sold out/low stock 
		if ($mark_lowstock_products == 1) {
			$child_parent_array = $this->get_top_parents();

			$q = "SELECT virtuemart_product_id, product_in_stock, product_ordered FROM #__virtuemart_products p
			      WHERE published='1' AND (product_in_stock - product_ordered) <= $product_lowstock_max ";
			$db->setQuery($q);
			$db->query();
			$rows_lowstock = $db->loadObjectList();

			foreach ($rows_lowstock as $row_lowstock) {
					if (array_key_exists($row_lowstock->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_lowstock->virtuemart_product_id] = $mark_products_arr[$row_hot->virtuemart_product_id].',lowstock';
					}
					else {
						$mark_products_arr[$row_lowstock->virtuemart_product_id]='lowstock';
					}
			}

			if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$product_lowstock_badge_img)) { 
				$product_lowstock_badge_img = $plugin_path.'img/'.$lang_prefix.'/'.$product_lowstock_badge_img;
			}
			else {$product_lowstock_badge_img = $plugin_path.'img/'.$product_lowstock_badge_img;}	

			switch ($pos_lowstock) {
				case 'topleft': $pos_lowstock_style='top:0px;left:0px';break;
				case 'topright': $pos_lowstock_style='top:0px;right:0px';break;
				case 'free': 
					if ($pos_lowstock_free_top == "") {$pos_top='';} else {$pos_top='top:'.$pos_lowstock_free_top.'px;';}
					if ($pos_lowstock_free_left == "") {$pos_left='';} else {$pos_left='left:'.$pos_lowstock_free_left.'px;';}
					$pos_lowstock_style=$pos_top.$pos_left;			
			}	
		}
		
		
		// determine 5 sets of individual products
		if ($product_ids1 != '' || $product_ids2 != '' || $product_ids3 != '' || $product_ids4 != '' || $product_ids5 != '') {
			if ($product_ids1 != '') {
				$q = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE published='1' AND virtuemart_product_id IN ($product_ids1) ";
				$db->setQuery($q);
				$db->query();
				$rows_products = $db->loadObjectList();
				foreach ($rows_products as $row_product) {
					if (array_key_exists($row_product->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_product->virtuemart_product_id] = $mark_products_arr[$row_product->virtuemart_product_id].',product1';
					}	
					else {
						$mark_products_arr[$row_product->virtuemart_product_id]='product1';
					}	
				}
				if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$product_badge_img1)) { 
					$product_badge_img1 = $plugin_path.'img/'.$lang_prefix.'/'.$product_badge_img1;
				}
					else {$product_badge_img1 = $plugin_path.'img/'.$product_badge_img1;}	
				}
			
				if ($product_ids2 != '') {
				$q = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE published='1' AND virtuemart_product_id IN ($product_ids2) ";
				$db->setQuery($q);
				$db->query();
				$rows_products = $db->loadObjectList();
				foreach ($rows_products as $row_product) {
					if (array_key_exists($row_product->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_product->virtuemart_product_id] = $mark_products_arr[$row_product->virtuemart_product_id].',product2';
					}	
					else {
						$mark_products_arr[$row_product->virtuemart_product_id]='product2';
					}	
				}
				if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$product_badge_img2)) { 
					$product_badge_img2 = $plugin_path.'img/'.$lang_prefix.'/'.$product_badge_img2;
				}
					else {$product_badge_img2 = $plugin_path.'img/'.$product_badge_img2;}	
				}	
			
				if ($product_ids3 != '') {
				$q = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE published='1' AND virtuemart_product_id IN ($product_ids3) ";
				$db->setQuery($q);
				$db->query();
				$rows_products = $db->loadObjectList();
				foreach ($rows_products as $row_product) {
					if (array_key_exists($row_product->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_product->virtuemart_product_id] = $mark_products_arr[$row_product->virtuemart_product_id].',product3';
					}	
					else {
						$mark_products_arr[$row_product->virtuemart_product_id]='product3';
					}	
				}
				if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$product_badge_img3)) { 
					$product_badge_img3 = $plugin_path.'img/'.$lang_prefix.'/'.$product_badge_img3;
				}
					else {$product_badge_img3 = $plugin_path.'img/'.$product_badge_img3;}	
				}
			
				if ($product_ids4 != '') {
				$q = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE published='1' AND virtuemart_product_id IN ($product_ids4) ";
				$db->setQuery($q);
				$db->query();
				$rows_products = $db->loadObjectList();
				foreach ($rows_products as $row_product) {
					if (array_key_exists($row_product->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_product->virtuemart_product_id] = $mark_products_arr[$row_product->virtuemart_product_id].',product4';
					}	
					else {
						$mark_products_arr[$row_product->virtuemart_product_id]='product4';
					}	
				}
				if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$product_badge_img4)) { 
					$product_badge_img4 = $plugin_path.'img/'.$lang_prefix.'/'.$product_badge_img4;
				}
				else {$product_badge_img4 = $plugin_path.'img/'.$product_badge_img4;}	
				}
			
				if ($product_ids5 != '') {
				$q = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE published='1' AND virtuemart_product_id IN ($product_ids5) ";
				$db->setQuery($q);
				$db->query();
				$rows_products = $db->loadObjectList();
				foreach ($rows_products as $row_product) {
					if (array_key_exists($row_product->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_product->virtuemart_product_id] = $mark_products_arr[$row_product->virtuemart_product_id].',product5';
					}	
					else {
						$mark_products_arr[$row_product->virtuemart_product_id]='product5';
					}	
				}
				if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$product_badge_img5)) { 
					$product_badge_img5 = $plugin_path.'img/'.$lang_prefix.'/'.$product_badge_img5;
				}
				else {$product_badge_img5 = $plugin_path.'img/'.$product_badge_img5;}	
				}
			
				switch ($pos_product) {
					case 'topleft': $pos_product_style='top:0px;left:0px';break;
					case 'topright': $pos_product_style='top:0px;right:0px';break;
					case 'free': 
						if ($pos_product_free_top == "") {$pos_top='';} else {$pos_top='top:'.$pos_product_free_top.'px;';}
						if ($pos_product_free_left == "") {$pos_left='';} else {$pos_left='left:'.$pos_product_free_left.'px;';}
						$pos_product_style=$pos_top.$pos_left;			
				}
		}	
		
		// determine all products of specified categories
		if ($category_ids1 != '' || $category_ids1 != '' || $category_ids1 != '' || $category_ids1 != '' || $category_ids1 != '' ) {
			if ($category_ids1 != '') {
				$q = "SELECT p.virtuemart_product_id FROM #__virtuemart_products p, #__virtuemart_product_categories x WHERE published='1' AND p.virtuemart_product_id=x.virtuemart_product_id AND virtuemart_category_id IN ($category_ids1) ";
//				$q = "SELECT p.virtuemart_product_id, p.product_parent_id FROM #__virtuemart_products p, #__virtuemart_product_categories x WHERE published='1' AND (p.virtuemart_product_id=x.virtuemart_product_id OR p.product_parent_id=x.virtuemart_product_id) AND virtuemart_category_id IN ($category_ids1) ";
				$db->setQuery($q);
				$db->query();
				$rows_categories = $db->loadObjectList();
				foreach ($rows_categories as $row_categories) {
					if (array_key_exists($row_categories->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_categories->virtuemart_product_id] = $mark_products_arr[$row_categories->virtuemart_product_id].',category1';
					}	
					else {
						$mark_products_arr[$row_categories->virtuemart_product_id]='category1';
					}	
				}
				if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$category_badge_img1)) { 
					$category_badge_img1 = $plugin_path.'img/'.$lang_prefix.'/'.$category_badge_img1;
				}
				else {$category_badge_img1 = $plugin_path.'img/'.$category_badge_img1;}	
			}
			if ($category_ids2 != '') {
				$q = "SELECT p.virtuemart_product_id FROM #__virtuemart_products p, #__virtuemart_product_categories x WHERE published='1' AND p.virtuemart_product_id=x.virtuemart_product_id AND virtuemart_category_id IN ($category_ids2) ";
				$db->setQuery($q);
				$db->query();
				$rows_categories = $db->loadObjectList();
				foreach ($rows_categories as $row_categories) {
					if (array_key_exists($row_categories->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_categories->virtuemart_product_id] = $mark_products_arr[$row_categories->virtuemart_product_id].',category2';
					}	
					else {
						$mark_products_arr[$row_categories->virtuemart_product_id]='category2';
					}	
				}
				if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$category_badge_img2)) { 
					$category_badge_img2 = $plugin_path.'img/'.$lang_prefix.'/'.$category_badge_img2;
				}
				else {$category_badge_img2 = $plugin_path.'img/'.$category_badge_img2;}	
			}
			if ($category_ids3 != '') {
				$q = "SELECT p.virtuemart_product_id FROM #__virtuemart_products p, #__virtuemart_product_categories x WHERE published='1' AND p.virtuemart_product_id=x.virtuemart_product_id AND virtuemart_category_id IN ($category_ids3) ";
				$db->setQuery($q);
				$db->query();
				$rows_categories = $db->loadObjectList();
				foreach ($rows_categories as $row_categories) {
					if (array_key_exists($row_categories->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_categories->virtuemart_product_id] = $mark_products_arr[$row_categories->virtuemart_product_id].',category3';
					}	
					else {
						$mark_products_arr[$row_categories->virtuemart_product_id]='category3';
					}	
				}
				if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$category_badge_img3)) { 
					$category_badge_img3 = $plugin_path.'img/'.$lang_prefix.'/'.$category_badge_img3;
				}
				else {$category_badge_img3 = $plugin_path.'img/'.$category_badge_img3;}	
			}
			if ($category_ids4 != '') {
				$q = "SELECT p.virtuemart_product_id FROM #__virtuemart_products p, #__virtuemart_product_categories x WHERE published='1' AND p.virtuemart_product_id=x.virtuemart_product_id AND virtuemart_category_id IN ($category_ids4) ";
				$db->setQuery($q);
				$db->query();
				$rows_categories = $db->loadObjectList();
				foreach ($rows_categories as $row_categories) {
					if (array_key_exists($row_categories->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_categories->virtuemart_product_id] = $mark_products_arr[$row_categories->virtuemart_product_id].',category4';
					}	
					else {
						$mark_products_arr[$row_categories->virtuemart_product_id]='category4';
					}	
				}
				if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$category_badge_img4)) { 
					$category_badge_img4 = $plugin_path.'img/'.$lang_prefix.'/'.$category_badge_img4;
				}
				else {$category_badge_img4 = $plugin_path.'img/'.$category_badge_img4;}	
			}
			if ($category_ids5 != '') {
				$q = "SELECT p.virtuemart_product_id FROM #__virtuemart_products p, #__virtuemart_product_categories x WHERE published='1' AND p.virtuemart_product_id=x.virtuemart_product_id AND virtuemart_category_id IN ($category_ids5) ";
				$db->setQuery($q);
				$db->query();
				$rows_categories = $db->loadObjectList();
				foreach ($rows_categories as $row_categories) {
					if (array_key_exists($row_categories->virtuemart_product_id,$mark_products_arr) ) {
						$mark_products_arr[$row_categories->virtuemart_product_id] = $mark_products_arr[$row_categories->virtuemart_product_id].',category5';
					}	
					else {
						$mark_products_arr[$row_categories->virtuemart_product_id]='category5';
					}	
				}
				if ($multilingual == 1 && file_exists($plugin_img_path.$lang_prefix.'/'.$category_badge_img5)) { 
					$category_badge_img5 = $plugin_path.'img/'.$lang_prefix.'/'.$category_badge_img5;
				}
				else {$category_badge_img5 = $plugin_path.'img/'.$category_badge_img5;}	
			}
			switch ($pos_category) {
				case 'topleft': $pos_category_style='top:0px;left:0px';break;
				case 'topright': $pos_category_style='top:0px;right:0px';break;
				case 'free': 
					if ($pos_category_free_top == "") {$pos_top='';} else {$pos_top='top:'.$pos_category_free_top.'px;';}
					if ($pos_category_free_left == "") {$pos_left='';} else {$pos_left='left:'.$pos_category_free_left.'px;';}
					$pos_category_style=$pos_top.$pos_left;			
			}
		}	

		if ($mark_products_arr != "") {
			foreach ($mark_products_arr as $mark_product_id=>$badge_types ) {
				$mark_products_obj .= "'".$mark_product_id."':'".$badge_types."',";
			}			
			$mark_products_obj = '{'. substr($mark_products_obj,0,strlen($mark_products_obj)-1) . '}';
			

			// place javascript link and call in the head
			$body = JResponse::getBody();
			$css_link = "<link rel=\"stylesheet\" href=\"$plugin_short_path"."css/bitvmbadges.css\" type=\"text/css\" />";
			$js_link = "<script type=\"text/javascript\" src=\"$plugin_short_path"."js/bitvmbadges.js\"></script>";
			$js_call = 
<<<BIT_PURE_JS
<script type="text/javascript">
	jQuery(document).ready( bit_badging );
	function bit_badging() {
	jQuery('div.product_badge').each(function(){
			var badge_relevant=$mark_products_obj;
			new_product_on_page(jQuery(this), badge_relevant,'$product_new_badge_img','$pos_new_style','$product_sale_badge_img',
				'$pos_sale_style','$product_discount_badge_img','$pos_discount_style','$product_hot_badge_img','$pos_hot_style',
				'$product_lowstock_badge_img','$pos_lowstock_style',
				'$product_badge_img1','$product_badge_img2','$product_badge_img3','$product_badge_img4','$product_badge_img5','$pos_product_style','$category_badge_img1','$category_badge_img2','$category_badge_img3','$category_badge_img4','$category_badge_img5','$pos_category_style');
			}) 
	}
</script> 
BIT_PURE_JS;
			
			$body = preg_replace ("/<\/head>/", "\n".$css_link."\n".$js_link."\n".$js_call."\n</head>", $body); 		

			JResponse::setBody($body);
		}
           
		return true;        
    }
	
	// get array of child ids with their top most parent ids plus sales
	function get_top_parents() {
		$children_parents = array();
		$i = 0;
		$q_children="SELECT p.virtuemart_product_id, p.product_parent_id, p.product_sales FROM #__virtuemart_products p WHERE product_parent_id > 0 ";
		$db_children =& JFactory::getDBO(); 
		$db_children->setQuery($q_children);
		$rows_children = $db_children->loadObjectList();
		foreach ($rows_children as $row_child) {
			$top_parent=$this->get_top_parent($row_child->product_parent_id);   
            $children_parents[$i][0] = $row_child->virtuemart_product_id;
			$children_parents[$i][1] = $top_parent;
   			$children_parents[$i][2] = $row_child->product_sales;      
			$i++;
		}
		return $children_parents;
	}
	
	// get the top most parent
	function get_top_parent($pid) {
	$q_parent="SELECT product_parent_id FROM #__virtuemart_products WHERE virtuemart_product_id = $pid ";
	$db_parents =& JFactory::getDBO();
	$db_parents->setQuery($q_parent);
	$rows_parents = $db_parents->loadObjectList();
	$row_parents = $rows_parents[0];
	if ($row_parents->product_parent_id == 0) {
		return $pid;} else {
		return $this->get_top_parent($row_parents->product_parent_id);
		}
	}

}

?>