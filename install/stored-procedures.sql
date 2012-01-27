DELIMITER //

-- Test authentication
DROP PROCEDURE IF EXISTS Auth_Authenticate//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_Authenticate(IN `email` VARCHAR(128), IN `password` VARCHAR(128), OUT `x` INT)
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
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_UserAddUpdate(IN `resource` VARCHAR(255), IN `email` VARCHAR(128), IN `password` VARCHAR(255), IN `level` VARCHAR(40), IN `ggroup` VARCHAR(128))
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
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_UserDelete(IN `email` VARCHAR(128), OUT `x` INT)
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
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_GroupAddUpdate(IN `resource` VARCHAR(255), IN `group` VARCHAR(128), IN `manager` VARCHAR(128), IN `description` VARCHAR(255), IN `owner` VARCHAR(128), OUT `x` INT)
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
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_GroupDelete(IN `group` VARCHAR(128), OUT `x` INT)
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
CREATE DEFINER='licensing'@'localhost' PROCEDURE License_Search(IN `name` VARCHAR(128), IN `expiration` VARCHAR(20), IN `amount` INT(4), IN `price` DECIMAL(20,2), IN `purchased` VARCHAR(20), IN `type` CHAR(5), IN `maintenance` INT(1), IN `notes` LONGTEXT, IN `serial` VARCHAR(255))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Search licenses'
BEGIN
 SELECT * FROM `license` WHERE `name` LIKE name OR `expiration` LIKE expiration OR `amount` LIKE amount OR `price` LIKE price OR `purchased` LIKE purchased OR `type` LIKE type OR `maintenance` LIKE maintenance OR `notes` LIKE notes OR `serial` LIKE serial;
END//

-- Add/Update licenses
DROP PROCEDURE IF EXISTS License_AddUpdate//
CREATE DEFINER='licensing'@'localhost' PROCEDURE License_AddUpdate(IN `name` VARCHAR(128), IN `expiration` VARCHAR(20), IN `amount` INT(4), IN `price` DECIMAL(20,2), IN `purchased` VARCHAR(20), IN `type` CHAR(5), IN `maintenance` INT(1), IN `notes` LONGTEXT, IN `serial` VARCHAR(255), OUT `x` INT)
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
CREATE DEFINER='licensing'@'localhost' PROCEDURE License_Delete(IN `name` VARCHAR(128), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete license'
BEGIN
 DELETE FROM `license` WHERE `name`=name LIMIT 1;
END//

-- Retrieve current session
DROP PROCEDURE IF EXISTS Session_Search//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Search(IN `session_id` VARCHAR(128), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieve current session'
BEGIN
 SELECT `session_id`,AES_DECRYPT(BINARY(UNHEX(session_data)), SHA1(sKey)) AS session_data,`session_expire`,`session_agent`,`session_ip`,`session_referer` FROM `sessions` WHERE `session_id`=session_id;
END//

DELIMITER //
DROP PROCEDURE IF EXISTS Session_Add//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Add(IN `session_id` VARCHAR(64), `session_data` LONGTEXT, `session_expire` INT(10), `session_agent` VARCHAR(64), `session_ip` VARCHAR(64), `session_referer` VARCHAR(64), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or update existing session id & data'
BEGIN
 INSERT INTO `sessions` (`session_id`,`session_data`,`session_expire`,`session_agent`,`session_ip`,`session_referer`) VALUES (session_id, HEX(AES_ENCRYPT(session_data, SHA1(sKey))), session_expire, session_agent, session_ip, session_referer) ON DUPLICATE KEY UPDATE `session_id`=session_id, `session_data`=HEX(AES_ENCRYPT(session_data, SHA1(sKey))), `session_expire`=session_expire;
END//

DROP PROCEDURE IF EXISTS Session_Destroy//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Destroy(IN `session_id` VARCHAR(64))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete users sessions id'
BEGIN
 DELETE FROM `sessions` WHERE `session_id`=session_id LIMIT 1;
END//

DELIMITER //
DROP PROCEDURE IF EXISTS Session_Timeout//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Timeout(IN `session_expire` INT(10))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Expire session based on timeout option'
BEGIN
 DELETE FROM `sessions` WHERE `session_expire`>session_expire LIMIT 1;
END//

DROP PROCEDURE IF EXISTS Logs_Add//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Logs_Add(IN `guid` VARCHAR(64), `adate` VARCHAR(64), `ip` VARCHAR(10), `hostname` VARCHAR(80), `agent` VARCHAR(128), `query` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or update logs'
BEGIN
 INSERT INTO `logs` (`guid`,`adate`,`ip`,`hostname`,`agent`,`query`) VALUES (guid, adate, ip, hostname, agent, query);
END//

DELIMITER ;
