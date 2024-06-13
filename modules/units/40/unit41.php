<?php 

class unit41 extends unit
{
    public $category = MONSTER;
    public $cost = 5;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("GodSlayer","Initiative","SneakAttack");

    public $stats =  [
        [9, 8, 0, 2,1,1,0],
        [8, 8, 0, 2,1,1,0],
        [8, 7, 0, 2,1,1,0],
        [7, 7, 0, 2,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [7, 6, 0, 1,1,1,0],
        [7, 6, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [5, 5, 0, 0,1,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Fenrir");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("The Hunt is On"), clienttranslate('When Fenrir performs an attack action, the enemy unit suffers -1 defense for this attack. Fenrir can then move one area away, without being affected by the Block talent.'));
         $this->powers[1] = new power(1,OFFENSIVE, WHITE, clienttranslate("The Devourer"), clienttranslate('Fenrir can use his 2 simple actions during this turn to perform 2 attack actions. This power is cumulative with THE HUNT IS ON power.'),1);
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == RECRUIT && $attack == $this)
        {            
            $id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT id from unit where type=56");
            self::DbQuery( "DELETE FROM unit WHERE type=56");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                'id' => 'unit'.$id
            ) );
        }
    }

    public function canUse($item)
    {
        $ret = parent::canUse($item);

        if($item == $this->powers[1])
        {
            $left = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'T2C_PickActionSimple'");
            $ret = $ret && $left>=2;
        }
        return $ret;
    }

    public function activatePower($index)
    {
        if($index == 1)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "addtalentblock",0);
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "move");
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "addtalentblock",1);
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A1A_targetChoice"); 
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "addtalentblock",0);
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "move");
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "addtalentblock",1);
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A1A_targetChoice"); 
            mythicbattlesragnarok::DbQuery( "delete from pending where function = 'T2C_PickActionSimple' and unit_id=".$this->id." and player_id=".$this->player_id);
        }
    }

    public function getStatBonus($stat, $to, $attack)
    {
        if( $stat == DEFENSE && $this->canUse($this->powers[0]) && $attack->from == $this  && $attack->to->player_id != $this->player_id && $attack->type == ATNORMAL)
        {
            return -1;                    
        }
        return 0;
    }

    function T2C_PickActionSimple($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 == "butattack")
        {            
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "addtalentblock",0);
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "move");
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "addtalentblock",1);
        }

        parent::T2C_PickActionSimple($parg1, $parg2, $varg1, $varg2);
    }
    
    function addtalentblock($parg1, $parg2, $varg1, $varg2) {  
        if($parg1 == 1)
        {
            $this->status[] = "talentBlock"; 
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' talentBlock' ) where id = ".$this->id);
            $talent = new talentBlock($this);
            $this->talents["Block"] = $talent;
            $talent->unit = $this; 
            $key = array_search("nowalk", $this->status);
            if ($key !== false) {
                unset($this->status[$key]);
            }
        }
        else{
            mythicbattlesragnarok::DbQuery("update unit set statusTurn =  REPLACE(statusTurn, ' talentBlock', '' )  where id = ".$this->id);
            unset($this->talents["Block"]);
        }
    }

}