<?php 

class unit34 extends unit
{
    public $category = HERO;
    public $cost = 4;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("Berserk","HeroSlayer","Leader");

    public $stats =  [
        [7, 7, 0, 2,7,1,0],
        [7, 7, 0, 2,7,1,0],
        [6, 7, 0, 2,6,1,0],
        [6, 7, 0, 1,6,1,0],
        [6, 6, 0, 1,6,1,0],
        [5, 6, 0, 1,5,1,0],
        [4, 6, 0, 1,4,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Veteran Lagertha");
         $this->powers[0] = new power(0,OFFENSIVE, BLACK, clienttranslate("Heroic Breakthrough"), clienttranslate('Make a range 0 area attack. Veteran Lagertha can then move 1 area away, without being affected by the Block talent.'),1);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Final Embrace"), clienttranslate('When she is destroyed, Veteran Lagertha makes a final attack with 6 dice against her attacker if they are at range 0 or 1.'));
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "addtalentblock",0);
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "move");
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "addtalentblock",1);
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 0, $this->getStat(POWER1)); 
        }
    }

    
    function addtalentblock($parg1, $parg2, $varg1, $varg2) {  
        if($parg1 == 1)
        {
            $this->status[] = "talentBlock"; 
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' talentBlock' ) where id = ".$this->id);
            $talent = new talentBlock($this);
            $this->talents["Block"] = $talent;
            $talent->unit = $this; 
            $key = array_search("nowalk", $this->status);
            if ($key !== false) {
                unset($this->status[$key]);
            }
        }
        else{
            mythicbattlesragnarok::DbQuery("update unit set statusTurn =  REPLACE(statusTurn, ' talentBlock', '' )  where id = ".$this->id);
            unset($this->talents["Block"]);
        }
    }



    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == BEFOREDIE &&  $attack->to == $this && $attack->from != null && $attack->from->player_id != $attack->to->player_id && $attack->range <= 1)
        {
            $nattack = new attack();
            $nattack->from = $this;
            $nattack->to = $attack->from;
            $nattack->range = 0;
            $nattack->type = RETALIATE;
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A2A_ValueCalculation", $nattack->toJSON(),6);
        }
        
        if($time == RECRUIT && $attack == $this)
        {            
            $id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT id from unit where type=33");
            self::DbQuery( "DELETE FROM unit WHERE type=33");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                'id' => 'unit'.$id
            ) );
        }
    }

}