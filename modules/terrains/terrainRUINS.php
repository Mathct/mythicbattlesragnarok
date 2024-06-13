<?php

class terrainRUINS extends terrain
{
    
    public function __construct()
    {
     $this->title = clienttranslate("Ruins");
     $this->description = clienttranslate("movement allowed – obstacle – 1 stele per area – +1 defense against range 0 attacks.");
    }

    public function isObstacle()
    {
        return true;
    }
    
    
    public function getStatBonus($stat, $attack)
    {        
        $ret = parent::getStatBonus($stat, $attack);
        if($stat == DEFENSE && $this->countAsFor($attack->to) == $this->type && $attack->range==0 && $attack->to->zone == $this)
        {
            $ret++;
        }
        return $ret;
    }

}