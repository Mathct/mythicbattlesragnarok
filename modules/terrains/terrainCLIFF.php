<?php

class terrainCLIFF extends terrain
{    
    public function __construct()
    {
     $this->title = clienttranslate("Cliff");
     $this->description = clienttranslate("Movement forbidden – obstacle.");
    }

    public function isMovementAllowed($unit, $forcemove = false)
    {
        return ($unit->hasTalent("Climb") && !$forcemove)|| ($unit->traitname == "Flying" && $unit->zone->id < 0);
    }

    public function isObstacle()
    {
        return true;
    }

}