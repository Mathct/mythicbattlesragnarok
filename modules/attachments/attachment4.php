<?php

class attachment4 extends attachment
{
    public function __construct()
    {
        $this->title = clienttranslate("Modgud");
        $this->description = clienttranslate("This troop gains the Block talent");
     }

    public function onTiming($time, $attack = NULL)
    {
        if($time == STARTGAME && !in_array("talentBlock", $this->unit->status))
        {            
            $this->unit->status[] = "talentBlock";
            mythicbattlesragnarok::DbQuery("update unit set statusGame = concat(statusGame, ' talentBlock' ) where id = ".$this->unit->id);
        }
    }

}
