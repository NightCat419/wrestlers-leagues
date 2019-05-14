-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.24 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table wp_wrestler.wp_kwl_bonuses
DROP TABLE IF EXISTS `wp_kwl_bonuses`;
CREATE TABLE IF NOT EXISTS `wp_kwl_bonuses` (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `wrestler_id` int(12) NOT NULL,
  `bonus_points` int(12) NOT NULL,
  `provider_name` varchar(255) NOT NULL,
  `dateline` int(12) DEFAULT NULL,
  `desc` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table wp_wrestler.wp_kwl_league_user
DROP TABLE IF EXISTS `wp_kwl_league_user`;
CREATE TABLE IF NOT EXISTS `wp_kwl_league_user` (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `league_id` int(12) NOT NULL,
  `user_id` int(12) NOT NULL,
  `team_name` varchar(255) NOT NULL,
  `date_join` int(12) NOT NULL,
  `is_commissioner` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:wait, 1:joined, -1:rejected',
  `note` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `league` (`league_id`,`status`),
  KEY `user` (`user_id`),
  KEY `is_commissioner` (`is_commissioner`,`user_id`),
  KEY `date_join` (`date_join`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table wp_wrestler.wp_kwl_league_wrestler
DROP TABLE IF EXISTS `wp_kwl_league_wrestler`;
CREATE TABLE IF NOT EXISTS `wp_kwl_league_wrestler` (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `league_id` int(12) NOT NULL,
  `wrestler_id` int(12) NOT NULL,
  `user_id` int(12) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `league` (`league_id`),
  KEY `wrestler` (`wrestler_id`),
  KEY `user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table wp_wrestler.wp_kwl_matches
DROP TABLE IF EXISTS `wp_kwl_matches`;
CREATE TABLE IF NOT EXISTS `wp_kwl_matches` (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `winner_id` int(12) NOT NULL,
  `loser_id` int(12) NOT NULL,
  `dateline` int(12) NOT NULL,
  `winner_points` int(12) NOT NULL,
  `loser_points` int(12) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `winner_id` (`winner_id`),
  KEY `loser_id` (`loser_id`),
  KEY `date` (`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table wp_wrestler.wp_kwl_seasons
DROP TABLE IF EXISTS `wp_kwl_seasons`;
CREATE TABLE IF NOT EXISTS `wp_kwl_seasons` (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `season_name` varchar(255) NOT NULL,
  `season_start` int(12) NOT NULL,
  `season_end` int(12) NOT NULL,
  `season_period_text` varchar(255) NOT NULL,
  `desc` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
