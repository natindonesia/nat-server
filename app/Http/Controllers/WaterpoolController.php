<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\StateMeta;
use Illuminate\Http\Request;

class WaterpoolController extends Controller
{


    public static function getStates(string $deviceName = 'natwave', int $limit = 15): array
    {
        $sensors = StateMeta::$sensors;
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

        // Get states for each metadata
        $sensors = [
            // sensor => [data => [...]
        ];

        // Laravel mad, we do one by one

        for ($i = 0; $i < $limit; $i++) { // 15 items

            $sensor = [];
            $timestamp = [
                // 1703656360.509
            ];
            foreach ($metadataIds as $metadataId) {
                // Get latest state
                $state = State::where('metadata_id', $metadataId)->orderBy('last_updated_ts', 'desc')->limit(1)->offset($i)->first();
                if (empty($state)) continue;
                $sensor[$state->metadata->entity_id] = $state->state;
                $timestamp[$state->metadata->entity_id] = $state->last_updated_ts;
            }
            // average timestamp
            $sensor['timestamp'] = array_sum($timestamp) / count($timestamp);
            $sensors[] = $sensor;

        }


        return $sensors;
    }

    public static function formatStates(array $states): array
    {
        $formattedStates = [];
        foreach ($states as $state) {
            $formattedState = [];
            foreach ($state as $sensor => $value) {
                $formattedState[$sensor] = self::formatSensor($sensor, $value);
            }
            $formattedStates[] = $formattedState;
        }
        return $formattedStates;
    }

    public static function formatSensor(string $sensor, $value)
    {
        if ($sensor == 'timestamp') return date('Y-m-d H:i:s', $value);
        $sensor_name = explode('_', $sensor)[1];
        switch ($sensor_name) {
            case 'ec':
                return StatusController::formatConductivity($value);
            case 'humid':
                return StatusController::formatSalt($value);
            case 'orp':
                return StatusController::formatORP($value);
            case 'ph':
                return StatusController::formatPH($value);
            case 'tds':
                return StatusController::formatTDS($value);
            case 'temp':
                return StatusController::formatTemperature(floatval($value));
            default:
                throw new \Exception("Unknown sensor: {$sensor_name}");
        }
    }

    public function index()
    {


        $status = $this->getStates();
        return view('waterpool/5-table-status', compact('status'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
