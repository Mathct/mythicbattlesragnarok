<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * mythicbattlesragnarok implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * gameoptions.inc.php
 *
 * mythicbattlesragnarok game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in mythicbattlesragnarok.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

$game_options = array(
    100 => array(
                'name' => totranslate('Board'),    
                'values' => array(
                    1 => array( 'name' => totranslate('Naglfar') ),
                    2 => array( 'name' => totranslate('Vigrid') ),
                    3 => array( 'name' => totranslate('Raid on Hedeby') ),
                    4 => array( 'name' => totranslate('Mimir\'s Well') ),
                            100 => array( 'name' => totranslate('Random') ),
                ),
                'default' => 1
            ),
    101 => array(
                'name' => totranslate('Draft pool'),    
                'values' => array(
                    1 => array( 'name' => totranslate('All units') ),
                    2 => array( 'name' => totranslate('Limited draft') )
                ),
                'default' => 1
            ),

);


