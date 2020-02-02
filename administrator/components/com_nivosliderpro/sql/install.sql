CREATE TABLE IF NOT EXISTS `#__nivosliderpro_sliders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL default '',
  `published` tinyint(1) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  `visual` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__nivosliderpro_slides` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `catid` int(10) unsigned NOT NULL default '0',
  `sliderid` int(10) unsigned NOT NULL default '0', 
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(10) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

