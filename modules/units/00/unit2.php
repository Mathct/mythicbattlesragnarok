<?php 

class unit2 extends unit
{
    public $category = MONSTER;
    public $cost = 5;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("Block","Guard","Terror");

    public function __construct()
    {
        $this->name = clienttranslate("Fafnir");
        $this->powers[0] = new power(0, OFFENSIVE, BLACK, clienttranslate("Claws and fangs"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_black">X</div> &nbsp;  &nbsp; <div class="mbr_range"></div>0</div>Make an X dice, <i>range</i> 0 area attack.'),1);
        $this->powers[1] = new power(1, PERMANENT, WHITE, clienttranslate("Scorn"), clienttranslate("Fafnir ignores wounds caused by area attacks and range 1+ attacks.<br/>Talents and powers that could move Fafnir are ignored."));
    }

    public $stats =  [
        [8, 8, 1, 2,7,1,0],
        [8, 8, 1, 2,7,1,0],
        [7, 8, 1, 1,6,1,0],
        [7, 7, 1, 1,6,1,0],
        [7, 7, 1, 1,6,1,0],
        [6, 7, 1, 1,5,1,0],
        [6, 6, 0, 1,4,1,0],
        [5, 6, 0, 0,0,1,0],
        [5, 6, 0, 0,0,1,0]
    ];

    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 0, $this->getStat(POWER1)); 
        }
    }

    public function getAuraStatus($to)
    {
        $ret = parent::getAuraStatus($to);
        if($to == $this && $this->canUse($this->powers[1]))
        {
            $ret[] = "noforce";
        }
        return $ret;
    }

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[1]) && $stat == DAMAGEBONUS && $attack->to == $this && ( $attack->type == AREA || $attack->range>0))
        {
            $ret[$this->powers[1]->title] = -99;
            $ret['total'] = -99;
        }
        return $ret;
    }
}