<?php 

class unit38 extends unit
{
    public $category = HERO;
    public $cost = 3;
    public $activation = 5;
    public $aow = 1;
    public $talentsNames = array("Archery","CloseProtection","Mobility");

    public $stats =  [
        [5, 6, 2, 2,1,1,0],
        [5, 6, 2, 2,1,1,0],
        [5, 6, 1, 1,1,1,0],
        [5, 5, 1, 1,1,1,0],
        [5, 5, 1, 0,1,1,0],
        [4, 5, 1, 0,0,0,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Skuld");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Power Over Death"), clienttranslate('When an allied troop has just been destroyed, you can immediately recall it in an unsaturated area next to the one in which it was destroyed. This effect is applied before any other.'),1);
         $this->powers[1] = new power(1,PASSIVE, WHITE, clienttranslate("Chimeric Approach"), clienttranslate('One use per game. Draw 5 cards. All other players can draw 1 card each.'),1);
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTERDIE && $attack->to->player_id == $this->player_id && $attack->to->category == TROOP)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Death", $attack->to->id, $attack->to->zone_id);
        }
    }

    function argDeath($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Power Over Death : ${actplayer} may recall ${unitid_display}');
        $ret['titleyou'] = clienttranslate('Power Over Death : ${you} may recall ${unitid_display}'); 
        $ret['unitid_display'] = $this->id;
      
        if($this->player->getAowInHand()>=1)
        {
            $unit_id = $parg1;
            $unit = mythicbattlesragnarok::$instance->units[$unit_id];
            $zoneDist = mythicbattlesragnarok::$instance->zones[$parg2];
            $zones = $zoneDist->getZonesAtDistance(1,1);
            foreach($zones as $zone)
            {
                if($zone->canEnter($unit))
                {
                    $ret['selectable']['zone'.$zone->id] = array(); 
                }
            }
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }
    
    function Death($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type <= 0 limit 1");
            $this->player->discard($card_id); 
            $unit_id = $parg1;
            $unit = mythicbattlesragnarok::$instance->units[$unit_id];
            $unit->deploy(NULL, NULL, $varg1, $varg2);                         

        }
    }

    function getAnyTimeActions($player_id)
    {
        $ret = parent::getAnyTimeActions($player_id);
        if($this->canUse($this->powers[1]) && $player_id == $this->player_id)
        {        
            $alreadyUse = mythicbattlesragnarok::getUniqueValueFromDB("select count(*) from unit where statusGame like '%skuld%'");
            if($alreadyUse == 0)
            {
                $ret['butAny'.$this->id."Chimeric"] = array("title" => clienttranslate("Chimeric Approach"));
            }
        }
        return $ret;
    }

    function Chimeric($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} uses ${talent}'), array(
                'i18n' => array( 'talent'),
                'unitid_display' => $this->id,
                'talent' => $this->powers[1]->title
                ) );

            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type <= 0 limit 1");
            $this->player->discard($card_id); 
            
            $this->status[] = "power1";
            mythicbattlesragnarok::DbQuery("update unit set statusGame = concat(statusGame, ' skuld' ) where id = ".$this->id);
           
            mythicbattlesragnarok::$instance->addPending($this->player->getOtherPlayer()->player_id,0, "draw"); 
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw", "mandatory");
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw", "mandatory");
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw", "mandatory");
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw", "mandatory");
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw", "mandatory");

        }
    }

}