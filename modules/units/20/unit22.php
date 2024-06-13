<?php 

class unit22 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("Block","Climb", "GemCollector");

    public $stats =  [
        [6, 8, 1, 2,1,1,0],
        [6, 8, 1, 2,1,1,0],
        [6, 8, 1, 2,1,1,0],
        [6, 7, 1, 2,1,1,0],
        [5, 7, 1, 2,1,1,0],
        [5, 7, 0, 1,1,1,0],
        [5, 7, 0, 1,1,1,0],
        [5, 6, 0, 1,0,1,0],
        [4, 6, 0, 0,0,0,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Sif");
         $this->powers[0] = new power(0,OFFENSIVE, BLACK, clienttranslate("Earthen Grip"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_black">5</div></div> Make a 5 dice area attack targeting only enemy units in one of the areas in Sif\'s surroundings.'),1);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Royal Presence"), clienttranslate('An enemy unit must discard 1 Art of War card before voluntarily entering Sif’s area. Attacks against Sif suffer -1 offense.'));
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 1, 5, "onlyennemy"); 
        }
    }

    public function getStatBonus($stat, $to, $attack)
    {
        if( $stat == OFFENSE && $attack->from == $to  && $attack->to == $this)
        {
            return -1;                    
        }
        return 0;
    }

    function requiresAdditionalAOW($action)
    {
        $zoneid = str_replace("zone","", $action->varg1);
        if($this->canUse($this->powers[1]) && $action->function == "move" && $action->unit->player_id != $this->player_id && $zoneid == $this->zone_id)
        {
            return 1;
        }
        return 0;
    }

}