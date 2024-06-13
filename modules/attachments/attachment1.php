<?php

class attachment1 extends attachment
{
    public function __construct()
    {
        $this->title = clienttranslate("Angrim");
        $this->description = clienttranslate("This troop ignores the first wound inflicted by an attacker");
     }

     public function getStatBonus($stat, $to, $attack)
     {
        if($stat == DAMAGEBONUS && $attack->to == $this->unit && $to == $this->unit)
        {            
            return -1;           
        }
     }
}
