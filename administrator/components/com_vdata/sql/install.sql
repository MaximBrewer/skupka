DROP TABLE IF EXISTS `#__vd_config`, `#__vd_profiles`, `#__vd_schedules`, `#__vd_widget`, `#__vd_notifications`, `#__vd_logs`,`#__vd_display`,`#__vd_list_template`,`#__vd_detail_template`;


CREATE TABLE IF NOT EXISTS `#__vd_config` (
  `id` int(11) NOT NULL,
  `limit` int(11) DEFAULT NULL,
  `delimiter` varchar(255) NOT NULL,
  `enclosure` varchar(255) NOT NULL,
  `csv_child` varchar(255) DEFAULT NULL,
  `dbconfig` varchar(255) DEFAULT NULL,
  `column_limit` varchar(100) NOT NULL,
  `row_limit` varchar(100) NOT NULL,
  `logging` tinyint(1) NOT NULL,
  `csv_header` tinyint(1) NOT NULL,
  `xml_parent` varchar(255) NOT NULL,
  `php_settings` varchar(255) NOT NULL,
  `notification` text NOT NULL,
  `sstatus` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__vd_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pluginid` int(11) NOT NULL,
  `iotype` tinyint(1) NOT NULL,
  `title` varchar(255) NOT NULL,
  `quick` tinyint(1) NOT NULL,
  `params` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `customjoin` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL,
  `modified_by` int(10) NOT NULL DEFAULT '0',
  `state` int(10) NOT NULL DEFAULT '0',
  `access` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__vd_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `profileid` int(255) NOT NULL,
  `url` text NOT NULL,
  `qry` text NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `iotype` int(11) NOT NULL,
  `params` text NOT NULL,
  `columns` text NOT NULL,
  `access` int(10) NOT NULL DEFAULT '0',
  `uid` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL,
  `modified_by` int(10) NOT NULL DEFAULT '0',
  `state` int(10) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__vd_widget` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `chart_type` varchar(100) NOT NULL,
  `datatype_option` varchar(20) NOT NULL,
  `data` text,
  `detail` text,
  `create_time` datetime NOT NULL,
  `userid` int(100) NOT NULL,
  `ordering` int(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `#__vd_notifications` (
 `id` int(10) NOT NULL AUTO_INCREMENT,
 `title` varchar(255) NOT NULL,
 `params` mediumtext NOT NULL,
 `notification_tmpl` mediumtext NOT NULL,
 `url` varchar(255) NOT NULL,
 `created` datetime NOT NULL,
 `created_by` int(11) NOT NULL,
 `widget_id` int(11) NOT NULL,
 `access` int(11) NOT NULL,
 `state` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vd_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iotype` tinyint(1) NOT NULL,
  `profileid` int(11) NOT NULL,
  `cronid` int(10) NOT NULL,
  `table` varchar(255) NOT NULL,
  `iocount` int(10) NOT NULL,
  `message` text NOT NULL,
  `iofile` varchar(255) NOT NULL,
  `op_start` datetime NOT NULL,
  `op_end` datetime NOT NULL,
  `side` varchar(255) NOT NULL,
  `user` int(255) NOT NULL,
  `logfile` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__vd_display` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `profileid` int(11) NOT NULL,
  `qry` text NOT NULL,
  `uniquekey` varchar(255) NOT NULL,
  `uniquealias` varchar(255) NOT NULL,
  `uidprofile` varchar(255) NOT NULL,
  `keyword` int(11) NOT NULL,
  `likesearch` text NOT NULL,
  `morefilterkey` text NOT NULL,
  `morefiltertype` text NOT NULL,
  `fieldtype` varchar(255) NOT NULL,
  `access` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `norowitem` int(11) NOT NULL,
  `listtmplid` int(11) NOT NULL,
  `itemlisttmpl` text NOT NULL,
  `detailtmplid` int(11) NOT NULL,
  `itemdetailtmpl` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__vd_list_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `template` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__vd_detail_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` text NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `#__vd_config` (`id`, `limit`, `delimiter`, `enclosure`, `csv_child`, `dbconfig`, `column_limit`, `row_limit`, `logging`, `csv_header`, `xml_parent`, `php_settings`, `notification`, `sstatus`) VALUES
(1, 100, '{"value":"comma"}', '{"value":"dquote"}', '{"csv_child":"1","child_sep":"pipe"}', '{"local_db":1}', '10', '150', 1, 1, '{"root":"ROOT\\/ITEMS","data":"ITEM","child":"CHID_ITEM","node":"column","name":"column","attribute":"id"}', '{"max_execution":"0","max_memory":"250","max_post":"20","max_upload":"5"}', '{"status":"0","recipients":"","interval":"0","tmpl":"<p>Total Import - {TOTAL_IMPORT}<\\/p>\\r\\n<p>Total Export - {TOTAL_EXPORT}<\\/p>\\r\\n<p>Profile Statistics -<\\/p>\\r\\n<p>{PROFILE_STATS}<\\/p>\\r\\n<p>Feed hits -<\\/p>\\r\\n<p>{FEED_HITS}<\\/p>\\r\\n<p>Cron Statistics -<\\/p>\\r\\n<p>{CRON_STATS}<\\/p>"}', 0);

INSERT INTO `#__vd_profiles` (`id`, `pluginid`, `iotype`, `title`, `quick`, `params`, `ordering`, `customjoin`, `created`, `created_by`, `modified`, `modified_by`, `state`, `access`) VALUES
(1, 0, 1, 'Export Joomla Articles', 0, '{"table":"#__content","unqkey":["id"],"filters":{"op":"and"},"groupby":"id","orderby":"id","orderdir":"asc","fields":{"id":{"data":"include"},"asset_id":{"data":"skip"},"title":{"data":"include"},"alias":{"data":"include"},"introtext":{"data":"include"},"fulltext":{"data":"include"},"state":{"data":"include"},"catid":{"data":"reference","table":"#__categories","on":"id","reftext":["extension","title"]},"created":{"data":"include"},"created_by":{"data":"reference","table":"#__users","on":"id","reftext":["email"]},"created_by_alias":{"data":"include"},"modified":{"data":"skip"},"modified_by":{"data":"skip"},"checked_out":{"data":"skip"},"checked_out_time":{"data":"skip"},"publish_up":{"data":"include"},"publish_down":{"data":"include"},"images":{"data":"include"},"urls":{"data":"include"},"attribs":{"data":"include"},"version":{"data":"include"},"ordering":{"data":"include"},"metakey":{"data":"include"},"metadesc":{"data":"include"},"access":{"data":"include"},"hits":{"data":"include"},"metadata":{"data":"include"},"featured":{"data":"include"},"language":{"data":"include"},"xreference":{"data":"include"}}}', 0, '', '2015-12-16 15:13:00', 0, '0000-00-00 00:00:00', 0, 0, 1),
(2, 0, 0, 'Import Joomla Articles', 0, '{"table":"#__content","component":{"value":"com_content","table":"content"},"unqkey":["id"],"operation":"1","fields":{"id":{"data":"skip"},"asset_id":{"data":"asset_reference","table":"com_content","on":"content"},"title":{"data":"file","format":"string","type":""},"alias":{"data":"file","format":"string","type":""},"introtext":{"data":"file","format":"string","type":""},"fulltext":{"data":"file","format":"string","type":""},"state":{"data":"file","format":"number"},"catid":{"data":"reference","table":"#__categories","on":"id","reftext":["extension","title"]},"created":{"data":"file","format":"string","type":""},"created_by":{"data":"reference","table":"#__users","on":"id","reftext":["email"]},"created_by_alias":{"data":"file","format":"string","type":""},"modified":{"data":"skip"},"modified_by":{"data":"skip"},"checked_out":{"data":"skip"},"checked_out_time":{"data":"skip"},"publish_up":{"data":"file","format":"string","type":""},"publish_down":{"data":"file","format":"string","type":""},"images":{"data":"file","format":"string","type":""},"urls":{"data":"file","format":"string","type":""},"attribs":{"data":"file","format":"string","type":""},"version":{"data":"file","format":"string","type":""},"ordering":{"data":"file","format":"string","type":""},"metakey":{"data":"file","format":"string","type":""},"metadesc":{"data":"file","format":"string","type":""},"access":{"data":"file","format":"string","type":""},"hits":{"data":"file","format":"string","type":""},"metadata":{"data":"file","format":"string","type":""},"featured":{"data":"file","format":"string","type":""},"language":{"data":"file","format":"string","type":""},"xreference":{"data":"file","format":"string","type":""}}}', 0, '', '2015-12-25 07:39:44', 0, '0000-00-00 00:00:00', 0, 0, 1),
(3, 0, 1, 'Export Joomla Users', 0, '{"table":"#__users","unqkey":["id"],"filters":{"op":"and"},"groupby":"id","orderby":"id","orderdir":"asc","fields":{"id":{"data":"include"},"name":{"data":"include"},"username":{"data":"include"},"email":{"data":"include"},"password":{"data":"include"},"block":{"data":"include"},"sendEmail":{"data":"include"},"registerDate":{"data":"include"},"lastvisitDate":{"data":"include"},"activation":{"data":"include"},"params":{"data":"include"},"lastResetTime":{"data":"include"},"resetCount":{"data":"include"},"otpKey":{"data":"skip"},"otep":{"data":"skip"},"requireReset":{"data":"skip"}},"joins":{"table1":["#__users"],"join":["left_join"],"table2":["#__user_usergroup_map"],"column1":["id"],"column2":["user_id"],"columns":[{"user_id":{"data":"skip"},"group_id":{"data":"reference","table":"#__usergroups","on":"id","reftext":["title"]}}]}}', 0, '', '2015-12-16 14:47:11', 0, '0000-00-00 00:00:00', 0, 0, 1),
(4, 0, 0, 'Import Joomla users', 0, '{"table":"#__users","component":{"value":"","table":""},"unqkey":["id"],"operation":"1","fields":{"id":{"data":"skip"},"name":{"data":"file","format":"string","type":""},"username":{"data":"file","format":"string","type":""},"email":{"data":"file","format":"string","type":""},"password":{"data":"file","format":"string","type":""},"block":{"data":"file","format":"string","type":""},"sendEmail":{"data":"file","format":"string","type":""},"registerDate":{"data":"file","format":"string","type":""},"lastvisitDate":{"data":"file","format":"string","type":""},"activation":{"data":"file","format":"number"},"params":{"data":"file","format":"string","type":""},"lastResetTime":{"data":"file","format":"string","type":""},"resetCount":{"data":"file","format":"number"},"otpKey":{"data":"file","format":"string","type":""},"otep":{"data":"file","format":"string","type":""},"requireReset":{"data":"file","format":"string","type":""}},"joins":{"table1":["#__users"],"join":["left_join"],"table2":["#__user_usergroup_map"],"column1":["id"],"column2":["user_id"],"component":{"value":[""],"table":[""]},"columns":[{"user_id":{"data":"skip"},"group_id":{"data":"reference","table":"#__usergroups","on":"id","reftext":["title"]}}]}}', 0, '', '2015-11-11 10:37:39', 0, '0000-00-00 00:00:00', 0, 0, 1),
(5, 0, 1, 'Export Joomla Menus', 0, '{"table":"#__menu","unqkey":["id"],"filters":{"op":"and","column":["id","client_id","home"],"cond":["<>","=","="],"value":["1","0","0"]},"groupby":"id","orderby":"lft","orderdir":"asc","fields":{"id":{"data":"skip"},"menutype":{"data":"include"},"title":{"data":"include"},"alias":{"data":"include"},"note":{"data":"include"},"path":{"data":"include"},"link":{"data":"include"},"type":{"data":"include"},"published":{"data":"include"},"parent_id":{"data":"reference","table":"#__menu","on":"id","reftext":["menutype","title"]},"level":{"data":"include"},"component_id":{"data":"reference","table":"#__extensions","on":"extension_id","reftext":["type","element"]},"checked_out":{"data":"skip"},"checked_out_time":{"data":"skip"},"browserNav":{"data":"include"},"access":{"data":"include"},"img":{"data":"include"},"template_style_id":{"data":"include"},"params":{"data":"include"},"lft":{"data":"include"},"rgt":{"data":"include"},"home":{"data":"include"},"language":{"data":"include"},"client_id":{"data":"include"}}}', 0, '', '2015-12-25 07:48:49', 0, '0000-00-00 00:00:00', 0, 0, 1),
(6, 0, 0, 'Import Joomla Menus', 0, '{"table":"#__menu","component":{"value":"com_menus","table":"menu"},"unqkey":["id"],"operation":"1","fields":{"id":{"data":"skip"},"menutype":{"data":"file","format":"string","type":""},"title":{"data":"file","format":"string","type":""},"alias":{"data":"file","format":"string","type":""},"note":{"data":"file","format":"string","type":""},"path":{"data":"file","format":"string","type":""},"link":{"data":"file","format":"string","type":""},"type":{"data":"file","format":"string","type":""},"published":{"data":"file","format":"number"},"parent_id":{"data":"reference","table":"#__menu","on":"id","reftext":["menutype","title"]},"level":{"data":"file","format":"number"},"component_id":{"data":"reference","table":"#__extensions","on":"extension_id","reftext":["type","element"]},"checked_out":{"data":"skip"},"checked_out_time":{"data":"skip"},"browserNav":{"data":"file","format":"number"},"access":{"data":"file","format":"number"},"img":{"data":"file","format":"string","type":""},"template_style_id":{"data":"file","format":"number"},"params":{"data":"file","format":"string","type":""},"lft":{"data":"file","format":"number"},"rgt":{"data":"file","format":"number"},"home":{"data":"file","format":"number"},"language":{"data":"file","format":"string","type":""},"client_id":{"data":"file","format":"number"}}}', 0, '', '2015-12-25 07:48:49', 0, '0000-00-00 00:00:00', 0, 0, 1),
(7, 0, 1, 'Export Joomla Categories', 0, '{"table":"#__categories","unqkey":["id"],"filters":{"op":"and"},"groupby":"id","orderby":"id","orderdir":"asc","fields":{"id":{"data":"include"},"asset_id":{"data":"skip"},"parent_id":{"data":"reference","table":"#__categories","on":"id","reftext":["extension","title"]},"lft":{"data":"include"},"rgt":{"data":"include"},"level":{"data":"include"},"path":{"data":"include"},"extension":{"data":"include"},"title":{"data":"include"},"alias":{"data":"include"},"note":{"data":"include"},"description":{"data":"include"},"published":{"data":"include"},"checked_out":{"data":"skip"},"checked_out_time":{"data":"skip"},"access":{"data":"include"},"params":{"data":"include"},"metadesc":{"data":"include"},"metakey":{"data":"include"},"metadata":{"data":"include"},"created_user_id":{"data":"reference","table":"#__users","on":"id","reftext":["email"]},"created_time":{"data":"include"},"modified_user_id":{"data":"skip"},"modified_time":{"data":"include"},"hits":{"data":"include"},"language":{"data":"include"},"version":{"data":"skip"}}}', 0, '', '2015-12-25 07:48:49', 0, '0000-00-00 00:00:00', 0, 0, 1),
(8, 0, 0, 'Import Joomla Categories', 0, '{"table":"#__categories","component":{"value":"com_categories","table":"category"},"unqkey":["id"],"operation":"1","fields":{"id":{"data":"skip"},"asset_id":{"data":"skip"},"parent_id":{"data":"reference","table":"#__categories","on":"id","reftext":["extension","title"]},"lft":{"data":"file","format":"number"},"rgt":{"data":"file","format":"number"},"level":{"data":"file","format":"number"},"path":{"data":"file","format":"string","type":""},"extension":{"data":"file","format":"string","type":""},"title":{"data":"file","format":"string","type":""},"alias":{"data":"file","format":"string","type":""},"note":{"data":"file","format":"string","type":""},"description":{"data":"file","format":"string","type":""},"published":{"data":"file","format":"number"},"checked_out":{"data":"skip"},"checked_out_time":{"data":"skip"},"access":{"data":"file","format":"number"},"params":{"data":"file","format":"string","type":""},"metadesc":{"data":"file","format":"string","type":""},"metakey":{"data":"file","format":"string","type":""},"metadata":{"data":"file","format":"string","type":""},"created_user_id":{"data":"reference","table":"#__users","on":"id","reftext":["email"]},"created_time":{"data":"file","format":"string","type":""},"modified_user_id":{"data":"skip"},"modified_time":{"data":"file","format":"string","type":""},"hits":{"data":"file","format":"number"},"language":{"data":"file","format":"string","type":""},"version":{"data":"skip"}}}', 0, '', '2015-12-25 07:48:49', 0, '0000-00-00 00:00:00', 0, 0, 1),
(9, 0, 1, 'data display profile', 0, '{"table":"#__content","unqkey":["id"],"filters":{"op":"and"},"groupby":"id","orderby":"id","orderdir":"asc","fields":{"id":{"data":"include"},"asset_id":{"data":"skip"},"title":{"data":"include"},"alias":{"data":"include"},"introtext":{"data":"include"},"fulltext":{"data":"include"},"state":{"data":"include"},"catid":{"data":"reference","table":"#__categories","on":"id","reftext":["extension","title"]},"created":{"data":"defined","default":"@vdPhp:date(\\"Y-m-d\\",strtotime(\\"@vdLocal:created\\"))"},"created_by":{"data":"reference","table":"#__users","on":"id","reftext":["name","email"]},"created_by_alias":{"data":"include"},"modified":{"data":"skip"},"modified_by":{"data":"skip"},"checked_out":{"data":"skip"},"checked_out_time":{"data":"skip"},"publish_up":{"data":"include"},"publish_down":{"data":"include"},"images":{"data":"include"},"urls":{"data":"include"},"attribs":{"data":"include"},"version":{"data":"include"},"ordering":{"data":"include"},"metakey":{"data":"include"},"metadesc":{"data":"include"},"access":{"data":"include"},"hits":{"data":"include"},"metadata":{"data":"include"},"featured":{"data":"include"},"language":{"data":"include"},"xreference":{"data":"include"}}}', 0, '', '2015-12-16 15:13:00', 0, '2017-03-20 06:29:09', 0, 0, 1);


INSERT INTO `#__vd_widget` (`id`, `name`, `chart_type`, `datatype_option`, `data`, `detail`, `create_time`, `userid`, `ordering`) VALUES
(15, 'Daily Registered Users', 'Line Chart', 'predefined', NULL, '{"descriptin_widget":"","existing_database_table":"Registered users ","remote_query_value":"select DATE_FORMAT(u.registerDate, ''%Y-%m-%d'')as registerDate, count(u.id) as Numbers from {tablename `#__users`} {as u} GROUP BY DATE_FORMAT(u.registerDate, ''%Y-%m-%d'')","box_column":"5","box_row":"2","style_layout":"charting_formate","style_type_allow":"","extra_condition":"","limit_value":"","series_column_color":"#feb165","legend":"none","x_axis":"Date","y_axis":"Number of Users","chart_churve":"chart_churve","style_layout_editor":""}', '2015-12-22 11:11:36', 918, 13),
(19, 'Quick Access to Top Profiles', 'Line Chart', 'predefined', NULL, '{"descriptin_widget":"Description","existing_database_table":"vData Profiles","remote_query_value":"select i.id, i.title, e.name as plugin, e.element, concat(''plg_'', e.folder, ''_'', e.element) as extension FROM {tablename #__vd_profiles} {as i} left join {tablename #__extensions} {as e} on (e.extension_id=i.pluginid and e.enabled=1)","box_column":"4","box_row":"2","style_layout":"listing_formate","style_type_allow":"","profiles":["2","3","4"],"profile_creation_button":"1","style_layout_editor":""}', '2015-12-22 13:12:33', 918, 6),
(27, 'Server Response Time', 'Area Chart', 'predefined', '', '{"descriptin_widget":"Description","existing_database_table":"Server Response Monitoring","remote_query_value":"Server Response Monitoring","box_column":"5","box_row":"2","style_layout":"charting_formate","style_type_allow":"","data_range_limit":"15","series_column_color":"#cc3333","legend":"none","x_axis":"Time","y_axis":"Server Response Time","isStacked":"isStacked","connectSteps":"1","enableInteractivity":"1","style_layout_editor":""}', '2015-12-22 11:07:13', 918, 10),
(30, 'CPU Utilization', 'Area Chart', 'predefined', NULL, '{"descriptin_widget":"Description","existing_database_table":"Server CPU Monitoring","remote_query_value":"Server CPU Monitoring","box_column":"5","box_row":"2","style_layout":"charting_formate","style_type_allow":"","data_range_limit":"30","series_column_color":"#0099cc","legend":"none","x_axis":"Time","y_axis":"CPU Utilization","isStacked":"isStacked","connectSteps":"1","enableInteractivity":"1","style_layout_editor":""}', '2015-12-22 11:06:39', 918, 9),
(39, '', '', 'predefined', NULL, '{"descriptin_widget":"","existing_database_table":"Total Imported Records","remote_query_value":"SELECT sum(convert( `message` , unsigned )) as total_imported FROM `#__vd_logs` WHERE `message` LIKE ''%imported%''","box_column":"2","box_row":"1","style_layout":"single_formate","style_type_allow":"","limit_value":"","style_layout_editor":"<div class=\\"t_import\\">\\r\\n<p><span class=\\"t_import_pic vdata_img\\">Image<\\/span><span class=\\"hex_value\\">{total_imported}<\\/span><\\/p>\\r\\n<h3>Total Records Imported<\\/h3>\\r\\n<\\/div>"}', '2015-12-19 13:29:11', 918, 4),
(40, '', '', 'predefined', NULL, '{"descriptin_widget":"","existing_database_table":"Total Exported Records","remote_query_value":"SELECT sum(convert( `message` , unsigned )) as total_exported FROM `#__vd_logs` WHERE `message` LIKE ''%exported%''","box_column":"2","box_row":"1","style_layout":"single_formate","style_type_allow":"","limit_value":"","style_layout_editor":"<div class=\\"t_export\\">\\r\\n<p><span class=\\"t_pic vdata_img\\">Image<\\/span><span class=\\"hex_value\\">{total_exported}<\\/span><\\/p>\\r\\n<h3>Total Records Exported<\\/h3>\\r\\n<\\/div>"}', '2015-12-11 14:17:51', 918, 3),
(41, '', '', 'predefined', NULL, '{"descriptin_widget":"Description","existing_database_table":"Total Feed Hits","remote_query_value":"SELECT sum(hits) as hits FROM {tablename `#__vd_schedules`} {as s}","box_column":"2","box_row":"1","style_layout":"single_formate","style_type_allow":"s","extra_condition":"","limit_value":"","style_layout_editor":"<div class=\\"t_feed\\">\\r\\n<p><span class=\\"t_feed_pic vdata_img\\">Image<\\/span><span class=\\"hex_value\\">{hits}<\\/span><\\/p>\\r\\n<h3>Total Feed Hits<\\/h3>\\r\\n<\\/div>"}', '2015-12-17 14:51:59', 918, 5),
(47, '', '', 'predefined', NULL, '{"descriptin_widget":"","existing_database_table":"Database Size","remote_query_value":"SELECT ROUND(SUM(data_length + index_length) \\/ 1024 \\/ 1024, 2) AS `Size` FROM informationschema.TABLES  where table_schema=''{databasename}'' GROUP BY table_schema","box_column":"2","box_row":"1","style_layout":"single_formate","style_type_allow":"","style_layout_editor":"<div class=\\"d_size\\">\\r\\n<p><span class=\\"d_pic vdata_img\\">Image<\\/span> <span class=\\"hex_value\\">{Size} <\\/span><span class=\\"mb\\">MB<br \\/><\\/span><\\/p>\\r\\n<h3>Database Size<\\/h3>\\r\\n<\\/div>"}', '2015-12-11 14:24:12', 918, 1),
(75, 'Most Viewed Articles', 'Pie Chart', 'writequery', NULL, '{"user_write_query_value":"select title , hits from #__content order by hits desc limit 10","existing_database_table":"","box_column":"6","box_row":"2","style_layout":"charting_formate","series_column_color":"#8bb13f,#658bb1,#ff9933,#0099cc,#cc3333,#00adef,#015786,#eb4c71","legend":"right","pie_3d":"1","piehole":"0.1","style_layout_editor":""}', '2015-12-19 14:09:40', 918, 7),
(90, 'Server Threads', 'Column Chart', 'predefined', NULL, '{"descriptin_widget":"Description","existing_database_table":"Thread Status","remote_query_value":"Thread Status","box_column":"5","box_row":"2","style_layout":"charting_formate","style_type_allow":"","data_range_limit":"10","series_column_color":"#3fc0f2","legend":"bottom","x_axis":"Time","y_axis":"Number of Threads","style_layout_editor":""}', '2015-12-22 11:08:41', 918, 12),
(94, 'vData Log Timeline', '', 'writequery', NULL, '{"user_write_query_value":"","existing_database_table":"#__vd_logs","box_column":"5","box_row":"4","style_layout":"listing_formate","extra_condition":"","limit_value":"","ordering_reference_column_name":"id","ordering":"desc","style_layout_editor":"","listing_column_name":["message","op_start","status"]}', '2015-12-22 07:58:20', 918, 11),
(97, 'Data Statistics', 'Area Chart', 'writequery', NULL, '{"user_write_query_value":"select DATE(`c`.`created`) as `created data`, `c`.`asset_id` as `Records Imported` , `c`.`hits` as `Records Exported`, `ca`.`asset_id` as `Feed Hits` from `#__content` as `c` left join `#__categories` as `ca` on `ca`.`id`=`c`.`catid` group by `c`.`created`","existing_database_table":"","box_column":"10","box_row":"2","style_layout":"charting_formate","series_column_color":"#699b06,#336699,#db7f3d","legend":"top","x_axis":"Date","y_axis":"Hits","isStacked":"isStacked","connectSteps":"1","enableInteractivity":"1","style_layout_editor":""}', '2015-12-22 15:09:23', 918, 8),
(100, '', '', 'predefined', NULL, '{"descriptin_widget":"Description","existing_database_table":"Total Users","remote_query_value":"SELECT count( id ) AS register_users FROM {tablename `#__users`} {as l}","box_column":"2","box_row":"1","style_layout":"single_formate","style_type_allow":"s","extra_condition":"","limit_value":"","style_layout_editor":"<div class=\\"t_user\\">\\r\\n<p><span class=\\"t_user_pic vdata_img\\">Image<\\/span><span class=\\"hex_value\\">{register_users}<\\/span><\\/p>\\r\\n<h3>Total Register Users<\\/h3>\\r\\n<\\/div>"}', '2015-12-17 15:25:30', 918, 2),
(107, '', 'Column Chart', 'predefined', NULL, '{"descriptin_widget":"Description","existing_database_table":"vData Statics","remote_query_value":"select dates, sum(total_imported) as `Total Imported`, sum(total_exported) as `Total Exported`, sum(feeds) as `Feed Hits` from ( select date(op_start) as dates, sum(convert( message , unsigned )) as total_imported, 0 as total_exported, 0 as feeds from #__vd_logs where side <> \\"cron\\" and message like \\"%import%\\" GROUP BY date(op_start)\\r\\nUNION ALL\\r\\nselect date(op_start) as dates, 0 as total_imported, sum(convert( message , unsigned )) as total_exported, 0 as feeds from #__vd_logs where side <> \\"cron\\" and message like \\"%export%\\" GROUP BY date(op_start)\\r\\nUNION ALL\\r\\nselect date(op_start) as dates, 0 as total_imported, 0 as total_exported, count(id) as feeds from #__vd_logs where iotype = 2 GROUP BY date(op_start) ) as a group by dates","box_column":"10","box_row":"3","style_layout":"charting_formate","style_type_allow":"c,l","limit_value":"4","series_column_color":"","legend":"top","x_axis":"","y_axis":"","style_layout_editor":""}', '2016-01-07 11:46:21', 429, 14); 


INSERT INTO `#__vd_schedules` (`id`, `title`, `profileid`, `url`, `qry`, `type`, `iotype`, `params`, `columns`, `access`, `uid`, `created`, `created_by`, `modified`, `modified_by`, `state`, `hits`) VALUES
(1, 'Export Joomla Users', 3, '', '',1, 1, '{"source":"json","server":"local","path":"users_backup.json","mode":"w"}', '{"fields":{"id":"id","name":"name","username":"username","email":"email","password":"password","block":"block","sendEmail":"sendEmail","registerDate":"registerDate","lastvisitDate":"lastvisitDate","activation":"activation","params":"params","lastResetTime":"lastResetTime","resetCount":"resetCount","#__user_usergroup_map":{"group_id":{"title":"title"}}}}', 6, 'nPAEtVq46q', '2016-01-04 14:54:04', 918, '2016-01-05 07:32:49', 918, 1, 1),
(2, 'Import Joomla Users', 4, '', '', 1, 0, '{"source":"xml","server":"local","path":"components\\/com_vdata\\/uploads\\/users_backup.xml","ftp":{"ftp_host":"","ftp_port":"","ftp_user":"","ftp_pass":"","ftp_directory":"","ftp_file":""}}', '{"fields":{"name":["ROWSET||ROW||name-1"],"username":["ROWSET||ROW||username-2"],"email":["ROWSET||ROW||email-3"],"password":["ROWSET||ROW||password-4"],"block":["ROWSET||ROW||block-5"],"sendEmail":["ROWSET||ROW||sendEmail-6"],"registerDate":["ROWSET||ROW||registerDate-7"],"lastvisitDate":["ROWSET||ROW||registerDate-7"],"activation":["ROWSET||ROW||activation-9"],"params":["ROWSET||ROW||params-10"],"lastResetTime":["ROWSET||ROW||lastResetTime-11"],"resetCount":["ROWSET||ROW||resetCount-12"],"otpKey":["ROWSET||ROW||otpKey-13"],"otep":["ROWSET||ROW||otep-14"],"requireReset":["ROWSET||ROW||requireReset-15"],"#__user_usergroup_map":{"group_id":{"title":["ROWSET||ROW||user_usergroup_map||CHILD||group_map_title-1"]}}},"base":["ROWSET||ROW","load_child"],"child_tags":{"#__user_usergroup_map":["ROWSET||ROW||user_usergroup_map-16","ROWSET||ROW||user_usergroup_map||CHILD"]}}', 6, 'Pn7OFmmqZW', '2016-01-04 15:02:33', 918, '2017-01-18 14:28:05', 869, 1, 10),
(3, 'Article Feeds', 1, '', '',2, 1, '{"source":"xml","server":"local","path":"","mode":"w"}', '{"fields":{"id":"id","title":"title","alias":"alias","introtext":"introtext","fulltext":"fulltext","state":"state","catid":{"extension":"#__categories_extension","title":"#__categories_title"},"created":"created","created_by":{"email":"#__users_email"},"created_by_alias":"created_by_alias","publish_up":"publish_up","publish_down":"publish_down","images":"images","urls":"urls","attribs":"attribs","version":"version","ordering":"ordering","metakey":"metakey","metadesc":"metadesc","access":"access","hits":"hits","metadata":"metadata","featured":"featured","language":"language","xreference":"xreference"}}', 1, 'eHSWSNONww', '2016-01-06 06:36:29', 918, '2016-01-06 07:00:36', 918, 1, 10);

INSERT INTO `#__vd_notifications` (`id`, `title`, `params`, `notification_tmpl`, `url`, `created`, `created_by`, `widget_id`, `access`, `state`) VALUES
('1', 'daily registered users', '{"query":"","custom":{"table":"#__users","columns":["id","name","email"],"clause":"and","orderby":"id","orderdir":"asc"},"filters":{"additional":"date(registerDate)=CURDATE()"}}', '{"subject":"Daily registered users","recipient":{"group":"8","sendmail":"0","custom":"","column":"0"},"tmpl":"<p>User registration report:<\/p>\r\n<p>\u00a0<\/p>"}', '', '2016-09-23 13:07:28', '978', '0', '1', '1'),
('2', 'daily active users', '{"query":"select id,name,email from #__users where date(lastvisitDate)=CURDATE()","custom":{"table":"","clause":"and","orderby":"","orderdir":"asc"},"filters":{"additional":""}}', '{"subject":"Today''s logged in users","recipient":{"group":"8","sendmail":"0","custom":"","column":"0"},"tmpl":"<p>Logged in user statics :<\/p>\r\n<p>\u00a0<\/p>"}', '', '2016-09-23 13:35:08', '978', '0', '1', '1');

INSERT INTO `#__vd_display` (`id`, `title`, `profileid`, `qry`, `uniquekey`, `uniquealias`, `uidprofile`, `keyword`, `likesearch`, `morefilterkey`, `morefiltertype`, `fieldtype`, `access`, `state`, `norowitem`, `listtmplid`, `itemlisttmpl`, `detailtmplid`, `itemdetailtmpl`, `created`, `modified`) VALUES
(1, 'Blog data display', 9, '', 'id', 'alias', 'blog-data-directory', 1, 'title,alias', '["title","featured","#__categories.title"]', '', '{"type":{"title":["typetext"],"featured":["typeradio"],"#__categories.title":["typetext"]},"value":{"title":["phrase"],"featured":["1|Yes","0|No"],"#__categories.title":["substr"]}}', 1, 1, 3, 2, '<div class="item_full items">
<div class="item_img_left"><a href="@vdata"><img src="images/{images}" /></a></div>
<div class="item_title_intro">
<div class="item_title"><a href="@vdata">{title}</a></div>
<div class="item_intro">{introtext}<a class="btn" href="@vdata">Read More</a></div>
</div>
<div class="item_info"><span class="item_autor"><label>Autor:</label> {#__users.name}</span><span class="item_cat"><label>Category:</label> {#__categories.title}</span><span class="item_dt"><label>Date Added:</label> {created}</span><span class="item_hits"><label>Hits:</label> {hits}</span></div>
</div>', 2, '<div class="item_detail">
<div class="item_title">{title}</div>
<div class="item_img_full"><img src="images/{images}" /><span class="item_date">{created}</span></div>
<div class="item_tabs">{tab Description} {introtext}{fulltext} {tab Contact Info} <span class="item_autor"><label>Autor:</label> {#__users.name}</span><span class="item_cat"><label>Category:</label> {#__categories.title}</span><span class="item_hits"><label>Hits:</label> {hits}</span> <span class="item_email"><label>Email:</label> {#__users.email}</span>{/tabs}</div>
<div class="item_tabs"> </div>
</div>', '2017-01-18 11:57:10', '2017-01-11 06:12:08'),
(2, 'Business directory data', 9, '', 'id', 'alias', 'A4knkqQh9m', 1, 'title,#__categories.title,#__users.email', '["title","state"]', '', '{"type":{"title":["typetext"],"state":["typeradio"]},"value":{"title":["phrase"]}}', 1, 1, 2, 1, '<div class="items">
<div class="item_img"><a href="@vdata"><img src="images/{images}" /></a></div>
<div class="item_title"><a href="@vdata">{title}</a></div>
<div class="item_button"><a class="btn" href="@vdata">Read More</a></div>
</div>', 2, '<p> </p>
<div class="item_detail">
<div class="item_title">{title}</div>
<div class="item_img_full"><img src="images/{images}" /><span class="item_date">{created}</span></div>
<div class="item_tabs">{tab Description} {introtext}{fulltext} {tab Contact Info} <span class="item_autor"><label>Autor:</label> {#__users.name}</span><span class="item_cat"><label>Category:</label> {#__categories.title}</span><span class="item_hits"><label>Hits:</label> {hits}</span> <span class="item_email"><label>Email:</label> {#__users.email}</span>{/tabs}</div>
</div>', '2017-01-18 12:08:36', '2017-01-13 14:26:21');

INSERT INTO `#__vd_list_template` (`id`, `title`, `template`) VALUES
(1, 'Grid Layout', '<div class="items">\r\n<div class="item_img"><a href="@vdata"><img src="images/{images}" /></a></div>\r\n<div class="item_title"><a href="@vdata">{title}</a></div>\r\n<div class="item_button"><a class="btn" href="@vdata">Read More</a></div>\r\n</div>'),
(2, 'List Layout', '<div class="item_full items">\r\n<div class="item_img_left"><a href="@vdata"><img src="images/{images}" /></a></div>\r\n<div class="item_title_intro">\r\n<div class="item_title"><a href="@vdata">{title}</a></div>\r\n<div class="item_intro">{introtext}<a class="btn" href="@vdata">Read More</a></div>\r\n</div>\r\n<div class="item_info"><span class="item_autor"><label>Autor:</label> {#__users.name}</span><span class="item_cat"><label>Category:</label> {#__categories.title}</span><span class="item_dt"><label>Date Added:</label> {created}</span><span class="item_hits"><label>Hits:</label> {hits}</span></div>\r\n</div>');

INSERT INTO `#__vd_detail_template` (`id`, `template`, `title`) VALUES
(1, '<div class="item_detail_simple">\r\n<div class="item_title">{title}</div>\r\n<div class="contact_info"><span class="item_autor"><label>Autor:</label> {#__users.name} </span><span class="item_cat"><label>Category:</label> {#__categories.title}</span><span class="item_hits"><label>Hits:</label> {hits}</span></div>\r\n<div class="item_img_full"><img src="images/{images}" /></div>\r\n<div class="item_descr">{introtext}{fulltext}</div>\r\n</div>', 'Basic Layout'),
(2, ' <div class="item_detail">\r\n<div class="item_title">{title}</div>\r\n<div class="item_img_full"><img src="images/{images}" /><span class="item_date">{created}</span></div>\r\n<div class="item_tabs">{tab Description} {introtext}{fulltext} {tab Contact Info} <span class="item_autor"><label>Autor:</label> {#__users.name}</span><span class="item_cat"><label>Category:</label> {#__categories.title}</span><span class="item_hits"><label>Hits:</label> {hits}</span> <span class="item_email"><label>Email:</label> {#__users.email}</span>{/tabs}</div>\r\n</div>', 'Tab Layout');