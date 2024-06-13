<?php 

class unit12 extends unit
{
    public $category = TROOP;
    public $cost = 1;
    public $activation = 3;
    public $talentsNames = array("GemCollector");

    public function __construct()
    {
        $this->name = clienttranslate("Dwarves");
        $this->powers[0] = new power(0,PERMANENT, WHITE, "Dwarves", clienttranslate('This unit can ignore terrain effects.'));
    }

    public $stats =  [
        [3, 6, 0, 1,1,0,0],
        [3, 6, 0, 1,1,0,0],
        [3, 6, 0, 1,1,0,0]
    ];

    public function getAuraStatus($to)
    {
        $ret = parent::getAuraStatus($to);
        if($to == $this)
        {
            $ret[] = "ignoreBurning";
            $ret[] = "ignoreWater";
            $ret[] = "ignorePolar";
        }
        return $ret;
    }
}