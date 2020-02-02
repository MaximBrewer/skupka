INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.smsservice', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.usessl', '1');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.clockworkkey', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.twiliosid', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.twiliotoken', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.smsglobaluser', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.smsglobalpassword', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.clickatellusername', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.clickatellpassword', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.clickatellapiid', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.clickatellmo', '0');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.nexmokey', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('smsnotification.nexmosecret', '');

CREATE TABLE IF NOT EXISTS `#__rsform_smsnotification` (
  `form_id` int(11) NOT NULL,
  `admin_sms` varchar(16) NOT NULL DEFAULT 0,
  `admin_from` varchar(255) NOT NULL,
  `admin_to` varchar(100) NOT NULL,
  `user_sms` varchar(16) NOT NULL DEFAULT 0,
  `user_from` varchar(255) NOT NULL,
  `user_to` varchar(100) NOT NULL,
	`admin_text` varchar(255) NOT NULL,
	`user_text` varchar(255) NOT NULL,
  PRIMARY KEY  (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;