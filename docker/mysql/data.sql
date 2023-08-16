-- Adminer 4.8.1 MySQL 8.0.33 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deleted` tinyint unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `brands` (`id`, `brand_name`, `deleted`) VALUES
(1,	'Форус',	0),
(2,	'AZIA',	0),
(3,	'КНАУФ',	0),
(4,	'VERO',	0);

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deleted` tinyint unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `category_name`, `deleted`) VALUES
(1,	'Гипсокартон',	0),
(2,	'Штукатурка',	0),
(3,	'Шпаклевка',	0),
(4,	'Грунтовка',	0);

DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint unsigned DEFAULT '0',
  `deleted` tinyint unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cities` (`id`, `name`, `is_active`, `deleted`) VALUES
(1,	'Ташкент',	1,	0),
(2,	'Нурафшон',	1,	0),
(3,	'Бухара',	1,	0),
(4,	'Самарканд',	0,	0),
(5,	'Карши',	0,	0),
(6,	'Термез',	0,	0),
(7,	'Навои',	0,	0),
(8,	'Джизак',	0,	0),
(9,	'Гулистан',	0,	0),
(10,	'Андижан',	0,	0),
(11,	'Наманган',	0,	0),
(12,	'Фергана',	0,	0),
(13,	'Угренч',	0,	0),
(14,	'Нукус',	0,	0);

DROP TABLE IF EXISTS `commissioners`;
CREATE TABLE `commissioners` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `tg_username` varchar(300) DEFAULT NULL,
  `tg_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `city_id` int unsigned NOT NULL,
  `tg_id` bigint unsigned NOT NULL,
  `tg_username` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `phone`, `city_id`, `tg_id`, `tg_username`) VALUES
(5,	'Uchken',	NULL,	'1007545645',	5,	32432533464,	'uch'),
(19,	'Лол',	'',	'79167625303',	1,	892205925,	'rodionaka'),
(20,	'Andrei',	'',	'79144098250',	1,	479734807,	'OkiTokiA');

DROP TABLE IF EXISTS `order_vendors`;
CREATE TABLE `order_vendors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `vendor_id` int unsigned NOT NULL,
  `products` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint unsigned NOT NULL,
  `archive` tinyint unsigned DEFAULT '0',
  `total_price` bigint unsigned NOT NULL,
  `distance` float unsigned NOT NULL,
  `notification_count` tinyint NOT NULL DEFAULT '0',
  `debt_accrued` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `order_date` int unsigned NOT NULL,
  `products` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_price` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `price_changes`;
CREATE TABLE `price_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `date_change` int unsigned NOT NULL,
  `old_price` int unsigned DEFAULT NULL,
  `new_price` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `photo` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `article` int unsigned DEFAULT NULL,
  `category_id` int unsigned NOT NULL,
  `brand_id` int unsigned NOT NULL,
  `vendor_id` int unsigned NOT NULL,
  `quantity_available` int unsigned NOT NULL,
  `price` int unsigned NOT NULL,
  `price_dollar` int unsigned DEFAULT '0',
  `max_price` int unsigned NOT NULL,
  `max_price_dollar` int unsigned DEFAULT '0',
  `unit_id` tinyint unsigned NOT NULL,
  `deleted` tinyint unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `description`, `photo`, `article`, `category_id`, `brand_id`, `vendor_id`, `quantity_available`, `price`, `max_price`, `unit_id`, `deleted`) VALUES
(7,	'Гипсокартон КНАУФ потолочный влагостойкий (4.5мм)',	'Гипсикартон',	'/upload/ee912d3775ca7a41ae67b33d91f96a16.jpg',	0,	1,	3,	1,	9,	70000,	100000,	1,	0),
(8,	'Гипсокартон КНАУФ потолочный влагостойкий (5.5мм)',	'Гипсокартон',	'/upload/04d9e57c7bf8870b647cd45d6d0c9e32.jpg',	0,	1,	3,	1,	90,	60000,	80000,	1,	0),
(9,	'Гипсокартон КНАУФ потолочный влагостойкий (10.5мм)',	'Гипсокартон',	'/upload/d8dae7d4e0906a272c951efa14e512ce.jpg',	0,	1,	3,	1,	10,	90000,	120000,	1,	0),
(10,	'Гипсокартон КНАУФ потолочный влагостойкий (12мм)',	'Гиипсокартон',	'/upload/f43e09e81d4ae4c55fba0c1e3addeabf.jpg',	0,	1,	3,	1,	15,	30000,	40000,	1,	0),
(11,	'Гипсокартон КНАУФ потолочный влагостойкий (1.5мм)',	'Гипскокартон',	'/upload/3cf09b639b45fe206435da7651e5d4ee.jpg',	0,	1,	4,	1,	20,	50000,	70000,	1,	0),
(12,	'Гипсокартон Azia потолочный влагостойкий (4.5мм)',	'Гипсокартон',	'/upload/af6a18fed7c5f929f79191ea60a77b75.jpg',	0,	1,	2,	1,	34,	45000,	60000,	1,	0),
(13,	'Гипсокартон Форус потолочный влагостойкий (4.5мм)',	'Гипсокартон',	'/upload/2c952ee31902dd8b39a48ccaf1e573f4.jpg',	0,	1,	1,	1,	454,	35000,	45000,	1,	0),
(14,	'Гипсокартон VERO потолочный влагостойкий (4.5мм)',	'Гипсокартон',	'/upload/3aa286f6b5f63324f4aa2da286410d2e.jpg',	0,	1,	4,	1,	45,	55000,	75000,	1,	0),
(15,	'Шпаклевка гипсовая Fugen Knauf',	'',	'/upload/bb16368d53d390283781f1b75325cca5.png',	0,	3,	3,	1,	43,	45000,	56000,	1,	0),
(16,	'Шпаклевка гипсовая Fugen Knauf',	'',	'/upload/83411ee9c45a81b1c05a61f9af50b2d6.png',	0,	3,	3,	1,	424,	45000,	56400,	1,	0),
(17,	'Шпаклевка гипсовая Fugen VERO',	'',	'/upload/d62460c7ce5680b113c6933c7e25590b.png',	0,	3,	4,	1,	332,	33000,	43000,	1,	0),
(18,	'Шпаклевка гипсовая Fugen VERO',	'',	'/upload/91f812b963d0a6347027ed207aee62e1.png',	0,	3,	4,	1,	45,	55500,	78000,	1,	0),
(19,	'Шпаклевка гипсовая Fugen VERO',	'',	'/upload/28c6904999eba3d0ad895577ffea13d5.png',	0,	3,	4,	1,	435,	67000,	87000,	1,	0),
(20,	'Шпаклевка гипсовая Fugen AZIA',	'',	'/upload/f6497f0e1161734c889de217904ac962.png',	0,	3,	2,	1,	56,	56000,	120000,	1,	0),
(21,	'Шпаклевка гипсовая Fugen AZIA',	'',	'/upload/6f0c5478b82ebb0e5f79f90ae53b05e2.png',	0,	3,	2,	1,	342,	12000,	15000,	1,	0),
(22,	'Шпаклевка гипсовая Fugen Форус',	'',	'/upload/ffd87208c9ad2bd3e0a6d99305950f0f.png',	0,	3,	1,	1,	24,	54000,	56000,	1,	0),
(23,	'Шпаклевка гипсовая Fugen Форус',	'',	'/upload/9e68d08464acd4066929e53902433383.png',	0,	3,	1,	1,	345,	87000,	90000,	1,	0),
(24,	'Штукатурка Форус (500 мл)',	'',	'/upload/abf6b9262eb4454ef73e41d4d5283cb8.jpg',	0,	2,	1,	1,	33,	55000,	78000,	1,	0),
(25,	'Штукатурка Форус (600 мл)',	'',	'/upload/6fd7014fabb756aa444b3acc104f1f07.jpg',	0,	2,	1,	1,	234,	45000,	67000,	1,	0),
(26,	'Штукатурка Форус (900 мл)',	'',	'/upload/105b6da3a4ce2683d1349c5c05010c4c.jpg',	0,	2,	1,	1,	353,	33300,	50000,	1,	0),
(27,	'Штукатурка AZIA (500 мл)',	'',	'/upload/6c733687e8ba553197f73283f3955644.jpg',	0,	2,	2,	1,	454,	45000,	60000,	1,	0),
(28,	'Штукатурка AZIA (200 мл)',	'',	'/upload/5badd64fb9db303c82888aaedbce6f0e.jpg',	0,	2,	2,	1,	464,	44530,	56000,	1,	0),
(29,	'Штукатурка AZIA (800 мл)',	'',	'/upload/8fea528b3e908c9c97cf2b53fdf29e59.jpg',	0,	2,	2,	1,	433,	12000,	15000,	1,	0),
(30,	'Штукатурка КНАУФ (200 мл)',	'',	'/upload/d94256f3ff1c2562b38fc7fad246db5a.jpg',	0,	2,	3,	1,	46,	67000,	89000,	1,	0),
(31,	'Штукатурка AZIA (600 мл)',	'',	'/upload/adac33216d860d177d522bf0f6bd03af.jpg',	0,	2,	2,	1,	35,	45000,	76000,	1,	0),
(32,	'Штукатурка VERO (500 мл)',	'',	'/upload/117aa75fad2585ec13ca491ff932e2a4.jpg',	0,	2,	4,	1,	435,	47000,	86500,	1,	0),
(33,	'Штукатурка VERO (750 мл)',	'',	'/upload/e9773a3fecac2c48a314f2d82b943f6e.jpg',	0,	2,	4,	1,	464,	17500,	19000,	1,	0),
(34,	'Грунтовка Форус (500 мл)',	'',	'/upload/989e7202a1a83b7a78363180ed73bfd6.jpg',	0,	4,	1,	1,	223,	44400,	56000,	1,	0),
(35,	'Грунтовка Форус (700 мл)',	'',	'/upload/47fbc44ce12a0855a8b70afd64d0c323.jpg',	0,	4,	1,	1,	543,	54000,	87000,	1,	0),
(36,	'Грунтовка Форус (900 мл)',	'',	'/upload/cbd5c1b841b8b2caeca76c7369fd6790.jpg',	0,	4,	1,	1,	334,	34000,	45000,	1,	0),
(37,	'Грунтовка VERO (500 мл)',	'',	'/upload/aa46e809738bcd071e1ecd47daf1a62c.jpg',	0,	4,	4,	1,	435,	33000,	36000,	1,	0),
(38,	'Грунтовка VERO (600 мл)',	'',	'/upload/a33d9e99e95dc754ff1d560fde2663cb.jpg',	0,	4,	4,	1,	435,	45000,	67000,	1,	0),
(39,	'Грунтовка VERO (900 мл)',	'',	'/upload/fb55e1c1392c73c0b6a36ad03eb5c258.jpg',	0,	4,	4,	1,	675,	67700,	70000,	1,	0),
(40,	'Грунтовка AZIA (500 мл)',	'',	'/upload/26990f1e37245b9e7bf4812e0091ce62.jpg',	0,	4,	2,	1,	453,	45000,	55000,	1,	0),
(41,	'Грунтовка AZIA (900 мл)',	'',	'/upload/5bdaa307b0aef3ee61a530911f7906ad.jpg',	0,	4,	2,	1,	435,	45500,	56000,	1,	0),
(42,	'Грунтовка Кнауф (500 мл)',	'',	'/upload/ca057d6c7569711c523796f81dc365c8.jpg',	0,	4,	3,	1,	45,	54000,	56000,	1,	0),
(43,	'Грунтовка Кнауф (800 мл)',	'',	'/upload/6d80ff814a59a0c5b11356757c6898e7.jpg',	0,	4,	3,	1,	686,	67000,	120000,	1,	0);

-- 2023-08-11 22:24:41

DROP TABLE IF EXISTS `units`;
CREATE TABLE `units` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_short` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `units` (`id`, `name_short`, `name`) VALUES
(1,	'шт',	'штука'),
(2,	'упак',	'упаковка'),
(3,	'компл',	'комплект'),
(4,	'кг',	'килограмм'),
(5,	'гр',	'грамм'),
(6,	'м2',	'квадратный метр'),
(7,	'л',	'литр');

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `city_id` int unsigned NOT NULL,
  `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tg_username` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tg_id` bigint unsigned DEFAULT NULL,
  `coordinates` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `role` tinyint NOT NULL,
  `percent` tinyint DEFAULT '0',
  `debt` int unsigned DEFAULT '0',
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_reg` bigint NOT NULL,
  `hash_string` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint NOT NULL,
  `deleted` tinyint unsigned DEFAULT '0',
  `price_confirmed` tinyint unsigned DEFAULT '1',
  `currency_dollar` tinyint unsigned DEFAULT '0',
  `rate` int unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vendors` (`id`, `name`, `city_id`, `phone`, `email`, `tg_username`, `tg_id`, `coordinates`, `role`, `percent`, `debt`, `comment`, `date_reg`, `hash_string`, `password`, `token`, `is_active`, `deleted`, `price_confirmed`, `currency_dollar`, `rate`) VALUES
(1,	'Поставщик1',	1,	'79996667788',	'first@bk.ru',	'rodionaka',	892205925,	'{\"latitude\":55.657049,\"longitude\":37.569306}',	2,	0,	1,	'Первый поставщик',	1689507890,	'haVuGqrLCiM1A',	'crK8GaB5k/z6A',	'toStqvGkDkeHU',	1,	0,	1,	0,	1),
(2,	'Поставщик2',	2,	'79168881122',	'second@bk.ru',	NULL,	NULL,	NULL,	2,	0,	0,	'Второй поставщик',	1689507982,	'haMCdWzHNM9hc',	'crF3z6ZLaP79c',	'',	1,	0,	1,	0,	1),
(3,	'Поставщик3',	3,	'76663334455',	'third@bk.ru',	'KlevtsovaEV',	1752911328,	'{\"latitude\":55.819855,\"longitude\":37.829782}',	2,	0,	0,	'Третий поставщик',	1689508041,	'hahUrbGggMKc',	'cr9Oe/o1K7r0o',	'',	1,	0,	1,	0,	1),
(4,	'Поставщик4',	4,	'71117770099',	'fourth@bk.ru',	NULL,	NULL,	NULL,	2,	0,	0,	'Четвёртый постащик',	1689508156,	'haa5ulKzPo6g6',	'crtdJGYGWRn1k',	'',	1,	0,	1,	0,	1),
(5,	'Админ',	5,	'77777777777',	'admin@bk.ru',	NULL,	NULL,	NULL,	1,	0,	0,	'Админ',	1688636888,	'hazetypXJkIIk',	'vendor',	'to9TonSdiTR22',	1,	0,	1,	0,	1);

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1,	'phone',	'89997776655');


-- 2023-08-09 13:06:11
