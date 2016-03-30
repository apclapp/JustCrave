USE `justcrave`;

DELIMITER $$

DROP TRIGGER IF EXISTS justcrave.items_add_friendly_synonym$$
USE `justcrave`$$
CREATE DEFINER=`root`@`localhost` TRIGGER `items_add_friendly_synonym` BEFORE INSERT ON `items` 
FOR EACH ROW
    BEGIN
        IF NEW.itemSynonym RLIKE "^[0-9]+[\.]?[0-9]*L$" THEN # Liquid volume in litres
				# Remove spaces
				# Change 'L' to 'Litres'
				# Format as decimal with leading 0
             SET NEW.friendlySynonym = CONCAT(TRIM(TRUNCATE(REPLACE(REPLACE(NEW.itemSynonym, ' ', ''), 'L', ''), 2))+0, ' Litre');
         ELSEIF NEW.itemSynonym RLIKE "\"" THEN # Size in inches
				# Replace '-' with spaces
				# Replace '".*?$' with ' Inches'
				# Remove leading 0's
             SET NEW.friendlySynonym = TRIM(LEADING '0' FROM SUBSTRING_INDEX( REPLACE(REPLACE(NEW.itemSynonym, '-', ' '), '"', ' Inches###'), '###', 1 ));
		ELSE 
			# Replace '-' with spaces
			SET NEW.friendlySynonym = REPLACE(NEW.itemSynonym, '-', ' ');
		END IF;
 
	END$$
DELIMITER ;
