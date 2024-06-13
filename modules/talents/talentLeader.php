<?php 

class talentLeader extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Leader');
        $this->description = clienttranslate('At the end of their activation phase, the active player can activate a visible allied troop unit in their surroundings without spending an AoW card. If the active player or their teammate doesn’t have the activation card in their hand, they can take it from the unit owners deck, show it, play it, discard it in the unit owners discard pile, and reshuffle the deck.');
    }

    public function onTiming($time, $attack)
    {
        if($time == ENDACTIVATION && $this->unit->canUse($this) && $attack->from == $this->unit)
        {
            $alreadyActivated = mythicbattlesragnarok::getUniqueValueFromDB("select count(*) from unit where statusTurn like '%activated%'"); 
            if($alreadyActivated < 2)
            {
                mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "talentLeader.leader");
            }
        }
    }

    function argleader($parg1, $parg2, $varg1, $varg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Leader : ${actplayer} can activate an allied troop');
        $ret['titleyou'] = clienttranslate('Leader : ${you} can activate an allied troop'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");

        $units = $this->unit->zone->getUnitsWithin(0, 1,$this->unit->player->player_id,false, true);
        foreach($units as $unit)
        {
            if($this->unit->canSee($unit) && $unit->canActivate() && $unit->category == TROOP)
            {
                $nb = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from deck".$this->unit->player->player_no." where (card_location = 'hand' or card_location = 'deck') and card_type_arg=".$this->unit->id);
                if($nb>0)
                {
                    $ret['selectable']['unit'.$unit->id] = array( 'confirm' => clienttranslate('Do you want to activate ${unitid_display}?'));
                }
            }
        }
        return $ret;
    }

    function leader($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {
            $unit_id = str_replace("unit","", $varg1);
            mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "talentLeader.wherefrom", $unit_id);
        }
    }

    function argwherefrom($parg1, $parg2, $varg1, $varg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Leader : ${actplayer} may choose to use activation card from deck');
        $ret['titleyou'] = clienttranslate('Leader : ${you}  may choose to use activation card from deck'); 
      
        $unit_id = $parg1;
        $nbdeck = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from deck".$this->unit->player->player_no." where card_location = 'deck' and card_type_arg=".$unit_id);
        $nbhand = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from deck".$this->unit->player->player_no." where card_location = 'hand' and card_type_arg=".$unit_id);
        
        if($nbdeck>0 && $nbhand==0)
        {
            $ret['selectable']['butdeck'] = array("title" => clienttranslate("Deck"));
        } 
        if($nbhand>0)
        {
            $ret['selectable']['buthand'] = array("title" => clienttranslate("Hand"), "color"=>"gray");
        }    
        return $ret;
    }

    function wherefrom($parg1, $parg2, $varg1, $varg2) { 
        $unit_id = $parg1;  
       if($varg1 != null)
       {     
            if($varg1 == "butdeck")
            {
                $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->unit->player->player_no." where card_location = 'deck' and card_type_arg = '".$unit_id."' limit 1");
                $this->unit->player->discard($card_id);
                $this->unit->player->getDeck()->shuffle('deck');
            }
            else{
                $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->unit->player->player_no." where card_location = 'hand' and card_type_arg = '".$unit_id."' limit 1");
                $this->unit->player->discard($card_id);
            }
            $this->unit->player->refreshCardsCounter();
            
            mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$unit_id, "T2A2_ActionTypes");
        }
        else{
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} has no activation card for ${unitid_display} in hand or deck'), array(
                'player_id' => $this->unit->player->id,
                'player_name' => $this->unit->player->player_name,
                'unitid_display' => $unit_id
            ) );
        }
    }

}