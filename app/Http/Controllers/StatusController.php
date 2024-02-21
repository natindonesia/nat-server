<?php

namespace App\Http\Controllers;

use App\Models\AppSettings;
use App\Models\Carbon;
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

            $device['final_score'] = $this->calculateFinalScore($device['scores'], $deviceName);
            $states = WaterpoolController::getStates($deviceName, 1);
            if (count($states) != 0) {
                $device['😎'] = $states[0];
            }
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

    public static function formatChlorine($value)
    {
        return [
            'value' => $value,
            'unit' => 'mg/l',
            'label' => __('translation.ch'),
        ];
    }

    public static function formatBattery($value)
    {
        return [
            'value' => $value,
            'unit' => '%',
            'label' => __('translation.battery'),
        ];
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
            $score = 1;
        }
        return $score;
    }


}
