<?php

class terrainDIVINE_SOURCE extends terrain
{

    public function __construct()
    {
     $this->title = clienttranslate("Divine Source");
     $this->description = clienttranslate("movement allowed - a unit entering this area must stop its movement, then roll a die and apply the result:<br/> Blank: The unit can move 1 area.<br/> 1: Draw 2 cards.<br/> 2: Search through your deck and take 1 card of your choice to add to your hand, then shuffle your deck.<br/> 3: Look at the first 3 cards of your deck then arrange them in the order of your choice.<br/> 4: Look at the first 2 cards of your opponent’s deck then arrange them in the order of your choice.<br/>5: Choose an effect from the choices above.");
    }

    public function onEnter($unit)
    {
        if($this->countAsFor($unit) == $this->type)
        {

            $idnot =  mythicbattlesragnarok::getUniqueValueFromDB( "SELECT id from pending where function <> 'move' order by id desc limit 1");
            mythicbattlesragnarok::DbQuery( "delete from pending where function = 'move' and unit_id=".$unit->id." and player_id=".$unit->player_id." and id >= ".$idnot);
            
            $d = bga_rand(0,5);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('Divine source rolls ${dice}'), array(
                'dice' => $d
            ) );
            mythicbattlesragnarok::$instance->addPending($unit->player_id,$unit->id, "terrain.divine".$d);
            mythicbattlesragnarok::$instance->resetUndo();
        }
    }

    function divine0($parg1, $parg2, $varg1, $varg2) { 
        
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $unit = mythicbattlesragnarok::$instance->units[$pending["unit_id"]];
        mythicbattlesragnarok::$instance->addPending($unit->player_id,$unit->id, "move");
    }
    
    function divine1($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {     
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $unit = mythicbattlesragnarok::$instance->units[$pending["unit_id"]];
        mythicbattlesragnarok::$instance->addPending($unit->player_id,0, "draw", "mandatory");
        mythicbattlesragnarok::$instance->addPending($unit->player_id,0, "draw", "mandatory");
        mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('Divine source 1: ${player_name} draws 2 cards'), array(
            'player_name' => $unit->player->player_name,
            'player_id' => $unit->player->player_id
        ) );
    }

    function divine2($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {     
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $unit = mythicbattlesragnarok::$instance->units[$pending["unit_id"]];
        mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('Divine source 2: ${player_name} takes 1 card from their deck'), array(
            'player_name' => $unit->player->player_name,
            'player_id' => $unit->player->player_id
        ) );
        mythicbattlesragnarok::$instance->addPending($unit->player->player_id,0, "AnySearch2");
    }

    
    function divine3($parg1, $parg2, $varg1, $varg2) { 
        
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $unit = mythicbattlesragnarok::$instance->units[$pending["unit_id"]];
        $unit->player->getDeck()->pickCardsForLocation( 3, 'deck', 'pick'); 
        mythicbattlesragnarok::$instance->addPending($unit->player_id,$unit->id, "terrain.divinepick");
    }

    function divine4($parg1, $parg2, $varg1, $varg2) { 
        
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $unit = mythicbattlesragnarok::$instance->units[$pending["unit_id"]];
        $unit->player->getOtherPlayer()->getDeck()->pickCardsForLocation( 2, 'deck', 'pick'); 
        mythicbattlesragnarok::$instance->addPending($unit->player_id,$unit->id, "terrain.divinepick", "other");
    }

    function argdivinepick($parg1, $parg2)
    {
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $unit = mythicbattlesragnarok::$instance->units[$pending["unit_id"]];

        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Divine source : ${actplayer} must select one card to put back');
        $ret['titleyou'] = clienttranslate('Divine source : ${you} must select one card to put back'); 
        
        $ret['pickcards'] = array();

        $player_no = $unit->player->player_no;
        if($parg1=="other")
        {
            $player_no = $unit->player->getOtherPlayer()->player_no;
        }
        $sql = "SELECT * from deck".$player_no." where card_location = 'pick'";
        $cards = self::getCollectionFromDb( $sql );
        foreach($cards as $card)
        {    
            $ret['selectable']['card'.$card['card_id']] = array();
            $ret['pickcards'][] = $card;            
        }
        return $ret;
    }
    
    function divinepick($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != null)
        { 
            $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
            $unit = mythicbattlesragnarok::$instance->units[$pending["unit_id"]];
            $deck = $unit->player->getDeck();
            if($parg1=="other")
            {
                $deck = $unit->player->getOtherPlayer()->getDeck();
            }
            $card_id = str_replace("card","", $varg1);
            $deck->insertCardOnExtremePosition( $card_id, 'deck', true);
            mythicbattlesragnarok::$instance->addPending($unit->player_id,$unit->id, "terrain.divinepick", $parg1);
        }
    }

    
    function argdivine5($parg1, $parg2, $varg1, $varg2) { 
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $unit = mythicbattlesragnarok::$instance->units[$pending["unit_id"]];

        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Divine source 5: ${actplayer} must choose an effect');
        $ret['titleyou'] = clienttranslate('Divine source 5: ${you} must choose an effect'); 
        $ret['selectable']['but0'] = array("title" => clienttranslate("Move 1 area"));
        $ret['selectable']['but1'] = array("title" => clienttranslate("Draw 2 cards"));
        $ret['selectable']['but2'] = array("title" => clienttranslate("Take 1 card"));
        $ret['selectable']['but3'] = array("title" => clienttranslate("Rearrange your deck"));
        $ret['selectable']['but4'] = array("title" => clienttranslate("Rearrange your opponent deck"));
        return $ret;
        
    }

    function divine5($parg1, $parg2, $varg1, $varg2) {         
        $choice = str_replace("but","", $varg1);
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $unit = mythicbattlesragnarok::$instance->units[$pending["unit_id"]];
        mythicbattlesragnarok::$instance->addPending($unit->player_id,$unit->id, "terrain.divine".$choice);
    }

}