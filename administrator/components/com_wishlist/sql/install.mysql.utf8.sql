-- 
-- Table structure for table `#__virtuemart_favorites`
--

#DROP TABLE IF EXISTS `#__virtuemart_favorites`;
CREATE TABLE IF NOT EXISTS `#__virtuemart_favorites` (
  `fav_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `product_qty` int(3) NOT NULL DEFAULT '-1',
  `user_id` int(11) NOT NULL,
  `fav_date` date NOT NULL,
  PRIMARY KEY (`fav_id`)
);

-- 
-- Table structure for table `#__virtuemart_favorites_log`
--

#DROP TABLE IF EXISTS `#__virtuemart_favorites_log`;
CREATE TABLE IF NOT EXISTS `#__virtuemart_favorites_log` (
  `dt_stamp` date NOT NULL,
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_type` varchar(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cust_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `product_qty` int(3) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
);

-- 
-- Table structure for table `#__virtuemart_favorites_sh`
--

#DROP TABLE IF EXISTS `#__virtuemart_favorites_sh`;
CREATE TABLE IF NOT EXISTS `#__virtuemart_favorites_sh` (
  `shared_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `share_date` date NOT NULL,
  `share_title` varchar(32) NOT NULL DEFAULT '',
  `share_desc` varchar(100) DEFAULT NULL,
  `isWishList` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shared_id`)
);

-- 
-- SQL Upgrade for `#__virtuemart_favorites_sh`
--
ALTER TABLE `#__virtuemart_favorites` CHANGE `user_id` `user_id` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 
ALTER TABLE `#__virtuemart_favorites` ADD `isGuest` BOOLEAN NOT NULL DEFAULT '0';
ALTER TABLE `#__virtuemart_favorites_sh` ADD `share_pass` VARCHAR( 100 ) NULL DEFAULT NULL;
