<?php 

class unit24 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("ForceOfNature","MightyThrow", "MonsterSlayer");

    public $stats =  [
        [8, 8, 0, 2,1,1,1],
        [8, 8, 0, 2,1,1,1],
        [8, 8, 0, 2,1,1,1],
        [8, 8, 0, 2,1,1,1],
        [7, 8, 0, 1,1,1,1],
        [7, 7, 0, 1,1,1,1],
        [7, 7, 0, 1,1,1,1],
        [7, 7, 0, 0,1,1,1],
        [6, 6, 0, 0,1,1,1]
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Thor");
         $this->powers[0] = new power(0,OFFENSIVE, BLACK, clienttranslate("Mjölnir"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_black">5</div></div> Make a normal attack on a unit in any area of the game board, ignoring obstacles.'),1);
         $this->powers[1] = new power(1,OFFENSIVE, WHITE, clienttranslate("Master of Thunder"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_black">6</div></div> Make a 6 dice area attack in one of the areas in Thor\'s surroundings.'),1);
         $this->powers[2] = new power(2,PERMANENT, BLACK, clienttranslate("Tanngnjóstr & Tanngrisnir"), clienttranslate('At the end of his activation phase, Thor regains 1 vitality point. When Thor is recruited, you can immediately recruit the Tanngnjóstr & Tanngrisnir Monster for 1 RP.'));
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Mjolnir"); 
        }
        if($index == 1)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 1, 6); 
        }
    }

    function argMjolnir($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Mjölnir : ${actplayer} may select a target for Mjölnir');
        $ret['titleyou'] = clienttranslate('Mjölnir : ${you} may select a target for Mjölnir'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");

        $attack = new attack();
        $attack->from = $this;
        $attack->type = ATNORMAL;

        foreach(mythicbattlesragnarok::$instance->units as $unit)
        {            
            $range = $this->zone->getDistanceWith($unit->zone);               
            $attack->range = $range;
            $attack->offense = 5;
            $attack->to = $unit;
            if($attack->to->canBeTargeted($attack))
            {
                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Do you want to attack ${unitid_display} ( ${offense} vs ${defense} ) ?',
                    "offense" => 5,
                    "defense" => $unit->getEffectiveStat(DEFENSE, $attack),
                ); 
            }                  
        } 
        return $ret;
    }

    function Mjolnir($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $unitid = str_replace("unit","", $varg1);
            $unit = mythicbattlesragnarok::$instance->units[$unitid];
            $attack = new attack();
            $attack->from = $this;
            $attack->type = ATNORMAL;
            $range = $this->zone->getDistanceWith($unit->zone);               
            $attack->range = $range;
            $attack->offense = 5;
            $attack->to = $unit;

            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A2A_ValueCalculation", $attack->toJSON(), 5);
        }
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == RECRUIT && $attack == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "extrarecruit");
        }
        else if($time == ENDACTIVATION && $this->canUse($this->powers[2]) && $attack->from == $this && $this->hp < count($this->stats))
        {
            $this->heal(1);
        }
    }

    function argextrarecruit($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Tanngnjóstr & Tanngrisnir : ${actplayer} may recruit Tanngnjóstr & Tanngrisnir for 1RP');
        $ret['titleyou'] = clienttranslate('Tanngnjóstr & Tanngrisnir : ${you} may recruit Tanngnjóstr & Tanngrisnir for 1RP'); 

        $cat_id = self::getUniqueValueFromDB("SELECT id FROM unit WHERE type=53 and player_id=0");
        if($this->player->rp >= 1 && $cat_id != null)
        {
            $ret['selectable']['butrecruit'] = array("title" => clienttranslate("Recruit"));
            $ret['selectable']['unit'.$cat_id] = array( 'confirm' => clienttranslate('Do you want to recruit ${unitid_display}?'));
        }

        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");

        return $ret;
    }

    function extrarecruit($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $unitid = self::getUniqueValueFromDB("SELECT id FROM unit WHERE type=53 and player_id=0");
            $unit = mythicbattlesragnarok::$instance->units[$unitid]; 
            $unit->player_id = $this->player->player_id; 
            $unit->zone_id = -$this->player->player_no;
            $this->player->rp --;

            self::DbQuery( "UPDATE player set rp = rp - 1  WHERE player_id = {$this->player_id}" );           
            self::DbQuery( "UPDATE unit set player_id = {$this->player_id}, zone_id = -".$this->player->player_no."  WHERE id = {$unitid}" );

            mythicbattlesragnarok::$instance->notifyAllPlayers( "innerhtml", '', array(
                'id' => "rp".$this->player->player_no,
                'html' => $this->player->rp
            ) );
    
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", clienttranslate('${player_name} draft ${unitid_display}'), array(
                'player_name' => $this->player->player_name,
                'player_id' => $this->player->player_id,
                'unitid_display' => $unitid,
                'id' => 'unit'.$unitid
            ) );

            mythicbattlesragnarok::$instance->notifyAllPlayers( "backontable", '', array(
                'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$unitid),
                'category' => $this->category
            ) );
        }
    }
 
}