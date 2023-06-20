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
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
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
  `phone` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city_id` int unsigned NOT NULL,
  `coordinates` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `tg_username`, `tg_id`, `phone`, `city_id`, `coordinates`) VALUES
(5,	'Uchken',	'Sharipov',	'uch',	32432533464,	'999999999',	5,	'[55.807066, 37.458454]');

DROP TABLE IF EXISTS `ordered_products`;
CREATE TABLE `ordered_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned DEFAULT NULL,
  `product_id` int unsigned NOT NULL,
  `vendor_id` int unsigned DEFAULT NULL,
  `quantity` int unsigned NOT NULL,
  `status` tinyint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ordered_products` (`id`, `order_id`, `product_id`, `vendor_id`, `quantity`, `status`) VALUES
(1,	1,	3,	1,	2,	0),
(2,	1,	6,	1,	8,	0);

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `order_date` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `orders` (`id`, `customer_id`, `order_date`) VALUES
(1,	3524646543,	1687277498);

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
(1,	'Гипсокартон ФОРУС стеновой простой (12.5мм) (2 сорт)',	'описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111,	1,	1,	1,	100,	49000,	200000),
(2,	'Гипсокартон ФОРУС стеновой влагостойкий (12.5мм) (2 сорт)',	'описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111,	1,	1,	1,	100,	51000,	200000),
(3,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм) (2 сорт)',	'описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111,	1,	1,	1,	100,	50000,	200000),
(4,	'Гипсокартон ФОРУС стеновой простой (12.5мм)',	'описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111,	1,	1,	1,	100,	56000,	200000),
(5,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм)',	'описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111,	1,	1,	1,	100,	58000,	200000),
(6,	'Гипсокартон ФОРУС потолочный простой (9.5мм)',	'описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111,	1,	1,	1,	100,	49000,	200000),
(7,	'Гипсокартон ФОРУС стеновой влагастойкий (12.5мм)',	'описание',	'https://files.glotr.uz/company/000/032/664/products/2023/01/04/2023-01-04-20-13-40-311478-133a1ba7ad6c046ac3a323b45830b541.webp?_=ozbol',	1111,	1,	1,	1,	100,	63000,	200000),
(8,	'Гипсокартон AZIA потолочный простой (9.5мм)',	'описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	1111,	1,	2,	1,	100,	49000,	200000),
(9,	'Гипсокартон AZIA стеновой простой (12.5мм)',	'описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	1111,	1,	2,	1,	100,	56000,	200000),
(10,	'Гипсокартон AZIA потолочный влагостойкий (9.5мм)',	'описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	1111,	1,	2,	1,	100,	60000,	200000),
(11,	'Гипсокартон AZIA стеновой влагостойкий (12.5мм)',	'описание',	'https://devel.prom.uz/upload/product_logos/31/50/3150bb9521557b1ec6fccb615bc40b05.png',	1111,	1,	2,	1,	100,	69000,	200000),
(12,	'Гипсокартон КНАУФ потолочный простой (9.5мм)',	'описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	1111,	1,	3,	1,	100,	52200,	200000),
(13,	'Гипсокартон КНАУФ стеновой простой (12.5мм)',	'описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	1111,	1,	3,	1,	100,	59300,	200000),
(14,	'Гипсокартон КНАУФ потолочный влагостойкий (9.5мм)',	'описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	1111,	1,	3,	1,	100,	67300,	200000),
(15,	'Гипсокартон КНАУФ стеновой влагостойкий (12.5мм)',	'описание',	'https://himtorgkirov.ru/upload/iblock/871/87144fffbd1986e51362eb87abcb04a9.jpg',	1111,	1,	3,	1,	100,	74200,	200000),
(16,	'Гипсокартон VERO стеновой влагостойкий (12.5мм)',	'описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	1111,	1,	4,	1,	100,	46000,	200000),
(17,	'Гипсокартон VERO стеновой простой (12.5мм)',	'описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	1111,	1,	4,	1,	100,	46500,	200000),
(18,	'Гипсокартон VERO потолочный простой (8.5мм)',	'описание',	'https://samdoli.uz/upload-file/2022/02/24/1187/350x350-da23cbab-7f69-45a2-b990-f663c14770b4.jpg',	1111,	1,	4,	1,	100,	41500,	200000),
(19,	'Фасадная Шпаклевка Усиленная (Maestro 25кг)',	'описание',	'https://postroy-novoe.ru/upload/iblock/12b/12b9cbaa64094370e8fdd69b9235c726.jpeg',	1111,	3,	5,	1,	100,	165000,	200000),
(20,	'Шпаклевка ЭЛЕРОН Травертин',	'описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	1111,	3,	6,	1,	100,	95000,	200000),
(21,	'Шпаклёвка гипсовая ЭЛЕРОН 01 (20 кг)',	'описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	1111,	3,	6,	1,	100,	32500,	200000),
(22,	'Шпаклевка фасадная Элерон белая С-003, 20кг',	'описание',	'https://postroy-novoe.ru/upload/iblock/12b/12b9cbaa64094370e8fdd69b9235c726.jpeg',	1111,	3,	6,	1,	100,	57300,	200000),
(23,	'Шпаклевочная смесь ЭЛЕРОН УНИВЕРСАЛ (06)',	'описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	1111,	3,	6,	1,	100,	28000,	200000),
(24,	'Шпаклёвка гипсовая ЭЛЕРОН 01',	'описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	1111,	3,	6,	1,	100,	34000,	200000),
(25,	'Шпаклёвка фасадная ЭЛЕРОН 03 (чёрная/25 кг)',	'описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	1111,	3,	6,	1,	100,	44500,	200000),
(26,	'Шпаклёвка гипсовая ЭЛЕРОН 04',	'описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	1111,	3,	6,	1,	100,	32000,	200000),
(27,	'Шпаклевочная смесь ЭЛЕРОН 03 Fasad серый 20кг',	'описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	1111,	3,	6,	1,	100,	44700,	200000),
(28,	'Шпаклёвка гипсовая ЭЛЕРОН 01',	'описание',	'https://totaltools.tj/image/cache/catalog/i-500x500.jpg',	1111,	3,	6,	1,	100,	34700,	200000),
(29,	'Декоративная шпатлевка RED STONE VENTUM',	'описание',	'https://ventumplus.com/img_product/38.jpg?v=2',	1111,	3,	7,	1,	100,	142000,	200000),
(30,	'Шпаклевка Fixer',	'описание',	'https://stroyinvest-market.ru/upload/resize_cache/iblock/2cd/l9fka5mfrm5it1gjnnpxql3d05877pmo/800_800_0/6b218b7d_385b_11e6_b1dc_000c2926fe71_3cc3fbaa_54ca_11eb_80cb_3cecef01db2d.resize1.jpg',	1111,	3,	8,	1,	100,	30000,	200000),
(31,	'Шпаклевка гипсовая Fugen Knauf',	'описание',	'https://cdn.leroymerlin.ru/lmru/image/upload/v1656334021/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/Xz_s07vVf0mj2kodJguxPg/81975392.png',	1111,	3,	9,	1,	100,	57000,	200000),
(32,	'Braus Шпаклевка 05 (20кг)',	'описание',	'https://www.prom.uz/_ipx/f_webp/https://devel.prom.uz/upload/products/97/75/9775f5c60f66adea07f2e7b0ef5fb7ff.PNG',	1111,	3,	10,	1,	100,	28000,	200000),
(33,	'Грунтовка HAYAT 1 л',	'описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	1111,	4,	12,	1,	100,	35000,	200000),
(34,	'Грунтовка HAYAT 5 л',	'описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	1111,	4,	12,	1,	100,	115000,	200000),
(35,	'Грунтовка HAYAT 2,8 л',	'описание',	'https://roagt.com/img/p/1/7/7/8/1778-medium_default.jpg',	1111,	4,	12,	1,	100,	65000,	200000),
(36,	'Грунтовка Fixer 3 л',	'описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	1111,	4,	8,	1,	100,	40000,	200000),
(37,	'Грунтовка Fixer 1 л',	'описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	1111,	4,	8,	1,	100,	20000,	200000),
(38,	'Грунтовка Fixer 5 л',	'описание',	'https://www.rosotdelka.ru/materials/ASTI/fiks/fiks-asti-upakovka.jpg',	1111,	4,	8,	1,	100,	75000,	200000),
(39,	'Megamix грунтовка 1.0 фиолетовый',	'описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	1111,	4,	11,	1,	100,	60000,	200000),
(40,	'Megamix грунтовка 1.0 эко',	'описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	1111,	4,	11,	1,	100,	55000,	200000),
(41,	'Megamix грунтовка 2.5 эко ок,',	'описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	1111,	4,	11,	1,	100,	125000,	200000),
(42,	'Megamix грунтовка 2.5 фиолетовый',	'описание',	'https://i4.stat01.com/2/5699/156987826/075a3e/megamix-jemal-metallik-nazvanie-cveta-quot-191-venera-quot-880ml.jpg',	1111,	4,	11,	1,	100,	135000,	200000),
(43,	'ГРУНТОВКА ГФ-021 АЛКИДНАЯ',	'описание',	'https://lakokraska-ya.ru/images/items/396.png',	1111,	4,	13,	1,	100,	45000,	200000),
(44,	'Грунтовка Акриловая (Maestro 5кг)',	'описание',	'https://marshall-paints.ru/upload/image/9L_Maestro_Facade_Grunt.png',	1111,	4,	11,	1,	100,	85000,	200000);

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `city_id` int unsigned NOT NULL,
  `phone` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tg_username` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tg_id` bigint unsigned DEFAULT NULL,
  `coordinates` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vendors` (`id`, `name`, `city_id`, `phone`, `email`, `tg_username`, `tg_id`, `coordinates`) VALUES
(1,	'VendorTest',	4,	'+9888888888',	'email@mail.com',	'vendornick',	5783470647,	'[67.809066, 25.457954]');

-- 2023-06-20 16:57:04