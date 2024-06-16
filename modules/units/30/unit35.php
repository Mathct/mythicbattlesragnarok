<?php 

class unit35 extends unit
{
    public $category = HERO;
    public $cost = 4;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("Bolster","CloseProtection","HeroSlayer");

    public $stats =  [
        [6, 8, 0, 1,1,1,1],
        [6, 7, 0, 1,1,1,1],
        [5, 7, 0, 1,1,1,1],
        [5, 6, 0, 1,1,1,1],
        [5, 6, 0, 1,1,1,1],
        [4, 5, 0, 0,1,1,1],
        [4, 5, 0, 0,0,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Norns");
         $this->powers[0] = new power(0,ACTIVE, BLACK, clienttranslate("Urd"), clienttranslate('Choose 1 card from your discard pile and place it on top of your deck.'),1);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Verdandi"), clienttranslate('At the beginning of the Norns\'s activation, look at the card at the top of your deck or another player\'s deck.'));
         $this->powers[2] = new power(2,ACTIVE, WHITE, clienttranslate("Skuld"), clienttranslate('At the beginning of the Norns\'s activation, choose a non-divine enemy unit. If its player wishes to activate it during their next turn, they must discard 1 Art of War card.'));
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Urd"); 
        }
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == STARTACTIVATION && $this->canUse($this->powers[1]) && $attack->from == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Verdandi");
        }
        if($time == STARTACTIVATION && $this->canUse($this->powers[2]) && $attack->from == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Skuld");
        }
    }   

    function argSkuld($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Skuld : ${actplayer} may choose a unit to curse');
        $ret['titleyou'] = clienttranslate('Skuld : ${you} may choose a unit to curse'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");       
        foreach(mythicbattlesragnarok::$instance->units as $unit)
        {       
            if($unit->player_id != $this->player_id && $unit->category != GOD && $unit->category != TITAN)
            {
                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Skuld : Do you want to curse ${unitid_display}?'
                );
            }
        }
        return $ret;
    }
    
    function Skuld($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $unit_id = str_replace("unit","", $varg1);
            $unit = mythicbattlesragnarok::$instance->units[$unit_id];            
            $unit->status[] = "skuld";
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' skuld' ) where id = ".$unit->id);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpletext", clienttranslate('${unitid_display} will need an extra aow card to be activated'), array(
                'unitid_display' => $unit->id
            ) );
        }
    }

    function requiresAdditionalAOW($action)
    {
        if($action->function == "T2A1_Activate")
        { 
            if (str_starts_with($action->varg1, "unit"))
            {
                $unit_id = str_replace("unit","", $action->varg1);
            }
            else {            
                $card_id = str_replace("card","", $action->varg1);
                $card = self::getObjectFromDB( "SELECT * FROM deck".$this->player->player_no." where card_id = ".$card_id);

                if($card == null)
                {
                    $card = self::getObjectFromDB( "SELECT * FROM deck".$this->player->getOtherPlayer()->player_no." where card_id = ".$card_id);
                }
                $unit_id = $card['card_type_arg'];
            }
            if($unit_id != 0)
            {
                $unit = mythicbattlesragnarok::$instance->units[$unit_id]; 
                if($unit != null && in_array("skuld", $unit->status))
                {
                    return 1;
                }
            }
        }
        return 0;
    }

    function argUrd($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Urd : ${actplayer} may place one card from their discard pile on top of their deck');
        $ret['titleyou'] = clienttranslate('Urd : ${you} may place one card from your discard pile on top of your deck'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['pickcards'] = array();
       
        $sql = "SELECT * from deck".mythicbattlesragnarok::$instance->getActivePlayer()->player_no." where card_location = 'discard'";
        $hand = self::getCollectionFromDb( $sql );
        foreach($hand as $card)
        {       
            $ret['selectable']['card'.$card['card_id']] = array();
            $ret['pickcards'][] = $card; 
        }
        return $ret;
    }
    
    function Urd($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $card_id = str_replace("card","", $varg1);
            $player = mythicbattlesragnarok::$instance->getActivePlayer();
            $card = self::getObjectFromDB( "SELECT * FROM deck".$player->player_no." where card_id = ".$card_id);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "discard", clienttranslate('${player_name} retrieves ${unitid_display}'), array(
                'player_name' => $player->player_name,
                'player_id' => $player->player_id,
                'unitid_display' => $card['card_type_arg'],
                'card' => $card
            ) );

            $player->getDeck()->insertCardOnExtremePosition( $card_id, 'deck', true);
            $player->refreshCardsCounter();
        }
    }

    function argVerdandi($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Verdandi : ${actplayer} may look at a deck top card');
        $ret['titleyou'] = clienttranslate('Verdandi : ${you} may look at a deck top card'); 
        $ret['selectable']['butmine'] = array("title" => clienttranslate("My deck"));
        $ret['selectable']['butother'] = array("title" => clienttranslate("Opponent deck"));
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
  
        return $ret;
    }
    
    function Verdandi($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            $player = $this->player;
            if($varg1 == "butother")
            {
                $player = $this->player->getOtherPlayer();
            }
            
            $card = $player->getDeck()->getCardOnTop("deck");
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Verdandi2", $card['id'], $player->player_id);
            mythicbattlesragnarok::$instance->resetUndo();
        }
    }

    function argVerdandi2($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Verdandi : ${actplayer} look at the top card of ${player_name} discard');
        $ret['titleyou'] = clienttranslate('Verdandi : ${you} look at the top card of ${player_name} discard'); 
        $ret['selectable']['butmine'] = array("title" => clienttranslate("OK"));
        $ret['selectable']['fake'] = array();
        $player = mythicbattlesragnarok::$instance->playersMBR[$parg2];
        $ret['player_name'] =  $player->player_name;
        $ret['player_id'] =  $player->player_id;

        $ret['pickcards'] = array();
        $sql = "SELECT * from deck".$player->player_no." where card_id = ".$parg1;
        $card = mythicbattlesragnarok::$instance->getObjectFromDb( $sql );
        $ret['pickcards'][] = $card; 

        return $ret;
    }

    
    function Verdandi2($parg1, $parg2, $varg1, $varg2) { 
        //nothing to do
    }
}