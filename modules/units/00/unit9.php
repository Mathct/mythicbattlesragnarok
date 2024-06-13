<?php 

class unit9 extends unit
{
    public $category = TROOP;
    public $cost = 1;
    public $activation = 3;
    public $talentsNames = array("Block","Guard");

    public function __construct()
    {
        $this->name = clienttranslate("Jomsvikings");
        $this->powers[0] = new power(0,PERMANENT, WHITE, "Jomsvikings", clienttranslate('When this unit is destroyed, before removing it from the board, carry out a range 0 attack against the attacking unit.'));
    }

    public $stats =  [
        [4, 5, 0, 1,1,0,0],
        [4, 5, 0, 1,1,0,0],
        [4, 5, 0, 1,1,0,0]
    ];

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == BEFOREDIE &&  $attack->to == $this && $attack->from != null && $attack->from->player_id != $attack->to->player_id)
        {
            $nattack = new attack();
            $nattack->from = $this;
            $nattack->to = $attack->from;
            $nattack->range = 0;
            $nattack->type = RETALIATE;
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A2A_ValueCalculation", $nattack->toJSON());
        }
    }
}