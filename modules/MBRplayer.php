<?php 


class MBRplayer extends APP_GameClass
{
    function T0_NewTurn($parg1, $parg2, $varg1, $varg2) 
    {        
        
        mythicbattlesragnarok::$instance->incStat(1, 'turns_number', $this->player_id);
        mythicbattlesragnarok::$instance->addPendingFirst($this->player_id,0, "T0_NewTurn");
        mythicbattlesragnarok::$instance->addPending($this->player_id,0, "T4B_EndOfTurn");
        mythicbattlesragnarok::$instance->addPending($this->player_id,0, "T4A_Recall");
        mythicbattlesragnarok::$instance->addPending($this->player_id,0, "T3A_NextActivate");
        mythicbattlesragnarok::$instance->addPending($this->player_id,0, "T2A1_Activate");
        mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw", "mandatory");
        mythicbattlesragnarok::$instance->addPending($this->player_id,0, "T1A_StartTurn");
  
    }

    function T1A_StartTurn($parg1, $parg2)
    {
        mythicbattlesragnarok::DbQuery("update unit set statusRound = '' where player_id = ".$this->id);
        mythicbattlesragnarok::DbQuery("update player set startofturnstatus = '' where player_id = ".$this->id);
        foreach($this->units as $unit)
        {
            $unit->status = array();
        }
    }

    function argT2A1_Activate($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may choose a unit to activate (activation ${nb})');
        $ret['titleyou'] = clienttranslate('${you} may choose a unit to activate (activation ${nb})'); 
        $ret['nb'] = 1 + mythicbattlesragnarok::getUniqueValueFromDB("select count(*) from unit where statusTurn like '%activated%'");

        $force = mythicbattlesragnarok::getUniqueValueFromDB( "select endofturnstatus from player where player_id=".$this->player_id);
        $forceuse = null;
        if (preg_match('/forceuse(\d+)/', $force, $matches)) {
            $forceuse = $matches[1];
        } 

        $sql = "SELECT * from deck".$this->player_no." where card_location = 'hand'";
        $hand = self::getCollectionFromDb( $sql );
        foreach($hand as $card)
        {
            if($card['card_type']>0 && $this->units[$card['card_type_arg']]->canActivate() && ($forceuse == null || $forceuse == $card['card_id']))
            {
                $ret['selectable']['card'.$card['card_id']] = array( 'confirm' => clienttranslate('Do you want to activate ${unitid_display}?'));
                $ret['selectable']['unit'.$card['card_type_arg']] = array( 'confirm' => clienttranslate('Do you want to activate ${unitid_display}?'));
            }            
        } 

        if($forceuse == null || count($ret['selectable'])==0)
        {
            if($ret['nb'] == 1)
            {
                $ret['selectable']['butPass'] = array("title" => clienttranslate("Pass"), "color"=>"red");
            }
            else{            
                $ret['selectable']['butSkip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
            }
        }
        $ret['selectable']['fake'] = array();
 
        return $ret;  
    }
    
    //parg4: noadditionalaow
    function T2A1_Activate($parg1, $parg2, $varg1, $varg2) {        
        if($varg1 == "butPass")
        {
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} passes'), array(
            'player_name' => $this->player_name,
            'player_id' => $this->player_id
            ) );
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw");
            mythicbattlesragnarok::DbQuery( "delete from pending where function = 'T3A_NextActivate'");
        }
        else if($varg1 == "butSkip" || $varg1 == null)
        {

        }
        else{

            $force = mythicbattlesragnarok::getUniqueValueFromDB( "select endofturnstatus from player where player_id=".$this->player_id);
            $forceuse = null;
            if (preg_match('/forceuse(\d+)/', $force, $matches)) {
                $forceuse = $matches[1];
                mythicbattlesragnarok::DbQuery("update player set endofturnstatus = REPLACE(endofturnstatus, 'forceuse".$forceuse."', '') where player_id=".$this->player_id);
            } 

            $action = action::fromPending();
            $action->varg1 = $varg1;
            $action->varg2 = $varg2;
            if($action->parg4 != "noadditionalaow" && mythicbattlesragnarok::$instance->requiresAdditionalAOW($action) > 0)
            {
                mythicbattlesragnarok::$instance->addPending($this->player_id,0, "additionalAOW", $action->toJSON());
            }
            else {
                $card_id = "";
                if (str_starts_with($varg1, "unit"))
                {
                    $unit_id = str_replace("unit","", $varg1);
                    $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player_no." where card_location = 'hand' and card_type_arg = '".$unit_id."' limit 1");
                }
                else {            
                    $card_id = str_replace("card","", $varg1);
                    $card = self::getObjectFromDB( "SELECT * FROM deck".$this->player_no." where card_id = ".$card_id);
                    $unit_id = $card['card_type_arg'];
                }
                $this->discard($card_id);
                mythicbattlesragnarok::$instance->addPending($this->player_id,$unit_id, "T2A2_ActionTypes");
                $unit = mythicbattlesragnarok::$instance->units[$unit_id];
                $unit->status[] = "currentactivation";
                mythicbattlesragnarok::DbQuery("update unit set statusActivation = concat(statusActivation, ' currentactivation' ) where id = ".$unit->id);
            }
        }
    }

    function getAowInHand()
    {
        return mythicbattlesragnarok::getUniqueValueFromDB("select count(*) from deck".$this->player_no." where card_location = 'hand' and card_type <= 0"); 
    }

    function argT3A_NextActivate($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may activate another unit');
        $ret['titleyou'] = clienttranslate('${you} may activate another unit'); 
        
        $alreadyActivated = mythicbattlesragnarok::getUniqueValueFromDB("select count(*) from unit where statusTurn like '%activated%'"); 
        $aow = $this->getAowInHand(); 
        
        if($alreadyActivated < 2 && $aow>0)
        {
            $sql = "SELECT * from deck".$this->player_no." where card_location = 'hand' and card_type <= 0";
            $hand = self::getCollectionFromDb( $sql );
            foreach($hand as $card)
            {            
                $ret['selectable']['card'.$card['card_id']] = array();
                $ret['selectable']['butActivate'] = array("title" => clienttranslate("Activate"));            
            }
        }

        if(count($ret['selectable']) == 0)
        {
            $ret['titleyou'] = clienttranslate('${you} cannot activate another unit'); 
        }

        $ret['selectable']['fake']  = array();
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray"); 

        return $ret;
    }
    
    function T3A_NextActivate($parg1, $parg2, $varg1, $varg2)   
    {
        if($varg1 != "butskip")
        {          
            if (str_starts_with($varg1, "but"))
            {
                $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player_no." where card_location = 'hand' and card_type <= 0 limit 1");
            }
            else
            {
                $card_id = str_replace("card","", $varg1);
            }
            $this->discard($card_id);
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "T2A1_Activate");  
        }
    }

    //$parg1 : nodiscard
    function argT4A_Recall($parg1, $parg2 = null)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may recall a unit of troops');
        $ret['titleyou'] = clienttranslate('${you} may recall a unit of troops'); 

        $divinityZone = $this->getDivinityZone();
        
        if(!in_array("norecall", $this->status) &&  $divinityZone  != null  &&  $divinityZone->id>0 )
        {          
            foreach($this->units as $unit)
            {     
                if($unit->category == TROOP && $unit->zone_id != 0
                && ($this->getAowInHand()>=$unit->getRecallCost() || $parg1 == "nodiscard")
                && (!$divinityZone->isFull() || $unit->zone == $divinityZone))
                {
                    $ret['selectable']['unit'.$unit->id] = array(); 
                }                  
            }  
        }
        if(count($ret['selectable']) == 0)
        {
            $ret['titleyou'] = clienttranslate('${you} cannot recall a unit of troops'); 
        }

        $ret['selectable']['fake']  = array();
        $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");   
        return $ret;
    }
    
    //$parg1 : nodiscard
    function T4A_Recall($parg1, $parg2, $varg1, $varg2)   
    {
        if($varg1 != "butskip")
        {
            $unit_id = str_replace("unit","", $varg1);
            
            if($parg1 != "nodiscard")
            {
                for($i=0;$i<$this->units[$unit_id]->getRecallCost();$i++)
                {
                    $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player_no." where card_location = 'hand' and card_type <= 0 limit 1");
                    $this->discard($card_id);
                }
            }
            $this->units[$unit_id]->recall();
        }
        if($parg1 != "nodiscard")
        {
            mythicbattlesragnarok::$instance->onTiming(ENDOFTURN, $this->player_id);
        }
    }

    function T4B_EndOfTurn($parg1, $parg2, $varg1, $varg2)   
    {
        foreach($this->units as $unit)
        {
            $unitdb = self::getObjectFromDB( "SELECT * FROM unit  where id = ".$unit->id);
            foreach (array_filter(explode(' ', $unitdb['statusOwnActivation'])) as $valeur) {
                $unit->status = array_filter($unit->status, function($value) use($valeur) {
                    return $value !== $valeur; // Retire 'b' du tableau
                });
            } 
            foreach (array_filter(explode(' ', $unitdb['statusTurn'])) as $valeur) {
                $unit->status = array_filter($unit->status, function($value) use($valeur) {
                    return $value !== $valeur; // Retire 'b' du tableau
                });
            } 
        }
        mythicbattlesragnarok::DbQuery("update unit set statusTurn = '', statusActivation = '' where player_id=".$this->player_id);
        mythicbattlesragnarok::DbQuery("update player set endofturnstatus = '' where player_id=".$this->player_id);
        $this->status=array();

    }

    function discard($card_id)
    {
        if($card_id != null)
        {
            $this->getDeck()->playCard($card_id);
            $card = self::getObjectFromDB( "SELECT * FROM deck".$this->player_no." where card_id = ".$card_id);
            mythicbattlesragnarok::$instance->notifyAllPlayers( "discard", clienttranslate('${player_name} discards ${unitid_display}'), array(
                'player_name' => $this->player_name,
                'player_id' => $this->player_id,
                'unitid_display' => $card['card_type_arg'],
                'card' => $card
            ) );
            $this->refreshCardsCounter();
        }
    }

    //$parg1 : mandatory
    function argdraw($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may draw a card');
        $ret['titleyou'] = clienttranslate('${you} may draw a card'); 
        $ret['selectable']['butdraw']  = array("title" => clienttranslate("Draw")); 
        if($parg1 != "mandatory")
        {
            $ret['selectable']['butskip'] = array("title" => clienttranslate("Skip"), "color"=>"gray");
        }
        return $ret;
    }

    //$parg1 : mandatory
    function draw($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)   
    {        
        if($varg1 != "butskip")
        {
            $card = $this->getDeck()->pickCardForLocation('deck', 'hand'); 
            if($card == NULL)
            {
                mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} deck runs out'), array(
                    'player_name' => $this->player_name,
                    'player_id' => $this->player_id
                ) );

                mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw", "mandatory");
                foreach(mythicbattlesragnarok::$instance->playersMBR as $player)
                {
                    mythicbattlesragnarok::$instance->addPending($player->player_id,0, "refreshDeck");
                }

            }
            else
            {
                $card = self::getObjectFromDB( "SELECT * FROM deck".$this->player_no." where card_id = ".$card['id']);
                mythicbattlesragnarok::$instance->notifyPlayer( $this->player_id, "draw", '', array(
                    'card' => $card
                ) );
                $this->refreshCardsCounter();
            }
            mythicbattlesragnarok::$instance->resetUndo();                           
        }
    }

    function refreshDeck($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $deck = $this->getDeck();
        $cards = mythicbattlesragnarok::$instance->getObjectListFromDB( "SELECT card_id FROM deck".$this->player_no." where card_location='deck'");        
        foreach($cards as $card)
        {
            mythicbattlesragnarok::$instance->notifyPlayer( $this->player_id, "draw", '', array(
                'card' => $card
            ) );
        }
        $deck->moveAllCardsInLocation("deck","hand");  
        $deck->moveAllCardsInLocation("discard","deck");  
        $deck->shuffle( 'deck' );
        
        $draws = max(0,3-$deck->countCardInLocation('hand'));

        for($i=0;$i<$draws;$i++)
        {
            mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw", "mandatory");
        }

        $this->refreshCardsCounter();
    }

    function getDeck()
    {
        if($this->player_no == 1)
        {
            return mythicbattlesragnarok::$instance->deck1;
        }
        else
        {            
            return mythicbattlesragnarok::$instance->deck2;
        }
    }

    function getOtherPlayer() {
        foreach(mythicbattlesragnarok::$instance->playersMBR as $player)
        {
            if($player != $this)
            {
                return $player;
            }
        }        
    }

    function getDivinityZone()
    {        
        foreach($this->units as $unit)
        {
            if($unit->category == GOD || $unit->category == TITAN)
            {
                return $unit->zone;
            }
        }
        return null;
    }

    function refreshCardsCounter()
    {
        $deck = $this->getDeck();
        $ret = array();
        $ret['player_id'] = $this->player_id;
        $ret['deck'] = $deck->countCardInLocation('deck');
        $ret['hand'] = $deck->countCardInLocation('hand');
        $ret['discard'] = $deck->countCardInLocation('discard');
        $ret['discardDetails'] = self::getCollectionFromDb( "SELECT card_type, count(*) from deck".$this->player_no." where card_location = 'discard' group by card_type", true);
        mythicbattlesragnarok::$instance->notifyAllPlayers( "updatecounter", '', $ret );
    }

    
    function argadditionalAOW($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['title'] = clienttranslate('${actplayer} must discard ${nb} additional aow card');
        $ret['titleyou'] = clienttranslate('${you} must discard ${nb} additional aow card to do this action');    
        $action = action::fromJSON($parg1);
        $aow = mythicbattlesragnarok::$instance->requiresAdditionalAOW($action);
        $aowinhand = $this->getAowInHand();
        $ret['nb'] = $aow;
        if($aowinhand>= $aow)
        {
            $ret['selectable']['butdiscard'] = array("title" => clienttranslate("Discard"));
        }
        else{
            $ret['titleyou'] = clienttranslate('${you} cannot do this action that requiers ${nb} additional aow card');  
        }
        $ret['selectable']['butcancel'] = array("title" => clienttranslate("Cancel"), "color"=>"gray");
        $ret['selectable']['fake'] = array();
        return $ret;
    }
    
    function additionalAOW($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $action = action::fromJSON($parg1);
        if($varg1 == "butdiscard")
        {
            $aow = mythicbattlesragnarok::$instance->requiresAdditionalAOW($action);
            for($i=0;$i<$aow;$i++)
            {
                $card_id = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT card_id from deck".$this->player_no." where card_location = 'hand' and card_type <= 0 limit 1");
                $this->discard($card_id);
            }
            $pending = $action->toPending();
            $pending['arg4'] = "noadditionalaow";
            mythicbattlesragnarok::$instance->callPending($pending, true, $action->varg1, $action->varg2);
        }
        else{
            mythicbattlesragnarok::$instance->addPending($action->player_id, $action->unit_id,$action->function, $action->parg1,$action->parg2,$action->parg3,$action->parg4);
        }
    }

    function getAnyTimeActions($player_id)
    {
        $ret = array();

        if(!in_array("nomaneuver", $this->status) && !in_array("nomandraw", $this->status) && count($this->argAnyDrawCards()['selectable'])>0)
        {
            $ret['butAnyDrawCards'] = array("title" => clienttranslate("Draw 2 cards"));
        }
        if(!in_array("nomaneuver", $this->status) && !in_array("nomansearch", $this->status) && count($this->argAnySearch()['selectable'])>0)
        {
            $ret['butAnySearch'] = array("title" => clienttranslate("Search for one card"));
        }     
        foreach(mythicbattlesragnarok::$instance->units as $unit)
        {
            $ret += $unit->getAnyTimeActions($player_id);
        }    
        return $ret;
    }

    function argAnyDrawCards($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('Maneuver : ${actplayer} must discard 1 AOW card, 1 divine stone or 2 activation cards from destroyed unit to draw 2 cards');
        $ret['titleyou'] = clienttranslate('Maneuver : ${you} must discard 1 AOW card, 1 divine stone or 2 activation cards from destroyed unit to draw 2 cards'); 
        
        $sql = "SELECT * from deck".$this->player_no." where card_location = 'hand'";
        $hand = self::getCollectionFromDb( $sql );
        foreach($hand as $card)
        {
            $unit_id = $card['card_type_arg'];
            if($unit_id <= 0)
            {
                $ret['selectable']["card".$card['card_id']] = array();
            }
            else {
                $unit = mythicbattlesragnarok::$instance->units[$unit_id];
                if($unit->isDead() && $unit->category != TROOP)
                {
                    $ret['selectable']["card".$card['card_id']] = array('title' => clienttranslate('${you} must select a second activation card'), 'target'=> array());

                    foreach($hand as $card2)
                    {
                        if($card2['card_type_arg']>0 && $card['card_id'] != $card2['card_id'])
                        {
                            $unit2 = mythicbattlesragnarok::$instance->units[$card2['card_type_arg']];
                            if($unit2->isDead() && $unit2->category != TROOP)
                            {
                                $ret['selectable']["card".$card['card_id']]['target'][] = "card".$card2['card_id'];
                            }
                        }
                    }
                    if(count($ret['selectable']["card".$card['card_id']]['target']) == 0)
                    {
                        unset( $ret['selectable']["card".$card['card_id']]);
                    }
                }
            }
        }
        return $ret;
    }

    function AnyDrawCards($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $card_id = str_replace("card","",$varg1);
        $this->discard($card_id);
        if($varg2 != null)
        {
            $card_id2 = str_replace("card","",$varg2);
            $this->discard($card_id2);
        }
        mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} draws 2 cards'), array(
            'player_name' => $this->player_name,
            'player_id' => $this->player_id
        ) );
        mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw", "mandatory");
        mythicbattlesragnarok::$instance->addPending($this->player_id,0, "draw", "mandatory");
        $this->status[] = 'nomandraw';
        mythicbattlesragnarok::DbQuery("update player set endofturnstatus = concat(endofturnstatus, ' nomandraw' ) where player_id = ".$this->player_id);
    }

    function argAnySearch($parg1 = NULL, $parg2 = NULL)
    {
        $ret = $this->argAnyDrawCards($parg1, $parg2);
        $ret['title'] = clienttranslate('Maneuver : ${actplayer} must discard 1 AOW card, 1 divine stone or 2 activation cards from destroyed unit to search for one card');
        $ret['titleyou'] = clienttranslate('Maneuver : ${you} must discard 1 AOW card, 1 divine stone or 2 activation cards from destroyed unit to search for one card'); 
        return $ret;
    }
    
    function AnySearch($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        $card_id = str_replace("card","",$varg1);
        $this->discard($card_id);
        if($varg2 != null)
        {
            $card_id2 = str_replace("card","",$varg2);
            $this->discard($card_id2);
        }
        mythicbattlesragnarok::$instance->addPending($this->player_id,0, "AnySearch2");
        $this->status[] = 'nomansearch';
        mythicbattlesragnarok::DbQuery("update player set endofturnstatus = concat(endofturnstatus, ' nomansearch' ) where player_id = ".$this->player_id);
    }

    
    function argAnySearch2($parg1 = NULL, $parg2 = NULL)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must pick 1 card from their deck');
        $ret['titleyou'] = clienttranslate('${you} must pick 1 card from your deck'); 
        $ret['pickcards'] = array();
       
        $sql = "SELECT * from deck".mythicbattlesragnarok::$instance->getActivePlayer()->player_no." where card_location = 'deck'";
        $hand = self::getCollectionFromDb( $sql );        
        foreach($hand as $card)
        {      
            $ret['selectable']['card'.$card['card_id']] = array();
            $ret['pickcards'][] = $card; 
        }
        return $ret;
    }
    
    function AnySearch2($parg1 = NULL, $parg2 = NULL, $varg1 = NULL, $varg2 = NULL)
    {
        if($varg1 != null)
        {
            $card_id = str_replace("card","",$varg1);
            $this->getDeck()->moveCard( $card_id, 'hand');
            $card = self::getObjectFromDB( "SELECT * FROM deck".$this->player_no." where card_id = ".$card_id);
            mythicbattlesragnarok::$instance->notifyPlayer( $this->player_id, "draw", '', array(
                'card' => $card
            ) );
            mythicbattlesragnarok::$instance->notifyAllPlayers( "simpleText", clienttranslate('${player_name} searched for one card'), array(
                'player_name' => $this->player_name,
                'player_id' => $this->player_id
            ) );
            $this->getDeck()->shuffle('deck');
            $this->refreshCardsCounter();
        }

    }

        ///////////////////////////////////////////// DRAFT /////////////////////////////////////

        //parg1 : divinity
        function argDraft($parg1, $parg2)
        {
            $ret = array();
            $ret['selectable'] = array();
            $divinity = $parg1 == "divinity";
            if($divinity)
            {
                $ret['title'] = clienttranslate('${actplayer} must recruit a Divinity');
                $ret['titleyou'] = clienttranslate('${you} must recruit a Divinity');  
            } 
            else
            {
                $ret['title'] = clienttranslate('${actplayer} must recruit another element');
                $ret['titleyou'] = clienttranslate('${you} must recruit another element');  
            }  

            $unitplus5 = 0;
            foreach($this->units as $unit)
            {
                if($unit->cost>=5 && $unit->category != GOD  && $unit->category != TITAN)
                {
                    $unitplus5++;
                }
            }

            foreach(mythicbattlesragnarok::$instance->units as $unit)
            {               
                if ((($unit->category == GOD || $unit->category == TITAN) == $divinity) && $unit->zone_id==DRAFT && $unit->cost <= $this->rp && ($unit->cost<5 || $unitplus5<1))
                {
                    $ret['selectable']["unit".$unit->id] = array( 'confirm' => clienttranslate('Do you want to draft ${unitid_display}?'));
                }
            }

            if(!$divinity && $this->rp >0 )
            {
                $attdbs = self::getObjectListFromDB( "SELECT id FROM attachmentdraft where player_id IS NULL", true );
                foreach($attdbs as $id)
                {
                    $ret['selectable']["miniattachment".$id] = array( 'confirm' => clienttranslate('Do you want to draft this attachment?'));
                }
            }


            return $ret;  
        }
        
        function Draft($parg1, $parg2, $varg1, $varg2) 
        {     
            $left = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'Draft'");
            if($varg1 != null)
            {      
                if($left<=2)
                {
                    mythicbattlesragnarok::$instance->addPendingFirst($this->player_id,0, "Draft");
                }
                if (str_starts_with($varg1, "unit"))  
                {                    
                    $unitid = str_replace("unit","", $varg1);  
                    $unit = mythicbattlesragnarok::$instance->units[$unitid]; 
                    $unit->player_id = $this->player_id; 
                    $unit->zone_id = -$this->player_no;
                    $this->rp -= $unit->cost;
                    $this->units[] = $unit;
                    self::DbQuery( "UPDATE unit set player_id = {$this->player_id}, zone_id = -".$this->player_no."  WHERE id = {$unitid}" );
                    mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", clienttranslate('${player_name} draft ${unitid_display}'), array(
                        'player_name' => $this->player_name,
                        'player_id' => $this->player_id,
                        'unitid_display' => $unitid,
                        'id' => $varg1
                    ) );                    
                    mythicbattlesragnarok::$instance->notifyAllPlayers( "backontable", '', array(
                        'unit' => self::getObjectFromDB( "SELECT* FROM unit where id = ".$unitid),
                        'category' => $unit->category
                    ) );
                    mythicbattlesragnarok::$instance->onTiming(RECRUIT, $unit);
                }   
                else{
                    $unitid = str_replace("miniattachment","", $varg1); 
                    $attachname = "attachment".$unitid;
                    $attach = new $attachname();
                    $this->rp --;
                    $attach->id = $unitid;
                    $attach->player_id = $this->player_id;
                    self::DbQuery( "UPDATE attachmentdraft set player_id = {$this->player_id} WHERE id = {$unitid}" );
                    mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", clienttranslate('${player_name} draft ${attachname}'), array(
                        'player_name' => $this->player_name,
                        'player_id' => $this->player_id,
                        'attachname' => $attach->title,
                        'id' => $varg1
                    ) );                    
                    mythicbattlesragnarok::$instance->notifyAllPlayers( "placeattachment", '', array(
                        'attachment' => $attach,
                    ) );
                }
                
                self::DbQuery( "UPDATE player set rp = {$this->rp}  WHERE player_id = {$this->player_id}" );   
                mythicbattlesragnarok::$instance->notifyAllPlayers( "innerhtml", '', array(
                    'id' => "rp".$this->player_no,
                    'html' => $this->rp
                ) );
                 
            }   
            else{
                if($left<=1)
                {
                    foreach(mythicbattlesragnarok::$instance->units as $unit)
                    {
                        if($unit->player_id == 0)
                        {
                            self::DbQuery( "DELETE FROM unit WHERE id=".$unit->id);
                        }                        
                    }
                    mythicbattlesragnarok::$instance->units = array_filter(mythicbattlesragnarok::$instance->units, static function ($element) {
                        return $element->player_id != 0;
                    });

                    mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                        'id' => 'draft'
                    ) );

                    self::DbQuery( "DELETE FROM attachmentdraft WHERE player_id IS NULL");
                     
                    mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                        'id' => 'draft'
                    ) );

                    $firstplayer = $this;
                    if($this->player_no == 2)
                    {
                        $firstplayer = $this->getOtherPlayer();
                    }                
                    mythicbattlesragnarok::$instance->addPendingFirst($firstplayer->player_id,0, "Attachment");
                    mythicbattlesragnarok::$instance->addPendingFirst($firstplayer->getOtherPlayer()->player_id,0, "Attachment");
                }
            }
        }


        function argAttachment($parg1, $parg2)
        {
            $ret = array();
            $ret['selectable'] = array();
            $ret['title'] = clienttranslate('${actplayer} must attach an attachment to one of your troop');
            $ret['titleyou'] = clienttranslate('${you} must attach an attachment to one of your troop'); 
            
            $attdbs = self::getObjectListFromDB( "SELECT id FROM attachmentdraft where player_id = ".$this->player_id, true );
            foreach($attdbs as $id)
            {
                $attName = 'attachment'.$id;
                $attachment  = new $attName();

                $ret['selectable']["miniattachment".$id] = array( 'title' => clienttranslate('${you} must select the troop to attach to?'), 'target'=> array());
                foreach($this->units as $unit)
                {
                    if($attachment->canBeAttachedTo($unit))
                    {
                        $ret['selectable']["miniattachment".$id]['target'][] = "unit".$unit->id;
                    }
                }
                if(count($ret['selectable']["miniattachment".$id]['target']) == 0)
                {
                    unset( $ret['selectable']["miniattachment".$id]);
                }
            }
            
            $ret['selectable']['fake'] = array();
            return $ret;  
        }
        
        function Attachment($parg1, $parg2, $varg1, $varg2) 
        {     
            if($varg1 != null && $varg1 != 'fake')
            {  
                mythicbattlesragnarok::$instance->addPendingFirst($this->player_id,0, "Attachment");
                $attachid = str_replace("miniattachment","", $varg1); 
                $unitid = str_replace("unit","", $varg2);
                
                self::DbQuery( "UPDATE unit set attachment = ".$attachid."  WHERE id = {$unitid}" );
                mythicbattlesragnarok::$instance->notifyAllPlayers( "fadeOutAndDestroy", '', array(
                    'id' => $varg1
                ) );  

                $attachname = "attachment".$attachid;
                $attach = new $attachname();
                $attach->id = $attachid;
                $attach->player_id = $this->player_id;
                $attach->unitid = $unitid;
                $this->units[$unitid]->attachment = $attach;
                mythicbattlesragnarok::$instance->notifyAllPlayers( "placeattachment", '', array(
                    'attachment' => $attach,
                ) );

                self::DbQuery( "delete from  attachmentdraft where id = ".$attachid);


            }
            else{
                $left = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'Attachment'");
                if($left<=1)
                {
                    self::DbQuery( "delete from attachmentdraft");

                    $firstplayer = $this;
                    if($this->player_no == 2)
                    {
                        $firstplayer = $this->getOtherPlayer();
                    }   
                    mythicbattlesragnarok::$instance->addPendingFirst($firstplayer->player_id,0, "InitialDeploy");
                    mythicbattlesragnarok::$instance->addPendingFirst($firstplayer->getOtherPlayer()->player_id,0, "InitialDeploy");
                }
            }
        }
    
        function argInitialDeploy($parg1, $parg2)
        {
            $ret = array();
            $ret['selectable'] = array();
            $ret['title'] = clienttranslate('${actplayer} must deploy a troop unit');
            $ret['titleyou'] = clienttranslate('${you} must deploy a troop unit'); 
            foreach($this->units as $unit)
            {
                if(($unit->category == TROOP || in_array("Scout", $unit->talentsNames)) && $unit->zone->id <= 0)
                {
                    $ret['selectable']["unit".$unit->id] = array( 'confirm' => clienttranslate('Do you want to deploy ${unitid_display}?'));
                }
            }
            return $ret;  
        }
        
        function InitialDeploy($parg1, $parg2, $varg1, $varg2) 
        {     
            if($varg1 != null)
            {  
                $unitid = str_replace("unit","", $varg1);
                mythicbattlesragnarok::$instance->addPending($this->player_id,$unitid, "deploy", "force");
                mythicbattlesragnarok::$instance->addPendingFirst($this->player_id,0, "InitialDeploy");
            }
            else{
                $left = mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from pending where function = 'InitialDeploy'");
                if($left<=1)
                {
                    $firstplayer = $this;
                    if($this->player_no == 2)
                    {
                        $firstplayer = $this->getOtherPlayer();
                    }

                    mythicbattlesragnarok::$instance->addPending($firstplayer->getOtherPlayer()->player_id,0, "T0_NewTurn");
                    mythicbattlesragnarok::$instance->addPending($firstplayer->player_id,0, "T0_NewTurn");
                    mythicbattlesragnarok::$instance->onTiming(STARTGAME);
                    mythicbattlesragnarok::$instance->addPending($firstplayer->getOtherPlayer()->player_id,0, "SetupDeck");
                    mythicbattlesragnarok::$instance->addPending($firstplayer->player_id,0, "SetupDeck");
                }
            }
        }

        function SetupDeck($parg1, $parg2, $varg1, $varg2) 
        {
            $cards1 = array();
            $cards1[]= array( 'type' =>0, 'type_arg' =>0, 'nbr' => 3+$this->rp);
            foreach ($this->units as $unit)
            {
                    $cards1[]= array( 'type' => $unit->type, 'type_arg' => $unit->id, 'nbr' => $unit->activation);
                    if($unit->aow>=1)
                    {
                        $cards1[]= array( 'type' =>0, 'type_arg' =>0, 'nbr' => $unit->aow);
                    }
            }

            $this->getDeck()->createCards( $cards1, 'deck' );
            self::DbQuery( "UPDATE deck".$this->player_no." SET card_location = 'hand' WHERE card_type = 0 LIMIT ".(3+$this->rp) );
            $this->getDeck()->shuffle( 'deck' );
            $this->getDeck()->pickCardsForLocation( 3, 'deck', 'hand');

            $this->rp = 0;
            self::DbQuery( "UPDATE player set rp = 0 WHERE player_id = {$this->player_id}" ); 

            foreach(self::getObjectListFromDB( "SELECT * FROM deck".$this->player_no." where card_location = 'hand'") as $card)
            {
                mythicbattlesragnarok::$instance->notifyPlayer( $this->player_id, "draw", '', array(
                    'card' => $card
                ) );
            }
            $this->refreshCardsCounter();
        }

}