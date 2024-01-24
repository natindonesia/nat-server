<?php

namespace App\Models;

use App\Http\Controllers\StatusController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// Cool stuff here
class AppSettings extends Model
{
    use HasFactory;

    public static $natwaveDevices = [
        'natwave01',
        'natwave_02_natwave02'
    ];
    protected $fillable = [
        'key',
        'value',
    ];

    public static $sensors = [
        //'cf', // Chlorophyll
        'ph', // PH
        'orp', // Sanitation (ORP)
        'tds', // TDS
        'humid', // Salt
        'temp', // Temperature
    ];

    // Used by all
    protected $casts = [
        'value' => 'json',
    ];

    public static function entityToSensorName(string $entity): string
    {
        // check if it has _ in it
        if (!str_contains($entity, '_')) return $entity;
        return explode('_', $entity)[1];
    }

    public static function getDevicesName()
    {
        $devicesName = self::where('key', 'devices_name')->first();
        $default = [];
        foreach (self::$natwaveDevices as $id) {
            $default[$id] = $id;
        }
        if (!$devicesName) {

            $devicesName = self::create([
                'key' => 'devices_name',
                'value' => $default,
            ]);
        }

        $devicesNameValue = self::syncWithDefault($default, $devicesName->value);
        $devicesName->value = $devicesNameValue;
        return $devicesName;
    }

    public static function translateDeviceName($id)
    {
        return __('devices_name.' . $id);
    }

    public static function translateSensorKey(string $sensor): string
    {
        $sensor = self::entityToSensorName($sensor);
        return __('translation.' . $sensor);
    }

    public static function getTranslation()
    {
        $translation = self::where('key', 'translation')->first();
        $default = [
            'ec' => 'Conductivity',
            'humid' => 'Salt',
            'orp' => 'Sanitation (ORP)',
            'ph' => 'PH',
            'tds' => 'TDS',
            'temp' => 'Temperature',
            'timestamp' => 'Timestamp'
        ];
        foreach (self::$sensors as $sensor) {
            if (!isset($default[$sensor])) $default[$sensor] = Str::upper($sensor);
        }

        if (!$translation) {
            $translation = self::create([
                'key' => 'translation',
                'value' => $default,
            ]);
        }
        // check if lacking
        $translationValue = self::syncWithDefault($default, $translation->value);
        $translation->value = $translationValue;

        return $translation;
    }

    /**
     * Synchronizes the provided value array with the default array.
     *
     * This function checks if the value array is lacking any keys present in the default array.
     * If it finds any, it adds them to the value array with the corresponding default value.
     * It also checks if the value array has any extra keys not present in the default array.
     * If it finds any, it removes them from the value array.
     *
     * @param array $default The default array to synchronize with.
     * @param array $value The array to be synchronized.
     * @param bool $doAdd Whether to add missing keys to the value array. Default is true.
     * @param bool $doRemove Whether to remove extra keys from the value array. Default is true.
     * @return array The synchronized array.
     */
    protected static function syncWithDefault(array $default, array $value, bool $doAdd = true, $doRemove = true): array
    {
        // check if lacking
        if ($doAdd)
            foreach ($default as $id => $name) {
                if (!isset($value[$id])) {
                    $value[$id] = $name;
                }
            }
        // check if more
        if ($doRemove)
            foreach ($value as $id => $name) {
                if (!in_array($id, array_keys($default))) {
                    unset($value[$id]);
                }
            }
        return $value;
    }

    // each profile has different parameter
    public static function getParameterProfile(): array
    {
        $parameterProfile = self::where('key', 'parameter_profile')->first();
        $default = [
            'Internasional' => StatusController::$parametersThresholdInternational
        ];
        if (!$parameterProfile) {
            $parameterProfile = self::create([
                'key' => 'parameter_profile',
                'value' => $default,
            ]);
        }

        if (!is_array($parameterProfile->value)) {
            $parameterProfile->value = $default;
        }
        $value = $parameterProfile->value;
        $value['Internasional'] = $default['Internasional']; // don't change this lol
        return $value;
    }

    // each pool has different profile
    public static function getPoolProfileParameter(): array
    {
        $poolProfileParameter = self::where('key', 'pool_profile_parameter')->first();
        $default = [

        ];
        foreach (self::$natwaveDevices as $id) {
            $default[$id] = "Internasional";
        }
        if (!$poolProfileParameter) {
            $poolProfileParameter = self::create([
                'key' => 'pool_profile_parameter',
                'value' => $default,
            ]);
        }

        if (!is_array($poolProfileParameter->value)) {
            $poolProfileParameter->value = $default;
        }

        $value = self::syncWithDefault($default, $poolProfileParameter->value);
        return $value;
    }

    public static function getSensorsScoreMultiplier(): array
    {
        $poolProfileParameter = self::where('key', 'sensors_score_multiplier')->first();
        $default = [];
        $sensorsMultiplierDefault = [];
        foreach (self::$sensors as $sensor) {
            $sensorsMultiplierDefault[$sensor] = 1;
        }
        foreach (self::$natwaveDevices as $id) {
            $default[$id] = $sensorsMultiplierDefault;
        }
        if (!$poolProfileParameter) {
            $poolProfileParameter = self::create([
                'key' => 'sensors_score_multiplier',
                'value' => $default,
            ]);
        }
        $value = self::syncWithDefault($default, $poolProfileParameter->value);
        foreach ($value as $id => $sensorsMultiplier) {
            $value[$id] = self::syncWithDefault($sensorsMultiplierDefault, $sensorsMultiplier);
        }
        return $value;
    }
}
