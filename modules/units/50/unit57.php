<?php 

class unit57 extends unit
{
    public $category = HERO;
    public $cost = 4;
    public $activation = 0;
    public $aow = 0;
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
        $this->name = clienttranslate("The Sleeper");     
        $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("The Sleeper & The Bear"), clienttranslate('When recruited, take the Bödvar the Sleeper and Bödvar the Bear miniatures. Deploy the Bear as normal. At the same time, deploy the Sleeper two areas away from the Bear, excluding areas touching your edge of the game board. The Sleeper does not act and cannot move or be moved. He suffers -2 defense if the Bear is not in his surroundings. However, he can retaliate as normal.'));
        $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Fylgja"), clienttranslate('Instead of performing a normal attack, the Bear can perform a range 0 area attack targeting only enemy units. Until the start of his next activation, the Bear has the Block talent, and talents and powers of other units do not affect him, even if he wants them to.'),1);
  
    }

    function CanBeDraft() 
    {
        return false;
    }

    public function wound($wounds, $cause, $arrayDetails = NULL)
    {
        parent::wound($wounds, $cause, $arrayDetails);
        $otherid = self::getUniqueValueFromDB("select id from unit where type = 27");
        
        if(mythicbattlesragnarok::$instance->units[$otherid]->hp != $this->hp)  
        {
            mythicbattlesragnarok::$instance->units[$otherid]->wound($wounds, $cause, $arrayDetails);
        }
    }

    public function getAuraStatus($to)
    {
        $ret = parent::getAuraStatus($to);
        if($to == $this)
        {
            $ret[] = "nowalk";
            $ret[] = "norun";
            $ret[] = "noattack";
            $ret[] = "noclaim";
            $ret[] = "noabsorb";
            $ret[] = "noattack";
            $ret[] = "noascend";
            $ret[] = "noattack";
            $ret[] = "noforce";
        }
        return $ret;
    }

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        $otherid = self::getUniqueValueFromDB("select id from unit where type = 27");
        if($stat == DEFENSE && $attack->to == $this && $this->zone->getDistanceWith(mythicbattlesragnarok::$instance->units[$otherid]->zone)>1)
        {
            $ret[$this->powers[1]->title] = -2;
            $ret['total']-=2;
        }
        return $ret;
    }

    function getRetaliateCard()
    {
        $otherid = self::getUniqueValueFromDB("select id from unit where type = 27");
        $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type_arg = ".$otherid." limit 1");
        return $card_id;      
    }

    
}