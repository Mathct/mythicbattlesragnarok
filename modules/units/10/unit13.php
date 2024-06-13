<?php 

class unit13 extends unit
{
    public $category = TROOP;
    public $cost = 1;
    public $activation = 3;
    public $talentsNames = array("Berserk","Mobility");

    public function __construct()
    {
        $this->name = clienttranslate("Jöfurr");
        $this->powers[0] = new power(0,PERMANENT, WHITE, "Jöfurr", clienttranslate('This unit\'s attacks cannot be redirected if it moved this turn before making its attack.'));
    }

    public $stats =  [
        [3, 5, 0, 2,1,0,0],
        [3, 5, 0, 2,1,0,0],
        [3, 5, 0, 2,1,0,0],
        [3, 5, 0, 2,1,0,0]
    ];

    
    public function getAuraStatus($to)
    {
        $ret = parent::getAuraStatus($to);
        if($to == $this && $this->canUse($this->powers[0]) && in_array("walked", $this->status))
        {
            $ret[] = "noredirect";
        }
        return $ret;
    }
}