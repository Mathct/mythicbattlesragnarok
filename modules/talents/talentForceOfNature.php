<?php 

class talentForceOfNature extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Force Of Nature');
        $this->description = clienttranslate('Before selecting the target of a normal or an area attack, if the unit with the Force of Nature talent is in an area with at least one 3D element, they can remove one of these elements from the board to gain +1 offense and +1 range until the end of their current activation.');
    }

    public function onTiming($time, $attack)
    {
        if($time == BEFORESELECTINGTARGET && $this->unit->canUse($this) && $attack->from == $this->unit)
        {
            mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "talentForceOfNature.forceOfNature", $attack->toJSON());
        }
    }

    function argforceOfNature($parg1, $parg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Force of nature : ${unitid_display} can remove an element to gain +1 <div class="mbr_offense"></div> and +1 <div class="mbr_range"></div>');
        $ret['titleyou'] = clienttranslate('Force of nature : ${unitid_display} can remove an element to gain +1 <div class="mbr_offense"></div> and +1 <div class="mbr_range"></div>'); 
        $ret['unitid_display'] = $this->unit->id;
        $nb = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where (type ='stele' or type = 'tree') and location = 'zone".$this->unit->zone->id."'");
        if($nb>0)
        {
            $ret['selectable']['butforce'] = array("title" => clienttranslate("Force of nature"));
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");

        return $ret;
    }

    function forceOfNature($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {
            $token_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT id from token where (type ='stele' or type = 'tree') and location = 'zone".$this->unit->zone->id."' limit 1");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", clienttranslate('${unitid_display} uses ${talent}'), array(
                'i18n' => array( 'talent'),
                'unitid_display' => $this->unit->id,
                'id' => 'token'.$token_id,
                "talent" => $this->name
            ) );
            
            mythicbattlesragnarok::DbQuery( "delete from token where id = ".$token_id);

            $this->unit->status[] = "offensep1";
            $this->unit->status[] = "rangep1";
            mythicbattlesragnarok::DbQuery("update unit set statusActivation = concat(statusActivation, ' offensep1 rangep1' ) where id = ".$this->unit->id);
        }
    }
    
}