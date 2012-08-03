-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 02, 2012 at 12:38 PM
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
-- Database: `search`
--
CREATE DATABASE `search` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `search`;

-- --------------------------------------------------------

--
-- Table structure for table `AuthAssignment`
--

CREATE TABLE IF NOT EXISTS `AuthAssignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `AuthItem`
--

CREATE TABLE IF NOT EXISTS `AuthItem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  `slug` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `AuthItemChild`
--

CREATE TABLE IF NOT EXISTS `AuthItemChild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `airline`
--

CREATE TABLE IF NOT EXISTS `airline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL DEFAULT '0',
  `code` varchar(5) NOT NULL,
  `localRu` varchar(45) DEFAULT NULL,
  `localEn` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1868 ;

-- --------------------------------------------------------

--
-- Table structure for table `airport`
--

CREATE TABLE IF NOT EXISTS `airport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `code` varchar(5) NOT NULL,
  `icaoCode` varchar(5) NOT NULL DEFAULT '',
  `localRu` varchar(45) DEFAULT NULL,
  `localEn` varchar(45) DEFAULT NULL,
  `cityId` int(11) NOT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `site` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_index` (`code`),
  KEY `fk_airport_city` (`cityId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6179 ;

-- --------------------------------------------------------

--
-- Table structure for table `benchmark`
--

CREATE TABLE IF NOT EXISTS `benchmark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text,
  `route` text,
  `params` text,
  `timeAdded` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `benchmark_result`
--

CREATE TABLE IF NOT EXISTS `benchmark_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benchmarkId` int(11) DEFAULT NULL,
  `initialLoadAverage` float DEFAULT NULL,
  `finalLoadAverage` float DEFAULT NULL,
  `serverSoftware` varchar(255) DEFAULT NULL,
  `serverHostname` varchar(255) DEFAULT NULL,
  `serverPort` int(11) DEFAULT NULL,
  `documentPath` text,
  `documentSize` int(11) DEFAULT NULL,
  `concurrency` int(11) DEFAULT NULL,
  `duration` float DEFAULT NULL,
  `completedRequests` int(11) DEFAULT NULL,
  `failedRequests` int(11) DEFAULT NULL,
  `failedOnConnect` int(11) DEFAULT NULL,
  `failedOnReceive` int(11) DEFAULT NULL,
  `failedOnLength` int(11) DEFAULT NULL,
  `failedOnException` int(11) DEFAULT NULL,
  `writeErrors` int(11) DEFAULT NULL,
  `totalTransferred` int(11) DEFAULT NULL,
  `htmlTransferred` int(11) DEFAULT NULL,
  `requestsPerSecond` float DEFAULT NULL,
  `timePerRequest` float DEFAULT NULL,
  `longestRequest` float DEFAULT NULL,
  `transferRate` float DEFAULT NULL,
  `timeAdded` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=58 ;

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE IF NOT EXISTS `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `userId` varchar(45) DEFAULT NULL,
  `flightId` varchar(45) DEFAULT NULL,
  `hotelId` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Table structure for table `booking_passport`
--

CREATE TABLE IF NOT EXISTS `booking_passport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(45) DEFAULT NULL,
  `lastName` varchar(45) DEFAULT NULL,
  `birthday` varchar(45) DEFAULT NULL,
  `series` varchar(45) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `bookingId` int(11) DEFAULT NULL,
  `documentTypeId` int(11) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `expiration` date DEFAULT NULL,
  `genderId` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_booking_passport_booking` (`bookingId`),
  KEY `fk_booking_passport_country` (`countryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE IF NOT EXISTS `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL,
  `localRu` varchar(45) DEFAULT NULL,
  `localEn` varchar(45) DEFAULT NULL,
  `countAirports` tinyint(1) NOT NULL DEFAULT '0',
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `hotelbookId` int(11) DEFAULT NULL,
  `metaphoneRu` varchar(20) DEFAULT NULL,
  `stateCode` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_city_country` (`countryId`),
  KEY `airportsCount` (`countAirports`),
  KEY `hotelbookId` (`hotelbookId`),
  KEY `cityNameRu` (`localRu`(3)),
  KEY `cityNameEn` (`localEn`(3)),
  KEY `cityCode` (`code`(3)),
  KEY `metaphone` (`metaphoneRu`(3))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9672 ;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL,
  `localRu` varchar(45) DEFAULT NULL,
  `localEn` varchar(45) DEFAULT NULL,
  `hotelbookId` int(11) DEFAULT NULL,
  `priority` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `priority` (`priority`),
  KEY `localRu` (`localRu`),
  KEY `positionOrder` (`position`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=251 ;

-- --------------------------------------------------------

--
-- Table structure for table `cron_task`
--

CREATE TABLE IF NOT EXISTS `cron_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerModel` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ownerId` int(11) DEFAULT NULL,
  `taskName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `taskId` int(11) DEFAULT NULL,
  `timeAdded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `uniqKey` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `executeTimestamp` timestamp NULL DEFAULT NULL,
  `executeOut` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `startDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL,
  `cityId` int(11) DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `preview` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_event_city` (`cityId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_category`
--

CREATE TABLE IF NOT EXISTS `event_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `root` int(11) unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lft` int(11) unsigned NOT NULL,
  `rgt` int(11) unsigned NOT NULL,
  `level` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `root` (`root`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_has_category`
--

CREATE TABLE IF NOT EXISTS `event_has_category` (
  `eventId` int(11) NOT NULL DEFAULT '0',
  `eventCategoryId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventId`,`eventCategoryId`),
  KEY `fk_event_has_category` (`eventCategoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_has_tag`
--

CREATE TABLE IF NOT EXISTS `event_has_tag` (
  `eventId` int(11) NOT NULL DEFAULT '0',
  `eventTagId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventId`,`eventTagId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_link`
--

CREATE TABLE IF NOT EXISTS `event_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eventId` int(11) DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_link_event` (`eventId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_tag`
--

CREATE TABLE IF NOT EXISTS `event_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `flight_booking`
--

CREATE TABLE IF NOT EXISTS `flight_booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(45) DEFAULT NULL,
  `pnr` varchar(10) DEFAULT NULL,
  `timeout` datetime DEFAULT NULL,
  `flightVoyageInfo` text,
  `updated` timestamp NULL DEFAULT NULL,
  `flightVoyageId` varchar(60) DEFAULT NULL,
  `orderBookingId` int(11) DEFAULT NULL,
  `nemoBookId` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_flight_booking_order_booking` (`orderBookingId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=58 ;

-- --------------------------------------------------------

--
-- Table structure for table `flight_booking_passport`
--

CREATE TABLE IF NOT EXISTS `flight_booking_passport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(45) DEFAULT NULL,
  `lastName` varchar(45) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `series` varchar(45) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `flightBookingId` int(11) DEFAULT NULL,
  `documentTypeId` int(11) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `expiration` date DEFAULT NULL,
  `genderId` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_flight_booking_passport_flight_booking` (`flightBookingId`),
  KEY `fk_flight_booking_passport_country` (`countryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

-- --------------------------------------------------------

--
-- Table structure for table `flight_cache`
--

CREATE TABLE IF NOT EXISTS `flight_cache` (
  `from` int(11) NOT NULL DEFAULT '0',
  `to` int(11) NOT NULL DEFAULT '0',
  `dateFrom` date NOT NULL DEFAULT '0000-00-00',
  `dateBack` date NOT NULL DEFAULT '0000-00-00',
  `priceBestPrice` int(11) DEFAULT NULL,
  `durationBestPrice` int(11) DEFAULT NULL,
  `validatorBestPrice` varchar(255) DEFAULT NULL,
  `transportBestPrice` varchar(255) DEFAULT NULL,
  `priceBestTime` int(11) DEFAULT NULL,
  `durationBestTime` int(11) DEFAULT NULL,
  `validatorBestTime` varchar(255) DEFAULT NULL,
  `transportBestTime` varchar(255) DEFAULT NULL,
  `priceBestPriceTime` int(11) DEFAULT NULL,
  `durationBestPriceTime` int(11) DEFAULT NULL,
  `validatorBestPriceTime` varchar(255) DEFAULT NULL,
  `transportBestPriceTime` varchar(255) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`from`,`to`,`dateFrom`,`dateBack`),
  KEY `dates` (`dateFrom`,`dateBack`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hotel`
--

CREATE TABLE IF NOT EXISTS `hotel` (
  `id` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `name` varchar(45) DEFAULT NULL,
  `description` text,
  `stars` tinyint(4) DEFAULT NULL,
  `cityId` int(11) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hotel_booking`
--

CREATE TABLE IF NOT EXISTS `hotel_booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(45) DEFAULT NULL,
  `expiration` datetime DEFAULT NULL,
  `hotelInfo` text,
  `updated` timestamp NULL DEFAULT NULL,
  `orderBookingId` int(11) DEFAULT NULL,
  `orderId` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `hotelResultKey` varchar(255) NOT NULL,
  `price` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_hotel_booking_order_booking` (`orderBookingId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Table structure for table `hotel_booking_passport`
--

CREATE TABLE IF NOT EXISTS `hotel_booking_passport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(45) DEFAULT NULL,
  `lastName` varchar(45) DEFAULT NULL,
  `birthday` varchar(45) DEFAULT NULL,
  `hotelBookingId` int(11) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `genderId` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `roomKey` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_booking_passport_country` (`countryId`),
  KEY `fk_hotel_booking_passport_hotel_booking` (`hotelBookingId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=75 ;

-- --------------------------------------------------------

--
-- Table structure for table `hotel_cache`
--

CREATE TABLE IF NOT EXISTS `hotel_cache` (
  `cityId` int(11) NOT NULL DEFAULT '0',
  `dateFrom` date NOT NULL DEFAULT '0000-00-00',
  `dateTo` date NOT NULL DEFAULT '0000-00-00',
  `stars` int(11) NOT NULL DEFAULT '0',
  `price` float DEFAULT NULL,
  `hotelId` int(11) DEFAULT NULL,
  `hotelName` varchar(255) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`cityId`,`dateFrom`,`dateTo`,`stars`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hotel_rating`
--

CREATE TABLE IF NOT EXISTS `hotel_rating` (
  `city_id` int(11) NOT NULL,
  `canonical_name` varchar(255) NOT NULL,
  `rating` float NOT NULL,
  UNIQUE KEY `pk` (`canonical_name`,`city_id`),
  KEY `fk_hotel_rating_city` (`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `log_table`
--

CREATE TABLE IF NOT EXISTS `log_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` varchar(128) DEFAULT NULL,
  `category` varchar(128) DEFAULT NULL,
  `logtime` int(11) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_booking`
--

CREATE TABLE IF NOT EXISTS `order_booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `userId` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_flight_voyage`
--

CREATE TABLE IF NOT EXISTS `order_flight_voyage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `departureDate` date DEFAULT NULL,
  `departureCity` int(11) DEFAULT NULL,
  `arrivalCity` int(11) DEFAULT NULL,
  `object` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `search_by_key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_has_flight_voyage`
--

CREATE TABLE IF NOT EXISTS `order_has_flight_voyage` (
  `orderId` int(11) NOT NULL DEFAULT '0',
  `orderFlightVoyage` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderId`,`orderFlightVoyage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `order_has_hotel`
--

CREATE TABLE IF NOT EXISTS `order_has_hotel` (
  `orderId` int(11) NOT NULL DEFAULT '0',
  `orderHotel` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`orderId`,`orderHotel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `order_hotel`
--

CREATE TABLE IF NOT EXISTS `order_hotel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `cityId` int(11) DEFAULT NULL,
  `checkIn` date DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `object` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `search_by_key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `passport`
--

CREATE TABLE IF NOT EXISTS `passport` (
  `id` int(11) NOT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `lastName` varchar(45) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `birthday` varchar(45) DEFAULT NULL,
  `series` varchar(45) DEFAULT NULL,
  `documentTypeId` int(11) DEFAULT NULL,
  `countryId` int(11) DEFAULT NULL,
  `genderId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(11) NOT NULL,
  `stateId` int(11) DEFAULT NULL,
  `timestamp` varchar(45) DEFAULT NULL,
  `paymentSystemId` varchar(45) DEFAULT NULL,
  `price` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payments_transaction`
--

CREATE TABLE IF NOT EXISTS `payments_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,0) DEFAULT NULL,
  `status` varchar(2) DEFAULT NULL,
  `transaction_id` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `uniteller_status` varchar(20) DEFAULT NULL,
  `prev` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payments_transaction_self` (`prev`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_migration`
--

CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `stateId` varchar(45) DEFAULT NULL,
  `timestamp` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `AuthAssignment`
--
ALTER TABLE `AuthAssignment`
  ADD CONSTRAINT `AuthAssignment_ibfk_1` FOREIGN KEY (`itemname`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `AuthItemChild`
--
ALTER TABLE `AuthItemChild`
  ADD CONSTRAINT `AuthItemChild_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `AuthItemChild_ibfk_2` FOREIGN KEY (`child`) REFERENCES `AuthItem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `airport`
--
ALTER TABLE `airport`
  ADD CONSTRAINT `fk_airport_city` FOREIGN KEY (`cityId`) REFERENCES `city` (`id`);

--
-- Constraints for table `booking_passport`
--
ALTER TABLE `booking_passport`
  ADD CONSTRAINT `fk_booking_passport_booking` FOREIGN KEY (`bookingId`) REFERENCES `booking` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_booking_passport_country` FOREIGN KEY (`countryId`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `city`
--
ALTER TABLE `city`
  ADD CONSTRAINT `fk_city_country` FOREIGN KEY (`countryId`) REFERENCES `country` (`id`);

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_city` FOREIGN KEY (`cityId`) REFERENCES `city` (`id`);

--
-- Constraints for table `event_has_category`
--
ALTER TABLE `event_has_category`
  ADD CONSTRAINT `fk_category_has_event` FOREIGN KEY (`eventId`) REFERENCES `event` (`id`),
  ADD CONSTRAINT `fk_event_has_category` FOREIGN KEY (`eventCategoryId`) REFERENCES `event_category` (`id`);

--
-- Constraints for table `event_link`
--
ALTER TABLE `event_link`
  ADD CONSTRAINT `fk_link_event` FOREIGN KEY (`eventId`) REFERENCES `event` (`id`);

--
-- Constraints for table `flight_booking`
--
ALTER TABLE `flight_booking`
  ADD CONSTRAINT `fk_flight_booking_order_booking` FOREIGN KEY (`orderBookingId`) REFERENCES `order_booking` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `flight_booking_passport`
--
ALTER TABLE `flight_booking_passport`
  ADD CONSTRAINT `fk_flight_booking_passport_country` FOREIGN KEY (`countryId`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_flight_booking_passport_flight_booking` FOREIGN KEY (`flightBookingId`) REFERENCES `flight_booking` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `hotel_booking`
--
ALTER TABLE `hotel_booking`
  ADD CONSTRAINT `fk_hotel_booking_order_booking` FOREIGN KEY (`orderBookingId`) REFERENCES `order_booking` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `hotel_booking_passport`
--
ALTER TABLE `hotel_booking_passport`
  ADD CONSTRAINT `fk_booking_passport_country0` FOREIGN KEY (`countryId`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_hotel_booking_passport_hotel_booking` FOREIGN KEY (`hotelBookingId`) REFERENCES `hotel_booking` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `hotel_rating`
--
ALTER TABLE `hotel_rating`
  ADD CONSTRAINT `fk_hotel_rating_city` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `payments_transaction`
--
ALTER TABLE `payments_transaction`
  ADD CONSTRAINT `fk_payments_transaction_self` FOREIGN KEY (`prev`) REFERENCES `payments_transaction` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
