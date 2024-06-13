<?php 

const PERMANENT = 'PERMANENT';
const PASSIVE   = 'PASSIVE';
const ACTIVE    = 'ACTIVE';
const OFFENSIVE = 'OFFENSIVE';
const WHITE     = 1;
const BLACK     = 0;

class power extends APP_GameClass
{
    public $white = 0;
    public $type = PERMANENT;
    public $title = "";
    public $description = "";
    public $aow = 0;
    public $token = 0;
    public $index = 0;

    public function __construct($index, $type, $white,$title, $description, $aow=0, $token=0)
    {
        $this->white = $white;
        $this->type = $type;
        $this->title = $title;
        $this->description = $description;
        $this->aow = $aow;
        $this->token = $token;
        $this->index = $index;
    }

}