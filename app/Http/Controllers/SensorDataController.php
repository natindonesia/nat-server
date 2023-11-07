<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    public function index(){
        $status = SensorData::all();
        return view('waterpool/5-table-status', compact('status'));
    }
}
