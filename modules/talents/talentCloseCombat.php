<?php 

class talentCloseCombat extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Close Protection');
        $this->description = clienttranslate('The unit gains +1 offense during range 0 attacks until the end of their activation.');
    }

    public function getStatBonus($stat, $to, $attack)
    {
        if($stat == OFFENSE && $attack->from == $this->unit && $to == $this->unit && $attack->range == 0)
        {
           return 1;         
        }
        return 0;
    }
}