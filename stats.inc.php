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
 * stats.inc.php
 *
 * mythicbattlesragnarok game statistics description
 *
 */

$stats_type = array(

    // Statistics global to table
    "table" => array(
    ),
    
    // Statistics existing for each player
    "player" => array(

        "turns_number" => array("id"=> 10,
                    "name" => totranslate("Number of turns"),
                    "type" => "int" ),
        "divine_stone" => array("id"=> 11,
                    "name" => totranslate("Divine strones absorbed"),
                    "type" => "int" ),
        "divinity_killed" => array("id"=> 12,
                    "name" => totranslate("Divinity killed"),
                    "type" => "int" ),
        "unit_killed" => array("id"=> 13,
                    "name" => totranslate("Units killed"),
                    "type" => "int" ),

    )

);
