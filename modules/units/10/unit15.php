<?php 

class unit15 extends unit
{
    public $category = TROOP;
    public $cost = 1;
    public $activation = 3;
    public $talentsNames = array("Terror");
    public $traitname = "Flying";

    public function __construct()
    {
        $this->name = clienttranslate("Light Elves");
        $this->powers[0] = new power(0,PERMANENT, WHITE, "Light Elves", clienttranslate('If complete, this unit may carry out an area attack instead of a normal attack.'));
    }

    public $stats =  [
        [3, 5, 1, 2,1,0,0],
        [3, 5, 1, 2,1,0,0],
        [3, 5, 1, 2,1,0,0],
        [3, 5, 1, 2,1,0,0]
    ];

       function argA1A_targetChoice($parg1 = NULL, $parg2 = NULL)
       {
           $ret = parent::argA1A_targetChoice($parg1 = NULL, $parg2 = NULL);
           if($this->canUse($this->powers[0]) && $this->hp == count($this->stats))
           {
                $attack = new attack();
                $attack->from = $this;
                $attack->type = AREA;
                foreach($this->zone->getZonesAtDistance(0,$this->getEffectiveStat(RANGE, $attack)['total']) as $zone)
                {
                    if($this->canSeeZone($zone))
                    {
                        $ret['selectable']['zone'.$zone->id] = array();
                    }
                }
           }
           return $ret;
       }
   
       function A1A_targetChoice($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
       {
            if (str_starts_with($varg1, "zone"))
            {
                $zoneid = str_replace("zone","", $varg1);
                $this->zoneattack($parg1, $parg2, $varg1, $varg2);      
            }
            else
            {
                parent::A1A_targetChoice($parg1, $parg2, $varg1, $varg2);
            }
       }

   
}