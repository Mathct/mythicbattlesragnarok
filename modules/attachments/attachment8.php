<?php

class attachment8 extends attachment
{
    public function __construct()
    {
        $this->title = clienttranslate("Thjalfi & Roskva");
        $this->description = clienttranslate("This unit can retaliate without discarding an activation card.");
     }

     
    function argA3_Retaliate($parg1 = NULL, $parg2 = NULL)
    {
        $parg2 = "nodiscard";        
        return $this->unit->argA3_Retaliate($parg1, $parg2);
    }

    function A3_Retaliate($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $parg2 = "nodiscard";
        $this->unit->A3_Retaliate($parg1, $parg2, $varg1, $varg2);
    }
}
