<?php 

class talentFlying extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Flying');
        $this->description = clienttranslate('Flying units can deploy on a Cliff or Rock area of their deployment zone. A flying unit is not constantly in the air and moves on the ground. However, they can take flight to land elsewhere or swoop down on an enemy.');
        $this->trait = true;
    }

    public function getStatBonus($stat, $to, $attack)
    {
        if($stat == RANGE && $attack->from == $this->unit && $attack->aerial)
        {
            return 1;
        }
        return 0;
    }


    public function getExtraActions($simple)
    {
        $ret = parent::getExtraActions($simple);
        if(!$simple)
        {
            $ret[] = array("title"=>clienttranslate("Fast Flight"), "function" => "talentFlying_FastFlightSetup");
            $ret[] = array("title"=>clienttranslate("Aerial Attack"), "function" => "talentFlying_AerialAttack");
        }
        return $ret;
    }

    function FastFlightSetup($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $move =  $this->unit->getEffectiveStat(MOVEMENT);
        $move['Flying'] = 1;
        $move['total']++;
        mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} flights fast up to ${movement}'), array(
            'unitid_display' => $this->unit->id,
            'movement' => $move
        ) );
        for ($i = 0; $i < $move['total']; $i++) {
            mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "talentFlying.FastFlight");
        }
    }

    function argFastFlight($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} may flight with ${unitid_display} (${left} left)');
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['titleyou'] = clienttranslate('${you} may flight with ${unitid_display} (${left} left)');
        
        $ret['unitid_display'] = $this->unit->id;
        $ret['left'] = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'talentFlying.FastFlight'");
        foreach($this->unit->zone->boundaries as $nextzoneid => $boundaryType)
        {
            $nextzone = mythicbattlesragnarok::$instance->zones[$nextzoneid];
            if(!$nextzone->isFull())
            {
                $ok = true;
                foreach($nextzone->units as $unit)
                {
                    $ok = $ok && !($unit instanceof unit21);
                }
                if($ok)
                {
                    $ret['selectable']['zone'.$nextzoneid] = array();
                }
            }
        }
        return $ret;
    }

    function FastFlight($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != "butskip")
        {
            $zoneid = str_replace("zone","", $varg1);            
            mythicbattlesragnarok::DbQuery("update unit set zone_id = ".$zoneid." where id = ".$this->unit->id);
            unset($this->unit->zone->units[$this->unit->id]);  
            $this->unit->zone =  mythicbattlesragnarok::$instance->zones[$zoneid];
            $this->unit->zone->units[$this->unit->id] = $this->unit;  
            
            mythicbattlesragnarok::$instance->notifyAllPlayers( "move", '', array(
                'unit' => self::getObjectFromDB( "SELECT * FROM unit where id = ".$this->unit->id),
                "category" => $this->unit->category
            ) );    
            mythicbattlesragnarok::$instance->onTiming(ENTER, $this->unit);
            mythicbattlesragnarok::$instance->onTiming(FASTFLIGHT, $this->unit);
        }
        else{
            mythicbattlesragnarok::DbQuery( "delete from pending where function = 'talentFlying.FastFlight' and unit_id=".$this->unit->id." and player_id=".$this->unit->player_id);
        }
    }

    function argAerialAttack($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} may carry out an aerial attack with ${unitid_display}');
        $ret['titleyou'] = clienttranslate('${you} may carry out an aerial attack with ${unitid_display}'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $this->unit->id;

        $attack = new attack();
        $attack->from = $this->unit;
        $attack->type = ATNORMAL;
        $attack->aerial = true;

        $units = $this->unit->zone->getUnitsWithin(0,$this->unit->getEffectiveStat(RANGE, $attack)['total'],$this->unit->player_id,true, false);
        foreach($this->unit->zone->getZonesAtDistance(1,1) as $adjacent)
        {
            if($adjacent->isFull())
            {
                $units += $adjacent->units;
            }
        }

        foreach($units as $unit)
        {
            if($unit != $this->unit && $unit->player_id != $this->unit->player_id)
            {                
                $range = $this->unit->zone->getDistanceWith($unit->zone);               
                $attack->range = $range;
                $attack->to = $unit;
                $attack->aerial = true;

                if($unit->canBeTargeted($attack))
                {
                    $ret['selectable']['unit'.$unit->id] = array(
                        "confirm" => 'Do you want to attack ${unitid_display} ( ${offense} vs ${defense} ) ?',
                        "offense" => $this->unit->getEffectiveStat(OFFENSE, $attack),
                        "defense" => $unit->getEffectiveStat(DEFENSE, $attack),
                    );
                }
            }
        }

        return $ret;
    }

    function AerialAttack($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != "butskip")
        {            
            $unitid = str_replace("unit","", $varg1);
            $unit = mythicbattlesragnarok::$instance->units[$unitid];
            $this->unit->status[] = "attacked";
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' attacked' ) where id = ".$this->unit->id);
            mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "A1B_rangeChoice", $unitid, true);
        }
    }
    
    public function onTiming($time, $attack)
    {
        if($time == AFTERSELECTINGTARGET && $this->unit->canUse($this) && $attack->to == $this->unit && $this->unit->category != TROOP && $attack->range == 0 && !in_array("noredirect", $attack->from->status) && !in_array("noevade", $this->unit->player->status))
        {
            mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "talentFlying.evade", $attack->toJSON());
        }
    }

    function argevade($parg1, $parg2)
    {
        $ret = $this->unit->player->argAnyDrawCards($parg1, $parg2);
        $ret['title'] = clienttranslate('Evade : ${actplayer} may discard 1 AOW card, 1 divine stone or 2 activation cards from destroyed unit to evade this attack');
        $ret['titleyou'] = clienttranslate('Evade : ${you} may discard 1 AOW card, 1 divine stone or 2 activation cards from destroyed unit to evade this attack'); 
        $ret['selectable']['butSkip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }

    function evade($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butSkip")
        {
            $card_id = str_replace("card","",$varg1);
            $this->unit->player->discard($card_id);
            if($varg2 != null)
            {
                $card_id2 = str_replace("card","",$varg2);
                $this->unit->player->discard($card_id2);
            }
            mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "talentFlying.evadetarget", $parg1);
            $this->unit->player->status[] = 'noevade';
            mythicbattlesragnarok::DbQuery("update player set endofturnstatus = concat(endofturnstatus, ' noevade' ) where player_id = ".$this->unit->player_id);
        }
    }

    function argevadetarget($parg1, $parg2, $varg1, $varg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Evade : ${unitid_display} can redirect attack on another unit');
        $ret['titleyou'] = clienttranslate('Evade : ${unitid_display} can redirect attack on another unit');  
        $ret['unitid_display'] = $this->unit->id;
        $attack2 = new attack();
        $attack2->from = $this;
        $attack2->type = ATNORMAL;

        $units = $this->unit->zone->getUnitsWithin(0,$this->unit->getEffectiveStat(RANGE, $attack2)['total'],$this->unit->player_id,true, true);
        foreach($units as $unit)
        {
            if($this->unit != $unit && $unit != $attack->from && $unit->player_id != $this->unit->player_id)
            {
                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Do you want to redirect the attack on ${unitid_display}?'
                );
            }
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");   
        
        return $ret;
    }

    function evadetarget($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != null && $varg1 != "butskip")
        {
            $unitid = str_replace("unit","", $varg1);
            $unit =  mythicbattlesragnarok::$instance->units[$unitid];
            $attack = attack::fromJSON($parg1);
            $attack->to = $unit;
            $json = $attack->toJSON();
            $sql = "update pending set arg = '".$json."' where arg='".$parg1."'";
            mythicbattlesragnarok::DbQuery( $sql);

            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} redirects attack on ${unitid_display2}'), array(
                'unitid_display' => $this->unit->id,
                'unitid_display2' => $unit->id
            ) );
        }
        else
        {
            $sql = "delete from pending where arg='".$parg1."'";
            mythicbattlesragnarok::DbQuery( $sql);
        }
    }

}