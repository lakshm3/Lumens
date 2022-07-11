<?php


namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Trip;
use App\Models\Engine;
use App\Models\EngineData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EngineRessource;

class EngineController extends Controller
{

    public function index(Request $request)
    {

        return EngineRessource::collection(Engine::all());
    }

    public function storeData(Request $request)
    {


        $json = $request->json()->all();
        if ($json == []) {
            $text = trim($request->getContent());
            $text = str_replace("}{", "},{", $text);
            $text = str_replace("}\n{", "},{", $text);
            $text = str_replace("}\n\n{", "},{", $text);
            $text = str_replace("}\r\n{", "},{", $text);
            $text = str_replace("}\r\n\r\n{", "},{", $text);
            if (strpos($text, "[", 0) !== 0) $text = "[" . $text . "]";
            $json = json_decode($text, true);
        } else {
            $json = [$json];
        }
        $engineCreated = 0;
        $dataAdded = 0;
        if (!is_array($json)) return ["success" => false, "reason" => "not_a_array", "content" => $text];
        foreach ($json as $key => $val) {
            foreach ($val as $date => $value) {

                if (isset($value["Engine"])) {
                    $engine = Engine::where("engine_vib_id", $value["Engine"]["Id"])
                        ->where("name", $value["Engine"]["Name"])
                        ->where("process_type", $value["Engine"]["processType"])
                        ->first();
                    if ($engine == null) {
                        $engine = new Engine();
                        $engine->name = $value["Engine"]["Name"];
                        $engine->process_type = $value["Engine"]["processType"];
                        $engine->engine_vib_id = $value["Engine"]["Id"];
                        $engine->type = $_GET["type"] ?? "vibox";
                        $engine->save();
                        $engineCreated++;
                    }
                    if ($date == null || strlen($date) < 4) $date = $value["DateAndTime"];

                    if (EngineData::where("engine_id", $engine->id)->where("datetime", $date)->first() == null) {
                        $engineData = new EngineData();
                        $engineData->engine_id = $engine->id;
                        $engineData->datetime = $date;
                        $engineData->data = trim(json_encode($value));
                        $engineData->save();
                        $dataAdded++;
                    }
                } else {
                    continue;
                }
            }
        }

        return [
            "success" => true,
            "engine_created" => $engineCreated,
            "data_added" => $dataAdded
        ];
    }
}
