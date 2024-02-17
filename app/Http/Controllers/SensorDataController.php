<?php

namespace App\Http\Controllers;

use App\Exports\SensorDataExport;
use App\Models\AppSettings;
use App\Models\SensorData;
use App\Models\State;
use App\Models\StateMeta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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

            if ($interval !== null) {
                $state = $state->groupBy(DB::raw('FLOOR(last_updated_ts / ' . $interval . ')'));
            }

            $state = $state->orderBy('formatted_timestamp', 'asc')
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

    
    


    // Threshold for each parameter
    // Example sensor 1
    // if range of 28< or >20 get score 1
    // else if range of 30< or >19 get score 0.7
    // else 0.5

    // Evaluated from top to bottom
    public static $parametersThresholdInternational = [
        [
            'sensor' => 'temp',
            'min' => 22,
            'max' => 26,
            'score' => 1.0
        ],
        [
            'sensor' => 'temp',
            'min' => 18,
            'max' => 29,
            'score' => 0.55
        ],
        [
            'sensor' => 'ph',
            'min' => 7.2,
            'max' => 8.0,
            'score' => 1.0
        ],
        [
            'sensor' => 'ph',
            'min' => 6.5,
            'max' => 8.6,
            'score' => 0.4
        ],
        [
            'sensor' => 'orp',
            'min' => 700,
            'max' => 750,
            'score' => 1.0
        ],
        [
            'sensor' => 'orp',
            'min' => 650,
            'max' => 700,
            'score' => 0.58
        ],
        [
            'sensor' => 'humid',
            'min' => 0,
            'max' => 60,
            'score' => 1.0
        ],
        [
            'sensor' => 'humid',
            'min' => 0,
            'max' => 120,
            'score' => 0.7
        ],
        [
            'sensor' => 'humid',
            'min' => 0,
            'max' => 150,
            'score' => 0.5
        ],
        [
            'sensor' => 'ec',
            'min' => 2.5,
            'max' => 3.0,
            'score' => 1.0
        ],
        [
            'sensor' => 'ec',
            'min' => 2.0,
            'max' => 2.5,
            'score' => 0.7
        ],
        [
            'sensor' => 'tds',
            'min' => 0,
            'max' => 500,
            'score' => 1.0
        ],
        [
            'sensor' => 'tds',
            'min' => 0,
            'max' => 600,
            'score' => 0.7
        ],
        [
            'sensor' => 'tds',
            'min' => 0,
            'max' => 750,
            'score' => 0.5
        ],

    ];


    public static $parameterThresholdDisplay = [
        'green' => 0.7, // above 70%
        'yellow' => 0.4, // above 60%
    ];
    public static $finalScoreDisplay = [
        'green' => 0.7,
        'yellow' => 0.5,
    ];










    public function index()
    {
    



        $deviceName = request()->get('device', AppSettings::$natwaveDevices[0]);
        //yes this is duplicate query, have problem ?
        $states = WaterpoolController::getStates($deviceName, 30);
        $stats = SensorDataController::getStats($deviceName, 30);

        

        $data = [
            'formatted_states' => WaterpoolController::formatStates($states),
            'stats' => $stats,
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
            'state' => $this->getState($deviceName),
        ];


        $device['scores'] = $this->calculateScore($device['state'], $deviceName);

        $device['final_score'] = $this->calculateFinalScore($device['scores'], $deviceName);
        $states = WaterpoolController::getStates($deviceName, 1);
        if (count($states) != 0) {
            $device['😎'] = $states[0];
        }
        $data['device'] = $device;

        return view('dashboards/detailed-dashboard', $data);
    }
    protected static function getState($deviceName)
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


    public static function calculateScore(array $state, string $deviceName): array
    {
        $scores = [];
        foreach ($state as $sensor => $value) {
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
        $parameterThreshold = AppSettings::getParameterProfile()[$parameterName];

        foreach ($parameterThreshold as $parameterThreshold) {
            if ($parameterThreshold['sensor'] !== $sensor) continue;
            $found = true;
            if ($value >= $parameterThreshold['min'] && $value <= $parameterThreshold['max']) {

                $score = $parameterThreshold['score'];
                break;
            }
        }
        if (!$found) {
            Log::warning("Sensor $sensor not found with parameter $parameterName");
            $score = 1;
        }
        return $score;
    }




}
