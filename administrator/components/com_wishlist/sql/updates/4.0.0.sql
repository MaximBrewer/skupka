-- 
-- SQL Upgrade for `#__virtuemart_favorites` tables
--
ALTER TABLE `#__virtuemart_favorites` CHANGE `user_id` `user_id` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 
ALTER TABLE `#__virtuemart_favorites` ADD `isGuest` BOOLEAN NOT NULL DEFAULT '0';
ALTER TABLE `#__virtuemart_favorites_sh` ADD `share_pass` VARCHAR( 100 ) NULL DEFAULT NULL;
