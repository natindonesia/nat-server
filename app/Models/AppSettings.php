<?php

namespace App\Models;

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
        if (!$devicesName) {
            $default = [];
            foreach (self::$natwaveDevices as $id) {
                $default[$id] = $id;
            }
            $devicesName = self::create([
                'key' => 'devices_name',
                'value' => $default,
            ]);
        }
        // check if lacking
        $devicesNameValue = $devicesName->value;
        foreach (self::$natwaveDevices as $id) {
            if (!isset($devicesNameValue[$id])) {
                $devicesNameValue[$id] = $id;
            }
        }

        // check if more
        foreach ($devicesNameValue as $id => $name) {
            if (!in_array($id, self::$natwaveDevices)) {
                unset($devicesNameValue[$id]);
            }
        }
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
        $translationValue = $translation->value;
        foreach ($default as $id => $name) {
            if (!isset($translationValue[$id])) {
                $translationValue[$id] = $name;
            }
        }
        // check if more
        foreach ($translationValue as $id => $name) {
            if (!in_array($id, array_keys($default))) {
                unset($translationValue[$id]);
            }
        }
        $translation->value = $translationValue;

        return $translation;
    }
}
