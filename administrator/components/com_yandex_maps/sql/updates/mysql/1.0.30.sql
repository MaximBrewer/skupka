ALTER TABLE  `#__yandex_maps_maps` ADD  `params` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  'Настройки';
ALTER TABLE  `#_yandex_maps_maps` CHANGE  `lat`  `lat` DECIMAL( 18, 14 ) NOT NULL COMMENT  'Широта';
ALTER TABLE  `#_yandex_maps_maps` CHANGE  `lan`  `lan` DECIMAL( 18, 14 ) NOT NULL COMMENT  'Долгота';
ALTER TABLE  `#__yandex_maps_categories` ADD  `params` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  'Настройки';
ALTER TABLE  `#__yandex_maps_categories` CHANGE  `lat`  `lat` DECIMAL( 18, 14 ) NOT NULL COMMENT  'Широта';
ALTER TABLE  `#__yandex_maps_categories` CHANGE  `lan`  `lan` DECIMAL( 18, 14 ) NOT NULL COMMENT  'Долгота';
ALTER TABLE  `#__yandex_maps_objects` ADD  `params` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  'Настройки';
ALTER TABLE  `#__yandex_maps_objects` CHANGE  `lat`  `lat` DECIMAL( 18, 14 ) NOT NULL COMMENT  'Широта';
ALTER TABLE  `#__yandex_maps_objects` CHANGE  `lan`  `lan` DECIMAL( 18, 14 ) NOT NULL COMMENT  'Долгота';