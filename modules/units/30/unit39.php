<?php 

class unit39 extends unit
{
    public $category = MONSTER;
    public $cost = 5;
    public $activation = 5;
    public $aow = 1;
    public $talentsNames = array("Block","MightyThrow","Terror");

    public $stats =  [
        [8, 9, 0, 2,1,1,0],
        [8, 9, 0, 2,1,1,0],
        [7, 8, 0, 1,1,1,0],
        [7, 8, 0, 1,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [6, 7, 0, 1,0,1,0],
        [6, 6, 0, 0,0,1,0] 
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Angrboda");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Bad Omens"), clienttranslate('Any enemy unit wishing to attack or target Angrboda with an offensive power must first discard 1 Art of War card.'));
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Gruesome Curse"), clienttranslate('When an allied monster is destroyed (including Angrboda), its attacker suffers an attack equal to as many dice as the monster\'s recruitment cost +2. This attack cannot be redirected.'));
    }

    function requiresAdditionalAOW($action)
    {
        $ret = parent::requiresAdditionalAOW($action);
        
        if($this->canUse($this->powers[0]) && $action->function == "A1A_targetChoice")
        {
            $unitid = str_replace("unit","", $action->varg1);
            $unit = mythicbattlesragnarok::$instance->units[$unitid];
            if($unit == $this)
            {
                $ret++;
            }
        }
        return $ret;
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == BEFOREDIE && $attack->to->player_id == $this->player_id && $attack->to->category == MONSTER && $attack->from != null)
        {
            $nattack = new attack();
            $nattack->range = 0;
            $nattack->from = $this;
            $nattack->to = $attack->from;
            $nattack->type = RETALIATE; 
            $nattack->offense = $attack->to->cost + 2;
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A2A_ValueCalculation", $nattack->toJSON(),$nattack->offense, "nodistance");
        }
    }

}