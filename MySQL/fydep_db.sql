-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 13, 2014 at 08:52 PM
-- Server version: 5.5.37
-- PHP Version: 5.3.10-1ubuntu3.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `fydepdb`
--

DROP DATABASE IF EXISTS `fydepdb`;
CREATE DATABASE `fydepdb` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci;
USE `fydepdb`;

CREATE USER 'fydep_u'@'localhost' IDENTIFIED BY '4syouwI5h';
GRANT ALL PRIVILEGES ON fydepdb.* TO 'fydep_u'@'localhost';

-- --------------------------------------------------------

--
-- Table structure for table `feeds`
--

DROP TABLE IF EXISTS `feeds`;
CREATE TABLE IF NOT EXISTS `feeds` (
  `id` varchar(36) COLLATE utf8_bin NOT NULL,
  `id_folder` varchar(36) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `upd_time` smallint(6) NOT NULL DEFAULT '10',
  `link` varchar(500) COLLATE utf8_spanish_ci DEFAULT NULL,
  `rss_link` varchar(500) COLLATE utf8_spanish_ci NOT NULL,
  `last_date_post` timestamp NOT NULL DEFAULT "2000-01-01 00:00:00",
  `enabled` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `feeds_ibfk_1` (`id_folder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS `folders`;
CREATE TABLE IF NOT EXISTS `folders` (
  `id` varchar(36) COLLATE utf8_bin NOT NULL,
  `name` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `user` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `hidden` tinyint(1) DEFAULT '0' NOT NULL,
  PRIMARY KEY (`id`),
  KEY `folders_ibfk_1` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

DROP TABLE IF EXISTS `post_tags`;
CREATE TABLE IF NOT EXISTS `post_tags` (
  `id_post` varchar(36) COLLATE utf8_bin NOT NULL,
  `id_tag` varchar(36) COLLATE utf8_bin NOT NULL,
  KEY `post_tags_ibfk_2` (`id_tag`),
  KEY `post_tags_ibfk_1` (`id_post`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` varchar(36) COLLATE utf8_bin NOT NULL,
  `id_feed` varchar(36) COLLATE utf8_bin NOT NULL,
  `title` text COLLATE utf8_spanish_ci NOT NULL,
  `description` text COLLATE utf8_spanish_ci NOT NULL,
  `link` varchar(500) COLLATE utf8_spanish_ci DEFAULT NULL,
  `unread` tinyint(1) NOT NULL,
  `favorite` tinyint(1) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idx` INT UNSIGNED UNIQUE NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `posts_ibfk_1` (`id_feed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` varchar(36) COLLATE utf8_bin NOT NULL,
  `user` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `tag_name` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `hidden` tinyint(1) DEFAULT '0' NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tags_ibfk_1` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `hidden_pass` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feeds`
--
ALTER TABLE `feeds`
  ADD CONSTRAINT `feeds_ibfk_1` FOREIGN KEY (`id_folder`) REFERENCES `folders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD CONSTRAINT `post_tags_ibfk_2` FOREIGN KEY (`id_tag`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_tags_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`id_feed`) REFERENCES `feeds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `tags_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

DELIMITER //
DROP FUNCTION IF EXISTS newID;
CREATE FUNCTION newID(num INT, tabname CHAR(40)) RETURNS VARCHAR(100)
BEGIN
  DECLARE `password` VARCHAR(100);
  DECLARE characters VARCHAR(100);
  DECLARE clength INT;
  DECLARE count INT;
  DECLARE val INT;
  SET characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-";
  SET clength = CHAR_LENGTH(characters);
  
  SET val = 1;
  
  WHILE (val!=0) DO
    SET `password` = "";
    SET count = 0;
    WHILE count < num DO
      SET `password` = CONCAT(`password`,SUBSTRING(characters,ROUND(RAND()*clength),1));
      SET count = count + 1;
    END WHILE;

    IF tabname="posts" THEN
      SET val = (SELECT count(*) FROM posts WHERE id=`password` );
    ELSEIF tabname="feeds" THEN
      SET val = (SELECT count(*) FROM feeds WHERE id=`password` );
    ELSEIF tabname="folders" THEN
      SET val = (SELECT count(*) FROM folders WHERE id=`password` );
    ELSEIF tabname="tags" THEN
      SET val = (SELECT count(*) FROM tags WHERE id=`password` );
    ELSE
      SET val=0;
    END IF;
    
  END WHILE;
  
  RETURN `password`;
END
//