<?php 

class unit17 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("Bolster","CloseProtection", "Mobility");

    public $stats =  [
        [7, 8, 1, 2,1,1,0],
        [7, 8, 1, 2,1,1,0],
        [7, 7, 1, 2,1,1,0],
        [7, 7, 1, 2,1,1,0],
        [6, 7, 1, 1,1,1,0],
        [6, 7, 1, 1,1,1,0],
        [5, 6, 1, 1,1,0,0],
        [4, 6, 1, 0,1,0,0]
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Frigg");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Divine Link"), clienttranslate('When recruited, take the Frigg tokens. Before the start of the game, place these tokens on 2 of your units whose cumulative Recruitment Points have a value of 6 at most. They benefit from the Guard talent, exclusively between them, until one of these units is destroyed.'),0,2);
         $this->powers[1] = new power(1,OFFENSIVE, WHITE, clienttranslate("Weaving the Threads of the Future"), clienttranslate('Make a 6 dice normal attack against up to two units in Frigg’s surroundings. Then, look at the first 2 cards in your deck OR in an opponent’s deck, and rearrange them in any order.'),1);

    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == STARTGAME)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "SetupDivineLink");
        }

        $unitids = mythicbattlesragnarok::$instance->getObjectListFromDB("select Replace(location, 'dashboard','') from token where type = 'Frigg'", true);
        if(count($unitids) == 2)
        {
            $unit1 = mythicbattlesragnarok::$instance->units[$unitids[0]];
            $unit2 = mythicbattlesragnarok::$instance->units[$unitids[1]];
            if($time == AFTERSELECTINGTARGET && ($attack->to == $unit1 || $attack->to == $unit2 ) && $unit1->zone == $unit2->zone && !in_array("noredirect", $attack->from->status))
            {
                if($attack->to == $unit1 )
                {
                    mythicbattlesragnarok::$instance->addPending($this->player_id,$unit2->id, "talentGuard.guard", $attack->toJSON());
                }
                else
                {
                    mythicbattlesragnarok::$instance->addPending($this->player_id,$unit1->id, "talentGuard.guard", $attack->toJSON());
                }
            }
        }
    } 

    function argSetupDivineLink($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Divine Link : ${actplayer} must choose its 2 allies');
        $ret['titleyou'] = clienttranslate('Divine Link : ${you} must choose your 2 allies'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        
        foreach($this->player->units as $unit1)
        {
            $ret['selectable']['unit'.$unit1->id] = array('title' => clienttranslate('${you} must choose the second unit'), 'target'=>array());
            foreach($this->player->units as $unit2)
            {            
                if($unit1 != $unit2 && $unit1->cost + $unit2->cost <= 6)
                {
                    $ret['selectable']['unit'.$unit1->id]['target'][] = 'unit'.$unit2->id;
                }
            }
            if(count($ret['selectable']['unit'.$unit1->id]['target']) == 0)
            {
                unset( $ret['selectable']['unit'.$unit1->id]);
            }
        }
        return $ret;
    }
    
    function SetupDivineLink($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != 'butskip')
        {
            $unitid1 = str_replace("unit","", $varg1);
            $unitid2 = str_replace("unit","", $varg2);
            self::DbQuery( "INSERT INTO token (type, location, remove) VALUES ( 'Frigg', 'dashboard".$unitid1."', 'destroyed')");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") ,
                "unitId" => $unitid1
            ) );
            self::DbQuery( "INSERT INTO token (type, location, remove) VALUES ( 'Frigg', 'dashboard".$unitid2."', 'destroyed')");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") ,
                "unitId" => $unitid2
            ) );
        }
    }    

    public function activatePower($index)
    {
        if($index == 1)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Weaving2"); 
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "WeavingAttack"); 
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "WeavingAttack"); 
        }
    }

    
    function argWeavingAttack($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Weaving the Threads of the Future : ${unitid_display} can make a 6 dice attack in their surroundings');
        $ret['titleyou'] = clienttranslate('Weaving the Threads of the Future : ${unitid_display} can make a 6 dice attack in their surroundings'); 
        $ret['unitid_display'] = $this->id;
       
        $units = $this->zone->getUnitsWithin(0,1,$this->player_id,true, false);
        foreach($units as $unit)
        {
            if($unit->player_id != $this->player_id)
            {       
                $attack = new attack();      
                $attack->from = $this;   
                $attack->to = $unit;
                $attack->range = $this->zone->getDistanceWith($unit->zone);               
                $attack->type = ATNORMAL;

                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Do you want to attack ${unitid_display} ( ${offense} vs ${defense} ) ?',
                    "offense" => 6,
                    "defense" => $unit->getEffectiveStat(DEFENSE, $attack),
                );
            }
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }

    function WeavingAttack($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != "butskip")
        {
            $unitid = str_replace("unit","", $varg1);
            $unit = mythicbattlesragnarok::$instance->units[$unitid];
            $attack = new attack();      
            $attack->from = $this;   
            $attack->to = $unit;
            $attack->offense = 6;
            $attack->range = $this->zone->getDistanceWith($unit->zone);               
            $attack->type = ATNORMAL;
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A2A_ValueCalculation", $attack->toJSON(), 6);
            mythicbattlesragnarok::$instance->onTiming(AFTERSELECTINGTARGET, $attack);
            mythicbattlesragnarok::$instance->resetUndo();
        }
    }
    
    function argWeaving2($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Weaving the Threads of the Future : ${actplayer} must look at a deck');
        $ret['titleyou'] = clienttranslate('Weaving the Threads of the Future : ${you} must look at a deck'); 
        $ret['selectable']['butyour'] = array("title" => clienttranslate("Your deck"));
        $ret['selectable']['butother'] = array("title" => clienttranslate("Opponent's deck"));
        return $ret;
    }

    function Weaving2($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Weaving3", $varg1); 
    }

    function argWeaving3($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Weaving the Threads of the Future : ${actplayer} must select the first card to put back');
        $ret['titleyou'] = clienttranslate('Weaving the Threads of the Future : ${you} must select the first card to put back'); 
        
        $ret['pickcards'] = array();
        $player_no = $this->player->player_no;
        if($parg1 == "butother")
        {
            $player_no = $this->player->getOtherPlayer()->player_no;
        }

        $sql = "SELECT * from deck".$player_no." where card_location = 'deck' order by card_location_arg desc limit 2";
        $cards = self::getCollectionFromDb( $sql );
        foreach($cards as $card)
        {    
            $ret['selectable']['card'.$card['card_id']] = array();
            $ret['pickcards'][] = $card;            
        }
        if(count($ret['pickcards'])>0)
        {
            $ret['selectable']['fake'] = array();
        }

        return $ret;
    }
    
    function Weaving3($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != null && $varg1 != "fake")
        {
            $card_id = str_replace("card","", $varg1);
            $deck = $this->player->getDeck();
            $player_no = $this->player->player_no;
            if($parg1 == "butother")
            {
                $deck = $this->player->getOtherPlayer()->getDeck();
                $player_no = $this->player->getOtherPlayer()->player_no;
            }

            $sql = "SELECT * from deck".$player_no." where card_location = 'deck' order by card_location_arg desc limit 2";
            $cards = self::getCollectionFromDb( $sql );

            $deck->insertCardOnExtremePosition( $card_id, 'deck', true);
            foreach(array_reverse($cards) as $card)
            {    
                if($card['card_id'] != $card_id)
                {
                    $deck->insertCardOnExtremePosition( $card_id, 'deck', true);
                }          
            }
        }
    }

}