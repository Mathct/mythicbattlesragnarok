<?php 

class talentAquatic extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Aquatic');
        $this->description = clienttranslate('Aquatic units gain +1 movement for walk or run actions if they start in, and only enter, Aquatic areas during that action. Aquatic units ignore all Aquatic terrain effects (Water, etc.).');
        $this->trait = true;
    }

    public function getAuraStatus($to)
    {
        $ret = parent::getAuraStatus($to);
        if($to == $this->unit)
        {
            $ret[] = "ignoreWater";
        }
        return $ret;
    }

    public function onTiming($time, $attack)
    {
        if($time == BEFORESELECTINGTARGET && $attack->from == $this->unit)
        {
            mythicbattlesragnarok::$instance->DbQuery("update unit set statusTurn = replace(statusTurn, ' forcewater', '')" );
        }
    }

    public function getExtraActions($simple)
    {
        $ret = parent::getExtraActions($simple);
        if($this->unit->zone->countAsFor($this->unit) == WATER && (($this->unit->canWalk() && $simple) || ($this->unit->canRun() && !$simple)))
        {
            $ret[] = array("title"=>"Aquatic", "function" => "talentAquatic_Aquatic");
        }
        return $ret;
    }

    function Aquatic($parg1, $parg2, $varg1, $varg2) {        
        
        mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} uses ${talent}'), array(
            'i18n' => array( 'talent'),
            'player_name' => $this->unit->player->player_name,
            'player_id' => $this->unit->player_id,
            'talent' => $this->name
            ) );

            $move =  $this->unit->getEffectiveStat(MOVEMENT);
            $move['Aquatic'] = 1;
            $move['total']++;
            $status = '';
            if(!in_array("complex", $this->unit->status))
            {
                mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} walks in water up to ${movement}'), array(
                    'unitid_display' => $this->unit->id,
                    'movement' => $move
                ) );
                $status = "walked";
            }
            else{
                mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} runs in water up to ${movement}'), array(
                    'unitid_display' => $this->unit->id,
                    'movement' => $move
                ) );
                $status = "run";
                $move['run'] = 1;
                $move['total']++;
            }

            $this->unit->status[] = "forcewater";
            $this->unit->status[] = $status;
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' forcewater ".$status."' ) where id = ".$this->unit->id);
            for ($i = 0; $i < $move['total']; $i++) {
                mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "move");
            }
            
    }
}