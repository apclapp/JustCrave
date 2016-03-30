ALTER TABLE `justcrave`.`restaurants` 
ADD COLUMN `address` VARCHAR(255) NOT NULL DEFAULT '' AFTER `lastUpdated`,
ADD COLUMN `postcode` VARCHAR(25) NOT NULL DEFAULT '' AFTER `address`,
ADD COLUMN `city` VARCHAR(255) NOT NULL DEFAULT '' AFTER `postcode`,
ADD COLUMN `url` VARCHAR(255) NOT NULL DEFAULT 'http://www.just-eat.co.uk/' AFTER `city`,
ADD COLUMN `is_halal` TINYINT NOT NULL DEFAULT 0 AFTER `url`,
ADD COLUMN `rating_stars` FLOAT NOT NULL DEFAULT 0.0 AFTER `is_halal`;
