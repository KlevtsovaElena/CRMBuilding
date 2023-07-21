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
(4,	'VERO',	0),
(5,	'Maestro',	0),
(6,	'Элерон',	0),
(7,	'RED STONE VENTUM',	0),
(8,	'Fixer',	0),
(9,	'Fugen Knauf',	0),
(10,	'Braus',	0),
(11,	'Megamix',	0),
(12,	'Hayat',	0),
(13,	'ГФ-021',	0);

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
(19,	'Лол',	'',	'+79167625303',	1,	892205925,	'rodionaka');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_vendors` (`id`, `order_id`, `vendor_id`, `products`, `status`, `archive`, `total_price`, `distance`) VALUES
(1,	124,	1,	'{\"15\":11,\"15\":21,\"5\":31}',	0,	0,	0,	0),
(2,	125,	1,	'{\"1\":11,\"4\":21,\"5\":31}',	1,	0,	0,	0),
(3,	126,	2,	'{\"1\":11,\"4\":21,\"5\":31}',	1,	0,	0,	0),
(4,	126,	1,	'{\"1\":11,\"4\":21,\"5\":31}',	1,	0,	0,	0),
(5,	127,	3,	'{\"1\":11,\"4\":21,\"5\":31}',	1,	0,	0,	0),
(6,	127,	2,	'{\"1\":11,\"15\":21,\"5\":31}',	1,	0,	0,	0),
(7,	127,	1,	'{\"10\":11,\"15\":1,\"5\":31}',	4,	1,	0,	0),
(8,	128,	1,	'{\"1\":11,\"15\":21,\"5\":31}',	1,	0,	0,	0),
(9,	129,	1,	'{\"10\":11,\"15\":1,\"5\":31}',	3,	1,	0,	0),
(10,	130,	1,	'{\"1\":11,\"15\":21,\"5\":31}',	1,	0,	0,	0),
(11,	131,	1,	'{\"10\":11,\"15\":1,\"5\":31}',	1,	0,	0,	0),
(12,	132,	1,	'{\"1\":11,\"15\":21,\"5\":31}',	2,	0,	0,	0),
(13,	133,	2,	'{\"10\":11,\"15\":1,\"5\":31}',	2,	0,	0,	0),
(14,	133,	1,	'{\"10\":11,\"15\":1,\"5\":31}',	1,	0,	0,	0),
(15,	134,	1,	'{\"30\":11,\"15\":21,\"5\":31}',	0,	0,	0,	0),
(16,	134,	3,	'{\"10\":11,\"15\":1,\"5\":31}',	1,	0,	0,	0),
(17,	135,	1,	'{\"20\":11,\"20\":21,\"5\":31}',	0,	0,	0,	0),
(18,	136,	1,	'{\"10\":11,\"15\":1,\"5\":31}',	4,	0,	0,	0),
(19,	137,	1,	'{\"43\":1,\"5\":10,\"6\":21,\"7\":1}',	0,	0,	0,	0),
(20,	138,	1,	'{\"43\":1,\"5\":10,\"6\":42,\"7\":33}',	0,	0,	0,	0);

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
(124,	5,	1688313600,	'{\"13\":1,\"14\":2,\"15\":3}',	'{\"latitude\": 0, \"longitude\": 0}',	0),
(125,	19,	1688313660,	'',	'{\"latitude\": 0, \"longitude\": 0}',	0),
(126,	5,	1688313720,	'',	'{\"latitude\": 55.657107, \"longitude\": 37.569608}',	0),
(127,	19,	1688313780,	'',	'{\"latitude\": 0, \"longitude\": 0}',	0),
(128,	5,	1688313840,	'',	'{\"latitude\": 0, \"longitude\": 0}',	0),
(129,	19,	1688313900,	'',	'{\"latitude\": 0, \"longitude\": 0}',	0),
(130,	5,	1688313960,	'',	'{\"latitude\": 0, \"longitude\": 0}',	0),
(131,	19,	1688314020,	'',	'{\"latitude\": 55.657107, \"longitude\": 37.569608}',	0),
(132,	5,	1688314080,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}',	0),
(133,	19,	1688314140,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}',	0),
(134,	19,	1688314200,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}',	0),
(135,	19,	1688314260,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}',	0),
(136,	19,	1688314320,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}',	0),
(137,	19,	1689341846,	'{\"43\":1,\"5\":10,\"6\":21,\"7\":1}',	'{\"latitude\":0,\"longitude\":0}',	0),
(138,	19,	1689376004,	'{\"43\":1,\"5\":10,\"6\":42,\"7\":33}',	'{\"latitude\":0,\"longitude\":0}',	0);

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
  `max_price` int unsigned NOT NULL,
  `unit_id` tinyint unsigned NOT NULL,
  `deleted` tinyint unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `description`, `photo`, `article`, `category_id`, `brand_id`, `vendor_id`, `quantity_available`, `price`, `max_price`, `unit_id`, `deleted`) VALUES
(1,	'Гипсокартон ФОРУС стеновой простой (12.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111111,	1,	1,	1,	555,	49000,	51000,	6,	0),
(2,	'Гипсокартон ФОРУС стеновой влагостойкий (12.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111112,	1,	1,	2,	444,	51000,	55000,	1,	0),
(3,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	2222222,	1,	1,	1,	4,	50000,	53000,	2,	0),
(4,	'Гипсокартон ФОРУС стеновой простой (12.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	5555555,	1,	1,	2,	222,	56000,	66000,	1,	0),
(5,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	99999,	1,	1,	1,	100,	58000,	59000,	3,	0),
(6,	'Гипсокартон ФОРУС потолочный простой (9.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	99999,	1,	1,	1,	100,	49000,	52000,	4,	0),
(7,	'Гипсокартон ФОРУС стеновой влагастойкий (12.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	99999,	1,	1,	1,	100,	63000,	64500,	1,	0),
(8,	'Гипсокартон AZIA потолочный простой (9.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	49000,	53000,	5,	0),
(9,	'Гипсокартон AZIA стеновой простой (12.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	56000,	57000,	1,	0),
(10,	'Гипсокартон AZIA потолочный влагостойкий (9.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	60000,	65000,	1,	0),
(11,	'Гипсокартон AZIA стеновой влагостойкий (12.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	69000,	72000,	6,	0),
(12,	'Гипсокартон КНАУФ потолочный простой (9.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	52200,	56700,	1,	0),
(13,	'Гипсокартон КНАУФ стеновой простой (12.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	59300,	64000,	1,	0),
(14,	'Гипсокартон КНАУФ потолочный влагостойкий (9.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	67300,	70000,	1,	0),
(15,	'Гипсокартон КНАУФ стеновой влагостойкий (12.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	74200,	80000,	1,	0),
(16,	'Гипсокартон VERO стеновой влагостойкий (12.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	99999,	1,	4,	1,	100,	46000,	50000,	1,	0),
(17,	'Гипсокартон VERO стеновой простой (12.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	99999,	1,	4,	1,	100,	46500,	50000,	1,	0),
(18,	'Гипсокартон VERO потолочный простой (8.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	99999,	1,	4,	1,	100,	41500,	50000,	1,	0),
(19,	'Фасадная Шпаклевка Усиленная (Maestro 25кг)',	'Описание',	'https://postroy-novoe.ru/upload/iblock/12b/12b9cbaa64094370e8fdd69b9235c726.jpeg',	99999,	3,	5,	1,	100,	165000,	170000,	1,	0),
(20,	'Шпаклевка ЭЛЕРОН Травертин',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	95000,	100000,	1,	0),
(21,	'Шпаклёвка гипсовая ЭЛЕРОН 01 (20 кг)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	32500,	43000,	5,	0),
(22,	'Шпаклевка фасадная Элерон белая С-003, 20кг',	'Описание',	'https://postroy-novoe.ru/upload/iblock/12b/12b9cbaa64094370e8fdd69b9235c726.jpeg',	99999,	3,	6,	1,	100,	57300,	61000,	1,	0),
(23,	'Шпаклевочная смесь ЭЛЕРОН УНИВЕРСАЛ (06)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	28000,	32000,	1,	0),
(24,	'Шпаклёвка гипсовая ЭЛЕРОН 01',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	34000,	38000,	1,	0),
(25,	'Шпаклёвка фасадная ЭЛЕРОН 03 (чёрная/25 кг)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	44500,	50000,	1,	0),
(26,	'Шпаклёвка гипсовая ЭЛЕРОН 04',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	32000,	37000,	1,	0),
(27,	'Шпаклевочная смесь ЭЛЕРОН 03 Fasad серый 20кг',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	44700,	50000,	1,	0),
(28,	'Шпаклёвка гипсовая ЭЛЕРОН 01',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	34700,	39000,	4,	0),
(29,	'Декоративная шпатлевка RED STONE VENTUM',	'Описание',	'https://ventumplus.com/img_product/38.jpg?v=2',	99999,	3,	7,	1,	100,	142000,	144000,	1,	0),
(30,	'Шпаклевка Fixer',	'Описание',	'https://stroyinvest-market.ru/upload/resize_cache/iblock/2cd/l9fka5mfrm5it1gjnnpxql3d05877pmo/800_800_0/6b218b7d_385b_11e6_b1dc_000c2926fe71_3cc3fbaa_54ca_11eb_80cb_3cecef01db2d.resize1.jpg',	99999,	3,	8,	1,	100,	30000,	33000,	1,	0),
(31,	'Шпаклевка гипсовая Fugen Knauf',	'Описание',	'https://cdn.leroymerlin.ru/lmru/image/upload/v1656334021/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/Xz_s07vVf0mj2kodJguxPg/81975392.png',	99999,	3,	9,	1,	100,	57000,	61000,	1,	0),
(32,	'Braus Шпаклевка 05 (20кг)',	'Описание',	'https://www.prom.uz/_ipx/f_webp/https://devel.prom.uz/upload/products/97/75/9775f5c60f66adea07f2e7b0ef5fb7ff.PNG',	99999,	3,	10,	1,	100,	28000,	29500,	1,	0),
(33,	'Грунтовка HAYAT 1 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	99999,	4,	12,	1,	100,	35000,	37000,	1,	0),
(34,	'Грунтовка HAYAT 5 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	99999,	4,	12,	1,	100,	115000,	121000,	1,	0),
(35,	'Грунтовка HAYAT 2,8 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	99999,	4,	12,	1,	100,	65000,	70000,	1,	0),
(36,	'Грунтовка Fixer 3 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	99999,	4,	8,	1,	100,	40000,	41500,	7,	0),
(37,	'Грунтовка Fixer 1 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	99999,	4,	8,	1,	100,	20000,	23000,	1,	0),
(38,	'Грунтовка Fixer 5 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	99999,	4,	8,	1,	100,	75000,	76500,	1,	0),
(39,	'Megamix грунтовка 1.0 фиолетовый',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	60000,	70000,	1,	0),
(40,	'Megamix грунтовка 1.0 эко',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	55000,	60000,	1,	0),
(41,	'Megamix грунтовка 2.5 эко ок,',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	125000,	131000,	1,	0),
(42,	'Megamix грунтовка 2.5 фиолетовый',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	135000,	140000,	1,	0),
(43,	'ГРУНТОВКА ГФ-021 АЛКИДНАЯ',	'Описание',	'https://lakokraska-ya.ru/images/items/396.png',	99999,	4,	13,	1,	100,	45000,	50000,	1,	0),
(44,	'Грунтовка Акриловая (Maestro 5кг)',	'Описание',	'https://marshall-paints.ru/upload/image/9L_Maestro_Facade_Grunt.png',	99999,	4,	11,	1,	100,	85000,	90000,	1,	0);

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
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_reg` bigint NOT NULL,
  `hash_string` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint NOT NULL,
  `password` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deleted` tinyint unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vendors` (`id`, `name`, `city_id`, `phone`, `email`, `tg_username`, `tg_id`, `coordinates`, `role`, `comment`, `date_reg`, `hash_string`, `is_active`, `password`, `token`, `deleted`) VALUES
(1,	'Поставщик1',	1,	'79996667788',	'first@bk.ru',	NULL,	NULL,	NULL,	2,	'Первый поставщик',	1689507890,	'haVuGqrLCiM1A',	1,	'crK8GaB5k/z6A',	'',	0),
(2,	'Поставщик2',	2,	'79168881122',	'second@bk.ru',	NULL,	NULL,	NULL,	2,	'Второй поставщик',	1689507982,	'haMCdWzHNM9hc',	1,	'crF3z6ZLaP79c',	'',	0),
(3,	'Поставщик3',	3,	'76663334455',	'third@bk.ru',	NULL,	NULL,	NULL,	2,	'Третий поставщик',	1689508041,	'hahUrbGggM/Kc',	1,	'cr9Oe/o1K7r0o',	'',	0),
(4,	'Поставщик4',	4,	'71117770099',	'fourth@bk.ru',	NULL,	NULL,	NULL,	2,	'Четвёртый постащик',	1689508156,	'haa5ulKzPo6g6',	1,	'crtdJGYGWRn1k',	'',	0),
(5,	'Админ',	5,	'77777777777',	'admin@bk.ru',	NULL,	NULL,	NULL,	1,	'Админ',	1688636888,	'hazetypXJkIIk',	1,	'vendor',	'toT8NRYasABqk',	0);

-- 2023-07-17 14:30:07