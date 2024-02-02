<?php

$conf = [
    'name' => env('DEVICES_NAME', \App\Models\AppSettings::$natwaveDevices),
    'sensors' => env('DEVICES_SENSORS', \App\Models\AppSettings::$sensors),
];

// convert string to array by splitting with space
if (is_string($conf['sensors'])) {
    $conf['sensors'] = explode(' ', $conf['sensors']);
}
if (is_string($conf['name'])) {
    $conf['name'] = explode(' ', $conf['name']);
}
\App\Models\AppSettings::$natwaveDevices = $conf['name'];
\App\Models\AppSettings::$sensors = $conf['sensors'];
return $conf;
