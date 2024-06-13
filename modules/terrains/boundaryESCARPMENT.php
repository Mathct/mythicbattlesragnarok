<?php

class boundaryESCARPMENT extends boundary
{
    public function isMovementAllowed($unit, $forcemove = false)
    {
        return $unit->hasTalent("Climb") && !$forcemove;
    }

}