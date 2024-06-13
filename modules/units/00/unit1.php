<?php 

class unit1 extends unit
{
    public $category = GOD;
    public $cost = 6;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("Bolster","Leader","Terror");
    
    public $stats =  [
        [7, 8, 1, 2,1,1,0],
        [7, 8, 1, 2,1,1,0],
        [7, 8, 1, 2,1,1,0],
        [7, 8, 1, 2,1,1,0],
        [7, 8, 0, 1,1,1,0],
        [6, 8, 0, 1,1,1,0],
        [6, 8, 0, 1,1,1,0],
        [6, 8, 0, 1,0,1,0],
        [6, 8, 0, 0,0,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Baldr");
        $this->powers[0] = new power(0, ACTIVE, BLACK, clienttranslate("Touched by the sun"), clienttranslate("At the beginning of Baldr’s activation, you can discard up to 2 cards from your hand into your deck.<br/>Then shuffle your deck and draw the same number of cards."));
        $this->powers[1] = new power(1, PERMANENT, WHITE, clienttranslate("Frigg’s Blessing"), clienttranslate("Baldr suffers 1 less wound per attack.However, if the attacker rolls a natural blank, 1, 2, 3, 4, <b>AND</b> 5 during the first assault, Baldr will instead suffer 1 additional wound (1 minimum)."));
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == STARTACTIVATION && $this->canUse($this->powers[0]) && $attack->from == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "TouchedByTheSun");
        }
    }    

    function argTouchedByTheSun($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Touched by the sun : ${actplayer} may change up to 2 selected cards');
        $ret['titleyou'] = clienttranslate('Touched by the sun : ${you} may change up to 2 selected cards'); 
        $ret['selectable']['butchange'] = array("title" => clienttranslate("Change"));
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['multiple'] = 2;

        $sql = "SELECT * from deck".$this->player->player_no." where card_location = 'hand'";
        $hand = self::getCollectionFromDb( $sql );
        foreach($hand as $card)
        {            
            $ret['selectable']['card'.$card['card_id']] = array();                      
        } 
        return $ret;
    }

    
    function TouchedByTheSun($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $deck = $this->player->getDeck();
            $nb = 0;
            foreach(array_filter(explode(" ",$varg2)) as $arg)
            {             
                $nb++;   
                $card_id = str_replace("card","", $arg);
                $deck->moveCard($card_id, 'deck');
                $card = self::getObjectFromDB( "SELECT * FROM deck".$this->player->player_no." where card_id = ".$card_id);
                mythicbattlesragnarok::$instance->notifyAllPlayers( "discard", clienttranslate('${player_name} changes ${unitid_display}'), array(
                    'player_name' => $this->player->player_name,
                    'player_id' => $this->player->player_id,
                    'unitid_display' => $card['card_type_arg'],
                    'card' => $card
                ) );
            }

            for($i = 0;$i<$nb;$i++)
            {
                $this->player->draw();
            }
        }
        $this->status[] = "power0";
        mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' power0' ) where id = ".$this->id);
    }

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[1]) && $stat == DAMAGEBONUS && $attack->to == $this)
        {
            $roll = "";
            foreach($attack->from->status as $statu)
            {
                if(str_starts_with($statu, "1stroll"))
                {
                    $roll = str_replace("1stroll","", $statu);
                }
            }
            if(strpos($roll, '0')!==false && strpos($roll, '1')!==false &&strpos($roll, '2')!==false &&strpos($roll, '3')!==false &&strpos($roll, '4')!==false &&strpos($roll, '5')!==false )
            {
                $ret["Frigg’s Blessing"] = 1;
                $ret['total']++;
            }
            else 
            {
                $ret["Frigg’s Blessing"] = -1;
                $ret['total']--;
            }
        }
        return $ret;
    }

}