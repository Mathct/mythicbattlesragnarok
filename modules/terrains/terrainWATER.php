<?php

class terrainWATER extends terrain
{   
    
    public function __construct()
    {
     $this->title = clienttranslate("Water (Aquatic)");
     $this->description = clienttranslate("Movement allowed – talents, offensive and active powers are ignored.");
    }

    public function canUse($unit, $item)
    {
        $ret = $this->id != 0;
        if($item instanceof talent)
        {
            $ret = $item->trait || in_array("ignoreWater", $unit->status);
        }
        if($item instanceof power)
        {  
            $ret = $ret && (($item->type != OFFENSIVE && $item->type != ACTIVE) || in_array("ignoreWater", $unit->status));            
        }
        return $ret;
    }
}