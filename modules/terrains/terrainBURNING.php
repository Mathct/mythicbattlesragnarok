<?php

class terrainBURNING extends terrain
{
    public function __construct()
    {
        $this->title = clienttranslate("Burning");
        $this->description = clienttranslate("Movement allowed – a unit entering this area suffers 1 wound. The unit is then moved into an adjacent non-burning area. The unit’s movement action ends.");
    }
    
    public function onEnter($unit)
    {
        parent::onEnter($unit);
        if(!in_array("ignoreBurning", $unit->status)  && $this->countAsFor($unit) == $this->type)
        {
            mythicbattlesragnarok::$instance->DbQuery("DELETE from pending where player_id = {$unit->player_id} AND unit_id = {$unit->id} and function = 'move'");
            mythicbattlesragnarok::$instance->addPending($unit->player_id, $unit->id, "move", "mandatory");
            $unit->wound(1, clienttranslate('Burning'));
        }
    }

}