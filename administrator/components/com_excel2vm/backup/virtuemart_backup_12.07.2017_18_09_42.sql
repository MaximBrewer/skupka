TRUNCATE TABLE `aj180_virtuemart_categories`;
INSERT INTO `aj180_virtuemart_categories` (`virtuemart_category_id`,`virtuemart_vendor_id`,`category_template`,`category_layout`,`category_product_layout`,`products_per_row`,`limit_list_step`,`limit_list_initial`,`hits`,`metarobot`,`metaauthor`,`ordering`,`shared`,`published`,`created_on`,`created_by`,`modified_on`,`modified_by`,`locked_on`,`locked_by`) VALUES
('22','1','0','0','0','0','0','0','0','','','0','0','1','2015-05-18 16:27:35','440','2015-05-18 16:27:35','440','0000-00-00 00:00:00','0'),
('23','1','0','0','0','0','0','0','0','','','0','0','1','2015-05-18 16:38:11','440','2015-05-18 16:38:11','440','0000-00-00 00:00:00','0'),
('36','1','0','0','0','0','0','0','0','','','0','0','1','2016-03-29 17:03:09','440','2017-05-01 15:06:31','440','0000-00-00 00:00:00','0'),
('37','1','0','0','0','0','0','0','0','','','0','0','1','2016-04-01 21:56:36','440','2016-10-08 12:40:59','440','0000-00-00 00:00:00','0'),
('40','1','0','0','0','0','0','0','0','','','0','0','1','2017-05-23 14:58:01','440','2017-05-23 14:58:01','440','0000-00-00 00:00:00','0');

TRUNCATE TABLE `aj180_virtuemart_categories_ru_ru`;
INSERT INTO `aj180_virtuemart_categories_ru_ru` (`virtuemart_category_id`,`category_name`,`category_description`,`metadesc`,`metakey`,`customtitle`,`slug`) VALUES
('36','Категория 1','','','','','kategoriya-1'),
('37','Подкатегория 1-1','<p>Подкатегория 1-1&nbsp;</p>&#13;&#10;<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">&#13;&#10;<tbody>&#13;&#10;<tr>&#13;&#10;<td>&nbsp;</td>&#13;&#10;</tr>&#13;&#10;<tr>&#13;&#10;<td>&nbsp;</td>&#13;&#10;</tr>&#13;&#10;</tbody>&#13;&#10;</table>','','','','podkategoriya-1-1'),
('40','фвцфвц','<div>&nbsp;</div>','','','','fvtsfvts');

TRUNCATE TABLE `aj180_virtuemart_category_categories`;
INSERT INTO `aj180_virtuemart_category_categories` (`id`,`category_parent_id`,`category_child_id`,`ordering`) VALUES
('22','0','22','0'),
('23','0','23','0'),
('83','0','36','0'),
('84','36','37','0'),
('87','0','40','0');

TRUNCATE TABLE `aj180_virtuemart_products`;
INSERT INTO `aj180_virtuemart_products` (`virtuemart_product_id`,`virtuemart_vendor_id`,`product_parent_id`,`product_sku`,`product_gtin`,`product_mpn`,`product_weight`,`product_weight_uom`,`product_length`,`product_width`,`product_height`,`product_lwh_uom`,`product_url`,`product_in_stock`,`product_ordered`,`low_stock_notification`,`product_available_date`,`product_availability`,`product_special`,`product_sales`,`product_unit`,`product_packaging`,`product_params`,`hits`,`intnotes`,`metarobot`,`metaauthor`,`layout`,`published`,`pordering`,`created_on`,`created_by`,`modified_on`,`modified_by`,`locked_on`,`locked_by`) VALUES
('76','1','0','testovyy-tovar-12','','','','KG','','','','M','','10','-2','0','0000-00-00 00:00:00','','0','0','KG','','min_order_level=\"\"|max_order_level=\"\"|step_order_level=\"\"|product_box=\"\"|','','','','','0','1','0','2016-11-09 08:34:59','440','2017-07-07 07:33:22','440','0000-00-00 00:00:00','0'),
('77','1','0','','','','','KG','','','','M','','15','-1','0','0000-00-00 00:00:00','','0','0','KG','','min_order_level=\"\"|max_order_level=\"\"|step_order_level=\"\"|product_box=\"\"|','','','','','0','1','0','2016-11-09 08:34:59','440','2017-07-06 14:08:14','440','0000-00-00 00:00:00','0');

TRUNCATE TABLE `aj180_virtuemart_products_ru_ru`;
INSERT INTO `aj180_virtuemart_products_ru_ru` (`virtuemart_product_id`,`product_name`,`created_by`,`product_s_desc`,`product_desc`,`metadesc`,`metakey`,`customtitle`,`slug`) VALUES
('76','Тестовый товар-12','440','','','','','','76-testovyy-tovar-12'),
('77','Тестовый товар-57','440','<div>&nbsp;</div>','','','','','77-testovyy-tovar-57');

TRUNCATE TABLE `aj180_virtuemart_product_medias`;
TRUNCATE TABLE `aj180_virtuemart_product_prices`;
INSERT INTO `aj180_virtuemart_product_prices` (`virtuemart_product_price_id`,`virtuemart_product_id`,`virtuemart_shoppergroup_id`,`product_price`,`override`,`product_override_price`,`product_tax_id`,`product_discount_id`,`product_currency`,`product_price_publish_up`,`product_price_publish_down`,`price_quantity_start`,`price_quantity_end`,`created_on`,`created_by`,`modified_on`,`modified_by`,`locked_on`,`locked_by`) VALUES
('66','76','0','130.000000','0','50.00000','0','0','0','0000-00-00 00:00:00','0000-00-00 00:00:00','0','0','2016-11-09 08:34:59','440','2017-07-07 07:33:22','440','0000-00-00 00:00:00','0'),
('67','77','0','1000.000000','0','0.00000','0','0','131','0000-00-00 00:00:00','0000-00-00 00:00:00','0','0','2016-11-09 08:34:59','440','2017-07-06 14:08:14','440','0000-00-00 00:00:00','0');

TRUNCATE TABLE `aj180_virtuemart_customs`;
INSERT INTO `aj180_virtuemart_customs` (`virtuemart_custom_id`,`custom_parent_id`,`virtuemart_vendor_id`,`custom_jplugin_id`,`custom_element`,`admin_only`,`custom_title`,`show_title`,`custom_tip`,`custom_value`,`custom_desc`,`field_type`,`is_list`,`is_hidden`,`is_cart_attribute`,`is_input`,`searchable`,`layout_pos`,`custom_params`,`shared`,`published`,`created_on`,`created_by`,`ordering`,`modified_on`,`modified_by`,`locked_on`,`locked_by`) VALUES
('24','0','1','0','','0','COM_VIRTUEMART_RELATED_PRODUCTS','1','','related_products','','R','0','0','0','0','0','related_products','wPrice=\"1\"|wImage=\"1\"|wDescr=\"1\"|width=\"170\"|height=\"170\"|','0','1','2015-06-09 15:26:50','440','0','2015-06-09 15:31:37','440','0000-00-00 00:00:00','0');

TRUNCATE TABLE `aj180_virtuemart_product_customfields`;
TRUNCATE TABLE `aj180_virtuemart_product_categories`;
INSERT INTO `aj180_virtuemart_product_categories` (`id`,`virtuemart_product_id`,`virtuemart_category_id`,`ordering`) VALUES
('78','76','36','1'),
('80','77','36','2');

TRUNCATE TABLE `aj180_virtuemart_product_manufacturers`;
INSERT INTO `aj180_virtuemart_product_manufacturers` (`id`,`virtuemart_product_id`,`virtuemart_manufacturer_id`) VALUES
('21','77','13'),
('22','76','13');

TRUNCATE TABLE `aj180_virtuemart_manufacturers`;
INSERT INTO `aj180_virtuemart_manufacturers` (`virtuemart_manufacturer_id`,`virtuemart_manufacturercategories_id`,`metarobot`,`metaauthor`,`hits`,`published`,`created_on`,`created_by`,`modified_on`,`modified_by`,`locked_on`,`locked_by`) VALUES
('13','3','','','0','1','2016-03-29 17:03:00','440','2016-12-21 12:46:50','440','0000-00-00 00:00:00','0');

TRUNCATE TABLE `aj180_virtuemart_manufacturers_ru_ru`;
INSERT INTO `aj180_virtuemart_manufacturers_ru_ru` (`virtuemart_manufacturer_id`,`mf_name`,`mf_email`,`mf_desc`,`mf_url`,`metadesc`,`metakey`,`customtitle`,`slug`) VALUES
('13','Производитель 1','','','','','','','proizvoditel-1');

TRUNCATE TABLE `aj180_virtuemart_medias`;
