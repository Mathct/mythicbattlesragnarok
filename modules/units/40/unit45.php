<?php 

class unit45 extends unit
{
    public $category = MONSTER;
    public $cost = 4;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("Berserk","ForceOfNature","Terror");    
    public $traitname = "Aquatic";

    public $stats =  [
        [8, 7, 0, 1,6,1,0],
        [8, 7, 0, 1,6,1,0],
        [7, 7, 0, 1,6,1,0],
        [7, 7, 0, 1,5,1,0],
        [7, 7, 0, 1,5,1,0],
        [7, 6, 0, 1,5,1,0],
        [6, 6, 0, 0,4,1,0],
        [6, 5, 0, 0,0,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Grendel");
         $this->powers[0] = new power(0,OFFENSIVE, BLACK, clienttranslate("Hail of Blows"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_black">X</div> &nbsp;  &nbsp; <div class="mbr_range"></div>0</div>Make an X dice, range 0 area attack. Grendel may perform a Mighty Throw against each enemy unit that was attacked, regardless of the number of blank results obtained.'),1);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Resilience"), clienttranslate('Grendel ignores the first wound caused by each attack.'));
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 0, $this->getStat(POWER1)); 
        }
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTER1STROLL && $attack->from == $this && $attack->type == AREA )
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "mightyThrow", $attack->toJSON());
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

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[1]) && $stat == DAMAGEBONUS && $attack->to == $this)
        {            
            $ret["Resilience"] = -1;
            $ret['total']--;            
        }
        return $ret;
    }
    
}