<?php 

class unit33 extends unit
{
    public $category = HERO;
    public $cost = 3;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("CloseProtection","Guard","Leader");

    public $stats =  [
        [6, 7, 0, 2,1,1,0],
        [6, 7, 0, 2,1,1,0],
        [5, 7, 0, 1,1,1,0],
        [5, 6, 0, 1,1,1,0],
        [4, 6, 0, 1,1,1,0],
        [4, 6, 0, 0,1,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Lagertha");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Shield Wall"), clienttranslate('All allied units except Lagertha in Lagertha\'s area gain +1 defense.'));
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Block"), clienttranslate('Whenever Lagertha is the target of an attack, immediately after the first assault and before any other effects, you can make the attacking unit re-roll up to 2 dice of your choosing.'));
    }

    public function getStatBonus($stat, $to, $attack)
    {
        $ret = parent::getStatBonus($stat, $to, $attack);        
        if($this->canUse($this->powers[0]) && $stat == DEFENSE && $to == $attack->to  && $attack->to->zone == $this->zone && $attack->to->player_id == $this->player_id && $attack->to != $this)
        {
            $ret++;
        }
        return $ret;
    }

    
    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTER1STROLL && $this->canUse($this->powers[1]) && $attack->to == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "block", $attack->toJSON());
        }
        if($time == RECRUIT && $attack == $this)
        {            
            $id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT id from unit where type=34");
            self::DbQuery( "DELETE FROM unit WHERE type=34");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                'id' => 'unit'.$id
            ) );
        }
        
    }

    function argblock($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Block : ${actplayer} may force opponent to reroll up to 2 selected dices');
        $ret['titleyou'] = clienttranslate('Block : ${you} may force opponent to reroll up to 2 selected dices'); 
        $ret['multiple'] = 2; 
        $attack = attack::fromJSON($parg1);       
        if(!in_array("noreroll", $attack->to->status))
        {
            $ret['selectable']['butchange'] = array("title" => clienttranslate("Reroll"));
            $dices = self::getCollectionFromDb( "SELECT * FROM die"); 
            foreach($dices as $die)
            {            
                $ret['selectable']['diceres'.$die['id']] = array();                                 
            } 
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }
    
    function block($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            foreach(array_filter(explode(" ",$varg2)) as $arg)
            { 
                $toimproveid = str_replace("diceres","", $arg);
                $face = bga_rand( 0, 5 );
                mythicbattlesragnarok::DbQuery( "update die set value = ".$face.", face = ".$face." where id=".$toimproveid);
                mythicbattlesragnarok::$instance->notifyAllPlayers( "setDice", '', array(
                    'die' => self::getObjectFromDB( "SELECT* FROM die where id = ".$toimproveid)
                ) );
                mythicbattlesragnarok::$instance->resetUndo();
            }
        }
    }

}