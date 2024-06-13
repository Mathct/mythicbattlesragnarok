<?php 

class unit27 extends unit
{
    public $category = HERO;
    public $cost = 4;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("Berserk","MightyThrow", "TroopSlayer");

    public $stats =  [
        [8, 8, 0, 2,1,1,0],
        [8, 8, 0, 2,1,1,0],
        [8, 8, 0, 2,1,1,0],
        [7, 8, 0, 2,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [6, 7, 0, 1,1,1,0],
        [6, 6, 0, 0,1,1,0]
        
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Bödvar Bjarki");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("The Sleeper & The Bear"), clienttranslate('When recruited, take the Bödvar the Sleeper and Bödvar the Bear miniatures. Deploy the Bear as normal. At the same time, deploy the Sleeper two areas away from the Bear, excluding areas touching your edge of the game board. The Sleeper does not act and cannot move or be moved. He suffers -2 defense if the Bear is not in his surroundings. However, he can retaliate as normal.'));
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Fylgja"), clienttranslate('Instead of performing a normal attack, the Bear can perform a range 0 area attack targeting only enemy units. Until the start of his next activation, the Bear has the Block talent, and talents and powers of other units do not affect him, even if he wants them to.'),1);
    }

    public function activatePower($index)
    {
        if($index == 1)
        {          
            $id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT max(id) from pending where function = 'T2C_PickActionSimple'");
            $sql = "delete from pending where id=".$id;
            mythicbattlesragnarok::DbQuery( $sql);

            $this->status[] = "talentBlock";
            $this->status[] = "ignoreTalent";
            $this->status[] = "ignorePower";
            mythicbattlesragnarok::DbQuery("update unit set statusOwnActivation = concat(statusOwnActivation, ' talentBlock ignoreTalent ignorePower' ) where id = ".$this->id);
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 0, NULL, "onlyennemy"); 
        }
    }

    public function getExtraActions($simple)
    {
        $ret = parent::getExtraActions($simple);
        if($this->canUse($this->powers[1]))
        {
            $ret[] = array("title"=>"Fylgja", "function" => "pow1");
        }
        return $ret;
    }


    function deploy($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        parent::deploy($parg1, $parg2, $varg1, $varg2);
        mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "sleeper"); 
    }

    
    function argsleeper($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('The Sleeper & The Bear : ${player_name} must deploy the sleeper');
        $ret['titleyou'] = clienttranslate('The Sleeper & The Bear : ${you} must deploy the sleeper'); 
        $ret['player_name'] = $this->player->player_name;
        $ret['player_id'] = $this->player->player_id;

        $board = mythicbattlesragnarok::$instance->getBoard();
        $side = $board['setup'][mythicbattlesragnarok::$instance->getGameStateValue( 'boardsetup')]["side".$this->player->player_no];

        foreach($this->zone->getZonesAtDistance(2, 2) as $testedzone)
        {
            if($testedzone->canEnter($this) && !in_array($testedzone->id, $side))
            {
                $ret['selectable']['zone'.$testedzone->id] = array();
            }
        }

        return $ret;
    }

    function sleeper($parg1, $parg2,  $varg1, $varg2)
    {
        $zoneid = str_replace("zone","", $varg1);
        self::DbQuery("INSERT INTO unit (player_id, type, zone_id, hp) VALUES ($this->player_id,57,$zoneid,7)");
        $unitdb = self::getObjectFromDB( "SELECT * FROM unit where type=57" );        
        $unit = unit::Create($unitdb);
        $unit->player = $this->player;
        $unit->player->units[$unit->id] = $unit;        
        mythicbattlesragnarok::$instance->units[$unit->id] = $unit;     
        mythicbattlesragnarok::$instance->zones[$zoneid]->units[$unit->id] = $unit;
        $unit->zone = mythicbattlesragnarok::$instance->zones[$zoneid];

        mythicbattlesragnarok::$instance->notifyAllPlayers( "move", clienttranslate('${player_name} deploys ${unitid_display}'), array(
            'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$unit->id),
            "category" => $unit->category,
            'player_name' => $this->player->player_name,
            'player_id' => $this->player->player_id,
            'unitid_display' => $unit->type
        ) ); 

        mythicbattlesragnarok::$instance->onTiming(ENTER, $unit); 
        $unit->zone->onEnter($unit);
    }

    public function wound($wounds, $cause, $arrayDetails = NULL)
    {
        parent::wound($wounds, $cause, $arrayDetails);
        $otherid = self::getUniqueValueFromDB("select id from unit where type = 57");
        
        if(mythicbattlesragnarok::$instance->units[$otherid] != null && mythicbattlesragnarok::$instance->units[$otherid]->hp != $this->hp)  
        {
            mythicbattlesragnarok::$instance->units[$otherid]->wound($wounds, $cause, $arrayDetails);
        }
    }

}