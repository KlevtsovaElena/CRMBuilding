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
  `phone` smallint unsigned DEFAULT NULL,
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
  `tg_username` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tg_id` bigint unsigned NOT NULL,
  `phone` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `city_id` int unsigned NOT NULL,
  `coordinates` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `tg_username`, `tg_id`, `phone`, `city_id`, `coordinates`) VALUES
(5,	'Uchken',	NULL,	'uch',	32432533464,	'1007545645',	5,	NULL),
(19,	'Лол',	'',	'rodionaka',	892205925,	'79167625303',	12,	NULL);

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `order_date` int NOT NULL,
  `product_id` int NOT NULL,
  `location` json NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `product_id`, `location`) VALUES
(124,	892205925,	1687282054,	0,	'null'),
(125,	892205925,	1687282279,	0,	'null'),
(130,	892205925,	1687300121,	43,	'{\"Latitude\": 55.657107, \"Longitude\": 37.569608}'),
(131,	892205925,	1687300310,	43,	'{\"Latitude\": 0, \"Longitude\": 0}'),
(132,	892205925,	1687300310,	43,	'{\"Latitude\": 0, \"Longitude\": 0}'),
(133,	892205925,	1687300310,	43,	'{\"Latitude\": 0, \"Longitude\": 0}'),
(134,	892205925,	1687300807,	35,	'{\"Latitude\": 0, \"Longitude\": 0}'),
(135,	892205925,	1687301289,	43,	'{\"Latitude\": 55.657107, \"Longitude\": 37.569608}'),
(136,	892205925,	1687346529,	41,	'{\"Latitude\": 55.657087, \"Longitude\": 37.569581}'),
(137,	892205925,	1687346529,	44,	'{\"Latitude\": 55.657087, \"Longitude\": 37.569581}');

DROP TABLE IF EXISTS `price_changes`;
CREATE TABLE `price_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `date_change` timestamp NOT NULL,
  `new_price` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `photo` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `article` int unsigned DEFAULT NULL,
  `category_id` int unsigned NOT NULL,
  `brand_id` int unsigned DEFAULT NULL,
  `vendor_id` int unsigned DEFAULT NULL,
  `quantity_available` int unsigned DEFAULT NULL,
  `price` int unsigned DEFAULT NULL,
  `max_price` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `description`, `photo`, `article`, `category_id`, `brand_id`, `vendor_id`, `quantity_available`, `price`, `max_price`) VALUES
(1,	'Гипсокартон ФОРУС стеновой простой (12.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	NULL,	1,	1,	NULL,	NULL,	49000,	50000),
(2,	'Гипсокартон ФОРУС стеновой влагостойкий (12.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	NULL,	1,	1,	NULL,	NULL,	51000,	50000),
(3,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	NULL,	1,	1,	NULL,	NULL,	50000,	50000),
(4,	'Гипсокартон ФОРУС стеновой простой (12.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	NULL,	1,	1,	NULL,	NULL,	56000,	50000),
(5,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	NULL,	1,	1,	NULL,	NULL,	58000,	50000),
(6,	'Гипсокартон ФОРУС потолочный простой (9.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	NULL,	1,	1,	NULL,	NULL,	49000,	50000),
(7,	'Гипсокартон ФОРУС стеновой влагастойкий (12.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	NULL,	1,	1,	NULL,	NULL,	63000,	50000),
(8,	'Гипсокартон AZIA потолочный простой (9.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	NULL,	1,	2,	NULL,	NULL,	49000,	50000),
(9,	'Гипсокартон AZIA стеновой простой (12.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	NULL,	1,	2,	NULL,	NULL,	56000,	50000),
(10,	'Гипсокартон AZIA потолочный влагостойкий (9.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	NULL,	1,	2,	NULL,	NULL,	60000,	50000),
(11,	'Гипсокартон AZIA стеновой влагостойкий (12.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	NULL,	1,	2,	NULL,	NULL,	69000,	50000),
(12,	'Гипсокартон КНАУФ потолочный простой (9.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	NULL,	1,	3,	NULL,	NULL,	52200,	50000),
(13,	'Гипсокартон КНАУФ стеновой простой (12.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	NULL,	1,	3,	NULL,	NULL,	59300,	50000),
(14,	'Гипсокартон КНАУФ потолочный влагостойкий (9.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	NULL,	1,	3,	NULL,	NULL,	67300,	50000),
(15,	'Гипсокартон КНАУФ стеновой влагостойкий (12.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	NULL,	1,	3,	NULL,	NULL,	74200,	50000),
(16,	'Гипсокартон VERO стеновой влагостойкий (12.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	NULL,	1,	4,	NULL,	NULL,	46000,	50000),
(17,	'Гипсокартон VERO стеновой простой (12.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	NULL,	1,	4,	NULL,	NULL,	46500,	50000),
(18,	'Гипсокартон VERO потолочный простой (8.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	NULL,	1,	4,	NULL,	NULL,	41500,	50000),
(19,	'Фасадная Шпаклевка Усиленная (Maestro 25кг)',	'Описание',	'https://postroy-novoe.ru/upload/iblock/12b/12b9cbaa64094370e8fdd69b9235c726.jpeg',	NULL,	3,	5,	NULL,	NULL,	165000,	50000),
(20,	'Шпаклевка ЭЛЕРОН Травертин',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	NULL,	3,	6,	NULL,	NULL,	95000,	50000),
(21,	'Шпаклёвка гипсовая ЭЛЕРОН 01 (20 кг)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	NULL,	3,	6,	NULL,	NULL,	32500,	50000),
(22,	'Шпаклевка фасадная Элерон белая С-003, 20кг',	'Описание',	'https://postroy-novoe.ru/upload/iblock/12b/12b9cbaa64094370e8fdd69b9235c726.jpeg',	NULL,	3,	6,	NULL,	NULL,	57300,	50000),
(23,	'Шпаклевочная смесь ЭЛЕРОН УНИВЕРСАЛ (06)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	NULL,	3,	6,	NULL,	NULL,	28000,	50000),
(24,	'Шпаклёвка гипсовая ЭЛЕРОН 01',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	NULL,	3,	6,	NULL,	NULL,	34000,	50000),
(25,	'Шпаклёвка фасадная ЭЛЕРОН 03 (чёрная/25 кг)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	NULL,	3,	6,	NULL,	NULL,	44500,	50000),
(26,	'Шпаклёвка гипсовая ЭЛЕРОН 04',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	NULL,	3,	6,	NULL,	NULL,	32000,	50000),
(27,	'Шпаклевочная смесь ЭЛЕРОН 03 Fasad серый 20кг',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	NULL,	3,	6,	NULL,	NULL,	44700,	50000),
(28,	'Шпаклёвка гипсовая ЭЛЕРОН 01',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	NULL,	3,	6,	NULL,	NULL,	34700,	50000),
(29,	'Декоративная шпатлевка RED STONE VENTUM',	'Описание',	'https://ventumplus.com/img_product/38.jpg?v=2',	NULL,	3,	7,	NULL,	NULL,	142000,	50000),
(30,	'Шпаклевка Fixer',	'Описание',	'https://stroyinvest-market.ru/upload/resize_cache/iblock/2cd/l9fka5mfrm5it1gjnnpxql3d05877pmo/800_800_0/6b218b7d_385b_11e6_b1dc_000c2926fe71_3cc3fbaa_54ca_11eb_80cb_3cecef01db2d.resize1.jpg',	NULL,	3,	8,	NULL,	NULL,	30000,	50000),
(31,	'Шпаклевка гипсовая Fugen Knauf',	'Описание',	'https://cdn.leroymerlin.ru/lmru/image/upload/v1656334021/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/Xz_s07vVf0mj2kodJguxPg/81975392.png',	NULL,	3,	9,	NULL,	NULL,	57000,	50000),
(32,	'Braus Шпаклевка 05 (20кг)',	'Описание',	'https://www.prom.uz/_ipx/f_webp/https://devel.prom.uz/upload/products/97/75/9775f5c60f66adea07f2e7b0ef5fb7ff.PNG',	NULL,	3,	10,	NULL,	NULL,	28000,	50000),
(33,	'Грунтовка HAYAT 1 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	NULL,	4,	12,	NULL,	NULL,	35000,	50000),
(34,	'Грунтовка HAYAT 5 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	NULL,	4,	12,	NULL,	NULL,	115000,	50000),
(35,	'Грунтовка HAYAT 2,8 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	NULL,	4,	12,	NULL,	NULL,	65000,	50000),
(36,	'Грунтовка Fixer 3 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	NULL,	4,	8,	NULL,	NULL,	40000,	50000),
(37,	'Грунтовка Fixer 1 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	NULL,	4,	8,	NULL,	NULL,	20000,	50000),
(38,	'Грунтовка Fixer 5 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	NULL,	4,	8,	NULL,	NULL,	75000,	50000),
(39,	'Megamix грунтовка 1.0 фиолетовый',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	NULL,	4,	11,	NULL,	NULL,	60000,	50000),
(40,	'Megamix грунтовка 1.0 эко',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	NULL,	4,	11,	NULL,	NULL,	55000,	50000),
(41,	'Megamix грунтовка 2.5 эко ок,',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	NULL,	4,	11,	NULL,	NULL,	125000,	50000),
(42,	'Megamix грунтовка 2.5 фиолетовый',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	NULL,	4,	11,	NULL,	NULL,	135000,	50000),
(43,	'ГРУНТОВКА ГФ-021 АЛКИДНАЯ',	'Описание',	'https://lakokraska-ya.ru/images/items/396.png',	NULL,	4,	13,	NULL,	NULL,	45000,	50000),
(44,	'Грунтовка Акриловая (Maestro 5кг)',	'Описание',	'https://marshall-paints.ru/upload/image/9L_Maestro_Facade_Grunt.png',	NULL,	4,	11,	NULL,	NULL,	85000,	50000);

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `city_id` int unsigned NOT NULL,
  `phone` smallint unsigned DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tg_username` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tg_id` bigint unsigned DEFAULT NULL,
  `coordinates` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vendors` (`id`, `name`, `city_id`, `phone`, `email`, `tg_username`, `tg_id`, `coordinates`) VALUES
(1,	'Поставщик',	4,	NULL,	NULL,	NULL,	NULL,	NULL);

-- 2023-06-21 11:47:44