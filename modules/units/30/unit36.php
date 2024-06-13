<?php 

class unit36 extends unit
{
    public $category = HERO;
    public $cost = 2;
    public $activation = 4;
    public $aow = 0;
    public $talentsNames = array("Initiative","Mobility");

    public $stats =  [
        [6, 6, 0, 1,1,1,0],
        [5, 6, 0, 1,1,1,0],
        [5, 6, 0, 1,1,1,0],
        [4, 5, 0, 1,1,1,0],
        [4, 5, 0, 0,1,1,0],
        [4, 5, 0, 0,1,1,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Sigmund");
         $this->powers[0] = new power(0,PASSIVE, BLACK, clienttranslate("Metamorph"), clienttranslate('When recruited, take the Sigmund/Wolf token. Sigmund starts the game with his token displaying the Sigmund side. When you use this power, place the token on the Wolf side. Sigmund gains +1 offense, defense, and movement, until the start of his next activation.'),1,1);
         $this->powers[1] = new power(1,PERMANENT, WHITE, clienttranslate("Gram"), clienttranslate('Sigmund can retaliate without discarding an activation card.'));
    }

    public function onTiming($time, $attack = NULL)
    {
        parent::onTiming($time, $attack);
        if($time == STARTGAME && $attack != null && $attack->from == $this)
        {
            self::DbQuery( "INSERT INTO token (type, location) VALUES ( 'Sigmund', 'dashboard".$this->id."')");
            mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") 
            ) );
        }
        else if($time == STARTACTIVATION && $attack->from == $this)
        {
            $token = mythicbattlesragnarok::getObjectFromDB( "SELECT * from token where type='Sigmund' or type='Wolf'");
            if($token != null)
            {
                self::DbQuery( "update token set type = 'Sigmund' where id = ".$token['id']);
                $token['type'] = "Sigmund";
                mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                    "token" => $token
                ) );
            }
            else{
                self::DbQuery( "INSERT INTO token (type, location) VALUES ( 'Sigmund', 'dashboard".$this->id."')");
                mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                    "token" => self::getObjectFromDB( "SELECT* FROM token order by id desc limit 1") 
                ) );
            }
        }
    } 


    public function getExtraActions($simple)
    {
        $ret = parent::getExtraActions($simple);
        $token = mythicbattlesragnarok::getObjectFromDB( "SELECT * from token where type='Sigmund'");
        if($token != null && $this->canUse($this->powers[0]))
        {
            $ret[] = array("title"=>"Metamorph", "function" => "pow0");
        }
        return $ret;
    }

    public function activatePower($index)
    {
        if($index == 0)
        {
            $token = mythicbattlesragnarok::getObjectFromDB( "SELECT * from token where type='Sigmund' or type='Wolf'");
            $token['type'] = "Wolf";
            self::DbQuery( "update token set type = 'Wolf' where id = ".$token['id']);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", '', array(
                "token" => $token
            ) );

            $this->status[] = "movementp1 offensep1 defensep1";
            mythicbattlesragnarok::DbQuery("update unit set statusOwnActivation = concat(statusOwnActivation, ' movementp1 offensep1 defensep1' ) where id = ".$this->id);
        }
    }


    function argA3_Retaliate($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $parg2 = "nodiscard";        
        return parent::argA3_Retaliate($parg1, $parg2);
    }

    function A3_Retaliate($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $parg2 = "nodiscard";
        parent::A3_Retaliate($parg1, $parg2, $varg1, $varg2);
    }

}