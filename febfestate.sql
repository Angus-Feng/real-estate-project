-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- 主机： localhost:3306
-- 生成日期： 2021-11-16 10:11:36
-- 服务器版本： 10.3.32-MariaDB-log
-- PHP 版本： 7.3.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `cp5016_realestate`
--

-- --------------------------------------------------------

--
-- 表的结构 `brokerpendinglist`
--

CREATE TABLE `brokerpendinglist` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `licenseNo` varchar(12) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `company` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `brokerpendinglist`
--

INSERT INTO `brokerpendinglist` (`id`, `userId`, `licenseNo`, `firstName`, `lastName`, `company`) VALUES
(1, 45, 'AAA000001AAA', 'John', 'Doe', 'John Abbott'),
(3, 47, 'GGG123456GGG', 'Chris', 'L', 'Company');

-- --------------------------------------------------------

--
-- 表的结构 `chatmsgs`
--

CREATE TABLE `chatmsgs` (
  `id` int(11) NOT NULL,
  `senderId` int(11) NOT NULL,
  `recipientId` int(11) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `msgTS` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `favourites`
--

CREATE TABLE `favourites` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `notes` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `favourites`
--

INSERT INTO `favourites` (`id`, `userId`, `propertyId`, `notes`) VALUES
(23, 44, 237, NULL),
(24, 44, 236, NULL),
(25, 44, 235, NULL),
(26, 44, 234, NULL),
(27, 44, 129, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `brokerId` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `title` varchar(100) NOT NULL,
  `bedrooms` int(11) NOT NULL,
  `bathrooms` int(11) NOT NULL,
  `buildingYear` year(4) NOT NULL,
  `lotArea` decimal(12,2) NOT NULL,
  `description` varchar(2000) NOT NULL,
  `createdTS` timestamp NOT NULL DEFAULT current_timestamp(),
  `appartmentNo` int(11) DEFAULT NULL,
  `streetAddress` varchar(320) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(2) NOT NULL,
  `postalCode` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `properties`
--

INSERT INTO `properties` (`id`, `brokerId`, `price`, `title`, `bedrooms`, `bathrooms`, `buildingYear`, `lotArea`, `description`, `createdTS`, `appartmentNo`, `streetAddress`, `city`, `province`, `postalCode`) VALUES
(101, 4, 9920274.00, 'Demo property', 5, 3, 1978, 321.00, 'sit amet eleifend pede libero quis orci nullam molestie nibh in lectus pellentesque at nulla suspendisse potenti cras in purus eu magna vulputate luctus cum sociis natoque penatibus et magnis dis parturient montes nascetur ridiculus mus vivamus vestibulum sagittis sapien cum sociis natoque penatibus et magnis dis parturient montes', '2021-08-12 07:00:00', NULL, '4040 Bd Grand', 'Montreal', 'QC', 'H4B2X5'),
(102, 8, 7950447.00, 'Demo property', 6, 2, 2019, 438.00, 'mauris lacinia sapien quis libero nullam sit amet turpis elementum ligula vehicula consequat morbi a ipsum integer a nibh in quis justo maecenas rhoncus aliquam lacus morbi quis tortor id nulla ultrices aliquet maecenas leo odio condimentum id luctus nec molestie sed justo pellentesque viverra pede ac diam cras pellentesque volutpat dui maecenas tristique est et tempus semper est quam pharetra magna ac consequat metus sapien ut nunc vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere', '2021-07-05 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(103, 10, 8499147.00, 'Demo property', 10, 1, 2019, 340.00, 'suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae ipsum aliquam non mauris morbi non lectus aliquam sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis fusce posuere felis sed lacus morbi sem mauris laoreet ut rhoncus aliquet pulvinar sed nisl nunc rhoncus dui vel sem sed sagittis nam congue risus semper porta volutpat quam pede lobortis ligula sit amet eleifend pede libero quis orci nullam molestie nibh in lectus pellentesque at nulla suspendisse potenti cras in purus eu magna vulputate luctus cum sociis natoque penatibus', '2021-06-20 07:00:00', 85, '4336 Av Marcil', 'Montreal', 'QC', 'H4A2Z8'),
(104, 3, 6669922.00, 'Demo property', 9, 4, 1985, 438.00, 'non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at feugiat non pretium quis lectus suspendisse potenti in eleifend', '2021-01-10 08:00:00', NULL, '3875 Plamondon ', 'Montreal', 'QC', 'H3S1L8'),
(105, 2, 4795412.00, 'Demo property', 7, 2, 1996, 461.00, 'in imperdiet et commodo vulputate justo in blandit ultrices enim lorem ipsum dolor sit amet consectetuer adipiscing elit proin interdum mauris non ligula pellentesque ultrices phasellus id sapien in sapien iaculis congue vivamus metus arcu adipiscing molestie hendrerit at vulputate vitae nisl aenean lectus pellentesque eget nunc donec quis orci eget orci vehicula condimentum curabitur in libero ut massa volutpat convallis morbi odio odio elementum eu interdum eu tincidunt in leo maecenas pulvinar lobortis est phasellus sit amet erat nulla', '2021-01-16 08:00:00', NULL, '4558 Av Wilson', 'Montreal', 'QC', 'H4A2V4'),
(106, 2, 650238.00, 'Demo property', 8, 4, 1962, 404.00, 'donec diam neque vestibulum eget vulputate ut ultrices vel augue vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi sit amet lobortis sapien sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at feugiat non pretium quis lectus suspendisse potenti in eleifend quam a odio in hac habitasse platea dictumst maecenas ut massa quis augue', '2021-10-12 07:00:00', NULL, '4750 Av de Courtra', 'Montreal', 'QC', 'H3W1A1'),
(107, 4, 1486911.00, 'Demo property', 10, 4, 1995, 376.00, 'ultrices enim lorem ipsum dolor sit amet consectetuer adipiscing elit proin interdum mauris non ligula pellentesque ultrices phasellus id sapien in sapien iaculis congue vivamus metus arcu adipiscing molestie hendrerit at vulputate vitae nisl aenean lectus', '2021-10-26 07:00:00', NULL, '11595 St Evariste Stree', 'Montreal', 'QC', 'H4J2N6'),
(108, 5, 2740911.00, 'Demo property', 9, 4, 2001, 486.00, 'venenatis non sodales sed tincidunt eu felis fusce posuere felis sed lacus morbi sem mauris laoreet ut rhoncus aliquet pulvinar sed nisl nunc rhoncus dui vel sem sed sagittis nam congue risus semper porta volutpat quam pede lobortis ligula sit amet eleifend pede libero quis orci nullam molestie', '2021-07-05 07:00:00', NULL, 'street', 'Montreal', 'QC', 'H4C3M4'),
(109, 7, 5012202.00, 'Demo property', 8, 1, 1968, 380.00, 'id ligula suspendisse ornare consequat lectus in est risus auctor sed tristique in tempus sit amet sem fusce consequat nulla nisl nunc nisl duis bibendum felis sed interdum venenatis turpis enim blandit mi in porttitor pede justo eu massa donec dapibus duis at velit eu est congue elementum in hac habitasse platea dictumst morbi vestibulum velit', '2021-03-14 08:00:00', 707, 'street', 'Montreal', 'QC', 'H3W3H9'),
(110, 8, 9534259.00, 'Demo property', 8, 4, 1963, 384.00, 'cras pellentesque volutpat dui maecenas tristique est et tempus semper est quam pharetra magna ac consequat metus sapien ut nunc vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra diam vitae quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae ipsum aliquam non mauris morbi non lectus aliquam sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis fusce posuere felis sed lacus morbi sem', '2020-09-05 07:00:00', NULL, 'street', 'Montreal', 'QC', 'H3W2G9'),
(111, 5, 8661569.00, 'Demo property', 9, 1, 1981, 374.00, 'interdum in ante vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae duis faucibus accumsan odio curabitur convallis duis consequat dui nec nisi volutpat eleifend donec ut dolor morbi vel lectus in', '2020-09-06 07:00:00', NULL, 'street', 'Montreal', 'QC', 'H3X2C5'),
(112, 4, 7512595.00, 'Demo property', 6, 3, 2005, 436.00, 'cras in purus eu magna vulputate luctus cum sociis natoque penatibus et magnis dis parturient montes nascetur ridiculus mus vivamus vestibulum sagittis sapien cum sociis natoque penatibus et magnis dis parturient montes nascetur ridiculus mus etiam vel augue vestibulum rutrum rutrum neque', '2021-04-14 07:00:00', NULL, 'street', 'Montreal', 'QC', 'H2S3J1'),
(113, 2, 7554313.00, 'Demo property', 2, 2, 1999, 397.00, 'nisi at nibh in hac habitasse platea dictumst aliquam augue quam sollicitudin vitae consectetuer eget rutrum at lorem integer tincidunt ante vel ipsum praesent blandit lacinia erat vestibulum sed magna at nunc commodo placerat praesent blandit', '2021-02-19 08:00:00', NULL, 'street', 'Montreal', 'QC', 'H3X2P6'),
(114, 6, 7534720.00, 'Demo property', 5, 1, 1984, 368.00, 'justo sollicitudin ut suscipit a feugiat et eros vestibulum ac est lacinia nisi venenatis tristique fusce congue diam id ornare imperdiet sapien urna pretium nisl ut volutpat sapien arcu sed augue aliquam erat volutpat in congue etiam justo etiam pretium iaculis justo in hac habitasse platea dictumst', '2020-08-08 07:00:00', 775, 'street', 'Montreal', 'QC', 'H2M2W7'),
(129, 3, 3519857.00, 'Demo property', 9, 1, 1965, 369.00, 'morbi vestibulum velit id pretium iaculis diam erat fermentum justo nec condimentum neque sapien placerat ante nulla justo aliquam quis turpis eget elit sodales scelerisque mauris sit amet eros suspendisse accumsan tortor quis turpis sed ante vivamus tortor duis mattis egestas metus aenean fermentum donec ut mauris eget massa tempor convallis nulla', '2021-11-05 07:00:00', 236, 'street', 'Montreal', 'QC', 'H4V2E4'),
(142, 8, 1841486.00, 'Demo property', 3, 5, 1969, 313.00, 'aenean auctor gravida sem praesent id massa id nisl venenatis lacinia aenean sit amet justo morbi', '2020-07-23 07:00:00', NULL, 'street', 'Montreal', 'QC', 'H4A2R3'),
(151, 9, 4684803.00, 'Demo property', 5, 3, 1969, 352.00, 'amet consectetuer adipiscing elit proin risus praesent lectus vestibulum quam sapien varius ut blandit non interdum in ante vestibulum ante ipsum primis in faucibus orci luctus', '2020-07-29 07:00:00', NULL, 'street', 'Montreal', 'QC', 'H4A2V4'),
(191, 8, 1841486.00, 'Demo property', 3, 5, 1969, 313.00, 'aenean auctor gravida sem praesent id massa id nisl venenatis lacinia aenean sit amet justo morbi', '2020-07-23 07:00:00', NULL, 'street', 'Montreal', 'QC', 'H4J1Z7'),
(192, 10, 3388696.00, 'Demo property', 4, 3, 1986, 317.00, 'tincidunt eget tempus vel pede morbi porttitor lorem id ligula suspendisse ornare consequat lectus in est risus auctor sed tristique in tempus sit amet sem fusce consequat nulla nisl nunc nisl duis bibendum felis sed interdum venenatis turpis enim blandit mi in porttitor pede justo eu massa', '2021-06-07 07:00:00', NULL, 'street', 'Montreal', 'QC', 'H3A1P7'),
(199, 4, 4083327.00, 'Demo property', 3, 5, 1993, 325.00, 'justo maecenas rhoncus aliquam lacus morbi quis tortor id nulla ultrices aliquet maecenas leo odio condimentum id luctus nec molestie sed justo pellentesque viverra pede ac diam cras pellentesque volutpat dui maecenas tristique est et tempus semper est quam pharetra magna ac consequat metus sapien ut nunc vestibulum ante ipsum primis in faucibus orci', '2020-08-28 07:00:00', NULL, 'street', 'Montreal', 'QC', 'H3N2N4'),
(200, 9, 4684803.00, 'Demo property', 5, 3, 1969, 352.00, 'amet consectetuer adipiscing elit proin risus praesent lectus vestibulum quam sapien varius ut blandit non interdum in ante vestibulum ante ipsum primis in faucibus orci luctus', '2020-07-29 07:00:00', NULL, 'street', 'Montreal', 'QC', 'H3N3N5'),
(203, 3, 5873492.00, 'Demo property', 1, 2, 1965, 398.00, 'luctus et ultrices posuere cubilia curae nulla dapibus dolor vel est donec odio justo sollicitudin ut suscipit a feugiat et eros vestibulum ac est lacinia nisi venenatis tristique fusce congue diam id ornare imperdiet sapien urna pretium nisl ut volutpat sapien arcu sed augue aliquam erat volutpat in congue etiam justo etiam pretium iaculis justo in hac habitasse platea dictumst etiam faucibus cursus urna ut tellus nulla ut erat id mauris vulputate elementum nullam varius nulla facilisi cras non velit nec', '2020-07-14 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(204, 5, 3447865.00, 'Demo property', 9, 4, 1985, 322.00, 'nec molestie sed justo pellentesque viverra pede ac diam cras pellentesque volutpat dui maecenas tristique est et tempus semper est quam pharetra magna ac consequat metus sapien ut nunc vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra diam vitae quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae ipsum aliquam non mauris morbi non lectus aliquam sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed', '2021-03-06 08:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(205, 5, 8246342.00, 'Demo property', 10, 4, 1967, 384.00, 'volutpat convallis morbi odio odio elementum eu interdum eu tincidunt in leo maecenas pulvinar lobortis est phasellus sit amet erat nulla tempus vivamus in', '2021-06-19 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(206, 3, 3786037.00, 'Demo property', 1, 5, 1962, 330.00, 'maecenas pulvinar lobortis est phasellus sit amet erat nulla tempus vivamus in felis eu sapien cursus vestibulum proin eu mi nulla ac enim in tempor turpis nec euismod scelerisque quam turpis adipiscing lorem vitae mattis nibh ligula nec sem duis aliquam convallis nunc proin at turpis a pede posuere nonummy integer non velit donec diam neque vestibulum eget vulputate ut ultrices vel augue vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices erat', '2021-01-01 08:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(207, 3, 3448589.00, 'Demo property', 1, 1, 1966, 357.00, 'elit ac nulla sed vel enim sit amet nunc viverra dapibus nulla suscipit ligula in lacus', '2021-07-13 07:00:00', 82, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(208, 5, 3283293.00, 'Demo property', 4, 3, 1991, 328.00, 'nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum pellentesque quisque porta volutpat erat quisque erat eros viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam dui proin', '2021-05-11 07:00:00', 677, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(209, 7, 765188.00, 'Demo property', 1, 3, 1978, 357.00, 'pulvinar lobortis est phasellus sit amet erat nulla tempus vivamus in felis eu sapien cursus vestibulum proin eu mi nulla ac enim in tempor turpis nec euismod scelerisque quam turpis adipiscing', '2021-07-31 07:00:00', 196, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(210, 5, 8698399.00, 'Demo property', 7, 4, 1965, 361.00, 'ut suscipit a feugiat et eros vestibulum ac est lacinia nisi venenatis tristique fusce congue diam id ornare imperdiet sapien urna pretium nisl ut volutpat sapien arcu sed augue aliquam erat volutpat in congue etiam justo etiam pretium iaculis justo in hac habitasse platea dictumst etiam faucibus cursus urna', '2020-09-12 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(211, 10, 8059075.00, 'Demo property', 6, 3, 2015, 352.00, 'luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi sit amet lobortis sapien sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at feugiat non pretium quis lectus suspendisse potenti in eleifend quam a', '2020-09-24 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(212, 6, 5160270.00, 'Demo property', 9, 3, 1978, 468.00, 'sed accumsan felis ut at dolor quis odio consequat varius integer ac leo pellentesque ultrices mattis odio', '2021-06-08 07:00:00', 511, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(213, 4, 2533947.00, 'Demo property', 8, 1, 2018, 389.00, 'volutpat in congue etiam justo etiam pretium iaculis justo in hac habitasse platea dictumst etiam faucibus cursus urna ut tellus nulla ut erat id mauris vulputate elementum nullam varius nulla facilisi cras non', '2021-09-14 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(214, 6, 871774.00, 'Demo property', 6, 1, 2011, 408.00, 'dis parturient montes nascetur ridiculus mus etiam vel augue vestibulum rutrum rutrum neque aenean auctor gravida sem praesent id massa id nisl venenatis lacinia aenean sit amet justo morbi ut odio cras mi pede malesuada in imperdiet et commodo vulputate justo in blandit ultrices enim lorem ipsum dolor sit amet consectetuer adipiscing elit proin interdum mauris non ligula pellentesque ultrices phasellus id sapien in sapien iaculis congue vivamus metus arcu adipiscing molestie hendrerit at vulputate vitae nisl aenean lectus pellentesque eget nunc donec quis orci eget orci vehicula condimentum curabitur in libero ut', '2021-03-06 08:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(215, 6, 2686516.00, 'Demo property', 7, 5, 1985, 485.00, 'nulla mollis molestie lorem quisque ut erat curabitur gravida nisi at nibh in hac habitasse platea dictumst aliquam augue quam sollicitudin vitae consectetuer eget rutrum at lorem integer tincidunt ante vel ipsum praesent blandit lacinia erat vestibulum sed magna at nunc commodo placerat praesent blandit nam nulla integer pede justo lacinia eget tincidunt eget tempus vel pede morbi porttitor lorem id ligula suspendisse ornare consequat lectus in est risus auctor sed tristique in tempus sit amet sem fusce consequat', '2021-05-11 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(216, 8, 1917124.00, 'Demo property', 7, 5, 1973, 359.00, 'aliquam convallis nunc proin at turpis a pede posuere nonummy integer non velit donec diam neque vestibulum eget vulputate ut ultrices vel augue vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae donec pharetra magna vestibulum aliquet ultrices erat tortor sollicitudin mi sit amet lobortis sapien sapien non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at feugiat non pretium', '2021-01-10 08:00:00', 535, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(217, 9, 6362815.00, 'Demo property', 8, 1, 2009, 336.00, 'vitae quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae ipsum aliquam non mauris morbi non lectus aliquam sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis fusce posuere felis sed lacus morbi sem mauris laoreet ut', '2021-07-18 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(218, 5, 5185678.00, 'Demo property', 6, 3, 2002, 377.00, 'diam vitae quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae ipsum aliquam non mauris morbi non lectus aliquam sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis fusce posuere felis sed lacus morbi sem mauris laoreet ut rhoncus aliquet pulvinar sed nisl nunc rhoncus dui vel sem sed sagittis nam congue risus semper porta volutpat', '2020-10-20 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(219, 7, 884783.00, 'Demo property', 9, 2, 2014, 452.00, 'cras pellentesque volutpat dui maecenas tristique est et tempus semper est quam pharetra magna ac consequat metus sapien ut nunc vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra diam vitae quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae ipsum aliquam non mauris morbi non lectus aliquam sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis fusce posuere felis sed lacus morbi', '2021-03-06 08:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(220, 4, 1980359.00, 'Demo property', 5, 4, 2004, 485.00, 'vitae ipsum aliquam non mauris morbi non lectus aliquam sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis fusce posuere felis', '2020-08-26 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(221, 4, 1849123.00, 'Demo property', 9, 4, 1969, 499.00, 'sapien quis libero nullam sit amet turpis elementum ligula vehicula consequat morbi a ipsum integer a nibh in quis justo maecenas rhoncus aliquam lacus morbi quis tortor id nulla ultrices aliquet maecenas leo odio condimentum id luctus nec molestie sed justo pellentesque viverra pede ac diam cras pellentesque volutpat', '2021-09-12 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(222, 2, 3199439.00, 'Demo property', 10, 4, 1988, 425.00, 'aliquam erat volutpat in congue etiam justo etiam pretium iaculis justo in hac habitasse platea dictumst etiam faucibus cursus urna ut tellus nulla ut erat id mauris vulputate', '2020-11-14 08:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(223, 4, 5464552.00, 'Demo property', 7, 2, 2014, 401.00, 'non mi integer ac neque duis bibendum morbi non quam nec dui luctus rutrum nulla tellus in sagittis dui vel nisl', '2021-05-26 07:00:00', 917, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(224, 8, 2533465.00, 'Demo property', 4, 4, 1974, 469.00, 'habitasse platea dictumst maecenas ut massa quis augue luctus tincidunt nulla mollis molestie lorem quisque ut erat curabitur gravida nisi at nibh in hac habitasse platea dictumst aliquam augue quam sollicitudin vitae consectetuer eget rutrum at lorem integer tincidunt ante vel ipsum praesent blandit lacinia erat vestibulum sed magna at nunc commodo placerat praesent blandit nam nulla integer pede justo lacinia eget tincidunt eget tempus vel pede morbi porttitor lorem id ligula suspendisse ornare consequat lectus in est risus auctor sed tristique in tempus sit amet sem fusce consequat nulla nisl', '2021-01-02 08:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(225, 3, 63049.00, 'Demo property', 10, 4, 1963, 404.00, 'tellus in sagittis dui vel nisl duis ac nibh fusce lacus purus aliquet at feugiat non pretium quis lectus suspendisse potenti in eleifend quam a odio in hac habitasse', '2021-03-15 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(226, 8, 9749904.00, 'Demo property', 3, 2, 1971, 437.00, 'in eleifend quam a odio in hac habitasse platea dictumst maecenas ut massa quis augue luctus tincidunt nulla', '2020-08-09 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(227, 5, 9211256.00, 'Demo property', 8, 4, 2019, 461.00, 'aliquam erat volutpat in congue etiam justo etiam pretium iaculis justo in hac habitasse platea dictumst etiam faucibus cursus urna ut tellus nulla ut erat id mauris vulputate elementum nullam varius nulla facilisi cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum pellentesque quisque porta volutpat erat quisque erat eros viverra eget congue eget semper rutrum nulla nunc purus', '2020-08-23 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(228, 2, 8942209.00, 'Demo property', 2, 1, 2008, 380.00, 'nam dui proin leo odio porttitor id consequat in consequat ut nulla sed accumsan felis ut at dolor quis odio consequat varius integer ac leo pellentesque ultrices mattis odio donec vitae nisi nam ultrices', '2021-06-01 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(229, 10, 8965568.00, 'Demo property', 3, 4, 1962, 499.00, 'et eros vestibulum ac est lacinia nisi venenatis tristique fusce congue diam id ornare imperdiet sapien urna pretium nisl ut volutpat sapien arcu sed augue aliquam erat volutpat in congue etiam justo etiam pretium iaculis justo in hac habitasse platea dictumst etiam faucibus cursus urna ut tellus nulla ut erat id mauris vulputate elementum nullam varius nulla facilisi cras non velit nec nisi vulputate nonummy maecenas tincidunt lacus at velit vivamus vel nulla eget eros elementum pellentesque quisque porta volutpat erat quisque erat eros viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec', '2021-03-12 08:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(230, 2, 5909950.00, 'Demo property', 9, 4, 1988, 357.00, 'velit id pretium iaculis diam erat fermentum justo nec condimentum neque sapien placerat ante nulla justo aliquam quis', '2020-09-01 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(231, 2, 1116499.00, 'Demo property', 7, 2, 2010, 407.00, 'eros viverra eget congue eget semper rutrum nulla nunc purus phasellus in felis donec semper sapien a libero nam dui proin leo odio porttitor id consequat in consequat ut nulla sed accumsan felis ut at dolor quis odio consequat varius integer ac leo pellentesque ultrices mattis odio donec vitae nisi nam ultrices libero non mattis pulvinar nulla pede ullamcorper augue a suscipit nulla elit ac nulla sed vel enim sit amet nunc viverra dapibus nulla suscipit ligula', '2021-04-23 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(232, 6, 6129918.00, 'Demo property', 3, 5, 1987, 323.00, 'ultrices aliquet maecenas leo odio condimentum id luctus nec molestie sed justo pellentesque viverra pede ac diam cras pellentesque volutpat dui maecenas tristique est et tempus semper est quam pharetra magna ac consequat metus sapien ut nunc vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra diam vitae quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae ipsum aliquam non mauris morbi non lectus aliquam sit', '2020-08-15 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(233, 5, 5854084.00, 'Demo property', 3, 4, 2009, 318.00, 'dui maecenas tristique est et tempus semper est quam pharetra magna ac consequat metus sapien ut nunc vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae mauris viverra diam vitae quam suspendisse potenti nullam porttitor lacus at turpis donec posuere metus vitae ipsum aliquam non mauris morbi non lectus aliquam sit amet diam in magna bibendum imperdiet nullam orci pede venenatis non sodales sed tincidunt eu felis fusce posuere felis sed lacus morbi sem mauris laoreet', '2020-07-27 07:00:00', NULL, '1509 Rue Sherbrooke O', 'Montreal', 'QC', 'H3G1M1'),
(234, 2, 311000.00, 'New Title', 9, 4, 1990, 200.00, 'Maxime consectetur ', '2021-11-15 20:25:39', 108, '4444 Sherbrooke O', 'Montreal', 'MB', 'H3Z1E4'),
(235, 2, 263000.00, 'Condo for sale', 1, 1, 1974, 50.00, 'A true turnkey home. Kitchen and bathroom designed by Manon Leblanc. Floors changed throughout the unit. The paint is recent with neutral colors. Two good size bedrooms, one with walkin closet. Heated garage included. Choosing Place Jumonville is choosing a haven of peace.', '2021-11-15 22:23:34', NULL, '5342 A Rue Marquette', 'Montreal', 'QC', 'H2J3Z3'),
(236, 2, 520000.00, 'House for Sale', 2, 1, 1990, 400.00, 'Lots of potential for revenue property. Many renovations were done to the building by the owners.Basement transformed into a Bachelor with new plumbing, kitchen, electrical installations, hydro bill for each housing. Nice location, close to schools, parks, highway access, riverside,etc.', '2021-11-16 16:12:45', NULL, '9391 Rue Centrale', 'montreal', 'MB', 'H8R2K4'),
(237, 2, 720300.00, 'House for Sale', 3, 2, 2001, 450.00, 'Lots of potential for revenue property. Many renovations were done to the building by the owners.Basement transformed into a Bachelor with new plumbing, kitchen, electrical installations, hydro bill for each housing. Nice location, close to schools, parks, highway access, riverside,etc.', '2021-11-16 16:16:33', NULL, '5260 Rue Fabre', 'montreal', 'QC', 'H2J3W5'),
(238, 2, 812000.00, 'Title', 5, 3, 1990, 275.00, 'Est est unde natus d', '2021-11-16 16:57:11', 825, '400 street', 'Montreal', 'QC', 'H3W1C5');

-- --------------------------------------------------------

--
-- 表的结构 `propertyphotos`
--

CREATE TABLE `propertyphotos` (
  `id` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `ordinalINT` int(11) NOT NULL,
  `photoFilePath` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `propertyphotos`
--

INSERT INTO `propertyphotos` (`id`, `propertyId`, `ordinalINT`, `photoFilePath`) VALUES
(1, 142, 0, 'bX1WAySffG.jpg'),
(2, 191, 0, 'qFB8B3Zbn1.jpg'),
(3, 151, 0, 'q078rf75E5.jpg'),
(4, 200, 0, 'uny1dW34yo.jpg'),
(5, 114, 0, 'uOHhpN3hu4.jpg'),
(6, 101, 0, 'SbvZbCnSpO.jpg'),
(7, 102, 0, 'VXGLp04gwT.jpg'),
(8, 103, 0, 'dt9ROa68SW.jpg'),
(9, 104, 0, '5Whw0IIjWT.jpg'),
(10, 105, 0, '0xKUUQA1LF.jpg'),
(11, 101, 1, 'gQ4pTvbz0e.jpg'),
(12, 102, 1, 't0zRk60MUO.jpg'),
(13, 103, 1, 'r4N9oz3EGU.jpg'),
(14, 104, 1, 'LebnnNp0e6.jpg'),
(15, 105, 1, 'qoR2cJea50.jpg'),
(16, 106, 0, 'BhebcDb7up.jpg'),
(17, 107, 0, 'UwOQvmwUAR.jpg'),
(18, 108, 0, 'HMT1n9Z2he.jpg'),
(19, 109, 0, 'qjZlYhPTbl.jpg'),
(20, 112, 0, 'mNgsB7a9vc.jpg'),
(21, 113, 0, 'OlibYdrsSq.jpg'),
(22, 114, 1, '8cIwNYC9qw.jpg'),
(23, 129, 0, 'XOKe1huwcb.jpg'),
(24, 142, 1, 'Chk0glIAFB.jpg'),
(25, 151, 1, 'bPAE2RHgMs.jpg'),
(26, 191, 1, '6fr1fhl3D7.jpg'),
(27, 192, 0, 'BqC4ssdkWE.jpg'),
(28, 199, 0, 'MWY62JayFR.jpg'),
(29, 200, 1, 'p5b3WV7MPF.jpg'),
(30, 111, 0, 'ajZfzTA9iV.jpg'),
(31, 233, 0, '7MrDTYSPiE.jpg'),
(32, 233, 1, 'Xx5bv6ROld.jpg'),
(33, 233, 2, 'kqPxmK4FOt.jpg'),
(34, 233, 3, 'bnbZBAlnQ9.jpg'),
(35, 232, 0, 'RgQsmYOOhu.jpg'),
(36, 232, 1, 'VqTcQGiUze.jpg'),
(37, 232, 2, 'xhB02asLy2.jpg'),
(38, 232, 3, '6spFNsN9MC.jpg'),
(39, 231, 3, 'FvK5Yju60W.jpg'),
(40, 231, 0, '4yIPkKVvAH.jpg'),
(41, 231, 1, 'HOaynIM8AW.jpg'),
(42, 231, 2, 'rA5k4ly0cL.jpg'),
(43, 234, 0, 'zQCuFtdKiK.jpg'),
(44, 234, 1, 'MTGev9CQ95.jpg'),
(45, 234, 2, 'QxJWIt24PC.jpg'),
(46, 235, 0, 'g1ci4EiO4Y.jpg'),
(47, 235, 1, 'KVES9cx7lP.jpg'),
(48, 235, 2, 'qkcCfYCxDD.jpg'),
(49, 235, 3, 'z0rTfzxytk.jpg'),
(50, 235, 4, 'oR40hF1V7r.jpg'),
(53, 237, 0, 'mRp2kDWTJ7.jpg'),
(54, 237, 1, 'EOOJhNkWx8.jpg'),
(55, 238, 0, 'OrjypezXok.jpg'),
(56, 238, 1, 'FzQH2IiMs7.jpg'),
(57, 238, 2, 'MIcvagw7Wm.jpg'),
(58, 238, 3, '99BCnHgoNR.jpg'),
(59, 238, 4, 'bisG497hzS.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(320) NOT NULL,
  `password` varchar(320) NOT NULL,
  `role` enum('buyer','broker','admin','broker-check') NOT NULL,
  `licenseNo` varchar(12) DEFAULT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `company` varchar(320) DEFAULT NULL,
  `jobTitle` varchar(100) DEFAULT NULL,
  `photoFilePath` varchar(100) DEFAULT 'images/default_broker.jpg',
  `appartmentNo` int(11) DEFAULT NULL,
  `streetAddress` varchar(320) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(2) DEFAULT NULL,
  `postalCode` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `licenseNo`, `firstName`, `lastName`, `phone`, `company`, `jobTitle`, `photoFilePath`, `appartmentNo`, `streetAddress`, `city`, `province`, `postalCode`) VALUES
(1, 'team8admin@team8.com', '$2y$12$KXsKwAOsQgFOztrwbkVo9Oxefqyu7wvHVZlGA5EV/VsIsT5igp5jS', 'admin', 'AAA100000AAA', NULL, NULL, NULL, NULL, NULL, 'images/default_broker.jpg', NULL, NULL, NULL, NULL, NULL),
(2, 'broker1@broker.com', '$2y$12$hHuQSzJNgUbHZ8cmxKIhlO/xENzC.hDUwIVKGY4aMrdO5efEjPHAu', 'broker', 'AAA100001AAA', 'Brandi', 'Kearsley', '5145555555', 'John Abbott', 'Sales Manager', 'uploads/AAA100001AAA.jpg', NULL, NULL, NULL, NULL, NULL),
(3, 'broker2@broker.com', '$2y$12$Iwi0EVyh5a9W51sKEfuyIOipgK80ViUk.JJRGl1EJpSixnvks85aC', 'broker', 'AAA100002AAA', 'Valentin', 'Skep', '5145555555', 'John Abbott', 'Sales Manager', 'uploads/AAA100002AAA.jpg', NULL, NULL, NULL, NULL, NULL),
(4, 'broker3@broker.com', '$2y$12$Bg2z5vWEHeanc.Lc2X3RUu61hognTGPEdt8VhTbvj3jjZd5IwicA6', 'broker', 'AAA100003AAA', 'Patty', 'Nesterov', '5145555555', 'John Abbott', 'Sales Manager', 'uploads/AAA100003AAA.jpg', NULL, NULL, NULL, NULL, NULL),
(5, 'broker4@broker.com', '$2y$12$KQEDwJrmdFvujkeijyiQZ.oE20MsoTU2nRuJ1a2OVgIkNeI0ICCsC', 'broker', 'AAA100004AAA', 'Orsola', 'Sinclair', '5145555555', 'John Abbott', 'Sales Manager', 'uploads/AAA100004AAA.jpg', NULL, NULL, NULL, NULL, NULL),
(6, 'broker5@broker.com', '$2y$12$jRzy55yRKxTag73clit/WeUaS8a6EBTzQ7KK.7IsH2ZPOX4ioBtdK', 'broker', 'AAA100005AAA', 'Nil', 'Neill', '5145555555', 'John Abbott', 'Sales Manager', 'uploads/AAA100005AAA.jpg', NULL, NULL, NULL, NULL, NULL),
(7, 'broker6@broker.com', '$2y$12$jW8oMjQ8v5dK6XjlZTSAOOwlQQ/5a0s8Zad/6nEA.SZnpVELg1i0a', 'broker', 'AAA100006AAA', 'Glenda', 'Docwra', '5145555555', NULL, 'Sales Manager', 'uploads/AAA100006AAA.jpg', NULL, NULL, NULL, NULL, NULL),
(8, 'broker7@broker.com', '$2y$12$J/6D8hHkZEQOBwoeycthwOwNafmG12ADR9czCUBXwnA3Kx2dA8sDa', 'broker', 'AAA100007AAA', 'Etheline', 'Pervew', '5145555555', NULL, 'Sales Manager', 'uploads/AAA100007AAA.jpg', NULL, NULL, NULL, NULL, NULL),
(9, 'broker8@broker.com', '$2y$12$C/eJu0o2.KdscTJsboa.c.AkH1C/.pDf0BkMcpr92wQQDlZBRyzrS', 'broker', 'AAA100008AAA', 'Dannel', 'Desborough', '5145555555', NULL, 'Sales Manager', 'uploads/AAA100008AAA.jpg', NULL, NULL, NULL, NULL, NULL),
(10, 'broker9@broker.com', '$2y$12$aXNWADK3heC0VTGfYzJgR.wM15wgPkFamL76hq99kA83cxj7.12da', 'broker', 'AAA100009AAA', 'Leda', 'Derx', '5145555555', NULL, 'Sales Manager', 'uploads/AAA100009AAA.jpg', NULL, NULL, NULL, NULL, NULL),
(44, 'demobuyer@demobuyer.com', '$2y$10$Z/qBvxkmSsMnPaKHRTaRcuyCzLORfEnGDt841u/bLf1TrNiIkSC8W', 'buyer', '', '', '', NULL, '', NULL, 'images/default_broker.jpg', NULL, NULL, NULL, NULL, NULL),
(45, 'brokertobe@brokertobe.com', '$2y$12$9aBTllngC0wzIF.ro6yd6uq2jz6ayBV0wsDHG8R2/kT03tvC7Q7vq', 'broker-check', 'AAA000001AAA', 'John', 'Doe', NULL, 'John Abbott', NULL, 'images/default_broker.jpg', NULL, NULL, NULL, NULL, NULL),
(46, 'classdemobroker@gmail.com', '$2y$12$s/mYUFsRVXn2cfWt4TLknut8mzXGd4KZ81JTou2FBOpu6eEhmaKoG', 'broker', 'QQQ123456QQQ', 'Chris', 'Latchman', NULL, 'FSD01', NULL, 'images/default_broker.jpg', NULL, NULL, NULL, NULL, NULL),
(47, 'Randombroker@gmail.com', '$2y$12$bMm0eI5yDoFg4tIcQ0baquLR/rZcUaqQkvnnJ482w7cs0djeJvwjS', 'broker-check', 'GGG123456GGG', 'Chris', 'L', NULL, 'Company', NULL, 'images/default_broker.jpg', NULL, NULL, NULL, NULL, NULL),
(48, 'Testtest@gmail.com', '$2y$10$3mF5Bu7l33ibZftarvX7suR69tW0y8hcPRB4r8m45MUlt/Cg058pe', 'buyer', '', '', '', NULL, '', NULL, 'images/default_broker.jpg', NULL, NULL, NULL, NULL, NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `brokerpendinglist`
--
ALTER TABLE `brokerpendinglist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- 表的索引 `chatmsgs`
--
ALTER TABLE `chatmsgs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `senderId` (`senderId`),
  ADD KEY `recipientId` (`recipientId`);

--
-- 表的索引 `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `propertyId` (`propertyId`);

--
-- 表的索引 `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brokerId` (`brokerId`);

--
-- 表的索引 `propertyphotos`
--
ALTER TABLE `propertyphotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propertyId` (`propertyId`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `brokerpendinglist`
--
ALTER TABLE `brokerpendinglist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `chatmsgs`
--
ALTER TABLE `chatmsgs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `favourites`
--
ALTER TABLE `favourites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- 使用表AUTO_INCREMENT `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=239;

--
-- 使用表AUTO_INCREMENT `propertyphotos`
--
ALTER TABLE `propertyphotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- 限制导出的表
--

--
-- 限制表 `brokerpendinglist`
--
ALTER TABLE `brokerpendinglist`
  ADD CONSTRAINT `brokerpendinglist_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- 限制表 `chatmsgs`
--
ALTER TABLE `chatmsgs`
  ADD CONSTRAINT `chatmsgs_ibfk_1` FOREIGN KEY (`recipientId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chatmsgs_ibfk_2` FOREIGN KEY (`senderId`) REFERENCES `users` (`id`);

--
-- 限制表 `favourites`
--
ALTER TABLE `favourites`
  ADD CONSTRAINT `favourites_ibfk_1` FOREIGN KEY (`propertyId`) REFERENCES `properties` (`id`),
  ADD CONSTRAINT `favourites_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- 限制表 `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`brokerId`) REFERENCES `users` (`id`);

--
-- 限制表 `propertyphotos`
--
ALTER TABLE `propertyphotos`
  ADD CONSTRAINT `propertyphotos_ibfk_1` FOREIGN KEY (`propertyId`) REFERENCES `properties` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
