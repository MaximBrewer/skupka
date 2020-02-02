 CREATE TABLE IF NOT EXISTS `#__virtuemart_custom_plg_customsforall_values` (
  `customsforall_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `customsforall_value_name` varchar(255) NOT NULL COMMENT 'is the value of a custom field',
  `customsforall_value_label` varchar(255) NOT NULL COMMENT 'Used to add also labels (Primarily to colors)',
  `virtuemart_custom_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customsforall_value_id`),
  KEY `virtuemart_custom_id` (`virtuemart_custom_id`)
) DEFAULT CHARSET=utf8;



 CREATE TABLE IF NOT EXISTS `#__virtuemart_product_custom_plg_customsforall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customsforall_value_id` int(11) NOT NULL,
  `virtuemart_product_id` int(11) NOT NULL,
  `customfield_id` int(11) NOT NULL DEFAULT '0' COMMENT 'The group/row to which the custom value belongs. Key to the customfield_id of the product_customfields table',
  PRIMARY KEY (`id`),
  KEY `virtuemart_product_id` (`virtuemart_product_id`),
  KEY `customsforall_value_id` (`customsforall_value_id`),
  KEY `value_id_product_id` (`customsforall_value_id`,`virtuemart_product_id`),
  KEY `virtuemart_customfield_id` (`customfield_id`)
) DEFAULT CHARSET=utf8;