CREATE TABLE IF NOT EXISTS `#__cf_customfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL,
  `vm_custom_id` int(11) NOT NULL COMMENT 'is the key to the custom field id ',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `type_id` varchar(12) NOT NULL DEFAULT '3' COMMENT 'The display type',
  `order_by` varchar(64) NOT NULL DEFAULT 'custom_title' COMMENT 'The way that the values will be displayed',
  `order_dir` varchar(12) NOT NULL DEFAULT 'ASC' COMMENT 'the direction',
  `params` text NOT NULL,
   `data_type` varchar(12) NOT NULL DEFAULT 'string', 
  PRIMARY KEY (`id`),
  UNIQUE KEY `virtuemart_custom_id` (`vm_custom_id`),
  KEY `type_id` (`type_id`)
) DEFAULT CHARSET=utf8;


