-- Create the database & drop if it already exists
DROP DATABASE IF EXISTS `licensing2012`;
CREATE DATABASE `licensing2012`;

-- Create a default user and assign limited permissions
CREATE USER "licensing2012"@"localhost" IDENTIFIED BY "d3v3l0pm3n+";
GRANT SELECT, INSERT, UPDATE, DELETE, REFERENCES, INDEX, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE ON `licensing2012`.* TO "licensing2012"@"localhost";

-- Switch to newly created db context
USE `licensing2012`;

-- Set FK checks to 0 during table creation
SET foreign_key_checks = 0;

-- Creates a table for authentication groups
--  Primary key: id
--  Unique key: resource
--  Index: resource
DROP TABLE IF EXISTS `authentication_groups`;
CREATE TABLE IF NOT EXISTS `authentication_groups` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `group` varchar(128) NOT NULL,
  `manager` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `owner` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group` (`group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

-- Create a default 'administrative' group
INSERT INTO `authentication_groups` (`group`, `owner`) VALUES ("admin", "admin");

-- Creates a table for authentication access levels
--  Primary key: id
--  Unique key: level
DROP TABLE IF EXISTS `authentication_levels`;
CREATE TABLE IF NOT EXISTS `authentication_levels` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `level` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

-- Create a set of default access levels
INSERT INTO `authentication_levels` (`level`) VALUES ("admin");
INSERT INTO `authentication_levels` (`level`) VALUES ("user");
INSERT INTO `authentication_levels` (`level`) VALUES ("view");

-- Creates the authentication users table
--  Primary key: id
--  Unique key: resource, username
--  Index: group, level, username
--  Foreign key details:
--   authentication.group updates from authentication_groups.group
--   authentication.level updates from authentication_levels.level
DROP TABLE IF EXISTS `authentication`;
CREATE TABLE IF NOT EXISTS `authentication` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `password` mediumtext NOT NULL,
  `level` varchar(40) NOT NULL,
  `group` varchar(128) NOT NULL,
  `authentication_token` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  INDEX `group` (`group`),
  CONSTRAINT `fk_groups` FOREIGN KEY (`group`)
   REFERENCES `authentication_groups` (`group`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `level` (`level`),
  CONSTRAINT `fk_levels` FOREIGN KEY (`level`)
   REFERENCES `authentication_levels` (`level`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Create a table for default application settings
--  Primary key: id
--  Unique key: email
DROP TABLE IF EXISTS `configuration`;
CREATE TABLE IF NOT EXISTS `configuration` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `templates` varchar(255) NOT NULL,
  `cache` varchar(255) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `email` varchar(45) NOT NULL,
  `timeout` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

-- Create a table for the software licenses
--  Primary key: id
--  Indexed key: name
DROP TABLE IF EXISTS `license`;
CREATE TABLE IF NOT EXISTS `license` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `expiration` varchar(20) NOT NULL,
  `amount` int(4) NOT NULL,
  `price` decimal(20,2) NOT NULL,
  `purchased` varchar(20) NOT NULL,
  `type` char(5) NOT NULL,
  `maintenance` int(1) NOT NULL,
  `notes` longtext NOT NULL,
  `serial` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

-- Re-enable the foreign key checks
SET foreign_key_checks = 1;
