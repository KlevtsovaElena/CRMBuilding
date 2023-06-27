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
  `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `city_id` int unsigned NOT NULL,
  `tg_id` bigint unsigned NOT NULL,
  `tg_username` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `phone`, `city_id`, `tg_id`, `tg_username`) VALUES
(5,	'Uchken',	NULL,	'1007545645',	5,	32432533464,	'uch'),
(19,	'Лол',	'',	'79167625303',	12,	892205925,	'rodionaka');
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
  `order_id` int unsigned NOT NULL,
  `vendor_id` int unsigned NOT NULL,
  `products` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_vendors` (`id`, `order_id`, `vendor_id`, `products`, `status`) VALUES
(3,	124,	1,	'{\"1\":11,\"4\":21,\"5\":31}',	1),
(4,	1,	2,	'{\"1\":11,\"4\":21,\"5\":31}',	1),
(5,	1,	2,	'{\"1\":11,\"4\":21,\"5\":31}',	1),
(6,	1,	2,	'[]',	1),
(7,	1,	2,	'',	1);
INSERT INTO `order_vendors` (`id`, `order_id`, `vendor_id`, `products`, `status`) VALUES
(3,	124,	1,	'{\"1\":11,\"4\":21,\"5\":31}',	1),
(4,	1,	2,	'{\"1\":11,\"4\":21,\"5\":31}',	1),
(5,	1,	2,	'{\"1\":11,\"4\":21,\"5\":31}',	1),
(6,	1,	2,	'[]',	1),
(7,	1,	2,	'',	1);

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `order_date` int unsigned NOT NULL,
  `products` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `products` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `location` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `products`, `location`) VALUES
(124,	892205925,	1687282054,	'{\"13\":1,\"14\":2,\"15\":3}',	'{\"latitude\": 0, \"longitude\": 0}'),
(125,	892205925,	1687282279,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(130,	892205925,	1687300121,	'',	'{\"latitude\": 55.657107, \"longitude\": 37.569608}'),
(131,	892205925,	1687300310,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(132,	892205925,	1687300310,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(133,	892205925,	1687300310,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(134,	892205925,	1687300807,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(135,	892205925,	1687301289,	'',	'{\"latitude\": 55.657107, \"longitude\": 37.569608}'),
(136,	892205925,	1687346529,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}'),
(137,	892205925,	1687346529,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}');
INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `products`, `location`) VALUES
(124,	892205925,	1687282054,	'{\"13\":1,\"14\":2,\"15\":3}',	'{\"latitude\": 0, \"longitude\": 0}'),
(125,	892205925,	1687282279,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(130,	892205925,	1687300121,	'',	'{\"latitude\": 55.657107, \"longitude\": 37.569608}'),
(131,	892205925,	1687300310,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(132,	892205925,	1687300310,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(133,	892205925,	1687300310,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(134,	892205925,	1687300807,	'',	'{\"latitude\": 0, \"longitude\": 0}'),
(135,	892205925,	1687301289,	'',	'{\"latitude\": 55.657107, \"longitude\": 37.569608}'),
(136,	892205925,	1687346529,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}'),
(137,	892205925,	1687346529,	'',	'{\"latitude\": 55.657087, \"longitude\": 37.569581}');

DROP TABLE IF EXISTS `price_changes`;
CREATE TABLE `price_changes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `date_change` int unsigned NOT NULL,
  `old_price` int unsigned DEFAULT NULL,
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
  `brand_id` int unsigned NOT NULL,
  `vendor_id` int unsigned NOT NULL,
  `quantity_available` int unsigned NOT NULL,
  `price` int unsigned NOT NULL,
  `max_price` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `description`, `photo`, `article`, `category_id`, `brand_id`, `vendor_id`, `quantity_available`, `price`, `max_price`) VALUES
(1,	'Гипсокартон ФОРУС стеновой простой (12.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111111,	1,	1,	1,	555,	49000,	50000),
(2,	'Гипсокартон ФОРУС стеновой влагостойкий (12.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111112,	1,	1,	2,	444,	51000,	50000),
(3,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм) (2 сорт)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	2222222,	1,	1,	1,	4,	50000,	50000),
(4,	'Гипсокартон ФОРУС стеновой простой (12.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	5555555,	1,	1,	2,	222,	56000,	50000),
(5,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	99999,	1,	1,	1,	100,	58000,	50000),
(6,	'Гипсокартон ФОРУС потолочный простой (9.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	99999,	1,	1,	1,	100,	49000,	50000),
(7,	'Гипсокартон ФОРУС стеновой влагастойкий (12.5мм)',	'Описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	99999,	1,	1,	1,	100,	63000,	50000),
(8,	'Гипсокартон AZIA потолочный простой (9.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	49000,	50000),
(9,	'Гипсокартон AZIA стеновой простой (12.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	56000,	50000),
(10,	'Гипсокартон AZIA потолочный влагостойкий (9.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	60000,	50000),
(11,	'Гипсокартон AZIA стеновой влагостойкий (12.5мм)',	'Описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	99999,	1,	2,	1,	100,	69000,	50000),
(12,	'Гипсокартон КНАУФ потолочный простой (9.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	52200,	50000),
(13,	'Гипсокартон КНАУФ стеновой простой (12.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	59300,	50000),
(14,	'Гипсокартон КНАУФ потолочный влагостойкий (9.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	67300,	50000),
(15,	'Гипсокартон КНАУФ стеновой влагостойкий (12.5мм)',	'Описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	99999,	1,	3,	1,	100,	74200,	50000),
(16,	'Гипсокартон VERO стеновой влагостойкий (12.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	99999,	1,	4,	1,	100,	46000,	50000),
(17,	'Гипсокартон VERO стеновой простой (12.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	99999,	1,	4,	1,	100,	46500,	50000),
(18,	'Гипсокартон VERO потолочный простой (8.5мм)',	'Описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	99999,	1,	4,	1,	100,	41500,	50000),
(19,	'Фасадная Шпаклевка Усиленная (Maestro 25кг)',	'Описание',	'https://postroy-novoe.ru/upload/iblock/12b/12b9cbaa64094370e8fdd69b9235c726.jpeg',	99999,	3,	5,	1,	100,	165000,	50000),
(20,	'Шпаклевка ЭЛЕРОН Травертин',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	95000,	50000),
(21,	'Шпаклёвка гипсовая ЭЛЕРОН 01 (20 кг)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	32500,	50000),
(22,	'Шпаклевка фасадная Элерон белая С-003, 20кг',	'Описание',	'https://postroy-novoe.ru/upload/iblock/12b/12b9cbaa64094370e8fdd69b9235c726.jpeg',	99999,	3,	6,	1,	100,	57300,	50000),
(23,	'Шпаклевочная смесь ЭЛЕРОН УНИВЕРСАЛ (06)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	28000,	50000),
(24,	'Шпаклёвка гипсовая ЭЛЕРОН 01',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	34000,	50000),
(25,	'Шпаклёвка фасадная ЭЛЕРОН 03 (чёрная/25 кг)',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	44500,	50000),
(26,	'Шпаклёвка гипсовая ЭЛЕРОН 04',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	32000,	50000),
(27,	'Шпаклевочная смесь ЭЛЕРОН 03 Fasad серый 20кг',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	44700,	50000),
(28,	'Шпаклёвка гипсовая ЭЛЕРОН 01',	'Описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	99999,	3,	6,	1,	100,	34700,	50000),
(29,	'Декоративная шпатлевка RED STONE VENTUM',	'Описание',	'https://ventumplus.com/img_product/38.jpg?v=2',	99999,	3,	7,	1,	100,	142000,	50000),
(30,	'Шпаклевка Fixer',	'Описание',	'https://stroyinvest-market.ru/upload/resize_cache/iblock/2cd/l9fka5mfrm5it1gjnnpxql3d05877pmo/800_800_0/6b218b7d_385b_11e6_b1dc_000c2926fe71_3cc3fbaa_54ca_11eb_80cb_3cecef01db2d.resize1.jpg',	99999,	3,	8,	1,	100,	30000,	50000),
(31,	'Шпаклевка гипсовая Fugen Knauf',	'Описание',	'https://cdn.leroymerlin.ru/lmru/image/upload/v1656334021/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/Xz_s07vVf0mj2kodJguxPg/81975392.png',	99999,	3,	9,	1,	100,	57000,	50000),
(32,	'Braus Шпаклевка 05 (20кг)',	'Описание',	'https://www.prom.uz/_ipx/f_webp/https://devel.prom.uz/upload/products/97/75/9775f5c60f66adea07f2e7b0ef5fb7ff.PNG',	99999,	3,	10,	1,	100,	28000,	50000),
(33,	'Грунтовка HAYAT 1 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	99999,	4,	12,	1,	100,	35000,	50000),
(34,	'Грунтовка HAYAT 5 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	99999,	4,	12,	1,	100,	115000,	50000),
(35,	'Грунтовка HAYAT 2,8 л',	'Описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	99999,	4,	12,	1,	100,	65000,	50000),
(36,	'Грунтовка Fixer 3 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	99999,	4,	8,	1,	100,	40000,	50000),
(37,	'Грунтовка Fixer 1 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	99999,	4,	8,	1,	100,	20000,	50000),
(38,	'Грунтовка Fixer 5 л',	'Описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	99999,	4,	8,	1,	100,	75000,	50000),
(39,	'Megamix грунтовка 1.0 фиолетовый',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	60000,	50000),
(40,	'Megamix грунтовка 1.0 эко',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	55000,	50000),
(41,	'Megamix грунтовка 2.5 эко ок,',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	125000,	50000),
(42,	'Megamix грунтовка 2.5 фиолетовый',	'Описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	99999,	4,	11,	1,	100,	135000,	50000),
(43,	'ГРУНТОВКА ГФ-021 АЛКИДНАЯ',	'Описание',	'https://lakokraska-ya.ru/images/items/396.png',	99999,	4,	13,	1,	100,	45000,	50000),
(44,	'Грунтовка Акриловая (Maestro 5кг)',	'Описание',	'https://marshall-paints.ru/upload/image/9L_Maestro_Facade_Grunt.png',	99999,	4,	11,	1,	100,	85000,	50000);

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `city_id` int unsigned NOT NULL,
  `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tg_username` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tg_id` bigint unsigned DEFAULT NULL,
  `coordinates` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `coordinates` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vendors` (`id`, `name`, `city_id`, `phone`, `email`, `tg_username`, `tg_id`, `coordinates`) VALUES
(1,	'Поставщик',	4,	NULL,	NULL,	NULL,	NULL,	NULL),
(2,	'Поставщик 2',	1,	'123213',	'test@vendor.ru',	'Vendor TG',	1111,	NULL);
(1,	'Поставщик',	4,	NULL,	NULL,	NULL,	NULL,	NULL),
(2,	'Поставщик 2',	1,	'123213',	'test@vendor.ru',	'Vendor TG',	1111,	NULL);

-- 2023-06-25 13:23:36