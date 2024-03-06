<?php

namespace App\Models\Pool;

use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\WaterpoolController;
use App\Models\AppSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class StateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device',
        'friendly_name',
        'attributes',
        'headers',
        'sensors',
        'ip_address',
    ];

    protected $hidden = [
        'id',
        'headers',
        'updated_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'sensors' => 'array',
        'attributes' => 'array',
    ];


    protected $appends = [
        'scores',
        'final_score',
        'formatted_sensors',
    ];

    public static function withoutAppends(): Builder
    {
        $model = (new static);
        $model->setAppends([]);

        return $model->newQuery();
    }

    // return [device => friendly_name, ...]
    public static function getDevices(): array
    {
        return StateLog::withoutAppends()->distinct('device', 'friendly_name')
            ->pluck('friendly_name', 'device')->toArray();
    }

    public function getScoresAttribute()
    {
        $scores = [];
        foreach ($this->sensors as $sensor => $value) {
            if (in_array($sensor, AppSettings::$ignoreSensors)) continue;
            // check if not float
            if (!is_numeric($value)) {
                $scores[$sensor] = 0;
                continue;
            }
            $scores[$sensor] = SensorDataController::calculateScoreFor($sensor, $value, $this->device);
        }
        return $scores;
    }

    public function getFinalScoreAttribute()
    {
        return SensorDataController::calculateFinalScore($this->scores, $this->device);
    }


    public function getFormattedSensorsAttribute()
    {
        $formattedSensors = [];
        foreach ($this->sensors as $sensor => $value) {
            $formattedSensors[$sensor] = WaterpoolController::formatSensor($sensor, $value);
        }
        return $formattedSensors;
    }
}
