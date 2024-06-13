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
  * mythicbattlesragnarok.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );
require_once('modules/terrains/terrain.php');
require_once('modules/terrains/boundary.php');
include('modules/talent.php');
include('modules/unit.php');
include('modules/action.php');
include('modules/MBRplayer.php');

class mythicbattlesragnarok extends Table
{ 
    public static $instance = null;

    public $playersMBR = array();
    public $units = array();
    public $terrains = array();
        
	function __construct( )
	{
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        
        self::$instance = $this;
        
        self::initGameStateLabels( array( 
            "no_undo" => 10,
            "debug" => 11,
            "boardsetup" => 12,
            //    "my_second_global_variable" => 11,
            //      ...
                "board" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ) ); 
              
        
        $this->deck1 = self::getNew( "module.common.deck" );
        $this->deck1->init( "deck1" );
        
        $this->deck2 = self::getNew( "module.common.deck" );
        $this->deck2->init( "deck2" );    
        
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "mythicbattlesragnarok";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        ksort($players); //force le 1er jouer DEBUG
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, rp) VALUES ";
        $values = array();
        $player_id_first= "";
        $player_id_second= "";
        $rp = 18;
        foreach( $players as $player_id => $player )
        {
            if($player_id_first == "")
            {
                $player_id_first = $player_id;
            }
            else{
                $player_id_second = $player_id;
            }
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."', ".$rp.")";
        }
        $sql .= implode( ',', $values );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();
        
        /************ Start the game initialization *****/

        // Init global values with their initial values
        self::setGameStateInitialValue( 'boardsetup', bga_rand(1,2) );

        if(self::getGameStateValue( 'board') == 100)
        {
            self::setGameStateInitialValue( 'board', bga_rand(1,4) );
        }

        self::setGameStateInitialValue( 'no_undo', 1 );

        // Init game statistics
        self::initStat( 'player', 'turns_number', 0 );  // Init a player statistics (for all players)
        self::initStat( 'player', 'divine_stone', 0 );  // Init a player statistics (for all players)
        self::initStat( 'player', 'divinity_killed', 0 );  // Init a player statistics (for all players)
        self::initStat( 'player', 'unit_killed', 0 );  // Init a player statistics (for all players)

        $setup = $this->getBoard()['setup'][self::getGameStateValue( 'boardsetup')];
        $sql = "INSERT INTO token (type, location) VALUES ";
        $values = array();
        foreach( $setup["runes"] as $location_id )
        {
            $values[] = "( 'rune', 'zone".$location_id."')";
        }

        foreach($this->getBoard()['zones'] as $zone)
        {            
            if($zone["type"] == FOREST)
            {
                for($i=0;$i<ceil($zone['capacity']/2);$i++)
                {
                    $values[] = "( 'tree', 'zone".$zone['id']."')";
                }
            }
            if($zone["type"] == RUINS)
            {
                $values[] = "( 'stele', 'zone".$zone['id']."')";
            }
        }

        $sql .= implode( ',', $values );
        self::DbQuery( $sql );

        self::setGameStateInitialValue( 'debug', 0 );

        if(self::getGameStateValue( 'debug') == 1)
        {
            $unittested = 24;
            $ennemyUnit = 50;
            self::DbQuery("INSERT INTO unit (player_id, type, zone_id, hp) VALUES ($player_id_first,50,9,4)");
            self::DbQuery("INSERT INTO unit (player_id, type, zone_id, hp) VALUES ($player_id_first,11,15,2)");
            self::DbQuery("INSERT INTO unit (player_id, type, zone_id, hp) VALUES ($player_id_first,$unittested,4,4)");

            self::DbQuery("INSERT INTO unit (player_id, type, zone_id, hp) VALUES ($player_id_second,$ennemyUnit,4,1)");
            self::DbQuery("INSERT INTO unit (player_id, type, zone_id, hp) VALUES ($player_id_second,47,16,4)");
            
            self::DbQuery("INSERT INTO unit (player_id, type, zone_id, hp) VALUES ($player_id_second,13,16,4)");

            $this->loadAll();

            mythicbattlesragnarok::$instance->addPending($player_id_second,0, "T0_NewTurn");
            mythicbattlesragnarok::$instance->addPending($player_id_first,0, "T0_NewTurn");
            mythicbattlesragnarok::$instance->onTiming(STARTGAME);
            mythicbattlesragnarok::$instance->addPending($player_id_second,0, "SetupDeck");
            mythicbattlesragnarok::$instance->addPending($player_id_first,0, "SetupDeck");        
        }
        else
        { 
            $sql = "INSERT INTO unit (type, hp, zone_id, player_id) VALUES ";
            $values = array();
            for ($i=1; $i<=57; $i++)
                {
                    $unitClassName = "unit" . $i;
                    $unitinstance=new $unitClassName();
                    $hp=count($unitinstance->stats);
                    $hpunit[$i][]=$hp;
                    $values[] = "($i, $hp, -3, 0)";
                }
            $sql .= implode( ',', $values );
            self::DbQuery( $sql );

            $sql = "INSERT INTO attachmentdraft (id) VALUES ";
            $values = array();
            for ($i=1; $i<=8; $i++)
                {
                    $values[] = "($i)";
                }
            $sql .= implode( ',', $values );
            self::DbQuery( $sql );

            mythicbattlesragnarok::$instance->addPendingFirst($player_id_first,0, "Draft", "divinity");
            mythicbattlesragnarok::$instance->addPendingFirst($player_id_second,0, "Draft", "divinity"); 
            mythicbattlesragnarok::$instance->addPendingFirst($player_id_second,0, "Draft");
            mythicbattlesragnarok::$instance->addPendingFirst($player_id_first,0, "Draft"); 
        }

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    function loadAll()
    {
        $playerdbs = self::getObjectListFromDB( "SELECT * FROM player");
        if(count($this->units) == 0)
        {       
            $this->playersMBR = array();
            $this->units = array();
            $this->zones = array();
            
            foreach($playerdbs as $player_id => $playerdb)
            {
                $player = new MBRplayer();
                $player->id = $playerdb['player_id'];   
                $player->player_id = $playerdb['player_id'];      
                $player->player_no = $playerdb['player_no'];
                $player->player_name = $playerdb['player_name'];
                $player->player_score = $playerdb['player_score'];
                $player->rp = $playerdb['rp'];
                $player->status = array_filter( explode(' ',$playerdb['endofturnstatus']));                 
                foreach (array_filter(explode(' ', $playerdb['startofturnstatus'])) as $valeur) {
                    $player->status[] = $valeur;
                }
                $player->units = array();
                $this->playersMBR[$player->id] = $player;
            }

            $unitdbs = self::getObjectListFromDB( "SELECT * FROM unit" );
            foreach($unitdbs as $unit_id => $unitdb)
            {
                $unit = unit::Create($unitdb);
                if($unit->player_id != 0)
                {
                    $unit->player = $this->playersMBR[$unit->player_id];
                    $unit->player->units[$unit->id] = $unit;
                }
                $this->units[$unit->id] = $unit;
            }

            foreach($this->getBoard()['zones'] as $zone)
            {
                $terrain = terrain::Create($zone);
                $this->zones[$terrain->id] = $terrain;
            }

            foreach($this->units as $unit)
            {
                $unit->zone = $this->zones[$unit->zone_id];
            }
            foreach($this->units as $unit)
            {
                $unit->status = array_unique(array_merge($unit->status, $unit->getAuraStatus($unit)));  
                foreach($this->units as $unit2)
                {
                    if($unit != $unit2)
                    {
                        $unit->status = array_unique(array_merge($unit->status, $unit2->getAuraStatus($unit))); 
                    }                   
                }
            }
        }
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();
        $this->loadAll();
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        $result['players'] = self::getCollectionFromDb( "SELECT player_id id, player_score score, player_no, player_id, player_color, player_name, rp FROM player");  
        foreach($result['players'] as $player_id => $player)
        {
            $deck = $this->getDeck($player_id);
            $result['players'][$player_id]['deck'] = $deck->countCardInLocation('deck');
            $result['players'][$player_id]['hand'] = $deck->countCardInLocation('hand');
            $result['players'][$player_id]['discard'] = $deck->countCardInLocation('discard');
            $result['players'][$player_id]['discardDetails'] = self::getCollectionFromDb( "SELECT card_type, count(*) from deck".$player['player_no']." where card_location = 'discard' group by card_type", true);
            $result['players'][$player_id]['starting'] = $this->getStartingZones($player_id);
        }        

        $result['dices'] = self::getCollectionFromDb( "SELECT * FROM die"); 
        $result['tokens'] = self::getCollectionFromDb( "SELECT * FROM token");

        $unitdbs = self::getObjectListFromDB( "SELECT * FROM unit" );

        $result['units'] = array();
        foreach($unitdbs as $unit_id => $unitdb)
        {
            $unit = unit::Create($unitdb);
            foreach($unit->talents as $t => $talent)
            {
                $talent->unit = 0;
            }
            $unit->attachment->unit = 0;
            $unit->status = $this->units[$unitdb['id']]->status;
            $result['units'][$unit->id] = $unit;
        }

        $attdbs = self::getObjectListFromDB( "SELECT * FROM attachmentdraft" );
        $result['attachmentdrafts'] = array();
        foreach($attdbs as $att_id => $att)
        {
            $attname = "attachment".$att["id"];
            $result['attachmentdrafts'][$att_id] = new $attname();
            $result['attachmentdrafts'][$att_id]->id = $att["id"];
            $result['attachmentdrafts'][$att_id]->player_id = $att["player_id"];
        }

        $result['zones'] = array();
        foreach($this->zones as $zone)
        {
            if($zone->id>0)
            {
                $result['zones'][$zone->id] = array();
                $result['zones'][$zone->id]["id"] = $zone->id;
                $result['zones'][$zone->id]["title"] = $zone->title;
                $result['zones'][$zone->id]["description"] = $zone->description;
                $result['zones'][$zone->id]["capacity"] = $zone->capacity;
            }
        }

        $result['hand'] = array();
        if(!$this->isSpectator())
        {
            $sql = "SELECT * from deck".$result['players'][$current_player_id]['player_no']." where card_location = 'hand'";
            $result['hand'] = self::getCollectionFromDb( $sql );
        }

        
        $result['board'] = self::getGameStateValue( 'board');
        $result['debug'] = self::getGameStateValue( 'debug');
  
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        $nbrune = self::getUniqueValueFromDB( "select max(player_score) from player");
        $ret = 0;

        if($nbrune>=4)
        {
            $ret = 100;
        }
        else if($nbrune>=3)
        {
            $ret = 75;
        }
        else  if($nbrune>=2)
        {
            $ret = 51;
        }
        else  if($nbrune>=1)
        {
            $ret = 25;
        }
        return $ret;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

function getActivePlayer()
{
    return $this->playersMBR[$this->getActivePlayerId()];
}

function getBoard()
{
    return $this->boards[self::getGameStateValue( 'board')];
}

function getStartingZones($player_id)
{
    $board = $this->getBoard();
    return $board['setup'][self::getGameStateValue( 'boardsetup')][$this->playersMBR[$player_id]->player_no];
}

function getDeck($playerId)
    {
        
        $order = self::getUniqueValueFromDB( "SELECT player_no from player where player_id=".$playerId);
        if($order == null)
        {
            return null;
        }
        else if($order == 1)
        {
            return $this->deck1;
        }
        else
        {            
            return $this->deck2;
        }
    }

    
    function addPending($player_id, $unit_id = 0, $function = NULL, $arg = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL) {
        $sql = "INSERT INTO pending (player_id, unit_id, function,  arg, arg2, arg3, arg4) VALUES (".$player_id.", '".$unit_id."', '".$function."', '".$arg."', '".$arg2."', '".$arg3."', '".$arg4."')";
        self::DbQuery( $sql );
    }
    
    function addPendingFirst($player_id, $unit_id = 0, $function, $arg = NULL, $arg2 = NULL) {
        $minid = self::getUniqueValueFromDB( "select min(id) from pending")-1;
        $sql = "INSERT INTO pending (id, player_id,unit_id, function, arg, arg2) VALUES (".$minid.",".$player_id.",'".$unit_id."', '".$function."', '".$arg."', '".$arg2."')";
        self::DbQuery( $sql );
    }

    function callPending($pending, $execute, $arg1 = null, $arg2 = null)
    {        
        $this->loadAll();
        $obj = $this;
        if($pending['player_id'] != null)
        {
            $obj = $this->playersMBR[$pending['player_id']];
        }
        if($pending['unit_id'] != 0)
        {
            $unit = $this->units[$pending['unit_id']];
            $unit->player = $obj;
            $obj = $unit;
        }        

        $fname = $pending['function'];
        if(str_starts_with($fname, "talent"))
        {
            $talentName = str_replace("talent","",explode(".",$fname)[0]);
            if(!array_key_exists($talentName, $obj->talents))
            {
                $talentNameFull = 'talent'.$talentName;
                $talent = new $talentNameFull($unit);
                $unit->talents[$talentName] = $talent;
                $talent->unit = $unit;
            }
            $fname = explode(".",$fname)[1];

            if(!method_exists($obj, $fname))
            {
                $obj = $obj->talents[$talentName];
            }
        }
        if(str_starts_with($fname, "terrain"))
        {
            $obj = $this->zones[$unit->zone->id];
            $fname = explode(".",$fname)[1];
        }
        if(str_starts_with($fname, "attachment"))
        {
            $obj = $unit->attachment;
            $fname = explode(".",$fname)[1];
        }
        
        if($obj instanceof unit && method_exists($obj->attachment, $fname))
        {
            $obj = $obj->attachment;
        }


        if(!$execute)
        {
            $fname = "arg".$fname;
        }
        
        $ret = null;
        if(method_exists($obj, $fname))
        {
            $ret = $obj->$fname($pending['arg'], $pending['arg2'], $arg1, $arg2);
        }
        
        return $ret;
    }

    function onTiming($time, $attack = NULL)
    {
        foreach($this->zones as $zone)
        {
            $zone->onTiming ($time, $attack);
        }

        foreach($this->units as $unit)
        {
            $unit->onTiming($time, $attack);
        }
    }

    function requiresAdditionalAOW($action)
    {
        $ret = 0;
        foreach($this->units as $unit)
        {
            $ret += $unit->requiresAdditionalAOW($action);
        }
        return $ret;
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

function actSelect($arg1, $arg2 )
    {       
        $this->loadAll();
        self::setGameStateValue( 'no_undo', 0 );
        if(str_starts_with($arg1, "butAny"))
        {
            $player_id = $this->getCurrentPlayerId(true);
            if($player_id != null)
            {
                $action = str_replace("but","",$arg1);

                if (preg_match('/\d/', $action)) {
                    $unit_id = preg_replace("/[^0-9]/", "", $action);
                    $action = str_replace($unit_id,"",$action);
                    $action = str_replace("Any","",$action);
                    mythicbattlesragnarok::$instance->addPending($player_id, $unit_id, $action);
                }
                else
                {
                    mythicbattlesragnarok::$instance->addPending($player_id,0, $action);
                }

            }
        }
        else
        {
            self::checkAction( 'select' );  
            self::checkArgs($arg1, $arg2 );
            if($arg1 == "butUndo")
            {
                $this->undoRestorePoint();
                $this->gamestate->nextState('next');
                return;
            }
            
            $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
            $this->callPending($pending, true, $arg1, $arg2);
            self::DbQuery("delete from pending where id=".$pending['id']);            
            
            $this->giveExtraTime(self::getActivePlayerId());  
        }
        $this->gamestate->nextState( 'next');   
    }

    function checkArgs($arg1, $arg2)
    {
        $ret = self::argPlayerTurn();
        
        if(in_array('multiple',array_keys($ret)))
        {
            if(!in_array($arg1,array_keys($ret['selectable'])))
            {
                throw new feException( "Not a valid move");
            }
            foreach(array_filter(explode(" ",$arg2)) as $arg)
            {
                if(!in_array($arg,array_keys($ret['selectable'])))
                {
                    throw new feException( "Not a valid target");
                }
            }
        }
        else if(!in_array($arg1,array_keys($ret['selectable'])))
        {
            throw new feException( "Not a valid move");
        }
        else if($arg2 != null && (!is_array($ret['selectable'][$arg1]['target']) || !in_array($arg2,$ret['selectable'][$arg1]['target'])))
        {
            throw new feException( "Not a valid target");
        }
    }

    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    function argPlayerTurn()
    {        
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $arg = $this->callPending($pending, false); 
        
        //HACK RATATOSK
        if($pending['unit_id'] != NULL && $pending['unit_id'] != 0 && $arg != null && $arg['selectable'] != null)
        {
            $rata_id = self::getUniqueValueFromDB("SELECT id FROM unit WHERE type = 52");
            if($rata_id != null && array_key_exists("unit".$rata_id, $arg['selectable']))
            {
                $nb = 0;
                foreach($arg['selectable'] as $id => $test)
                {
                    if (strpos($id, "unit") === 0) {                        
                        $unit_id = str_replace("unit","",$id);
                        if($this->units[$unit_id]->zone->id == $this->units[$rata_id]->zone->id)
                        {
                            $nb++;
                        }
                    }
                }
                if($nb>1)
                {
                    unset($arg['selectable']["unit".$rata_id]);
                }
            }
        }
        //END HACK

        $sql = "SELECT * FROM pending  order by id";
        $arg['pendings'] = self::getObjectListFromDB( $sql );

        $arg['status'] = array();
        foreach($this->units as $unit)
        {
            $arg['status'][$unit->id] = array_unique($unit->status);
        }
        $arg['players'] = array();
        foreach($this->playersMBR as $player)
        {
            $arg['players'][$player->player_id] = $player->getAnyTimeActions($player->player_id);
        }

        if($this->getGameStateValue( 'no_undo') == 0)
        {
            $arg['selectable']['butUndo'] = array("title" => clienttranslate("Undo"), "color"=>"gray");
        }

        return $arg;
    } 

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////


    function resetUndo()
    {
        $this->setGameStateValue( 'no_undo', 1);
        $this->undoSavepoint( );
    }

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    function stPending() {
        
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");  
        if($pending == null)
        {
            $this->gamestate->nextState( 'end' );            
        }
        else
        {
            //force reload
            $this->units = array();

            $args = $this->callPending($pending, false);
            if($pending['player_id'] != self::getActivePlayerId())
            {          
                $this->resetUndo();

                //change active player      
                $this->gamestate->changeActivePlayer( $pending['player_id']);    
                $this->gamestate->nextState( 'same' );
            }
            else if($args == null || count($args['selectable']) == 0 )
            {
                //no args required, execute
                $this->callPending($pending, true);
                self::DbQuery("delete from pending where id=".$pending['id']);
                $this->gamestate->nextState( 'same' );
            }
            else if(count($args['selectable']) == 1 && !array_key_exists('Pass',$args['selectable']) && !array_key_exists('Undo',$args['selectable']))
            {
                //AUTO PLAY IF ONLY ONE CHOICE
                foreach($args['selectable'] as $arg1 => $argnul)
                {
                    $this->callPending($pending, true, $arg1);
                }
                self::DbQuery("delete from pending where id=".$pending['id']);
                $this->gamestate->nextState( 'same' );
            }
            else
            {  
                //player input required
                $this->gamestate->nextState( 'player' );
            }
        }
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            //endof game
            $zombie = $this->playersMBR[$active_player];
            mythicbattlesragnarok::DbQuery( "update player set player_score = 4 where player_id = ".$zombie->getOtherPlayer()->id);
            mythicbattlesragnarok::DbQuery( "update player set player_score = 0 where player_id = ".$active_player);
            mythicbattlesragnarok::DbQuery( "delete from pending");

            mythicbattlesragnarok::$instance->notifyAllPlayers( "innerhtml", '', array(
                'id' => "player_score_".$active_player,
                'html' => '0'
            ) );
            mythicbattlesragnarok::$instance->notifyAllPlayers( "innerhtml", '', array(
                'id' => "player_score_".$zombie->getOtherPlayer()->id,
                'html' => '4'
            ) );

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive( $active_player, '' );
            
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        if( $from_version <= 2402090900 )
       {
            // ! important ! Use DBPREFIX_<table_name> for all tables
            $sql = "ALTER TABLE DBPREFIX_unit ADD `statusOwnActivation` varchar(200) NOT NULL DEFAULT ''";
            self::applyDbUpgradeToAllDB( $sql );
        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//


    }    
}
