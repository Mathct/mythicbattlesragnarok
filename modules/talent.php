<?php 

include("talents/talentBolster.php"); 
include("talents/talentLeader.php"); 
include("talents/talentTerror.php"); 
include("talents/talentBlock.php"); 
include("talents/talentGuard.php"); 
include("talents/talentInitiative.php"); 
include("talents/talentMobility.php"); 
include("talents/talentForceOfNature.php"); 
include("talents/talentMightyThrow.php"); 
include("talents/talentBerserk.php"); 
include("talents/talentClimb.php"); 
include("talents/talentTorment.php"); 
include("talents/talentGemCollector.php"); 
include("talents/talentSneakAttack.php"); 
include("talents/talentCloseProtection.php"); 
include("talents/talentCloseCombat.php"); 
include("talents/talentSlayer.php"); 
include("talents/talentScout.php"); 
include("talents/talentArchery.php"); 

include("traits/talentTerrestrial.php"); 
include("traits/talentAquatic.php"); 
include("traits/talentFireproof.php"); 
include("traits/talentBoreal.php"); 
include("traits/talentFlying.php"); 

class talent extends APP_GameClass
{
    public $name = 'todo';
    public $description = 'todo';
    public $trait = false;
  
    public function getStatBonus($stat, $to, $attack)
    {
        return 0;
    }

    public function onTiming($time, $attack)
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

}