<?php 

for($i = 1; $i<=8;$i++)
{
    include("attachments/attachment{$i}.php");    
}

class attachment extends APP_GameClass
{    
    public $title = "";
    public $description = "";
    public $unit;
    public $id = 0;

    public function getStatBonus($stat, $to, $attack)
    {
        return 0;
    }

    public function onTiming($time, $attack = NULL)
    {
    }

    public function getAuraStatus($to)
    {
        return array();
    }

    public function getExtraActions($simple)
    {
        return array();
    }

    public function canBeAttachedTo($unit)
    {
        return $unit->category == TROOP && $unit->attachment->id == 0;
    }
}