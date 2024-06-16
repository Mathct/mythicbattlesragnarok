<?php 


class action extends APP_GameClass
{
    public $player_id = 0;
    public $unit_id = 0;
    public $unit = NULL;
    public $function = '';
    public $parg1 = '';
    public $parg2 = '';
    public $parg3 = '';
    public $parg4 = '';
    public $varg1 = '';
    public $varg2 = '';

    public static function fromPending()
    {        
        $pending =  mythicbattlesragnarok::$instance->getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $action = new action();
        $action->player_id = $pending['player_id'];
        $action->player = mythicbattlesragnarok::$instance->playersMBR[$action->player_id];
        $action->unit_id = $pending['unit_id'];
        if($action->unit_id>0)
        {
            $action->unit = mythicbattlesragnarok::$instance->units[$action->unit_id];
        }
        $action->function = $pending['function'];
        $action->parg1 = $pending['arg'];
        $action->parg2 = $pending['arg2'];
        $action->parg3 = $pending['arg3'];
        $action->parg4 = $pending['arg4'];
        return $action;
    }

    public static function fromJSON($json)
    {
        $json = str_replace('"{', '{', $json);
        $json = str_replace('}"', '}', $json);

        $data = json_decode($json, true); // Décoder le JSON en tableau associatif
        
        $action = new action();
        $action->player_id = $data['player_id'];
        $action->player = mythicbattlesragnarok::$instance->playersMBR[$action->player_id];
        $action->unit_id = $data['unit_id'];
        if($action->unit_id>0)
        {
            $action->unit = mythicbattlesragnarok::$instance->units[$action->unit_id];
        }
        $action->function = $data['function'];
        $action->parg1 = $data['arg'];
        $action->parg2 = $data['arg2'];
        $action->parg3 = $data['arg3'];
        $action->parg4 = $data['arg4'];
        $action->varg1 = $data['varg1'];
        $action->varg2 = $data['varg2'];
        return $action;
    }

    public function toPending()
    {
        $ret = array(
            'player_id' => $this->player->id,
            'unit_id' => 0,
            'function' => $this->function,
            'arg' => $this->parg1,
            'arg2' => $this->parg2,
            'arg3' => $this->parg3,
            'arg4' => $this->parg4,
            'varg1' => $this->varg1,
            'varg2' => $this->varg2
        );

        if($this->unit != null)
        {
            $ret['unit_id'] = $this->unit->id;
        }

        return $ret;
    }

    public function toJSON()
    {
        return json_encode($this->toPending());
    }

}
