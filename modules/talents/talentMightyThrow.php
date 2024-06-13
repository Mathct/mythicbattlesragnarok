<?php 

class talentMightyThrow extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Mighty Throw');
        $this->description = clienttranslate('The unit can use their dice with blank results from the first assault to throw their target one area. 1 blank result is necessary to throw a troop or a hero, 2 for a monster or a god, and 3 for a titan.');    
    }

    
    public function onTiming($time, $attack)
    {
        if($time == AFTER1STROLL && $this->unit->canUse($this) && $attack->from == $this->unit)
        {
            $nbblank = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from die where value = 0");
            
            if($nbblank>=3
            || ($nbblank >= 2 && ($attack->to->category == MONSTER || $attack->to->category == GOD))
            || ($nbblank >= 1 && ($attack->to->category == TROOP  || $attack->to->category == HERO )))
            {
                mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "talentMightyThrow.mightyThrow", $attack->toJSON());
            }
        }
    }

    function argmightyThrow($parg1, $parg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Mighty Throw : ${unitid_display} can throw ${unitid_display2} one area');
        $ret['titleyou'] = clienttranslate('Mighty Throw : ${unitid_display} can throw ${unitid_display2} one area'); 
        $ret['unitid_display'] = $this->unit->id;
        $ret['unitid_display2'] = $attack->to->id;        
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");

        foreach($attack->to->zone->boundaries as $nextzoneid => $boundaryType)
        {
            $nextzone = mythicbattlesragnarok::$instance->zones[$nextzoneid];
            if($nextzone->canEnter($attack->to, true))
            {
                $ret['selectable']['zone'.$nextzoneid] = array();
            }
        }
        return $ret;
    }

    function mightyThrow($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {
            $attack = attack::fromJSON($parg1);
            $attack->to->dropAll();
            $attack->to->move("force", null, $varg1);
        }
    }
}