<?php 

class talentGuard extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Guard');
        $this->description = clienttranslate('After the choice of the target of a normal or an area attack by an enemy, if the unit with the Guard talent is in the same area as the targeted allied unit, they become the new target. This talent cannot be used against a retaliation or against terrain effects.');
    }
    
    public function onTiming($time, $attack)
    {
        if($time == AFTERSELECTINGTARGET && $this->unit->canUse($this) && $attack->to != $this->unit && $attack->to->zone == $this->unit->zone && $attack->to->player_id == $this->unit->player_id && !in_array("noredirect", $attack->from->status))
        {
            mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "talentGuard.guard", $attack->toJSON());
        }
    }

    function argguard($parg1, $parg2, $varg1, $varg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Guard : ${actplayer} can redirect attack on ${unitid_display} onto ${unitid_display2}');
        $ret['titleyou'] = clienttranslate('Guard : ${you} can redirect attack on ${unitid_display} onto ${unitid_display2}'); 
        $ret['selectable']['butredirect'] = array("title" => clienttranslate("Redirect"));
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $attack->to->id;
        $ret['unitid_display2'] = $this->unit->id;
        return $ret;
    }

    function guard($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {
            $attack = attack::fromJSON($parg1);
            $attack->to = $this->unit;
            $json = $attack->toJSON();
            $sql = "update pending set arg = '".$json."' where arg='".$parg1."'";
            mythicbattlesragnarok::DbQuery( $sql);
            $sql = "delete from pending where function = 'A3_Retaliate'";
            mythicbattlesragnarok::DbQuery( $sql);

            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} redirects attack on ${unitid_display}'), array(
                'player_name' => $this->unit->player->player_name,
                'player_id' => $this->unit->player->player_id,
                'unitid_display' => $this->unit->id
            ) );
            mythicbattlesragnarok::$instance->onTiming(AFTERVALIDATINGTARGET, $attack);  
        }
    }
}