<?php

namespace App\Http\Controllers;

use App\Models\AppSettings;
use App\Models\Carbon;
use App\Models\State;
use App\Models\StateMeta;
use Illuminate\Support\Facades\Log;

class StatusController extends Controller
{


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

        // internal name => display name
        $devices = [

        ];
        foreach (AppSettings::getDevicesName()->value as $id => $name) {
            $devices[$id] = $name;

        }

        $data = [
            'devices' => [
            ]
        ];

        foreach ($devices as $deviceName => $deviceDisplayName) {
            $device = [
                'name' => $deviceName,
                'display_name' => $deviceDisplayName,
                'state' => $this->getState($deviceName),
            ];

            $device['scores'] = $this->calculateScore($device['state'], $deviceName);
            $device['final_score'] = $this->calculateFinalScore($device['scores']);
            $states = WaterpoolController::getStates($deviceName, 1);
            $device['😎'] = $states[0];
            $data['devices'][] = $device;
        }


        // add state to data
        foreach ($data['devices'] as $device) {
            foreach ($device['state'] as $sensor => $value) {
                $data[$sensor] = $value;
            }
        }

        $data['parameterThresholdDisplay'] = self::$parameterThresholdDisplay;
        $data['finalScoreDisplay'] = self::$finalScoreDisplay;

        return view('dashboards.smart-home', $data);
    }


    protected static function getState($deviceName = 'natwave')
    {
        $sensors = AppSettings::$sensors;

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
        $temperature = self::formatTemperature(floatval($latestStates["sensor.{$deviceName}_temp"]['state'] ?? 0));

        // Ambil data pH
        $ph = self::formatPH(floatval($latestStates["sensor.{$deviceName}_ph"]['state'] ?? 0));

        // Ambil data (Salt)
        $salt = self::formatSalt(floatval($latestStates["sensor.{$deviceName}_humid"]['state'] ?? 0));

        // Ambil data ORP (Sanitation)
        $orp = self::formatORP(floatval($latestStates["sensor.{$deviceName}_orp"]['state'] ?? 0));

        // Ambil data konduktivitas (Conductivity)
        $conductivity = self::formatConductivity(floatval($latestStates["sensor.{$deviceName}_ec"]['state'] ?? 0));

        // Ambil data TDS
        $tds = self::formatTDS(floatval($latestStates["sensor.{$deviceName}_tds"]['state'] ?? 0));

        $data = [
            'temp' => $temperature,
            'ph' => $ph,
            'humid' => $salt,
            'orp' => $orp,
            'ec' => $conductivity,
            'tds' => $tds,
        ];
        return $data;
    }

    public static function formatTemperature($value)
    {

        $formattedValue = number_format($value, 2, '.', '');

        return [
            'value' => $formattedValue,
            'unit' => '°C',
            'label' => __('translation.temp'),
        ];
    }

    public static function formatPH($value)
    {
        return [
            'value' => $value,
            'unit' => 'pH',
            'label' => __('translation.ph'),
        ];
    }

    // allow for multiple devices

    public static function formatSalt($value)
    {
        return [
            'value' => $value,
            'unit' => 'mg/l',
            'label' => __('translation.humid'),
        ];
    }

    public static function formatORP($value)
    {
        return [
            'value' => $value,
            'unit' => 'mV',
            'label' => __('translation.orp'),
        ];
    }

    public static function formatConductivity($value)
    {
        return [
            'value' => $value,
            'unit' => 'μS/cm',
            'label' => __('translation.ec'),
        ];
    }

    public static function formatTDS($value)
    {
        return [
            'value' => $value,
            'unit' => 'ppm',
            'label' => __('translation.tds'),
        ];
    }

    /**
     * Calculate final score from all parameters
     * @param array $scores
     * @return float
     */
    public static function calculateFinalScore(array $scores): float
    {
        $finalScore = 0;
        foreach ($scores as $score) {
            $finalScore += $score;
        }
        $finalScore = $finalScore / count($scores);
        return $finalScore;
    }

    /**
     * Calculate score for each parameter
     * @param array<string, array> $state // e.g ['temperature' => ['value' => 30.0, 'unit' => '°C']]
     * @return array<string, float> $scores // e.g ['temperature' => 1.0]
     */
    public static function calculateScore(array $state, string $deviceName): array
    {
        $scores = [];
        foreach ($state as $sensor => $value) {
            $value = floatval($value['value'] ?? 0);
            $scores[$sensor] = self::calculateScoreFor($sensor, $value, $deviceName);
        }

        return $scores;
    }

    /**
     * Calculate score for sensor
     * @param string $sensor entity name of sensor
     * @param float $value any value
     * @return float 0.0 - 1.0
     */
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
            $score = 0.0;
        }
        return $score;
    }


}
