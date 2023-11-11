<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SensorDataController extends Controller
{
    public function index()
    {
        $status = SensorData::latest()->get();
    
        $dataUpdate = SensorData::latest()->first();
        $dataUpdate->temp_current = $this->convertToDecimal($dataUpdate->temp_current);

        $originalPH = $dataUpdate->ph_current;
        $dataUpdate->ph_current = $this->convertToPercentage($originalPH);


        $chartData = $this->getChartData();
        // dd($dataUpdate); //; dd('Before Formatting:', $dataUpdate->toArray());
        return view('dashboards/detailed-dashboard', compact('status', 'dataUpdate', 'chartData'));
    }

    private function convertToDecimal($value)
    {
        // Mengonversi nilai temperatur dari format 263 menjadi 26.3
        $intValue = intval($value);
        $decimalValue = $intValue / 10.0; // Menggunakan 10.0 agar hasilnya berupa bilangan pecahan
        return number_format($decimalValue, 1, '.', '');
    }
    private function convertToPercentage($value)
    {
        // Mengonversi nilai pH dari format 498 menjadi 4.98
        $intValue = intval($value);
        $decimalValue = $intValue / 100.0; // Menggunakan 100.0 agar hasilnya berupa bilangan pecahan
        return number_format($decimalValue, 2, '.', '');
    }

    private function getChartData()
    {
        // Mengambil data historis untuk grafik
        $chartData = DB::table('sensor_data')
            ->select('created_at', 'temp_current', 'ph_current', 'tds_current', 'ec_current', 'salinity_current')
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->limit(10)
            ->get();

        // Formatting data untuk grafik
        $formattedChartData = [
            'labels' => $chartData->pluck('created_at'),
            'temp' => $chartData->pluck('temp_current'),
            'ph' => $chartData->pluck('ph_current'),
            'tds' => $chartData->pluck('tds_current'),
            'ec' => $chartData->pluck('ec_current'),
            'salinity' => $chartData->pluck('salinity_current'),
        ];
        return $formattedChartData;
    }
}