<?php 

class talentBoreal extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Boreal');
        $this->description = clienttranslate('Boreal units ignore all Polar terrain effects.');
        $this->trait = true;
    }


    public function getAuraStatus($to)
    {
        $ret = parent::getAuraStatus($to);
        if($to == $this->unit)
        {
            $ret[] = "ignorePolar";
        }
        return $ret;
    }
}