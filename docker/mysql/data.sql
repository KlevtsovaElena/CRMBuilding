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
(4,	'VERO');

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
  `phone` smallint NOT NULL,
  `city_id` int unsigned NOT NULL,
  `coordinates` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `ordered_product`;
CREATE TABLE `ordered_product` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `brand_id` int unsigned NOT NULL,
  `vendor_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `price_per_unit` int unsigned DEFAULT NULL,
  `total_price` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `order_date` timestamp NOT NULL,
  `ordered_products` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


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
  `category_id` int unsigned NOT NULL,
  `brand_id` int unsigned DEFAULT NULL,
  `vendor_id` int unsigned DEFAULT NULL,
  `quantity_available` int unsigned DEFAULT NULL,
  `price` int unsigned DEFAULT NULL,
  `market_price` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `name`, `description`, `photo`, `category_id`, `brand_id`, `vendor_id`, `quantity_available`, `price`, `market_price`) VALUES
(1,	'Гипсокартон ФОРУС стеновой простой (12.5мм) (2 сорт)',	'Описание',	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	1,	NULL,	NULL,	49000,	50000),
(2,	'Гипсокартон ФОРУС стеновой влагостойкий (12.5мм) (2 сорт)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	1,	NULL,	NULL,	51000,	NULL),
(3,	'Гипсокартон ФОРУС потолочный влагостойкий (9.5мм) (2 сорт)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	1,	NULL,	NULL,	50000,	NULL),
(4,	'Гипсокартон ФОРУС стеновой простой (12.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	1,	NULL,	NULL,	56000,	NULL),
(5,	'Гипсокартон ФОРУС паталочный влагастойкий (9.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	1,	NULL,	NULL,	58000,	NULL),
(6,	'Гипсокартон ФОРУС потолочный простой (9.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	1,	NULL,	NULL,	49000,	NULL),
(7,	'Гипсокартон ФОРУС стеновой влагастойкий (12.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	1,	NULL,	NULL,	63000,	NULL),
(8,	'Гипсокартон AZIA потолочный простой (9.5мм)',	NULL,	NULL,	1,	2,	NULL,	NULL,	49000,	NULL),
(9,	'Гипсокартон AZIA стеновой простой (12.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	2,	NULL,	NULL,	56000,	NULL),
(10,	'Гипсокартон AZIA потолочный влагостойкий (9.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	2,	NULL,	NULL,	60000,	NULL),
(11,	'Гипсокартон AZIA стеновой влагостойкий (12.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	2,	NULL,	NULL,	69000,	NULL),
(12,	'Гипсокартон КНАУФ потолочный простой (9.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	3,	NULL,	NULL,	52200,	NULL),
(13,	'Гипсокартон КНАУФ стеновой простой (12.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	3,	NULL,	NULL,	59300,	NULL),
(14,	'Гипсокартон КНАУФ потолочный влагостойкий (9.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	3,	NULL,	NULL,	67300,	NULL),
(15,	'Гипсокартон КНАУФ стеновой влагостойкий (12.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	3,	NULL,	NULL,	74200,	NULL),
(16,	'Гипсокартон VERO стеновой влагостойкий (12.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	4,	NULL,	NULL,	46000,	NULL),
(17,	'Гипсокартон VERO стеновой простой (12.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	NULL,	4,	NULL,	46500,	NULL),
(18,	'Гипсокартон VERO потолочный простой (8.5мм)',	NULL,	'https://cdn.leroymerlin.ru/lmru/image/upload/v1613641220/b_white,c_pad,d_photoiscoming.png,f_auto,h_2000,q_auto,w_2000/lmcode/T8QNN_Bj2Uq3Zvaw8pHo6Q/10072681.jpg',	1,	4,	NULL,	NULL,	41500,	NULL);

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `city_id` int unsigned NOT NULL,
  `phone` smallint unsigned DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tg_username` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tg_id` bigint unsigned DEFAULT NULL,
  `coordinates` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 2023-06-19 14:55:15