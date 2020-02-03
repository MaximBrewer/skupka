CREATE TABLE IF NOT EXISTS `#__slideshowck_styles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `state` int(10) NOT NULL DEFAULT '1',
  `params` longtext NOT NULL,
  `layoutcss` text NOT NULL,
  `checked_out` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

