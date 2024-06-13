<?php

class attachment6 extends attachment
{
    public function __construct()
    {
        $this->title = clienttranslate("Wiglaf");
        $this->description = clienttranslate("You may remove Wiglaf from the game when this troop is destroyed. Instead it returns to full <i>vitality</i>.");
     }

     public function onTiming($time, $attack=NULL)
     {
         parent::onTiming($time, $attack);
         if($time == BEFOREDIE && $attack->to == $this->unit)
         {
             mythicbattlesragnarok::$instance->addPending($this->unit->player_id,$this->unit->id, "attachment.wiglaf");
         }
     }
 
     function argwiglaf($parg1, $parg2)
     {
         $ret = array();
         $ret['selectable'] = array();
         $ret['title'] = clienttranslate('Wiglaf : ${unitid_display} may remain in play');
         $ret['titleyou'] = clienttranslate('Wiglaf : ${unitid_display} may remain in play'); 
         $ret['unitid_display'] = $this->unit->id;
       
         $ret['selectable']['butsurvive'] = array("title" => clienttranslate("Survive"));         
         $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
         return $ret;
     }
     
     function wiglaf($parg1, $parg2, $varg1, $varg2) { 
         if($varg1 != "butskip")
         {
             $this->unit->hp = count($this->unit->stats);
             mythicbattlesragnarok::DbQuery("update unit set attachment = 0, hp = ".$this->unit->hp." where id = ".$this->unit->id);
             mythicbattlesragnarok::$instance->notifyAllPlayers( "wounds", clienttranslate('${unitid_display} uses ${talent}'), array(
                 'i18n' => array( 'talent'),
                 'unitid_display' => $this->unit->id,
                 'talent' => "Wiglaf",
                 "wounds" => 0,
                 'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$this->unit->id),
                 "diff" => 0,
                 "maxhp" => count($this->unit->stats)
             ) );
             mythicbattlesragnarok::DbQuery( "delete from pending where function = 'die' and unit_id=".$this->unit->id);    
             mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                'id' => 'attachment'.$this->id
            ) );                      
 
         }
     }
}
