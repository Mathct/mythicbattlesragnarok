<?php 

class talentGemCollector extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Gem Collector');
        $this->description = clienttranslate('The unit can carry out a claim action from any visible area in their surroundings.');
    }
}