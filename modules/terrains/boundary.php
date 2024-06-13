<?php

const ESCARPMENT = "ESCARPMENT";
const NORMAL = "NORMAL";
const WALL = "WALL";

include("boundaryNORMAL.php"); 
include("boundaryWALL.php");  
include("boundaryESCARPMENT.php");  

class boundary extends APP_GameClass
{
    public $from;
    public $to;
    public $type;

    public static function Create($from, $to)
    {
        $type =  $from->boundaries[$to->id];
        $className = 'boundary'.$type;
        if (class_exists($className))
        {
            $boundary = new $className();
        }
        else{
            $boundary = new boundary();
        }
        $boundary->from = $from;
        $boundary->to = $to;
        $boundary->type = $type;
        
        return $boundary;
    }

    public function isMovementAllowed($unit, $forcemove = false)
    {
        return true;
    }

    public function isObstacle()
    {
        return false;
    }
}