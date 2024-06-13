<?php 

class unit47 extends unit
{
    public $category = MONSTER;
    public $cost = 2;
    public $activation = 3;
    public $aow = 2;
    public $talentsNames = array("GemCollector","Guard");

    public $stats =  [
        [6, 6, 0, 3,1,1,1],
        [5, 6, 0, 3,1,1,1],
        [4, 5, 0, 2,1,1,1],
        [3, 5, 0, 2,1,1,1],
        [3, 4, 0, 1,0,1,1],
        [3, 4, 0, 1,0,1,1]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Gullinbursti");
         $this->powers[0] = new power(0,ACTIVE, BLACK, clienttranslate("Tusk Attack"), clienttranslate('At the end of Gullinbursti\'s activation, perform a normal attack with 3 dice against each enemy unit in his area.'));
         $this->powers[1] = new power(1,PASSIVE, WHITE, clienttranslate("Forcing The Passage"), clienttranslate('When Gullinbursti performs a walk action, entering an area containing one or more enemy units does not end his movement. Gullinbursti also ignores the Block talent.'));
         $this->powers[2] = new power(2,PERMANENT, BLACK, clienttranslate("Golden Silks"), clienttranslate('When an opponent destroys Gullinbursti, they take an Art of War card from the Art of War deck and add it to their hand.'));
    
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == ENDACTIVATION && $this->canUse($this->powers[0]) && $attack->from == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 0, 3, "onlyennemy"); 
        }   
        if($time == BEFOREDIE && $attack->to == $this && $attack->from != null)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Golden", $attack->toJSON()); 
        }   
    } 

    public function getAuraStatus($to)
    {
        $ret = parent::getAuraStatus($to);
        if($to == $this && $this->canUse($this->powers[1]))
        {
            $ret[] = "noblock";
        }
        return $ret;
    }

    function IsStoppedMoving($other)
    {
        return parent::IsStoppedMoving($other) && !$this->canUse($this->powers[1]);
    }

    function Golden($parg1, $parg2, $varg1, $varg2) {        
        
        $other = $this->player->getOtherPlayer();
        $player_no = $other->player_no;
        $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT max(card_id) from deck".$player_no) + 1;
        mythicbattlesragnarok::DbQuery( "insert into deck".$player_no."(card_id, card_type, card_type_arg, card_location, card_location_arg) values(".$card_id.",".AOW.",".AOW.",'hand',0)");
        $card = self::getObjectFromDB( "SELECT * FROM deck".$player_no." where card_id = ".$card_id);
        
        mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} gets an extra aow card'), array(
            'player_name' => $other->player_name,
            'player_id' => $other->player_id
        ) );
        mythicbattlesragnarok::$instance->notifyPlayer( $other->player_id, "draw", '', array(
            'card' => $card
        ) );
        $other->refreshCardsCounter();        
    }

    function zonetarget($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {        
        if($varg1 != 'fake')
        {      
            $offensive = $parg1;
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zonetarget", $offensive, $parg2);

            $unitid = str_replace("unit","", $varg1);        
            $unit = mythicbattlesragnarok::$instance->units[$unitid]; 
            
            $unit->status[] = "zonetarget";
            mythicbattlesragnarok::DbQuery("update unit set statusActivation = REPLACE(statusActivation, ' zonetarget', '' ) where id = ".$unit->id);

            $range = $this->zone->getDistanceWith($unit->zone);
            $player_id = $this->player_id;
            if($this->player_id == $unit->player_id)
            {
                $player_id = $this->player->getOtherPlayer()->player_id;
            }      
            
            $attack = new attack();
            $attack->range = $range;
            $attack->from = $this;
            $attack->to = $unit;
            $attack->type = NORMAL;
            mythicbattlesragnarok::$instance->addPending($player_id,$this->id, "A2A_ValueCalculation", $attack->toJSON(), $offensive);
            mythicbattlesragnarok::$instance->onTiming(AFTERSELECTINGTARGET, $attack);
            
        }   
    }
}