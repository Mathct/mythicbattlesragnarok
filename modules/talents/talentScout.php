<?php 

class talentScout extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Scout');
        $this->description = clienttranslate('The unit can deploy without cost during the table setup step, as if it were a troop unit.');
    }
}