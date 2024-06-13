<?php 

class unit7 extends unit
{
    public $category = TROOP;
    public $cost = 1;
    public $activation = 3;
    public $talentsNames = array("Mobility");

    public function __construct()
    {
        $this->name = clienttranslate("Seers");
        $this->powers[0] = new power(0,PERMANENT, WHITE, "Seers", clienttranslate('When this unit is wounded or destroyed by an attack, you may draw a card from your deck.'));
    }

    public $stats =  [
        [3, 4, 2, 1,1,0,0],
        [3, 4, 2, 1,1,0,0],
        [3, 4, 2, 1,1,0,0],
        [3, 4, 2, 1,1,0,0],
        [3, 4, 2, 1,1,0,0]
    ];

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTERATTACKWOUND && $this->canUse($this->powers[0]) && $attack->to == $this && $attack->wounds>0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw"); 
        } 
        if($time == AFTERDIE && $attack->to == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw"); 
        }
    }

}