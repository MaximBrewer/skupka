CREATE TABLE IF NOT EXISTS `#__map_yandex` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name_map_yandex` text NOT NULL,
  `map_type` enum('map','calculator') NOT NULL DEFAULT 'map',
  `defaultmap` enum('map','satellite','hybrid','publicMap','publicMapHybrid') NOT NULL DEFAULT 'map',
  `misil` text NOT NULL,
  `misilonclick` text NOT NULL,
  `id_map_yandex` text,
  `city_map_yandex` text NOT NULL,
  `street_map_yandex` text NOT NULL,
  `full_address_map_yandex` varchar(255) NOT NULL DEFAULT 'Санкт-Петербург, пр. Невский, 100',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `catid` int(11) unsigned NOT NULL,
  `params` text NOT NULL,
  `width_map_yandex` varchar(10) NOT NULL DEFAULT '600',
  `height_map_yandex` varchar(255) NOT NULL DEFAULT '400',
  `oblako_width_map_yandex` int(8) NOT NULL,
  `yandexbutton` int(2) NOT NULL,
  `color_map_yandex` varchar(255) NOT NULL,
  `bradius` int(2) NOT NULL DEFAULT '1',
  `yandexborder` int(2) NOT NULL DEFAULT '1',
  `yandexcoord` int(11) NOT NULL DEFAULT '1',
  `lng` varchar(255) NOT NULL,
  `text_map_yandex` text NOT NULL,
  `where_text` int(11) NOT NULL DEFAULT '0',
  `center_map_yandex` int(11) NOT NULL DEFAULT '1',
  `yandexzoom` int(2) NOT NULL DEFAULT '14',
  `autozoom` int(2) NOT NULL DEFAULT '1',
  `yandexel` varchar(100) NOT NULL,
  `route_map_yandex` text NOT NULL,
  `region_map_yandex` text NOT NULL,
  `map_region_style` varchar(100) NOT NULL,
  `color_map_route` varchar(100) NOT NULL DEFAULT '8f2f62',
  `map_route_opacity` varchar(10) NOT NULL DEFAULT '1',
  `map_baloon_or_placemark` int(2) NOT NULL,
  `map_baloon_minwidth` varchar(5) NOT NULL,
  `map_baloon_minheight` varchar(5) NOT NULL,
  `map_baloon_autopanduration` varchar(2) NOT NULL,
  `map_baloon_autopan` int(2) NOT NULL,
  `map_centering` int(2) NOT NULL,
  `map_settings_user_all` int(1) NOT NULL,
  `map_calculator_settings` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Дамп данных таблицы `#__map_yandex`
--

INSERT INTO `#__map_yandex` (`id`, `name_map_yandex`, `map_type`, `defaultmap`, `misil`, `misilonclick`, `id_map_yandex`, `city_map_yandex`, `street_map_yandex`, `full_address_map_yandex`, `checked_out`, `checked_out_time`, `ordering`, `published`, `hits`, `catid`, `params`, `width_map_yandex`, `height_map_yandex`, `oblako_width_map_yandex`, `yandexbutton`, `color_map_yandex`, `bradius`, `yandexborder`, `yandexcoord`, `lng`, `text_map_yandex`, `where_text`, `center_map_yandex`, `yandexzoom`, `autozoom`, `yandexel`, `route_map_yandex`, `region_map_yandex`, `map_region_style`, `color_map_route`, `map_route_opacity`, `map_baloon_or_placemark`, `map_baloon_minwidth`, `map_baloon_minheight`, `map_baloon_autopanduration`, `map_baloon_autopan`, `map_centering`, `map_settings_user_all`, `map_calculator_settings`) VALUES
(1, 'Первый пример!', 'map', 'map', 'by Aleksander Ermakov', 'Привет я Карта!', '', 'Пермь', 'Ленина, 40', 'Санкт-Петербург, пр. Невский, 100', 0, '2012-11-19 11:10:51', 3, 1, 547, 1, '', 600, 600, 0, 1, '949494', 1, 1, 2, '{"longitude_map_yandex":"56.253024","latitude_map_yandex":"58.003774"}', '<h2>История компании</h2>\r\n<p>Как и предыдущие версии Joomla!, версия 1.5 обеспечивает единую и удобную в работе платформу для управления информацией на веб-сайтах самых разных типов. Чтобы идти в ногу со временем были переработаны базовые функции системы и модернизирован пользовательский интерфейс.</p>\r\n<p>Среди прочего изменения коснулись следующих аспектов Joomla! 1.5:</p>\r\n<ul>\r\n<li>Расширены возможности по настройке и адаптации системы к конкретным требованиям.</li>\r\n<li>Благодаря применению двухбайтовой кодировки символов расширены возможности по локализации Joomla!, теперь возможна поддержка любых языков, включая культуры с правосторонним написанием текстов.</li>\r\n<li>Расширены возможности по интеграции со сторонними приложениями с помощью веб-сервисов. Возможна удалённая авторизация пользователей через LDAP, OpenID или Gmail.</li>\r\n<li>Расширены возможности по созданию тем оформления и настройке внешнего вида отображаемой на сайте информации.</li>\r\n<li>Благодаря новому программному каркасу (framework), значительно увеличены возможности разработчиков Расширений.</li>\r\n<li>Реализована поддержка многих расширений от предыдущих версий системы.</li>\r\n</ul>', 1, 2, 14, 0, '["1","2","3","4","5"]', '["\\u041f\\u0435\\u0440\\u043c\\u044c, \\u041b\\u043e\\u0434\\u044b\\u0433\\u0438\\u043d\\u0430, 49","\\u041f\\u0435\\u0440\\u043c\\u044c, \\u041a\\u0440\\u0430\\u0441\\u043d\\u043e\\u043f\\u043e\\u043b\\u044f\\u043d\\u0441\\u043a\\u0430\\u044f, 25","\\u041f\\u0435\\u0440\\u043c\\u044c, \\u0411\\u0435\\u043b\\u0438\\u043d\\u0441\\u043a\\u043e\\u0433\\u043e, 57"]', '["57.9954,56.2168,58.0012,56.2145,58.0048,56.1982,58.0090,56.2277,58.0031,56.2315,57.9954,56.2168","58.0092,56.2409,58.0127,56.2374,58.0134,56.2458,58.0113,56.2479,58.0092,56.2409","57.9953,56.2538,58.0061,56.2431,58.0107,56.2602,57.9991,56.2728,57.9965,56.2611,57.9953,56.2538","58.0150,56.2495,58.0166,56.2471,58.0191,56.2515,58.0159,56.2547,58.0150,56.2495"]', '["0.8","3d719c"]', 'eb1ed7', '0.8', 0, '100', '100', '50', 1, 0, 0, ''),
(4, 'Санкт-Петербург,Ленина, 100', 'map', 'publicMap', 'Ленина, 100', 'пр. Невский, 100', '', 'Санкт-Петербург', 'Ленина, 100', 'Санкт-Петербург, пр. Невский, 100', 0, '2012-11-08 00:00:00', 4, 1, 63, 1, '', 500, 500, 0, 1, '', 1, 1, 1, '{"longitude_map_yandex":"56.253024","latitude_map_yandex":"58.003774"}', '<p>Ленина, 100</p>', 1, 1, 13, 0, '["1","2","3","4","5"]', '', '', '', '8f2f62', '1', 0, '100', '100', '50', 0, 0, 0, ''),
(3, 'Первый калькулятор!', 'calculator', 'publicMap', '', '', NULL, 'Санкт-Петербург, пр. Невский, 100', '', 'Санкт-Петербург, пр. Невский, 100', 0, '2012-11-14 00:00:00', 0, 0, 118, 0, '', 500, 500, 0, 0, '', 1, 1, 1, '', '', 0, 1, 15, 1, '["1","2","3","4","5"]', '', '', '', '8f2f62', '1', 0, '', '', '', 0, 0, 0, '["30","200","1","1"]'),
(5, 'Второй калькулятор!', 'calculator', 'publicMap', '', '', NULL, 'Санкт-Петербург, пр. Невский, 100', '', 'Санкт-Петербург, пр. Невский, 100', 0, '2012-11-19 00:00:00', 0, 0, 165, 0, '', 500, 500, 0, 0, '', 1, 1, 1, '', '', 0, 1, 13, 1, '["4","5"]', '', '', '', '8f2f62', '1', 0, '', '', '', 0, 0, 0, '["15","100","1","1"]'),
(11, 'Последний тест', 'map', 'publicMap', 'пр. Невский, 1', 'Привет я Карта!', '', 'Санкт-Петербург', 'пр. Невский, 1', 'Санкт-Петербург, пр. Невский, 100', 0, '2012-11-19 11:17:31', 4, 1, 39, 1, '', 500, 500, 0, 1, '', 1, 1, 1, '{"longitude_map_yandex":"56.253024","latitude_map_yandex":"58.003774"}', '<p>пр. Невский, 1</p>', 1, 1, 14, 0, '["1","2","3","4","5"]', '', '', '', '8f2f62', '1', 0, '100', '100', '50', 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `#__map_yandex_metki`
--

CREATE TABLE IF NOT EXISTS `#__map_yandex_metki` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name_marker` text NOT NULL,
  `misil` text NOT NULL,
  `misilonclick` text NOT NULL,
  `city_map_yandex` text NOT NULL,
  `street_map_yandex` text NOT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `catid` int(11) unsigned NOT NULL,
  `params` text NOT NULL,
  `yandexcoord` int(11) NOT NULL DEFAULT '1',
  `lng` varchar(255) NOT NULL,
  `id_map` int(11) NOT NULL,
  `deficon` varchar(255) NOT NULL,
  `whoadd` int(8) NOT NULL,
  `userimg` varchar(255) NOT NULL,
  `wih` char(20) NOT NULL DEFAULT '["250","250"]',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `#__map_yandex_metki`
--

INSERT INTO `#__map_yandex_metki` (`id`, `name_marker`, `misil`, `misilonclick`, `city_map_yandex`, `street_map_yandex`, `checked_out`, `checked_out_time`, `ordering`, `published`, `hits`, `catid`, `params`, `yandexcoord`, `lng`, `id_map`, `deficon`, `whoadd`, `userimg`, `wih`) VALUES
(1, 'Метка', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum', 'Пермь', 'Ленина, 60', 0, '2012-11-19 10:05:00', 4, 1, 0, 1, '', 2, '{"longitude_map_yandex":"56.236486","latitude_map_yandex":"58.00262"}', 1, 'yellowStretchyIcon', 721, '{"smallfile":"50a8b20427f2e_s.png","startfile":"50a8b20427f2e_n.png"}', '["250","250"]'),
(2, 'Проверка', 'пр. Невский, 100', 'пр. Невский, 100', 'Санкт-Петербург', ' пр. Невский, 100', 0, '2012-11-12 00:00:00', 4, 1, 0, 1, '', 1, '{"longitude_map_yandex":"","latitude_map_yandex":""}', 4, 'yellowStretchyIcon', 721, '{"smallfile":"50a8f7c2f0e05_s.jpg","startfile":"50a8f7c2f0e05_n.jpg"}', '["250","250"]'),
(3, 'Пермь', 'РПК SouvenirPrint - сувенирная продукция оптом со склада в Москве. Рекламные сувениры. Все виды нанесения логотипа. Круговая шелкография на ручках. Тампопечать на ручках', 'РПК SouvenirPrint - сувенирная продукция оптом со склада в Москве. Рекламные сувениры. Все виды нанесения логотипа. Круговая шелкография на ручках. Тампопечать на ручках', 'Пермь', 'Стахановская, 40', 0, '0000-00-00 00:00:00', 4, 1, 0, 1, '', 1, '{"longitude_map_yandex":"","latitude_map_yandex":""}', 1, 'blueStretchyIcon', 721, '{"smallfile":"50a9b8ed25995_s.jpg","startfile":"50a9b8ed25995_n.jpg"}', '["250","150"]'),
(4, 'Пермь', 'Пожар в колонии в Калининграде локализован, пострадавших нет!', 'Пожар в колонии в Калининграде локализован, пострадавших нет!', 'Пермь', 'Чкалова, 5', 0, '2012-11-19 10:48:20', 4, 1, 0, 1, '', 1, '{"longitude_map_yandex":"","latitude_map_yandex":""}', 1, 'darkorangeStretchyIcon', 721, '{"smallfile":"50a9ba0c524c0_s.jpg","startfile":"50a9ba0c524c0_n.jpg"}', '["250","150"]'),
(5, 'Питер', 'пр. Невский, 24', 'пр. Невский, 24', 'Санкт-Петербург', 'пр. Невский, 24', 0, '2012-11-19 11:28:49', 4, 1, 0, 1, '', 1, '{"longitude_map_yandex":"","latitude_map_yandex":""}', 11, 'gymIcon', 721, '{"smallfile":null,"startfile":null}', '["150","100"]');
