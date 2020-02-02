CREATE TABLE IF NOT EXISTS  `#__yandex_maps_datetimes` (
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  'ID',
`object_id` INT UNSIGNED NOT NULL COMMENT  'Объект',
`date_value` DATE NOT NULL COMMENT  'Дата',
`time_value` TIME NOT NULL COMMENT  'Время',
INDEX (`object_id` ,  `date_value` ,  `time_value` )
) COMMENT =  'Таблица показа объекта на карте. Используется в плагине plg_system_yandex_maps_arendator';