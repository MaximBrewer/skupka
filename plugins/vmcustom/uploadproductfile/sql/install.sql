 CREATE TABLE IF NOT EXISTS `#__virtuemart_product_custom_plg_uploadphoto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `virtuemart_product_id` int(11) NOT NULL,
  `uploadphoto`  text,
  PRIMARY KEY (`id`),
  KEY `virtuemart_product_id` (`virtuemart_product_id`)
) DEFAULT CHARSET=utf8;

 CREATE TABLE IF NOT EXISTS `#__virtuemart_custom_plg_photosrc` (
  `uploadphoto_id` int(11) NOT NULL AUTO_INCREMENT,
  `uploadphoto_src` varchar(255) NOT NULL ,
  PRIMARY KEY (`uploadphoto_id`)
) DEFAULT CHARSET=utf8;