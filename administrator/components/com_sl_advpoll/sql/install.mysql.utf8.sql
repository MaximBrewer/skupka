CREATE TABLE IF NOT EXISTS `#__sl_advpoll_polls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catid` int(10) NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `schedule` tinyint(1) NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `access` int(10) NOT NULL DEFAULT '1',
  `language` char(7) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `voters` int (10) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__sl_advpoll_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pollid` int(10) NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT '',
  `type_answer` ENUM('default', 'other') NOT NULL DEFAULT 'default',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(10) NOT NULL DEFAULT '0',
  `votes` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`),
  KEY `idx_pollid` (`pollid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__sl_advpoll_logs` (
  `poll_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(250) NOT NULL DEFAULT '',
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

UPDATE `#__assets` SET `rules` ='{"core.admin":[],"core.manage":[],"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[],"frontend.vote":{"1":1}}' WHERE `name` = 'com_sl_advpoll' AND `title` = 'com_sl_advpoll';