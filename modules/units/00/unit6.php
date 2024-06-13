<?php 

class unit6 extends unit
{
    public $category = TROOP;
    public $cost = 1;
    public $activation = 3;
    public $talentsNames = array("Berserk","Torment");

    public function __construct()
    {
        $this->name = clienttranslate("Ulfhednar");
        $this->powers[0] = new power(0,PERMANENT, WHITE, "Ulfhednar", clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div>+1</div> if this unit contains at least 2 Ulfhednar.'));
    }

    public $stats =  [
        [3, 5, 0, 1,1,0,0],
        [3, 5, 0, 1,1,0,0],
        [3, 5, 0, 1,1,0,0],
        [3, 5, 0, 1,1,0,0]
    ];

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[0]) && $stat == OFFENSE && $attack->from == $this && $this->hp > 1)
        {
            $ret[$this->name] = 1;
            $ret['total']++;
        }
        return $ret;
    }

}