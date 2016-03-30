USE `justcrave`;
DROP procedure IF EXISTS `erase_restaurant_data`;
 
DELIMITER $$
USE `justcrave`$$
CREATE PROCEDURE `erase_restaurant_data` (IN restaurantIdIn INT(11))
BEGIN
 
DELETE FROM items
WHERE restaurantId = restaurantIdIn;
 
DELETE FROM categories
WHERE
    restaurantId = restaurantIdIn;
 
DELETE FROM restaurants
WHERE
    restaurantId = restaurantIdIn;
 
END$$
 
DELIMITER ;