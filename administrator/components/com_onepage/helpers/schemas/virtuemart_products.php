<?php 
defined('_JEXEC') or die('Restricted access');
$p[$prefix.'virtuemart_products.virtuemart_product_id'] = 'val:NULL'; //int(1) unsigned NOT NULL auto_increment
$p[$prefix.'virtuemart_products.virtuemart_vendor_id'] = 'val:1'; //int(1) unsigned NOT NULL DEFAULT '1' 
$p[$prefix.'virtuemart_products.product_parent_id'] = 'val:0'; //int(1) unsigned NOT NULL 
$p[$prefix.'virtuemart_products.product_sku'] = 'val:'; //varchar(255) 
$p[$prefix.'virtuemart_products.product_gtin'] = 'val:'; //varchar(64) 
$p[$prefix.'virtuemart_products.product_mpn'] = 'val:'; //varchar(64) 
$p[$prefix.'virtuemart_products.product_weight'] = 'val:'; //decimal(10,4) 
$p[$prefix.'virtuemart_products.product_weight_uom'] = 'val:KG'; //varchar(7) 
$p[$prefix.'virtuemart_products.product_length'] = 'val:0.1000'; //decimal(10,4) 
$p[$prefix.'virtuemart_products.product_width'] = 'val:0.1000'; //decimal(10,4) 
$p[$prefix.'virtuemart_products.product_height'] = 'val:0.1000'; //decimal(10,4) 
$p[$prefix.'virtuemart_products.product_lwh_uom'] = 'val:M'; //varchar(7) 
$p[$prefix.'virtuemart_products.product_url'] = 'val:'; //varchar(255) 
$p[$prefix.'virtuemart_products.product_in_stock'] = 'val:10'; //int(1) NOT NULL 
$p[$prefix.'virtuemart_products.product_ordered'] = 'val:0'; //int(1) NOT NULL 
$p[$prefix.'virtuemart_products.low_stock_notification'] = 'val:0'; //int(1) unsigned NOT NULL 
$p[$prefix.'virtuemart_products.product_available_date'] = 'val:0000-00-00 00:00:00'; //datetime NOT NULL DEFAULT '0000-00-00 00:00:00' 
$p[$prefix.'virtuemart_products.product_availability'] = 'val:'; //char(32) 
$p[$prefix.'virtuemart_products.product_special'] = 'val:0'; //tinyint(1) 
$p[$prefix.'virtuemart_products.product_sales'] = 'val:0'; //int(1) unsigned NOT NULL 
$p[$prefix.'virtuemart_products.product_unit'] = 'val:KG'; //varchar(8) 
$p[$prefix.'virtuemart_products.product_packaging'] = 'val:'; //decimal(8,4) unsigned 
$p[$prefix.'virtuemart_products.product_params'] = 'val:min_order_level=""|max_order_level=""|step_order_level=""|product_box="1"|'; //text NOT NULL 
$p[$prefix.'virtuemart_products.hits'] = 'val:'; //int(1) unsigned 
$p[$prefix.'virtuemart_products.intnotes'] = 'val:'; //text 
$p[$prefix.'virtuemart_products.metarobot'] = 'val:'; //varchar(400) 
$p[$prefix.'virtuemart_products.metaauthor'] = 'val:'; //varchar(400) 
$p[$prefix.'virtuemart_products.layout'] = 'val:0'; //char(16) 
$p[$prefix.'virtuemart_products.published'] = 'val:0'; //tinyint(1) 
$p[$prefix.'virtuemart_products.pordering'] = 'val:0'; //int(1) unsigned NOT NULL 
$p[$prefix.'virtuemart_products.created_on'] = 'val:NOW()'; //datetime NOT NULL DEFAULT '0000-00-00 00:00:00' 
$p[$prefix.'virtuemart_products.created_by'] = 'val:42'; //int(1) NOT NULL 
$p[$prefix.'virtuemart_products.modified_on'] = 'val:NOW()'; //datetime NOT NULL DEFAULT '0000-00-00 00:00:00' 
$p[$prefix.'virtuemart_products.modified_by'] = 'val:42'; //int(1) NOT NULL 
$p[$prefix.'virtuemart_products.locked_on'] = 'val:0000-00-00 00:00:00'; //datetime NOT NULL DEFAULT '0000-00-00 00:00:00' 
$p[$prefix.'virtuemart_products.locked_by'] = 'val:0'; //int(1) NOT NULL 
