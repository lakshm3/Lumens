<?php

namespace App\Http\Controllers\Api;

use App\Models\Engine;
use App\Models\EngineData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EngineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $engines = Engine::all();
        foreach ($engines as $k => $engine) {
            $engines[$k]->measure = EngineData::where("engine_id", $engine->id)
                ->orderBy("datetime", "DESC")
                ->first()->data;
        }
        return $engines;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Engine  $engine
     * @return \Illuminate\Http\Response
     */
    public function show(Engine $engine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Engine  $engine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Engine $engine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Engine  $engine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Engine $engine)
    {
        EngineData::where("engine_id", $engine->id)->delete();
        $engine->delete();
    }
}
