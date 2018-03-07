-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 07, 2018 at 09:10 AM
-- Server version: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `upstrain`
--

-- --------------------------------------------------------

--
-- Table structure for table `attempt_log`
--

CREATE TABLE IF NOT EXISTS `attempt_log` (
  `ip` varchar(50) NOT NULL,
  `time` int(100) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attempts` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COMMENT='Logs failed login attempts';

--
-- Dumping data for table `attempt_log`
--

INSERT INTO `attempt_log` (`ip`, `time`, `username`, `id`, `attempts`) VALUES
('::1', 1520184966, 'WG95', 1, 1),
('::1', 1520241982, 'admin2', 2, 0),
('::1', 0, 'admin', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `backbone`
--

CREATE TABLE IF NOT EXISTS `backbone` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `Bb_reg` varchar(50) DEFAULT NULL,
  `date_db` varchar(10) NOT NULL,
  `creator` int(3) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bb_name` (`name`),
  KEY `bb_creat_id` (`creator`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `backbone`
--

INSERT INTO `backbone` (`id`, `name`, `Bb_reg`, `date_db`, `creator`, `comment`, `private`) VALUES
(2, 'test', 'test', '2018-02-10', 1, 'bleh', 0);

--
-- Triggers `backbone`
--
DELIMITER $$
CREATE TRIGGER `log_backbone_add` AFTER INSERT ON `backbone` FOR EACH ROW insert into event_log (object, object_id, time, type) values("Backbone", new.id, NOW(), "Added")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_backbone_delete` BEFORE DELETE ON `backbone` FOR EACH ROW insert into event_log (object, object_id, time, type) values("Backbone", old.id, NOW(), "Deleted")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_backbone_update` AFTER UPDATE ON `backbone` FOR EACH ROW insert into event_log (object, object_id, time, type) values("Backbone", old.id, NOW(), "Edited")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_deleted_backbone` BEFORE DELETE ON `backbone` FOR EACH ROW insert into backbone_log(BB_reg, comment, creator, date_db, id, name, time, type, private) values(old.BB_reg, old.comment, old.creator, old.date_db, old.id, old.name, UNIX_TIMESTAMP(NOW()), "Deleted", old.private)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_updated_backbone` AFTER UPDATE ON `backbone` FOR EACH ROW insert into backbone_log(BB_reg, comment, creator, date_db, id, name, time, type, private) values(old.BB_reg, old.comment, old.creator, old.date_db, old.id, old.name, UNIX_TIMESTAMP(NOW()), "Edited", old.private)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `backbone_log`
--

CREATE TABLE IF NOT EXISTS `backbone_log` (
  `id` int(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `Bb_reg` varchar(50) DEFAULT NULL,
  `date_db` varchar(10) NOT NULL,
  `creator` int(3) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `old_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(15) NOT NULL,
  `time` int(100) UNSIGNED NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`old_data_id`),
  KEY `bb_creat_id` (`creator`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `backbone_log`
--

INSERT INTO `backbone_log` (`id`, `name`, `Bb_reg`, `date_db`, `creator`, `comment`, `old_data_id`, `type`, `time`, `private`) VALUES
(2, 'test', 'test', '2018-02-10', 1, 'bleh', 1, 'Edited', 1520246191, 0),
(2, 'test', 'test', '2018-02-10', 1, 'blabla', 2, 'Edited', 1520330486, 0);

-- --------------------------------------------------------

--
-- Table structure for table `entry`
--

CREATE TABLE IF NOT EXISTS `entry` (
  `id` int(3) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `comment` varchar(100) NOT NULL,
  `year_created` int(4) NOT NULL,
  `date_db` varchar(10) NOT NULL,
  `entry_reg` varchar(50) DEFAULT NULL,
  `backbone` int(3) NOT NULL,
  `strain` int(3) NOT NULL,
  `creator` int(3) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `created` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `backbone_id` (`backbone`),
  KEY `strain_id` (`strain`),
  KEY `creator_id` (`creator`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `entry`
--

INSERT INTO `entry` (`id`, `comment`, `year_created`, `date_db`, `entry_reg`, `backbone`, `strain`, `creator`, `private`, `created`) VALUES
(001, 'testestestest', 2018, '2018-02-07', 'test', 2, 1, 1, 0, 1);

--
-- Triggers `entry`
--
DELIMITER $$
CREATE TRIGGER `create_upstrain_id` AFTER INSERT ON `entry` FOR EACH ROW INSERT INTO entry_upstrain (entry_id,upstrain_id) VALUES (new.id,CONCAT("UU",new.year_created,new.id))
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_entry_add` AFTER INSERT ON `entry` FOR EACH ROW INSERT INTO event_log(object, object_id, time, type) VALUES("Entry",NEW.id, NOW(), "Added")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_entry_delete` BEFORE DELETE ON `entry` FOR EACH ROW INSERT INTO event_log(object, object_id, time, type) VALUES("Entry", OLD.id, NOW(), "Deleted")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_entry_update` AFTER UPDATE ON `entry` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Entry", OLD.id, NOW(), "Edited")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_deleted_entry` BEFORE DELETE ON `entry` FOR EACH ROW INSERT into entry_log(backbone, comment, creator, date_db, entry_reg, id, private, strain, year_created, type, time, created) VALUES(OLD.backbone, OLD.comment, OLD.creator, OLD.date_db, OLD.entry_reg, OLD.id, OLD.private, OLD.strain, OLD.year_created, "Deleted", UNIX_TIMESTAMP(NOW()), old.created)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_updated_entry` AFTER UPDATE ON `entry` FOR EACH ROW INSERT INTO entry_log(backbone, comment, creator, date_db, entry_reg, id, private, strain, year_created, type, time, created) VALUES(OLD.backbone, OLD.comment, OLD.creator, OLD.date_db, OLD.entry_reg, OLD.id, OLD.private, OLD.strain, OLD.year_created, "Edited", UNIX_TIMESTAMP(NOW()), old.created)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `entry_inserts`
--

CREATE TABLE IF NOT EXISTS `entry_inserts` (
  `entry_id` int(3) UNSIGNED ZEROFILL NOT NULL,
  `insert_id` int(3) NOT NULL,
  `position` int(11) NOT NULL,
  UNIQUE KEY `entry_ins_link` (`entry_id`,`position`) USING BTREE,
  KEY `ins` (`insert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `entry_inserts`
--

INSERT INTO `entry_inserts` (`entry_id`, `insert_id`, `position`) VALUES
(001, 3, 1),
(001, 3, 3),
(001, 4, 2);

--
-- Triggers `entry_inserts`
--
DELIMITER $$
CREATE TRIGGER `log_entryinsert_add` AFTER INSERT ON `entry_inserts` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Entry-insert link", CONCAT(new.entry_id, "-", new.insert_id), NOW(), "Added")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_entryinsert_delete` BEFORE DELETE ON `entry_inserts` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Entry-insert link", CONCAT(old.entry_id, "-", old.insert_id), NOW(), "Delete")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_entryinsert_update` AFTER UPDATE ON `entry_inserts` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Entry-insert link", CONCAT(old.entry_id, "-", old.insert_id), NOW(), "Edited")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_deleted_entryinsert` BEFORE DELETE ON `entry_inserts` FOR EACH ROW insert into entry_inserts_log(entry_id, insert_id, position, time, type) values(old.entry_id, old.insert_id, old.position, UNIX_TIMESTAMP(NOW()), "Deleted")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_updated_entryinsert` AFTER UPDATE ON `entry_inserts` FOR EACH ROW insert into entry_inserts_log(entry_id, insert_id, position, time, type) values(old.entry_id, old.insert_id, old.position, UNIX_TIMESTAMP(NOW()), "Edited")
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `entry_inserts_log`
--

CREATE TABLE IF NOT EXISTS `entry_inserts_log` (
  `entry_id` int(3) UNSIGNED ZEROFILL NOT NULL,
  `insert_id` int(3) NOT NULL,
  `position` int(11) NOT NULL,
  `old_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(15) NOT NULL,
  `time` int(100) UNSIGNED NOT NULL,
  PRIMARY KEY (`old_data_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `entry_inserts_log`
--

INSERT INTO `entry_inserts_log` (`entry_id`, `insert_id`, `position`, `old_data_id`, `type`, `time`) VALUES
(001, 5, 3, 1, 'Deleted', 1519824306);

-- --------------------------------------------------------

--
-- Table structure for table `entry_log`
--

CREATE TABLE IF NOT EXISTS `entry_log` (
  `id` int(3) UNSIGNED ZEROFILL NOT NULL,
  `comment` varchar(100) DEFAULT NULL,
  `year_created` int(4) NOT NULL,
  `date_db` varchar(10) NOT NULL,
  `entry_reg` varchar(50) DEFAULT NULL,
  `backbone` int(3) NOT NULL,
  `strain` int(3) NOT NULL,
  `creator` int(3) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `old_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(15) NOT NULL,
  `time` int(1) UNSIGNED NOT NULL,
  `created` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`old_data_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `entry_log`
--

INSERT INTO `entry_log` (`id`, `comment`, `year_created`, `date_db`, `entry_reg`, `backbone`, `strain`, `creator`, `private`, `old_data_id`, `type`, `time`, `created`) VALUES
(002, 'test for log', 2016, '2018-02-26', 'logtest', 2, 1, 7, 0, 1, 'Deleted', 1519646513, 1),
(003, 'test2 for log', 2015, '2018-02-26', 'sdfada', 2, 1, 9, 0, 2, 'Deleted', 1519646595, 1);

-- --------------------------------------------------------

--
-- Table structure for table `entry_upstrain`
--

CREATE TABLE IF NOT EXISTS `entry_upstrain` (
  `entry_id` int(3) UNSIGNED ZEROFILL NOT NULL,
  `upstrain_id` varchar(10) NOT NULL,
  PRIMARY KEY (`upstrain_id`),
  UNIQUE KEY `id_link` (`entry_id`,`upstrain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `entry_upstrain`
--

INSERT INTO `entry_upstrain` (`entry_id`, `upstrain_id`) VALUES
(001, 'UU2018001');

-- --------------------------------------------------------

--
-- Table structure for table `event_log`
--

CREATE TABLE IF NOT EXISTS `event_log` (
  `object_id` int(3) UNSIGNED ZEROFILL NOT NULL,
  `object` varchar(20) NOT NULL,
  `type` varchar(15) NOT NULL,
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `time` varchar(20) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event_log`
--

INSERT INTO `event_log` (`object_id`, `object`, `type`, `event_id`, `time`) VALUES
(003, 'Entry', 'Added', 1, '2018-02-22 15:49:19'),
(003, 'Entry', 'Deleted', 2, '2018-02-22 16:21:14'),
(012, 'User', 'Added', 3, '2018-02-22 16:56:38'),
(012, 'User', 'Deleted', 4, '2018-02-22 16:59:56'),
(002, 'User', 'Deleted', 5, '2018-02-22 17:00:46'),
(002, 'Entry', 'Added', 6, '2018-02-26 12:21:46'),
(001, 'User', 'Edited', 7, '2018-02-26 12:23:38'),
(001, 'User', 'Edited', 9, '2018-02-26 12:37:53'),
(001, 'User', 'Edited', 10, '2018-02-26 12:41:58'),
(001, 'User', 'Edited', 11, '2018-02-26 12:44:35'),
(001, 'User', 'Edited', 12, '2018-02-26 12:45:31'),
(001, 'User', 'Edited', 13, '2018-02-26 12:59:43'),
(001, 'User', 'Edited', 14, '2018-02-26 13:00:33'),
(002, 'Entry', 'Deleted', 15, '2018-02-26 13:01:53'),
(003, 'Entry', 'Added', 16, '2018-02-26 13:02:57'),
(003, 'Entry', 'Deleted', 17, '2018-02-26 13:03:15'),
(001, 'User', 'Edited', 18, '2018-02-27 12:16:50'),
(001, 'User', 'Edited', 19, '2018-02-27 12:17:42'),
(010, 'User', 'Added', 20, '2018-02-27 15:21:50'),
(010, 'User', 'Edited', 21, '2018-02-27 15:23:34'),
(001, 'User', 'Edited', 22, '2018-03-01 10:19:26'),
(001, 'User', 'Edited', 23, '2018-03-01 10:29:10'),
(001, 'User', 'Edited', 24, '2018-03-01 11:19:19'),
(001, 'User', 'Edited', 25, '2018-03-01 12:33:28'),
(001, 'User', 'Edited', 26, '2018-03-01 13:36:12'),
(001, 'User', 'Edited', 27, '2018-03-01 15:05:54'),
(001, 'User', 'Edited', 28, '2018-03-01 15:06:05'),
(001, 'User', 'Edited', 29, '2018-03-01 15:31:57'),
(001, 'User', 'Edited', 30, '2018-03-01 17:14:54'),
(001, 'User', 'Edited', 31, '2018-03-02 10:49:29'),
(001, 'User', 'Edited', 33, '2018-03-02 12:08:36'),
(001, 'User', 'Edited', 34, '2018-03-02 12:08:44'),
(001, 'Strain', 'Edited', 35, '2018-03-02 17:50:12'),
(001, 'User', 'Edited', 36, '2018-03-03 11:34:07'),
(009, 'User', 'Edited', 37, '2018-03-03 11:34:37'),
(009, 'User', 'Edited', 38, '2018-03-03 11:35:13'),
(003, 'Insert', 'Edited', 50, '2018-03-03 12:02:28'),
(003, 'Insert', 'Edited', 51, '2018-03-03 12:05:12'),
(003, 'Insert', 'Edited', 52, '2018-03-03 12:05:31'),
(003, 'Insert', 'Edited', 53, '2018-03-03 12:05:48'),
(003, 'Insert', 'Edited', 54, '2018-03-03 12:06:06'),
(011, 'User', 'Added', 55, '2018-03-03 23:04:05'),
(011, 'User', 'Deleted', 56, '2018-03-03 23:06:45'),
(012, 'User', 'Added', 57, '2018-03-03 23:10:01'),
(012, 'User', 'Edited', 58, '2018-03-04 16:14:19'),
(012, 'User', 'Edited', 59, '2018-03-04 21:46:27'),
(012, 'User', 'Deleted', 60, '2018-03-04 21:49:51'),
(002, 'Backbone', 'Edited', 61, '2018-03-05 11:36:31'),
(002, 'Backbone', 'Edited', 62, '2018-03-06 11:01:26'),
(011, 'User', 'Added', 63, '2018-03-06 12:28:04'),
(011, 'User', 'Edited', 64, '2018-03-06 12:29:51'),
(011, 'User', 'Edited', 65, '2018-03-06 12:30:14'),
(011, 'User', 'Edited', 66, '2018-03-06 12:30:16'),
(011, 'User', 'Edited', 67, '2018-03-06 12:30:22'),
(011, 'User', 'Edited', 68, '2018-03-06 12:30:24'),
(011, 'User', 'Edited', 69, '2018-03-06 12:30:26'),
(001, 'Strain', 'Edited', 70, '2018-03-06 14:31:15'),
(001, 'Strain', 'Edited', 71, '2018-03-06 14:32:38'),
(006, 'Insert', 'Added', 72, '2018-03-06 17:04:26'),
(006, 'Insert', 'Deleted', 73, '2018-03-06 17:05:45'),
(006, 'Insert', 'Added', 74, '2018-03-06 17:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `ins`
--

CREATE TABLE IF NOT EXISTS `ins` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` int(3) NOT NULL,
  `ins_reg` varchar(50) NOT NULL,
  `creator` int(3) NOT NULL,
  `date_db` varchar(10) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `insert_name` (`name`),
  KEY `creator_id` (`creator`),
  KEY `ins_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ins`
--

INSERT INTO `ins` (`id`, `name`, `type`, `ins_reg`, `creator`, `date_db`, `comment`, `private`) VALUES
(3, 'test ins', 2, 'test ins reg', 1, '2018-02-09', 'blahbleh', 0),
(4, 'test ins 2', 3, 'test ins reg 2', 1, '2018-02-10', 'blah', 0),
(5, 'test ins 3', 3, 'test ins reg 3', 1, '2018-02-10', 'blah', 0),
(6, 'test test', 2, 'BBa_K234243', 7, '2018-03-06', ' lol', 0);

--
-- Triggers `ins`
--
DELIMITER $$
CREATE TRIGGER `log_insert_add` AFTER INSERT ON `ins` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Insert", new.id, NOW(), "Added")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_insert_delete` BEFORE DELETE ON `ins` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Insert", old.id, NOW(), "Deleted")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_insert_update` AFTER UPDATE ON `ins` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Insert", old.id, NOW(), "Edited")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_deleted_insert` BEFORE DELETE ON `ins` FOR EACH ROW insert into ins_log(comment, creator, date_db, id, ins_reg, name, time, event_type, type, private) values(old.comment, old.creator, old.date_db, old.id, old. ins_reg, old.name, UNIX_TIMESTAMP(NOW()), "Deleted", old.type, old.private)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_updated_insert` AFTER UPDATE ON `ins` FOR EACH ROW insert into ins_log(comment, creator, date_db, id, ins_reg, name, time, type, event_type, private) values(old.comment, old.creator, old.date_db, old.id, old.ins_reg, old.name, UNIX_TIMESTAMP(NOW()), old.type, "Edited", old.private)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ins_log`
--

CREATE TABLE IF NOT EXISTS `ins_log` (
  `id` int(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` int(3) NOT NULL,
  `ins_reg` varchar(50) NOT NULL,
  `creator` int(3) NOT NULL,
  `date_db` varchar(10) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `old_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(15) NOT NULL,
  `time` int(100) UNSIGNED NOT NULL,
  `private` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`old_data_id`),
  KEY `creator_id` (`creator`),
  KEY `ins_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ins_log`
--

INSERT INTO `ins_log` (`id`, `name`, `type`, `ins_reg`, `creator`, `date_db`, `comment`, `old_data_id`, `event_type`, `time`, `private`) VALUES
(3, 'test ins', 2, 'test ins reg', 1, '2018-02-09', 'blah', 1, 'Edited', 1520074948, 0),
(3, 'test ins', 2, 'test ins reg', 1, '2018-02-09', 'blahbleh', 2, 'Edited', 1520075112, 0),
(3, 'test ins', 2, 'test ins reg', 1, '2018-02-09', 'blah', 3, 'Edited', 1520075131, 0),
(3, 'test ins', 2, 'test ins reg', 1, '2018-02-09', 'blahbleh', 4, 'Edited', 1520075148, 0),
(3, 'test ins', 2, 'test ins reg', 1, '2018-02-09', 'blah', 5, 'Edited', 1520075166, 0),
(6, 'test test', 2, 'BBa_K234243', 7, '2018-03-06', ' lol', 6, 'Deleted', 1520352345, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ins_type`
--

CREATE TABLE IF NOT EXISTS `ins_type` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ins_type`
--

INSERT INTO `ins_type` (`id`, `name`) VALUES
(2, 'Coding'),
(3, 'Promotor'),
(4, 'RBS');

--
-- Triggers `ins_type`
--
DELIMITER $$
CREATE TRIGGER `log_instype_add` AFTER INSERT ON `ins_type` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Insert type", new.id, NOW(), "Added")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_instype_delete` BEFORE DELETE ON `ins_type` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Insert type", old.id, NOW(), "Deleted")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_instype_update` AFTER UPDATE ON `ins_type` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Insert type", old.id, NOW(), "Edited")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_deleted_instype` BEFORE DELETE ON `ins_type` FOR EACH ROW insert into ins_type_log(id, name, time, type) values(old.id, old.name, UNIX_TIMESTAMP(NOW()), "Deleted")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_updated_instype` AFTER UPDATE ON `ins_type` FOR EACH ROW insert into ins_type_log(id, name, time, type) values(old.id, old.name, UNIX_TIMESTAMP(NOW()), "Updated")
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ins_type_log`
--

CREATE TABLE IF NOT EXISTS `ins_type_log` (
  `id` int(3) NOT NULL,
  `name` varchar(30) NOT NULL,
  `old_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(15) NOT NULL,
  `time` int(100) UNSIGNED NOT NULL,
  PRIMARY KEY (`old_data_id`),
  UNIQUE KEY `type_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `strain`
--

CREATE TABLE IF NOT EXISTS `strain` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `creator` int(3) NOT NULL,
  `date_db` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `strain_name` (`name`),
  KEY `strain_creator` (`creator`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `strain`
--

INSERT INTO `strain` (`id`, `name`, `comment`, `private`, `creator`, `date_db`) VALUES
(1, 'test', '', 0, 1, '2018-03-02');

--
-- Triggers `strain`
--
DELIMITER $$
CREATE TRIGGER `log_strain_add` AFTER INSERT ON `strain` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Strain", new.id, NOW(), "Added")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_strain_delete` BEFORE DELETE ON `strain` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Strain", old.id, NOW(), "Deleted")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_strain_update` AFTER UPDATE ON `strain` FOR EACH ROW insert into event_log(object, object_id, time, type) values("Strain", old.id, NOW(), "Edited")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_deleted_strain` BEFORE DELETE ON `strain` FOR EACH ROW insert into strain_log(comment, id, name, time, type, date_db, private, creator) values(old.comment, old.id, old.name, UNIX_TIMESTAMP(NOW()), "Deleted", old.date_db, old.private, old.creator)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_updated_strain` AFTER UPDATE ON `strain` FOR EACH ROW insert into strain_log(comment, id, name, time, type, date_db, private, creator) values(old.comment, old.id, old.name, UNIX_TIMESTAMP(NOW()), "Edited", old.date_db, old.private, old.creator)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `strain_log`
--

CREATE TABLE IF NOT EXISTS `strain_log` (
  `id` int(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `old_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(15) NOT NULL,
  `time` int(100) UNSIGNED NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `date_db` varchar(15) NOT NULL,
  `creator` int(3) NOT NULL,
  PRIMARY KEY (`old_data_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `strain_log`
--

INSERT INTO `strain_log` (`id`, `name`, `comment`, `old_data_id`, `type`, `time`, `private`, `date_db`, `creator`) VALUES
(1, 'test', '', 1, 'Edited', 1520343075, 0, '2018-03-02', 1),
(1, 'test', 'bla', 2, 'Edited', 1520343158, 0, '2018-03-02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `upstrain_file`
--

CREATE TABLE IF NOT EXISTS `upstrain_file` (
  `upstrain_id` varchar(10) NOT NULL,
  `name_original` varchar(50) NOT NULL,
  `name_new` varchar(20) GENERATED ALWAYS AS (concat(`upstrain_id`,'.fasta')) VIRTUAL,
  PRIMARY KEY (`upstrain_id`),
  UNIQUE KEY `name_new` (`name_new`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `upstrain_file`
--

INSERT INTO `upstrain_file` (`upstrain_id`, `name_original`) VALUES
('UU2018001', 'test.txt');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(3) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(14) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `hash` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `username`, `password`, `hash`, `active`, `admin`) VALUES
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testy', '', '', 0, 0),
(7, 'Admin', 'Adminson', 'admin.adminson@testmail.com', '536545', 'admin2', '$2y$10$ZAsTVraYj6XTRxCJ1Jgy0enqbAp89w/BLjyMmWz4uSxahoz0a6xCm', 'e7b24b112a44fdd9ee93bdf998c6ca0e', 1, 1),
(9, 'testy', 'testville', 'testytestville@testyness.com', '57466446', 'testytest', '$2y$10$mfunilAu.QVka8M0V0cZUeZ9duzDXQH.UMYn5BsfoGYsyVh59LjuS', '704afe073992cbe4813cae2f7715336f', 1, 1),
(10, 'Fredrik', 'Lindeberg', 'fredrik.lindeberg@igemuppsala.se', '', 'FredrikLindeberg', '$2y$10$E5YGenXrBZRwdFVSiFp4TuLVLGayAmZo8mJeaxrG7jKMTHEVaNBTi', '912d2b1c7b2826caf99687388d2e8f7c', 1, 1),
(11, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '761059274', 'wigu95', '$2y$10$lgJ0qp.84./MrDnzB9jh7.rB8tqFeG3nmjmy.SbqPZfT/Hv6pzKj.', 'f899139df5e1059396431415e770c6dd', 0, 0);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `log_user_add` AFTER INSERT ON `users` FOR EACH ROW INSERT INTO event_log(object, object_id, time, type) VALUES("User", NEW.user_id, NOW(), "Added")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_user_delete` BEFORE DELETE ON `users` FOR EACH ROW INSERT INTO event_log(object, object_id, time, type) VALUES("User", OLD.user_id, NOW(), "Deleted")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_user_edit` AFTER UPDATE ON `users` FOR EACH ROW insert into event_log(object, object_id, time, type) values("User", OLD.user_id, NOW(), "Edited")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_deleted_user` BEFORE DELETE ON `users` FOR EACH ROW insert into users_log(active, admin, email, first_name, hash, last_name, password, phone, username, user_id, time, type) values(old.active, old.admin, old.email, old.first_name, old.hash, old.last_name, old.password, old.phone, old.username, old.user_id, UNIX_TIMESTAMP(NOW()), "Deleted")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `save_updated_user` AFTER UPDATE ON `users` FOR EACH ROW insert into users_log(active, admin, email, first_name, hash, last_name, password, phone, username, user_id, time, type) values(old.active, old.admin, old.email, old.first_name, old.hash, old.last_name, old.password, old.phone, old.username, old.user_id, UNIX_TIMESTAMP(NOW()), "Edited")
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users_log`
--

CREATE TABLE IF NOT EXISTS `users_log` (
  `user_id` int(3) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(14) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `hash` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `old_data_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(15) NOT NULL,
  `time` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`old_data_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_log`
--

INSERT INTO `users_log` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `username`, `password`, `hash`, `active`, `admin`, `old_data_id`, `type`, `time`) VALUES
(1, 'test', 'testson', 'email@email.com', '0123456789', 'testlord', '', '', 0, 0, 1, 'Edited', 1519646383),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testlord', '', '', 0, 0, 2, 'Edited', 1519646433),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testmaster', '', '', 0, 0, 3, 'Edited', 1519730210),
(1, 'hej', 'hej', 'mail@mail.com', '0123456789', 'testmaster', '', '', 0, 0, 4, 'Edited', 1519730262),
(10, 'Fredrik', 'Lindeberg', 'fredrik.lindeberg@igemuppsala.se', '', 'FredrikLindeberg', '$2y$10$E5YGenXrBZRwdFVSiFp4TuLVLGayAmZo8mJeaxrG7jKMTHEVaNBTi', '912d2b1c7b2826caf99687388d2e8f7c', 0, 0, 5, 'Edited', 1519741414),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testmaster', '', '', 0, 0, 6, 'Edited', 1519895966),
(1, 'test', 'testson', 'email@email.com', '0123456789', 'testlord', '', '', 0, 0, 7, 'Edited', 1519896550),
(1, 'test', 'testson', 'email@email.com', '0123456789', 'testlord', '', '', 0, 0, 8, 'Edited', 1519899559),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testmaster', '', '', 0, 0, 9, 'Edited', 1519904008),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testlord', '', '', 0, 0, 10, 'Edited', 1519907772),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testmaster', '', '', 0, 0, 11, 'Edited', 1519913154),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testy', '', '', 0, 0, 12, 'Edited', 1519913165),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testmaster', '', '', 0, 0, 13, 'Edited', 1519914717),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testy', '', '', 0, 0, 14, 'Edited', 1519920894),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testmaster', '', '', 0, 0, 15, 'Edited', 1519984169),
(1, 'test', 'testson', 'email@email.com', '0123456789', 'testlord', '', '', 0, 0, 16, 'Edited', 1519988916),
(1, 'test', 'testson', 'mail@mail.com', '0123456789', 'testmaster', '', '', 0, 0, 17, 'Edited', 1519988924),
(1, 'test', 'testson', 'email@email.com', '0123456789', 'testlord', '', '', 0, 0, 18, 'Edited', 1520073247),
(9, 'testy', 'testville', 'testytestville@testyness.com', '57466446', 'testytest', '$2y$10$mfunilAu.QVka8M0V0cZUeZ9duzDXQH.UMYn5BsfoGYsyVh59LjuS', '704afe073992cbe4813cae2f7715336f', 1, 1, 19, 'Edited', 1520073277),
(9, 'Testy', 'McTestface', 'testytestville@testyness.com', '57466446', 'testytest', '$2y$10$mfunilAu.QVka8M0V0cZUeZ9duzDXQH.UMYn5BsfoGYsyVh59LjuS', '704afe073992cbe4813cae2f7715336f', 1, 1, 20, 'Edited', 1520073313),
(11, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '761059274', 'wigu95', '$2y$10$lgJ0qp.84./MrDnzB9jh7.rB8tqFeG3nmjmy.SbqPZfT/Hv6pzKj.', 'f899139df5e1059396431415e770c6dd', 0, 0, 21, 'Deleted', 1520114805),
(12, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '0761059274', 'WG95', '$2y$10$ClrWba5cgnf.J31Ff1iUi.P9Uk2dcKK0XyOm/pOzKP00lCwCJ76VG', 'f899139df5e1059396431415e770c6dd', 0, 0, 22, 'Edited', 1520176459),
(12, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '0761059274', 'WG95', '$2y$10$ClrWba5cgnf.J31Ff1iUi.P9Uk2dcKK0XyOm/pOzKP00lCwCJ76VG', 'f899139df5e1059396431415e770c6dd', 1, 0, 23, 'Edited', 1520196387),
(12, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '0761059274', 'WG95', '$2y$10$ClrWba5cgnf.J31Ff1iUi.P9Uk2dcKK0XyOm/pOzKP00lCwCJ76VG', 'f899139df5e1059396431415e770c6dd', 1, 1, 24, 'Deleted', 1520196591),
(11, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '761059274', 'wigu95', '$2y$10$lgJ0qp.84./MrDnzB9jh7.rB8tqFeG3nmjmy.SbqPZfT/Hv6pzKj.', 'f899139df5e1059396431415e770c6dd', 0, 0, 25, 'Edited', 1520335791),
(11, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '761059274', 'wigu95', '$2y$10$lgJ0qp.84./MrDnzB9jh7.rB8tqFeG3nmjmy.SbqPZfT/Hv6pzKj.', 'f899139df5e1059396431415e770c6dd', 0, 0, 26, 'Edited', 1520335814),
(11, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '761059274', 'wigu95', '$2y$10$lgJ0qp.84./MrDnzB9jh7.rB8tqFeG3nmjmy.SbqPZfT/Hv6pzKj.', 'f899139df5e1059396431415e770c6dd', 0, 0, 27, 'Edited', 1520335816),
(11, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '761059274', 'wigu95', '$2y$10$lgJ0qp.84./MrDnzB9jh7.rB8tqFeG3nmjmy.SbqPZfT/Hv6pzKj.', 'f899139df5e1059396431415e770c6dd', 0, 0, 28, 'Edited', 1520335822),
(11, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '761059274', 'wigu95', '$2y$10$lgJ0qp.84./MrDnzB9jh7.rB8tqFeG3nmjmy.SbqPZfT/Hv6pzKj.', 'f899139df5e1059396431415e770c6dd', 0, 0, 29, 'Edited', 1520335824),
(11, 'Wiktor', 'Gustafsson', 'wiktorg.95@gmail.com', '761059274', 'wigu95', '$2y$10$lgJ0qp.84./MrDnzB9jh7.rB8tqFeG3nmjmy.SbqPZfT/Hv6pzKj.', 'f899139df5e1059396431415e770c6dd', 0, 0, 30, 'Edited', 1520335826);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `backbone`
--
ALTER TABLE `backbone`
  ADD CONSTRAINT `bb_creat_id` FOREIGN KEY (`creator`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `entry`
--
ALTER TABLE `entry`
  ADD CONSTRAINT `backbone_id` FOREIGN KEY (`backbone`) REFERENCES `backbone` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `strain_id` FOREIGN KEY (`strain`) REFERENCES `strain` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id` FOREIGN KEY (`creator`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `entry_inserts`
--
ALTER TABLE `entry_inserts`
  ADD CONSTRAINT `entry` FOREIGN KEY (`entry_id`) REFERENCES `entry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ins` FOREIGN KEY (`insert_id`) REFERENCES `ins` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `entry_upstrain`
--
ALTER TABLE `entry_upstrain`
  ADD CONSTRAINT `db_id` FOREIGN KEY (`entry_id`) REFERENCES `entry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ins`
--
ALTER TABLE `ins`
  ADD CONSTRAINT `creator_id` FOREIGN KEY (`creator`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ins_type` FOREIGN KEY (`type`) REFERENCES `ins_type` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `strain`
--
ALTER TABLE `strain`
  ADD CONSTRAINT `strain_creator` FOREIGN KEY (`creator`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `upstrain_file`
--
ALTER TABLE `upstrain_file`
  ADD CONSTRAINT `upstrain_uniqueid` FOREIGN KEY (`upstrain_id`) REFERENCES `entry_upstrain` (`upstrain_id`);

DELIMITER $$
--
-- Events
--
CREATE EVENT `AutoDeleteEntryLog` ON SCHEDULE EVERY 30 DAY STARTS '2018-02-26 12:57:57' ON COMPLETION PRESERVE ENABLE DO DELETE LOW_PRIORITY FROM upstrain.entry_log WHERE FROM_UNIXTIME(time) < DATE_SUB(NOW(), INTERVAL 30 DAY)$$

CREATE EVENT `AutoDeleteNonActiveUser` ON SCHEDULE EVERY 24 HOUR STARTS '2018-03-01 23:59:59' ON COMPLETION PRESERVE ENABLE DO DELETE LOW_PRIORITY FROM upstrain.users WHERE FROM_UNIXTIME(time) < DATE_SUB(NOW(), INTERVAL 72 HOUR) AND active = '0'$$

CREATE EVENT `AutoDeleteInstypeLog` ON SCHEDULE EVERY 30 DAY STARTS '2018-03-02 10:00:28' ON COMPLETION PRESERVE ENABLE DO DELETE LOW_PRIORITY FROM upstrain.ins_type_log WHERE FROM_UNIXTIME(time) < DATE_SUB(NOW(), INTERVAL 30 DAY)$$

CREATE EVENT `AutoDeleteUserLog` ON SCHEDULE EVERY 30 DAY STARTS '2018-02-26 12:57:31' ON COMPLETION PRESERVE ENABLE DO DELETE LOW_PRIORITY FROM upstrain.users_log WHERE FROM_UNIXTIME(time) < DATE_SUB(NOW(), INTERVAL 30 DAY)$$

CREATE EVENT `AutoEmptyLog` ON SCHEDULE EVERY 30 DAY STARTS '2018-02-26 13:04:33' ON COMPLETION PRESERVE ENABLE DO DELETE LOW_PRIORITY FROM upstrain.event_log WHERE FROM_UNIXTIME(time) < DATE_SUB(NOW(), INTERVAL 30 DAY)$$

CREATE EVENT `AutoDeleteInsertLog` ON SCHEDULE EVERY 30 DAY STARTS '2018-02-28 11:23:13' ON COMPLETION PRESERVE ENABLE DO DELETE LOW_PRIORITY FROM upstrain.ins_log WHERE FROM_UNIXTIME(time) < DATE_SUB(NOW(), INTERVAL 30 DAY)$$

CREATE EVENT `AutoDeleteEntryInsertLog` ON SCHEDULE EVERY 30 DAY STARTS '2018-02-28 14:35:41' ON COMPLETION PRESERVE ENABLE DO DELETE LOW_PRIORITY FROM upstrain.entry_inserts_log WHERE FROM_UNIXTIME(time) < DATE_SUB(NOW(), INTERVAL 30 DAY)$$

CREATE EVENT `AutoDeleteStrainLog` ON SCHEDULE EVERY 30 DAY STARTS '2018-02-28 14:28:15' ON COMPLETION PRESERVE ENABLE DO DELETE LOW_PRIORITY FROM upstrain.strain_log WHERE FROM_UNIXTIME(time) < DATE_SUB(NOW(), INTERVAL 30 DAY)$$

CREATE EVENT `AutoDeleteBackboneLog` ON SCHEDULE EVERY 30 DAY STARTS '2018-02-28 14:27:32' ON COMPLETION PRESERVE ENABLE DO DELETE LOW_PRIORITY FROM upstrain.backbone_log WHERE FROM_UNIXTIME(time) < DATE_SUB(NOW(), INTERVAL 30 DAY)$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
