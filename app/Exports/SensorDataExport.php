<?php

namespace App\Exports;

use App\Http\Controllers\WaterpoolController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class SensorDataExport implements FromCollection, ShouldAutoSize, WithEvents
{

    use Exportable, RegistersEventListeners;
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
        $states = Cache::remember(SensorDataExport::class . 'states2', 60 * 15, function () {
            return WaterpoolController::getStates($this->deviceName, 200);
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
                if ($value === 'unavailable') {
                    $value = '?';
                }
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


    public static function afterSheet(AfterSheet $event)
    {

        $event->getSheet()->getDelegate()->getStyle('A1:Z1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $event->getSheet()->getDelegate()->getStyle('A1:Z1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }
}
