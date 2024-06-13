<?php 

class talentArchery extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Archery');
        $this->description = clienttranslate('The unit gains +1 offense for their range 1+ attacks.');
    }

    
    public function getStatBonus($stat, $to, $attack)
    {
        if( $stat == OFFENSE && $this->unit->player_id != $attack->to->player_id && $to == $this->unit && $attack->range>=1)
        {
            return 1;                    
        }
        return 0;
    }
}