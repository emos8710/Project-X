-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 07, 2018 at 09:09 PM
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
-- Table structure for table `backbone`
--

DROP TABLE IF EXISTS `backbone`;
CREATE TABLE IF NOT EXISTS `backbone` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `Bb_reg` varchar(50) DEFAULT NULL,
  `date_db` int(10) NOT NULL,
  `year_created` int(4) NOT NULL,
  `creator` int(3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bb_name` (`name`),
  UNIQUE KEY `bb_regname` (`Bb_reg`),
  KEY `bb_creat_id` (`creator`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `backbone`
--

INSERT INTO `backbone` (`id`, `name`, `Bb_reg`, `date_db`, `year_created`, `creator`) VALUES
(2, 'test', 'test', 20180207, 2018, 1);

-- --------------------------------------------------------

--
-- Table structure for table `entry`
--

DROP TABLE IF EXISTS `entry`;
CREATE TABLE IF NOT EXISTS `entry` (
  `id` int(3) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `comment` varchar(100) DEFAULT NULL,
  `year_created` int(4) NOT NULL,
  `date_db` int(10) NOT NULL,
  `entry_reg` varchar(50) DEFAULT NULL,
  `sequence` tinyint(1) NOT NULL DEFAULT '0',
  `backbone` int(3) NOT NULL,
  `strain` int(3) NOT NULL,
  `ins` int(3) DEFAULT NULL,
  `creator` int(3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_regname` (`entry_reg`),
  UNIQUE KEY `entry_reg` (`entry_reg`),
  KEY `backbone_id` (`backbone`),
  KEY `insert_id` (`ins`),
  KEY `strain_id` (`strain`),
  KEY `creator_id` (`creator`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `entry`
--

INSERT INTO `entry` (`id`, `comment`, `year_created`, `date_db`, `entry_reg`, `sequence`, `backbone`, `strain`, `ins`, `creator`) VALUES
(001, 'testestestest', 2018, 20180207, 'test', 1, 2, 1, 2, 1);

--
-- Triggers `entry`
--
DROP TRIGGER IF EXISTS `upstrain_id`;
DELIMITER $$
CREATE TRIGGER `upstrain_id` AFTER INSERT ON `entry` FOR EACH ROW INSERT INTO `entry_upstrain` (entry_id, upstrain_id) VALUES (NEW.id, CONCAT("UU",NEW.year_created,NEW.id))
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `entry_upstrain`
--

DROP TABLE IF EXISTS `entry_upstrain`;
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
-- Table structure for table `ins`
--

DROP TABLE IF EXISTS `ins`;
CREATE TABLE IF NOT EXISTS `ins` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `ins_reg` varchar(50) NOT NULL,
  `creator` int(3) NOT NULL,
  `year_created` int(4) NOT NULL,
  `date_db` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `insert_name` (`name`),
  UNIQUE KEY `ins_regname` (`ins_reg`),
  KEY `creator_id` (`creator`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ins`
--

INSERT INTO `ins` (`id`, `name`, `type`, `ins_reg`, `creator`, `year_created`, `date_db`) VALUES
(2, 'test', 'coding', 'test', 1, 2018, 20180207);

-- --------------------------------------------------------

--
-- Table structure for table `strain`
--

DROP TABLE IF EXISTS `strain`;
CREATE TABLE IF NOT EXISTS `strain` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `strain_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `strain`
--

INSERT INTO `strain` (`id`, `name`) VALUES
(1, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `upstrain_file`
--

DROP TABLE IF EXISTS `upstrain_file`;
CREATE TABLE IF NOT EXISTS `upstrain_file` (
  `upstrain_id` varchar(10) NOT NULL,
  `name_original` varchar(50) NOT NULL,
  `name_new` varchar(20) GENERATED ALWAYS AS (concat(`upstrain_id`,'.fasta')) VIRTUAL,
  PRIMARY KEY (`upstrain_id`),
  UNIQUE KEY `name_new` (`name_new`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(3) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(14) NOT NULL,
  `password` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `password` (`password`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `password`, `username`) VALUES
(1, 'test', 'testson', 'test.testson@testmail.com', '0123456789', 'testtest123', 'testymctestface');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `backbone`
--
ALTER TABLE `backbone`
  ADD CONSTRAINT `bb_creat_id` FOREIGN KEY (`creator`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `entry`
--
ALTER TABLE `entry`
  ADD CONSTRAINT `backbone_id` FOREIGN KEY (`backbone`) REFERENCES `backbone` (`id`),
  ADD CONSTRAINT `insert_id` FOREIGN KEY (`ins`) REFERENCES `ins` (`id`),
  ADD CONSTRAINT `strain_id` FOREIGN KEY (`strain`) REFERENCES `strain` (`id`),
  ADD CONSTRAINT `user_id` FOREIGN KEY (`creator`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `entry_upstrain`
--
ALTER TABLE `entry_upstrain`
  ADD CONSTRAINT `db_id` FOREIGN KEY (`entry_id`) REFERENCES `entry` (`id`);

--
-- Constraints for table `ins`
--
ALTER TABLE `ins`
  ADD CONSTRAINT `creator_id` FOREIGN KEY (`creator`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `upstrain_file`
--
ALTER TABLE `upstrain_file`
  ADD CONSTRAINT `upstrain_uniqueid` FOREIGN KEY (`upstrain_id`) REFERENCES `entry_upstrain` (`upstrain_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
