<?php 

class unit53 extends unit
{
    public $category = MONSTER;
    public $cost = 2;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("Block","Climb");

    public $stats =  [
        [7, 7, 0, 2,1,1,0],
        [6, 6, 0, 2,1,1,0],
        [6, 6, 0, 2,1,1,0],
        [5, 5, 0, 1,1,1,0],
        [5, 5, 0, 1,1,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Tanngnjóstr & Tanngrisnir");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Pay Your Way"), clienttranslate('Whenever an enemy unit tries to claim a divine stone in Tanngrisnir & Tanngnjóstr surroundings, the enemy unit must first discard 1 Art of War card.'));
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Horned And Stubborn"), clienttranslate('At the end of a run action, you can make a 3 dice, range 0 area attack, which benefits from the Mighty Throw talent.'));
    }

    
    function requiresAdditionalAOW($action)
    {
        if($this->canUse($this->powers[0]) && $action->function == "claim" && $action->unit->player_id != $this->player_id)
        {
            
            $token_id = str_replace("token","", $action->varg1);
            $token = mythicbattlesragnarok::getObjectFromDB( "SELECT * from token where id=".$token_id);
            $token_zoneid = str_replace("zone","",$token['location']);
            $zonerune = mythicbattlesragnarok::$instance->zones[$token_zoneid];

            if($zonerune->getDistanceWith($this->zone)<=1 && $this->canSeeZone($zonerune))
            {
                return 1;
            }
        }
        return 0;
    }

    function T2C_PickActionComplex($parg1, $parg2, $varg1, $varg2) {  
        if($varg1 == "butrun" && $this->canUse($this->powers[1]))
        {            
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Horned"); 
        }
        parent::T2C_PickActionComplex($parg1, $parg2, $varg1, $varg2);
    }

    function argHorned($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Horned And Stubborn : ${unitid_display} can make a 3 dice range 0 area attack');
        $ret['titleyou'] = clienttranslate('Horned And Stubborn : ${unitid_display} can make a 3 dice range 0 area attack'); 
        $ret['unitid_display'] = $this->id;       
        $ret['selectable']['butattack'] = array("title" => clienttranslate("Attack"));
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }

    function Horned($parg1, $parg2, $varg1, $varg2)
    {
        if($varg1 != "butskip")
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 0, 3); 
        }
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTER1STROLL && $this->canUse($this->powers[1]) && $attack->from == $this && $attack->type == AREA)
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