ALTER TABLE  `#__yandex_maps_objects` ADD  `metadata` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  'Meta информация';
ALTER TABLE  `#__yandex_maps_objects` ADD  `publish_up` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' COMMENT  'Дата начала публикации',
ADD INDEX (  `publish_up` );
ALTER TABLE  `#__yandex_maps_objects` ADD  `publish_down` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' COMMENT  'Дата конца публикации',
ADD INDEX (  `publish_down` );