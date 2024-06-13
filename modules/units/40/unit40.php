<?php 

class unit40 extends unit
{
    public $category = MONSTER;
    public $cost = 3;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("SneakAttack","Terror","Torment");

    public $stats =  [
        [7, 7, 0, 1,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [5, 6, 0, 1,1,1,0],
        [5, 6, 0, 1,1,1,0],
        [4, 5, 0, 1,1,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Draugr");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Sepulcral Willpower"), clienttranslate('The Draugr can never suffer more than 2 wounds per attack.'));
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Spectral Touch"), clienttranslate('When the Draugr is in an area containing one or more divine stones, no enemy unit can claim or absorb them, even using talents or powers.'));
    }

    public function wound($wounds, $cause, $arrayDetails = NULL)
    {
        if($this->canUse($this->powers[0]))
        {
            $wounds = min($wounds,2);
        }
        parent::wound($wounds, $cause, $arrayDetails);
    }

    public function blockClaimGemInArea($to)
    {
        return parent::blockClaimGemInArea($to) || $this->canUse($this->powers[1]);
    }

    
    public function getAuraStatus($to)
    {
        $ret = array();
        if($to->zone == $this->zone && $to->player_id != $this->player_id)
        {
            $ret = array("noabsorb");
        }
        return $ret;
    }

}