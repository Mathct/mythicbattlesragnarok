<?php 

class unit42 extends unit
{
    public $category = MONSTER;
    public $cost = 3;
    public $activation = 3;
    public $aow = 0;
    public $talentsNames = array("Climb","GemCollector","Scout");  
    public $traitname = "Flying";

    public $stats =  [
        [5, 5, 0, 2,1,1,0],
        [5, 5, 0, 2,1,1,0],
        [4, 5, 0, 2,1,1,0],
        [4, 4, 0, 1,1,1,0],
        [3, 4, 0, 1,1,1,0],
        [3, 3, 0, 1,1,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Freyja's Cats");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Fast as Lightning"), clienttranslate('The target of a Freyja\'s Cats attack cannot retaliate.'));
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Camouflage"), clienttranslate('Freyja\'s Cats cannot be the target of an attack or offensive power used at range 2+.'));
    }

    public function canBeTargeted($attack = NULL)
    {
        if($attack != null)
        {
            if($attack->type == RETALIATE)
            {
                return false;
            }
            if($attack->type == ATNORMAL && $attack->range>=2)
            {
                return false;
            }
        }
        return parent::canBeTargeted($attack);
    }

}