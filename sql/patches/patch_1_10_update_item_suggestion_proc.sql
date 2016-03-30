
USE `justcrave`;
DROP procedure IF EXISTS `justcrave`.`refresh_suggestions`;

DELIMITER $$
USE `justcrave`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `refresh_item_suggestions`()
BEGIN

/*
	DROP TABLE IF EXISTS suggestions;

CREATE TABLE suggestions (
    commonName varchar(255) default NULL
)  ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE suggestions ADD FULLTEXT commonNameIdx(commonName);

*/

# Remove all the item suggestions
DELETE FROM suggestions WHERE suggestionId > -1 AND suggestionType = 1;

INSERT INTO suggestions (commonName, suggestionType)
SELECT
	REPLACE(itemName, '.', '') as commonName,
	1 AS suggestionType
FROM
    items
GROUP BY
    REPLACE(itemName, '.', '')
HAVING 
    COUNT(distinct(restaurantId)) > 3;

END$$

DELIMITER ;
;
