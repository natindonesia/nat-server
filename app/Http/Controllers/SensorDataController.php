<?php

namespace App\Http\Controllers;

use App\Exports\SensorDataExport;
use App\Models\AppSettings;
use App\Models\SensorData;
use App\Models\State;
use App\Models\StateMeta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SensorDataController extends Controller
{

    /**
     * Guarantee to return same length for each sensor
     * @param string $deviceName based on AppSettings::$natwaveDevices
     * @param int $limit number of data to return
     * @param int|null $startTimestamp start timestamp
     * @param int|null $endTimestamp end timestamp
     * @param int $interval interval in seconds
     * @return array<string, array<string, array<int, mixed>>>
     */
    public static function getStats(string $deviceName, int $limit = 15, $startTimestamp = null, $endTimestamp = null, $interval = 60 * 1440): array
    {
        $metadata = StateMeta::getMetadata($deviceName);


        $metadataIds = $metadata['metadataIds'];


        // Get stats for each metadata
        $sensors = [
            /**
             * sensor_1 => [
             *  data => [...]
             *  timestamp => [...]
             */
        ];


        if (request()->has('date')) {
            try {
                $date = request()->get('date');

                $startTimestamp = strtotime($date);
                $endTimestamp = strtotime($date . ' +1 day');
            } catch (NotFoundExceptionInterface $e) {
            } catch (ContainerExceptionInterface $e) {
            }
        }
        if (request()->has('interval')) {
            try {
                $interval = request()->get('interval');
                $interval = intval($interval);
                $interval = $interval > 0 ? $interval : 60 * 30;

            } catch (NotFoundExceptionInterface $e) {
            } catch (ContainerExceptionInterface $e) {
            }
        }
        // Laravel mad, we do one by one



        foreach ($metadataIds as $metadataId) {
            // Get latest state
            /**
             * SELECT
             * FROM_UNIXTIME(last_updated_ts) AS formatted_timestamp,
             * state
             * FROM
             * states
             * GROUP BY
             * FLOOR(last_updated_ts / (30 * 60))
             * ORDER BY
             * formatted_timestamp;
             */
            DB::statement("SET sql_mode = ''");
            $state = State::selectRaw('metadata_id, state, FROM_UNIXTIME(last_updated_ts) AS formatted_timestamp, last_updated_ts')
                ->where('metadata_id', $metadataId);

            if ($startTimestamp !== null) {
                $state = $state->where('last_updated_ts', '>=', $startTimestamp);
                $state = $state->where('last_updated_ts', '<', $endTimestamp);
            }
            $state->where('state', '!=', 'unavailable');

            if ($interval !== null && $limit > 1) {
                $state = $state->groupBy(DB::raw('FLOOR(last_updated_ts / ' . $interval . ')'));
            }

            $state = $state->orderBy('formatted_timestamp', 'desc')
                ->take($limit);
            $state = $state->get();
            if (empty($state)) continue;
            $data = [];
            $timestamp = [];
            foreach ($state as $item) {
                $data[] = $item->state;
                $timestamp[] = date('Y-m-d H:i:s', $item->last_updated_ts);
            }

            $stateValue = $data[0] ?? 0.0;
            $sensors[$state->first()->metadata->entity_id] = [
                'data' => $data,
                'timestamp' => $timestamp,
                'format' => WaterpoolController::formatSensor($state->first()->metadata->entity_id, $stateValue),
            ];
        }


        return $sensors;
    }










    public static function getStats2(string $deviceName, int $limit = 7, $startTimestamp = null, $endTimestamp = null, $interval = 60 * 60*24): array
    {
        $metadata = StateMeta::getMetadata($deviceName);


        $metadataIds = $metadata['metadataIds'];


        // Get stats for each metadata
        $sensors = [
            /**
             * sensor_1 => [
             *  data => [...]
             *  timestamp => [...]
             */
        ];


        if (request()->has('date')) {
            try {
                $date = request()->get('date');

                $startTimestamp = strtotime($date. ' -7 day');
                $endTimestamp = strtotime($date . ' +1 day');
            } catch (NotFoundExceptionInterface $e) {
            } catch (ContainerExceptionInterface $e) {
            }
        }
        if (request()->has('interval')) {
            try {
                $interval = request()->get('interval');
                $interval = intval($interval);
                $interval = $interval > 0 ? $interval : 60 * 30;

            } catch (NotFoundExceptionInterface $e) {
            } catch (ContainerExceptionInterface $e) {
            }
        }
        // Laravel mad, we do one by one



        foreach ($metadataIds as $metadataId) {
            // Get latest state
            /**
             * SELECT
             * FROM_UNIXTIME(last_updated_ts) AS formatted_timestamp,
             * state
             * FROM
             * states
             * GROUP BY
             * FLOOR(last_updated_ts / (30 * 60))
             * ORDER BY
             * formatted_timestamp;
             */
            DB::statement("SET sql_mode = ''");
            $state = State::selectRaw('metadata_id, state, FROM_UNIXTIME(last_updated_ts) AS formatted_timestamp, last_updated_ts')
                ->where('metadata_id', $metadataId);

            if ($startTimestamp !== null) {
                $state = $state->where('last_updated_ts', '>=', $startTimestamp);
                $state = $state->where('last_updated_ts', '<', $endTimestamp);
            }
            $state->where('state', '!=', 'unavailable');

            if ($interval !== null) {
                $state = $state->groupBy(DB::raw('FLOOR(last_updated_ts / ' . $interval . ')'));
            }

            $state = $state->orderBy('formatted_timestamp', 'desc')
                ->take($limit);
            $state = $state->get();
            if (empty($state)) continue;
            $data = [];
            $timestamp = [];
            foreach ($state as $item) {
                $data[] = $item->state;
                $timestamp[] = date('Y-m-d H:i:s', $item->last_updated_ts);
            }
            $stateValue = $data[0] ?? 0.0;
            $sensors[$state->first()->metadata->entity_id] = [
                'data' => $data,
                'timestamp' => $timestamp,
                'format' => WaterpoolController::formatSensor($state->first()->metadata->entity_id, $stateValue),
            ];
        }


        return $sensors;
    }
















    public static $parameterThresholdDisplay = [
    ];




    public function index()
    {




        $deviceName = request()->get('device', AppSettings::$natwaveDevices[0]);
        //yes this is duplicate query, have problem ?
        $states = WaterpoolController::getStates($deviceName, 30);
        # $stats = SensorDataController::getStats($deviceName, 30);
        $stats2 = SensorDataController::getStats2($deviceName, 30);



        $data = [
            'formatted_states' => WaterpoolController::formatStates($states),
            'stats' => $stats2,
            'deviceName' => $deviceName,
        ];
        if (count($data['formatted_states']) !== 0)
        $data['formatted_state'] = $data['formatted_states'][0];
        else $data['formatted_state'] = [];



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

        $max_date = State::max('last_updated_ts');
        $min_date = State::min('last_updated_ts');
        $data['date_filter'] = [
            'max' => date('Y-m-d', $max_date),
            'min' => date('Y-m-d', $min_date),
        ];

        $data['parameterThresholdDisplay'] = self::$parameterThresholdDisplay;





        $device = [
            'name' => $deviceName,
            'display_name' => __('devices_name_'.$deviceName),
        ];

        $states = WaterpoolController::getStates($deviceName, 1);
        $states = WaterpoolController::formatStates($states);
        if (!empty($states)) {
            $device['state'] = $states[0];
        } else {
            $device['state'] = [];
        }


        $device['scores'] = $this->calculateScore($device['state'], $deviceName);
        $data['device'] = $device;

        return view('dashboards/detailed-dashboard', $data);
    }

    protected static function getState($deviceName, $startTimestamp = null, $endTimestamp = null, $interval = 60 * 1440)
    {
        $data = SensorDataController::getStats($deviceName, 1);
        $result = [];

        foreach ($data as $key => $value) {
            $sensorName = AppSettings::entityToSensorName($key);
            $result[$sensorName] = $value['format'];

        }
        return $result;
    }

    /**
     * Calculate final score from all parameters
     * @param array $scores
     * @return float
     */
    public static function calculateFinalScore(array $scores, string $deviceName): float
    {
        $finalScore = 0;
        $scoreMultipliers = AppSettings::getSensorsScoreMultiplier()[$deviceName];
        $totalMultiplier = 0;

        foreach ($scores as $sensor => $score) {
            $scoreMultiplier = $scoreMultipliers[$sensor] ?? 1.0;
            $totalMultiplier += $scoreMultiplier;
            $finalScore += $score * $scoreMultiplier;
        }
        if ($totalMultiplier == 0) return 0.0;
        $finalScore = $finalScore / $totalMultiplier;
        return $finalScore;
    }


    public function export()
    {
        $isPdf = request()->get('isPdf', false);

        $deviceName = request()->get('deviceName', AppSettings::$natwaveDevices[0]);
        // check if device name is valid
        if (!in_array($deviceName, AppSettings::$natwaveDevices)) {
            abort(404);
        }

        if ($isPdf) {
            return Excel::download(new SensorDataExport($deviceName), "sensor_data_{$deviceName}.pdf", \Maatwebsite\Excel\Excel::TCPDF);
        }
        return Excel::download(new SensorDataExport($deviceName), "sensor_data_{$deviceName}.xlsx");
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


    /**
     * @param array $state // e.g ['ph' => ['value' => 7.0, 'unit' => 'pH']]
     * @param string $deviceName
     * @return array
     */
    public static function calculateScore(array $state, string $deviceName): array
    {
        $scores = [];

        foreach ($state as $sensor => $value) {
            if (in_array($sensor, AppSettings::$ignoreSensors)) continue;
            $value = floatval($value['value'] ?? 0);
            $scores[$sensor] = self::calculateScoreFor($sensor, $value, $deviceName);
        }

        return $scores;
    }

    public static function calculateScoreFor(string $sensor, float $value, string $deviceName): float
    {
        $score = 0.0;
        $found = false;
        $parameterName = AppSettings::getPoolProfileParameter()[$deviceName];
        $parameterThresholds = AppSettings::getParameterProfile()[$parameterName];


        foreach ($parameterThresholds as $parameterThreshold) {
            if ($parameterThreshold['sensor'] !== $sensor) continue;
            $found = true;
            if ($value >= $parameterThreshold['min'] && $value <= $parameterThreshold['max']) {

                $score = $parameterThreshold['score'];
                break;
            }
        }
        if (!$found) {
            Log::warning("Sensor $sensor not found with parameter $parameterName");
        }
        return $score;
    }




}

SensorDataController::$parameterThresholdDisplay['green'] = AppSettings::$greenScoreMin;
SensorDataController::$parameterThresholdDisplay['yellow'] = AppSettings::$yellowScoreMin;
