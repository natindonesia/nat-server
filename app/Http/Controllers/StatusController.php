<?php

namespace App\Http\Controllers;

use App\Models\Carbon;
use App\Models\State;
use App\Models\StateMeta;

class StatusController extends Controller
{

    // allow for multiple devices
    protected function getState($deviceName = 'natwave')
    {
        $sensors = [
            'ec', // Conductivity
            'humid', // Salt
            'orp', // Sanitation
            'ph', // pH acidity
            'tds', // TDS
            'temp' // Temperature
        ];

        // Required for converting entity_id to attributes_id
        $entityIds = [];
        // e.g sensor.natwave_ec
        foreach ($sensors as $sensor) {
            $entityIds[] = "sensor.{$deviceName}_{$sensor}";
        }

        // Required for querying states table
        $metadataToEntityIds = [];
        $metadatas = StateMeta::whereIn('entity_id', $entityIds)->get()->toArray();
        $metadataIds = [];
        foreach ($metadatas as $metadata) {
            $metadataToEntityIds[$metadata['metadata_id']] = $metadata['entity_id'];
            $metadataIds[] = $metadata['metadata_id'];
        }

        // Get latest states for each metadata
        $latestStates = [];

        // Laravel mad, we do one by one
        foreach ($metadataIds as $metadataId) {
            // Get latest state
            $state = State::where('metadata_id', $metadataId)->orderBy('last_updated_ts', 'desc')->first();
            if (empty($state)) continue;
            $latestStates[$metadataToEntityIds[$metadataId]] = $state->toArray();
        }



        // Ambil data suhu (Temperature)
        $temperature = $this->formatTemperature(floatval($latestStates["sensor.{$deviceName}_temp"]['state'] ?? 0));

        // Ambil data pH
        $ph = $this->formatPH(floatval($latestStates["sensor.{$deviceName}_ph"]['state'] ?? 0));

        // Ambil data (Salt)
        $salt = $this->formatSalt(floatval($latestStates["sensor.{$deviceName}_humid"]['state'] ?? 0));

        // Ambil data ORP (Sanitation)
        $orp = $this->formatORP(floatval($latestStates["sensor.{$deviceName}_orp"]['state'] ?? 0));

        // Ambil data konduktivitas (Conductivity)
        $conductivity = $this->formatConductivity(floatval($latestStates["sensor.{$deviceName}_ec"]['state'] ?? 0));

        // Ambil data TDS
        $tds = $this->formatTDS(floatval($latestStates["sensor.{$deviceName}_tds"]['state'] ?? 0));

        $data = [
            'temperature' => $temperature,
            'ph' => $ph,
            'salt' => $salt,
            'orp' => $orp,
            'conductivity' => $conductivity,
            'tds' => $tds,
        ];
        return $data;
    }

    public function index()
    {

        $data = $this->getState();
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
