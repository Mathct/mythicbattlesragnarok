<?php 

class unit32 extends unit
{
    public $category = HERO;
    public $cost = 4;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("Berserk","Bolster","Leader");

    public $stats =  [
        [6, 7, 1, 1,1,1,0],
        [6, 7, 1, 1,1,1,0],
        [6, 6, 1, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [5, 6, 0, 0,1,0,0],
        [5, 6, 0, 0,0,0,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Hrolf Kraki");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Skofnung"), clienttranslate('Hrolf gains +1 offense and defense for each enemy unit present in his area. Any enemy unit wishing to leave the area with a walk, run, or ascend action must first discard 1 Art of War card.'));
         $this->powers[1] = new power(1,ACTIVE, WHITE, clienttranslate("Sowing Your Treasure"), clienttranslate('When recruited, take Hrolf\'s token. Place it in an area in his surroundings. An enemy unit beginning its activation in the surroundings of the token must move and stop in this area (if it is not already saturated) or discard an Art of War card to avoid this effect. An opponent can remove this token from the game at any time by discarding 2 Art of War cards.'),1,1);
    }

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[0]) && (($stat == OFFENSE && $attack->from == $this) ||($stat == DEFENSE && $attack->to == $this)))
        {
            $nb = 0;
            foreach($this->zone->units as $unit)
            {
                if($unit->player_id != $this->player_id)
                {
                    $nb++;
                }
            }
            if($nb>0)
            {
                $ret["Skofnung"] = $nb;
                $ret['total']+=$nb;
            }
        }
        return $ret;
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        $token = mythicbattlesragnarok::getObjectFromDB( "SELECT * from token where type = 'Hrolf'");
        if($token && $time == STARTACTIVATION && $attack->from->player_id != $this->player_id)
        {
            $token_zoneid = str_replace("zone","",$token['location']);
            $zoneToken = mythicbattlesragnarok::$instance->zones[$token_zoneid];
            if($attack->from->canSeeZone($zoneToken) && $zoneToken->getDistanceWith($attack->from->zone)<=1)
            {
                mythicbattlesragnarok::$instance->addPending($this->player->getOtherPlayer()->player_id,$this->id, "SowingOpp", $attack->from->id);
            }
        }
    }

    function argSowingOpp($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Sowing Your Treasure : ${actplayer} must move in Hrolf Kraki token area or discard an Art of War card');
        $ret['titleyou'] = clienttranslate('Sowing Your Treasure : ${you} must move in Hrolf Kraki token area or discard an Art of War card'); 
        $ret['selectable']['butmove'] = array("title" => clienttranslate("Move"));

        if($this->player->getAowInHand()>0)
        {
            $ret['selectable']['butdiscard'] = array("title" => clienttranslate("Discard"));
        }
        
        return $ret;
    }
    
    function SowingOpp($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butdiscard")
        {
            $token = mythicbattlesragnarok::getObjectFromDB( "SELECT * from token where type = 'Hrolf'");
            $token_zoneid = str_replace("zone","",$token['location']);
            $unitid = str_replace("unit","", $parg1);
            $unit = mythicbattlesragnarok::$instance->units[$unitid];
            $unit->move("force",NULL, $token_zoneid);
        }
        else{
            mythicbattlesragnarok::$instance->addPending($this->player->getOtherPlayer()->id,0, "DiscardAOW");     
        }
    }

    
    function requiresAdditionalAOW($action)
    {
        $zoneid = str_replace("zone","", $action->varg1);
        $ret = parent::requiresAdditionalAOW($action);
        if($this->canUse($this->powers[0]) && $action->function == "move" && $action->unit->player_id != $this->player_id && $action->unit->zone == $this->zone)
        {
            $ret++;
        }
        return $ret;
    }

    public function canUse($item)
    {
        $ret = parent::canUse($item);
        if($item == $this->powers[1])
        {
            $ret = $ret && mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type = 'Hrolf'")<1;
        }
        return $ret;
    }

    public function activatePower($index)
    {
        if($index == 1)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Sowing"); 
        }
    }

    function argSowing($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Sowing Your Treasure : ${actplayer} may place one token');
        $ret['titleyou'] = clienttranslate('Sowing Your Treasure : ${you} may place one token'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $zones = $this->zone->getZonesAtDistance(0,1);
        foreach($zones as $zone)
        {
            if($this->canSeeZone($zone))
            {
                $ret['selectable']['zone'.$zone->id] = array(); 
            }
        }
        return $ret;
    }
    
    function Sowing($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $zoneid = str_replace("zone","", $varg1);
            self::DbQuery( "INSERT INTO token (type, location) VALUES ( 'Hrolf', 'zone".$zoneid."')");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "drop", '', array(
                "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") ,
                "unitId" => 'fake'
            ) );
            $this->status[] = 'power1';
            mythicbattlesragnarok::DbQuery("update unit set statusGame = concat(statusGame, ' power1' ) where id = ".$this->id);
        }
    }

    function getAnyTimeActions($player_id)
    {
        $ret = parent::getAnyTimeActions($player_id);
        $player = mythicbattlesragnarok::$instance->playersMBR[$player_id];
        if($this->player_id != $player_id && mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type = 'Hrolf'")>0 && $player->getAowInHand()>=2)
        {            
            $ret['butAny'.$this->id."Remove"] = array("title" => clienttranslate("Remove Hrolf Kraki token"));
        }
        return $ret;
    }

    function Remove($parg1, $parg2, $varg1, $varg2)
    {
        //Opponent action
        $player = $this->player;
        mythicbattlesragnarok::$instance->addPending($player->player_id,0, "DiscardAOW"); 
        mythicbattlesragnarok::$instance->addPending($player->player_id,0, "DiscardAOW"); 

        $token_id = self::getUniqueValueFromDB( "SELECT id from token where type='Hrolf'");
        mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
            'id' => 'token'.$token_id
        ) );
        mythicbattlesragnarok::DbQuery( "delete from token where id = ".$token_id);
        mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} removes Hrolf Kraki token'), array(
            'player_name' => $this->player->player_name,
            'player_id' => $this->player_id
            ) );
    }

}