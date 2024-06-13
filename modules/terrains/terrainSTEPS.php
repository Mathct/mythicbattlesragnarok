<?php

class terrainSTEPS extends terrain
{
    
    public function __construct()
    {
     $this->title = clienttranslate("Steps");
     $this->description = clienttranslate("Movement allowed – gain +1 range. Gain +1 offense for range 1+ attacks. Obstacles ignored for line of sight.");
    }

    public function ignoreObstacle($from, $to) {
        return $from == $this;
    }
    
    public function getStatBonus($stat, $attack)
    {        
        $ret = parent::getStatBonus($stat, $attack);
        if($stat == OFFENSE && $this->countAsFor($attack->from) == $this->type && $range >=1 && $attack->from->zone == $this)
        {
            $ret++;
        }
        return $ret;
    }    

}