<?php 

class unit19 extends unit
{
    public $category = GOD;
    public $cost = 5;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("CloseProtection", "Mobility", "MonsterSlayer");

    public $stats =  [
        [7, 8, 1, 2,1,1,0],
        [7, 8, 1, 2,1,1,0],
        [6, 7, 1, 2,1,1,0],
        [6, 7, 1, 2,1,1,0],
        [6, 7, 1, 1,1,1,0],
        [6, 7, 1, 1,1,1,0],
        [5, 6, 0, 1,0,1,0],
        [4, 6, 0, 0,0,1,0]
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Idunn");
         $this->powers[0] = new power(0,ACTIVE, BLACK, clienttranslate("Deceptive Youth"), clienttranslate('At the end of Idunn’s activation, all opponents discard the top card from their deck.'));
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Golden Apples"), clienttranslate('When recruited, take the GOLDEN APPLES tokens. At the beginning of Idunn\'s first activation, place each of the GOLDEN APPLES in a different area on the board. An allied unit present in or crossing an area containing a GOLDEN APPLE can consume it (and discard it permanently) to regain 1 vitality point. A unit can only consume one per turn.'),0,3);
    }
    
    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == STARTGAME)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Setup");
        }
        if($time == ENDACTIVATION && $this->canUse($this->powers[0]) && $attack->from == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Youth");
        }
        if($time == ENTER && $attack->player_id == $this->player_id && self::getUniqueValueFromDB( "SELECT count(*) from token where type='goldenapple' and location = 'zone".$attack->zone->id."'")>0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "GoldenApples", $attack->id);
        }
        if($time == STARTACTIVATION && $attack->from->player_id == $this->player_id && self::getUniqueValueFromDB( "SELECT count(*) from token where type='goldenapple' and location = 'zone".$attack->from->zone->id."'")>0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "GoldenApples", $attack->from->id);
        }
    }

    function argSetup($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Golden Apples : ${actplayer} must place their golden apple tokens');
        $ret['titleyou'] = clienttranslate('Golden Apples : ${you} must place your 3 golden apple tokens'); 
        $ret['selectable']['butconfirm'] = array("title" => clienttranslate("Place"));
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['multiple'] = 3;
        
        foreach(mythicbattlesragnarok::$instance->zones as $zone)
        {            
            $ret['selectable']['zone'.$zone->id] = array();
        }
        return $ret;
    }

    
    function Setup($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != 'butskip')
        {
            foreach(array_filter(explode(" ",$varg2)) as $arg)
            { 
                $zoneid = str_replace("zone","", $arg);
                self::DbQuery( "INSERT INTO token (type, location) VALUES ( 'goldenapple', 'zone".$zoneid."')");
                mythicbattlesragnarok::$instance->notifyAllPlayers( "drop", '', array(
                    "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") ,
                    "unitId" => 'fake'
                ) );
            }
        }
    } 

    function argYouth($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Deceptive Youth : ${actplayer} may force you to discard the first card of your deck');
        $ret['titleyou'] = clienttranslate('Deceptive Youth : ${you} may force opponent to discard the top card of their deck'); 
        $ret['selectable']['butDiscard'] = array("title" => clienttranslate("Discard"));
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        
        return $ret;
    }

    
    function Youth($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != 'butskip')
        {
            $card = $this->player->getOtherPlayer()->getDeck()->getCardOnTop("deck");
            if($card != null)
            {
                $this->player->getOtherPlayer()->discard($card['id']);
            }        
        }
    } 

    function argGoldenApples($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Golden Apples : ${unitid_display} may consume a golden apple to regain 1 vitality point');
        $ret['titleyou'] = clienttranslate('Golden Apples : ${unitid_display} may consume a golden apple to regain 1 vitality point'); 
        $ret['unitid_display'] = $parg1;
        $unit =  mythicbattlesragnarok::$instance->units[$parg1];
        if(self::getUniqueValueFromDB( "SELECT count(*) from token where type='goldenapple' and location = 'zone".$unit->zone->id."'")>0)
        {
            $ret['selectable']['butConsume'] = array("title" => clienttranslate("Consume"));
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");        
        return $ret;
    }
    
    function GoldenApples($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != 'butskip')
        {
            $unitid = $parg1;
            $unit =  mythicbattlesragnarok::$instance->units[$unitid];
            $token_id = self::getUniqueValueFromDB( "SELECT id from token where type='goldenapple' and location = 'zone".$unit->zone->id."'");
            
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                'id' => 'token'.$token_id
            ) );
            mythicbattlesragnarok::DbQuery( "delete from token where id = ".$token_id);
            $unit->heal(1);   
        }
    } 

}