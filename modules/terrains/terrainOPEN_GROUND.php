<?php

class terrainOPEN_GROUND extends terrain
{
    //Nothing to do
    
    public function __construct()
    {
     $this->title = clienttranslate("Open Ground");
     $this->description = clienttranslate("Movement allowed.");
    }
}