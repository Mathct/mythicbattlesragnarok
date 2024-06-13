<?php 

class unit23 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("Archery","Climb", "Mobility");  
    public $traitname = "Boreal";

    public $stats =  [
        [7, 8, 3, 3,1,1,0],
        [6, 7, 3, 2,1,1,0],
        [6, 7, 2, 2,1,1,0],
        [6, 7, 2, 2,1,1,0],
        [6, 7, 2, 1,1,1,0],
        [5, 7, 2, 1,1,1,0],
        [5, 6, 2, 1,1,1,0],
        [5, 6, 1, 0,1,0,0]
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Skadi");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Ondurdis"), clienttranslate('When Skadi carries out a range 1+ attack, her target suffers 1 extra wound (1 minimum).'));
         $this->powers[1] = new power(1,ACTIVE, WHITE, clienttranslate("Mistress of traps"), clienttranslate('When recruited, take Skadi\'s tokens. Place one token in any Open Ground area. When an enemy unit enters this area, it loses 1 vitality point and can no longer move for the rest of the turn. The token then returns to the player\'s reserve.'),1,2);
    }

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[0]) && $stat == DAMAGEBONUS && $attack->from == $this && $attack->range >= 1)
        {
            $ret["Ondurdis"] = 1;
            $ret['total']++;
        }
        return $ret;
    }

    public function activatePower($index)
    {
        if($index == 1)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Mistress"); 
        }
    }

    public function canUse($item)
    {
        $ret = parent::canUse($item);
        if($item == $this->powers[1])
        {
            $ret = $ret && mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type = 'Skadi'")<2;
        }
        return $ret;
    }

    function argMistress($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Mistress of traps : ${actplayer} may place one Skadi\'s token');
        $ret['titleyou'] = clienttranslate('Mistress of traps : ${you} may place one Skadi\'s token'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");

        foreach(mythicbattlesragnarok::$instance->zones as $zone)
        {            
            if($zone->countAsFor($this) == OPEN_GROUND)
            {
                $ret['selectable']['zone'.$zone->id] = array();
            }
        }
        return $ret;
    }
    
    function Mistress($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $zoneid = str_replace("zone","", $varg1);
            self::DbQuery( "INSERT INTO token (type, location) VALUES ( 'Skadi', 'zone".$zoneid."')");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "drop", '', array(
                "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") ,
                "unitId" => 'fake'
            ) );
        }
        $this->status[] = "power1";
        mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' power1' ) where id = ".$this->id);
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == ENTER && $attack->player_id != $this->player_id && self::getUniqueValueFromDB( "SELECT count(*) from token where type='Skadi' and location = 'zone".$attack->zone->id."'")>0)
        {                        
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "MistressTrigger", $attack->id);
        }
    }

    function MistressTrigger($parg1, $parg2, $varg1, $varg2) { 
        $unitid = $parg1;
        $unit =  mythicbattlesragnarok::$instance->units[$unitid];
        $token_id = self::getUniqueValueFromDB( "SELECT id from token where type='Skadi' and location = 'zone".$unit->zone->id."'");
        
        mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
            'id' => 'token'.$token_id
        ) );
        mythicbattlesragnarok::DbQuery( "delete from token where id = ".$token_id);
        
        $unit->status[] = "norun";
        $unit->status[] = "nowalk";
        mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' norun nowalk' ) where id = ".$unit->id);
        mythicbattlesragnarok::DbQuery( "delete from pending where function = 'move' and unit_id=".$unit->id." and player_id=".$unit->player_id);

        $unit->wound(1, $this); 

    }

}