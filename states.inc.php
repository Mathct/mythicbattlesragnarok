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
 * states.inc.php
 *
 * mythicbattlesragnarok game states description
 *
 */


if ( !defined('STATE_PLAYER_TURN') )
{
    define("STATE_PENDING",2);
    define("STATE_PLAYER_TURN",3);
    define("STATE_END_GAME",99);
}
 
 
$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 2 )
    ),
    
    STATE_PENDING=> array(
        "name" => "pending",
        "description" => '',
        "type" => "game",
        "action" => "stPending",
        "updateGameProgression" => true,
        "transitions" => array("end" => STATE_END_GAME, "player"=>STATE_PLAYER_TURN, "same" => STATE_PENDING)
    ),
    
    STATE_PLAYER_TURN => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must take an action or Pass'),
        "descriptionmyturn" => clienttranslate('${you} must take an action or pass'),
        "type" => "activeplayer",
        "args" => "argPlayerTurn",
        "possibleactions" => array( "select"),
        "transitions" => array( "next" => STATE_PENDING, "zombiePass" => STATE_PENDING)
    ),  
   
    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);



