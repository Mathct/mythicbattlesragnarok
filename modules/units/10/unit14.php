<?php 

class unit14 extends unit
{
    public $category = TROOP;
    public $cost = 1;
    public $activation = 3;
    public $talentsNames = array("Guard","SneakAttack");

    public function __construct()
    {
        $this->name = clienttranslate("Huscarls");
        $this->powers[0] = new power(0,PERMANENT, WHITE, "Huscarls", clienttranslate('This unit inflicts an additional wound if it wounds a troop.'));
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
        if($this->canUse($this->powers[0]) && $stat == DAMAGEBONUS && $attack->from == $this && $attack->to->category == TROOP && $attack->to->player_id != $attack->from->player_id && $attack->wounds>0)
        {          
            $ret[$this->name] = 1;
            $ret['total']++;
        }
        return $ret;
    }

}