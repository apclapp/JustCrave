CREATE DATABASE  IF NOT EXISTS `justcrave` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `justcrave`;
-- MySQL dump 10.13  Distrib 5.6.17, for osx10.6 (i386)
--
-- Host: localhost    Database: justcrave
-- ------------------------------------------------------
-- Server version	5.5.38

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `categoryId` int(11) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(255) DEFAULT NULL,
  `restaurantId` int(11) DEFAULT NULL,
  `menuId` int(11) DEFAULT NULL,
  PRIMARY KEY (`categoryId`),
  FULLTEXT KEY `categoryName_idx` (`categoryName`)
) ENGINE=MyISAM AUTO_INCREMENT=11782 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `itemId` int(11) NOT NULL AUTO_INCREMENT,
  `menuId` int(11) DEFAULT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `restaurantId` int(11) DEFAULT NULL,
  `itemName` varchar(255) DEFAULT NULL,
  `itemSynonym` varchar(255) DEFAULT NULL,
  `itemDescription` text,
  `itemPrice` float DEFAULT NULL,
  PRIMARY KEY (`itemId`),
  FULLTEXT KEY `itemName_idx` (`itemName`),
  FULLTEXT KEY `itemSynonym_idx` (`itemSynonym`)
) ENGINE=MyISAM AUTO_INCREMENT=19695903 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patches`
--

DROP TABLE IF EXISTS `patches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patches` (
  `patchname` varchar(255) NOT NULL,
  UNIQUE KEY `patchname_UNIQUE` (`patchname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `restaurants`
--

DROP TABLE IF EXISTS `restaurants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restaurants` (
  `restaurantId` int(11) NOT NULL,
  `restaurantName` varchar(255) DEFAULT NULL,
  `lastUpdated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(255) NOT NULL DEFAULT '',
  `postcode` varchar(25) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT 'http://www.just-eat.co.uk/',
  `is_halal` tinyint(4) NOT NULL DEFAULT '0',
  `rating_stars` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`restaurantId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suggestions`
--

DROP TABLE IF EXISTS `suggestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suggestions` (
  `commonName` varchar(255) DEFAULT NULL,
  FULLTEXT KEY `commonNameIdx` (`commonName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `version`
--

DROP TABLE IF EXISTS `version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `version` (
  `dbversion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'justcrave'
--
/*!50003 DROP PROCEDURE IF EXISTS `erase_restaurant_data` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `erase_restaurant_data`(IN restaurantIdIn INT(11))
BEGIN

DELETE FROM items
WHERE restaurantId = restaurantIdIn;

DELETE FROM categories 
WHERE
    restaurantId = restaurantIdIn;

DELETE FROM restaurants 
WHERE
    restaurantId = restaurantIdIn;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `refresh_suggestions` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `refresh_suggestions`()
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

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `search_food_items` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
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
    searchScore > max_searchscore * new_search_threshold; 






DROP TEMPORARY TABLE IF EXISTS tmp_search_food_items;
DROP TEMPORARY TABLE IF EXISTS tmp_result_stddev_stats;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `search_suggestions` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_suggestions`(IN search_query VARCHAR(255))
BEGIN

SELECT * FROM suggestions WHERE
    MATCH (commonName) AGAINST (search_query IN NATURAL LANGUAGE MODE)
OR
	commonName LIKE CONCAT("%", search_query, "%")
ORDER BY 
	MATCH (commonName) AGAINST (search_query IN NATURAL LANGUAGE MODE) DESC;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-03-29 20:37:40
