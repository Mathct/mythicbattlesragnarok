<?php

class attachment2 extends attachment
{
    public function __construct()
    {   
        $this->title = clienttranslate("Harald Fairhair");
        $this->description = clienttranslate("Recalling this troop does not require discarding Art of War card. May not be attached to a troop with the Guard talent.");
     }

    public function canBeAttachedTo($unit)
    {
        $ret = parent::canBeAttachedTo($unit);

        if($ret)
        {
            foreach($unit->talents as $talent)
            {
                if(($talent instanceof talentGuard))
                {
                    $ret = false;
                    break;
                }
            }
        }

        return $ret;
    }
}
