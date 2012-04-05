DELIMITER //

DROP PROCEDURE IF EXISTS Configuration_access_add_allow//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_access_add_allow(IN `allow` LONGTEXT, `skey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates allowed access control lists'
BEGIN
 IF EXISTS (SELECT `allow` FROM `configuration_access` WHERE `allow`=HEX(AES_ENCRYPT(allow, SHA1(skey))))
 THEN
  UPDATE `configuration_access` SET `allow`=HEX(AES_ENCRYPT(allow, SHA1(skey))) WHERE `id`=id LIMIT 1;
 ELSE
  INSERT INTO `configuration_access` (`allow`) VALUES (HEX(AES_ENCRYPT(allow, SHA1(skey)))) ON DUPLICATE KEY UPDATE `allow`=HEX(AES_ENCRYPT(allow, SHA1(skey)));
 END IF;
END//

DROP PROCEDURE IF EXISTS Configuration_access_add_deny//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_access_add_deny(IN `deny` LONGTEXT, `skey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates denied access control lists'
BEGIN
 IF EXISTS (SELECT `deny` FROM `configuration_access` WHERE `deny`=HEX(AES_ENCRYPT(deny, SHA1(skey))))
 THEN
  UPDATE `configuration_access` SET `deny`=HEX(AES_ENCRYPT(deny, SHA1(skey))) WHERE `id`=id LIMIT 1;
 ELSE
  INSERT INTO `configuration_access` (`deny`) VALUES (HEX(AES_ENCRYPT(deny, SHA1(skey)))) ON DUPLICATE KEY UPDATE `deny`=HEX(AES_ENCRYPT(deny, SHA1(skey)));
 END IF;
END//

DROP PROCEDURE IF EXISTS Configuration_access_del//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_access_del(IN `id` INT(255))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Deletes access control list entry'
BEGIN
 DELETE FROM `configuration_access` WHERE `id`=id LIMIT 1;
END//

DELIMITER //
DROP PROCEDURE IF EXISTS Configuration_access_get//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_access_get(IN `type` VARCHAR(30), IN `skey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns list of access controls'
BEGIN
 SELECT AES_DECRYPT(BINARY(UNHEX(type)), SHA1(sKey)) AS type FROM `configuration_access` WHERE type != "";
END//

DELIMITER ;
