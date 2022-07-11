<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EngineData extends Model
{
    public $table = "engine_data";

    public function getDataAttribute($data){
        $json  = json_decode($data,true);
        if(!isset($json["InjectionOver"]) && isset($json["Combustion"])) $json["InjectionOver"] = $json["Combustion"];
        return $json;
    }
    public function getTsAttribute(){
        return  Carbon::createFromFormat('Y-m-d H:i:s' , $this->datetime ,'UTC')->timestamp;
    }
}
