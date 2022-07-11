<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Engine;
use App\Models\EngineData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EngineDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Engine  $engine
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Engine $engine)
    {


        $timeStart = \microtime(true);

        $granularity = $request->get("granularity","1min");
        $lastPoint = $request->get("lastPoint",null);

        if($request->has("live"))
        {

            $date = Carbon::now();
            $tdate = $date->format("Y-m-d H:i:s");
            
            if($granularity == "1min") $fdate = $date->addHour(-1);
            if($granularity == "10min") $fdate = $date->addHour(-10);
            if($granularity == "1hour") $fdate = $date->addDay(-1);
            if($granularity == "1day") $fdate = $date->addDay(-10);
            if($granularity == "1week") $fdate = $date->addDay(-70);


            $fdate = $fdate->format("Y-m-d H:i:s");
            
            if($lastPoint != null)
            {
                $fdate = $lastPoint;
            }
        }else{
            
            if($request->has("toDate"))
            {
                $tdate = $request->get("toDate");
                $fdate = $request->get("fromDate");
            }else{

                $date = Carbon::now("UTC");
                $tdate = $date->addDays(1)->format("Y-m-d H:i");
                $fdate = $date->addDays(-5)->format("Y-m-d H:i");
            }
        }

        $dbRequest = EngineData::where("engine_id",$engine->id)
                ->where("datetime","<=",$tdate)
                ->where("datetime",">=",$fdate)
                ->orderBy("datetime","ASC");

        if($granularity == "1min") $sample = 60;
        if($granularity == "10min") $sample = 60*10;
        if($granularity == "1hour") $sample = 60*60;
        if($granularity == "1day") $sample = 24*60*60;
        if($granularity == "1week") $sample = 7*24*60*60;

        $data = $this->dateSampleFast($dbRequest, $sample);

        $measures = [];
        foreach($data as $d){
            $measures[$d->ts] = $d->data; 
        }
        return [
            "measures" => $measures,
            "time_to_retrieve" => microtime(true) - $timeStart,
            "from_date" => $fdate,
            "to_date" => $tdate
        ];
    }

    private function granularize($db, $sample){
        
        $countPoints = $db->count();
        $step = $countPoints/$granularity;
        if($step<1)  return $db->get();
        else{
            $ids = $db->select("id")->get();
            $idsToRetrieve = [];
            for($i=0;$i<$countPoints;$i+=$step)
            {
                $idsToRetrieve[] = $ids[$i]["id"];
            }
            $idsToRetrieve[] = $ids[$countPoints-1]["id"];
            return $db->whereIn("id",$idsToRetrieve)->get();
        }

    }

    private function getTSdb(){
        return config("database.default") == "sqlite" ? "strftime('%s', datetime)":"UNIX_TIMESTAMP(datetime)";
    }

    private function dateSampleFast($db, $sample){


        $datas = $db->groupBy(DB::raw("ROUND( ".$this->getTSdb()."/".$sample.")"))->get();
        return $datas;
    }

    private function dateSample($db, $sample){
        
        $datas = $db->select("id","ts","data")->get();
        $dataSelected = collect([]);
        $count = count($datas);
        if($count == 0) return collect([]);
        $dataSelected[] = $datas[0];
        $lastDateTime = $datas[0]["ts"];
        for($i=1;$i<$count;$i++)
        {
            if($this->canPickThisValueWithSample($lastDateTime,$datas[$i]["ts"], $sample)){
                
                $dataSelected[] = $datas[$i];
                $lastDateTime = $datas[$i]["ts"];
            }
        }
        return $dataSelected;
    }
    private function canPickThisValueWithSample($lastDt, $currentDt, $sample)
    {
        $lastTS = strtotime($lastDt);
        $currentTS = strtotime($currentDt);
        return ($currentTS-$lastTS)>$sample;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Engine  $engine
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Engine $engine)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Engine  $engine
     * @param  \App\Models\EngineData  $engineData
     * @return \Illuminate\Http\Response
     */
    public function show(Engine $engine, EngineData $engineData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Engine  $engine
     * @param  \App\Models\EngineData  $engineData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Engine $engine, EngineData $engineData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Engine  $engine
     * @param  \App\Models\EngineData  $engineData
     * @return \Illuminate\Http\Response
     */
    public function destroy(Engine $engine, EngineData $engineData)
    {
        //
    }
}
