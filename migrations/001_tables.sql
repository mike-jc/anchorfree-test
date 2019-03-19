USE `anchorfree-test`;

CREATE TABLE `events` (
  `date` DATE NOT NULL,
  `country` CHAR(2) NOT NULL,
  `type` ENUM('view', 'play', 'click') NOT NULL,
  `counter` INT DEFAULT 0 NOT NULL,
  PRIMARY KEY (`date`, `country`, `type`)
) ENGINE=InnoDB;

CREATE TABLE `countries` (
  `country` CHAR(2) NOT NULL,
  `counter` INT DEFAULT 0,
  PRIMARY KEY (`country`)
) ENGINE=InnoDB;

DELIMITER ;;

CREATE TRIGGER after_event_insert
    AFTER INSERT ON `events`
    FOR EACH ROW
BEGIN
    INSERT INTO `countries` (country, counter)
    VALUES (NEW.country, 1)
    ON DUPLICATE KEY UPDATE counter = counter + 1;
END;;

CREATE TRIGGER after_event_update
    AFTER UPDATE ON `events`
    FOR EACH ROW
BEGIN
    INSERT INTO `countries` (country, counter)
    VALUES (NEW.country, 1)
    ON DUPLICATE KEY UPDATE counter = counter + 1;
END;;

DELIMITER ;