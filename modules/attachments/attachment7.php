<?php

class attachment7 extends attachment
{
    public function __construct()
    {
        $this->title = clienttranslate("Brokk & Eitri");
        $this->description = clienttranslate("Ennemy units cannot redirect attacks declared by this troop.");
     }

     public function getAuraStatus($to)
     {
         $ret = parent::getAuraStatus($to);
         if($to == $this->unit)
         {
             $ret[] = "noredirect";
         }
         return $ret;
     }
}
