<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\SensorData;
use App\Models\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    public function index()
    {
        $latestStatus = Status::latest()->first();
        $latestStatus = $latestStatus ?? new Status();

        // $sensorListData = $latestStatus->sensor_list ? json_decode($latestStatus->sensor_list, true) : [];

        // Ambil data suhu (Temperature)
        $temperature = $this->formatTemperature($latestStatus->temp_current ?? 0);

        // Ambil data pH
        $ph = $this->formatPH($latestStatus->ph_current ?? 0);

        // Ambil data (Salt)
        $salt = $this->formatSalt($latestStatus->salinity_current ?? 0);

        // Ambil data ORP (Sanitation)
        $orp = $this->formatORP($latestStatus->orp_current ?? 0);

        // Ambil data konduktivitas (Conductivity)
        $conductivity = $this->formatConductivity($latestStatus->ec_current ?? 0);

        // Ambil data TDS
        $tds = $this->formatTDS($latestStatus->tds_current ?? 0);

        $data = [
            'temperature' => $temperature,
            'ph' => $ph,
            'salt' => $salt,
            'orp' => $orp,
            'conductivity' => $conductivity,
            'tds' => $tds,
        ];

        return view('dashboards.smart-home', $data);
    }

    private function formatTemperature($value)
    {
        $formattedValue = number_format($value, 2, '.', '');

        return [
            'value' => $formattedValue,
            'unit' => '°C',
        ];
    }

    private function formatPH($value)
    {
        return [
            'value' => $value,
            'unit' => 'pH',
        ];
    }

    private function formatSalt($value)
    {
        return [
            'value' => $value,
            'unit' => 'mg/l',
        ];
    }

    private function formatORP($value)
    {
        return [
            'value' => $value,
            'unit' => 'mV',
        ];
    }

    private function formatConductivity($value)
    {
        return [
            'value' => $value,
            'unit' => 'μS/cm',
        ];
    }

    private function formatTDS($value)
    {
        return [
            'value' => $value,
            'unit' => 'ppm',
        ];
    }
}
