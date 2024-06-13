<?php 

class unit4 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("Bolster","Initiative", "Mobility");

    public $stats =  [
        [8, 8, 0, 2,1,1,1],
        [8, 8, 0, 2,1,1,1],
        [7, 8, 0, 2,1,1,1],
        [7, 7, 0, 1,1,1,1],
        [7, 7, 0, 1,1,1,1],
        [6, 7, 0, 1,1,1,1],
        [6, 7, 0, 0,1,0,1],
        [5, 6, 0, 0,1,0,1]
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Freyja");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Necklace of Brísingar"), clienttranslate('Freyja and any allied units in her area ignore the first wound caused by range 0 attacks.'));
         $this->powers[1] = new power(1,ACTIVE, WHITE, clienttranslate("Valshamr"), clienttranslate('One use per game. Place Freyja in any Open Ground area that isn’t saturated.'), 2);
         $this->powers[2] = new power(2,PERMANENT, WHITE, clienttranslate("Freyja's cats"), clienttranslate('When recruited, you can immediately recruit the Freyja’s Cats Monster for 2 RP.'));
    }

    public function getStatBonus($stat, $to, $attack)
    {
        $ret = parent::getStatBonus($stat, $to, $attack);
        
        if($this->canUse($this->powers[0]) && $stat == DAMAGEBONUS && $to == $attack->to  && $attack->to->zone == $this->zone && $attack->to->player_id == $this->player_id && $attack->range == 0)
        {
            $ret--;
        }
        return $ret;
    }
    
    public function activatePower($index)
    {
        if($index == 1)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "valshamr"); 
        }
    }

    function argvalshamr($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Valshamr : ${actplayer} may place ${unitid_display} on any Open Ground');
        $ret['titleyou'] = clienttranslate('Valshamr : ${you} may place ${unitid_display} on any Open Ground'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $this->id;

        foreach(mythicbattlesragnarok::$instance->zones as $zone)
        {            
            if($zone->countAsFor($this) == OPEN_GROUND && !$zone->isFull())
            {
                $ret['selectable']['zone'.$zone->id] = array();
            }                     
        } 
        return $ret;
    }

    
    function valshamr($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $this->status[] = "power1";
            mythicbattlesragnarok::DbQuery("update unit set statusGame = concat(statusGame, ' power1' ) where id = ".$this->id);

            $this->dropAll();
            $this->move(NULL, NULL, $varg1);
        }
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == RECRUIT && $attack == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "extrarecruit");
        }
    }

    function argextrarecruit($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Freyja\'s cats : ${actplayer} may recruit Freyja\'s cats for 2RP');
        $ret['titleyou'] = clienttranslate('Freyja\'s cats : ${you} may recruit Freyja\'s cats for 2RP'); 

        $cat_id = self::getUniqueValueFromDB("SELECT id FROM unit WHERE type=42 and player_id=0");
        if($this->player->rp >= 2 && $cat_id != null)
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
            $unitid = self::getUniqueValueFromDB("SELECT id FROM unit WHERE type=42 and player_id=0");
            $unit = mythicbattlesragnarok::$instance->units[$unitid]; 
            $unit->player_id = $this->player->player_id; 
            $unit->zone_id = -$this->player->player_no;
            $this->player->rp -= 2;

            self::DbQuery( "UPDATE player set rp = rp - 2  WHERE player_id = {$this->player_id}" );           
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