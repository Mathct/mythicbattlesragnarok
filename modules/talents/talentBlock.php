<?php 

class talentBlock extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Block');
        $this->description = clienttranslate('An enemy unit that is in the area of a blocker unit cannot carry out a walk, run, or ascend action. An enemy unit cannot claim a divine stone that is in the area of a unit with the Block talent, but may still absorb a divine stone if that blocked unit is a divinity. A unit with the Block talent ignores these effects.');
    }

    public function getAuraStatus($to)
    {
        $ret = array();
        if($to->zone == $this->unit->zone && $to->player_id != $this->unit->player_id && !$to->hasTalent("Block") && !in_array("noblock", $to->status))
        {
            $ret = array("nowalk","norun","noascend","noclaim");
        }
        return $ret;
    }
}