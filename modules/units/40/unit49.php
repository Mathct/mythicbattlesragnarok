<?php 

class unit49 extends unit
{
    public $category = MONSTER;
    public $cost = 4;
    public $activation = 3;
    public $aow = 1;
    public $talentsNames = array("ForceOfNature","GodSlayer","Scout");
    public $traitname = "Boreal";

    public $stats =  [
        [8, 8, 1, 2,1,5,0],
        [8, 8, 1, 2,1,5,0],
        [7, 7, 1, 1,1,5,0],
        [7, 7, 1, 1,1,4,0],
        [6, 7, 0, 1,1,4,0],
        [6, 6, 0, 1,1,3,0],
        [6, 6, 0, 0,1,3,0],
        [6, 6, 0, 0,1,0,0] 
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Hrym");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Ice Lord"), clienttranslate('When recruited, take Hrym\'s tokens. At the end of his activation, you may place a Hrym token in his area. That area is now considered Polar terrain and loses any of its 3D elements. These tokens remain in play until the end of the game.'),1,3);
         $this->powers[1] = new power(1,OFFENSIVE, WHITE, clienttranslate("Sweep"), clienttranslate('<div class="mbr_desc"><div class="mbr_offense"></div><div class="mbr_white">X</div></div>Make an X dice area attack in Hrym\'s area and one adjacent area.'),1);
    }

    public function activatePower($index)
    {
        if($index == 1)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 1, 1, $this->getStat(POWER2));  
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zoneattack", 0, 0, $this->getStat(POWER2));
        }
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == ENDACTIVATION && $this->canUse($this->powers[0]) && $attack->from == $this  && mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type = 'Hrym'")<3 )
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id, $this->id, "Ice");
        }
    }

    
    function argIce($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Ice Lord : ${actplayer} may place a Hrym\'s token in his area');
        $ret['titleyou'] = clienttranslate('Ice Lord : ${you} may place a Hrym\'s token in his area');  
        if($this->player->getAowInHand()>0 && mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type = 'Hrym'")<3)
        {
            $ret['selectable']['butplace'] = array("title" => clienttranslate("Place"));
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
      
        
        return $ret;
    }

    function Ice($parg1, $parg2, $varg1, $varg2) {     
        if($varg1 != "butskip")
        {            
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "DiscardAOW");     

            self::DbQuery( "INSERT INTO token (type, location) VALUES ( 'Hrym', 'zone".$this->zone->id."')");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "drop", clienttranslate('${unitid_display} uses ${talent}'), array(
                'i18n' => array( 'talent'),
                'unitid_display' => $this->id,
                "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") ,
                "unitId" => 'fake',
                "talent" => $this->powers[0]->title
            ) );

            foreach(mythicbattlesragnarok::getObjectListFromDB( "SELECT id from token where (type ='stele' or type = 'tree') and location = 'zone".$this->zone->id."'", true) as $token_id)
            {
                mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                    'id' => 'token'.$token_id
                ));
                mythicbattlesragnarok::DbQuery( "delete from token where id = ".$token_id);            
            }
        }
    }

}