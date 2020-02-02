CREATE TABLE  IF NOT EXISTS `#__yandex_maps_category_to_map` (
`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`category_id` INT UNSIGNED NOT NULL COMMENT  'ID Категории',
`map_id` INT UNSIGNED NOT NULL COMMENT  'ID карты',
INDEX (  `category_id` ,  `map_id` )
);