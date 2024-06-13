
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- mythicbattlesragnarok implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

CREATE TABLE IF NOT EXISTS `pending` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `player_id` int(10) NULL,
  `unit_id` int(2) NOT NULL,  
  -- function dans MBR.game.php, unit.php, unitXX.php, voir unit->talent.php
  `function` varchar(50) NULL,
  `arg` varchar(300) NULL,  
  `arg2` varchar(50) NULL,
  `arg3` varchar(50) NULL,
  `arg4` varchar(50) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;
  
 CREATE TABLE IF NOT EXISTS `unit` (
   `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
   `player_id` int(10) NULL,
   -- type d'unité
   `type` INT UNSIGNED NOT NULL DEFAULT '0',
   -- -1,-2 : bord de table, 0 : quand mort et retourné dans la boite, -3 : draft zone_id sinon
   `zone_id` INT NOT NULL DEFAULT '0',
   -- vie restante
   `hp` INT NOT NULL DEFAULT '0',
   -- status qui disparait en fin d'activation
   `statusActivation` varchar(200) NOT NULL DEFAULT '',
   -- status qui disparait au début de sa prochaine activation
   `statusOwnActivation` varchar(200) NOT NULL DEFAULT '',
   -- status qui disparait à la fin du tour
   `statusTurn` varchar(200) NOT NULL DEFAULT '',
   -- status qui disparait au début du round suivant
   `statusRound` varchar(200) NOT NULL DEFAULT '',
   -- status qui reste
   `statusGame` varchar(200) NOT NULL DEFAULT '',
   -- jarl type
   `attachment` INT NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

 CREATE TABLE IF NOT EXISTS `deck1` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  -- Type d'unité
  `card_type` varchar(16) NOT NULL,
  -- Id d'unité
  `card_type_arg`  int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `deck2` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;

CREATE TABLE IF NOT EXISTS `die` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `face` int(2) NOT NULL DEFAULT '0',
  -- en tenant compte du +5
  `value` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  -- rune
  `type` varchar(16) NOT NULL,
  -- zonexx, unitxx, dashboardxx
  `location` varchar(16) NOT NULL,
  -- destroyed
  `remove` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `attachmentdraft` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(10) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE `player` ADD `endofturnstatus` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `player` ADD `startofturnstatus` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `player` ADD `rp` int(2) unsigned DEFAULT 18;