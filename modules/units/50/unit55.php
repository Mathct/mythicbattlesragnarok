<?php 

class unit55 extends unit
{
    public $category = MONSTER;
    public $cost = 4;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("Block","Climb","ForceOfNature");
    public $traitname = "Boreal";

    public $stats =  [
        [7, 8, 0, 1,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [6, 7, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [6, 6, 0, 0,1,1,0],
        [6, 5, 0, 0,0,1,0]  
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Utgarda-Loki");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Master Of Challenges"), clienttranslate('When he targets or is targeted by an enemy unit, Utgarda-Loki can decide, before the first assault and for the duration of the attack, to use the enemy values of offense, defense, or both, instead of his own. In this case, he uses the same values as if the enemy unit had full vitality.'),1);
         $this->powers[1] = new power(1,ACTIVE, WHITE, clienttranslate("Lord Of Utgard"), clienttranslate('At the beginning of Utgarda-Loki\'s activation, you can discard up to 2 cards of your choosing from your hand. Each opponent then discards the same number of cards, at random.'));
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTERSELECTINGTARGET && $this->canUse($this->powers[0]) && $attack->type == ATNORMAL && ($attack->to == $this || $attack->from == $this ) && $this->player->getAowInHand()>0 )
        {
            $other_id = $attack->from->id;
            if($attack->from == $this)
            {
                $other_id = $attack->to->id;
            }
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Master", $other_id);
        }
        if($time == STARTACTIVATION && $this->canUse($this->powers[1]) && $attack->from == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Lord");
        }
    }

    function argMaster($parg1, $parg2, $varg1, $varg2) { 
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Master Of Challenges : ${unitid_display} may use ${unitid_display2} offense ${offense} and/or defense ${defense}');
        $ret['titleyou'] = clienttranslate('Master Of Challenges : ${unitid_display} may use ${unitid_display2} offense ${offense} and/or defense ${defense}'); 
        $ret['unitid_display'] = $this->id;       
        $ret['unitid_display2'] = $parg1;       

        $other = mythicbattlesragnarok::$instance->units[$parg1];
        $ret['offense'] = $other->stats[0][OFFENSE];      
        $ret['defense'] = $other->stats[0][DEFENSE];       

        $ret['selectable']['butoffense'] = array("title" => clienttranslate('Offense'));
        $ret['selectable']['butdefense'] = array("title" => clienttranslate('Defense'));
        $ret['selectable']['butboth'] = array("title" => clienttranslate("Both"));
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
       
    }

    function Master($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type <= 0 limit 1");
            $this->player->discard($card_id);  

            $other = mythicbattlesragnarok::$instance->units[$parg1];
            $offense = $other->stats[0][OFFENSE];      
            $defense = $other->stats[0][DEFENSE];     

            if($varg1 == "butoffense" || $varg1 == "butboth")
            {
                $this->status[] = "baseoffense".$offense; 
                mythicbattlesragnarok::DbQuery("update unit set statusActivation = concat(statusActivation, ' baseoffense".$offense."' ) where id = ".$this->id); 
                mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} uses ${unitid_display2} offense ${offense}'), array(
                    'unitid_display' => $this->id,
                    'unitid_display2' => $parg1,
                    'offense' => $offense
                    ) );
            }
            if($varg1 == "butdefense" || $varg1 == "butboth")
            {
                $this->status[] = "basedefense".$offense; 
                mythicbattlesragnarok::DbQuery("update unit set statusActivation = concat(statusActivation, ' basedefense".$defense."' ) where id = ".$this->id); 
                mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} uses ${unitid_display2} defense ${defense}'), array(
                    'unitid_display' => $this->id,
                    'unitid_display2' => $parg1,
                    'defense' => $defense
                    ) );
            }
        }
    }

    function getStat($stat)
    {
        $ret = parent::getStat($stat);
        if($stat == OFFENSE)
        {
            foreach($this->status as $status)
            {
                if(str_starts_with($status,"baseoffense"))
                {
                    return intval(str_replace("baseoffense", "",$status));
                }
            }
        }
        if($stat == DEFENSE)
        {
            foreach($this->status as $status)
            {
                if(str_starts_with($status,"basedefense"))
                {
                    return intval(str_replace("basedefense", "",$status));
                }
            }
        }
        return $ret;
    }


    function argLord($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Lord Of Utgard : ${actplayer} may discard up to 2 selected cards');
        $ret['titleyou'] = clienttranslate('Lord Of Utgard : ${you} may discard up to 2 selected cards'); 
        $ret['selectable']['butchange'] = array("title" => clienttranslate("Discard"));
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

    
    function Lord($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $deck = $this->player->getDeck();
            $nb = 0;
            foreach(array_filter(explode(" ",$varg2)) as $arg)
            {             
                $nb++;   
                $card_id = str_replace("card","", $arg);
                $this->player->discard($card_id);
            }

            $ophands = self::getObjectListFromDB( "SELECT card_id from deck".$this->player->getOtherPlayer()->player_no." where card_location = 'hand'", true );
           
            for($i = 0;$i<$nb && count($ophands)>0.;$i++)
            {
                $index = bga_rand(0, count($ophands)-1);
                $card_id = $ophands[$index];
                $this->player->getOtherPlayer()->discard($card_id);
                unset($ophands[$index]);
                $ophands = array_values($ophands);
            }
            mythicbattlesragnarok::$instance->resetUndo();
        }
    }

}