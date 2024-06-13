<?php

class terrainFOREST extends terrain
{
    
    public function __construct()
    {
     $this->title = clienttranslate("Forest");
     $this->description = clienttranslate("Movement allowed – obstacle – number of trees = area capacity divided by 2, rounded up – +1 defense against range 1+ attacks.");
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