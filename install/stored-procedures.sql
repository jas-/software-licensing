DELIMITER //

-- Test authentication
DROP PROCEDURE IF EXISTS Auth_Authenticate//
CREATE DEFINER='licensing2012'@'localhost' PROCEDURE Auth_Authenticate(IN `email` VARCHAR(128), IN `password` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Authentication check'
BEGIN
 SELECT * FROM `authentication` WHERE `email`=email AND `password`=password;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add/Update user
DROP PROCEDURE IF EXISTS Auth_UserAddUpdate//
CREATE DEFINER='licensing2012'@'localhost' PROCEDURE Auth_UserAddUpdate(IN `resource` VARCHAR(255), IN `email` VARCHAR(128), IN `password` VARCHAR(255), IN `level` VARCHAR(40), IN `ggroup` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update user authentication'
BEGIN
 INSERT INTO `authentication` (`resource`, `emailAddress`, `password`, `level`, `group`) VALUES (resource, emailAddress, password, level, ggroup) ON DUPLICATE KEY UPDATE `resource`=resource, `emailAddress`=email, `password`=password, `level`=level, `group`=ggroup;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete user
DROP PROCEDURE IF EXISTS Auth_UserDelete//
CREATE DEFINER='licensing2012'@'localhost' PROCEDURE Auth_UserDelete(IN `email` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete user authentication'
BEGIN
 DELETE FROM `authentication` WHERE `email`=@email LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Add/Update group
DROP PROCEDURE IF EXISTS Auth_GroupAddUpdate//
CREATE DEFINER='licensing2012'@'localhost' PROCEDURE Auth_GroupAddUpdate(IN `resource` VARCHAR(255), IN `group` VARCHAR(128), IN `manager` VARCHAR(128), IN `description` VARCHAR(255), IN `owner` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update group'
BEGIN
 INSERT INTO `authentication_groups` (`resource`, `group`, `manager`, `description`, `owner`) VALUES (@resource, @group, @manager, @description, @owner) ON DUPLICATE KEY UPDATE `resource`=@resource, `group`=@group, `manager`=@manager, `description`=@description, `owner`=@owner;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Delete group
DROP PROCEDURE IF EXISTS Auth_GroupDelete//
CREATE DEFINER='licensing2012'@'localhost' PROCEDURE Auth_GroupDelete(IN `group` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete group'
BEGIN
 DELETE FROM `authentication_groups` WHERE `group`=@group LIMIT 1;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Search licensing
DROP PROCEDURE IF EXISTS License_Search//
CREATE DEFINER='licensing2012'@'localhost' PROCEDURE License_Search(IN `name` VARCHAR(128), IN `expiration` VARCHAR(20), IN `amount` INT(4), IN `price` DECIMAL(20,2), IN `purchased` VARCHAR(20), IN `type` CHAR(5), IN `maintenance` INT(1), IN `notes` LONGTEXT, IN `serial` VARCHAR(255))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Search licenses'
BEGIN
 SELECT * FROM `license` WHERE `name` LIKE name OR `expiration` LIKE expiration OR `amount` LIKE amount OR `price` LIKE price OR `purchased` LIKE purchased OR `type` LIKE type OR `maintenance` LIKE maintenance OR `notes` LIKE notes OR `serial` LIKE serial;
END//

-- Add/Update licenses
DROP PROCEDURE IF EXISTS License_AddUpdate//
CREATE DEFINER='licensing2012'@'localhost' PROCEDURE License_AddUpdate(IN `name` VARCHAR(128), IN `expiration` VARCHAR(20), IN `amount` INT(4), IN `price` DECIMAL(20,2), IN `purchased` VARCHAR(20), IN `type` CHAR(5), IN `maintenance` INT(1), IN `notes` LONGTEXT, IN `serial` VARCHAR(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update licenses'
BEGIN
 SET x=0;
 SELECT COUNT(*) INTO x FROM `license` WHERE `name`=name;
 IF x>0 THEN
  UPDATE `license` SET `name`=name, `expiration`=expiration, `amount`=amount, `price`=price, `purchased`=purchased, `type`=type, `maintenance`=maintenance, `notes`=notes, `serial`=serial WHERE `name`=name LIMIT 1;
 ELSE
  SELECT * FROM `license` WHERE `name` LIKE name OR `expiration` LIKE expiration OR `amount` LIKE amount OR `price` LIKE price OR `purchased` LIKE purchased OR `type` LIKE type OR `maintenance` LIKE maintenance OR `notes` LIKE notes OR `serial` LIKE serial;
 END IF;
 SET x = ROW_COUNT();
 SELECT x;
END//

-- Delete license
DROP PROCEDURE IF EXISTS License_Delete//
CREATE DEFINER='licensing2012'@'localhost' PROCEDURE License_Delete(IN `name` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete license'
BEGIN
 DELETE FROM `license` WHERE `name`=name LIMIT 1;
END//

DELIMITER ;
