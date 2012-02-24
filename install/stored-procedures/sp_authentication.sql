DELIMITER //

DROP PROCEDURE IF EXISTS Auth_CheckUser//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_CheckUser(IN `email` VARCHAR(128), IN `pword` LONGTEXT, IN `challenge` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Performs authentication check'
BEGIN
 SELECT COUNT(*) AS x FROM `authentication` WHERE AES_DECRYPT(BINARY(UNHEX(email)), SHA1(challenge))=email AND AES_DECRYPT(BINARY(UNHEX(password)), SHA1(challenge))=pword;
END//

DROP PROCEDURE IF EXISTS Auth_GetLevelGroup//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Auth_GetLevelGroup(IN `email` VARCHAR(128), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieves the users access level and group membership'
BEGIN
 SELECT `level`,`group` FROM `authentication` WHERE AES_DECRYPT(BINARY(UNHEX(email)), SHA1(sKey))=email;
END//

DELIMITER ;