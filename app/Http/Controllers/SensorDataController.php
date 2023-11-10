<?php

namespace App\Http\Controllers;

use App\Models\SensorData;

use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    public function index()
    {
        $status = SensorData::latest()->get();

        $dataUpdate = SensorData::latest()->get();
        $dataUpdate->temp_current = $this->convertToDecimal($dataUpdate->temp_current);

        $originalPH = $dataUpdate->ph_current;
        $dataUpdate->ph_current = $this->convertToPercentage($originalPH);

        return view('dashboard/dashboard-smart-home', compact('status', 'dataUpdate'));
    }

    private function convertToDecimal($value)
    {
        $intValue = intval($value);
        $decimalValue = $intValue / 10.0;
        return number_format($decimalValue, 1, '.', '');
    }
    private function convertToPercentage($value)
    {
        $intValue = intval($value);
        $decimalValue = $intValue / 100.0;
        return number_format($decimalValue, 2, '.', '');
    }
}