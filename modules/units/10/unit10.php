<?php 

class unit10 extends unit
{
    public $category = TROOP;
    public $cost = 1;
    public $activation = 3;
    public $talentsNames = array("Torment");

    public function __construct()
    {
        $this->name = clienttranslate("Oathbreakers");
        $this->powers[0] = new power(0,PERMANENT, WHITE, "Offense", clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div>+1</div> if this unit contains at least 3 Oathbreakers.'));
        $this->powers[1] = new power(1,PERMANENT, WHITE, "Regen", clienttranslate('<div class="mbr_desc"></div>+1 Oathbreaker</div> if this unit is not complete at the end of its activation phase.'));
    }

    public $stats =  [
        [3, 4, 0, 2,1,1,0],
        [3, 4, 0, 2,1,1,0],
        [3, 4, 0, 2,1,1,0],
        [3, 4, 0, 2,1,1,0],
        [3, 4, 0, 2,1,1,0]
    ];

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[0]) && $stat == OFFENSE && $attack->from == $this && $this->hp > 2)
        {
            $ret[$this->name] = 1;
            $ret['total']++;
        }
        return $ret;
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == ENDACTIVATION && $this->canUse($this->powers[1]) && $attack->from == $this && $this->hp < count($this->stats))
        {
            $this->heal(1);
        }
    }

}