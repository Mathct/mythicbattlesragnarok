<?php

const BUILDING = "BUILDING";
const BURNING = "BURNING";
const CLIFF = "CLIFF";
const DIVINE_SOURCE = "DIVINE_SOURCE";
const FOREST = "FOREST";
const OPEN_GROUND = "OPEN_GROUND";
const POLAR = "POLAR";
const ROCK = "ROCK";
const RUINS = "RUINS";
const STEPS = "STEPS";
const STRUCTURE = "STRUCTURE";
const WATER = "WATER";

include("terrainOPEN_GROUND.php");
include("terrainROCK.php"); 
include("terrainBUILDING.php"); 
include("terrainBURNING.php");    
include("terrainCLIFF.php");  
include("terrainPOLAR.php");  
include("terrainSTEPS.php");    
include("terrainSTRUCTURE.php"); 
include("terrainWATER.php");   
include("terrainFOREST.php");   
include("terrainDIVINE_SOURCE.php");   
include("terrainRUINS.php");   

class terrain extends APP_GameClass
{
    public $id;
    public $capacity;
    public $visible;
    public $boundaries;
    public $units;
    public $type;
    public $height; //0 bas, 0.5 moyen, 1 haut
    public $title = "";
    public $description = "";

    public static function Create($zone)
    {
        //Hack Start : HRYM
        if( mythicbattlesragnarok::getUniqueValueFromDB( "SELECT count(*) from token where type='Hrym' and location = 'zone".$zone['id']."'")>0)
        {
            $zone['type'] = POLAR;
        }
        //Hack End : HRYM

        $className = 'terrain'.$zone['type'];
        if (class_exists($className))
        {
            $terrain = new $className();
        }
        else{
            $terrain = new terrain();
        }
        $terrain->type = $zone['type'];
        $terrain->id = $zone['id'];
        $terrain->capacity = $zone['capacity'];
        $terrain->visible = $zone['visible'];
        $terrain->boundaries = $zone['boundaries'];
        $terrain->height = $zone['height'];
        $terrain->units = array_filter(mythicbattlesragnarok::$instance->units, function ($unit)  use ($zone) {return $unit->zone_id == $zone['id'];});
            
        return $terrain;
    }

    public function getDistanceWith($otherzone)
    {
        $ret = -1;

        $alreadyChecked = array();
        $alreadyChecked[$this->id] = $this;
        $next = array();
        $next[$this->id] = $this;

        while(count($next)>0)
        {
            $ret ++;
            $totest = $next;
            $next = array();
            foreach($totest as $zoneid => $zone)
            {
                if($zone == $otherzone)
                {
                    return $ret;
                }
                foreach($zone->boundaries as $nextzoneid => $boundaryType)
                {
                    if(!array_key_exists($nextzoneid, $alreadyChecked))
                    {
                        $nextzone = mythicbattlesragnarok::$instance->zones[$nextzoneid];
                        $alreadyChecked[$nextzoneid] = $nextzone;
                        $next[$nextzoneid] = $nextzone;                        
                    }
                }  
            }
        }

        return -1;
    }

    public function getZonesAtDistance($min, $max)
    {
        $ret = array();
        $alreadyChecked = array();
        $alreadyChecked[$this->id] = $this;
        $next = array();
        $next[$this->id] = $this;

        if($min<=0 && $max >= 0)
        {
            $ret[$this->id] = $this;
        } 

        while($max>0 && count($next)>0)
        {
            $totest = $next;
            $next = array();
            $min--;
            $max--;
            foreach($totest as $zoneid => $zone)
            {
                foreach($zone->boundaries as $nextzoneid => $boundaryType)
                {
                    if(!array_key_exists($nextzoneid, $alreadyChecked))
                    {
                        $nextzone = mythicbattlesragnarok::$instance->zones[$nextzoneid];
                        $alreadyChecked[$nextzoneid] = $nextzone;
                        if($min<=0 && $max >= 0)
                        {
                            $ret[$nextzoneid] = $nextzone;
                        }
                        $next[$nextzoneid] = $nextzone;
                    }
                }  
            }
        }
        return $ret;
    }

    public function getUnitsWithin($min, $max, $player_id, $withEnemy, $withAlly)
    {
        $selectedUnits = array();
        $zones = $this->getZonesAtDistance($min, $max);

        foreach($zones as $zone)
        {           
            foreach($zone->units as $unit)
            {
                if($unit->player_id == $player_id && $withAlly)
                {
                    $selectedUnits[$unit->id] = $unit;
                }
                if($unit->player_id != $player_id && $withEnemy)
                {
                    $selectedUnits[$unit->id] = $unit;
                }
            }
        
        }
        return $selectedUnits;
    }

    public function isFull()
    {
        return count($this->units) >= $this->capacity;
    }

    public function canEnter($unit, $forcemove = false)
    {   
        $ret = true;
        $ret = $ret && $this->isMovementAllowed($unit);
        $ret = $ret && !$this->isFull();
        $ret = $ret && ($this instanceof terrainWater || !in_array("forcewater", $unit->status));

        if($ret && array_key_exists($this->id, $unit->zone->boundaries))
        {
            $boundary = boundary::Create($unit->zone, $this);
            $ret = $ret && $boundary->isMovementAllowed($unit, $forcemove);
        }

        return $ret;
    }

    public function isMovementAllowed($unit, $forcemove = false)
    {
        return true;
    }

    public function isObstacle()
    {
        return count($this->units) > 0;
    }

    public function onEnter($unit)
    {
    }
    
    public function onExit($unit)
    {
    }

    public function onTiming($time, $attack)
    {
        
    }

    public function getStatBonus($stat, $attack)
    {
        if($stat == RANGE && $attack->from->zone == $this && $attack->from->zone->height == 1 && ($attack->to == null || $attack->to->zone->height == 0))
        {
            return 1;
        }
        if($stat == OFFENSE && $attack->from->zone == $this && $attack->from->zone->height == 1 && ($attack->to == null || $attack->to->zone->height == 0))
        {
            return 1;
        }
        return 0;
    }

    public function ignoreObstacle($from, $to) {
        return $from == $this && $from->height == 1;
    }
    
    public function countAsFor($unit)
    {
        $type = $this->type;

        //Hack Start : NJORD
        if($this->units != null)
        {
        foreach($this->units as $unittest)
        {
                if($unittest instanceof unit21 && !in_array("run", $unittest->status) && !in_array("walk", $unittest->status) && ($unit->player_id != $unittest->player_id || $unit == $unittest))
                {
                    $type = WATER;
                    break;
                }
            }
        }
        //Hack End : NJORD
        return $type;
    }

    public function canUse($unit, $item)
    {
        $ret = $this->id != 0 && $this->id>-3;
        $type = $this->countAsFor($unit);
        if($type != $this->type)
        {
            $name = "terrain".$type;
            $water = new $name();
            $water->type = $type;
            $water->id = $this->id;
            $water->units = $this->units;
            $ret = $ret && $water->canUse($unit, $item);
        }
        return $ret;

    }
    
}