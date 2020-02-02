CREATE TABLE IF NOT EXISTS `#__excel2vm` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `profile` varchar(256) NOT NULL,
  `active` text NOT NULL,
  `config` text NOT NULL,
  `default_profile` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;



CREATE TABLE IF NOT EXISTS `#__excel2vm_backups` (
  `backup_id` int(20) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(256) NOT NULL,
  `size` int(20) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`backup_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__excel2vm_fields` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(256) NOT NULL,
  `type` varchar(256) NOT NULL DEFAULT 'default',
  `example` varchar(256) NOT NULL,
  `extra_id` VARCHAR( 256 ) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`(255))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

REPLACE INTO `#__excel2vm_fields` (`id`, `name`, `title`, `type`, `example`, `extra_id`) VALUES
(1, 'product_sku', 'PRODUCT_SKU','default', 'GC0012;584542', 0),
(2, 'product_name', 'PRODUCT_NAME','default', 'Ardo 1745;Samsung TE5685', 0),
(3, 'product_price', 'COST_PRICE','default', '255;300', 2),
(4, 'product_override_price', 'OVERRIDE_PRICE','default', '200;250', 2),
(5, 'product_s_desc', 'SHORT_DESCRIPTION','default', 'Ardo 1745;Samsung TE5685', 0),
(6, 'product_desc', 'PRODUCT_DESCRIPTION','default', 'Ardo 1745;Samsung TE5685', 0),
(7, 'file_url_thumb', 'USED_THUMB_URL','default', 'thumb_refregerator1.jpg;thumb_refregerator2.jpg', 0),
(8, 'file_url', 'USED_IMAGE_URL','default', 'refregerator1.jpg;refregerator2.jpg', 0),
(9, 'virtuemart_vendor_id', 'VENDOR_ID','default', '1;2', 0),
(10, 'published', 'PUBLISHED','default', '1;0', 0),
(11, 'product_weight', 'WEIGHT','default', '50;50000', 0),
(12, 'product_weight_uom', 'WEIGHT_UOM','default', 'Kg;g', 0),
(13, 'product_length', 'PRODUCT_LENGTH','default', '40;40000', 0),
(14, 'product_width', 'WIDTH','default', '40;40000', 0),
(15, 'product_height', 'HEIGHT','default', '80;80000', 0),
(16, 'product_lwh_uom', 'LWH_UOM', 'default', 'м;см', 0),
(17, 'product_url', 'PRODUCT_LINK', 'default', 'http://site.ru/product1.zip;http://site.ru/product2.zip', 0),
(18, 'product_in_stock', 'PRODUCT_IN_STOCK', 'default', '5;0', 0),
(19, 'product_availability', 'PRODUCT_AVAILABILITY', 'default', 'PRODUCT_AVAILABILITY_EXAMPLE1;PRODUCT_AVAILABILITY_EXAMPLE2', 0),
(20, 'product_unit', 'PRODUCT_UNITS', 'default', 'PRODUCT_UNITS_EXAMPLE1;PRODUCT_UNITS_EXAMPLE2', 0),
(21, 'product_packaging', 'PACKAGING',  'default', '12;20', 0),
(22, 'virtuemart_manufacturer_id', 'MANUFACTURER_ID', 'default', '1;2', 0),
(23, 'mf_name', 'MANUFACTURER', 'default', 'Ardo;Sumsung', 0),
(24, 'metadesc', 'PRODUCT_META_DESCRIPTION', 'default', 'PRODUCT_META_DESCRIPTION_EXAMPLE;PRODUCT_META_DESCRIPTION_EXAMPLE', '0'),
(25, 'metakey', 'PRODUCT_META_KEY', 'default', 'PRODUCT_META_KEY_EXAMPLE1;PRODUCT_META_KEY_EXAMPLE2', '0'),
(26, 'customtitle', 'CUSTOM_PAGE_TITLE', 'default', 'CUSTOM_PAGE_TITLE_EXAMPLE1;CUSTOM_PAGE_TITLE_EXAMPLE2', '0'),
(27, 'related_products', 'RELATED_PRODUCTS', 'default', '2|55|66;', 0),
(28, 'virtuemart_product_id', 'PRODUCT_ID','default', '1;2', 0),
(29, 'product_parent_id', 'PRODUCT_PARENT_ID','default', '1;2', 0),
(30, 'parent_sku', 'PARENT_PRODUCT_SKU', 'default', 'SKU12;TTOT-15', '0'),
(31, 'path', 'CATEGORY_NOMBER', 'default', '1;2', 0),
(32, 'file_description', 'IMAGE_DESCRIPTION', 'default', 'Ardo 1745;Samsung TE5685', 0),
(33, 'file_meta', 'IMAGE_ALT', 'default', 'Ardo 1745;Samsung TE5685', '0'),
(34, 'product_url_path', 'PRODUCT_URL_PATH', 'default', 'PRODUCT_URL_PATH_EXAMPLE;PRODUCT_URL_PATH_EXAMPLE', 0),
(35, 'slug', 'PRODUCT_ALIAS', 'default', 'ardo1745;samsung-te5685', 0),
(36, 'currency', 'CURRENCY', 'default', 'RUR;UAH', 0),
(37, 'product_box', 'PRODUCT_BOX',  'default', '12;20', 0),
(38, 'min_order_level', 'MIN_ORDER_LEVEL',  'default', '0;1', 0),
(39, 'max_order_level', 'MAX_ORDER_LEVEL',  'default', '10;20', 0),
(40, 'product_special', 'ON_FEATURED', 'default', '1;0', '0'),
(41, 'shoppergroup_id', 'SHOPPERGROUP_ID', 'default', '2|3|4;2', 0),
(42, 'product_available_date', 'PRODUCT_AVAILABLE_DATE', 'default', '2013-12-31 12:00:00;2013-10-24 00:00:00', 0),
(43, 'related_products_sku', 'RELATED_PRODUCTS_SKU', 'default', 'GC0012|584542;55687|Art_256', 0),
(44, 'product_ordered', 'PRODUCT_ORDERED', 'default', '2;5', 0),
(45, 'category_template', 'CATEGORY_TEMPLATE', 'default', 'default;default', 0),
(46, 'category_layout', 'CATEGORY_LAYOUT', 'default', '0;0', 0),
(47, 'category_product_layout', 'CATEGORY_PRODUCT_LAYOUT', 'default', 'default;default', 0),
(48, 'product_tax_id', 'PRODUCT_TAX_ID', 'default', '6;-1', 0),
(49, 'product_discount_id', 'PRODUCT_DISCOUNT_ID', 'default', '7;-1', 0),
(50, 'override', 'OVERRIDE', 'default', '1 (Overwrite final);-1 (Overwrite price to be taxed)', 0),
(51, 'step_order_level', 'STEP_ORDER_LEVEL', 'default', '1;5', 0),
(52, 'product_mpn', 'MPN', 'default', '45487;NPft5', 0),
(53, 'product_gtin', 'GTIN', 'default', '4600702077186;4823012508359', 0),
(54, 'created_on', 'CREATED_ON', 'default', '2014-12-25 13:22:15;2015-01-05 12:55:36', 0),
(55, 'modified_on', 'MODIFIED_ON', 'default', '2014-12-25 13:22:15;2015-01-05 12:55:36', 0),
(56, 'delete', 'Удаление', 'delete', '1;0', 0),
(57, 'ordering', 'Порядок', 'default', '1;2', 0),
(58, 'img2', 'Изображение 2', 'default', 'refregerator3.jpg;refregerator4.jpg', 0),
(59, 'img3', 'Изображение 3', 'default', 'refregerator5.jpg;refregerator6.jpg', 0),
(60, 'img4', 'Изображение 4', 'default', 'refregerator7.jpg;refregerator8.jpg', 0),
(61, 'img5', 'Изображение 5', 'default', 'refregerator9.jpg;refregerator10.jpg', 0),
(62, 'img6', 'Изображение 6', 'default', 'refregerator11.jpg;refregerator12.jpg', 0),
(63, 'img7', 'Изображение 7', 'default', 'refregerator13.jpg;refregerator14.jpg', 0),
(64, 'img8', 'Изображение 8', 'default', 'refregerator15.jpg;refregerator16.jpg', 0),
(65, 'img9', 'Изображение 9', 'default', 'refregerator17.jpg;refregerator18.jpg', 0),
(66, 'img10', 'Изображение 10', 'default', 'refregerator19.jpg;refregerator20.jpg', 0);



CREATE TABLE IF NOT EXISTS `#__excel2vm_log` (
  `log_id` int(20) NOT NULL AUTO_INCREMENT,
  `vm_id` int(11) NOT NULL,
  `type` enum('cu','cn','pu','pn') NOT NULL,
  `title` varchar(256) NOT NULL,
  `row` int(10) NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__excel2vm_related_products` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `sku` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__excel2vm_multy` (
  `mv_id` int(20) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL,
  `custom_field_id` int(10) NOT NULL,
  `child_id` int(10) NOT NULL,
  `type` varchar(256) NOT NULL,
  `clabel` varchar(256) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`mv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__excel2vm_yml` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `yml_export_path` text NOT NULL,
  `yml_import_path` text NOT NULL,
  `params` text NOT NULL,
  `export_params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


