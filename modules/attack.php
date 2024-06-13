<?php 

const ATNORMAL    = 'NORMAL';
const RETALIATE = 'RETALIATE';
const AREA      = 'AREA';

class attack
{
    public $from = null;
    public $to = null;
    public $offense = 0;
    public $defense = 0;
    public $range = 0;
    public $type = ATNORMAL;
    public $wounds = 0;
    public $aerial = false;

    public static function fromJSON($json)
    {
        if(is_array($json))
        {
            $data = $json;
        }
        else{
            $data = json_decode($json, true); // Décoder le JSON en tableau associatif                
        }
        $att = new attack();
        $att->offense = $data['offense'];
        $att->defense = $data['defense'];
        $att->range = $data['range'];
        $att->type = $data['type'];
        $att->wounds = $data['wounds'];
        if(array_key_exists('from',$data))
        {
            $att->from = mythicbattlesragnarok::$instance->units[$data['from']];
        }
        if(array_key_exists('to',$data))
        {
            $att->to = mythicbattlesragnarok::$instance->units[$data['to']];
        }
        return $att;
    }

    public function toJSON()
    {
        $data = array(
            'offense' => $this->offense,
            'defense' => $this->defense,
            'range' => $this->range,
            'type' => $this->type,
            'wounds' => $this->wounds,
        );

        if($this->from != null)
        {
            $data['from'] = $this->from->id;
        }
        if($this->to != null)
        {
            $data['to'] = $this->to->id;
        }
        return json_encode($data);
    }

}