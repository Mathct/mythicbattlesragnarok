<?php 

class unit11 extends unit
{
    public $category = TROOP;
    public $cost = 1;
    public $activation = 3;
    public $talentsNames = array("Guard","Initiative");

    public function __construct()
    {
        $this->name = clienttranslate("Varangian Guards");
        $this->powers[0] = new power(0,PERMANENT, WHITE, "Varangian Guards", clienttranslate('<div class="mbr_desc"><div class="mbr_range"></div>+1</div> if this unit is complete.'));
    }

    public $stats =  [
        [4, 5, 0, 1,1,0,0],
        [4, 5, 0, 1,1,0,0],
        [4, 5, 0, 1,1,0,0],
        [4, 5, 0, 1,1,0,0]
    ];

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[0]) && $stat == RANGE && $attack->from == $this && $this->hp  == count($this->stats))
        {
            $ret[$this->name] = 1;
            $ret['total']++;
        }
        return $ret;
    }

}