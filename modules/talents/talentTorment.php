<?php 

class talentTorment extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Torment');
        $this->description = clienttranslate('A targeted unit suffers -1 defense against the range 0 attack of a unit with the Torment talent.');
    }

    public function getStatBonus($stat, $to, $attack)
    {
        if($stat == DEFENSE && $attack->from == $this->unit && $attack->range == 0 && ($attack->type == ATNORMAL || $attack->type == RETALIATE))
        {
            return -1;
        }
        return 0;
    }
}