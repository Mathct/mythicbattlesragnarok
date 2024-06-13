<?php 

class unit8 extends unit
{
    public $category = TROOP;
    public $cost = 1;
    public $activation = 3;
    public $talentsNames = array("Guard","Initiative");

    public function __construct()
    {
        $this->name = clienttranslate("Shield-Maidens");
        $this->powers[0] = new power(0,PERMANENT, WHITE, "Shield-Maidens", clienttranslate('When retaliating, this unit gains the Mighty Throw talent.'));
    }

    public $stats =  [
        [4, 5, 0, 1,1,0,0],
        [4, 5, 0, 1,1,0,0],
        [4, 5, 0, 1,1,0,0],
        [4, 5, 0, 1,1,0,0]
    ];

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTER1STROLL && $this->canUse($this->powers[0]) && $attack->from == $this && $attack->type == RETALIATE)
        {
            $nbblank = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from die where value = 0");
            
            if($nbblank>=3
            || ($nbblank == 2 && ($attack->to->category == MONSTER || $attack->to->category == HERO))
            || ($nbblank == 1 && $attack->to->category == TROOP ))
            {
                mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "mightyThrow", $attack->toJSON());
            }
        }
    }

    function argmightyThrow($parg1, $parg2)
    {
        $talent = new talentMightyThrow();
        $talent->unit = $this;
        return $talent->argmightyThrow($parg1, $parg2);
    }

    function mightyThrow($parg1, $parg2, $varg1, $varg2) {  
        $talent = new talentMightyThrow();
        $talent->unit = $this;
        $talent->mightyThrow($parg1, $parg2, $varg1, $varg2);
    }

}