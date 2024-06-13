<?php

class terrainBUILDING extends terrain
{
    public function __construct()
    {
     $this->title = clienttranslate("Building");
     $this->description = clienttranslate("Movement allowed – obstacle – gain +1 defense against range 1+ attacks.");
    }
    public function isObstacle()
    {
        return true;
    }

    public function getStatBonus($stat, $attack)
    {        
        $ret = parent::getStatBonus($stat, $attack);
        if($stat == DEFENSE && $this->countAsFor($attack->to) == $this->type && $attack->range>=1 && $attack->to->zone == $this)
        {
            $ret++;
        }
        return $ret;
    }

}