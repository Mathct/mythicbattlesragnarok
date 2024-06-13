<?php 

class talentBerserk extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Berserk');
        $this->description = clienttranslate('The unit can use its 2 simple turn actions to perform 2 normal attack actions of range 0, unmodifiable by any power or any talent. At the end of the second attack action, the unit suffers 1 automatic wound.');
    }


    public function getExtraActions($simple)
    {
        $ret = parent::getExtraActions($simple);
        $left = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'T2C_PickActionSimple'");
        if($simple && $left>=2)
        {
            $ret[] = array("title"=>"Berserk", "function" => "talentBerserk_Berserk");
        }
        return $ret;
    }


    function Berserk($parg1, $parg2, $varg1, $varg2) {        
        
        $this->unit->status[] = "rangem99";
        mythicbattlesragnarok::DbQuery("update unit set statusActivation = concat(statusActivation, ' rangem99' ) where id = ".$this->unit->id);

        mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} uses ${talent}'), array(
            'i18n' => array( 'talent'),
            'player_name' => $this->unit->player->player_name,
            'player_id' => $this->unit->player_id,
            'talent' => $this->name
            ) );

        mythicbattlesragnarok::DbQuery( "delete from pending where function = 'T2C_PickActionSimple' and unit_id=".$this->unit->id." and player_id=".$this->unit->player_id);
        mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "A1A_targetChoice"); 
        mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "A1A_targetChoice"); 
    }
}