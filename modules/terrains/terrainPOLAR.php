<?php

class terrainPOLAR extends terrain
{
    public function __construct()
    {
     $this->title = clienttranslate("Polar");
     $this->description = clienttranslate("Movement allowed – obstacle – units starting their activation in this area suffer 1 wound. Only one simple or complex action is possible. Active and offensive powers are ignored for the current turn.");
    }

    public function isObstacle()
    {
        return true;
    }
   
    public function onTiming($time, $attack)
    {
        if($time == STARTACTIVATION && $this->countAsFor($attack->from) == $this->type && $this == $attack->from->zone && !in_array("ignorePolar", $attack->from->status))
        {
            $unit = $attack->from;
            $unit->wound(1, clienttranslate('Polar'));
            $unit->status[] = "noACTIVE";
            $unit->status[] = "noOFFENSIVE";
            $unit->status[] = "oneSimple";
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' noACTIVE noOFFENSIVE oneSimple' ) where id = ".$unit->id);


            
            if(mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'T2C_PickActionSimple'")>1)
            {
                $id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT max(id) from pending where function = 'T2C_PickActionSimple'");
                $sql = "delete from pending where id=".$id;
                mythicbattlesragnarok::DbQuery( $sql);
            }
        }
    }
}