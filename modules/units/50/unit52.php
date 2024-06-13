<?php 

class unit52 extends unit
{
    public $category = MONSTER;
    public $cost = 2;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("Climb","GemCollector","Mobility");

    public $stats =  [
        [4, 8, 0, 2,1,1,1],
        [4, 7, 0, 2,1,1,1],
        [4, 7, 0, 2,1,1,1],
        [3, 6, 0, 2,1,1,1],
        [3, 6, 0, 1,0,0,1]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Ratatosk");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Sowing Discord"), clienttranslate('It is impossible to target Ratatosk if another targetable unit is in his area, including for a beneficial effect.'));
         $this->powers[1] = new power(1,ACTIVE, WHITE, clienttranslate("Stirring Up Bitterness"), clienttranslate('At the beginning of Ratatosk\'s activation, choose an opponent. They must show you an activation card in their hand, which they must then use on their next turn or allow you to draw a card.'));
         $this->powers[2] = new power(2,PERMANENT, BLACK, clienttranslate("Crafty"), clienttranslate('Ratatosk can never suffer more than 2 wounds per attack.'));
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == STARTACTIVATION && $this->canUse($this->powers[1]) && $this == $attack->from)
        {
            mythicbattlesragnarok::$instance->addPending($this->player->getOtherPlayer()->player_id,$this->id, "Stirring");
        }
    } 

    function argStirring($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('Stirring Up Bitterness : ${actplayer} must show you an activation card or let you draw');
        $ret['titleyou'] = clienttranslate('Stirring Up Bitterness : ${you} must show your opponent an activation card or let it draw'); 
       
        $sql = "SELECT * from deck".$this->player->player_no." where card_location = 'hand' and card_type>0";
        $hand = self::getCollectionFromDb( $sql );
        foreach($hand as $card)
        {            
            $ret['selectable']['card'.$card['card_id']] = array();                      
        } 
        $ret['selectable']['butdraw'] = array("title" => clienttranslate("Let it draw"));
        return $ret;
    }
    
    function Stirring($parg1, $parg2, $varg1, $varg2)   
    {        
        if($varg1 == "butdraw")
        {
            mythicbattlesragnarok::$instance->addPending($this->player->getOtherPlayer()->player_id,0, "draw");
        }
        else
        {
            $card_id = str_replace("card","",$varg1);
            $card = self::getObjectFromDB( "SELECT * FROM deck".$this->player->player_no." where card_id = ".$card_id);
            $card['card_id'] = 'tmp'.$card['card_id'];
            mythicbattlesragnarok::$instance->notifyAllPlayers( "discard", clienttranslate('${player_name} shows you ${unitid_display} and will be force to use it next turn'), array(
                'player_name' => $this->player->player_name,
                'player_id' => $this->player_id,
                'unitid_display' => $card['card_type_arg'],
                'card' => $card
            ) );
            $this->player->status[] = 'forceuse'.$card_id;
            mythicbattlesragnarok::DbQuery("update player set endofturnstatus = concat(endofturnstatus, ' forceuse".$card_id."' ) where player_id = ".$this->player->player_id);
        }
    }

    public function wound($wounds, $cause, $arrayDetails = NULL)
    {
        if($this->canUse($this->powers[2]))
        {
            $wounds = min($wounds,2);
        }
        parent::wound($wounds, $cause, $arrayDetails);
    }

}