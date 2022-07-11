<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserInterface;
use Illuminate\Http\Request;

class UserInterfaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserInterface::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:50",
            "config" => "required|string|max:60000"
        ]);

        $interface = new UserInterface();
        $interface->user_id = 1;
        $interface->name = $request["name"];
        $interface->config = $request["config"];
        $interface->save();
        return $interface;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserInterface  $interface
     * @return \Illuminate\Http\Response
     */
    public function show(UserInterface $interface)
    {
        return $interface;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserInterface  $interface
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserInterface $interface)
    {
        $request->validate([
            "name" => "required|string|max:50",
            "config" => "required|string|max:60000"
        ]);

        $interface->user_id = 1;
        $interface->name = $request["name"];
        $interface->config = $request["config"];
        $interface->save();
        return $interface;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserInterface  $interface
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserInterface $interface)
    {
        $interface->delete();
    }
}
