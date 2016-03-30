USE `justcrave`;
DROP procedure IF EXISTS `search_suggestions`;

DELIMITER $$
USE `justcrave`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_suggestions`(IN search_query VARCHAR(255))
BEGIN

SELECT * FROM suggestions WHERE
    MATCH (commonName) AGAINST (search_query IN NATURAL LANGUAGE MODE)
OR
	commonName LIKE CONCAT("%", search_query, "%")
ORDER BY 
	MATCH (commonName) AGAINST (search_query IN NATURAL LANGUAGE MODE) DESC;

END$$

DELIMITER ;

