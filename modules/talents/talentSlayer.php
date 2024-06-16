<?php 

class talentSlayer extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Troop Slayer');
        $this->description = clienttranslate('During the first assault, before discarding blank results, the unit may re-roll up to 2 dice against a target of the unit type corresponding to its Slayer talent.');
        $this->slayer = TROOP;
    }

    public function onTiming($time, $attack)
    {
        if($time == AFTER1STROLL && $this->unit->canUse($this) && $attack->from == $this->unit && $attack->to->category == $this->slayer)
        {
            mythicbattlesragnarok::$instance->addPending($attack->to->player->getOtherPlayer()->id,$this->unit->id, "talentTroopSlayer.slayer", $attack->toJSON());
        }
    }

    function argslayer($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Slayer : ${actplayer} may reroll up to 2 selected dices');
        $ret['titleyou'] = clienttranslate('Slayer : ${you} may reroll up to 2 selected dices'); 
        $ret['multiple'] = 2;        
        if(!in_array("noreroll", $this->unit->status))
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
    
    function slayer($parg1, $parg2, $varg1, $varg2) { 
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
            }
            mythicbattlesragnarok::$instance->resetUndo();
        }
    }
}

class talentTitanlayer extends talentSlayer
{    
    public function __construct()
    {
        parent::__construct();
        $this->name = clienttranslate('Titan Slayer');
        $this->slayer = TITAN;
    }
}

class talentGodSlayer extends talentSlayer
{    
    public function __construct()
    {
        parent::__construct();
        $this->name = clienttranslate('God Slayer');
        $this->slayer = GOD;
    }
}

class talentHeroSlayer extends talentSlayer
{    
    public function __construct()
    {
        parent::__construct();
        $this->name = clienttranslate('Hero Slayer');
        $this->slayer = HERO;
    }
}

class talentMonsterSlayer extends talentSlayer
{    
    public function __construct()
    {
        parent::__construct();
        $this->name = clienttranslate('Monster Slayer');
        $this->slayer = MONSTER;
    }
}

class talentTroopSlayer extends talentSlayer
{    
    public function __construct()
    {
        parent::__construct();
        $this->name = clienttranslate('Troop Slayer');
        $this->slayer = TROOP;
    }
}