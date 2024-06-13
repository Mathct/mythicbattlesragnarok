<?php 

class talentInitiative extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Initiative');
        $this->description = clienttranslate('After the choice of the target of a range 0 normal attack, if the target is the unit with the Initiative talent, they can retaliate before the original attack is resolved. After the retaliation, if the attacker is further away from the target than their range, the action ends. If not, the attacker carries out the attack without the target retaliating again. If both the attacker and the target have this talent, the effect is ignored.');
    }

    public function onTiming($time, $attack)
    {
        
        if($time == AFTERVALIDATINGTARGET)
        {
            if($this->unit->canUse($this) && $attack->to == $this->unit && !$attack->from->hasTalent("Initiative") && $attack->type == ATNORMAL && $attack->range == 0 && $this->unit->canRetaliate($attack))
            {
                mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "A3_Retaliate", $attack->toJSON(), "initiative");
            }
        } 
    }
}