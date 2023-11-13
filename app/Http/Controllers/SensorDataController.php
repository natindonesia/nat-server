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
        if ($dataUpdate !== null) {

            $dataUpdate->temp_current = $this->convertToDecimal($dataUpdate->temp_current);

            // dd($dataUpdate);
        } else {

            $defaultTemp = 0; // Set a default value or adjust as needed
            \Illuminate\Support\Facades\Log::info('No data found in sensor_data table.');


            $dataUpdate = new SensorData();
            $dataUpdate->temp_current = $defaultTemp;

            // dd($dataUpdate);
        }


        // $dataUpdate->temp_current = $this->convertToDecimal($dataUpdate->temp_current);
        // dd($dataUpdate);

        $originalPH = $dataUpdate->ph_current;
        $dataUpdate->ph_current = $this->convertToPercentage($originalPH);

        $chartData = $this->getChartData();
// <<<<<<< nat-server-dashboard-level1
//         //; dd('Before Formatting:', $dataUpdate->toArray());
//         return view('dashboards/detailed-dashboard', compact('status', 'dataUpdate', 'chartData'));
// =======
        $chartDataWeekly = $this->getWeeklyChartData();
        // dd($dataUpdate); //; dd('Before Formatting:', $dataUpdate->toArray());
        return view('dashboards/detailed-dashboard', compact('status', 'dataUpdate', 'chartData', 'chartDataWeekly'));
// >>>>>>> master
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

    private function getChartData()
    {
        $now = Carbon::now();
        $interval = 8; // Jam

        // Hitung waktu mulai untuk interval terakhir
        $lastIntervalStart = $now->copy()->subHours($interval);

        // dd($lastIntervalStart);
        $chartData = DB::table('sensor_data')
            ->select('created_at', 'temp_current', 'ph_current', 'tds_current', 'ec_current', 'salinity_current')
            ->whereBetween('created_at', [$lastIntervalStart, $now])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->reverse();


        // Debugging statements
        // dd($chartData);
        $formattedChartData = [
            'labels' => $chartData->pluck('created_at')->map(function ($timestamp) {
                return Carbon::parse($timestamp)->format('H:i');
            }),
            'temp' => $chartData->pluck('temp_current'),
            'ph' => $chartData->pluck('ph_current'),
            'tds' => $chartData->pluck('tds_current'),
            'ec' => $chartData->pluck('ec_current'),
            'salinity' => $chartData->pluck('salinity_current'),
        ];

        return $formattedChartData;
    }

    private function getWeeklyChartData()
    {
        $now = Carbon::now();
        $lastSevenDays = $now->subDays(7);


        $chartDataWeekly = DB::table('sensor_data')
            ->selectRaw('DATE(created_at) as date, AVG(temp_current) as temp_avg, AVG(ph_current) as ph_avg, AVG(tds_current) as tds_avg, AVG(ec_current) as ec_avg, AVG(salinity_current) as salinity_avg')
            ->where('created_at', '>=', $lastSevenDays)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // dd($chartDataWeekly);


        $formattedChartDataWeekly = [
            'labels' => $chartDataWeekly->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('D, M d');
            }),
            'temp' => $chartDataWeekly->pluck('temp_avg'),
            'ph' => $chartDataWeekly->pluck('ph_avg'),
            'tds' => $chartDataWeekly->pluck('tds_avg'),
            'ec' => $chartDataWeekly->pluck('ec_avg'),
            'salinity' => $chartDataWeekly->pluck('salinity_avg'),
        ];

        return $formattedChartDataWeekly;
    }
}
