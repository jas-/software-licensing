DELIMITER //

DROP PROCEDURE IF EXISTS Configuration_access_add//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_access_add(IN `allow` VARCHAR(30), IN `deny` VARCHAR(30), `skey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates access control lists'
BEGIN
 INSERT INTO `configuration_access` (`allow`, `deny`) VALUES (HEX(AES_ENCRYPT(allow, SHA1(skey))), HEX(AES_ENCRYPT(deny, SHA1(skey)))) ON DUPLICATE KEY UPDATE `allow`=HEX(AES_ENCRYPT(allow, SHA1(skey))), `deny`=HEX(AES_ENCRYPT(deny, SHA1(skey)));
END//

DROP PROCEDURE IF EXISTS Configuration_access_del//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_access_del(IN `id` INT(255))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Deletes access control list entry'
BEGIN
 DELETE FROM `configuration_access` WHERE `id`=id LIMIT 1;
END//

DROP PROCEDURE IF EXISTS Configuration_access_get//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_access_get(IN `skey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns list of access controls'
BEGIN
 SELECT AES_DECRYPT(BINARY(UNHEX(allow)), SHA1(sKey)) AS allow, AES_DECRYPT(BINARY(UNHEX(deny)), SHA1(sKey)) AS deny FROM `configuration_access`;
END//

DELIMITER ;
