-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 02, 2012 at 12:40 PM
-- Server version: 5.1.58
-- PHP Version: 5.3.6-13ubuntu3.7

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `backend`
--
CREATE DATABASE `backend` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `backend`;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `ownerModel`, `ownerId`, `ownerAttribute`, `name`, `description`, `path`, `type`, `size`, `userId`, `timeAdded`) VALUES
(14, 'User', 12, 'thumbnail', 'test.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/User/12/thumbnail/test.jpg', 'image/jpeg', 13893, 1, '0000-00-00 00:00:00'),
(45, 'Event', 9, 'pictureBig', '274.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/9/pictureBig/274.jpg', 'image/jpeg', 184473, 1, '0000-00-00 00:00:00'),
(24, 'Event', 6, 'pictures', 'lp_small.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/6/pictures/lp_small.jpg', 'image/jpeg', 7690, 1, '0000-00-00 00:00:00'),
(25, 'Event', 6, 'pictures', 'lp_big.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/6/pictures/lp_big.jpg', 'image/jpeg', 65445, 1, '0000-00-00 00:00:00'),
(36, 'Event', 8, 'pictureSmall', '52.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/8/pictureSmall/52.jpg', 'image/jpeg', 25089, 1, '0000-00-00 00:00:00'),
(34, 'Event', NULL, 'pictureSmall', '52.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event//pictureSmall/52.jpg', 'image/jpeg', 25089, 1, '0000-00-00 00:00:00'),
(44, 'Event', 9, 'pictureSmall', '273.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/9/pictureSmall/273.jpg', 'image/jpeg', 45445, 1, '0000-00-00 00:00:00'),
(46, 'Event', 9, 'pictures', 'phoca_thumb_l_fbffe11_22_medie.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/9/pictures/phoca_thumb_l_fbffe11_22_medie.jpg', 'image/jpeg', 58810, 1, '0000-00-00 00:00:00'),
(48, 'Event', 9, 'pictures', 'phoca_thumb_l_fbffe11_24_medie.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/9/pictures/phoca_thumb_l_fbffe11_24_medie.jpg', 'image/jpeg', 39698, 1, '0000-00-00 00:00:00'),
(49, 'Event', 9, 'pictures', 'phoca_thumb_l_fbffe11_25_medie.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/9/pictures/phoca_thumb_l_fbffe11_25_medie.jpg', 'image/jpeg', 22373, 1, '0000-00-00 00:00:00'),
(50, 'Event', 9, 'pictures', 'phoca_thumb_l_fbffe11_26_medie.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/9/pictures/phoca_thumb_l_fbffe11_26_medie.jpg', 'image/jpeg', 18936, 1, '0000-00-00 00:00:00'),
(51, 'Event', 9, 'pictures', 'phoca_thumb_l_fbffe11_27_medie.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/9/pictures/phoca_thumb_l_fbffe11_27_medie.jpg', 'image/jpeg', 14501, 1, '0000-00-00 00:00:00'),
(52, 'Event', 9, 'pictures', 'phoca_thumb_l_fbffe11_28_medie.jpg', NULL, '/srv/www/misha.voyanga/public_html/backend/www/resources/Event/9/pictures/phoca_thumb_l_fbffe11_28_medie.jpg', 'image/jpeg', 43793, 1, '0000-00-00 00:00:00');

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `salt`, `password`, `email`, `requireNewPassword`) VALUES
(1, 'Куклин Михаил', '78c8b0edba251e8966ba2f2d70824cd46d4f9e0a', '9de8c4639207b96666611136aa9c9598deccaf3e', 'mihan007@ya.ru', NULL),
(2, 'Михаил', 'b1800804db3e93b7e14ffdc3e10f06999279d9b9', 'eb14d46dcb7c9b208dcd9c2a557f99ede982802f', 'kuklin@voyanga.com', NULL),
(14, 'Шадрин Евгений', 'bd4bb21c52e275e170f6f0174f76fd53ef0f105b', 'e68f68468c4ac130561ca2ffd7eff5c4a6c91663', 'shadrin@voyanga.com', NULL),
(13, 'Олег', '05efd0c40555cf64297025aaf15b150d4cfe98ad', '5eb40e8f33a65056753ea3555f8d6c89d55abb74', 'oleg@voyanga.com', NULL),
(12, 'Тест', '50fd6dcdf999c2c3b43f6f54224a6c17ed1d9b7d', '717127fc4bad8ab68bd64f8fccaca5b09a803c39', 'test@test.com', NULL);

--
-- Dumping data for table `usergroups`
--

INSERT INTO `usergroups` (`id`, `name`) VALUES
(1, 'test');
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
