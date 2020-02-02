
CREATE TABLE IF NOT EXISTS `#__unitegallery_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL default '',
  `ordering` int not NULL, 
  `params` text NOT NULL,
  `type` tinytext,
  `parent_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__unitegallery_items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` tinytext,
  `published` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL,				  
  `alias` varchar(255),  
  `url_image` varchar(255),  
  `url_thumb` varchar(255),  
  `imageid` int(9),
  `contentid` varchar(60),
  `ordering` int not NULL,
  `catid` int(9) NOT NULL,
  `params` text,
  `content` text,
  `parent_id` int(10) unsigned NOT NULL,
PRIMARY KEY  (`id`)  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__unitegallery_galleries` (
    `id` int(10) unsigned NOT NULL auto_increment,
	`type` tinytext NOT NULL,
	`title` tinytext NOT NULL,
	`alias` tinytext,
	`ordering` int not NULL,
	`params` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__unitegallery_options` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `val` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
