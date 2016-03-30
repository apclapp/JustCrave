ALTER TABLE `justcrave`.`suggestions` 
CHANGE COLUMN `commonName` `commonName` VARCHAR(255) NULL DEFAULT '.' ,
ADD COLUMN `suggestionId` INT NOT NULL AUTO_INCREMENT FIRST,
ADD COLUMN `suggestionType` INT NULL AFTER `commonName`,
ADD PRIMARY KEY (`suggestionId`);

ALTER TABLE `justcrave`.`suggestions` 
CHANGE COLUMN `suggestionType` `suggestionType` INT(11) NULL DEFAULT NULL COMMENT '0 = items, 1 = synonyms, 2 = categories (ids)' ;