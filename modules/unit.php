<?php 

include("attack.php");   
include("power.php");    
include("attachment.php");   

const TROOP      = 'TROOP';
const HERO      = 'HERO';
const MONSTER   = 'MONSTER';
const GOD       = 'GOD';
const TITAN     = 'TITAN';

const AOW     = 0;
const RUNE     = -1;
const DRAFT = -3;

const OFFENSE    = 0;
const DEFENSE    = 1;
const RANGE      = 2;
const MOVEMENT   = 3;
const POWER1     = 4;
const POWER2     = 5;
const POWER3     = 6;
const DAMAGEBONUS = 7;
const VITALITY     = 10;

const statsname = array("offense","defense","range", "movement","power1","power2","power3","damagebonus","vitality");

const STARTACTIVATION = 1;
const ENDACTIVATION = 2;
const BEFORESELECTINGTARGET = 3;
const AFTERSELECTINGTARGET = 4;
const AFTER1STROLL = 5;
const BEFOREDIE = 6;
const ENDOFTURN = 7;
const STARTGAME = 8;
const ENTER = 9;
const AFTERATTACKWOUND = 10;
const AFTERDIE = 11;
const FASTFLIGHT = 12;
const AFTERVALIDATINGTARGET = 13;
const RECRUIT = 14;
const AFTER2DROLL = 15;

for($i = 1; $i<=57;$i++)
{
    $folder = floor($i / 10).'0';
    include("units/{$folder}/unit{$i}.php");    
}

class unit extends APP_GameClass
{
    public $category = "";
    public $type = 0;
    public $id = 0;
    public $player_id = 0;
    public $name = "";
    public $cost = 99;
    public $activation = 3;    
    public $aow = 0;
    public $talentsNames = array();
    public $talents = array();
    public $powers = array();

    public $stats =  [];
    public $status = [];
    
    public $zone_id = 0;
    public $hp = 0;
    public $traitname = "Terrestrial";
    public $attachment;

    public static function create($unitdb)
    {
        $className = 'unit'.$unitdb['type'];
        $unit = new $className();
        $unit->player_id = intval($unitdb['player_id']);
        $unit->zone_id = intval($unitdb['zone_id']);
        $unit->hp = intval($unitdb['hp']);
        $unit->id = intval($unitdb['id']);
        $unit->type = intval($unitdb['type']);

        $unit->status = array_filter( explode(' ',$unitdb['statusActivation']));
        foreach (array_filter(explode(' ', $unitdb['statusOwnActivation'])) as $valeur) {
            $unit->status[] = $valeur;
        } 
        foreach (array_filter(explode(' ', $unitdb['statusTurn'])) as $valeur) {
            $unit->status[] = $valeur;
        } 
        foreach (array_filter(explode(' ', $unitdb['statusRound'])) as $valeur) {
            $unit->status[] = $valeur;
        } 
        foreach (array_filter(explode(' ', $unitdb['statusGame'])) as $valeur) {
            $unit->status[] = $valeur;
        }
        
        $unit->attachment = new attachment();
        if ($unitdb['attachment'] != 0 )
        {
            $attName = 'attachment'.$unitdb['attachment'];
            $unit->attachment  = new $attName();
        }
        $unit->attachment->id = $unitdb['attachment'];
        $unit->attachment->unit = $unit;
        
        $tokens = self::getCollectionFromDb( "SELECT * FROM token where location = 'unit".$unit->id."' or location = 'dashboard".$unit->id."'");
        foreach($tokens as $token)
        {
            $unit->status[] = "token".$token["type"];
        }       

        foreach($unit->status as $status)
        {
            if(str_starts_with($status, "talent"))
            {
                $unit->talentsNames[] = str_replace("talent","", $status);
            }
        }
        $unit->talentsNames[] = $unit->traitname;

        foreach($unit->talentsNames as $t)
        {                    
            $talentName = 'talent'.$t;
            if (class_exists($talentName))
            {
                $talent = new $talentName($unit);
            }
            else{
                $talent = new talent($unit);
            }
            $unit->talents[$t] = $talent;
            $talent->unit = $unit;
        }


        return $unit;
    }

    function getStat($stat)
    {
        $ret = $this->hp;
        if($stat <= POWER3)
        {
            $ret = $this->stats[count($this->stats) - max(1,$this->hp)][$stat];
        }
        return $ret;
    }

    function argT2A2_ActionTypes($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose between action types for ${unitid_display}');
        $ret['titleyou'] = clienttranslate('${you} must choose between action types for ${unitid_display}');  
        
        if($this->zone_id > 0)
        {
            $ret['selectable']['butSimple'] = array("title" => clienttranslate("Simple actions"));
        }
        $ret['selectable']['butComplex'] = array("title" => clienttranslate("1 complex action"));

        $ret['unitid_display'] = $this->id;
        return $ret;
    }
    
    function T2A2_ActionTypes($parg1, $parg2, $varg1, $varg2) {   

        mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "T2D_EndActivation"); 
        $type = str_replace("but","", $varg1);
        
        if($type == "Complex")
        {
            $this->status[] = "complex";
            $this->status[] = "notalent";
            $this->status[] = "noACTIVE";
            $this->status[] = "noPASSIVE";
            $this->status[] = "noOFFENSIVE";
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' complex notalent noACTIVE noPASSIVE noOFFENSIVE' ) where id = ".$this->id);
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "T2C_PickActionComplex".$parg1,$parg1);
        }
        else
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "T2C_PickActionSimple",$parg1);
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "T2C_PickActionSimple",$parg1);
        }
        mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "T2B_StartActivation",$type);        
        mythicbattlesragnarok::DbQuery("update unit set statusOwnActivation = '' where id = ".$this->id);
    }   
 
    
    function onTiming($time, $attack)
    {
        foreach($this->talents as $talent)
        {
            if($this->canUse($talent))
            {
                $talent->onTiming($time, $attack);
            }
        }
        $this->attachment->onTiming($time, $attack);
    }
    
    function T2B_StartActivation($parg1, $parg2, $varg1, $varg2) { 
        
        $this->status[] = "activated";
        mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' activated' ) where id = ".$this->id); 
        $attack = new attack();
        $attack->from = $this;
        mythicbattlesragnarok::$instance->onTiming(STARTACTIVATION, $attack);
        
    }  
    
    function argT2C_PickActionSimple($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} may make one simple action with ${unitid_display} (${left} left)');
        $ret['titleyou'] = clienttranslate('${you} may make one simple action with ${unitid_display} (${left} left)'); 
        $ret['unitid_display'] = $this->id;
        $ret['left'] = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'T2C_PickActionSimple'");

        //Walk
        if($this->canWalk() && $this->getEffectiveStat(MOVEMENT)['total']>0)
        {
            $ret['selectable']['butwalk'] = array("title" => clienttranslate("Walk"));
        }
        if(count($this->argclaim($parg1, $parg2)['selectable'])>1)
        {
            $ret['selectable']['butclaim'] = array("title" => clienttranslate("Claim"));
        }
        if($this->canAttack())
        {
            $ret['selectable']['butattack'] = array("title" => clienttranslate("Attack"));           
        }
        foreach($this->powers as $power)
        {
            if($this->canUse($power) && ($power->type == ACTIVE || $power->type == OFFENSIVE) && strpos($power->description,'At the end') !== 0 && strpos($power->description,'At the beginning') !== 0 )
            {
                $ret['selectable']["butpow".$power->index] = array( 'title' =>  $power->title );
            }
        }
        $extras = $this->getExtraActions(true);
        foreach($extras as $extra)
        {
            $ret['selectable']["but".$extra["function"]] = array( 'title' =>  $extra['title'] );
        }
        foreach($this->talents as $talent)
        {
            if($this->canUse($talent))
            {
                $extras = $talent->getExtraActions(true);
                foreach($extras as $extra)
                {
                    $ret['selectable']["but".$extra["function"]] = array( 'title' =>  $extra['title'] );
                }
            }
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }
    
    function T2C_PickActionSimple($parg1, $parg2, $varg1, $varg2) {  
        if($varg1 == "butskip")
        {
            mythicbattlesragnarok::DbQuery( "delete from pending where function = 'T2C_PickActionSimple'");
        }   
        else if($varg1 == "butwalk")
        {               
            $move =  $this->getEffectiveStat(MOVEMENT);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} walks up to ${movement}'), array(
                'unitid_display' => $this->id,
                'movement' => $move
            ) );

            $this->status[] = "walked";
            
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' walked' ) where id = ".$this->id);
            for ($i = 0; $i < $move['total']; $i++) {
                mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "move");
            }
        }
        else if($varg1 == "butattack") //Attack action
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A1A_targetChoice");
            $attack = new attack();
            $attack->from = $this;
            $attack->type = ATNORMAL;
            mythicbattlesragnarok::$instance->onTiming(BEFORESELECTINGTARGET, $attack);
            
        }
        else if ($varg1 == "butclaim") //Claim action
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "claim");
        }
        else if (str_starts_with($varg1, "butpow")) //power action
        {
            $index = str_replace("butpow","", $varg1);
            $power = $this->powers[$index];
            for($i = 0;$i<$power->aow;$i++)
            {
                $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type <= 0 limit 1");
                $this->player->discard($card_id);
            }
            
            $this->status[] = "power".$power->index;
            $addedstatus = "power".$power->index;

            if($this->powers[$index]->type == OFFENSIVE)
            {
                $this->status[] = "attacked";
                $addedstatus .=  " attacked";
            }
            else{
                mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "T2C_PickActionSimple",$parg1);
            }

            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' ".$addedstatus."' ) where id = ".$this->id);
            $this->activatePower($index);

            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} uses ${talent}'), array(
                'i18n' => array( 'talent'),
                'unitid_display' => $this->id,
                'talent' => $this->powers[$index]->title
                ) );
            
        }
        else if (str_starts_with($varg1, "buttalent")) //talent action
        {
            
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "T2C_PickActionSimple",$parg1);
            $function = str_replace("_",".",str_replace("but","", $varg1));
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, $function);           
        }
          
    } 

    public function activatePower($index)
    {
    }

    //$parg1 type
    //$parg4 noadditionalaow
    function argA1A_targetChoice($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} may attack with ${unitid_display}');
        $ret['titleyou'] = clienttranslate('${you} may attack with ${unitid_display}'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $this->id;

        $attack = new attack();
        $attack->from = $this;
        $attack->type = ATNORMAL;

        $units = array();

        foreach(mythicbattlesragnarok::$instance->units as $tested)
        {
            if($tested->player_id != $this->player_id)
            {
               $attack->to = $tested;
               $range = $this->getEffectiveStat(RANGE, $attack)['total'];
               $dist = $this->zone->getDistanceWith($tested->zone); 
               
               if($range>=$dist)
               {
                    $units[$tested->id] = $tested;
               }
            }
        }

        $attack->to = null;

        foreach($this->zone->getZonesAtDistance(1,1) as $adjacent)
        {
            if($adjacent->isFull())
            {
                $units += $adjacent->units;
            }
        } 

        foreach($units as $unit)
        {
            if($unit != $this && $unit->player_id != $this->player_id && ($this->canSee($unit)))
            {                
                $range = $this->zone->getDistanceWith($unit->zone);               
                $attack->range = $range;
                $attack->to = $unit;

                if($attack->to->canBeTargeted($attack))
                {
                    $ret['selectable']['unit'.$unit->id] = array(
                        "confirm" => 'Do you want to attack ${unitid_display} ( ${offense} vs ${defense} ) ?',
                        "offense" => $this->getEffectiveStat(OFFENSE, $attack),
                        "defense" => $unit->getEffectiveStat(DEFENSE, $attack),
                    ); 
                }
            }
        }

        return $ret;
    }

    //$parg4 noadditionalaow
    function A1A_targetChoice($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != "butskip")
        {   
            $action = action::fromPending();
            $action->varg1 = $varg1;
            $action->varg2 = $varg2;
            if($action->parg4 != "noadditionalaow" && mythicbattlesragnarok::$instance->requiresAdditionalAOW($action) > 0)
            {
                mythicbattlesragnarok::$instance->addPending($this->player_id,0, "additionalAOW", $action->toJSON());
            }
            else {
                $unitid = str_replace("unit","", $varg1);
                $unit = mythicbattlesragnarok::$instance->units[$unitid];
                $this->status[] = "attacked";
                mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' attacked' ) where id = ".$this->id);
                mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A1B_rangeChoice", $unitid);
            }            
        }
    }

    //parg1 target, parg2 aerial
    function argA1B_rangeChoice($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $unit = mythicbattlesragnarok::$instance->units[$parg1];
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${unitid_display} can attack ${unitid_display2} with a <i>range 0</i> attack');
        $ret['titleyou'] = clienttranslate('${unitid_display} can attack ${unitid_display2} with a <i>range 0</i> attack');
        $range = $this->zone->getDistanceWith($unit->zone);

        $attack = new attack();
        $attack->from = $this;
        $attack->type = ATNORMAL;
        if($parg2 != null)
        {
            $attack->aerial = $parg2;
        }

        if($range == 1 && $unit->zone->isFull())
        {
            $ret['selectable']['butrange0'] = array("title" => clienttranslate("Range").' 0');
        }
        if($range>0 && $range <= $this->getEffectiveStat(RANGE, $attack)['total'])
        {
            $ret['selectable']['butrange'.$range] = array("title" => clienttranslate("Range").' '.$range);
        }
        else {
            $ret['selectable']['butrange0'] = array("title" => clienttranslate("Range").' 0');
        }
        $ret['unitid_display'] = $this->id;
        $ret['unitid_display2'] = $unit->id;
        return $ret;
    }

    function A1B_rangeChoice($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $unit = mythicbattlesragnarok::$instance->units[$parg1];
        $range = str_replace("butrange","", $varg1);    
        
        $attack = new attack();
        $attack->range = $range;
        $attack->from = $this;
        $attack->to = $unit;
        $attack->type = ATNORMAL;
        if($parg2 != null)
        {
            $attack->aerial = $parg2;
        }

        mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A2A_ValueCalculation", $attack->toJSON());
        mythicbattlesragnarok::$instance->onTiming(AFTERVALIDATINGTARGET, $attack);
        mythicbattlesragnarok::$instance->onTiming(AFTERSELECTINGTARGET, $attack);        
    }

    //parg2 : forceoffense 
    //parg3 : nodistance
    function A2A_ValueCalculation($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {      
        $attack = attack::fromJSON($parg1);
        $unit = $attack->to;
        $offense = $this->getEffectiveStat(OFFENSE, $attack);
        $defense = $unit->getEffectiveStat(DEFENSE, $attack);
        $attack->offense = $offense['total'];
        $attack->defense = $defense['total']; 
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $nodistance = $pending['arg3'];
        if(is_numeric($parg2))
        {
            $offense = $parg2;
            $attack->offense = $parg2;
        }

        mythicbattlesragnarok::$instance->notifyAllPlayers( "simpletext", clienttranslate('${unitid_display} attacks ${unitid_display2} ( ${offense} vs ${defense} )'), array(
            'unitid_display' => $this->id,
            'unitid_display2' => $unit->id,
            "offense" => $offense,
            "defense" => $defense,
        ) );  

        if($nodistance != "nodistance" && $attack->range == 0 && $this->zone != $unit->zone && !($this->zone->getDistanceWith($unit->zone) == 1 && $unit->zone->isFull()))
        {
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpletext", clienttranslate('${unitid_display} is too far from ${unitid_display2}'), array(
                'unitid_display' => $this->id,
                'unitid_display2' => $unit->id
            ) );
        }
        else {     
            mythicbattlesragnarok::$instance->addPending(mythicbattlesragnarok::$instance->getActivePlayerId(),$this->id, "A2A_RuneCard", $attack->toJSON());
            
        }
    }

    function argA2A_RuneCard($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    { 
        $ret = array();
        $attack = attack::fromJSON($parg1);
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} can discard a rune card to increase their ${unitid_display} offense by 1');
        $ret['titleyou'] = clienttranslate('${you} can discard a rune card to increase ${unitid_display} offense by 1');

        $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type = -1 limit 1");
        if(($this->category == GOD || $this->category == TITAN) && $attack->offense<10 && $card_id != null)
        {
            $ret['selectable']['butdiscard'] = array("title" => clienttranslate("Discard"));
        }

        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");

        $ret['unitid_display'] = $this->id;

        return $ret;
    }

    function A2A_RuneCard($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {      
        $attack = attack::fromJSON($parg1);

        if($varg1 != null && $varg1 != 'butskip')
        {
            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type = -1 limit 1");
            $this->player->discard($card_id);
            $attack->offense++;

            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpletext", clienttranslate('${unitid_display} attacks ${unitid_display2} ( ${offense} vs ${defense} )'), array(
                'unitid_display' => $this->id,
                'unitid_display2' => $attack->to->id,
                "offense" => $attack->offense,
                "defense" => $attack->defense,
            ) );  

            mythicbattlesragnarok::$instance->addPending(mythicbattlesragnarok::$instance->getActivePlayerId(),$this->id, "A2A_RuneCard", $attack->toJSON());
        }
        else{
            mythicbattlesragnarok::$instance->addPending(mythicbattlesragnarok::$instance->getActivePlayerId(),$this->id, "A2B1_FirstRoll", $attack->toJSON());
        }
    }

    //parg4
    function A2B1_FirstRoll($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {            
            $attack = attack::fromJSON($parg1);
            mythicbattlesragnarok::DbQuery( "delete from die");

            $roll = "1stroll";
            for ($i = 1; $i <= $attack->offense; $i++) {
                $face = bga_rand( 0, 5 );
                mythicbattlesragnarok::DbQuery("INSERT INTO die (face, value) VALUES (".$face.",".$face.")");
                $roll = $roll.$face;
            }        
            $this->status[] = $roll;
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' ".$roll."' ) where id = ".$this->id);

            mythicbattlesragnarok::$instance->notifyAllPlayers( "dices", '', array(
                'dices' => self::getCollectionFromDb( "SELECT * FROM die")
            ) );
            
            mythicbattlesragnarok::$instance->addPending(mythicbattlesragnarok::$instance->getActivePlayerId(),$this->id, "A2C1_CanReroll", $attack->toJSON());
            mythicbattlesragnarok::$instance->addPending(mythicbattlesragnarok::$instance->getActivePlayerId(),$this->id, "A2_BonusDice", $attack->toJSON());
            mythicbattlesragnarok::$instance->addPending(mythicbattlesragnarok::$instance->getActivePlayerId(),$this->id, "A2_DiscardBlanks", $attack->toJSON());
            mythicbattlesragnarok::$instance->onTiming(AFTER1STROLL, $attack);

            mythicbattlesragnarok::$instance->resetUndo();
        
    }

    function A2_DiscardBlanks($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {       
        foreach(self::getCollectionFromDb( "SELECT * FROM die where face = 0") as $die) {
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                'id' => 'diceres'.$die['id']
            ) );
        }
        mythicbattlesragnarok::DbQuery( "delete from die where face = 0");
    }

    function argA2_BonusDice($parg1 = NULL, $parg2 = NULL)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} can discard dice to gain +1 bonuses (${defense})');
        $ret['titleyou'] = clienttranslate('${you} can discard dice to gain +1 bonuses (${defense})'); 
        $ret['selectable']['butSkip'] = array("title" => clienttranslate("Keep all"));
        $ret['selectable']['fake'] = array();
        $ret['unitid_display'] = $this->id;
        $ret['defense'] = $attack->defense;

        foreach(self::getCollectionFromDb( "SELECT * FROM die") as $die) {
            if($die['value']< $attack->defense)
            {
                $ret['selectable']['diceres'.$die['id']] = array('title' => clienttranslate('${you} must choose which die to improve (${defense})'), 'defense' => $attack->defense);
                $ret['selectable']['diceres'.$die['id']]['target'] = array();
                foreach(self::getCollectionFromDb( "SELECT * FROM die") as $die2) {
                    if( $die2['value']< $attack->defense && $die['id'] != $die2['id'])
                    {
                        $ret['selectable']['diceres'.$die['id']]['target'][] = 'diceres'.$die2['id'];
                    }
                }
                if(count($ret['selectable']['diceres'.$die['id']]['target']) == 0)
                {
                    unset( $ret['selectable']['diceres'.$die['id']]);
                }
            }
        }
        return $ret;
    }

    function A2_BonusDice($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != 'butSkip')
        {
            $attack = attack::fromJSON($parg1);
            $todiscardid = str_replace("diceres","", $varg1);
            $toimproveid = str_replace("diceres","", $varg2);
            
            mythicbattlesragnarok::DbQuery( "delete from die where id=".$todiscardid);
            mythicbattlesragnarok::DbQuery( "update die set value = value+1, face = face + 1 where id=".$toimproveid);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                'id' => 'diceres'.$todiscardid
            ) );
            mythicbattlesragnarok::$instance->notifyAllPlayers( "setDice", '', array(
                'die' => self::getObjectFromDB( "SELECT* FROM die where id = ".$toimproveid)
            ) );
            
            mythicbattlesragnarok::$instance->addPending(mythicbattlesragnarok::$instance->getActivePlayerId(),$this->id, "A2_BonusDice", $attack->toJSON());
        }
    }

    function argA2C1_CanReroll($parg1 = NULL, $parg2 = NULL)
    {
        $attack = attack::fromJSON($parg1);
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} can reroll dice with a value of 5 (${defense})');
        $ret['titleyou'] = clienttranslate('${you} can reroll dice with a value of 5 (${defense})'); 
        $ret['defense'] = $attack->defense;
        $ret['multiple'] = 99;
              
        $ret['selectable']['butreroll'] = array("title" => clienttranslate("Reroll BLUE"));
        foreach(self::getCollectionFromDb( "SELECT * FROM die") as $die) {
            if($die['value'] == 5)
            {
                $ret['selectable']['diceres'.$die['id']] = array();
                if($attack->defense > 5)
                {
                    $ret['selectable']['diceres'.$die['id']]['selected'] = true;
                }
            }
        }
        
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }

    function A2C1_CanReroll($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $attack = attack::fromJSON($parg1);
        if($varg1 != "butskip")
        {
            foreach(array_filter(explode(" ",$varg2)) as $arg)
            {
                $toimproveid = str_replace("diceres","", $arg);
                $face = bga_rand( 0, 5 );
                mythicbattlesragnarok::DbQuery( "update die set value = value+".$face.", face = ".$face." where id=".$toimproveid);
                mythicbattlesragnarok::$instance->notifyAllPlayers( "setDice", '', array(
                    'die' => self::getObjectFromDB( "SELECT* FROM die where id = ".$toimproveid)
                ) );
            }
        }
        if($attack->range == 0 && $attack->type == ATNORMAL && $attack->to->canRetaliate($attack))
        {
            mythicbattlesragnarok::$instance->addPending($attack->to->player_id,$attack->to->id, "A3_Retaliate", $attack->toJSON());
        }
        mythicbattlesragnarok::$instance->addPending(mythicbattlesragnarok::$instance->getActivePlayerId(),$this->id, "A2D_Wounds", $attack->toJSON());
        mythicbattlesragnarok::$instance->addPending(mythicbattlesragnarok::$instance->getActivePlayerId(),$this->id, "A2_BonusDice", $attack->toJSON());
        mythicbattlesragnarok::$instance->addPending(mythicbattlesragnarok::$instance->getActivePlayerId(),$this->id, "A2_DiscardBlanks", $attack->toJSON());
        mythicbattlesragnarok::$instance->onTiming(AFTER2DROLL, $attack);
        if($varg1 != "butskip")
        {
            mythicbattlesragnarok::$instance->resetUndo();
        }
    }

    function A2D_Wounds($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $attack = attack::fromJSON($parg1);
        $unit = $attack->to;
        $attack->wounds = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from die where value >= ".$attack->defense);        

        $damageArray1 = $this->getEffectiveStat(DAMAGEBONUS, $attack);
        $damageArray2 = $unit->getEffectiveStat(DAMAGEBONUS, $attack);
        $damageArray = array_merge($damageArray1, $damageArray2);

        $damageArray['base'] = $attack->wounds;
        $damageArray['total'] = $attack->wounds + $damageArray1['total'] + $damageArray2['total']; 

        $attack->wounds = $damageArray['total'];

        if($attack->wounds<0)
        {
            $damageArray['min 0'] = -$attack->wounds;
            $damageArray['total'] = 0;
            $attack->wounds = 0;
        }

        $dices = array();
        foreach(self::getCollectionFromDb( "SELECT * FROM die") as $die) {
            $dices[] = $die['value'];
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array( 'id' => 'diceres'.$die['id'] ) );
        }

        
        $diceresult = implode( ',', $dices );
        mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('Final dice result : ${result}'), array( 'result' => $diceresult ) );

        mythicbattlesragnarok::DbQuery( "delete from die");
        $unit->wound($attack->wounds, $this, $damageArray);
        mythicbattlesragnarok::$instance->onTiming(AFTERATTACKWOUND, $attack);        
    }

    //parg2 : nodiscard initiative
    function argA3_Retaliate($parg1 = NULL, $parg2 = NULL)
    { 
        $attack = attack::fromJSON($parg1);
        $unit = $attack->from;
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} may retaliate with ${unitid_display} against ${unitid_display2} ( ${offense} vs ${defense} )');
        $ret['titleyou'] = clienttranslate('${you} may retaliate with ${unitid_display} against ${unitid_display2} ( ${offense} vs ${defense} )'); 
        $ret['unitid_display'] = $this->id;
        $ret['unitid_display2'] = $unit->id;

        $newattack = new attack();
        $newattack->range = 0;
        $newattack->from = $this;
        $newattack->to = $unit;
        $newattack->type = RETALIATE;

        $ret['offense'] = $this->getEffectiveStat(OFFENSE, $newattack);
        $ret['defense'] = $unit->getEffectiveStat(DEFENSE, $newattack);
        $newattack->offense = $ret['offense'];
        $newattack->defense = $ret['defense'];        

        
        if($this->canRetaliate($attack) && ($parg2 == "nodiscard" || $this->getRetaliateCard() != null))
        {
            $ret['selectable']['butretaliate'] = array("title" => clienttranslate("Retaliate"));
        }
        else
        {
            $ret['titleyou'] = clienttranslate('${you} cannot retaliate with ${unitid_display}'); 
        }    
        if(!$this->isDead())
        {
            $ret['selectable']['fake']  = array();
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");       
        return $ret;
    }
    
    function getRetaliateCard()
    {
        $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player->player_no." where card_location = 'hand' and card_type_arg = ".$this->id." limit 1");
        return $card_id;      
    }

    //parg2 : nodiscard initiative
    function A3_Retaliate($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != 'butskip')
        {           
            $attack = attack::fromJSON($parg1);
            $unit = $attack->from; 
            if($parg2 != "nodiscard")
            {
                $card_id = $this->getRetaliateCard();
                $this->player->discard($card_id);
            }
            $this->status[] = "retaliated";
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' retaliated' ) where id = ".$this->id);
            
            $newattack = new attack();
            $newattack->range = 0;
            $newattack->from = $this;
            $newattack->to = $unit;
            $newattack->type = RETALIATE;

            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "A2A_ValueCalculation", $newattack->toJSON());
        }
    }

    //parg4 : forceoffense
    function argzoneattack($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose a zone to attack with ${unitid_display}');
        $ret['titleyou'] = clienttranslate('${you} must choose a zone to attack with ${unitid_display}'); 
        $ret['unitid_display'] = $this->id;
        $min = $parg1;
        $max = $parg2;

        foreach($this->zone->getZonesAtDistance($min,$max) as $nextzoneid => $nextzone)
        {
            if($this->canSeeZone($nextzone))
            {
                $ret['selectable']['zone'.$nextzoneid] = array();
            }
        }
        return $ret;
    }

    //parg4 : forceoffense onlyennemy
    function zoneattack($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $zoneid = str_replace("zone","", $varg1);
        $zone = mythicbattlesragnarok::$instance->zones[$zoneid];
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $offensive = $pending['arg3'];
        $onlyennemy = strpos($pending['arg4'], "onlyennemy") !== false;
        $forceoffense = strpos($pending['arg4'], "forceoffense") !== false;

        foreach($zone->units as $unit)
        {
            if($unit != $this && ($unit->player_id != $this->player_id || !$onlyennemy))
            {
                $unit->status[] = "zonetarget";
                mythicbattlesragnarok::DbQuery("update unit set statusActivation = concat(statusActivation, ' zonetarget' ) where id = ".$unit->id);
            }
        }
        mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zonetarget", $offensive, $forceoffense?"forceoffense":"");        
    }

    //parg2 : forceoffense
    function argzonetarget($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose the next target for ${unitid_display} area attack');
        $ret['titleyou'] = clienttranslate('${you} must choose the next target for ${unitid_display} area attack'); 
        $ret['unitid_display'] = $this->id;
        $offensive = $parg1;

        foreach(mythicbattlesragnarok::$instance->units as $unit)
        {
            if(in_array("zonetarget", $unit->status) && $unit->hp > 0)
            {                
                $range = $this->zone->getDistanceWith($unit->zone); 
                
                $attack = new attack();
                $attack->range = $range;
                $attack->from = $this;
                $attack->to = $unit;
                $attack->type = AREA;
                if($offensive == NULL)
                {
                    $offensive = $this->getEffectiveStat(OFFENSE, $attack);
                }

                $ret['selectable']['unit'.$unit->id] = array(
                    "confirm" => 'Do you want ${unitid_display2} to attack ${unitid_display} ( ${offense} vs ${defense} ) ?',
                    "offense" => $offensive,
                    "defense" => $unit->getEffectiveStat(DEFENSE, $attack),
                    "unitid_display2" => $this->id
                );
            }
        }
        $ret['selectable']['fake'] = array();
        return $ret;
    }

    function zonetarget($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {        
        if($varg1 != 'fake')
        {      
            $offensive = $parg1;
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "zonetarget", $offensive, $parg2);

            $unitid = str_replace("unit","", $varg1);        
            $unit = mythicbattlesragnarok::$instance->units[$unitid]; 
            
            $unit->status[] = "zonetarget";
            mythicbattlesragnarok::DbQuery("update unit set statusActivation = REPLACE(statusActivation, ' zonetarget', '' ) where id = ".$unit->id);

            $range = $this->zone->getDistanceWith($unit->zone);
            $player_id = $this->player_id;
            if($this->player_id == $unit->player_id)
            {
                $player_id = $this->player->getOtherPlayer()->player_id;
            }      
            
            $attack = new attack();
            $attack->range = $range;
            $attack->from = $this;
            $attack->to = $unit;
            $attack->type = AREA;
            mythicbattlesragnarok::$instance->addPending($player_id,$this->id, "A2A_ValueCalculation", $attack->toJSON(), $offensive);
            mythicbattlesragnarok::$instance->onTiming(AFTERSELECTINGTARGET, $attack);
            
        }   
    }

    //$parg1 : force || mandatory
    //parg4: noadditionalaow
    function argmove($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} may move with ${unitid_display} (${left} left)');
        
        if ($parg1 != "mandatory")
        { 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['titleyou'] = clienttranslate('${you} may move with ${unitid_display} (${left} left)');
       
        }
        else
        {
            $ret['titleyou'] = clienttranslate('${you} must move with ${unitid_display} (${left} left)');
        }
        $ret['unitid_display'] = $this->id;

        $idnot =  mythicbattlesragnarok::getUniqueValueFromDB( "SELECT id from pending where function <> 'move' order by id desc limit 1");
        $ret['left'] = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'move' and id>".$idnot);

        if(!in_array("nowalk", $this->status) || $parg1 == "mandatory")
        { 
            foreach($this->zone->boundaries as $nextzoneid => $boundaryType)
            {
                $nextzone = mythicbattlesragnarok::$instance->zones[$nextzoneid];
                if($nextzone->canEnter($this))
                {
                    $ret['selectable']['zone'.$nextzoneid] = array();
                }
            }
        }
        return $ret;
    }

    //$parg1 : force || mandatory
    //parg4: noadditionalaow
    function move($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != "butskip")
        {

            if(!in_array("deployed", $this->status))
            {
                mythicbattlesragnarok::DbQuery("update unit set statusGame = concat(statusGame, ' deployed' ) where id = ".$this->id);
            }

            if($parg1 != "force" || !in_array("noforce", $this->status))
            {                
                $zoneid = str_replace("zone","", $varg1);
                $action = action::fromPending();
                $action->varg1 = $varg1;
                $action->varg2 = $varg2;

                if($parg1 != "force" && $action->parg4 != "noadditionalaow" && mythicbattlesragnarok::$instance->requiresAdditionalAOW($action) > 0)
                {
                    mythicbattlesragnarok::$instance->addPending($this->player_id,0, "additionalAOW", $action->toJSON());
                }
                else {
                    $this->zone->onExit($this);
                    mythicbattlesragnarok::DbQuery("update unit set zone_id = ".$zoneid." where id = ".$this->id);
                    unset($this->zone->units[$this->id]);  
                    $this->zone =  mythicbattlesragnarok::$instance->zones[$zoneid];
                    $this->zone->units[$this->id] = $this;  
                    mythicbattlesragnarok::$instance->notifyAllPlayers( "move", '', array(
                        'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$this->id),
                        "category" => $this->category
                    ) );    
                    mythicbattlesragnarok::$instance->onTiming(ENTER, $this); 
                    $this->zone->onEnter($this);
                    foreach($this->zone->units as $unit)
                    {
                        if($this->IsStoppedMoving($unit))
                        {
                            $idnot =  mythicbattlesragnarok::getUniqueValueFromDB( "SELECT id from pending where function <> 'move' order by id desc limit 1");
                            mythicbattlesragnarok::DbQuery( "delete from pending where function = 'move' and unit_id=".$this->id." and player_id=".$this->player_id." and arg <> 'mandatory' and id >= ".$idnot);
                            break;
                        }
                    }
                }
            }  
        }
        else{
            $idnot =  mythicbattlesragnarok::getUniqueValueFromDB( "SELECT id from pending where function <> 'move' order by id desc limit 1");
            mythicbattlesragnarok::DbQuery( "delete from pending where function = 'move' and unit_id=".$this->id." and player_id=".$this->player_id." and id >= ".$idnot);
        }
    }


    function argascend($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} may ascend with ${unitid_display}');
        $ret['titleyou'] = clienttranslate('${you} may ascend with ${unitid_display}'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $this->id;

        if($this->canAscend())
        {
            foreach($this->zone->boundaries as $nextzoneid => $boundaryType)
            {
                $nextzone = mythicbattlesragnarok::$instance->zones[$nextzoneid];
                if($nextzone instanceof terrainROCK && !$nextzone->isFull())
                {
                    $ret['selectable']['zone'.$nextzoneid] = array("confirm" => 'Do you want to ascend ?');
                }
            }
        }
        return $ret;
    }

    function ascend($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $this->move($parg1, $parg2, $varg1, $varg2);
    }


    //$parg4 noadditionalaow
    function argclaim($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${unitid_display} may claim a rune');
        $ret['titleyou'] = clienttranslate('${unitid_display} may claim a rune'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $this->id;

        if(!$this->isDead() && !in_array("claimed", $this->status) && mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type = 'rune' and location = 'unit".$this->id."'") == 0)
        {            
            $zones[$this->zone->id] = $this->zone;
            if($this->hasTalent("GemCollector"))
            {
                foreach($this->zone->boundaries as $nextzoneid => $boundaryType)
                {
                    $nextzone = mythicbattlesragnarok::$instance->zones[$nextzoneid];
                    if($this->canSeeZone($nextzone))
                    {
                        $zones[$nextzone->id] = $nextzone;
                    }
                }
            }


            foreach($zones as $zone)
            {

                $allow = true;
                foreach($zone->units as $unit)
                {
                    if($unit->blockClaimGemInArea($this) && $unit->player_id != $this->player_id)
                    {
                        $allow = false;
                        break;
                    }
                }
                if($allow)
                {
                    $runes = self::getCollectionFromDb(  "SELECT * from token where type = 'rune' and location = 'zone".$zone->id."'");
                    foreach($runes as $rune)
                    {
                        $ret['selectable']['token'.$rune['id']] = array();
                    }
                }
            }
        }
        return $ret;
    }

    //$parg4 noadditionalaow
    function claim($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != "butskip" && $varg1 != null)
        {
            $action = action::fromPending();
            $action->varg1 = $varg1;
            $action->varg2 = $varg2;
            if($action->parg4 != "noadditionalaow" && mythicbattlesragnarok::$instance->requiresAdditionalAOW($action) > 0)
            {
                mythicbattlesragnarok::$instance->addPending($this->player_id,0, "additionalAOW", $action->toJSON());
            }
            else {

                $runeid = str_replace("token","", $varg1);
                mythicbattlesragnarok::DbQuery( "update token set location='unit".$this->id."' where id=".$runeid);
                
                mythicbattlesragnarok::$instance->notifyAllPlayers( "movetoNewParent", clienttranslate('${unitid_display} claims a rune'), array(
                    'unitid_display' => $this->id,
                    "mobile_obj" => 'token'.$runeid,
                    "target_obj" => 'unit'.$this->id,
                ) );

                
                $this->status[] = "claimed";
                mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' claimed' ) where id = ".$this->id);
            }
        }
    }

    //parg1 : force
    function argdeploy($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} must deploy ${unitid_display}');
        $ret['titleyou'] = clienttranslate('${you} must deploy ${unitid_display}'); 
        $ret['unitid_display'] = $this->id;

        if($parg1 != "force")
        {
            $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        }
       
        $zones = mythicbattlesragnarok::$instance->getStartingZones($this->player_id);
        foreach($zones as $zoneid)
        {
            $zone = mythicbattlesragnarok::$instance->zones[$zoneid];
            if($zone->canEnter($this))
            {
                $ret['selectable']['zone'.$zoneid] = array();
            }
        }
        
        return $ret;
    }

    function deploy($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != "butskip" && $varg1 != null)
        {

            $this->hp = count($this->stats);
            mythicbattlesragnarok::DbQuery("update unit set hp = ".$this->hp." where id = ".$this->id);

            $this->move($parg1, $parg2, $varg1, $varg2);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} deploys ${unitid_display}'), array(
                'player_name' => $this->player->player_name,
                'player_id' => $this->player->player_id,
                'unitid_display' => $this->id
            ) );
        }
    }

    
    function argabsorb($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} may absorb a divine stone');
        $ret['titleyou'] = clienttranslate('${you} may absorb a divine stone'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
       
        if($this->canAbsorb())
        {
            $runes = self::getCollectionFromDb(  "SELECT * from token where type = 'rune' and location = 'zone".$this->zone->id."'");
            foreach($runes as $rune)
            {
                $ret['selectable']['token'.$rune['id']] = array();
            }
            foreach($this->zone->units as $unit)
            {
                if($unit->player_id == $this->player_id)
                {
                    $runes = self::getCollectionFromDb(  "SELECT * from token where type = 'rune' and location = 'unit".$unit->id."'");
                    foreach($runes as $rune)
                    {
                        $ret['selectable']['token'.$rune['id']] = array();
                    }
                }
            }
        }
        
        return $ret;
    }

    function absorb($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != "butskip")
        {
            $this->status[] = "absorbed";
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' absorbed' ) where id = ".$this->id);

            $runeid = str_replace("token","", $varg1);
            mythicbattlesragnarok::DbQuery( "update token set location='dashboard".$this->id."' where id=".$runeid);
            
            mythicbattlesragnarok::$instance->notifyAllPlayers( "absorb", clienttranslate('${unitid_display} absorbs a rune'), array(
                'unitid_display' => $this->id,
                "token" => self::getObjectFromDB( "SELECT* FROM token where id = ".$runeid) ,
                "unitId" => $this->id
            ) );
            
            $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT max(card_id) from deck".$this->player->player_no) + 1;
            mythicbattlesragnarok::DbQuery( "insert into deck".$this->player->player_no."(card_id, card_type, card_type_arg, card_location, card_location_arg) values(".$card_id.",".RUNE.",".RUNE.",'hand',0)");
            $card = self::getObjectFromDB( "SELECT * FROM deck".$this->player->player_no." where card_id = ".$card_id);
            mythicbattlesragnarok::$instance->notifyPlayer( $this->player_id, "draw", '', array(
                'card' => $card
            ) );

            
            mythicbattlesragnarok::$instance->incStat(1, 'divine_stone', $this->player_id);

            $this->player->player_score++;
            mythicbattlesragnarok::DbQuery( "update player set player_score = player_score + 1 where player_id = ".$this->player_id);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "innerhtml", '', array(
                'id' => "player_score_".$this->player_id,
                'html' => $this->player->player_score
            ) );

            if($this->player->player_score>=4)
            {
                //endof game
                mythicbattlesragnarok::DbQuery( "delete from pending");
            }
        
        }
    }

    
    function argT2C_PickActionComplex($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} may make one complex action with ${unitid_display} (${left} left)');
        $ret['titleyou'] = clienttranslate('${you} may make one complex action with ${unitid_display} (${left} left)');                 
        $ret['selectable']['fake1'] = array();
        $ret['unitid_display'] = $this->id;
        $ret['left'] = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'T2C_PickActionComplex'");

        if($this->zone_id < 0)
        {
            if(count($this->argdeploy($parg1, $parg2)['selectable'])>1 && !in_array("deployed", $this->status) )
            {
                $ret['selectable']['butdeploy'] = array("title" => clienttranslate("Deploy"));
            }
        }
        else{

            if($this->canRun() && $this->getStat(MOVEMENT)>-1 && count($this->argmove($parg1, $parg2)['selectable'])>1)
            {
                $ret['selectable']['butrun'] = array("title" => clienttranslate("Run"));
            }
            if(count($this->argabsorb($parg1, $parg2)['selectable'])>1)
            {
                $ret['selectable']['butabsorb'] = array("title" => clienttranslate("Absorb"));
            }
            if(count($this->argascend($parg1, $parg2)['selectable'])>1)
            {
                $ret['selectable']['butascend'] = array("title" => clienttranslate("Ascend"));
            }            
            foreach($this->talents as $talent)
            {
                if($this->canUse($talent))
                {
                    $extras = $talent->getExtraActions(false);
                    foreach($extras as $extra)
                    {
                        $ret['selectable']["but".$extra["function"]] = array( 'title' =>  $extra['title'] );
                    }
                }
            }
        }
        
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");

        return $ret;
    }
    
    function T2C_PickActionComplex($parg1, $parg2, $varg1, $varg2) {       
        if($varg1 == "butskip")
        {
            mythicbattlesragnarok::DbQuery( "delete from pending where function = 'T2C_PickActionComplex'");
        }   
        else if($varg1 == "butrun")
        {
            $move =  $this->getEffectiveStat(MOVEMENT);
            $move['run'] = 1;
            $move['total']++;
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${unitid_display} runs up to ${movement}'), array(
                'unitid_display' => $this->id,
                'movement' => $move
            ) );
            $this->status[] = "run";
            mythicbattlesragnarok::DbQuery("update unit set statusTurn = concat(statusTurn, ' run' ) where id = ".$this->id);
            for ($i = 0; $i < $move['total']; $i++) {
                mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "move");
            }
        }    
        else
        {
            $function = str_replace("_",".",str_replace("but","", $varg1));
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, $function);
        }

          
    }  

    function T2D_EndActivation($parg1, $parg2, $varg1, $varg2) 
    {      
        mythicbattlesragnarok::DbQuery("update unit set statusActivation = ''");
        $attack = new attack();
        $attack->from = $this;
        mythicbattlesragnarok::$instance->onTiming(ENDACTIVATION, $attack);
        mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "dropAllEnd");
    } 

    
    public function canSeeZone($zone)
    {
        if($zone != $this->zone)
        {
            if(!($this->zone->ignoreObstacle($this->zone, $zone) || $zone->ignoreObstacle($this->zone, $zone)))
            {
                $path = $this->zone->visible[$zone->id];
                if($path == null)
                {
                    return true;
                }
                $previous = $this->zone;
                foreach($path as $testedzoneid)
                {  
                    $testedzone = mythicbattlesragnarok::$instance->zones[$testedzoneid];
                    if($testedzone->isObstacle())
                    {
                        return false;
                    }

                    if(array_key_exists($testedzone->id, $previous->boundaries))
                    {
                        $boundary = boundary::Create($previous, $testedzone);
                        if($boundary->isObstacle())
                        {
                            return false;
                        }
                    }
                    $previous = $testedzone; 
                }
                if(array_key_exists($zone->id, $previous->boundaries))
                {
                    $boundary = boundary::Create($previous, $zone);
                    if($boundary->isObstacle())
                    {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function canSee($otherUnit)
    {
        if($otherUnit->zone->id != $this->zone->id)
        {
            if(!($this->ignoreObstacle($otherUnit) || $this->zone->ignoreObstacle($this->zone, $otherUnit->zone) || $otherUnit->zone->ignoreObstacle($this->zone, $otherUnit->zone)))
            {
                $path = $this->zone->visible[$otherUnit->zone->id];
                $previous = $this->zone;
                foreach($path as $testedzoneid)
                {  
                    $testedzone = mythicbattlesragnarok::$instance->zones[$testedzoneid];
                    if($testedzone->isObstacle())
                    {
                        return false;
                    }

                    if(array_key_exists($testedzone->id, $previous->boundaries))
                    {
                        $boundary = boundary::Create($previous, $testedzone);
                        if($boundary->isObstacle())
                        {
                            return false;
                        }
                    }
                    $previous = $testedzone; 
                }
                if(array_key_exists($otherUnit->zone->id, $previous->boundaries))
                {
                    $boundary = boundary::Create($previous, $otherUnit->zone);
                    if($boundary->isObstacle())
                    {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function argdropAllEnd($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${unitid_display} may drop its divine stone');
        $ret['titleyou'] = clienttranslate('${unitid_display} may drop its divine stone');
        $ret['unitid_display'] = $this->id;
        if(mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type='rune' and location = 'unit".$this->id."'")>0)
        {
            $ret['selectable']['butdrop'] = array("title" => clienttranslate("Drop Divine Stone"));
        }
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        return $ret;
    }

    
    public function dropAllEnd($parg1 = null, $parg2 = null, $varg1 = null, $varg2 = null) {
        if($varg1 != "butskip")
        {
            $this->dropAll();
        }
    }

    public function dropAll() {
       
            $runes = self::getCollectionFromDb(  "SELECT * from token where type = 'rune' and location = 'unit".$this->id."'");
            foreach($runes as $rune)
            {
                $rune['location'] = "zone".$this->zone_id;
                mythicbattlesragnarok::$instance->notifyAllPlayers( "drop", '', array(
                    'token' => $rune
                ) );
            }
            
            mythicbattlesragnarok::DbQuery( "update token set location = 'zone".$this->zone_id."' where location = 'unit".$this->id."'"); 
        
    }

    public function recall() {

        $zone = $this->player->getDivinityZone();
        if(!$zone->isFull() || $zone == $this->zone)
        {            
            mythicbattlesragnarok::DbQuery( "update unit set hp = ".count($this->stats)." where id=".$this->id);
            $this->hp = count($this->stats);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "wounds", clienttranslate('${player_name} recalls ${unitid_display}'), array(
                'player_name' => $this->player->player_name,
                'player_id' => $this->player->player_id,
                'unitid_display' => $this->id,
                "wounds" => 0,
                'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$this->id),
                "diff" => 0,
                "maxhp" => count($this->stats)
            ) );
            $this->dropAll();
            $this->move(NULL, NULL, $zone->id);
        }
    }

    public function heal($diff)
    {
        $diff = min($diff, count($this->stats) - $this->hp);
        mythicbattlesragnarok::DbQuery( "update unit set hp = hp + ".$diff." where id=".$this->id);
        $this->hp += $diff;
        mythicbattlesragnarok::$instance->notifyAllPlayers( "wounds", clienttranslate('${unitid_display} gains ${vitality}'), array(
            'unitid_display' => $this->id,
            'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$this->id),
            "diff" => $diff,
            "maxhp" => count($this->stats),
            "vitality" => $diff
        ) );
    }

    public function pendWound($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $this->wound($parg1, $parg2);
    }

    public function wound($wounds, $cause, $arrayDetails = NULL)
    {  
        $alreadyDead = $this->hp <= 0;
        if($arrayDetails == NULL)
        {
            $arrayDetails = $wounds;
        }

        mythicbattlesragnarok::DbQuery( "update unit set hp = hp - ".$wounds." where id=".$this->id);
        $this->hp -= $wounds;
        
        if($cause instanceof unit )
        {
            mythicbattlesragnarok::$instance->notifyAllPlayers( "wounds", clienttranslate('${unitid_display} inflicts ${vitality} to ${unitid_display2}'), array(
                'unitid_display' => $cause->id,
                'unitid_display2' => $this->id,
                'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$this->id),
                "diff" => -$wounds,
                "maxhp" => count($this->stats),
                "vitality" => $arrayDetails
            ) );
        }
        else
        {
            mythicbattlesragnarok::$instance->notifyAllPlayers( "wounds", clienttranslate('${unitid_display2} suffers ${vitality} from ${cause}'), array(
                'i18n' => array( 'cause'),
                'cause' => $cause,
                'unitid_display2' => $this->id,
                'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$this->id),
                "diff" => -$wounds,
                "maxhp" => count($this->stats),
                "vitality" => $arrayDetails
            ) );
        }

        if($this->hp <= 0 && !$alreadyDead)
        {
            $attack = new attack();
            $attack->to = $this;
            if($cause instanceof unit )
            {
                $attack->from = $cause;
            }
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "die", $attack->toJSON());
            mythicbattlesragnarok::$instance->onTiming(BEFOREDIE, $attack);
        }

    }

    public function die($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL) 
    {
        mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", clienttranslate('${unitid_display} is destroyed'), array(
            'unitid_display' => $this->id,
            'id' => 'unit'.$this->id
        ) );

        
        mythicbattlesragnarok::$instance->incStat(1, 'unit_killed', $this->player->getOtherPlayer()->player_id);

        $todelete = self::getCollectionFromDb(  "SELECT * from token where remove = 'destroyed' and (location = 'unit".$this->id."' or location = 'dashboard".$this->id."')");
        foreach($todelete as $rune)
        {
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                'id' => 'token'.$rune['id']
            ) );
            mythicbattlesragnarok::DbQuery( "delete from token where id = ".$rune['id']);
        }        

        $this->dropAll();

        if($this->category == TROOP)
        {
            $zone_id = -$this->player->player_no;
            mythicbattlesragnarok::DbQuery( "update unit set zone_id = -".$this->player->player_no." where id=".$this->id);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "backontable", '', array(
                'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$this->id),
                'category' => $this->category
            ) );    
        }
        else
        {                
            mythicbattlesragnarok::DbQuery( "update unit set zone_id = 0 where id=".$this->id);
            $zone_id = 0;            
        }
            
    
        mythicbattlesragnarok::DbQuery( "delete from pending where unit_id = ".$this->id);
        mythicbattlesragnarok::DbQuery( "delete from die ");
        $attack = new attack();
        $attack->to = $this;
        if($parg1 != null)
        {
            $attack = attack::fromJSON($parg1);
        }
        mythicbattlesragnarok::$instance->onTiming(AFTERDIE, $attack);

        unset($this->zone->units[$this->id]);  
        $this->zone =  mythicbattlesragnarok::$instance->zones[$zone_id];
        $this->zone->units[$this->id] = $this; 
        $this->zone_id = $zone_id;
        foreach(self::getCollectionFromDb( "SELECT * FROM die") as $die) {
            
            mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array( 'id' => 'diceres'.$die['id'] ) );
        }

        if($this->category == GOD || $this->category == TITAN)
        {
            
            mythicbattlesragnarok::$instance->incStat(1, 'divinity_killed', $this->player->getOtherPlayer()->player_id);

            //endof game
            mythicbattlesragnarok::DbQuery( "update player set player_score = 4 where player_id = ".$this->player->getOtherPlayer()->id);
            mythicbattlesragnarok::DbQuery( "update player set player_score = 0 where player_id = ".$this->player->id);
            mythicbattlesragnarok::DbQuery( "delete from pending");

            mythicbattlesragnarok::$instance->notifyAllPlayers( "innerhtml", '', array(
                'id' => "player_score_".$this->player_id,
                'html' => '0'
            ) );
            mythicbattlesragnarok::$instance->notifyAllPlayers( "innerhtml", '', array(
                'id' => "player_score_".$this->player->getOtherPlayer()->player_id,
                'html' => '4'
            ) );
        }
    }

    public function getEffectiveStat($stat, $attack = NULL)
    {        
        if($attack == null)
        {
            $attack = new attack();
            $attack->from = $this;
        }

        $tab = array();
        $ret = 0;
        if($stat <= MOVEMENT)
        {
            $ret = $this->getStat($stat);
        }
        $tab['base'] = $ret;
        if(!$attack->aerial)
        {           
            $modif = $this->zone->getStatBonus($stat, $attack);
            if($modif != 0)
            {
                $tab[clienttranslate('terrain')] = $modif;
                $ret += $modif;
            } 
        }
        foreach($this->status as $status)
        {
            if(str_starts_with($status, statsname[$stat]))
            {
                $smodif = str_replace(statsname[$stat],"", $status);
                $modif = intval(substr($smodif, 1));
                if(str_starts_with($smodif,"m"))
                {
                    $modif = -$modif;
                }                
                $tab[clienttranslate('status')] = $modif;
                $ret += $modif;
            }
        }

        $modif = $this->attachment->getStatBonus($stat, $this, $attack);
        if($modif != 0)
        {
            $tab[$this->attachment->title] = $modif;
            $ret += $modif;
        }

        foreach($this->talents as $talent)
        {
            if($this->canUse($talent))
            {
                $modif = $talent->getStatBonus($stat, $this, $attack);
                if($modif != 0)
                {
                    $tab[$talent->name] = $modif;
                    $ret += $modif;
                }
            }
        }
        foreach(mythicbattlesragnarok::$instance->units as $unit)
        {        
            if($unit != $this)
            {    
                $modif = $unit->getStatBonus($stat, $this, $attack);
                if($modif != 0)
                {
                    $tab[$unit->name] = $modif;
                    $ret += $modif;
                }  
            }          
        }
        if($stat <= MOVEMENT)
        {
            if($ret > 10)
            {
                $modif = $ret - 10;
                $tab[clienttranslate('Max 10')] = $modif;
                $ret += $modif;
            }
            if($ret < 0)
            {
                $modif = -$ret;
                $tab[clienttranslate('Min 0')] = $modif;
                $ret += $modif;
            }
        }
        
        $tab['total'] = $ret;
        return $tab;
    }

    public function getStatBonus($stat, $to, $attack)
    {
        $ret = 0;
        foreach($this->talents as $talent)
        {
            if($this->canUse($talent))
            {
                $ret += $talent->getStatBonus($stat, $to, $attack);
            }
        }
        return $ret;
    }

    public function getAuraStatus($to)
    {
        $ret = array();
        foreach($this->talents as $talent)
        {
            if($this->canUse($talent))
            {
                $ret = array_merge($ret,$talent->getAuraStatus($to));
            }
        }
        $ret = array_merge($ret,$this->attachment->getAuraStatus($to));
        return $ret;
    }

    public function canActivate() {
       return $this->zone->id != 0 && !in_array("activated", $this->status);
    }

    public function canRetaliate($attack)
    {
        $retaliateAttack = new attack();
        $retaliateAttack->from = $attack->to;
        $retaliateAttack->to = $attack->from;
        $retaliateAttack->range = $attack->range;
        $retaliateAttack->type = RETALIATE;
        return !$this->isDead() && $attack->type == ATNORMAL && ($attack->to->zone == $attack->from->zone || ($attack->from->zone->getDistanceWith($attack->to->zone) == 1 && $attack->to->zone->isFull() )) && !in_array("retaliated", $this->status) && !in_array("noretaliate", $this->status) && $attack->from->canBeTargeted($retaliateAttack);
    }

    public function canAttack()
    {
        return !$this->isDead() && !in_array("attacked", $this->status) && !in_array("noattack", $this->status);
    }
    
    public function canWalk()
    {
        return !$this->isDead() && (!in_array("attacked", $this->status) || $this->hasTalent("Mobility")) && !in_array("walked", $this->status) && !in_array("nowalk", $this->status);
    }
    
    public function canRun()
    {
        return !$this->isDead() && in_array("complex", $this->status) && !in_array("norun", $this->status);
    }
    
    public function canAscend()
    {
        return !$this->isDead() && !in_array("noascend", $this->status);
    }

    public function canClaim()
    {
        return !$this->isDead() && !in_array("noclaim", $this->status) && !in_array("claimed", $this->status) && mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type = 'rune' and location = 'unit".$this->id."'") == 0;
    }

    
    public function canAbsorb()
    {
        return !$this->isDead()  && !in_array("noabsorb", $this->status) && ( $this->category == GOD || $this->category == TITAN) ;
    }

    public function canBeTargeted($attack = NULL)
    {
        return true;
    }

    function canUse($item)
    {
        $ret = $this->zone->canUse($this, $item);
        
        if($item instanceof talent)
        {
            $ret = $ret && !$this->isDead() && $this->zone_id>0 && ($item->trait || (!in_array("notalent", $this->status) && ($this->category != TROOP || $this->hp>1)));
        }
        if($item instanceof power)
        {
            $ret = $ret && !in_array("nopower".$item->index, $this->status);
            $ret = $ret && !$this->isDead() && ( $this->zone_id>0 || $item->type == PERMANENT) && !in_array("no".$item->type, $this->status) && $this->getStat(POWER1+$item->index)>0;
            $ret = $ret && $this->player->getAowInHand() >= $item->aow;
            $ret = $ret && ($item->type != ACTIVE || !in_array("power".$item->index, $this->status));
            $ret = $ret && ($item->type != OFFENSIVE || $this->canAttack());
        }
        return $ret;
    }

    function IsStoppedMoving($other)
    {
        return $other->player_id != $this->player_id;
    }

    public function ignoreObstacle($other)
    {
        return false;
    }

    public function isDead()
    {
        return $this->hp <= 0;
    }

    function hasTalent($talentName)
    {
        foreach($this->talents as $talent)
        {
            $tname="talent".$talentName;
            if(($talent instanceof $tname) && $this->canUse($talent))
            {
                return true;
            }
        }
        return false;
    }
    
    function requiresAdditionalAOW($action)
    {
        return 0;
    }

    function getAnyTimeActions($player_id)
    {
        $ret = array();
        if(in_array("currentactivation", $this->status) && mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type='rune' and location = 'unit".$this->id."'")>0)
        {
            $ret['butAny'.$this->id."dropAll"] = array("title" => clienttranslate("Drop divine stone"));
        }
        return $ret;
    }

    function blockClaimGemInArea($to)
    {
        return $this->hasTalent("Block") && !$to->hasTalent("Block") && !in_array("noblock", $to->status);
    }

    function getRecallCost()
    {
        if($this->attachment instanceof attachment2)
        {
            return 0;
        }
        return 1;
    }

    function CanBeDraft() 
    {
        return true;
    }

    public function getExtraActions($simple)
    {
        return array();
    }

}