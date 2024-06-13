<?php 

class talentSneakAttack extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Sneak Attack');
        $this->description = clienttranslate('The unit gains +1 offense until the end of their activation if another allied unit is in the same area during a range 0 normal attack.');
    }

    public function getStatBonus($stat, $to, $attack)
    {
        if($stat == OFFENSE && $to == $this->unit && $attack->from == $this->unit && $attack->range == 0)
        {
            foreach($this->unit->zone->units as $unit)
            {
                if($unit != $this->unit && $unit->player_id == $this->unit->player_id)
                {
                    return 1;
                }
            }
        }
        return 0;
    }
}