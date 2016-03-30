USE `justcrave`;
DROP procedure IF EXISTS `refresh_suggestions`;

DELIMITER $$
USE `justcrave`$$
CREATE PROCEDURE `refresh_suggestions` ()
BEGIN


	DROP TABLE IF EXISTS suggestions;

CREATE TABLE suggestions (
    commonName varchar(255) default NULL
)  ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE suggestions ADD FULLTEXT commonNameIdx(commonName);

INSERT INTO suggestions (commonName)
SELECT
	REPLACE(itemName, '.', '') as commonName
FROM
    items
GROUP BY
    REPLACE(itemName, '.', '')
HAVING 
    COUNT(distinct(restaurantId)) > 3;

END$$

DELIMITER ;