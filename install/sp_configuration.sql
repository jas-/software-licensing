DELIMITER //

DROP PROCEDURE IF EXISTS Configuration_def_add//
CREATE DEFINER='jas'@'localhost' PROCEDURE Configuration_def_add(IN `title` VARCHAR(128), IN `private` INT(1), IN `email` VARCHAR(64), IN `timeout` INT(10), IN `pkey` LONGTEXT, IN `pvkey` LONGTEXT, IN `pass` LONGTEXT, IN `skey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates configuration'
BEGIN
 DECLARE x INT DEFAULT 0;
 SELECT COUNT(*) INTO x FROM `configuration`;
 IF x>0 THEN
  INSERT INTO `configuration` (`title`, `private`, `email`, `timeout`, `pkey`, `pvkey`, `pass`) VALUES (title, private, email, timeout, HEX(AES_ENCRYPT(pkey, SHA1(skey))), HEX(AES_ENCRYPT(pvkey, SHA1(skey))), HEX(AES_ENCRYPT(pass, SHA1(skey)))) ON DUPLICATE KEY UPDATE `title`=title, `private`=private, `email`=email, `timeout`=timeout, `pkey`=HEX(AES_ENCRYPT(pkey, SHA1(skey))), `pvkey`=HEX(AES_ENCRYPT(pvkey, SHA1(skey))), `pass`=HEX(AES_ENCRYPT(pass, SHA1(skey)));
 ELSE
  UPDATE `configuration` SET `title`=title, `private`=private, `email`=email, `timeout`=timeout, `pkey`=HEX(AES_ENCRYPT(pkey, SHA1(skey))), `pvkey`=HEX(AES_ENCRYPT(pvkey, SHA1(skey))), `pass`=HEX(AES_ENCRYPT(pass, SHA1(skey)));
 END IF;
 SELECT ROW_COUNT() INTO x;
END//

DROP PROCEDURE IF EXISTS Configuration_def_get//
CREATE DEFINER='jas'@'localhost' PROCEDURE Configuration_def_get(IN `skey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieves configuration'
BEGIN
 DECLARE public VARCHAR(255) DEFAULT '';
 DECLARE priv VARCHAR(255) DEFAULT '';
 DECLARE password VARCHAR(255) DEFAULT '';
 SELECT `title`, `private`, `email`, `timeout`, AES_DECRYPT(BINARY(UNHEX(pkey)), SHA1(skey)) AS public, AES_DECRYPT(BINARY(UNHEX(pvkey)), SHA1(skey)) AS priv, AES_DECRYPT(BINARY(UNHEX(pass)), SHA1(skey)) AS password FROM `configuration`;
END//

DELIMITER ;
