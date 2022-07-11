<?php


namespace App\Http\Controllers;


use App\Models\EngineData;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index(Request $request){

        $lastData = [];
        foreach($trip->engines as $engine)
        {
            $engineData = EngineData::where("engine_id",$engine->id)->orderBy("ts","DESC")->first();
            if($engineData!=null)
            {
                $json = $engineData->json;
                $lastData[$engine->id] = $json;
            }
        }

        if(strpos($request->fullUrl(),"oryx") != FALSE) $blade = "oryx/dashboard";
        else $blade = "dashboard";

        return view("dashboard",[
            "title" => "Dashboard",
            "selected" => "dashboard",
            "lastData" => $lastData
        ]);
    }
}
