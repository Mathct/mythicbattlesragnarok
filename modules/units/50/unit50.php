<?php 

class unit50 extends unit
{
    public $category = MONSTER;
    public $cost = 4;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("Block","ForceOfNature","MightyThrow");

    public $stats =  [
        [9, 7, 0, 3,1,5,0],
        [8, 7, 0, 2,1,5,0],
        [8, 7, 0, 2,1,5,0],
        [7, 7, 0, 2,1,4,0],
        [7, 6, 0, 1,1,4,0],
        [7, 6, 0, 1,1,4,0],
        [6, 5, 0, 1,1,3,0],
        [6, 5, 0, 0,1,0,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Hyrrokkin");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Venom"), clienttranslate('At the end of a range 0 attack targeting Hyrrokkin, inflict 1 wound on her attacker.'));
         $this->powers[1] = new power(1,OFFENSIVE, WHITE, clienttranslate("Beast's Bite"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_black">X</div> &nbsp;  &nbsp; <div class="mbr_range"></div>0</div>Make an X dice, range 0 area attack, targeting only enemy units.'),1);
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTERATTACKWOUND && $this->canUse($this->powers[0]) && $attack->to == $this && $attack->range==0)
        {
            $attack->from->wound(1,$this);
        }
    }

    public function activatePower($index)
    {
        if($index == 1 && $this->getStat(POWER2)>0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 0, $this->getStat(POWER2), "onlyennemy forceoffense"); 
        }
    }

}