<?php

namespace App\Models;

use App\Http\Controllers\StatusController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Cool stuff here
class AppSettings extends Model
{
    use HasFactory;

    public static $natwaveDevices = [
        'natwave'
    ];
    protected $fillable = [
        'key',
        'value',
    ];


    // Used by all
    protected $casts = [
        'value' => 'json',
    ];

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
        // check if has _ in it
        if (strpos($sensor, '_') === false) return __('translation.' . $sensor);
        $sensor_name = explode('_', $sensor)[1];
        return __('translation.' . $sensor_name);
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
}
