<?php

namespace App\Exports;

use App\Http\Controllers\WaterpoolController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class SensorDataExport implements FromCollection, ShouldAutoSize
{

    protected $deviceName;


    public function __construct(string $deviceName)
    {
        $this->deviceName = $deviceName;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $states = Cache::remember(SensorDataExport::class . 'states', 60 * 15, function () {
            return WaterpoolController::getStates($this->deviceName, 600);
        });
        $collection = new Collection();
        if (empty($states)) return $collection;
        $state = [$states[0]];
        $state = WaterpoolController::formatStates($state)[0];
        $row = [];
        foreach ($state as $key => $value) {
            if ($key === 'timestamp') continue;
            $row[] = $value['label'];
        }
        $row[] = 'Timestamp';
        $collection->push($row);


        foreach ($states as $state) {
            $values = [];
            foreach ($state as $key => $value) {
                if ($key === 'timestamp') {
                    $value = date('Y-m-d H:i:s', $value);
                    $values[] = $value;
                    continue;
                }
                $values[] = $value;
            }
            $collection->push($values);
        }
        return $collection;
    }


}
