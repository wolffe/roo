-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 172.17.0.136
-- Generation Time: Mar 30, 2016 at 12:54 PM
-- Server version: 5.5.48
-- PHP Version: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db1392057_roo`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `to` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `message` text CHARACTER SET latin1 NOT NULL,
  `sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recd` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `data`
--

CREATE TABLE IF NOT EXISTS `data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `projecttype` tinyint(4) NOT NULL,
  `name` varchar(256) CHARACTER SET utf8 NOT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `priority` varchar(32) CHARACTER SET utf8 NOT NULL,
  `author` int(11) NOT NULL,
  `uids` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `flags`
--

CREATE TABLE IF NOT EXISTS `flags` (
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `flag` tinyint(4) NOT NULL,
  KEY `user_id` (`user_id`,`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `roo_priorities`
--

CREATE TABLE IF NOT EXISTS `roo_priorities` (
  `priority_id` int(11) NOT NULL AUTO_INCREMENT,
  `priority_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`priority_id`),
  KEY `priority_name` (`priority_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `roo_priorities`
--

INSERT INTO `roo_priorities` (`priority_id`, `priority_name`) VALUES
(5, 'closed'),
(6, 'critical'),
(2, 'high'),
(4, 'low'),
(3, 'medium');

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE IF NOT EXISTS `updates` (
  `updateid` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL,
  `udate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `udeadline` date NOT NULL DEFAULT '0000-00-00',
  `usubject` text CHARACTER SET latin1 NOT NULL,
  `udescription` text CHARACTER SET latin1 NOT NULL,
  `uuser` text CHARACTER SET latin1 NOT NULL,
  `type` tinyint(4) NOT NULL,
  `assignee` text CHARACTER SET latin1 NOT NULL,
  `parent` int(11) NOT NULL,
  `hidden` int(11) NOT NULL,
  UNIQUE KEY `updateid` (`updateid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `update_types`
--

CREATE TABLE IF NOT EXISTS `update_types` (
  `update_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `update_type_name` text CHARACTER SET latin1 NOT NULL,
  `update_type_colour` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`update_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `update_types`
--

INSERT INTO `update_types` (`update_type_id`, `update_type_name`, `update_type_colour`) VALUES
(1, 'Task', 'red'),
(2, 'Comment', 'blue'),
(3, 'Done', 'green'),
(4, 'Update', 'purple'),
(5, 'User Story', 'purple'),
(6, 'News Item', 'blue');

-- --------------------------------------------------------

--
-- Table structure for table `upload`
--

CREATE TABLE IF NOT EXISTS `upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `user` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `project_id_fk` int(11) DEFAULT NULL,
  `reply_id_fk` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `project_id_fk` (`project_id_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `userid` int(5) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL,
  `username` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `password` mediumtext CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `email` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `fullname` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `paypal` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `salt` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `lang` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `isnotify` tinyint(4) NOT NULL DEFAULT '1',
  `upp` tinyint(4) NOT NULL DEFAULT '10',
  `plan` int(11) NOT NULL,
  `timezone` varchar(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `registration_date` datetime NOT NULL,
  `currency` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
