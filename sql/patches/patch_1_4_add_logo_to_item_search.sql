USE `justcrave`;
DROP procedure IF EXISTS `search_food_items`;

DELIMITER $$
USE `justcrave`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_food_items`(IN item_query VARCHAR(255), restaurant_ids VARCHAR(65000))
BEGIN

DECLARE search_average FLOAT;
DECLARE search_deviation FLOAT;
DECLARE max_searchscore FLOAT;
DECLARE words_in_query INT(4);
DECLARE new_search_threshold FLOAT;

DROP TEMPORARY TABLE IF EXISTS tmp_search_food_items;
  
CREATE TEMPORARY TABLE tmp_search_food_items
SELECT 
    r.restaurantName,
    c.categoryName,
    i . *,
    MATCH (i.itemName) AGAINST (item_query IN NATURAL LANGUAGE MODE) as IsearchScore,
	MATCH (i.itemSynonym) AGAINST (item_query IN NATURAL LANGUAGE MODE) as SSearchScore,
	MATCH (c.categoryName) AGAINST (item_query IN NATURAL LANGUAGE MODE) as CsearchScore,
    MATCH (i.itemName) AGAINST (item_query IN NATURAL LANGUAGE MODE) * 2 + MATCH (i.itemSynonym) AGAINST (item_query IN NATURAL LANGUAGE MODE) + MATCH (c.categoryName) AGAINST (item_query IN NATURAL LANGUAGE MODE) as searchScore
FROM
    `justcrave`.`items` i
        INNER JOIN
    `justcrave`.`categories` c ON i.categoryId = c.categoryId
        INNER JOIN
    `justcrave`.`restaurants` r ON i.restaurantId = r.restaurantId
WHERE
    (MATCH (i.itemName) AGAINST (item_query IN NATURAL LANGUAGE MODE)
        OR MATCH (i.itemSynonym) AGAINST (item_query IN NATURAL LANGUAGE MODE)
        OR MATCH (c.categoryName) AGAINST (item_query IN NATURAL LANGUAGE MODE))
	AND FIND_IN_SET(i.restaurantId, restaurant_ids) > 0
ORDER BY searchScore DESC;


SELECT (LENGTH(item_query) - LENGTH(replace(item_query, ' ', '')) + 1) INTO words_in_query;

SET new_search_threshold = (1-(1/words_in_query)) + 0.05;

 
SELECT 
    MAX(searchScore)
INTO max_searchscore FROM
    tmp_search_food_items;
SELECT 
    *, CONCAT('images/logo/' , restaurantId , '.gif') as itemLogo
FROM
    tmp_search_food_items
WHERE
    searchScore > max_searchscore * new_search_threshold; # Should get values greater than 95%

# Old method: Get values > 1 standard deviation from the mean
# SELECT AVG(searchScore), stddev_pop(searchScore) INTO search_average, search_deviation FROM tmp_search_food_items;
# SELECT * FROM tmp_search_food_items WHERE searchScore > search_average + (search_deviation * std_range); # Should get values greater than 95%


DROP TEMPORARY TABLE IF EXISTS tmp_search_food_items;
DROP TEMPORARY TABLE IF EXISTS tmp_result_stddev_stats;
END$$

DELIMITER ;

