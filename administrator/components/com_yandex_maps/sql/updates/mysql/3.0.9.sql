INSERT INTO `#__yandex_maps_category_to_map` (`id`, `category_id`, `map_id`) (select null as first, id, map_id from `#__yandex_maps_categories`);
ALTER TABLE `#__yandex_maps_categories` DROP `map_id`;
CREATE TABLE IF NOT EXISTS  `#__yandex_maps_object_to_category` (
 `id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ,
 `object_id` INT( 10 ) UNSIGNED NOT NULL COMMENT  'ID Объекта',
 `category_id` INT( 10 ) UNSIGNED NOT NULL COMMENT  'ID Категории',
PRIMARY KEY (  `id` ) ,
KEY  `object_id` (  `object_id` ,  `category_id` )
);
INSERT INTO `#__yandex_maps_object_to_category` (`id`, `object_id`, `category_id`) (select null as first, id, category_id from `#__yandex_maps_objects`);
ALTER TABLE `#__yandex_maps_objects` DROP `map_id`;
ALTER TABLE `#__yandex_maps_objects` DROP `category_id`;