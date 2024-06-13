<?php 

class unit18 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("Mobility","Terror", "Torment");

    public $stats =  [
        [7, 8, 0, 2,1,1,1],
        [7, 8, 0, 2,1,1,1],
        [7, 8, 0, 2,1,1,1],
        [7, 8, 0, 2,1,1,1],
        [6, 7, 0, 1,1,1,1],
        [6, 7, 0, 1,1,1,1],
        [6, 6, 0, 1,1,1,1],
        [5, 6, 0, 0,1,1,0],
        [5, 6, 0, 0,1,1,0]
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Hel");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Ganglati & Ganglot"), clienttranslate('When recruited, take Hel\'s token. Before the start of the game, place this token on the base of a non-divinity enemy unit. That unit loses 1 movement point (to a minimum of 1).'),0,1);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Death"), clienttranslate('Before the start of the game, Hel obtains a Slayer talent of your choosing from Titan, God, Hero, Monster, or Troop.'));
         $this->powers[2] = new power(2,ACTIVE, BLACK, clienttranslate("Life"), clienttranslate('A unit in Hel\'s area regains 1 vitality point.'));
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == STARTGAME)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Setup");
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Death");
        }
        if($time == ENDACTIVATION && $this->canUse($this->powers[2]) && $attack->from == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "life"); 
        }  
    } 

    public function activatePower($index)
    {
        if($index == 2)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Life"); 
        }
    }

    function argSetup($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Ganglati & Ganglot : ${actplayer} must choose the unit that loses 1 movement point');
        $ret['titleyou'] = clienttranslate('Ganglati & Ganglot : ${you} must choose the unit that loses 1 movement point'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        
        foreach(mythicbattlesragnarok::$instance->units as $unit)
        {            
            if($unit->player_id != $this->player_id && $unit->category != GOD  && $unit->category != TITAN )
            {
                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Do you want to place Hel\'s token on ${unitid_display}?'
                );
            }
        }
        return $ret;
    }

    
    function Setup($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != 'butskip')
        {
            $unitid1 = str_replace("unit","", $varg1);
            self::DbQuery( "INSERT INTO token (type, location, remove) VALUES ( 'Hel', 'dashboard".$unitid1."', 'destroyed')");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1"),
                "unitId" => $unitid1
            ) );
        }
    } 
    
    public function getStatBonus($stat, $to, $attack)
    {
        $ret = parent::getStatBonus($stat, $to, $attack);
        if($stat == MOVEMENT && $this->canUse($this->powers[0]) && in_array("tokenHel", $to->status) && $to->getStat(MOVEMENT)>1)
        {  
            $ret--;
        }
        return $ret;
    }

    function argDeath($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Death : ${unitid_display} may obtain a Slayer talent');
        $ret['titleyou'] = clienttranslate('Death : ${unitid_display} may obtain a Slayer talent'); 
        $ret['unitid_display'] = $this->id;
        $ret['selectable']['butTitan'] = array("title" => clienttranslate("Titan"));
        $ret['selectable']['butGod'] = array("title" => clienttranslate("God"));
        $ret['selectable']['butHero'] = array("title" => clienttranslate("Hero"));
        $ret['selectable']['butMonster'] = array("title" => clienttranslate("Monster"));
        $ret['selectable']['butTroop'] = array("title" => clienttranslate("Troop"));
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        
        return $ret;
    }

    
    function Death($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != 'butskip')
        {
            $type = str_replace("but","", $varg1);
            $this->status[] = "talent".$type."Slayer"; 
            mythicbattlesragnarok::DbQuery("update unit set statusGame = concat(statusGame, ' talent".$type."Slayer' ) where id = ".$this->id);           
        }
    } 

    function argLife($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Life : ${actplayer} must select the unit to gain 1 vitality point.');
        $ret['titleyou'] = clienttranslate('Life : ${you} must select the unit to gain 1 vitality point.'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");   
        
        foreach($this->zone->units as $unit)
        {
            $ret['selectable']['unit'.$unit->id] = array(
                "confirm" => 'Do you want to heal ${unitid_display}?'
            ); 
        }

        return $ret;
    }

    
    function Life($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != 'butskip')
        {
            $unitid = str_replace("unit","", $varg1);
            mythicbattlesragnarok::$instance->units[$unitid]->heal(1);          
        }
    } 

}