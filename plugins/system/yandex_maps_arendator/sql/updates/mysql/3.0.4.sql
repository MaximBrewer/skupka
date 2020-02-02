ALTER TABLE  `#__yandex_maps_datetimes` ADD  `book_user` INT UNSIGNED NULL DEFAULT NULL COMMENT  'Забронировавший пользователь',
ADD INDEX (`book_user`);