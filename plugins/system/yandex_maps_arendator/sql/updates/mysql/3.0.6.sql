ALTER TABLE  `#__yandex_maps_datetimes` ADD  `price` DECIMAL(10, 2) UNSIGNED NULL DEFAULT NULL COMMENT  'Цена часа' AFTER  `time_value` ,
ADD INDEX (`price`);