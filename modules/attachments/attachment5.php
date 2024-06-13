<?php

class attachment5 extends attachment
{
    public function __construct()
    {
        $this->title = clienttranslate("Eric Bloodaxe");
        $this->description = clienttranslate('This troop may gain <div class="mbr_desc"><div class="mbr_offense"></div>+2</div> this turn, but then suffers 1 automatic wound at the end of this unit\'s activation.');
     }

     public function onTiming($time, $attack = NULL)
     {
         if($time == STARTACTIVATION && $attack->from == $this->unit)
         {            
            mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "attachment.bloodaxe");
         }
         if($time == ENDACTIVATION && $attack->from == $this->unit && in_array("bloodaxe", $this->unit->status))
         { 
            mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "pendWound",1,"Eric Bloodaxe");
         }
     }

     function argbloodaxe($parg1, $parg2)
     {
         $ret = array();
         $ret['selectable'] = array();
         $ret['title'] = clienttranslate('Eric Bloodaxe : ${unitid_display} may gain <div class="mbr_offense"></div>+2');
         $ret['titleyou'] = clienttranslate('Eric Bloodaxe : ${you} may gain <div class="mbr_offense"></div>+2'); 
         $ret['selectable']['butuse'] = array("title" => clienttranslate("Use"));
         $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray"); 
         $ret['unitid_display'] = $this->unit->id;
         return $ret;
     }
 
     
     function bloodaxe($parg1, $parg2, $varg1, $varg2) { 
         if($varg1 != "butskip")
         {
            $this->unit->status[] = "bloodaxe";
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' bloodaxe' ) where id = ".$this->unit->id);
         }
     }

     public function getStatBonus($stat, $to, $attack)
     {
        if($stat == OFFENSE && $to == $this->unit && in_array("bloodaxe", $this->unit->status))
        {            
            return 2;           
        }
     }
}
