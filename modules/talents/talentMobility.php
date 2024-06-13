<?php 

class talentMobility extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Mobility');
        $this->description = clienttranslate('A unit with the Mobility talent can walk even after having carried out an attack.');
    }

    public function getAuraStatus($to)
    {
        if($to == $this->unit && $this->unit->canUse($this))
        {
            return array("mobility");
        }
        return array();
    }
}