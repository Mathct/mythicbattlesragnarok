<?php 

class unit37 extends unit
{
    public $category = HERO;
    public $cost = 3;
    public $activation = 4;
    public $aow = 1;
    public $talentsNames = array("Initiative","MonsterSlayer","Torment");

    public $stats =  [
        [7, 8, 0, 2,1,1,0],
        [7, 7, 0, 2,1,1,0],
        [7, 6, 0, 2,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [5, 6, 0, 1,1,1,0],
        [5, 5, 0, 0,1,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Sigurd");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Dragon's Blood"), clienttranslate('When Sigurd would be destroyed, he can use the DRAGON\'S BLOOD power to remain in play with 3 vitality points.'),2);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("The Ring Of Andvari"), clienttranslate('Whenever Sigurd\'s player has to roll any number of dice, including outside of his own activation, they may choose to re-roll one. If they do, Sigurd loses 1 vitality point.'));
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == BEFOREDIE && $attack->to == $this)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Dragon");
        }
        if($time == AFTER1STROLL && $this->canUse($this->powers[1]) && $attack->from->player_id == $this->player_id)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Ring", $attack->toJSON());            
        }
        if($time == AFTER2DROLL && $this->canUse($this->powers[1]) && $attack->from->player_id == $this->player_id)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "Ring", $attack->toJSON());            
        }
    }

    function argDragon($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Dragon\'s Blood : ${unitid_display} may remain in play');
        $ret['titleyou'] = clienttranslate('Dragon\'s Blood : ${unitid_display} may remain in play'); 
        $ret['unitid_display'] = $this->id;
      
        if($this->player->getAowInHand()>=2)
        {
            $ret['selectable']['butsurvive'] = array("title" => clienttranslate("Survive"));
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }
    
    function Dragon($parg1, $parg2, $varg1, $varg2) { 
        if($varg1 != "butskip")
        {
            
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "DiscardAOW");     
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "DiscardAOW");     
            $this->hp = 3;
            mythicbattlesragnarok::DbQuery("update unit set hp = 3 where id = ".$this->id);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "wounds", clienttranslate('${unitid_display} uses ${talent}'), array(
                'i18n' => array( 'talent'),
                'unitid_display' => $this->id,
                'talent' => $this->powers[0]->title,
                "wounds" => 0,
                'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$this->id),
                "diff" => 0,
                "maxhp" => count($this->stats)
            ) );


            mythicbattlesragnarok::DbQuery( "delete from pending where function = 'die' and unit_id=".$this->id);                          

        }
    }

    function argRing($parg1, $parg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('The Ring Of Andvari : ${actplayer} may reroll one dice (${defense})');
        $ret['titleyou'] = clienttranslate('The Ring Of Andvari : ${you} may reroll one dice (${defense})'); 
        $ret['defense'] = $attack->defense;
        $attack = attack::fromJSON($parg1);       
        if(!in_array("noreroll", $attack->to->status))
        {
            $dices = self::getCollectionFromDb( "SELECT * FROM die"); 
            foreach($dices as $die)
            {            
                $ret['selectable']['diceres'.$die['id']] = array();                                 
            } 
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }

    function Ring($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {
            $toimproveid = str_replace("diceres","", $varg1);
            $face = bga_rand(0,5);
            mythicbattlesragnarok::DbQuery( "update die set value = ".$face.", face = ".$face." where id=".$toimproveid);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "setDice", '', array(
                'die' => self::getObjectFromDB( "SELECT* FROM die where id = ".$toimproveid)
            ) );
            $this->wound(1, "The Ring Of Andvari");
            mythicbattlesragnarok::$instance->resetUndo();
        }
    }
}