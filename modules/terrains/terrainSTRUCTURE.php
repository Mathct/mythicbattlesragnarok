<?php

class terrainSTRUCTURE extends terrain
{
    
    public function __construct()
    {
     $this->title = clienttranslate("Structure");
     $this->description = clienttranslate("Movement allowed - obstacle - a unit in this area ignores obstacles for its attacks of range 1+ to another Structure area.");
    }

    public function isObstacle()
    {
        return true;
    }
    
    public function ignoreObstacle($from, $to) {
        return parent::ignoreObstacle($from, $to) || ($from instanceof terrainSTRUCTURE)&&($to instanceof terrainSTRUCTURE);
    }

    

}