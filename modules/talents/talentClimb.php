<?php 

class talentClimb extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Climb');
        $this->description = clienttranslate('A unit with the Climb talent can walk into rock or cliff areas. The unit may cross escarpment boundaries. In a rock area, the unit gains +1 defense against units who don’t have the Climb talent.');
    }
    
    public function getStatBonus($stat, $to, $attack)
    {
        if($stat == DEFENSE && $attack->to == $this->unit && $to == $this->unit && $this->unit->zone instanceof terrainROCK && !$attack->from->hasTalent("Climb"))
        {
            return 1;
        }
        return 0;
    }
}