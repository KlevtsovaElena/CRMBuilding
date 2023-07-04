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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `brands` (`id`, `brand_name`) VALUES
(1,	'Форус'),
(2,	'AZIA'),
(3,	'КНАУФ'),
(4,	'VERO'),
(5,	'Maestro'),
(6,	'Элерон'),
(7,	'RED STONE VENTUM'),
(8,	'Fixer'),
(9,	'Fugen Knauf'),
(10,	'Braus'),
(11,	'Megamix'),
(12,	'Hayat'),
(13,	'ГФ-021');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `category_name`) VALUES
(1,	'Гипсокартон'),
(2,	'Штукатурка'),
(3,	'Шпаклевка'),
(4,	'Грунтовка');

DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cities` (`id`, `name`) VALUES
(1,	'Ташкент'),
(2,	'Нурафшон'),
(3,	'Бухара'),
(4,	'Самарканд'),
(5,	'Карши'),
(6,	'Термез'),
(7,	'Навои'),
(8,	'Джизак'),
(9,	'Гулистан'),
(10,	'Андижан'),
(11,	'Наманган'),
(12,	'Фергана'),
(13,	'Угренч'),
(14,	'Нукус');

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
(19,	'Лол',	'',	'79167625303',	12,	892205925,	'rodionaka');

DROP TABLE IF EXISTS `order_vendors`;
CREATE TABLE `order_vendors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `vendor_id` int unsigned NOT NULL,
  `products` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_vendors` (`id`, `order_id`, `vendor_id`, `products`, `status`) VALUES
(1,	124,	1,	'{\"15\":11,\"15\":21,\"5\":31}',	0),
(2,	125,	1,	'{\"1\":11,\"4\":21,\"5\":31}',	1),
(3,	126,	2,	'{\"1\":11,\"4\":21,\"5\":31}',	1),
(4,	126,	1,	'{\"1\":11,\"4\":21,\"5\":31}',	1),
(5,	127,	3,	'{\"1\":11,\"4\":21,\"5\":31}',	1),
(6,	127,	2,	'{\"1\":11,\"15\":21,\"5\":31}',	1),
(7,	127,	1,	'{\"10\":11,\"15\":1,\"5\":31}',	0),
(8,	128,	1,	'{\"1\":11,\"15\":21,\"5\":31}',	1),
(9,	129,	1,	'{\"10\":11,\"15\":1,\"5\":31}',	3),
(10,	130,	1,	'{\"1\":11,\"15\":21,\"5\":31}',	1),
(11,	131,	1,	'{\"10\":11,\"15\":1,\"5\":31}',	1),
(12,	132,	1,	'{\"1\":11,\"15\":21,\"5\":31}',	2),
(13,	133,	2,	'{\"10\":11,\"15\":1,\"5\":31}',	2),
(14,	133,	1,	'{\"10\":11,\"15\":1,\"5\":31}',	1),
(15,	134,	1,	'{\"30\":11,\"15\":21,\"5\":31}',	0),
(16,	134,	3,	'{\"10\":11,\"15\":1,\"5\":31}',	1),
(17,	135,	1,	'{\"20\":11,\"20\":21,\"5\":31}',	0),
(18,	136,	1,	'{\"10\":11,\"15\":1,\"5\":31}',	4);

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `order_date` int unsigned NOT NULL,
  `products` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `products`, `location`) VALUES
(124,	5,	1688313600,	'{\"13\":1,\"14\":2,\"15\":3}',	'{\"latitude\": 0, \"longitude\": 0}'),
(125,	19,	1688313660,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(126,	5,	1688313720,	'',	'{\"latitude\": 55.657107, \"longitude\": 37.569608}'),
(127,	19,	1688313780,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(128,	5,	1688313840,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(129,	19,	1688313900,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(130,	5,	1688313960,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(131,	19,	1688314020,	'',	'{\"latitude\": 55.657107, \"longitude\": 37.569608}'),
(132,	5,	1688314080,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}'),
(133,	19,	1688314140,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}'),
(134,	19,	1688314200,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}'),
(135,	19,	1688314260,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}'),
(136,	19,	1688314320,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}');

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
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `photo` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `article` int unsigned NOT NULL,
  `category_id` int unsigned NOT NULL,
  `brand_id` int unsigned NOT NULL,
  `vendor_id` int unsigned NOT NULL,
  `quantity_available` int unsigned NOT NULL,
  `price` int unsigned NOT NULL,
  `max_price` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `description`, `photo`, `article`, `category_id`, `brand_id`, `vendor_id`, `quantity_available`, `price`, `max_price`) VALUES
(1,	'Гипсокартон ФОРУС стеновой простой (12.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111111,	1,	1,	1,	555,	49000,	51000),
(2,	'Гипсокартон ФОРУС стеновой влагостойкий (12.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111112,	1,	1,	2,	444,	51000,	55000),
(3,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	2222222,	1,	1,	1,	4,	50000,	53000),
(4,	'Гипсокартон ФОРУС стеновой простой (12.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	5555555,	1,	1,	2,	222,	56000,	66000),
(5,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	99999,	1,	1,	1,	100,	58000,	59000),
(6,	'Гипсокартон ФОРУС потолочный простой (9.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	99999,	1,	1,	1,	100,	49000,	52000),
(7,	'Гипсокартон ФОРУС стеновой влагастойкий (12.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	99999,	1,	1,	1,	100,	63000,	64500),
(8,	'Гипсокартон AZIA потолочный простой (9.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	49000,	53000),
(9,	'Гипсокартон AZIA стеновой простой (12.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	56000,	57000),
(10,	'Гипсокартон AZIA потолочный влагостойкий (9.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	60000,	65000),
(11,	'Гипсокартон AZIA стеновой влагостойкий (12.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	69000,	72000),
(12,	'Гипсокартон КНАУФ потолочный простой (9.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	52200,	56700),
(13,	'Гипсокартон КНАУФ стеновой простой (12.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	59300,	64000),
(14,	'Гипсокартон КНАУФ потолочный влагостойкий (9.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	67300,	70000),
(15,	'Гипсокартон КНАУФ стеновой влагостойкий (12.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	74200,	80000),
(16,	'Гипсокартон VERO стеновой влагостойкий (12.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	99999,	1,	4,	1,	100,	46000,	50000),
(17,	'Гипсокартон VERO стеновой простой (12.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	99999,	1,	4,	1,	100,	46500,	50000),
(18,	'Гипсокартон VERO потолочный простой (8.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	99999,	1,	4,	1,	100,	41500,	50000),
(19,	'Фасадная Шпаклевка Усиленная (Maestro 25кг)',	'Описание',	'https://postroy-novoe.ru/upload/iblock/12b/12b9cbaa64094370e8fdd69b9235c726.jpeg',	99999,	3,	5,	1,	100,	165000,	170000),
(20,	'Шпаклевка ЭЛЕРОН Травертин',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	95000,	100000),
(21,	'Шпаклёвка гипсовая ЭЛЕРОН 01 (20 кг)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	32500,	43000),
(22,	'Шпаклевка фасадная Элерон белая С-003, 20кг',	'Описание',	'https://postroy-novoe.ru/upload/iblock/12b/12b9cbaa64094370e8fdd69b9235c726.jpeg',	99999,	3,	6,	1,	100,	57300,	61000),
(23,	'Шпаклевочная смесь ЭЛЕРОН УНИВЕРСАЛ (06)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	28000,	32000),
(24,	'Шпаклёвка гипсовая ЭЛЕРОН 01',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	34000,	38000),
(25,	'Шпаклёвка фасадная ЭЛЕРОН 03 (чёрная/25 кг)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	44500,	50000),
(26,	'Шпаклёвка гипсовая ЭЛЕРОН 04',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	32000,	37000),
(27,	'Шпаклевочная смесь ЭЛЕРОН 03 Fasad серый 20кг',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	44700,	50000),
(28,	'Шпаклёвка гипсовая ЭЛЕРОН 01',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	34700,	39000),
(29,	'Декоративная шпатлевка RED STONE VENTUM',	'Описание',	'https://ventumplus.com/img_product/38.jpg?v=2',	99999,	3,	7,	1,	100,	142000,	144000),
(30,	'Шпаклевка Fixer',	'Описание',	'https://stroyinvest-market.ru/upload/resize_cache/iblock/2cd/l9fka5mfrm5it1gjnnpxql3d05877pmo/800_800_0/6b218b7d_385b_11e6_b1dc_000c2926fe71_3cc3fbaa_54ca_11eb_80cb_3cecef01db2d.resize1.jpg',	99999,	3,	8,	1,	100,	30000,	33000),
(31,	'Шпаклевка гипсовая Fugen Knauf',	'Описание',	'https://cdn.leroymerlin.ru/lmru/image/upload/v1656334021/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/Xz_s07vVf0mj2kodJguxPg/81975392.png',	99999,	3,	9,	1,	100,	57000,	61000),
(32,	'Braus Шпаклевка 05 (20кг)',	'Описание',	'https://www.prom.uz/_ipx/f_webp/https://devel.prom.uz/upload/products/97/75/9775f5c60f66adea07f2e7b0ef5fb7ff.PNG',	99999,	3,	10,	1,	100,	28000,	29500),
(33,	'Грунтовка HAYAT 1 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	99999,	4,	12,	1,	100,	35000,	37000),
(34,	'Грунтовка HAYAT 5 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	99999,	4,	12,	1,	100,	115000,	121000),
(35,	'Грунтовка HAYAT 2,8 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	99999,	4,	12,	1,	100,	65000,	70000),
(36,	'Грунтовка Fixer 3 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	99999,	4,	8,	1,	100,	40000,	41500),
(37,	'Грунтовка Fixer 1 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	99999,	4,	8,	1,	100,	20000,	23000),
(38,	'Грунтовка Fixer 5 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	99999,	4,	8,	1,	100,	75000,	76500),
(39,	'Megamix грунтовка 1.0 фиолетовый',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	60000,	70000),
(40,	'Megamix грунтовка 1.0 эко',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	55000,	60000),
(41,	'Megamix грунтовка 2.5 эко ок,',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	125000,	131000),
(42,	'Megamix грунтовка 2.5 фиолетовый',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	135000,	140000),
(43,	'ГРУНТОВКА ГФ-021 АЛКИДНАЯ',	'Описание',	'https://lakokraska-ya.ru/images/items/396.png',	99999,	4,	13,	1,	100,	45000,	50000),
(44,	'Грунтовка Акриловая (Maestro 5кг)',	'Описание',	'https://marshall-paints.ru/upload/image/9L_Maestro_Facade_Grunt.png',	99999,	4,	11,	1,	100,	85000,	90000);

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
  `unique_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vendors` (`id`, `name`, `city_id`, `phone`, `email`, `tg_username`, `tg_id`, `coordinates`, `role`, `comment`, `date_reg`, `unique_id`, `is_active`) VALUES
(1,	'Поставщик',	4,	NULL,	'ffff@gmail.hjhjh',	NULL,	NULL,	'{\"latitude\": 44.657107, \"longitude\": 32.569608}',	2,	NULL,	1688481000,	'',	1),
(2,	'Поставщик 2',	1,	'123213',	'test@vendor.ru',	'Vendor TG',	1111,	'{\"latitude\": 55.657107, \"longitude\": 37.569608}',	1,	NULL,	1688481020,	'',	1);

-- 2023-07-04 14:44:28
