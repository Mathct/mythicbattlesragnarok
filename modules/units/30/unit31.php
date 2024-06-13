<?php 

class unit31 extends unit
{
    public $category = HERO;
    public $cost = 3;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("Bolster","CloseProtection", "Leader");

    public $stats =  [
        [6, 7, 0, 2,1,1,0],
        [6, 7, 0, 1,1,1,0],
        [6, 7, 0, 1,1,1,0],
        [6, 7, 0, 1,1,1,0],
        [5, 6, 0, 0,1,1,0],
        [5, 5, 0, 0,1,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Harald Hardrada");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Landeythan Banner"), clienttranslate('When an allied troop unit is in Harald\'s area at the end of his activation, it regains 1 vitality point.'));
         $this->powers[1] = new power(1,PASSIVE, WHITE, clienttranslate("The Merciless"), clienttranslate('When Harald is the target of an attack, he can redirect it to an allied troop in his area.'));
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == ENDACTIVATION && $this->canUse($this->powers[0]) && $attack->from == $this)
        {
            foreach($this->zone->units as $unit)
            {
                if($unit->category == TROOP && $unit->player_id == $this->player_id)
                {
                    $unit->heal(1);
                }
            }
        }
        if($time == AFTERSELECTINGTARGET && $this->canUse($this->powers[1]) && $attack->to == $this && !in_array("noredirect", $attack->from->status))
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Merciless", $attack->toJSON());           
        }        
    } 

    
    function argMerciless($parg1, $parg2, $varg1, $varg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('The Merciless : ${unitid_display} can redirect attack on an allied troop');
        $ret['titleyou'] = clienttranslate('The Merciless : ${unitid_display} can redirect attack on an allied troop');  
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $this->id;

        foreach($this->zone->units as $unit)
        {
            if($unit != $this && $unit != $attack->from && $unit->player_id == $this->player_id && $unit->category == TROOP)
            {
                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Do you want to redirect the attack on ${unitid_display}?'
                );
            }
        }
        return $ret;
    }

    function Merciless($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {
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

}