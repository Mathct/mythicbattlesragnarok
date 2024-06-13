<?php 

class talentCloseProtection extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Close Protection');
        $this->description = clienttranslate('The unit gains +1 defense if another allied unit is in the same area.');
    }

    public function getStatBonus($stat, $to, $attack)
    {
        if($stat == DEFENSE && $attack->to == $this->unit && $to == $this->unit)
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