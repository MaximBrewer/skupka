DELETE FROM `#__csvi_availabletables` WHERE `component` = 'com_tags';
INSERT IGNORE INTO `#__csvi_availabletables` (`task_name`, `template_table`, `component`, `action`, `enabled`) VALUES
('tags', 'tags', 'com_tags', 'export', '1'),
('tags', 'tags', 'com_tags', 'import', '1');

DELETE FROM `#__csvi_tasks` WHERE `component` = 'com_tags';
INSERT IGNORE INTO `#__csvi_tasks` (`task_name`, `action`, `component`, `url`, `options`) VALUES
('tags', 'export', 'com_tags', 'index.php?option=com_tags', 'source,file,layout,fields,limit.advancedUser'),
('tags', 'import', 'com_tags', 'index.php?option=com_tags', 'source,file,tags,fields,limit.advancedUser');
