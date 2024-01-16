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
}
