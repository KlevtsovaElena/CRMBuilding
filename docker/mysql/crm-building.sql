-- Adminer 4.8.1 MySQL 8.2.0 dump

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
(4,	'VERO',	0),
(5,	'Brend NoNaMe',	0),
(6,	'MEGAMIX',	0),
(7,	'Форус',	0),
(8,	'КНАУФ',	0);

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
(4,	'Грунтовка',	0),
(5,	'Профиль',	0),
(6,	'1000 мелочей',	0),
(7,	'Клей монтажный',	0),
(8,	'Плиточный клей',	0),
(9,	'Наливной пол и Гидроизоляция',	0);

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
(4,	'Самарканд',	1,	0),
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
  `is_blocked` tinyint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `phone`, `city_id`, `tg_id`, `tg_username`, `is_blocked`) VALUES
(5,	'Uchken',	NULL,	'1007545645',	5,	32432533464,	'uch',	0),
(21,	'Timon',	'Decathlon',	'79672772550',	1,	223054377,	'timondecathlon',	0),
(22,	'S',	'R',	'998903480305',	1,	601131024,	'skidkabor',	0),
(23,	'GsmServer™',	'',	'998981282810',	1,	197583494,	'unlockservers',	0),
(25,	'Леночка',	'',	'79060685310',	3,	1752911328,	'KlevtsovaEV',	0),
(27,	'Игорь',	'',	'79152032125',	1,	5677540667,	'LogunovIgor',	0),
(28,	'Лол',	'',	'998777777777',	2,	892205925,	'rodionaka',	0),
(29,	'Catherine',	'',	'79854377397',	3,	443133309,	'cazerine_hg',	0),
(30,	'Skidkabuy_Admin',	'',	'998977608888',	3,	5515948675,	'skidkabuy_admin',	0),
(31,	'Машъал',	'Маматкулов',	'998946847020',	2,	1669936,	'umdcam',	0),
(32,	'zafar',	'',	'998949471027',	1,	5310841263,	'',	0),
(33,	'FARHAD',	'',	'998901762607',	1,	64449190,	'',	0),
(34,	'Nastya',	'',	'998008889999',	4,	299855596,	'anvikse',	0),
(35,	'iLLbeBack',	'',	'',	1,	4327064,	'Bumbusik',	0),
(36,	'Candy',	'Key',	'998933540322',	1,	6184975087,	'candycey',	0),
(37,	'Mrs',	'',	'79262832982',	3,	1415183995,	'',	0),
(38,	'Na',	'',	'998903498558',	3,	1959569,	'bravosmm',	0),
(39,	'......',	'',	'',	1,	6237608066,	'',	0),
(40,	'iPad 7',	'',	'998903726322',	1,	6451636866,	'',	0),
(41,	'',	'',	'',	3,	34404845,	'Miraziz_Xadichabegim',	0),
(42,	'A.',	'N.',	'',	3,	302995860,	'',	0),
(43,	'Olimjon',	'Isomov',	'',	3,	769918709,	'isamovolim',	0),
(44,	'ЭЛЁР',	'',	'998915882002',	3,	1232585433,	'',	0),
(45,	'Olesik',	'',	'79036616687',	3,	1061474542,	'Olesik_404',	0),
(46,	'Мася Че',	'',	'79175577254',	3,	1638101427,	'',	0),
(47,	'Andrei',	'Tokaiskii',	'998970098250',	1,	479734807,	'',	0);

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

INSERT INTO `order_vendors` (`id`, `order_id`, `vendor_id`, `products`, `status`, `archive`, `total_price`, `distance`, `notification_count`, `debt_accrued`) VALUES
(1,	1,	2,	'{\"64\":11,\"72\":11}',	0,	0,	1342000,	0,	0,	0),
(2,	2,	2,	'{\"64\":21}',	0,	0,	1407000,	0,	0,	0),
(3,	3,	2,	'{\"76\":21}',	0,	0,	1428000,	0,	0,	0),
(4,	4,	2,	'{\"78\":12}',	0,	0,	672000,	0,	0,	0),
(5,	5,	2,	'{\"78\":10}',	0,	0,	560000,	0,	0,	0),
(6,	6,	2,	'{\"64\":30}',	0,	0,	2010000,	0,	0,	0),
(7,	6,	3,	'{\"96\":12}',	2,	0,	240000,	2781.52,	5,	0),
(8,	7,	2,	'{\"64\":100,\"74\":6}',	4,	0,	6970000,	0,	0,	1),
(9,	8,	2,	'{\"64\":33,\"76\":24}',	4,	0,	3843000,	0,	0,	1),
(10,	9,	2,	'{\"64\":30}',	4,	0,	2010000,	0,	0,	1),
(11,	10,	2,	'{\"64\":53,\"74\":22}',	1,	0,	4541000,	0,	0,	0),
(12,	10,	1,	'{\"65\":22}',	3,	0,	1474000,	7053.75,	5,	0),
(13,	10,	3,	'{\"96\":22}',	2,	0,	440000,	7078.11,	5,	0),
(14,	11,	3,	'{\"96\":10}',	0,	0,	200000,	24.35,	4,	0),
(15,	12,	3,	'{\"96\":10}',	0,	0,	200000,	24.35,	4,	0),
(16,	13,	1,	'{\"71\":10}',	4,	0,	560000,	0.01,	5,	1),
(17,	14,	1,	'{\"65\":41}',	4,	1,	2788000,	2790.19,	1,	1),
(18,	15,	1,	'{\"65\":11}',	4,	0,	748000,	24.37,	1,	1),
(19,	16,	1,	'{\"65\":10}',	0,	0,	680000,	7053.75,	4,	0),
(20,	17,	1,	'{\"67\":11}',	0,	0,	600600,	0.01,	4,	0),
(21,	20,	1,	'{\"67\":10,\"71\":10}',	4,	0,	1106000,	0.01,	5,	1),
(22,	22,	3,	'{\"96\":10}',	1,	0,	200000,	24.35,	5,	0),
(23,	23,	1,	'{\"69\":46}',	1,	0,	2070000,	7053.75,	5,	0),
(24,	24,	1,	'{\"65\":10,\"69\":10,\"79\":3}',	1,	0,	1331000,	2792.82,	5,	0),
(25,	27,	1,	'{\"71\":10}',	2,	0,	560000,	7053.75,	3,	0),
(26,	28,	2,	'{\"64\":50}',	0,	0,	3350000,	0,	0,	0),
(27,	29,	1,	'{\"65\":10}',	1,	0,	680000,	7053.75,	5,	0),
(28,	30,	1,	'{\"65\":14}',	3,	1,	952000,	574.23,	5,	0),
(29,	31,	10,	'{\"100\":10,\"107\":10}',	0,	0,	850000,	0,	0,	0),
(30,	33,	2,	'{\"66\":1}',	0,	0,	67000,	0,	0,	0),
(31,	34,	3,	'{\"134\":1,\"135\":1,\"136\":1,\"139\":1,\"141\":1}',	2,	0,	8300,	9.09,	1,	0),
(32,	39,	3,	'{\"139\":10}',	1,	0,	13000,	9.09,	1,	0),
(34,	43,	1,	'{\"140\":1}',	1,	0,	1000,	33.45,	5,	0),
(37,	46,	3,	'{\"137\":1,\"138\":1,\"139\":1}',	4,	0,	4300,	9.09,	2,	1),
(42,	58,	3,	'{\"141\":2}',	4,	0,	2000,	9.09,	1,	1);

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

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `products`, `location`, `total_price`) VALUES
(1,	20,	1692702127,	'{\"64\":11,\"72\":11}',	'{\"latitude\":41.321713,\"longitude\":69.2758}',	1342000),
(2,	21,	1692702176,	'{\"64\":21}',	'{\"latitude\":41.321655,\"longitude\":69.275765}',	1407000),
(3,	21,	1692702219,	'{\"76\":21}',	'{\"latitude\":41.321695,\"longitude\":69.275906}',	1428000),
(4,	21,	1692702272,	'{\"78\":12}',	'{\"latitude\":0,\"longitude\":0}',	672000),
(5,	21,	1692702313,	'{\"78\":10}',	'{\"latitude\":41.319178,\"longitude\":69.279079}',	560000),
(6,	21,	1692702454,	'{\"64\":30,\"96\":12}',	'{\"latitude\":41.321721,\"longitude\":69.275828}',	2250000),
(7,	23,	1692702534,	'{\"64\":100,\"74\":6}',	'{\"latitude\":41.321703,\"longitude\":69.275934}',	6970000),
(8,	21,	1692702742,	'{\"64\":33,\"76\":24}',	'{\"latitude\":41.321743,\"longitude\":69.275818}',	3843000),
(9,	21,	1692703024,	'{\"64\":30}',	'{\"latitude\":41.331845,\"longitude\":69.290556}',	2010000),
(10,	20,	1692708650,	'{\"64\":53,\"65\":22,\"74\":22,\"96\":22}',	'{\"latitude\":0,\"longitude\":0}',	6455000),
(11,	24,	1692786016,	'{\"96\":10}',	'{\"latitude\":55.657139,\"longitude\":37.569316}',	200000),
(12,	26,	1692786351,	'{\"96\":10}',	'{\"latitude\":55.657139,\"longitude\":37.569316}',	200000),
(13,	26,	1692786490,	'{\"71\":10}',	'{\"latitude\":55.657124,\"longitude\":37.569351}',	560000),
(14,	20,	1692787861,	'{\"65\":41}',	'{\"latitude\":41.321813,\"longitude\":69.280077}',	2788000),
(15,	25,	1692787869,	'{\"65\":11}',	'{\"latitude\":55.819991,\"longitude\":37.829688}',	748000),
(16,	27,	1692788071,	'{\"65\":10}',	'{\"latitude\":0,\"longitude\":0}',	680000),
(17,	26,	1692788431,	'{\"67\":11}',	'{\"latitude\":55.657139,\"longitude\":37.569316}',	600600),
(18,	28,	1692906633,	'[]',	'{\"latitude\":55.657124,\"longitude\":37.569351}',	0),
(19,	28,	1692906945,	'[]',	'{\"latitude\":0,\"longitude\":0}',	0),
(20,	28,	1692906996,	'{\"67\":10,\"71\":10}',	'{\"latitude\":55.657113,\"longitude\":37.569448}',	1106000),
(21,	28,	1692907045,	'[]',	'{\"latitude\":55.657143,\"longitude\":37.569378}',	0),
(22,	28,	1692907388,	'{\"96\":10}',	'{\"latitude\":55.657113,\"longitude\":37.569448}',	200000),
(23,	22,	1693457444,	'{\"69\":46}',	'{\"latitude\":0,\"longitude\":0}',	2070000),
(24,	30,	1693458214,	'{\"65\":10,\"69\":10,\"79\":3}',	'{\"latitude\":41.351361,\"longitude\":69.365355}',	1331000),
(25,	30,	1693458214,	'[]',	'{\"latitude\":41.351361,\"longitude\":69.365355}',	0),
(26,	30,	1693458214,	'[]',	'{\"latitude\":41.351361,\"longitude\":69.365355}',	0),
(27,	34,	1694113016,	'{\"71\":10}',	'{\"latitude\":0,\"longitude\":0}',	560000),
(28,	34,	1694113392,	'{\"64\":50}',	'{\"latitude\":0,\"longitude\":0}',	3350000),
(29,	35,	1694621946,	'{\"65\":10}',	'{\"latitude\":0,\"longitude\":0}',	680000),
(30,	37,	1694719776,	'{\"65\":14}',	'{\"latitude\":59.899858,\"longitude\":43.103967}',	952000),
(31,	30,	1696660408,	'{\"100\":10,\"107\":10}',	'{\"latitude\":0,\"longitude\":0}',	850000),
(32,	30,	1696660409,	'[]',	'{\"latitude\":0,\"longitude\":0}',	0),
(33,	28,	1699025200,	'{\"66\":1}',	'{\"latitude\":0,\"longitude\":0}',	67000),
(34,	25,	1702115825,	'{\"134\":1,\"135\":1,\"136\":1,\"139\":1,\"141\":1}',	'{\"latitude\":55.877656,\"longitude\":37.932814}',	8300),
(35,	25,	1702115825,	'[]',	'{\"latitude\":55.877656,\"longitude\":37.932814}',	0),
(36,	25,	1702115826,	'[]',	'{\"latitude\":55.877656,\"longitude\":37.932814}',	0),
(37,	25,	1702115826,	'[]',	'{\"latitude\":55.877656,\"longitude\":37.932814}',	0),
(38,	25,	1702115827,	'[]',	'{\"latitude\":55.877656,\"longitude\":37.932814}',	0),
(39,	25,	1702116055,	'{\"139\":10}',	'{\"latitude\":55.877671,\"longitude\":37.932779}',	13000),
(43,	25,	1702116220,	'{\"140\":1}',	'{\"latitude\":55.877671,\"longitude\":37.932779}',	1000),
(46,	45,	1702117131,	'{\"137\":1,\"138\":1,\"139\":1}',	'{\"latitude\":55.877656,\"longitude\":37.932814}',	4300),
(47,	45,	1702117131,	'[]',	'{\"latitude\":55.877656,\"longitude\":37.932814}',	0),
(48,	45,	1702117132,	'[]',	'{\"latitude\":55.877656,\"longitude\":37.932814}',	0),
(58,	46,	1702117820,	'{\"141\":2}',	'{\"latitude\":55.877656,\"longitude\":37.932814}',	2000);

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
  `name` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '',
  `name2` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '',
  `name3` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '',
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `description2` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `description3` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
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
  `is_active` tinyint unsigned DEFAULT '1',
  `is_confirm` tinyint unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `name2`, `name3`, `description`, `description2`, `description3`, `photo`, `article`, `category_id`, `brand_id`, `vendor_id`, `quantity_available`, `price`, `price_dollar`, `max_price`, `max_price_dollar`, `unit_id`, `deleted`, `is_active`, `is_confirm`) VALUES
(64,	'Гипсокартон КНАУФ потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	3,	2,	65464,	67000,	0,	70000,	0,	1,	0,	1,	1),
(65,	'Гипсокартон КНАУФ потолочный влагостойкий (7.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	3,	1,	50,	68000,	0,	70000,	0,	1,	0,	1,	1),
(66,	'Гипсокартон Форус потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	2,	564,	67000,	0,	80000,	0,	1,	0,	1,	1),
(67,	'Гипсокартон Форус потолочный влагостойкий (9.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	1,	464,	54600,	0,	55000,	0,	1,	0,	1,	1),
(68,	'Штукатурка VERO потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	2,	4564,	65600,	0,	70000,	0,	1,	0,	1,	1),
(69,	'Штукатурка VERO потолочный влагостойкий (12.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	1,	454,	45000,	0,	55000,	0,	1,	0,	1,	1),
(70,	'Штукатурка AZIA потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	2,	2,	345,	46460,	0,	60000,	0,	1,	0,	1,	1),
(71,	'Штукатурка AZIA потолочный влагостойкий (7.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	2,	1,	55,	56000,	0,	70000,	0,	1,	0,	1,	1),
(72,	'Шпаклёвка Форус потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	1,	2,	46,	55000,	0,	70000,	0,	1,	0,	1,	1),
(73,	'Шпаклёвка Форус потолочный влагостойкий (8.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	1,	1,	346,	55000,	0,	67000,	0,	1,	0,	1,	1),
(74,	'Шпаклёвка КНАУФ потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	3,	2,	53,	45000,	0,	55000,	0,	1,	0,	1,	1),
(75,	'Шпаклёвка КНАУФ потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	3,	1,	354,	45000,	0,	56000,	0,	1,	0,	1,	1),
(76,	'Грунтовка AZIA потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	4,	2,	2,	353,	68000,	0,	70000,	0,	1,	0,	1,	1),
(77,	'Грунтовка AZIA потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	4,	2,	1,	453,	45000,	0,	80000,	0,	1,	0,	1,	1),
(78,	'Грунтовка VERO потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	4,	4,	2,	345,	56000,	0,	70000,	0,	1,	0,	1,	1),
(79,	'Грунтовка VERO потолочный влагостойкий (4.5мм)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	4,	4,	1,	534,	67000,	0,	70000,	0,	1,	0,	1,	1),
(80,	'Товар 3 гипсокартон форус',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	3,	2,	10000,	1,	20000,	2,	1,	1,	1,	1),
(81,	'Товар 3 штукатурка vero',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	3,	3,	20000,	2,	30000,	3,	1,	1,	1,	1),
(82,	'Товар 3 гипсокартон vero',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	4,	3,	6,	20000,	2,	30000,	3,	2,	1,	1,	1),
(83,	'товар 3 гипс форус',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	3,	15,	10000,	1,	20000,	2,	2,	1,	1,	1),
(84,	'hjhjhjhjhjhj',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	3,	2,	100000,	10,	110000,	11,	1,	1,	1,	1),
(85,	'Товар с мобилы редакт',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	3,	5,	20000,	2,	30000,	3,	2,	1,	1,	1),
(86,	'последний тест поставщик 3',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	2,	3,	2,	30000,	3,	40000,	4,	6,	1,	1,	1),
(87,	'Товар поставщика 4 тест',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	4,	4,	4,	400,	1000,	0,	1200,	0,	2,	1,	1,	1),
(88,	'Тестовый товар по новой категории и бренду',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	5,	5,	4,	5,	500,	0,	600,	0,	4,	0,	1,	1),
(89,	'поставщик тест товар1',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	6,	2,	84300,	6,	98350,	7,	2,	2,	1,	1),
(90,	'поставщик тест товар2',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	6,	2,	210750,	15,	281000,	20,	2,	2,	1,	1),
(91,	'поставщик тест товар3',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	6,	2,	210750,	15,	224800,	16,	1,	2,	1,	1),
(92,	'Товар поставщик 3 ',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	3,	2,	20000,	2,	30000,	3,	1,	1,	1,	1),
(93,	'товар 3',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	3,	2,	10000,	1,	20000,	2,	2,	1,	1,	1),
(94,	'hjhjjhjh',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	3,	5,	40000,	4,	50000,	5,	1,	1,	1,	1),
(95,	'Поаоаоа 17.08',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	3,	2,	30000,	3,	40000,	4,	5,	1,	1,	1),
(96,	'товар 17.08',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	3,	2,	20000,	2,	30000,	3,	1,	0,	1,	1),
(97,	'fffff',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	6,	20,	84300,	6,	112400,	8,	1,	0,	1,	1),
(98,	'шпаклевка',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	2,	1,	3,	4000,	0,	4500,	0,	2,	0,	1,	1),
(99,	'wwwdx',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	2,	6,	566,	702500,	50,	716550,	51,	3,	0,	1,	1),
(100,	'Гипсокартон КНАУФ Стандартный 2500х1200 (12.5 ММ)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	3,	10,	1000,	46000,	0,	48000,	0,	1,	0,	1,	1),
(101,	'Гипсокартон КНАУФ Стандартный 2500х1200 (9.5 ММ)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	3,	10,	1000,	41000,	0,	43000,	0,	1,	0,	1,	1),
(102,	'Гипсокартон КНАУФ Влагостойкий 2500х1200 (12.5 ММ)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	3,	10,	1000,	54000,	0,	56000,	0,	1,	0,	1,	1),
(103,	'Гипсокартон КНАУФ Влагостойкий 2500х1200 (9.5 ММ)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	3,	10,	1000,	52000,	0,	54000,	0,	1,	0,	1,	1),
(104,	'Гипсокартон КНАУФ Огнестойкий 2500х1200 (12.5 ММ)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	3,	10,	1000,	60000,	0,	63000,	0,	1,	0,	1,	1),
(105,	'Штукатурка  КНАУФ-Ротбанд (Гипсовая универсальная)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	3,	10,	1000,	37000,	0,	39000,	0,	1,	0,	1,	1),
(106,	'Штукатурка КНАУФ Старт (Гипсовая)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	3,	10,	1000,	39000,	0,	41000,	0,	1,	0,	1,	1),
(107,	'Штукатурка  КНАУФ-MP 75 (Машинного Нанесения)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	3,	10,	1000,	39000,	0,	42000,	0,	1,	0,	1,	1),
(108,	'Шпаклевка КНАУФ Ротбанд Финиш (Гипсовая Финишная)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	3,	10,	1000,	44000,	0,	48000,	0,	1,	0,	1,	1),
(109,	'Шпаклевка  КНАУФ-Фуген (Гипсовая универсальная)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	3,	10,	1000,	62000,	0,	64000,	0,	1,	0,	1,	1),
(110,	'Шпаклевка КНАУФ-Сатенгипс (Гипсовая Финишная)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	3,	10,	1000,	30000,	0,	32000,	0,	1,	0,	1,	1),
(111,	'Грунтовка КНАУФ-Бетоконтакт',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	4,	3,	10,	1000,	400000,	0,	420000,	0,	1,	0,	1,	1),
(112,	'Грунтовка КНАУФ-Тифенгрунд (Укрепляющая глубокого проникновения)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	4,	3,	10,	1000,	400000,	0,	420000,	0,	1,	0,	1,	1),
(113,	'Грунтовка КНАУФ-Мультигрунд (Для впитывающих оснований)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	4,	3,	10,	1000,	400000,	0,	420000,	0,	1,	0,	1,	1),
(114,	'Клей монтажный КНАУФ-Перлфикс',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	7,	3,	10,	1000,	28000,	0,	31000,	0,	1,	0,	1,	1),
(115,	'Клей Megamix Adhesive (Для плиток)  25кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	8,	6,	10,	1000,	35000,	0,	37000,	0,	1,	0,	1,	1),
(116,	'Клей Megamix Adhesive Extra (Усиленный для плиток)  25кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	8,	6,	10,	1000,	37000,	0,	39000,	0,	1,	0,	1,	1),
(117,	'Клей Megamix Granit (Для Гранита, Мрамора, Травертина)  25кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	8,	6,	10,	1000,	40000,	0,	42000,	0,	1,	0,	1,	1),
(118,	'Клей Megamix Mosaic (Для Майолики и мозаичной плитки бассейнов)  25кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	8,	6,	10,	1000,	42000,	0,	45000,	0,	1,	0,	1,	1),
(119,	'Клей Megamix Resistant (Для печей и каминов)  25кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	8,	6,	10,	1000,	41000,	0,	43000,	0,	1,	0,	1,	1),
(120,	'Наливной пол Megamix Floor level 25кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	9,	6,	10,	1000,	35000,	0,	37000,	0,	1,	0,	1,	1),
(121,	'Гидроизоляция Megamix MONO STOP 25кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	9,	6,	10,	1000,	42000,	0,	45000,	0,	1,	0,	1,	1),
(122,	'Гидроизоляция Megamix DI STOP (Двухкомпонентная) 25кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	9,	6,	10,	1000,	45000,	0,	49000,	0,	1,	0,	1,	1),
(123,	'Шпаклевка  MegaMix READY FACADE (Фасадная) 25 кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	6,	10,	1000,	35000,	0,	37000,	0,	1,	0,	1,	1),
(124,	'Шпаклевка  MegaMix WHITE FACADE (Фасадная) 25 кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	6,	10,	1000,	38000,	0,	42000,	0,	1,	0,	1,	1),
(125,	'Штукатурка  MegaMix ROTMIX (Универсальная на основе цемента)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	6,	10,	1000,	42000,	0,	45000,	0,	1,	0,	1,	1),
(126,	'Штукатурка  MegaMix PLASTER GYPS (Гипсовая)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	6,	10,	1000,	42000,	0,	43000,	0,	1,	0,	1,	1),
(127,	'Штукатурка  MegaMix MEGA GLATT (Гипсовая Универсальная)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	6,	10,	1000,	45000,	0,	48000,	0,	1,	0,	1,	1),
(128,	'Шпаклевка MegaMix SATIN GYPS (Финишная Гипсовая) 25 кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	6,	10,	1000,	32000,	0,	35000,	0,	1,	0,	1,	1),
(129,	'Шпаклевка MegaMix SATIN-2 (Финишная Гипсовая Мягкая) 25 кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	6,	10,	1000,	35000,	0,	37000,	0,	1,	0,	1,	1),
(130,	'Шпаклевка MegaMix PROFINISH (Профессиональная финишная) 25 кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	6,	10,	1000,	42000,	0,	45000,	0,	1,	0,	1,	1),
(131,	'Штукатурка  MegaMix ROTGYPS (Гипсовая)',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	6,	10,	1000,	46000,	0,	49000,	0,	1,	0,	1,	1),
(132,	'Шпаклевка MegaMix FUGEGYPS (Универсальная Гипсовая) 25 кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	3,	6,	10,	1000,	38000,	0,	42000,	0,	1,	0,	1,	1),
(133,	'Наливной пол Megamix POLGYPS (Гипсовый) 25кг',	'',	'',	'Описание RUS',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	9,	6,	10,	1000,	37000,	0,	39000,	0,	1,	0,	1,	1),
(134,	'nameRUS (только русский)',	'',	'',	'descriptionRUS (только русский)',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	3,	15,	1000,	0,	2000,	0,	1,	0,	1,	1),
(135,	'',	'nameОʻzbekcha (только Оʻzbekcha)',	'',	'',	'descriptionОʻzbekcha (только Оʻzbekcha)',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	3,	12,	2000,	0,	4000,	0,	2,	0,	1,	1),
(136,	'',	'',	'nameЎзбекчa (только Ўзбекча)',	'',	'',	'descriptionЎзбекчa (только Ўзбекча)',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	3,	20,	3000,	0,	4000,	0,	3,	0,	1,	1),
(137,	'nameRUS (RUS + Оʻzbekcha)',	'nameОʻzbekcha (RUS + Оʻzbekcha)',	'',	'descriptionRUS (RUS + Оʻzbekcha)',	'descriptionОʻzbekcha (RUS + Оʻzbekcha)',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	3,	20,	1500,	0,	1999,	0,	2,	0,	1,	1),
(138,	'nameRUS (RUS + Ўзбекча)',	'',	'nameЎзбекча (RUS + Ўзбекча)',	'descriptionRUS (RUS + Ўзбекча)',	'',	'descriptionЎзбекча (RUS + Ўзбекча)',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	3,	30,	1500,	0,	2000,	0,	4,	0,	1,	1),
(139,	'',	'nameОʻzbekcha (Оʻzbekcha + Ўзбекча)',	'nameЎзбекча (Оʻzbekcha + Ўзбекча)',	'',	'descriptionОʻzbekcha (Оʻzbekcha + Ўзбекча)',	'descriptionЎзбекча (Оʻzbekcha + Ўзбекча)',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	3,	20,	1300,	0,	1500,	0,	1,	0,	1,	1),
(140,	'товар поставщик1 рус+Ўзбек',	'',	'товар поставщик1 рус+Ўзбек',	'описание рус+Ўзбек',	'',	'описание рус+Ўзбек',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	1,	1,	1,	8,	1000,	0,	2000,	0,	2,	0,	1,	1),
(141,	'nameRUS (RUS+Оʻzbekcha + Ўзбекча)',	'nameОʻzbekcha (RUS+Оʻzbekcha + Ўзбекча)',	'nameЎзбекча (RUS+Оʻzbekcha + Ўзбекча)',	'descriptionRUS (RUS+Оʻzbekcha + Ўзбекча)',	'descriptionОʻzbekcha (RUS+Оʻzbekcha + Ўзбекча)',	'descriptionЎзбекча (RUS+Оʻzbekcha + Ўзбекча)',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	3,	15,	1000,	0,	1199,	0,	3,	0,	1,	1),
(142,	'test Ташкент',	'',	'',	'test Ташкент (описание)',	'',	'',	'https://cdn.vseinstrumenti.ru/images/goods/stroitelnye-materialy/stroitelnaya-himiya/7478731/560x504/102576788.jpg',	0,	2,	4,	9,	15,	1000,	0,	1999,	0,	2,	0,	1,	1);

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `settings` (`id`, `name`, `value`, `first_name`) VALUES
(1,	'phone',	'89997776655',	'Имя в телеграме');

DROP TABLE IF EXISTS `units`;
CREATE TABLE `units` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_short` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deleted` tinyint unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `units` (`id`, `name_short`, `name`, `deleted`) VALUES
(1,	'шт',	'штука',	0),
(2,	'упак',	'упаковка',	0),
(3,	'компл',	'комплект',	0),
(4,	'кг',	'килограмм',	0),
(5,	'гр',	'грамм',	0),
(6,	'м2',	'квадратный метр',	0),
(7,	'л',	'литр',	0);

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
  `categories` json DEFAULT NULL,
  `time_price_confirm` bigint DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vendors` (`id`, `name`, `city_id`, `phone`, `email`, `tg_username`, `tg_id`, `coordinates`, `role`, `percent`, `debt`, `comment`, `date_reg`, `hash_string`, `password`, `token`, `is_active`, `deleted`, `price_confirmed`, `currency_dollar`, `rate`, `categories`, `time_price_confirm`) VALUES
(1,	'Поставщик1',	1,	'79996667788',	'first@bk.ru',	'rodionaka',	223054377,	'{\"latitude\":55.657049,\"longitude\":37.569306}',	2,	10,	1854111,	'Первый поставщик',	1689507890,	'haVuGqrLCiM1A',	'crK8GaB5k/z6A',	'toiYSB47rMGsw',	1,	0,	1,	0,	1,	NULL,	1703071582),
(2,	'Поставщик2',	2,	'79168881122',	'second@bk.ru',	NULL,	NULL,	NULL,	2,	10,	197000,	'Второй поставщик',	1689507982,	'haMCdWzHNM9hc',	'crF3z6ZLaP79c',	'toiWcZzTq83Bs',	1,	0,	1,	0,	1,	NULL,	1703071584),
(3,	'Поставщик3',	3,	'76663334455',	'third@bk.ru',	'KlevtsovaEV',	1752911328,	'{\"latitude\":55.819855,\"longitude\":37.829782}',	2,	0,	0,	'Третий поставщик',	1689508041,	'hahUrbGggMKc',	'cr9Oe/o1K7r0o',	'tocoGvpzeAQIM',	1,	0,	1,	0,	1,	NULL,	1703071586),
(4,	'Поставщик4',	4,	'998903555444',	'fourth@bk.ru',	NULL,	NULL,	NULL,	2,	0,	0,	'Четвёртый постащик',	1689508156,	'haa5ulKzPo6g6',	'crtdJGYGWRn1k',	'',	1,	0,	1,	0,	1,	NULL,	1703071588),
(5,	'Админ',	5,	'998903480305 ',	'admin@bk.ru',	NULL,	1752911328,	NULL,	1,	0,	0,	'Админ',	1688636888,	'hazetypXJkIIk',	'vendor',	'toFjHExWsijY',	1,	0,	1,	0,	1,	NULL,	0),
(6,	'папарпа',	2,	'43421210101',	'цпычрпрыцо@hwhsg.com',	NULL,	NULL,	NULL,	2,	3,	0,	'',	1692701415,	'hazTJvvfTq1T',	'crQnh5B1fshs',	'',	1,	0,	1,	1,	14050,	NULL,	1703071589),
(9,	'test',	1,	'79999999999',	'test@bk.ru',	NULL,	NULL,	NULL,	2,	10,	100,	'',	1692882849,	'hag730ZITTgZ6',	'crzMepLxVp3UA',	'',	1,	0,	1,	0,	1,	NULL,	1703071591),
(10,	'Сирожиддин',	3,	'998907100666',	'sirojbekshodmonov@gmail.com',	NULL,	NULL,	NULL,	2,	3,	0,	'',	1695118166,	'haLElwpeMtx1I',	'crsva418npIw6',	'toBur8jXOiv6',	1,	0,	1,	0,	1,	NULL,	1703071593),
(11,	'Оптовик 1',	2,	'998876006809',	'sdndfnb@fb.ru',	NULL,	NULL,	NULL,	3,	2,	500,	'',	1701310809,	'hairMjVdU9JXc',	'crR76rgNfytk',	'',	1,	0,	1,	0,	1,	'{\"1\": \"Гипсокартон\", \"3\": \"Шпаклевка\", \"4\": \"Грунтовка\"}',	0),
(12,	'Оптовик 2',	3,	'998465467688',	'sdndfnb@djg.ru',	NULL,	NULL,	NULL,	3,	1,	0,	'',	1701389901,	'hae9gWFPPuJ6',	'crAQDBLYhT9w',	'totbqfyxs93Z',	1,	0,	1,	0,	1,	'{\"1\": \"Гипсокартон\", \"3\": \"Шпаклевка\", \"8\": \"Плиточный клей\", \"9\": \"Наливной пол и Гидроизоляция\"}',	0);

-- 2023-12-20 11:26:58
