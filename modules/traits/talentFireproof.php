<?php 

class talentFireproof extends talent
{    
    public function __construct()
    {
        $this->name = clienttranslate('Fireproof');
        $this->description = clienttranslate('Fireproof units ignore all Burning terrain effects.');
        $this->trait = true;
    }

    public function getAuraStatus($to)
    {
        $ret = parent::getAuraStatus($to);
        if($to == $this->unit)
        {
            $ret[] = "ignoreBurning";
        }
        return $ret;
    }
}