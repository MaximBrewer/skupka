CREATE TABLE IF NOT EXISTS `#__vm2sms` (
  `send_sms` int(1) NOT NULL DEFAULT '0',
  `status` char(1) NOT NULL DEFAULT '',
  `text_sms` text NOT NULL,
  `worktime` int(1) not null default '0',
  `manager_send_sms` int(1) NOT NULL DEFAULT '0',
  `manager_text_sms` text NOT NULL,
  `manager_worktime` int(1) not null default '0',
  `include_comment` INT( 1 ) NOT NULL DEFAULT '0',
  PRIMARY KEY (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

