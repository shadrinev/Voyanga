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
-- Database: `logdb`
--
CREATE DATABASE `logdb` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `logdb`;

--
-- Dumping data for table `log_table`
--

INSERT INTO `log_table` (`id`, `level`, `category`, `logtime`, `message`) VALUES
(1, 'info', 'system.kk', 1334319570, 'Hello people!!!\nin /srv/www/voyanga/public_html/protected/controllers/SiteController.php (140)\nin /srv/www/voyanga/public_html/index.php (13)'),
(2, 'info', 'system.kk', 1334319738, 'Hello people!!!\nin /srv/www/voyanga/public_html/protected/controllers/SiteController.php (140)\nin /srv/www/voyanga/public_html/index.php (13)'),
(3, 'info', 'system.kk', 1334323369, 'Hello people!!!\nin /srv/www/voyanga/public_html/protected/controllers/SiteController.php (124)\nin /srv/www/voyanga/public_html/index.php (13)'),
(4, 'info', 'system.kk', 1334323616, 'Hello people!!!\nin /srv/www/voyanga/public_html/protected/controllers/SiteController.php (125)\nin /srv/www/voyanga/public_html/index.php (13)');
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
