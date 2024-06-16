<?php 

class unit30 extends unit
{
    public $category = HERO;
    public $cost = 5;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("GemCollector","Leader", "Torment");

    public $stats =  [
        [7, 7, 0, 2,1,1,0],
        [7, 7, 0, 2,1,1,0],
        [6, 8, 0, 1,1,1,0],
        [6, 8, 0, 1,1,1,0],
        [6, 9, 0, 1,1,1,0],
        [5, 9, 0, 1,1,0,0],
        [5, 9, 0, 0,1,0,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Gullveig");
         $this->powers[0] = new power(0,ACTIVE, BLACK, clienttranslate("Corruption"), clienttranslate('During your turn, you can recruit a destroyed enemy troop unit (minus any attachment). Put that unit\'s activation cards into your deck. You and your opponent shuffle your new decks. Redeploy this unit in Gullveig\'s surroundings. Your opponent takes an Art of War card from the Art of War deck and adds it to their hand.'),1);
         $this->powers[1] = new power(1,ACTIVE, WHITE, clienttranslate("Discord"), clienttranslate('When recruited, take Gullveig\'s token. Give this token to an opponent during your turn. That opponent is unable to perform a maneuver or a troop recall during their next turn.'),1,1);
    }

    
    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Corruption"); 
        }
    }

    function argCorruption($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Corruption : ${actplayer} may choose the destroyed ennemy troop to corrupt');
        $ret['titleyou'] = clienttranslate('Touched by the sun : ${you} may choose the destroyed ennemy troop to corrupt'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");

        foreach(mythicbattlesragnarok::$instance->units as $unit)
        {
            if($unit->player_id != $this->player_id && $unit->zone_id < 0 && $unit->category == TROOP)
            {
                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Do you want to corrupt ${unitid_display} ?'
                );
            }
        }
        return $ret;
    }

    
    function Corruption($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {            
            $unit_id = str_replace("unit","", $varg1);
            $unit = mythicbattlesragnarok::$instance->units[$unit_id];
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", clienttranslate('${unitid_display} is corrupted'), array(
                'unitid_display' => $unit_id,
                'id' => 'unit'.$unit_id
            ) );

            if($unit->attachment->id>0)
            {
                mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                    'id' => 'attachment'.$unit->attachment->id
                ) );
            }

            mythicbattlesragnarok::DbQuery("update unit set player_id = ".$this->player_id.", zone_id = -".$this->player->player_no.", attachment = 0 where id = ".$unit_id);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "backontable", '', array(
                'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$unit_id),
                'category' => $this->category
            ) ); 

            $otherplayer = $this->player->getOtherPlayer();            

            $sql = "SELECT * from deck".$otherplayer->player_no." where card_type_arg = ".$unit_id;
            $cards = self::getCollectionFromDb( $sql );
            $nbcards = count($cards);
            foreach($cards as $card)
            {    
                if($card['card_location'] == 'hand')
                {
                    mythicbattlesragnarok::$instance->notifyPlayer( $otherplayer->player_id, "fadeOutAndDestroy", '', array(
                        'id' => 'card'.$card['card_id']
                    ) ); 
                }      
            }
            mythicbattlesragnarok::DbQuery( "delete from deck".$otherplayer->player_no." where card_type_arg = ".$unit_id);
            
            for($i=0;$i<$nbcards;$i++)
            {
                mythicbattlesragnarok::DbQuery( "insert into deck".$this->player->player_no."(card_type, card_type_arg, card_location, card_location_arg) values(".$unit->type.",".$unit->id.",'deck',0)");
            }

            $otherplayer->getDeck()->shuffle( 'deck' );
            $this->player->getDeck()->shuffle( 'deck' );
            $otherplayer->refreshCardsCounter();
            $this->player->refreshCardsCounter();

            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT max(card_id) from deck".$otherplayer->player_no) + 1;
            mythicbattlesragnarok::DbQuery( "insert into deck".$otherplayer->player_no."(card_id, card_type, card_type_arg, card_location, card_location_arg) values(".$card_id.",".RUNE.",".RUNE.",'hand',0)");
            $card = self::getObjectFromDB( "SELECT * FROM deck".$otherplayer->player_no." where card_id = ".$card_id);
            mythicbattlesragnarok::$instance->notifyPlayer( $otherplayer->player_id, "draw", '', array(
                'card' => $card
            ) );

            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Redeploy", $unit_id); 

        }
    }

    function argRedeploy($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may redeploy ${unitid_display}');
        $ret['titleyou'] = clienttranslate('${you} may redeploy ${unitid_display}'); 
        $ret['unitid_display'] = $parg1; 
        
        $unit_id = $parg1;
        $unit = mythicbattlesragnarok::$instance->units[$unit_id];

        $zones = $this->zone->getZonesAtDistance(0,1);
        foreach($zones as $zone)
        {
            if($this->canSeeZone($zone) && $zone->canEnter($unit))
            {
                $ret['selectable']['zone'.$zone->id] = array(); 
            }
        }
        if(count($ret['selectable']) == 0)
        {
            $ret['titleyou'] = clienttranslate('${you} cannot recall a unit of troops'); 
            $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray"); 
            $ret['selectable']['fake']  = array();  
        }

        return $ret;
    }
    
    //$parg1 : nodiscard
    function Redeploy($parg1, $parg2, $varg1, $varg2)   
    {        
        if($varg1 != "butskip")
        {
            $unit_id = $parg1;
            $unit = mythicbattlesragnarok::$instance->units[$unit_id];
            $unit->deploy(NULL, NULL, $varg1, $varg2);
        }
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == ENDOFTURN && $this->canUse($this->powers[1]) && $attack == $this->player_id)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Discord");
        }
        if($time == ENDACTIVATION && $attack->from == $this && $this->canUse($this->powers[0]))
        { 
           mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Corruption");
        }
    } 
    
    function argDiscord($parg1, $parg2)   
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Discord : ${actplayer} may prevent you to perform maneuvers or troop recalls');
        $ret['titleyou'] = clienttranslate('Discord : ${you} may prevent your opponent to perform maneuvers or troop recalls'); 
        if($this->player->getAowInHand()>0)
        {
            $ret['selectable']['butuse'] = array("title" => clienttranslate("Use")); 
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray"); 

        return $ret;
    }

    function Discord($parg1, $parg2, $varg1, $varg2)   
    {
        if($varg1 != "butskip")
        {      
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} uses ${talent}'), array(
                'i18n' => array( 'talent'),
                'player_name' => $this->player->player_name,
                'player_id' => $this->player_id,
                'talent' => "Discord"
                ) );

            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "DiscardAOW");         
            $this->status[] = 'power1';
            mythicbattlesragnarok::DbQuery("update unit set statusGame = concat(statusGame, ' power1' ) where id = ".$this->id);
            mythicbattlesragnarok::DbQuery("update player set endofturnstatus = concat(endofturnstatus, ' nomaneuver norecall' ) where player_id = ".$this->player->getOtherPlayer()->id);
        }
    }

}