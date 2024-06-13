<?php 

class talentTerror extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Terror');
        $this->description = clienttranslate('An attacking unit performing a range 0 attack against a unit with the Terror talents suffers -1 offense.');
    }

    public function getStatBonus($stat, $to, $attack)
    {
        if($stat == OFFENSE && $attack->to == $this->unit && $attack->from == $to && $attack->range == 0)
        {
            return -1;
        }
        return 0;
    }
}