<?php 

class unit25 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("Block","Initiative", "MonsterSlayer");

    public $stats =  [
        [8, 8, 0, 2,1,1,0],
        [8, 8, 0, 2,1,1,0],
        [8, 8, 0, 2,1,1,0],
        [7, 7, 0, 2,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [6, 6, 0, 0,1,1,0],
        [6, 6, 0, 0,0,0,0]
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Vidar");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Bound Boot"), clienttranslate('Vidar is not affected by the Mighty Throw talent and cannot be moved by an attack or offensive power. Vidar can discard 1 Art of War card when he sustains a range 0 attack to inflict 1 wound on his attacker.'));
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("The Avenger"), clienttranslate('Vidar does not need to discard an activation card during a retaliation if he does not use the Initiative talent.'));
    }

    public function getAuraStatus($to)
    {
        $ret = parent::getAuraStatus($to);
        if($to == $this && $this->canUse($this->powers[1]))
        {
            $ret[] = "noforce";
        }
        return $ret;
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTERATTACKWOUND && $this->canUse($this->powers[1]) && $attack->to == $this && $attack->range==0 && $this->player->getAowInHand()>0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "BoundBoot", $attack->from->id);
        } 
    }

    function argA3_Retaliate($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($parg2 != "initiative")
        {
            $parg2 = "nodiscard";
        }
        return parent::argA3_Retaliate($parg1, $parg2);
    }

    function A3_Retaliate($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($parg2 != "initiative")
        {
            $parg2 = "nodiscard";
        }
        parent::A3_Retaliate($parg1, $parg2, $varg1, $varg2);
    }

    function argBoundBoot($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Bound Boot : ${actplayer} may discard 1 Art of War to inflict 1 wound to ${unitid_display}');
        $ret['titleyou'] = clienttranslate('Bound Boot : ${you} may discard 1 Art of War to inflict 1 wound to ${unitid_display}'); 
        $ret['unitid_display'] = $parg1;
        if($this->player->getAowInHand()>0)
        {
            $ret['selectable']['butdiscard'] = array("title" => clienttranslate("Discard"));
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }

    function BoundBoot($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $unit = mythicbattlesragnarok::$instance->units[$parg1];
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "DiscardAOW");     
            $unit->wound(1,$this);
        }
    }
}