<?php

class terrainROCK extends terrain
{
    
    public function __construct()
    {
     $this->title = clienttranslate("Rock");
     $this->description = clienttranslate("Ascent movement allowed - obstacle - +1 in range. +1 offense for attacks of range 1+. A unit on a Rock ignores obstacles when determining if a target is visible, and targeted units ignore obstacles when determining line of sight to the attacking unit .");
    }

    public function isMovementAllowed($unit, $forcemove = false)
    {
        return ($unit->hasTalent("Climb") && !$forcemove) || ($unit->traitname == "Flying" && $unit->zone->id < 0);
    }

    public function isObstacle()
    {
        return true;
    }

    public function ignoreObstacle($from, $to) {
        return true;
    }
    
    
    public function getStatBonus($stat, $attack)
    {
        $ret = parent::getStatBonus($stat, $attack);
        if($stat == RANGE && $attack->from->zone == $this)
        {
            $ret++;
        }
        if($stat == OFFENSE && $attack->range >=1 && $attack->from->zone == $this)
        {
            $ret++;
        }
        return $ret;
    }

}