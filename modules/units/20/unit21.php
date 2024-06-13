<?php 

class unit21 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("GemCollector","Leader", "Mobility");    
    public $traitname = "Aquatic";

    public $stats =  [
        [8, 8, 1, 2,1,1,0],
        [8, 8, 1, 2,1,1,0],
        [7, 8, 1, 2,1,1,0],
        [7, 7, 1, 2,1,1,0],
        [7, 7, 1, 1,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [6, 7, 0, 1,1,1,0],
        [6, 6, 0, 1,0,1,0],
        [5, 6, 0, 0,0,1,0]
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Njord");
         $this->powers[0] = new power(0,OFFENSIVE, BLACK, clienttranslate("Whirlwind"), clienttranslate('Make a 6 dice area attack in one of the areas in Njord\'s surroundings. You can then move one of the units present in this area one area closer or farther away, taking into account terrain and boundary restrictions.'),1);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Lord of the Currents"), clienttranslate('Enemy flying units cannot use fast flight to cross Njord\'s area, or attack Njord using an aerial attack. When Njord is in an area from the beginning of the turn, he and his enemies treat the area as Water terrain.'));
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Whirlwind"); 
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 1, 6, "forceoffense"); 
        }
    }

    public function canBeTargeted($attack = NULL)
    {
        return parent::canBeTargeted($attack) && ($attack == null || !$attack->aerial);
    }

    function zoneattack($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        parent::zoneattack($parg1, $parg2, $varg1, $varg2);        
        $zoneid = str_replace("zone","", $varg1);
        $sql = "update pending set arg = '".$zoneid."' where function='Whirlwind'";
        mythicbattlesragnarok::DbQuery( $sql);
    }

    function argWhirlwind($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} may move a unit one area closer or farther away');
        $ret['titleyou'] = clienttranslate('${you} may move a unit one area closer or farther away'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        
        $zonefrom = mythicbattlesragnarok::$instance->zones[$parg1];
        $distance = $this->zone->getDistanceWith($zonefrom);

        $otherzones = $this->zone->getZonesAtDistance($distance+1, $distance+1);       
        if($distance>0)
        {
            $otherzones += $this->zone->getZonesAtDistance($distance-1, $distance-1);
        } 
       
        foreach( $zonefrom->units as $unit)
        {     
            $possibleZones = array();
            foreach($otherzones as $otherzone)
            {
                if(array_key_exists($otherzone->id, $unit->zone->boundaries) && $otherzone->CanEnter($unit))
                {
                    $possibleZones[] = "zone".$otherzone->id;
                }
            }

            if(count($possibleZones)>0){               
                $ret['selectable']['unit'.$unit->id] = array(
                    "title" => 'Where do you want to move ${unitid_display} ?',
                    "target" => $possibleZones,
                    "unitid_display" => $unit->id
                ); 
            }
        }
        return $ret;
    }

    function Whirlwind($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {     
        if($varg1 != null && $varg1 != "butskip")
        {
            $unitid = str_replace("unit","", $varg1);
            $unit = mythicbattlesragnarok::$instance->units[$unitid];
            $zoneid = str_replace("zone","", $varg2);
            $unit->move("force",NULL, $zoneid);
        }
    }

}