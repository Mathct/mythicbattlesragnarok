<?php 

class unit46 extends unit
{
    public $category = MONSTER;
    public $cost = 3;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("GemCollector","SneakAttack","Terror");
    public $traitname = "Aquatic";

    public $stats =  [
        [6, 7, 0, 1,1,1,0],
        [6, 7, 0, 1,1,1,0],
        [6, 7, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [5, 6, 0, 1,1,1,0],
        [5, 6, 0, 0,1,1,0],
        [4, 5, 0, 0,0,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Grendel's Mother");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Sneaky Crone"), clienttranslate('When Grendel\'s Mother is the target of an attack, she can redirect it to an allied unit in her area.'),1);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Curse"), clienttranslate('When recruited, take the Grendel\'s Mother token. When a unit destroys Grendel\'s Mother, place the token on one of the unit\'s powers. The chosen power can no longer be used until the end of the game.'),0,1);
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTERSELECTINGTARGET && $this->canUse($this->powers[1]) && $attack->to == $this && !in_array("noredirect", $attack->from->status))
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Sneaky", $attack->toJSON());           
        }    
        if($time == BEFOREDIE && $attack->to == $this && $attack->from != null)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Curse", $attack->toJSON()); 
        }    
    }

    function argSneaky($parg1, $parg2, $varg1, $varg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Sneaky Crone : ${unitid_display} can redirect attack on an allied unit');
        $ret['titleyou'] = clienttranslate('Sneaky Crone : ${unitid_display} can redirect attack on an allied unit');  
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $this->id;

        if($this->player->getAowInHand()>0)
        {
            foreach($this->zone->units as $unit)
            {
                if($unit != $this && $unit->player_id == $this->player_id &&  $unit != $attack->from)
                {
                    $ret['selectable']['unit'.$unit->id] = array(
                        "confirm" => 'Do you want to redirect the attack on ${unitid_display}?'
                    );
                }
            }
        }
        return $ret;
    }

    function Sneaky($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {
            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type <= 0 limit 1");
            $this->player->discard($card_id); 

            $unitid = str_replace("unit","", $varg1);
            $unit =  mythicbattlesragnarok::$instance->units[$unitid];
            $attack = attack::fromJSON($parg1);
            $attack->to = $unit;
            $json = $attack->toJSON();
            $sql = "update pending set arg = '".$json."' where arg='".$parg1."'";
            mythicbattlesragnarok::DbQuery( $sql);

            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} redirects attack on ${unitid_display2}'), array(
                'unitid_display' => $this->id,
                'unitid_display2' => $unit->id
            ) );
        }
    }

    function argCurse($parg1, $parg2, $varg1, $varg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Curse : ${actplayer} can block one power of ${unitid_display}');
        $ret['titleyou'] = clienttranslate('Curse : ${you} can block one power of ${unitid_display}');  
        $ret['unitid_display'] = $attack->from->id;
        
        foreach($attack->from->powers as $power)
        {            
            $ret['selectable']['butpower'.$power->index] = array("title" => $power->title);            
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        
        return $ret;
    }

    function Curse($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {

            $attack = attack::fromJSON($parg1);
            $index = str_replace("butpower","", $varg1);
            $power = $attack->from->powers[$index];

            $attack->from->status[] = 'nopower'.$index;
            mythicbattlesragnarok::DbQuery("update unit set statusGame = concat(statusGame, ' nopower".$index."' ) where id = ".$attack->from->id);
            self::DbQuery( "INSERT INTO token (type, location) VALUES ( 'Grendel', 'dashboard".$attack->from->id."')");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1")
            ) );

            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} cannot use ${talent} anymore'), array(
                'i18n' => array( 'talent'),
                'unitid_display' => $attack->from->id,
                'talent' => $power->title
            ) );
        }
    }


}