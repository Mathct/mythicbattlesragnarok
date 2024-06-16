<?php 

class unit48 extends unit
{
    public $category = MONSTER;
    public $cost = 3;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("Initiative","MightyThrow","Mobility");
    public $traitname = "Flying";

    public $stats =  [
        [7, 7, 0, 3,1,1,1],
        [6, 6, 0, 2,1,1,1],
        [6, 6, 0, 2,1,1,1],
        [5, 6, 0, 2,1,1,1],
        [5, 5, 0, 1,1,1,1],
        [5, 5, 0, 1,1,1,1],
        [4, 5, 0, 1,0,0,1]  
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Hraesvelg");
         $this->powers[0] = new power(0,ACTIVE, BLACK, clienttranslate("Violent Gust"), clienttranslate('Choose a unit in Hraesvelg\'s area and move it 1 or 2 areas away. This move ignores all obstacles.'),1);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Devour The Dead"), clienttranslate('When Hraesvelg destroys a unit, remove 1 of its activation cards from the game. If possible, take it from the discard pile, or if not then take it from the player\'s hand or from their deck, which the player then shuffles.'));
         $this->powers[2] = new power(2,PERMANENT, BLACK, clienttranslate("Freezing Wind"), clienttranslate('When Hraesvelg carries out a fast flight complex action, inflict 1 wound on all non-boreal units in the area where it takes flight.'));
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "Gust");
        }
    }

    public function onTiming($time, $attack)
    {  
        parent::onTiming($time, $attack);
        if($time == BEFOREDIE && $attack->from != null && $attack->from == $this && $this->canUse($this->powers[1]))
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Devour", $attack->to->id); 
        }   
        if($time == FASTFLIGHT && $attack == $this && $this->canUse($this->powers[2]) && !in_array("power2", $this->status))
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Freezing", $this->zone->id); 
        } 
    } 

    function argGust($parg1, $parg2, $varg1, $varg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Violent Gust : ${actplayer} may choose the unit to move');
        $ret['titleyou'] = clienttranslate('Violent Gust : ${you}  may choose the unit to move');  
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
      
        foreach($this->zone->units as $unit)
        {            
            $ret['selectable']['unit'.$unit->id] = array(
                "confirm" => 'Do you want to move ${unitid_display}?'
            );            
        }
        
        return $ret;
    }

    function Gust($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {
            $unitid = str_replace("unit","", $varg1);
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "GustMove", $unitid);
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "GustMove", $unitid);
        }
    }

    
    function argGustMove($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Violent Gust : ${actplayer} may move ${unitid_display} (${left} left)');
        $ret['titleyou'] = clienttranslate('Violent Gust : ${you} may move ${unitid_display} (${left} left)'); 
        $ret['left'] = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'GustMove' and unit_id=".$this->id." and player_id=".$this->player_id);
        $ret['unitid_display'] = $parg1;
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");

        $unit = mythicbattlesragnarok::$instance->units[$parg1];
       
            foreach($unit->zone->boundaries as $zoneid => $zonetype)
            {
                $zone = mythicbattlesragnarok::$instance->zones[$zoneid];
                if(!$zone->isFull())
                {
                    $ret['selectable']['zone'.$zoneid] = array();
                }
                
            }
        
        return $ret;
    }

    function GustMove($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {     
        if($varg1 != "butskip")
        {
            $unitid = $parg1;
            $unit = mythicbattlesragnarok::$instance->units[$unitid];
            $zoneid = str_replace("zone","", $varg1);
            $unit->move("force",NULL, $zoneid);
        }
        else{
            mythicbattlesragnarok::DbQuery( "delete from pending where function = 'GustMove' and unit_id=".$this->id." and player_id=".$this->player_id);
        }
    }

    
    function Devour($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {     
        $unitid = $parg1;
        $other = $this->player->getOtherPlayer();

        $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$other->player_no." where card_location = 'discard' and card_type_arg = ".$unitid." limit 1");
        if($card_id == null)
        {
            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$other->player_no." where card_location = 'hand' and card_type_arg = ".$unitid." limit 1");
        }
        if($card_id == null)
        {
            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$other->player_no." where card_location = 'deck' and card_type_arg = ".$unitid." limit 1");
            $other->getDeck()->shuffle( 'deck' ); 
        }
        
        if($card_id != null)
        {
            mythicbattlesragnarok::DbQuery( "delete from deck".$other->player_no." where card_id=".$card_id);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} destroys an activation card from ${unitid_display}'), array(
                'player_name' => $other->player_name,
                'player_id' => $other->player_id,
                'unitid_display' => $unitid,
            ) );
            $other->refreshCardsCounter();
        }
           
    }

    
    
    function Freezing($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {     

        foreach(mythicbattlesragnarok::$instance->zones[$parg1]->units as $unit)
        {
            if($unit != $this)
            {
                $unit->wound(1, $this);
            }
        } 
        
        $this->status[] = "power2";
        mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' power2' ) where id = ".$this->id);
    }


}