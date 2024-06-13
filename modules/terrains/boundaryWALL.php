<?php

class boundaryWALL extends boundary
{
    public function isMovementAllowed($unit, $forcemove = false)
    {
        return false;
    }

    public function isObstacle()
    {
        return true;
    }
}