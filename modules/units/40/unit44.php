<?php 

class unit44 extends unit
{
    public $category = MONSTER;
    public $cost = 3;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("Block","Guard","Torment");

    public $stats =  [
        [7, 7, 0, 2,1,1,0],
        [7, 7, 0, 2,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [5, 6, 0, 1,1,1,0],
        [5, 6, 0, 0,0,0,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Garm");
         $this->powers[0] = new power(0,OFFENSIVE, BLACK, clienttranslate("Howl"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_black">5</div> &nbsp;  &nbsp; <div class="mbr_range"></div>1</div>Make a 5 dice, range 1 area attack.'),1);
         $this->powers[1] = new power(1,OFFENSIVE, WHITE, clienttranslate("Frenzy"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_black">5</div> &nbsp;  &nbsp; <div class="mbr_range"></div>0</div>Make a 5 dice, range 0 area attack, then Garm loses 1 vitality point.'));
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 1, 5); 
        }
        if($index == 1)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "pendWound", 1, "Frenzy"); 
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 0, 5); 
        }
    }

}