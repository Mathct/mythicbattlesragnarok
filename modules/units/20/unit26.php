<?php 

class unit26 extends unit
{
    public $category = HERO;
    public $cost = 4;
    public $activation = 4;
    public $aow = 2;
    public $talentsNames = array("Bolster","Leader", "MonsterSlayer");

    public $stats =  [
        [7, 8, 0, 1,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [7, 7, 0, 1,1,1,0],
        [7, 6, 0, 1,1,1,0],
        [6, 6, 0, 1,1,1,0],
        [6, 6, 0, 0,1,0,0],
        [5, 5, 0, 0,0,0,0]
        
        
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Beowulf");
         $this->powers[0] = new power(0,OFFENSIVE, BLACK, clienttranslate("Hrunting"), clienttranslate('Instead of rolling dice for the first assault, Beowulf can inflict 2 automatic wounds on his target. These wounds cannot be cancelled or modified.'),1);
         $this->powers[1] = new power(1,PASSIVE, WHITE, clienttranslate("Naegling"), clienttranslate('When Beowulf attacks, you can change a blank result from the first assault into a 5.'));
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            $this->T2C_PickActionSimple(NULL,NULL,'butattack', NULL);
        }
    }

    function A2B1_FirstRoll($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if(in_array("power0", $this->status) )
        {
            $attack = attack::fromJSON($parg1);
            $attack->wounds = 2;
            $attack->to->wound(2,$this);
            if($attack->range == 0 && $attack->type == ATNORMAL && $attack->to->canRetaliate($attack))
            {
                mythicbattlesragnarok::$instance->addPending($attack->to->player_id,$attack->to->id, "A3_Retaliate", $attack->toJSON());
            }
        }
        else{
            parent::A2B1_FirstRoll($parg1, $parg2, $varg1, $varg2);
        }
    }

    public function onTiming($time, $attack)
    {
        parent::onTiming($time, $attack);
        if($time == AFTER1STROLL && $this->canUse($this->powers[1]) && $attack->from == $this)
        {
            $nbblank = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from die where value = 0");            
            if($nbblank>=1)
            {
                mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "naegling", $attack->toJSON());
            }
        }
    }

    function argnaegling($parg1, $parg2)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Naegling : ${actplayer} may change a blank dice to 5 (${defense})');
        $ret['titleyou'] = clienttranslate('Naegling : ${you} may change a blank dice to 5 (${defense})'); 
        $ret['defense'] = $attack->defense;
        $nbblank = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from die where value = 0");            
        if($nbblank>=1)
        {
            $ret['selectable']['butchange'] = array("title" => clienttranslate("Change to 5"));
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }

    function naegling($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 != "butskip")
        {
            $toimproveid = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT id from die where value = 0 limit 1"); 
            $face = 5;
            mythicbattlesragnarok::DbQuery( "update die set value = ".$face.", face = ".$face." where id=".$toimproveid);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "setDice", '', array(
                'die' => self::getObjectFromDB( "SELECT* FROM die where id = ".$toimproveid)
            ) );
        }
    }

}