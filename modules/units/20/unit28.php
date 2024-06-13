<?php 

class unit28 extends unit
{
    public $category = HERO;
    public $cost = 4;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("Mobility","Torment");

    public $stats =  [
        [8, 8, 0, 2,1,1,0],
        [8, 7, 0, 2,1,1,0],
        [7, 7, 0, 2,1,1,0],
        [7, 6, 0, 2,1,1,0],
        [6, 6, 0, 2,1,1,0],
        [6, 5, 0, 1,1,1,0],
        [7, 5, 0, 1,1,1,0],
        [7, 7, 0, 0,0,1,0]
        
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Brunhild");
         $this->powers[0] = new power(0,OFFENSIVE, BLACK, clienttranslate("Grudge"), clienttranslate('When she targets a god, Brunhild gains +2 offense for her attack.'),1);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Bring Back The Brave"), clienttranslate('During the end of turn phase, in addition to being able to perform a troop recall, you may deploy one of your destroyed troop units to your deployment area(s).'));
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            $this->T2C_PickActionSimple(NULL,NULL,'butattack', NULL);
        }
    }

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if( $stat == OFFENSE && in_array("power0", $this->status) && $attack->from == $this && $attack->to->category == GOD )
        {
            $ret['Grudge'] = 2;
            $ret['total']+=2;
        }
        return $ret;
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == ENDOFTURN && $this->canUse($this->powers[1]) && $attack == $this->player_id)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "BringBack");
        }
    } 
    
    function argBringBack($parg1, $parg2)   
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Bring Back The Brave : ${actplayer} may deploy a destroyed troop');
        $ret['titleyou'] = clienttranslate('Bring Back The Brave : ${you} may deploy a destroyed troop'); 

        foreach(mythicbattlesragnarok::$instance->units as $unit)
        {
            if($unit->player_id == $this->player_id && $unit->zone_id < 0 && $unit->category == TROOP)
            {
                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Do you want to deploy ${unitid_display} ?'
                );
            }
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }

    function BringBack($parg1, $parg2, $varg1, $varg2)   
    {
        if($varg1 != "butskip")
        {            
            $unitid = str_replace("unit","", $varg1);
            mythicbattlesragnarok::$instance->addPending($this->player_id,$unitid, "deploy");
        }
    }

}