ALTER TABLE  `#__yandex_maps_datetimes` ADD  `status` TINYINT(1) NOT NULL DEFAULT  '0' COMMENT  'Статус времени',
ADD INDEX (`status`);