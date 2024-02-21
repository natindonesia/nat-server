<?php

namespace App\Http\Controllers;

use App\Models\AppSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WaterpoolController extends Controller
{


    public static function getStates(string $deviceName = null, int $limit = 15): array
    {
        if ($deviceName == null) $deviceName = AppSettings::$natwaveDevices[0];
        $datas = SensorDataController::getStats($deviceName, $limit);

        $sensors = [];
        for ($i = 0; $i < $limit; $i++) {
            $state = [];
            $averageTimestamp = 0;
            $latestTimestamp = 0;

            foreach ($datas as $sensor => $data) {
                if (!isset($data['data'][$i])) {
                    $state[$sensor] = 0.0;
                } else {
                    $state[$sensor] = $data['data'][$i];
                }
                $averageTimestamp += isset($data['timestamp'][$i]) ? strtotime($data['timestamp'][$i]) : 0;
                $latestTimestamp = max($latestTimestamp, isset($data['timestamp'][$i]) ? strtotime($data['timestamp'][$i]) : 0);
            }

            if (count($datas) == 0) continue;
            $averageTimestamp /= count($datas);
            $state['timestamp'] = $averageTimestamp;
            $state['latestTimestamp'] = $latestTimestamp; // in here as not to fuck the array
            $sensors[] = $state;
        }


        return $sensors;
    }

    public static function formatStates(array $states): array
    {
        $formattedStates = [];
        foreach ($states as $state) {
            $formattedState = [];
            foreach ($state as $sensorOrEntityName => $value) {
                $sensor = AppSettings::entityToSensorName($sensorOrEntityName);
                $formattedState[$sensor] = self::formatSensor($sensor, $value);
            }
            $formattedStates[] = $formattedState;
        }
        return $formattedStates;
    }


    public static function formatSensor(string $sensor, $value)
    {
        if ($sensor == 'timestamp' || $sensor == 'latestTimestamp')
            return date('Y-m-d H:i:s', $value);
        if (in_array($sensor, AppSettings::$ignoreSensors)) return $value;

        $sensor_name = AppSettings::entityToSensorName($sensor);

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
            case 'ch':
                return StatusController::formatChlorine($value);
            case 'battery':
                return StatusController::formatBattery($value);
            default:
                //throw new \Exception("Unknown sensor: {$sensor_name}");
                Log::warning("Unknown sensor: {$sensor_name}");
                return [
                    'value' => $value,
                    'unit' => '',
                    'label' => __('translation.' . $sensor_name),
                ];
        }
    }

    public function index()
    {
        $deviceName = request()->query('device');
        if (!$deviceName) $deviceName = AppSettings::$natwaveDevices[0];


        return view('waterpool/5-table-status', [
            'deviceName' => $deviceName,
        ]);
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
