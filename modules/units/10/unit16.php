<?php 

class unit16 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 5;
    public $aow = 2;
    public $talentsNames = array("Bolster","CloseProtection", "Leader");

    public $stats =  [
        [7, 8, 1, 2,1,1,1],
        [7, 8, 1, 2,1,1,1],
        [7, 8, 1, 2,1,1,1],
        [7, 7, 1, 1,1,1,1],
        [6, 7, 0, 1,1,1,1],
        [6, 7, 0, 1,1,1,1],
        [6, 7, 0, 0,1,0,1],
        [5, 6, 0, 0,0,0,1]
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Freyr");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Abundance"), clienttranslate('At the end of the turn, Freyr can perform a Troop Recall without discarding an Art of War card.'));
         $this->powers[1] = new power(1,ACTIVE, WHITE, clienttranslate("Skibladnir"), clienttranslate('Freyr is immune to the effects of Polar, Aquatic, Lava, and Burning terrain types. Freyr can discard 1 Art of War card to increase his movement by 1 during a walk action.'));
         $this->powers[2] = new power(2,PERMANENT, WHITE, clienttranslate("Gullinbursti"), clienttranslate('When recruited, you can immediately recruit the Gullinbursti Monster for 1 RP.'));
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == ENDOFTURN && $this->canUse($this->powers[0]) && $attack == $this->player_id && count($this->player->argT4A_Recall("nodiscard")['selectable'])>2)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Abundance");
        }
        if($time == RECRUIT && $attack == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "extrarecruit");
        }        
    } 
    
    function argAbundance($parg1, $parg2)   
    {
        $ret = $this->player->argT4A_Recall("nodiscard", $parg2);
        $ret['title'] = clienttranslate('Abundance : ${actplayer} may recall a unit of troops without discarding');
        $ret['titleyou'] = clienttranslate('Abundance : ${you} may recall a unit of troops without discarding'); 
        return $ret;
    }

    function Abundance($parg1, $parg2, $varg1, $varg2)   
    {
        $this->player->T4A_Recall("nodiscard", $parg2, $varg1, $varg2);
    }

    function argextrarecruit($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Gullinbursti : ${actplayer} may recruit Gullinbursti for 1RP');
        $ret['titleyou'] = clienttranslate('Gullinbursti : ${you} may recruit Gullinbursti for 1RP'); 

        $cat_id = self::getUniqueValueFromDB("SELECT id FROM unit WHERE type=47 and player_id=0");
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
            $unitid = self::getUniqueValueFromDB("SELECT id FROM unit WHERE type=47 and player_id=0");
            $unit = mythicbattlesragnarok::$instance->units[$unitid]; 
            $unit->player_id = $this->player->player_id; 
            $unit->zone_id = -$this->player->player_no;
            $this->player->rp -= 1;

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

    public function activatePower($index)
    {
        if($index == 1)
        {
            
            $this->status[] = "ignoreWater ignoreBurning ignorePolar";
            mythicbattlesragnarok::DbQuery("update unit set statusOwnActivation = concat(statusOwnActivation, ' ignoreWater ignoreBurning ignorePolar' ) where id = ".$this->id);

            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Skilbladnir"); 
        }
    }

    function argSkilbladnir($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Skilbladnir : ${actplayer} may discard 1 Art of War to increase your movement speed by 1');
        $ret['titleyou'] = clienttranslate('Skilbladnir : ${you} may discard 1 Art of War to increase their movement speed by 1');         
        if($this->player->getAowInHand()>0)
        {
            $ret['selectable']['butdiscard'] = array("title" => clienttranslate("Discard"));
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }

    function Skilbladnir($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            
            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type <= 0 limit 1");
            $this->player->discard($card_id);

            $this->status[] = "movementp1";
            mythicbattlesragnarok::DbQuery("update unit set statusOwnActivation = concat(statusOwnActivation, ' movementp1' ) where id = ".$this->id);
       
        }
    }

}