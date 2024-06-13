<?php 

class talentBolster extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Bolster');
        $this->description = clienttranslate('During the calculation of the effective offense and defense values of a normal or area attack, the allied troop units in the same area as the unit with the Bolster talent gain +1 offense and +1 defense.');
    }

    public function getStatBonus($stat, $to, $attack)
    {
        if( ($stat == DEFENSE || $stat == OFFENSE) && ($attack->type == ATNORMAL || $attack->type == AREA) && $to->category == TROOP && $this->unit->player_id == $to->player_id && $this->unit->zone == $to->zone)
        {
            return 1;                    
        }
        return 0;
    }
}