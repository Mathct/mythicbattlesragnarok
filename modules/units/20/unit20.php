<?php 

class unit20 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("GemCollector","Mobility", "SneakAttack");

    public $stats =  [
        [6, 9, 0, 2,1,1,0],
        [6, 8, 0, 2,1,1,0],
        [5, 8, 0, 2,1,1,0],
        [5, 7, 0, 2,1,1,0],
        [5, 7, 0, 1,1,1,0],
        [4, 7, 0, 1,1,1,0],
        [4, 6, 0, 0,1,1,0]
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Loki");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Malice"), clienttranslate('When Loki is the target of a range 0 attack, he can redirect the attack towards another unit in Loki\'s area, other than the attacking unit.'));
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Metamorphosis"), clienttranslate('One use per game. When recruited, take Loki\'s token and place it on his base. When Loki is the target of an attack, you can discard the token to exchange Loki\'s place with another allied unit in his surroundings. The attack continues on the unit replacing him.'),0,1);
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == STARTGAME)
        {
            self::DbQuery( "INSERT INTO token (type, location) VALUES ( 'Loki', 'dashboard".$this->id."')");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") 
            ) );
        }
        if($time == AFTERSELECTINGTARGET && $this->canUse($this->powers[1]) && $attack->to == $this && $attack->range == 0 && self::getUniqueValueFromDB( "SELECT count(*) from token where type='Loki' and location = 'dashboard".$this->id."'")>0 )
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Metamorphosis", $attack->toJSON());           
        } 
        if($time == AFTERSELECTINGTARGET && $this->canUse($this->powers[0]) && $attack->to == $this && $attack->range == 0 && !in_array("noredirect", $attack->from->status))
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Malice", $attack->toJSON());           
        }        
    } 

    
    function argMalice($parg1, $parg2, $varg1, $varg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Malice : ${unitid_display} can redirect attack on someone else');
        $ret['titleyou'] = clienttranslate('Malice : ${unitid_display} can redirect attack on someone else');  
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $this->id;

        foreach($this->zone->units as $unit)
        {
            if($unit != $this && $unit != $attack->from)
            {
                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Do you want to redirect the attack on ${unitid_display}?'
                );
            }
        }
        return $ret;
    }

    function Malice($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {
            $unitid = str_replace("unit","", $varg1);
            $unit =  mythicbattlesragnarok::$instance->units[$unitid];
            $attack = attack::fromJSON($parg1);
            $attack->to = $unit;
            $json = $attack->toJSON();
            $sql = "update pending set player_id = '".$unit->player->getOtherPlayer()->player_id."', arg = '".$json."' where arg='".$parg1."' and function <> 'Metamorphosis'";
            mythicbattlesragnarok::DbQuery( $sql);

            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} redirects attack on ${unitid_display2}'), array(
                'unitid_display' => $this->id,
                'unitid_display2' => $unit->id
            ) );
        }
    }

    function argMetamorphosis($parg1, $parg2, $varg1, $varg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Metamorphosis : ${unitid_display} can exchange its place');
        $ret['titleyou'] = clienttranslate('Metamorphosis : ${unitid_display} can exchange its place');  
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $this->id;

        $units = $this->zone->getUnitsWithin(0,1,$this->player_id,false, true);
        foreach($units as $unit)
        {            
            if($unit != $this && $this->player_id == $unit->player_id && $unit != $attack->from)
            {
                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Do you want ${unitid_display2} to exchange place with ${unitid_display}?',
                    'unitid_display2' => $this->id
                );
            }
        }
        return $ret;
    }

    function Metamorphosis($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip" && $varg1 != null)
        {
            $unitid = str_replace("unit","", $varg1);
            $unit =  mythicbattlesragnarok::$instance->units[$unitid];            
            $attack = attack::fromJSON($parg1);
            $attack->to = $unit;
            $json = $attack->toJSON();
            $sql = "update pending set arg = '".$json."' where arg='".$parg1."'";
            mythicbattlesragnarok::DbQuery( $sql);

            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} changes place with ${unitid_display2}'), array(
                'unitid_display' => $this->id,
                'unitid_display2' => $unit->id
            ) );

            $zoneidtmp = $this->zone->id;
            mythicbattlesragnarok::DbQuery("update unit set zone_id = ".$unit->zone->id." where id = ".$this->id);
            unset($this->zone->units[$this->id]);  
            $this->zone =  mythicbattlesragnarok::$instance->zones[$unit->zone->id];
            $this->zone->units[$this->id] = $this;  
            mythicbattlesragnarok::$instance->notifyAllPlayers( "move", '', array(
                'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$this->id),
                "category" => $this->category
            ) ); 

            
            mythicbattlesragnarok::DbQuery("update unit set zone_id = ".$zoneidtmp." where id = ".$unit->id);
            unset($unit->zone->units[$unit->id]);  
            $unit->zone =  mythicbattlesragnarok::$instance->zones[$zoneidtmp];
            $unit->zone->units[$unit->id] = $unit;  
            mythicbattlesragnarok::$instance->notifyAllPlayers( "move", '', array(
                'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$unit->id),
                "category" => $unit->category
            ) ); 

            $token_id = self::getUniqueValueFromDB( "SELECT id from token where type='Loki'");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                'id' => 'token'.$token_id
            ) );
            mythicbattlesragnarok::DbQuery( "delete from token where id = ".$token_id);
        }
    }

}