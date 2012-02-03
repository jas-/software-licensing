DELIMITER //

DROP PROCEDURE IF EXISTS Sessions_find//
CREATE DEFINER='jas'@'localhost' PROCEDURE Sessions_find(IN `session_id` VARCHAR(64))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Find existing session id'
BEGIN
 SELECT `session_id` FROM `sessions` WHERE `session_id` = id;
END//

DROP PROCEDURE IF EXISTS Sessions_add//
CREATE DEFINER='jas'@'localhost' PROCEDURE Sessions_add(IN `session_id` VARCHAR(64), IN `session_data` LONGTEXT, IN `session_expire` INT(10), IN `session_agent` VARCHAR(64), IN `session_ip` VARCHAR(64), IN `session_referrer` VARCHAR(64), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Adds new session or updates existing'
BEGIN
 SET x=0;
 INSERT INTO `sessions` (`session_id`, `session_data`, `session_expire`, `session_agent`, `session_ip`, `session_referrer`) VALUES (session_id, session_data, session_expire, session_agent, session_ip, session_referrer) ON DUPLICATE KEY UPDATE `session_id`=session_id, `session_data`=session_data, `session_expire`=session_expire, `session_agent`=session_agent, `session_ip`=session_ip, `session_referrer`=session_referrer;
 SET x=ROW_COUNT();
 SELECT x;
END//

DROP PROCEDURE IF EXISTS Sessions_delete//
CREATE DEFINER='jas'@'localhost' PROCEDURE Sessions_delete(IN `session_id` VARCHAR(64), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add new user account'
BEGIN
 SET x=0;
 DELETE FROM `sessions` WHERE `session_id`=session_id LIMIT 1;
 SET x=ROW_COUNT();
 SELECT x;
END//

DELIMITER ;
