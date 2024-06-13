<?php 

class unit5 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("Block","ForceOfNature","MightyThrow");
    
    public $stats =  [
        [9, 9, 0, 2,1,1,1],
        [8, 9, 0, 2,1,1,1],
        [8, 8, 0, 2,1,1,1],
        [8, 8, 0, 2,1,1,1],
        [7, 8, 0, 2,1,1,1],
        [7, 8, 0, 1,1,1,1],
        [7, 7, 0, 1,1,1,1],
        [7, 7, 0, 1,1,1,1],
        [6, 7, 0, 0,0,1,1],
        [6, 6, 0, 0,0,0,1],
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Tyr");
        $this->powers[0] = new power(0, OFFENSIVE, BLACK, clienttranslate("Hand of Tyr"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_black">6</div> &nbsp;  &nbsp; <div class="mbr_range"></div>0</div>Make a 6 dice, <i>range</i> 0 area attack.'),1);
        $this->powers[1] = new power(1, PASSIVE, WHITE, clienttranslate("Tiwaz"), clienttranslate("After Tyr’s activation, if the player activates another unit in the same turn, that unit can re-roll a die (even a blank result) if it attacks."));
        $this->powers[2] = new power(2, PERMANENT, BLACK, clienttranslate("Justice"), clienttranslate("During Tyr’s activation, you can retrieve a non-divinity card from your discard pile. You choose one of your opponents to do the same."));
    }
    
    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 0, 6); 
        }
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == AFTER1STROLL && in_array("activated", $this->status) && $this != $attack->from && $this->player_id == $attack->from->player_id)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Tiwaz");
        }
        if($time == STARTACTIVATION && $this->canUse($this->powers[2]) && $this == $attack->from)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Justice");
        }
    }  

    function argTiwaz($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Tiwaz : ${actplayer} may reroll a die');
        $ret['titleyou'] = clienttranslate('Tiwaz : ${you} may reroll a die'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");        
        if(!in_array("noreroll", $this->status))
        {
            $dices = self::getCollectionFromDb( "SELECT * FROM die"); 
            foreach($dices as $die)
            {            
                $ret['selectable']['diceres'.$die['id']] = array();                                 
            } 
        }
        return $ret;
    }
    
    function Tiwaz($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $toimproveid = str_replace("diceres","", $varg1);
            $face = bga_rand( 0, 5 );
            mythicbattlesragnarok::DbQuery( "update die set value = value+".$face.", face = ".$face." where id=".$toimproveid);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "setDice", '', array(
                'die' => self::getObjectFromDB( "SELECT* FROM die where id = ".$toimproveid)
            ) );
            mythicbattlesragnarok::$instance->resetUndo();
        }
    }

    function argJustice($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Justice : ${actplayer} may retrieve a non-divinity card from their discard pile');
        $ret['titleyou'] = clienttranslate('Justice : ${you} may retrieve a non-divinity card from your discard pile'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['pickcards'] = array();
       
        $sql = "SELECT * from deck".mythicbattlesragnarok::$instance->getActivePlayer()->player_no." where card_location = 'discard'";
        $hand = self::getCollectionFromDb( $sql );
        foreach($hand as $card)
        {        
            if($card['card_type_arg']>0) 
            {
                $unit = mythicbattlesragnarok::$instance->units[$card['card_type_arg']];
                if($unit->category == GOD || $unit->category == TITAN)
                {
                    continue;
                }
            }  
            if($card['card_type_arg'] != $this->id)
            {
                $ret['selectable']['card'.$card['card_id']] = array();
                $ret['pickcards'][] = $card;  
            }          
        }
        return $ret;
    }
    
    function Justice($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $card_id = str_replace("card","", $varg1);
            $player = mythicbattlesragnarok::$instance->getActivePlayer();
            $card = self::getObjectFromDB( "SELECT * FROM deck".$player->player_no." where card_id = ".$card_id);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "discard", clienttranslate('${player_name} retrieves ${unitid_display}'), array(
                'player_name' => $player->player_name,
                'player_id' => $player->player_id,
                'unitid_display' => $card['card_type_arg'],
                'card' => $card
            ) );
            $player->getDeck()->moveCard($card_id, 'hand'); 
            $card = self::getObjectFromDB( "SELECT * FROM deck".$player->player_no." where card_id = ".$card_id);
            mythicbattlesragnarok::$instance->notifyPlayer( $player->player_id, "draw", '', array(
                'card' => $card
            ) );

            $player->refreshCardsCounter();

            if(mythicbattlesragnarok::$instance->getActivePlayer()->player_id == $this->player_id)
            {
                mythicbattlesragnarok::$instance->addPending($this->player->getOtherPlayer()->player_id,$this->id, "Justice");
            }
        }
    }
}