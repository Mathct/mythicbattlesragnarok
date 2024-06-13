<?php 

class unit43 extends unit
{
    public $category = MONSTER;
    public $cost = 2;
    public $activation = 3;
    public $aow = 0;
    public $talentsNames = array("ForceOfNature","Scout");
    public $traitname = "Boreal";

    public $stats =  [
        [6, 6, 0, 2,1,1,0],
        [6, 6, 0, 2,1,1,0],
        [5, 5, 0, 1,1,1,0],
        [5, 5, 0, 1,1,1,0],
        [4, 4, 0, 0,1,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Frost Jötunn");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Horde"), clienttranslate('During your turn, you may discard 1 Art of War card to redeploy this unit if it was previously destroyed. This deployment does not count as an activation. The unit returns with full vitality.'));
         $this->powers[1] = new power(1,ACTIVE, WHITE, clienttranslate("Icy Touch"), clienttranslate('When recruited, take the Frost Jötunn\'s token. Place the token on an enemy unit in the Frost Jötunn\'s area. When the enemy unit activates, it suffers the effect of Polar terrain then returns the token to its owner.'),1,1);
    }

    public function activatePower($index)
    {
        if($index == 1)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Icy"); 
        }
    }
    
    public function canUse($item)
    {
        $ret = parent::canUse($item);
        if($item == $this->powers[1])
        {
            $ret = $ret && mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type = 'Frost'")==0;
        }
        return $ret;
    }


    function getAnyTimeActions($player_id)
    {
        $ret = parent::getAnyTimeActions($player_id);
        if($this->isDead() && $player_id == $this->player_id && $this->player->getAowInHand()>0)
        {            
            $ret['butAny'.$this->id."Horde"] = array("title" => clienttranslate("Horde"));
        }
        return $ret;
    }

    
    function argHorde($parg1, $parg2) { 
        return $this->argdeploy($parg1, $parg2);
    }

    function Horde($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {            
            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type <= 0 limit 1");
            $this->player->discard($card_id); 
            $this->deploy($parg1, $parg2, $varg1, $varg2);
        }
    }

    
    
    function argIcy($parg1, $parg2) { 
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Icy Touch : ${actplayer} may choose a unit to suffer polar terrain effect at activation');
        $ret['titleyou'] = clienttranslate('Icy Touch : ${you} may choose a unit to suffer polar terrain effect at activation'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");       
        foreach($this->zone->units as $unit)
        {       
            if($unit->player_id != $this->player_id)
            {
                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Icy Touch : Do you want ${unitid_display} to suffer polar terrain effect at activation?'
                );
            }
        }
        return $ret;
    }

    function Icy($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {            
            $unit_id = str_replace("unit","", $varg1);
            self::DbQuery( "INSERT INTO token (type, location) VALUES ( 'Frost', 'dashboard".$unit_id."')");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") ,
                "unitId" => $unit_id
            ) );
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpletext", clienttranslate('${unitid_display} will suffer polar terrain effect at activation'), array(
                'unitid_display' => $unit_id
            ) );
        }
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == STARTACTIVATION )
        {
            $tokenid = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT id from token where type = 'Frost' and location = 'dashboard".$attack->from->id."'");
            if($tokenid != null)
            {
                $zonetmp = $attack->from->zone;
                $polar = new terrainPOLAR();
                $polar->type = POLAR;
                $polar->unit = $attack->from;
                $attack->from->zone = $polar;
                $polar->onTiming($time, $attack);
                $attack->from->zone = $zonetmp;
                self::DbQuery( "delete from token where id = ".$tokenid);
                mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                    "id" => "token". $tokenid
                ) );
            }
        }
    }
}