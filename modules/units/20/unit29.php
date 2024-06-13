<?php 

class unit29 extends unit
{
    public $category = HERO;
    public $cost = 3;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("Berserk","Mobility", "Scout");

    public $stats =  [
        [6, 7, 0, 1,1,1,0],
        [6, 7, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [5, 6, 0, 1,1,1,0],
        [5, 5, 0, 0,1,0,0]
        
        
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Egill");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Nidstang"), clienttranslate('No enemy re-rolls can be carried out in Egill\'s area. He is also immune to area attacks.'));
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Recounting The saga"), clienttranslate('At the end of Egill\'s activation, you can draw a card.'));
    }

    public function getAuraStatus($to)
    {
        $ret = parent::getAuraStatus($to);
        if($to->player_id != $this->player_id && $to->zone == $this->zone && $this->canUse($this->powers[0]))
        {
            $ret[] = "noreroll";
        }
        return $ret;
    }

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[0]) && $stat == DAMAGEBONUS && $attack->to == $this && $attack->type == AREA )
        {
            $ret[$this->powers[0]->title] = -99;
            $ret['total'] = -99;
        }
        return $ret;
    }
    
    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == ENDACTIVATION && $this->canUse($this->powers[1]) && $attack->from == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw");
        }
    }

}