<?php 

class unit56 extends unit
{
    public $category = TITAN;
    public $cost = 8;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("Block","Climb","GodSlayer");

    public $stats =  [
        [10, 9, 1, 3,8,1,0],
        [10, 9, 1, 3,8,1,0],
        [9, 8, 1, 2,7,1,0],
        [9, 8, 1, 2,7,1,0],
        [8, 8, 1, 2,7,1,0],
        [8, 8, 1, 2,7,1,0],
        [7, 7, 0, 1,7,1,0],
        [7, 6, 0, 1,6,1,0],
        [7, 6, 0, 1,5,1,0],
        [6, 6, 0, 1,0,1,0],
        [5, 5, 0, 0,0,1,0] 
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Titan Fenrir");
         $this->powers[0] = new power(0,OFFENSIVE, BLACK, clienttranslate("Bitter Jaws"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_black">X</div></div>Make an X dice area attack in any 2 areas in Titan Fenrir\'s surroundings.'),2);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Dusk"), clienttranslate('A troop destroyed by Titan Fenrir cannot benefit from a troop recall. When a normal attack from Titan Fenrir is not resolved against its initial target, it inflicts 2 additional wounds on its new target (2 minimum). Titan Fenrir suffers -1 defense when he is in an enemy divinity’s surroundings.'));
    }
    
    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 1, $this->getStat(POWER1)); 
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 1, $this->getStat(POWER1)); 
        }
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTERDIE && $attack->to->category == TROOP && $attack->from == $this && $this->canUse($this->powers[1]))
        {
            mythicbattlesragnarok::DbQuery( "update unit set zone_id = 0 where id=".$attack->to->id);
            $attack->to->zone_id = 0; 
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                'id' => 'unit'.$attack->to->id
            ) );
        }
        if($time == AFTERSELECTINGTARGET && $attack->from == $this)
        {
            $this->status[] = "fenrirtarget".$attack->to->id; 
            mythicbattlesragnarok::DbQuery("update unit set statusActivation = concat(statusActivation, ' fenrirtarget".$attack->to->id."' ) where id = ".$this->id); 
        }
        if($time == AFTERATTACKWOUND && $attack->from == $this)
        {
            if(!in_array("fenrirtarget".$attack->to->id, $this->status))
            {
                $attack->to->wound(2,$this);
            }

            foreach($this->status as $status)
            {
                if(str_starts_with($status, "fenrirtarget"))
                {                    
                    mythicbattlesragnarok::$instance->DbQuery("update unit set statusActivation = replace(statusActivation, ' ".$status."', '')" );
                }
            }
        }
        
    }

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[1]) && $stat == DEFENSE && $attack->to == $this)
        {
            foreach($this->zone->getUnitsWithin(0,1,$this->player_id, true, false) as $unit)
            {
                if(($unit->category == GOD || $unit->category == TITAN) && $this->canSeeZone($unit->zone) )
                {
                    $ret[$this->powers[1]->title] = -1;
                    $ret['total']--;
                    return $ret;
                }
            }
        }
        return $ret;
    }
}