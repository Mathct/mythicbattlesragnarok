<?php 

class talentTerrestrial extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Terrestrial');
        $this->description = clienttranslate('None.');
        $this->trait = true;
    }
}