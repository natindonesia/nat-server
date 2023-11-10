<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    public function index()
        $status = SensorData::latest()->get();
        
        $dataUpdate = SensorData::latest()->first();
        $dataUpdate->temp_current = $this->convertToDecimal($dataUpdate->temp_current);

        $originalPH = $dataUpdate->ph_current;
        {
        // $status = SensorData::first()->get();
        $status = SensorData::latest()->get();
        return view('waterpool/5-table-status', compact('status'));
    }
}
