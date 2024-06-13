<?php

class attachment3 extends attachment
{
    public function __construct()
    {
        $this->title = clienttranslate("Hervor");
        $this->description = clienttranslate('This troop gains <div class="mbr_desc"><div class="mbr_offense"></div>+2</div> when retaliating');
     }

     
     public function getStatBonus($stat, $to, $attack)
     {
        if($stat == OFFENSE && $attack->from == $this->unit && $to == $this->unit && $attack->type == RETALIATE)
        {            
            return 2;           
        }
     }
}
