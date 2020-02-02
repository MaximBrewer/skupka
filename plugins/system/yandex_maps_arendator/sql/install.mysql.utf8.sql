CREATE TABLE IF NOT EXISTS `#__yandex_maps_datetimes` (
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  'ID',
`object_id` INT UNSIGNED NOT NULL COMMENT  'Объект',
`date_value` DATE NOT NULL COMMENT  'Дата',
`time_value` TIME NOT NULL COMMENT  'Время',
INDEX (`object_id` ,  `date_value` ,  `time_value` )
) COMMENT =  'Таблица показа объекта на карте. Используется в плагине plg_system_yandex_maps_arendator';
ALTER TABLE  `#__yandex_maps_datetimes` ADD  `status` TINYINT(1) NOT NULL DEFAULT  '0' COMMENT  'Статус времени',
ADD INDEX (`status`);
ALTER TABLE  `#__yandex_maps_datetimes` ADD  `book_user` INT UNSIGNED NULL DEFAULT NULL COMMENT  'Забронировавший пользователь',
ADD INDEX (`book_user`);
ALTER TABLE  `#__yandex_maps_datetimes` ADD  `params` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  'Данные брони';
ALTER TABLE  `#__yandex_maps_datetimes` ADD  `price` DECIMAL(10, 2) UNSIGNED NULL DEFAULT NULL COMMENT  'Цена часа' AFTER  `time_value` ,
ADD INDEX (`price`);
