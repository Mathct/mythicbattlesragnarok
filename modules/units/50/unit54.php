<?php 

class unit54 extends unit
{
    public $category = MONSTER;
    public $cost = 4;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("Block","Climb","ForceOfNature");

    public $stats =  [
        [8, 8, 0, 1,1,1,1],
        [8, 8, 0, 1,1,1,1],
        [7, 8, 0, 1,1,1,1],
        [7, 7, 0, 1,1,1,1],
        [7, 7, 0, 1,1,1,1],
        [7, 7, 0, 1,1,1,1],
        [6, 6, 0, 1,1,1,1],
        [6, 6, 0, 0,1,0,0],
        [6, 6, 0, 0,1,0,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Troll");
         $this->powers[0] = new power(0,PERMANENT, BLACK, clienttranslate("Earthbound"), clienttranslate('When recruited, take the Troll token. The two sides of the token represent its PEBBLE and MOUNTAIN powers. The Troll chooses which side is active when it is deployed. At the beginning of its activation, it can change which side is active.'),1,1);
         $this->powers[1] = new power(1,PASSIVE, WHITE, clienttranslate("Pebble"), clienttranslate('The Troll gains +1 movement and the Gem Collector talent. It can cross areas containing enemy units.'));
         $this->powers[2] = new power(2,PASSIVE, BLACK, clienttranslate("Mountain"), clienttranslate('The Troll gains +1 range and the Mighty Throw talent. When it uses Force of Nature, it can choose to not remove the 3D elements.'));
    }

    public function isPebble()
    {
        return mythicbattlesragnarok::getUniqueValueFromDB( "select count(*) from token where type='pebble'")>0;
    }

    public function getEffectiveStat($stat, $attack = NULL)
    {
        $ret = parent::getEffectiveStat($stat, $attack);
        if($this->canUse($this->powers[1]) && $stat == MOVEMENT && $this->isPebble())
        {
            $ret[$this->powers[1]->title] = 1;
            $ret['total'] ++;
        }
        if($this->canUse($this->powers[2]) && $stat == RANGE && $attack->from == $this && !$this->isPebble())
        {
            $ret[$this->powers[2]->title] = 1;
            $ret['total']++;
        }
        return $ret;
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == STARTACTIVATION && $this->canUse($this->powers[0]) && $attack->from == $this && $this->player->getAowInHand()>0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Setup");
        }
    }   

    function argSetup($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Earthbound : ${actplayer} must choose the side for Troll\'s token');
        $ret['titleyou'] = clienttranslate('Earthbound : ${you} must choose the side for Troll\'s token'); 
        
        $token = mythicbattlesragnarok::getObjectFromDB( "SELECT * from token where type='pebble' or type='mountain'");
        if($token != null)
        {
            $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        }
        if($token == null || $this->player->getAowInHand()>0)
        {
            $ret['selectable']['butpebble'] = array("title" => clienttranslate("Pebble"));
            $ret['selectable']['butmountain'] = array("title" => clienttranslate("Mountain")); 
        }
        return $ret;
    }

    
    function Setup($parg1, $parg2, $varg1, $varg2) { 
        
        if($varg1 != null && $varg1 != "butskip")
        {
            $side = str_replace("but","", $varg1);

            $token = mythicbattlesragnarok::getObjectFromDB( "SELECT * from token where type='pebble' or type='mountain'");

            if($token == null)
            {
                self::DbQuery( "INSERT INTO token (type, location) VALUES ( '$side', 'dashboard".$this->id."')");
                mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                    "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") 
                ) );
            }
            else if($token['type'] != $side)
            {            
                
                mythicbattlesragnarok::$instance->addPending($this->player_id,0, "DiscardAOW");       

                $token['type'] = $side;
                self::DbQuery( "update token set type = '".$side."' where id = ".$token['id']);
                mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                    "token" => $token
                ) );
            }

            if($side == "pebble")
            {
                $this->status[] = "talentGemCollector"; 
                mythicbattlesragnarok::DbQuery("update unit set statusGame = concat(statusGame, ' talentGemCollector' ) where id = ".$this->id); 

                $this->status = array_filter($this->status, function ($value) {
                    return $value != "talentMightyThrow"; 
                });
                mythicbattlesragnarok::$instance->DbQuery("update unit set statusGame = replace(statusGame, ' talentMightyThrow', '') where id = ".$this->id);
            
            }
            else{

                $this->status[] = "talentMightyThrow"; 
                mythicbattlesragnarok::DbQuery("update unit set statusGame = concat(statusGame, ' talentMightyThrow' ) where id = ".$this->id); 

                $this->status = array_filter($this->status, function ($value) {
                    return $value != "talentGemCollector"; 
                });
                mythicbattlesragnarok::$instance->DbQuery("update unit set statusGame = replace(statusGame, ' talentGemCollector', '') where id = ".$this->id);
            }
        }

    } 

    function IsStoppedMoving($other)
    {
        return parent::IsStoppedMoving($other) && !($this->canUse($this->powers[1] && $this->isPebble()));
    }

    function argForceOfNature($parg1, $parg2) { 
       
        $ret = $this->talents["ForceOfNature"]->argForceOfNature($parg1, $parg2);
       
        $nb = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where (type ='stele' or type = 'tree') and location = 'zone".$this->zone->id."'");
        if($nb>0)
        {
            $ret['selectable']['butforcewithout'] = array("title" => clienttranslate("Force of nature (without removing)"));
        } 
        return $ret;
    }

    function ForceOfNature($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 == "butforcewithout")
        {
            $this->status[] = "offensep1";
            $this->status[] = "rangep1";
            mythicbattlesragnarok::DbQuery("update unit set statusActivation = concat(statusActivation, ' offensep1 rangep1' ) where id = ".$this->id);
        }
        else{
            $this->talents["ForceOfNature"]->ForceOfNature($parg1, $parg2, $varg1, $varg2);
        }
    }

}