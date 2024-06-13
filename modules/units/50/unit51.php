<?php 

class unit51 extends unit
{
    public $category = MONSTER;
    public $cost = 2;
    public $activation = 3;
    public $aow = 2;
    public $talentsNames = array("Scout");

    public $stats =  [
        [6, 7, 2, 0,1,2,0],
        [6, 7, 2, 0,1,2,0],
        [6, 6, 2, 0,1,2,0],
        [5, 6, 1, 0,1,1,0],
        [5, 6, 1, 0,1,1,0],
        [5, 5, 0, 0,1,0,0]
    ];

    public function __construct()
    {
        $this->name = clienttranslate("Mimir");
         $this->powers[0] = new power(0,ACTIVE, BLACK, clienttranslate("Divination"), clienttranslate('The Search for a Card maneuver does not cost any Art of War cards.'));
         $this->powers[1] = new power(1,ACTIVE, WHITE, clienttranslate("Deep Root"), clienttranslate('Mimir can make a claim action up to <div class="mbr_white">X</div> areas away. The collected area must be visible to Mimir.'));
    }

    
    public function activatePower($index)
    {
        if($index == 0)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "divination"); 
        }
        if($index == 1)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,$this->id, "deep"); 
        }
    }

    function argdivination($parg1, $parg2)
    {
        return $this->player->argAnySearch2($parg1, $parg2);
    }

    function divination($parg1, $parg2, $varg1, $varg2) {
        $this->player->status[] = 'nomansearch';
        mythicbattlesragnarok::DbQuery("update player set endofturnstatus = concat(endofturnstatus, ' nomansearch' ) where player_id = ".$this->player_id);
        $this->player->AnySearch2($parg1, $parg2, $varg1, $varg2);
    }

    
    function argdeep($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('Deep Root : ${unitid_display} may claim a rune');
        $ret['titleyou'] = clienttranslate('Deep Root : ${unitid_display} may claim a rune'); 
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        $ret['unitid_display'] = $this->id;

        if( $this->canClaim())
        {
            $zones = $this->zone->getZonesAtDistance(0, $this->getStat(POWER2));
            foreach($zones as $zone)
            {
                if($this->canSeeZone($zone))
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
        }
        return $ret;
    }

    function deep($parg1, $parg2,  $varg1, $varg2)
     {
        $this->claim($parg1, $parg2,  $varg1, $varg2);
     }

}