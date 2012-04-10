DELIMITER //

DROP PROCEDURE IF EXISTS Resources_add//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Resources_add(IN `resource` VARCHAR(128), IN `cName` VARCHAR(128), `grp` VARCHAR(128), `usr` VARCHAR(128), `gw` INT(1), `gr` INT(1), `uw` INT(1), `gw` INT(1), `skey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates permissions for specified object'
BEGIN

END//

DELIMITER ;

